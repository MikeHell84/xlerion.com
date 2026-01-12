import React from 'react';
import { Heart, Smartphone, Users, Lightbulb } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function TecnologiasComunidadPage() {
    const { t } = useLanguage();

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
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('tecnologias_banner_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('tecnologias_banner_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('tecnologias_header')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Smartphone size={18} className="text-[#00e9fa]" /> {t('tecnologias_accessibility_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('tecnologias_accessibility_1')}</li>
                            <li>{t('tecnologias_accessibility_2')}</li>
                            <li>{t('tecnologias_accessibility_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Users size={18} className="text-[#00e9fa]" /> {t('tecnologias_territorial_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('tecnologias_territorial_1')}</li>
                            <li>{t('tecnologias_territorial_2')}</li>
                            <li>{t('tecnologias_territorial_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Lightbulb size={18} className="text-[#00e9fa]" /> {t('tecnologias_innovation_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('tecnologias_innovation_1')}</li>
                            <li>{t('tecnologias_innovation_2')}</li>
                            <li>{t('tecnologias_innovation_3')}</li>
                        </ul>
                    </div>
                </div>

                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">{t('tecnologias_proposal_title')}</h2>
                    <div className="space-y-6 text-gray-300 leading-relaxed">
                        <p>{t('tecnologias_proposal_p1')}</p>
                        <p>{t('tecnologias_proposal_p2')}</p>
                        <p>{t('tecnologias_proposal_p3')}</p>
                    </div>
                </section>

                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">{t('tecnologias_areas_title')}</h2>
                    <div className="grid md:grid-cols-2 gap-6">
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('tecnologias_health_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('tecnologias_health_desc')}</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('tecnologias_education_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('tecnologias_education_desc')}</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('tecnologias_economy_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('tecnologias_economy_desc')}</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('tecnologias_environment_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('tecnologias_environment_desc')}</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">{t('tecnologias_principles_title')}</h2>
                    <div className="space-y-4">
                        <div className="p-6 border-l-4 border-[#00e9fa] bg-white/5">
                            <h4 className="text-white font-bold mb-2">{t('tecnologias_purpose_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('tecnologias_purpose_desc')}</p>
                        </div>
                        <div className="p-6 border-l-4 border-[#00e9fa] bg-white/5">
                            <h4 className="text-white font-bold mb-2">{t('tecnologias_sustainability_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('tecnologias_sustainability_desc')}</p>
                        </div>
                        <div className="p-6 border-l-4 border-[#00e9fa] bg-white/5">
                            <h4 className="text-white font-bold mb-2">{t('tecnologias_inclusion_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('tecnologias_inclusion_desc')}</p>
                        </div>
                    </div>
                </section>
            </div>
        </Layout>
    );
}
