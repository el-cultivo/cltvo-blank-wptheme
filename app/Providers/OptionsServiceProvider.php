<?php

namespace App\Providers;

class OptionsServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $config = require __DIR__ . '/../../config/options_pages.php';

        if( function_exists('acf_add_options_page') ) {

            foreach($config as $post_type => $groups){

                acf_add_options_page(array(

                    /* (string) The title displayed on the options page. Required. */
                    'page_title' => 'Opciones',

                    /* (string) The title displayed in the wp-admin sidebar. Defaults to page_title */
                    'menu_title' => 'Opciones',

                    /* (string) The slug of another WP admin page. if set, this will become a child page. */
                    'parent_slug' => 'edit.php?post_type=' . $post_type,

                    /* (string) The slug name to refer to this menu by (should be unique for this menu).
                    Defaults to a url friendly version of menu_slug */
                    'menu_slug' => $post_type . '-options',

                    /* (int|string) The '$post_id' to save/load data to/from. Can be set to a numeric post ID (123), or a string ('user_2').
                    Defaults to 'options'. Added in v5.2.7 */
                    'post_id' => $post_type . '-options',

                ));

                $this->add_option_fields($post_type, $groups);

            }

        }
    }

    protected function add_option_fields($post_type, $groups)
    {
        if( function_exists('acf_add_local_field_group') ){

            acf_add_local_field_group(array(
                'key' => $groups[0]['key'],
                'title' => 'Opciones',
                'fields' => array(
                    array(
                        'key' => $groups[0]['fields'][0]['key'],
                        'label' => 'Titulo',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => $groups[0]['fields'][1]['key'],
                        'label' => 'Descripción',
                        'name' => 'description',
                        'type' => 'wysiwyg',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => '',
                        'new_lines' => '',
                    ),
                    array(
                        'key' => $groups[0]['fields'][2]['key'],
                        'label' => 'Subespacios',
                        'name' => 'subspaces',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'block',
                        'button_label' => 'Agregar',
                        'sub_fields' => array(
                            array(
                                'key' => $groups[0]['fields'][2]['subfields'][0],
                                'label' => 'Titulo',
                                'name' => 'title',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => $groups[0]['fields'][2]['subfields'][1],
                                'label' => 'Descripción',
                                'name' => 'description',
                                'type' => 'wysiwyg',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => '',
                                'new_lines' => '',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => $post_type . '-options',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));

            acf_add_local_field_group(array(
                'key' => $groups[1]['key'],
                'title' => 'Imágen destacada',
                'fields' => array(
                    array(
                        'key' => $groups[1]['fields'][0]['key'],
                        'label' => '',
                        'name' => 'thumbnail',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => $post_type . '-options',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));

        }
    }
}
