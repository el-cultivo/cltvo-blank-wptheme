# Templates y Views
**Estado:** En revisión

## Descripción general

La capa de presentación PHP del blank theme se divide actualmente entre:

```text
templates raíz de WordPress
    ↓
header.php
footer.php
index.php
page.php
single.php
archive.php
404.php
page-*.php
template-page.php

views/
    ↓
partials reutilizables
```

En el estado actual del blank existe una mezcla de:

- templates funcionales;
- templates mínimos de ejemplo;
- archivos vacíos;
- código de debugging;
- referencias heredadas que ya no coinciden con la configuración actual.

Por lo tanto, estos archivos deben entenderse como punto de partida del boilerplate y no como una implementación terminada.

---

# Flujo general

Un flujo típico de página es:

```text
WordPress Template Hierarchy
    ↓
page.php / page-home.php / 404.php / ...
    ↓
get_header()
    ↓
header.php
    ↓
views/general/header.php
    ↓
contenido del template
    ↓
get_footer()
    ↓
footer.php
    ↓
views/general/footer.php
```

---

# `header.php`

Se encuentra en:

```text
header.php
```

Contiene la apertura general del documento HTML y ejecuta:

```php
wp_head();
```

antes de cerrar `<head>`.

Posteriormente abre:

```html
<body>
```

y finalmente:

```html
<div class="main-wrap">
```

que se cierra desde `footer.php`.

---

## Título

Actualmente incluye:

```php
<title><?php bloginfo('name'); ?></title>
```

Sin embargo, `functions.php` también registra:

```php
add_theme_support('title-tag');
```

Cuando un theme declara `title-tag`, WordPress puede administrar el `<title>` mediante `wp_head()`.

Por lo tanto, conservar un `<title>` manual al mismo tiempo es redundante y puede generar una estrategia inconsistente.

### Recomendación

Si `title-tag` se mantiene como soporte oficial del blank theme, el `<title>` manual debería eliminarse.

---

## Favicon

Carga:

```php
include_once('inc/favicon.php');
```

Esto confirma que el sistema histórico de favicons del blank sigue conectado al frontend.

Actualmente:

```text
header.php
    ↓
inc/favicon.php
    ↓
images/favicon/
```

Si posteriormente se moderniza el manejo del favicon, debe actualizarse también esta referencia.

---

## `humans.txt`

Incluye:

```php
<meta name="author" content="<?php echo THEMEURL;?>humans.txt">
```

Este uso es semánticamente extraño: `meta[name="author"]` normalmente contiene un nombre de autor y no una URL a `humans.txt`.

Si se desea conservar `humans.txt`, conviene revisar cómo debe exponerse actualmente.

---

# Instancia global de `Contacto`

`header.php` ejecuta:

```php
use App\Contacto;

global $contact;

$contact = new Contacto;
```

Por lo tanto, cada request del frontend que pase por este header crea una instancia de `Contacto`.

Esto conecta:

```text
header.php
    ↓
App\Contacto
    ↓
Special Page contacto
    ↓
metadata de contacto
```

En el blank actual no se observa que:

```text
views/general/header.php
```

utilice `$contact`.

### Recomendación

Antes de conservar esta inicialización como patrón del boilerplate debe verificarse si algún proyecto base realmente requiere `$contact` de forma global.

Si no existe una dependencia real, puede eliminarse del header y dejar que cada feature solicite la información que necesite.

---

# Analytics

El header también hace:

```php
if (!defined('CLTVO_ISLOCAL') || (CLTVO_ISLOCAL != true)) {
    include_once('inc/analytics.php');
}
```

Esto ocupa:

```text
inc/analytics.php
```

Agregar el código de analytics cuando el proyecto lo comparta en ese archivo.

---

# Aviso para navegadores antiguos

`header.php` conserva un bloque condicional para versiones antiguas de Internet Explorer:

```html
<!--[if gt IE 8]>
...
<![endif]-->
```

Este bloque es código legacy y puede eliminarse en una limpieza moderna del blank theme.

---

# Partial principal de navegación

El header termina llamando:

```php
get_template_part('views/general/header');
```

por lo que la navegación visual vive en:

```text
views/general/header.php
```

---

# `footer.php`

Se encuentra en:

```text
footer.php
```

Su flujo actual es:

