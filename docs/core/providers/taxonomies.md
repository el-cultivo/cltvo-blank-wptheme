# Taxonomías
**Estado:** En revisión 

## Descripción general

El blank theme incluye una abstracción para registrar **taxonomías personalizadas** de WordPress.

La clase base se encuentra en:

```text
framework/Illuminate/Taxonomy.php
```

Las taxonomías específicas se crean dentro de:

```text
app/Taxonomies/
```

extendiendo:

```php
Illuminate\Taxonomy
```

---

## Clase base `Taxonomy`

`Illuminate\Taxonomy` implementa `Illuminate\Contracts\Taxonomy` y define la configuración común para las taxonomías del tema.

Sus valores por defecto son:

| Configuración | Default | Descripción |
| --- | --- | --- |
| `nombre_plural` | `Cltvo_Taxonomy` | Nombre plural utilizado en los labels. |
| `nombre_singular` | `Cltvo_Taxonomy` | Nombre singular utilizado en los labels. |
| `slug` | `Cltvo_Taxonomy` | Slug utilizado para `rewrite`. |
| `hierarchical` | `true` | Define si la taxonomía es jerárquica. |
| `show_ui` | `true` | Habilita la interfaz de administración. |
| `query_var` | `true` | Habilita query vars. |
| `show_admin_column` | `true` | Muestra la taxonomía como columna en el administrador. |
| `$postypes` | `['post']` | Post Types asociados a la taxonomía. |
| `$initialTerms` | `[]` | Términos que deben existir inicialmente. |

---

## Nombre interno y slug

Al igual que los Custom Post Types, el nombre interno de una taxonomía **no se obtiene de `slug`**.

El método:

```php
public static function classname()
```

obtiene el nombre corto de la clase y lo transforma mediante:

```php
toSnakeCase($shortname)
```

Por ejemplo:

```php
class ExampleTaxonomie extends Taxonomy
```

utilizará como identificador interno el resultado de:

```php
toSnakeCase('ExampleTaxonomie')
```

Por ejemplo:

| Clase | Nombre registrado |
| --- | --- |
| `ExampleTaxonomie` | `example_taxonomie` |
| `Example` | `example` |


El valor:

```php
const slug = 'ejemplo-taxonomia';
```

se utiliza en:

```php
'rewrite' => ['slug' => static::slug]
```

Por lo tanto, el nombre de la clase determina el identificador utilizado por WordPress y `slug` determina la URL de rewrite.

---

## Registro de la taxonomía

El método principal es:

```php
static function registerTaxonomy()
```

Este método:

1. construye los labels;
2. construye los argumentos de registro;
3. ejecuta `registerTaxonomyHook()`;
4. ejecuta `setInitialTerms()`.

El registro final se realiza mediante:

```php
register_taxonomy(
    static::classname(),
    static::$postypes,
    $args
);
```

---

## Asociación con Post Types

Los Post Types asociados se definen mediante:

```php
protected static $postypes = ['post'];
```

Cada implementación puede sobrescribir este arreglo.

Por ejemplo:

```php
protected static $postypes = [
    'post',
    'example_post_type',
];
```

Los valores deben corresponder a los identificadores internos de los Post Types con los que se relacionará la taxonomía.

> En el framework la propiedad se llama `$postypes`, conservando esa escritura exacta.

---

## Términos iniciales

Una taxonomía puede declarar términos que deben existir desde su registro:

```php
protected static $initialTerms = [
    'cine' => 'Cine'
];
```

Después de registrar la taxonomía, `registerTaxonomy()` ejecuta:

```php
static::setInitialTerms();
```

`setInitialTerms()` recorre `$initialTerms` utilizando la estructura:

```text
slug => nombre
```

Antes de crear cada término comprueba:

```php
term_exists($slug, static::classname())
```

Si el término no existe, lo registra mediante `wp_insert_term()` con el nombre y slug configurados.

Esto permite que una taxonomía incluya términos base sin duplicarlos cada vez que se inicializa el tema.

---

## Obtener términos

La clase base incluye el helper:

```php
static function getTerms(array $args = array())
```

que devuelve:

```php
get_terms(static::classname(), $args);
```

Esto permite consultar los términos de la taxonomía directamente desde su clase.

Por ejemplo:

```php
$terms = ExampleTaxonomie::getTerms();
```

También pueden enviarse argumentos:

```php
$terms = ExampleTaxonomie::getTerms([
    'hide_empty' => false,
]);
```

---

## Crear una taxonomía

El ejemplo del blank theme sigue esta estructura:

```php
namespace App\Taxonomies;

use Illuminate\Taxonomy;

class ExampleTaxonomie extends Taxonomy
{
    const nombre_plural = 'Ejemplo Taxonomías';
    const nombre_singular = 'Ejemplo taxonomía';
    const slug = 'ejemplo-taxonomia';

    protected static $postypes = [
    ];

    protected static $initialTerms = [
        'cine' => 'Cine'
    ];
}
```

Para agregar una nueva taxonomía:

1. Crear una clase dentro de `app/Taxonomies/`.
2. Extender `Illuminate\Taxonomy`.
3. Definir nombres y `slug`.
4. Configurar `$postypes`.
5. Configurar `$initialTerms` si requiere términos iniciales.
6. Registrar la clase en `TaxonomyServiceProvider`.

---

## `TaxonomyServiceProvider`

Las taxonomías activas se centralizan en:

```text
app/Providers/TaxonomyServiceProvider.php
```

El provider contiene:

```php
protected $taxonomies = [
    // \App\Taxonomies\ExampleTaxonomie::class,
];
```

Durante `boot()` ejecuta:

```php
public function boot()
{
    foreach ($this->taxonomies as $taxonomy) {
        $taxonomy::registerTaxonomy();
    }
}
```

---

## Flujo

```text
config/app.php
    ↓
TaxonomyServiceProvider
    ↓
$taxonomies
    ↓
Clase que extiende Illuminate\Taxonomy
    ↓
registerTaxonomy()
    ├── registerTaxonomyHook()
    │       ↓
    │   register_taxonomy()
    │
    └── setInitialTerms()
            ↓
        term_exists()
            ↓
        wp_insert_term()
```

---

## Consideraciones

- El identificador interno se obtiene del nombre de la clase mediante `classname()`.
- `slug` se utiliza únicamente para el `rewrite`.
- Por defecto las taxonomías son jerárquicas.
- Por defecto se asocian a `post`, salvo que `$postypes` sea sobrescrito.
- `show_admin_column` está habilitado por defecto.
- Los términos iniciales solo se insertan cuando `term_exists()` determina que no existen.
- `getTerms()` permite consultar los términos sin repetir manualmente el nombre interno de la taxonomía.

Las taxonomías concretas, sus términos editoriales y su uso en filtros o templates deben documentarse dentro del proyecto correspondiente.
