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
						<div style="display:flex;gap:48px;align-items:flex-start;flex-wrap:wrap;">
							<?php if ( has_post_thumbnail()) : ?>
							<div style="flex:0 0 380px;max-width:100%;">
								<?php the_post_thumbnail(array(500,400), array('style' => 'width:100%;height:auto;border-radius:8px;')); ?>
							</div>
							<?php endif; ?>
							<div style="flex:1;min-width:260px;">
								<?php the_content(); ?>
							</div>
						</div>

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
