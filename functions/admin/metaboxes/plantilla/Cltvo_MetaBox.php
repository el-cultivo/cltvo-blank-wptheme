<?php

class Cltvo_MetaBox extends Cltvo_Metabox_Master
{

    /**
     * sobre escribiendo los args del master
     */
    protected $description_metabox = "Nombre del meta";
	// protected $post_type = array("post"); // postypes 
	// protected $position = "normal"; // posicion
	// protected $prioridad = "default"; //prioridad
	// protected $ags = null; //args


    /**
    * define el metodo donde se mostrara el meta
    * @return boolean si es verdadero el meta sera desplegado en el admin en caso constrario no
    */
    // ublic static function metaboxDisplayRule(){
    // return true;
    //


    /**
     * define el metodo que inicializa el valor del meta
     */
    // public static function setMetaValue($meta){
    //     return $meta;
    // }


	/**
	 * Es la funcion que muestra el contenido del metabox
	 * @param object $object en principio es un objeto de WP_post
	 */
	public function CltvoDisplayMetabox( $object ){

        var_dump($this->meta_key);
        var_dump($this->meta_value);

	}



}
