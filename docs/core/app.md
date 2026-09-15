# App
**Estado:** En revisión

## Descripción general

La carpeta:

```text
app/
```

contiene la implementación PHP propia del proyecto que utiliza las abstracciones disponibles en:

```text
framework/src/Illuminate/
```

Composer registra este namespace mediante:

```text
App\ → app/
```

La separación conceptual es:

```text
framework/
    ↓
infraestructura reutilizable del blank theme

app/
    ↓
implementaciones, providers y lógica propia del proyecto
```

---

## Estructura actual

```text
app/
├── Http/
│   └── Ajax/
│       └── ContactAjax.php
├── Mail/
│   ├── AdminContactMail.php
│   └── ContactMail.php
├── Metaboxes/
│   └── CltvoSocialNet.php
├── Providers/
├── Taxonomies/
│   └── Theme.php
├── Contacto.php
├── ExamplePostType.php
├── Space.php
└── helpers.php
```

Los providers y `helpers.php` se documentan por separado.

Este documento se concentra en las implementaciones concretas restantes.

---

# `Contacto`

Se encuentra en:

```text
app/Contacto.php
```

Extiende:

```php
Illuminate\Page
```

y representa la Special Page:

```text
contacto
```

mediante:

```php
parent::__construct(specialPage('contacto', true));
```

La clase expone:

```php
public $social_net;
```

y obtiene sus valores desde:

```php
App\Metaboxes\CltvoSocialNet
```

mediante:

```php
CltvoSocialNet::getMetaValue($this->post);
```

---

## Fallback de correo

`Contacto` define:

```php
public $mailables = [
    'mail'
];
```

y durante `setMetas()` garantiza que exista un correo:

```php
info@zonapaz.com
```

si el metadato correspondiente no está configurado.

Este valor pertenece claramente a un proyecto histórico y no debería conservarse como fallback genérico del blank theme.

---

## Uso actual

`header.php` crea una instancia global:

```php
global $contact;

$contact = new Contacto;
```

Por lo tanto, actualmente `Contacto` se instancia en cada carga del frontend que utilice ese header.

También se utiliza desde:

```text
App\Mail\ContactMail
```

para obtener el correo configurado de contacto.

### Consideración

La instancia global en `header.php` es una convención histórica. Si un proyecto no necesita información de contacto global, esta dependencia puede eliminarse.

---

# Flujo de contacto

El ejemplo de contacto actual conecta varias capas del blank theme:

```text
frontend
    ↓
AJAX action
    ↓
App\Http\Ajax\ContactAjax
    ↓
Illuminate\Ajax
    ↓
App\Mail\ContactMail
App\Mail\AdminContactMail
    ↓
Illuminate\Mail\Mailable
    ↓
Mailer / SMTPMailer
    ↓
mail/
```

Es un buen ejemplo para entender cómo se conectan las piezas del framework, pero contiene valores y decisiones específicas de proyectos anteriores que no deben considerarse configuración obligatoria para nuevos proyectos.

---

# `ContactAjax`

Se encuentra en:

```text
app/Http/Ajax/ContactAjax.php
```

Extiende:

```php
Illuminate\Ajax
```

y se encuentra registrado actualmente desde:

```text
AjaxServiceProvider
```

Por lo tanto, a diferencia de varias clases de ejemplo del blank, este AJAX **sí forma parte del flujo activo actual**.

---

## Validación

Valida:

```text
name
email
```

como obligatorios:

```php
$this->validate($input, [
    'name'  => 'required',
    'email' => 'required',
]);
```

La validación depende de la implementación propia de `Illuminate\Ajax`.

---

## Selección del remitente

Consulta:

```php
get_theme_support('CLTVO_USEMAILGUN');
```

Si la bandera está activa intenta recuperar:

```text
Mailgun → from-address
```

desde las opciones del plugin.

Si la bandera no está activa utiliza:

```text
admin@elcultivo.mx
```

como dirección para el correo administrativo.

Este valor también es específico del Cultivo y debería revisarse si el flujo se conserva como ejemplo genérico.

---

## Envíos

Realiza dos envíos:

```php
Mail::to($input['email'])->send(new ContactMail($input));
```

y:

```php
Mail::to($this->from_address)->send(new AdminContactMail($input));
```

Conceptualmente:

```text
usuario
    ← ContactMail

administrador
    ← AdminContactMail
```

---

## Bug detectado en `getMailgunFromAddress()`

