import React, { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import * as THREE from 'three';
import {
  ChevronRight, Mail, Code, Zap, Activity, Cpu, Rocket,
  Target, Send, AtSign, MessageCircle,
  Briefcase, Clock, CheckCircle2, Users, MapPin,
  Heart, Shield, Database, AlertTriangle, Info, Terminal,
  TrendingUp, BookOpen, Wrench, Linkedin, Instagram
} from 'lucide-react';
import Footer from './components/Footer';
import ContactForm from './components/ContactForm';
import EvolutionaryScene from './components/EvolutionaryScene';
import { useLanguage } from './context/LanguageContext';

/**
 * XLERION CORPORATE WEB V3.1 - FIX & OPTIMIZATION
 * Arquitectura basada en EstiloWeb.md y ContenidoWeb.md
 */

// Componente de Escena 3D con animaciones mapeadas de ANIMACIONES_3D.md
const ThreeScene = ({ actionType }) => {
  const containerRef = useRef(null);
  const rendererRef = useRef(null);
  const modelGroupRef = useRef(null);
  const animationStateRef = useRef({ actionType: 'default' });
  const parallaxImageRef = useRef(null);
  const currentImageRef = useRef(null);
  const mouseRef = useRef({ x: 0, y: 0 });
  const [imageOpacity, setImageOpacity] = React.useState(1);

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

  const getRandomImage = () => PARALLAX_IMAGES[Math.floor(Math.random() * PARALLAX_IMAGES.length)];

  const changeImage = () => {
    setImageOpacity(0);
    setTimeout(() => {
      const newImage = getRandomImage();
      currentImageRef.current = newImage;
      if (parallaxImageRef.current) {
        parallaxImageRef.current.src = `/images/${newImage}`;
      }
      setImageOpacity(1);
    }, 300);
  };

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return undefined;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(35, window.innerWidth / window.innerHeight, 0.1, 100);
    camera.position.set(0, 0, 6);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    container.appendChild(renderer.domElement);
    rendererRef.current = renderer;

    const group = new THREE.Group();
    modelGroupRef.current = group;
    scene.add(group);

    const shellGeo = new THREE.IcosahedronGeometry(1.4, 0);
    const shellMat = new THREE.MeshStandardMaterial({
      color: 0x00e9fa,
      emissive: 0x00e9fa,
      emissiveIntensity: 0.35,
      roughness: 0.2,
      metalness: 0.6,
      wireframe: true
    });
    group.add(new THREE.Mesh(shellGeo, shellMat));

    const innerGeo = new THREE.SphereGeometry(0.8, 32, 32);
    const innerMat = new THREE.MeshStandardMaterial({
      color: 0xffffff,
      emissive: 0x00e9fa,
      emissiveIntensity: 0.25,
      roughness: 0.5,
      metalness: 0.2
    });
    group.add(new THREE.Mesh(innerGeo, innerMat));

    const orbitParticles = [];
    const addOrbit = (count, radius, size, speed, plane) => {
      for (let i = 0; i < count; i++) {
        const angle = (i / count) * Math.PI * 2;
        const particle = new THREE.Mesh(
          new THREE.SphereGeometry(size, 16, 16),
          new THREE.MeshStandardMaterial({ color: 0x00e9fa, emissive: 0x00e9fa, emissiveIntensity: 0.9 })
        );
        particle.userData = { angle, speed, radius, plane };
        if (plane === 'xy') {
          particle.position.set(Math.cos(angle) * radius, Math.sin(angle) * radius, 0);
        } else if (plane === 'yz') {
          particle.position.set(0, Math.cos(angle) * radius, Math.sin(angle) * radius);
        } else {
          particle.position.set(Math.cos(angle) * radius, 0, Math.sin(angle) * radius);
        }
        group.add(particle);
        orbitParticles.push(particle);
      }
    };

    addOrbit(8, 1.5, 0.08, 1.0, 'xy');
    addOrbit(6, 1.8, 0.07, 0.7, 'yz');
    addOrbit(10, 2.2, 0.06, 1.2, 'xz');

    const particles = [];
    const cubeGeom = new THREE.BoxGeometry(0.1, 0.1, 0.1);
    for (let i = 0; i < 12; i++) {
      const angle = (i / 12) * Math.PI * 2;
      const cube = new THREE.Mesh(
        cubeGeom,
        new THREE.MeshStandardMaterial({ color: 0x00e9fa, emissive: 0x00e9fa, emissiveIntensity: 0.8 })
      );
      cube.position.set(Math.cos(angle) * 2.5, Math.sin(angle) * 2.5, Math.cos(angle * 0.5) * 0.5);
      cube.userData = { angle, speed: 0.5 + Math.random() * 0.5 };
      group.add(cube);
      particles.push(cube);
    }

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

      if (modelGroupRef.current) {
        const rotSpeedX = 0.002;
        const rotSpeedY = 0.003;
        const rotSpeedZ = 0.001;

        switch (state.actionType) {
          case 'identidad':
            modelGroupRef.current.rotation.y += 0.01;
            modelGroupRef.current.rotation.x += rotSpeedX;
            modelGroupRef.current.position.y = Math.sin(time * 0.5) * 0.3;
            modelGroupRef.current.scale.set(
              1 + Math.sin(time * 2) * 0.05,
              1 + Math.sin(time * 2) * 0.05,
              1 + Math.sin(time * 2) * 0.05
            );
            break;
          case 'mision':
            modelGroupRef.current.rotation.y += 0.05;
            modelGroupRef.current.rotation.z += rotSpeedZ;
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
            modelGroupRef.current.scale.set(
              1 + Math.sin(time * 1.5) * 0.03,
              1 + Math.sin(time * 1.5) * 0.03,
              1 + Math.sin(time * 1.5) * 0.03
            );
        }

        orbitParticles.forEach((particle) => {
          const { plane, speed, radius, angle } = particle.userData;
          const newAngle = angle + time * speed * 0.3;

          if (plane === 'xy') {
            particle.position.x = Math.cos(newAngle) * radius;
            particle.position.y = Math.sin(newAngle) * radius;
          } else if (plane === 'yz') {
            particle.position.y = Math.cos(newAngle) * radius;
            particle.position.z = Math.sin(newAngle) * radius;
          } else {
            particle.position.x = Math.cos(newAngle) * radius;
            particle.position.z = Math.sin(newAngle) * radius;
          }

          particle.rotation.x += 0.02;
          particle.rotation.y += 0.03;
        });

        particles.forEach((particle, idx) => {
          const { speed, angle } = particle.userData;
          const newAngle = angle + time * speed * 0.5;
          particle.position.x = Math.cos(newAngle) * 2.5;
          particle.position.y = Math.sin(newAngle) * 2.5;
          particle.position.z = Math.cos(newAngle * 0.5) * 0.5;

          particle.rotation.x += 0.05;
          particle.rotation.y += 0.07;
          particle.scale.set(
            1 + Math.sin(time * 3 + idx) * 0.3,
            1 + Math.sin(time * 3 + idx) * 0.3,
            1 + Math.sin(time * 3 + idx) * 0.3
          );
        });
      }

      const lights = scene.children.filter((child) => child instanceof THREE.PointLight);
      lights.forEach((light, idx) => {
        light.position.x = Math.cos(time * 0.5 + idx) * 5;
        light.position.y = Math.sin(time * 0.3 + idx) * 5;
        light.position.z = Math.cos(time * 0.7 + idx) * 3;
      });

      const hue = (time * 0.1) % 1;
      const dynamicColor = new THREE.Color().setHSL(hue, 1, 0.5);
      group.children.forEach((child) => {
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
  }, []);

  useEffect(() => {
    animationStateRef.current.actionType = actionType;
  }, [actionType]);

  return (
    <div className="absolute inset-0 z-0">
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
      <div className="absolute inset-0 bg-gradient-to-b from-black/50 via-black/50 to-black/50 pointer-events-none backdrop-blur-sm" />
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
  const { t, lang, setLang } = useLanguage();

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
          <div className="flex items-center gap-3 cursor-pointer relative z-50" onClick={() => navTo('home', 'default')}>
            <img src="/LogoX.svg" alt="Xlerion" className="h-9 md:h-10 object-contain" />
          </div>

          <div className="hidden lg:flex items-center gap-10 text-[13px] font-work uppercase tracking-[0.2em] font-light">
            <div className="relative group">
              <button className="hover:text-[#00e9fa] transition-colors">{t('nav_company')}</button>
              <div className="absolute left-0 top-0 pt-12 hidden group-hover:block">
                <div className="bg-black/80 backdrop-blur-sm rounded-sm p-3">
                  <div className="flex flex-col gap-2 text-[13px] font-work uppercase tracking-[0.2em] font-light items-end">
                    <button onClick={() => navTo('identidad', 'identidad')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_identity')}</button>
                    <button onClick={() => navTo('blog', 'vision')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_blog')}</button>
                    <button onClick={() => navTo('filosofia', 'identidad')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_philosophy')}</button>
                    <button onClick={() => navTo('legal', 'mision')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_legal')}</button>
                  </div>
                </div>
              </div>
            </div>

            <button onClick={() => navTo('toolkit', 'toolkit')} className="hover:text-[#00e9fa] transition-colors">{t('nav_toolkit')}</button>
            <button onClick={() => navTo('servicios', 'servicios')} className="hover:text-[#00e9fa] transition-colors">{t('nav_services')}</button>
            <button onClick={() => navTo('soluciones', 'servicios')} className="hover:text-[#00e9fa] transition-colors">{t('nav_solutions')}</button>
            <button onClick={() => navTo('proyectos', 'vision')} className="hover:text-[#00e9fa] transition-colors">{t('nav_projects')}</button>
            <button onClick={() => navTo('documentacion', 'mision')} className="hover:text-[#00e9fa] transition-colors">{t('nav_docs')}</button>
            <button onClick={() => navTo('estrategia', 'vision')} className="hover:text-[#00e9fa] transition-colors">{t('nav_strategy')}</button>
            <button onClick={() => navTo('contacto', 'mision')} className="hover:text-[#00e9fa] transition-colors">{t('nav_contact')}</button>
            <div className="flex items-center gap-2 border-l border-white/10 pl-4">
              <button onClick={() => setLang('es')} className={`text-xs tracking-[0.25em] ${lang === 'es' ? 'text-[#00e9fa]' : 'text-gray-400'} hover:text-[#00e9fa]`}>
                {t('lang_es')}
              </button>
              <span className="text-gray-600">/</span>
              <button onClick={() => setLang('en')} className={`text-xs tracking-[0.25em] ${lang === 'en' ? 'text-[#00e9fa]' : 'text-gray-400'} hover:text-[#00e9fa]`}>
                {t('lang_en')}
              </button>
            </div>
          </div>

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
            <div className="border-b border-white/10 pb-6">
              <button
                onClick={() => setEmpresaOpen(!empresaOpen)}
                className="text-2xl font-work uppercase tracking-[0.2em] font-light text-[#00e9fa] mb-4 flex items-center justify-center gap-2 w-full"
              >
                {t('nav_company')}
                <span className={`transition-transform duration-300 ${empresaOpen ? 'rotate-180' : ''}`}>▼</span>
              </button>
              {empresaOpen && (
                <div className="flex flex-col gap-3 mt-4">
                  <button onClick={() => navTo('identidad', 'identidad')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_identity')}</button>
                  <button onClick={() => navTo('blog', 'vision')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_blog')}</button>
                  <button onClick={() => navTo('filosofia', 'identidad')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_philosophy')}</button>
                  <button onClick={() => navTo('legal', 'mision')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_legal')}</button>
                </div>
              )}
            </div>

            <button onClick={() => navTo('toolkit', 'toolkit')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_toolkit')}</button>
            <button onClick={() => navTo('servicios', 'servicios')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_services')}</button>
            <button onClick={() => navTo('soluciones', 'servicios')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_solutions')}</button>
            <button onClick={() => navTo('proyectos', 'vision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_projects')}</button>
            <button onClick={() => navTo('documentacion', 'mision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_docs')}</button>
            <button onClick={() => navTo('estrategia', 'vision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_strategy')}</button>
            <button onClick={() => navTo('contacto', 'mision')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors border-t border-white/10 pt-6">{t('nav_contact')}</button>
            <div className="flex items-center justify-center gap-4 pt-4 text-gray-400 text-xs tracking-[0.25em]">
              <button onClick={() => setLang('es')} className={`${lang === 'es' ? 'text-[#00e9fa]' : ''} hover:text-[#00e9fa]`}>{t('lang_es')}</button>
              <span className="text-gray-600">/</span>
              <button onClick={() => setLang('en')} className={`${lang === 'en' ? 'text-[#00e9fa]' : ''} hover:text-[#00e9fa]`}>{t('lang_en')}</button>
            </div>
          </div>
        </div>
      </div>

      {/* Hero: Xlerion - Soluciones Disruptivas */}
      <section id="home" className="relative h-screen flex items-center justify-center overflow-hidden">
        <EvolutionaryScene />
        <div className="relative z-10 text-center px-6 flex flex-col items-center justify-center h-full">
          <img src="/LogoX.svg" alt="Xlerion" className="mx-auto mb-6 h-32 md:h-40 lg:h-56 object-contain" style={{ filter: 'drop-shadow(0 0 30px rgba(0, 233, 250, 0.6))' }} />
          <p className="text-lg md:text-xl text-gray-400 mb-12 mt-2 max-w-2xl mx-auto font-light leading-relaxed">
            {t('hero_line1')} <br />
            <span className="text-white">{t('hero_line2')}</span>
          </p>
          <div className="flex flex-col sm:flex-row gap-6 justify-center pb-12">
            <button onClick={() => navTo('toolkit', 'toolkit')} className="xl-btn-primary">{t('hero_cta_toolkit')}</button>
            <button onClick={() => navTo('servicios', 'servicios')} className="px-10 py-3 border border-white/20 text-xs font-bold uppercase tracking-widest hover:bg-white/5 transition-all">{t('hero_cta_services')}</button>
          </div>
          <div className="mt-8 text-center">
            <p className="text-sm md:text-base text-[#00e9fa] font-mono uppercase tracking-[0.3em]">{t('hero_claim')}</p>
          </div>
        </div>
      </section >

      {/* Identidad Empresarial Detallada */}
      < section id="identidad" className="py-40 px-8 max-w-7xl mx-auto" >
        <XlSectionHeader title={t('identity_title')} subtitle={t('identity_subtitle')} />
        <div className="grid lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 grid md:grid-cols-2 gap-8">
            <XlCard title="Misión" icon={Target} variant="primary" to="/mision">
              {t('identity_mision')}
            </XlCard>
            <XlCard title="Visión" icon={Rocket} to="/vision">
              {t('identity_vision')}
            </XlCard>
          </div>
          <div className="bg-white/5 border border-white/10 p-10 flex flex-col justify-between">
            <div className="space-y-6">
              <h4 className="font-mono text-[#00e9fa] text-xs uppercase tracking-widest">// {t('identity_legal_info')}</h4>
              <div className="space-y-4">
                <div className="flex justify-between border-b border-white/5 pb-2">
                  <span className="text-gray-500 text-[10px] uppercase font-mono tracking-widest">{t('identity_location')}</span>
                  <span className="text-xs">Nocaima, Cundinamarca</span>
                </div>
                <div className="flex justify-between border-b border-white/5 pb-2">
                  <span className="text-gray-500 text-[10px] uppercase font-mono tracking-widest">{t('identity_ciiu')}</span>
                  <span className="text-xs">6201, 7410, 7110</span>
                </div>
                <div className="flex justify-between border-b border-white/5 pb-2">
                  <span className="text-gray-500 text-[10px] uppercase font-mono tracking-widest">{t('identity_entrepreneurship')}</span>
                  <span className="text-xs">Digital / Social</span>
                </div>
              </div>
            </div>
            <div className="mt-8 pt-6 border-t border-[#00e9fa]/20">
              <p className="text-[10px] font-mono text-gray-400 uppercase leading-relaxed">
                {t('identity_nit')} <br />
                {t('identity_company_desc')}
              </p>
            </div>
          </div>
        </div>
      </section >

      {/* Xlerion Toolkit: Producto Principal */}
      < section id="toolkit" className="py-40 bg-[#050505] border-y border-white/5" >
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title={t('toolkit_title')} subtitle={t('toolkit_subtitle')} />
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <XlCard title={t('toolkit_validation_title')} icon={CheckCircle2} to="/toolkit/validacion">{t('toolkit_validation_desc')}</XlCard>
            <XlCard title={t('toolkit_logging_title')} icon={Terminal} to="/toolkit/logging">{t('toolkit_logging_desc')}</XlCard>
            <XlCard title={t('toolkit_diagnostico_title')} icon={Activity} to="/toolkit/diagnostico">{t('toolkit_diagnostico_desc')}</XlCard>
            <XlCard title={t('toolkit_performance_title')} icon={Zap} to="/toolkit/performance">{t('toolkit_performance_desc')}</XlCard>
          </div>
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="space-y-6">
              <h4 className="text-2xl font-bold italic uppercase font-mono tracking-tighter">{t('toolkit_specs_title')}</h4>
              <p className="text-gray-400 text-sm leading-relaxed">
                {t('toolkit_specs_desc')}
              </p>
              <ul className="space-y-3 font-mono text-xs text-[#00e9fa] uppercase tracking-widest">
                <li>{t('toolkit_spec_1')}</li>
                <li>{t('toolkit_spec_2')}</li>
                <li>{t('toolkit_spec_3')}</li>
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
      </section >

      {/* Servicios y Aplicaciones */}
      < section id="servicios" className="py-40 px-8 max-w-7xl mx-auto" >
        <XlSectionHeader title={t('services_title')} subtitle={t('services_subtitle')} />
        <div className="space-y-8 max-w-5xl text-sm text-gray-300 leading-relaxed">
          <p>
            {t('services_intro')}
          </p>
          <p className="font-mono text-xs uppercase tracking-[0.3em] text-[#00e9fa]">{t('services_detailed')}</p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
          <XlCard title={t('services_modular_title')} icon={Code} to="/servicios/toolkits">
            {t('services_modular_desc')}
          </XlCard>
          <XlCard title={t('services_diag_title')} icon={Activity} to="/servicios/diag-log-perf">
            {t('services_diag_desc')}
          </XlCard>
          <XlCard title={t('services_brand_title')} icon={Target} to="/servicios/branding">
            {t('services_brand_desc')}
          </XlCard>
          <XlCard title={t('services_docs_title')} icon={BookOpen} to="/servicios/documentacion">
            {t('services_docs_desc')}
          </XlCard>
          <XlCard title={t('services_3d_title')} icon={Cpu} to="/servicios/integracion-3d">
            {t('services_3d_desc')}
          </XlCard>
          <XlCard title={t('services_consul_title')} icon={Briefcase} to="/servicios/consultoria-modular">
            {t('services_consul_desc')}
          </XlCard>
          <XlCard title={t('services_training_title')} icon={Users} to="/servicios/capacitacion">
            {t('services_training_desc')}
          </XlCard>
          <XlCard title={t('services_support_title')} icon={Shield} to="/servicios/soporte-actualizacion">
            {t('services_support_desc')}
          </XlCard>
          <XlCard title={t('services_custom_title')} icon={Wrench} to="/servicios/soluciones-medida">
            {t('services_custom_desc')}
          </XlCard>
        </div>
      </section >

      {/* Estrategia, Cronograma y Riesgos */}
      < section id="estrategia" className="py-40 bg-[#080808]" >
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title={t('strategy_title')} subtitle={t('strategy_subtitle')} />
          <div className="grid lg:grid-cols-2 gap-16 mb-20">
            <div className="space-y-8">
              <div>
                <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-widest mb-4 italic">{t('strategy_target')}</h4>
                <p className="text-gray-400 text-sm">{t('strategy_target_desc')}</p>
              </div>
              <div className="grid grid-cols-2 gap-6">
                <div className="p-6 border border-white/5 bg-white/5">
                  <h5 className="font-mono text-white text-xs mb-2 uppercase italic">{t('strategy_timeline')}</h5>
                  <p className="text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed font-mono">{t('strategy_timeline_desc')}</p>
                </div>
                <div className="p-6 border border-white/5 bg-white/5">
                  <h5 className="font-mono text-white text-xs mb-2 uppercase italic">{t('strategy_goals')}</h5>
                  <p className="text-[10px] text-gray-500 uppercase tracking-widest leading-relaxed font-mono">{t('strategy_goals_desc')}</p>
                </div>
              </div>
            </div>
            <div className="space-y-6">
              <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-widest mb-4 italic">{t('strategy_risks')}</h4>
              <div className="space-y-4 text-xs font-mono">
                <div className="flex gap-4 items-start p-4 border border-white/5 bg-white/2">
                  <AlertTriangle className="text-amber-500 shrink-0" size={18} />
                  <div>
                    <span className="text-white uppercase">{t('risk_technical')}</span>
                    <p className="text-gray-500 mt-1 uppercase tracking-tighter">{t('risk_technical_desc')}</p>
                  </div>
                </div>
                <div className="flex gap-4 items-start p-4 border border-white/5 bg-white/2">
                  <TrendingUp className="text-[#00e9fa] shrink-0" size={18} />
                  <div>
                    <span className="text-white uppercase">{t('risk_commercial')}</span>
                    <p className="text-gray-500 mt-1 uppercase tracking-tighter">{t('risk_commercial_desc')}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section >

      {/* Blog / Bitácora */}
      < section id="blog" className="py-40 px-8 max-w-7xl mx-auto" >
        <XlSectionHeader title={t('blog_title')} subtitle={t('blog_subtitle')} />
        <div className="space-y-12 text-gray-300">
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">{t('blog_origin_title')}</h4>
            <p className="text-sm leading-relaxed">{t('blog_origin_desc')}</p>
          </div>
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">{t('blog_modular_title')}</h4>
            <p className="text-sm leading-relaxed">{t('blog_modular_desc')}</p>
          </div>
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">{t('blog_documentation_title')}</h4>
            <p className="text-sm leading-relaxed">{t('blog_documentation_desc')}</p>
          </div>
          <div>
            <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">{t('blog_diagnostic_title')}</h4>
            <p className="text-sm leading-relaxed">{t('blog_diagnostic_desc')}</p>
          </div>
        </div>
      </section >

      {/* Soluciones */}
      < section id="soluciones" className="py-40 bg-[#050505] border-y border-white/5" >
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title={t('solutions_title')} subtitle={t('solutions_subtitle')} />
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <XlCard title={t('solutions_modular_title')} icon={Code} to="/servicios/toolkits">{t('solutions_modular_desc')}</XlCard>
            <XlCard title={t('solutions_diag_title')} icon={Activity} to="/servicios/diag-log-perf">{t('solutions_diag_desc')}</XlCard>
            <XlCard title={t('solutions_brand_title')} icon={Target} to="/servicios/branding">{t('solutions_brand_desc')}</XlCard>
            <XlCard title={t('solutions_docs_title')} icon={BookOpen} to="/servicios/documentacion">{t('solutions_docs_desc')}</XlCard>
            <XlCard title={t('solutions_3d_title')} icon={Cpu} to="/servicios/integracion-3d">{t('solutions_3d_desc')}</XlCard>
          </div>
        </div>
      </section >

      {/* Proyectos */}
      < section id="proyectos" className="py-40 px-8 max-w-7xl mx-auto" >
        <XlSectionHeader title={t('projects_title')} subtitle={t('projects_subtitle')} />
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <XlCard title={t('projects_total_darkness')} icon={Cpu} to="/proyectos/total-darkness">{t('projects_total_darkness_desc')}</XlCard>
          <XlCard title={t('projects_toolkit')} icon={Terminal} to="/proyectos/toolkit">{t('projects_toolkit_desc')}</XlCard>
          <XlCard title={t('projects_transit')} icon={Activity} to="/proyectos/transito-movilidad">{t('projects_transit_desc')}</XlCard>
          <XlCard title={t('projects_technology')} icon={Heart} to="/proyectos/tecnologias-comunidad">{t('projects_technology_desc')}</XlCard>
        </div>
      </section >

      {/* Documentación */}
      < section id="documentacion" className="py-40 bg-[#080808]" >
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title={t('docs_title')} subtitle={t('docs_subtitle')} />
          <div className="grid lg:grid-cols-3 gap-8">
            <XlCard title={t('docs_manuals')} icon={Database} to="/documentacion/manuales">{t('docs_manuals_desc')}</XlCard>
            <XlCard title={t('docs_diagrams')} icon={TrendingUp} to="/documentacion/diagramas-flujos">{t('docs_diagrams_desc')}</XlCard>
            <XlCard title={t('docs_installation')} icon={Shield} to="/documentacion/guias-instalacion">{t('docs_installation_desc')}</XlCard>
          </div>
          <div className="mt-12 text-sm text-gray-300 leading-relaxed">
            {t('docs_intro')}
          </div>
        </div>
      </section >

      {/* Fundador */}
      < section id="fundador" className="py-40 px-8 max-w-7xl mx-auto" >
        <XlSectionHeader title={t('founder_title')} subtitle={t('founder_subtitle')} />
        <div className="grid lg:grid-cols-2 gap-12 items-start">
          <div className="space-y-6 text-sm text-gray-300 leading-relaxed">
            <p>{t('founder_intro')}</p>
            <p>{t('founder_author')} <span className="text-white">Total Darkness</span>, {t('founder_work_desc')}</p>
            <Link
              to="/fundador"
              className="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-[#00e9fa]/10 border border-[#00e9fa] text-[#00e9fa] hover:bg-[#00e9fa]/20 transition-all duration-300 font-mono text-xs uppercase tracking-wider"
            >
              {t('founder_learn_more')} <ChevronRight size={16} />
            </Link>
          </div>
          <div className="space-y-8">
            <div className="p-8 border border-white/10 bg-white/5">
              <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-3">{t('founder_values')}</h4>
              <ul className="space-y-2 text-[12px] text-gray-400 font-mono uppercase tracking-widest">
                <li>{t('founder_value_1')}</li>
                <li>{t('founder_value_2')}</li>
                <li>{t('founder_value_3')}</li>
                <li>{t('founder_value_4')}</li>
              </ul>
            </div>
            <div className="p-8 border border-white/10 bg-white/5">
              <h4 className="font-mono text-white uppercase tracking-widest text-sm mb-4">CONNECT</h4>
              <div className="space-y-3">
                <a
                  href="https://linkedin.com/in/mikerodriguez84"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center gap-3 text-gray-400 hover:text-[#00e9fa] transition-colors text-xs font-mono"
                >
                  <Linkedin size={16} />
                  <span>LINKEDIN</span>
                </a>
                <a
                  href="https://artstation.com/xlerion"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center gap-3 text-gray-400 hover:text-[#00e9fa] transition-colors text-xs font-mono"
                >
                  <Briefcase size={16} />
                  <span>ARTSTATION</span>
                </a>
                <a
                  href="https://instagram.com/mike_hellawaits"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center gap-3 text-gray-400 hover:text-[#00e9fa] transition-colors text-xs font-mono"
                >
                  <Instagram size={16} />
                  <span>INSTAGRAM</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section >

      {/* Filosofía */}
      < section id="filosofia" className="py-40 px-8 max-w-7xl mx-auto" >
        <XlSectionHeader title={t('philosophy_title')} subtitle={t('philosophy_subtitle')} />
        <div className="grid lg:grid-cols-2 gap-12">
          <div className="space-y-6 text-sm text-gray-300 leading-relaxed">
            <p>{t('philosophy_intro')}</p>
            <div>
              <h5 className="font-mono text-white uppercase tracking-widest text-xs mb-2">{t('philosophy_mission')}</h5>
              <p>{t('philosophy_mission_desc')}</p>
            </div>
            <div>
              <h5 className="font-mono text-white uppercase tracking-widest text-xs mb-2">{t('philosophy_vision')}</h5>
              <p>{t('philosophy_vision_desc')}</p>
            </div>
          </div>
          <div className="p-8 border border-white/10 bg-white/5">
            <h5 className="font-mono text-white uppercase tracking-widest text-xs mb-4">{t('philosophy_values')}</h5>
            <ul className="space-y-2 text-[12px] text-gray-400 font-mono uppercase tracking-widest">
              <li>{t('philosophy_value_1')}</li>
              <li>{t('philosophy_value_2')}</li>
              <li>{t('philosophy_value_3')}</li>
              <li>Modularidad estructural</li>
              <li>Impacto cultural territorial</li>
            </ul>
          </div>
        </div>
      </section >

      {/* Legal y Privacidad */}
      < section id="legal" className="py-40 bg-[#080808]" >
        <div className="max-w-7xl mx-auto px-8">
          <XlSectionHeader title={t('legal_title')} subtitle={t('legal_subtitle')} />
          <div className="grid lg:grid-cols-3 gap-8">
            <XlCard title={t('legal_privacy_title')} icon={Shield} to="/legal/privacidad">{t('legal_privacy_desc')}</XlCard>
            <XlCard title={t('legal_terms_title')} icon={Info} to="/legal/terminos">{t('legal_terms_desc')}</XlCard>
            <XlCard title={t('legal_licenses_title')} icon={Database} to="/legal/licencias">{t('legal_licenses_desc')}</XlCard>
          </div>
          <div className="mt-12 text-[11px] font-mono text-gray-500 uppercase tracking-widest">
            {t('legal_consumer_rights')}
          </div>
        </div>
      </section >

      {/* Contacto & Resumen Ejecutivo */}
      < section id="contacto" className="py-40 px-8 max-w-7xl mx-auto border-t border-white/10" >
        <XlSectionHeader title={t('contact_title')} subtitle={t('contact_subtitle')} />
        <div className="grid lg:grid-cols-2 gap-20">
          <ContactForm />
          <div className="space-y-8">
            <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5">
              <h4 className="text-[#00e9fa] font-mono text-xs mb-6 uppercase tracking-widest italic">// {t('contact_channels_title')}</h4>
              <div className="space-y-5 text-sm font-sans">
                <a href="https://wa.me/573208605600" target="_blank" rel="noopener noreferrer" className="flex items-center justify-between px-4 py-3 border border-[#00e9fa]/40 bg-black/40 hover:border-[#00e9fa] transition-all">
                  <span className="flex items-center gap-3 text-white"><MessageCircle size={18} className="text-[#00e9fa]" /> {t('contact_whatsapp')}</span>
                  <span className="font-mono text-xs text-gray-400">+57 3208605600</span>
                </a>
                <div className="grid sm:grid-cols-2 gap-3 text-[12px] text-gray-300 font-mono uppercase tracking-widest">
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
              <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-widest mb-4">// {t('contact_social_title')}</h4>
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
      </section >

      <Footer navTo={navTo} />
    </div >
  );
}