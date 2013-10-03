<?php

define( 'JSPATH', get_template_directory_uri() . '/js/' );
define( 'BLOGURL', get_home_url('/') );
define( 'THEMEURL', get_bloginfo('template_url').'/' );

add_theme_support( 'post-thumbnails' );
add_action( 'init', 'cltvo_posttypes' );
add_action( 'init', 'cltvo_custom_tax' );
add_action( 'add_meta_boxes', 'cltvo_metaboxes' );
add_action( 'save_post', 'cltvo_save_post' );
add_action( 'wp_enqueue_scripts', 'cltvo_js' );
//add_action( 'admin_enqueue_scripts', 'cltvo_admin_js' ); //descomentar para tener JS en admin (no olvidar crear el file [admin-functions.js])


/*	SCRIPTS
	-------
*/

function cltvo_js(){
	wp_register_script( 'cltvo_functions_js', JSPATH.'functions.js', array('jquery'), false, true );
	wp_localize_script( 'cltvo_functions_js', 'cltvo_js_vars', cltvo_js_vars() );
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('cltvo_functions_js');
}

function cltvo_admin_js(){
	wp_register_script( 'cltvo_admin_functions_js', JSPATH.'admin-functions.js', array('jquery'), false, false );
	wp_localize_script( 'cltvo_admin_functions_js', 'cltvo_js_vars', cltvo_js_vars() );

	wp_enqueue_script('cltvo_admin_functions_js');
}

function cltvo_js_vars(){
	$php2js_vars = array(
		'site_url'     => home_url('/'),
		'template_url' => get_bloginfo('template_url')
	);
	return $php2js_vars;
}



/*	TIPOS DE POSTS
	--------------
*/

function cltvo_posttypes(){
	//Nombre del posttype!
	// $args = array(
	// 	'label' => 'Artistas',  								//nombre
	// 	'public' => true,										//Público
	// 	'rewrite' => array( 'slug' => 'artistas' ),				//Nombre en la url
	// 	'has_archive' => true,									//Se puede usar en un archivo
	// 	'supports' => array( 'title', 'editor', 'thumbnail' )	//Inupts UI en la página de new post
	// );
	// register_post_type( 'inter_artistas_pt', $args );		//Se registra
}



/*	TAXONOMÍAS
	----------
*/
	
function cltvo_custom_tax(){
	//Nombre de la taxonomía
	// $argumentos = array(						
	// 	'labels' => array(
	// 		'name'			=> 'Secciones',			//Nombre
	// 		'add_new_item'	=> 'Nueva Sección',		//Nombre del botón para agregar nuevo término
	// 		'parent_item'	=> 'Sección madre'		//Asignar el término a un término padre
	// 	),
	// 	'hierarchical' => true
	// );
	
	// register_taxonomy(
	// 	'inter_seccion_tax',						//nombre de la tax
	// 	'inter_activi_pt',							//a qué posttype pertenece
	// 	$argumentos
	// );	
}



/*	META CAJAS
	----------
*/
	
function cltvo_metaboxes(){
	// add_meta_box(
	// 	'inter_descripcion_mb',		//id
	// 	'Descripción',				//título
	// 	'inter_descripcion_mb',		//callback function
	// 	'inter_activi_pt',			//post type
	// 	'side'						//posición
	// );
	// add_meta_box(
	// 	'inter_colaborador_mb',
	// 	'Colaborador',
	// 	'inter_colaborador_mb',
	// 	'inter_colabora_pt',
	// 	'side'
	// );
}

// function inter_descripcion_mb($object){
// 	echo '<p><input type="checkbox" name="inter_descripcion_in" ';
// 	if( get_post_meta($object->ID, 'inter_descripcion_meta') )echo "checked";
// 	echo '> Descripción de sección</p>';
// }
// function inter_colaborador_mb($object){
// 	echo '<p><label>Nombre del colaborador:</label></p>';
// 	echo '<input name="inter_colaborador_in" type="text" value="';
// 	echo get_post_meta($object->ID, 'inter_colaborador_meta', true);
// 	echo '" />';
// }

function crdmn_equipo_mb($object){?>
	<div class="cltvo_multi_mb">
		<div class="cltvo_multi_papa">
			<?php $crdmn_equipo_arr = get_post_meta($object->ID, 'crdmn_equipo_meta', true) ? get_post_meta($object->ID, 'crdmn_equipo_meta', true) : array(''=>'');?>
			<?php $i=1;?>
			<?php foreach ($crdmn_equipo_arr as $nombre => $link):?>
			<div class="cltvo_multi_hijo cltvo_multi_hijo<?php echo $i;?>">
				<p>
					<label>Nombre </label>
					<input name="crdmn_equipo_nom<?php echo $i;?>" type="text" value="<?php echo $nombre;?>" />
				</p>
				<p>
					<label>Link </label>
					<input name="crdmn_equipo_link<?php echo $i;?>" type="text" value="<?php echo $link;?>" />
				</p>
				<hr>
			</div>
			<?php $i++;?>
			<?php endforeach;?>
		</div>
		<a href="#" class="nuevo-equipo-JS">+ agregar otro miembro de equipo</a>
	</div>
<?php
}



/*	AL GUARDAR EL POST
	------------------
*/
	
function cltvo_save_post($id){
	// Permisos
	if( !current_user_can('edit_post', $id) ) return $id;

	// Vs Autosave
	if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return $id;
	if( wp_is_post_revision($id) OR wp_is_post_autosave($id) ) return $id;

	// Cultiva Código...
}


/*	FUNCIONES DEL TEMA
	------------------
*/

?>