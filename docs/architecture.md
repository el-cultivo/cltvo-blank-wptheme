# Arquitectura del tema

## Descripción general

`cltvo_blank_wptheme` es un boilerplate de WordPress que sirve como punto de partida para todos los proyectos de la agencia. No es un tema hijo ni una dependencia: se descarga, se renombra y a partir de él se desarrolla el proyecto completo desde cero.

Cada proyecto vive en su propio repositorio independiente. El boilerplate no se actualiza en proyectos existentes; las mejoras se incorporan solo en proyectos nuevos.


## Estructura de carpetas

> ⚠️ Pendiente: mapear la estructura real de carpetas del tema.

---

## Compilación

La herramienta de compilación depende de la versión del boilerplate con la que se inició el proyecto:

| Versión | Herramienta | Comando | Requisito |
| :--- | :--- | :--- | :--- |
| 3.0 | Gulp 3.* | `gulp watch` | Node < 9 |
| 3.2+ | laravel-mix | `npm run watch` | Cualquier versión de Node |

Para identificar qué versión usa un proyecto, revisar si existe un `gulpfile.js` (v3.0) o un `webpack.mix.js` (v3.2+) en la raíz del tema.

---

## Flags del tema

Todas las flags se declaran en `functions.php` mediante `add_theme_support()`. Controlan el comportamiento global del tema y deben revisarse al iniciar cualquier proyecto.

| Flag | Default | Descripción |
| :--- | :--- | :--- |
| `title-tag` | — | Permite que plugins como Yoast controlen el `<title>` |
| `CLTVO_USEMAILGUN` | `false` | Activa Mailgun como mailer. Si es `false`, usa el mailer del servidor (WP Engine) |
| `CLTVO_DISABLE_COMMENTS` | `true` | Desactiva comentarios globalmente. Admite excepciones por post type: `['except' => ['post']]` |
| `CLTVO_PARTYTOWN` | `true` | Activa Partytown para scripts externos en `analytics.php` |

