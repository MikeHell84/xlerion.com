import React from 'react';
import { Shield, CheckCircle2, Eye, Lock } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function PrivacyPage() {
    const { t } = useLanguage();

    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/legal-privacidad-parallax.jpg"
                    alt="Privacidad Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Shield className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('privacy_banner_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('privacy_banner_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('privacy_header')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Eye size={18} className="text-[#00e9fa]" /> {t('privacy_principles_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('privacy_principles_1')}</li>
                            <li>{t('privacy_principles_2')}</li>
                            <li>{t('privacy_principles_3')}</li>
                            <li>{t('privacy_principles_4')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Lock size={18} className="text-[#00e9fa]" /> {t('privacy_safeguards_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('privacy_safeguards_1')}</li>
                            <li>{t('privacy_safeguards_2')}</li>
                            <li>{t('privacy_safeguards_3')}</li>
                            <li>{t('privacy_safeguards_4')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><CheckCircle2 size={18} className="text-[#00e9fa]" /> {t('privacy_rights_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('privacy_rights_1')}</li>
                            <li>{t('privacy_rights_2')}</li>
                            <li>{t('privacy_rights_3')}</li>
                            <li>{t('privacy_rights_4')}</li>
                        </ul>
                    </div>
                </div>

                <div className="p-8 border border-[#00e9fa]/20 bg-[#00e9fa]/5 rounded-sm">
                    <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] mb-3">{t('privacy_contact_title')}</h4>
                    <p className="text-gray-300 text-sm leading-relaxed">{t('privacy_contact_desc')}</p>
                </div>
            </div>
        </Layout>
    );
}
