<?php

/**
 * cambia el m2 por m<sup>2</sup>
 *
 *	Parámetros:
 *
 * @param string $valor valor a ser verificado 
 * 
 * @return string con el cambio 
 */

function m2_to_msup2($valor){

	return str_replace( array("m2", "M2", "m 2", "M 2") , "m<sup>2</sup>", $valor ) ;
}

/**
 * Inicializa e imprime un array
 * @param  array $meta 
 * @param  boolean $echo  echo o return initialized array 
 * @return array o empty string       
 */
function echoMeta( $meta, $key, $echo = true) {
	$meta = is_array($meta) ? $meta : array();

	echo ($echo && isset($meta[$key])) ? $meta[$key] : '';	 	
	
	return isset($meta[$key]) ? $meta[$key] : '';	 
}

