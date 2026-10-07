<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Kernel;
use App\Core\Db;
use App\Core\Mailer;
use App\Core\Settings;

final class Dashboard
{
    public static function index(): void
    {
        $n = static fn (string $sql, array $p = []): int => (int) (Db::one($sql, $p)['n'] ?? 0);
        $week = date('Y-m-d H:i:s', time() - 7 * 86400);
        $stats = [
            'Leads novos' => $n("SELECT COUNT(*) n FROM leads WHERE status = 'new'"),
            'Leads (7 dias)' => $n('SELECT COUNT(*) n FROM leads WHERE created_at > ?', [$week]),
            'Downloads (total)' => $n('SELECT COALESCE(SUM(download_count),0) n FROM downloads'),
            'Inscritos na newsletter' => $n("SELECT COUNT(*) n FROM newsletter_subscribers WHERE status = 'confirmed'"),
            'Planos ativos' => $n('SELECT COUNT(*) n FROM plans WHERE active = 1'),
            'Artigos publicados' => $n("SELECT COUNT(*) n FROM posts WHERE status = 'published'"),
        ];
        $alerts = [];
        if (env('APP_ENV', 'production') !== 'production') {
            $alerts[] = ['warn', 'O site está em modo de teste (APP_ENV=' . env('APP_ENV') . '): buscadores são bloqueados.'];
        }
        if (Settings::get('ads_id') === '' && Settings::get('gtm_id') === '' && Settings::get('ga4_id') === '') {
            $alerts[] = ['warn', 'Medição desligada: preencha Google Ads, GA4 ou GTM em Configurações para registrar conversões.'];
        }
        if (!Mailer::configured()) {
            $alerts[] = ['warn', 'SMTP não configurado: a newsletter fica desligada e o formulário não aparece no site.'];
        }
        if (Settings::get('turnstile_secret') === '') {
            $alerts[] = ['warn', 'Turnstile desligado: os formulários usam apenas as proteções internas (CSRF, campo isca, tempo mínimo e limite por IP).'];
        }
        if (Settings::get('lead_webhook_url') === '') {
            $alerts[] = ['warn', 'Sem webhook de leads: você só vê novos contatos aqui no painel. Configure para receber aviso.'];
        }
        $leads = Db::all('SELECT id, created_at, name, phone, interest, page, status FROM leads ORDER BY id DESC LIMIT 6');
        $audit = Db::all('SELECT a.created_at, a.action, a.entity, u.name FROM audit_logs a LEFT JOIN admins u ON u.id = a.admin_id ORDER BY a.id DESC LIMIT 6');
        Kernel::view('dashboard', compact('stats', 'alerts', 'leads', 'audit'), 'Visão geral');
    }
}
