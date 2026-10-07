(function () {
  'use strict';
  var t = document.querySelector('[data-toggle="menu"]'), n = document.getElementById('a-nav');
  if (t && n) t.addEventListener('click', function () { n.classList.toggle('open'); });
  document.addEventListener('submit', function (e) {
    var m = e.target.getAttribute && e.target.getAttribute('data-confirm');
    if (m && !window.confirm(m)) e.preventDefault();
  });
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-copy]'); if (!b || !navigator.clipboard) return;
    navigator.clipboard.writeText(b.getAttribute('data-copy')); b.textContent = 'Copiado';
  });
})();