```text
cerrar .main-wrap
    ↓
views/general/footer.php
    ↓
wp_footer()
    ↓
cerrar body/html
```

Incluye correctamente:

```php
wp_footer();
```

antes del cierre de `<body>`.

Esto es necesario para que WordPress y los plugins puedan imprimir scripts y markup al final del documento.

---

# `views/general/header.php`

Renderiza dos menús:

```text
desktop
mobile
```

## Desktop

Utiliza:

```php
'theme_location' => 'header_menu'
```

que sí está registrado en:

```text
MenuServiceProvider
```

## Mobile

Utiliza:

```php
'theme_location' => 'sidebar_menu'
```

pero el provider únicamente registra:

```php
protected $menus = [
    'header_menu' => 'Header Menu',
    'footer_menu' => 'Footer Menu',
];
```

Por lo tanto:

```text
views/general/header.php
    → solicita sidebar_menu

MenuServiceProvider
    → NO registra sidebar_menu
```

### Bug / inconsistencia

`sidebar_menu` no forma parte de los locations registrados actualmente.

Debe decidirse si:

- el menú móvil debe reutilizar `header_menu`; o
- realmente debe existir un tercer location `sidebar_menu`.

No conviene conservar esta discrepancia en el boilerplate.

---

## Propiedad `menu`

Ambos `wp_nav_menu()` incluyen:

```php
'menu' => 'main'
```

además de `theme_location`.

Esto acopla el ejemplo al nombre concreto de un menú creado en WordPress.

Para un boilerplate general sería preferible depender del:

```text
theme_location
```

y dejar que el administrador asigne el menú correspondiente.

---

# `views/general/footer.php`

Actualmente contiene:

```text
Todos los derechos reservados © 2019 El Cultivo
```

Este contenido es claramente placeholder / histórico.

No debe considerarse parte de la implementación base de un nuevo proyecto.

Podría sustituirse por un ejemplo neutral o dejarse vacío para que cada proyecto implemente su footer.

---

# `index.php`

Actualmente contiene únicamente:

```php
dump($wp_query);
```

## Problema detectado

No se encontró una función global:

```php
dump()
```

definida por el blank theme.

La dependencia:

```text
symfony/var-dumper
```

se encuentra únicamente en:

```text
require-dev
```

y, además, la función global que proporciona Symfony VarDumper depende de que esa dependencia exista en el entorno.

Por lo tanto, `index.php` es actualmente un template de debugging y no una fallback page segura para producción.

### Recomendación

El `index.php` de un theme debería ser un fallback válido de la Template Hierarchy.

Como mínimo debería implementar un loop básico o delegar a una vista genérica.

Este archivo es candidato prioritario a cleanup.

---

# `archive.php`

Actualmente está vacío.

WordPress utilizará este archivo cuando corresponda según Template Hierarchy, pero no imprimirá contenido.

Por lo tanto, cualquier archive que termine aquí puede devolver visualmente una página vacía.

### Estado

Template placeholder / incompleto.

---

# `single.php`

Actualmente está vacío.

Esto significa que Posts o CPTs que terminen utilizando el fallback:

```text
single.php
```

no tendrán salida visual.

### Estado

Template placeholder / incompleto.

---

# `page.php`

`page.php` sí contiene un loop funcional:

```php
if (have_posts()) :
    while (have_posts()) :
        the_post();
```

e imprime:

```text
the_title()
the_content()
```

También utiliza:

```text
get_header()
get_footer()
```

por lo que puede funcionar como fallback general para Pages.

### Consideración

El markup y clases actuales:

```text
grid__row
grid__container--fluid
grid__col-1-1
page-template__*
```

pertenecen al sistema visual histórico del blank / Mazorca.

Un proyecto puede conservarlas, adaptarlas o reemplazarlas según su estructura frontend.

---

# `template-page.php`

Declara:

```php
/* Template name: Página simple */
```

Por lo tanto aparece como Page Template seleccionable en el admin de WordPress.

Su implementación es una versión todavía más mínima que `page.php`:

```text
h1 → título
contenido
```

### Estado

Template de ejemplo válido.

No depende de ninguna lógica interna adicional.

---

# `page-home.php`

Por nombre, WordPress lo interpreta como template específico para una Page cuyo slug sea:

```text
home
```

Esto coincide con la Special Page:

```text
home
```

de `config/app.php`.

El flujo es:

