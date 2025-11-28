SCSS scaffold for xlerion_cmr

Quick start:

1. Install dev deps (from project root):

   npm install --save-dev sass postcss postcss-cli autoprefixer cssnano stylelint

2. Compile SCSS to CSS (example):

   npx sass src/scss/main.scss public/css/main.css --no-source-map --style=expanded
   npx postcss public/css/main.css --use autoprefixer cssnano -o public/css/main.min.css

3. Add build scripts to package.json (example):

  "scripts": {
    "build:css": "sass src/scss/main.scss xlerion_cmr/public/css/main.css --no-source-map && postcss xlerion_cmr/public/css/main.css --use autoprefixer cssnano -o xlerion_cmr/public/css/main.min.css",
    "lint:css": "stylelint \"src/scss/**/*.scss\" --fix"
  }

Notes:
- This is an incremental migration: start by moving variables and themes, then components.
- Keep `public/styles.css` as fallback until you verify compiled CSS matches visuals.
