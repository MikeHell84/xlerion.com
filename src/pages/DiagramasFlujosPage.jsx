import React from 'react';
import { TrendingUp, Workflow, GitBranch } from 'lucide-react';
import Layout from '../components/Layout';

export default function DiagramasFlujosPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/documentacion-recursos-parallax.jpg"
                    alt="Diagramas Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <TrendingUp className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Documentación</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Diagramas de Flujo</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Visualización clara de la arquitectura, flujo de datos y procesos internos que se actualizan con cada nueva funcionalidad.</p>
                </header>

                <div className="grid md:grid-cols-2 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Workflow size={18} className="text-[#00e9fa]" /> Tipos de Diagramas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Arquitectura de sistemas y componentes.</li>
                            <li>Flujo de datos y procesamiento.</li>
                            <li>Secuencias de operación y eventos.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><GitBranch size={18} className="text-[#00e9fa]" /> Mantenimiento</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Actualización continua con cambios.</li>
                            <li>Versioning y control de cambios.</li>
                            <li>Documentación de integraciones nuevas.</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                    <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-4">Propósito</h3>
                    <p className="text-gray-400 text-sm leading-relaxed">Los diagramas representan visualmente la complejidad del sistema, facilitando el entendimiento rápido de cómo se relacionan los componentes y cómo fluye la información. Son esenciales para onboarding de nuevos desarrolladores y análisis de impacto en cambios.</p>
                </div>
            </div>
        </Layout>
    );
}
