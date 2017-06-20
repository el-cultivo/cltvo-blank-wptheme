# Metaboxes


## Estado actual del repositorio
De momento los archivos de test están corriendo, y los pueden ver en cualquier página

Pero al final deberían de estar todos en la carpeta de test y ser usados sólo para hacer pruebas (para usarlos hay que moverlos a la carpeta en la que se encuentran actualmente) o como templates para configurar


## Uso
Los archivos que se encuentran en la carpeta de masters pueden extenders de la siguiente manera
```
<?php
require_once 'masters/Cltvo_Checkboxes.php';// o cualquier otro template

class MisCheckboxes extends Cltvo_Checkboxes//
{
	//Configuración (algunas opciones)

	protected $description_metabox = 'Radio botons';//Configura titulo del meta

	protected $post_type = 'page';//Configura post type donde aparece el meta

	public static $opciones = [ //Configura opciones disponibles
		'opt1'	=> 'opcion 1',
		'opt2'	=> 'opcion 2',
	];

	public static function metaboxDisplayRule(){// Configura opciones de display
		return true;//debe regresar siempre un booleano
	}
}
```

##  Detalles
Cada metabox puede tener opciones ligeramente distintas, pero es muy fácil ver qué opciones hay disponibles viendo las propiedades que presentan las clases que cada metabox extiende.

Si las dudas persisten levanten un issue en github o hagan pull para mejorar la documentación. 
