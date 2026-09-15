# Mazorca / Sass
**Estado:** En revisión

## Descripción

El Sass del blank theme se encuentra en:

```text
sass/
├── mazorca.scss
└── mazorca/
```

El entry point:

```text
sass/mazorca.scss
```

se compila mediante Laravel Mix a:

```text
style.css
```

y posteriormente `StylesServiceProvider` lo carga en WordPress.

---

# Flujo

```text
mazorca-core
    ↓
sass/mazorca.scss
    ↓
config
elements
grid
components
theme helpers
modules
    ↓
style.css
```

---

# Entry point

`sass/mazorca.scss` contiene también el header requerido por WordPress:

```css
/*
Theme Name: - Blank Cltvo Theme -
Author: El Cultivo
...
*/
```

Esto es importante porque el output final:

```text
style.css
```

es a la vez:

1. stylesheet compilado;
2. archivo de identificación del theme para WordPress.

---

# `mazorca-core`

Antes del Sass propio del proyecto se importa:

```scss
@import "node_modules/mazorca-core/_mazorca-core.scss";
```

Después se cargan las configuraciones e implementaciones del theme.

Conceptualmente:

```text
mazorca-core
    ↓
mixins / helpers / infraestructura Sass

sass/mazorca/
    ↓
configuración y estilos del proyecto
```

---

# Orden de imports

El orden actual es:

```scss
@import "node_modules/mazorca-core/_mazorca-core.scss";

@import "mazorca/config/_config";

@import "mazorca/_elements";
@import "mazorca/_grid";
@import "mazorca/_components";
@import "mazorca/_theme-helpers";
@import "mazorca/_modules";
```

El orden es relevante porque varios archivos utilizan mixins, funciones y variables definidos anteriormente.

---

# Config

```text
sass/mazorca/config/
```

contiene:

```text
_widths.scss
_base.scss
_pads-n-margs.scss
_z-index.scss
_html-body.scss
_breaks.scss
_colors.scss
_fonts.scss
_inputs.scss
_transition-speeds.scss
```

No todos están importados actualmente.

---

## `_config.scss`

Carga:

```text
widths
base
pads-n-margs
z-index
html-body
breaks
colors
fonts
inputs
```

y agrupa varios mapas en:

```scss
$maps
```

---

# Breakpoints

Actualmente:

```scss
$breaks: (
    xs: 480px,
    mobile: 650px,
    sm: 768px,
    md: 1024px,
    lg: $container-max-width,
    xt: 1201px,
);
```

y existe:

```scss
b($break)
```

para consultar el mapa.

Los mixins responsive como:

```text
under()
respond-to()
```

provienen de Mazorca Core.

---

# Colores

`_colors.scss` declara:

```scss
$colors
```

y la función:

```scss
c($color)
```

para consultar colores.

Ejemplo:

```scss
background-color: c(yellow);
```

## Variables problemáticas

El archivo también define:

```scss
$colorForeground: c();
$colorBackground: c();
$colorFeatured: c();
$colorError: c();
```

pero el mapa `$colors` revisado no contiene una key:

```text
base
```

y `c()` utiliza `base` como argumento default.

Por lo tanto estas variables merecen revisión; no existe en el archivo una entrada `base` que resuelva explícitamente esos valores.

---

# Fuentes

`_fonts.scss` incluye numerosas declaraciones mediante:

```scss
@include declara-font-face(...)
```

y define el mapa:

```scss
$fonts
```

más la función:

```scss
f($font)
```

## Código específico de un proyecto histórico

El archivo contiene un comentario:

```text
Zona Paz
```

y múltiples fuentes:

```text
Bodoni
OpenSans
Bauer
```

Además las rutas apuntan a:

```text
../fonts/...
```

pero en el ZIP del blank revisado no existe una carpeta raíz:

```text
fonts/
```

con esos archivos.

Por lo tanto este bloque no debe entenderse como configuración neutral del blank theme. Es claramente material heredado que debe evaluarse para cleanup.

---

# Base

`_base.scss`:

1. vuelve a importar `_colors`;
2. ejecuta `@include reset`;
3. configura una base de `10px`;
4. aplica color de selección de texto mediante `$colorFeatured`.

La base actual:

```scss
$base-px: 10px;
html {
    font-size: $base-px;
}
```

pertenece al sistema histórico de escalas de Mazorca.

---

# Inputs globales

`config/_inputs.scss` aplica estilos directamente a elementos HTML:

```text
input
textarea
select
checkbox
radio
```

Incluye decisiones como:

```scss
input:focus,
select:focus,
textarea:focus,
button:focus {
    outline: none;
}
```

## Consideración

Eliminar el outline de foco globalmente puede afectar accesibilidad si no existe un estilo de foco alternativo.

Este archivo debe revisarse antes de conservarlo sin cambios en un boilerplate moderno.

---

# Spacing helpers

`_pads-n-margs.scss` declara mapas:

```text
$pads
$margs
$lh
$mb
$spacings
```

y helpers:

```scss
s()
lh()
mb()
```

Ejemplo conceptual:

```scss
margin-bottom: s(2, mb);
```

Es parte del sistema de utilidades Sass del theme.

---

# Z-index

`_z-index.scss` contiene:

```scss
$z
```

y la función:

```scss
z()
```

para evitar valores de z-index repartidos sin una fuente central.

---

# HTML / Body

`_html-body.scss` implementa el patrón de sticky footer mediante flex:

```text
html/body → height 100%
body      → flex column
.main-wrap → flex grow
.footer    → flex-shrink 0
```

