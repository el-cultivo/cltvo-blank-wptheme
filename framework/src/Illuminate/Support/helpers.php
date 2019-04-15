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


/**
 * Clase para las imagenes del post que contiene el id de la imagen, la src, el tamaño y el alt
 *
 */

	// include 'Classes/Cltvo_Img.php';
	// include 'helper-functions/helpers.php';

use Illuminate\Image\Image;

/**
 * regresa todas las imagenes del post con sus caracteristicas en un array
 *
 * Parametros:
 *
 * @param int $parentId id del post
 * @param boolean|array $exclude imagenes a ser excluidas (por defecto false)
 *
 * @return array con las imagenes y sus caracteristicas
 */

 function cltvo_todasImgsDelPost($parentId, $exclude= false){
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
	     $images[] = new Image($image->ID);
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
	function cltvo_print_r(){
		foreach (func_get_args() as $value) {
			echo "<pre>";
				var_dump($value);
			echo "</pre>";
		}
		die;
	}
}

//convierte int a string con signo de pesos, con punto decimal y dos ceros al final
if( !function_exists('cltvo_dinero') ){
	function cltvo_dinero( $numero ){
		return '$ ' . number_format($numero, 2);
	}
}

/**
 * trae el permalink de una pagina especial
 * @param  string $slug slug de la pagina especial
 * @return string       url de la pagina
 */
function specialPagePermalink($slug) {
   return get_permalink( specialPage($slug) );
}

/**
 * verifica si la pagina especial existe
 * @param  string $slug slug de la pagina especial
 * @return boolean       generera una esepcion en caso de  que la pagia especial no exista
 */
function specialPageExists($slug){

   if (!isset( $GLOBALS['special_pages_ids'][$slug] ) ) {

	   throw new Exception("Special page -- $slug -- not found");
   }

   return true;
}

/**
 * Trae las paginas especiales
 * @param  string  $slug   Slug de la página especial
 * @param  boolean $object Si se desea traer el un objeto o sólo el id
 * @return objeto o string
 */
function specialPage( $slug, $object = false )
{
	specialPageExists($slug);

   if ($object){
	   return get_post($GLOBALS['special_pages_ids'][$slug]);
   }
   return $GLOBALS['special_pages_ids'][$slug];
}

/**
 * funcion auxuliar para las paginas especiales, verifica si la pagina a editar es una pagina especial
 * @param  string  $slug slug de la pagina a verificar
 * @return boolean      si se edita la pagina especual mencionada
 */
function isSpecialPage($slug)
{
   specialPageExists($slug);
   return isset($_GET['post']) && $_GET['post'] == specialPage( $slug);
}

function specialPageMeta($slug, $meta, $single = true) {

   if (!isset( $GLOBALS['special_pages_ids'][$slug] ) ) {
	   throw new Exception("Special page -- $slug -- not found");
   }

   return get_post_meta($GLOBALS['special_pages_ids'][$slug], $meta , $single);
}

function inSpecialPage($slug)
{
   return is_page(specialPage($slug));
}

function inSpecialPageOrChild($slug, $post = null)
{
   $page_id = specialPage($slug);

   if ($post) {
	   return in_array($page_id, [$post->ID,$post->post_parent]);
   }

   return false;
}

function getSpecialPageBySlug($slug,$post_parent_id)
{
   global $wpdb;

   $querystr = "
	  SELECT $wpdb->posts.*
	  FROM $wpdb->posts
	  WHERE $wpdb->posts.post_name = '$slug'
	  AND $wpdb->posts.post_type = 'page'
   ";
   $pages = $wpdb->get_results($querystr, OBJECT);

   if (count($pages) > 1) {
	   $pages = array_filter($pages , function($page ) use ($post_parent_id){
		   return $page->post_parent == $post_parent_id;
	   });
   }

   return empty($pages) ? null : array_values($pages)[0];
}

function getSpecialPageBySlugTrashed($slug,$post_parent_id)
{
   global $wpdb;

   $querystr = "
	  SELECT $wpdb->posts.*
	  FROM $wpdb->posts
	  WHERE $wpdb->posts.post_name = '".$slug."__trashed'
	  AND $wpdb->posts.post_status = 'trash'
	  AND $wpdb->posts.post_type = 'page'
   ";

   $pages = $wpdb->get_results($querystr, OBJECT);

   if (count($pages) > 1) {
	   $pages = array_filter($pages , function($page ) use ($post_parent_id){
		   return $page->post_parent == $post_parent_id;
	   });
   }

   if (count($pages) > 1) {
	   throw new \Exception("mor than 1 page $slug", 1);

   }

   return empty($pages) ? null :array_values($pages)[0] ;
}

function toDateTime($date) {
	return new DateTime($date);
}

function isMobile()
{
	return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
}

function toSnakeCase($input)
{
	return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
}

function parse_money($value, $decimals = 0)
{
    return "$".number_format(floatval($value), $decimals);
}

function getInstagramPosts($args = []) {

    if(!function_exists('getSelfMediaRecent')){
        return [];
    }

    return getSelfMediaRecent($args);
}

function add_to_global_query($post = 0) {

    global $query_args;

    $post = get_post( $post );

    if(array_key_exists('post__not_in', $query_args)) {
        $query_args['post__not_in'] = array_merge($query_args['post__not_in'], [$post->ID]);
    }else {
        $query_args['post__not_in'] = [$post->ID];
    }
}

function get_thumbnail_image_url($thumbnailId = 0, $size = 'full') {

    if($thumbnailId == 0){
        $thumbnailId = get_post_thumbnail_id();
    }

    return wp_get_attachment_image_src($thumbnailId, $size)[0];

}

function get_terms_of_post($post = 0, $taxonomy = 'category', $output = false) {

    $post = get_post( $post );

    $terms = get_the_terms($post->ID, $taxonomy);

    if(!$output) {
        return $terms;
    }

    $echo = '';

    if($terms) {

        $terms = array_map(function($term) {
            $term->order = get_term_meta($term->term_id, 'order', true) ?: 0;
            return $term;
        }, $terms);

        usort($terms, function($a, $b) {
            return $a->order > $b->order;
        });

        $echo .= '<ul>';
        foreach($terms as $term) {
            $echo .= '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
        }
        $echo .= '</ul>';
    }

    return $echo;
}

function the_terms_of_post($post = 0, $taxonomy = '') {
    echo get_terms_of_post($post, $taxonomy, true);
}

function return_404() {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
}

function is_taxonomy_page($taxonomy = '', $term = '')
{
    return is_tax($taxonomy, $term) || is_category($term);
}

function date_from_format($date, $format = 'd/m/Y')
{
    return DateTime::createFromFormat($format, $date);
}

function date_abr($abr, $date)
{
    $date = date_from_format($date);

    if(!$date){
        return '';
    }

    return strftime($abr, $date->getTimestamp());
}

function pluck($array, $prop)
{
     if(!is_array($array)){
          return [];
     }

     return array_map(function($item) use ($prop){
          return $item->{$prop};
     }, $array);
}
