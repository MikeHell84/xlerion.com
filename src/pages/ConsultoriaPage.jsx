import React from 'react';
import { Briefcase, Map, BarChart3, Workflow } from 'lucide-react';
import Layout from '../components/Layout';

export default function ConsultoriaPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Briefcase className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Consultoría en modularidad</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Estrategia Modular</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Asesoría para adoptar metodologías modulares que mejoran escalabilidad, mantenimiento y eficiencia en proyectos técnicos y creativos.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Map size={18} className="text-[#00e9fa]" /> Roadmap</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Evaluación de madurez modular.</li>
                            <li>Plan de adopción por fases.</li>
                            <li>Gobernanza y ownership.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BarChart3 size={18} className="text-[#00e9fa]" /> KPIs</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Tiempo de entrega y MTTR.</li>
                            <li>Reutilización y reducción de retrabajo.</li>
                            <li>Disponibilidad y estabilidad.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Workflow size={18} className="text-[#00e9fa]" /> Implementación</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Playbooks de migración.</li>
                            <li>Capacitación por roles.</li>
                            <li>Automatización de pipelines.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
