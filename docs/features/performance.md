# Performance

## Descripción

Módulo transversal del boilerplate que estandariza las optimizaciones de performance en todos los proyectos. Cubre carga de scripts, manejo de imágenes, video y configuración del `<head>`. Disponible desde la versión 3.3.

---

## Estructura de archivos

```
includes/
└── performance/
    ├── performance.php   — Punto de entrada del módulo; carga los archivos requeridos
    ├── scripts.php       — Defer para scripts internos; Partytown para scripts externos
    ├── images.php        — Tamaños de imagen y helpers
    └── head.php          — Preconnects y preload del LCP
└── analytics.php         — Scripts de tracking del proyecto
```

Para agregar un nuevo archivo al módulo: crear el archivo dentro de `includes/performance/` y registrarlo en `performance.php`.

---

## Scripts

### Scripts internos

Todos los scripts internos registrados en WordPress reciben `defer` automáticamente. Si algún handle tiene conflictos con `defer`, agregarlo al array `$exclude` en `scripts.php`.

### Scripts externos (Partytown)

Los scripts externos de tracking van en `analytics.php` usando la variable `$type`. Partytown los corre en un web worker, liberando el hilo principal del navegador y mejorando el TBT (Total Blocking Time).

#### Activar / desactivar Partytown

La flag se controla desde `functions.php`:

```php
add_theme_support('CLTVO_PARTYTOWN', true);
```

| Valor | Comportamiento |
| :--- | :--- |
| `true` (default) | Scripts en `analytics.php` corren via Partytown |
| `false` | Scripts en `analytics.php` regresan a `text/javascript` automáticamente |

#### Excluir un script específico de Partytown

Usar `text/javascript` directamente en ese script dentro de `analytics.php`, en lugar de `$type`. El resto seguirá usando Partytown normalmente.

#### Objetos globales

Si la plataforma expone un objeto global (`fbq`, `ttq`, etc.), agregarlo al forward de Partytown en `scripts.php` para que el web worker lo intercepte correctamente:

```php
echo 'partytown = { forward: ["dataLayer.push", "fbq"] };';
```

---

## Imágenes

### Reglas generales

- Los campos de imagen en ACF deben estar configurados para retornar **ID**, nunca array ni URL.
- No usar el tamaño `full` ni la URL original. El tamaño máximo permitido es `cltvo-xl`.
- Los tamaños están definidos al 2x para cubrir pantallas Retina. ShortPixel se encarga del peso y de servir WebP.

### Tamaños disponibles

Los tamaños base se ajustan por proyecto según los breakpoints del diseño, en `images.php`:

```php
add_image_size('cltvo-sm', 1280, 9999, false); // 640px * 2
add_image_size('cltvo-md', 2048, 9999, false); // 1024px * 2
add_image_size('cltvo-lg', 2880, 9999, false); // 1440px * 2
add_image_size('cltvo-xl', 2560, 9999, false); // Tope máximo
```

### Helper de imagen

Usar siempre `wp_get_attachment_image()` para imprimir imágenes provenientes de ACF. No usar la URL directa ni `the_post_thumbnail()`.

Esta función genera automáticamente el `srcset` con todos los tamaños registrados y los atributos `width` y `height`, evitando saltos de layout (CLS).

#### Contenido general

```php
wp_get_attachment_image(get_field('card_img'), 'cltvo-md', false, ['class' => 'card__img']);
```

#### Hero (LCP)

La imagen más grande visible al cargar la página. `fetchpriority high` le dice al navegador que la descargue antes que cualquier otra cosa. `loading eager` desactiva el lazy load.

```php
wp_get_attachment_image(get_field('hero_img'), 'cltvo-lg', false, [
    'fetchpriority' => 'high',
    'loading'       => 'eager',
]);
```

#### Slider

Solo el primer slide lleva prioridad alta; los demás se cargan en lazy para no bloquear el render:

```php
foreach ($slides as $i => $slide) {
    $attrs = ['class' => 'slide__img'];
    if ($i === 0) {
        $attrs['fetchpriority'] = 'high';
        $attrs['loading']       = 'eager';
    }
    wp_get_attachment_image(get_field('slide_img', $slide), 'cltvo-lg', false, $attrs);
}
```

#### Art direction

Cuando la composición de la imagen cambia completamente entre mobile y desktop (no solo el tamaño), el `srcset` automático no es suficiente. Se necesitan dos campos separados en ACF con visibilidad controlada por CSS:

```php
wp_get_attachment_image(get_field('hero_img_desktop'), 'cltvo-lg', false, ['class' => 'hero__img--desktop']);
wp_get_attachment_image(get_field('hero_img_mobile'), 'cltvo-sm', false, ['class' => 'hero__img--mobile']);
```

---

## Video

Usar cuando el hero sea un video en lugar de una imagen. Genera el elemento `<video>` con los atributos de performance recomendados.

Requiere dos campos en ACF:
- **Campo de video** → retornar **URL**
- **Campo de imagen poster** → retornar **ID** — se muestra mientras el video carga, evitando que el usuario vea un fondo negro

```php
cltvo_video($video_url, $poster_id, $attrs = []);
```

```php
cltvo_video(
    get_field('hero_video'),
    get_field('hero_poster'),
    ['class' => 'hero__video']
);
```

---

## Head

### Preconnects

Abren conexión con dominios externos antes de que el navegador los necesite, ahorrando tiempo al cargar fuentes, CDNs e imágenes. Agregar o quitar según lo que use el proyecto en `head.php`:

```php
$links = [
    'https://fonts.googleapis.com',
    'https://fonts.gstatic.com',
    // 'https://cdn-proyecto.s3.amazonaws.com', // Descomentar si el proyecto usa S3
];
```

### LCP Preload

Le indica al navegador que descargue la imagen del hero antes que cualquier otro recurso, mejorando el LCP. Cambiar `hero_img` por el nombre real del campo ACF del proyecto en `head.php`:

```php
$img_id = get_field('hero_img', $current_id);
```