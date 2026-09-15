# Bootstrap
**Estado:** En revisión
## Descripción general

El bootstrap prepara el blank theme, carga sus dependencias y construye la instancia principal de la aplicación antes de inicializar los providers.

Los archivos involucrados son:

```text
functions.php
bootstrap/
├── autoload.php
└── app.php
```

La clase principal de la aplicación se encuentra en:

```text
framework/src/Illuminate/Foundation/Application.php
```

Composer participa en este proceso mediante:

```text
composer.json
vendor/autoload.php
```

---

## Flujo general

```text
WordPress
    ↓
functions.php
    ↓
bootstrap/autoload.php
    ├── CLTVO_START
    ├── vendor/autoload.php
    │       ↓
    │   Composer
    │       ├── Illuminate\ → framework/src/Illuminate/
    │       ├── App\        → app/
    │       ├── framework/src/Illuminate/Support/helpers.php
    │       └── app/helpers.php
    │
    └── TGMPA
    ↓
bootstrap/app.php
    ↓
new Illuminate\Foundation\Application($themePath)
    ↓
Application::__construct()
    ├── constantes globales
    ├── config/app.php
    └── providers
            ↓
        new Provider($this)
            ↓
        boot()
```

---

# `functions.php`

WordPress carga `functions.php` como punto de entrada del theme.

Actualmente comienza con:

```php
require __DIR__.'/bootstrap/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
```

La primera línea carga las dependencias necesarias para el blank theme y la segunda construye la aplicación.

Después del bootstrap, el archivo actual también contiene configuración adicional:

- soporte para `title-tag`;
- bandera `CLTVO_USEMAILGUN`;
- bandera `CLTVO_DISABLE_COMMENTS`;
- asignación de `edit_theme_options` al rol `editor`.

Estas responsabilidades no forman parte estrictamente del proceso de bootstrap y son candidatas a revisión para mantener `functions.php` enfocado en la inicialización del theme.

---

# `bootstrap/autoload.php`

Se encuentra en:

```text
bootstrap/autoload.php
```

Su responsabilidad es preparar las dependencias necesarias antes de construir la aplicación.

## `CLTVO_START`

Define:

```php
define('CLTVO_START', microtime(true));
```

Esta constante conserva el tiempo en el que comienza la carga del blank theme.

## Composer

Carga:

```php
require __DIR__.'/../vendor/autoload.php';
```

A partir de este punto Composer se encarga del autoload de las clases y archivos globales declarados en `composer.json`.

## TGMPA

También incluye:

```php
include_once __DIR__ . "/../includes/tgm_plugin_activation/class-tgm-plugin-activation.php";
```

Esto hace que TGMPA se encuentre disponible posteriormente para el registro de plugins requeridos desde `ActionsServiceProvider`.

---

# Composer Autoload

La configuración se encuentra en:

```text
composer.json
```

El blank theme utiliza PSR-4 para registrar dos namespaces:

```json
"psr-4": {
    "Illuminate\\": "framework/src/Illuminate/",
    "App\\": "app/"
}
```

La separación es:

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

Esto permite utilizar clases como:

```php
Illuminate\Foundation\Application
App\Providers\AppServiceProvider
```

sin realizar `require` manual de cada archivo.

---

## Helpers globales

Composer también carga automáticamente:

```json
"files": [
    "framework/src/Illuminate/Support/helpers.php",
    "app/helpers.php"
]
```

Por lo tanto, ambos archivos se encuentran disponibles durante la carga del theme sin requerir un `include` adicional.

La separación conceptual es:

```text
framework/src/Illuminate/Support/helpers.php
    ↓
helpers reutilizables del framework / Cultivo

app/helpers.php
    ↓
helpers globales propios del proyecto
```

Ver [Helpers](./helpers.md).

---

# `bootstrap/app.php`

Se encuentra en:

```text
bootstrap/app.php
```

Su responsabilidad es construir y devolver la aplicación:

```php
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

return $app;
```

El argumento enviado al constructor corresponde a la raíz del theme.

El resultado queda disponible desde `functions.php` mediante:

```php
$app
```

---

# `Illuminate\Foundation\Application`

Se encuentra en:

```text
framework/src/Illuminate/Foundation/Application.php
```

Es la clase que conecta el bootstrap con la configuración y los providers del blank theme.

Su constructor realiza cuatro tareas principales.

