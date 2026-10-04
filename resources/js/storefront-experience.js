import * as THREE from 'three';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const mobile = window.matchMedia('(max-width: 767px)').matches;

const latLon = (lat, lon, radius) => {
    const phi = (90 - lat) * Math.PI / 180;
    const theta = (lon + 180) * Math.PI / 180;
    return new THREE.Vector3(-radius * Math.sin(phi) * Math.cos(theta), radius * Math.cos(phi), radius * Math.sin(phi) * Math.sin(theta));
};

const initialiseHeroScene = (container) => {
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(34, 1, .1, 100);
    camera.position.set(0, .15, 9.5);
    const renderer = new THREE.WebGLRenderer({ antialias: !mobile, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, mobile ? 1.25 : 1.75));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    container.appendChild(renderer.domElement);

    const world = new THREE.Group();
    scene.add(world);
    scene.add(new THREE.AmbientLight(0x9db7ff, 1.8));
    const key = new THREE.DirectionalLight(0xffffff, 4.5); key.position.set(4, 5, 6); scene.add(key);
    const red = new THREE.PointLight(0xff3151, 55, 15); red.position.set(-4, -2, 4); scene.add(red);
    const blue = new THREE.PointLight(0x4778ff, 42, 14); blue.position.set(4, 2, 3); scene.add(blue);

    const globe = new THREE.Mesh(new THREE.SphereGeometry(2.42, mobile ? 36 : 64, mobile ? 24 : 48), new THREE.MeshPhysicalMaterial({ color: 0x111827, roughness: .42, metalness: .18, transparent: true, opacity: .94, clearcoat: .55, clearcoatRoughness: .25 }));
    world.add(globe);
    const wire = new THREE.Mesh(new THREE.SphereGeometry(2.46, 32, 24), new THREE.MeshBasicMaterial({ color: 0x7895c8, wireframe: true, transparent: true, opacity: .075 }));
    world.add(wire);
    const atmosphere = new THREE.Mesh(new THREE.SphereGeometry(2.58, 40, 30), new THREE.MeshBasicMaterial({ color: 0x5b7cff, transparent: true, opacity: .055, side: THREE.BackSide }));
    world.add(atmosphere);

    const cities = [
        [40.71,-74.0,0xff365f], [51.50,-.12,0xffffff], [43.65,-79.38,0xff365f], [6.52,3.37,0x43ffb1], [25.20,55.27,0xffffff], [52.52,13.40,0xffd85b], [48.85,2.35,0x7ea1ff], [-26.20,28.04,0x43ffb1]
    ];
    const hub = latLon(6.52, 3.37, 2.5);
    cities.forEach(([lat, lon, color], index) => {
        const point = latLon(lat, lon, 2.5);
        const marker = new THREE.Mesh(new THREE.SphereGeometry(index === 3 ? .075 : .052, 12, 12), new THREE.MeshBasicMaterial({ color }));
        marker.position.copy(point); world.add(marker);
        const halo = new THREE.Mesh(new THREE.RingGeometry(.08, .13, 24), new THREE.MeshBasicMaterial({ color, transparent: true, opacity: .45, side: THREE.DoubleSide }));
        halo.position.copy(point); halo.lookAt(new THREE.Vector3(0,0,0)); world.add(halo);
        if (index !== 3) {
            const midpoint = hub.clone().add(point).multiplyScalar(.5).normalize().multiplyScalar(3.45);
            const curve = new THREE.QuadraticBezierCurve3(hub, midpoint, point);
            const geometry = new THREE.BufferGeometry().setFromPoints(curve.getPoints(42));
            const line = new THREE.Line(geometry, new THREE.LineBasicMaterial({ color, transparent: true, opacity: .38 }));
            world.add(line);
            if (!reducedMotion && !mobile) {
                const traveller = new THREE.Mesh(new THREE.SphereGeometry(.035, 8, 8), new THREE.MeshBasicMaterial({ color: 0xffffff })); world.add(traveller); traveller.userData.curve = curve; traveller.userData.offset = index / cities.length;
            }
        }
    });

    const starsGeometry = new THREE.BufferGeometry();
    const count = mobile ? 80 : 180; const positions = new Float32Array(count * 3);
    for (let i=0;i<positions.length;i+=3) { positions[i]=(Math.random()-.5)*13; positions[i+1]=(Math.random()-.5)*9; positions[i+2]=(Math.random()-.5)*6; }
    starsGeometry.setAttribute('position', new THREE.BufferAttribute(positions,3));
    scene.add(new THREE.Points(starsGeometry,new THREE.PointsMaterial({color:0xffffff,size:.018,transparent:true,opacity:.42})));

    const pointer = {x:0,y:0};
    const onPointer = (event) => { pointer.x=(event.clientX/window.innerWidth-.5)*.5; pointer.y=(event.clientY/window.innerHeight-.5)*.32; };
    const resize = () => { const w=container.clientWidth,h=container.clientHeight||600; camera.aspect=w/h; camera.updateProjectionMatrix(); renderer.setSize(w,h); world.position.set(w>1024?2.35:.65,.15,0); world.scale.setScalar(w<640?.78:1); };
    window.addEventListener('pointermove',onPointer,{passive:true}); window.addEventListener('resize',resize,{passive:true}); resize();

    const clock = new THREE.Clock(); let frame;
    const render = () => { const t=clock.getElapsedTime(); if(!reducedMotion){ world.rotation.y += ((pointer.x + t*.045)-world.rotation.y)*.018; world.rotation.x += (-pointer.y-world.rotation.x)*.025; world.children.forEach((child)=>{ if(child.userData.curve){ const p=child.userData.curve.getPoint((t*.08+child.userData.offset)%1); child.position.copy(p); } }); } renderer.render(scene,camera); frame=requestAnimationFrame(render); };
    render();

    const trigger = ScrollTrigger.create({trigger:container,start:'top top',end:'bottom top',scrub:true,onUpdate:({progress})=>{ world.position.y=progress*1.25+.15; const s=(mobile?.78:1)-progress*.14; world.scale.setScalar(s); }});
    container._ghostCleanup = () => { cancelAnimationFrame(frame); trigger.kill(); window.removeEventListener('pointermove',onPointer); window.removeEventListener('resize',resize); renderer.dispose(); renderer.domElement.remove(); };
};

