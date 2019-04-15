<?php

namespace Illuminate;

use Illuminate\Contracts\Controller as ControllerContract;
use ReflectionClass;

abstract class Controller implements ControllerContract
{
    public function handle()
    {
        unset($_POST['action']);

        return $this->store($_POST);
    }

    public function validate($input, $validation)
    {
        foreach($validation as $field => $rules){

            $rules = explode('|', $rules);

            foreach($rules as $rule){

                if(!$this->rule($rule, $field, $input)){

                    $this->error();

                }

            }

        }
    }

    public function rule($rule, $field, $input)
    {
        if($rule == 'required'){
            return array_key_exists($field, $input) && !empty($input[$field]);
        }
    }

    public function error()
    {
        if ( wp_get_referer() ) {
            wp_safe_redirect( wp_get_referer() );
        } else {
            wp_safe_redirect( get_home_url() );
        }
    }

    public function success($link)
    {
        wp_safe_redirect($link);
    }

    public function registerController()
    {
        $name = $this->classname();

        add_action( 'admin_post_nopriv_' .  $name, [ $this , 'handle'] );
        add_action( 'admin_post_' . $name, [ $this , 'handle'] );
    }

    public function classname()
    {
        return toSnakeCase( str_replace('Controller', '', (new ReflectionClass($this))->getShortName()) );
    }
}
