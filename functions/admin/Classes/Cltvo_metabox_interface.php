<?php

interface Cltvo_metabox_interface{
	/**
	 * Es la funcion que muestra el contenido del metabox
	 * @param object $object en principio es un objeto de WP_post
	 */
	public function CltvoDisplayMetabox($object);
	/**
	 * en esta funcion se inicializan los valores del metabox
	 */
	public  function setMetaValue($meta_value);
	/**
	 * regresa los valores del metabox para un post
	 */
	public function getMetaValue($object);
	/**
	 * define el metodo donde se mostrara el meta
	 * @param object $object en principio es un objeto de WP_post
	 */
	public static function displayRule();
	/**
	 * guarda el valor del metabox
	 */
	public function CltvoSaveMetaValue();
	/**
	 * Agrega el hook que coloca el meta en el admin
	 */
	public function CltvoMetaBox();

}
