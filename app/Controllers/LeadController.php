<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Db;
use App\Core\Logger;
use App\Core\Settings;

/** Captura de leads das landings: valida, grava, avisa por webhook (opcional) e redireciona para /obrigado. */
final class LeadController
{
    private const CONSENT = 'Ao enviar, concordo em receber contato da Mídia Server por WhatsApp sobre este serviço e com a Política de Privacidade.';

    public static function store(): void
    {
        $in = static fn (string $k, int $max = 200): string => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);

        if (!Csrf::check($in('csrf', 64))) {
            self::fail(403, 'Sessão expirada. Volte à página, recarregue e tente de novo.');
        }
        if ($in('website') !== '') {            // campo isca preenchido: bot
            self::okSilently();
        }
        if (!Csrf::stampOk($in('ts', 12), $in('sig', 80))) {
            self::fail(400, 'Envio muito rápido ou expirado. Volte, aguarde um instante e tente de novo.');
        }
        if (!self::turnstileOk($in('cf-turnstile-response', 2000))) {
            self::fail(400, 'Não foi possível validar que você é uma pessoa. Tente de novo.');
        }

        $name = preg_replace('/\s+/u', ' ', $in('name', 80));
        $phone = self::phone($in('phone', 30));
        if (mb_strlen((string) $name) < 2 || !preg_match('/^[\p{L}\p{M}\' .-]+$/u', (string) $name)) {
            self::fail(422, 'Informe seu nome.');
        }
        if ($phone === null) {
            self::fail(422, 'Informe um WhatsApp válido com DDD, por exemplo (14) 98815-9045.');
        }

        $pages = require BASE_PATH . '/config/pages.php';
        $page = $in('page', 160);
        $interest = $in('interest', 80);
        if (!isset($pages[$page])) {
            $page = '/';
        }

        $ip = Csrf::ipHash();
        try {
            $recent = Db::one("SELECT COUNT(*) AS n FROM leads WHERE ip_hash = ? AND created_at > ?", [$ip, date('Y-m-d H:i:s', time() - 3600)]);
            if ($recent && (int) $recent['n'] >= 5) {
                self::fail(429, 'Muitos envios em pouco tempo. Tente novamente mais tarde ou fale pelo WhatsApp.');
            }
            $dup = Db::one('SELECT id FROM leads WHERE phone = ? AND created_at > ?', [$phone, date('Y-m-d H:i:s', time() - 600)]);
            if (!$dup) {
                $attr = self::attribution($in('attribution', 1200));
                Db::run(
                    'INSERT INTO leads (name, phone, interest, page, attribution, ip_hash, consent_text) VALUES (?,?,?,?,?,?,?)',
                    [$name, $phone, $interest, $page, json_encode($attr, JSON_UNESCAPED_UNICODE), $ip, self::CONSENT]
                );
                Logger::write('leads', 'novo', ['phone_tail' => substr($phone, -4), 'page' => $page, 'interest' => $interest]);
                self::webhook(['name' => $name, 'phone' => $phone, 'interest' => $interest, 'page' => $page, 'attribution' => $attr]);
            }
        } catch (\Throwable $e) {
            Logger::write('leads', 'erro', ['error' => $e->getMessage()]);
            self::fail(500, 'Não conseguimos registrar agora. Fale com a gente pelo WhatsApp.');
        }

        setcookie('ms_ty', base64_encode(json_encode(['n' => mb_substr((string) $name, 0, 40), 'i' => $interest, 'p' => $page], JSON_UNESCAPED_UNICODE)), [
            'expires' => time() + 900, 'path' => '/', 'secure' => Csrf::https(), 'httponly' => true, 'samesite' => 'Lax',
        ]);
        header('Location: /obrigado', true, 303);
        exit;
    }

    /** Dados para a página de obrigado (nome e interesse vêm de cookie curto, nunca da URL). */
    public static function thanksData(): array
    {
        $raw = base64_decode((string) ($_COOKIE['ms_ty'] ?? ''), true);
        $d = $raw ? json_decode($raw, true) : null;
        return is_array($d) ? $d : [];
    }

    private static function phone(string $raw): ?string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if (str_starts_with($d, '55') && strlen($d) >= 12) {
            $d = substr($d, 2);
        }
        if (!preg_match('/^[1-9][1-9]9?\d{8}$/', $d)) {      // DDD + 8 ou 9 dígitos
            return null;
        }
        return '55' . $d;
    }

    /** @return array<string,string> */
    private static function attribution(string $json): array
    {
        $a = json_decode($json, true);
        $out = [];
        if (is_array($a)) {
            foreach (['gclid', 'gbraid', 'wbraid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'matchtype', 'network'] as $k) {
                if (isset($a[$k]) && is_string($a[$k])) {
                    $out[$k] = mb_substr($a[$k], 0, 120);
                }
            }
        }
        return $out;
    }

    private static function turnstileOk(string $token): bool
    {
        $secret = Settings::get('turnstile_secret');
        if ($secret === '') {
            return true;                                   // Turnstile não configurado
        }
        if ($token === '') {
            return false;
        }
        $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5,
            CURLOPT_POSTFIELDS => http_build_query(['secret' => $secret, 'response' => $token, 'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''])]);
        $res = json_decode((string) curl_exec($ch), true);
        curl_close($ch);
        return is_array($res) && !empty($res['success']);
    }

    private static function webhook(array $payload): void
    {
        $url = Settings::get('lead_webhook_url');
        if ($url === '' || !preg_match('#^https://#', $url)) {
            return;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 4, CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE)]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300) {
            Logger::write('leads', 'webhook_falhou', ['code' => $code]);
        }
    }

    private static function okSilently(): never
    {
        header('Location: /obrigado', true, 303);
        exit;
    }

    private static function fail(int $code, string $msg): never
    {
        http_response_code($code);
        $page = [
            'path' => '/lead', 'template' => 'lead_error', 'og' => 'home', 'noindex' => true, 'title' => 'Não foi possível enviar | Mídia Server',
            'description' => 'Não foi possível enviar o formulário.', 'h1' => 'Não foi possível enviar', 'crumb' => 'Erro',
            'wa' => 'Olá, tive um problema ao enviar o formulário do site.',
        ];
        $body = \App\Core\View::render('pages/lead_error', ['page' => $page, 'message' => $msg]);
        echo \App\Core\View::render('layout', ['page' => $page, 'content' => $body]);
        exit;
    }
}
