<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php include_once('inc/favicon.php'); ?>

	<meta name="author" content="<?php echo THEMEURL;?>humans.txt">

	<?php wp_head(); ?>

	<?php
	/**
	 *CLTVO: poner esto en true sólo en la versiones locales.
	 */

	if( !defined('CLTVO_ISLOCAL') || ( CLTVO_ISLOCAL != true) ){ include_once('inc/analytics.php'); }

	?>
</head>
<body <?php body_class(); ?> >

	<!-- Aquí abre el main-wrap -->
	<div class="main-wrap">

		<!-- N a v -->
		<?php get_template_part('views/general/header'); ?>
