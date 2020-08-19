<?php 

class CustomLocation extends ACF_Location {

	protected $special_pages;

    public function initialize() 
    {
        $this->name = 'special_page';
        $this->label = __( "Special Pages", 'acf' );
        $this->category = 'page';
        $this->object_type = 'page';
        $this->config = require realpath(__DIR__.'/../../') . '/config/app.php';
    }

    public function get_values( $rule ) {
	    $special_pages = $this->config['special-pages'];

	    if( $special_pages ) {
	        foreach( $special_pages as $slug => $page ) {
	            $choices[ $slug ] = $page[0];
	        }
	    }

	    return $choices;
	}

	public function match( $rule, $screen, $field_group ) {

	    //Verificamos si existe el post_id en la pantalla actual 
	    if( isset($screen['post_id']) ) {
	        $post_id = $screen['post_id'];
	    } else {
	        return false;
	    }

	    //Cargamos el post y si no existe, regresamos falso
	    $post = get_post( $post_id );
	    if( !$post ) {
	        return false;
	    }

	    //Preguntamos si el name del posst es igual a la regla que se está buscando
	    $result = ( $post->post_name == $rule['value'] );

	    //Si el operador de la regla es "Diferente de", negamos el resultado anterior
	    if( $rule['operator'] == '!=' ) {
	        return !$result;
	    }

	    //Regresamos el booleano
	    return $result;
	}
}
