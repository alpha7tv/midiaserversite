<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);

spl_autoload_register(static function (string $class): void {
    $path = BASE_PATH . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

require BASE_PATH . '/app/helpers.php';

App\Core\Env::load(BASE_PATH . '/.env');
date_default_timezone_set('America/Sao_Paulo');
mb_internal_encoding('UTF-8');

if (!env('APP_DEBUG')) {
    ini_set('display_errors', '0');
}
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
