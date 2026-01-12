import React from 'react';
import { MapPin, Navigation, Compass, Users } from 'lucide-react';
import Layout from '../components/Layout';

export default function TurismoPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <MapPin className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Turismo Incluyente</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Rutas Accesibles</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Diseñamos experiencias seguras para personas con discapacidad, adultos mayores y mujeres embarazadas. Enfoque total en accesibilidad y acompañamiento técnico.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Navigation size={18} className="text-[#00e9fa]" /> Rutas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Mapeo de puntos accesibles y descansos.</li>
                            <li>Protocolos de evacuación y señalética clara.</li>
                            <li>Coordinación con guías certificados.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Compass size={18} className="text-[#00e9fa]" /> Logística</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Transporte adaptado y reservas priorizadas.</li>
                            <li>Kits de apoyo y dispositivos de asistencia.</li>
                            <li>Briefing previo con necesidades individuales.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Users size={18} className="text-[#00e9fa]" /> Acompañamiento</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Personal capacitado en accesibilidad.</li>
                            <li>Plan de comunicación con familiares.</li>
                            <li>Documentación de experiencia y mejoras.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