La clase declara:

```php
private $error_messages = [
    // ...
];
```

pero el método utiliza:

```php
$error_messages['development']
$error_messages['production']
```

en lugar de:

```php
$this->error_messages
```

Por lo tanto, en su implementación actual:

```php
$error_message = (WP_DEBUG)
    ? $error_messages['development']
    : $error_messages['production'];
```

consulta una variable local que no está definida.

Debe corregirse a:

```php
$error_message = (WP_DEBUG)
    ? $this->error_messages['development']
    : $this->error_messages['production'];
```

si este flujo continúa utilizándose.

---

## Dependencia de `is_plugin_active()`

El método también llama:

```php
is_plugin_active('mailgun/mailgun.php')
```

Esta función pertenece al área administrativa de WordPress y no siempre se encuentra cargada automáticamente en requests de frontend.

Antes de conservar este mecanismo debe verificarse que el archivo correspondiente de WordPress esté disponible en el contexto donde se ejecuta el AJAX.

---

# Mailables del proyecto

## `ContactMail`

Se encuentra en:

```text
app/Mail/ContactMail.php
```

Extiende:

```php
Illuminate\Mail\Mailable
```

Durante su construcción:

1. conserva `$input`;
2. crea un nuevo `Contacto`;
3. obtiene el nombre del sitio;
4. obtiene el correo de contacto;
5. lo utiliza como remitente.

```php
$this->from['name'] = get_bloginfo('name');
$this->from['address'] = $this->contact->social_net['mail'];
```

Su `build()` únicamente define:

```php
$this->subject('Gracias por contactarnos');
```

Por lo tanto, conserva la vista default de `Mailable`:

```text
mail/layout.php
```

Actualmente esa vista únicamente contiene:

```text
Gracias por contactarnos.
```

---

## `AdminContactMail`

Se encuentra en:

```text
app/Mail/AdminContactMail.php
```

Su `build()` define:

```text
subject → Información de contacto
view    → mail/contact.php
```

y utiliza como `from`:

```php
$this->to['address']
```

En el flujo actual esto significa que el destinatario administrativo termina utilizándose también como remitente.

Esto funciona porque:

```text
Mail::to(...)
    ↓
MailableMailer
    ↓
rellena $mailable->to
    ↓
Mailable::send()
    ↓
build()
```

pero semánticamente merece revisión. El remitente debería definirse explícitamente según la estrategia de correo del proyecto.

---

# `CltvoSocialNet`

Se encuentra en:

```text
app/Metaboxes/CltvoSocialNet.php
```

Extiende:

```php
Illuminate\Metabox
```

y representa un metabox de información de contacto.

Actualmente define:

```text
mail
phone
```

aunque la interfaz renderizada sólo muestra directamente el campo `mail` y deja preparada una estructura para redes con URL.

---

## Regla de visualización

El metabox sólo debe mostrarse en:

```text
Special Page: contacto
```

mediante:

```php
return isSpecialPage('contacto');
```

---

## Estado de registro

Aunque `Contacto` consume los valores de este metabox, actualmente su registro está comentado en:

```text
MetaboxServiceProvider
```

```php
protected $metaboxes = [
    /**\App\Metaboxes\CltvoSocialNet::class,**/
];
```

Por lo tanto:

```text
Contacto puede leer metadata existente
```

pero:

```text
CltvoSocialNet no registra actualmente su UI en el admin
```

a través del provider.

Esto confirma que el ejemplo de contacto está parcialmente desacoplado / histórico dentro del blank actual.

---

## Seguridad y escape

La implementación imprime valores directamente dentro de inputs:

```php
value="<?php echo $this->meta_value['mail']; ?>"
```

y otros valores similares.

Si este metabox se conserva o reutiliza debería utilizarse escape apropiado, por ejemplo:

```php
esc_attr()
```

Además, la clase base `Metabox` ya fue identificada como una implementación que merece revisión de nonce y sanitización durante el guardado.

---

# `ExamplePostType`

Se encuentra en:

```text
app/ExamplePostType.php
```

Extiende:

```php
Illuminate\CustomPostType
```

y sirve como ejemplo mínimo de un Custom Post Type.

Define:

```text
nombre plural
nombre singular
slug
supports
menu icon
```

y deja:

```php
setMetas()
```

vacío.

---

## Estado

Actualmente está comentado en:

```text
CustomPostTypeServiceProvider
```

