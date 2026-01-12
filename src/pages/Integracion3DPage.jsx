import React from 'react';
import { Cpu, MonitorSmartphone, RefreshCcw, Link as LinkIcon } from 'lucide-react';
import Layout from '../components/Layout';

export default function Integracion3DPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/proyectos-parallax.jpg"
                    alt="Integración con Motores 3D Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Cpu className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Integración con motores 3D</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Interoperabilidad</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Adaptamos y optimizamos soluciones para Unreal Engine, Unity y 3DS Max, garantizando despliegues fluidos y multiplataforma.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><MonitorSmartphone size={18} className="text-[#00e9fa]" /> Pipelines</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Export/import optimizado de assets.</li>
                            <li>Perfiles de build por plataforma.</li>
                            <li>Automatización de empaquetado.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><RefreshCcw size={18} className="text-[#00e9fa]" /> Optimización</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>LOD, lightmaps y batching.</li>
                            <li>Monitoreo de fps y memoria.</li>
                            <li>Perfiles de red para streaming.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><LinkIcon size={18} className="text-[#00e9fa]" /> Interoperabilidad</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Conectores entre motores y DCC.</li>
                            <li>Conversión de formatos y materiales.</li>
                            <li>Pruebas cruzadas multi-motor.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
