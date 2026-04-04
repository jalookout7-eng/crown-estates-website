/**
 * Admin dashboard sparkline charts via Chart.js.
 */
(function () {
  'use strict';

  var sparklines = document.querySelectorAll('.ce-admin-sparkline');
  if (!sparklines.length || typeof Chart === 'undefined') return;

  sparklines.forEach(function (canvas) {
    var ctx = canvas.getContext('2d');
    // Placeholder data — in production, pass real data via wp_localize_script
    var data = [3, 5, 2, 8, 6, 4, 7, 9, 5, 11, 8, 12];

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(function (_, i) { return ''; }),
        datasets: [{
          data: data,
          borderColor: '#C4973A',
          borderWidth: 2,
          fill: true,
          backgroundColor: 'rgba(196, 151, 58, 0.1)',
          tension: 0.4,
          pointRadius: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { display: false }, y: { display: false } },
      }
    });
  });
})();
