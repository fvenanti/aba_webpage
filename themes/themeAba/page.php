<?php get_header(); ?>
<div class="titlePage">
	<div class="box">
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div id="news" class="article">

	<div class="newBox">
		<div class="box">
			<main class="colLeft">
				<?php if (have_posts()): while (have_posts()) : the_post(); ?>

					<!-- article -->
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<?php the_content(); ?>

						<br class="clear">

						<?php edit_post_link(); ?>

					</article>
					<!-- /article -->

				<?php endwhile; ?>

				<?php else: ?>

					<!-- article -->
					<article>

						<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>

					</article>
					<!-- /article -->

				<?php endif; ?>
			</main>
			<?php get_sidebar(); ?>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<?php get_template_part('book'); ?>
<?php get_footer(); ?>
