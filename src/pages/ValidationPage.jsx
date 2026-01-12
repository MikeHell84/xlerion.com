import React from 'react';
import { CheckCircle2, Shield, ListChecks, AlertTriangle } from 'lucide-react';
import Layout from '../components/Layout';

export default function ValidationPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <CheckCircle2 className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Validación</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Control de Calidad</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Pipeline automático que valida activos multimedia, formatos y códigos de error en tiempo real para asegurar integridad en cada release.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Shield size={18} className="text-[#00e9fa]" /> Reglas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Checks de formato, peso y resolución.</li>
                            <li>Validación de hash y firmas.</li>
                            <li>Listas de compatibilidad por plataforma.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><ListChecks size={18} className="text-[#00e9fa]" /> Automatización</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Triggers en CI/CD para cada commit.</li>
                            <li>Reportes JSON con trazas y tiempos.</li>
                            <li>Alertas cuando hay bloqueos críticos.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><AlertTriangle size={18} className="text-[#00e9fa]" /> Recuperación</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Rollback asistido de activos fallidos.</li>
                            <li>Bitácora de incidentes con contexto.</li>
                            <li>Reintentos controlados y cuarentena.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
