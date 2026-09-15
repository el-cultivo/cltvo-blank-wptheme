<header class="header">
	<div class="container">
		<div class="menu">
			<?php
				wp_nav_menu([
					'theme_location' => 'header_menu',
					'menu_class'     => 'lista',
				]);
			?>
		</div>

		<div class="menu-mobile">
			<?php
				wp_nav_menu([
					'theme_location' => 'header_menu',
					'menu_class'     => 'lista-responsive',
				]);
			?>
		</div>
	</div>
</header>