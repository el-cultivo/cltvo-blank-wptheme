# Estructura del tema

**Estado:** Revisado

## Objetivo

Este documento funciona como mapa físico del blank theme revisado. Describe dónde vive cada responsabilidad sin duplicar la documentación técnica de cada sistema.

---

## Estructura general

```text
cltvo-blank-wptheme/
├── acf-json/
├── app/
│   ├── Http/Ajax/
│   ├── Mail/
│   ├── Metaboxes/
│   ├── Providers/
│   └── Taxonomies/
├── bootstrap/
├── config/
├── docs/
├── es6/
│   └── admin/
├── framework/
│   └── src/Illuminate/
├── images/
│   └── favicon/
├── inc/
├── includes/
│   ├── custom/
│   ├── plugins/
│   └── tgm_plugin_activation/
├── js/
├── languages/
├── mail/
├── sass/
│   └── mazorca/
├── views/
│   └── general/
├── 404.php
├── archive.php
├── footer.php
├── functions.php
├── header.php
├── index.php
├── page.php
├── page-home.php
├── page-splash.php
├── page-atomos.php
├── single.php
├── template-page.php
├── composer.json
├── package.json
├── webpack.mix.js
└── style.css
```

> El árbol muestra las áreas arquitectónicamente relevantes del tema; no pretende listar cada asset, dependencia o archivo generado.

---

## PHP / arquitectura

### `bootstrap/`

Contiene `autoload.php` y `app.php`: carga Composer/TGMPA y crea `Illuminate\Foundation\Application`.

Ver [Bootstrap](./core/bootstrap.md).

### `framework/`

Contiene la infraestructura PHP reutilizable del blank bajo `framework/src/Illuminate/`: Application, ServiceProvider, AJAX, Controllers, CPT, Taxonomy, Metabox, wrappers, Mail y helpers.

Composer registra:

```text
Illuminate\ → framework/src/Illuminate/
```

Ver [Framework](./core/framework.md).

### `config/`

Contiene `app.php`, `conf_plugins.php`, `options_pages.php` y `required_plugins.php`.

Ver [Configuración](./core/config.md).

### `app/`

Contiene las implementaciones PHP del proyecto: Providers, AJAX, Mailables, Metaboxes, Taxonomies, helpers y clases propias.

Composer registra:

```text
App\ → app/
```

El blank actual mezcla implementaciones activas, ejemplos y código histórico.

Ver [App](./core/app.md).

---

## ACF

### `acf-json/`

Contiene los Field Groups versionados mediante ACF Local JSON.

Ver [Integración ACF](./integrations/acf.md).

---

## Presentación

### Templates raíz

`index.php`, `page.php`, `single.php`, `archive.php`, `404.php`, `page-*.php` y `template-page.php` participan en WordPress Template Hierarchy. Algunos son funcionales y otros son placeholders o código histórico.

Ver [Templates y Views](./core/templates-and-views.md).

### `views/`

Contiene partials PHP reutilizables. En el blank auditado:

```text
views/general/header.php
views/general/footer.php
```

### `mail/`

Contiene vistas utilizadas por Mailables:

```text
mail/contact.php
mail/layout.php
```

No implementa el transporte.

Ver [Mailgun y sistema de correo](./integrations/mailgun.md).

---

## Includes auxiliares

### `includes/`

Contiene:

```text
custom/              → CustomLocation de ACF
plugins/             → paquetes históricos de ACF PRO / WPML
tgm_plugin_activation/ → librería third-party TGMPA
```

`CustomLocation.php` se documenta dentro de ACF. Los paquetes third-party no constituyen subsistemas propios del core.

### `inc/`

Contiene fragments heredados:

```text
analytics.php
favicon.php
```

`favicon.php` sigue incluido desde `header.php`. `analytics.php` también se incluye fuera de local, aunque en el ZIP auditado su código está comentado.

Esta carpeta se considera legacy pendiente de cleanup, no un subsistema de core.

---

## Frontend

### `es6/`

Contiene `constants.js`, `micorriza.js`, `micorriza-admin.js` y `admin/`.

Actualmente sólo `micorriza.js` forma parte del build principal. El toolkit de admin está deshabilitado.

Ver [JavaScript](./frontend/javascript.md).

### `sass/`

Contiene `mazorca.scss` y `mazorca/`. El entry se compila a `style.css`.

Ver [Mazorca](./frontend/mazorca.md).

### `js/`

Contiene el output compilado:

```text
functions.js
functions.js.map
```

Su fuente es `es6/micorriza.js`.

### `style.css`

Es simultáneamente el archivo de metadata requerido por WordPress y el CSS compilado del theme.

### Build

La configuración vive en `package.json`, `webpack.mix.js` y `.babelrc`.

Ver [Build](./frontend/build.md).

---

## Assets

### `images/`

Contiene assets estáticos pertenecientes al theme. Las imágenes editoriales administrables deben vivir en la Media Library de WordPress.

### `languages/`

Contiene recursos de traducción del theme. El textdomain se carga desde `ActionsServiceProvider`.

---

## Documentación

La documentación se organiza por responsabilidad:

```text
docs/
├── README.md
├── architecture.md
├── theme-structure.md
├── CHANGELOG.md
├── core/
│   ├── app.md
│   ├── bootstrap.md
│   ├── config.md
│   ├── framework.md
│   ├── helpers.md
│   ├── providers.md
│   ├── templates-and-views.md
│   └── providers/
├── frontend/
├── integrations/
└── features/
```

No es necesario replicar una carpeta documental por cada carpeta física del código. El criterio es documentar responsabilidades arquitectónicas.

---

## Dependencias generadas

`vendor/` y `node_modules/` se generan a partir de Composer y npm. No son lugares para implementar código propio del proyecto.

---

## Regla práctica de navegación

```text
¿cómo arranca?                    → bootstrap/
¿infraestructura PHP?             → framework/
¿qué está configurado?            → config/
¿qué implementa el proyecto?      → app/
¿cómo renderiza WordPress?        → templates + views/
¿cómo se construye frontend?      → es6/ + sass/ + webpack.mix.js
¿integraciones externas?          → integrations/
```

La existencia de archivos de ejemplo o legacy debe distinguirse de la infraestructura que participa realmente en runtime.
