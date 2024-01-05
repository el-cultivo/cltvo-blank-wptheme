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




## Notas importantes
* Para enviar correctamente los correos la constante WP_DEBUG debe estar en false
* En versiones de producción, se deben actualizar las versiones de los css y js esto se hace cambiando el número en el quinto parámetro ('0.0') de las funciones `wp_register_style` y `wp_register_script`
