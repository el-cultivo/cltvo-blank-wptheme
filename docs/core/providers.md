# Providers
**Estado:** En revisión 

## Descripción general

Los **Providers** son el mecanismo utilizado por el blank theme para centralizar el registro e inicialización de distintas responsabilidades globales.

Las clases se encuentran en:

```text
app/Providers/
```

y los providers activos se declaran en:

```text
config/app.php
```

Esto evita concentrar toda la inicialización del tema directamente en `functions.php` y permite separar cada responsabilidad.

---

## Configuración

`config/app.php` contiene el arreglo `providers`:

```php
'providers' => [
    App\Providers\AppServiceProvider::class,
    App\Providers\AjaxServiceProvider::class,
    App\Providers\ControllerServiceProvider::class,
    App\Providers\CustomPostTypeServiceProvider::class,
    App\Providers\FiltersServiceProvider::class,
    App\Providers\ActionsServiceProvider::class,
    App\Providers\MenuServiceProvider::class,
    App\Providers\MetaboxServiceProvider::class,
    App\Providers\ScriptsServiceProvider::class,
    App\Providers\StylesServiceProvider::class,
    App\Providers\SupportServiceProvider::class,
    App\Providers\TaxonomyServiceProvider::class,
    App\Providers\OptionsServiceProvider::class,
],
```

Este arreglo funciona como el punto central de configuración de los providers utilizados por el tema.

---

## Providers configurados

| Provider | Responsabilidad |
| --- | --- |
| `AppServiceProvider` | Configuración general del tema/aplicación. |
| `AjaxServiceProvider` | Inicialización relacionada con AJAX. |
| `ControllerServiceProvider` | Registro o inicialización de controllers. |
| `CustomPostTypeServiceProvider` | Registro de Custom Post Types. |
| `FiltersServiceProvider` | Filtros de WordPress. |
| `ActionsServiceProvider` | Acciones de WordPress. |
| `MenuServiceProvider` | Configuración relacionada con menús. |
| `MetaboxServiceProvider` | Registro de metaboxes propios del tema. |
| `ScriptsServiceProvider` | Registro y carga de JavaScript. |
| `StylesServiceProvider` | Registro y carga de estilos. |
| `SupportServiceProvider` | Registro de capacidades nativas del theme mediante `add_theme_support()`. |
| `TaxonomyServiceProvider` | Registro de taxonomías. |
| `OptionsServiceProvider` | Registro de Options Pages y Field Groups locales mediante ACF. |

La responsabilidad exacta de cada provider depende de su implementación. La tabla sirve como mapa general de la arquitectura.

---

## Métodos `register()` y `boot()`

Los providers revisados exponen dos métodos:

```php
public function register()
{
    //
}

public function boot()
{
    //
}
```

En los providers revisados, `boot()` concentra la inicialización efectiva de cada responsabilidad. En varios de ellos `register()` existe como parte de la estructura del provider pero actualmente no contiene lógica.

La implementación concreta puede variar entre providers, por lo que no debe asumirse que ambos métodos realizan siempre trabajo.

---

# ActionsServiceProvider

Se encuentra en:

```text
app/Providers/ActionsServiceProvider.php
```

Centraliza acciones globales relacionadas con la inicialización y configuración del blank theme.

El provider conecta distintas funcionalidades con hooks de WordPress como:

```text
after_setup_theme
admin_init
admin_menu
init
tgmpa_register
```

Entre sus responsabilidades se encuentran:

- configuración inicial del tema;
- carga del textdomain;
- ajustes globales y administrativos;
- registro de plugins requeridos mediante TGMPA;
- integración con ACF;
- manejo y sincronización de ACF Local JSON;
- compatibilidad de la lógica de ACF JSON con WPML;
- detección de Field Groups huérfanos respecto a sus archivos JSON;
- herramienta administrativa para ACF JSON Sync;
- deshabilitado global de comentarios cuando se encuentra configurado `CLTVO_DISABLE_COMMENTS`.

