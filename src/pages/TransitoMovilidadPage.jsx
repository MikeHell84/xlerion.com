import React from 'react';
import { Car, Brain, BarChart3, MapPin } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function TransitoMovilidadPage() {
    const { t } = useLanguage();

    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/cronograma-progreso-parallax.jpg"
                    alt="Tránsito y Movilidad Inteligente Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Car className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('transito_banner_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('transito_banner_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('transito_header')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Brain size={18} className="text-[#00e9fa]" /> {t('transito_ai_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('transito_ai_1')}</li>
                            <li>{t('transito_ai_2')}</li>
                            <li>{t('transito_ai_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BarChart3 size={18} className="text-[#00e9fa]" /> {t('transito_optimization_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('transito_optimization_1')}</li>
                            <li>{t('transito_optimization_2')}</li>
                            <li>{t('transito_optimization_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><MapPin size={18} className="text-[#00e9fa]" /> {t('transito_impact_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('transito_impact_1')}</li>
                            <li>{t('transito_impact_2')}</li>
                            <li>{t('transito_impact_3')}</li>
                        </ul>
                    </div>
                </div>

                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">{t('transito_functioning_title')}</h2>
                    <div className="space-y-6 text-gray-300 leading-relaxed">
                        <p>{t('transito_functioning_p1')}</p>
                        <p>{t('transito_functioning_p2')}</p>
                        <p>{t('transito_functioning_p3')}</p>
                    </div>
                </section>

                <section>
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">{t('transito_benefits_title')}</h2>
                    <div className="grid md:grid-cols-2 gap-6">
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('transito_efficiency_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('transito_efficiency_desc')}</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('transito_sustainability_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('transito_sustainability_desc')}</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('transito_scalability_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('transito_scalability_desc')}</p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">{t('transito_data_title')}</h4>
                            <p className="text-gray-400 text-sm leading-relaxed">{t('transito_data_desc')}</p>
                        </div>
                    </div>
                </section>
            </div>
        </Layout>
    );
}
