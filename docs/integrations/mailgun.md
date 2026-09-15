# Mailgun y sistema de correo
**Estado:** En revisión — requiere refactor

## Descripción

El blank theme contiene una integración histórica de correo formada por varias capas:

```text
WordPress / plugin Mailgun
        ↑
framework/src/Illuminate/Mail/
        ↑
App\Mail\*
        ↑
formularios / AJAX
```

Mailgun no debe entenderse como un sistema aislado. La flag:

```php
CLTVO_USEMAILGUN
```

interactúa con:

- el plugin Mailgun;
- `ContactAjax`;
- `Mailable`;
- `Mailer`;
- `SMTPMailer`;
- configuración histórica de debug/producción.

La implementación actual contiene inconsistencias y código heredado, por lo que debe documentarse antes de reutilizarse o modernizarse.

---

# Dependencias relacionadas

## Plugin Mailgun

Se declara en:

```text
config/required_plugins.php
```

como plugin requerido.

En producción su configuración histórica utiliza activación forzada según el valor de:

```php
WP_DEBUG
```

La configuración exacta debe revisarse por proyecto antes de asumir que Mailgun está listo para enviar.

---

## PHPMailer

`composer.json` declara:

```text
phpmailer/phpmailer ^6.12
```

por lo que PHPMailer sí es una dependencia actual del código revisado.

Esta dependencia es utilizada por:

```text
framework/src/Illuminate/Mail/SMTPMailer.php
```

---

# Flag `CLTVO_USEMAILGUN`

En `functions.php` se declara actualmente:

```php
add_theme_support('CLTVO_USEMAILGUN', false);
```

El valor se consulta mediante:

```php
get_theme_support('CLTVO_USEMAILGUN');
```

---

## Importante: el nombre de la flag es engañoso

No existe un flujo simple:

```text
false → servidor
true  → Mailgun
```

en todas las capas del framework.

La implementación real de `Mailable::send()` también toma en cuenta:

```php
WP_DEBUG
```

Por lo tanto el comportamiento debe entenderse a partir del código actual y no únicamente del nombre `CLTVO_USEMAILGUN`.

---

# Flujo de `Mailable`

`Illuminate\Mail\Mailable::send()` ejecuta primero:

```php
$this->build();
```

y después consulta:

```php
get_theme_support('CLTVO_USEMAILGUN');
```

Conceptualmente, la selección actual es:

```text
CLTVO_USEMAILGUN
│
├── false
│      ↓
│    Mailer
│
└── true
       ↓
    WP_DEBUG?
       ├── true  → SMTPMailer
       └── false → Mailer
```

Esto significa que activar la flag **no provoca que `Mailable` utilice directamente el plugin Mailgun**.

El nombre de la flag y el comportamiento real no están alineados con claridad.

---

# `Mailer`

Se encuentra en:

```text
framework/src/Illuminate/Mail/Mailer.php
```

y representa el envío basado en WordPress/PHP.

Utiliza:

```php
wp_mail()
```

y contempla:

```php
mail()
```

como fallback.

Cuando el plugin Mailgun intercepta `wp_mail()`, este camino puede terminar utilizando Mailgun, pero esa integración depende de la configuración del plugin y de WordPress.

Por lo tanto:

```text
Mailer
    ↓
wp_mail()
    ↓
plugin/configuración del entorno
```

es diferente de una integración directa con la API de Mailgun.

---

# Bug en CC / BCC

`Mailer` intenta agregar headers para:

```text
cc
bcc
```

pero consulta variables locales:

```php
$cc
$bcc
```

en lugar de las propiedades del Mailable:

```php
$mailable->cc
$mailable->bcc
```

Por lo tanto esta parte de la implementación actual debe corregirse antes de confiar en CC/BCC.

---

# `SMTPMailer`

Se encuentra en:

```text
framework/src/Illuminate/Mail/SMTPMailer.php
```

y utiliza directamente:

```text
PHPMailer
```

mediante la dependencia de Composer.

---

## Seguridad

La implementación revisada contiene configuración SMTP y credenciales hardcodeadas históricas.

Esas credenciales **no deben documentarse, copiarse ni reutilizarse**.

Si el código estuvo versionado o compartido, las credenciales deben considerarse expuestas y rotarse en el proveedor correspondiente.

La configuración sensible debería vivir fuera del repositorio, por ejemplo mediante:

