import React from 'react';
import { MessageCircle } from 'lucide-react';
import { useLanguage } from '../context/LanguageContext';

export default function Footer({ navTo }) {
    const { t } = useLanguage();
    const go = (id, anim) => {
        if (navTo) navTo(id, anim);
    };

    return (
        <footer className="bg-black pt-32 pb-16 border-t border-white/10">
            <div className="max-w-7xl mx-auto px-8">
                <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-16 mb-24">
                    <div className="space-y-6">
                        <div className="flex items-center gap-4">
                            <img src="/LogoX.svg" alt="Xlerion Logo" className="h-12 object-contain" />
                        </div>
                        <p className="text-[11px] text-gray-500 font-mono uppercase leading-relaxed tracking-wider">
                            {t('footer_tagline')}
                        </p>
                    </div>
                    <div className="space-y-6">
                        <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] font-bold">{t('footer_contact')}</h4>
                        <a href="https://wa.me/573208605600" target="_blank" rel="noopener noreferrer" className="flex items-center gap-3 text-[12px] text-white font-mono uppercase tracking-widest hover:text-[#00e9fa] transition-colors">
                            <MessageCircle size={16} className="text-[#00e9fa]" /> +57 3208605600
                        </a>
                        <div className="flex flex-col gap-2 text-[10px] text-gray-500 font-mono uppercase tracking-widest">
                            {[
                                'contactus@xlerion.com',
                                'support@xlerion.com',
                                'sales@xlerion.com',
                                'admin@xlerion.com',
                                'branding@xlerion.com',
                                'toolkit@xlerion.com',
                                'neuro@xlerion.com',
                                'mike@xlerion.com',
                                'totaldarkness@xlerion.com'
                            ].map((mail) => (
                                <a
                                    key={mail}
                                    href={`mailto:${mail}`}
                                    className="text-gray-400 no-underline hover:text-[#00e9fa] transition-colors"
                                >
                                    {mail}
                                </a>
                            ))}
                        </div>
                    </div>
                    <div className="space-y-6">
                        <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] font-bold">{t('footer_access')}</h4>
                        <ul className="space-y-3 text-[10px] font-mono text-gray-500 uppercase tracking-widest">
                            <li><button onClick={() => go('identidad', 'identidad')} className="hover:text-[#00e9fa] transition-colors">{t('footer_access_identity')}</button></li>
                            <li><button onClick={() => go('toolkit', 'toolkit')} className="hover:text-[#00e9fa] transition-colors">{t('footer_access_toolkit')}</button></li>
                            <li><button onClick={() => go('servicios', 'servicios')} className="hover:text-[#00e9fa] transition-colors">{t('footer_access_services')}</button></li>
                            <li><button onClick={() => go('proyectos', 'vision')} className="hover:text-[#00e9fa] transition-colors">{t('footer_access_projects')}</button></li>
                            <li><button onClick={() => go('legal', 'mision')} className="hover:text-[#00e9fa] transition-colors">{t('footer_access_legal')}</button></li>
                        </ul>
                    </div>
                    <div className="space-y-6">
                        <h4 className="text-[#00e9fa] font-mono text-xs uppercase tracking-[0.3em] font-bold">{t('footer_networks')}</h4>
                        <div className="grid grid-cols-2 gap-3 text-[10px] font-mono uppercase tracking-widest text-gray-500">
                            <a href="https://www.linkedin.com/company/xlerion" className="hover:text-[#00e9fa] transition-colors">LinkedIn</a>
                            <a href="https://www.instagram.com/ultimatexlerion/" className="hover:text-[#00e9fa] transition-colors">Instagram</a>
                            <a href="https://www.facebook.com/xlerionultimate" className="hover:text-[#00e9fa] transition-colors">Facebook</a>
                            <a href="https://www.behance.net/xlerionultimate" className="hover:text-[#00e9fa] transition-colors">Behance</a>
                            <a href="https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9?redirect_reason#/overview" className="hover:text-[#00e9fa] transition-colors">Indiegogo</a>
                            <a href="https://www.kickstarter.com/profile/xlerionstudios" className="hover:text-[#00e9fa] transition-colors">Kickstarter</a>
                            <a href="https://www.patreon.com/xlerion" className="hover:text-[#00e9fa] transition-colors">Patreon</a>
                        </div>
                    </div>
                </div>
                <div className="pt-16 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-8 text-[9px] font-mono text-gray-700 tracking-[0.3em] uppercase text-center md:text-left">
                    <div>
                        {t('footer_rights')}<br />
                        <span className="text-[#00e9fa]/60">{t('footer_claim')}</span>
                    </div>
                    <div className="flex gap-12 opacity-40">
                        <div>LAT: 05°04′00″N</div>
                        <div>LONG: 74°23′00″W</div>
                    </div>
                </div>
            </div>
        </footer>
    );
}
