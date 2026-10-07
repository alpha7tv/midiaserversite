<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;
use App\Core\Mailer;
use App\Core\Markup;
use App\Services\NewsletterSender;

final class Newsletter
{
    public static function index(): void
    {
        $counts = ['confirmed' => 0, 'pending' => 0, 'unsubscribed' => 0];
        foreach (Db::all('SELECT status, COUNT(*) n FROM newsletter_subscribers GROUP BY status') as $r) {
            $counts[(string) $r['status']] = (int) $r['n'];
        }
        $q = trim((string) ($_GET['q'] ?? ''));
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
        $subs = $q !== ''
            ? Db::all("SELECT * FROM newsletter_subscribers WHERE email LIKE ? ESCAPE '\\' ORDER BY id DESC LIMIT 100", [$like])
            : Db::all('SELECT * FROM newsletter_subscribers ORDER BY id DESC LIMIT 100');
        $campaigns = Db::all('SELECT * FROM campaigns ORDER BY id DESC LIMIT 50');
        Kernel::view('newsletter', ['counts' => $counts, 'subs' => $subs, 'campaigns' => $campaigns, 'smtp' => Mailer::configured(), 'q' => $q], 'Newsletter');
    }

    public static function campaign(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $c = $id ? Db::one('SELECT * FROM campaigns WHERE id = ?', [$id]) : null;
        Kernel::view('campaign_edit', ['c' => $c], $c ? 'Campanha' : 'Nova campanha');
    }

    public static function saveCampaign(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $subject = mb_substr(trim((string) ($_POST['subject'] ?? '')), 0, 200);
        $md = mb_substr(trim((string) ($_POST['body'] ?? '')), 0, 60000);
        $c = $id ? Db::one('SELECT status FROM campaigns WHERE id = ?', [$id]) : null;
        if ($subject === '' || $md === '') {
            Session::flash('err', 'Assunto e texto são obrigatórios.');
            Kernel::redirect('/admin/newsletter/campanha' . ($id ? '?id=' . $id : ''));
        }
        if ($c && $c['status'] !== 'draft') {
            Session::flash('err', 'Só é possível editar campanhas em rascunho.');
            Kernel::redirect('/admin/newsletter');
        }
        if ($id) {
            Db::run('UPDATE campaigns SET subject = ?, body_html = ?, body_text = ? WHERE id = ?', [$subject, Markup::toHtml($md), $md, $id]);
        } else {
            Db::run("INSERT INTO campaigns (subject, body_html, body_text, source, status, created_by) VALUES (?,?,?,'manual','draft',?)", [$subject, Markup::toHtml($md), $md, Session::user()['id'] ?? null]);
            $id = (int) Db::pdo()->lastInsertId();
        }
        Audit::log('campanha_salva', 'campaigns#' . $id);
        Session::flash('ok', 'Campanha salva como rascunho. Envie um teste para você antes de disparar.');
        Kernel::redirect('/admin/newsletter/campanha?id=' . $id);
    }

    public static function test(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $c = Db::one('SELECT * FROM campaigns WHERE id = ?', [$id]);
        $to = (string) (Session::user()['email'] ?? '');
        if (!$c) {
            Session::flash('err', 'Campanha não encontrada.');
        } elseif (!Mailer::configured()) {
            Session::flash('err', 'Configure o SMTP antes.');
        } else {
            $r = Mailer::send($to, '[TESTE] ' . $c['subject'], NewsletterSender::wrap((string) $c['body_html'], str_repeat('0', 48)));
            Session::flash($r['ok'] ? 'ok' : 'err', $r['ok'] ? "Teste enviado para $to." : 'Falha: ' . $r['error']);
        }
        Kernel::redirect('/admin/newsletter/campanha?id=' . $id);
    }

    /** Coloca a campanha na fila de envio (somente inscritos confirmados). */
    public static function send(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $c = Db::one('SELECT * FROM campaigns WHERE id = ?', [$id]);
        $total = (int) Db::one("SELECT COUNT(*) n FROM newsletter_subscribers WHERE status = 'confirmed'")['n'];
        if (!$c || $c['status'] !== 'draft') {
            Session::flash('err', 'Só campanhas em rascunho podem ser enviadas.');
        } elseif (!Mailer::configured()) {
            Session::flash('err', 'Configure o SMTP antes de enviar.');
        } elseif ($total === 0) {
            Session::flash('err', 'Não há inscritos confirmados.');
        } elseif (Db::one("SELECT id FROM campaigns WHERE status = 'sending'")) {
            Session::flash('err', 'Já existe uma campanha em envio. Aguarde terminar.');
        } else {
            Db::run("UPDATE campaigns SET status = 'sending', total_count = ?, started_at = ? WHERE id = ?", [$total, date('Y-m-d H:i:s'), $id]);
            Audit::log('campanha_enviada', 'campaigns#' . $id, ['destinatarios' => $total]);
            Session::flash('ok', "Envio iniciado para $total inscrito(s) confirmado(s). Use “Enviar próximo lote” ou deixe o agendador da VPS (newsletter:send) concluir.");
        }
        Kernel::redirect('/admin/newsletter');
    }

    public static function batch(): void
    {
        $r = NewsletterSender::runBatch(25);
        Session::flash($r['failed'] ? 'warn' : 'ok', "Lote processado: {$r['sent']} enviado(s), {$r['failed']} falha(s)." . ($r['done'] ? '' : ' Ainda há destinatários na fila.'));
        Kernel::redirect('/admin/newsletter');
    }

    public static function export(): void
    {
        Audit::log('inscritos_exportados');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="newsletter-' . date('Y-m-d') . '.csv"');
        $o = fopen('php://output', 'w');
        fwrite($o, "\xEF\xBB\xBF");
        fputcsv($o, ['email', 'status', 'inscrito_em', 'confirmado_em', 'origem']);
        foreach (Db::all('SELECT email, status, created_at, confirmed_at, source FROM newsletter_subscribers ORDER BY id') as $r) {
            fputcsv($o, [$r['email'], $r['status'], $r['created_at'], $r['confirmed_at'], $r['source']]);
        }
        fclose($o);
        exit;
    }

    public static function remove(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (Db::run('DELETE FROM newsletter_subscribers WHERE id = ?', [$id])) {
            Audit::log('inscrito_removido', 'newsletter_subscribers#' . $id);
            Session::flash('ok', 'Inscrito removido definitivamente.');
        }
        Kernel::redirect('/admin/newsletter');
    }
}
