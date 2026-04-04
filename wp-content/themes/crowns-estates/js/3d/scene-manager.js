/**
 * Three.js Scene Manager.
 * Creates and manages a WebGL renderer, camera, and render loop for a given container.
 */
window.CeSceneManager = (function () {
  'use strict';

  function create(containerId, options) {
    var container = document.getElementById(containerId);
    if (!container || !window.ceCanUse3D && !window.ceCanUse3DReduced) return null;

    var THREE = window.THREE;
    if (!THREE) return null;

    var width = container.clientWidth;
    var height = container.clientHeight || window.innerHeight;

    // Scene
    var scene = new THREE.Scene();
    scene.background = null; // Transparent — CSS background shows through

    // Camera
    var fov = options.fov || 60;
    var camera = new THREE.PerspectiveCamera(fov, width / height, 0.1, 1000);
    camera.position.set(
      options.cameraX || 0,
      options.cameraY || 2,
      options.cameraZ || 10
    );

    // Renderer
    var renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    container.appendChild(renderer.domElement);

    // Resize
    function onResize() {
      width = container.clientWidth;
      height = container.clientHeight || window.innerHeight;
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);
    }
    window.addEventListener('resize', onResize);

    // Render loop
    var animateCallbacks = [];
    var disposed = false;

    function animate() {
      if (disposed) return;
      requestAnimationFrame(animate);
      animateCallbacks.forEach(function (cb) { cb(); });
      renderer.render(scene, camera);
    }
    animate();

    return {
      scene: scene,
      camera: camera,
      renderer: renderer,
      container: container,
      onAnimate: function (cb) { animateCallbacks.push(cb); },
      dispose: function () {
        disposed = true;
        renderer.dispose();
        window.removeEventListener('resize', onResize);
      }
    };
  }

  return { create: create };
})();
