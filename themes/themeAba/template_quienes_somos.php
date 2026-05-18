<?php /* Template Name: Quienes somos */ get_header(); ?>
<div class="titlePage">
	<div class="box">
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div id="news" class="article">

	<div class="newBox">
		<div class="box">
			<main>
				<?php if (have_posts()): while (have_posts()) : the_post(); ?>

					<!-- article -->
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
							<?php the_post_thumbnail(array(500,250), array('class' => 'left blockPhone')); // Declare pixel size you need inside the array ?>
						<?php endif; ?>
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
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="clearfix"></div>
	
</div>
<div id="location">
		<?php get_template_part('map'); ?>
	</div>
<?php get_template_part('book'); ?>
<?php get_footer(); ?>
