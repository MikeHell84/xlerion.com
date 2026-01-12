import React from 'react';
import { Database, FileBadge2, Copyright, BadgeCheck } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function LicensesPage() {
    const { t } = useLanguage();

    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/servicios-productos-parallax.jpg"
                    alt="Licencias Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Database className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('licenses_banner_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('licenses_banner_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('licenses_header')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><FileBadge2 size={18} className="text-[#00e9fa]" /> {t('licenses_code_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('licenses_code_1')}</li>
                            <li>{t('licenses_code_2')}</li>
                            <li>{t('licenses_code_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><BadgeCheck size={18} className="text-[#00e9fa]" /> {t('licenses_media_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('licenses_media_1')}</li>
                            <li>{t('licenses_media_2')}</li>
                            <li>{t('licenses_media_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Copyright size={18} className="text-[#00e9fa]" /> {t('licenses_documentation_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('licenses_documentation_1')}</li>
                            <li>{t('licenses_documentation_2')}</li>
                            <li>{t('licenses_documentation_3')}</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5 rounded-sm">
                    <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] mb-3">{t('licenses_contact_title')}</h4>
                    <p className="text-gray-300 text-sm leading-relaxed">{t('licenses_contact_desc')}</p>
                </div>
            </div>
        </Layout>
    );
}
