<?php 

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->specialPages();
        $this->specialCategories();
        $this->specialTags();
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

    public function specialPages()
    {
        $special_pages = $this->app->config['special-pages'];

        $special_pages_ids = get_option('special_pages_ids'); // almacena los ids de las paginas especiales

        if ( !is_array($special_pages_ids) )  { //crea la opccion si aun no esta creada
            add_option('special_pages_ids');
            $special_pages_ids = [];
        }

        foreach ($special_pages as $slug => $args) { // genera y revisa las paginas

            $slug = trim($slug);
            
            $post_parent_id = empty(trim($args[1])) ? 0 : $special_pages_ids[ trim($args[1]) ];

            // Si el slug existe
            if (!isset($special_pages_ids[$slug])) {
                
                $page_by_slug = getSpecialPageBySlug($slug, $post_parent_id);

                if ($page_by_slug)  {
                    $special_pages_ids[$slug] = $page_by_slug->ID;
                }

            }

            // Si el slug exixste pero en la papelera
            if (!isset($special_pages_ids[$slug])) { 
                $page_trashed = getSpecialPageBySlugTrashed($slug,$post_parent_id);

                if ($page_trashed )  {
                    $special_pages_ids[$slug] = $page_trashed->ID;
                }

            }

            $CreaPost = true;

            // Si ya se ha creado
            if(isset($special_pages_ids[$slug]) ){

                // Por si esta borrado
                $trased = wp_untrash_post($special_pages_ids[$slug]);

                $special_pages_ids[$slug] = intval($special_pages_ids[$slug]);

                // Traemos el post
                $pagina = get_post( $special_pages_ids[$slug] );

                // Si no borraron permanentemente la pagina
                if ( $pagina ) {
                    
                    $CreaPost = false;
                    $actualizar = false;

                    $pagina_by_slug = getSpecialPageBySlug($slug,$post_parent_id );

                    if($pagina_by_slug){ // verifica que el slug no lo tenga otra pagina
                        $pagina = ($pagina_by_slug->ID != $pagina->ID ) ? $pagina_by_slug : $pagina ;
                        $special_pages_ids[$slug] = $pagina->ID;
                    }else{ // si el slug no pertenece a ninguna pagina lo manda a crear
                        $CreaPost = true;
                    }

                    if (!$CreaPost) { // si no tiene que crearse
                        $pagina_args = array(
                            'ID'		   =>   $pagina->ID,
                            'post_title'   =>   $pagina->post_title,
                            'post_content' =>   $pagina->post_content,
                        );
                        // si no esta publicada
                        if ( $pagina->post_status != 'publish' ){ // evita que las paginas se coloquen en borador o se envien a la papelera.
                            $pagina_args['post_status'] = 'publish';
                            $actualizar = true;
                        }

                        // si modificaron el post parent
                        if ( $pagina->post_parent != $post_parent_id ){ // evita que las paginas se cambien de padre
                            $pagina_args['post_parent'] = $post_parent_id;
                            $actualizar = true;
                        }

                        // si modificaron el slug
                        if ( $pagina->post_name != $slug ){ // evita que las paginas se cambien de slug
                            $pagina_args['post_name'] = $slug;
                            $actualizar = true;
                        }

                        if( $actualizar ){
                            wp_update_post( $pagina_args );
                        }
                    }
                }
            }

            if( $CreaPost ){ // si no existe la pagina guarda

                $page = [
                    'post_author'   => 1,
                    'post_status'   => 'publish',
                    'post_name'	    => $slug,
                    'post_title'    => $args[0],
                    'post_type'	    => 'page',
                    'post_parent'   => $post_parent_id
                ];

                $special_pages_ids[$slug] = wp_insert_post( $page, true );
            }

        }

        foreach ($special_pages_ids as $slug => $id) {
            if (!isset($special_pages[$slug])) {
                unset($special_pages_ids[$slug]);
            }
        }

        update_option('special_pages_ids', $special_pages_ids);

        $GLOBALS['special_pages_ids'] = $special_pages_ids;
    }

    public function specialCategories()
    {
        $special_categories = $this->app->config['special-categories'];

        foreach ($special_categories as $name) {
            if( ! term_exists($name, 'category') ){
                wp_insert_term($name, 'category');
            }
        }
    }

    public function specialTags()
    {
        $special_tags = $this->app->config['special-tags'];

        foreach ($special_tags as $name) {
            if( ! term_exists($name, 'post_tag') ){
                wp_insert_term($name, 'post_tag');
            }
        }
    }
}
