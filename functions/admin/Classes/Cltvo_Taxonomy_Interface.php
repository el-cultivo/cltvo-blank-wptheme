<?php

interface Cltvo_Taxonomy_Interface{

	/**
	 * registra las taxonomies
	 */
	static function registerTaxonomy();

	/**
	 * registra el hook
	 */
	static function registerTaxonomyHook(array $args);

	/**
	 * registra los terms inicales
	 */
	static function setInitialTerms();

	/**
	 * trae los terms de esa taxonomia
	 */
	static function getTerms();	

}
