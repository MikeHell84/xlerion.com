import React from 'react';
import { User, Linkedin, Instagram, Briefcase, Award, Palette } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function FounderPage() {
    const { t } = useLanguage();

    return (
        <Layout>
            {/* Banner con foto */}
            <div className="relative h-[60vh] overflow-hidden">
                <div className="absolute inset-0 bg-gradient-to-b from-black via-black/80 to-black" />
                <img
                    src="/images/MikeProfile.jpg"
                    alt="Miguel Eduardo Rodríguez Martínez"
                    className="absolute inset-0 w-full h-full object-cover opacity-30"
                />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8 max-w-4xl">
                        <User className="text-[#00e9fa] mx-auto mb-6" size={80} />
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl mb-4">
                            {t('founder_page_title')}
                        </h1>
                        <p className="text-xl text-gray-300 font-mono">Miguel Eduardo Rodríguez Martínez</p>
                    </div>
                </div>
            </div>

            {/* Contenido principal */}
            <div className="pt-20 pb-20 px-8 max-w-6xl mx-auto">
                {/* Introducción */}
                <div className="mb-16">
                    <p className="text-2xl text-gray-300 leading-relaxed mb-8">
                        {t('founder_page_intro')}
                    </p>
                </div>

                {/* Biografía detallada */}
                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6 flex items-center gap-3">
                        <Briefcase className="text-[#00e9fa]" size={32} />
                        {t('founder_page_experience_title')}
                    </h2>
                    <div className="space-y-4 text-gray-300 leading-relaxed">
                        <p>{t('founder_page_bio_p1')}</p>
                        <p>{t('founder_page_bio_p2')}</p>
                        <p>{t('founder_page_bio_p3')}</p>
                    </div>
                </section>

                {/* Proyectos destacados */}
                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">
                        {t('founder_page_projects_title')}
                    </h2>
                    <div className="grid md:grid-cols-2 gap-6">
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">
                                {t('founder_page_project_1_title')}
                            </h4>
                            <p className="text-gray-400 text-sm leading-relaxed">
                                {t('founder_page_project_1_desc')}
                            </p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">
                                {t('founder_page_project_2_title')}
                            </h4>
                            <p className="text-gray-400 text-sm leading-relaxed">
                                {t('founder_page_project_2_desc')}
                            </p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">
                                {t('founder_page_project_3_title')}
                            </h4>
                            <p className="text-gray-400 text-sm leading-relaxed">
                                {t('founder_page_project_3_desc')}
                            </p>
                        </div>
                        <div className="p-6 border border-white/10 bg-white/5 rounded-sm">
                            <h4 className="text-[#00e9fa] font-mono text-sm uppercase tracking-[0.2em] mb-3">
                                {t('founder_page_project_4_title')}
                            </h4>
                            <p className="text-gray-400 text-sm leading-relaxed">
                                {t('founder_page_project_4_desc')}
                            </p>
                        </div>
                    </div>
                </section>

                {/* Formación y certificaciones */}
                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6 flex items-center gap-3">
                        <Award className="text-[#00e9fa]" size={32} />
                        {t('founder_page_education_title')}
                    </h2>
                    <div className="space-y-4 text-gray-300 leading-relaxed">
                        <p>{t('founder_page_education_desc')}</p>
                    </div>
                </section>

                {/* Filosofía */}
                <section className="mb-16">
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">
                        {t('founder_page_philosophy_title')}
                    </h2>
                    <div className="bg-gradient-to-r from-[#00e9fa]/10 to-transparent border-l-4 border-[#00e9fa] p-8">
                        <p className="text-gray-300 text-lg leading-relaxed">
                            {t('founder_page_philosophy_desc')}
                        </p>
                    </div>
                </section>

                {/* Enlaces sociales */}
                <section>
                    <h2 className="text-3xl font-black italic uppercase text-white mb-6">
                        {t('founder_page_connect_title')}
                    </h2>
                    <div className="flex flex-wrap gap-4">
                        <a
                            href="https://linkedin.com/in/mikerodriguez84"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center gap-3 px-6 py-3 bg-white/5 border border-white/10 rounded-sm hover:border-[#00e9fa] transition-colors"
                        >
                            <Linkedin className="text-[#00e9fa]" size={24} />
                            <span className="text-white font-mono text-sm">LinkedIn</span>
                        </a>
                        <a
                            href="https://artstation.com/xlerion"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center gap-3 px-6 py-3 bg-white/5 border border-white/10 rounded-sm hover:border-[#00e9fa] transition-colors"
                        >
                            <Palette className="text-[#00e9fa]" size={24} />
                            <span className="text-white font-mono text-sm">ArtStation</span>
                        </a>
                        <a
                            href="https://instagram.com/mike_hellawaits"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center gap-3 px-6 py-3 bg-white/5 border border-white/10 rounded-sm hover:border-[#00e9fa] transition-colors"
                        >
                            <Instagram className="text-[#00e9fa]" size={24} />
                            <span className="text-white font-mono text-sm">Instagram</span>
                        </a>
                    </div>
                </section>
            </div>
        </Layout>
    );
}
