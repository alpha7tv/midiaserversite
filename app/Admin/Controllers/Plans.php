<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;
use App\Services\WhmcsSync;

final class Plans
{
    public static function index(): void
    {
        $rows = Db::all('SELECT pl.*, p.name AS product, p.slug AS product_slug FROM plans pl JOIN products p ON p.id = pl.product_id ORDER BY p.sort_order, pl.sort_order, pl.id');
        $groups = [];
        foreach ($rows as $r) {
            $groups[(string) $r['product']][] = $r;
        }
        Kernel::view('plans', ['groups' => $groups], 'Planos e preços');
    }

    public static function save(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $pl = Db::one('SELECT * FROM plans WHERE id = ?', [$id]);
        $price = str_replace(',', '.', trim((string) ($_POST['price'] ?? '')));
        $name = trim((string) ($_POST['name'] ?? ''));
        if (!$pl || $name === '' || mb_strlen($name) > 120 || !is_numeric($price) || (float) $price < 0 || (float) $price > 99999) {
            Session::flash('err', 'Dados inválidos para o plano.');
            Kernel::redirect('/admin/planos');
        }
        $new = ['name' => $name, 'price_month' => round((float) $price, 2), 'highlight' => empty($_POST['highlight']) ? 0 : 1, 'active' => empty($_POST['active']) ? 0 : 1];
        Db::run('UPDATE plans SET name = ?, price_month = ?, highlight = ?, active = ? WHERE id = ?', [$new['name'], $new['price_month'], $new['highlight'], $new['active'], $id]);
        Audit::log('plano_editado', 'plans#' . $id, ['pid' => $pl['whmcs_pid'], 'de' => ['preco' => (float) $pl['price_month'], 'nome' => $pl['name'], 'ativo' => (int) $pl['active']], 'para' => $new]);
        Session::flash('ok', 'Plano salvo: ' . $name . '.');
        Kernel::redirect('/admin/planos#p' . $id);
    }

    public static function sync(): void
    {
        $d = WhmcsSync::diff();
        Kernel::view('plans_sync', $d, 'Conferir com o WHMCS');
    }

    /** Aplica preço e nome da vitrine do WHMCS a UM plano (ação explícita do administrador). */
    public static function apply(): void
    {
        $pid = (int) ($_POST['pid'] ?? 0);
        $remote = WhmcsSync::fetchRemote()[$pid] ?? null;
        $pl = Db::one('SELECT * FROM plans WHERE whmcs_pid = ?', [$pid]);
        if (!$remote || !$pl || $remote['price'] === null) {
            Session::flash('err', 'Não foi possível ler esse plano no WHMCS agora.');
        } else {
            Db::run('UPDATE plans SET price_month = ?, name = ?, synced_at = ? WHERE id = ?', [$remote['price'], $remote['name'] ?: $pl['name'], date('Y-m-d H:i:s'), $pl['id']]);
            Audit::log('plano_sincronizado', 'plans#' . $pl['id'], ['pid' => $pid, 'preco_antigo' => (float) $pl['price_month'], 'preco_novo' => $remote['price']]);
            Session::flash('ok', 'Plano ' . $pid . ' atualizado com os dados do WHMCS.');
        }
        Kernel::redirect('/admin/planos/whmcs');
    }
}
