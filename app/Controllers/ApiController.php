<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Db;
use App\Core\Logger;
use App\Core\Settings;

/** API pública assinada. POST /api/v1/studio/version: o software avisa uma nova versão; ela chega como RASCUNHO. */
final class ApiController
{
    public static function studioVersion(string $method): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        if ($method !== 'POST') {
            self::out(405, ['error' => 'use POST']);
        }
        $secret = Settings::get('studio_webhook_secret');
        $raw = (string) file_get_contents('php://input', false, null, 0, 20001);
        if ($secret === '' || strlen($raw) > 20000) {
            self::out($secret === '' ? 503 : 413, ['error' => $secret === '' ? 'webhook não configurado' : 'corpo grande demais']);
        }
        $sig = (string) ($_SERVER['HTTP_X_SIGNATURE'] ?? '');
        $good = 'sha256=' . hash_hmac('sha256', $raw, $secret);
        if (!hash_equals($good, $sig)) {
            self::log('assinatura_invalida');
            self::out(401, ['error' => 'assinatura inválida']);
        }
        $d = json_decode($raw, true);
        $version = is_array($d) ? trim((string) ($d['version'] ?? '')) : '';
        $url = is_array($d) ? trim((string) ($d['download_url'] ?? '')) : '';
        if (!preg_match('/^\d+(\.\d+){0,3}([-+][\w.]+)?$/', $version) || ($url !== '' && !preg_match('#^https://\S+$#', $url))) {
            self::log('payload_invalido');
            self::out(422, ['error' => 'version ou download_url inválidos']);
        }
        if (Db::one("SELECT id FROM software_versions WHERE software_slug = 'midia-radio-studio' AND version = ?", [$version])) {
            self::out(200, ['status' => 'exists', 'version' => $version]);
        }
        $date = (string) ($d['released_at'] ?? '');
        $released = preg_match('/^\d{4}-\d{2}-\d{2}/', $date) ? substr($date, 0, 10) . ' 00:00:00' : date('Y-m-d H:i:s');
        Db::run(
            "INSERT INTO software_versions (software_slug, version, released_at, notes, fixes, download_url, published) VALUES ('midia-radio-studio', ?, ?, ?, ?, ?, 0)",
            [$version, $released, mb_substr((string) ($d['notes'] ?? ''), 0, 4000), mb_substr((string) ($d['fixes'] ?? ''), 0, 4000), $url ?: null]
        );
        self::log('versao_recebida', ['version' => $version]);
        self::out(201, ['status' => 'created_as_draft', 'version' => $version]);
    }

    private static function log(string $msg, array $ctx = []): void
    {
        Logger::write('api', $msg, $ctx);
        try {
            Db::run('INSERT INTO integration_logs (source, level, message, payload) VALUES (?,?,?,?)', ['studio', str_contains($msg, 'invalid') ? 'warn' : 'info', $msg, json_encode($ctx)]);
        } catch (\Throwable) {
        }
    }

    private static function out(int $code, array $body): never
    {
        http_response_code($code);
        echo json_encode($body, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
