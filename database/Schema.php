<?php
declare(strict_types=1);

/**
 * Esquema do banco. Gera DDL para MySQL (produção) e SQLite (testes locais).
 * Todas as tabelas pedidas no escopo + tabelas de apoio (integration_logs, redirects, downloads_log).
 */
final class Schema
{
    /** @return list<string> */
    public static function statements(string $driver): array
    {
        $pk   = $driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY';
        $ts   = $driver === 'sqlite' ? "TEXT NOT NULL DEFAULT (datetime('now'))" : 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP';
        $txt  = $driver === 'sqlite' ? 'TEXT' : 'MEDIUMTEXT';
        $eng  = $driver === 'sqlite' ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        $int  = $driver === 'sqlite' ? 'INTEGER' : 'INT';

        $t = [];

        $t[] = "CREATE TABLE IF NOT EXISTS migrations (id $pk, name VARCHAR(190) NOT NULL UNIQUE, applied_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS settings (
            id $pk, `skey` VARCHAR(100) NOT NULL UNIQUE, `svalue` $txt, updated_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS categories (
            id $pk, slug VARCHAR(100) NOT NULL UNIQUE, name VARCHAR(150) NOT NULL,
            type VARCHAR(30) NOT NULL DEFAULT 'product', sort_order $int NOT NULL DEFAULT 0)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS products (
            id $pk, slug VARCHAR(100) NOT NULL UNIQUE, name VARCHAR(190) NOT NULL,
            category_id $int NULL, whmcs_group VARCHAR(100) NULL, description $txt,
            active $int NOT NULL DEFAULT 1, sort_order $int NOT NULL DEFAULT 0, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS plans (
            id $pk, product_id $int NOT NULL, whmcs_pid $int NOT NULL, name VARCHAR(190) NOT NULL,
            price_month DECIMAL(10,2) NOT NULL, price_year DECIMAL(10,2) NULL,
            highlight $int NOT NULL DEFAULT 0, includes_site $int NOT NULL DEFAULT 0,
            specs $txt, features $txt, cta_label VARCHAR(80) NULL,
            sort_order $int NOT NULL DEFAULT 0, active $int NOT NULL DEFAULT 1,
            synced_at TIMESTAMP NULL, updated_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS pages (
            id $pk, slug VARCHAR(150) NOT NULL UNIQUE, title VARCHAR(190) NOT NULL, body $txt,
            template VARCHAR(60) NOT NULL DEFAULT 'default', published $int NOT NULL DEFAULT 1, updated_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS seo_metadata (
            id $pk, entity_type VARCHAR(40) NOT NULL, entity_id $int NOT NULL,
            title VARCHAR(190) NULL, description VARCHAR(320) NULL, canonical VARCHAR(300) NULL,
            og_image VARCHAR(300) NULL, robots VARCHAR(60) NULL, schema_json $txt, keywords $txt)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS banners (
            id $pk, position VARCHAR(60) NOT NULL, image VARCHAR(300) NULL, title VARCHAR(190) NULL,
            link VARCHAR(300) NULL, starts_at TIMESTAMP NULL, ends_at TIMESTAMP NULL,
            active $int NOT NULL DEFAULT 1)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS downloads (
            id $pk, category_id $int NULL, slug VARCHAR(120) NOT NULL UNIQUE, name VARCHAR(190) NOT NULL,
            version VARCHAR(40) NULL, description $txt, os VARCHAR(120) NULL, size_bytes BIGINT NULL,
            file_url VARCHAR(400) NULL, logo VARCHAR(300) NULL, license_note VARCHAR(300) NULL,
            external INT NOT NULL DEFAULT 0, download_count $int NOT NULL DEFAULT 0,
            released_at TIMESTAMP NULL, active $int NOT NULL DEFAULT 1)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS downloads_log (
            id $pk, download_id $int NOT NULL, ip_hash CHAR(64) NOT NULL, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS software_versions (
            id $pk, software_slug VARCHAR(80) NOT NULL, version VARCHAR(40) NOT NULL,
            released_at TIMESTAMP NULL, notes $txt, fixes $txt, download_url VARCHAR(400) NULL,
            published $int NOT NULL DEFAULT 0, notify_site $int NOT NULL DEFAULT 0,
            notify_newsletter $int NOT NULL DEFAULT 0, notify_push $int NOT NULL DEFAULT 0, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS radio_categories (
            id $pk, slug VARCHAR(100) NOT NULL UNIQUE, name VARCHAR(150) NOT NULL)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS radios (
            id $pk, remote_id VARCHAR(80) NULL, slug VARCHAR(190) NOT NULL UNIQUE, name VARCHAR(190) NOT NULL,
            logo VARCHAR(300) NULL, description $txt, city VARCHAR(120) NULL, state VARCHAR(4) NULL,
            country VARCHAR(80) NULL, genre VARCHAR(120) NULL, stream_url VARCHAR(400) NULL, site VARCHAR(300) NULL,
            social $txt, status VARCHAR(20) NOT NULL DEFAULT 'pending', source VARCHAR(40) NULL, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS radio_highlights (
            id $pk, radio_id $int NOT NULL, slot VARCHAR(40) NOT NULL, position $int NOT NULL DEFAULT 0,
            sponsored $int NOT NULL DEFAULT 0, starts_at TIMESTAMP NULL, ends_at TIMESTAMP NULL)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id $pk, email VARCHAR(190) NOT NULL UNIQUE, status VARCHAR(20) NOT NULL DEFAULT 'pending',
            token VARCHAR(64) NOT NULL, consent_text VARCHAR(500) NOT NULL, consent_at TIMESTAMP NULL,
            consent_ip_hash CHAR(64) NULL, source VARCHAR(80) NULL, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS push_subscriptions (
            id $pk, endpoint VARCHAR(600) NOT NULL, p256dh VARCHAR(200) NOT NULL, auth VARCHAR(100) NOT NULL,
            whmcs_client_ref VARCHAR(60) NULL, consent_at $ts, active $int NOT NULL DEFAULT 1)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS posts (
            id $pk, slug VARCHAR(190) NOT NULL UNIQUE, title VARCHAR(190) NOT NULL, excerpt VARCHAR(400) NULL,
            body $txt, category_id $int NULL, cover VARCHAR(300) NULL, og_image VARCHAR(300) NULL,
            published_at TIMESTAMP NULL, status VARCHAR(20) NOT NULL DEFAULT 'draft')$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS admins (
            id $pk, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL, role VARCHAR(30) NOT NULL DEFAULT 'admin',
            last_login_at TIMESTAMP NULL, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS audit_logs (
            id $pk, admin_id $int NULL, action VARCHAR(80) NOT NULL, entity VARCHAR(80) NULL,
            payload $txt, ip_hash CHAR(64) NULL, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS integration_logs (
            id $pk, source VARCHAR(40) NOT NULL, level VARCHAR(20) NOT NULL DEFAULT 'info',
            message VARCHAR(400) NOT NULL, payload $txt, created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS redirects (
            id $pk, from_path VARCHAR(300) NOT NULL UNIQUE, to_path VARCHAR(300) NOT NULL,
            code $int NOT NULL DEFAULT 301, hits $int NOT NULL DEFAULT 0)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS leads (
            id $pk, name VARCHAR(120) NOT NULL, phone VARCHAR(20) NOT NULL, interest VARCHAR(80) NULL,
            page VARCHAR(160) NULL, attribution $txt, ip_hash CHAR(64) NOT NULL, consent_text VARCHAR(300) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'new', created_at $ts)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS campaigns (
            id $pk, subject VARCHAR(200) NOT NULL, body_html $txt, body_text $txt, source VARCHAR(30) NOT NULL DEFAULT 'manual',
            status VARCHAR(20) NOT NULL DEFAULT 'draft', last_id $int NOT NULL DEFAULT 0, total_count $int NOT NULL DEFAULT 0,
            sent_count $int NOT NULL DEFAULT 0, failed_count $int NOT NULL DEFAULT 0, created_by $int NULL,
            created_at $ts, started_at TIMESTAMP NULL, finished_at TIMESTAMP NULL)$eng";

        $t[] = "CREATE TABLE IF NOT EXISTS login_attempts (
            id $pk, ip_hash CHAR(64) NOT NULL, email_hash CHAR(64) NOT NULL, success $int NOT NULL DEFAULT 0, created_at $ts)$eng";

        return $t;
    }

    /** Alterações incrementais (executadas uma única vez cada, registradas em migrations). @return array<string,string> */
    public static function alters(): array
    {
        return [
            '001_posts_kind'            => 'ALTER TABLE posts ADD COLUMN kind VARCHAR(30) NULL',
            '002_posts_notify_push'     => 'ALTER TABLE posts ADD COLUMN notify_push INTEGER NOT NULL DEFAULT 0',
            '003_posts_notify_newsletter' => 'ALTER TABLE posts ADD COLUMN notify_newsletter INTEGER NOT NULL DEFAULT 0',
            '004_subscribers_confirmed_at' => 'ALTER TABLE newsletter_subscribers ADD COLUMN confirmed_at TIMESTAMP NULL',
            '005_versions_campaign'     => 'ALTER TABLE software_versions ADD COLUMN campaign_id INTEGER NULL',
        ];
    }
}