por lo que **no se registra**.

Debe entenderse como boilerplate / ejemplo y no como una funcionalidad activa del blank theme.

---

# `Space`

Se encuentra en:

```text
app/Space.php
```

También extiende:

```php
Illuminate\CustomPostType
```

y únicamente define:

```php
protected static $supports = [
    'title',
    'editor',
    'excerpt',
    'thumbnail'
];
```

No aparece registrado desde `CustomPostTypeServiceProvider` ni se encontraron referencias adicionales en el blank theme.

### Estado

Actualmente parece ser código residual / ejemplo histórico.

Si no existe una razón para conservarlo como referencia puede considerarse candidato a eliminación.

---

# `Taxonomies/Theme.php`

El archivo contiene la clase:

```php
ExampleTaxonomie
```

que extiende:

```php
Illuminate\Taxonomy
```

Define:

```text
slug → ejemplo-taxonomia
initialTerms → cine
```

pero no tiene Post Types asociados:

```php
protected static $postypes = [];
```

y además se encuentra comentada en:

```text
TaxonomyServiceProvider
```

Por lo tanto no se registra actualmente.

---

## Nombre de archivo y clase

Existe una discrepancia entre:

```text
archivo → Theme.php
clase   → ExampleTaxonomie
```

Con PSR-4, una referencia directa esperaría normalmente:

```text
App\Taxonomies\ExampleTaxonomie
→ app/Taxonomies/ExampleTaxonomie.php
```

pero el archivo se llama:

```text
Theme.php
```

Actualmente esto no produce un problema visible porque la clase no está registrada.

Si se intentara habilitar utilizando:

```php
\App\Taxonomies\ExampleTaxonomie::class
```

Composer no encontraría esa clase por la ruta PSR-4 esperada, salvo que el archivo hubiera sido incluido por otro medio.

### Recomendación

Si se conserva como ejemplo debería renombrarse:

```text
Theme.php
    ↓
ExampleTaxonomie.php
```

o bien renombrar la clase de acuerdo con la intención real del archivo.

---

# Clasificación actual de `app/`

| Componente | Estado |
| --- | --- |
| `Providers/` | Core del proyecto; ya documentado |
| `helpers.php` | Helpers globales del proyecto; ya documentado |
| `Http/Ajax/ContactAjax.php` | Activo, pero con bugs / dependencias históricas |
| `Contacto.php` | Activo desde `header.php` y Mail |
| `Mail/ContactMail.php` | Activo dentro del flujo Contact AJAX |
| `Mail/AdminContactMail.php` | Activo dentro del flujo Contact AJAX |
| `Metaboxes/CltvoSocialNet.php` | Implementación existente, registro comentado |
| `ExamplePostType.php` | Boilerplate / ejemplo no registrado |
| `Space.php` | Residual o ejemplo histórico |
| `Taxonomies/Theme.php` | Ejemplo no registrado y con inconsistencia PSR-4 |

---

# Qué debería vivir en `app/`

La carpeta `app/` es apropiada para implementaciones PHP propias del proyecto:

```text
Providers
Ajax
Controllers
Mailables
CPTs
Taxonomías
Metaboxes
clases de dominio / wrappers propios
helpers globales del proyecto
```

El framework debe proporcionar las abstracciones reutilizables; `app/` debe extenderlas o configurarlas para el proyecto concreto.

---

# Oportunidades de limpieza

## Alta prioridad si se conserva Contact

- Corregir `$error_messages` → `$this->error_messages`.
- Revisar disponibilidad de `is_plugin_active()` en frontend.
- Definir claramente estrategia de remitente.
- Eliminar fallbacks históricos como `info@zonapaz.com`.
- Revisar `admin@elcultivo.mx`.
- Revisar integración Mailgun / Mailer / SMTPMailer de forma conjunta.

## Media prioridad

- Decidir si `CltvoSocialNet` sigue siendo parte del ejemplo.
- Evitar instanciar `Contacto` globalmente desde `header.php` si no se necesita.
- Escapar valores del metabox.

## Cleanup

- Eliminar `Space.php` si no cumple ninguna función de ejemplo.
- Renombrar o eliminar `Taxonomies/Theme.php`.
- Mantener `ExamplePostType.php` únicamente si se quiere conservar un ejemplo oficial claro.
- Diferenciar explícitamente dentro del blank qué código es boilerplate de ejemplo y qué código es infraestructura necesaria.
