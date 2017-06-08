<?php

class Cltvo_SingleCheckbox extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Selecciona el checkbox';
	protected $post_type = 'page';
	protected $prioridad = 'high';
	protected $label_text = 'Label';
	protected $checkbox_value_name = 0;

	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){	
		return is_array($meta) ? $meta : array();
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
							<input type="hidden" name="<?=  $this->meta_key; ?>[init]" id="<?= $this->meta_key; ?>_init" value="1" />
								<label for="<?php echo  $this->meta_key; ?>">
									<?php echo $this->label_text ?>
									<input type="checkbox" name="<?=  $this->meta_key; ?>[<?php echo $this->checkbox_value_name ?>]" id="<? echo $this->meta_key; ?>_<?php echo $this->checkbox_value_name ?>" 
										<?php if (isset($this->meta_value[$this->checkbox_value_name])): ?>
											value="1" 
											checked="true"
										<?php endif ?>
									/>
								</label>
							</td>
						</tr>
				</tbody>
			</table>
	<?php 
	}
}

