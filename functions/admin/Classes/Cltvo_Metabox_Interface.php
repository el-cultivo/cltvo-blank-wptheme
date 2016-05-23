<?php

interface Cltvo_Metabox_Interface{
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
	 * define donde se mostrara el meta
	 */
	public static function metaboxDisplayRule();

	/**
	 * guarda el valor del metabox
	 */
	public function CltvoSaveHook();

	/**
	 * guarda el valor del metabox
	 */
	public function CltvoSaveMetaValue($id);
	/**
	 * Agrega el hook que coloca el meta en el admin
	 */
	public function CltvoMetaBoxHook();

}