Algunas funcionalidades únicamente se inicializan cuando se encuentran disponibles las dependencias o configuraciones correspondientes.

> La integración de ACF es una de las responsabilidades críticas de este provider. `ActionsServiceProvider` documenta el punto de integración, mientras que el flujo completo de ACF y Local JSON debe documentarse de manera independiente.

Ver [ActionsServiceProvider](./providers/actions.md).

---

# AjaxServiceProvider

Se encuentra en:

```text
app/Providers/AjaxServiceProvider.php
```

Centraliza el registro de las clases encargadas de procesar peticiones AJAX del tema.

Las implementaciones se encuentran en:

```text
app/Http/Ajax/
```

y deben extender `Illuminate\Ajax`.

El provider mantiene las clases AJAX que deben registrarse:

```php
protected $files = [
    \App\Http\Ajax\ContactAjax::class,
];
```

Durante `boot()` instancia cada clase y ejecuta su registro:

```php
foreach ($this->files as $ajax) {
    $ajax = new $ajax;
    $ajax->registerAjax();
}
```

`registerAjax()` registra automáticamente las acciones `wp_ajax_*` y `wp_ajax_nopriv_*` correspondientes.

El nombre de la acción se genera a partir del nombre de la clase. Por ejemplo:

```text
ContactAjax → contact
```

Por lo tanto, el provider únicamente determina qué clases AJAX deben inicializarse; la validación y procesamiento de cada petición permanece dentro de su implementación correspondiente.

Ver [AJAX](./providers/ajax.md).

---
# AppServiceProvider

Se encuentra en:

```text
app/Providers/AppServiceProvider.php
```

Administra configuraciones estructurales declaradas en `config/app.php`.

Durante `boot()` procesa:

```text
special-pages
special-categories
special-tags
```

Las `special-pages` permiten declarar páginas requeridas por el tema y mantener referencias a sus IDs sin depender de valores hardcodeados entre ambientes.

También permite crear categorías y etiquetas especiales requeridas por alguna funcionalidad del proyecto.

Ver [AppServiceProvider](./providers/app.md).

---

# ControllerServiceProvider

Se encuentra en:

```text
app/Providers/ControllerServiceProvider.php
```

Centraliza el registro de Controllers utilizados para procesar solicitudes POST tradicionales mediante `admin-post.php`.

Las clases registradas se declaran en:

```php
protected $controllers = [];
```

y durante `boot()` cada implementación ejecuta:

```php
registerController()
```

El nombre de la acción se genera automáticamente a partir del nombre de la clase.

Ejemplo:

```text
ContactController → contact
```

La clase base registra:

```text
admin_post_contact
admin_post_nopriv_contact
```

por lo que puede procesar solicitudes de usuarios autenticados y visitantes.

Los Controllers están pensados principalmente para flujos tradicionales de formulario que terminan en una redirección, mientras que las interacciones que necesitan permanecer en la misma interfaz y responder mediante JSON se manejan con el sistema AJAX.

Ver [Controllers](./providers/controllers.md).

---

# CustomPostTypeServiceProvider

Se encuentra en:

```text
app/Providers/CustomPostTypeServiceProvider.php
```

Mantiene la lista de CPT que deben registrarse:

```php
protected $posttypes = [
    /**\App\ExamplePostType::class,**/
];
```

Durante `boot()`:

```php
foreach ($this->posttypes as $file) {
    $file::registerPostype();
}
```

Cada clase debe extender `Illuminate\CustomPostType`, que proporciona el método estático `registerPostype()`.

El provider no contiene la configuración de cada CPT; únicamente determina cuáles deben inicializarse.

Ver [Custom Post Types](./providers/custom-post-types.md).

---

# FiltersServiceProvider

Se encuentra en:

```text
app/Providers/FiltersServiceProvider.php
```

Centraliza filtros globales aplicados por el blank theme.

Actualmente registra filtros para:

- ocultar la barra de administración en el frontend;
- agregar la query var personalizada `starts_with`;
- modificar el `WHERE` de consultas de WordPress para filtrar contenidos cuyo título comienza con un valor determinado.

