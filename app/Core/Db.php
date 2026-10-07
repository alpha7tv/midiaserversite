<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Db
{
    private static ?PDO $pdo = null;

    public static function driver(): string
    {
        return env('DB_DRIVER', 'mysql') === 'sqlite' ? 'sqlite' : 'mysql';
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $opts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            if (self::driver() === 'sqlite') {
                $file = env('DB_SQLITE', 'storage/portal.sqlite');
                if ($file[0] !== '/') {
                    $file = BASE_PATH . '/' . $file;
                }
                self::$pdo = new PDO('sqlite:' . $file, null, null, $opts);
                self::$pdo->exec('PRAGMA foreign_keys = ON');
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=utf8mb4',
                    env('DB_HOST', '127.0.0.1'),
                    env('DB_NAME', 'portal_midiaserver')
                );
                self::$pdo = new PDO($dsn, (string) env('DB_USER'), (string) env('DB_PASS'), $opts);
            }
        }
        return self::$pdo;
    }

    /** @return list<array<string,mixed>> */
    public static function all(string $sql, array $params = []): array
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public static function one(string $sql, array $params = []): ?array
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    public static function run(string $sql, array $params = []): int
    {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }
}
