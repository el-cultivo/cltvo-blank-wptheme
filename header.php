<!DOCTYPE html>
<html lang="<?php echo substr(get_bloginfo ( 'language' ), 0, 2);?>">
<head>
	<meta charset="UTF-8">
	<title><?php
		global $page, $paged;

		wp_title( '|', true, 'right' );
		bloginfo( 'name' );

		$site_description = get_bloginfo( 'description', 'display' );

		if ( $site_description && ( is_home() || is_front_page() ) ) echo " | $site_description";
		if ( $paged >= 2 || $page >= 2 ) echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );
	?></title>

	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="author" content="<?php echo THEMEURL;?>humans.txt">

	<?php
	//generar el favicon usando http://www.favicomatic.com/ y agregar los archivos a images/favicon/
	include_once('inc/favicon.php');?>

	<!-- Facebook Metadata /-->
	<meta property="fb:page_id" content="" />
	<meta property="og:image" content="" />
	<meta property="og:description" content=""/>
	<meta property="og:title" content=""/>

	<!-- Google+ Metadata /-->
	<meta itemprop="name" content="">
	<meta itemprop="description" content="">
	<meta itemprop="image" content="">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

	<link href="<?php bloginfo('template_url'); ?>/style.css" rel="stylesheet" type="text/css" />

	<?php wp_head(); ?>
</head>
<body>
	<?php

	/**
	 *CLTVO: poner esto en true sólo en la versiones locales.
	 */

	if( !defined('CLTVO_ISLOCAL') || ( CLTVO_ISLOCAL != true) ){ include_once('inc/analytics.php'); }

	?>

	<!--[if gt IE 8]><div style="z-index: 1000; padding: 5px 0; text-align: center; position: absolute; top: 0; left: 0; width: 100%; background-color: #312822;"><p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; color: white;">Consider <a style="color: #EA7640;
	text-decoration: underline;" href="http://www.google.com/intl/es/chrome/browser/" target="_blank">updating your browser</a> in order to render this site correctly.</p></div><!-->
<!--<![endif]-->

	<!-- Aquí podría abrir el main-wrap -->
