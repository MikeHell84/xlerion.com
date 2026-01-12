import React from 'react';
import { Info, ShieldCheck, Scale, FileText } from 'lucide-react';
import Layout from '../components/Layout';

export default function TermsPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/noticias-eventos-parallax.jpg"
                    alt="Términos Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Info className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Términos de Uso</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Condiciones del Servicio</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Estos términos regulan el acceso y uso del sitio, toolkits y contenidos asociados. Al usar la plataforma aceptas las condiciones, políticas y limitaciones aquí descritas.</p>
                </header>

                <div className="grid md:grid-cols-2 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><ShieldCheck size={18} className="text-[#00e9fa]" /> Responsabilidades</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Uso lícito y respetuoso de propiedad intelectual.</li>
                            <li>No se garantiza disponibilidad continua ni ausencia de errores.</li>
                            <li>El usuario es responsable de sus credenciales y actividad.</li>
                            <li>Reportes de seguridad deben canalizarse a seguridad@xlerion.com.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Scale size={18} className="text-[#00e9fa]" /> Limitaciones</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Prohibido el uso para actividades ilícitas o dañinas.</li>
                            <li>Las herramientas se entregan "tal cual" sin garantías implícitas.</li>
                            <li>La responsabilidad de Xlerion se limita a los montos pagados por servicios contratados.</li>
                            <li>Descargas y cargas de datos deben cumplir marcos regulatorios aplicables.</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5 rounded-sm">
                    <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] mb-3 flex items-center gap-2"><FileText size={16} /> Actualizaciones</h4>
                    <p className="text-gray-300 text-sm leading-relaxed">Podemos actualizar estos términos para reflejar cambios normativos o funcionales. Notificaremos modificaciones relevantes y mantendremos el historial de versiones.</p>
                </div>
            </div>
        </Layout>
    );
}
