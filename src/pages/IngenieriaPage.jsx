import React from 'react';
import { Cpu, Wrench, Layers, BookOpen } from 'lucide-react';
import Layout from '../components/Layout';

export default function IngenieriaPage() {
    return (
        <Layout>
            <div className="pt-32 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <div className="flex items-center gap-4 mb-4">
                        <Cpu className="text-[#00e9fa]" size={48} />
                        <div>
                            <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase">// Ingeniería Creativa</p>
                            <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white">Tooling Modular</h1>
                        </div>
                    </div>
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Desarrollo de herramientas modulares para optimizar procesos técnicos y creativos en la industria multimedia.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Layers size={18} className="text-[#00e9fa]" /> Arquitectura</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Módulos reutilizables y desacoplados.</li>
                            <li>Integración con pipelines existentes.</li>
                            <li>APIs claras y documentación técnica.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Wrench size={18} className="text-[#00e9fa]" /> Implementación</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Scripts de automatización y build.</li>
                            <li>Toolkits de soporte para QA y dev.</li>
                            <li>Versionado semántico y releases guiados.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BookOpen size={18} className="text-[#00e9fa]" /> Documentación</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Guías de uso y ejemplos prácticos.</li>
                            <li>Playbooks para integraciones.</li>
                            <li>Historias de usuario y casos de éxito.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
