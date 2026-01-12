import React, { useState, useEffect, useRef } from 'react';

export default function VideoIntro({ onComplete }) {
    const [isPlaying, setIsPlaying] = useState(true);
    const [canSkip, setCanSkip] = useState(false);
    const videoRef = useRef(null);

    useEffect(() => {
        // Permitir saltar después de 2 segundos
        const skipTimer = setTimeout(() => {
            setCanSkip(true);
        }, 2000);

        return () => clearTimeout(skipTimer);
    }, []);

    const handleVideoEnd = () => {
        setIsPlaying(false);
        onComplete();
    };

    const handleSkip = () => {
        if (canSkip) {
            setIsPlaying(false);
            onComplete();
        }
    };

    const handleKeyPress = (e) => {
        if (canSkip && (e.key === 'Enter' || e.key === ' ' || e.key === 'Escape')) {
            handleSkip();
        }
    };

    useEffect(() => {
        window.addEventListener('keydown', handleKeyPress);
        return () => window.removeEventListener('keydown', handleKeyPress);
    }, [canSkip]);

    if (!isPlaying) return null;

    return (
        <div className="fixed inset-0 z-[9999] bg-black flex items-center justify-center">
            <video
                ref={videoRef}
                className="w-full h-full object-contain"
                autoPlay
                muted
                playsInline
                onEnded={handleVideoEnd}
            >
                <source src="/videos/xlerionIntro.mp4" type="video/mp4" />
            </video>

            {/* Botón Skip - Optimizado para móviles */}
            {canSkip && (
                <button
                    onClick={handleSkip}
                    className="absolute bottom-4 right-4 md:bottom-8 md:right-8 px-4 py-2 md:px-6 md:py-3 bg-[#00e9fa]/20 hover:bg-[#00e9fa]/40 active:bg-[#00e9fa]/60 border border-[#00e9fa] text-[#00e9fa] font-mono text-xs md:text-sm uppercase tracking-wider transition-all duration-300 rounded-sm backdrop-blur-sm touch-manipulation"
                    aria-label="Saltar introducción"
                >
                    <span className="hidden sm:inline">Saltar Intro</span>
                    <span className="sm:hidden">Saltar</span>
                </button>
            )}

            {/* Indicador de progreso */}
            <div className="absolute bottom-0 left-0 right-0 h-1 bg-white/10">
                <div
                    className="h-full bg-[#00e9fa] transition-all duration-100"
                    style={{
                        width: videoRef.current
                            ? `${(videoRef.current.currentTime / videoRef.current.duration) * 100}%`
                            : '0%'
                    }}
                />
            </div>
        </div>
    );
}
