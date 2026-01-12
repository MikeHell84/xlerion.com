import React from 'react';
import { Database, FileBadge2, Copyright, BadgeCheck } from 'lucide-react';
import Layout from '../components/Layout';

export default function LicensesPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/servicios-productos-parallax.jpg"
                    alt="Licencias Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Database className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Licencias</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Software y Contenido</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Definimos licencias claras para código, recursos y documentación. Respetamos propiedad intelectual y promovemos el uso responsable de terceros.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><FileBadge2 size={18} className="text-[#00e9fa]" /> Código</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Repos públicos bajo licencias OSS compatibles.</li>
                            <li>Componentes internos con licencias propietarias.</li>
                            <li>Uso de dependencias conforme a sus licencias originales.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BadgeCheck size={18} className="text-[#00e9fa]" /> Medios</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Assets gráficos y 3D con permisos documentados.</li>
                            <li>Fotografía y video con releases y atribución cuando aplica.</li>
                            <li>Prohibida la redistribución no autorizada de recursos premium.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Copyright size={18} className="text-[#00e9fa]" /> Documentación</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Documentos técnicos con atribución requerida.</li>
                            <li>Manual de marca y guías de uso con restricciones explícitas.</li>
                            <li>Se prohíbe el uso comercial no autorizado.</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5 rounded-sm">
                    <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] mb-3">Consultas de licenciamiento</h4>
                    <p className="text-gray-300 text-sm leading-relaxed">Para acuerdos específicos o aclarar compatibilidades de licencias, contáctanos en <span className="text-white font-semibold">licencias@xlerion.com</span>. Incluye el contexto de uso y alcance del proyecto.</p>
                </div>
            </div>
        </Layout>
    );
}
