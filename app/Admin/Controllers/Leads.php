<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;

final class Leads
{
    private const STATUS = ['new' => 'Novo', 'contacted' => 'Contatado', 'won' => 'Fechou', 'lost' => 'Perdido'];

    public static function index(): void
    {
        $st = (string) ($_GET['status'] ?? '');
        $q = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['p'] ?? 1));
        $where = '1=1';
        $params = [];
        if (isset(self::STATUS[$st])) {
            $where .= ' AND status = ?';
            $params[] = $st;
        }
        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $where .= " AND (name LIKE ? ESCAPE '\\' OR phone LIKE ? ESCAPE '\\' OR interest LIKE ? ESCAPE '\\')";
            array_push($params, $like, $like, $like);
        }
        $total = (int) Db::one("SELECT COUNT(*) n FROM leads WHERE $where", $params)['n'];
        $per = 30;
        $rows = Db::all("SELECT * FROM leads WHERE $where ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params);
        foreach ($rows as &$r) {
            $r['attr'] = json_decode((string) $r['attribution'], true) ?: [];
        }
        Kernel::view('leads', ['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per, 'st' => $st, 'q' => $q, 'status' => self::STATUS], 'Leads');
    }

    public static function status(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $s = (string) ($_POST['status'] ?? '');
        if (isset(self::STATUS[$s]) && Db::run('UPDATE leads SET status = ? WHERE id = ?', [$s, $id])) {
            Audit::log('lead_status', 'leads#' . $id, ['status' => $s]);
            Session::flash('ok', 'Lead atualizado.');
        }
        Kernel::redirect('/admin/leads' . (!empty($_POST['back']) ? '?' . http_build_query(['status' => (string) $_POST['back']]) : ''));
    }

    /** Exclusão definitiva (pedido do titular, LGPD). */
    public static function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (Db::run('DELETE FROM leads WHERE id = ?', [$id])) {
            Audit::log('lead_excluido', 'leads#' . $id);
            Session::flash('ok', 'Lead excluído definitivamente.');
        }
        Kernel::redirect('/admin/leads');
    }

    public static function export(): void
    {
        Audit::log('leads_exportados');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="leads-' . date('Y-m-d') . '.csv"');
        $o = fopen('php://output', 'w');
        fwrite($o, "\xEF\xBB\xBF");
        fputcsv($o, ['id', 'data', 'nome', 'whatsapp', 'interesse', 'pagina', 'status', 'utm_source', 'utm_campaign', 'utm_term', 'gclid']);
        foreach (Db::all('SELECT * FROM leads ORDER BY id DESC') as $r) {
            $a = json_decode((string) $r['attribution'], true) ?: [];
            fputcsv($o, [$r['id'], $r['created_at'], self::csvSafe((string) $r['name']), $r['phone'], self::csvSafe((string) $r['interest']), $r['page'], $r['status'], $a['utm_source'] ?? '', $a['utm_campaign'] ?? '', self::csvSafe((string) ($a['utm_term'] ?? '')), $a['gclid'] ?? '']);
        }
        fclose($o);
        exit;
    }

    /** Evita injeção de fórmulas ao abrir no Excel. */
    private static function csvSafe(string $v): string
    {
        return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
    }
}
