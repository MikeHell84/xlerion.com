import React from 'react';
import { BookOpen, FileText, Share2, ShieldCheck } from 'lucide-react';
import Layout from '../components/Layout';

export default function DocsStructPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/documentacion-parallax.jpg"
                    alt="Documentación Estructurada Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <BookOpen className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Documentación estructurada</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Legado Operativo</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Guías y manuales que preservan conocimiento, facilitan capacitación y aseguran continuidad operativa.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><FileText size={18} className="text-[#00e9fa]" /> Contenido</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Runbooks, checklists y diagramas.</li>
                            <li>Ejemplos prácticos y troubleshooting.</li>
                            <li>Versionado y control de cambios.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Share2 size={18} className="text-[#00e9fa]" /> Transferencia</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Onboarding guiado y capacitación.</li>
                            <li>Formato portable (PDF/MD/HTML).</li>
                            <li>Roles de actualización delegables.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><ShieldCheck size={18} className="text-[#00e9fa]" /> Confiabilidad</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Políticas de respaldo y retención.</li>
                            <li>Firmas y control de acceso.</li>
                            <li>Auditoría y trazabilidad.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