```text
variables de entorno
secrets del hosting
configuración segura del proveedor
```

---

## Otros puntos de revisión

La implementación también utiliza:

```php
utf8_decode()
```

y no devuelve explícitamente el resultado de:

```php
$mailer->send()
```

Estos comportamientos merecen revisión en una modernización del módulo.

---

# `MailableMailer`

El framework contiene una capa intermedia:

```text
MailableMailer
```

que permite una sintaxis del tipo:

```php
Mail::to($email)->send(new SomeMailable(...));
```

Esta capa asigna destinatarios al Mailable antes de ejecutar el envío.

Es importante para entender código como:

```php
$this->to['address']
```

dentro de algunos Mailables del proyecto.

---

# Templates de correo

El theme contiene:

```text
mail/
├── contact.php
└── layout.php
```

Estos archivos son vistas HTML/PHP utilizadas por los Mailables.

---

## `mail/contact.php`

Es una vista de contacto que recibe:

```php
$input
```

y utiliza valores como:

```text
name
email
phone_number
message
intention
```

También utiliza datos de WordPress como:

```text
site URL
site name
template URL
```

y un logo del theme.

### Consideración de seguridad

Los valores de formulario se imprimen directamente.

Si esta vista se conserva debe aplicarse escape apropiado según el contexto, por ejemplo:

```php
esc_html()
```

También debe validarse que valores como:

```text
intention
```

tengan el tipo esperado antes de procesarlos.

---

## `mail/layout.php`

Actualmente contiene únicamente un mensaje simple:

```text
Gracias por contactarnos.
```

Aunque se llama `layout.php`, no funciona como un layout reutilizable con slots o composición.

Debe entenderse como una vista mínima / placeholder.

---

# Ejemplo activo: Contact AJAX

El blank incluye:

```text
App\Http\Ajax\ContactAjax
```

que conecta el ejemplo de formulario con el sistema de correo.

Su flujo es:

```text
ContactAjax
    ↓
ContactMail
AdminContactMail
    ↓
Mailable
    ↓
Mailer / SMTPMailer
    ↓
mail/*
```

---

# Dirección de contacto y configuración Mailgun

`ContactAjax` consulta:

```php
get_theme_support('CLTVO_USEMAILGUN');
```

Cuando la flag está activa intenta recuperar del plugin Mailgun:

```text
from-address
```

y utiliza ese valor como dirección para el envío administrativo.

Por lo tanto, en este ejemplo histórico el campo **From** del plugin termina participando también en la determinación del destinatario administrativo.

Esto mezcla dos conceptos diferentes:

```text
From      → quién envía
Recipient → quién recibe
```

y debe revisarse en un refactor.

---

## Fallback histórico

Cuando Mailgun no está activo, `ContactAjax` utiliza:

```text
admin@elcultivo.mx
```

como dirección administrativa.

Además `App\Contacto` contiene otro fallback histórico:

```text
info@zonapaz.com
```

para el correo de contacto.

Ambos valores son específicos de implementaciones anteriores y no deben permanecer como defaults de un boilerplate genérico.

---

# Bug en `ContactAjax`

La clase declara:

```php
private $error_messages = [...]
```

pero `getMailgunFromAddress()` consulta:

```php
$error_messages
```

en lugar de:

```php
$this->error_messages
```

Esto es un bug de la implementación actual.

---

# `is_plugin_active()`

`ContactAjax` utiliza:

```php
is_plugin_active('mailgun/mailgun.php')
```

para validar el plugin.

`is_plugin_active()` pertenece a las funciones administrativas de plugins de WordPress y no siempre está cargada automáticamente en requests frontend.

Antes de reutilizar este flujo debe verificarse/cargarse correctamente la función o utilizar una estrategia más robusta para comprobar la integración.

---

# Mailables del ejemplo

## `ContactMail`

Obtiene el remitente desde:

```text
App\Contacto
    ↓
CltvoSocialNet
    ↓
mail
```

y utiliza como asunto:

```text
Gracias por contactarnos
```

Su vista default termina siendo:

```text
mail/layout.php
```

---

## `AdminContactMail`

Utiliza:

```text
mail/contact.php
```

y asunto:

```text
Información de contacto
```

Su implementación actual utiliza el destinatario asignado como parte del `from`, comportamiento que también debe revisarse semánticamente.

---

# Relación con `CltvoSocialNet`

Históricamente el correo de contacto también podía almacenarse mediante:

