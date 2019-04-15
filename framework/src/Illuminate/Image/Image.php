<?php

namespace Illuminate\Image;

class Image
{
	public $id;
	
	public $src;
	
	public $srcs = [];
	
	public $width;
	
	public $height;
	
	public $alt;
	
	public $title;
	
	public $proportion;
	
	public $metadata;

	public function __construct( $id )
	{
		$this->metadata = wp_get_attachment_metadata($id);
		
		if (!empty( $this->metadata ) ) {
			
			$this->id = $id;

			// Links a los diferentes tamaños
			$this->src = $this->srcs['full'] = wp_get_attachment_image_url($id, 'full');
			
			foreach ($this->metadata['sizes'] as $size => $size_info) {
				$this->{'src_'.$size} = $this->srcs[$size] = wp_get_attachment_image_url($id, $size);
			}

			$this->width = $this->metadata['width'];
			$this->height = $this->metadata['height'];
			$this->alt = get_post_meta($this->id, '_wp_attachment_image_alt', true);
			$this->title = get_the_title($this->id);


			// Proporción de la imagen
			if( $this->width == $this->height ){
				$this->proportion = 'cuadrada';
			}elseif( $this->width > $this->height ){
				$this->proportion = 'horizontal';
			}else{
				$this->proportion = 'vertical';
			}

		}
	}

	/**
	 * regresa el src de la imagen en un tamaño desplegado
	 * @param  string  $size tamaño de ña imagen
	 * @return string        src de la imagen
	 */
	public function getImgSrc($size)
	{
		return array_key_exists($size, $this->srcs) ? $this->srcs[$size] : $this->src;
	}
}
