<?php get_header(); ?>

<main>
	<header>
		<?php the_archive_title('<h1>', '</h1>'); ?>
	</header>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article>
				<h2>
					<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
				</h2>

				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p>No se encontraron contenidos.</p>
	<?php endif; ?>
</main>

<?php get_footer(); ?>