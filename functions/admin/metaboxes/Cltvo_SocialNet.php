<?php

class Cltvo_SocialNet extends Cltvo_Metabox_Master
{

    /**
     * sobre escribiendo las opcciones del master
     */
    protected $description_metabox = "Redes Sociales";
    protected $post_type = "page";


    /**
     * proiedades de esta clase
     */
    protected static $redesConUrl = [
                        'facebook' => 'Facebook:',
						'twitter' => 'Twitter:',
						// 'pinterest' => 'Pinterest:',
						// 'instagram' => 'Instagram:',
						// 'google' => 'Google plus:',
						// 'tumblr' => 'Tumblr:'
					];

    protected static $redesSinUrl = [ 'mail' => 'Email:',
						'telefono' => 'Teléfono:'
					];




  /**
	 * define el metodo donde se mostrara el meta
	 * @return boolean si es verdadero el meta sera desplegado en el admin en caso constrario no
	 */
	public static function metaboxDisplayRule(){
		return isSpecialPage("contacto");
	}


    /**
     * define el metodo que inicializa el valor del meta
     */
    public static function setMetaValue($meta){

        $meta = is_array($meta) ? $meta : [] ;

        foreach (self::$redesSinUrl as $red => $imagen) {
			$meta[$red] = isset($meta[$red]) ? $meta[$red] :  "";
		}

		foreach (self::$redesConUrl as $red => $imagen) {
			$meta[$red] = isset($meta[$red]) ? $meta[$red] :  array('cuenta'=> '', 'link'=> '');
		}

        return $meta;
    }


	/**
	 * Es la funcion que muestra el contenido del metabox
	 * @param object $object en principio es un objeto de WP_post
	 */
	public function CltvoDisplayMetabox( $object ){

        ?>

        		<table class="ancho_100" >
        			<tr>
        				<td >
        					 Teléfono:
        				</td>
        				<td>
        					<input type="text" placeholder="+(52 55) 5555 5555 " name="<?php echo  $this->meta_key; ?>[telefono]" id="<?php echo  $this->meta_key; ?>[telefono]" value="<?php echo $this->meta_value['telefono']; ?>" class="ancho_100" />
        				</td>
        			</tr>
        			<tr>
        				<td>
        					 Correo:
        				</td>
        				<td>
        					<input type="email" placeholder="ejemplo@ejemplo.com" name="<?php echo  $this->meta_key; ?>[mail]" id="<?php echo  $this->meta_key; ?>[mail]" value="<?php echo $this->meta_value['mail']; ?>" class="ancho_100" />
        				</td>
        			</tr>
        			<?php foreach (self::$redesConUrl as $slug => $nombre): ?>
        				<tr>
        					<td>
        						<?php echo $nombre; ?>
        					</td>
        					<td>
        						<?php $this->social_network_link($slug); ?>
        					</td>
        				</tr>

        			<?php endforeach; ?>

        		</table>
        		<?php
	}



    /**
     * Imprime los input de las redes sociales
     *
     * Parametros:
     *
     * @param string $slug slug de la red social
     * @param array $meta array con los valores link y cuenta
     */

    private function social_network_link($slug) {
    	 ?>
            <p>
                <label for="<?php echo $this->meta_key."_".$slug; ?>_cuenta" >Cuenta:</label><br>
                <input type="text"
                      placeholder="<?= $slug ?>"
                      name="<?php echo $this->meta_key."[".$slug."][cuenta]"; ?>"
                      id="<?php echo $this->meta_key."_".$slug; ?>_cuenta"
                      value="<?php echo $this->meta_value[$slug]['cuenta']; ?>"
                      class="ancho_100" />
            </p>
    		<p>
    			<label for="<?php echo $this->meta_key."_".$slug; ?>_link" >Link:</label><br>
    			<input type="url"
                        placeholder="http://"
                        name="<?php echo $this->meta_key."[".$slug."][link]"; ?>"
                        id="<?php echo $this->meta_key."_".$slug; ?>_link"
                        value="<?php echo $this->meta_value[$slug]['link']; ?>"
                        class="ancho_100" />
    		</p>
    	 <?php
    }

}
