import { StrictMode, useState, useEffect } from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter, Routes, Route } from 'react-router-dom'
import './index.css'
import App from './App.jsx'
import VideoIntro from './components/VideoIntro.jsx'
import { LanguageProvider } from './context/LanguageContext.jsx'
import MisionPage from './pages/MisionPage.jsx'
import VisionPage from './pages/VisionPage.jsx'
import PrivacyPage from './pages/PrivacyPage.jsx'
import TermsPage from './pages/TermsPage.jsx'
import LicensesPage from './pages/LicensesPage.jsx'
import ValidationPage from './pages/ValidationPage.jsx'
import LoggingPage from './pages/LoggingPage.jsx'
import DiagnosticoPage from './pages/DiagnosticoPage.jsx'
import PerformancePage from './pages/PerformancePage.jsx'
import TurismoPage from './pages/TurismoPage.jsx'
import AlojamientoPage from './pages/AlojamientoPage.jsx'
import IngenieriaPage from './pages/IngenieriaPage.jsx'
import TotalDarknessProjectPage from './pages/TotalDarknessProjectPage.jsx'
import XlerionToolkitProjectPage from './pages/XlerionToolkitProjectPage.jsx'
import TransitoMovilidadPage from './pages/TransitoMovilidadPage.jsx'
import TecnologiasComunidadPage from './pages/TecnologiasComunidadPage.jsx'
import ToolkitsPage from './pages/ToolkitsPage.jsx'
import DiagLogPerfPage from './pages/DiagLogPerfPage.jsx'
import BrandingPage from './pages/BrandingPage.jsx'
import DocsStructPage from './pages/DocsStructPage.jsx'
import Integracion3DPage from './pages/Integracion3DPage.jsx'
import ConsultoriaPage from './pages/ConsultoriaPage.jsx'
import CapacitacionPage from './pages/CapacitacionPage.jsx'
import SoportePage from './pages/SoportePage.jsx'
import SolucionesMedidaPage from './pages/SolucionesMedidaPage.jsx'
import ManualesPage from './pages/ManualesPage.jsx'
import DiagramasFlujosPage from './pages/DiagramasFlujosPage.jsx'
import GuiasInstalacionPage from './pages/GuiasInstalacionPage.jsx'
import FounderPage from './pages/FounderPage.jsx'

function Root() {
  const [showIntro, setShowIntro] = useState(true);
  const [hasSeenIntro, setHasSeenIntro] = useState(false);

  useEffect(() => {
    // Verificar si ya vio el intro en esta sesión
    const seen = sessionStorage.getItem('xlerion_intro_seen');
    if (seen) {
      setShowIntro(false);
      setHasSeenIntro(true);
    }
  }, []);

  const handleIntroComplete = () => {
    sessionStorage.setItem('xlerion_intro_seen', 'true');
    setShowIntro(false);
    setHasSeenIntro(true);
  };

  return (
    <>
      {showIntro && <VideoIntro onComplete={handleIntroComplete} />}
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<App />} />
          <Route path="/mision" element={<MisionPage />} />
          <Route path="/vision" element={<VisionPage />} />
          <Route path="/legal/privacidad" element={<PrivacyPage />} />
          <Route path="/legal/terminos" element={<TermsPage />} />
          <Route path="/legal/licencias" element={<LicensesPage />} />
          <Route path="/toolkit/validacion" element={<ValidationPage />} />
          <Route path="/toolkit/logging" element={<LoggingPage />} />
          <Route path="/toolkit/diagnostico" element={<DiagnosticoPage />} />
          <Route path="/toolkit/performance" element={<PerformancePage />} />
          <Route path="/servicios/turismo-incluyente" element={<TurismoPage />} />
          <Route path="/servicios/alojamiento-adaptado" element={<AlojamientoPage />} />
          <Route path="/servicios/ingenieria-creativa" element={<IngenieriaPage />} />
          <Route path="/servicios/toolkits" element={<ToolkitsPage />} />
          <Route path="/servicios/diag-log-perf" element={<DiagLogPerfPage />} />
          <Route path="/servicios/branding" element={<BrandingPage />} />
          <Route path="/servicios/documentacion" element={<DocsStructPage />} />
          <Route path="/servicios/integracion-3d" element={<Integracion3DPage />} />
          <Route path="/servicios/consultoria-modular" element={<ConsultoriaPage />} />
          <Route path="/servicios/capacitacion" element={<CapacitacionPage />} />
          <Route path="/servicios/soporte-actualizacion" element={<SoportePage />} />
          <Route path="/servicios/soluciones-medida" element={<SolucionesMedidaPage />} />
          <Route path="/proyectos/total-darkness" element={<TotalDarknessProjectPage />} />
          <Route path="/proyectos/toolkit" element={<XlerionToolkitProjectPage />} />
          <Route path="/proyectos/transito-movilidad" element={<TransitoMovilidadPage />} />
          <Route path="/proyectos/tecnologias-comunidad" element={<TecnologiasComunidadPage />} />
          <Route path="/documentacion/manuales" element={<ManualesPage />} />
          <Route path="/documentacion/diagramas-flujos" element={<DiagramasFlujosPage />} />
          <Route path="/documentacion/guias-instalacion" element={<GuiasInstalacionPage />} />
          <Route path="/fundador" element={<FounderPage />} />
        </Routes>
      </BrowserRouter>
    </>
  );
}

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <LanguageProvider>
      <Root />
    </LanguageProvider>
  </StrictMode>,
)
