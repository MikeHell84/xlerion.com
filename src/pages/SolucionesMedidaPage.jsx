import React from 'react';
import { Wrench, Puzzle, Layers, Sparkles } from 'lucide-react';
import Layout from '../components/Layout';

export default function SolucionesMedidaPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Wrench className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Soluciones a medida</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Diseño Personalizado</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Herramientas y sistemas hechos a la medida, combinando innovación tecnológica con enfoque modular y escalable.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Puzzle size={18} className="text-[#00e9fa]" /> Descubrimiento</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Levantamiento de requerimientos.</li>
                            <li>Mapeo de riesgos y dependencias.</li>
                            <li>Prototipos rápidos.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Layers size={18} className="text-[#00e9fa]" /> Arquitectura</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Diseños modulares y desacoplados.</li>
                            <li>Integraciones con APIs y DCC.</li>
                            <li>Escalabilidad y resiliencia.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Sparkles size={18} className="text-[#00e9fa]" /> Entrega</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Deploy asistido y documentación.</li>
                            <li>KPIs de éxito acordados.</li>
                            <li>Planes de soporte post-lanzamiento.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
