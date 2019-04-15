<?php

get_header();

if (have_posts()) : while (have_posts()) : the_post(); ?>

	<!-- P a g e -->
	<div class="page-template">
	    <div class="grid__row">
	        <div class="grid__container--fluid">
	            <div class="grid__col-1-1">
	                <div class="page-template__container">
						<div class="page-template__content">
							<div class="titulo"><?php the_title(); ?></div>
	 			   			<div class="contenido"><?php the_content(); ?></div>
						</div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

<?php endwhile; endif;

get_footer();
