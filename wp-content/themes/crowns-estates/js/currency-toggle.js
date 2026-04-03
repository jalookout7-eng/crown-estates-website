/**
 * Currency toggle: reads cookie, fetches rates, recalculates all [data-price] elements.
 * Cache-safe — no server-side cookie reading.
 */
(function () {
  'use strict';

  var toggles = document.querySelectorAll('.ce-currency-toggle__option');
  if (!toggles.length) return;

  var rates = null;

  function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  function setCookie(name, value) {
    document.cookie = name + '=' + value + ';path=/;max-age=31536000;SameSite=Lax';
  }

  function formatPrice(amount, currency) {
    var symbols = { GBP: '£', SAR: 'SAR ', USD: '$' };
    var symbol = symbols[currency] || currency + ' ';
    return symbol + Math.round(amount).toLocaleString();
  }

  function convert(amount, from, to) {
    if (from === to || !rates) return amount;
    // Convert to SAR first, then to target
    var inSar = amount;
    if (from === 'GBP') inSar = amount * rates.GBP_SAR;
    if (from === 'USD') inSar = amount * rates.USD_SAR;

    if (to === 'SAR') return inSar;
    if (to === 'GBP') return inSar / rates.GBP_SAR;
    if (to === 'USD') return inSar / rates.USD_SAR;
    return amount;
  }

  function updatePrices(targetCurrency) {
    document.querySelectorAll('[data-price]').forEach(function (el) {
      var price = parseFloat(el.getAttribute('data-price'));
      var native = el.getAttribute('data-currency');
      if (!price || !native) return;
      var converted = convert(price, native, targetCurrency);
      el.textContent = formatPrice(converted, targetCurrency);
    });
  }

  function setActive(currency) {
    toggles.forEach(function (t) {
      t.classList.toggle('active', t.textContent.trim() === currency);
    });
  }

  // Fetch rates then apply saved preference
  var restUrl = (window.ceData && window.ceData.restUrl) || '/wp-json/ce/v1/';
  fetch(restUrl + 'rates')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      rates = data;
      var saved = getCookie('ce_currency');
      if (saved && saved !== 'SAR') {
        setActive(saved);
        updatePrices(saved);
      }
    })
    .catch(function () { /* silently fail — prices stay in native currency */ });

  // Toggle click
  toggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      var currency = this.textContent.trim();
      setCookie('ce_currency', currency);
      setActive(currency);
      if (rates) updatePrices(currency);
    });
  });
})();
