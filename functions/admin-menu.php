<?php

/**
 * En este archivo se incluyen las personalizaciones al menú principal 
 *
 */

/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

// add_action( 'admin_menu', 'cltvo_nombre_entradas_menu' ); //	Cambia el nombre "Endradas" en Menú del admin


/** ==============================================================================================================
 *                                       FUNCIONES admin personalizado
 *  ==============================================================================================================
 */

//	Nombre "Endradas" en Menú

function cltvo_nombre_entradas_menu() {
    global $menu;
    global $submenu;
    $menu[5][0] = '-Nuevo nombre-';
    $submenu['edit.php'][5][0] = '-Nuevo nombre-';
    $submenu['edit.php'][10][0] = '-Nuevo nombre para "Nueva Entrada"-';
    echo '';
}





?>