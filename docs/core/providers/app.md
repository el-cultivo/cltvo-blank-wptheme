# AppServiceProvider
**Estado:** En revisión

## Descripción general

`AppServiceProvider` administra configuraciones base declaradas en:

```text
config/app.php
```

Se encuentra en:

```text
app/Providers/AppServiceProvider.php
```

Su responsabilidad principal es inicializar elementos especiales definidos por configuración, como páginas, categorías y etiquetas que el blank theme necesita mantener disponibles.

Durante `boot()` procesa:

```text
special-pages
special-categories
special-tags
```

El flujo general es:

```text
config/app.php
    ↓
AppServiceProvider
    ↓
boot()
    ├── specialPages()
    ├── specialCategories()
    └── specialTags()
```

---

# Special Pages

Las páginas especiales se configuran desde `config/app.php`.

Ejemplo:

```php
'special-pages' => [
    'splash'   => ['Splash', ''],
    'home'     => ['Home', ''],
    'contacto' => ['Contacto', ''],
],
```

Cada elemento utiliza:

```text
slug => [título, slug del padre]
```

El segundo valor permite declarar relaciones jerárquicas entre páginas especiales.

Ejemplo conceptual:

```php
'special-pages' => [
    'servicios'   => ['Servicios', ''],
    'corporativo' => ['Corporativo', 'servicios'],
],
```

produce:

```text
Servicios
└── Corporativo
```

## Comportamiento

El provider verifica si la página configurada ya existe y conserva su relación mediante los IDs almacenados por el tema.

Cuando una página especial no existe, puede crearla. Si ya existe, el provider asegura principalmente que permanezca consistente con la configuración del blank theme.

Entre los valores que mantiene se encuentran:

```text
slug
post_status
post_parent
```

El título y el contenido de una página existente no se utilizan como valores que deban sobrescribirse continuamente, por lo que pueden editarse desde WordPress sin que el provider los sustituya en cada inicialización.

El flujo puede resumirse como:

```text
special-pages
    ↓
buscar página configurada
    ↓
¿existe?
    ├── no → crear página
    └── sí → validar configuración
                ├── slug
                ├── estado
                └── parent
```

---

## IDs de páginas especiales

El provider mantiene una referencia a las páginas especiales mediante la opción:

```php
special_pages_ids
```

y la hace disponible globalmente mediante:

```php
$GLOBALS['special_pages_ids']
```

Esto permite que otras partes del blank theme utilicen los IDs configurados sin depender de valores hardcodeados.

Por ejemplo, una implementación puede consultar el ID correspondiente a una página definida como:

```text
home
contacto
splash
```

sin asumir cuál es su ID real en la base de datos.

---

# Special Categories

Las categorías especiales se declaran en:

```php
'special-categories' => [
    // ...
],
```

El provider verifica si los términos configurados existen y crea los que hagan falta.

El flujo general es:

```text
special-categories
    ↓
¿existe el término?
    ├── sí → conservar
    └── no → wp_insert_term()
```

---

# Special Tags

Las etiquetas especiales siguen el mismo principio mediante:

```php
'special-tags' => [
    // ...
],
```

El provider verifica la existencia de los términos configurados y crea los faltantes.

---

## Cuándo utilizar estas configuraciones

Las configuraciones `special-pages`, `special-categories` y `special-tags` resultan útiles cuando una funcionalidad del tema depende de la existencia de determinados contenidos estructurales.

Ejemplos:

- una página que funciona como Home;
- una página necesaria para una plantilla o flujo específico;
- una categoría que el código consulta de forma recurrente;
- un término requerido por alguna funcionalidad del tema.

No deben utilizarse para crear contenido editorial normal del sitio.

---

## Flujo general

```text
config/app.php
    ↓
AppServiceProvider
    ↓
boot()
    ├── specialPages()
    │       ↓
    │   páginas estructurales
    │       ↓
    │   special_pages_ids
    │
    ├── specialCategories()
    │       ↓
    │   términos requeridos
    │
    └── specialTags()
            ↓
        términos requeridos
```

---

## Consideraciones

- Las páginas especiales forman parte de la configuración estructural del tema.
- El slug configurado funciona como identificador dentro de `special-pages`.
- El segundo valor de cada página permite definir una página especial padre.
- Los IDs reales se almacenan para evitar depender de IDs hardcodeados entre ambientes.
- El contenido editorial de una página existente puede modificarse sin utilizar `config/app.php` como fuente de contenido.
- Las categorías y etiquetas especiales deben reservarse para términos que realmente sean requeridos por el código.
