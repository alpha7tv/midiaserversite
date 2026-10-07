<?php
declare(strict_types=1);

namespace App\Core;

/** Monta links de contratação do WHMCS. Nunca inventa IDs: o pid vem da tabela plans. */
final class Whmcs
{
    public static function base(): string
    {
        return rtrim(Settings::get('whmcs_base'), '/');
    }

    public static function cart(int $pid, ?string $cycle = null): string
    {
        $url = self::base() . '/cart.php?a=add&pid=' . $pid;
        if ($cycle !== null && in_array($cycle, ['monthly', 'quarterly', 'semiannually', 'annually'], true)) {
            $url .= '&billingcycle=' . $cycle;
        }
        return $url;
    }

    public static function login(): string
    {
        return self::base() . '/index.php?rp=/login';
    }

    public static function register(): string
    {
        return self::base() . '/register.php';
    }

    public static function clientArea(): string
    {
        return self::base() . '/clientarea.php';
    }

    public static function ticket(): string
    {
        return self::base() . '/submitticket.php';
    }

    public static function domainRegister(): string
    {
        return self::base() . '/cart.php?a=add&domain=register';
    }
}
