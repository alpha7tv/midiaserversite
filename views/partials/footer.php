<?php
use App\Core\Settings;
use App\Core\Whmcs;
?>
<footer class="ftr">
  <div class="wrap">
    <?php if (\App\Core\Mailer::configured()): [$nts, $nsig] = \App\Core\Csrf::stamp(); ?>
    <form class="nl" method="post" action="/newsletter/inscrever">
      <div><h2 class="ftr-h" style="margin:0 0 4px">Receba novidades, promoções e ferramentas para sua rádio</h2>
      <p class="nl-note">Enviaremos um e-mail para você confirmar. Ao se inscrever, você concorda com a <a href="/politica-de-privacidade">Política de Privacidade</a>. Cancele quando quiser.</p></div>
      <div class="nl-row">
        <label class="sr" for="nl-email">Seu e-mail</label>
        <input id="nl-email" name="email" type="email" required maxlength="190" autocomplete="email" placeholder="seu@email.com">
        <input type="hidden" name="ts" value="<?= e($nts) ?>"><input type="hidden" name="sig" value="<?= e($nsig) ?>"><input type="hidden" name="source" value="<?= e((string) ($page['path'] ?? '/')) ?>">
        <div class="hp" aria-hidden="true"><label>Não preencha <input name="website" tabindex="-1" autocomplete="off"></label></div>
        <button class="btn btn-primary" type="submit" data-ev="cta_click" data-where="newsletter">QUERO RECEBER</button>
      </div>
    </form>
    <?php endif; ?>
    <div class="ftr-grid">
      <div class="ftr-brand">
        <img src="/assets/img/logo-horizontal-dark.svg" width="188" height="48" alt="Mídia Server" loading="lazy">
        <p>Hospedagem, streaming, automação e conteúdos para rádios e para a sua empresa na internet.</p>
        <p class="ftr-contact">
          <a href="<?= e(Settings::whatsapp()) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="rodape"><?= icon('whatsapp', 18) ?> WhatsApp</a><br>
          <a href="mailto:<?= e(Settings::get('support_email')) ?>"><?= icon('mail', 18) ?> <?= e(Settings::get('support_email')) ?></a>
        </p>
      </div>
      <div>
        <h2 class="ftr-h">Hospedagem</h2>
        <ul>
          <li><a href="/hospedagem">Hospedagem cPanel</a></li>
          <li><a href="/revenda-hospedagem">Revenda de hospedagem</a></li>
          <li><a href="/vps">VPS Linux</a></li>
          <li><a href="<?= e(Whmcs::domainRegister()) ?>">Registrar domínio</a></li>
        </ul>
      </div>
      <div>
        <h2 class="ftr-h">Rádios</h2>
        <ul>
          <li><a href="/streaming">Streaming de áudio</a></li>
          <li><a href="/web-radio-completa">Web rádio completa</a></li>
          <li><a href="/revenda-streaming">Revenda de streaming</a></li>
          <li><a href="/sites-para-radio">Sites para rádio</a></li>
          <li><a href="/automacao-radio">Automação de rádio</a></li>
          <li><a href="/conteudos-para-radio">Conteúdos para rádios</a></li>
          <li><a href="/radios">Rádios online</a></li>
          <li><a href="/downloads">Downloads</a></li>
        </ul>
      </div>
      <div>
        <h2 class="ftr-h">Empresa</h2>
        <ul>
          <li><a href="/sobre">Sobre</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="/contato">Contato</a></li>
          <li><a href="/suporte">Suporte</a></li>
          <li><a href="<?= e(Whmcs::login()) ?>">Área do Cliente</a></li>
          <li><a href="<?= e(Whmcs::register()) ?>">Criar conta</a></li>
        </ul>
      </div>
    </div>
    <div class="ftr-bottom">
      <span>© <?= date('Y') ?> Mídia Server. Todos os direitos reservados.</span>
      <span class="ftr-legal">
        <a href="/termos-de-uso">Termos de uso</a>
        <a href="/politica-de-privacidade">Privacidade</a>
        <a href="/politica-de-cookies">Cookies</a>
        <a href="/lgpd">LGPD</a>
      </span>
    </div>
  </div>
</footer>
