# Controllers
**Estado:** En revisión

## Descripción general

El blank theme incluye una abstracción propia llamada `Controller` para procesar solicitudes POST tradicionales mediante WordPress.

La clase base pertenece al framework y trabaja junto con:

```text
app/Providers/ControllerServiceProvider.php
```

A diferencia de `Ajax`, un Controller está pensado para solicitudes que pasan por:

```text
wp-admin/admin-post.php
```

y normalmente terminan con una redirección.

El flujo general es:

```text
formulario
    ↓
admin-post.php
    ↓
action
    ↓
Controller
    ↓
handle()
    ↓
store($_POST)
    ↓
redirect
```

---

# ControllerServiceProvider

El provider contiene las implementaciones que deben inicializarse:

```php
protected $controllers = [];
```

En el blank theme base no hay controllers registrados por defecto.

Durante `boot()` cada clase configurada es instanciada y registrada:

```php
foreach ($this->controllers as $controller) {
    $controller = new $controller;
    $controller->registerController();
}
```

Conceptualmente:

```text
ControllerServiceProvider
    ↓
$controllers
    ↓
new Controller
    ↓
registerController()
```

---

# Registro de acciones

`registerController()` genera automáticamente el nombre de la acción a partir del nombre de la clase.

El sufijo:

```text
Controller
```

se elimina y el nombre restante se transforma a `snake_case`.

Ejemplos:

```text
ContactController        → contact
CourseRegisterController → course_register
```

Posteriormente se registran las dos variantes de WordPress:

```text
admin_post_{action}
admin_post_nopriv_{action}
```

Esto permite procesar solicitudes tanto de usuarios autenticados como de visitantes.

---

# Uso desde un formulario

Un formulario tradicional puede enviar su información a:

```php
<?php echo admin_url('admin-post.php'); ?>
```

incluyendo el nombre de la acción:

```html
<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
    <input type="hidden" name="action" value="contact">

    <!-- campos -->
</form>
```

Si la clase fuera:

```php
ContactController
```

el valor esperado sería:

```text
contact
```

WordPress ejecutaría:

```text
admin_post_contact
```

o:

```text
admin_post_nopriv_contact
```

según el estado de autenticación del usuario.

---

# Procesamiento

Cuando WordPress dispara la acción correspondiente, `handle()` elimina el parámetro interno `action` y delega el procesamiento a:

```php
store($_POST)
```

Por lo tanto, una implementación concreta debe encargarse del comportamiento real dentro de `store()`.

Ejemplo conceptual:

```php
class ContactController extends Controller
{
    public function store($input)
    {
        // validar
        // procesar
        // redirigir
    }
}
```

---

# Validación

La clase base incluye una validación sencilla.

Actualmente la regla documentada por la implementación es:

```text
required
```

Ejemplo:

```php
$this->validate($input, [
    'email' => 'required',
]);
```

No debe asumirse que esta API ofrece el conjunto de reglas de Laravel u otro framework de validación.

Si una validación falla, el Controller utiliza su flujo de error y redirección.

---

# Respuestas y redirecciones

A diferencia del sistema AJAX del blank theme, los Controllers no responden principalmente con JSON.

El flujo de éxito utiliza:

```php
success($link)
```

para realizar una redirección segura.

El flujo de error utiliza:

```php
error()
```

y redirige al referer cuando está disponible o, como fallback, al Home.

Conceptualmente:

```text
store()
    ├── error
    │      ↓
    │   página anterior / home
    │
    └── success
           ↓
        URL destino
```

---

# Controllers vs AJAX

Ambos sistemas permiten enviar información desde el frontend hacia WordPress, pero están pensados para experiencias diferentes.

| Caso | Controller | AJAX |
| --- | --- | --- |
| Endpoint de WordPress | `admin-post.php` | `admin-ajax.php` |
| Navegación | Normalmente redirige | Permanece en la página |
| Respuesta principal | Redirect | JSON |
| Requiere JavaScript | No necesariamente | Sí |
| Formularios tradicionales | Muy adecuado | Posible, pero innecesario |
| Actualización parcial de UI | Poco adecuado | Adecuado |
| Búsquedas, filtros, loaders | No | Sí |
| Fallback sin JS | Natural | Requiere implementación adicional |

---

## Cuándo conviene utilizar Controller

Un Controller resulta especialmente útil cuando el formulario puede resolverse mediante el flujo web tradicional:

```text
submit
    ↓
procesar
    ↓
redirect
```

Ejemplos:

- formularios simples de contacto;
- formularios que terminan en una página de gracias;
- altas o acciones donde no necesitas actualizar el DOM;
- formularios que deberían seguir funcionando aunque JavaScript falle o esté deshabilitado;
- procesos donde una redirección posterior es parte natural de la experiencia.

En estos casos AJAX puede agregar complejidad innecesaria.

---

## Cuándo conviene utilizar AJAX

AJAX es más adecuado cuando necesitas conservar al usuario dentro de la interfaz actual y responder dinámicamente.

Ejemplos:

- filtros;
- búsquedas;
- paginación sin recarga;
- viewers o modales;
- cargas incrementales;
- formularios que deben mostrar éxito o errores inline;
- cálculos;
- cambios de estado que actualizan partes específicas de la página.

El flujo es:

```text
interacción
    ↓
JavaScript
    ↓
admin-ajax.php
    ↓
Ajax::store()
    ↓
JSON
    ↓
actualización de UI
```

---

## Regla práctica

Para decidir entre ambos sistemas puede utilizarse esta pregunta:

> ¿Después de procesar el formulario necesito seguir trabajando en la misma interfaz?

Si la respuesta es **no** y una redirección es suficiente:

```text
Controller
```

Si la respuesta es **sí** y necesitas actualizar la pantalla sin recargar:

```text
AJAX
```

No es necesario convertir todos los formularios a AJAX por defecto.

---

## Registro de un nuevo Controller

1. Crear una clase que extienda la clase base `Controller`.
2. Implementar `store($input)`.
3. Agregarla a `$controllers` dentro de `ControllerServiceProvider`.
4. Utilizar en el formulario el nombre de acción generado a partir de la clase.
5. Enviar el formulario a `admin-post.php`.

Ejemplo conceptual:

```text
ContactController
        ↓
contact
        ↓
ControllerServiceProvider
        ↓
admin_post_contact
admin_post_nopriv_contact
```

---

## Consideraciones

- El blank theme no registra Controllers por defecto.
- `Controller` no es una API nativa de WordPress; es una abstracción propia del framework.
- Internamente utiliza los hooks nativos `admin_post_*`.
- El nombre de la acción depende del nombre de la clase.
- La validación implementada es deliberadamente pequeña y no debe confundirse con Laravel Validation.
- Para interacciones dependientes de JavaScript y actualización parcial de interfaz debe preferirse el sistema AJAX.
- Para submits tradicionales con redirección, Controller puede mantener una implementación más simple y resiliente.
