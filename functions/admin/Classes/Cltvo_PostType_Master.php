<?php
abstract class Cltvo_PostType_Master {

	public $ID;
	public $post;
	public $permalink;
	public $thumbail_img; // hace referencia a la imagen destacada y no la tamaño de la imagen 
	public $thumbail_img_id; // hace referencia a la imagen destacada y no la tamaño de la imagen
	public $images;
	public $terms;


	/**
	 * construccion del metabox
	 * @param object $post     en pricipio un WP_post
	 */

	function __construct( $post_obj = false ){

	//  se carga el post
		if (!$post_obj) { // se carga el que se encuentre en el loop
			global $post;
			$this->post = $post;
		}else{ // se carga uno en esecifico
			$this->post = $post_obj;
		}
	// permalink
		$this->permalink = get_permalink($this->post->ID);
	// ID
		$this->ID = $this->post->ID;
	// se cargan los metas
		$this->setMetas();
	// se cargan las imagenes
		$this->setImages();
	// se cargan las categorias
		$this->setTerms();
	}


	/**
	 * otorga los valores de los metas al objeto
	 */
	abstract public function setMetas();

	/**
	 * otorga las imagenes al objeto
	 */
	public function setImages(){
		// imagen destacada
            $this->thumbail_img = $this->getThumbnailImg(); // hace referencia a la imagen destacada y no la tamaño de la imagen
        // imagenes
            $this->images = $this->getImages();
	}

	/**
	 * regressa un objeto de imagen desacada
	 * @return Cltvo_Img       Imagen destacada
	 */
	public function getThumbnailImg()
	{
		$this->thumbail_img_id = intval( get_post_thumbnail_id( $this->post->ID ) ); // hace referencia a la imagen destacada y no la tamaño de la imagen
		return has_post_thumbnail($this->post->ID) ? new Cltvo_Img( $this->thumbail_img_id) : NULL;
	}

	/**
	 * regresa un arreglo con las imagenes del post
	 * @param  string $size tamaño de la imagen
	 * @param  array $size con los ids de las imagenes a excluir
	 * @return array       Imagenes del post  como objetos Cltvo_Img
	 */
	public function getImages( $exclude= false)
	{
		return cltvo_todasImgsDelPost($this->post->ID,  $exclude);
	}

	/**
	 * otorga las categorias al objeto
	 */
	public function setTerms(){

		foreach (get_taxonomies() as $taxonomy  ) {
			$this->terms[$taxonomy] =  $this->getTerms( $taxonomy);
			//$this->{'terms_'.$taxonomy} = $this->terms[$taxonomy];
		}
	}

	/**
	 * regressa un arrglo con los temrs de una categorya
	 * @param string	$taxomnomies  taxomomia a ser regresdas
	 * @return Cltvo_Img       Imagen destacada
	 */
	public function getTerms( $taxomnomy)
	{
		return wp_get_post_terms($this->post->ID, $taxomnomy );
	}
}
