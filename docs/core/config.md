# Configuración
**Estado:** En revisión

## Descripción general

La carpeta:

```text
config/
```

centraliza parte de la configuración utilizada por el blank theme.

Actualmente contiene:

```text
config/
├── app.php
├── conf_plugins.php
├── options_pages.php
└── required_plugins.php
```

No todos los archivos tienen el mismo nivel de responsabilidad:

```text
app.php
    ↓
configuración principal de la aplicación

required_plugins.php
conf_plugins.php
    ↓
configuración de TGMPA

options_pages.php
    ↓
configuración histórica de Options Pages / ACF
```

---

# `app.php`

Se encuentra en:

```text
config/app.php
```

Es la configuración principal cargada directamente por:

```text
Illuminate\Foundation\Application
```

La aplicación conserva su contenido en:

```php
$this->config
```

---

## Providers

El arreglo:

```php
'providers' => [
    // ...
]
```

declara los providers que deben inicializarse.

Durante el bootstrap:

```text
config/app.php
    ↓
providers
    ↓
Application
    ↓
new Provider($this)
    ↓
boot()
```

El orden declarado también determina el orden en que `Application` ejecuta `boot()`.

Por lo tanto, no conviene modificar el orden de providers sin revisar si existe alguna dependencia entre ellos.

Ver [Providers](./providers.md).

---

## Special Pages

La configuración actual incluye:

```php
'special-pages' => [
    'splash' => [
        'Splash',
        ''
    ],
    'home' => [
        'Home',
        ''
    ],
    'contacto' => [
        'Contacto',
        ''
    ],
],
```

Cada elemento utiliza la estructura:

```text
slug interno
    ↓
[título, parent]
```

Ejemplo:

```php
'contacto' => [
    'Contacto',
    ''
]
```

Estas páginas son procesadas por `AppServiceProvider`.

Su finalidad es declarar contenido estructural requerido por el theme sin depender de IDs hardcodeados entre ambientes.

---

## Special Categories y Special Tags

También existen:

```php
'special-categories' => [],
'special-tags' => [],
```

`AppServiceProvider` puede utilizar estas configuraciones para crear términos estructurales requeridos por un proyecto.

Actualmente ambos arreglos se encuentran vacíos en el blank theme.

---

# `required_plugins.php`

Se encuentra en:

```text
config/required_plugins.php
```

Define los plugins que TGMPA debe registrar.

Actualmente incluye configuraciones para:

```text
Yoast SEO
Classic Editor
Post Types Order
Advanced Custom Fields Pro
WPML Multilingual CMS
Mailgun
```

Cada plugin puede definir propiedades como:

```text
name
slug
source
required
version
force_activation
force_deactivation
```

La configuración se consume desde `ActionsServiceProvider` durante:

```text
tgmpa_register
```

---

## Plugins incluidos dentro del theme

ACF PRO y WPML utilizan actualmente archivos ZIP ubicados dentro de:

```text
includes/plugins/
```

mediante:

```php
get_template_directory()
```

Esto permite a TGMPA instalar estos plugins desde el propio theme.

### Consideración

Las versiones declaradas actualmente corresponden a versiones históricas:

```text
ACF PRO 5.9.1
WPML 4.3.16
```

Antes de mantener este mecanismo como parte recomendada del blank theme conviene revisar:

- si esos ZIP siguen existiendo;
- si deben continuar versionándose dentro del theme;
- cómo se gestionan actualmente las licencias y actualizaciones;
- si las versiones declaradas siguen teniendo alguna utilidad real.

---

## `force_activation`

Varios plugins utilizan:

```php
'force_activation' => true
```

o una condición basada en:

```php
WP_DEBUG
```

Esto significa que TGMPA puede forzar que determinados plugins permanezcan activos.

Conviene revisar esta política antes de considerarla una convención general, especialmente para plugins opcionales o dependencias que ya no se utilicen en todos los proyectos.

---

## Mailgun

Mailgun está configurado actualmente como:

```text
required = true
force_activation = true en producción
```

Sin embargo, el framework también contiene lógica propia de correo y la bandera:

```text
CLTVO_USEMAILGUN
```

Por lo tanto, antes de documentar Mailgun como dependencia obligatoria debe revisarse el flujo de correo completo.

---

# `conf_plugins.php`

Se encuentra en:

```text
config/conf_plugins.php
```

Contiene la configuración general enviada a TGMPA.

Actualmente define propiedades como:

```text
id
menu
parent_slug
capability
has_notices
dismissable
is_automatic
strings
```

Entre ellas:

```php
'capability' => 'edit_theme_options',
```

