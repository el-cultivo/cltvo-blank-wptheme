cltvo_blank_wptheme
===================

* Este es un tema de wordpress que se utiliza en la mayoría de los proyectos del cultivo, hay variaciones de este pero básicamente siguen la misma estructura.

## Proceso de instalación general del tema
* 1.- Instalar la última versión de WP y eliminar los temas que trae por default
* 2.- Dentro de la carpeta de temas, descargar este proyecto y al finalizar la descarga, eliminar la carpeta .git (para que no sufra modificaciones éste repositorio).
* 3.- Cambiar el nombre de la carpeta recién instalada/descargada a nombre-proyecto-theme
* 4.- Dentro de la carpeta del tema (la del paso anterior) se deben correr los comandos`npm install`y`composer install`
* 5.- En el administrador del sitio de WP, en el apartado de apariencia debemos activar el tema

## Variaciones entre versiones del blank theme
* Versión 3

	- Compilación: Para compilar se debe ocupar el comando`gulp watch`
	- Compilación: Para esta versión, el tema ocupa gulp 3.* , por lo que los proyectos (con esta versión) tienen el gulpfile para ejecutar las tareas de compilación de CSS y JS
	- Compilación: Para poder ejecutar el gulpfile correctamente se debe tener instalada una versión de node menor a la 9 (si no es así, gulpfile 3 pedirá actualizar a su versión 4 donde cambia completamente la forma de ejecutar las tareas y esto impide compilar los archivos)
	- Email: Para agregar al administrador un campo con el correo de contacto, se debe descomentar la clase `CltvoSocialNet`en el archivo`MetaboxServiceProvider`dicho campo aparecerá en la página de contacto del administrador
	- Plugins: En esta versión, los plugins se agregan al repositorio, por lo que para actualizar un plugin o agregar un plugin, se debe hacer desde local y después hacer push al repositorio
	- Plugins: Los ACF no se actualizan automáticamente, por lo que una adición o actualización de un campo, se tiene que hacer manualmente (Exportar los acf e importarlos en el sitio de desarrollo o de producción). Además se debe verificar que los ACF se importaron de la manera correcta (en el page o post correspondiente)


* Versión 3.2
	- Compilación: Para compilar se debe ocupar el comando `npm run watch`
	- Compilación: Para esta versión, el tema ocupa laravel-mix sin importar la versión de node
	- Plugins: Se ocupa el `TGM Plugin Activation` como una lista para agregar los plugins sugeridos y obligatorios en el proyecto sin tener que subirlos al repositorio
	- Plugins: Los ACF se agregan automáticamente (cuando ya se activó el tema) en una carpeta llamada acf-json (dentro del tema), por lo que ya no es necesario exportar e importar los ACF cuando se ocupa esta versión.
	- Plugins: Se agrega un nuevo campo de location en las opciones de los ACF (cuando se selecciona en dónde irá el campo) ese campo se llama Special Pages y sirve para que no se pierda la configuración de la correspondencia de los campos entre las pages
	- Email: Para esta versión se ocupa el plugin de Mailgun, y del "from" en la configuración de este plugin, se obtiene rl correo a donde se enviará la información de contacto

* Versión 3.2.1
	- Para definir el uso de Mailgun en el envío de correos, es necesario modificar la variable para el tema CLTVO_USEMAILGUN en functions.php y ponerla en true. Por defecto se usa el de WP Engine así que la variable se encuentra siempre en false.

* Versión 3.2.2
	- Se agrega en functions.php la función cltvo_role_edit(), para otorgarle permiso al rol de editor de poder administrar los menús

