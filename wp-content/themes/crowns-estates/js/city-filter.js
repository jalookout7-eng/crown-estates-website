/**
 * City filter: show/hide property cards by city, developer, and status.
 * No page reload — uses CSS classes.
 */
(function () {
  'use strict';

  var filterBar = document.querySelector('.ce-filter-bar');
  if (!filterBar) return;

  var cards = document.querySelectorAll('.ce-property-card');

  filterBar.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-filter]');
    if (!btn) return;

    var filterType = btn.getAttribute('data-filter-type') || 'city';
    var filterValue = btn.getAttribute('data-filter');

    // Update active state
    btn.closest('.ce-filter-group').querySelectorAll('[data-filter]').forEach(function (b) {
      b.classList.remove('active');
    });
    btn.classList.add('active');

    // Filter cards
    cards.forEach(function (card) {
      var match = filterValue === 'all' || card.getAttribute('data-' + filterType) === filterValue;
      card.style.display = match ? '' : 'none';
    });
  });
})();
