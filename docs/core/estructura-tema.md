# Estructura del tema

**Estado:** En revisión  

---

## Objetivo

Documentar la estructura actual del Blank Theme de WordPress utilizado para la programación de custom themes.

La estructura descrita en este documento corresponde al estado actual del repositorio y deberá mantenerse actualizada cuando se agreguen, eliminen o reorganicen componentes relevantes.

---

## Estructura general

```text
cltvo-blank-wptheme/
├── acf-json/
├── admin/
├── app/
│   ├── Http/
│   ├── Mail/
│   ├── Metaboxes/
│   ├── Providers/
│   └── Taxonomies/
├── bootstrap/
├── config/
├── data/
├── docs/
├── es6/
│   └── admin/
├── fonts/
├── framework/
│   └── src/
├── images/
│   ├── favicon/
│   ├── map/
│   └── svg/
├── inc/
├── includes/
│   ├── custom/
│   ├── performance/
│   ├── plugins/
│   └── tgm_plugin_activation/
├── js/
│   ├── function.js/
│   └── function.js.map/
├── languages/
├── mail/
│   ├── contact.php/
│   └── layout.php/
├── sass/
│   └── mazorca/
├── views/
│   └── general/
│       ├── header.php/
│       └── footer.php/
├── 404.php
├── archive.php
├── footer.php
├── functions.php
├── header.php
├── index.php
├── style.css
├── webpack.mix.js
├── package.json
├── composer.json
└── ...
```

---

## Directorios principales

### `acf-json/`

Contiene la configuración exportada por ACF Local JSON.

Permite versionar los grupos de campos de Advanced Custom Fields dentro del repositorio y mantener consistencia entre ambientes.

Consideraciones:

- Los archivos JSON deberán permanecer versionados.
- Los cambios realizados en ACF deberán revisarse antes de desplegarse entre ambientes.
- No deberán modificarse manualmente salvo que exista una razón técnica documentada.

---

### `app/`

Contiene una parte importante de la lógica de negocio y registro de componentes personalizados del tema.

Actualmente se identifican, entre otros:

```text
app/Contacto.php
app/helpers.php
```

También contiene subdirectorios para responsabilidades específicas:

```text
app/Http/
app/Mail/
app/Metaboxes/
app/Providers/
app/Taxonomies/
```

El objetivo de esta separación es evitar concentrar toda la lógica del proyecto dentro de `functions.php`.

---

### `bootstrap/`

Contiene archivos relacionados con la carga e inicialización de la aplicación y del framework utilizado por el tema.

Actualmente incluye:

```text
bootstrap/app.php
bootstrap/autoload.php
```

Estos archivos forman parte del proceso de inicialización del tema y deberán modificarse únicamente cuando sea necesario ajustar el mecanismo de carga de dependencias o componentes.

---

### `config/`

Centraliza configuraciones del tema.

Actualmente incluye:

```text
config/app.php
config/conf_plugins.php
config/options_pages.php
config/required_plugins.php
```

Entre sus responsabilidades se encuentran la configuración general de la aplicación, plugins requeridos y páginas de opciones.

Las configuraciones específicas de ambiente no deberán incluir credenciales o información sensible versionada dentro del repositorio.

---

### `docs/`

Contiene la documentación técnica del proyecto.

Los cambios relevantes en arquitectura, seguridad, performance, despliegue o dependencias deberán acompañarse de la actualización correspondiente en esta carpeta.

---

### `es6/`

Contiene el código fuente JavaScript utilizado por el frontend y por algunas funcionalidades administrativas.

Actualmente existen archivos separados por sección o funcionalidad, por ejemplo:

```text
es6/micorriza.js
```

El código fuente deberá mantenerse separado por responsabilidad y compilarse mediante el proceso definido en `webpack.mix.js`.

---

### `fonts/`

Contiene las fuentes utilizadas por el sitio.

---

### `framework/`

Contiene código base o componentes del framework interno utilizado por el proyecto.

Actualmente incluye:

```text
framework/src/
framework/src/Illuminate/
```

Debido a que este directorio contiene infraestructura compartida, cualquier modificación deberá realizarse con precaución para evitar afectar componentes dependientes.

---

### `images/`

Contiene recursos gráficos pertenecientes al tema.

Las imágenes administrables de contenido no deberán almacenarse aquí, sino mediante la biblioteca de medios de WordPress o el mecanismo definido para archivos administrables.

---

### `inc/`

Contiene archivos auxiliares cargados por el tema.

Actualmente se identifican:

```text
inc/anaylitics.php
inc/favicon.php
```

Este directorio deberá revisarse junto con `includes/`, ya que existen responsabilidades similares y archivos duplicados por nombre.

---

### `includes/`

Contiene funcionalidades adicionales organizadas por responsabilidad.

Actualmente existen:

```text
includes/custom/
includes/plugins/
includes/tgm_plugin_activation/
```
La carpeta `includes/plugins/` contiene integraciones o configuraciones asociadas con plugins.

---

### `js/`

