import React from 'react';
import { Target } from 'lucide-react';
import Layout from '../components/Layout';

export default function MisionPage() {
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/filosofia-parallax.jpg"
                    alt="Misión Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Target className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <h1 className="text-6xl md:text-8xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">Misión</h1>
                    </div>
                </div>
            </div>
            {/* Content */}
            <div className="pt-20 pb-20 px-8 max-w-5xl mx-auto">
                {/* Hero Section */}
                <div className="mb-16">
                    <div className="border-l-4 border-[#00e9fa] pl-8">
                        <h2 className="text-2xl md:text-3xl text-gray-300 font-light leading-relaxed">
                            Potenciar la ingeniería creativa con toolkits modulares que diagnostican, optimizan y automatizan tareas técnicas, permitiendo a los creadores enfocarse en la esencia de su obra.
                        </h2>
                    </div>
                </div>

                {/* Detailed Content */}
                <div className="space-y-12">
                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
              // Objetivo Principal
                        </h3>
                        <div className="prose prose-invert max-w-none">
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                En Xlerion, nuestra misión es revolucionar la forma en que los profesionales creativos y técnicos abordan sus proyectos. Creemos que la tecnología debe ser un facilitador, no un obstáculo.
                            </p>
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                Desarrollamos soluciones modulares que se adaptan a las necesidades específicas de cada proyecto, eliminando tareas repetitivas y permitiendo que los creadores se concentren en lo que realmente importa: su visión creativa.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
              // Principios Fundamentales
                        </h3>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">Modularidad</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    Herramientas diseñadas para integrarse perfectamente en flujos de trabajo existentes, sin imponer estructuras rígidas.
                                </p>
                            </div>
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">Automatización Inteligente</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    Sistemas que aprenden y se adaptan, reduciendo la carga técnica mientras mantienen el control en manos del usuario.
                                </p>
                            </div>
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">Diagnóstico Preciso</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    Análisis profundo de proyectos para identificar oportunidades de optimización y áreas de mejora.
                                </p>
                            </div>
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">Enfoque en el Creador</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    Liberando tiempo y recursos mentales para que los profesionales se concentren en la innovación y la creatividad.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
              // Impacto
                        </h3>
                        <div className="bg-gradient-to-r from-[#00e9fa]/10 to-transparent border-l-4 border-[#00e9fa] p-8">
                            <p className="text-gray-300 text-lg leading-relaxed">
                                Aspiramos a ser el catalizador que transforma la manera en que se conciben y ejecutan los proyectos creativos y técnicos,
                                empoderando a individuos y equipos para alcanzar nuevos niveles de excelencia y eficiencia.
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </Layout>
    );
}
