# Mailgun

## Descripción

El plugin de Mailgun se usa para el envío de correos transaccionales (formularios de contacto y notificaciones del sitio). Disponible desde la versión 3.2 del boilerplate.

Por defecto, el tema usa el mailer del servidor (WP Engine). Mailgun debe activarse explícitamente por proyecto mediante una flag en `functions.php`.

---

## Configuración

### Activar Mailgun

En `functions.php`, cambiar la flag a `true`:

```php
add_theme_support('CLTVO_USEMAILGUN', true);
```

| Valor | Comportamiento |
| :--- | :--- |
| `false` (default) | Usa el mailer del servidor (WP Engine) |
| `true` | Usa Mailgun |

### Correo de destino

El correo al que se envía la información de contacto se obtiene del campo **From** en la configuración del plugin de Mailgun. Verificar que esté correctamente configurado tras instalar el plugin.

### WP_DEBUG

Para que el envío de correos funcione correctamente en cualquier entorno, `WP_DEBUG` debe estar en `false`:

```php
define('WP_DEBUG', false);
```

---

## Historial de cambios relevantes

| Versión | Cambio |
| :--- | :--- |
| 3.0 | Sin Mailgun. El correo de contacto se configura descomentando `CltvoSocialNet` en `MetaboxServiceProvider`. |
| 3.2 | Se incorpora el plugin de Mailgun. El campo `from` de su configuración determina el correo de destino. |
| 3.2.1 | Se introduce la flag `CLTVO_USEMAILGUN` en `functions.php` para controlar el mailer activo por proyecto. |