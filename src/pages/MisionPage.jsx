import React from 'react';
import { Target } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function MisionPage() {
    const { t } = useLanguage();

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
                        <h1 className="text-6xl md:text-8xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('mision_banner_title')}</h1>
                    </div>
                </div>
            </div>
            {/* Content */}
            <div className="pt-20 pb-20 px-8 max-w-5xl mx-auto">
                {/* Hero Section */}
                <div className="mb-16">
                    <div className="border-l-4 border-[#00e9fa] pl-8">
                        <h2 className="text-2xl md:text-3xl text-gray-300 font-light leading-relaxed">
                            {t('mision_hero')}
                        </h2>
                    </div>
                </div>

                {/* Detailed Content */}
                <div className="space-y-12">
                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
                            {t('mision_objective_title')}
                        </h3>
                        <div className="prose prose-invert max-w-none">
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                {t('mision_objective_p1')}
                            </p>
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                {t('mision_objective_p2')}
                            </p>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
                            {t('mision_principles_title')}
                        </h3>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">{t('mision_modular_title')}</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    {t('mision_modular_desc')}
                                </p>
                            </div>
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">{t('mision_automation_title')}</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    {t('mision_automation_desc')}
                                </p>
                            </div>
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">{t('mision_diagnostic_title')}</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    {t('mision_diagnostic_desc')}
                                </p>
                            </div>
                            <div className="bg-white/5 border border-white/10 p-6 rounded-sm">
                                <h4 className="font-bold text-lg mb-3 text-white">{t('mision_creator_title')}</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    {t('mision_creator_desc')}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
                            {t('mision_impact_title')}
                        </h3>
                        <div className="bg-gradient-to-r from-[#00e9fa]/10 to-transparent border-l-4 border-[#00e9fa] p-8">
                            <p className="text-gray-300 text-lg leading-relaxed">
                                {t('mision_impact_text')}
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </Layout>
    );
}
