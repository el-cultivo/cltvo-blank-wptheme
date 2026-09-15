# Framework
**Estado:** En revisión

## Descripción general

El blank theme contiene una capa propia dentro de:

```text
framework/src/Illuminate/
```

Esta capa proporciona abstracciones reutilizables para distintos proyectos del Cultivo.

No corresponde al framework Laravel. Aunque utiliza nombres como `Illuminate`, `Application`, `ServiceProvider` o `Mailable`, la implementación pertenece al blank theme.

---

## Autoload mediante Composer

El framework se registra mediante `composer.json`.

Composer utiliza PSR-4 para mapear:

```json
"psr-4": {
    "Illuminate\\": "framework/src/Illuminate/",
    "App\\": "app/"
}
```

Esto establece la separación:

```text
Illuminate\
    ↓
framework/src/Illuminate/
    ↓
infraestructura reutilizable del blank theme

App\
    ↓
app/
    ↓
implementación propia del proyecto
```

El autoloader se activa desde:

```text
bootstrap/autoload.php
```

mediante:

```php
require __DIR__.'/../vendor/autoload.php';
```

A partir de ese momento las clases registradas bajo ambos namespaces pueden resolverse sin `require` manual.

---

## Estructura actual

```text
Illuminate/
├── Contracts/
├── Foundation/
│   └── Application.php
├── Image/
│   └── Image.php
├── Mail/
│   ├── Mail.php
│   ├── Mailable.php
│   ├── MailableMailer.php
│   ├── Mailer.php
│   └── SMTPMailer.php
├── Support/
│   ├── ServiceProvider.php
│   └── helpers.php
├── Ajax.php
├── Controller.php
├── CustomPostType.php
├── Metabox.php
├── Page.php
├── Post.php
├── PostType.php
├── Taxonomy.php
└── Term.php
```

---

## Componentes ya documentados

```text
Illuminate\Ajax             → AJAX
Illuminate\Controller       → Controllers
Illuminate\CustomPostType   → Custom Post Types
Illuminate\Taxonomy         → Taxonomías
Illuminate\Metabox          → Metaboxes
Illuminate\Support\ServiceProvider
                             → Providers / Bootstrap
```

Este documento funciona como mapa general y no duplica el detalle de esos sistemas.

---

# Foundation

`Illuminate\Foundation\Application` conecta:

```text
bootstrap
    ↓
config/app.php
    ↓
providers
```

Carga configuración y ejecuta `boot()` sobre cada provider declarado.

Ver [Bootstrap](./bootstrap.md).

---

# Contracts

La carpeta `Contracts/` contiene interfaces para:

```text
Ajax
Controller
CustomPostType
Metabox
Taxonomy
```

Describen los métodos que deben exponer las implementaciones base.

El contrato de CPT, por ejemplo, conserva la firma real:

```php
static function registerPostype();
```

El typo `registerPostype()` forma parte de la implementación actual.

---

# Support

## `ServiceProvider`

Entrega la instancia de `Application` a los providers mediante `$this->app`.

No implementa un contenedor de dependencias ni un sistema adicional de resolución.

## `helpers.php`

El archivo declara explícitamente:

```text
No agregar funciones del tema
```

Contiene utilidades globales del framework/Cultivo, entre ellas:

- Special Pages;
- imágenes;
- fechas;
- términos;
- formato de dinero;
- detección móvil;
- `toSnakeCase()`;
- utilidades de consultas.

Esto lo diferencia de:

```text
app/helpers.php
```

que queda reservado para helpers globales propios de cada proyecto.

Composer carga este archivo automáticamente mediante la sección:

```json
"autoload": {
    "files": [
        "framework/src/Illuminate/Support/helpers.php",
        "app/helpers.php"
    ]
}
```

Por lo tanto, tanto los helpers reutilizables del framework como los helpers globales del proyecto se encuentran disponibles durante la carga del theme sin requerir `include` o `require` manual.

---

# PostType, Post, Page y Term

## `PostType`

`Illuminate\PostType` funciona como wrapper de un `WP_Post`.

Durante su construcción carga:

```text
post
    ↓
permalink + ID
    ↓
setMetas()
    ↓
setImages()
    ↓
setTerms()
```

También ofrece imagen destacada, attachments, términos y un `find()` mediante `WP_Query`.

`setMetas()` es abstracto.

## `Post` y `Page`

Extienden `PostType`, pero actualmente su implementación de `setMetas()` está vacía.

## `Term`

Actualmente sólo expone:

```php
Term::find($id, $taxonomy)
```

mediante `get_term_by()`.

Estas clases deben considerarse auxiliares existentes; antes de ampliarlas conviene confirmar si siguen utilizándose en proyectos recientes.

---

# Image

`Illuminate\Image\Image` encapsula metadata de attachments.

Obtiene:

- URL completa;
- URLs de tamaños;
- ancho y alto;
- alt;
- título;
- proporción.

También expone:

```php
getImgSrc($size)
```

con fallback a la imagen full.

La implementación asume que `metadata['sizes']` existe cuando hay metadata; vale la pena endurecer este caso si el componente sigue vigente.

---

# Mail

La carpeta `Illuminate/Mail/` implementa un sistema propio para construir y enviar correos.

El flujo actual pasa por `Mailable`, que consulta:

```php
get_theme_support('CLTVO_USEMAILGUN');
```

y elige entre `Mailer` y `SMTPMailer`.

## Problemas detectados

### `Mailer`

El código consulta variables locales `$cc` y `$bcc` que no están definidas dentro del método. Los datos correspondientes existen en:

```php
$mailable->cc
$mailable->bcc
```

El manejo de CC/BCC debe revisarse.

### `SMTPMailer`

La clase contiene credenciales SMTP hardcodeadas.

Estas credenciales deben considerarse expuestas: deben rotarse y retirarse del código. Si fueron versionadas, también conviene eliminarlas del historial cuando sea viable.

La configuración futura debería provenir de variables de entorno o de un mecanismo seguro de configuración.

También conviene revisar:

- uso de `utf8_decode()`;
- ausencia de un `return` explícito al enviar;
- valores históricos de host/remitente dentro del framework;
- el nombre `CLTVO_USEMAILGUN`, que no describe claramente el flujo actual.

Por estas razones `Mail/` es candidato prioritario a refactor antes de presentarlo como patrón recomendado.

---

## Clasificación general

| Área | Estado |
| --- | --- |
| `Foundation/Application` | Core vigente |
| `Support/ServiceProvider` | Core vigente |
| `Contracts` | Infraestructura vigente |
| `Ajax`, `Controller`, `CustomPostType`, `Taxonomy`, `Metabox` | Vigentes; documentados por sistema |
| `Support/helpers.php` | Utilidades framework; revisar progresivamente |
| `PostType`, `Post`, `Page`, `Term` | Confirmar uso antes de ampliar |
| `Image` | Utilidad existente; confirmar uso |
| `Mail` | Refactor prioritario |

---

## Regla de separación

```text
framework/
    ↓
comportamiento reutilizable entre proyectos

app/
    ↓
implementación y helpers propios del proyecto
```

Antes de modificar una abstracción del framework debe evaluarse el impacto sobre proyectos que ya la extiendan o consuman.
