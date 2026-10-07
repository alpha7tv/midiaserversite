<?php
declare(strict_types=1);

namespace App\Core;

final class Logger
{
    public static function write(string $channel, string $message, array $context = []): void
    {
        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('c'),
            $channel,
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ''
        );
        @file_put_contents(BASE_PATH . '/storage/logs/' . preg_replace('/[^a-z0-9_-]/i', '', $channel) . '.log', $line, FILE_APPEND | LOCK_EX);
    }
}
