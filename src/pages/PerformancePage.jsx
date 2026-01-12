import React from 'react';
import { Zap, Gauge, Cpu, Timer } from 'lucide-react';
import Layout from '../components/Layout';

export default function PerformancePage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Zap className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Performance</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Rendimiento Optimizado</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Comparadores y perfiles que maximizan el flujo en Windows, identificando cuellos de botella antes de que escalen.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Gauge size={18} className="text-[#00e9fa]" /> Métricas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Latencia p95/p99, throughput y uso de CPU.</li>
                            <li>Seguimiento de I/O y colas de trabajo.</li>
                            <li>Históricos comparables entre builds.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Cpu size={18} className="text-[#00e9fa]" /> Optimización</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Perfiles guiados para ajustar configuraciones.</li>
                            <li>Recomendaciones de hardware y caching.</li>
                            <li>Scripts de tuning para despliegue.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Timer size={18} className="text-[#00e9fa]" /> Automatización</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Benchmarks programados y comparativos.</li>
                            <li>Alertas de degradación progresiva.</li>
                            <li>Export a reportes ejecutivos.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
