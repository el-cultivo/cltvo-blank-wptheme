# OptionsServiceProvider
**Estado:** En revisión

## Descripción general

`OptionsServiceProvider` registra páginas de opciones asociadas a determinados Post Types mediante ACF PRO.

Se encuentra en:

```text
app/Providers/OptionsServiceProvider.php
```

Su configuración se obtiene desde:

```text
config/options_pages.php
```

El provider depende de funciones de ACF como:

```php
acf_add_options_page()
acf_add_local_field_group()
```

Por lo tanto, esta funcionalidad solo se inicializa cuando ACF está disponible.

---

# Configuración

El archivo:

```text
config/options_pages.php
```

devuelve un arreglo donde cada key representa un Post Type.

Ejemplo real:

```php
return [
    'bibliography' => [
        // configuración de Field Groups
    ],
    'conversation' => [
        // configuración de Field Groups
    ],
];
```

El provider recorre:

```php
foreach ($config as $post_type => $groups)
```

y crea una Options Page para cada Post Type configurado.

---

# Página de opciones por Post Type

Para cada elemento se registra una página con:

```php
acf_add_options_page()
```

La configuración utilizada es:

```text
page_title  → Opciones
menu_title  → Opciones
parent_slug → edit.php?post_type={post_type}
menu_slug   → {post_type}-options
post_id     → {post_type}-options
```

Esto hace que la página aparezca como hija del menú del Post Type correspondiente.

Conceptualmente:

```text
Post Type
    └── Opciones
```

Ejemplo para:

```text
bibliography
```

se genera:

```text
edit.php?post_type=bibliography
    └── Opciones
```

y los valores se almacenan utilizando como `post_id`:

```text
bibliography-options
```

---

# Field Groups registrados

Después de crear la página, el provider ejecuta:

```php
$this->add_option_fields($post_type, $groups);
```

y registra dos Field Groups locales mediante:

```php
acf_add_local_field_group()
```

## Grupo principal

El primer grupo utiliza:

```text
position → normal
```

e incluye los campos:

```text
title
description
subspaces
```

`subspaces` es un repeater con:

```text
title
description
```

La ubicación del grupo se limita a:

```text
options_page == {post_type}-options
```

---

## Imagen destacada

El segundo Field Group utiliza:

```text
position → side
```

e incluye un campo:

```text
thumbnail
```

de tipo:

```text
image
```

También se limita a:

```text
options_page == {post_type}-options
```

---

# Importancia de las keys

`config/options_pages.php` no define los campos completos.

El provider contiene la estructura de los Field Groups y el archivo de configuración aporta principalmente las keys únicas de ACF.

La estructura esperada es conceptualmente:

```text
post_type
    ↓
groups[0]
    ├── key
    └── fields
        ├── title key
        ├── description key
        └── subspaces key
            └── subfields
                ├── title key
                └── description key

groups[1]
    ├── key
    └── fields
        └── thumbnail key
```

Estas keys deben mantenerse únicas para evitar conflictos entre Field Groups y campos registrados mediante ACF.

---

# Lectura de valores

Debido a que cada página utiliza:

```php
'post_id' => $post_type . '-options'
```

los valores deben consultarse utilizando ese mismo identificador.

Ejemplo conceptual:

```php
get_field('title', 'bibliography-options');
get_field('thumbnail', 'bibliography-options');
```

La key del Post Type y el `post_id` generado forman parte del contrato de esta funcionalidad.

---

# Relación con la integración ACF

`OptionsServiceProvider` utiliza ACF, pero cumple una responsabilidad distinta al flujo documentado de Local JSON.

La integración general de ACF y su sincronización continúa documentada en:

```text
docs/integrations/acf.md
```

Este provider registra Field Groups directamente desde PHP mediante:

```php
acf_add_local_field_group()
```

por lo que estos grupos no deben confundirse con Field Groups administrados exclusivamente desde `acf-json`.

---

# ¿Cuándo utilizar este provider?

Esta implementación es útil cuando varios Post Types necesitan exactamente la misma estructura de opciones:

```text
Título
Descripción
Subespacios
Imagen destacada
```

pero con keys independientes para cada uno.

En vez de repetir la definición completa del Field Group para cada Post Type, el provider mantiene una estructura común y `config/options_pages.php` proporciona las keys particulares.

No es un sistema genérico para crear cualquier tipo de Options Page: la estructura de campos está definida directamente en `OptionsServiceProvider`.

Si un proyecto necesita una Options Page con campos completamente diferentes, debe evaluarse si conviene:

- ampliar este provider de forma explícita;
- crear otra abstracción;
- o administrar esa Options Page mediante el flujo habitual de ACF.

---

## Flujo general

```text
config/options_pages.php
    ↓
OptionsServiceProvider
    ↓
boot()
    ↓
¿ACF disponible?
    ├── no → no registra opciones
    └── sí
         ↓
    por cada Post Type
         ↓
    acf_add_options_page()
         ↓
    {post_type}-options
         ↓
    add_option_fields()
         ├── grupo principal
         │   ├── title
         │   ├── description
         │   └── subspaces
         └── grupo lateral
             └── thumbnail
```

---

## Consideraciones

- Requiere ACF PRO para las Options Pages y el repeater utilizado.
- La configuración actual está orientada a una estructura de campos específica.
- Las keys de `config/options_pages.php` deben ser únicas.
- Los valores se guardan bajo un `post_id` independiente por Post Type.
- Los grupos se registran desde PHP mediante `acf_add_local_field_group()`.
- No debe asumirse que estos Field Groups siguen el mismo flujo que los grupos mantenidos mediante `acf-json`.
- Si esta funcionalidad no se utiliza en proyectos actuales, conviene conservarla documentada como capacidad del boilerplate antes de decidir si debe mantenerse o deprecarse.
