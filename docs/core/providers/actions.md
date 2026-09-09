# ActionsServiceProvider
**Estado:** En revisión

## Descripción general

`ActionsServiceProvider` centraliza distintas acciones globales necesarias para la inicialización y configuración del blank theme.

Se encuentra en:

```text
app/Providers/ActionsServiceProvider.php
```

A diferencia de providers enfocados en un único tipo de componente, este provider agrupa comportamientos que se conectan con el ciclo de WordPress mediante acciones globales.

Entre sus responsabilidades se encuentran la configuración inicial del tema, ajustes administrativos, registro de plugins requeridos, integración con ACF y el deshabilitado global de comentarios cuando la configuración del proyecto lo requiere.

---

## Registro de acciones

El método `boot()` funciona como punto de entrada del provider y conecta sus funcionalidades con distintos hooks de WordPress.

Entre los hooks utilizados se encuentran:

```text
after_setup_theme
admin_init
admin_menu
init
tgmpa_register
```

Además, algunas acciones se registran únicamente cuando existen las dependencias o constantes necesarias para ejecutarlas.

El flujo general puede representarse como:

```text
config/app.php
    ↓
ActionsServiceProvider
    ↓
boot()
    ↓
hooks de WordPress
    ↓
métodos específicos del provider
```

---

## Inicialización del tema

El provider contiene acciones relacionadas con la configuración inicial del sitio.

Entre ellas se encuentra la carga del textdomain del tema mediante el hook:

```text
after_setup_theme
```

También incluye comportamiento ejecutado durante `init`, como configuraciones relacionadas con páginas especiales del blank theme y ajustes sobre los tipos de contenido considerados durante una búsqueda.

Estas configuraciones forman parte del comportamiento base del tema y pueden adaptarse según las necesidades de cada proyecto.

---

## Configuración administrativa

Mediante hooks como:

```text
admin_init
admin_menu
```

el provider registra comportamientos específicos del administrador de WordPress.

Algunas de estas funcionalidades dependen de plugins o configuraciones adicionales y únicamente se inicializan cuando se encuentran disponibles.

---

## Plugins requeridos

`ActionsServiceProvider` participa en el registro de plugins requeridos o recomendados por el tema mediante TGMPA.

El registro se conecta al hook:

```text
tgmpa_register
```

Esto permite centralizar desde el tema las dependencias de plugins necesarias para determinadas funcionalidades.

Las dependencias concretas pueden variar entre proyectos.

---

# Integración con ACF

Una de las responsabilidades más importantes de `ActionsServiceProvider` es la integración del blank theme con **Advanced Custom Fields (ACF)**.

El provider comprueba la disponibilidad de ACF antes de registrar las funcionalidades que dependen de él.

La integración contempla:

- configuración relacionada con ACF;
- registro de ubicaciones personalizadas;
- manejo de ACF Local JSON;
- lectura de grupos almacenados en subdirectorios de `acf-json`;
- sincronización de Field Groups desde JSON hacia la base de datos;
- detección de Field Groups cuyo archivo JSON ya no existe;
- herramienta administrativa de sincronización;
- consideraciones para sitios multidioma con WPML.

Debido a que esta funcionalidad es crítica para el flujo de desarrollo del blank theme, su comportamiento debe entenderse antes de modificar o eliminar archivos dentro de `acf-json`.

Ver para más detalles [ACF](../integrations/acf.md).

---

# Deshabilitado de comentarios

`ActionsServiceProvider` incluye comportamiento para deshabilitar los comentarios de WordPress cuando el proyecto define la configuración correspondiente mediante:

```php
CLTVO_DISABLE_COMMENTS
```

Cuando esta funcionalidad se encuentra activa, el provider registra las acciones y filtros necesarios para retirar el soporte de comentarios de distintas áreas de WordPress.

Esto puede involucrar:

- soporte de comentarios en Post Types;
- pantallas administrativas;
- menú de comentarios;
- widgets relacionados del dashboard;
- elementos de la barra de administración;
- template de comentarios.

La finalidad es deshabilitar la funcionalidad de manera global y no únicamente ocultar su acceso desde el administrador.

---

## Flujo general

```text
config/app.php
    ↓
ActionsServiceProvider
    ↓
boot()
    ├── after_setup_theme
    │       ↓
    │   configuración del tema
    │
    ├── init
    │       ↓
    │   configuración global
    │
    ├── admin_init / admin_menu
    │       ↓
    │   administración
    │       ↓
    │   ACF / ACF JSON Sync
    │
    ├── tgmpa_register
    │       ↓
    │   plugins requeridos
    │
    └── CLTVO_DISABLE_COMMENTS
            ↓
        deshabilitado de comentarios
```

---

## Consideraciones

- `ActionsServiceProvider` debe mantenerse para comportamientos globales conectados con acciones del ciclo de WordPress.
- Antes de agregar una nueva responsabilidad debe revisarse si existe un provider más específico.
- Las funcionalidades dependientes de ACF solo deben ejecutarse cuando ACF se encuentre disponible.
- Los archivos de `acf-json` forman parte del flujo de configuración del proyecto y deben mantenerse bajo control de versiones.
- La sincronización documentada se realiza desde JSON hacia la base de datos.
- Un Field Group sin JSON no debe asumirse automáticamente como contenido que deba eliminarse.
- En proyectos con WPML debe considerarse también la configuración multidioma de ACF.
- `CLTVO_DISABLE_COMMENTS` permite activar el comportamiento global para deshabilitar comentarios cuando el proyecto no los utiliza.

## Documentación relacionada

- [Advanced Custom Fields (ACF)](../integrations/acf.md)