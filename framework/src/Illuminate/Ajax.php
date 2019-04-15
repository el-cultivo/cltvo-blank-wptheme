<?php

namespace Illuminate;

use Illuminate\Contracts\Ajax as AjaxContract;
use ReflectionClass;

abstract class Ajax implements AjaxContract
{
	public function handle()
	{
        unset($_POST['action']);
        return $this->store($_POST);
    }

	public function parse($inputs)
	{
		return array_filter(array_map(function($input) {
			return trim($input);
		}, $inputs));
    }

    public function validate($input, $validation)
    {
        foreach($validation as $field => $rules){
            $rules = explode('|', $rules);
            foreach($rules as $rule){
                if(!$this->rule($rule, $field, $input)){
                    $this->returnValidationError($field, $rule);
                }
            }
        }
    }

    public function rule($rule, $field, $input)
    {
        if($rule == 'required'){
            if(array_key_exists($field, $input)){
                if(is_array($input[$field])){
                    return !!count($input[$field]);
                }else {
                    return !!strlen($input[$field]);
                }
            }else {
                return false;
            }
        }
        if($rule == 'array'){
            return is_array($input[$field]);
        }
    }

    public function defaultMessages()
	{
		return [
			'required' => 'Please fill all the fields.'
		];
    }

    public function messages()
    {
        return [];
    }

    public function returnValidationError($name, $rule)
	{
		$messages = array_merge($this->defaultMessages(), $this->messages());

		$message = array_key_exists($name.'.'.$rule, $messages) ? $messages[$name.'.'.$rule] : $messages[$rule];

		return $this->returnError($message);
	}

    public function returnError($message)
	{
		$this->setHeaders();
		header('HTTP/1.1 422 Unprocessable Entity');
		echo json_encode(['message' => $message]);
		die;
	}

	public function success($message)
	{
		$this->setHeaders();
		echo json_encode(['message' => $message]);
		die;
	}

	public function setHeaders()
	{
		header('Content-Type: application/json; charset=UTF-8');
	}

	public function registerAjax()
	{
        $name = $this->classname();

		add_action( 'wp_ajax_nopriv_' . $name, [$this, 'handle']);
		add_action( 'wp_ajax_' . $name, [$this, 'handle']);
	}

    public function classname()
    {
        return toSnakeCase( str_replace('Ajax', '', (new ReflectionClass($this))->getShortName()) );
    }
}
