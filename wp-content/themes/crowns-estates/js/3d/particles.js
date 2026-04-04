/**
 * Gold Particle System.
 * Adds floating gold particles to a Three.js scene.
 */
window.CeParticles = (function () {
  'use strict';

  function create(scene, options) {
    var THREE = window.THREE;
    if (!THREE) return null;

    var count = options.count || 200;
    var spread = options.spread || 20;
    var color = options.color || 0xC4973A;

    var geometry = new THREE.BufferGeometry();
    var positions = new Float32Array(count * 3);
    var velocities = new Float32Array(count * 3);

    for (var i = 0; i < count * 3; i += 3) {
      positions[i] = (Math.random() - 0.5) * spread;
      positions[i + 1] = (Math.random() - 0.5) * spread;
      positions[i + 2] = (Math.random() - 0.5) * spread;
      velocities[i] = (Math.random() - 0.5) * 0.005;
      velocities[i + 1] = Math.random() * 0.005 + 0.002;
      velocities[i + 2] = (Math.random() - 0.5) * 0.005;
    }

    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    var material = new THREE.PointsMaterial({
      color: color,
      size: options.size || 0.05,
      transparent: true,
      opacity: options.opacity || 0.6,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });

    var points = new THREE.Points(geometry, material);
    scene.add(points);

    return {
      mesh: points,
      update: function () {
        var pos = geometry.attributes.position.array;
        for (var i = 0; i < count * 3; i += 3) {
          pos[i] += velocities[i];
          pos[i + 1] += velocities[i + 1];
          pos[i + 2] += velocities[i + 2];
          if (pos[i + 1] > spread / 2) pos[i + 1] = -spread / 2;
        }
        geometry.attributes.position.needsUpdate = true;
      },
      dispose: function () {
        geometry.dispose();
        material.dispose();
        scene.remove(points);
      }
    };
  }

  return { create: create };
})();
