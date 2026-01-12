import React from 'react';
import { Activity, Terminal, Gauge, Bell } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function DiagLogPerfPage() {
    const { t } = useLanguage();
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/documentacion-recursos-parallax.jpg"
                    alt="Diagnóstico, Logging y Performance Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Activity className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('diag_page_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('diag_page_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('diag_page_desc')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Terminal size={18} className="text-[#00e9fa]" /> {t('diag_logging')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('diag_logging_1')}</li>
                            <li>{t('diag_logging_2')}</li>
                            <li>{t('diag_logging_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Gauge size={18} className="text-[#00e9fa]" /> {t('diag_performance')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('diag_performance_1')}</li>
                            <li>{t('diag_performance_2')}</li>
                            <li>{t('diag_performance_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Bell size={18} className="text-[#00e9fa]" /> {t('diag_alerts')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('diag_alerts_1')}</li>
                            <li>{t('diag_alerts_2')}</li>
                            <li>{t('diag_alerts_3')}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