```text
config/app.php
    ↓
special-pages['home']
    ↓
AppServiceProvider
    ↓
Page slug: home
    ↓
page-home.php
```

Actualmente la implementación sólo imprime:

```text
Home
```

y contiene código comentado relacionado con un término destacado.

### Estado

Template placeholder para la Special Page `home`.

---

# `page-splash.php`

También se conecta por slug con:

```text
special-pages['splash']
```

y renderiza dos imágenes:

```text
images/cultivo_logo.svg
images/ElCultivo.png
```

### Estado

Página de demostración / branding del blank theme.

No representa una funcionalidad obligatoria para proyectos nuevos.

---

# `page-atomos.php`

Renderiza una sección de prueba:

```text
¿Funcionan las fuentes?
```

con comentarios para:

```text
Botones
Menú
Footer
```

No existe una Special Page `atomos` declarada en `config/app.php`.

Por lo tanto este archivo parece corresponder a una página histórica de pruebas visuales / componentes.

### Estado

Development/demo.

Puede eliminarse en proyectos que no utilicen una página de átomos o styleguide.

---

# `404.php`

Actualmente intenta cargar:

```php
get_template_part('views/general/error');
```

pero sólo existen:

```text
views/general/header.php
views/general/footer.php
```

No existe:

```text
views/general/error.php
```

### Bug detectado

La página 404 actualmente envuelve un template part inexistente:

```text
404.php
    ↓
views/general/error.php
```

`get_template_part()` no genera un fatal si el archivo no existe, pero el resultado será esencialmente un contenedor vacío entre header y footer.

### Recomendación

Agregar una vista real de error o implementar directamente el contenido del 404.

---

# `views/`

La carpeta actualmente contiene únicamente:

```text
views/
└── general/
    ├── header.php
    └── footer.php
```

Por lo tanto todavía no existe una capa amplia de componentes o layouts PHP en este blank.

La convención actual puede entenderse como:

```text
templates raíz
    ↓
deciden qué página se está renderizando

views/
    ↓
partials reutilizables
```

---

# Relación con WordPress Template Hierarchy

Los archivos raíz no son registrados por el framework propio.

WordPress los selecciona mediante su Template Hierarchy.

Ejemplos:

```text
Page slug home
    ↓
page-home.php
    ↓
page.php
    ↓
index.php

Single
    ↓
single-{post_type}.php
    ↓
single.php
    ↓
index.php

Archive
    ↓
archive-{post_type}.php
    ↓
archive.php
    ↓
index.php

404
    ↓
404.php
```

Por esta razón `index.php` tiene especial importancia: es el fallback final del theme y actualmente sólo contiene debugging.

---

# Clasificación actual

| Archivo / área | Estado |
| --- | --- |
| `header.php` | Activo; contiene varias piezas legacy |
| `footer.php` | Activo |
| `views/general/header.php` | Activo; `sidebar_menu` inconsistente |
| `views/general/footer.php` | Activo; contenido placeholder |
| `page.php` | Fallback funcional para Pages |
| `template-page.php` | Page Template de ejemplo |
| `page-home.php` | Placeholder ligado a Special Page |
| `page-splash.php` | Demo del blank |
| `page-atomos.php` | Demo / styleguide histórico |
| `404.php` | Activo pero referencia una view inexistente |
| `single.php` | Vacío |
| `archive.php` | Vacío |
| `index.php` | Debugging; fallback no apto para producción |

---

# Oportunidades de limpieza

## Alta prioridad

- Reemplazar `index.php` por un fallback válido.
- Implementar o corregir `404.php`.
- Resolver `sidebar_menu` vs locations registrados.
- Evitar templates vacíos para `single.php` y `archive.php` si el blank pretende funcionar sin personalización previa.

## Media prioridad

- Eliminar el `<title>` manual si se conserva `title-tag`.
- Revisar la instancia global de `Contacto`.
- Eliminar Analytics legacy.
- Eliminar el aviso para Internet Explorer.
- Evitar `'menu' => 'main'` y utilizar únicamente locations.

## Cleanup / boilerplate

- Actualizar o neutralizar el footer de 2019.
- Decidir si `page-atomos.php` sigue siendo parte del blank.
- Definir si `page-splash.php` y `page-home.php` deben permanecer como ejemplos oficiales.
- Revisar la estrategia histórica de favicon y `humans.txt`.
