<?php

class Cltvo_Img{
	public $img_id;
	public $srcs;
	public $width;
	public $height;
	public $alt;
	public $proporcion;
	public $img_info;

	public function __construct( $img_id ){

		$this->img_info = wp_get_attachment_metadata($img_id);
		if (!empty($this->img_info ) ) {
			$this->img_id = $img_id;

		// ligas a los diferentes tamaños
			$this->src = $this->srcs["full"] = wp_get_attachment_image_url ($img_id,"full");
			foreach ($this->img_info["sizes"] as $size => $size_info) {
				$this->{"src_".$size}  = $this->srcs[$size] = wp_get_attachment_image_url ($img_id,$size);
			}

			$this->width = $this->img_info['width'];
			$this->height = $this->img_info['height'];
			$this->alt = get_post_meta($this->img_id, '_wp_attachment_image_alt', true);

		// porporcion de la imagen
			if( $this->width == $this->height ){
				$this->proporcion = "cuadrada";
			}elseif( $this->width > $this->height ){
				$this->proporcion = "horizontal";
			}else{
				$this->proporcion = "vertical";
			}

		}

	}

	/**
	 * regresa el src de la imagen en un tamaño desplegado
	 * @param  string  $size tamaño de ña imagen
	 * @return string        src de la imagen
	 */
	public function getImgSrc($size){
		return isset($this->srcs[$size]) ? $this->srcs[$size] : $this->src;
	}
}
