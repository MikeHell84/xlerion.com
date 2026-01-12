import React from 'react';
import { Code, ListChecks, Layers, Sliders } from 'lucide-react';
import Layout from '../components/Layout';

export default function ToolkitsPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/soluciones-parallax.jpg"
                    alt="Toolkits Modulares Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Code className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Toolkits modulares</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Interfaces Jerárquicas</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Conjuntos de herramientas modulares con capas jerárquicas que facilitan interacción intuitiva, personalización y escalabilidad sin fricción.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Layers size={18} className="text-[#00e9fa]" /> Estructura</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Capas modulares reutilizables.</li>
                            <li>Roles y permisos por nivel.</li>
                            <li>APIs claras para integraciones.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Sliders size={18} className="text-[#00e9fa]" /> Adaptabilidad</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Configuración por contexto y entorno.</li>
                            <li>Componentes UI parametrizables.</li>
                            <li>Soporte multilenguaje y theming.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><ListChecks size={18} className="text-[#00e9fa]" /> Entrega</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Kits listos para CI/CD.</li>
                            <li>Playbooks de adopción rápida.</li>
                            <li>Versionado semántico y soporte.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
