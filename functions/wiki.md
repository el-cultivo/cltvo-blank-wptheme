# Estructura del *functions.php*

El archivo *functions.php* se encuentra dividido en cuatro secciones principales:

* **Constantes y Variables Globales**  
 * Tiene como objetivo definir constantes y variables globales que se utilizaran de manera reiterada. 
   * **Nota:** Se define dentro la variable superglobal ``` $GLOBALS ``` el arreglo ``` $GLOBALS['special_pages'] ``` con el objetivo de en listar las paginas con diseños o funciones especiales

* **Archivos generarles**
 * Tiene como objetivo agregar los java scripts y las funciones que son utilizadas constantemente en diferentes proyectos

* **Archivos del administrador**
 * Tiene como objetivo organizar las funciones que modificaran, agregaran o eliminaran elementos al administrador de Workpress

* **Archivos del tema**
 * Tiene como objetivo organizar las funciones que generaran los diseños del tema
 

## Archivos generarles

En esta sección se incluyen dos archivos:

* **general-scripts_js.php** 
 * Contiene la llamada de los archivos functions.js y admin-functions.js
 
* **general-functions_cltvo.php** 
 * Contiene las funciones generales del cultivo que son independientes de cada proyecto 
 

## Archivos del administrador

En esta sección se incluyen cinco archivos :

* **admin-menu.php** 
 * Contiene las funciones para personalizar el menú del administrador de wp 
 
* **admin-images.php** 
 * Contiene la funciones para personalizar los tamaños de la imágenes 

* **admin-post_type.php** 
 * Contiene el registro de tipos de post personalizados y configuración del editor de los mismos
 
* **admin-taxonomies.php** 
 * Contiene el registro de taxonomías personalizadas
 
* **admin-metabox_savepost.php** 
 * Contiene las funciones de las metabox así como las funciones del save post 
 

## Archivos del tema

* **theme-menu.php** 
 * Contiene el menú del sitio y sus funciones
   * **Nota:** Es importante considerar que el menú se crea como una función para luego ser llamada dentro del sitio. Esto es particularmente útil cuando el menú cambia su diseño en diferentes paginas.
 
* **theme-filters.php** 
 * Contiene los filtros específicos del tema

* **theme-functions.php** 
 * Contiene los funciones específicas del tema
 