const initialiseMotion = (root) => {
    if (reducedMotion) return;
    gsap.fromTo(root.querySelectorAll('[data-hero-reveal]'), {y:32,opacity:0}, {y:0,opacity:1,duration:.9,stagger:.075,ease:'power3.out'});
    gsap.fromTo(root.querySelectorAll('[data-float-card]'), {scale:.78,opacity:0,y:18}, {scale:1,opacity:1,y:0,duration:.8,stagger:.11,ease:'back.out(1.45)',delay:.3});
    root.querySelectorAll('[data-reveal]').forEach((el)=>gsap.from(el,{y:42,opacity:0,duration:.85,ease:'power3.out',scrollTrigger:{trigger:el,start:'top 88%',once:true}}));
    root.querySelectorAll('[data-country-card]').forEach((card)=>{ card.addEventListener('pointermove',(event)=>{ if(mobile)return; const r=card.getBoundingClientRect(); card.style.setProperty('--rx',`${((event.clientY-r.top)/r.height-.5)*-6}deg`); card.style.setProperty('--ry',`${((event.clientX-r.left)/r.width-.5)*8}deg`); }); card.addEventListener('pointerleave',()=>{card.style.setProperty('--rx','0deg');card.style.setProperty('--ry','0deg');}); });
};

const initialiseStorefront = () => {
    const root=document.querySelector('[data-storefront-home]'); if(!root||root.dataset.experienceReady==='true')return; root.dataset.experienceReady='true'; initialiseMotion(root);
    const canvas=root.querySelector('[data-hero-canvas]'); if(canvas){ const start=()=>{try{initialiseHeroScene(canvas)}catch(error){canvas.classList.add('storefront-canvas-fallback');console.warn('3D global scene unavailable.',error)}}; 'requestIdleCallback' in window?requestIdleCallback(start,{timeout:500}):setTimeout(start,50); }
};

if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initialiseStorefront);else initialiseStorefront();
document.addEventListener('livewire:navigated',initialiseStorefront);
