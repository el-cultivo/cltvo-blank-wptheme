<?php

/**
 * Regresa solo la parte del texto del idioma correpondiete
 * 
 * @param  string $text cadena de texto que ontiene los idiomas
 * @param  string $lang idioma al que se le va a traducir (por defecto  qtranxf_getLanguage() )
 * @return string  cadena con el texto en el idioma
 */
function cltvo_translate($text, $lang = "" ){
		if ( empty($lang) ) {
				$lang = qtranxf_getLanguage() ;
		}
		return qtranxf_use($lang , $text , false, true);
}

function cltvo_siteTitle()
{
	echo get_bloginfo( 'name' );
	
}

function cltvo_siteDescription()
{
	echo get_bloginfo( 'description' );
}

function cltvo_title($post, $translate=false)
{
	if ($translate == true) {
		return cltvo_translate($post->post_title);
	}
	 return $post->post_title;
}

function cltvo_content($post, $translate=false, $filters=false)
{
	if ($translate==false && $filters==false)
	{
		return wpautop($post->post_content);
	}
	elseif ($translate==false && $filters==true) {
		return apply_filters('the_content', $post->post_content);
	}
	elseif ($translate==true && $filters==false) {
		return cltvo_translate( wpautop($post->post_content) );
	}
	elseif ($translate==true && $filters==true) {
		return cltvo_translate(apply_filters('the_content', $post->post_content) );
	}

}




function cltvo_slug($post)
{
	 echo $post->post_name;
}



/**
 * Easily includes files within the theme directory
 * 
 * @param  string  	$path 	path to the image from theme directory
 * @return string  			full path
 */
function themeInc($path, $global_post=true)
{
	if ($global_post) {
		global $post;
	}
	include ( get_stylesheet_directory() . $path );
}


/**
 * Como themeInc, pero permite pasar variables al archivo incluido mediate un array llamado $opts
 * 
 * @param  string  	$path 	path to the image from theme directory
 * @return string  			full path
 */
function cltvo_superInc($path, $opts = array(), $global_post = true) {
	if ($global_post) {
		global $post;
	}
	include ( get_stylesheet_directory() . $path );
}

/**
 * Easily includes files withing the theme directory
 * 
 * @param  string  	$path 	path to the image from theme directory
 * @return string  			full path
 */
function echoImg($img_name)
{
	echo ( get_stylesheet_directory_uri().'/images/'.$img_name );
}



/**
 * Get Page ID from Page Slug
 * @param  string $page_slug      	Slug
 * @param  string $slug_page_type 	Post Type
 * @return string                	ID
 */
function get_id_by_slug($page_slug, $slug_page_type = 'page') 
{
	$found_page = get_page_by_path($page_slug, OBJECT, $slug_page_type);
	
	if ( ! $found_page) {
		return null;
	}
	return $found_page->ID;
}


/**
 * Get Page by from Page Slug
 * @param  string $page_slug      	Slug
 * @param  string $slug_page_type 	Post Type
 * @return string                	ID
 */
function getPageFromSlug($page_slug, $slug_page_type = 'page') 
{
	$found_page = get_page_by_path($page_slug, OBJECT, $slug_page_type);
	
	if ( ! $found_page) {
		return null;
	}
	return $found_page;
}



/**
 * Echo Page Content from Page Slug (with wpautop)
 * @param  string $page_slug      	Slug
 * @param  string $slug_page_type 	Post Type
 * @return string                	ID
 */
function echoPageContent($page_slug, $slug_page_type = 'page')
{
	$page = get_page_by_path($page_slug, OBJECT, $slug_page_type);
	echo wpautop($page->post_content);

}

/**
 * Echo Page Title from Page Slug
 * @param  string $page_slug      	Slug
 * @param  string $slug_page_type 	Post Type
 * @return string                	ID
 */
function echoPageTitle($page_slug, $slug_page_type = 'page')
{
	$page = get_page_by_path($page_slug, OBJECT, $slug_page_type);
	echo $page->post_title;
}


// function queryChildren($parent_slug, $default_args = true , $query_args = [] ){
// 	$cltvo_wp_query = new WP_Query();

// 	if (true == $default_args) 
// 	{
// 		$queried_pages = $cltvo_wp_query->query(array('post_type' => 'page'));
// 		return get_page_children( get_id_by_slug($parent_slug), $queried_pages );
// 	}
	
// 	$queried_pages = $cltvo_wp_query->query($query_args));
// 	return get_page_children( get_id_by_slug($parent_slug), $queried_pages )
// }

