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
