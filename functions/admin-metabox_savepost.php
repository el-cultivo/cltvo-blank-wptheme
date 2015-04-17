<?php

/**
 * En este archivo se incluyen los meta box y las funciones de save post. 
 *
 */

/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

// add_action( 'add_meta_boxes', 'cltvo_metaboxes' ); // agrega las metabox
// add_action( 'save_post', 'cltvo_save_post' ); // guarda el valor de las metabox 


/** ==============================================================================================================
 *                                                Meta box
 *  ==============================================================================================================
 */



// ---------------------- agrega el meta box ---------------------- 
function cltvo_metaboxes(){

	add_meta_box(
		'inter_descripcion_mb',		//id
		'Descripción',				//título
		'inter_descripcion_fc',		//callback function
		'inter_activi_pt',			//post type
		'side'						//posición
	);

	// agrega aqui ...
}

// ---------------------- funcion del meta box ---------------------- 

function inter_descripcion_fc($object){
	echo '<p><input type="checkbox" name="inter_descripcion_in" ';
	if( get_post_meta($object->ID, 'inter_descripcion_meta') )echo "checked";
	echo '> Descripción de sección</p>';
}
function inter_colaborador_fc($object){
	echo '<p><label>Nombre del colaborador:</label></p>';
	echo '<input name="inter_colaborador_in" type="text" value="';
	echo get_post_meta($object->ID, 'inter_colaborador_meta', true);
	echo '" />';
}

function crdmn_equipo_fc($object){?>
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

// funciones aqui ...


/** ==============================================================================================================
 *                                                Save post
 *  ==============================================================================================================
 */

function cltvo_save_post($id){
	// Permisos
	if( !current_user_can('edit_post', $id) ) return $id;

	// Vs Autosave
	if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return $id;
	if( wp_is_post_revision($id) OR wp_is_post_autosave($id) ) return $id;

	// ---------------------- salva el meta box ----------------------  

	// coloca el meta del metabox en el array 

	$meta_data_array = array( 
								'inter_descripcion_meta', 
								'inter_colaborador_meta'
							);

	foreach ( $meta_data_array as $meta_data ) {
		cltvo_save_metabox($meta_data);
	}

	// ---------------------- funciones interiores del save ---------------------- 


}

/** ==============================================================================================================
 *                               funciones adicionales de los metabox o del save post
 *  ==============================================================================================================
 */

/**
 * Guarda o actulaliza el valor de un meta data 
 * 
 * Parametros:
 *
 * @param string $meta_data nombre del meta data 
 *
 */

function cltvo_save_metabox($meta_data){

		if( isset( $_POST[ $meta_data ] ) ) {
	    update_post_meta( $id, $meta_data , $_POST[ $meta_data ] );
	}

}
?>