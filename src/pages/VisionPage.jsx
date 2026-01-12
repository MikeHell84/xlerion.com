import React from 'react';
import { Rocket } from 'lucide-react';
import Layout from '../components/Layout';

export default function VisionPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/proyectos-parallax.jpg"
                    alt="Visión Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Rocket className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <h1 className="text-6xl md:text-8xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Visión</h1>
                    </div>
                </div>
            </div>
            {/* Content */}
            <div className="pt-20 pb-20 px-8 max-w-5xl mx-auto">
                {/* Hero Section */}
                <div className="mb-16">
                    <div className="border-l-4 border-[#00e9fa] pl-8">
                        <h2 className="text-2xl md:text-3xl text-gray-300 font-light leading-relaxed">
                            Ser referentes en Latinoamérica en el diseño inteligente de soluciones técnicas y creativas, construyendo puentes entre la tecnología autónoma y el impacto cultural.
                        </h2>
                    </div>
                </div>

                {/* Detailed Content */}
                <div className="space-y-12">
                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
              // Horizonte 2030
                        </h3>
                        <div className="prose prose-invert max-w-none">
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                Nos visualizamos como la empresa líder en Latinoamérica para soluciones técnicas modulares, reconocidos por nuestra capacidad de integrar tecnología de vanguardia con sensibilidad cultural y creatividad.
                            </p>
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                Queremos ser el socio estratégico de elección para proyectos que requieren innovación técnica, desde startups disruptivas hasta instituciones culturales establecidas.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
              // Pilares de Crecimiento
                        </h3>
                        <div className="space-y-6">
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">Excelencia Técnica</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    Mantener los más altos estándares en desarrollo de software, arquitectura de sistemas y optimización de procesos, siendo reconocidos por la calidad y robustez de nuestras soluciones.
                                </p>
                            </div>
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">Impacto Cultural</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    Contribuir al desarrollo cultural de la región mediante herramientas que empoderan a artistas, creadores y organizaciones culturales a amplificar su impacto.
                                </p>
                            </div>
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">Innovación Continua</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    Estar a la vanguardia de las tendencias tecnológicas, explorando constantemente nuevas posibilidades en IA, automatización y sistemas inteligentes.
                                </p>
                            </div>
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">Ecosistema Colaborativo</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    Construir una red de colaboradores, partners y clientes que compartan nuestra visión de tecnología al servicio de la creatividad y el desarrollo humano.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
              // Compromiso Regional
                        </h3>
                        <div className="bg-gradient-to-br from-[#00e9fa]/10 via-transparent to-[#00e9fa]/5 border border-[#00e9fa]/20 p-8 rounded-sm">
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                Creemos firmemente en el potencial de Latinoamérica como polo de innovación tecnológica y creatividad. Nuestra visión incluye:
                            </p>
                            <ul className="space-y-3 text-gray-400">
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>Generar empleo de calidad y oportunidades de desarrollo profesional en la región</span>
                                </li>
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>Contribuir al desarrollo tecnológico local mediante transferencia de conocimiento</span>
                                </li>
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>Fomentar la colaboración entre tecnología, cultura y desarrollo social</span>
                                </li>
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>Posicionar a Latinoamérica como referente en soluciones tecnológicas innovadoras</span>
                                </li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </Layout>
    );
}
