/**
 * Investment cost calculator.
 * Reads rates from ceCalcRates (localized via wp_localize_script).
 */
(function () {
  'use strict';

  var form = document.getElementById('ce-calculator');
  if (!form) return;

  var priceInput = form.querySelector('[name="calc-price"]');
  var output = document.getElementById('ce-calculator-output');
  if (!priceInput || !output) return;

  var ceRates = (window.CE && CE.calcRates) || {};
  var rates = { registration_fee: ceRates.registration || 2.5, vat: ceRates.vat || 5, agency_fee: ceRates.agency || 2 };

  function calculate() {
    var price = parseFloat(priceInput.value) || 0;
    var regFee = price * (rates.registration_fee / 100);
    var vat = price * (rates.vat / 100);
    var agencyFee = price * (rates.agency_fee / 100);
    var total = price + regFee + vat + agencyFee;

    output.querySelector('[data-calc="price"]').textContent = Math.round(price).toLocaleString();
    output.querySelector('[data-calc="reg-fee"]').textContent = Math.round(regFee).toLocaleString();
    output.querySelector('[data-calc="vat"]').textContent = Math.round(vat).toLocaleString();
    output.querySelector('[data-calc="agency-fee"]').textContent = Math.round(agencyFee).toLocaleString();
    output.querySelector('[data-calc="total"]').textContent = Math.round(total).toLocaleString();
  }

  priceInput.addEventListener('input', calculate);
  form.addEventListener('change', calculate);
})();