* Versión 3.3 (Actual)
	- Se agrega la carpeta de languages al .gitignore para proyectos con multiples idiomas

	- Sincronización de ACF controlada (IMPORTANTE)
	  	-	Se elimina el sync automático de ACF al cargar admin
		-	Se agrega herramienta manual: `Tools → ACF JSON Sync`
	  	-	¿Por qué? Se detectaron problemas en proyectos con: WPML (múltiples idiomas), duplicación de Field Groups, inconsistencias entre JSON y DB
		-	Nueva estrategia: El source of truth = acf-json; Sync manual bajo demanda; Validaciones para evitar corrupción.
	
		-	Qué hace el sync:
				-	Importa solo si: no existe en DB, o JSON es más reciente. NO elimina Field Groups (evita pérdida de info) y detecta huérfanos (DB ≠ JSON)

	- Compatibilidad ACF + WPML (CRÍTICO)
 		- Problema: ACF + WPML guarda JSON por idioma:

				acf-json/
					├── en/
					├── es/
     
		  Esto provoca: Fields no visibles en otros idiomas; Sync incompleto; Duplicaciones.

	    - Solución implementada
			Se extiende la configuración de ACF `acf/settings/load_json` para incluir todos los subdirectorios dentro de acf-json incluyendo:
   
				/acf-json
				/acf-json/en
				/acf-json/es
				cualquier subfolder

		- Resultado:
				- ACF siempre ve todos los field groups
				- Sync consistente entre idiomas

	- Detección de Field Groups huérfanos

		Se agregó validación que detecta:

		Field Groups que existen en DB pero ya no tienen JSON

		Esto ayuda a:
			•	limpiar basura histórica
			•	evitar duplicaciones invisibles

		Importante:
			•	NO se eliminan automáticamente
			•	se deben revisar manualmente

	- Nuevas Flags del tema (functions.php):

		`add_theme_support('title-tag');`
		`add_theme_support('CLTVO_USEMAILGUN', false);`
		`add_theme_support('CLTVO_DISABLE_COMMENTS', true);`

			title-tag
				•	Permite que plugins como Yoast controlen el <title>
	
			CLTVO_USEMAILGUN
				•	Define si se usa Mailgun o el mailer del server
	
			CLTVO_DISABLE_COMMENTS
				•	Desactiva comentarios globalmente
				•	Puede configurarse por excepción: `['except' => ['post']]`
   

## Notas importantes
* Para enviar correctamente los correos la constante WP_DEBUG debe estar en false
* En versiones de producción, se deben actualizar las versiones de los css y js esto se hace cambiando el número en el quinto parámetro ('0.0') de las funciones `wp_register_style` y `wp_register_script`
* ACF + WPML
	•	Siempre trabajar con JSON como fuente principal
	•	Evitar editar directamente en producción sin sync
	•	Usar el tool de sync después de:
		•	pull
		•	deploy
		•	cambio de idioma

## Estado actual
* Sync funcional
* En investigación: edge cases con WPML y posibles duplicaciones históricas
* Se recomienda monitoreo en proyectos activos

## Alertas
* Se identificó que las alertas de Dependabot relacionadas con PHPMailer provienen de una dependencia declarada directamente en el `composer.json` del blank theme (`phpmailer/phpmailer: ^6.0`). El riesgo no está en el constraint por sí solo, sino en la versión resuelta en composer.lock, que quedó desactualizada.
* En proyectos activos, la revisión debe hacerse caso por caso según su composer.lock y uso real de la dependencia.

## Recomendaciones
* Siempre hacer backup antes de sync masivo
* No confiar en DB como fuente de verdad
* Revisar huérfanos periódicamente
* Evitar editar ACF directo en producción sin control

## Performance
Dentro de este modulo se busca tener una mejora del performance implementada para todos los proyectos futuros que se realicen con blank theme, buscado tener una consistencia con todo lo que se ha ido realizando.

* Los archivos que se incluyen son los siguientes (todos se encuentran dentro de la carpeta includes/performance):
```
includes/
└── performance/
    ├── performance.php   — Punto donde se cargan los archivos requeridos, si se requiere agregar uno nuevo, crear el archivo y agregarlo en este archivo
    ├── scripts.php       — Se utiliza defer para los archivos internos y partytown para los externos que esten definidos en el archivo analytics.php
    ├── images.php        — tamaños de imagen y helpers
    └── head.php          — preconnects y preload del LCP
└── analytics.php         — scripts de tracking del proyecto
```

### Configuración 
Las baderas del módulo se controlan desde functions.php: (Esta bandera solo aplica para scripts integrados en el archivo analytics.php)
```
add_theme_support('CLTVO_PARTYTOWN', true);
```

Si en dado caso se es requerido tener un mid / mid que algunos utilicen y otros no se puede realizar de la siguiente forma:
1. **Apagar todo** — cambiar `CLTVO_PARTYTOWN` a `false` en `functions.php`. 
   Todos los scripts de `analytics.php` regresan a `text/javascript` automáticamente.

2. **Excluir un script específico** — usar `text/javascript` directo en ese script 
   en lugar de `$type`. El resto sigue usando Partytown normalmente.


### Scripts
Los scripts internos registrados en WordPress reciben defer automaticamente. Si algún handle falla con defer, agregarlo al array $exclude en scripts.php

Los scripts externos como se menciona anteriormente, iran dentro de analytics.php usando la variable $type, dentro de la sección de Analytics se encuentra una explicación más explicita.

