**Origen:**

<!-- 

Usar esta bandera para indicar si el feature se desarolló para el blank-theme y se implementó tal cual, si se modificó o si se desarrolló ex profeso para el proyecto.

**Origen:** Boilerplate base — sin modificaciones
**Origen:** Boilerplate base — extendido para este proyecto  
**Origen:** Desarrollado específicamente para este proyecto
-->


# Nombre de la funcionalidad

<!-- 
  Nombre en español, claro y directo. Debe describir qué es, no qué hace.
  Ejemplos: "Integración de FAQs por API", 
  Evitar: "Feature de FAQs", "Módulo nuevo", "Sistema de asesores v2"
-->

## Qué hace

<!--
  2-4 líneas máximo. Responde tres preguntas en orden:
  1. Qué problema resuelve o qué automatiza
  2. Cómo lo resuelve en términos generales (sin entrar en código)
  3. Si reemplaza algo anterior, mencionarlo: qué había antes y qué cambia

  Ejemplo:
  "Integra el sistema de FAQs de GBM al sitio consumiendo un API REST externo.
  Reemplaza la página external `about.appgbm.com/faqs/` administrada en otro WordPress,
  centralizando la edición desde un único lugar."
-->

---

## Cómo se usa (Admin)

<!--
  Solo incluir esta sección si la funcionalidad tiene una interfaz de administración.
  Usar pasos numerados si hay un flujo secuencial.
  Redactarlo concretamente, no es una guía de usuario explicativa, es una referencia util de la funcionalidad. 

  Ejemplo:
  1. Acceder al panel de WordPress → Nombre de la página de opciones.
  2. Cargar el archivo con el formato requerido.
  3. Al guardar, el sistema procesa los datos automáticamente.
-->

---

## Cómo funciona

<!--
  Explicación técnica del flujo interno. Dividir en etapas numeradas cuando el
  proceso tiene pasos secuenciales claros (consumo → parseo → caché → render).
  Cada etapa debe tener un título descriptivo y 2-5 líneas de explicación.
  
  Incluir aquí:
  - Lógica principal y orden de ejecución
  - Decisiones técnicas relevantes (por qué caché, por qué sin DB, etc.)
  - Comportamiento en el front-end si es relevante para entender el flujo completo

  No incluir aquí:
  - Nombres de archivos o funciones (eso va en "Archivos involucrados")
  - Configuración de campos o formato de datos (eso va en su propia sección)
-->

---

## Estructura de URLs

<!--
  Solo incluir si la funcionalidad registra rutas públicas propias.
  Usar tabla con columnas Vista | URL.
  Documentar todos los parámetros de query string disponibles.

  Ejemplo:
  | Vista            | URL                                      |
  | :---             | :---                                     |
  | Vista principal  | `/ruta/`                                 |
  | Filtro por X     | `/ruta/?parametro={valor}`               |
  | Vista individual | `/ruta/{slug}`                           |
-->

---

## Entradas y salidas

<!--
  Solo incluir si la funcionalidad debe recibir información en un formato específico, un archivo externo (CSV, JSON, XML, etc.).
  Documentar los requerimientos técnicos del archivo y el mapeo de campos.

-->

---

## Archivos involucrados

<!--
  Listar todos los archivos PHP, JS y de templates que forman parte de la funcionalidad.
  Agrupar por tipo o carpeta cuando hay suficientes archivos para justificarlo.
  Para cada archivo: nombre en código, seguido de una línea de descripción de su responsabilidad.
  
  Si un archivo preexistente fue modificado (no creado), documentarlo en una subsección
  separada "Archivos modificados" indicando qué método o sección se cambió y por qué.

  Si se agregaron funciones a un archivo compartido (helpers, utils), listarlas también.
-->

---

## Dependencias

<!--
  Lista de todo lo que esta funcionalidad necesita para operar:
  - CPTs o taxonomías registradas
  - Plugins requeridos (ACF, Redirection, etc.)
  - Librerías externas (Composer, npm)
  - Permisos de sistema (escritura en carpetas, etc.)
  - Clases, módulos o funcionalidades del mismo proyecto de las que depende
  - APIs externas

  Formato sugerido:
  - **Nombre** — Para qué se usa en el contexto de esta funcionalidad.
-->

---

## Limitaciones conocidas

<!--
  Comportamientos incompletos, edge cases no resueltos o decisiones conscientes
  de no implementar algo. Documentar aunque parezca obvio.

  Incluir:
  - Validaciones que no se realizan y por qué
  - Casos donde el sistema puede fallar silenciosamente
  - Funcionalidad pendiente o parcialmente implementada
  - Dependencias de datos externos que pueden causar inconsistencias
-->