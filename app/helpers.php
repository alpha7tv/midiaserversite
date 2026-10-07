<?php
declare(strict_types=1);

use App\Core\Env;

function env(string $key, ?string $default = null): ?string
{
    return Env::get($key, $default);
}

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money(float $v): string
{
    return 'R$ ' . number_format($v, 2, ',', '.');
}

function app_url(string $path = ''): string
{
    $base = rtrim((string) env('APP_URL', 'http://localhost'), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $file = BASE_PATH . '/public/' . ltrim($path, '/');
    $v = is_file($file) ? substr((string) filemtime($file), -6) : '1';
    return '/' . ltrim($path, '/') . '?v=' . $v;
}

function icon(string $name, int $size = 24, string $class = 'i'): string
{
    return '<svg class="' . e($class) . '" width="' . $size . '" height="' . $size . '" aria-hidden="true" focusable="false"><use href="#i-' . e($name) . '"/></svg>';
}

/** Valor de spec: texto, ✓ ou —. */
function spec_cell(mixed $v): string
{
    if ($v === true) {
        return '<span class="yes" aria-label="Incluso">' . icon('check', 20, 'i yes-i') . '</span>';
    }
    if ($v === false) {
        return '<span class="no" aria-label="Não incluso">—</span>';
    }
    return e((string) $v);
}

function menu_href(string $href): string
{
    return match ($href) {
        'whmcs:domain' => \App\Core\Whmcs::domainRegister(),
        default => $href,
    };
}

/** Usa a versão minificada (.min) quando existir; senão, o arquivo-fonte. */
function asset_min(string $path): string
{
    $min = preg_replace('/\.(css|js)$/', '.min.$1', $path);
    return is_file(BASE_PATH . '/public/' . ltrim((string) $min, '/')) ? asset((string) $min) : asset($path);
}
