/**
 * GA4 custom event tracking via dataLayer.
 * Tracks: WhatsApp clicks, form submissions (handled in modal.js), brochure downloads.
 */
(function () {
  'use strict';

  window.dataLayer = window.dataLayer || [];

  // WhatsApp click
  document.addEventListener('click', function (e) {
    var wa = e.target.closest('[data-ga4-event="whatsapp_click"]');
    if (wa) {
      window.dataLayer.push({ event: 'whatsapp_click' });
    }
  });

  // Contact form (inline on contact page)
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('#form-contact');
    if (form) {
      window.dataLayer.push({ event: 'contact_submit' });
    }
  });
})();
