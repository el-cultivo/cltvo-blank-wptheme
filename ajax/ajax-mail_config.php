<?php


/**
 * Es necesario definir los siguientes parámetros en función de los form de la pagina
 *
 * @const string mail e-mail de la prestadora de servicios
 *
 * Adicionalmente para cada form que contenga la pagina es necesario definir los siguientes parámetros:
 *  @const string id_from identificador del form al que se trate
 *  @const string reg_name nombre de la persona que se registra
 *  @const string reg_mail e-mail de la persona que se registra
 *  @const string primera_linea primera linea del contenido del mail de notificación.
 *  @const boolean mailchip llave para identificar el uso de mailchimp para este form. Por defecto false.
 *
 *  Solo para el uso de mailchimp
 *      @const string mailchip_apikey parámetro de la api de mailchimp
 *      @const string mailchip_listid parámetro de la lista de mailchimp
 *      @const string mailchip_listurl dirección de la lista de mailchimp
 *      @const boolean mailchip_mergevar_on llave para identificar el uso de parámetros adicionales en para el registro de mailchimp. Por defecto false.
 *      @const boolean mailchip_errors_send llave para identificar el envió de errores por correo del registro de mailchimp. Por defecto false.
 *
 *      Solo para el uso  de parámetros  adicionales en mailchimp
 *          @param array $mailchip_merge_array arreglo con la identificación de la columnas de mailchimp como llaves y los nombres de los input del form que contienen esa informacion.
 *
 *      Solo para el uso de reporte de errores de registro a mailchimp vía email
 *          @const string mail_errors_send e-mail donde se envían el reporte de errores
 *
 */

/*--------------------- datos del correo -------------------------------------*/
$GLOBALS['special_pages_ids'] = isset($GLOBALS['special_pages_ids'] )  ? $GLOBALS['special_pages_ids'] : get_option('special_pages_ids');

$meta_cotacto = get_post_meta($GLOBALS['special_pages_ids']['contacto'], 'social_net', true);
define('mail', isset($meta_cotacto['mail']) ? $meta_cotacto['mail'] : 'hola@elcultivo.mx' ); //  mail de la prestadora de servicios

/*-------------------- es necesario repetir y modificar las siguientes lineas para cada form que contenga la pagina ------------*/
$id_form = 'contact-form_JS'; // cambiar el identificador del form
if (isset($_POST[$id_form])) {
    if (defined('id_form')) { // detiene el código en caso de que ingrese información de dos o mas form diferentes
        $break_form = true;
    } else {

        /*--------------------- identificadores del form -------------------------------------*/
        define('id_form', $id_form);
        define('reg_name', $_POST[$id_form]['Nombre']); // cambiar el identificador del nombre de la persona que envía en caso de no contar con este campo marcarlo como ''
        define('reg_mail', $_POST[$id_form]['E-mail']); // cambiar el identificador del mail de la persona que suscribe

        /*--------------------- contenido del correo -------------------------------------*/
        define('primera_linea', __('Información de contacto', TRANSDOMAIN) ); // cambiar el primera linea del contenido del correo

        /*----------------------uso de mail chimp----------------------------------------*/
        define('mailchip', false); // cambiar a true en caso de uso del mailchimp

        /*-----------------------modificar solo en caso de uso de mailchimp-------------------------*/

        if (mailchip == true) {
            define('mailchip_apikey', 'API key'); // cambiar el api key de mailchimp
            define('mailchip_listid', 'List ID'); // cambiar el list id de mailchimp
            define('mailchip_listurl', 'URL'); // cambiar el url de mailchimp
            define('mailchip_mergevar_on', false); // cambiar a true en caso de que se cuente con campos extra
            if (mailchip_mergevar_on == true) {//permite tener mas input en el form para seleccionar solo los indispensables para mailchimp o cambiar así como depurar los input no enviados
                $GLOBALS["mailchip_merge_array"] = $mailchip_merge_array = array( // cambiar la clave y el valor en función del tag de mailchimp y el nombre del input
                                            'MERGE1' => id_form . '[input1]',
                                            'MERGE2' => id_form . '[input2]',
                                            'MERGE3' => id_form . '[input3]');// puedes crecer este array tantas veces sea necesario
            }
            define('mailchip_errors_send', false); // cambiar a true en caso de que quieras reporte de errores de mailchimp por mail
            if (mailchip_errors_send == true) {
                define('mail_errors_send', 'reporte@contanto.com'); // cambiar correo donde se envían los errores del mailchimp
            }
        }
    }

}
unset($id_form);
/*--------------------- repetir hasta aquí -------------------------*/
?>
