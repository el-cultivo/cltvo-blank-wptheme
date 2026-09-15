<?php 

/* Template name: Página simple */

get_header();

if (have_posts()) : while (have_posts()) : the_post(); ?>

    <main>
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
    </main>

<?php endwhile; endif;

get_footer();