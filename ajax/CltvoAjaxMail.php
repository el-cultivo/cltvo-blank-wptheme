<?php



class CltvoAjaxMail
{

/*--------------------- informacion basica -------------------------------------*/
    protected $sender_mail = 'hola@elcultivo.mx' ; // mail del provedor
    protected $primera_linea; // asunto del correo

/*--------------------- varibles del funcionamiento -------------------------------------*/
    protected $break_form = true; // variable de breack de problemas

/*--------------------- identificadores del form  de contacto  -------------------------------------*/

    protected $id_form; // key del array donde se encuentran la informacion xe contacto
    protected $insputs_array; // array donde se encuentran la informacion xe contacto

/*--------------------- datos del contacto  -------------------------------------*/
    protected $suscriber_name; // nombre del que contacta
    protected $suscriber_mail; // mail del que contacta

/*--------------------- datos del mailchimp  -------------------------------------*/
// mailchip vars
    protected $mailchip_listurl;
    protected $mailchip_info = array();

// reporte de errores de mailchimp
    protected $mail_errors_send; // cambiar correo donde se envían los errores del mailchimp

//---------------------------- constructor --------------------------------


    /**
     * @param string $sender_mail    email del la persona que envia
     * @param string $primera_linea primera linea del mail
     */
    function __construct( $sender_mail , $primera_linea = 'Información de contacto' ){

        if ( filter_var( $sender_mail, FILTER_VALIDATE_EMAIL) ) { // si el mail de envio es un mail
            $this->senderMail = $sender_mail;
        }

        $this->primera_linea = $primera_linea;

    }


//---------------------------- informacion del suscriptor --------------------------------

    /**
     *  inicializa los valores del usuario
     * @param string $suscriberMail    email del la persona que se suscribe
     * @param string $suscriberName     nombre  del la persona que se suscribe
     * @param sarray $inputsArray       array con las variables que formaran parte del correo
     */
    public function setSuscriber($suscriberMail , $suscriberName = "", array $inputsArray = [] )
    {

        if ( filter_var( $suscriberMail, FILTER_VALIDATE_EMAIL) ) { // si el mail de envio es un mail
            $this->suscriber_mail = $suscriberMail;
        }

        $this->suscriber_name = $suscriberName;

        if ( is_array( $inputsArray )  ) { // si se agrgega un arrglo
            $this->insputs_array = $inputsArray;

            $this->break_form = false;
        }

    }

    /**
     * inicializa los valores del usuario por medio de un post con la esctructura de un subarray
     * @param string $idForm    idenfificador del form y llave del subarray donde se almasenan las variables
     * @param string $mailKey   llave donde se almacena el valor del mail por defecto E-mail
     * @param string $nameKey   llave donde se almacena el valor del Nombre por defecto Nombre, este cambo puede llehar vacio
     */
    public function setPostForm( $idForm , $mailKey = "E-mail", $nameKey = "Nombre" )
    {
        if ( !empty( $idForm )  &&  is_string( $idForm ) && isset( $_POST[$idForm] )  && is_array( $_POST[$idForm] )  ) {

            $this->id_form = $idForm;

            $this->setSuscriber(
                isset( $_POST[ $this->id_form ][$mailKey] ) ? $_POST[$this->id_form ][$mailKey] : "",
                !empty($nameKey) && isset( $_POST[ $this->id_form ][$nameKey] ) ?  $_POST[ $this->id_form ][$nameKey] : "",
                $_POST[$idForm]
            );

        }

    }

//----------------------------funciones para el envió del correo --------------------------------

    /**
     * Envía los mails con la informacion del usuario como content
     *
     * Necesita:
     * @function CltvoMailsSender envía los correos
     *
     * @return string Solo regresa mensajes de éxito o fracaso en el envió de los correos.
    */
    public function CltvoSuscribe()
    {
        if (  $this->break_form ) { // en esta función se envía la información  solo en caso de que la información llegue correctamente
            return __("Error en envío.", TRANSDOMAIN);
        }

        $mailContentForProvider = static::convertArrayToMailHtmlContent($this->insputs_array, $this->primera_linea . '<br>'); // nombre del form para crear la cadena
        return $this->CltvoMailsSender( nl2br( $mailContentForProvider ) ); // requiere conocer los input de nombre y mail
    }

    /**
     * Envía un mail de confirmación a la persona que se suscribe y uno de notificación a la prestadora del servicio
     * Opcionalmente se puede registrar la información de la persona solicitante en mailchimp
     *
     * Necesita:
     * @function mailChimpRegister envía el registro a mailchimp
     *
     * Parametros:
     * @param string $mailContentForProvider Contenido del correo de notificación al prestador de servicios.
     * Opcionalmente si se desea registrar en mailchimp con campos adicionales (cualquier campo diferente al correo se considera adicional) los nombres de las columnas en mailchimp serán las llaves y el nombre del input en el form los valores de este array.
     *
     * @return string Solo regresa mensajes de éxito o fracaso en el envió de los correos.
     * Opcionalmente se puede solicitar que los errores de registren mailchimp sean enviados vía correo electrónico a una dirección especificada
    */

