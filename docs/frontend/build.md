# Build de Frontend
**Estado:** En revisión

## Objetivo

El blank theme compila JavaScript y Sass mediante:

```text
Laravel Mix 5
Webpack
Babel
Sass
```

La configuración principal se encuentra en:

```text
package.json
webpack.mix.js
.babelrc
```

---

## Flujo actual

```text
es6/micorriza.js
    ↓
Laravel Mix / Webpack / Babel
    ↓
js/functions.js

sass/mazorca.scss
    ↓
Laravel Mix / Sass
    ↓
style.css
```

Después WordPress carga ambos outputs desde:

```text
ScriptsServiceProvider
StylesServiceProvider
```

El flujo completo es:

```text
npm scripts
    ↓
webpack.mix.js
    ↓
ES6 + Sass
    ↓
js/functions.js + style.css
    ↓
Providers
    ↓
wp_enqueue_script() / wp_enqueue_style()
```

---

# `package.json`

El `package.json` raíz declara tres comandos:

```bash
npm run dev
npm run watch
npm run production
```

Los tres ejecutan Webpack utilizando la configuración de Laravel Mix.

## Dependencias runtime

```text
@babel/polyfill
jquery
mazorca-core
```

## Dependencias de desarrollo

```text
@babel/cli
@babel/core
@babel/preset-env
browser-sync
browser-sync-webpack-plugin
laravel-mix
sass
sass-loader
vue-template-compiler
```

`vue-template-compiler` está instalado, aunque el blank revisado no contiene una configuración ni componentes Vue.

Debe considerarse una dependencia pendiente de confirmar antes de conservarla como parte obligatoria del boilerplate.

---

# Babel

`.babelrc` contiene:

```json
{
  "presets": ["@babel/preset-env"]
}
```

Por lo tanto, el JavaScript fuente pasa por Babel durante el build configurado por Laravel Mix.

---

# `webpack.mix.js`

La configuración actual es:

```js
mix.sourceMaps(true, 'source-map')
    .js('es6/micorriza.js', 'js/functions.js')
    .sass('sass/mazorca.scss', './style.css')
```

También define:

```js
processCssUrls: false
```

y BrowserSync.

---

## Source maps

El build genera source maps:

```text
js/functions.js.map
style.css.map
```

Estos archivos existen en el blank revisado.

Antes de definir el estándar de entrega a clientes conviene decidir si los `.map` deben permanecer en producción o únicamente en entornos de desarrollo.

---

# BrowserSync

Actualmente:

```js
.browserSync({
    open: 'local',
    host: 'localhost',
    proxy: 'localhost',
    files: ['views/*.php', '*.php', 'js/*.js', '*.css']
});
```

## Observación

Los partials actuales viven en:

```text
views/general/*.php
```

pero el watch utiliza:

```text
views/*.php
```

Ese patrón no cubre archivos anidados como:

```text
views/general/header.php
views/general/footer.php
```

Si BrowserSync se conserva, conviene revisar el glob para que observe la estructura real del theme.

---

# Outputs cargados por WordPress

## JavaScript

`ScriptsServiceProvider` registra:

```text
js/functions.js
```

como:

```php
cltvo_functions_js
```

y le agrega:

```text
cltvo_js_vars.site_url
cltvo_js_vars.template_url
cltvo_js_vars.ajax_url
```

mediante:

```php
wp_localize_script()
```

Antes de `functions.js` también carga:

```text
jquery
slick
```

porque Slick está habilitado por default en `$cdn`.

---

## CSS

`StylesServiceProvider` registra:

```text
style.css
```

como:

```php
cltvo_style_css
```

y actualmente carga antes:

```text
slick.css
```

desde CDN.

---

# Build de admin

Actualmente NO existe un build activo para:

```text
es6/micorriza-admin.js
```

`webpack.mix.js` sólo compila:

```text
es6/micorriza.js
```

Además:

- los imports de `micorriza-admin.js` están comentados;
- el hook `admin_enqueue_scripts` está comentado;
- `adminEnqeueScripts()` contiene únicamente código comentado.

Por lo tanto:

```text
es6/admin/*
    ↓
no forma parte del build actual
    ↓
no se carga en wp-admin
```

Esto no significa que las utilidades deban eliminarse automáticamente. Deben evaluarse antes de decidir si se modernizan o se retiran del blank.

Ver:

```text
javascript.md
```

---

# Duplicación dentro de `sass/`

Existe además:

```text
sass/package.json
sass/package-lock.json
```

Este package declara nuevamente:

```text
mazorca-core
```

y describe un flujo histórico de instalación de Mazorca.

Al mismo tiempo, el `package.json` raíz ya declara:

```text
mazorca-core ^1.6.2
```

La documentación dentro de `sass/package.json` menciona incluso una instalación global de otra versión histórica.

Esto indica que existen rastros de dos estrategias de instalación.

Antes de limpiar el boilerplate debe definirse cuál es la fuente única de dependencias del frontend. En el flujo actual documentado por el README del theme se ejecuta:

```bash
npm install
```

en la raíz del tema.

---

# README del theme

El README actual indica:

```text
npm install
composer install
```

desde la carpeta raíz del theme.

Esto respalda al:

```text
package.json
```

raíz como punto de instalación documentado actualmente.

---

# Clasificación

| Elemento | Estado |
| --- | --- |
| `package.json` raíz | Activo |
| `.babelrc` | Activo |
| `webpack.mix.js` | Activo |
| `es6/micorriza.js → js/functions.js` | Activo |
| `sass/mazorca.scss → style.css` | Activo |
| BrowserSync | Configurado; revisar globs |
| Source maps | Activos |
| `sass/package.json` | Configuración histórica / duplicada a revisar |
| build de admin | Deshabilitado |
| `vue-template-compiler` | Dependencia sin uso identificado en el blank revisado |

---

# Antes de entregar código a clientes

Esta revisión no propone todavía el cleanup definitivo, pero sí identifica elementos que deben evaluarse para una versión limpia del boilerplate:

```text
sass/package.json + package-lock
vue-template-compiler
source maps de producción
BrowserSync config
build administrativo deshabilitado
dependencias CDN habilitadas por default
```

La decisión de eliminar o modernizar cada pieza debe hacerse después de revisar el uso real en proyectos vigentes.
