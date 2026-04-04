/**
 * Homepage Hero 3D Scene.
 * Procedural cityscape with camera orbit on scroll and gold particles.
 * Replaces with GLTF model when skyline.glb is available.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D && !window.ceCanUse3DReduced) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-hero');
    if (!container) return;

    var mgr = CeSceneManager.create('ce-3d-hero', {
      fov: 50, cameraX: 0, cameraY: 3, cameraZ: 12
    });
    if (!mgr) return;

    var THREE = window.THREE;

    // Ambient light
    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.4));
    var gold = new THREE.DirectionalLight(0xC4973A, 0.8);
    gold.position.set(5, 10, 5);
    mgr.scene.add(gold);

    // Procedural buildings
    var buildingMat = new THREE.MeshStandardMaterial({ color: 0x1a1a1a, metalness: 0.3, roughness: 0.7 });
    for (var i = 0; i < 30; i++) {
      var w = 0.3 + Math.random() * 0.7;
      var h = 1 + Math.random() * 5;
      var d = 0.3 + Math.random() * 0.7;
      var geo = new THREE.BoxGeometry(w, h, d);
      var mesh = new THREE.Mesh(geo, buildingMat);
      mesh.position.set((Math.random() - 0.5) * 20, h / 2, (Math.random() - 0.5) * 10 - 5);
      mgr.scene.add(mesh);
    }

    // Gold particles
    var particles = null;
    if (!window.ceCanUse3DReduced) {
      particles = CeParticles.create(mgr.scene, { count: 150, spread: 25, size: 0.04, opacity: 0.4 });
    }

    // Scroll-driven camera orbit
    CeScrollController.bind('#ce-3d-hero', function (progress) {
      var angle = progress * Math.PI * 0.5;
      mgr.camera.position.x = Math.sin(angle) * 12;
      mgr.camera.position.y = 3 + progress * 2;
      mgr.camera.position.z = Math.cos(angle) * 12;
      mgr.camera.lookAt(0, 2, 0);
    }, { start: 'top top', end: 'bottom top' });

    // Animation
    mgr.onAnimate(function () {
      if (particles) particles.update();
    });
  });
})();