    public function CltvoMailsSender( $mailContentForProvider ){ // envía correo de registro y notificación

        if ( !$this->suscriber_mail ) { // si no hay mail de suscriptor
            return __( "Correo incorrecto" , TRANSDOMAIN);
        }

        $subjectMailToProvider = $this->primera_linea.": " . $this->suscriber_mail;

        if (!empty($this->suscriber_name)) {
            $subjectMailToProvider .= " (" . $this->suscriber_name . ") ";
        }

        $mailContentForProvider = empty($mailContentForProvider) ?  __('- No hubo mensaje escrito -', TRANSDOMAIN) :  $mailContentForProvider;

        $mailsHead = "MIME-Version: 1.0\r\n";
        $mailsHead .= "Content-Type: text/html; charset=UTF-8\r\n";

    /*---------------------------------envió del mail de registro ----------------------------------------------------------*/
        $headFromForProvider = "FROM: " . $this->suscriber_mail . "\r\n";

        $mailForProvider = mail(
            $this->senderMail,
            $subjectMailToProvider,
            $mailContentForProvider,
            $mailsHead.$headFromForProvider,
            "-f".$this->suscriber_mail
        );

    /*---------------------------------envió del mail de agradecimiento ----------------------------------------------------------*/

        $mailContentForSubscriber = __("Gracias por tu mensaje. Nos comunicaremos contigo pronto.", TRANSDOMAIN);

        $headFromForSuscriber = 'FROM: ' .$this->senderMail. "\r\n";
        $mailForSuscriber = mail(
            $this->suscriber_mail,
            $this->primera_linea,
            $mailContentForSubscriber,
            $mailsHead.$headFromForSuscriber,
            "-f".$this->senderMail
        );


    /*---------------------------------regreso de información vía ajax y activación del mailchimp----------------------------------------------------------*/

        if ( !$mailForProvider && !$mailForSuscriber ) { // Si no se envian los mails
            return __( "Error en el envío" , TRANSDOMAIN);
        }

        $this->mailChimpRegister($mailsHead); // función para resgitrar en mailchimp

        return "__okcode__".__('¡Gracias!', TRANSDOMAIN);

    }


//---------------------------- funciones para contenido del correo --------------------------------


    /**
     * Genera el contenido del correo a partir de un array:
     *
     * Parámetros:
     * @param array|string $variable Arreglo generado por la estructura de los imput enviados
     * @param string $first_line primera linea o titulo del contenido
     *
     * @return string con formato <strong> key1 .- </strong>  value1 <br> <strong> key2 .- </strong>  value2 <br> ... donde  key y value son las llaves y los valores del array respectivamente
    */

    public static function convertArrayToMailHtmlContent($variable, $first_line){ // genera el texto del correo a partir de la cadena enviada por el form
        if (!empty($variable)) {
            if (is_array($variable)) {
                $salida = '<strong>'.$first_line.'</strong> <br>'; // escribe la primer linea
                foreach ($variable as $key => $value ) { // escribe las key para como encabezados
                    $salida .= '<strong>' . $key ;
                    if(!is_array($value)){ // Solo en caso de no array
                        $salida .= '.- ';
                    }
                    $salida .= '</strong> ' . static::convertArrayToMailHtmlContent($value, '');
                }
            } else { // escribe en contenido de cada input
                $salida = $variable . '. <br>';
            }
        } else { // en caso de que el input se enviara vacío
            $salida = 'Sin información' . '. <br>';
        }
        return $salida;
    }


//----------------------------funciones para el mailchimp --------------------------------

    /**
     * Define los valores de las varibles del mailchimp
     * @param string  $mailchip_listurl    url de la lista de mailchimp
     * @param string  $mailchip_apikey     api key de mailchimp
     * @param string  $mailchip_listid     id de la lista  de mailchimp
     * @param array   $mailchimp_mergevars arreglo con los campos extra de mailchimp  por defecto vacio
     * @param string  $mail_errors_send  email al que se le envian los errores
     * @param boolean $double_optin       ???  por defecto false
     * @param boolean $send_welcome       ???  por defecto false
     * @param string  $email_type         tipo de email   por defecto html
     */
    public function setMailChimp(
        $mailchip_listurl,
        $mailchip_apikey,
        $mailchip_listid,
        array $mailchimp_mergevars = array(),
        $mail_errors_send = "",
        $double_optin =  false,
        $send_welcome =  false,
        $email_type =  'html'
    )
    {
        $this->mailchip_info = array(
            'apikey' => $mailchip_apikey, // cambiar el api key de mailchimp
            'id' => $mailchip_listid, // cambiar el list id de mailchimp
            'double_optin' => $double_optin,
            'send_welcome' => $send_welcome,
            'email_type' => $email_type,
            'email_address' => $this->suscriber_mail
        );

        if ( !empty($mailchimp_mergevars) ) {
            $this->mailchip_info['merge_vars'] = $mailchimp_mergevars; // definición del resto de los campos
        }


        if ( filter_var( $mailchip_listurl, FILTER_VALIDATE_URL) ) { // si la url es una url
            $this->mailchip_listurl = $mailchip_listurl;
        }

        if ( filter_var( $mail_errors_send, FILTER_VALIDATE_EMAIL) ) { // si el mail de envio es un mail
            $this->mail_errors_send = $mail_errors_send;
        }

    }



