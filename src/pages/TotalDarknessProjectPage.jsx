import React from 'react';
import { Cpu, BookOpen, GitBranch, Sparkles } from 'lucide-react';
import Layout from '../components/Layout';

export default function TotalDarknessProjectPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/blog-bitacora-parallax.jpg"
                    alt="Total Darkness Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Cpu className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Total Darkness – Pelijuego</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Narrativa Inmersiva</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Obra literaria adaptada a experiencia interactiva con decisiones ramificadas, entornos 3D y cinemáticas filosóficas.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <a
                        className="p-8 border border-white/10 bg-white/5 rounded-sm block hover:border-[#00e9fa]/60 transition-colors"
                        href="/total-darkness/historia.html"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BookOpen size={18} className="text-[#00e9fa]" /> Historia</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Arcos narrativos con decisiones.</li>
                            <li>Temas filosóficos y existenciales.</li>
                            <li>Documentación de lore y guiones.</li>
                        </ul>
                    </a>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><GitBranch size={18} className="text-[#00e9fa]" /> Tecnología</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Entornos 3D optimizados.</li>
                            <li>Sistemas de decisiones y estados.</li>
                            <li>Tooling interno para escenas.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Sparkles size={18} className="text-[#00e9fa]" /> Experiencia</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Cinemáticas y atmósferas adaptativas.</li>
                            <li>Audio reactivo y voz en off.</li>
                            <li>Accesibilidad y controles asistidos.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
