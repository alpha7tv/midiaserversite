<?php
declare(strict_types=1);

namespace App\Core;

/** Cliente SMTP mínimo (SSL direto na 465 ou STARTTLS na 587, AUTH LOGIN/PLAIN). Configuração em Settings (smtp_*). */
final class Mailer
{
    public static function configured(): bool
    {
        return Settings::get('smtp_host') !== '' && Settings::get('smtp_from_email') !== '';
    }

    /** @return array{ok:bool,error:string} */
    public static function send(string $to, string $subject, string $html, string $text = '', array $headers = []): array
    {
        if (!self::configured()) {
            return ['ok' => false, 'error' => 'SMTP não configurado'];
        }
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'destinatário inválido'];
        }
        $host = Settings::get('smtp_host');
        $port = (int) Settings::get('smtp_port', '587');
        $secure = Settings::get('smtp_secure', 'tls');             // ssl | tls | none
        $user = Settings::get('smtp_user');
        $pass = Settings::get('smtp_pass');
        $from = Settings::get('smtp_from_email');
        $fromName = Settings::get('smtp_from_name', 'Mídia Server');

        $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => $host]]);
        $remote = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
        $fp = @stream_socket_client($remote, $errno, $errstr, 12, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            return self::fail("conexão falhou: $errstr ($errno)");
        }
        stream_set_timeout($fp, 15);

        try {
            self::expect($fp, [220]);
            $ehlo = gethostname() ?: 'localhost';
            self::cmd($fp, "EHLO $ehlo", [250]);
            if ($secure === 'tls') {
                self::cmd($fp, 'STARTTLS', [220]);
                if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \RuntimeException('falha ao iniciar TLS');
                }
                self::cmd($fp, "EHLO $ehlo", [250]);
            }
            if ($user !== '') {
                $r = self::cmd($fp, 'AUTH LOGIN', [334, 235, 503]);
                if (str_starts_with($r, '334')) {
                    self::cmd($fp, base64_encode($user), [334]);
                    self::cmd($fp, base64_encode($pass), [235]);
                }
            }
            self::cmd($fp, 'MAIL FROM:<' . $from . '>', [250]);
            self::cmd($fp, 'RCPT TO:<' . $to . '>', [250, 251]);
            self::cmd($fp, 'DATA', [354]);
            fwrite($fp, self::dotStuff(self::message($to, $from, $fromName, $subject, $html, $text, $headers)) . "\r\n.\r\n");
            self::expect($fp, [250]);
            self::cmd($fp, 'QUIT', [221]);
            fclose($fp);
            return ['ok' => true, 'error' => ''];
        } catch (\Throwable $e) {
            @fclose($fp);
            return self::fail($e->getMessage());
        }
    }

    private static function fail(string $msg): array
    {
        Logger::write('mail', 'falha', ['error' => $msg]);
        return ['ok' => false, 'error' => $msg];
    }

    /** @param resource $fp */
    private static function cmd($fp, string $line, array $ok): string
    {
        fwrite($fp, $line . "\r\n");
        return self::expect($fp, $ok);
    }

    /** @param resource $fp */
    private static function expect($fp, array $codes): string
    {
        $resp = '';
        while (($l = fgets($fp, 1024)) !== false) {
            $resp .= $l;
            if (strlen($l) < 4 || $l[3] === ' ') {
                break;
            }
        }
        $code = (int) substr($resp, 0, 3);
        if (!in_array($code, $codes, true)) {
            throw new \RuntimeException('SMTP ' . trim(preg_replace('/\s+/', ' ', $resp) ?? ''));
        }
        return $resp;
    }

    private static function dotStuff(string $m): string
    {
        return preg_replace('/^\./m', '..', $m) ?? $m;
    }

    private static function message(string $to, string $from, string $fromName, string $subject, string $html, string $text, array $extra = []): string
    {
        $b = '=_ms_' . bin2hex(random_bytes(8));
        $enc = static fn (string $s): string => '=?UTF-8?B?' . base64_encode($s) . '?=';
        if ($text === '') {
            $text = trim(html_entity_decode(strip_tags(preg_replace('#<(br|/p|/h\d|/li)>#i', "\n", $html) ?? $html), ENT_QUOTES, 'UTF-8'));
        }
        $domain = substr(strrchr($from, '@') ?: '@localhost', 1);
        $h = [
            'Date: ' . date('r'),
            'From: ' . $enc($fromName) . " <$from>",
            "To: <$to>",
            'Subject: ' . $enc($subject),
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $domain . '>',
            'MIME-Version: 1.0',
        ];
        if (!isset($extra['List-Unsubscribe'])) {
            $h[] = 'List-Unsubscribe: <mailto:' . $from . '?subject=sair>';
        }
        foreach ($extra as $k => $v) {
            if (preg_match('/^[A-Za-z-]+$/', (string) $k) && !preg_match('/[\r\n]/', (string) $v)) {
                $h[] = $k . ': ' . $v;
            }
        }
        $h[] = "Content-Type: multipart/alternative; boundary=\"$b\"";
        return implode("\r\n", $h) . "\r\n\r\n"
            . "--$b\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($text)) . "\r\n"
            . "--$b\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "\r\n"
            . "--$b--";
    }
}
