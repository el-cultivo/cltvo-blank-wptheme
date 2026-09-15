# MetaboxServiceProvider
**Estado:** En revisión

## Descripción general

`MetaboxServiceProvider` centraliza el registro de metaboxes propios del blank theme.

Se encuentra en:

```text
app/Providers/MetaboxServiceProvider.php
```

Las clases que deben inicializarse se declaran en:

```php
protected $metaboxes = [
    /**\App\Metaboxes\CltvoSocialNet::class,**/
];
```

Durante `boot()` el provider instancia cada clase:

```php
foreach ($this->metaboxes as $metabox) {
    $object = new $metabox;
}
```

El registro real del metabox ocurre dentro de la clase base `Illuminate\Metabox`, por lo que el provider funciona principalmente como punto central para indicar qué metaboxes deben activarse.

El flujo general es:

```text
MetaboxServiceProvider
    ↓
$metaboxes
    ↓
new Metabox
    ↓
Illuminate\Metabox::__construct()
    ├── add_meta_boxes
    └── save_post
```

---

# Clase base `Illuminate\Metabox`

Las implementaciones deben extender:

```php
Illuminate\Metabox
```

Al instanciarse, la clase base:

1. genera automáticamente el `meta_key`;
2. evalúa si el metabox debe mostrarse;
3. registra el hook que lo dibuja en el administrador;
4. registra el hook que guarda los valores.

---

## Nombre y `meta_key`

El identificador se genera a partir del nombre de la clase mediante:

```php
classname()
```

El nombre corto de la clase se transforma a `snake_case`.

Ejemplo:

```text
CltvoSocialNet
    ↓
cltvo_social_net
```

Ese valor se utiliza como:

- `meta_key`;
- base para el ID del metabox;
- nombre del arreglo enviado en `$_POST`.

El ID del metabox queda conceptualmente como:

```text
cltvo_social_net_mb
```

---

# Mostrar el metabox

La clase base ejecuta:

```php
metaboxDisplayRule()
```

antes de registrar el metabox.

Por defecto:

```php
public static function metaboxDisplayRule()
{
    return true;
}
```

Una implementación puede sobrescribir esta regla para limitar dónde debe aparecer.

Si la regla devuelve `true`, se registra:

```text
add_meta_boxes
    ↓
add_meta_box()
```

La implementación puede configurar propiedades como:

```php
protected $description_metabox;
protected $post_type = 'post';
protected $position = 'normal';
protected $prioridad = 'default';
```

---

# Render del contenido

Cada metabox debe implementar:

```php
CltvoDisplayMetabox($object)
```

Este método es responsable de imprimir el HTML de los campos.

Antes del render, la clase base obtiene los datos existentes mediante:

```php
get_post_meta()
```

y permite inicializarlos o normalizarlos con:

```php
setMetaValue($meta_value)
```

El flujo es:

```text
add_meta_box()
    ↓
getMetaValue()
    ↓
get_post_meta()
    ↓
setMetaValue()
    ↓
CltvoDisplayMetabox()
```

---

# Guardado

La clase base registra el hook:

```text
save_post
```

Antes de guardar realiza varias comprobaciones:

- el usuario debe poder editar el post;
- no debe ser un autosave;
- no debe ser una revisión;
- no debe ser un autosave generado por WordPress.

Después verifica que exista:

```php
$_POST[static::classname()]
```

y ejecuta:

```php
CltvoSaveMetaValue($id)
```

La implementación base guarda directamente mediante:

```php
update_post_meta(
    $id,
    static::classname(),
    $_POST[static::classname()]
);
```

---

## Seguridad y limitaciones

La implementación actual contiene explícitamente:

```php
// Aquí pondría el Nonce.
```

Por lo tanto, la clase base **no implementa actualmente validación de nonce propia para el metabox**.

Para metaboxes nuevos se recomienda agregar:

- nonce;
- verificación del nonce durante `save_post`;
- sanitización específica para cada campo antes de guardar.

También debe evitarse tomar el `$_POST` completo como valor confiable sin procesarlo cuando los campos acepten datos sensibles o estructurados.

---

# Ejemplo: `CltvoSocialNet`

El blank incluye una implementación de ejemplo:

```text
app/Metaboxes/CltvoSocialNet.php
```

Esta clase extiende `Illuminate\Metabox` y define:

```php
protected $description_metabox = 'Redes Sociales';
protected $post_type = 'page';
```

Su regla de visualización es:

```php
public static function metaboxDisplayRule()
{
    return isSpecialPage('contacto');
}
```

Por lo tanto, el metabox se registra únicamente cuando la página actual corresponde a la Special Page:

```text
contacto
```

Esto conecta el sistema de Metaboxes con las Special Pages administradas por `AppServiceProvider`.

---

## Inicialización de valores

`CltvoSocialNet` sobrescribe:

```php
setMetaValue($meta)
```

para garantizar una estructura consistente antes de renderizar.

Actualmente incluye valores sin URL para:

```text
mail
phone
```

y contempla también una colección configurable de redes con:

```text
label
url
```

---

# Metaboxes vs ACF

Ambos mecanismos pueden almacenar metadatos, pero no son equivalentes.

```text
Metabox propio
    ↓
HTML manual
    ↓
save_post
    ↓
post meta
```

```text
ACF
    ↓
Field Group
    ↓
UI y API de ACF
    ↓
post meta / options
```

Un metabox propio puede ser conveniente cuando:

- el dato es sencillo;
- se necesita una UI completamente personalizada;
- se quiere evitar depender de ACF para esa funcionalidad;
- existe lógica específica de render o guardado que no encaja bien en un Field Group.

ACF normalmente resulta más práctico cuando:

- hay muchos campos;
- se necesitan repeaters, relationships, flexible content u otros campos avanzados;
- el equipo necesita administrar el esquema desde ACF;
- los Field Groups forman parte del flujo de `acf-json`.

---

# Registrar un nuevo metabox

1. Crear una clase en:

```text
app/Metaboxes/
```

2. Extender:

```php
Illuminate\Metabox
```

3. Definir sus propiedades y `CltvoDisplayMetabox()`.

4. Si es necesario, sobrescribir:

```php
metaboxDisplayRule()
setMetaValue()
CltvoSaveMetaValue()
```

5. Registrar la clase dentro de:

```php
protected $metaboxes = [
    \App\Metaboxes\ExampleMetabox::class,
];
```

---

## Consideraciones

- El provider solo inicializa las clases registradas.
- La clase base es responsable de conectar cada metabox con `add_meta_boxes` y `save_post`.
- El `meta_key` se deriva automáticamente del nombre de la clase.
- `metaboxDisplayRule()` permite controlar dónde aparece el metabox.
- La implementación actual no incluye nonce propio.
- Para código nuevo deben sanitizarse los valores antes de guardarlos.
- Cuando la necesidad pueda resolverse de forma más mantenible con ACF, debe evaluarse si un metabox custom aporta realmente una ventaja.
