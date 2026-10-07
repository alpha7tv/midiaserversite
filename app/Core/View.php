<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    /** @param array<string,mixed> $data */
    public static function render(string $view, array $data = []): string
    {
        $file = BASE_PATH . '/views/' . $view . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('View não encontrada: ' . $view);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    /** @param array<string,mixed> $data */
    public static function partial(string $name, array $data = []): void
    {
        echo self::render('partials/' . $name, $data);
    }
}
