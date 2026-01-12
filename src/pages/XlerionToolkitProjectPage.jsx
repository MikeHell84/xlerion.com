import React from 'react';
import { Terminal, Activity, Zap, Shield } from 'lucide-react';
import Layout from '../components/Layout';

export default function XlerionToolkitProjectPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/soluciones-parallax.jpg"
                    alt="Toolkit Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Terminal className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Xlerion Toolkit</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Core Modular</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Conjunto modular para diagnóstico, logging y rendimiento en entornos técnicos complejos con trazabilidad total.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Activity size={18} className="text-[#00e9fa]" /> Diagnóstico</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Checklists y health checks.</li>
                            <li>Alertas tempranas configurables.</li>
                            <li>Reportes técnicos exportables.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Zap size={18} className="text-[#00e9fa]" /> Performance</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Comparadores de fps y memoria.</li>
                            <li>Perfiles por plataforma.</li>
                            <li>Optimización continua con KPIs.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Shield size={18} className="text-[#00e9fa]" /> Seguridad</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Controles de acceso y auditoría.</li>
                            <li>Logs estructurados y firmados.</li>
                            <li>Backups y recuperación asistida.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
