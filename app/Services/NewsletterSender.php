<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Db;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Settings;

/** Envio de campanhas em lotes, somente para inscritos que CONFIRMARAM. */
final class NewsletterSender
{
    public static function wrap(string $bodyHtml, string $token): string
    {
        $unsub = app_url('/newsletter/sair?t=' . $token);
        $footer = e(Settings::get('newsletter_footer'));
        return '<!doctype html><html lang="pt-BR"><body style="margin:0;background:#f4f6fa;font-family:Arial,Helvetica,sans-serif;color:#101828">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:24px 12px">'
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:14px;overflow:hidden">'
            . '<tr><td style="background:#0b1424;padding:18px 24px"><a href="' . e(app_url('')) . '"><img src="' . e(app_url('/assets/img/logo-horizontal-dark.png')) . '" alt="Mídia Server" height="34" style="display:block;height:34px"></a></td></tr>'
            . '<tr><td style="padding:24px;font-size:16px;line-height:1.6">' . $bodyHtml . '</td></tr>'
            . '<tr><td style="padding:16px 24px;background:#f0f3f8;font-size:12.5px;color:#5b667d;line-height:1.5">' . $footer
            . '<br><a href="' . e($unsub) . '" style="color:#2e6bff">Cancelar inscrição</a> · <a href="' . e(app_url('/politica-de-privacidade')) . '" style="color:#2e6bff">Privacidade</a></td></tr>'
            . '</table></td></tr></table></body></html>';
    }

    /** @return array{sent:int,failed:int,done:bool} */
    public static function runBatch(int $limit = 50): array
    {
        $sent = 0; $failed = 0; $done = true;
        foreach (Db::all("SELECT * FROM campaigns WHERE status = 'sending' ORDER BY id LIMIT 1") as $c) {
            $subs = Db::all("SELECT id, email, token FROM newsletter_subscribers WHERE status = 'confirmed' AND id > ? ORDER BY id LIMIT " . (int) $limit, [(int) $c['last_id']]);
            foreach ($subs as $s) {
                $r = Mailer::send(
                    (string) $s['email'], (string) $c['subject'], self::wrap((string) $c['body_html'], (string) $s['token']), '',
                    ['List-Unsubscribe' => '<' . app_url('/newsletter/sair?t=' . $s['token']) . '>', 'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click']
                );
                $r['ok'] ? $sent++ : $failed++;
                Db::run('UPDATE campaigns SET last_id = ?, sent_count = sent_count + ?, failed_count = failed_count + ? WHERE id = ?', [$s['id'], $r['ok'] ? 1 : 0, $r['ok'] ? 0 : 1, $c['id']]);
                usleep(150000);
            }
            if (count($subs) < $limit) {
                Db::run("UPDATE campaigns SET status = 'sent', finished_at = ? WHERE id = ?", [date('Y-m-d H:i:s'), $c['id']]);
                Logger::write('newsletter', 'campanha_concluida', ['id' => $c['id']]);
            } else {
                $done = false;
            }
        }
        return ['sent' => $sent, 'failed' => $failed, 'done' => $done];
    }
}