Esto también se relaciona con la capability agregada actualmente al rol `editor` desde `functions.php`.

Antes de cambiar cualquiera de estas dos piezas conviene revisar si el objetivo histórico era permitir al rol Editor acceder a la pantalla de plugins requeridos.

---

## Textdomain

Las cadenas personalizadas utilizan actualmente:

```php
__( '...', 'theme-slug' )
```

mientras que el blank theme también define:

```text
TRANSDOMAIN
```

durante el bootstrap.

Conviene unificar el textdomain utilizado por TGMPA con la convención actual del theme si esta configuración sigue vigente.

---

# `options_pages.php`

Se encuentra en:

```text
config/options_pages.php
```

Contiene configuración utilizada por:

```text
OptionsServiceProvider
```

Actualmente la estructura se encuentra organizada por Post Type:

```text
bibliography
conversation
correspondence
media
work
invention
bibliohemerography
```

y cada elemento contiene principalmente:

```text
group keys
field keys
subfield keys
```

de ACF.

Ejemplo conceptual:

```php
'post_type' => [
    [
        'key' => 'group_...',
        'fields' => [
            [
                'key' => 'field_...',
            ],
        ],
    ],
]
```

---

## Estado actual

Esta configuración no define Options Pages de forma genérica.

En realidad funciona como complemento de una estructura de campos hardcodeada dentro de `OptionsServiceProvider`.

El flujo actual es:

```text
config/options_pages.php
    ↓
keys de ACF
    ↓
OptionsServiceProvider
    ↓
estructura fija de fields
    ↓
acf_add_options_page()
acf_add_local_field_group()
```

Esto hace que:

```text
config/options_pages.php
```

no sea suficiente por sí solo para declarar una nueva página de opciones con estructura diferente.

---

## Refactor recomendado

Esta parte sí es candidata a refactor independiente.

La separación deseable sería:

```text
config/options_pages.php
    ↓
define páginas de opciones

OptionsServiceProvider
    ↓
registra páginas

acf-json/
    ↓
define Field Groups y Fields
```

Ejemplo conceptual:

```php
return [
    'global' => [
        'page_title'  => 'Opciones globales',
        'menu_title'  => 'Opciones',
        'menu_slug'   => 'global-options',
        'post_id'     => 'global-options',
        'parent_slug' => '',
    ],
];
```

y el provider podría limitarse a:

```php
foreach ($config as $page) {
    acf_add_options_page($page);
}
```

Los campos quedarían fuera del provider y se administrarían mediante ACF Local JSON.

> Este comportamiento todavía no existe. Debe implementarse en una rama de feature antes de documentarlo como flujo vigente.

---

# Qué pertenece a `config/`

Una configuración es buena candidata para esta carpeta cuando:

```text
define comportamiento o componentes globales
            ↓
puede expresarse declarativamente
            ↓
es consumida por Application / Providers
            ↓
config/
```

Ejemplos actuales:

```text
providers
special pages
plugins requeridos
configuración TGMPA
options pages
```

---

## Qué evitar

`config/` no debería convertirse en un lugar para almacenar:

- lógica de negocio;
- callbacks;
- queries;
- HTML;
- credenciales;
- valores sensibles;
- comportamiento específico de templates.

Las credenciales y secretos deben mantenerse fuera del repositorio.

---

# Relación con otras capas

```text
config/
    ↓
declara configuración

Providers / Application
    ↓
interpretan y registran comportamiento

app/
    ↓
implementa lógica del proyecto

framework/
    ↓
proporciona infraestructura reutilizable
```

---

## Oportunidades de refactor detectadas

### Alta prioridad

- Refactorizar `options_pages.php` + `OptionsServiceProvider` para separar registro de páginas y definición de Field Groups.
- Revisar la política actual de Mailgun junto con el módulo `Illuminate\Mail`.
- Revisar versiones y distribución de ACF PRO / WPML dentro de `includes/plugins/`.

### Prioridad media

- Revisar `force_activation` de plugins.
- Unificar el textdomain de `conf_plugins.php`.
- Confirmar por qué TGMPA utiliza `edit_theme_options` y su relación con el rol Editor.

### Baja prioridad

- Mejorar nombres de configuración donde ayude a explicar su intención.
- Añadir documentación inline mínima únicamente donde la estructura no sea evidente.

---

## Resumen

```text
config/
├── app.php
│   ├── providers
│   ├── special-pages
│   ├── special-categories
│   └── special-tags
│
├── required_plugins.php
│   └── plugins registrados mediante TGMPA
│
├── conf_plugins.php
│   └── configuración general de TGMPA
│
└── options_pages.php
    └── configuración histórica de ACF Options Pages
```
