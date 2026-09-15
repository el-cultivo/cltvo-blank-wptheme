# MenuServiceProvider
**Estado:** En revisión

## Descripción general

`MenuServiceProvider` centraliza el registro de las ubicaciones de menú disponibles en el blank theme.

Se encuentra en:

```text
app/Providers/MenuServiceProvider.php
```

Las ubicaciones disponibles se declaran en:

```php
protected $menus = [
    'header_menu' => 'Header Menu',
    'footer_menu' => 'Footer Menu',
];
```

y se registran mediante:

```php
register_nav_menus($this->menus);
```

---

# Ubicaciones de menú

Cada elemento del arreglo utiliza:

```text
theme_location => nombre visible en el administrador
```

Por ejemplo:

```php
'header_menu' => 'Header Menu'
```

registra:

```text
header_menu
```

como identificador interno de la ubicación.

El nombre:

```text
Header Menu
```

es la etiqueta que WordPress muestra en el administrador.

---

## Uso en templates

Una ubicación registrada puede utilizarse posteriormente mediante:

```php
wp_nav_menu([
    'theme_location' => 'header_menu',
]);
```

El identificador debe coincidir exactamente con la key registrada en el provider.

Ejemplo:

```text
MenuServiceProvider
    ↓
header_menu
    ↓
Apariencia → Menús
    ↓
menú asignado por el administrador
    ↓
wp_nav_menu()
    ↓
frontend
```

---

## Agregar una nueva ubicación

Para agregar otra ubicación global al tema basta con incorporarla al arreglo:

```php
protected $menus = [
    'header_menu' => 'Header Menu',
    'footer_menu' => 'Footer Menu',
    'mobile_menu' => 'Mobile Menu',
];
```

Posteriormente puede utilizarse desde las vistas mediante:

```php
wp_nav_menu([
    'theme_location' => 'mobile_menu',
]);
```

---

## Cuándo registrar un menú aquí

`MenuServiceProvider` debe utilizarse para ubicaciones estructurales del tema.

Ejemplos:

```text
header
footer
mobile
utility navigation
secondary navigation
```

No debe utilizarse para registrar contenido específico de un proyecto que no represente una ubicación de navegación administrable desde WordPress.

---

## Flujo general

```text
MenuServiceProvider
    ↓
$menus
    ↓
register_nav_menus()
    ↓
theme locations
    ↓
Apariencia → Menús
    ↓
wp_nav_menu()
```

---

## Consideraciones

- Centralizar las ubicaciones de navegación en este provider evita distribuir registros por `functions.php`.
- Las keys del arreglo funcionan como identificadores internos.
- Cambiar una key existente afecta cualquier template que utilice ese `theme_location`.
- La creación de la ubicación no crea automáticamente un menú ni asigna contenido; la asignación se realiza desde WordPress o mediante código adicional.
