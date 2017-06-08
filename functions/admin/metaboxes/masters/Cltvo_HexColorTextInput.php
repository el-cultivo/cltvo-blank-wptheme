<?php


class Cltvo_HexColorTextInput extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Selecciona un color de fondo ingresando un valor hexadecimal';
	protected $post_type = 'page';
	protected $prioridad = 'high';

	protected static $colors = array ('color'	=> 	'Color');


	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		$meta = is_array($meta) ? $meta : [] ;
		foreach (static::$colors as $stuff => $key) {
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
					<?php foreach (static::$colors as $key => $value): ?>
						<tr id="" class="">
							<td style="width: 150px">
								<label for="<?php echo  $this->meta_key; ?>[<?php echo $key ?>]"><?php echo $value ?></label>
							</td>
							<td style="width: 100%; padding-left: 15px;">
								<input type="text" placeholder="343434" name="<?php echo  $this->meta_key; ?>[<?php echo $key ?>]" id="<?php echo  $this->meta_key; ?>[<?php echo $key ?>]" value="<?php echo $this->meta_value[$key]; ?>" class="hex-color-text-input_JS" style="max-width: 500px; width: 100%" />
								<div class="hex-color-text-sample__container" 
									style="height: 25px; 
									width: 25px; 
									background-image: url('<?php echoImg('admin/transparent.png'); ?>');
									float: left; 
									border: 1px solid black;">
									<div class="hex-color-text-sample_JS" style="height: 100%; width: 100%; background-color:<?php echo $this->meta_value[$key] != '' ?  '#'.$this->meta_value[$key]  : 'transparent';?>"></div>
								</div>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
	<?php 
	}
}


