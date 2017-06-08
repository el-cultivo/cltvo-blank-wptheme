<?php 

/**
 * Wrapper sobre var_dump
 * @param   $variable
 * @return  var_dump con <pre> tags
 */
function vd($variable)
{
	echo "<pre>";
	var_dump($variable);
	echo "</pre>";
}

/**
 * Wrapper sobre var_dump que además ejecuta die
 * @param   $variable
 * @return  var_dump con <pre> tags
 */
function dd($variable)
{
	echo "<pre>";
	var_dump($variable);
	echo "</pre>";
	die;
}
