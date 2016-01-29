<?php

//----------------------------funciones para el envió del correo --------------------------------

/**
 * Genera el contenido del correo a partir de un array:
 *
 * Parámetros:
 * @param array $variable Arreglo generado por la estructura de los imput enviados
 * @param string $first_line primera linea o titulo del contenido
 *
 * @return string con formato <strong> key1 .- </strong>  value1 <br> <strong> key2 .- </strong>  value2 <br> ... donde  key y value son las llaves y los valores del array respectivamente
*/

function Post_array_to_mail_html_cont($variable, $first_line){ // genera el texto del correo a partir de la cadena enviada por el form
    if (!empty($variable)) {
        if (is_array($variable)) {
            $salida = '<strong>'.$first_line.'</strong> <br>'; // escribe la primer linea
            foreach ($variable as $key => $value ) { // escribe las key para como encabezados
                $salida .= '<strong>' . $key ;
                if(!is_array($value)){ // Solo en caso de no array
                    $salida .= '.- ';
                }
                $salida .= '</strong> ' . Post_array_to_mail_html_cont($value, '');
            }
        } else { // escribe en contenido de cada input
            $salida = $variable . '. <br>';
        }
    } else { // en caso de que el input se enviara vacío
        $salida = 'Sin información' . '. <br>';
    }
    return $salida;
}

/**
 * Envía un mail de confirmación a la persona que se suscribe y uno de notificación a la prestadora del servicio
 * Opcionalmente se puede registrar la información de la persona solicitante en mailchimp
 *
 * Necesita:
 * @const string 'mail' correo electrónico de la prestadora de servicios
 * @const Boolean 'mailchip' define la si se realiza o no el registro en mailchimp
 * @const Boolean 'mailchip_mergevar_on' define la si el registro en mailchimp contiene o no parámetros adicionales
 * @const string 'id_form' valor form
 * @function merge_vars_gen genera los campos extra en caso del uso del mailchimp a partir de $_post
 * @function mailchimp_reg envía el registro a mailchimp
 *
 * Parametros:
 * @param string $de_quien Nombre de la persona que desea suscribirse
 * @param string $de_quien_mail Correo electrónico de la persona que desea suscribirse
 * @param string $qui_hubo Contenido del correo de notificación al prestador de servicios.
 * @param array  $mailchip_merge_array Puede ser vació.
 * Opcionalmente si se desea registrar en mailchimp con campos adicionales (cualquier campo diferente al correo se considera adicional) los nombres de las columnas en mailchimp serán las llaves y el nombre del input en el form los valores de este array.
 *
 * @return string Solo regresa mensajes de éxito o fracaso en el envió de los correos.
 * Opcionalmente se puede solicitar que los errores de registren mailchimp sean enviados vía correo electrónico a una dirección especificada
*/


function cltvo_manda_mail($de_quien, $de_quien_mail, $qui_hubo, $mailchip_merge_array){ // envía correo de registro y notificación
    $pa_donde = mail;
    $qui_hubo_asunto = primera_linea.": " . $de_quien_mail;

    if (!empty($de_quien)) {
        $qui_hubo_asunto .= " (" . $de_quien . ") ";
    }

    if ($qui_hubo == '')
        $qui_hubo = '- No hubo mensaje escrito -';
    $qui_hubo_msj = $qui_hubo;

    /*---------------------------------envió del mail de registro ----------------------------------------------------------*/
    $from = "FROM: " . $de_quien_mail . "\r\n";
    $cabeza = "MIME-Version: 1.0\r\n";
    $cabeza .= "Content-Type: text/html; charset=UTF-8\r\n";

    $primer_mail = mail($pa_donde, $qui_hubo_asunto, $qui_hubo_msj,  $cabeza.$from, "-f".$de_quien_mail  );

    /*---------------------------------envió del mail de agradecimiento ----------------------------------------------------------*/
    $asunto = primera_linea;
    $mensaje = __("Gracias por tu mensaje. Nos comunicaremos contigo pronto.", TRANSDOMAIN);
    $from2 = 'FROM: ' .$pa_donde. "\r\n";
    $segundo_mail = mail($de_quien_mail, $asunto, $mensaje, $cabeza.$from2, "-f".$pa_donde );



    /*---------------------------------regreso de información vía ajax y activación del mailchimp----------------------------------------------------------*/



    if ($primer_mail && $segundo_mail) {
        if (mailchip == true) { // solo en caso de que quieran registrarse en mailchimp

            $mergevars_val = array(); // inicia si campos extra en para mailchimp
            if (mailchip_mergevar_on == true) { // solo en caso de que quieran registrarse campos extra en mailchimp
                $mailchimp_mergevars = merge_vars_gen($_POST[id_form]); // función para definir el valor de los campos extra

                foreach ($mailchip_merge_array as $key => $value) { // asigna el valor correspondiente a campo extra
                    if (isset($mailchimp_mergevars[$value])) {
                        $mergevars_val[$key] = $mailchimp_mergevars[$value];
                    } else {
                        $mergevars_val[$key] = ""; // en caso de que el input no llegara lo marca como vació
                    }
                }

            }
            mailchimp_reg($mergevars_val, $cabeza); // función para enviar a mailchimp
        }
        return "__okcode__".__('¡Gracias!', TRANSDOMAIN);
    } else {
        return "Error en envío";
    }
}

//----------------------------funciones para el mailchimp --------------------------------


