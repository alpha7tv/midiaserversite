<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Kernel;
use App\Core\Db;

final class Logs
{
    private const FILES = ['leads' => 'Leads', 'mail' => 'E-mail', 'newsletter' => 'Newsletter', 'api' => 'API e webhooks', 'whmcs' => 'WHMCS', 'admin' => 'Painel (erros)', 'app' => 'Aplicação', 'php-error' => 'PHP'];

    public static function index(): void
    {
        $audit = Db::all('SELECT a.created_at, a.action, a.entity, a.payload, u.name FROM audit_logs a LEFT JOIN admins u ON u.id = a.admin_id ORDER BY a.id DESC LIMIT 100');
        $integ = Db::all('SELECT created_at, source, level, message, payload FROM integration_logs ORDER BY id DESC LIMIT 50');
        $key = isset(self::FILES[$_GET['arquivo'] ?? '']) ? (string) $_GET['arquivo'] : 'leads';
        $file = BASE_PATH . '/storage/logs/' . $key . '.log';
        $tail = '';
        if (is_file($file)) {
            $size = filesize($file) ?: 0;
            $fh = fopen($file, 'rb');
            fseek($fh, max(0, $size - 20000));
            $tail = (string) stream_get_contents($fh);
            fclose($fh);
            $lines = explode("\n", trim($tail));
            $tail = implode("\n", array_slice($lines, -80));
        }
        Kernel::view('logs', ['audit' => $audit, 'integ' => $integ, 'files' => self::FILES, 'key' => $key, 'tail' => $tail], 'Logs e auditoria');
    }
}
