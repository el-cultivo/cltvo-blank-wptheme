# StylesServiceProvider
**Estado:** En revisión

## Descripción general

`StylesServiceProvider` centraliza el registro y carga de las hojas de estilo utilizadas por el tema.

Se encuentra en:

```text
app/Providers/StylesServiceProvider.php
```

El provider permite registrar estilos externos mediante CDN y estilos locales, manteniendo sus dependencias antes de cargar la hoja de estilos principal del tema.

---

## Flujo general

La carga de estilos sigue conceptualmente este orden:

```text
styles CDN
    ↓
styles locales
    ↓
style.css
```

Los estilos registrados previamente se utilizan como dependencias de la hoja principal, permitiendo que WordPress conserve el orden de carga.

---

## Hoja de estilos principal

La hoja principal del tema se registra con el identificador:

```php
cltvo_style_css
```

y se carga desde:

```text
/style.css
```

El archivo recibe como dependencias los estilos registrados previamente por el provider.

---

## Registro en WordPress

Los estilos son registrados y cargados mediante el hook:

```php
wp_enqueue_scripts
```

Aunque el nombre del hook hace referencia a scripts, WordPress utiliza este mismo hook para realizar el enqueue de scripts y estilos del frontend.

Los estilos terminan siendo impresos dentro del documento mediante:

```php
wp_head();
```

presente en el header del tema.

---

## Responsabilidad del provider

`StylesServiceProvider` se encarga del **registro, dependencias y carga en WordPress de los archivos CSS resultantes**.

La arquitectura de CSS/SCSS, framework de estilos, dependencias de desarrollo, compilación, minificación y generación de `style.css` corresponden al sistema de frontend/build del blank theme y se documentan por separado.

---

## Consideraciones

- Los estilos globales deben registrarse desde este provider.
- Los estilos externos o locales que requiera `style.css` deben declararse respetando sus dependencias.
- La hoja principal se carga después de las dependencias registradas.
- La carga de assets en WordPress y el proceso mediante el cual estos assets son compilados son responsabilidades diferentes.
