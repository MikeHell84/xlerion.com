import React from 'react';
import { Terminal, Database, BarChart3, Bell } from 'lucide-react';
import Layout from '../components/Layout';

export default function LoggingPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Terminal className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Logging</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Trazabilidad Total</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Sistema de logs dinámicos en formato JSON, estructurado para auditoría, depuración y observabilidad de extremo a extremo.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Database size={18} className="text-[#00e9fa]" /> Estructura</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Eventos normalizados con IDs correlacionados.</li>
                            <li>Contexto de request, usuario y módulo.</li>
                            <li>Soporte para adjuntos y métricas.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BarChart3 size={18} className="text-[#00e9fa]" /> Observabilidad</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Dashboards en tiempo real y export.</li>
                            <li>Filtros por severidad, módulo y rango.</li>
                            <li>Integración con métricas de rendimiento.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Bell size={18} className="text-[#00e9fa]" /> Alertas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Notificaciones a canales configurables.</li>
                            <li>Umbrales por tipo de error y frecuencia.</li>
                            <li>Planes de respuesta automatizada.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
