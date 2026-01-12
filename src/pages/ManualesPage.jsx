import React from 'react';
import { Database, FileText, Code } from 'lucide-react';
import Layout from '../components/Layout';

export default function ManualesPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/documentacion-parallax.jpg"
                    alt="Manuales Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Database className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Documentación</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Manuales por Módulo</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Documentación detallada que explica el funcionamiento, configuración y mantenimiento de cada módulo con ejemplos prácticos y recomendaciones.</p>
                </header>

                <div className="grid md:grid-cols-2 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><FileText size={18} className="text-[#00e9fa]" /> Alcance</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Módulo por módulo con instrucciones precisas.</li>
                            <li>Ejemplos de uso y casos reales.</li>
                            <li>Troubleshooting y soluciones comunes.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Code size={18} className="text-[#00e9fa]" /> Contenido</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Instalación y setup por entorno.</li>
                            <li>APIs y métodos disponibles.</li>
                            <li>Patrones de implementación y mejores prácticas.</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                    <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-4">Objetivos</h3>
                    <p className="text-gray-400 text-sm leading-relaxed">Proporcionar una guía completa que permita a desarrolladores y usuarios comprender cada componente, integrarlo correctamente y resolver problemas de forma autónoma. La documentación es clara, accesible y constantemente actualizada.</p>
                </div>
            </div>
        </Layout>
    );
}
