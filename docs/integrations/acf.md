# Advanced Custom Fields (ACF)
**Estado:** En revisión

## Descripción

Advanced Custom Fields forma parte de las integraciones principales del blank theme.

En la versión revisada interviene en cuatro áreas:

```text
ACF PRO
├── Field Groups + Local JSON
├── sincronización manual
├── Special Pages Location
└── Options Pages
```

La integración no vive en una sola clase. Participan principalmente:

```text
acf-json/
config/app.php
config/options_pages.php
includes/custom/CustomLocation.php
app/Providers/ActionsServiceProvider.php
app/Providers/OptionsServiceProvider.php
```

---

# Dependencia

ACF PRO se declara en:

```text
config/required_plugins.php
```

como plugin requerido y empaquetado dentro del theme.

El ZIP revisado también contiene una copia en:

```text
includes/plugins/advanced-custom-fields-pro.zip
```

La versión incluida en el blank revisado es histórica y debe revisarse antes de considerar ese paquete como estrategia vigente de distribución.

---

# Estrategia de Local JSON

La fuente de verdad de los Field Groups es:

```text
acf-json/
```

La base de datos contiene la representación activa que utiliza WordPress/ACF, pero para versionado y despliegues el archivo JSON debe considerarse la fuente principal.

Conceptualmente:

```text
acf-json/
    ↓
Git
    ↓
pull / deploy
    ↓
Tools → ACF JSON Sync
    ↓
base de datos del entorno
```

Esto permite que la estructura de campos viaje con el código y no dependa únicamente de cambios manuales realizados en cada entorno.

---

# Sincronización manual

La sincronización automática al cargar el admin fue eliminada.

Actualmente existe una herramienta en:

```text
Tools → ACF JSON Sync
```

registrada desde:

```text
ActionsServiceProvider
```

La herramienta utiliza nonce:

```text
cltvo_acf_sync
```

y revisa los archivos JSON disponibles contra los Field Groups almacenados en WordPress.

---

## Cuándo hacer sync

Debe revisarse la sincronización después de:

- un `pull` que incluya cambios en `acf-json/`;
- un deploy a otro entorno;
- cambios de Field Groups realizados por otro integrante del equipo;
- cambios relacionados con idiomas/subdirectorios en proyectos WPML.

---

## Qué hace el sync

Para cada Field Group encontrado en JSON:

- lo importa si todavía no existe en la base de datos;
- si ya existe, compara su modificación con la versión almacenada;
- actualiza cuando el JSON corresponde a una versión más reciente;
- salta los grupos que no requieren actualización.

La herramienta genera un reporte de:

```text
importados
saltados
huérfanos
```

---

# Field Groups huérfanos

La herramienta también detecta Field Groups presentes en la base de datos que ya no tienen un JSON correspondiente.

Conceptualmente:

```text
DB
└── group_xxx

acf-json/
└── ❌ no existe group_xxx.json
```

Estos registros se reportan como huérfanos.

## Importante

Los huérfanos:

```text
NO se eliminan automáticamente
```

La eliminación automática podría borrar configuración válida o información que todavía necesita revisión.

Deben inspeccionarse manualmente antes de decidir si se eliminan.

---

# Compatibilidad con WPML

## Problema

En proyectos multiidioma pueden existir Field Groups organizados en subdirectorios:

```text
acf-json/
├── en/
├── es/
└── ...
```

ACF normalmente carga rutas declaradas mediante:

```text
acf/settings/load_json
```

El blank extiende esta configuración para incorporar también los subdirectorios existentes dentro de:

```text
acf-json/
```

---

## Implementación

`ActionsServiceProvider::adminInit()` detecta ACF y agrega las rutas de Local JSON.

Conceptualmente:

```text
acf-json/
    ↓
glob de subdirectorios
    ↓
acf/settings/load_json
    ↓
ACF puede encontrar JSON fuera del directorio raíz
```

Esto permite trabajar con estructuras como:

```text
/acf-json
/acf-json/en
/acf-json/es
/acf-json/{subfolder}
```

sin tener que declarar manualmente cada idioma.

---

## Consideración

La lógica no está limitada específicamente a:

```text
en
es
```

sino que incorpora los subdirectorios encontrados.

Por ello la implementación es útil para WPML, pero técnicamente funciona con cualquier organización por subcarpetas dentro de `acf-json/`.

---

# Special Pages Location

El blank agrega una Location Rule custom de ACF:

```text
Special Pages
```

La implementación se encuentra en:

```text
includes/custom/CustomLocation.php
```

y extiende:

```php
ACF_Location
```

---

## Fuente de opciones

