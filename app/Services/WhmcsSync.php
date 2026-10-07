<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Db;
use App\Core\Logger;
use App\Core\Settings;

/** Lê a vitrine pública do WHMCS e compara com o catálogo local. Nunca altera nada sozinho. */
final class WhmcsSync
{
    /** @return array<int,array{pid:int,name:string,price:?float,cycle:string,group:string}> */
    public static function fetchRemote(): array
    {
        $out = [];
        $base = rtrim(Settings::get('whmcs_base'), '/');
        foreach (Db::all('SELECT DISTINCT whmcs_group FROM products WHERE whmcs_group IS NOT NULL AND active = 1') as $g) {
            $group = (string) $g['whmcs_group'];
            $html = self::get($base . '/index.php?rp=/store/' . rawurlencode($group));
            if ($html === null) {
                Logger::write('whmcs', 'falha_vitrine', ['group' => $group]);
                continue;
            }
            $parts = preg_split('/<div class="product clearfix" id="product(\d+)">/', $html, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
            for ($i = 1; $i < count($parts); $i += 2) {
                $pid = (int) $parts[$i];
                $b = explode('</footer>', (string) $parts[$i + 1])[0];
                preg_match('#id="product\d+-name">(.*?)</span>#s', $b, $n);
                preg_match('#<span class="price">\s*R\$\s*([\d.,]+)\s*</span>(.*?)</div>#s', $b, $p);
                $price = isset($p[1]) ? (float) str_replace(['.', ','], ['', '.'], $p[1]) : null;
                $cycle = isset($p[2]) ? trim(html_entity_decode(strip_tags($p[2]))) : '';
                $out[$pid] = ['pid' => $pid, 'name' => trim(html_entity_decode(strip_tags($n[1] ?? ''))), 'price' => $price, 'cycle' => $cycle, 'group' => $group];
            }
        }
        return $out;
    }

    /**
     * @return array{rows:list<array<string,mixed>>,missing_local:list<array<string,mixed>>,remote_ok:bool}
     */
    public static function diff(): array
    {
        $remote = self::fetchRemote();
        $rows = [];
        foreach (Db::all('SELECT pl.id, pl.whmcs_pid, pl.name, pl.price_month, p.name AS product FROM plans pl JOIN products p ON p.id = pl.product_id ORDER BY p.sort_order, pl.sort_order') as $pl) {
            $r = $remote[(int) $pl['whmcs_pid']] ?? null;
            $status = 'ok';
            if (!$r) {
                $status = 'ausente';
            } elseif ($r['price'] === null) {
                $status = 'sem_preco';
            } elseif (abs($r['price'] - (float) $pl['price_month']) > 0.001) {
                $status = 'preco';
            } elseif ($r['name'] !== '' && $r['name'] !== $pl['name']) {
                $status = 'nome';
            }
            $rows[] = ['plan' => $pl, 'remote' => $r, 'status' => $status];
        }
        $known = array_map(static fn (array $x): int => (int) $x['plan']['whmcs_pid'], $rows);
        $missing = array_values(array_filter($remote, static fn (array $r): bool => !in_array($r['pid'], $known, true)));
        return ['rows' => $rows, 'missing_local' => $missing, 'remote_ok' => (bool) $remote];
    }

    private static function get(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 12, CURLOPT_CONNECTTIMEOUT => 6, CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 3, CURLOPT_USERAGENT => 'MidiaServerPortal/1.0']);
        $res = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code === 200 && is_string($res) ? $res : null;
    }
}
