import React from 'react';
import { Cpu, BookOpen, GitBranch, Sparkles } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function TotalDarknessProjectPage() {
    const { t } = useLanguage();

    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/blog-bitacora-parallax.jpg"
                    alt="Total Darkness Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Cpu className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('totaldarkness_banner_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('totaldarkness_banner_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('totaldarkness_header')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <a
                        className="p-8 border border-white/10 bg-white/5 rounded-sm block hover:border-[#00e9fa]/60 transition-colors"
                        href="/total-darkness/historia.html"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BookOpen size={18} className="text-[#00e9fa]" /> {t('totaldarkness_story_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('totaldarkness_story_1')}</li>
                            <li>{t('totaldarkness_story_2')}</li>
                            <li>{t('totaldarkness_story_3')}</li>
                        </ul>
                    </a>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><GitBranch size={18} className="text-[#00e9fa]" /> {t('totaldarkness_tech_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('totaldarkness_tech_1')}</li>
                            <li>{t('totaldarkness_tech_2')}</li>
                            <li>{t('totaldarkness_tech_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Sparkles size={18} className="text-[#00e9fa]" /> {t('totaldarkness_experience_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('totaldarkness_experience_1')}</li>
                            <li>{t('totaldarkness_experience_2')}</li>
                            <li>{t('totaldarkness_experience_3')}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
