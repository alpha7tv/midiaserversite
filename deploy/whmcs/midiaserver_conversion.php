<?php
/**
 * Hook do WHMCS: conversão de COMPRA para Google Ads (e GA4) na página "pedido concluído".
 *
 * Instalação: copie para /var/www/whmcs/includes/hooks/midiaserver_conversion.php (o script deploy/whmcs-hook.sh faz isso).
 * Não altera nenhum arquivo do WHMCS. Para desligar, apague este arquivo.
 *
 * - Só mede quem aceitou os cookies no portal (cookie ms_consent=yes, gravado em .midiaserver.com.br).
 * - O valor vem do próprio banco do WHMCS (tblorders.amount), não do navegador.
 * - O número do pedido é o transaction_id: recarregar a página não conta duas vezes.
 */
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;

add_hook('ClientAreaFooterOutput', 1, function ($vars) {
    $adsId = '__ADS_ID__';          // AW-XXXXXXXXX
    $adsLabel = '__ADS_LABEL__';    // AW-XXXXXXXXX/abcDEF  (rótulo da conversão "Compra")
    $ga4Id = '__GA4_ID__';          // G-XXXXXXXXXX (opcional; deixe vazio para não enviar ao GA4)

    if (strpos($adsLabel, '__') !== false || strpos($adsId, '__') !== false) {
        return '';                  // ainda não configurado
    }
    if (($vars['filename'] ?? '') !== 'cart' || ($_GET['a'] ?? '') !== 'complete') {
        return '';
    }
    $orderId = (int) ($_SESSION['orderdetails']['OrderID'] ?? 0);
    if ($orderId < 1 || !empty($_SESSION['ms_conv_sent_' . $orderId])) {
        return '';
    }
    $order = Capsule::table('tblorders')->where('id', $orderId)->first();
    if (!$order) {
        return '';
    }
    $_SESSION['ms_conv_sent_' . $orderId] = 1;

    $payload = [
        'adsId' => $adsId,
        'label' => $adsLabel,
        'ga4' => strpos($ga4Id, '__') === false ? $ga4Id : '',
        'value' => round((float) $order->amount, 2),
        'currency' => 'BRL',
        'tid' => 'ord-' . (string) ($order->ordernum ?: $orderId),
    ];
    $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);

    return <<<HTML
<script>
(function () {
  var c = {$json};
  var m = document.cookie.match(/(?:^|; )ms_consent=([^;]+)/);
  if (!m || m[1] !== 'yes') { return; }
  window.dataLayer = window.dataLayer || [];
  function gtag() { dataLayer.push(arguments); }
  var s = document.createElement('script'); s.async = true;
  s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(c.ga4 || c.adsId);
  document.head.appendChild(s);
  gtag('consent', 'default', { ad_storage: 'granted', analytics_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted' });
  gtag('js', new Date());
  if (c.ga4) { gtag('config', c.ga4); }
  gtag('config', c.adsId);
  gtag('event', 'conversion', { send_to: c.label, value: c.value, currency: c.currency, transaction_id: c.tid });
  if (c.ga4) { gtag('event', 'purchase', { transaction_id: c.tid, value: c.value, currency: c.currency }); }
})();
</script>
HTML;
});
