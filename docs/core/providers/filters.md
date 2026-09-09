# FiltersServiceProvider
**Estado:** En revisión

## Descripción general

`FiltersServiceProvider` centraliza filtros globales aplicados por el blank theme mediante la API de filtros de WordPress.

Se encuentra en:

```text
app/Providers/FiltersServiceProvider.php
```

Actualmente registra filtros para:

- ocultar la barra de administración en el frontend;
- registrar la query var personalizada `starts_with`;
- modificar el `WHERE` de determinadas consultas de WordPress para filtrar contenidos cuyo título comienza con un valor determinado.

El flujo general es:

```text
WordPress
    ↓
FiltersServiceProvider
    ↓
boot()
    ↓
add_filter()
    ↓
callback del provider
    ↓
valor filtrado
```

---

# Barra de administración

El provider registra el filtro:

```php
show_admin_bar
```

y devuelve:

```php
false
```

Esto deshabilita la barra de administración de WordPress en el frontend.

El comportamiento es global para el tema mientras el provider permanezca activo.

---

# Query var `starts_with`

El provider agrega una query var personalizada llamada:

```text
starts_with
```

mediante el filtro:

```php
query_vars
```

Esto permite utilizar el valor dentro de consultas de WordPress.

Ejemplo conceptual:

```php
$query = new WP_Query([
    'post_type'   => 'post',
    'starts_with' => 'A',
]);
```

La intención es obtener contenidos cuyo título comience con el valor indicado.

---

## Modificación de `posts_where`

El filtro:

```php
posts_where
```

permite modificar la cláusula `WHERE` generada por WordPress.

Cuando la consulta contiene:

```text
starts_with
```

el provider agrega una condición equivalente a:

```sql
post_title LIKE 'A%'
```

Por lo tanto, el flujo es:

```text
WP_Query
    ↓
starts_with = A
    ↓
query_vars
    ↓
posts_where
    ↓
post_title LIKE 'A%'
    ↓
resultados cuyo título inicia con A
```

Un caso de uso típico sería un directorio alfabético.

Ejemplos:

```text
A → Apple, Alfa, Atlas
B → Banco, Beta
C → Casa, Consultoría
```

---

## Cuándo utilizar filtros

Los filtros de WordPress son adecuados cuando se necesita **modificar un valor o comportamiento que WordPress ya está procesando**.

La idea general es:

```text
WordPress genera un valor
        ↓
aplica_filter()
        ↓
nuestro callback recibe el valor
        ↓
lo modifica o conserva
        ↓
WordPress continúa
```

Por ejemplo:

```text
query vars
SQL de una consulta
contenido de un post
excerpt
clases HTML
URLs
argumentos de una funcionalidad
datos antes de mostrarse
```

Un filtro no debería utilizarse simplemente para ejecutar una tarea independiente. Para ese tipo de comportamiento normalmente corresponde una Action.

---

# Filters vs Actions

Una regla práctica:

```text
¿Necesito modificar algo que WordPress ya me entrega?
    ↓
Filter

¿Necesito ejecutar algo cuando sucede un evento?
    ↓
Action
```

Ejemplos:

```text
Modificar el excerpt
→ Filter

Agregar una variable reconocida por WP_Query
→ Filter

Modificar una consulta
→ Filter

Registrar un CPT durante init
→ Action

Ejecutar lógica al guardar un post
→ Action
```

---

## Cómo agregar un nuevo filtro al provider

Un filtro global del tema puede registrarse desde `boot()`:

```php
public function boot()
{
    add_filter('example_filter', [$this, 'exampleFilter']);
}
```

y manejarse en un método separado:

```php
public function exampleFilter($value)
{
    return $value;
}
```

Siempre debe respetarse la firma que WordPress espera para el filtro utilizado.

Si el filtro recibe más argumentos, también deben declararse al registrarlo:

```php
add_filter('example_filter', [$this, 'exampleFilter'], 10, 2);
```

---

## Ejemplo práctico: modificar excerpts

Conceptualmente, si un proyecto necesitara cambiar globalmente la longitud del excerpt:

```php
add_filter('excerpt_length', [$this, 'excerptLength']);

public function excerptLength($length)
{
    return 30;
}
```

Esto sería apropiado para `FiltersServiceProvider` porque se está modificando un valor que WordPress ya calcula.

---

## Ejemplo práctico: limitar una consulta

El comportamiento existente de `starts_with` es un ejemplo de cómo extender las consultas estándar de WordPress sin construir SQL completamente separado.

Conceptualmente:

```php
new WP_Query([
    'post_type'   => 'empresa',
    'starts_with' => 'G',
]);
```

puede utilizarse para:

- directorios de empresas;
- listados de abogados;
- glosarios;
- autores;
- catálogos alfabéticos;
- índices de contenidos.

---

## Seguridad al modificar SQL

El provider actual modifica `posts_where` para implementar `starts_with`.

Cuando se agreguen nuevos filtros que incorporen valores dinámicos al SQL, se debe utilizar la API de `$wpdb` para preparar y escapar correctamente los valores.

Para código nuevo, preferir patrones como:

```php
$where .= $wpdb->prepare(
    " AND {$wpdb->posts}.post_title LIKE %s",
    $wpdb->esc_like($starts_with) . '%'
);
```

No se recomienda concatenar directamente valores recibidos desde una petición o consulta.

---

## Cuándo NO agregar un filtro global

No todo filtro merece vivir en `FiltersServiceProvider`.

Antes de agregar uno debe revisarse su alcance.

Un filtro global tiene sentido cuando:

- forma parte del comportamiento base del tema;
- aplica a varias plantillas o componentes;
- debe registrarse en cada request correspondiente;
- representa una regla transversal del proyecto.

Si el comportamiento pertenece únicamente a una feature concreta, puede ser más claro mantenerlo junto a esa funcionalidad.

---

## Consideraciones

- Los filtros reciben un valor y deben devolver el valor resultante.
- Evitar efectos secundarios innecesarios dentro de filtros.
- Revisar siempre la documentación del hook para conocer sus argumentos.
- Mantener callbacks pequeños y con una responsabilidad clara.
- Para eventos o procesos que no modifican un valor existente, utilizar Actions.
- Los filtros SQL requieren especial cuidado con sanitización, escaping y `$wpdb->prepare()`.
- `starts_with` es una extensión propia del blank theme, no una query var nativa de WordPress.
