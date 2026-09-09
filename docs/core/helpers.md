# Helpers de aplicación
**Estado:** En revisión

## Descripción general

El archivo:

```text
app/helpers.php
```

funciona como punto central para **funciones auxiliares globales propias de cada proyecto**.

El blank theme incluye algunas funciones de ejemplo, son funciones de ejemplo incluidas en el blank theme y no es necesario conservarlas en cada proyecto. En proyectos nuevos el contenido puede limpiarse, reemplazarse o adaptarse según las necesidades de implementación.

La finalidad del archivo es evitar concentrar funciones auxiliares del proyecto directamente en `functions.php` y mantenerlas en una ubicación reconocible dentro de `app/`.

---

## Usos habituales

`app/helpers.php` puede utilizarse para funciones reutilizadas en distintas partes del proyecto, por ejemplo:

- obtener colecciones de Custom Post Types;
- construir consultas reutilizables;
- ordenar información;
- transformar o preparar datos antes de enviarlos a una vista;
- helpers compartidos entre templates o componentes;
- lógica auxiliar de búsquedas;
- funciones globales del proyecto que no justifican por sí mismas una clase o provider.

Ejemplo conceptual:

```php
function get_latest_events()
{
    return get_posts([
        'post_type'      => 'evento',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
    ]);
}
```

La implementación concreta depende de cada proyecto.

---

## Relación con `functions.php`

La separación recomendada es:

```text
functions.php
    ↓
arranque e inicialización del theme

app/helpers.php
    ↓
funciones auxiliares globales del proyecto
```

De esta forma `functions.php` puede mantenerse enfocado en el bootstrap del tema y las utilidades reutilizables permanecen dentro de la capa `app`.

---

## Cuándo utilizar `app/helpers.php`

Una función es buena candidata cuando:

```text
¿Se utiliza en distintas partes del proyecto?
            ↓
           sí
            ↓
¿No pertenece claramente a una clase,
provider o feature específica?
            ↓
           sí
            ↓
      app/helpers.php
```

Ejemplos habituales:

```text
obtener CPTs
ordenar colecciones
preparar información
queries compartidas
transformaciones reutilizables
```

---

## Cuándo NO utilizarlo

No toda función auxiliar debe convertirse en un helper global.

### Hooks globales de WordPress

Si el comportamiento consiste en registrar un hook global, primero debe revisarse si corresponde a:

```text
ActionsServiceProvider
FiltersServiceProvider
```

Ejemplo:

```php
add_filter('excerpt_length', ...);
```

debería formar parte del sistema de filtros y no agregarse a `app/helpers.php` únicamente para mantener limpio `functions.php`.

### Funciones de una feature específica

Si una función únicamente existe para una funcionalidad concreta, es preferible mantenerla cerca de esa implementación.

```text
feature específica
    ↓
helpers / lógica de esa feature
```

Esto evita convertir `app/helpers.php` en un archivo global demasiado grande.

### Lógica del framework

Las utilidades que deberían estar disponibles para todos los proyectos del blank theme pertenecen a la capa del framework, no a `app/helpers.php`.

La separación conceptual es:

```text
framework
    ↓
utilidades reutilizables entre proyectos

app/helpers.php
    ↓
utilidades propias del proyecto
```

### Lógica demasiado compleja

Si un conjunto de funciones empieza a manejar demasiado estado, reglas de negocio o múltiples responsabilidades, debe evaluarse convertirlo en una clase o servicio en lugar de seguir creciendo como helpers globales.

---

## Helpers incluidos por defecto

El archivo actual del blank theme contiene funciones de ejemplo relacionadas principalmente con:

- filtros por taxonomía;
- query strings;
- ordenamiento;
- paginación;
- `starts_with`;
- tipologías.

Estas funciones sirven como referencia de implementaciones anteriores, pero **no forman parte de una estructura obligatoria del blank theme y pueden eliminarse si el proyecto no las necesita**.

Por lo tanto, la documentación del blank theme debe considerar estable el **propósito de `app/helpers.php`**, no necesariamente las funciones que actualmente contiene.

---

## Convenciones recomendadas

- Mantener aquí únicamente helpers globales que realmente se reutilicen.
- Utilizar nombres descriptivos y evitar funciones demasiado genéricas.
- Evitar duplicar lógica que ya exista en WordPress o en el framework.
- Sanitizar valores provenientes de `$_GET`, `$_POST` u otras entradas cuando corresponda.
- Mantener las queries reutilizables en funciones con una responsabilidad clara.
- Evitar efectos secundarios inesperados: un helper debería ser predecible para quien lo invoque.
- Si una función deja de ser global y pasa a pertenecer a una feature concreta, moverla junto con esa feature.
- No utilizar `app/helpers.php` como reemplazo general de `functions.php`.

---

## Resumen

```text
app/helpers.php
    ↓
helpers globales del proyecto
    ├── obtención de CPTs
    ├── queries reutilizables
    ├── ordenamiento
    ├── preparación de datos
    └── utilidades compartidas

NO
    ├── hooks que pertenecen a Providers
    ├── lógica exclusiva de una feature
    ├── utilidades del framework
    └── lógica compleja que merece una clase
```
