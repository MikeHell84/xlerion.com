import React, { useEffect, useRef, useState, useCallback } from 'react';
import * as THREE from 'three';

const PARALLAX_IMAGES = [
    'blog-bitacora-parallax.jpg',
    'contacto-parallax.jpg',
    'convocatorias-alianzas-parallax.jpg',
    'cronograma-progreso-parallax.jpg',
    'documentacion-parallax.jpg',
    'documentacion-recursos-parallax.jpg',
    'filosofia-parallax.jpg',
    'fundador-parallax.jpg',
    'inversionistas-alianzas-parallax.jpg',
    'legal-privacidad-parallax.jpg',
    'noticias-eventos-parallax.jpg',
    'proyectos-parallax.jpg',
    'servicios-productos-parallax.jpg',
    'soluciones-parallax.jpg'
];

const getRandomImage = () => {
    return PARALLAX_IMAGES[Math.floor(Math.random() * PARALLAX_IMAGES.length)];
};

const EvolutionaryScene = () => {
    const containerRef = useRef(null);
    const sceneRef = useRef(null);
    const cameraRef = useRef(null);
    const rendererRef = useRef(null);
    const groupRef = useRef(null);
    const phaseRef = useRef(0);
    const phaseStartTimeRef = useRef(null);
    const animationIdRef = useRef(null);
    const objectsRef = useRef([]);
    const parallaxImageRef = useRef(null);
    const isTransitioningRef = useRef(false);
    const [currentImage, setCurrentImage] = useState(() => getRandomImage());
    const [imageOpacity, setImageOpacity] = useState(1);

    const changeImage = useCallback(() => {
        setImageOpacity(0);
        setTimeout(() => {
            const newImage = getRandomImage();
            setCurrentImage(newImage);
            setTimeout(() => {
                setImageOpacity(1);
            }, 50);
        }, 500);
    }, []);

    const PHASE_DURATION = 5000; // 5 seconds
    const TRANSITION_DURATION = 1500; // 1.5 seconds for morphing transition
    const PHASES = [
        'Big Bang',
        'Abstract Universe',
        'Earth',
        'Organic Life',
        'Human Technology',
        'AI/Chips'
    ];

    useEffect(() => {
        if (!containerRef.current) return;

        // Initialize phase start time
        phaseStartTimeRef.current = Date.now();

        // Scene setup
        const scene = new THREE.Scene();
        sceneRef.current = scene;

        // Camera setup
        const camera = new THREE.PerspectiveCamera(
            75,
            containerRef.current.clientWidth / containerRef.current.clientHeight,
            0.1,
            1000
        );
        camera.position.z = 20;
        cameraRef.current = camera;

        // Renderer setup with transparent background
        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(containerRef.current.clientWidth, containerRef.current.clientHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        containerRef.current.appendChild(renderer.domElement);
        rendererRef.current = renderer;

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);

        const pointLight = new THREE.PointLight(0xffffff, 1);
        pointLight.position.set(10, 10, 10);
        scene.add(pointLight);

        // Rotating group
        const group = new THREE.Group();
        scene.add(group);
        groupRef.current = group;

        // Phase creation functions
        const createBigBang = () => {
            const objects = [];
            const geometry = new THREE.SphereGeometry(0.2, 8, 8);
            const colors = [0x00e9fa, 0x00c5d9, 0x0099b8, 0x006d97]; // Cyan palette
            for (let i = 0; i < 100; i++) {
                const color = colors[Math.floor(Math.random() * colors.length)];
                const material = new THREE.MeshPhongMaterial({
                    color: color,
                    emissive: color,
                    emissiveIntensity: 0.5,
                    wireframe: false,
                    transparent: true,
                    opacity: 0
                });
                const mesh = new THREE.Mesh(geometry, material);
                mesh.position.set(
                    (Math.random() - 0.5) * 40,
                    (Math.random() - 0.5) * 40,
                    (Math.random() - 0.5) * 40
                );
                const baseScale = Math.random() * 0.5 + 0.1;
                mesh.userData.targetScale = { x: baseScale, y: baseScale, z: baseScale };
                mesh.scale.set(0.01, 0.01, 0.01);
                group.add(mesh);
                objects.push({ mesh, geometry });
            }
            return objects;
        };

        const createAbstractUniverse = () => {
            const objects = [];
            const icoGeometry = new THREE.IcosahedronGeometry(2, 3);
            const icoMaterial = new THREE.MeshPhongMaterial({
                color: 0x00e9fa,
                emissive: 0x00e9fa,
                emissiveIntensity: 0.6,
                wireframe: true,
                transparent: true,
                opacity: 0
            });
            const icoMesh = new THREE.Mesh(icoGeometry, icoMaterial);
            icoMesh.userData.targetScale = { x: 1, y: 1, z: 1 };
            icoMesh.scale.set(0.01, 0.01, 0.01);
            group.add(icoMesh);
            objects.push({ mesh: icoMesh, geometry: icoGeometry });

            // Orbiting spheres
            const sphereGeometry = new THREE.SphereGeometry(0.3, 8, 8);
            for (let i = 0; i < 20; i++) {
                const material = new THREE.MeshPhongMaterial({
                    color: 0xffffff,
                    emissive: 0x00c5d9,
                    emissiveIntensity: 0.7,
                    wireframe: false,
                    transparent: true,
                    opacity: 0
                });
                const mesh = new THREE.Mesh(sphereGeometry, material);
                const angle = (i / 20) * Math.PI * 2;
                mesh.position.set(Math.cos(angle) * 8, Math.sin(angle) * 8, 0);
                mesh.userData.targetScale = { x: 1, y: 1, z: 1 };
                mesh.scale.set(0.01, 0.01, 0.01);
                group.add(mesh);
                objects.push({ mesh, geometry: sphereGeometry, orbitAngle: angle });
            }
            return objects;
        };

        const createEarth = () => {
            const objects = [];

            // Main sphere
            const earthGeometry = new THREE.SphereGeometry(2, 32, 32);
            const earthMaterial = new THREE.MeshPhongMaterial({
                color: 0x0099b8,
                emissive: 0x006d97,
                emissiveIntensity: 0.5,
                wireframe: false,
                transparent: true,
                opacity: 0
            });
            const earthMesh = new THREE.Mesh(earthGeometry, earthMaterial);
            earthMesh.userData.targetScale = { x: 1, y: 1, z: 1 };
            earthMesh.scale.set(0.01, 0.01, 0.01);
            group.add(earthMesh);
            objects.push({ mesh: earthMesh, geometry: earthGeometry });

            // Atmosphere
            const atmosphereGeometry = new THREE.SphereGeometry(2.1, 32, 32);
            const atmosphereMaterial = new THREE.MeshPhongMaterial({
                color: 0x00e9fa,
                emissive: 0x00c5d9,
                emissiveIntensity: 0.4,
                transparent: true,
                opacity: 0,
                wireframe: true
            });
            const atmosphereMesh = new THREE.Mesh(atmosphereGeometry, atmosphereMaterial);
            atmosphereMesh.userData.targetScale = { x: 1, y: 1, z: 1 };
            atmosphereMesh.userData.targetOpacity = 0.3;
            atmosphereMesh.scale.set(0.01, 0.01, 0.01);
            group.add(atmosphereMesh);
            objects.push({ mesh: atmosphereMesh, geometry: atmosphereGeometry });

            return objects;
        };

        const createOrganicLife = () => {
            const objects = [];
            const torusGeometry = new THREE.TorusGeometry(1, 0.4, 8, 100);
            const colors = [0x00e9fa, 0x00f9b8, 0x00d9c5, 0x00b89f]; // Cyan-green palette
            for (let i = 0; i < 15; i++) {
                const color = colors[Math.floor(Math.random() * colors.length)];
                const material = new THREE.MeshPhongMaterial({
                    color: color,
                    emissive: color,
                    emissiveIntensity: 0.4,
                    wireframe: false,
                    transparent: true,
                    opacity: 0
                });
                const mesh = new THREE.Mesh(torusGeometry, material);
                mesh.position.set(
                    (Math.random() - 0.5) * 15,
                    (Math.random() - 0.5) * 15,
                    (Math.random() - 0.5) * 15
                );
                mesh.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI);
                mesh.userData.targetScale = { x: 1, y: 1, z: 1 };
                mesh.scale.set(0.01, 0.01, 0.01);
                group.add(mesh);
                objects.push({ mesh, geometry: torusGeometry });
            }
            return objects;
        };

        const createHumanTechnology = () => {
            const objects = [];
            const boxGeometry = new THREE.BoxGeometry(0.8, 0.8, 0.8);
            for (let i = 0; i < 12; i++) {
                const material = new THREE.MeshPhongMaterial({
                    color: 0xffffff,
                    emissive: 0x00e9fa,
                    emissiveIntensity: 0.8,
                    wireframe: false,
                    transparent: true,
                    opacity: 0
                });
                const mesh = new THREE.Mesh(boxGeometry, material);
                const angle = (i / 12) * Math.PI * 2;
                mesh.position.set(Math.cos(angle) * 8, Math.sin(angle) * 8, 0);
                mesh.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI);
                mesh.userData.targetScale = { x: 1, y: 1, z: 1 };
                mesh.scale.set(0.01, 0.01, 0.01);
                group.add(mesh);
                objects.push({ mesh, geometry: boxGeometry });
            }
            return objects;
        };

        const createAIChips = () => {
            const objects = [];
            const chipGeometry = new THREE.BoxGeometry(0.3, 0.3, 0.3);
            for (let i = 0; i < 25; i++) {
                const material = new THREE.MeshPhongMaterial({
                    color: 0x00e9fa,
                    emissive: 0x00e9fa,
                    emissiveIntensity: 1.0,
                    wireframe: false,
                    transparent: true,
                    opacity: 0
                });
                const mesh = new THREE.Mesh(chipGeometry, material);
                mesh.position.set(
                    (Math.random() - 0.5) * 20,
                    (Math.random() - 0.5) * 20,
                    (Math.random() - 0.5) * 20
                );
                mesh.userData.targetScale = { x: 1, y: 1, z: 1 };
                mesh.scale.set(0.01, 0.01, 0.01);
                group.add(mesh);
                objects.push({ mesh, geometry: chipGeometry });
            }
            return objects;
        };

        // Clear objects helper
        const clearObjects = () => {
            objectsRef.current.forEach(obj => {
                group.remove(obj.mesh);
                obj.geometry.dispose();
                if (obj.mesh.material) {
                    if (Array.isArray(obj.mesh.material)) {
                        obj.mesh.material.forEach(m => m.dispose());
                    } else {
                        obj.mesh.material.dispose();
                    }
                }
            });
            objectsRef.current = [];
        };

        // Animation loop
        const animate = () => {
            animationIdRef.current = requestAnimationFrame(animate);

            // Update phase
            const now = Date.now();
            const elapsedInPhase = now - phaseStartTimeRef.current;
            const fadeOutStart = PHASE_DURATION - TRANSITION_DURATION;

            // Start fade out before transition
            if (elapsedInPhase > fadeOutStart && !isTransitioningRef.current) {
                isTransitioningRef.current = true;
            }

            // Handle phase transition
            if (elapsedInPhase > PHASE_DURATION) {
                clearObjects();
                phaseRef.current = (phaseRef.current + 1) % PHASES.length;
                phaseStartTimeRef.current = now;
                isTransitioningRef.current = false;

                // Create new phase objects
                switch (phaseRef.current) {
                    case 0:
                        objectsRef.current = createBigBang();
                        break;
                    case 1:
                        objectsRef.current = createAbstractUniverse();
                        break;
                    case 2:
                        objectsRef.current = createEarth();
                        break;
                    case 3:
                        objectsRef.current = createOrganicLife();
                        break;
                    case 4:
                        objectsRef.current = createHumanTechnology();
                        break;
                    case 5:
                        objectsRef.current = createAIChips();
                        break;
                    default:
                        break;
                }
            }

            // Smooth transition animation
            const timeSincePhaseStart = now - phaseStartTimeRef.current;

            // Fade in at the start
            if (timeSincePhaseStart < TRANSITION_DURATION) {
                const fadeInProgress = timeSincePhaseStart / TRANSITION_DURATION;
                const easeProgress = fadeInProgress < 0.5
                    ? 2 * fadeInProgress * fadeInProgress
                    : 1 - Math.pow(-2 * fadeInProgress + 2, 2) / 2;

                objectsRef.current.forEach(obj => {
                    // Fade in
                    if (obj.mesh.material.transparent) {
                        obj.mesh.material.opacity = easeProgress;
                        if (obj.mesh.userData.targetOpacity !== undefined) {
                            obj.mesh.material.opacity = easeProgress * obj.mesh.userData.targetOpacity;
                        }
                    }

                    // Scale up
                    if (obj.mesh.userData.targetScale) {
                        obj.mesh.scale.x = obj.mesh.userData.targetScale.x * easeProgress;
                        obj.mesh.scale.y = obj.mesh.userData.targetScale.y * easeProgress;
                        obj.mesh.scale.z = obj.mesh.userData.targetScale.z * easeProgress;
                    }
                });
            }

            // Fade out before transition
            if (isTransitioningRef.current && elapsedInPhase > fadeOutStart) {
                const fadeOutProgress = (elapsedInPhase - fadeOutStart) / TRANSITION_DURATION;
                const easeFadeOut = 1 - (fadeOutProgress < 0.5
                    ? 2 * fadeOutProgress * fadeOutProgress
                    : 1 - Math.pow(-2 * fadeOutProgress + 2, 2) / 2);

                objectsRef.current.forEach(obj => {
                    // Fade out
                    if (obj.mesh.material.transparent) {
                        const targetOp = obj.mesh.userData.targetOpacity !== undefined
                            ? obj.mesh.userData.targetOpacity
                            : 1;
                        obj.mesh.material.opacity = targetOp * easeFadeOut;
                    }

                    // Scale down
                    if (obj.mesh.userData.targetScale) {
                        obj.mesh.scale.x = obj.mesh.userData.targetScale.x * easeFadeOut;
                        obj.mesh.scale.y = obj.mesh.userData.targetScale.y * easeFadeOut;
                        obj.mesh.scale.z = obj.mesh.userData.targetScale.z * easeFadeOut;
                    }
                });
            }

            // Rotate group
            group.rotation.x += 0.001;
            group.rotation.y += 0.002;

            // Rotate individual objects in orbit phases
            if (phaseRef.current === 1) {
                objectsRef.current.forEach((obj, index) => {
                    if (index > 0 && obj.orbitAngle !== undefined) {
                        obj.orbitAngle += 0.01;
                        obj.mesh.position.x = Math.cos(obj.orbitAngle) * 8;
                        obj.mesh.position.y = Math.sin(obj.orbitAngle) * 8;
                    }
                });
            }

            renderer.render(scene, camera);
        };

        // Initialize with Big Bang
        objectsRef.current = createBigBang();
        animate();

        // Handle window resize
        const handleResize = () => {
            if (!containerRef.current) return;
            const width = containerRef.current.clientWidth;
            const height = containerRef.current.clientHeight;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
        };

        window.addEventListener('resize', handleResize);

        // Efecto parallax con movimiento del mouse
        const handleMouseMove = (e) => {
            if (parallaxImageRef.current) {
                const centerX = window.innerWidth / 2;
                const centerY = window.innerHeight / 2;
                const distX = (e.clientX - centerX) * 0.02;
                const distY = (e.clientY - centerY) * 0.02;
                parallaxImageRef.current.style.transform = `translate(${distX}px, ${distY}px)`;
            }
        };
        window.addEventListener('mousemove', handleMouseMove);

        // Temporizador para cambiar imagen cada 10 segundos
        const imageInterval = setInterval(changeImage, 10000);

        // Cleanup
        const container = containerRef.current;
        return () => {
            window.removeEventListener('resize', handleResize);
            window.removeEventListener('mousemove', handleMouseMove);
            clearInterval(imageInterval);
            if (animationIdRef.current) {
                cancelAnimationFrame(animationIdRef.current);
            }
            clearObjects();
            renderer.dispose();
            if (container && renderer.domElement.parentNode === container) {
                container.removeChild(renderer.domElement);
            }
        };
    }, [PHASES.length, changeImage]);

    return (
        <div className="absolute inset-0 z-0">
            {/* Fondo Parallax */}
            <img
                ref={parallaxImageRef}
                src={`/images/${currentImage}`}
                alt="parallax-background"
                className="absolute inset-0 w-full h-full object-cover"
                style={{
                    willChange: 'transform, opacity',
                    opacity: imageOpacity,
                    transition: 'opacity 500ms ease-in-out'
                }}
            />
            {/* Overlay gradient oscuro con opacidad 50% y blur */}
            <div className="absolute inset-0 bg-gradient-to-b from-black/50 via-black/50 to-black/50 pointer-events-none backdrop-blur-sm" />
            {/* Canvas de Three.js con blur leve */}
            <div ref={containerRef} className="absolute inset-0 pointer-events-auto" style={{ filter: 'blur(2px)' }} />
        </div>
    );
};

export default EvolutionaryScene;
