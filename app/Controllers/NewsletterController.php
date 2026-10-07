<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Db;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Settings;
use App\Core\View;

/** Inscrição com dupla confirmação (LGPD): ninguém recebe e-mail sem confirmar. */
final class NewsletterController
{
    private const CONSENT = 'Quero receber novidades, promoções e ferramentas para rádios da Mídia Server por e-mail. Posso cancelar quando quiser.';

    public static function subscribe(): void
    {
        $in = static fn (string $k, int $max = 200): string => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);
        if (!Mailer::configured()) {
            self::msg('Newsletter indisponível', 'Esta função ainda não está ativa. Volte em breve.', 503);
        }
        if ($in('website') !== '') {
            self::msg('Confira seu e-mail', 'Se o endereço estiver correto, enviamos uma mensagem para confirmar a inscrição.');
        }
        if (!Csrf::stampOk($in('ts', 12), $in('sig', 80), 2)) {
            self::msg('Tente de novo', 'O formulário expirou. Volte e envie novamente.', 400);
        }
        $email = mb_strtolower($in('email', 190));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::msg('E-mail inválido', 'Confira o endereço e tente de novo.', 422);
        }
        $ip = Csrf::ipHash();
        $recent = Db::one('SELECT COUNT(*) n FROM newsletter_subscribers WHERE consent_ip_hash = ? AND created_at > ?', [$ip, date('Y-m-d H:i:s', time() - 3600)]);
        if ((int) ($recent['n'] ?? 0) >= 5) {
            self::msg('Muitas tentativas', 'Tente novamente mais tarde.', 429);
        }
        $row = Db::one('SELECT * FROM newsletter_subscribers WHERE email = ?', [$email]);
        $token = bin2hex(random_bytes(24));
        $send = true;
        if (!$row) {
            Db::run('INSERT INTO newsletter_subscribers (email, status, token, consent_text, consent_ip_hash, source) VALUES (?,?,?,?,?,?)', [$email, 'pending', $token, self::CONSENT, $ip, mb_substr($in('source', 60), 0, 60)]);
        } elseif ($row['status'] === 'confirmed') {
            $send = false;                                   // sem revelar que já está inscrito
        } else {
            $recentMail = strtotime((string) $row['created_at']) > time() - 600 && $row['status'] === 'pending';
            if ($recentMail) {
                $send = false;
            } else {
                Db::run("UPDATE newsletter_subscribers SET status = 'pending', token = ?, consent_text = ?, consent_ip_hash = ?, created_at = ? WHERE id = ?", [$token, self::CONSENT, $ip, date('Y-m-d H:i:s'), $row['id']]);
            }
        }
        if ($send) {
            $link = app_url('/newsletter/confirmar?t=' . $token);
            $html = '<p>Olá!</p><p>Recebemos um pedido para inscrever este e-mail na newsletter da Mídia Server (novidades, promoções e ferramentas para rádios).</p>'
                . '<p><a href="' . e($link) . '" style="display:inline-block;background:#2e6bff;color:#fff;padding:12px 20px;border-radius:10px;text-decoration:none;font-weight:bold">Confirmar minha inscrição</a></p>'
                . '<p style="font-size:13px;color:#5b667d">Se você não pediu, ignore esta mensagem: nada será enviado sem a sua confirmação.</p>';
            $r = Mailer::send($email, 'Confirme sua inscrição na newsletter da Mídia Server', \App\Services\NewsletterSender::wrap($html, $token));
            Logger::write('newsletter', 'confirmacao', ['ok' => $r['ok']]);
        }
        self::msg('Confira seu e-mail', 'Se o endereço estiver correto, enviamos uma mensagem com um link para confirmar a inscrição. Ela só vale depois da confirmação.');
    }

    public static function confirm(): void
    {
        $t = (string) ($_GET['t'] ?? '');
        $row = preg_match('/^[a-f0-9]{48}$/', $t) ? Db::one('SELECT id, status FROM newsletter_subscribers WHERE token = ?', [$t]) : null;
        if (!$row) {
            self::msg('Link inválido', 'Este link de confirmação não é válido ou já foi usado.', 404);
        }
        Db::run("UPDATE newsletter_subscribers SET status = 'confirmed', confirmed_at = ? WHERE id = ?", [date('Y-m-d H:i:s'), $row['id']]);
        self::msg('Inscrição confirmada', 'Pronto! A partir de agora você recebe as novidades da Mídia Server. Você pode cancelar quando quiser pelo link no final de cada e-mail.');
    }

    public static function unsubscribe(string $method): void
    {
        $t = (string) ($_REQUEST['t'] ?? '');
        $row = preg_match('/^[a-f0-9]{48}$/', $t) ? Db::one('SELECT id, status FROM newsletter_subscribers WHERE token = ?', [$t]) : null;
        if (!$row) {
            self::msg('Link inválido', 'Este link não é válido.', 404);
        }
        if ($method === 'POST') {                               // também atende o "um clique" do cabeçalho List-Unsubscribe-Post
            Db::run("UPDATE newsletter_subscribers SET status = 'unsubscribed' WHERE id = ?", [$row['id']]);
            self::msg('Inscrição cancelada', 'Você não receberá mais nossos e-mails. Se foi engano, é só se inscrever de novo no rodapé do site.');
        }
        $page = self::page('Cancelar inscrição');
        $body = View::render('pages/newsletter_unsub', ['page' => $page, 'token' => $t]);
        echo View::render('layout', ['page' => $page, 'content' => $body]);
        exit;
    }

    private static function page(string $title): array
    {
        return ['path' => '/newsletter', 'template' => 'newsletter_msg', 'og' => 'home', 'noindex' => true, 'title' => $title . ' | Mídia Server', 'description' => $title, 'h1' => $title, 'crumb' => $title, 'wa' => 'Olá, tenho uma dúvida sobre a newsletter.'];
    }

    private static function msg(string $title, string $text, int $code = 200): never
    {
        http_response_code($code);
        $page = self::page($title);
        $body = View::render('pages/newsletter_msg', ['page' => $page, 'text' => $text]);
        echo View::render('layout', ['page' => $page, 'content' => $body]);
        exit;
    }
}
