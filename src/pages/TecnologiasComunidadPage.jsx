import React from 'react';
import { Heart, Smartphone, Users, Lightbulb } from 'lucide-react';
import Layout from '../components/Layout';

export default function TecnologiasComunidadPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/convocatorias-alianzas-parallax.jpg"
                    alt="Tecnologías al Alcance del Bienestar de la Comunidad Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Heart className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">// Impacto Social</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Tecnologías al Alcance del Bienestar</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">Proyectos orientados a implementar soluciones tecnológicas accesibles que mejoran la calidad de vida y el bienestar social de las comunidades.</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Smartphone size={18} className="text-[#00e9fa]" /> Accesibilidad</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Tecnología al alcance de todos.</li>
                            <li>Interfases intuitivas y simples.</li>
                            <li>Bajo costo de implementación.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Users size={18} className="text-[#00e9fa]" /> Enfoque Territorial</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Soluciones contextualizadas.</li>
                            <li>Respeto por identidades culturales.</li>
                            <li>Participación comunitaria activa.</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Lightbulb size={18} className="text-[#00e9fa]" /> Innovación Social</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>Inclusión digital efectiva.</li>
                            <li>Desarrollo sostenible y equitativo.</li>
                            <li>Empoderamiento comunitario.</li>
                        </ul>
                    </div>
                </div>

                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">Nuestra Propuesta</h2>
                    <div className="space-y-6 text-gray-300 leading-relaxed">
                        <p>Estas iniciativas buscan integrar innovación con enfoque territorial, promoviendo la inclusión digital y el desarrollo sostenible en comunidades tradicionalmente excluidas del acceso tecnológico.</p>
                        <p>A través de soluciones modulares y adaptables, facilitamos el acceso a herramientas tecnológicas que mejoran la calidad de vida, fortalecen el tejido social y potencian las capacidades locales.</p>
                        <p>Nuestro compromiso es crear tecnología con propósito social, diseñada desde y para las comunidades, respetando sus contextos culturales y promoviendo su autonomía técnica.</p>
                    </div>
                </section>

                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">Áreas de Impacto</h2>
                    <div className="grid md:grid-cols-2 gap-6">
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Salud Comunitaria</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Soluciones tecnológicas para mejorar el acceso a servicios de salud, monitoreo y prevención en comunidades remotas o vulnerables.</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Educación Digital</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Plataformas y herramientas educativas accesibles que democratizan el conocimiento y fomentan el aprendizaje continuo.</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Economía Local</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Sistemas que facilitan el comercio local, la gestión de recursos y el fortalecimiento de emprendimientos comunitarios.</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">Medio Ambiente</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Tecnologías para monitoreo ambiental, gestión de recursos naturales y promoción de prácticas sostenibles.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">Principios Rectores</h2>
                    <div className="space-y-4">
                        <div className="p-6 border-l-4 border-[#00e9fa] bg-white/5">
                            <h4 className="text-white font-bold mb-2">Tecnología con Propósito</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Cada solución tecnológica debe responder a necesidades reales identificadas en conjunto con las comunidades.</p>
                        </div>
                        <div className="p-6 border-l-4 border-[#00e9fa] bg-white/5">
                            <h4 className="text-white font-bold mb-2">Sostenibilidad y Autonomía</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Promovemos la transferencia de conocimiento y la apropiación tecnológica para garantizar la sostenibilidad a largo plazo.</p>
                        </div>
                        <div className="p-6 border-l-4 border-[#00e9fa] bg-white/5">
                            <h4 className="text-white font-bold mb-2">Inclusión y Equidad</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">Diseñamos soluciones accesibles que no perpetúan brechas digitales ni sociales, sino que las reducen activamente.</p>
                        </div>
                    </div>
                </section>
            </div>
        </Layout>
    );
}
