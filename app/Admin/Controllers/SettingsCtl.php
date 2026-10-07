<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Mailer;
use App\Core\Settings;

final class SettingsCtl
{
    public static function index(): void
    {
        $schema = require BASE_PATH . '/config/settings_schema.php';
        $stored = Settings::allStored();
        Kernel::view('settings', ['schema' => $schema, 'stored' => $stored], 'Configurações');
    }

    public static function save(): void
    {
        $schema = require BASE_PATH . '/config/settings_schema.php';
        $changed = [];
        $errors = [];
        foreach ($schema as $fields) {
            foreach ($fields as [$key, $label, $type, , $pattern, $msg]) {
                if (!array_key_exists($key, $_POST) && empty($_POST['clear_' . $key])) {
                    continue;
                }
                $isSecret = $type === 'secret';
                $new = trim((string) ($_POST[$key] ?? ''));
                if (!empty($_POST['clear_' . $key])) {
                    $new = '';
                } elseif ($isSecret && $new === '') {
                    continue;                                   // mantém o segredo atual
                }
                if (str_starts_with($type, 'select:')) {
                    $allowed = array_map(static fn (string $o): string => explode('=', $o)[0], explode(',', substr($type, 7)));
                    if (!in_array($new, $allowed, true)) {
                        $errors[] = "$label: opção inválida.";
                        continue;
                    }
                }
                if ($new !== '' && $pattern !== '' && !preg_match($pattern, $new)) {
                    $errors[] = "$label: $msg";
                    continue;
                }
                if ($type === 'email' && $new !== '' && !filter_var($new, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "$label: e-mail inválido.";
                    continue;
                }
                if ($isSecret) {
                    Settings::set($key, $new);
                    $changed[] = $key . ' (segredo)';
                    continue;
                }
                if ($new !== Settings::get($key)) {
                    Settings::set($key, $new);
                    $changed[] = $key;
                }
            }
        }
        foreach ($errors as $e) {
            Session::flash('err', $e);
        }
        if ($changed) {
            Audit::log('configuracoes', 'settings', ['campos' => $changed]);
            Session::flash('ok', 'Configurações salvas: ' . count($changed) . ' campo(s).');
        } elseif (!$errors) {
            Session::flash('warn', 'Nada foi alterado.');
        }
        Kernel::redirect('/admin/configuracoes');
    }

    public static function testSmtp(): void
    {
        $to = (string) (Session::user()['email'] ?? '');
        if (!Mailer::configured()) {
            Session::flash('err', 'Preencha servidor SMTP e e-mail remetente antes de testar.');
        } else {
            $r = Mailer::send($to, 'Teste de e-mail do painel Mídia Server', '<p>Se você recebeu esta mensagem, o SMTP está configurado corretamente.</p>');
            Audit::log('teste_smtp', 'settings', ['ok' => $r['ok']]);
            Session::flash($r['ok'] ? 'ok' : 'err', $r['ok'] ? "E-mail de teste enviado para $to." : 'Falha no envio: ' . $r['error']);
        }
        Kernel::redirect('/admin/configuracoes');
    }
}
