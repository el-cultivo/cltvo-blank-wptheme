<?php

/**
 * Los metabox se incluyen como clases en la carpeta metaboxes
 *
 */

/** ==============================================================================================================
 *                                                inaterface
 *  ==============================================================================================================
 */

interface Cltvo_metabox_interface{
	/**
	 * Es la funcion que muestra el contenido del metabox
	 * @param object $object en principio es un objeto de WP_post
	 */
	public function CltvoDisplayMetabox($object);
	/**
	 * en esta funcion se inicializan los valores del metabox
	 */
	public  function setMetaValue($meta_value);
	/**
	 * regresa los valores del metabox para un post
	 */
	public function getMetaValue($object);
	/**
	 * define el metodo donde se mostrara el meta
	 * @param object $object en principio es un objeto de WP_post
	 */
	public static function displayRule();
	/**
	 * guarda el valor del metabox
	 */
	public function CltvoSaveMetaValue();
	/**
	 * Agrega el hook que coloca el meta en el admin
	 */
	public function CltvoMetaBox();

}

/** ==============================================================================================================
 *                                                abstract class
 *  ==============================================================================================================
 */
abstract class Cltvo_metabox_master implements Cltvo_metabox_interface{

	protected $meta_key;
	protected $meta_value;

	private $id_metabox;
	private $description_metabox;
	private $post_type = "post";
	private $position = "normal";
	private $prioridad = "default";
	private $ags = null;

	/**
	 * construccion del metabox
	 * @param string $meta_key     nombre del meta
	 * @param string $metabox_name titulo de la caja del metabox
	 * @param array $values       agumentos restante para costruir el meta
	 */

	function __construct(  $meta_key, $metabox_name, array $values = [] ){

		$this->meta_key = $meta_key;
		$this->description_metabox = $metabox_name;

		$this->id_metabox = $this->meta_key."_mb";

		$this->post_type = isset($values["post_type"]) ? $values["post_type"] : $this->post_type ;
		$this->position = isset($values["position"]) ? $values["position"] : $this->position ;
		$this->prioridad = isset($values["prioridad"]) ? $values["prioridad"] : $this->prioridad ;
		$this->ags = isset($values["ags"]) ? $values["ags"] : $this->ags ;

		if ($this->displayRule()) {
			$this->CltvoMetaBox();
		}

		$this->CltvoSaveMetaValue();
	}

	/**
	 * Agrega el hook que coloca el meta en el admin
	 */
	public function CltvoMetaBox(){
		add_action( 'add_meta_boxes', function(){
			add_meta_box(
				$this->id_metabox,		//id
				$this->description_metabox, //título
				function($object){
					$this->meta_value = $this->getMetaValue($object);
					$this->CltvoDisplayMetabox($object);
				},		//callback function
				$this->post_type,			//post type
				$this->position,						//posición
				$this->prioridad
			);
		} ); // agrega las metabox
	}

	/**
	 * guarda el valor del metabox
	 */
	public function CltvoSaveMetaValue(){
		add_action( 'save_post', function($id){

			if( !current_user_can('edit_post', $id) ) return $id;

			// Vs Autosave
			if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return $id;
			if( wp_is_post_revision($id) OR wp_is_post_autosave($id) ) return $id;

			// ---------------------- salva el meta box ----------------------

			if( isset( $_POST[ $this->meta_key ] ) ) {
			    update_post_meta( $id, $this->meta_key , $_POST[ $this->meta_key ] );
			}

		} ); // guarda el valor de las metabox
	}

	/**
	* define el metodo donde se mostrara el meta
	* @return boolean si es verdadero el meta sera desplegado en el admin en caso constrario no
	*/
	public static function displayRule(){
		return true;
	}

	/**
	 * define el metodo que inicializa el valor del meta
	 */
	public function setMetaValue($meta_value){
		return $meta_value;
	}

	/**
	 * regresa el el valor del meta inicializado para un post
	 * @param object $object en principio es un objeto de WP_post
	 * @return string|array valor del meta inicalizado
	 */
	public function getMetaValue($object){
		return $this->setMetaValue(
			get_post_meta($object->ID, $this->meta_key, true)
		);;
	}



	/**
	 * Es la funcion que muestra el contenido del metabox
	 * @param object $object en principio es un objeto de WP_post
	 */
	abstract public function CltvoDisplayMetabox( $object );

}


/** ==============================================================================================================
 *                                               agrega todos los metaboxes
 *  ==============================================================================================================
 */
add_action('admin_init', function(){
	foreach (glob(METADOXDIR.'*.php') as $filename){
		$clase =  str_replace( [METADOXDIR,".php"],[""], $filename );
		include $filename;
		new $clase;
	}
});
