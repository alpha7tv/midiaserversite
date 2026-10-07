<?php
declare(strict_types=1);

namespace App\Core;

/** Configurações editáveis (tabela settings) com fallback para .env e padrões. */
final class Settings
{
    /** @var array<string,string>|null */
    private static ?array $cache = null;

    private const DEFAULTS = [
        'site_name'        => 'Mídia Server',
        'whatsapp_number'  => '5514988159045',
        'whmcs_base'       => 'https://cliente-area.midiaserver.com.br',
        'support_email'    => 'contato@midiaserver.com.br',
        'gtm_id'           => '',
        'ga4_id'           => '',
        'ads_id'           => '',
        'meta_pixel_id'    => '',
        'search_console'   => '',
        'youtube_studio'   => 'https://www.youtube.com/@MidiaServerStudio',
        'studio_download'  => 'https://licencas.midiaserver.com.br/downloads/MidiaRadioStudio-Setup.exe',
        'demo_pattern'     => 'http://demo{n}.164-68-121-142.sslip.io/',
        'radios_url'       => 'https://radiosdobrasil.midiaserver.com.br/',
        'conteudos_url'    => 'https://conteudospararadios.midiaserver.com.br/',
    ];

    public static function get(string $key, ?string $default = null): string
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                foreach (Db::all('SELECT `skey`, `svalue` FROM settings') as $row) {
                    self::$cache[(string) $row['skey']] = (string) $row['svalue'];
                }
            } catch (\Throwable) {
                // banco indisponível: segue com os padrões
            }
        }
        if (isset(self::$cache[$key]) && self::$cache[$key] !== '') {
            return self::$cache[$key];
        }
        $fromEnv = env(strtoupper($key));
        if ($fromEnv !== null && $fromEnv !== '') {
            return $fromEnv;
        }
        return self::DEFAULTS[$key] ?? ($default ?? '');
    }

    public static function set(string $key, string $value): void
    {
        $exists = Db::one('SELECT 1 AS x FROM settings WHERE `skey` = ?', [$key]);
        if ($exists) {
            Db::run('UPDATE settings SET `svalue` = ? WHERE `skey` = ?', [$value, $key]);
        } else {
            Db::run('INSERT INTO settings (`skey`, `svalue`) VALUES (?, ?)', [$key, $value]);
        }
        self::$cache = null;
    }

    /** Link de WhatsApp com mensagem. */
    public static function whatsapp(string $message = ''): string
    {
        $n = preg_replace('/\D+/', '', self::get('whatsapp_number'));
        $url = 'https://wa.me/' . $n;
        return $message === '' ? $url : $url . '?text=' . rawurlencode($message);
    }
}
