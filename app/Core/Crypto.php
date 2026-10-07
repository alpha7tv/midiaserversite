<?php
declare(strict_types=1);

namespace App\Core;

/** Criptografia de segredos em repouso (AES-256-GCM, chave derivada de APP_KEY). */
final class Crypto
{
    private static function key(): string
    {
        $k = (string) env('APP_KEY', '');
        if (strlen($k) < 32) {
            throw new \RuntimeException('APP_KEY ausente ou curta (mínimo 32 caracteres).');
        }
        return hash('sha256', 'ms-portal|' . $k, true);
    }

    public static function encrypt(string $plain): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $ct = openssl_encrypt($plain, 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        return 'enc:' . base64_encode($iv . $tag . (string) $ct);
    }

    public static function decrypt(string $stored): string
    {
        if (!str_starts_with($stored, 'enc:')) {
            return $stored;                       // valor antigo, ainda em texto
        }
        $raw = base64_decode(substr($stored, 4), true);
        if ($raw === false || strlen($raw) < 29) {
            return '';
        }
        $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
        return $plain === false ? '' : $plain;
    }

    public static function isSecretKey(string $key): bool
    {
        return (bool) preg_match('/(secret|pass|token|private)/i', $key);
    }
}
