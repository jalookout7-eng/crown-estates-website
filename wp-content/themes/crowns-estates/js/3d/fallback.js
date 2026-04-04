/**
 * WebGL detection and fallback.
 * Sets body class 'ce-no-webgl' if WebGL is unavailable.
 * Sets body class 'ce-low-power' if on mobile/low-memory device.
 */
(function () {
  'use strict';

  function hasWebGL() {
    try {
      var canvas = document.createElement('canvas');
      return !!(window.WebGLRenderingContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
    } catch (e) {
      return false;
    }
  }

  function isLowPower() {
    var nav = navigator;
    if (nav.deviceMemory && nav.deviceMemory < 4) return true;
    if (nav.hardwareConcurrency && nav.hardwareConcurrency < 4) return true;
    if (/Mobi|Android|iPhone/i.test(nav.userAgent) && window.innerWidth < 768) return true;
    return false;
  }

  if (!hasWebGL()) {
    document.body.classList.add('ce-no-webgl');
  }
  if (isLowPower()) {
    document.body.classList.add('ce-low-power');
  }

  window.ceCanUse3D = hasWebGL() && !isLowPower();
  window.ceCanUse3DReduced = hasWebGL() && isLowPower();
})();