`CustomLocation` lee:

```text
config/app.php
    ↓
special-pages
```

Por ejemplo:

```text
splash
home
contacto
```

y los expone como opciones dentro de las reglas de ubicación de un Field Group.

Conceptualmente:

```text
config/app.php
    ↓
special-pages
    ↓
CustomLocation
    ↓
ACF Location Rule
    ↓
Special Pages
```

---

## Matching

La regla compara el:

```text
post_name
```

de la Page actual con el slug configurado.

También soporta operadores:

```text
==
!=
```

Esto permite asignar Field Groups por intención estructural de la página y no por un ID concreto de WordPress.

---

## Registro

`ActionsServiceProvider` registra esta Location Rule únicamente cuando ACF está disponible.

Por ello:

```text
CustomLocation.php
```

forma parte de la integración ACF y no necesita documentación independiente dentro de `core/`.

---

# Options Pages

Existe otra integración con ACF en:

```text
OptionsServiceProvider
```

que utiliza la API de ACF Options Pages.

Su configuración complementaria vive en:

```text
config/options_pages.php
```

---

## Estado actual

La implementación revisada:

- crea Options Pages relacionadas con Post Types configurados;
- contiene una estructura de campos ACF hardcodeada;
- utiliza `config/options_pages.php` principalmente para proporcionar keys;
- depende en la práctica de ACF PRO.

Por lo tanto no debe considerarse todavía un sistema declarativo genérico de Options Pages.

---

## Dirección de refactor futura

Una posible mejora sería separar:

```text
configuración de páginas
```

de:

```text
definición de Field Groups
```

para que:

```text
config/options_pages.php
    ↓
declare Options Pages

acf-json/
    ↓
declare los campos
```

y el provider se limite a registrar las páginas mediante ACF.

Esta es una propuesta futura; **no describe el comportamiento actual**.

---

# Relación con `ActionsServiceProvider`

ACF explica varias responsabilidades que actualmente viven en este provider:

```text
ActionsServiceProvider
├── carga rutas Local JSON
├── registra CustomLocation
├── registra Tools → ACF JSON Sync
└── procesa sincronización manual
```

La lógica detallada de ACF debe mantenerse en esta documentación de integración, mientras que `providers.md` sólo necesita explicar que `ActionsServiceProvider` conecta esas piezas.

---

# Flujo completo

```text
config/app.php
    └── special-pages
            ↓
    CustomLocation
            ↓
      ACF Field Groups

acf-json/
    ↓
Git / deploy
    ↓
ActionsServiceProvider
    ├── load_json + subfolders
    └── ACF JSON Sync
            ↓
        WordPress DB

OptionsServiceProvider
    ↓
ACF Options Pages
```

---

# Reglas de uso

- Versionar los Field Groups mediante `acf-json/`.
- Revisar el sync después de pulls y deploys con cambios ACF.
- No depender de cambios realizados únicamente en producción.
- Revisar manualmente los grupos huérfanos antes de eliminarlos.
- En proyectos multiidioma, comprobar que los JSON esperados estén en los subdirectorios correctos.
- Mantener las definiciones estructurales de Special Pages en `config/app.php`.
- No asumir que `OptionsServiceProvider` es genérico en su implementación actual.

---

# Limitaciones y puntos de revisión

## ACF + WPML

Existen antecedentes de duplicaciones o inconsistencias en proyectos con configuraciones multiidioma complejas.

La carga de subdirectorios resuelve la visibilidad de JSON, pero no debe interpretarse como garantía de que cualquier historial previo de ACF/WPML quede automáticamente saneado.

---

## Plugin empaquetado

El blank contiene un ZIP histórico de ACF PRO.

Antes de entregar o reutilizar el boilerplate debe revisarse:

```text
versión
licencia
estrategia de actualización
necesidad de empaquetar el plugin dentro del repositorio
```

---

## Options Pages

La implementación actual mezcla configuración e implementación específica.

Es candidata a refactor, pero debe documentarse como está hasta que dicho cambio se realice.

---

# Historial documentado

| Versión | Cambio |
| :--- | :--- |
| 3.0 | ACF sin sync automático; exportación e importación manual entre entornos. |
| 3.2 | Sync automático vía `acf-json/` al activar el theme. Se agrega Location `Special Pages`. |
| 3.3 | Se elimina el sync automático. Se introduce `Tools → ACF JSON Sync` y carga de subdirectorios para proyectos multiidioma. |

> El historial anterior proviene de la documentación existente del proyecto. La revisión actual se concentra en confirmar y explicar la arquitectura presente en el código del blank revisado.
