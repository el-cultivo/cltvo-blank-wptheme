<?php 

namespace App\Providers;

class ActionsServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Activa la traduccion del sitio por archivos .mo
        add_action('after_setup_theme', [$this, 'afterSetupTheme']);
        add_action('admin_init', [$this, 'adminInit']);
        add_action('admin_menu', [$this, 'adminMenu'] );
        add_action('init', [$this, 'init']);
        add_action('tgmpa_register', [$this, 'registerRequiredPlugins']);

    }

    public function init()
    {
        update_option('page_on_front', specialPage('splash'));
        update_option('show_on_front', 'page');

        // Eliminamos las páginas de los reultados de búsqueda.
        global $wp_post_types;
        $wp_post_types['page']->exclude_from_search = true;
    }

    public function afterSetupTheme()
    {        
        load_theme_textdomain( TRANSDOMAIN, get_template_directory() . '/languages' );
    }

    public function adminInit()
    {
        if (isSpecialPage('contacto')) {
            remove_post_type_support( 'page', 'editor' );
        }

        //Verificamos que el plugin de ACF exista cuando se inicie el admin
        if( self::isAcfActive() )
        {
            self::syncAcfFields();
        }

    }

    public function adminMenu()
    {
        // global $menu;
        // global $submenu;
        
        // $menu[5][0] = 'Proyectos';
        // $submenu['edit.php'][5][0] = 'Proyectos';
        // $submenu['edit.php'][10][0] = 'Nuevo proyecto';
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /*
        Función que muestra un mensaje con los plugins necesarios y sugeridos para el tema
    */

    public function registerRequiredPlugins(){
        
        //Llama al arreglo de plugins en el archivo
        $plugins = require_once __DIR__ . '/../../config/required_plugins.php';
        
        //Llama al arreglo de configuraciones del archivo
        $config = require_once __DIR__ . '/../../config/conf_plugins.php';

        tgmpa( $plugins, $config );
    }


    /*
        Función que verifica que el plugin de acf pro o acf normales, existan y están activados
    */

    public function isAcfActive()
    {
        $active = (is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) || 
            is_plugin_active( 'advanced-custom-fields/acf.php' ) )
            ? true 
            : false;

        return $active;
    }

    /**
     *  Función que actualiza automáticamente los acf dentro de la carpeta acf-json

        Se obtuvo del siguiente link, https://gist.github.com/jessepearson/a537b2f78556cd705947
        Analizando el código del plugin, una función similar se puede encontrar en /plugins/advanced-custom-fields-pro/includes/admin

        Posiblemente el plugin en un futuro, incorpore la sincronización automática
        Falta analizar la situación cuando eliminan los campos
     */

    public function syncAcfFields()
    {
        //Obtiene todos los grupos de campos
        $groups = acf_get_field_groups();
        $sync   = array();

        // Si no hay grupos de campos
        if( empty( $groups ) )
            return;

        // Buscamos los grupos de campos que aún no han sido importados
        //Y los asignamos al arreglo sync
        foreach( $groups as $group ) {
            
            // vars
            $local      = acf_maybe_get( $group, 'local', false );
            $modified   = acf_maybe_get( $group, 'modified', 0 );
            $private    = acf_maybe_get( $group, 'private', false );

            // ignore DB / PHP / private field groups
            if( $local !== 'json' || $private ) {
                
                // do nothing
                
            } elseif( ! $group[ 'ID' ] ) {
                
                //Si aún no está en la base de datos, lo asignamos al arreglo de sync
                $sync[ $group[ 'key' ] ] = $group;
                
            } elseif( $modified && $modified > get_post_modified_time( 'U', true, $group[ 'ID' ], true ) ) {
                
                //Si es digerente la fecha de modificacón, lo asignamos al arreglo de sync
                $sync[ $group[ 'key' ] ]  = $group;
            }
        }

        //Si hay algo que sincronizar
        if( ! empty( $sync ) ) {
            
            // Inicializamos el arreglo
            $new_ids = array();
            
            //Deshabilitamos los filtros
            acf_disable_filters();
            acf_enable_filter('local');

            //Deshabilitamos json para prevenir que un nuevo archivo sea creado
            acf_update_setting('json', false);

            //Recorremos sync
            foreach( $sync as $group_key => $group ) {
                
                //Obtenemos el grupo de campos
                $field_group = $sync[ $group_key ];
                
                // append fields
                if( acf_have_local_fields( $group_key ) ) {

                    $field_group[ 'fields' ] = acf_get_fields( $group_key );
                    
                }

                // insertamos los campos
                acf_import_field_group( $field_group );
            }
        }else{
            //Si está vacío sync, retornamos
            return;
        }
    }
}