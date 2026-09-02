import * as THREE from 'three';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const createParcel = (color, scale = 1) => {
    const parcel = new THREE.Group();
    const box = new THREE.Mesh(
        new THREE.BoxGeometry(1.5 * scale, 1.1 * scale, 1.5 * scale),
        new THREE.MeshStandardMaterial({ color, roughness: 0.34, metalness: 0.08 }),
    );
    box.castShadow = true;
    parcel.add(box);

    const ribbon = new THREE.Mesh(
        new THREE.BoxGeometry(0.18 * scale, 1.13 * scale, 1.53 * scale),
        new THREE.MeshStandardMaterial({ color: 0xf7eee3, roughness: 0.55 }),
    );
    ribbon.castShadow = true;
    parcel.add(ribbon);
    return parcel;
};

const initialiseHeroScene = (container) => {
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 100);
    camera.position.set(0, 0.25, 9);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.75));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.shadowMap.enabled = !reducedMotion;
    container.appendChild(renderer.domElement);

    scene.add(new THREE.HemisphereLight(0xffe4d8, 0x210509, 3.2));
    const keyLight = new THREE.DirectionalLight(0xffffff, 5.5);
    keyLight.position.set(4, 7, 5);
    keyLight.castShadow = true;
    scene.add(keyLight);
    const redLight = new THREE.PointLight(0xff2431, 45, 16);
    redLight.position.set(-3, -1, 3);
    scene.add(redLight);

    const world = new THREE.Group();
    scene.add(world);
    const torus = new THREE.Mesh(
        new THREE.TorusGeometry(2.65, 0.06, 24, 180),
        new THREE.MeshStandardMaterial({ color: 0xd9272e, emissive: 0x5e060b, metalness: 0.65, roughness: 0.25 }),
    );
    torus.rotation.set(1.05, 0.2, -0.2);
    world.add(torus);

    const parcels = [
        [0xd9272e, 1.25, [0, 0, 0]],
        [0xf2c94c, 0.58, [-2.1, 1.5, -0.2]],
        [0x6d59e8, 0.48, [2.4, -1.35, 0.15]],
    ].map(([color, scale, position], index) => {
        const parcel = createParcel(color, scale);
        parcel.position.set(...position);
        parcel.rotation.set(0.18, -0.5 + index * 0.4, 0.08);
        world.add(parcel);
        return parcel;
    });

    const positions = new Float32Array(270);
    for (let index = 0; index < positions.length; index += 3) {
        positions[index] = (Math.random() - 0.5) * 11;
        positions[index + 1] = (Math.random() - 0.5) * 8;
        positions[index + 2] = (Math.random() - 0.5) * 5;
    }
    const particlesGeometry = new THREE.BufferGeometry();
    particlesGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    scene.add(new THREE.Points(particlesGeometry, new THREE.PointsMaterial({ color: 0xffffff, size: 0.025, transparent: true, opacity: 0.48 })));

    const pointer = { x: 0, y: 0 };
    const move = (event) => {
        pointer.x = (event.clientX / window.innerWidth - 0.5) * 0.45;
        pointer.y = (event.clientY / window.innerHeight - 0.5) * 0.3;
    };
    const resize = () => {
        const width = container.clientWidth;
        const height = container.clientHeight;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
        world.position.set(width > 1024 ? 2.7 : 0.7, 0.2, 0);
    };
    window.addEventListener('pointermove', move, { passive: true });
    window.addEventListener('resize', resize, { passive: true });
    resize();

    const clock = new THREE.Clock();
    const render = () => {
        const elapsed = clock.getElapsedTime();
        if (!reducedMotion) {
            world.rotation.y += (pointer.x - world.rotation.y) * 0.025;
            world.rotation.x += (-pointer.y - world.rotation.x) * 0.025;
            torus.rotation.z = elapsed * 0.08;
            parcels.forEach((parcel, index) => {
                parcel.rotation.y += 0.0015 * (index + 1);
                parcel.position.y += Math.sin(elapsed + index) * 0.0015;
            });
        }
        renderer.render(scene, camera);
        window.requestAnimationFrame(render);
    };
    render();

    ScrollTrigger.create({
        trigger: container,
        start: 'top top',
        end: 'bottom top',
        scrub: true,
        onUpdate: ({ progress }) => {
            world.position.y = progress * 1.8;
            world.scale.setScalar(1 - progress * 0.18);
        },
    });
};

const initialiseMotion = (root) => {
    if (reducedMotion) return;
    gsap.fromTo('[data-hero-reveal]', { y: 24 }, { y: 0, duration: .85, stagger: 0.07, ease: 'power3.out', clearProps: 'transform' });
    gsap.fromTo('[data-float-card]', { scale: 0.88 }, { scale: 1, duration: .75, stagger: 0.1, ease: 'back.out(1.4)', clearProps: 'transform' });
    root.querySelectorAll('[data-reveal]').forEach((element) => gsap.from(element, {
        y: 44,
        duration: 0.85,
        ease: 'power3.out',
        scrollTrigger: { trigger: element, start: 'top 88%', once: true },
    }));
    const marquee = root.querySelector('.storefront-marquee div');
    if (marquee) gsap.to(marquee, { xPercent: -35, ease: 'none', scrollTrigger: { trigger: marquee, scrub: 1, start: 'top bottom', end: 'bottom top' } });
};

const initialiseStorefront = () => {
    const root = document.querySelector('[data-storefront-home]');
    if (!root || root.dataset.experienceReady === 'true') return;
    root.dataset.experienceReady = 'true';
    initialiseMotion(root);
    const canvas = root.querySelector('[data-hero-canvas]');
    if (canvas) {
        const startScene = () => {
            try { initialiseHeroScene(canvas); } catch (error) {
                canvas.classList.add('storefront-canvas-fallback');
                console.warn('The 3D storefront scene could not start.', error);
            }
        };

        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(startScene, { timeout: 800 });
        } else {
            window.setTimeout(startScene, 80);
        }
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseStorefront);
} else {
    initialiseStorefront();
}
document.addEventListener('livewire:navigated', initialiseStorefront);
