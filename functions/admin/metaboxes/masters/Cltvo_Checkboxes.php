<?php

class Cltvo_Checkboxes extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Activar Reservaciones';
	protected $post_type = 'page';
	protected $prioridad = 'high';


	/* */
	public static $opciones = [ 
		'reservations'	=> 'Mostrar contenidos relacionados con la información de las reservaciones, de lo contrario se mostrará el mensaje <strong>No reservations</strong>',
	];


	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		$meta = is_array($meta) ? $meta : [] ;
		$meta['checkboxes'] = isset($meta['checkboxes']) ? $meta['checkboxes'] : [] ;
		foreach (static::$opciones as $stuff => $key) {
			$meta['checkboxes'][$stuff] = isset($meta['checkboxes'][$stuff]) ? $meta['checkboxes'][$stuff] :  '';
		}
		return $meta;
	}


	/* 
	Es la funcion que muestra el contenido del metabox
	@param object $object en principio es un objeto de WP_post
	*/
	public function CltvoDisplayMetabox($object) {
		?>
			<input type="hidden" value="true" name="<?php echo $this->meta_key.'[init]'?>">
			<table class="" cellpadding="0" cellspacing="0">
				<tbody id="">
					<?php foreach (static::$opciones as $key_value => $value) : ?>
						<tr id="<?php echo $this->meta_key."_".$key_value; ?>" class="banner_row">
							<td>
								<label for="<?php echo $this->meta_key."_".$key_value; ?>"><?php echo $value ?></label>
							</td>
							<td>
								<input 
									<?php echo $this->meta_value['checkboxes'][$key_value] == 'on' ? 'checked="checked"': ''; ?>
									type="checkbox"
									name="<?php echo $this->meta_key.'[checkboxes]['.$key_value.']'?>"
									id="<?php echo $this->meta_key."_".$key_value; ?>"
								>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
	<?php } 

}