Esto se relaciona directamente con:

```text
header.php
footer.php
```

porque ambos manejan `.main-wrap`.

---

# Elements

```text
sass/mazorca/elements/
```

contiene placeholders para elementos reutilizables:

```text
%title
%paragraph
%link
%button
%input
%label
```

y placeholders de fuentes.

Algunos contienen implementación real y otros están casi vacíos.

## Estado

Esqueleto de diseño / boilerplate.

No todos deben considerarse estilos necesarios.

---

# Grid

Existen dos piezas diferentes:

```text
sass/mazorca/_grid.scss
sass/mazorca/grid/_fluid-grid.scss
```

## Grid actualmente importado

El entry de Mazorca importa:

```scss
@import "mazorca/_grid";
```

`_grid.scss` implementa clases:

```text
.grid__row
.grid__container
.grid__col-1-1
.grid__col-1-2
.grid__col-1-3
...
```

Estas clases sí aparecen en templates como:

```text
page.php
```

Por lo tanto el grid BEM de `_grid.scss` forma parte del blank actual.

---

## `_fluid-grid.scss`

Implementa otro sistema basado en:

```text
.row
.cuadricula
.columns
.cuadro
```

pero no aparece importado desde:

```text
mazorca.scss
_config.scss
_grid.scss
```

dentro del código revisado.

Por lo tanto parece ser infraestructura histórica no activa en el build actual.

Debe confirmarse antes de conservarse.

---

# Components

```text
sass/mazorca/_components.scss
```

está vacío.

Sólo existe un archivo de muestra:

```text
components/_.sample-component.scss
```

con:

```scss
@mixin sample-component {
    //your reusable code goes here
}
```

Este bloque es claramente scaffold / ejemplo.

---

# Theme helpers

`_theme-helpers.scss` contiene placeholders reutilizables para:

```text
aspect ratio
imagen absoluta / cover
contenedor máximo
contenido WYSIWYG
biografías
```

Sin embargo varios estilos incluyen decisiones concretas:

```text
OpenSans
tamaños tipográficos
anchos específicos
```

Por lo tanto mezcla:

```text
helpers estructurales reutilizables
```

con:

```text
estilos visuales de proyectos anteriores
```

Es un candidato importante para separar responsabilidades durante el cleanup.

---

# Modules

`_modules.scss` importa actualmente:

```text
modules/general/header
modules/general/footer
modules/general/error

modules/pages/atomos
modules/pages/splash
```

---

## Header / Footer

Ambos estilos incluyen:

```scss
display: none;
```

por default.

Esto coincide con que el blank funciona más como base de desarrollo que como theme visual terminado.

No debe asumirse como comportamiento deseable para proyectos nuevos sin modificación.

---

## Átomos

`modules/pages/_atomos.scss` corresponde a:

```text
page-atomos.php
```

y funciona como página histórica de pruebas visuales.

Es candidato natural para salir de una versión entregable a clientes si el proyecto no utiliza un styleguide interno de este tipo.

---

## Splash

`modules/pages/_splash.scss` corresponde a:

```text
page-splash.php
```

y sí proporciona una pantalla de presentación visual del blank.

También utiliza colores y assets propios del Cultivo.

Por lo tanto debe considerarse demo/branding del boilerplate, no arquitectura necesaria.

---

# `_transition-speeds.scss`

Existe:

```text
config/_transition-speeds.scss
```

con un mapa y:

```scss
@include make-transitions();
```

pero no está importado desde `_config.scss`.

En el build revisado no participa actualmente.

---

# Segundo `package.json`

Dentro de:

```text
sass/
```

existe otro:

```text
package.json
package-lock.json
```

Su descripción indica un flujo histórico para instalar el boilerplate de Mazorca y hace referencia a instalación global.

El theme raíz ya contiene su propio:

```text
package.json
```

con `mazorca-core`.

Esto debe tratarse como duplicación/historial hasta definir el flujo oficial actual.

---

# Clasificación de Mazorca

| Área | Estado |
| --- | --- |
| `mazorca.scss` | Entry activo |
| `mazorca-core` | Dependencia activa |
| `config/` | Activo, con varias piezas heredadas |
| `_elements.scss` | Activo |
| `_grid.scss` | Activo |
| `_components.scss` | Activo pero vacío |
| `_theme-helpers.scss` | Activo; mezcla helpers y estilos históricos |
| `_modules.scss` | Activo |
| `grid/_fluid-grid.scss` | No importado en el flujo revisado |
| `config/_transition-speeds.scss` | No importado |
| página Átomos | Demo / histórica |
| página Splash | Demo / branding |
| fuentes Zona Paz | Legacy específico de proyecto |
| `sass/package.json` | Histórico / duplicado |

---

# Puntos de revisión para cleanup futuro

Esta documentación registra hallazgos, no aplica todavía cambios.

Para una versión limpia destinada también a clientes conviene revisar especialmente:

```text
fuentes y comentarios de Zona Paz
assets inexistentes referenciados por fonts
c() sin key base
outline:none global
fluid grid no importado
transition-speeds no importado
sample component
Atomos
Splash / branding Cultivo
sass/package.json duplicado
helpers WYSIWYG con estilos de proyecto
```

El objetivo del cleanup debería ser conservar en el boilerplate únicamente lo que sea:

```text
infraestructura reusable
```

o:

```text
ejemplo deliberado y claramente identificado
```

evitando entregar residuos de proyectos anteriores como si fueran parte del framework.
