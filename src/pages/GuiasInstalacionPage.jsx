import React from 'react';
import { Shield, Download, CheckCircle2 } from 'lucide-react';
import Layout from '../components/Layout';

export default function GuiasInstalacionPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/cronograma-progreso-parallax.jpg"
                    alt="Guías Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Shield className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Documentación</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Guías de Instalación</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Instrucciones paso a paso para diferentes entornos, plataformas y casos de uso con soluciones para problemas comunes.</p>
                </header>

                <div className="grid md:grid-cols-2 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Download size={18} className="text-[#00e9fa]" /> Entornos Soportados</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Windows, macOS y Linux.</li>
                            <li>Entornos cloud (AWS, GCP, Azure).</li>
                            <li>Contenedores Docker y Kubernetes.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><CheckCircle2 size={18} className="text-[#00e9fa]" /> Validación</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Tests de verificación post-instalación.</li>
                            <li>Checklist de configuración mínima.</li>
                            <li>Diagnóstico de dependencias.</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                    <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-4">Cobertura</h3>
                    <p className="text-gray-400 text-sm leading-relaxed">Cada guía incluye requisitos previos, pasos de instalación, configuración de variables de entorno, troubleshooting de errores frecuentes y recursos adicionales. Se mantiene actualizada con cada versión y se adapta a nuevos entornos según demanda.</p>
                </div>
            </div>
        </Layout>
    );
}
