# JavaScript / ES6
**Estado:** En revisión

## Estructura

El JavaScript fuente del blank theme se encuentra en:

```text
es6/
├── admin/
│   ├── featured-image.js
│   ├── file-upload.js
│   ├── gallery.js
│   ├── hex-color-text-input.js
│   └── sortable-tables.js
├── constants.js
├── micorriza-admin.js
└── micorriza.js
```

Actualmente sólo:

```text
micorriza.js
```

forma parte del build del frontend.

---

# `constants.js`

Expone:

```js
export const $ = jQuery;
export const w = $(window);
```

Su intención es centralizar las referencias de jQuery utilizadas por los módulos ES6.

---

# `micorriza.js`

Es el entry point actual de JavaScript:

```text
es6/micorriza.js
    ↓
webpack.mix.js
    ↓
js/functions.js
```

En el blank revisado únicamente:

1. importa `$` y `w`;
2. registra un callback para `window.load`;
3. registra un `document.ready`;
4. imprime mensajes de prueba en consola.

```js
console.log('Hello world from El Cultivo! --Load');
console.log('Hello world from El Cultivo! --Ready');
```

## Estado

El archivo funciona principalmente como entry point / placeholder.

Los `console.log()` son código de demostración y son candidatos claros a cleanup en una versión destinada a clientes.

---

# JavaScript administrativo

La carpeta:

```text
es6/admin/
```

contiene utilidades históricas para interfaces custom de `wp-admin`.

No se encuentra activa actualmente.

El flujo necesario sería conceptualmente:

```text
es6/admin/*
    ↓
micorriza-admin.js
    ↓
build de admin
    ↓
admin_enqueue_scripts
    ↓
wp-admin
```

pero los tres puntos de conexión están deshabilitados actualmente.

---

# `micorriza-admin.js`

Contiene imports para:

```text
gallery
hex-color-text-input
sortable-tables
featured-image
file-upload
```

pero todos están comentados.

Por lo tanto, actualmente no ejecuta ninguna funcionalidad.

---

# `admin/file-upload.js`

Es la utilidad más genérica del conjunto.

Expone:

```js
metaboxUploadInterface(config)
```

que crea una interfaz sobre:

```js
wp.media()
```

para seleccionar archivos desde la Media Library de WordPress.

La configuración permite proporcionar:

```text
selector del botón
opciones de wp.media
callback por attachment
callback después de la selección
handler de eliminación
```

También incluye:

```js
defaultFileUploadConfig()
```

con selectores predefinidos:

```text
.cltvo_upload_JS
.cltvo_file_id_input_JS
.cltvo_filename_input_JS
.fileUpload__success_JS
.cltvo_remove_upload_JS
```

## Potencial

La abstracción puede seguir siendo útil para metaboxes custom que no utilicen ACF.

## Observaciones

Utiliza:

```js
jQuery(window).load(...)
```

una convención antigua que debe revisarse antes de reutilizarse en código moderno.

---

# `admin/featured-image.js`

Implementa selección de imágenes con:

```js
wp.media()
```

pero está fuertemente acoplado a:

```text
#table__banners
.banner_row
.media-button
.media-input
.thumbnail_holder
```

Por lo tanto no es una utilidad genérica de "featured image"; en realidad corresponde a una interfaz específica de banners.

## Estado

Código histórico/específico.

Si se conserva, el nombre debería reflejar mejor su responsabilidad o debería refactorizarse para recibir configuración como `file-upload.js`.

---

# `admin/gallery.js`

Implementa una galería custom que permite:

```text
seleccionar imagen con wp.media
guardar attachment ID en inputs hidden
eliminar imágenes
ordenarlas mediante sortable()
```

Expone globalmente:

```js
window.initGallery = (...)
```

## Dependencias identificadas

Importa:

```js
import R from 'ramda'
```

pero `ramda` no aparece declarado en el `package.json` raíz revisado.

También utiliza:

```js
sortable()
```

por lo que depende de jQuery UI Sortable o de una implementación compatible cargada en el contexto.

No se identificó dentro del flujo activo del blank un enqueue específico de jQuery UI Sortable para este módulo.

## Estado

La idea es reutilizable, pero la implementación actual no debe activarse sin revisar primero sus dependencias.

---

# `admin/hex-color-text-input.js`

Escucha cambios en:

```text
.hex-color-text-input_JS
```

y actualiza visualmente:

```text
.hex-color-text-sample_JS
```

con el color hexadecimal ingresado.

## Bug identificado

Contiene:

```js
if (input.lenght < 0) { return; }
```

`lenght` es un typo de:

```js
length
```

Además la condición:

```js
length < 0
```

no sería útil para detectar una colección vacía; una colección jQuery vacía tiene longitud `0`.

## Estado

Utilidad pequeña y recuperable, pero requiere corrección antes de reutilizarse.

---

# `admin/sortable-tables.js`

Implementa una tabla repetible custom:

```text
agregar fila
eliminar fila
renombrar inputs
drag & drop
```

Trabaja con selectores como:

```text
.cltvo_sortable_table__container_JS
.tr_sortable_JS
.add__sortable_JS
.delete__sortable_JS
#sortable_clone_JS
#tbody__sortable_JS
```

y utiliza:

```js
sortable()
```

por lo que también depende del comportamiento de jQuery UI Sortable.

## Estado

Puede ser útil en metaboxes custom, pero actualmente está deshabilitado y acoplado a una estructura HTML específica.

---

# Relación con ACF

Estas utilidades tienen mayor sentido cuando el proyecto implementa:

```text
metaboxes custom
inputs custom
interfaces del wp-admin propias
```

En proyectos donde ACF resuelve:

```text
galerías
repetidores
imágenes
archivos
colores
```

parte de estas utilidades puede resultar redundante.

Esto no implica que deban eliminarse automáticamente: el criterio debe ser si el blank moderno pretende seguir ofreciendo infraestructura para metaboxes custom además de ACF.

---

# Documentación `.md` existente

Dentro de:

```text
es6/admin/
```

ya existen archivos de documentación para:

```text
featured-image
file-upload
hex-color-text-input
sortable-tables
```

No existe un `.md` equivalente para:

```text
gallery.js
```

Estos archivos deben revisarse junto con el código antes de considerarlos documentación vigente, ya que el toolkit no forma parte actualmente del build.

---

# Clasificación

| Archivo | Estado actual |
| --- | --- |
| `constants.js` | Activo |
| `micorriza.js` | Activo como entry point; contenido de demo |
| `micorriza-admin.js` | Deshabilitado |
| `admin/file-upload.js` | Toolkit opcional, potencialmente reusable |
| `admin/featured-image.js` | Histórico y específico |
| `admin/gallery.js` | Toolkit opcional; dependencias pendientes |
| `admin/hex-color-text-input.js` | Toolkit opcional; contiene bug |
| `admin/sortable-tables.js` | Toolkit opcional; requiere sortable |

---

# Posible criterio para el blank moderno

Antes de entregar el theme a clientes conviene separar claramente:

```text
código necesario para que el blank funcione
```

de:

```text
ejemplos / toolkit opcional de desarrollo interno
```

La carpeta `es6/admin/` cae actualmente en la segunda categoría.

Una decisión futura puede ser:

```text
A. modernizarla y conectarla oficialmente;
B. moverla a documentación/snippets internos;
C. retirarla del boilerplate entregable.
```

La revisión actual no impone todavía ninguna de las tres opciones.
