<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Db;

/** Registro de ações administrativas (nunca grava senhas ou valores de segredos). */
final class Audit
{
    public static function log(string $action, ?string $entity = null, array $payload = []): void
    {
        try {
            Db::run(
                'INSERT INTO audit_logs (admin_id, action, entity, payload, ip_hash) VALUES (?,?,?,?,?)',
                [Session::user()['id'] ?? null, $action, $entity, json_encode($payload, JSON_UNESCAPED_UNICODE), Csrf::ipHash()]
            );
        } catch (\Throwable) {
        }
    }
}
