# AJAX

## Descripción general

El blank theme incluye una abstracción para crear y registrar acciones **AJAX de WordPress** sin declarar manualmente `wp_ajax_*` y `wp_ajax_nopriv_*` para cada funcionalidad.

La clase base se encuentra en:

```text
framework/Illuminate/Ajax.php
```

Las implementaciones específicas del proyecto se crean dentro de:

```text
app/Http/Ajax/
```

extendiendo:

```php
Illuminate\Ajax
```

El registro de las clases AJAX se centraliza en:

```text
app/Providers/AjaxServiceProvider.php
```

---

## Estructura general

El flujo de una petición AJAX es:

```text
config/app.php
    ↓
AjaxServiceProvider
    ↓
$files
    ↓
Clase que extiende Illuminate\Ajax
    ↓
registerAjax()
    ↓
wp_ajax_{action}
wp_ajax_nopriv_{action}
    ↓
handle()
    ↓
store($_POST)
```

---

## `AjaxServiceProvider`

El provider mantiene las clases AJAX que deben registrarse:

```php
protected $files = [
    \App\Http\Ajax\ContactAjax::class
];
```

Durante `boot()` instancia cada clase y ejecuta:

```php
public function boot()
{
    foreach ($this->files as $ajax) {
        $ajax = new $ajax;
        $ajax->registerAjax();
    }
}
```

Por lo tanto, crear una clase dentro de `app/Http/Ajax/` no es suficiente: debe agregarse a `$files` para que sus acciones de WordPress sean registradas.

---

## Nombre de la acción AJAX

El nombre de la acción se genera automáticamente a partir del **nombre de la clase**.

`Illuminate\Ajax::classname()`:

1. obtiene el nombre corto de la clase;
2. elimina `Ajax`;
3. convierte el resultado a `snake_case` mediante `toSnakeCase()`.

Por ejemplo:

| Clase | Action AJAX |
| --- | --- |
| `ContactAjax` | `contact` |
| `EjemploAjax` | `ejemplo` |

Para:

```php
class ContactAjax extends Ajax
```

WordPress registra:

```text
wp_ajax_contact
wp_ajax_nopriv_contact
```

Por lo tanto, desde JavaScript la petición debe enviar:

```js
action: 'contact'
```

> El nombre de la clase determina automáticamente el valor de `action`. El sufijo `Ajax` no forma parte del nombre registrado.

---

## Usuarios autenticados y no autenticados

`registerAjax()` registra las dos variantes de WordPress:

```php
add_action('wp_ajax_nopriv_' . $name, [$this, 'handle']);
add_action('wp_ajax_' . $name, [$this, 'handle']);
```

Esto significa que, por defecto, las acciones creadas mediante esta abstracción pueden ejecutarse tanto por:

- usuarios autenticados;
- usuarios no autenticados.

Si una funcionalidad requiere restricciones adicionales, estas deben implementarse de acuerdo con las necesidades del proyecto.

---

## Procesamiento de la petición

Cuando WordPress ejecuta la acción AJAX se llama:

```php
handle()
```

La clase base elimina primero `action` del `$_POST`:

```php
unset($_POST['action']);
```

y después envía el resto de los datos al método:

```php
$this->store($_POST);
```

Por esta razón, cada implementación debe definir su lógica principal en `store()`.

Ejemplo:

```php
public function store($input)
{
    // Procesar la petición.
}
```

El arreglo `$input` contiene los valores enviados mediante `POST`, excepto `action`.

---

## Crear una acción AJAX

Una implementación básica puede tener esta estructura:

```php
namespace App\Http\Ajax;

use Illuminate\Ajax;

class EjemploAjax extends Ajax
{
    public function store($input)
    {
        $this->validate($input, [
            'name' => 'required',
        ]);

        // Procesar información...

        $this->success('Petición procesada correctamente.');
    }
}
```

Después debe registrarse en:

```text
app/Providers/AjaxServiceProvider.php
```

```php
protected $files = [
    \App\Http\Ajax\EjemploAjax::class,
];
```

La acción generada para `EjemploAjax` será:

```text
ejemplo
```

---

## Validación

La clase base incluye:

```php
validate($input, $validation)
```

