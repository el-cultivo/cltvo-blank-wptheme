<?php


class Cltvo_Links extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Links de la Página';
	protected $post_type = 'page';
	protected $prioridad = 'high';

	protected static $links = array ('link'	=> 	'Link');


	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		$meta = is_array($meta) ? $meta : [] ;
		foreach (static::$links as $stuff => $key) {
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
					<?php foreach (static::$links as $key => $value): ?>
						<tr id="" class="">
							<td style="width: 150px">
								<label for="<?php echo  $this->meta_key; ?>[<?php echo $key ?>]"><?php echo $value ?></label>
							</td>
							<td style="width: 100%; padding-left: 15px;">
								<input type="url" placeholder="http://" name="<?php echo  $this->meta_key; ?>[<?php echo $key ?>]" id="<?php echo  $this->meta_key; ?>[<?php echo $key ?>]" value="<?php echo $this->meta_value[$key]; ?>" style="max-width: 500px; width: 100%" />
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
	<?php 
	}
}


