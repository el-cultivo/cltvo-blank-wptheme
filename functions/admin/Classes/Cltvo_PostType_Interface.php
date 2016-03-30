<?php

interface Cltvo_PostType_Interface{

	/**
	 * registra los posttypes
	 */
	static function registerPostype();


	/**
	 *  registra el hook
	 */
	static function registerPostypeHook(array  $args);

}