### Imágenes
#### Reglas generales a tomar en cuenta

* ACF en campos de imagen deben retornar un ID, no debe retornar un array o URL.
* No utilizar el tamaño full ni la URL original, usar cltvo-xl como máximo.
* Los tamaños están generados al 2x para cubrir pantallas Retina. ShortPixel se encarga del peso y de servir WebP.

#### Tamaños disponibles

Los tamaños base se definen por proyecto según los breakpoints del diseño.  
* Ajustar en images.php:

```
add_image_size('cltvo-sm', 1280, 9999, false); // 640 * 2
add_image_size('cltvo-md', 2048, 9999, false); // 1024 * 2
add_image_size('cltvo-lg', 2880, 9999, false); // 1440 * 2
add_image_size('cltvo-xl', 2560, 9999, false); // Tope máximo
```

### Helper de imagen

Usa siempre `wp_get_attachment_image()` para imprimir imágenes que vienen de ACF. 
Nunca uses la URL directa ni `the_post_thumbnail()`.

¿Por qué? Porque esta función genera automáticamente:
- El `srcset` con todos los tamaños registrados, para que el navegador descargue solo 
  el tamaño que necesita según la pantalla.
- Los atributos `width` y `height`, que le dicen al navegador cuánto espacio reservar 
  antes de que cargue la imagen, evitando los saltos de layout (CLS).

En ACF el campo de imagen siempre debe estar configurado para retornar **ID**, no array ni URL.

**Contenido general:**
```php
wp_get_attachment_image(get_field('card_img'), 'cltvo-md', false, ['class' => 'card__img']);
```

**Hero (LCP)**: La imagen más grande visible al cargar la página. `fetchpriority high` 
le dice al navegador que la descargue antes que cualquier otra cosa. `loading eager` 
desactiva el lazy load para que no espere a que el usuario haga scroll hasta ella:
```php
wp_get_attachment_image(get_field('hero_img'), 'cltvo-lg', false, [
    'fetchpriority' => 'high',
    'loading'       => 'eager',
]);
```

**Slider**: Solo el primer slide lleva prioridad alta porque es el único visible al cargar. 
Los demás se cargan en lazy para no bloquear el render de la página:
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

**Art direction**: Cuando la composición de la imagen cambia completamente entre mobile 
y desktop (no solo el tamaño), por ejemplo un hero muy horizontal en desktop que se vuelve 
vertical en móvil. En ese caso el srcset automático no es suficiente, se necesitan dos campos 
separados en ACF y controlar la visibilidad con CSS:
```php
wp_get_attachment_image(get_field('hero_img_desktop'), 'cltvo-lg', false, ['class' => 'hero__img--desktop']);
wp_get_attachment_image(get_field('hero_img_mobile'), 'cltvo-sm', false, ['class' => 'hero__img--mobile']);
```

### Helper de video

Úsalo cuando el hero sea un video en lugar de una imagen. Genera automáticamente 
el elemento `<video>` con los atributos de performance recomendados.

Requiere dos campos en ACF:
- Campo de video → retornar **URL**
- Campo de imagen poster → retornar **ID** — se muestra mientras el video carga, 
  evita que el usuario vea un fondo negro.

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

### Head 

#### Preconnects
Le dice al navegador que abra conexión con dominios externos antes de que los necesite, 
ahorrando milisegundos al cargar fuentes, CDNs e imágenes. Agregar o quitar según lo 
que use el proyecto en `head.php`:

```php
$links = [
    'https://fonts.googleapis.com',
    'https://fonts.gstatic.com',
    // 'https://cdn-proyecto.s3.amazonaws.com', // Descomentar si el proyecto usa S3
];
```

### LCP Preload

Le dice al navegador que descargue la imagen del hero antes que cualquier otra cosa, 
mejorando el LCP. Cambiar `hero_img` por el nombre real del campo ACF en `head.php`:

```php
$img_id = get_field('hero_img', $current_id);
```

## Analytics

Todo lo que se agregue en `analytics.php` usa la variable `$type` para determinar si 
va a Partytown o no. Partytown corre los scripts de tracking en un web worker, liberando 
el hilo principal del navegador y mejorando el TBT. No usar `text/javascript` directamente.

Si la plataforma usa un objeto global (ej. `fbq`, `ttq`), agregarlo al forward de 
Partytown en `scripts.php` para que el web worker lo intercepte correctamente:

```php
echo 'partytown = { forward: ["dataLayer.push", "fbq"] };';
```


