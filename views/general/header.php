<header class="header">
	<div class="grid__container">
		<div class="menu">
			<?php
                wp_nav_menu(array(
                    'menu'           => "main",
                    'theme_location' => 'header_menu',
                    'menu_class'     => 'lista',
            	));
            ?>
		</div>

        <div class="menu-mobile">
            <?php
                wp_nav_menu(array(
                    'menu'           => "main",
                    'theme_location' => 'sidebar_menu',
                    'menu_class'     => 'lista-responsive',
                ));
            ?>
        </div>
	</div>
</header>