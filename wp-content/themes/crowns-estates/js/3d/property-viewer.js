/**
 * Single Property 3D Model Viewer.
 * Loads GLTF model from data attribute. User can orbit with mouse.
 * Falls back gracefully if no model URL is provided.
 */
(function () {
  'use strict';
  if (!window.ceCanUse3D) return;

  document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('ce-3d-property-viewer');
    if (!container) return;

    var modelUrl = container.getAttribute('data-model-url');
    if (!modelUrl) return; // No 3D model for this property

    var mgr = CeSceneManager.create('ce-3d-property-viewer', {
      fov: 45, cameraX: 0, cameraY: 2, cameraZ: 5
    });
    if (!mgr) return;

    var THREE = window.THREE;

    mgr.scene.add(new THREE.AmbientLight(0xffffff, 0.6));
    var dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
    dirLight.position.set(5, 10, 5);
    mgr.scene.add(dirLight);

    // Load GLTF
    var loader = new THREE.GLTFLoader();
    loader.load(modelUrl, function (gltf) {
      var model = gltf.scene;
      model.scale.set(1, 1, 1);
      mgr.scene.add(model);
    });

    // Simple orbit on mouse drag
    var isDragging = false;
    var prevX = 0;
    var angle = 0;

    container.addEventListener('mousedown', function (e) { isDragging = true; prevX = e.clientX; });
    window.addEventListener('mouseup', function () { isDragging = false; });
    container.addEventListener('mousemove', function (e) {
      if (!isDragging) return;
      var delta = e.clientX - prevX;
      prevX = e.clientX;
      angle += delta * 0.005;
      mgr.camera.position.x = Math.sin(angle) * 5;
      mgr.camera.position.z = Math.cos(angle) * 5;
      mgr.camera.lookAt(0, 1, 0);
    });
  });
})();
