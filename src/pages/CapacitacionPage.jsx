import React from 'react';
import { Users, GraduationCap, ClipboardList, Mic } from 'lucide-react';
import Layout from '../components/Layout';

export default function CapacitacionPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Users className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Capacitación y talleres</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Formación Especializada</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Formación personalizada en herramientas, filosofía modular y mejores prácticas de documentación y diagnóstico técnico.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><GraduationCap size={18} className="text-[#00e9fa]" /> Programas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Bootcamps modulares.</li>
                            <li>Capacitación por roles.</li>
                            <li>Laboratorios prácticos.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><ClipboardList size={18} className="text-[#00e9fa]" /> Materiales</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Guías, checklists y playbooks.</li>
                            <li>Ejemplos listos para producción.</li>
                            <li>Grabaciones y documentación.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Mic size={18} className="text-[#00e9fa]" /> Modalidades</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Workshops en vivo y on-demand.</li>
                            <li>Sesiones 1:1 y cohortes.</li>
                            <li>Certificación interna.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
