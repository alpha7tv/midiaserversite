<?php
// Roteador para o servidor embutido do PHP (somente testes locais).
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/../public' . $path;
if ($path !== '/' && is_file($file) && !str_ends_with($file, '.php')) {
    return false;
}
require __DIR__ . '/../public/index.php';
