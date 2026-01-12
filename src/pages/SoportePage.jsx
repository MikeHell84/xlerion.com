import React from 'react';
import { Shield, LifeBuoy, RefreshCcw, Clock } from 'lucide-react';
import Layout from '../components/Layout';

export default function SoportePage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Shield className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Soporte y actualización</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Continuidad</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Implementación, mantenimiento y evolución continua alineada a necesidades de negocio y cambios tecnológicos.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><LifeBuoy size={18} className="text-[#00e9fa]" /> Soporte</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Mesas de ayuda y SLAs claros.</li>
                            <li>Soporte funcional y técnico.</li>
                            <li>Planes de continuidad.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><RefreshCcw size={18} className="text-[#00e9fa]" /> Actualización</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Release cycles planificados.</li>
                            <li>Compatibilidad y parches.</li>
                            <li>Roadmaps compartidos.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Clock size={18} className="text-[#00e9fa]" /> Monitoreo</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Alertas de disponibilidad.</li>
                            <li>Health checks y métricas.</li>
                            <li>Reportes de estado periódicos.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
