import React, { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import * as THREE from 'three';
import {
  ChevronRight, Mail, Code, Zap, Activity, Cpu, Rocket,
  Target, Send, AtSign, MessageCircle,
  Briefcase, Clock, CheckCircle2, Users, MapPin,
  Heart, Shield, Database, AlertTriangle, Info, Terminal,
  TrendingUp, BookOpen, Wrench
} from 'lucide-react';
import Footer from './components/Footer';
import ContactForm from './components/ContactForm';
import EvolutionaryScene from './components/EvolutionaryScene';

/**
 * XLERION CORPORATE WEB V3.1 - FIX & OPTIMIZATION
 * Arquitectura basada en EstiloWeb.md y ContenidoWeb.md
 */

// Componente de Escena 3D con animaciones mapeadas de ANIMACIONES_3D.md
const ThreeScene = ({ actionType }) => {
  const containerRef = useRef(null);
  const rendererRef = useRef(null);
  const modelGroupRef = useRef(null);
  const sceneRef = useRef(null);
  const cameraRef = useRef(null);
  const animationStateRef = useRef({ actionType: 'default' });
  const parallaxImageRef = useRef(null);
  const currentImageRef = useRef(null);
  const mouseRef = useRef({ x: 0, y: 0 });
  const [imageOpacity, setImageOpacity] = React.useState(1);

  // Lista de imágenes disponibles
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
    'Oficina0010.jpg',
    'Oficina0013.jpg',
    'proyectos-parallax.jpg',
    'servicios-productos-parallax.jpg',
    'soluciones-parallax.jpg'
  ];

  const getRandomImage = () => {
    return PARALLAX_IMAGES[Math.floor(Math.random() * PARALLAX_IMAGES.length)];
  };

  const changeImage = () => {
    // Fade out
    setImageOpacity(0);

    // Esperar a que termine el fade out antes de cambiar la imagen
    setTimeout(() => {
      const newImage = getRandomImage();
      currentImageRef.current = newImage;
      if (parallaxImageRef.current) {
        parallaxImageRef.current.src = `/images/${newImage}`;
      }
      // Fade in después de cambiar la imagen
      setTimeout(() => {
        setImageOpacity(1);
      }, 50);
    }, 500); // Duración del fade out
  };

  // Inicializar imagen al montar
  useEffect(() => {
    currentImageRef.current = getRandomImage();
  }, []);

  // Setup inicial - solo una vez
  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.z = 5;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.setSize(window.innerWidth, window.innerHeight);
    container.appendChild(renderer.domElement);

    rendererRef.current = renderer;
    sceneRef.current = scene;
    cameraRef.current = camera;

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);
    const pointLight = new THREE.PointLight(0x00e9fa, 3);
    pointLight.position.set(5, 5, 5);
    scene.add(pointLight);

    const group = new THREE.Group();
    modelGroupRef.current = group;
    scene.add(group);

    // Geometría Modular Mejorada con múltiples capas
    // Outer wireframe icosahedron
    const geometry = new THREE.IcosahedronGeometry(1.8, 1);
    const material = new THREE.MeshStandardMaterial({
      color: 0x00e9fa,
      wireframe: true,
      transparent: true,
      opacity: 0.8
    });
    const mesh = new THREE.Mesh(geometry, material);
    group.add(mesh);

    // Inner glowing sphere
    const innerGeom = new THREE.SphereGeometry(0.7, 32, 32);
    const innerMat = new THREE.MeshStandardMaterial({
      color: 0x00e9fa,
      emissive: 0x00e9fa,
      emissiveIntensity: 0.5
    });
    const innerMesh = new THREE.Mesh(innerGeom, innerMat);
    group.add(innerMesh);

    // Array de colores para transición
    const colors = [0x00e9fa, 0x00d9ff, 0x00f0ff, 0x00e9fa];
    let colorIndex = 0;

    // Sistema expandido de partículas orbitales alrededor de la esfera
    const orbitParticles = [];

    // Órbita 1: Plano XY
    for (let i = 0; i < 8; i++) {
      const angle = (i / 8) * Math.PI * 2;
      const orbitRadius = 1.5;
      const particle = new THREE.Mesh(
        new THREE.SphereGeometry(0.08, 16, 16),
        new THREE.MeshStandardMaterial({
          color: 0x00e9fa,
          emissive: 0x00e9fa,
          emissiveIntensity: 0.9
        })
      );
      particle.position.x = Math.cos(angle) * orbitRadius;
      particle.position.y = Math.sin(angle) * orbitRadius;
      particle.position.z = 0;
      particle.userData.angle = angle;
      particle.userData.speed = 1;
      particle.userData.orbitRadius = orbitRadius;
      particle.userData.plane = 'xy';
      group.add(particle);
      orbitParticles.push(particle);
    }

    // Órbita 2: Plano YZ
    for (let i = 0; i < 6; i++) {
      const angle = (i / 6) * Math.PI * 2;
      const orbitRadius = 1.8;
      const particle = new THREE.Mesh(
        new THREE.SphereGeometry(0.07, 16, 16),
        new THREE.MeshStandardMaterial({
          color: 0x00d9ff,
          emissive: 0x00d9ff,
          emissiveIntensity: 1.0
        })
      );
      particle.position.y = Math.cos(angle) * orbitRadius;
      particle.position.z = Math.sin(angle) * orbitRadius;
      particle.position.x = 0;
      particle.userData.angle = angle;
      particle.userData.speed = 0.7;
      particle.userData.orbitRadius = orbitRadius;
      particle.userData.plane = 'yz';
      group.add(particle);
      orbitParticles.push(particle);
    }

    // Órbita 3: Plano XZ
    for (let i = 0; i < 10; i++) {
      const angle = (i / 10) * Math.PI * 2;
      const orbitRadius = 2.2;
      const particle = new THREE.Mesh(
        new THREE.SphereGeometry(0.06, 16, 16),
        new THREE.MeshStandardMaterial({
          color: 0x00f0ff,
          emissive: 0x00f0ff,
          emissiveIntensity: 1.0
        })
      );
      particle.position.x = Math.cos(angle) * orbitRadius;
      particle.position.z = Math.sin(angle) * orbitRadius;
      particle.position.y = 0;
      particle.userData.angle = angle;
      particle.userData.speed = 1.2;
      particle.userData.orbitRadius = orbitRadius;
      particle.userData.plane = 'xz';
      group.add(particle);
      orbitParticles.push(particle);
    }

    // Sistema antiguo de partículas (pequeños cubos) - se mantiene
    const particleGeom = new THREE.BoxGeometry(0.1, 0.1, 0.1);
    const particleMat = new THREE.MeshStandardMaterial({
      color: 0x00e9fa,
      emissive: 0x00e9fa,
      emissiveIntensity: 0.8
    });
    const particles = [];
    for (let i = 0; i < 12; i++) {
      const particle = new THREE.Mesh(particleGeom, particleMat.clone());
      const angle = (i / 12) * Math.PI * 2;
      particle.position.x = Math.cos(angle) * 2.5;
      particle.position.y = Math.sin(angle) * 2.5;
      particle.position.z = Math.cos(angle * 0.5) * 0.5;
      particle.userData.angle = angle;
      particle.userData.speed = 0.5 + Math.random() * 0.5;
      group.add(particle);
      particles.push(particle);
    }

    // Luces adicionales dinámicas
    const light1 = new THREE.PointLight(0x00e9fa, 2);
    light1.position.set(3, 3, 3);
    scene.add(light1);

    const light2 = new THREE.PointLight(0xff00ff, 1.5);
    light2.position.set(-3, -3, 3);
    scene.add(light2);

    const light3 = new THREE.PointLight(0x00ff88, 1);
    light3.position.set(0, 0, -5);
    scene.add(light3);

    let rafId = null;
    const animate = () => {
      const time = Date.now() * 0.001;
      const state = animationStateRef.current;

      if (modelGroupRef.current && state.actionType) {
        // Rotación dinámica en múltiples ejes
        const rotSpeedX = 0.002;
        const rotSpeedY = 0.003;
        const rotSpeedZ = 0.001;

        switch (state.actionType) {
          case 'identidad':
            modelGroupRef.current.rotation.y += 0.01;
            modelGroupRef.current.rotation.x += rotSpeedX;
            modelGroupRef.current.position.y = Math.sin(time * 0.5) * 0.3;
            // Pulso
            modelGroupRef.current.scale.set(
              1 + Math.sin(time * 2) * 0.05,
              1 + Math.sin(time * 2) * 0.05,
              1 + Math.sin(time * 2) * 0.05
            );
            break;
          case 'mision':
            modelGroupRef.current.rotation.y += 0.05;
            modelGroupRef.current.rotation.z += rotSpeedZ;
            // Movimiento ondulante
            modelGroupRef.current.position.y = Math.sin(time * 1.5) * 0.4;
            modelGroupRef.current.position.x = Math.cos(time * 1.2) * 0.3;
            break;
          case 'vision':
            modelGroupRef.current.position.y = Math.sin(time * 2) * 0.5;
            modelGroupRef.current.rotation.x = Math.cos(time * 0.5) * 0.2;
            modelGroupRef.current.rotation.z += rotSpeedZ * 2;
            break;
          case 'toolkit':
            modelGroupRef.current.rotation.z += 0.1;
            modelGroupRef.current.rotation.x += 0.02;
            modelGroupRef.current.rotation.y += rotSpeedY * 2;
            // Escala variable
            modelGroupRef.current.scale.set(
              1 + Math.sin(time * 3) * 0.08,
              1 + Math.sin(time * 3) * 0.08,
              1 + Math.sin(time * 3) * 0.08
            );
            break;
          case 'servicios':
            modelGroupRef.current.rotation.y = Math.sin(time) * 0.8;
            modelGroupRef.current.position.x = Math.cos(time * 1.5) * 0.3;
            modelGroupRef.current.rotation.z += rotSpeedZ;
            break;
          default:
            modelGroupRef.current.rotation.y += 0.02;
            modelGroupRef.current.rotation.x += 0.005;
            modelGroupRef.current.rotation.z += rotSpeedZ * 0.5;
            modelGroupRef.current.position.y = Math.sin(time * 0.5) * 0.3;
            // Pulso suave en default
            modelGroupRef.current.scale.set(
              1 + Math.sin(time * 1.5) * 0.03,
              1 + Math.sin(time * 1.5) * 0.03,
              1 + Math.sin(time * 1.5) * 0.03
            );
        }

        // Animar partículas orbitales alrededor de la esfera
        orbitParticles.forEach((particle) => {
          const plane = particle.userData.plane;
          const speed = particle.userData.speed;
          const radius = particle.userData.orbitRadius;
          const newAngle = particle.userData.angle + time * speed * 0.3;

          if (plane === 'xy') {
            particle.position.x = Math.cos(newAngle) * radius;
            particle.position.y = Math.sin(newAngle) * radius;
          } else if (plane === 'yz') {
            particle.position.y = Math.cos(newAngle) * radius;
            particle.position.z = Math.sin(newAngle) * radius;
          } else if (plane === 'xz') {
            particle.position.x = Math.cos(newAngle) * radius;
            particle.position.z = Math.sin(newAngle) * radius;
          }

          // Rotación de partícula
          particle.rotation.x += 0.02;
          particle.rotation.y += 0.03;
        });

        // Animar partículas en órbita
        particles.forEach((particle, idx) => {
          const orbitSpeed = particle.userData.speed;
          const newAngle = particle.userData.angle + time * orbitSpeed * 0.5;
          particle.position.x = Math.cos(newAngle) * 2.5;
          particle.position.y = Math.sin(newAngle) * 2.5;
          particle.position.z = Math.cos(newAngle * 0.5) * 0.5;

          // Rotación y escala de partículas
          particle.rotation.x += 0.05;
          particle.rotation.y += 0.07;
          particle.scale.set(
            1 + Math.sin(time * 3 + idx) * 0.3,
            1 + Math.sin(time * 3 + idx) * 0.3,
            1 + Math.sin(time * 3 + idx) * 0.3
          );
        });
      }

      // Mover luces dinámicamente
      const lights = scene.children.filter(child => child instanceof THREE.Light && child instanceof THREE.PointLight);
      lights.forEach((light, idx) => {
        light.position.x = Math.cos(time * 0.5 + idx) * 5;
        light.position.y = Math.sin(time * 0.3 + idx) * 5;
        light.position.z = Math.cos(time * 0.7 + idx) * 3;
      });

      // Cambio de colores dinámico en el icosahedron y esfera interna
      const hue = (time * 0.1) % 1;
      const saturation = 1;
      const lightness = 0.5;
      const dynamicColor = new THREE.Color().setHSL(hue, saturation, lightness);

      // Aplicar color a todos los meshes del grupo
      group.children.forEach(child => {
        if (child.material) {
          child.material.color.copy(dynamicColor);
          if (child.material.emissive) {
            child.material.emissive.copy(dynamicColor);
          }
        }
      });

      renderer.render(scene, camera);
      rafId = requestAnimationFrame(animate);
    };
    animate();

    const handleResize = () => {
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(window.innerWidth, window.innerHeight);
    };
    window.addEventListener('resize', handleResize);

    // Efecto parallax con movimiento del mouse
    const handleMouseMove = (e) => {
      mouseRef.current = { x: e.clientX, y: e.clientY };
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

    return () => {
      window.removeEventListener('resize', handleResize);
      window.removeEventListener('mousemove', handleMouseMove);
      clearInterval(imageInterval);
      if (rafId) cancelAnimationFrame(rafId);
      if (container && renderer.domElement && container.contains(renderer.domElement)) {
        container.removeChild(renderer.domElement);
      }
      try {
        renderer.dispose();
      } catch (e) {
        console.warn('Renderer disposal error:', e);
      }
    };
  }, []); // Solo una vez al montar

  // Actualizar estado de animación cuando actionType cambia
  useEffect(() => {
    animationStateRef.current.actionType = actionType;
  }, [actionType]);

  return (
    <div className="absolute inset-0 z-0">
      {/* Fondo Parallax */}
      <img
        ref={parallaxImageRef}
        src={`/images/${currentImageRef.current || getRandomImage()}`}
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

// Componentes Atómicos de UI
const XlSectionHeader = ({ title, subtitle }) => (
  <div className="mb-16 border-l-2 border-[#00e9fa] pl-8">
    <h2 className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// {subtitle}</h2>
    <h3 className="text-4xl md:text-6xl font-black italic uppercase tracking-tighter text-white">{title}</h3>
  </div>
);

const XlCard = ({ title, icon: Icon, children, variant = "default", to }) => {
  const CardContent = (
    <>
      <div className="flex items-center gap-4 mb-6 relative z-10">
        {Icon && <Icon className="text-[#00e9fa] group-hover:scale-110 transition-transform" size={24} />}
        <h4 className="font-mono font-bold text-lg uppercase italic text-white">{title}</h4>
      </div>
      <div className="text-gray-400 text-sm leading-relaxed font-sans relative z-10">{children}</div>
      <div className="absolute -bottom-4 -right-4 text-[#00e9fa]/5 group-hover:text-[#00e9fa]/10 transition-colors pointer-events-none">
        {Icon && <Icon size={120} />}
      </div>
    </>
  );

  const baseClasses = `group p-8 border transition-all duration-500 ${variant === 'primary' ? 'bg-[#00e9fa]/5 border-[#00e9fa]/30' : 'bg-white/5 border-white/10'
    } hover:border-[#00e9fa]/60 rounded-sm relative overflow-hidden`;

  if (to) {
    return (
      <Link to={to} className={`${baseClasses} block cursor-pointer`}>
        {CardContent}
      </Link>
    );
  }

  return (
    <div className={baseClasses}>
      {CardContent}
    </div>
  );
};

export default function App() {
  const [activeAnim, setActiveAnim] = useState('default');
  const [scrolled, setScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [empresaOpen, setEmpresaOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 50);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    // Prevenir scroll cuando el menú móvil está abierto
    if (mobileMenuOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'unset';
    }
    return () => {
      document.body.style.overflow = 'unset';
    };
  }, [mobileMenuOpen]);

  const navTo = (id, anim) => {
    setActiveAnim(anim);
    setMobileMenuOpen(false); // Cerrar menú móvil al navegar
    const el = document.getElementById(id);
    if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: 'smooth' });
  };

  return (
    <div className="bg-black text-white selection:bg-[#00e9fa] selection:text-black min-h-screen overflow-x-hidden font-sans">
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Roboto+Mono:ital,wght@0,400;0,700;1,400&family=Work+Sans:wght@300;400;500;600;700&display=swap');
        .font-mono { font-family: 'Roboto Mono', monospace; }
        .font-sans { font-family: 'Inter', sans-serif; }
        .font-work { font-family: 'Work Sans', sans-serif; }
        .xl-btn-primary { 
          background: #00e9fa; color: #000; font-weight: 800; padding: 1rem 2.5rem; 
          border-radius: 2px; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.2em;
          transition: all 0.4s; border: 1px solid #00e9fa;
        }
        .xl-btn-primary:hover { background: transparent; color: #00e9fa; box-shadow: 0 0 30px rgba(0, 233, 250, 0.4); }
        .xl-input {
          background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); 
          border-radius: 2px; padding: 1rem; color: #fff; width: 100%; font-size: 0.8rem;
        }
        .xl-input:focus { outline: none; border-color: #00e9fa; background: rgba(0, 233, 250, 0.05); }
      `}</style>

      {/* Navegación Técnica */}
      <nav className={`fixed top-0 w-full z-50 transition-all duration-500 ${scrolled ? 'bg-black/80 backdrop-blur-sm py-1' : 'bg-black/50 backdrop-blur-sm py-2'} border-b border-[#00e9fa]/30`} style={{ boxShadow: '0 1px 10px rgba(0, 233, 250, 0.3)' }}>
        <div className="w-full px-4 md:px-8 flex items-center justify-between">
          {/* Logo with left and right spacing */}
          <div className="flex items-center gap-3 cursor-pointer relative z-50" onClick={() => navTo('home', 'default')}>
            <img src="/LogoX.svg" alt="Xlerion" className="h-9 md:h-10 object-contain" />
          </div>

          {/* Menú Desktop */}
          <div className="hidden lg:flex gap-10 text-[13px] font-work uppercase tracking-[0.2em] font-light">{/* Empresa dropdown */}
            <div className="relative group">
              <button className="hover:text-[#00e9fa] transition-colors">EMPRESA</button>
              <div className="absolute left-0 top-0 pt-12 hidden group-hover:block">
                <div className="bg-black/80 backdrop-blur-sm rounded-sm p-3">
                  <div className="flex flex-col gap-2 text-[13px] font-work uppercase tracking-[0.2em] font-light items-end">
                    <button onClick={() => navTo('identidad', 'identidad')} className="hover:text-[#00e9fa] transition-colors text-right">IDENTIDAD</button>
                    <button onClick={() => navTo('blog', 'vision')} className="hover:text-[#00e9fa] transition-colors text-right">BITÁCORA</button>
                    <button onClick={() => navTo('filosofia', 'identidad')} className="hover:text-[#00e9fa] transition-colors text-right">FILOSOFÍA</button>
                    <button onClick={() => navTo('legal', 'mision')} className="hover:text-[#00e9fa] transition-colors text-right">LEGAL</button>
                  </div>
                </div>
              </div>
            </div>

            {/* Rest of main nav */}
            <button onClick={() => navTo('toolkit', 'toolkit')} className="hover:text-[#00e9fa] transition-colors">TOOLKIT</button>
            <button onClick={() => navTo('servicios', 'servicios')} className="hover:text-[#00e9fa] transition-colors">SERVICIOS</button>
            <button onClick={() => navTo('soluciones', 'servicios')} className="hover:text-[#00e9fa] transition-colors">SOLUCIONES</button>
            <button onClick={() => navTo('proyectos', 'vision')} className="hover:text-[#00e9fa] transition-colors">PROYECTOS</button>
            <button onClick={() => navTo('documentacion', 'mision')} className="hover:text-[#00e9fa] transition-colors">DOCUMENTACIÓN</button>
            <button onClick={() => navTo('estrategia', 'vision')} className="hover:text-[#00e9fa] transition-colors">ESTRATEGIA</button>
            <button onClick={() => navTo('contacto', 'mision')} className="hover:text-[#00e9fa] transition-colors">CONTACTO</button>
          </div>

          {/* Botón Menú Hamburguesa Mobile */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="lg:hidden relative z-50 w-10 h-10 flex flex-col items-center justify-center gap-1.5"
            aria-label="Toggle menu"
          >
            <span className={`w-6 h-0.5 bg-[#00e9fa] transition-all duration-300 ${mobileMenuOpen ? 'rotate-45 translate-y-2' : ''}`}></span>
            <span className={`w-6 h-0.5 bg-[#00e9fa] transition-all duration-300 ${mobileMenuOpen ? 'opacity-0' : ''}`}></span>
            <span className={`w-6 h-0.5 bg-[#00e9fa] transition-all duration-300 ${mobileMenuOpen ? '-rotate-45 -translate-y-2' : ''}`}></span>
          </button>
        </div>
      </nav>

      {/* Menú Móvil Fullscreen */}
      <div className={`fixed inset-0 z-40 bg-black/95 backdrop-blur-md transition-transform duration-500 lg:hidden ${mobileMenuOpen ? 'translate-x-0' : 'translate-x-full'}`}>
        <div className="h-full flex flex-col items-center justify-center px-8 pt-20 pb-8 overflow-y-auto">
          <div className="flex flex-col gap-6 text-center w-full max-w-sm">
            {/* Empresa con submenu */}
            <div className="border-b border-white/10 pb-6">
              <button
                onClick={() => setEmpresaOpen(!empresaOpen)}
                className="text-2xl font-work uppercase tracking-[0.2em] font-light text-[#00e9fa] mb-4 flex items-center justify-center gap-2 w-full"
              >
                EMPRESA
                <span className={`transition-transform duration-300 ${empresaOpen ? 'rotate-180' : ''}`}>▼</span>
              </button>
              {empresaOpen && (
                <div className="flex flex-col gap-3 mt-4">
                  <button onClick={() => navTo('identidad', 'identidad')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">IDENTIDAD</button>
                  <button onClick={() => navTo('blog', 'vision')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">BITÁCORA</button>
                  <button onClick={() => navTo('filosofia', 'identidad')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">FILOSOFÍA</button>
                  <button onClick={() => navTo('legal', 'mision')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">LEGAL</button>
                </div>
              )}
            </div>

            {/* Rest of menu items */}
            <button onClick={() => navTo('toolkit', 'toolkit')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">TOOLKIT</button>
            <button onClick={() => navTo('servicios', 'servicios')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">SERVICIOS</button>
            <button onClick={() => navTo('soluciones', 'servicios')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">SOLUCIONES</button>
            <button onClick={() => navTo('proyectos', 'vision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">PROYECTOS</button>
            <button onClick={() => navTo('documentacion', 'mision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">DOCUMENTACIÓN</button>
            <button onClick={() => navTo('estrategia', 'vision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">ESTRATEGIA</button>
            <button onClick={() => navTo('contacto', 'mision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors border-t border-white/10 pt-6">CONTACTO</button>
          </div>
        </div>
      </div>

      {/* Hero: Xlerion - Soluciones Disruptivas */}
      <section id="home" className="relative h-screen flex items-center justify-center overflow-hidden">
        <EvolutionaryScene />
        <div className="relative z-10 text-center px-6 flex flex-col items-center justify-center h-full">
          <img src="/LogoX.svg" alt="Xlerion" className="mx-auto mb-6 h-32 md:h-40 lg:h-56 object-contain" style={{ filter: 'drop-shadow(0 0 30px rgba(0, 233, 250, 0.6))' }} />
          <p className="text-lg md:text-xl text-gray-400 mb-12 mt-2 max-w-2xl mx-auto font-light leading-relaxed">
            Modularidad que transforma, innovación que perdura. <br />
            <span className="text-white">Soluciones disruptivas</span> para un futuro escalable.
          </p>
          <div className="flex flex-col sm:flex-row gap-6 justify-center pb-12">
            <button onClick={() => navTo('toolkit', 'toolkit')} className="xl-btn-primary">Explorar Toolkit</button>
            <button onClick={() => navTo('servicios', 'servicios')} className="px-10 py-3 border border-white/20 text-xs font-bold uppercase tracking-widest hover:bg-white/5 transition-all">Servicios</button>
          </div>
          <div className="mt-8 text-center">
            <p className="text-sm md:text-base text-[#00e9fa] font-mono uppercase tracking-[0.3em]">Sistematizando la intuición creativa</p>
          </div>
        </div>
      </section>

      {/* Identidad Empresarial Detallada */}
      <section id="identidad" className="py-40 px-8 max-w-7xl mx-auto">
        <XlSectionHeader title="Identidad Empresarial" subtitle="Compliance & Datos" />
        <div className="grid lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 grid md:grid-cols-2 gap-8">
            <XlCard title="Misión" icon={Target} variant="primary" to="/mision">
              Potenciar la ingeniería creativa con toolkits modulares que diagnostican, optimizan y automatizan tareas técnicas, permitiendo a los creadores enfocarse en la esencia de su obra.
            </XlCard>
            <XlCard title="Visión" icon={Rocket} to="/vision">
              Ser referentes en Latinoamérica en el diseño inteligente de soluciones técnicas y creativas, construyendo puentes entre la tecnología autónoma y el impacto cultural.
            </XlCard>
          </div>
          <div className="bg-white/5 border border-white/10 p-10 flex flex-col justify-between">
            <div className="space-y-6">
              <h4 className="font-mono text-[#00e9fa] text-xs uppercase tracking-widest">// Información Legal</h4>
              <div className="space-y-4">
                <div className="flex justify-between border-b border-white/5 pb-2">
                  <span className="text-gray-500 text-[10px] uppercase font-mono tracking-widest">Ubicación</span>
                  <span className="text-xs">Nocaima, Cundinamarca</span>
                </div>
                <div className="flex justify-between border-b border-white/5 pb-2">
                  <span className="text-gray-500 text-[10px] uppercase font-mono tracking-widest">CIIU Sugerido</span>
                  <span className="text-xs">6201, 7410, 7110</span>
                </div>
                <div className="flex justify-between border-b border-white/5 pb-2">
                  <span className="text-gray-500 text-[10px] uppercase font-mono tracking-widest">Emprendimiento</span>
                  <span className="text-xs">Digital / Social</span>
                </div>
              </div>
            </div>
            <div className="mt-8 pt-6 border-t border-[#00e9fa]/20">
              <p className="text-[10px] font-mono text-gray-400 uppercase leading-relaxed">
                NIT: [En Trámite] <br />
                Empresa de turismo incluyente y tecnología.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Xlerion Toolkit: Producto Principal */}
      <section id="toolkit" className="py-40 bg-[#050505] border-y border-white/5">
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title="Xlerion Toolkit" subtitle="Producto Principal" />
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <XlCard title="Validación" icon={CheckCircle2} to="/toolkit/validacion">Validación automática de activos multimedia y códigos de error en tiempo real.</XlCard>
            <XlCard title="Logging" icon={Terminal} to="/toolkit/logging">Sistema de logs dinámicos en formato JSON para trazabilidad total del flujo.</XlCard>
            <XlCard title="Diagnóstico" icon={Activity} to="/toolkit/diagnostico">Protocolos de diagnóstico previo para evitar fallos críticos en producción.</XlCard>
            <XlCard title="Performance" icon={Zap} to="/toolkit/performance">Comparadores de rendimiento optimizados para flujos de trabajo Windows.</XlCard>
          </div>
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="space-y-6">
              <h4 className="text-2xl font-bold italic uppercase font-mono tracking-tighter">Especificaciones Técnicas</h4>
              <p className="text-gray-400 text-sm leading-relaxed">
                Diseñado como un entorno modular para Windows, el toolkit ofrece una interfaz jerárquica con iconos personalizados.
                Es adaptativo, documenta dinámicamente cada interacción y se integra en el sistema sin comprometer la estabilidad.
              </p>
              <ul className="space-y-3 font-mono text-xs text-[#00e9fa] uppercase tracking-widest">
                <li>+ Preservación Documental Automática</li>
                <li>+ Interfaz Jerárquica Adaptativa</li>
                <li>+ Integración Modular Sin Fricción</li>
              </ul>
            </div>
            <div className="bg-[#00e9fa]/5 p-8 border-l-4 border-[#00e9fa] font-mono text-[11px] leading-relaxed text-gray-500">
              <span className="text-white">root@xlerion:~$</span> toolkit --status<br />
              <span className="text-green-500">System: Operational</span><br />
              <span className="text-[#00e9fa]">Modules: 4 Active</span><br />
              <span className="text-gray-600">Trace: Documenting dynamics...</span>
            </div>
          </div>
        </div>
      </section>

      {/* Servicios y Aplicaciones */}
      <section id="servicios" className="py-40 px-8 max-w-7xl mx-auto">
        <XlSectionHeader title="Servicios & Aplicaciones" subtitle="Líneas de Negocio" />
        <div className="space-y-8 max-w-5xl text-sm text-gray-300 leading-relaxed">
          <p>
            Xlerion ofrece una gama completa de servicios técnicos y creativos diseñados para industrias exigentes, combinando innovación modular con un enfoque en la autonomía y la eficiencia.
          </p>
          <p className="font-mono text-xs uppercase tracking-[0.3em] text-[#00e9fa]">// Servicios detallados</p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
          <XlCard title="Toolkits modulares" icon={Code} to="/servicios/toolkits">
            Desarrollo de toolkits con interfaces jerárquicas y adaptativas que facilitan interacción intuitiva, gestión de funciones complejas y escalabilidad.
          </XlCard>
          <XlCard title="Diagnóstico, logging y performance" icon={Activity} to="/servicios/diag-log-perf">
            Monitoreo en tiempo real, detección temprana de fallos y optimización continua con trazas y métricas para mantenimiento predictivo.
          </XlCard>
          <XlCard title="Branding técnico-creativo" icon={Target} to="/servicios/branding">
            Identidades que fusionan lógica simbólica y funcional, fortaleciendo coherencia, impacto cultural y estrategias visuales del producto.
          </XlCard>
          <XlCard title="Documentación estructurada" icon={BookOpen} to="/servicios/documentacion">
            Guías, diagramas y manuales que aseguran continuidad operativa, transferencia de conocimiento y autosuficiencia de los equipos.
          </XlCard>
          <XlCard title="Integración con motores 3D" icon={Cpu} to="/servicios/integracion-3d">
            Adaptación y optimización para Unreal Engine, Unity y 3DS Max, garantizando interoperabilidad y despliegues fluidos entre plataformas.
          </XlCard>
          <XlCard title="Consultoría en modularidad" icon={Briefcase} to="/servicios/consultoria-modular">
            Asesoría para adoptar metodologías modulares que mejoran escalabilidad, mantenimiento y eficiencia en proyectos técnicos y creativos.
          </XlCard>
          <XlCard title="Capacitación y talleres" icon={Users} to="/servicios/capacitacion">
            Formación especializada en uso de herramientas, filosofía modular y mejores prácticas de documentación y diagnóstico técnico.
          </XlCard>
          <XlCard title="Soporte y actualización" icon={Shield} to="/servicios/soporte-actualizacion">
            Implementación, mantenimiento y actualización continua de soluciones, alineadas a necesidades cambiantes del mercado y la tecnología.
          </XlCard>
          <XlCard title="Soluciones a medida" icon={Wrench} to="/servicios/soluciones-medida">
            Desarrollo de sistemas personalizados que integran innovación tecnológica con enfoque modular y escalable para requerimientos específicos.
          </XlCard>
        </div>
      </section>

      {/* Estrategia, Cronograma y Riesgos */}
      <section id="estrategia" className="py-40 bg-[#080808]">
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title="Estrategia & Futuro" subtitle="Mercado y Proyección" />
          <div className="grid lg:grid-cols-2 gap-16 mb-20">
            <div className="space-y-8">
              <div>
                <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-widest mb-4 italic">// Segmento Objetivo</h4>
                <p className="text-gray-400 text-sm">Población con discapacidad, adultos mayores y empresas multimedia que requieren validación técnica de alto nivel.</p>
              </div>
              <div className="grid grid-cols-2 gap-6">
                <div className="p-6 border border-white/5 bg-white/5">
                  <h5 className="font-mono text-white text-xs mb-2 uppercase italic">Cronograma</h5>
                  <p className="text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed font-mono">Arranque: Nov-Dic 2024.<br />Periodo improductivo: 3 meses para adecuaciones.</p>
                </div>
                <div className="p-6 border border-white/5 bg-white/5">
                  <h5 className="font-mono text-white text-xs mb-2 uppercase italic">Metas 2025</h5>
                  <p className="text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed font-mono">Generación de 5 empleos directos en el primer año fiscal.</p>
                </div>
              </div>
            </div>
            <div className="space-y-6">
              <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-widest mb-4 italic">// Gestión de Riesgos</h4>
              <div className="space-y-4 text-xs font-mono">
                <div className="flex gap-4 items-start p-4 border border-white/5 bg-white/2">
                  <AlertTriangle className="text-amber-500 shrink-0" size={18} />
                  <div>
                    <span className="text-white uppercase">Riesgo Técnico:</span>
                    <p className="text-gray-500 mt-1 uppercase tracking-tighter">Mitigación: Vigilancia tecnológica y consulta a expertos en accesibilidad.</p>
                  </div>
                </div>
                <div className="flex gap-4 items-start p-4 border border-white/5 bg-white/2">
                  <TrendingUp className="text-[#00e9fa] shrink-0" size={18} />
                  <div>
                    <span className="text-white uppercase">Riesgo Comercial:</span>
                    <p className="text-gray-500 mt-1 uppercase tracking-tighter">Mitigación: Campañas directas con operadores turísticos y marketing digital.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Blog / Bitácora */}
      <section id="blog" className="py-40 px-8 max-w-7xl mx-auto">
        <XlSectionHeader title="Blog / Bitácora" subtitle="Reflexiones y Avances" />
        <div className="space-y-12 text-gray-300">
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">El origen de Total Darkness</h4>
            <p className="text-sm leading-relaxed">Un recorrido profundo por la génesis de esta obra literaria y su evolución hacia un pelijuego interactivo que combina narrativa inmersiva y filosofía. Este artículo detalla el proceso creativo, los desafíos técnicos y las decisiones narrativas que dieron forma a Total Darkness.</p>
          </div>
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">Filosofía modular aplicada en videojuegos</h4>
            <p className="text-sm leading-relaxed">Exploramos cómo la modularidad impulsa la innovación técnica y creativa en el desarrollo de videojuegos, facilitando escalabilidad y adaptabilidad. Incluye ejemplos prácticos y beneficios para equipos de desarrollo.</p>
          </div>
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">Documentar para empoderar</h4>
            <p className="text-sm leading-relaxed">La documentación rigurosa como herramienta para transferir conocimiento, fomentar la autosuficiencia y fortalecer comunidades técnicas. Metodologías, casos de éxito e impacto cultural.</p>
          </div>
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">Participación en Colombia 4.0</h4>
            <p className="text-sm leading-relaxed">Relato de la experiencia y aprendizajes durante la participación en este evento clave para la innovación tecnológica y cultural en Colombia.</p>
          </div>
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">Diagnóstico técnico como herramienta cultural</h4>
            <p className="text-sm leading-relaxed">Cómo el diagnóstico técnico se convierte en un medio para entender, preservar y potenciar el patrimonio cultural mediante soluciones tecnológicas.</p>
          </div>
        </div>
      </section>

      {/* Soluciones */}
      <section id="soluciones" className="py-40 bg-[#050505] border-y border-white/5">
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title="Soluciones" subtitle="Herramientas Técnicas" />
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <XlCard title="Toolkits modulares" icon={Code} to="/servicios/toolkits">Interfaces jerárquicas y adaptativas para gestionar funciones complejas y escalar sin fricción.</XlCard>
            <XlCard title="Diagnóstico, logging y performance" icon={Activity} to="/servicios/diag-log-perf">Monitoreo en tiempo real, trazas estructuradas y optimización continua para entornos exigentes.</XlCard>
            <XlCard title="Branding técnico-creativo" icon={Target} to="/servicios/branding">Identidades con lógica simbólica y funcional que refuerzan coherencia e impacto cultural.</XlCard>
            <XlCard title="Documentación estructurada" icon={BookOpen} to="/servicios/documentacion">Guías, diagramas y manuales que aseguran continuidad operativa y transferencia de conocimiento.</XlCard>
            <XlCard title="Integración con motores 3D" icon={Cpu} to="/servicios/integracion-3d">Adaptación y optimización para Unreal Engine, Unity y 3DS Max con despliegues fluidos y multiplataforma.</XlCard>
          </div>
        </div>
      </section>

      {/* Proyectos */}
      <section id="proyectos" className="py-40 px-8 max-w-7xl mx-auto">
        <XlSectionHeader title="Proyectos" subtitle="Casos Destacados" />
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <XlCard title="Total Darkness – Pelijuego" icon={Cpu} to="/proyectos/total-darkness">Experiencia narrativa inmersiva con decisiones ramificadas, entornos 3D y cinemáticas filosóficas.</XlCard>
          <XlCard title="Xlerion Toolkit" icon={Terminal} to="/proyectos/toolkit">Conjunto modular para diagnóstico, logging y rendimiento en entornos técnicos complejos.</XlCard>
          <XlCard title="Tránsito y Movilidad Inteligente" icon={Activity} to="/proyectos/transito-movilidad">Sistema de semáforos con IA que optimiza el flujo vehicular urbano mediante conteo dinámico.</XlCard>
          <XlCard title="Tecnologías al Alcance del Bienestar" icon={Heart} to="/proyectos/tecnologias-comunidad">Soluciones tecnológicas accesibles que mejoran la calidad de vida y el bienestar social.</XlCard>
        </div>
      </section>

      {/* Documentación */}
      <section id="documentacion" className="py-40 bg-[#080808]">
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title="Documentación" subtitle="Legado Replicable" />
          <div className="grid lg:grid-cols-3 gap-8">
            <XlCard title="Manuales por módulo" icon={Database} to="/documentacion/manuales">Explican funcionamiento, configuración y mantenimiento con ejemplos y recomendaciones.</XlCard>
            <XlCard title="Diagramas de flujo" icon={TrendingUp} to="/documentacion/diagramas-flujos">Representan arquitectura y flujo de datos; se actualizan con nuevas funcionalidades.</XlCard>
            <XlCard title="Guías de instalación" icon={Shield} to="/documentacion/guias-instalacion">Instrucciones paso a paso para diferentes entornos y plataformas con soluciones comunes.</XlCard>
          </div>
          <div className="mt-12 text-sm text-gray-300 leading-relaxed">
            Más que soporte técnico, la documentación es una herramienta estratégica para empoderar comunidades y fomentar autonomía.
          </div>
        </div>
      </section>

      {/* Fundador */}
      <section id="fundador" className="py-40 px-8 max-w-7xl mx-auto">
        <XlSectionHeader title="Sobre el Fundador" subtitle="Perfil" />
        <div className="grid lg:grid-cols-2 gap-12 items-start">
          <div className="space-y-6 text-sm text-gray-300 leading-relaxed">
            <p>Miguel Eduardo Rodríguez Martínez (Mike), autodidacta con enfoque neurodivergente, integra arte digital, modelado 3D, scripting y defensa legal para crear soluciones con impacto cultural y territorial.</p>
            <p>Autor de <span className="text-white">Total Darkness</span>, obra literaria que refleja su compromiso con la narrativa inmersiva y la exploración filosófica.</p>
          </div>
          <div className="p-8 border border-white/10 bg-white/5">
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">Principios</h4>
            <ul className="space-y-2 text-[12px] text-gray-400 font-mono uppercase tracking-widest">
              <li>Autosuficiencia creativa</li>
              <li>Documentación rigurosa</li>
              <li>Modularidad aplicada</li>
              <li>Impacto cultural</li>
            </ul>
          </div>
        </div>
      </section>

      {/* Filosofía */}
      <section id="filosofia" className="py-40 px-8 max-w-7xl mx-auto">
        <XlSectionHeader title="Filosofía" subtitle="Principios y Valores" />
        <div className="grid lg:grid-cols-2 gap-12">
          <div className="space-y-6 text-sm text-gray-300 leading-relaxed">
            <p>Innovación técnica y creativa guiada por autonomía, colaboración e impacto cultural sostenible. Las soluciones modulares empoderan comunidades y fomentan aprendizaje continuo.</p>
            <div>
              <h5 className="font-mono text-white uppercase tracking-widest text-xs mb-2">Misión</h5>
              <p>Impulsar el desarrollo técnico contemporáneo mediante soluciones modulares que anticipan fallos y optimizan flujos de trabajo.</p>
            </div>
            <div>
              <h5 className="font-mono text-white uppercase tracking-widest text-xs mb-2">Visión</h5>
              <p>Ser referente latinoamericano en toolkits inteligentes que integren técnica, creatividad, documentación y escalabilidad.</p>
            </div>
          </div>
          <div className="p-8 border border-white/10 bg-white/5">
            <h5 className="font-mono text-white uppercase tracking-widest text-xs mb-4">Valores</h5>
            <ul className="space-y-2 text-[12px] text-gray-400 font-mono uppercase tracking-widest">
              <li>Empatía técnica</li>
              <li>Autosuficiencia creativa</li>
              <li>Documentación como legado</li>
              <li>Modularidad estructural</li>
              <li>Impacto cultural territorial</li>
            </ul>
          </div>
        </div>
      </section>

      {/* Legal y Privacidad */}
      <section id="legal" className="py-40 bg-[#080808]">
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title="Legal y Privacidad" subtitle="Políticas y Términos" />
          <div className="grid lg:grid-cols-3 gap-8">
            <XlCard title="Política de Privacidad" icon={Shield} to="/legal/privacidad">Protección de datos personales y uso conforme a normativas vigentes.</XlCard>
            <XlCard title="Términos de Uso" icon={Info} to="/legal/terminos">Condiciones de uso del sitio y toolkits, responsabilidades y limitaciones.</XlCard>
            <XlCard title="Licencias" icon={Database} to="/legal/licencias">Licencias de software y contenido, respeto por propiedad intelectual.</XlCard>
          </div>
          <div className="mt-12 text-[11px] font-mono text-gray-500 uppercase tracking-widest">
            Declaración de derechos del consumidor: transparencia, calidad y mecanismos claros de reclamación.
          </div>
        </div>
      </section>

      {/* Contacto & Resumen Ejecutivo */}
      <section id="contacto" className="py-40 px-8 max-w-7xl mx-auto border-t border-white/10">
        <XlSectionHeader title="Contacto" subtitle="Colabora, invierte o conoce más" />
        <div className="grid lg:grid-cols-2 gap-20">
          <ContactForm />
          <div className="space-y-8">
            <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5">
              <h4 className="text-[#00e9fa] font-mono text-xs mb-6 uppercase tracking-widest italic">// Canales directos</h4>
              <div className="space-y-5 text-sm font-sans">
                <a href="https://wa.me/573208605600" target="_blank" rel="noopener noreferrer" className="flex items-center justify-between px-4 py-3 border border-[#00e9fa]/40 bg-black/40 hover:border-[#00e9fa] transition-all">
                  <span className="flex items-center gap-3 text-white"><MessageCircle size={18} className="text-[#00e9fa]" /> WhatsApp</span>
                  <span className="font-mono text-xs text-gray-400">+57 3208605600</span>
                </a>
                <div className="grid sm:grid-cols-2 gap-3 text-[12px] text-gray-300 font-mono uppercase tracking-widest">
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />contacto@xlerion.tech</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />contactus@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />totaldarkness@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />support@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />sales@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />admin@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />branding@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />toolkit@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />neuro@xlerion.com</span>
                  <span className="flex items-center gap-2"><AtSign size={14} className="text-[#00e9fa]" />mike@xlerion.com</span>
                </div>
              </div>
            </div>
            <div className="p-8 border border-white/10 bg-white/5">
              <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-widest mb-4">// Redes y perfiles</h4>
              <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 text-[12px] font-mono uppercase tracking-widest text-gray-400">
                <a href="https://www.linkedin.com/company/xlerion" className="hover:text-[#00e9fa] transition-colors">LinkedIn</a>
                <a href="https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9?redirect_reason#/overview" className="hover:text-[#00e9fa] transition-colors">Indiegogo</a>
                <a href="https://www.kickstarter.com/profile/xlerionstudios" className="hover:text-[#00e9fa] transition-colors">Kickstarter</a>
                <a href="https://www.patreon.com/xlerion" className="hover:text-[#00e9fa] transition-colors">Patreon</a>
                <a href="https://www.instagram.com/ultimatexlerion/" className="hover:text-[#00e9fa] transition-colors">Instagram</a>
                <a href="https://www.facebook.com/xlerionultimate" className="hover:text-[#00e9fa] transition-colors">Facebook</a>
                <a href="https://www.behance.net/xlerionultimate" className="hover:text-[#00e9fa] transition-colors">Behance</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <Footer navTo={navTo} />
    </div>
  );
}