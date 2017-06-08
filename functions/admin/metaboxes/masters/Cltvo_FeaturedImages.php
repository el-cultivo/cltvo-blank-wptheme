<?php

class Cltvo_FeaturedImages extends Cltvo_Metabox_Master {

	/* Sobre escribiendo las opcciones del master */
	protected $description_metabox = 'Sellos';
	protected $post_type = 'page';
	protected $prioridad = 'high';


	/* Banners... habría que renombrar la todo a featuredImages... */
	protected static $banners = [ 
		'featured' 		=> 'Imagen Principal',
		'hover'	=> 'Imagen en Hover',
	];

	/* Define el metodo que inicializa el valor del meta */
	public static function setMetaValue($meta){
		$meta = is_array($meta) ? $meta : [] ;
		foreach (self::$banners as $banner => $key) {
			$meta[$banner] = isset($meta[$banner]) ? $meta[$banner] :  '';
		}
		return $meta;
	}


	/* 
	Es la funcion que muestra el contenido del metabox
	@param object $object en principio es un objeto de WP_post
	*/
	public function CltvoDisplayMetabox($object) {
		//echo "<pre>"; print_r($this->meta_value); echo "</pre>";
		?>

			<div id="table__banners">

			<table class="table__banners" cellpadding="0" cellspacing="0">
				<tbody id="tbody__imagen_JS">
					<?php foreach (self::$banners as $key_value => $value) : ?>

						<tr id="<?php echo $this->meta_key."_".$key_value; ?>" class="banner_row">
							<td class="label">
								<?php echo $value; ?>
							</td>
							<td class="thumbnail">
								<div class="thumbnail_holder">
									<?php if ( $this->meta_value[$key_value] != '' ) : ?>
										<div class="reset">&#10005;</div>
										<?php echo wp_get_attachment_image($this->meta_value[$key_value],array(100,100)); ?>
									<?php endif; ?>
									<button class="button media-button" style="display:none;">Elegir Imagen</button>
								</div>
							</td>
							<td>
								<input 
								type="text"
								class="media-input"
								placeholder="URL"
								name="<?php echo $this->meta_key."[".$key_value."]"; ?>"
								id="<?php echo $this->meta_key."_".$key_value; ?>"
								value="<?php echo $this->meta_value[$key_value]; ?>"
								style="display:none;"
								/>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

		</div>

	<?php } 
}