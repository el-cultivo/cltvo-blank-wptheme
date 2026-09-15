# Custom Post Types
**Estado:** En revisión 

## Descripción general

El blank theme incluye una abstracción para registrar **Custom Post Types (CPT)** de WordPress de forma centralizada.

La clase base se encuentra en:

```text
framework/Illuminate/CustomPostType.php
```

Los CPT específicos de cada proyecto se crean dentro de `app/` extendiendo:

```php
Illuminate\CustomPostType
```

El blank theme incluye `app/ExamplePostType.php` como referencia de implementación.

---

## Clase base `CustomPostType`

`Illuminate\CustomPostType` extiende `PostType` e implementa `Illuminate\Contracts\CustomPostType`.

La clase define valores por defecto que pueden sobrescribirse desde cada CPT:

| Configuración | Default | Descripción |
| --- | --- | --- |
| `nombre_plural` | `Cltvo_PostTypeCustom` | Nombre plural utilizado en los labels del administrador. |
| `nombre_singular` | `Cltvo_PostTypeCustom` | Nombre singular utilizado en los labels del administrador. |
| `slug` | `Cltvo_PostTypeCustom` | Slug utilizado en la regla `rewrite`. |
| `publico` | `true` | Valor enviado a `public`. |
| `publicly_queryable` | `true` | Permite consultas públicas del Post Type. |
| `show_ui` | `true` | Muestra interfaz de administración. |
| `show_in_menu` | `true` | Muestra el CPT en el menú del administrador. |
| `query_var` | `true` | Habilita su query var. |
| `capability_type` | `post` | Tipo de capacidades utilizado. |
| `has_archive` | `true` | Habilita archivo para el CPT. |
| `hierarchical` | `false` | Define si el contenido es jerárquico. |
| `menu_position` | `6` | Posición del CPT en el menú del administrador. |
| `$supports` | `['title', 'editor']` | Funcionalidades nativas de WordPress habilitadas. |
| `$taxonomies` | `[]` | Taxonomías asociadas desde los argumentos del CPT. |
| `$menu_icon` | `''` | Icono utilizado en el administrador. |

---
## Nombre registrado del Custom Post Type

El nombre con el que WordPress registra el Custom Post Type se genera automáticamente a partir del **nombre de la clase**.

El método:

```php
public static function classname()
```

obtiene el nombre de la clase que realizó la llamada mediante `get_called_class()`, toma su nombre corto y lo convierte utilizando:

```php
toSnakeCase($shortname)
```

Por ejemplo, para:

```php
class ExamplePostType extends CustomPostType
```

el identificador interno utilizado por `register_post_type()` será el resultado de:

```php
toSnakeCase('ExamplePostType')
```

Por ejemplo:

| Clase | Nombre registrado |
| --- | --- |
| `ExamplePostType` | `example_post_type` |
| `Example` | `example` |

Este es el nombre que debe utilizarse posteriormente para consultar o referenciar el Post Type:

```php
'post_type' => 'example_post_type'
```

## Slug registrado del Custom Post Type

Por otro lado:

```php
const slug = 'ejemplo-post-type';
```

se utiliza exclusivamente dentro de:

```php
'rewrite' => ['slug' => static::slug]
```

Por esta razón, el nombre de la clase y el `slug` cumplen funciones diferentes.

---

## Registro del Post Type

El método público de registro es:

```php
static function registerPostype()
```

Este método construye los labels y argumentos del CPT a partir de las constantes y propiedades de la clase.

Finalmente ejecuta:

```php
static::registerPostypeHook($args);
```

`registerPostypeHook()` es un método `final` que registra el Post Type en WordPress:

```php
register_post_type(static::classname(), $args);
```

---

## Labels generados

La clase base genera automáticamente los labels principales utilizando `nombre_plural` y `nombre_singular`, incluyendo:

- nombre del contenido;
- nombre del menú;
- crear;
- editar;
- ver;
- buscar;
- listado general;
- mensajes cuando no existen resultados;
- mensajes de papelera.

Esto evita repetir la configuración completa de labels en cada CPT.

---

## Crear un Custom Post Type

El ejemplo incluido en el blank theme es:

```php
namespace App;

use Illuminate\CustomPostType;

class ExamplePostType extends CustomPostType
{
    const nombre_plural = 'Ejemplo PostType';
    const nombre_singular = 'ejemplo PostType';
    const slug = 'ejemplo-post-type';

    protected static $supports = ['title', 'editor', 'thumbnail'];
    protected static $menu_icon = 'dashicons-laptop';

    public function setMetas()
    {
    }
}
```

Para crear un nuevo CPT:

1. Crear una clase dentro de `app/`.
2. Extender `Illuminate\CustomPostType`.
3. Definir `nombre_plural`, `nombre_singular` y `slug`.
4. Sobrescribir únicamente las propiedades de configuración necesarias.
5. Registrar la clase en `CustomPostTypeServiceProvider`.

Por ejemplo:

```php
protected $posttypes = [
    \App\ExamplePostType::class,
];
```

---

## `CustomPostTypeServiceProvider`

El registro centralizado se realiza desde:

```text
app/Providers/CustomPostTypeServiceProvider.php
```

El provider contiene el arreglo:

```php
protected $posttypes = [
    /**\App\ExamplePostType::class,**/
];
```

Durante `boot()` recorre las clases configuradas:

```php
public function boot()
{
    foreach ($this->posttypes as $file) {
        $file::registerPostype();
    }
}
```

Por lo tanto, crear la clase no es suficiente: debe agregarse al provider para que sea registrada durante la inicialización del tema.

---

## Flujo

```text
config/app.php
    ↓
CustomPostTypeServiceProvider
    ↓
$posttypes
    ↓
Clase que extiende Illuminate\CustomPostType
    ↓
registerPostype()
    ↓
registerPostypeHook()
    ↓
register_post_type()
```

---

## Consideraciones

- `slug` controla el `rewrite`, no el identificador interno del Post Type.
- El identificador interno se genera a partir del nombre de la clase mediante `classname()`.
- Los valores de la clase base pueden sobrescribirse en cada implementación.
- `$supports` permite habilitar elementos como `title`, `editor` y `thumbnail`.
- `$taxonomies` permite enviar taxonomías asociadas en los argumentos de `register_post_type()`.
- `$menu_icon` acepta el valor utilizado por WordPress para el icono del CPT.
- `setMetas()` aparece en el CPT de ejemplo, pero no forma parte del flujo de registro mostrado por `Illuminate\CustomPostType`; su uso depende de otras partes de la arquitectura del tema.

Los CPT concretos y su lógica editorial deben documentarse dentro de cada proyecto.
