<?php get_header(); ?>
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	<?php $terms = get_the_terms( $post->ID , 'abacategory' ); 
   	foreach ( $terms as $term ) {
        $term_link = get_term_link( $term, 'abacategory' );
        if( is_wp_error( $term_link ) )
        continue;
    /*echo '<a href="' . $term_link . '">' . $term->name . '</a>';*/
    } 
?>
<div class="titlePage twoLines">
	<div class="box">
		
			<h4 class="subTitle">
				<?php echo $term->name; ?>
			</h4>
			<h1><?php the_title(); ?></h1>
	</div>
</div>

<div id="news" class="article">
	<div class="newBox carBox">
		<div class="box">

			<div class="colLeft blockPhone">
			<!-- article -->
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
						<?php
							$thumb_id = get_post_thumbnail_id();
							$thumb_url = wp_get_attachment_image_src($thumb_id,'blog', true);
						?>
						<div class="carImg" style="background: url(<?php echo $thumb_url[0]; ?>"></div>
					<?php endif; ?>
					<?php the_content(); ?>
					<?php if(qtranxf_getLanguage() == 'en'): ?>
					    <a href="#book" class="btn">Enquire</a>
					<?php endif; ?>
					<?php if(qtranxf_getLanguage() == 'es'): ?>
					    <a href="#book" class="btn">Consultar</a>
					<?php endif; ?>
					<?php if(qtranxf_getLanguage() == 'pt'): ?>
					    <a href="#book" class="btn">Consultar</a>
					<?php endif; ?>	


					<br class="clear">

					<?php edit_post_link(); ?>

				</article>
				<!-- /article -->
			
			</div>
			
				<?php get_sidebar(); ?>
			
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<?php endwhile; ?>

<?php else: ?>

	<!-- article -->
	<article>

		<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>

	</article>
	<!-- /article -->

<?php endif; ?>
<?php get_template_part('services'); ?>
<?php get_template_part('book'); ?>
<?php get_footer(); ?>
