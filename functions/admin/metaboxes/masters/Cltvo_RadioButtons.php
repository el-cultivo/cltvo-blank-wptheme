<?php

class Cltvo_RadioButtons extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Radio botons';
	protected $post_type = 'page';
	protected $prioridad = 'high';


	/* */
	public static $opciones = [ 
		'aventura'	=> 'Aventura',
		'confort' 		=> 'Confort'
	];


	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		$meta = is_array($meta) ? $meta : [] ;
		$meta['radios'] = isset($meta['radios']) ? $meta['radios'] : [] ;
		foreach (static::$opciones as $stuff => $key) {
			$meta['radios'][$stuff] = isset($meta['radios'][$stuff]) ? $meta['radios'][$stuff] :  '';
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
					<?php foreach (static::$opciones as $key_value => $value) : ?>
						<tr id="<?php echo $this->meta_key."_".$key_value; ?>" class="banner_row">
							<td>
								<label for="<?php echo $this->meta_key."_".$key_value; ?>"><?php echo $value ?></label>
							</td>
							<td>
								<input 
								<?php echo $this->meta_value == $key_value ? 'checked="checked"': ''; ?>
									type="radio"
									group="<?php echo $this->meta_key?>"
									name="<?php echo $this->meta_key ?>"
									id="<?php echo $this->meta_key."_".$key_value; ?>"
									value="<?php echo $key_value?>"
								/>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php 
	} 

}