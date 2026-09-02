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
| `MetaboxServiceProvider` | Configuración de metaboxes. |
| `ScriptsServiceProvider` | Registro y carga de JavaScript. |
| `StylesServiceProvider` | Registro y carga de estilos. |
| `SupportServiceProvider` | Configuración de soportes del tema. |
| `TaxonomyServiceProvider` | Registro de taxonomías. |
| `OptionsServiceProvider` | Configuración relacionada con opciones globales. |

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

En `CustomPostTypeServiceProvider` y `TaxonomyServiceProvider`, `register()` se encuentra disponible pero actualmente no contiene lógica.

`boot()` es el método que realiza la inicialización de los elementos configurados en cada provider.

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

Ver [Custom Post Types](./custom-post-types.md).

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

Ver [Taxonomías](./taxonomies.md).

---

## Relación con `config/app.php`

Los dos providers anteriores también deben estar registrados dentro del arreglo global:

```php
'providers' => [
    // ...
    App\Providers\CustomPostTypeServiceProvider::class,
    // ...
    App\Providers\TaxonomyServiceProvider::class,
    // ...
],
```

De esta forma existen dos niveles de configuración:

```text
config/app.php
    ↓
define qué Providers utiliza el tema

Provider
    ↓
define qué componentes de esa responsabilidad se registran
```

Por ejemplo:

```text
config/app.php
    ↓
CustomPostTypeServiceProvider
    ↓
\App\ExamplePostType
```

---

## Agregar componentes a providers existentes

Cuando se crea un CPT o una taxonomía no es necesario crear un provider nuevo.

Debe registrarse en el provider correspondiente.

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
CustomPostTypeServiceProvider
    ↓
registerPostype()
    ↓
register_post_type()

TaxonomyServiceProvider
    ↓
registerTaxonomy()
    ├── register_taxonomy()
    └── términos iniciales
```

---

## Convenciones

- Mantener cada provider enfocado en una responsabilidad.
- Registrar los providers globales desde `config/app.php`.
- Registrar CPT y taxonomías desde sus providers existentes.
- Evitar llevar inicializaciones a `functions.php` cuando ya existe un provider responsable.
- Mantener la configuración particular dentro de la clase del componente y utilizar el provider únicamente como punto de registro.
- Documentar las funcionalidades específicas de cada proyecto fuera de la documentación base del boilerplate.
