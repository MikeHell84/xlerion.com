import React from 'react';
import { Cpu, MonitorSmartphone, RefreshCcw, Link as LinkIcon } from 'lucide-react';
import Layout from '../components/Layout';
import { useLanguage } from '../context/LanguageContext';

export default function Integracion3DPage() {
    const { t } = useLanguage();
    return (
        <Layout>
            {/* Banner Parallax */}
            <div className="relative h-[40vh] overflow-hidden">
                <img
                    src="/images/proyectos-parallax.jpg"
                    alt="Integración con Motores 3D Banner"
                    className="absolute inset-0 w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="text-center px-8">
                        <Cpu className="text-[#00e9fa] mx-auto mb-4" size={64} />
                        <p className="text-[#00e9fa] font-mono text-xs tracking-[0.4em] uppercase mb-2">{t('integration3d_page_subtitle')}</p>
                        <h1 className="text-5xl md:text-7xl font-black italic uppercase tracking-tighter text-white drop-shadow-2xl">{t('integration3d_page_title')}</h1>
                    </div>
                </div>
            </div>
            <div className="pt-20 pb-24 px-8 max-w-6xl mx-auto">
                <header className="mb-16">
                    <p className="text-gray-300 max-w-3xl text-lg leading-relaxed">{t('integration3d_page_desc')}</p>
                </header>

                <div className="grid md:grid-cols-3 gap-10 mb-16">
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><MonitorSmartphone size={18} className="text-[#00e9fa]" /> {t('integration3d_pipelines')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('integration3d_pipelines_1')}</li>
                            <li>{t('integration3d_pipelines_2')}</li>
                            <li>{t('integration3d_pipelines_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><RefreshCcw size={18} className="text-[#00e9fa]" /> {t('integration3d_optimization')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('integration3d_optimization_1')}</li>
                            <li>{t('integration3d_optimization_2')}</li>
                            <li>{t('integration3d_optimization_3')}</li>
                        </ul>
                    </div>
                    <div className="p-8 border border-white/10 bg-white/5 rounded-sm">
                        <h3 className="text-white font-mono text-sm uppercase tracking-[0.2em] mb-3 flex items-center gap-2"><LinkIcon size={18} className="text-[#00e9fa]" /> {t('integration3d_interop')}</h3>
                        <ul className="space-y-2 text-gray-400 text-sm leading-relaxed">
                            <li>{t('integration3d_interop_1')}</li>
                            <li>{t('integration3d_interop_2')}</li>
                            <li>{t('integration3d_interop_3')}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