/**
 * Convierte un array multi dimensional en un one dimensional generando como llaves del arreglo la estructura del original del array agregándoles el prefijo el valor de id_form
 *
 * Necesita:
 * @const string 'id_form' valor del prefijo
 * @function merge_vars_string convierte el contenido de un array one o multi dimensional en una cadena
 *
 * Parámetros:
 * @param array $variable array multi dimensional pude se también un one dimensional array
 *
 * @return array Array one dimensional con la siguiente estructura(id_form se refiere al valor de la constante id_form):
 * para el caso one dimensional -> llave 'id_form[array_key]' valor 'value'
 * para el caso multi dimensional -> llave 'id_form[array_key][subarray_key]' valor 'value'
*/

function merge_vars_gen($variable){ // genera los campos extra en caso del uso del mailchimp

    $string = merge_vars_string($variable, id_form); // obtiene un string con todas las llaves y las variables
    $array_simple = explode('/fin/', $string); // obtiene un array con cada llave y su variable

    foreach ($array_simple as $key_array) {
        if (!empty($key_array)) { //Solo no vacias
            $key_y_array = explode('/sep/', $key_array);
            //$salida[]=$key_y_array;
            if (count($key_array) <> 2) { //solo los array que se encuentren bien
                $salida[$key_y_array[0]] = $key_y_array[1];
            }
        }
    }
    return $salida;
}

/**
 * Convierte el contenido de un array one o multi dimensional en una cadena:
 *
 * Parametros:
 * @param array $mailchimp_mergevars Arreglo generado por la estructura de los imput enviados
 * @param string $prehol No puede ser vació. Prefijo agregado a las llaves para la estructura del array (se recomienda usar el nombre del array a ser convertido)
 *
 * @return string con la siguiente estructura:
 * para el caso one dimensional -> id_form[array_key]/sep/value/fin/...
 * para el caso multi dimensional -> id_form[array_key][subarray_key]/sep/value/fin/...
*/

function merge_vars_string($variable, $prehol){ // genera una cadena a partir de la cadena enviada por el form para obtener los los campos extra en caso del uso del mailchimp
    if (!empty($variable)) {
        if (is_array($variable)) {
            $salida = ""; // escribe la primer linea
            foreach ($variable as $key => $value) { // escribe las key para como encabezados
                if (!is_array($value)) { // solo escribe la key en caso de que no sea array
                    $salida .= $prehol . '[' . $key . ']/sep/';
                }
                $salida .= merge_vars_string($value, $prehol . "[" . $key . "]");
            }
        } else { // escribe en contenido de cada input
            $salida = $variable . '/fin/';
        }
    } else { // en caso de que el input se enviara vacío
        $salida = '' . '/fin/';
    }
    return $salida;
}

/**
 * Envia la solicitud de registro a una lista de mailchimp
 *
 * Necesita:
 * @const string 'mailchip_apikey' valor de la llave de la api de mailchimp
 * @const string 'mailchip_listid' valor de la llave de la lista de mailchimp
 * @const string 'mailchip_listurl' dirección de la lista de mailchimp
 * @const string 'reg_mail' dirección de correo de registro del mailchimp
 * @const Boolean 'mailchip_mergevar_on' define la si el registro en mailchimp contiene o no parámetros adicionales
 * @const Boolean 'mailchip_errors_send' define la si se desea el envió de errores del registro en mailchimp
 * @const string 'mail_errors_send' dirección de correo donde se desean recibir los errores de registro de mailchimp
 *
 * Parámetros:
 * @param array $mailchimp_mergevars arreglo que contiene los campos adicional que seran enviados al registro mailchimp cualquier campo diferente al correo se considera adicional) los nombres de las columnas en mailchimp serán las llaves y el nombre del input en el form los valores de este array.
 * @param string $cabeza Puede ser vació. Opcionalmente si se puede enviar el reporte de errores de registro por mailchimp a una dirección. especificada
*/

function mailchimp_reg($mailchimp_mergevars, $cabeza){ // función de registro en mailchimp

    /*----------------------------parámetros del mailchimp----------------------------------*/

    $apiKey = mailchip_apikey; // apikey de mail chimp
    $listId = mailchip_listid; // id de la lista de mail chimp
    $submit_url = mailchip_listurl; // dirección de la lista en mail chimp
    $double_optin = false;
    $send_welcome = false;
    $email_type = 'html';

    $data = array( //array de parametros
        'apikey' => $apiKey,
        'id' => $listId,
        'double_optin' => $double_optin,
        'send_welcome' => $send_welcome,
        'email_type' => $email_type);

    /*-----------------------ingreso de los datos------------------------------------------------*/

    $data['email_address'] = reg_mail; //definición del mail que se inscribe
    if (mailchip_mergevar_on == true) {
        $data['merge_vars'] = $mailchimp_mergevars; // definición del resto de los campos
    }


    /*--------------- codigo de envio a mailchimp----------------------*/
    $payload = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $submit_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode($payload));

    $result = curl_exec($ch);
    curl_close($ch);

    /*---------------detección de errores y envió de los mismos-------------------*/
    if ($result != true) {
        if (mailchip_errors_send == true) { // solo en caso de que quieran recibirse los errores por mail
            $data = json_decode($result);
            if ($data->error) {
                if ($data->code != '214') {
                    $mail_cultivo = mail_errors_send; // mail para el reporte de errores
                    $mailchimp_mensaje =
                        "Hubo un error al tratar de registrar un mail a la lista. (" . $listId . ") \r\nCódigo de error: " .
                        $data->code;
                    mail($mail_cultivo, "Error Mailchimp", $mailchimp_mensaje, $cabeza);
                } else {
                    // en caso de mail ya inscrito
                }
            }
        }
    } else {
        // en caso de correcto
    }

}

?>
