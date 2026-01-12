import React from 'react';
import { Target, Palette, Sparkles, Layout as LayoutIcon } from 'lucide-react';
import Layout from '../components/Layout';

export default function BrandingPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/filosofia-parallax.jpg"
                    alt="Branding Técnico-Creativo Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Target className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Branding técnico-creativo</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Identidad Funcional</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Identidades visuales y conceptuales que combinan lógica simbólica y funcional para reforzar coherencia e impacto cultural.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Palette size={18} className="text-[#00e9fa]" /> Sistema Visual</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Paletas técnicas y accesibles.</li>
                            <li>Iconografía funcional y semántica.</li>
                            <li>Tipografía orientada a legibilidad.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><LayoutIcon size={18} className="text-[#00e9fa]" /> Arquitectura</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Guías de uso multicanal.</li>
                            <li>Componentes UI coherentes.</li>
                            <li>Design tokens versionados.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Sparkles size={18} className="text-[#00e9fa]" /> Estrategia</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Mensajes alineados a producto.</li>
                            <li>Activos listos para lanzamientos.</li>
                            <li>Coherencia entre técnica y narrativa.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
