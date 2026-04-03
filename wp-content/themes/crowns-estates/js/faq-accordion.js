/**
 * FAQ accordion: click to expand/collapse. One open at a time.
 * Uses aria-expanded for accessibility.
 */
(function () {
  'use strict';

  var items = document.querySelectorAll('.ce-faq__question');
  if (!items.length) return;

  items.forEach(function (question) {
    question.addEventListener('click', function () {
      var isOpen = this.getAttribute('aria-expanded') === 'true';

      // Close all
      items.forEach(function (q) {
        q.setAttribute('aria-expanded', 'false');
        q.nextElementSibling.style.maxHeight = null;
      });

      // Open clicked (if it was closed)
      if (!isOpen) {
        this.setAttribute('aria-expanded', 'true');
        this.nextElementSibling.style.maxHeight = this.nextElementSibling.scrollHeight + 'px';
      }
    });
  });
})();
