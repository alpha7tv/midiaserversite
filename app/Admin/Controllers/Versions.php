<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;
use App\Core\Markup;
use App\Core\Settings;

/** Versões e changelog do Mídia Rádio Studio. A publicação e os avisos (site, newsletter, push) são sempre escolha do administrador. */
final class Versions
{
    public static function index(): void
    {
        $rows = Db::all("SELECT * FROM software_versions ORDER BY (released_at IS NULL), released_at DESC, id DESC");
        $secret = Settings::get('studio_webhook_secret') !== '';
        Kernel::view('versions', ['rows' => $rows, 'secret' => $secret], 'Versões do Studio');
    }

    public static function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $v = $id ? Db::one('SELECT * FROM software_versions WHERE id = ?', [$id]) : null;
        Kernel::view('version_edit', ['v' => $v], $v ? 'Editar versão' : 'Nova versão');
    }

    public static function save(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $in = static fn (string $k, int $max = 4000): string => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);
        $version = $in('version', 40);
        $url = $in('download_url', 400);
        $date = $in('released_at', 10);
        if (!preg_match('/^\d+(\.\d+){0,3}([-+][\w.]+)?$/', $version)) {
            Session::flash('err', 'Versão inválida. Exemplo: 1.4.2');
            Kernel::redirect('/admin/versoes/editar' . ($id ? '?id=' . $id : ''));
        }
        if ($url !== '' && !preg_match('#^https://\S+$#', $url)) {
            Session::flash('err', 'O link de download precisa começar com https://.');
            Kernel::redirect('/admin/versoes/editar' . ($id ? '?id=' . $id : ''));
        }
        $released = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date . ' 00:00:00' : date('Y-m-d H:i:s');
        $published = empty($_POST['published']) ? 0 : 1;
        $vals = ['midia-radio-studio', $version, $released, $in('notes'), $in('fixes'), $url ?: null, $published, empty($_POST['notify_site']) ? 0 : 1, empty($_POST['notify_newsletter']) ? 0 : 1, empty($_POST['notify_push']) ? 0 : 1];
        if ($id) {
            Db::run('UPDATE software_versions SET software_slug=?, version=?, released_at=?, notes=?, fixes=?, download_url=?, published=?, notify_site=?, notify_newsletter=?, notify_push=? WHERE id=?', [...$vals, $id]);
        } else {
            Db::run('INSERT INTO software_versions (software_slug, version, released_at, notes, fixes, download_url, published, notify_site, notify_newsletter, notify_push) VALUES (?,?,?,?,?,?,?,?,?,?)', $vals);
            $id = (int) Db::pdo()->lastInsertId();
        }
        Audit::log('versao_salva', 'software_versions#' . $id, ['versao' => $version, 'publicada' => $published]);
        $msg = 'Versão ' . $version . ($published ? ' publicada no site.' : ' salva como rascunho (não aparece no site).');
        // newsletter: cria RASCUNHO de campanha para você revisar; nunca envia sozinho
        $row = Db::one('SELECT campaign_id FROM software_versions WHERE id = ?', [$id]);
        if ($published && !empty($_POST['notify_newsletter']) && empty($row['campaign_id'])) {
            $md = "**Nova versão do Mídia Rádio Studio: v{$version}**\n\n" . ($vals[3] !== '' ? "## Novidades\n\n" . $vals[3] . "\n\n" : '') . ($vals[4] !== '' ? "## Correções\n\n" . $vals[4] . "\n\n" : '') . '[Baixar ou atualizar](' . app_url('/automacao-radio#download') . ')';
            Db::run("INSERT INTO campaigns (subject, body_html, body_text, source, status, created_by) VALUES (?,?,?,?,?,?)", ['Mídia Rádio Studio v' . $version . ' já está disponível', Markup::toHtml($md), $md, 'version', 'draft', Session::user()['id'] ?? null]);
            $cid = (int) Db::pdo()->lastInsertId();
            Db::run('UPDATE software_versions SET campaign_id = ? WHERE id = ?', [$cid, $id]);
            $msg .= ' Criei um rascunho de newsletter para você revisar e enviar em Newsletter.';
        }
        if (!empty($_POST['notify_push'])) {
            $msg .= ' Aviso de Web Push registrado: o envio de push será ativado quando as chaves VAPID forem configuradas.';
        }
        Session::flash('ok', $msg);
        Kernel::redirect('/admin/versoes');
    }
}
