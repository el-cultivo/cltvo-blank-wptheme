# ScriptsServiceProvider
**Estado:** En revisión

## Descripción general

`ScriptsServiceProvider` centraliza el registro y carga de los scripts utilizados por el tema.

Se encuentra en:

```text
app/Providers/ScriptsServiceProvider.php
```

El provider permite registrar dependencias externas mediante CDN y scripts locales, manteniendo un orden de dependencias antes de cargar el archivo JavaScript principal del tema.

---

## Flujo general

La carga de scripts sigue conceptualmente este orden:

```text
jquery
    ↓
scripts CDN
    ↓
scripts locales
    ↓
js/functions.js
```

Cada script registrado se agrega a la lista de dependencias utilizada por el siguiente, permitiendo conservar el orden configurado.

---

## Script principal

El archivo JavaScript principal del tema se registra con el identificador:

```php
cltvo_functions_js
```

y se carga desde:

```text
/js/functions.js
```

Este archivo depende de los scripts registrados previamente por el provider.

---

## Variables disponibles en JavaScript

El provider utiliza `wp_localize_script()` para exponer información generada desde PHP al JavaScript del tema.

Las variables se encuentran disponibles mediante:

```js
cltvo_js_vars
```

Actualmente incluye valores como:

```text
site_url
template_url
ajax_url
```

Esto permite acceder desde JavaScript a rutas necesarias sin declararlas manualmente.

Por ejemplo, `ajax_url` puede utilizarse junto con las acciones registradas mediante `AjaxServiceProvider` para realizar peticiones a `admin-ajax.php`.

---

## Carga en el footer

Los scripts registrados por este provider se configuran para cargarse en el footer.

WordPress los imprime mediante:

```php
wp_footer();
```

presente en el footer del tema.

---

## Responsabilidad del provider

`ScriptsServiceProvider` se encarga del **registro, dependencias y carga en WordPress de los archivos JavaScript resultantes**.

La estructura del código JavaScript, ES6, dependencias de desarrollo, compilación, minificación y generación de `functions.js` corresponden al sistema de frontend/build del blank theme y se documentan por separado.

---

## Consideraciones

- Los scripts globales deben registrarse desde este provider.
- Debe conservarse el orden de dependencias cuando un script requiera otro previamente registrado.
- Las URLs o valores generados desde PHP que necesite JavaScript pueden centralizarse mediante `cltvo_js_vars`.
- Las acciones AJAX deben utilizar la URL proporcionada por `ajax_url` en lugar de declarar manualmente la ruta de `admin-ajax.php`.
- La carga de assets y su proceso de compilación son responsabilidades diferentes.