El flujo de `starts_with` es:

```text
WP_Query
    ↓
starts_with
    ↓
query_vars
    ↓
posts_where
    ↓
post_title LIKE '{valor}%'
```

`starts_with` es una extensión propia del blank theme y puede utilizarse en directorios o listados alfabéticos.

Ver [Filters](./providers/filters.md).

---

# MenuServiceProvider

Se encuentra en:

```text
app/Providers/MenuServiceProvider.php
```

Centraliza el registro de las ubicaciones de menú disponibles en el theme.

Las locations se declaran en:

```php
protected $menus = [
    'header_menu' => 'Header Menu',
    'footer_menu' => 'Footer Menu',
];
```

y se registran mediante:

```php
register_nav_menus($this->menus);
```

Las keys pueden utilizarse posteriormente como `theme_location` dentro de `wp_nav_menu()`.

Ver [Menus](./providers/menus.md).

---

# MetaboxServiceProvider

Se encuentra en:

```text
app/Providers/MetaboxServiceProvider.php
```

Centraliza el registro de metaboxes propios del blank theme.

Las implementaciones se declaran en:

```php
protected $metaboxes = [
    /**\App\Metaboxes\CltvoSocialNet::class,**/
];
```

Durante `boot()` cada clase es instanciada. Las implementaciones extienden `Illuminate\Metabox`, cuya inicialización registra los hooks necesarios para mostrar y guardar el metabox mediante:

```text
add_meta_boxes
save_post
```

El `meta_key` se genera automáticamente a partir del nombre de la clase.

> La clase base actual no implementa validación de nonce propia. Para nuevos metaboxes deben considerarse nonce y sanitización antes de guardar datos provenientes de `$_POST`.

Ver [Metaboxes](./providers/metaboxes.md).

---

# OptionsServiceProvider

Se encuentra en:

```text
app/Providers/OptionsServiceProvider.php
```

Registra páginas de opciones asociadas a Post Types mediante ACF PRO.

La configuración se obtiene desde:

```text
config/options_pages.php
```

Por cada Post Type configurado crea una Options Page hija de su menú administrativo y registra mediante `acf_add_local_field_group()` la estructura de campos definida actualmente por el provider.

Esta funcionalidad depende de ACF y es distinta al flujo de Field Groups administrados mediante Local JSON.

> La implementación actual no funciona como un generador genérico de Options Pages: la estructura de campos se encuentra definida dentro del provider. Si se generaliza posteriormente, esta documentación deberá actualizarse.

Ver [Options](./providers/options.md) y [ACF](../integrations/acf.md).

---

# SupportServiceProvider

Se encuentra en:

```text
app/Providers/SupportServiceProvider.php
```

Centraliza las capacidades nativas de WordPress declaradas por el theme mediante:

```php
add_theme_support()
```

Actualmente habilita:

```text
post-thumbnails
html5
```

Para HTML5 se declaran:

```text
comment-list
comment-form
search-form
gallery
caption
```

Este provider permite mantener los soportes globales del theme fuera de `functions.php`.

Ver [Support](./providers/support.md).

---

# TaxonomyServiceProvider

Se encuentra en:

```text
app/Providers/TaxonomyServiceProvider.php
```

Mantiene las taxonomías que deben registrarse:

```php
protected $taxonomies = [
    // \App\Taxonomies\ExampleTaxonomie::class,
];
```

Durante `boot()`:

```php
foreach ($this->taxonomies as $taxonomy) {
    $taxonomy::registerTaxonomy();
}
```

Cada clase registrada debe extender `Illuminate\Taxonomy`, que proporciona `registerTaxonomy()`.

El provider determina qué taxonomías se inicializan, mientras que cada clase contiene su propia configuración.

Ver [Taxonomías](./providers/taxonomies.md).

---

# ScriptsServiceProvider

Se encuentra en:

```text
app/Providers/ScriptsServiceProvider.php
```

Centraliza el registro y carga de los archivos JavaScript utilizados por el tema.

