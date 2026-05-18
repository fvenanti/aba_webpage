<?php
/*
 * Template Name: Full Width
 */
get_header(); ?>
<div class="titlePage">
	<div class="box">
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div id="news" class="article">
	<div class="newBox">
		<div class="box">
			<main style="width:100%;max-width:100%;float:none;">
				<?php if (have_posts()): while (have_posts()) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php the_content(); ?>
					</article>
				<?php endwhile; endif; ?>
			</main>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
