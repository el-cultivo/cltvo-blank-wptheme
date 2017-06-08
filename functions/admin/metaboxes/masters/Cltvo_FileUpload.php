<?php

 /* Inicializa el Media Manager de WP */
function my_load_wp_media_files() { 
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'my_load_wp_media_files' );


class Cltvo_FileUpload extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'PDF';
	protected $post_type = 'page';
	protected $prioridad = 'high';

	protected static $stuffs = array (
			'file_id'	=> 	'',
			'filename'	=> 	''
		);


	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		$meta = is_array($meta) ? $meta : [] ;
		foreach (self::$stuffs as $stuff => $key) {
			$meta[$stuff] = isset($meta[$stuff]) ? $meta[$stuff] :  '';
		}
		return $meta;
	}


	/* 
	Es la funcion que muestra el contenido del metabox
	@param object $object en principio es un objeto de WP_post
	*/
	public function CltvoDisplayMetabox($object) {
		$file_isset = $this->meta_value['filename'] != '' && $this->meta_value['file_id'] != '';
		?>
			<table class="" cellpadding="0" cellspacing="0">
				<tbody id="">
						<tr id="" class="banner_row fileUpload_row_JS">
							<td>
								<input type="button" class="button cltvo_upload_JS" value="Añadir" style="display: <?php echo $file_isset ? 'none' : 'block'; ?>">	
								<p class="fileUpload__success fileUpload__success_JS"><?php echo $this->meta_value['filename'] != '' ? $this->meta_value['filename'] : '' ?></p>
								<input type="button" class="button cltvo_remove_upload_JS" value="Eliminar" style="display: <?php echo $file_isset ? 'block' : 'none'; ?>">	
								<input type="hidden" 
									name="<?php echo $this->meta_key ?>[file_id]" class="cltvo_file_id_input_JS" 
									value="<?php echo $file_isset ? $this->meta_value['file_id'] : ''; ?>">
								<input type="hidden" 
									name="<?php echo $this->meta_key ?>[filename]" class="cltvo_filename_input_JS" 
									value="<?php echo $file_isset ? $this->meta_value['filename'] : ''; ?>">
							</td>
						</tr>
				</tbody>
			</table>
	<?php 
	}

	public static function getMetaValue($post) {
		$meta = static::setMetaValue(
			get_post_meta($post->ID, get_called_class(), true)
		);
		$meta['url'] = wp_get_attachment_url($meta['file_id']);
		return $meta;
	}
}


