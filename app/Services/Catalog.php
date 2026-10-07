<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Db;

/** Leitura do catálogo (produtos e planos) gravado no banco. */
final class Catalog
{
    /** @return list<array<string,mixed>> */
    public static function plans(string $productSlug): array
    {
        $rows = Db::all(
            'SELECT pl.* FROM plans pl JOIN products p ON p.id = pl.product_id
             WHERE p.slug = ? AND pl.active = 1 AND p.active = 1 ORDER BY pl.sort_order, pl.id',
            [$productSlug]
        );
        foreach ($rows as &$r) {
            $r['specs'] = json_decode((string) $r['specs'], true) ?: [];
            $r['price_month'] = (float) $r['price_month'];
            $r['price_year'] = $r['price_year'] !== null ? (float) $r['price_year'] : null;
        }
        return $rows;
    }

    public static function minPrice(string $productSlug): ?float
    {
        $row = Db::one(
            'SELECT MIN(pl.price_month) AS m FROM plans pl JOIN products p ON p.id = pl.product_id
             WHERE p.slug = ? AND pl.active = 1',
            [$productSlug]
        );
        return $row && $row['m'] !== null ? (float) $row['m'] : null;
    }

    /**
     * Linhas da tabela comparativa: união ordenada dos recursos de todos os planos.
     * @param list<array<string,mixed>> $plans
     * @return list<string>
     */
    public static function specLabels(array $plans): array
    {
        $labels = [];
        foreach ($plans as $p) {
            foreach (array_keys($p['specs']) as $l) {
                $labels[$l] = true;
            }
        }
        return array_keys($labels);
    }
}
