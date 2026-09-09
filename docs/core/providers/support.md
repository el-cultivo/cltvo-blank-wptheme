# SupportServiceProvider
**Estado:** En revisión

## Descripción general

`SupportServiceProvider` centraliza los soportes nativos de WordPress habilitados por el blank theme.

Se encuentra en:

```text
app/Providers/SupportServiceProvider.php
```

Durante `boot()` utiliza:

```php
add_theme_support()
```

para declarar capacidades que el tema implementa.

---

# Featured Images

El provider habilita:

```php
add_theme_support('post-thumbnails');
```

Esto permite utilizar Featured Images en los Post Types que soporten thumbnails.

Una vez habilitado el soporte del tema pueden utilizarse funciones nativas como:

```php
has_post_thumbnail()
get_the_post_thumbnail()
the_post_thumbnail()
```

La disponibilidad final también depende de los supports declarados por cada Post Type.

---

# HTML5

El provider habilita salida HTML5 para:

```php
add_theme_support('html5', [
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption',
]);
```

Esto indica a WordPress que el tema soporta markup HTML5 para esos componentes.

Actualmente contempla:

```text
comment-list
comment-form
search-form
gallery
caption
```

---

# Agregar soporte adicional

Si el blank theme necesita declarar una nueva capacidad nativa, este provider es el lugar natural para mantenerla centralizada.

Ejemplos de soportes que WordPress puede manejar mediante `add_theme_support()` incluyen distintas capacidades del theme; antes de agregar una nueva debe revisarse que el frontend realmente la implemente.

Conceptualmente:

```php
public function boot()
{
    add_theme_support('post-thumbnails');

    // nuevos soportes globales del theme
}
```

---

## Flujo general

```text
SupportServiceProvider
    ↓
boot()
    ↓
add_theme_support()
    ├── post-thumbnails
    └── html5
        ├── comment-list
        ├── comment-form
        ├── search-form
        ├── gallery
        └── caption
```

---

## Consideraciones

- `add_theme_support()` declara capacidades del tema ante WordPress.
- Este provider debe reservarse para soportes globales del theme.
- Habilitar un soporte no sustituye la implementación correspondiente en templates o componentes.
- Los Post Types pueden necesitar además declarar sus propios `supports`.
- Centralizar estas declaraciones evita distribuirlas en `functions.php`.