    // /**
    //  * Convierte un array multi dimensional en un one dimensional generando como llaves del arreglo la estructura del original del array agregándoles el prefijo el valor de id_form
    //  *
    //  * Necesita:
    //  * @const string 'id_form' valor del prefijo
    //  * @function mailChimpMergeVarsToString convierte el contenido de un array one o multi dimensional en una cadena
    //  *
    //  * Parámetros:
    //  * @param array $variable array multi dimensional pude se también un one dimensional array
    //  *
    //  * @return array Array one dimensional con la siguiente estructura(id_form se refiere al valor de la constante id_form):
    //  * para el caso one dimensional -> llave 'id_form[array_key]' valor 'value'
    //  * para el caso multi dimensional -> llave 'id_form[array_key][subarray_key]' valor 'value'
    // */
    //
    // private function mailChimpMergeVarsGenerator($variable, $preholder ){ // genera los campos extra en caso del uso del mailchimp
    //
    //     $string = $this->mailChimpMergeVarsToString($variable, $preholder ); // obtiene un string con todas las llaves y las variables
    //     $array_simple = explode('/fin/', $string); // obtiene un array con cada llave y su variable
    //
    //     foreach ($array_simple as $key_array) {
    //         if (!empty($key_array)) { //Solo no vacias
    //             $key_y_array = explode('/sep/', $key_array);
    //             //$salida[]=$key_y_array;
    //             if (count($key_array) <> 2) { //solo los array que se encuentren bien
    //                 $salida[$key_y_array[0]] = $key_y_array[1];
    //             }
    //         }
    //     }
    //     return $salida;
    // }
    //
    //
    // /**
    //  * Convierte el contenido de un array one o multi dimensional en una cadena:
    //  *
    //  * Parametros:
    //  * @param array|string $variable Arreglo generado por la estructura de los imput enviados
    //  * @param string $prehol No puede ser vació. Prefijo agregado a las llaves para la estructura del array (se recomienda usar el nombre del array a ser convertido)
    //  *
    //  * @return string con la siguiente estructura:
    //  * para el caso one dimensional -> id_form[array_key]/sep/value/fin/...
    //  * para el caso multi dimensional -> id_form[array_key][subarray_key]/sep/value/fin/...
    // */
    //
    // private function mailChimpMergeVarsToString( $variable, $prehol){ // genera una cadena a partir de la cadena enviada por el form para obtener los los campos extra en caso del uso del mailchimp
    //     if (!empty($variable)) {
    //         if (is_array($variable)) {
    //             $salida = ""; // escribe la primer linea
    //             foreach ($variable as $key => $value) { // escribe las key para como encabezados
    //                 if (!is_array($value)) { // solo escribe la key en caso de que no sea array
    //                     $salida .= $prehol . '[' . $key . ']/sep/';
    //                 }
    //                 $salida .= $this->mailChimpMergeVarsToString($value, $prehol . "[" . $key . "]");
    //             }
    //         } else { // escribe en contenido de cada input
    //             $salida = $variable . '/fin/';
    //         }
    //     } else { // en caso de que el input se enviara vacío
    //         $salida = '' . '/fin/';
    //     }
    //     return $salida;
    // }


    /**
     * Envia la solicitud de registro a una lista de mailchimp
     * Parámetros:
     * @param string $mailsHead Puede ser vació. Opcionalmente si se puede enviar el reporte de errores de registro por mailchimp a una dirección. especificada
    */

    private function mailChimpRegister($mailsHead){ // función de registro en mailchimp

        if (!$this->mailchip_listurl) { // solo en caso de que quieran registrarse en mailchimp
            return false;
        }

    /*--------------- codigo de envio a mailchimp----------------------*/
        $payload = json_encode($this->mailchip_info);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->mailchip_listurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode($payload));

        $result = curl_exec($ch);
        curl_close($ch);
        var_dump($result);

    /*---------------detección de errores y envió de los mismos-------------------*/
        if ($result != true &&   $this->mail_errors_send ) {  // solo en caso de que quieran recibirse los errores por mail


            $data = json_decode($result);
            if ($data->error && $data->code != '214' ) { // si exixte el error  y es diferente a que ya esta registrado
                mail(
                    $this->mail_errors_send,
                    "Error Mailchimp",
                    "Hubo un error al tratar de registrar un mail a la lista. (" .$this->mailchip_info['id'] . ") \r\nCódigo de error: " .$data->code,
                    $mailsHead
                );

            } // si exixte el error  y es diferente a que ya esta registrado

        } // si errores en mailchimp // si se quieren mandar errores de Mailchimp

    }



}
