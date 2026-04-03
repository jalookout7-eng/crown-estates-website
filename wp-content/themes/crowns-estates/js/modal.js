/**
 * Modal: open/close + form submission via fetch to REST endpoint.
 * Handles both Register Interest and Brochure Gate modals.
 */
(function () {
  'use strict';

  // Open modal
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-open-modal]');
    if (!trigger) return;
    e.preventDefault();
    var modalId = 'modal-' + trigger.getAttribute('data-open-modal');
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Set source URL
    var sourceInput = modal.querySelector('input[name="source_url"]');
    if (sourceInput) sourceInput.value = window.location.href;

    // Set property interest + brochure URL for brochure gate
    var propInput = modal.querySelector('input[name="property_interest"]');
    if (propInput && trigger.dataset.propertyName) {
      propInput.value = trigger.dataset.propertyName;
    }
    var brochureInput = modal.querySelector('input[name="brochure_url"]');
    if (brochureInput && trigger.dataset.brochureUrl) {
      brochureInput.value = trigger.dataset.brochureUrl;
    }
  });

  // Close modal
  document.addEventListener('click', function (e) {
    if (!e.target.closest('[data-close-modal]')) return;
    var modal = e.target.closest('.ce-modal');
    if (modal) closeModal(modal);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var open = document.querySelector('.ce-modal[aria-hidden="false"]');
    if (open) closeModal(open);
  });

  function closeModal(modal) {
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // Form submission
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('.ce-form[data-endpoint]');
    if (!form) return;
    e.preventDefault();

    var btn = form.querySelector('button[type="submit"]');
    var status = form.querySelector('.ce-form__status');
    var endpoint = form.getAttribute('data-endpoint');

    btn.disabled = true;
    btn.textContent = 'Sending...';
    if (status) status.textContent = '';

    var data = {};
    new FormData(form).forEach(function (value, key) {
      data[key] = key === 'gdpr_consent' ? true : value;
    });

    fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (json.success) {
          if (status) status.textContent = json.message || 'Thank you!';
          status.classList.add('ce-form__status--success');
          form.reset();

          // Push GA4 event
          var source = data.source || 'enquiry';
          if (window.dataLayer) {
            window.dataLayer.push({
              event: source === 'brochure_download' ? 'brochure_download' : 'enquiry_submit',
            });
          }
        } else {
          if (status) status.textContent = json.error || 'Something went wrong.';
          status.classList.add('ce-form__status--error');
        }
      })
      .catch(function () {
        if (status) status.textContent = 'Network error. Please try again.';
        status.classList.add('ce-form__status--error');
      })
      .finally(function () {
        btn.disabled = false;
        btn.textContent = form.id === 'form-brochure-gate' ? 'Get Brochure' : 'Send Enquiry';
      });
  });
})();
