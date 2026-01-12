import React from 'react';
import { Terminal, Activity, Zap, Shield } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function XlerionToolkitProjectPage() {
    const { t } = useLanguage();

    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/soluciones-parallax.jpg"
                    alt="Toolkit Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Terminal className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('xleriontoolkit_banner_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('xleriontoolkit_banner_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('xleriontoolkit_header')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Activity size={18} className="text-[#00e9fa]" /> {t('xleriontoolkit_diagnostic_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('xleriontoolkit_diagnostic_1')}</li>
                            <li>{t('xleriontoolkit_diagnostic_2')}</li>
                            <li>{t('xleriontoolkit_diagnostic_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Zap size={18} className="text-[#00e9fa]" /> {t('xleriontoolkit_performance_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('xleriontoolkit_performance_1')}</li>
                            <li>{t('xleriontoolkit_performance_2')}</li>
                            <li>{t('xleriontoolkit_performance_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Shield size={18} className="text-[#00e9fa]" /> {t('xleriontoolkit_security_title')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('xleriontoolkit_security_1')}</li>
                            <li>{t('xleriontoolkit_security_2')}</li>
                            <li>{t('xleriontoolkit_security_3')}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
