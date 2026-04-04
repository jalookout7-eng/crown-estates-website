/**
 * Projects Page 3D City Map.
 * Simple 3D plane with pin markers for each city.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D && !window.ceCanUse3DReduced) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-projects-map');
    if (!container) return;

    var mgr = CeSceneManager.create('ce-3d-projects-map', {
      fov: 45, cameraX: 0, cameraY: 8, cameraZ: 8
    });
    if (!mgr) return;

    var THREE = window.THREE;

    // Lights
    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.5));
    var dirLight = new THREE.DirectionalLight(0xC4973A, 0.6);
    dirLight.position.set(3, 10, 5);
    mgr.scene.add(dirLight);

    // Ground plane
    var ground = new THREE.Mesh(
      new THREE.PlaneGeometry(16, 10),
      new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.9 })
    );
    ground.rotation.x = -Math.PI / 2;
    mgr.scene.add(ground);

    // City pins — positions are approximate relative coords
    var cityData = [
      { name: 'Riyadh', x: 1, z: -1 },
      { name: 'Jeddah', x: -4, z: 1 },
      { name: 'NEOM', x: -5, z: -3 },
      { name: 'AlUla', x: -3, z: -2 },
    ];

    var pinMat = new THREE.MeshStandardMaterial({ color: 0xC4973A, emissive: 0xC4973A, emissiveIntensity: 0.5 });

    cityData.forEach(function (city) {
      var pin = new THREE.Mesh(new THREE.SphereGeometry(0.15, 16, 16), pinMat);
      pin.position.set(city.x, 0.15, city.z);
      pin.userData.city = city.name;
      mgr.scene.add(pin);

      // Pin stem
      var stem = new THREE.Mesh(
        new THREE.CylinderGeometry(0.03, 0.03, 0.3),
        new THREE.MeshStandardMaterial({ color: 0xC4973A })
      );
      stem.position.set(city.x, 0.15, city.z);
      mgr.scene.add(stem);
    });

    // Gentle rotation
    var time = 0;
    mgr.onAnimate(function () {
      time += 0.005;
      mgr.camera.position.x = Math.sin(time * 0.3) * 2;
      mgr.camera.lookAt(0, 0, 0);
    });
  });
})();
