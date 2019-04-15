<?php

get_header();

?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php

// Tema destacado.
global $term;
//$term = get_term(get_field('theme'));

?>

<div class="home">
    Home
</div>

<?php endwhile; endif; ?>

<?php

get_footer();
