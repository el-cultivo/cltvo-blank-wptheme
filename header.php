<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11">

	<title><?php wp_title(); ?></title>

	<?php include_once('inc/favicon.php'); ?>

	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="author" content="<?php echo THEMEURL;?>humans.txt">

	<!-- Font awesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?> >

	<?php

    use App\Contacto;

    global $contact;

    $contact = new Contacto;

	/**
	 *CLTVO: poner esto en true sólo en la versiones locales.
	 */

	if( !defined('CLTVO_ISLOCAL') || ( CLTVO_ISLOCAL != true) ){ include_once('inc/analytics.php'); }

	?>

	<!--[if gt IE 8]><div style="z-index: 1000; padding: 5px 0; text-align: center; position: absolute; top: 0; left: 0; width: 100%; background-color: #312822;"><p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; color: white;">Consider <a style="color: #EA7640;
	text-decoration: underline;" href="http://www.google.com/intl/es/chrome/browser/" target="_blank">updating your browser</a> in order to render this site correctly.</p></div><!-->
	<!--<![endif]-->

	<!-- Aquí abre el main-wrap -->
	<div class="main-wrap">

		<!-- N a v -->
		<?php get_template_part('views/general/header'); ?>
