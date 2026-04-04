/**
 * How It Works — 3D Journey Path.
 * Camera follows a golden line through 5 floating step cards.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D && !window.ceCanUse3DReduced) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-journey');
    if (!container) return;

    var mgr = CeSceneManager.create('ce-3d-journey', {
      fov: 50, cameraX: 0, cameraY: 2, cameraZ: 8
    });
    if (!mgr) return;

    var THREE = window.THREE;

    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.6));
    var light = new THREE.PointLight(0xC4973A, 1, 50);
    light.position.set(0, 5, 5);
    mgr.scene.add(light);

    // Golden path curve
    var pathPoints = [
      new THREE.Vector3(-6, 0, 0),
      new THREE.Vector3(-3, 1, -2),
      new THREE.Vector3(0, 2, 0),
      new THREE.Vector3(3, 1, -2),
      new THREE.Vector3(6, 0, 0),
    ];

    var curve = new THREE.CatmullRomCurve3(pathPoints);
    var pathGeo = new THREE.TubeGeometry(curve, 100, 0.02, 8, false);
    var pathMat = new THREE.MeshBasicMaterial({ color: 0xC4973A });
    mgr.scene.add(new THREE.Mesh(pathGeo, pathMat));

    // Step markers
    var markerMat = new THREE.MeshStandardMaterial({ color: 0xC4973A, emissive: 0xC4973A, emissiveIntensity: 0.3 });
    pathPoints.forEach(function (pt) {
      var marker = new THREE.Mesh(new THREE.SphereGeometry(0.15, 16, 16), markerMat);
      marker.position.copy(pt);
      mgr.scene.add(marker);
    });

    // Gold particles
    var particles = null;
    if (!window.ceCanUse3DReduced) {
      particles = CeParticles.create(mgr.scene, { count: 80, spread: 15, size: 0.03, opacity: 0.3 });
    }

    // Scroll-driven camera along path
    CeScrollController.bind('#ce-3d-journey', function (progress) {
      var point = curve.getPoint(progress);
      var lookAhead = curve.getPoint(Math.min(progress + 0.05, 1));
      mgr.camera.position.set(point.x, point.y + 2, point.z + 6);
      mgr.camera.lookAt(lookAhead);
    }, { start: 'top center', end: 'bottom center' });

    mgr.onAnimate(function () {
      if (particles) particles.update();
    });
  });
})();