Contiene JavaScript compilado y recursos de ejecución en frontend.

Actualmente incluye:

```text
js/functions.js
js/functions.js.map
```

`functions.js` corresponde al bundle generado a partir del código fuente.

Los archivos generados deberán modificarse mediante el proceso de compilación y no editarse manualmente.

---

### `languages/`

Contiene archivos de traducción del tema.

Actualmente:

```text
languages/en_US.po
languages/en_US.mo
```

Los archivos `.po` contienen las cadenas traducibles y los `.mo` corresponden a la versión compilada utilizada por WordPress.

---

### `mail/`

Contiene plantillas relacionadas con correo electrónico.

Actualmente:

```text
mail/contact.php
mail/layout.php
```

Las plantillas deberán mantener separada la presentación del correo respecto a la lógica de envío.

---

### `sass/`

Contiene los estilos fuente del proyecto.

La estructura principal se encuentra dentro de:

```text
sass/mazorca/
```

con componentes organizados por responsabilidad:

```text
components/
config/
elements/
grid/
modules/
```

El archivo principal identificado es:

```text
sass/mazorca.scss
```

Los estilos deberán modificarse desde los archivos fuente y compilarse al CSS final.

---

### `views/`

Contiene vistas y fragmentos de presentación organizados por sección del sitio.

---

## Archivos principales del tema

### `functions.php`

Punto principal de carga de funcionalidades del tema.

Deberá utilizarse principalmente para registrar o requerir módulos, evitando concentrar implementaciones completas dentro del archivo.

---

### `header.php` y `footer.php`

Plantillas globales utilizadas por WordPress para construir el encabezado y pie del sitio.

---

### `index.php`

Plantilla fallback requerida por WordPress.

---

### `style.css`

Archivo requerido por WordPress para identificar el tema y archivo CSS compilado utilizado por el frontend.

Actualmente también existe:

```text
style.css.map
```

---

### `webpack.mix.js`

Define el proceso de compilación de JavaScript y estilos del proyecto.

La configuración deberá mantenerse alineada con las versiones soportadas por el entorno de desarrollo.

---

### `package.json`

Define las dependencias JavaScript utilizadas durante el desarrollo y compilación del tema.

El archivo `package-lock.json` deberá mantenerse versionado para asegurar instalaciones reproducibles.

---

### `composer.json`

Define las dependencias PHP utilizadas por el tema.

El archivo `composer.lock` deberá mantenerse versionado para asegurar que todos los ambientes utilicen las mismas versiones de dependencias.

---

## Templates de WordPress

El tema incluye templates estándar y específicos para las diferentes secciones del sitio.

Entre ellos:

```text
404.php
archive.php
page.php
search.php
single.php
```

También existe page template:

```text
template-page.php
```

Estos archivos funcionan como punto de entrada para cada tipo de página y podrán delegar la presentación a vistas dentro de `views/`.

---

## Flujo general

De forma general, la arquitectura del tema sigue el siguiente flujo:

```text
WordPress
   ↓
Template / Single / Archive
   ↓
Lógica y configuración del tema
   ↓
app/ + includes/ + config/
   ↓
views/
   ↓
HTML renderizado
   ↓
CSS / JavaScript compilado
```

El flujo exacto puede variar dependiendo de cada sección.

---

## Assets y compilación

El código fuente se mantiene principalmente en:

```text
es6/
sass/
```

Los archivos generados se encuentran en:

```text
js/
style.css
```

El proceso de compilación está definido mediante:

```text
webpack.mix.js
```

y sus dependencias están declaradas en:

```text
package.json
```

No deberán realizarse modificaciones directas sobre archivos compilados si existe un archivo fuente correspondiente.

---

## Consideraciones de mantenimiento

### Archivo `ExamplePostType.php`

Se encuentra identificado:

```text
app/ExamplePostType.php
```

Deberá validarse si sigue siendo utilizado por el proyecto o corresponde a código de ejemplo heredado.

Si no tiene uso, podrá considerarse para eliminación como parte de la limpieza técnica del tema.

---

### Source maps

Se identificaron:

```text
js/functions.js.map
style.css.map
```

Deberá definirse si los source maps se mantienen únicamente para Development o si también se publican en Staging y Production.

La decisión deberá considerar necesidades de depuración y criterios de exposición del código fuente.

---

## Criterios generales

La estructura del tema deberá mantenerse bajo los siguientes criterios:

- Separar lógica, configuración y presentación.
- Mantener los componentes organizados por responsabilidad.
- Evitar código duplicado.
- Evitar dependencias innecesarias.
- Evitar lógica extensa dentro de templates.
- Mantener versionados los archivos fuente.
- No modificar directamente archivos compilados.
- Mantener la documentación actualizada.
- Priorizar compatibilidad con WordPress y WP Engine.
- Considerar performance y seguridad en cualquier nueva dependencia o funcionalidad.

---

## Pendientes de validación

- Documentar el proceso exacto de bootstrap del tema.
- Documentar la función específica de `framework/`.
- Documentar el proceso completo de compilación y sus comandos.
