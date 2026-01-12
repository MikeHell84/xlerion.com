import React from 'react';
import { Zap, Gauge, Cpu, Timer } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function PerformancePage() {
    const { t } = useLanguage();
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/filosofia-parallax.jpg"
                    alt="Performance Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Zap className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <h1 className="text-6xl md:text-8xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('performance_page_title')}</h1>
                    </div>
                </div>
            </div>
            {/* Content */}
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <div className="mb-16">
                    <div className="border-l-4 border-[#00e9fa] pl-8">
                        <h2 className="text-2xl md:text-3xl text-gray-300 font-light leading-relaxed">
                            {t('performance_page_desc')}
                        </h2>
                    </div>
                </div>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Gauge size={18} className="text-[#00e9fa]" /> {t('performance_metrics')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('performance_metrics_1')}</li>
                            <li>{t('performance_metrics_2')}</li>
                            <li>{t('performance_metrics_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Cpu size={18} className="text-[#00e9fa]" /> {t('performance_optimization')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('performance_optimization_1')}</li>
                            <li>{t('performance_optimization_2')}</li>
                            <li>{t('performance_optimization_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><Timer size={18} className="text-[#00e9fa]" /> {t('performance_automation')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('performance_automation_1')}</li>
                            <li>{t('performance_automation_2')}</li>
                            <li>{t('performance_automation_3')}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