El provider permite definir scripts externos mediante CDN y scripts locales. Durante su inicialización registra las dependencias necesarias y finalmente carga el archivo JavaScript principal del tema:

```text
/js/functions.js
```

La cadena de dependencias parte de `jquery` y agrega los scripts configurados antes de registrar el archivo principal:

```text
jquery
    ↓
scripts CDN
    ↓
scripts locales
    ↓
js/functions.js
```

El provider también utiliza `wp_localize_script()` para exponer información de PHP al frontend mediante:

```js
cltvo_js_vars
```

Actualmente incluye valores como:

```text
site_url
template_url
ajax_url
```

`ajax_url` permite que las implementaciones del frontend se comuniquen con las acciones registradas mediante `AjaxServiceProvider`.

Los scripts son registrados para cargarse en el footer del tema mediante `wp_footer()`.

> `ScriptsServiceProvider` se encarga únicamente del registro y carga de scripts dentro de WordPress. La arquitectura JavaScript, ES6 y el proceso de compilación de assets se documentan por separado.

Ver [Scripts](./providers/scripts.md).

---

# StylesServiceProvider

Se encuentra en:

```text
app/Providers/StylesServiceProvider.php
```

Centraliza el registro y carga de las hojas de estilo utilizadas por el tema.

El provider permite definir estilos externos mediante CDN y estilos locales, manteniendo sus dependencias antes de registrar la hoja de estilos principal:

```text
/style.css
```

El flujo general de dependencias es:

```text
styles CDN
    ↓
styles locales
    ↓
style.css
```

Los estilos son registrados y cargados mediante el hook `wp_enqueue_scripts` y WordPress los imprime en el documento a través de `wp_head()`.

> `StylesServiceProvider` se encarga únicamente del registro y carga de estilos dentro de WordPress. La arquitectura CSS/SCSS, el framework de estilos y el proceso de compilación de assets se documentan por separado.

Ver [Styles](./providers/styles.md).

---

## Agregar componentes a providers existentes

Cuando se crea una nueva implementación correspondiente a una responsabilidad que ya cuenta con un provider, no es necesario crear uno nuevo.

Debe registrarse en el provider correspondiente.

### AJAX

```php
protected $files = [
    \App\Http\Ajax\ContactAjax::class,
    \App\Http\Ajax\ExampleAjax::class,
];
```

### CPT

```php
protected $posttypes = [
    \App\ExamplePostType::class,
    \App\OtroPostType::class,
];
```

### Taxonomía

```php
protected $taxonomies = [
    \App\Taxonomies\ExampleTaxonomie::class,
    \App\Taxonomies\OtraTaxonomia::class,
];
```

### Controller

```php
protected $controllers = [
    \App\Http\Controllers\ExampleController::class,
];
```

### Metabox

```php
protected $metaboxes = [
    \App\Metaboxes\ExampleMetabox::class,
];
```

---

## Crear un provider

Un provider adicional tiene sentido cuando se necesita centralizar una nueva responsabilidad global que no corresponde a los providers existentes.

El flujo general es:

1. Crear la clase dentro de `app/Providers/`.
2. Implementar la inicialización necesaria en `register()` y/o `boot()` de acuerdo con la arquitectura utilizada.
3. Registrar la clase en `config/app.php`.

Ejemplo:

```php
'providers' => [
    // ...
    App\Providers\ExampleServiceProvider::class,
],
```

No toda funcionalidad requiere un provider. Antes de crear uno nuevo debe revisarse si ya existe un provider responsable de ese tipo de comportamiento.

---

## Flujo general

```text
config/app.php
    ↓
providers
    ↓
app/Providers/*
    ↓
register() / boot()
    ↓
registro e inicialización de componentes
```

En los casos documentados:

