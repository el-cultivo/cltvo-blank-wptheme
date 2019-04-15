<?php

namespace Illuminate;

use Illuminate\Image\Image;
use \WP_Query;

abstract class PostType
{
	public $post;
	
	public $permalink;
	
	public $ID;

	public $thumbail_img;
	
	public $thumbail_img_id;
	
	public $images;
	
	public $terms;

	function __construct( $post_obj = false )
	{
		// Se carga el post
		if (!$post_obj) {
			// Se carga el que se encuentre en el loop
			global $post;
			$this->post = $post;
		}else{
			// Se carga uno en especifico
			$this->post = $post_obj;
		}
		
		// Permalink
		$this->permalink = get_permalink($this->post->ID);
		
		// ID
		$this->ID = $this->post->ID;
		
		// se cargan los metas
		$this->setMetas();
		
		// Se cargan las imagenes
		$this->setImages();
		
		// Se cargan las categorias
		$this->setTerms();
	}

	abstract public function setMetas();

	public function setImages()
	{
		// Imagen destacada
        $this->thumbail_img = $this->getThumbnailImg(); // hace referencia a la imagen destacada y no la tamaño de la imagen
		
		// Imagenes
        $this->images = $this->getImages();
	}

	public function getThumbnailImg()
	{
		$this->thumbail_img_id = get_post_thumbnail_id( $this->ID );
		
		return has_post_thumbnail($this->ID) ? new Image($this->thumbail_img_id) : null;
	}

	public function getImages($exclude = false)
	{
		return cltvo_todasImgsDelPost($this->ID,  $exclude);
	}

	public function setTerms()
	{
		foreach (get_taxonomies() as $taxonomy) {
			$this->terms[$taxonomy] = $this->getTerms($taxonomy);
		}
	}

	public function getTerms( $taxonomy)
	{
		return wp_get_post_terms($this->post->ID, $taxonomy);
	}

	public static function find($id, $post_type = 'any')
	{
		$args = [
			'post_type' => $post_type,
			'post__in' => [$id],
			'posts_per_page' => 1,
		];

		$query = new WP_Query($args);

		if(empty($query->posts)){
			return null;
		}

		return $query->posts[0];
	}
}
