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
	public static function setMetaValue($meta_value);
	/**
	 * regresa los valores del metabox para un post
	 */
	public static function getMetaValue($object);
	/**
	 * define el metodo donde se mostrara el meta
	 */
	public static function metaboxDisplayRule();
	/**
	 * guarda el valor del metabox
	 */
	public function CltvoSaveMetaValue();
	/**
	 * Agrega el hook que coloca el meta en el admin
	 */
	public function CltvoMetaBox();

	/**
	 * define el meta key
	 */
	public static function GetMetaKey();

}
