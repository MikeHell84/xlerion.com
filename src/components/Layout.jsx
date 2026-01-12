import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import Footer from './Footer';
import { useLanguage } from '../context/LanguageContext';

export default function Layout({ children }) {
    const [scrolled, setScrolled] = useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [empresaOpen, setEmpresaOpen] = useState(false);
    const navigate = useNavigate();
    const { lang, setLang, t } = useLanguage();

    // Scroll al inicio cuando se monta el componente (al cambiar de página)
    useEffect(() => {
        window.scrollTo(0, 0);
    }, []);

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

    const navTo = (id) => {
        setMobileMenuOpen(false); // Cerrar menú móvil al navegar
        if (window.location.pathname !== '/') {
            navigate('/');
            setTimeout(() => {
                const el = document.getElementById(id);
                if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: 'smooth' });
            }, 100);
        } else {
            const el = document.getElementById(id);
            if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: 'smooth' });
        }
    };

    return (
        <div className="min-h-screen bg-black text-white flex flex-col">
            {/* Navegación Técnica */}
            <nav className={`fixed top-0 w-full z-50 transition-all duration-500 ${scrolled ? 'bg-black/80 backdrop-blur-sm py-1' : 'bg-black/50 backdrop-blur-sm py-2'} border-b border-[#00e9fa]/30`} style={{ boxShadow: '0 1px 10px rgba(0, 233, 250, 0.3)' }}>
                <div className="w-full px-4 md:px-8 flex items-center justify-between">
                    {/* Logo */}
                    <Link to="/" className="flex items-center gap-3 cursor-pointer relative z-50">
                        <img src="/LogoX.svg" alt="Xlerion" className="h-9 md:h-10 object-contain" />
                    </Link>

                    {/* Menú Desktop */}
                    <div className="hidden lg:flex items-center gap-10 text-[13px] font-work uppercase tracking-[0.2em] font-light">
                        {/* Empresa dropdown */}
                        <div className="relative group">
                            <button className="hover:text-[#00e9fa] transition-colors">{t('nav_company')}</button>
                            <div className="absolute left-0 top-0 pt-12 hidden group-hover:block">
                                <div className="bg-black/80 backdrop-blur-sm rounded-sm p-3">
                                    <div className="flex flex-col gap-2 text-[13px] font-work uppercase tracking-[0.2em] font-light items-end">
                                        <button onClick={() => navTo('identidad')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_identity')}</button>
                                        <button onClick={() => navTo('blog')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_blog')}</button>
                                        <button onClick={() => navTo('filosofia')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_philosophy')}</button>
                                        <button onClick={() => navTo('legal')} className="hover:text-[#00e9fa] transition-colors text-right">{t('nav_legal')}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Rest of main nav */}
                        <button onClick={() => navTo('toolkit')} className="hover:text-[#00e9fa] transition-colors">{t('nav_toolkit')}</button>
                        <button onClick={() => navTo('servicios')} className="hover:text-[#00e9fa] transition-colors">{t('nav_services')}</button>
                        <button onClick={() => navTo('soluciones')} className="hover:text-[#00e9fa] transition-colors">{t('nav_solutions')}</button>
                        <button onClick={() => navTo('proyectos')} className="hover:text-[#00e9fa] transition-colors">{t('nav_projects')}</button>
                        <button onClick={() => navTo('documentacion')} className="hover:text-[#00e9fa] transition-colors">{t('nav_docs')}</button>
                        <button onClick={() => navTo('estrategia')} className="hover:text-[#00e9fa] transition-colors">{t('nav_strategy')}</button>
                        <button onClick={() => navTo('contacto')} className="hover:text-[#00e9fa] transition-colors">{t('nav_contact')}</button>

                        {/* Language Switcher */}
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
                                {t('nav_company')}
                                <span className={`transition-transform duration-300 ${empresaOpen ? 'rotate-180' : ''}`}>▼</span>
                            </button>
                            {empresaOpen && (
                                <div className="flex flex-col gap-3 mt-4">
                                    <button onClick={() => navTo('identidad')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_identity')}</button>
                                    <button onClick={() => navTo('blog')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_blog')}</button>
                                    <button onClick={() => navTo('filosofia')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_philosophy')}</button>
                                    <button onClick={() => navTo('legal')} className="text-lg text-gray-300 hover:text-[#00e9fa] transition-colors uppercase tracking-wider">{t('nav_legal')}</button>
                                </div>
                            )}
                        </div>

                        {/* Rest of menu items */}
                        <button onClick={() => navTo('toolkit')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_toolkit')}</button>
                        <button onClick={() => navTo('servicios')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_services')}</button>
                        <button onClick={() => navTo('soluciones')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_solutions')}</button>
                        <button onClick={() => navTo('proyectos')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_projects')}</button>
                        <button onClick={() => navTo('documentacion')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_docs')}</button>
                        <button onClick={() => navTo('estrategia')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_strategy')}</button>
                        <button onClick={() => navTo('convocatorias')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors">{t('nav_calls')}</button>
                        <button onClick={() => navTo('contacto')} className="text-2xl font-work uppercase tracking-[0.2em] font-light hover:text-[#00e9fa] transition-colors border-t border-white/10 pt-6">{t('nav_contact')}</button>
                        <div className="flex items-center justify-center gap-3 text-lg font-mono uppercase tracking-[0.2em] pt-6">
                            <button
                                onClick={() => setLang('es')}
                                className={`transition-colors ${lang === 'es' ? 'text-[#00e9fa]' : 'text-gray-400 hover:text-[#00e9fa]'}`}
                            >
                                {t('lang_es')}
                            </button>
                            <span className="text-gray-600">/</span>
                            <button
                                onClick={() => setLang('en')}
                                className={`transition-colors ${lang === 'en' ? 'text-[#00e9fa]' : 'text-gray-400 hover:text-[#00e9fa]'}`}
                            >
                                {t('lang_en')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Contenido */}
            <main className="flex-1">{children}</main>
            <Footer navTo={navTo} />
        </div>
    );
}
