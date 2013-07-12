<!DOCTYPE html>
<html>
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

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="icon" type="image/ico" href="<?php bloginfo('template_url'); ?>/images/favicon.ico">

	<link href="<?php bloginfo('template_url'); ?>/style.css" rel="stylesheet" type="text/css" />

	<?php wp_head(); ?>
</head>
<body>

	<!-- Aquí podría abrir el main-wrap -->