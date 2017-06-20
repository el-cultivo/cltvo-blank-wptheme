<?php

class Cltvo_Galeria extends Cltvo_Metabox_Master {

    /* Sobre escribiendo las opcciones del master */
    protected $description_metabox = 'Galería';
    protected $post_type = 'page';


    /* Galería */
    private $galeria = array( 'imagen'  => 'Imagen' );

    public static function metaboxDisplayRule(){
    	return true;
    }

    /* Define el metodo que inicializa el valor del meta */
     public static function setMetaValue($meta_value){
         if (is_array($meta_value)) {
            foreach ($meta_value as $key => $value) {
                foreach ((new static)->galeria as $galeria_key => $item) {
                    $meta_value[$key][$galeria_key] = (isset($meta_value[$key][$galeria_key]) && !empty($meta_value[$key][$galeria_key])) ? $meta_value[$key][$galeria_key] : '';
                }
            }
         } else {
         	$meta_value = array( 0 => array( 'imagen'  => '' ) );
         }
         return $meta_value;
     }

    public static function getImages($object){
     	return array_map(function($img_arr){
			return new Cltvo_Img($img_arr['imagen']);
		}, static::getMetaValue($object));
    }


	/* 
	Es la funcion que muestra el contenido del metabox
	@param object $object en principio es un objeto de WP_post
	*/
	public function CltvoDisplayMetabox($object) {
		//echo "<pre>"; print_r($this->meta_value); echo "</pre>";
		?>
		<div id="table__galeria">

			<table class="table__galeria" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<?php foreach ($this->galeria as $item): ?>
						<!--
							<th class="th__galeria" align="left" style="padding-bottom:10px;">
								 <?php echo $item; ?> <span class="warn">*</span> 
							</th>
						-->
						<?php endforeach; ?>
						<th class="th__galeria" style="padding-bottom:10px;">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody id="tbody__imagen_JS">
					<?php $this->drawTemplate($this->meta_value); ?>
				</tbody>
			</table>
		</div>

	<?php }

	public function drawTemplate($meta_value) {
		$gallery_images = array_map(function($image) {
			$image['url'] =  wp_get_attachment_url($image['imagen']);
			return $image;
		}, $meta_value);
		$gallery_images_json = json_encode($gallery_images);
		?>
			<tr>
				<td>
					<style>
						.gallery__image {
							height: 150px;
							width: 150px;
							background-size: cover;
							background-position: center;
						}

						.gallery__image-container {
							margin-bottom: 15px;
						}
					</style>
					<div id="<?php echo $this->meta_key ?>" class="cltvo_gallery_container_JS" data-gallery-var="<?php echo $this->meta_key ?>"></div>
					<button class="button add-image-to-gallery_JS" data-gallery-container-id="<?php echo $this->meta_key ?>">Agregar</button>
					<script>
						var <?php echo $this->meta_key ?> = JSON.parse('<?php echo  json_encode($gallery_images) ?>');
						initGallery('<?php echo $this->meta_key ?>', <?php echo $this->meta_key ?>)
					</script>
				</td>				
			</tr>
		<?php 
	}
 }