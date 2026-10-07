<?php
declare(strict_types=1);

namespace App\Core;

/** Converte um subconjunto seguro de Markdown em HTML. Tudo é escapado primeiro (sem HTML bruto, sem XSS). */
final class Markup
{
    public static function toHtml(string $src): string
    {
        $src = str_replace(["\r\n", "\r"], "\n", trim($src));
        $blocks = preg_split('/\n{2,}/', $src) ?: [];
        $out = [];
        foreach ($blocks as $b) {
            $b = trim($b);
            if ($b === '') {
                continue;
            }
            if (preg_match('/^(#{2,3})\s+(.+)$/s', $b, $m)) {
                $n = strlen($m[1]);
                $out[] = "<h$n>" . self::inline($m[2]) . "</h$n>";
            } elseif (preg_match('/^(?:[-*]\s+.+(?:\n|$))+$/', $b . "\n")) {
                $items = array_map(static fn (string $l): string => '<li>' . self::inline(preg_replace('/^[-*]\s+/', '', $l) ?? $l) . '</li>', explode("\n", $b));
                $out[] = '<ul>' . implode('', $items) . '</ul>';
            } else {
                $out[] = '<p>' . nl2br(self::inline($b), false) . '</p>';
            }
        }
        return implode("\n", $out);
    }

    private static function inline(string $s): string
    {
        $s = e($s);
        $s = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $s) ?? $s;
        $s = preg_replace('/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/s', '<em>$1</em>', $s) ?? $s;
        // links [texto](https://...): só http(s) e caminhos internos
        $s = preg_replace_callback('/\[([^\]]+)\]\((https?:\/\/[^\s)]+|\/[^\s)]*)\)/', static function (array $m): string {
            $url = $m[2];
            $ext = str_starts_with($url, 'http');
            return '<a href="' . $url . '"' . ($ext ? ' target="_blank" rel="noopener nofollow"' : '') . '>' . $m[1] . '</a>';
        }, $s) ?? $s;
        return $s;
    }

    public static function slug(string $s): string
    {
        $s = mb_strtolower(trim($s));
        $s = strtr($s, ['á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'é' => 'e', 'ê' => 'e', 'è' => 'e', 'í' => 'i', 'ï' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ' => 'n']);
        $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? $s;
        return trim(mb_substr($s, 0, 80), '-') ?: 'post';
    }
}
