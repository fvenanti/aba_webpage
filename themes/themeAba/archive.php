<?php get_header(); ?>
<?php $terms = get_the_terms( $post->ID , 'abacategory' ); 
    foreach ( $terms as $term ) {
        $term_link = get_term_link( $term, 'abacategory' );
        if( is_wp_error( $term_link ) )
        continue;
    /*echo '<a href="' . $term_link . '">' . $term->name . '</a>';*/
    } 
?>
	
			
				<?php if($term->name){ ?>
				<div class="titlePage twoLines">
					<div class="box">
						<?php $post_type = get_post_type_object( get_post_type($post) ); ?>
						<?php echo "<h4 class='subTitle'>".$post_type->label."</h4>"; ?>
						<?php echo "<h1>".$term->name."</h1>"; ?>
					</div>
				</div>
					
				<?php } else { ?>
				<div class="titlePage">
					<div class="box">
						<?php $post_type = get_post_type_object( get_post_type($post) ); ?>
						<?php echo "<h1>".$post_type->label."</h1>"; ?>
					</div>
				</div>
				<?php } ?>
			
		
	<div id="news" class="blog">

		<?php 
			if (have_posts()): while (have_posts()) : the_post(); 
			$count++;
      		$even_odd_class = ( ($count % 2) == 0 ) ? "style02" : "style01"; 
		?>

			<!-- article -->

				<div class="newBox <?php echo $even_odd_class; ?>">
					<div class="box">
						<?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
							<?php the_post_thumbnail(array(782,450), array('class' => 'left blockPhone')); // Declare pixel size you need inside the array ?>
						<?php endif; ?>
						<span class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></span>
						<h3>
							<?php the_title(); ?>
						</h3>
						
						<?php the_content(); // Dynamic Content ?>
						<?php if(qtranxf_getLanguage()=='es'){ ?>
	                        <a href="#book" class="btn">Consultar</a>
	                    <?php } else if(qtranxf_getLanguage()=='en'){ ?>
	                        <a href="#book" class="btn">Enquire</a>
	                     <?php } else { ?>
	                        <a href="#book" class="btn">Consultar</a>
	                    <?php } ?>
						<div class="clearfix"></div>
						<?php edit_post_link(); ?>
					</div>
				</div>
				
			<!-- /article -->

		<?php endwhile; ?>

		<?php else: ?>

			<!-- article -->
	
				<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
		
			<!-- /article -->

		<?php endif; ?>

		<?php get_template_part('pagination'); ?>

		<div class="clearfix"></div>
	</div>

<?php get_template_part('book'); ?>

<?php get_footer(); ?>
