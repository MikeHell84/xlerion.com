import React from 'react';
import { Car, Brain, BarChart3, MapPin } from 'lucide-react';
import Layout from '../components/Layout';

export default function TransitoMovilidadPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/cronograma-progreso-parallax.jpg"
                    alt="Tránsito y Movilidad Inteligente Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Car className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Innovación Urbana</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Tránsito y Movilidad Inteligente</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Desarrollo de un sistema de semáforos guiados con inteligencia artificial que realiza conteo dinámico del flujo vehicular para optimizar la movilidad urbana.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Brain size={18} className="text-[#00e9fa]" /> Inteligencia Artificial</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Conteo dinámico de vehículos.</li>
                            <li>Análisis de flujo en tiempo real.</li>
                            <li>Algoritmos de optimización adaptativa.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BarChart3 size={18} className="text-[#00e9fa]" /> Optimización</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Ajuste automático de tiempos.</li>
                            <li>Reducción de congestión vehicular.</li>
                            <li>Mejora en tiempos de desplazamiento.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><MapPin size={18} className="text-[#00e9fa]" /> Impacto Urbano</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Circulación ágil y eficiente.</li>
                            <li>Reducción de emisiones contaminantes.</li>
                            <li>Ciudades más sostenibles.</li>
                        </ul>
                    </div>
                </div>

                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">Funcionamiento del Sistema</h2>
                    <div className="space-y-6 text-gray-300 leading-relaxed">
                        <p>El sistema permite ajustar los tiempos de paso en función del volumen de vehículos en cada dirección, facilitando una circulación ágil y eficiente en las ciudades.</p>
                        <p>Utilizando visión por computadora e inteligencia artificial, el sistema detecta y cuenta vehículos en tiempo real, procesando esta información para tomar decisiones inteligentes sobre la sincronización semafórica.</p>
                        <p>Esta tecnología modular se adapta a diferentes contextos urbanos, desde intersecciones críticas hasta corredores viales completos, escalando según las necesidades de cada ciudad.</p>
                    </div>
                </section>

                <section>
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">Beneficios Clave</h2>
                    <div className="grid md:grid-cols-2 gap-6">
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Eficiencia Operativa</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Reducción de tiempos de espera y mejora en el flujo vehicular mediante ajustes dinámicos basados en datos reales.</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Sostenibilidad Ambiental</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Disminución de emisiones contaminantes al reducir tiempos de ralentí y optimizar desplazamientos.</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Escalabilidad Modular</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Arquitectura flexible que permite implementación gradual y adaptación a diferentes contextos urbanos.</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Datos Accionables</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Generación de métricas y estadísticas para planificación urbana y toma de decisiones informadas.</p>
                        </div>
                    </div>
                </section>
            </div>
        </Layout>
    );
}
