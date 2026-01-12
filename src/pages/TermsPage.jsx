import React from 'react';
import { Info, ShieldCheck, Scale, FileText } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function TermsPage() {
    const { t } = useLanguage();

    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/noticias-eventos-parallax.jpg"
                    alt="Términos Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Info className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('terms_banner_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('terms_banner_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('terms_header')}</p>
                </header>

                <div className="grid md:grid-cols-2 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><ShieldCheck size={18} className="text-[#00e9fa]" /> {t('terms_responsibilities_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('terms_responsibilities_1')}</li>
                            <li>{t('terms_responsibilities_2')}</li>
                            <li>{t('terms_responsibilities_3')}</li>
                            <li>{t('terms_responsibilities_4')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Scale size={18} className="text-[#00e9fa]" /> {t('terms_limitations_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('terms_limitations_1')}</li>
                            <li>{t('terms_limitations_2')}</li>
                            <li>{t('terms_limitations_3')}</li>
                            <li>{t('terms_limitations_4')}</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5 rounded-sm">
                    <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] mb-3 flex items-center gap-2"><FileText size={16} /> {t('terms_updates_title')}</h4>
                    <p className="text-gray-300 text-sm leading-relaxed">{t('terms_updates_desc')}</p>
                </div>
            </div>
        </Layout>
    );
}
