(function(){
  var C=window.CP||{};
  function track(name,params){params=params||{};
    try{if(window.gtag){gtag('event',name,params);
      if(name==='whatsapp_click'&&C.gads&&C.gadsWa)gtag('event','conversion',{send_to:C.gads+'/'+C.gadsWa});}}catch(e){}
    try{if(window.fbq){if(name==='whatsapp_click')fbq('track','Contact');else fbq('trackCustom',name,params);}}catch(e){}
  }
  window.cpTrack=track;
  document.addEventListener('click',function(e){var a=e.target.closest('[data-track]');if(!a)return;
    var t=a.getAttribute('data-track');track(t==='whatsapp'?'whatsapp_click':t,{page:location.pathname});});
  // menu
  var mb=document.querySelector('.menu-btn'),menu=document.getElementById('menu');
  if(mb)mb.addEventListener('click',function(){var o=menu.classList.toggle('open');mb.setAttribute('aria-expanded',o);});
  // consentimento LGPD
  var cs=document.getElementById('consent'),st=null;try{st=localStorage.getItem('cp_consent');}catch(e){}
  if(cs&&!st)cs.hidden=false;
  document.querySelectorAll('[data-consent]').forEach(function(b){b.addEventListener('click',function(){var v=b.getAttribute('data-consent');
    try{localStorage.setItem('cp_consent',v);}catch(e){}
    if(v==='granted'){try{gtag('consent','update',{ad_storage:'granted',ad_user_data:'granted',ad_personalization:'granted',analytics_storage:'granted'});}catch(e){}try{fbq('consent','grant');}catch(e){}}
    cs.hidden=true;});});
  // popup apontamento (1x por sessão, após 4s)
  var pp=document.getElementById('popup');
  if(pp){var seen=null;try{seen=sessionStorage.getItem('cp_popup');}catch(e){}
    if(!seen)setTimeout(function(){pp.hidden=false;track('popup_view');try{sessionStorage.setItem('cp_popup','1');}catch(e){}},4000);
    function close(){pp.hidden=true;}
    pp.addEventListener('click',function(e){if(e.target===pp||e.target.classList.contains('popup-x'))close();});
    document.addEventListener('keydown',function(e){if(e.key==='Escape')close();});}
  // som do trailer
  var sb=document.querySelector('.sound-btn'),ifr=document.getElementById('trailer');
  if(sb&&ifr)sb.addEventListener('click',function(){var m=sb.getAttribute('data-muted')!=='0';
    ifr.contentWindow.postMessage(JSON.stringify({event:'command',func:m?'unMute':'mute',args:[]}),'*');
    sb.setAttribute('data-muted',m?'0':'1');sb.textContent=m?'🔊':'🔇';sb.setAttribute('aria-label',m?'Desativar som':'Ativar som');});
  // máscara WhatsApp
  document.querySelectorAll('input[name=whatsapp]').forEach(function(i){i.addEventListener('input',function(){var d=i.value.replace(/\D/g,'').slice(0,11);
    i.value=d.length>6?'('+d.slice(0,2)+') '+d.slice(2,7)+'-'+d.slice(7):d.length>2?'('+d.slice(0,2)+') '+d.slice(2):d;});});
  // UTMs nos formulários
  var q=new URLSearchParams(location.search);['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid'].forEach(function(k){
    var v=q.get(k);if(v)try{sessionStorage.setItem(k,v);}catch(e){}});
  document.querySelectorAll('form[data-utm]').forEach(function(f){['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid'].forEach(function(k){
    var v=null;try{v=sessionStorage.getItem(k);}catch(e){}if(v){var h=document.createElement('input');h.type='hidden';h.name=k;h.value=v;f.appendChild(h);}});
    f.addEventListener('submit',function(){track('form_submit',{form:f.getAttribute('data-utm')});});});
})();
