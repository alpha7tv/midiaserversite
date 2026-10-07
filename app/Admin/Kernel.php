<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Db;
use App\Core\View;

/** Roteador do painel (/admin/*): autenticação, CSRF em todo POST e cabeçalhos de segurança. */
final class Kernel
{
    /** @return array<string,array{0:class-string,1:string}> */
    private static function routes(): array
    {
        return [
            'GET /admin'                    => [Controllers\Dashboard::class, 'index'],
            'GET /admin/configuracoes'      => [Controllers\SettingsCtl::class, 'index'],
            'POST /admin/configuracoes'     => [Controllers\SettingsCtl::class, 'save'],
            'POST /admin/configuracoes/teste-smtp' => [Controllers\SettingsCtl::class, 'testSmtp'],
            'GET /admin/planos'             => [Controllers\Plans::class, 'index'],
            'POST /admin/planos'            => [Controllers\Plans::class, 'save'],
            'GET /admin/planos/whmcs'       => [Controllers\Plans::class, 'sync'],
            'POST /admin/planos/aplicar'    => [Controllers\Plans::class, 'apply'],
            'GET /admin/leads'              => [Controllers\Leads::class, 'index'],
            'POST /admin/leads/status'      => [Controllers\Leads::class, 'status'],
            'POST /admin/leads/excluir'     => [Controllers\Leads::class, 'delete'],
            'GET /admin/leads/exportar'     => [Controllers\Leads::class, 'export'],
            'GET /admin/downloads'          => [Controllers\Downloads::class, 'index'],
            'GET /admin/downloads/editar'   => [Controllers\Downloads::class, 'edit'],
            'POST /admin/downloads/editar'  => [Controllers\Downloads::class, 'save'],
            'GET /admin/versoes'            => [Controllers\Versions::class, 'index'],
            'GET /admin/versoes/editar'     => [Controllers\Versions::class, 'edit'],
            'POST /admin/versoes/editar'    => [Controllers\Versions::class, 'save'],
            'GET /admin/redirects'          => [Controllers\Redirects::class, 'index'],
            'POST /admin/redirects'         => [Controllers\Redirects::class, 'save'],
            'POST /admin/redirects/excluir' => [Controllers\Redirects::class, 'delete'],
            'GET /admin/publicar'           => [Controllers\Posts::class, 'index'],
            'GET /admin/publicar/editar'    => [Controllers\Posts::class, 'edit'],
            'POST /admin/publicar/editar'   => [Controllers\Posts::class, 'save'],
            'GET /admin/newsletter'         => [Controllers\Newsletter::class, 'index'],
            'POST /admin/newsletter/campanha' => [Controllers\Newsletter::class, 'saveCampaign'],
            'GET /admin/newsletter/campanha'  => [Controllers\Newsletter::class, 'campaign'],
            'POST /admin/newsletter/teste'    => [Controllers\Newsletter::class, 'test'],
            'POST /admin/newsletter/enviar'   => [Controllers\Newsletter::class, 'send'],
            'POST /admin/newsletter/lote'     => [Controllers\Newsletter::class, 'batch'],
            'GET /admin/newsletter/exportar'  => [Controllers\Newsletter::class, 'export'],
            'POST /admin/newsletter/remover'  => [Controllers\Newsletter::class, 'remove'],
            'GET /admin/logs'               => [Controllers\Logs::class, 'index'],
            'GET /admin/conta'              => [Controllers\Account::class, 'index'],
            'POST /admin/conta'             => [Controllers\Account::class, 'save'],
        ];
    }

    public static function dispatch(string $uri, string $method): void
    {
        header('Cache-Control: no-store');
        header('X-Robots-Tag: noindex, nofollow');
        header('X-Frame-Options: DENY');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline'; script-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
        Session::start();

        $path = rtrim($uri, '/') ?: '/admin';
        if ($path === '/admin/login') {
            Controllers\Login::handle($method);
            return;
        }
        if ($path === '/admin/sair' && $method === 'POST') {
            if (Session::csrfOk((string) ($_POST['csrf'] ?? ''))) {
                Audit::log('logout');
                Session::logout();
            }
            self::redirect('/admin/login');
        }
        if (!Session::user()) {
            self::redirect('/admin/login');
        }
        $route = self::routes()[$method . ' ' . $path] ?? null;
        if (!$route) {
            http_response_code(404);
            self::page('Página não encontrada', '<p>Esta página do painel não existe.</p>');
            return;
        }
        if ($method === 'POST' && !Session::csrfOk((string) ($_POST['csrf'] ?? ''))) {
            http_response_code(403);
            self::page('Sessão expirada', '<p>O formulário expirou. Volte, recarregue a página e tente de novo.</p>');
            return;
        }
        try {
            [$class, $fn] = $route;
            $class::$fn();
        } catch (\Throwable $e) {
            \App\Core\Logger::write('admin', 'erro', ['path' => $path, 'error' => $e->getMessage()]);
            http_response_code(500);
            self::page('Erro', '<p>Não foi possível concluir a ação. O erro foi registrado em <code>storage/logs/admin.log</code>.</p>');
        }
    }

    public static function redirect(string $to): never
    {
        header('Location: ' . $to, true, 303);
        exit;
    }

    /** Renderiza uma view de admin dentro do layout. @param array<string,mixed> $data */
    public static function view(string $view, array $data = [], string $title = 'Painel'): void
    {
        $data['title'] = $title;
        echo View::render('admin/layout', ['content' => View::render('admin/' . $view, $data), 'title' => $title]);
    }

    public static function page(string $title, string $html): void
    {
        echo View::render('admin/layout', ['content' => '<h1>' . e($title) . '</h1>' . $html, 'title' => $title]);
    }
}
