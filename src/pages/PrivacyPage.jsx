import React from 'react';
import { Shield, CheckCircle2, Eye, Lock } from 'lucide-react';
import Layout from '../components/Layout';

export default function PrivacyPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/legal-privacidad-parallax.jpg"
                    alt="Privacidad Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Shield className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Política de Privacidad</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Protección de Datos</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Custodiamos la información personal con medidas técnicas, administrativas y legales alineadas a las normativas vigentes. Transparencia, consentimiento informado y mínima recolección guían todo el ciclo de datos.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Eye size={18} className="text-[#00e9fa]" /> Principios</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Finalidad explícita y legítima.</li>
                            <li>Minimización y retención limitada.</li>
                            <li>Exactitud y actualización continua.</li>
                            <li>Seguridad por diseño y por defecto.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Lock size={18} className="text-[#00e9fa]" /> Salvaguardas</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Cifrado en tránsito (TLS 1.2+).</li>
                            <li>Segregación de ambientes y roles.</li>
                            <li>Backups cifrados y rotación de llaves.</li>
                            <li>Monitoreo de accesos y alertas.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><CheckCircle2 size={18} className="text-[#00e9fa]" /> Derechos</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Acceso, rectificación y supresión.</li>
                            <li>Portabilidad y restricción de tratamiento.</li>
                            <li>Oposición y revocatoria de consentimiento.</li>
                            <li>Canales de reclamación y respuesta.</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5 rounded-sm">
                    <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] mb-3">Canal de contacto</h4>
                    <p className="text-gray-300 text-sm leading-relaxed">Para ejercer derechos o conocer más sobre el tratamiento de datos, escríbenos a <span className="text-white font-semibold">datos@xlerion.com</span>. Responderemos en un máximo de 15 días hábiles.</p>
                </div>
            </div>
        </Layout>
    );
}
