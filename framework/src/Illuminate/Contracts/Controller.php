<?php

namespace Illuminate\Contracts;

interface Controller
{
    public function handle();

    public function store($input);
    
	public function validate($input, $validation);

    public function rule($rule, $field, $input);

    public function error();
    
    public function success($link);

    public function registerController();
}
