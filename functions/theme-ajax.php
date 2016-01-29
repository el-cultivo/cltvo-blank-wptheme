<?php

/**
 * En este archivo se incluyen las funciones ajax del tema
 *
 */


/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

 //.................................... envia los mails de contacto
 // add_action( 'wp_ajax_nopriv_contact_mail', 'SendContactMail' );
 // add_action( 'wp_ajax_contact_mail', 'SendContactMail' );

/** ==============================================================================================================
 *                                                FUNCIONES
 *  ==============================================================================================================
 */

/**
*  configuracion del ajax mail y funciones
*/
include_once get_template_directory()."/ajax/ajax-mail.php";

/**
* callback que envia los cooreos de subscripcion y contacto
*/
function SendContactMail() {
    if ( isset($_POST['action']) ) {
        unset($_POST['action']);
    }
//----------------------------envió de los correos --------------------------------

    include_once  get_template_directory().'/ajax/ajax-mail_config.php';

    if (isset($_POST[id_form]) && !isset($break_form)) { // en esta función se envía la información  solo en caso de que la información llegue correctamente
        $datos = nl2br(Post_array_to_mail_html_cont($_POST[id_form], primera_linea . '<br>')); // nombre del form para crear la cadena

        if (!isset($mailchip_merge_array)) {
            $mailchip_merge_array = isset($GLOBALS["mailchip_merge_array"]) ? $GLOBALS["mailchip_merge_array"] : array();
        }

        echo cltvo_manda_mail(reg_name, reg_mail, $datos, $mailchip_merge_array); // requiere conocer los input de nombre y mail
    } else { // o regresa un error en caso de que exista un problema con los form
        echo __("Error en envío", TRANSDOMAIN);
    }
    die();

}
