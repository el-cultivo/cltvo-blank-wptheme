# Arquitectura del tema

**Estado:** En revisión

## Descripción general

`cltvo_blank_wptheme` es un boilerplate de WordPress que sirve como punto de partida para todos los proyectos de la agencia. No es un tema hijo ni una dependencia: se descarga, se renombra y a partir de él se desarrolla el proyecto completo desde cero.

Cada proyecto vive en su propio repositorio independiente. El boilerplate no se actualiza en proyectos existentes; las mejoras se incorporan solo en proyectos nuevos.

## Estructura del tema

La arquitectura separa principalmente:

```text
bootstrap/   → arranque de la aplicación
framework/   → infraestructura PHP reutilizable
config/      → configuración del blank / proyecto
app/         → implementaciones PHP del proyecto
templates + views/ → presentación WordPress
es6/ + sass/ → fuentes frontend
js/ + style.css → assets compilados
```

Ver [Estructura del tema](./theme-structure.md).

---

## Bootstrap

El punto de entrada PHP es `functions.php`, que carga:

```php
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

`bootstrap/autoload.php` carga Composer y TGMPA. `bootstrap/app.php` crea `Illuminate\Foundation\Application`.

Ver [Bootstrap](./core/bootstrap.md).

---

## Composer y namespaces

Composer registra:

```text
Illuminate\ → framework/src/Illuminate/
App\        → app/
```

y carga globalmente:

```text
framework/src/Illuminate/Support/helpers.php
app/helpers.php
```

Esto separa la infraestructura reutilizable (`framework/`) de la implementación del proyecto (`app/`).

Ver [Framework](./core/framework.md) y [Helpers](./core/helpers.md).

---

## Application y Providers

`Illuminate\Foundation\Application` carga `config/app.php`, conserva la configuración en `$this->config` y recorre el arreglo `providers`.

El ciclo real es:

```text
config/app.php
    ↓
new Provider($application)
    ↓
ServiceProvider::__construct()
    ↓
$this->app = $application
    ↓
Provider::boot()
```

Aunque algunas clases contienen `register()`, **Application no ejecuta ese método automáticamente**. En la arquitectura actual, `boot()` es el punto efectivo de inicialización.

Ver [Providers](./core/providers.md).

---

## Configuración

`config/` contiene:

```text
app.php
conf_plugins.php
options_pages.php
required_plugins.php
```

`config/app.php` define providers, Special Pages, Special Categories y Special Tags. El orden de los providers también determina el orden en que se ejecutan sus `boot()`.

Ver [Configuración](./core/config.md).

---

## Implementación del proyecto

`app/` contiene las implementaciones que utilizan las abstracciones del framework:

```text
Http/
Mail/
Metaboxes/
Providers/
Taxonomies/
helpers.php
clases propias del proyecto
```

El blank revisado mezcla implementaciones activas, ejemplos y código histórico.

Ver [App](./core/app.md).

---

## Custom Post Types y Taxonomías

Los CPT extienden `Illuminate\CustomPostType` y se registran desde `CustomPostTypeServiceProvider`.

Ver [Custom Post Types](./core/providers/custom-post-types.md).

Las taxonomías extienden `Illuminate\Taxonomy` y se registran desde `TaxonomyServiceProvider`.

Ver [Taxonomías](./core/providers/taxonomies.md).

---

## AJAX y Controllers

El blank contiene dos mecanismos distintos:

```text
AJAX
Illuminate\Ajax
→ wp_ajax_{action}
→ wp_ajax_nopriv_{action}

Controllers
Illuminate\Controller
→ admin_post_{action}
→ admin_post_nopriv_{action}
```

AJAX está orientado a interacciones sin navegación tradicional. Controllers, a formularios POST cuyo flujo puede terminar en una redirección.

Ver [AJAX](./core/providers/ajax.md) y [Controllers](./core/providers/controllers.md).

---

## Templates y Views

Los templates raíz participan en WordPress Template Hierarchy:

```text
index.php
page.php
single.php
archive.php
404.php
page-{slug}.php
```

Los partials reutilizables viven en `views/`.

Las Special Pages pueden relacionarse naturalmente con templates como:

```text
special page: home
    ↓
page-home.php
```

Ver [Templates y Views](./core/templates-and-views.md).

---

## Integraciones

### ACF

La integración incluye Local JSON, sincronización manual, Special Pages Location, Options Pages y carga de subdirectorios para proyectos multiidioma.

Ver [ACF](./integrations/acf.md).

### Correo / Mailgun

El sistema actual combina plugin Mailgun, `wp_mail()`, PHPMailer, el framework Mail y Mailables del proyecto. Contiene comportamiento histórico y puntos pendientes de refactor.

No debe resumirse como `false = servidor / true = Mailgun`, porque `CLTVO_USEMAILGUN` y `WP_DEBUG` participan en el flujo actual.

Ver [Mailgun y sistema de correo](./integrations/mailgun.md).LTVO_PARTYTOWN` | `true` | Activa Partytown para scripts externos en `analytics.php` |

---

## Frontend

El build revisado utiliza Laravel Mix, Webpack, Babel y Sass:

```text
es6/micorriza.js → js/functions.js
sass/mazorca.scss → style.css
```

Ver [Build](./frontend/build.md), [JavaScript](./frontend/javascript.md) y [Mazorca](./frontend/mazorca.md).

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

## Sobre la configuración todavía presente en `functions.php`

Se documenta así porque es el comportamiento actual, pero forma parte de la auditoría de refactor:

- `title-tag` podría centralizarse con los soportes de WordPress en `SupportServiceProvider`;
- `CLTVO_DISABLE_COMMENTS` funciona como configuración interna consumida por `ActionsServiceProvider`;
- `CLTVO_USEMAILGUN` pertenece conceptualmente a la configuración del sistema de correo.

Estas propuestas **no deben documentarse como comportamiento implementado** hasta realizar el cambio.

---

## Principio general

La arquitectura busca mantener `functions.php` cercano al bootstrap y distribuir responsabilidades entre framework, configuración, providers, implementaciones de `app/`, integraciones y frontend.

Al trabajar sobre el blank debe distinguirse siempre entre:

```text
infraestructura vigente
ejemplos / boilerplate
código histórico / legacy
```

La presencia de un archivo en el repositorio no implica que forme parte del flujo activo.
