<?php

namespace Illuminate\Contracts;

interface CustomPostType
{
	/**
	 * registra los posttypes
	 */
	static function registerPostype();

	/**
	 *  registra el hook
	 */
	static function registerPostypeHook(array  $args);
}