## Constantes globales

Define:

```text
BLOGURL
THEMEURL
TRANSDOMAIN
```

utilizando información del theme y de WordPress.

## Ruta del theme

Guarda la raíz recibida desde `bootstrap/app.php` en:

```php
$this->path
```

## Configuración

Carga:

```text
config/app.php
```

y conserva el resultado en:

```php
$this->config
```

## Providers

Recorre:

```php
$this->config['providers']
```

e inicializa cada provider mediante:

```php
foreach ($this->config['providers'] as $provider) {
    $object = new $provider($this);
    $object->boot();
}
```

La instancia de `Application` se entrega a cada provider, permitiendo acceder a información como:

```php
$this->app->config
```

---

# Ciclo de inicialización de Providers

El ciclo real de un provider es:

```text
config/app.php
    ↓
clase declarada en providers
    ↓
new Provider($application)
    ↓
ServiceProvider::__construct()
    ↓
$this->app = $application
    ↓
Provider::boot()
```

Aunque algunos providers contienen un método:

```php
public function register()
{
    //
}
```

`Illuminate\Foundation\Application` **no ejecuta `register()` automáticamente**.

Dentro de la implementación actual del blank theme, `boot()` es el punto efectivo de inicialización.

Ver [Providers](./providers.md).

---

# `Illuminate\Support\ServiceProvider`

Se encuentra en:

```text
framework/src/Illuminate/Support/ServiceProvider.php
```

Su implementación es mínima:

```php
class ServiceProvider
{
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
```

Su responsabilidad es conservar la instancia de `Application` dentro de cada provider.

El blank theme utiliza nombres y conceptos similares a otros frameworks PHP, pero esta implementación pertenece al framework propio del proyecto y es considerablemente más ligera.

---

## Dependencias de Composer

Además del autoload, `composer.json` declara actualmente:

```text
phpmailer/phpmailer
```

como dependencia del proyecto.

También contiene:

```text
symfony/var-dumper
```

como dependencia de desarrollo.

Estas dependencias no forman parte directamente del ciclo de bootstrap, pero Composer garantiza que sus clases se encuentren disponibles después de cargar:

```text
vendor/autoload.php
```

---

## Oportunidades de refactor

### Mantener `functions.php` como punto de entrada

Idealmente `functions.php` debe permanecer enfocado en iniciar el blank theme:

```text
functions.php
    ↓
autoload
    ↓
application
```

Las responsabilidades adicionales deben colocarse en el provider, configuración o componente correspondiente cuando exista una ubicación más apropiada.

### `title-tag`

Actualmente se registra desde `functions.php`:

```php
add_theme_support('title-tag');
```

Como `SupportServiceProvider` ya centraliza los soportes nativos de WordPress, este soporte es candidato a moverse ahí.

### Flags propias del blank theme

Actualmente se utiliza `add_theme_support()` para:

```text
CLTVO_USEMAILGUN
CLTVO_DISABLE_COMMENTS
```

Estas son banderas internas del blank theme y no soportes nativos de WordPress.

Antes de refactorizarlas debe revisarse todo el código que las consume para determinar si conviene mantener el mecanismo actual o mover la configuración a una estructura propia.

### Rol Editor

`cltvo_role_edit()` agrega:

```text
edit_theme_options
```

al rol `editor` durante `admin_init`.

Conviene revisar el propósito actual de esta capability antes de modificar el flujo.

La implementación también debe contemplar que:

```php
get_role('editor')
```

puede no devolver un objeto.

Además, `add_cap()` persiste la capability en la base de datos, por lo que no necesariamente requiere ejecutarse durante cada carga de `admin_init`.

---

## Convenciones

- Mantener `bootstrap/` limitado a preparar dependencias y construir la aplicación.
- Utilizar Composer para el autoload de clases y helpers declarados globalmente.
- Mantener separadas las responsabilidades de `Illuminate\` y `App\`.
- No realizar `require` manual de clases que ya se encuentren cubiertas por PSR-4.
- No realizar `require` manual de los helpers registrados mediante Composer.
- Mantener el ciclo de providers documentado conforme a la implementación real de `Application`.
- No asumir que `register()` se ejecuta automáticamente.
- Evitar agregar lógica específica de features directamente al bootstrap.
- Revisar si una responsabilidad colocada en `functions.php` ya tiene un provider o archivo de configuración apropiado.