function queryChildrenBySlug( $parent_slug, $default_args = true , $query_args = [] ){
	$cltvo_wp_query = new WP_Query();
	
	if (true == $default_args)
	{
		$queried_pages = $cltvo_wp_query->query(
			array(
				'posts_per_page'	=> 	-1,
				'post_type' => 'page', 
				'orderby' => 'menu_order', 
				'order'   => 'ASC'
			)
		);
		return get_page_children( get_id_by_slug($parent_slug), $queried_pages );
	}
	$queried_pages = $cltvo_wp_query->query($query_args);
	return get_page_children( get_id_by_slug($parent_slug), $queried_pages );

}

function queryChildren( $post, $default_args = true , $query_args = [] ){
	$cltvo_wp_query = new WP_Query();
	
	if (true == $default_args)
	{
		$queried_pages = $cltvo_wp_query->query(array('post_type' => 'page'));
		return get_page_children( $post->ID, $queried_pages );
	}

	$query = $cltvo_wp_query->query($query_args);
	return get_page_children( $post->ID, $query );

}


/**
 * Determines if an attachment is of the specified MIME Type
 * @param  number  $attachment_id 
 * @param  string  $mime_type     
 * @return boolean                False if MIME Type does not match.
 */
function isMimeType($attachment_id, $mime_type) {
	return strpos( get_post_mime_type( $attachment_id ), $mime_type ) !== false;
}

/**
 * Adds class to selected menu item
 * 
 * @param  string $name Slug, custom post type name, or categroy name.
 * @param  string $type Page, single, category, or custom
 * @return string       CSS class
 */
function selectMenuItem($name, $type='page') {
	global $post;

	if ($type === 'page') {
		if ( is_page($name) ) {
			return 'selected';
		}
	}

	if ($type === 'custom') {
		if ( is_post_type_archive($name) ) {
			return 'selected';
		}
	}

	 if ($type === 'category') {
		if ( is_category($name) ) {
			return 'selected';
		}
	}

	if ($type === 'single') {
		if (is_single() && get_the_category($post->ID)[0]->slug === $name) {
			return 'selected';
		}
	}
}

function specialPageMeta($slug, $meta, $single = true) {

	if (!isset( $GLOBALS['special_pages_ids'][$slug] ) ) {
		throw new Exception("Special page -- $slug -- not found");
	}

	return get_post_meta($GLOBALS['special_pages_ids'][$slug], $meta , $single);
}

/**
 * Imprime los meta property de la pagina
 * @param  string $title       titulo de la pagina que se le asginara
 * @param  string $descripcion descripccion de la pagina
 * @param  object $image       objeto tipo Cltvo_Img
 */
function social_properties($title, $descripcion, $image){


		// if( empty($descripcion ) ){
		// 		$page = get_post($GLOBALS['special_pages_ids']['acerca-de']);
		// 		$descripcion = cltvo_translate($page->post_content);
		// }


		if(strlen($descripcion) > 160){
			$pos = strpos($descripcion, ' ', 160);
			$descripcion = substr($descripcion,0, $pos).'...';
		}




		echo '<meta name="description" content="'. $descripcion.'">
			
				<!-- Facebook Metadata /-->
				<!-- <meta property="fb:page_id" content="" /> -->
				<meta property="og:url" content=" '. cltvo_current_url() .' "/>
				<meta property="og:image" content=" '. $image->src .' "/>
				<meta property="og:image:width" content=" '. $image->width .' " />
				<meta property="og:image:height" content=" '. $image->height .' " />
				<meta property="og:description" content=" '. $descripcion .' "/>
				<meta property="og:title" content=" '. $title .' "/>
			
				<!-- Google+ Metadata /-->
			
				<meta itemprop="name" content=" '. $title .' ">
				<meta itemprop="description" content=" '. $descripcion .' ">
				<meta itemprop="image" content=" '. $image->src .' ">';

}

/**
 * genera la direccion donde se encuentr actualemnte
 * @return string  url actual
 */
function cltvo_current_url(){
		$s = &$_SERVER;
		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
		$sp = strtolower($s['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
		$port = $s['SERVER_PORT'];
		$port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
		$host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
		$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
		$uri = $protocol . '://' . $host . $s['REQUEST_URI'];
		$segments = explode('?', $uri, 2);
		$url = $segments[0];
		return $url;
}



function getTermNames($terms) {
	return join(', ', 
		array_map(function($term){
			return $term->name;
		}, $terms)
	);
}

function isChildOfPage($parent_id, $child_id) {
	$children = get_children( 
		array(
			'post_parent' => $parent_id,
			'numberposts' => -1
		)
	);
	
	$ids = array_map(function($child) {return $child->ID;}, $children);

	return in_array($child_id, $ids);
}


