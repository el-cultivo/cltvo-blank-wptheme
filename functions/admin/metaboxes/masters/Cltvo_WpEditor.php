<?php


class Cltvo_WpEditor extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Agrega tu texto';
	protected $post_type = 'page';
	protected $prioridad = 'high';

	protected static $editors = array ('wp_editor_1'	=> 	'');

	protected static $media_buttons = false;

	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		$meta = is_array($meta) ? $meta : [] ;
		foreach (static::$editors as $stuff => $value) {
			$meta[$stuff] = isset($meta[$stuff]) ? $meta[$stuff] :  '';
		}
		return $meta;
	}


	/* 
	Es la funcion que muestra el contenido del metabox
	@param object $object en principio es un objeto de WP_post
	*/
	public function CltvoDisplayMetabox($object) {
		?>
			<table class="" cellpadding="0" cellspacing="0">
				<tbody id="">
					<?php foreach (static::$editors as $key => $value): ?>
						<tr id="" class="">
							<label for="<?php echo  $this->meta_key; ?>[<?php echo $key ?>]"><?php echo $value ?></label>
						</tr>
						<tr>
							<?php wp_editor($this->meta_value[$key], strtolower($this->meta_key), array(
								'textarea_name'	=> 	$this->meta_key.'['.$key.']',
								'media_buttons'	=> 	self::$media_buttons
							)) ?>							
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
	<?php 
	}
}


