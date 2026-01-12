import React from 'react';
import { Activity, Stethoscope, Bug, Wrench } from 'lucide-react';
import Layout from '../components/Layout';

export default function DiagnosticoPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Activity className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Diagnóstico</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Prevención de Fallos</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Protocolos previos a despliegue para detectar condiciones críticas antes de impactar producción.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Stethoscope size={18} className="text-[#00e9fa]" /> Pruebas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Health checks de servicios y dependencias.</li>
                            <li>Pruebas sintéticas y escenarios de carga.</li>
                            <li>Validación de configuraciones críticas.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Bug size={18} className="text-[#00e9fa]" /> Detección</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Escaneo de vulnerabilidades conocidas.</li>
                            <li>Alertas tempranas de recursos saturados.</li>
                            <li>Listas de bloqueo para incidentes recurrentes.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Wrench size={18} className="text-[#00e9fa]" /> Mitigación</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Runbooks con pasos accionables.</li>
                            <li>Procedimientos de escalamiento.</li>
                            <li>Documentación de causa raíz y remediación.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
