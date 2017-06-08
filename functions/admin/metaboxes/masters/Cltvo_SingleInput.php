<?php

class Cltvo_SingleInput extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Ingresa tu texto';
	protected $post_type = 'page';
	protected $prioridad = 'high';
	protected $max_width = '100%';


	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		return $meta;
	}


	/* 
	Es la funcion que muestra el contenido del metabox
	@param object $object en principio es un objeto de WP_post
	*/
	public function CltvoDisplayMetabox($object) {
		?>
			<table class="" cellpadding="0" cellspacing="0" style="width: 100%">
				<tbody id="">
						<tr id="" class="">
							<td style="width: 100%; padding-left: 15px;">
								<input type="text" name="<?php echo  $this->meta_key; ?>" id="<?php echo  $this->meta_key; ?>" value="<?php echo $this->meta_value; ?>" style="max-width: <?php echo $this->max_width ?>; width: 100%" />
							</td>
						</tr>
				</tbody>
			</table>
	<?php 
	}
}