```text
ActionsServiceProvider
    ↓
boot()
    ├── after_setup_theme
    │       ↓
    │   configuración del tema
    │
    ├── init
    │       ↓
    │   configuración global
    │
    ├── admin_init / admin_menu
    │       ↓
    │   administración / ACF
    │
    ├── tgmpa_register
    │       ↓
    │   plugins requeridos
    │
    └── configuración opcional
            ↓
        deshabilitado de comentarios
        
AjaxServiceProvider
    ↓
registerAjax()
    ↓
wp_ajax_{action}
wp_ajax_nopriv_{action}

AppServiceProvider
    ↓
config/app.php
    ├── special-pages
    ├── special-categories
    └── special-tags

ControllerServiceProvider
    ↓
$controllers
    ↓
registerController()
    ↓
admin_post_{action}
admin_post_nopriv_{action}
    ↓
handle()
    ↓
store($_POST)
    ↓
redirect

CustomPostTypeServiceProvider
    ↓
registerPostype()
    ↓
register_post_type()

FiltersServiceProvider
    ↓
add_filter()
    ├── show_admin_bar
    ├── query_vars
    └── posts_where

MenuServiceProvider
    ↓
$menus
    ↓
register_nav_menus()
    ↓
theme_location

MetaboxServiceProvider
    ↓
$metaboxes
    ↓
new Metabox
    ↓
Illuminate\Metabox
    ├── add_meta_boxes
    └── save_post

OptionsServiceProvider
    ↓
config/options_pages.php
    ↓
ACF disponible
    ↓
acf_add_options_page()
    ↓
acf_add_local_field_group()

SupportServiceProvider
    ↓
add_theme_support()
    ├── post-thumbnails
    └── html5

TaxonomyServiceProvider
    ↓
registerTaxonomy()
    ├── register_taxonomy()
    └── términos iniciales

ScriptsServiceProvider
    ↓
registro de scripts y dependencias
    ↓
wp_enqueue_script()
    ↓
wp_footer()

StylesServiceProvider
    ↓
registro de estilos y dependencias
    ↓
wp_enqueue_style()
    ↓
wp_head()
```

---

## Convenciones

- Mantener cada provider enfocado en una responsabilidad.
- Registrar los providers globales desde `config/app.php`.
- Utilizar `ActionsServiceProvider` para acciones globales del ciclo de WordPress que no pertenezcan a un provider más específico.
- Mantener las integraciones complejas inicializadas desde este provider documentadas también en su propio segmento cuando involucren un flujo de trabajo adicional, como ACF Local JSON.
- Registrar las implementaciones AJAX, CPT y taxonomías desde sus providers correspondientes.
- Evitar llevar inicializaciones a `functions.php` cuando ya existe un provider responsable.
- Mantener la configuración particular dentro de la clase del componente y utilizar el provider únicamente como punto de registro.
- Documentar las funcionalidades específicas de cada proyecto fuera de la documentación base del boilerplate.
- Utilizar `special-pages`, `special-categories` y `special-tags` únicamente para contenido estructural requerido por el tema.
- Evitar IDs hardcodeados cuando una página puede declararse como Special Page.
- Utilizar Controllers para formularios POST tradicionales cuyo flujo pueda terminar en una redirección.
- Utilizar AJAX cuando la interacción deba actualizar la interfaz sin recargar la página.
- Utilizar `FiltersServiceProvider` para filtros globales o transversales del tema.
- Mantener filtros específicos de una feature junto a su implementación cuando no formen parte del comportamiento general del boilerplate.
- Los callbacks de filtros deben devolver siempre el valor correspondiente.
- Utilizar `$wpdb->prepare()` y escaping adecuado cuando un filtro modifique SQL con valores dinámicos.
- Registrar las ubicaciones globales de navegación desde `MenuServiceProvider` en lugar de distribuirlas en `functions.php`.
- Registrar metaboxes propios desde `MetaboxServiceProvider` y agregar nonce y sanitización en nuevas implementaciones.
- Utilizar `OptionsServiceProvider` únicamente cuando su estructura corresponda a la necesidad del proyecto; su implementación actual no es genérica.
- Mantener las keys utilizadas por `config/options_pages.php` únicas y consistentes.
- Centralizar los soportes globales de WordPress en `SupportServiceProvider`.
