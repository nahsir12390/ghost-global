import * as THREE from 'three';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const initialiseAuthExperience = () => {
    const root = document.querySelector('[data-auth-experience]');
    const container = root?.querySelector('[data-auth-canvas]');
    if (!container || root.dataset.authReady === 'true') return;
    root.dataset.authReady = 'true';

    root.querySelectorAll('[data-auth-copy]').forEach((element, index) => {
        if (reducedMotion) return;
        element.animate(
            [{ opacity: 0, transform: 'translateY(22px)' }, { opacity: 1, transform: 'translateY(0)' }],
            { duration: 720, delay: 130 + index * 150, easing: 'cubic-bezier(.2,.8,.2,1)', fill: 'both' },
        );
    });

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 100);
    camera.position.set(0, 0, 9);
    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    container.appendChild(renderer.domElement);

    scene.add(new THREE.HemisphereLight(0xffffff, 0x280307, 2.2));
    const light = new THREE.PointLight(0xff3038, 58, 18);
    light.position.set(1.5, 2.5, 4);
    scene.add(light);

    const group = new THREE.Group();
    group.position.set(1.8, .25, 0);
    scene.add(group);

    const knot = new THREE.Mesh(
        new THREE.TorusKnotGeometry(1.75, .42, 96, 14, 2, 3),
        new THREE.MeshStandardMaterial({ color: 0xd9272e, roughness: .22, metalness: .5 }),
    );
    knot.rotation.set(.2, -.2, .35);
    group.add(knot);

    const ring = new THREE.Mesh(
        new THREE.TorusGeometry(2.65, .028, 10, 100),
        new THREE.MeshBasicMaterial({ color: 0xffffff, transparent: true, opacity: .28 }),
    );
    ring.rotation.set(1.1, 0, .3);
    group.add(ring);

    const cardMaterial = new THREE.MeshStandardMaterial({ color: 0xf5f3ee, roughness: .38, metalness: .08 });
    [-1, 1].forEach((direction) => {
        const card = new THREE.Mesh(new THREE.BoxGeometry(1.45, .92, .08), cardMaterial);
        card.position.set(direction * 2.25, direction * -.9, direction * -.35);
        card.rotation.set(direction * .2, direction * .35, direction * .22);
        group.add(card);
    });

    const pointer = { x: 0, y: 0 };
    const move = (event) => {
        pointer.x = (event.clientX / window.innerWidth - .5) * .38;
        pointer.y = (event.clientY / window.innerHeight - .5) * .24;
    };
    const resize = () => {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    };
    window.addEventListener('pointermove', move, { passive: true });
    window.addEventListener('resize', resize, { passive: true });
    resize();

    let frame;
    const render = () => {
        if (!document.hidden) {
            if (!reducedMotion) {
                knot.rotation.y += .0024;
                ring.rotation.z -= .001;
                group.rotation.y += (pointer.x - group.rotation.y) * .025;
                group.rotation.x += (-pointer.y - group.rotation.x) * .025;
            }
            renderer.render(scene, camera);
        }
        frame = window.requestAnimationFrame(render);
    };
    render();

    window.addEventListener('pagehide', () => {
        window.cancelAnimationFrame(frame);
        renderer.dispose();
    }, { once: true });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseAuthExperience);
} else {
    initialiseAuthExperience();
}

document.addEventListener('livewire:navigated', initialiseAuthExperience);
