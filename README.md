# xlerion-site

Proyecto demo: sitio corporativo con React, Vite, Tailwind y Three.js.

Requisitos
- Node.js 18+ (o LTS recomendado)
- npm

Instalación (desde PowerShell):
```powershell
cd X:\Programacion\XlerionWeb\xlerion-site
npm install
```

Comandos útiles
- Desarrollo: `npm run dev` (arranca Vite en http://localhost:5173)
- Build producción: `npm run build`
- Previsualizar build: `npm run preview`

Notas
- Tailwind está configurado en `tailwind.config.cjs` y PostCSS en `postcss.config.cjs`.
- El archivo principal de la app es `src/App.jsx`. El componente `ThreeScene` hace cleanup del renderer y geometrías para evitar memory leaks.
- Las fuentes Inter y Roboto Mono se cargan desde Google Fonts; puedes mover la importación a CSS si lo prefieres.

Próximos pasos recomendados
- Añadir `.env` o variables si necesitas configurar la URL de la API.
- Añadir ESLint/Prettier y hooks si quieres estandarizar el formato.
# React + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Babel](https://babeljs.io/) (or [oxc](https://oxc.rs) when used in [rolldown-vite](https://vite.dev/guide/rolldown)) for Fast Refresh
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/) for Fast Refresh

## React Compiler

The React Compiler is not enabled on this template because of its impact on dev & build performances. To add it, see [this documentation](https://react.dev/learn/react-compiler/installation).

## Expanding the ESLint configuration

If you are developing a production application, we recommend using TypeScript with type-aware lint rules enabled. Check out the [TS template](https://github.com/vitejs/vite/tree/main/packages/create-vite/template-react-ts) for information on how to integrate TypeScript and [`typescript-eslint`](https://typescript-eslint.io) in your project.
