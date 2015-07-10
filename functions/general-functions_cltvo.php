<?php

/**
 * En este archivo se incluyen las Funciones de El Cultivo
 *
 * No agregar funciones del tema
 */

/** ==============================================================================================================
 *                                       Funciones de El Cultivo
 *  ==============================================================================================================
 */


// Valida de una forma sencilla (incompleta?)
// si el string que le pasas es un mail
function es_mail($string){
	$es_mail = false;
	$explode = explode('@', $string);
	if( count($explode)== 2 ){
		$explode_2 = explode('.', $explode[1]);
		$cuantos_exp2 = count($explode_2);
		if( $cuantos_exp2 >1 && $explode_2[$cuantos_exp2-1] != '')
			$es_mail = true;
	}
	return $es_mail;
}


/**
 * Clase para las imagenes del post que contiene el id de la imagen, la src, el tamaño y el alt  
 *
 */

class Cltvo_Img{
	public $img_id;
	public $src;
	public $width;
	public $height;
	public $alt;
	public function __construct( $img_id, $size='full' ){
	    $this->img_id = $img_id;
	    $img = wp_get_attachment_image_src( $this->img_id, $size);
	    $this->src = $img[0];
	    $this->width = $img[1];
	    $this->height = $img[2];
	    $this->alt = get_post_meta($this->img_id, '_wp_attachment_image_alt', true);
	}
}

/**
 * regresa todas las imagenes del post con sus caracteristicas en un array
 *
 * Parametros:
 *
 * @param int $parentId id del post
 * @param string $size tamaño de las imagenes (por defecto full)
 * @param boolean|array $exclude imagenes a ser excluidas (por defecto false)
 *
 * @return array con las imagenes y sus caracteristicas
 */

 function cltvo_todasImgsDelPost($parentId, $size='full', $exclude= false){
	 $query_images_args = array(
	     'post_parent' => $parentId,
	     'post_type' => 'attachment',
	     'post_mime_type' =>'image',
	     'post_status' => 'inherit',
	     'posts_per_page' => -1
	 );

	 if( !empty($exclude) ){
	     $query_images_args['post__not_in'] = $exclude;
	 }

	 $query_images = get_posts( $query_images_args );
	 $images = array();
	 foreach ( $query_images as $image) {
	     $images[] = new Cltvo_Img($image->ID, $size);
	 }
	 return $images;
 }


//dale el ID del post
//y un array con los ids de las imágenes a excluir, (si ninguna usar false)
//y te regresará un array con los IDS de las imágenes de ese post
function cltvo_todosIdsImgsDelPost($parentId, $exclude= false){

	$query_images_args = array(
		'post_parent' => $parentId,
	    'post_type' => 'attachment',
	    'orderby' => 'title',
	    'order' => 'ASC',
	    'post_mime_type' =>'image',
	    'post_status' => 'inherit',
	    'posts_per_page' => -1
	);

	if( $exclude && is_array($exclude)){
		$query_images_args['post__not_in'] = $exclude;
	}

	$query_images = get_posts( $query_images_args );
	$images = array();
	foreach ( $query_images as $image) {
	    $images[] = $image->ID;
	}

	return $images;
}

// ???
function cltvo_wpURL_2_path( $url ){
	$path = get_theme_root();
	$path = str_replace('wp-content/themes', '', $path);
	$path = str_replace(home_url('/'), $path, $url);
	return $path;
}

// modificacion de la funcion print_r
if( !function_exists('cltvo_print_r') ){
	function cltvo_print_r($var){
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
}

//convierte int a string con signo de pesos, con punto decimal y dos ceros al final
if( !function_exists('cltvo_dinero') ){
	function cltvo_dinero( $numero ){
		return '$ ' . number_format($numero, 2);
	}
}


// agrega aqui ...
?>
