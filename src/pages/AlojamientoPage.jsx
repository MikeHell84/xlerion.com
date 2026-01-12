import React from 'react';
import { Heart, BedDouble, Ruler, Shield } from 'lucide-react';
import Layout from '../components/Layout';

export default function AlojamientoPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Heart className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Alojamiento Adaptado</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Habitabilidad Segura</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Diseño de espacios con ergonomía, accesibilidad y protocolos de asistencia integral, especialmente en entornos rurales como Nocaima.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BedDouble size={18} className="text-[#00e9fa]" /> Diseño</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Alturas accesibles, pasamanos y rampas.</li>
                            <li>Baños adaptados con barras y duchas niveladas.</li>
                            <li>Iluminación segura y señalización nocturna.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Ruler size={18} className="text-[#00e9fa]" /> Ergonomía</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Mobiliario ajustable y superficies antideslizantes.</li>
                            <li>Zonas de apoyo para transferencias seguras.</li>
                            <li>Distribución pensada para movilidad asistida.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Shield size={18} className="text-[#00e9fa]" /> Protocolos</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Planes de emergencia con rutas accesibles.</li>
                            <li>Checklists operativas y bitácoras diarias.</li>
                            <li>Capacitación continua del personal de apoyo.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
