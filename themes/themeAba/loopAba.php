<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	<?php 
		$postcount++; 
		$new_class = ( ($postcount % 2) == 0 ) ? "style02" : "style01";
		$classes = array(
			'newBox',
			$new_class,
		);
	?> 
	<article id="post-<?php the_ID(); ?>" <?php post_class($classes) ?>>
		<div class="box">
			<?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php the_post_thumbnail('blog', array( 'class' => 'left blockPhone' )); // Declare pixel size you need inside the array ?>
				</a>
			<?php endif; ?>
			<h3><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
			<span class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></span>
			<?php html5wp_excerpt('html5wp_index'); // Build your custom callback length in functions.php ?>
			<a href="#" class="btn">Consultar</a>
			<a href="#" class="btn">Realizar reserva</a>
			<?php edit_post_link(); ?>
			<div class="clearfix"></div>
		</div>
	</article>

<?php endwhile; ?>

<?php else: ?>

	<!-- article -->
	<article>
		<h2><?php _e( 'Disculpe, El contenido que esta buscando no esta aquí.', 'html5blank' ); ?></h2>
	</article>
	<!-- /article -->

<?php endif; ?>
