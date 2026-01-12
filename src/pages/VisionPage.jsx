import React from 'react';
import { Rocket } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function VisionPage() {
    const { t } = useLanguage();

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
                        <h1 className="text-6xl md:text-8xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('vision_banner_title')}</h1>
                    </div>
                </div>
            </div>
            {/* Content */}
            <div className="pt-20 pb-20 px-8 max-w-5xl mx-auto">
                {/* Hero Section */}
                <div className="mb-16">
                    <div className="border-l-4 border-[#00e9fa] pl-8">
                        <h2 className="text-2xl md:text-3xl text-gray-300 font-light leading-relaxed">
                            {t('vision_hero')}
                        </h2>
                    </div>
                </div>

                {/* Detailed Content */}
                <div className="space-y-12">
                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
                            {t('vision_horizon_title')}
                        </h3>
                        <div className="prose prose-invert max-w-none">
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                {t('vision_horizon_p1')}
                            </p>
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                {t('vision_horizon_p2')}
                            </p>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
                            {t('vision_pillars_title')}
                        </h3>
                        <div className="space-y-6">
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">{t('vision_excellence_title')}</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    {t('vision_excellence_desc')}
                                </p>
                            </div>
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">{t('vision_cultural_title')}</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    {t('vision_cultural_desc')}
                                </p>
                            </div>
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">{t('vision_innovation_title')}</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    {t('vision_innovation_desc')}
                                </p>
                            </div>
                            <div className="bg-white/5 border-l-4 border-[#00e9fa] p-6">
                                <h4 className="font-bold text-xl mb-3 text-white">{t('vision_ecosystem_title')}</h4>
                                <p className="text-gray-400 leading-relaxed">
                                    {t('vision_ecosystem_desc')}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 className="text-2xl font-bold text-[#00e9fa] mb-6 font-mono uppercase tracking-wider">
                            {t('vision_commitment_title')}
                        </h3>
                        <div className="bg-gradient-to-br from-[#00e9fa]/10 via-transparent to-[#00e9fa]/5 border border-[#00e9fa]/20 p-8 rounded-sm">
                            <p className="text-gray-300 text-lg leading-relaxed mb-4">
                                {t('vision_commitment_intro')}
                            </p>
                            <ul className="space-y-3 text-gray-400">
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>{t('vision_commitment_1')}</span>
                                </li>
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>{t('vision_commitment_2')}</span>
                                </li>
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>{t('vision_commitment_3')}</span>
                                </li>
                                <li className="flex items-start gap-3">
                                    <span className="text-[#00e9fa] mt-1">▸</span>
                                    <span>{t('vision_commitment_4')}</span>
                                </li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </Layout>
    );
}