Las reglas se definen como strings separados por `|`:

```php
$this->validate($input, [
    'name'  => 'required',
    'email' => 'required',
]);
```

Actualmente la abstracción implementa las reglas:

### `required`

Comprueba que el campo exista y tenga contenido.

Para arrays comprueba que contengan al menos un elemento.

### `array`

Comprueba que el valor recibido sea un array.

Ejemplo:

```php
$this->validate($input, [
    'name'  => 'required',
    'items' => 'required|array',
]);
```

> No debe asumirse que esta validación incluye todas las reglas disponibles en frameworks como Laravel. Solamente pueden utilizarse las reglas implementadas por `Illuminate\Ajax::rule()`.

---

## Mensajes de validación

La clase base incluye el mensaje:

```php
'required' => 'Please fill all the fields.'
```

mediante:

```php
defaultMessages()
```

Cada implementación puede agregar o sobrescribir mensajes mediante:

```php
public function messages()
{
    return [
        'name.required' => 'El nombre es obligatorio.',
    ];
}
```

`returnValidationError()` combina ambos arreglos y busca primero un mensaje específico con el formato:

```text
campo.regla
```

y, si no existe, utiliza el mensaje general correspondiente a la regla.

---

## Respuestas

### Respuesta exitosa

Para terminar correctamente una petición se utiliza:

```php
$this->success('Mensaje');
```

La respuesta generada es JSON:

```json
{
    "message": "Mensaje"
}
```

### Respuesta de error

Para detener la petición con error:

```php
$this->returnError('Mensaje');
```

La clase:

- configura `Content-Type: application/json`;
- responde con HTTP `422 Unprocessable Entity`;
- devuelve el mensaje como JSON;
- termina la ejecución.

```json
{
    "message": "Mensaje"
}
```

Tanto `success()` como `returnError()` finalizan la ejecución mediante `die`.

---

## `parse()`

La clase base incluye:

```php
public function parse($inputs)
```

Este helper aplica `trim()` a los valores recibidos y elimina aquellos que resulten vacíos mediante `array_filter()`.

Puede utilizarse cuando una implementación requiera limpiar un conjunto simple de inputs antes de procesarlos.

---

## Ejemplo: `ContactAjax`

El blank theme incluye:

```text
app/Http/Ajax/ContactAjax.php
```

como implementación real de esta estructura.

La clase:

```php
class ContactAjax extends Ajax
```

genera automáticamente la acción:

```text
contact
```

y valida:

```php
$this->validate($input, [
    'name'  => 'required',
    'email' => 'required',
]);
```

Posteriormente procesa el envío de los correos de contacto mediante las clases de Mail del tema.

Al finalizar correctamente responde mediante:

```php
$this->success(
    __('Gracias por contactarnos, pronto tendrás noticias de nosotros.')
);
```

`ContactAjax` debe entenderse como un ejemplo de implementación. La lógica de correo, Mailgun y los destinatarios corresponde a esa funcionalidad y no a la abstracción AJAX en sí.

---

## Ejemplo desde JavaScript

Una petición hacia `admin-ajax.php` debe enviar como mínimo el `action` generado por la clase:

```js
$.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'contact',
        name: name,
        email: email
    },
    success: function(response) {
        // Petición correcta.
    },
    error: function(response) {
        // Error de validación o procesamiento.
    }
});
```

La forma concreta de obtener la URL de `admin-ajax.php` depende de cómo `ScriptsServiceProvider` exponga los datos al frontend en cada proyecto.

---

## Convenciones

- Crear las clases AJAX dentro de `app/Http/Ajax/`.
- Utilizar el sufijo `Ajax` en el nombre de la clase.
- Recordar que el nombre de la clase determina automáticamente el `action`.
- Registrar cada clase en `$files` de `AjaxServiceProvider`.
- Colocar la lógica principal de la petición en `store($input)`.
- Utilizar `success()` y `returnError()` para conservar el formato de respuesta de la abstracción.
- No utilizar reglas de validación que no estén implementadas en `Illuminate\Ajax::rule()`.
- Agregar validaciones de seguridad y permisos específicas cuando la funcionalidad lo requiera.
- Mantener en cada clase únicamente la lógica correspondiente a su acción.