```text
App\Metaboxes\CltvoSocialNet
```

sobre la Special Page:

```text
contacto
```

Sin embargo, el registro de ese metabox está actualmente comentado en:

```text
MetaboxServiceProvider
```

Por lo tanto el sistema de contacto actual mezcla piezas activas con configuración histórica parcialmente deshabilitada.

---

# `WP_DEBUG`

La documentación anterior indicaba que:

```php
WP_DEBUG
```

debía ser `false` para que el correo funcionara correctamente.

La revisión del código permite precisar el motivo:

```text
CLTVO_USEMAILGUN = true
    ↓
WP_DEBUG = true
    → SMTPMailer

WP_DEBUG = false
    → Mailer
```

Por lo tanto `WP_DEBUG` no sólo controla debugging: actualmente también cambia la implementación de transporte.

### Problema arquitectónico

El entorno de debugging no debería decidir implícitamente qué proveedor de correo se utiliza.

Esta relación es candidata prioritaria a refactor.

---

# Configuración actual: qué NO asumir

No debe documentarse como regla universal:

```text
false = WP Engine
true = Mailgun
```

porque el código del framework no expresa esa relación de forma directa.

Tampoco debe asumirse que instalar o activar el plugin Mailgun es suficiente.

Cada proyecto debe confirmar:

```text
plugin activo
credenciales/configuración
dominio/remitente
destinatario
wp_mail funcionando
entorno
```

---

# Flujo real resumido

```text
Formulario
    ↓
ContactAjax
    ↓
Mail::to(...)
    ↓
MailableMailer
    ↓
ContactMail / AdminContactMail
    ↓
Mailable::send()
    ↓
CLTVO_USEMAILGUN + WP_DEBUG
    ↓
Mailer ----------------→ wp_mail() → plugins / servidor
o
SMTPMailer ------------→ PHPMailer → SMTP configurado
```

---

# Estado de la integración

| Componente | Estado |
| --- | --- |
| Plugin Mailgun | Integración histórica activa/configurable |
| `CLTVO_USEMAILGUN` | Activa, pero nombre/comportamiento confusos |
| `Mailer` | Activo; bug en CC/BCC |
| `SMTPMailer` | Activo bajo una combinación específica; requiere refactor de seguridad |
| PHPMailer | Dependencia Composer vigente |
| `ContactAjax` | Activo; contiene bug y fallbacks históricos |
| `ContactMail` | Activo en ejemplo Contact |
| `AdminContactMail` | Activo en ejemplo Contact |
| `mail/contact.php` | Activo; necesita escape |
| `mail/layout.php` | Placeholder |
| `CltvoSocialNet` | Implementación existente; UI no registrada actualmente |

---

# Recomendaciones para proyectos actuales

Mientras el módulo no sea refactorizado:

1. No asumir el proveedor de envío únicamente por la flag.
2. Revisar la configuración de `wp_mail()` y Mailgun en el entorno real.
3. No introducir nuevas credenciales dentro del repositorio.
4. Probar el envío después de cada deploy/configuración.
5. Definir explícitamente remitente y destinatario.
6. Evitar reutilizar los fallbacks históricos del blank.
7. Revisar el comportamiento con `WP_DEBUG` antes de utilizar el SMTP interno.
8. No tomar `ContactAjax` como implementación final sin corregir sus bugs.

---

# Refactor prioritario

El sistema debería evolucionar hacia una configuración explícita:

```text
transport
from
reply-to
recipient
credentials/config externa
```

sin utilizar:

```text
WP_DEBUG
```

como selector de transporte.

Una dirección conceptual futura podría ser:

```text
config/mail.php
    ↓
transport seleccionado explícitamente
    ↓
wp_mail / Mailgun / SMTP
```

pero esto es únicamente una propuesta de refactor y **no describe el comportamiento actual**.

---

# Historial documentado

| Versión | Cambio |
| :--- | :--- |
| 3.0 | Sin Mailgun; el ejemplo de contacto dependía de `CltvoSocialNet`. |
| 3.2 | Se incorpora el plugin Mailgun. El campo `from` participa en el flujo de contacto. |
| 3.2.1 | Se introduce `CLTVO_USEMAILGUN` en `functions.php`. |

> El historial anterior proviene de la documentación existente. La revisión actual corrige la descripción del comportamiento con base en la implementación presente del framework y del ejemplo Contact.
