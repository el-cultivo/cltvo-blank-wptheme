# Advanced Custom Fields (ACF)

## Estrategia de sincronización

El source of truth de todos los Field Groups es **`acf-json/`**. La base de datos no debe considerarse fuente de verdad bajo ninguna circunstancia.

Desde la versión 3.3, la sincronización automática al cargar el admin fue eliminada. En su lugar existe una herramienta manual accesible desde:

```
Tools → ACF JSON Sync
```

### Cuándo hacer sync

Siempre después de:
- `pull` con cambios en `acf-json/`
- `deploy` a cualquier entorno
- Cambio de idioma activo en proyectos con WPML

### Qué hace el sync

- Importa un Field Group solo si **no existe en DB**, o si el **JSON es más reciente** que la versión en DB.
- **No elimina** Field Groups existentes (evita pérdida de información).
- Detecta Field Groups **huérfanos**: registros en DB que ya no tienen un JSON correspondiente en `acf-json/`.

### Field Groups huérfanos

Los huérfanos se detectan pero **no se eliminan automáticamente**. Deben revisarse y limpiarse manualmente. Su presencia puede causar duplicaciones invisibles o inconsistencias entre entornos.

Se recomienda revisar periódicamente, especialmente en proyectos con historial largo o que hayan migrado entre versiones del boilerplate.

---

## Compatibilidad con WPML

> **Crítico.** Leer antes de trabajar ACF en proyectos multiidioma.

### El problema

Cuando ACF y WPML coexisten, WPML guarda los archivos JSON organizados por idioma:

```
acf-json/
├── en/
└── es/
```

Esto provoca que ACF, por defecto, solo lea el directorio raíz de `acf-json/` e ignore los subdirectorios. Las consecuencias son:

- Fields no visibles en algunos idiomas
- Sync incompleto entre entornos
- Duplicación de Field Groups

### Solución implementada

Se extiende el hook `acf/settings/load_json` para que ACF lea **todos los subdirectorios** dentro de `acf-json/`, sin importar el idioma:

```
/acf-json
/acf-json/en
/acf-json/es
/acf-json/{cualquier subfolder}
```

Con esto, ACF ve todos los Field Groups independientemente del idioma activo, y el sync es consistente entre entornos.

---

## Reglas de uso

- Siempre trabajar con `acf-json/` como fuente principal.
- No editar Field Groups directamente en producción sin hacer sync posterior.
- Hacer backup antes de un sync masivo.
- No confiar en la DB como fuente de verdad.

---

## Limitaciones conocidas y estado actual

- El sync manual es funcional en todos los proyectos activos.
- Se encuentran **en investigación** edge cases con WPML que pueden generar duplicaciones históricas en proyectos con configuraciones complejas de idiomas.
- Se recomienda monitoreo activo en proyectos que combinen ACF + WPML, especialmente tras deploys o cambios de configuración de idioma.

---

## Historial de cambios relevantes

| Versión | Cambio |
| :--- | :--- |
| 3.0 | ACF sin sync automático; exportación e importación manual entre entornos. |
| 3.2 | Sync automático via `acf-json/` al activar el tema. Se agrega location `Special Pages`. |
| 3.3 | Se elimina sync automático. Se introduce `Tools → ACF JSON Sync`. Se implementa compatibilidad con WPML mediante extensión de `acf/settings/load_json`. |