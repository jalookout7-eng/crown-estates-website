/**
 * Scroll Controller: GSAP ScrollTrigger + Lenis smooth scrolling.
 */
window.CeScrollController = (function () {
  'use strict';

  var lenis = null;

  function init() {
    if (typeof Lenis === 'undefined' || typeof gsap === 'undefined') return;

    lenis = new Lenis({ duration: 1.2, easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); } });

    lenis.on('scroll', function () {
      if (window.ScrollTrigger) ScrollTrigger.update();
    });

    gsap.ticker.add(function (time) {
      lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);
  }

  /**
   * Bind scroll progress to a callback.
   * @param {string} trigger - CSS selector for the trigger element
   * @param {function} onUpdate - callback receiving progress (0-1)
   * @param {object} options - ScrollTrigger options override
   */
  function bind(trigger, onUpdate, options) {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    var defaults = {
      trigger: trigger,
      start: 'top bottom',
      end: 'bottom top',
      scrub: 1,
      onUpdate: function (self) { onUpdate(self.progress); },
    };

    return ScrollTrigger.create(Object.assign(defaults, options || {}));
  }

  return { init: init, bind: bind, getLenis: function () { return lenis; } };
})();

// Auto-init on DOM ready
document.addEventListener('DOMContentLoaded', function () {
  CeScrollController.init();
});
