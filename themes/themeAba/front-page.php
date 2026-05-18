<?php get_header(); ?>

	<div id="highligth">
		<div class="box">
			<?php if(qtranxf_getLanguage()=='es'){ ?>
                <h1><?php echo of_get_option('home_frase_es'); ?></h1>
            <?php } else if(qtranxf_getLanguage()=='en'){ ?>
                <h1><?php echo of_get_option('home_frase_en'); ?></h1>
             <?php } else { ?>
                <h1><?php echo of_get_option('home_frase_pt'); ?></h1>
            <?php } ?>
			<div id="buscador-home">
				<?php echo do_shortcode('[aba_reserva_form]'); ?>
			</div>
			<?php
			$post_type = "autos";
			$post_linkAba = get_post_type_archive_link( $post_type ); 
			?>
		</div>
	</div>

	<div id="flotaSlider">
		<div class="swiper-container">
	        <div class="swiper-wrapper">
				<?php
					$taxonomy = 'abacategory';
				  $args = array(
				      'post_type' => 'autos',
				      'posts_per_page' => '8',
				      'orderby' => 'rand'
				  );
				  // The Query
				  $the_query1 = new WP_Query( $args );

				  // The Loop
				  if ( $the_query1->have_posts() ) {
				    ?>
				      <?php while ( $the_query1->have_posts() ) {
				        $the_query1->the_post();
				      ?>
					<?php
						$thumb_id = get_post_thumbnail_id();
						$thumb_url = wp_get_attachment_image_src($thumb_id,'carThumb', true);
					?>
					<?php $terms = get_the_terms( $post->ID , 'abacategory' ); 
			            foreach ( $terms as $term ) {
			                $term_link = get_term_link( $term, 'abacategory' );
			                if( is_wp_error( $term_link ) )
			                continue;
			            /*echo '<a href="' . $term_link . '">' . $term->name . '</a>';*/
			            } 
			        ?>
				      <div class="swiper-slide">
			            	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" style="background: url(<?php echo $thumb_url[0]; ?>">
			            		<span class="content">
			            			<strong>
			            				<?php the_title(); ?> 
			            			</strong>
			            			<p class="hidden-phone"><?php echo $term->name; ?></p>
			            			<span class="more btn">Conocer más</span>
			            		</span>
			            	</a>
			            </div>
		           	<?php
						}
			        ?>
				  <?php
				    } else {
				  ?>
				  <h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
				<?php
				  }
				  /* Restore original Post Data */
				  wp_reset_postdata();
				?>

	        </div>
	        <!-- Add Pagination -->
	        <div class="swiper-pagination"></div>
	    </div>
		<a href="<?php echo $post_linkAba; ?>" class="allFlota btn">
			<?php if(qtranxf_getLanguage() == 'en'): ?>
			    Our Vehicles
			<?php endif; ?>
			<?php if(qtranxf_getLanguage() == 'es'): ?>
			    Conocé nuestra flota
			<?php endif; ?>
			<?php if(qtranxf_getLanguage() == 'pt'): ?>
			    Conheça nossa frota
			<?php endif; ?>	
		</a>
		<div class="clearfix"></div>
	</div>
	<div id="presentation">
		<div class="box">
			<?php if (have_posts()): while (have_posts()) : the_post(); ?>

				<!-- article -->
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<!-- post thumbnail -->
					<?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
							<?php the_post_thumbnail(array(737,374), array('class' => 'right blockPhone')); // Declare pixel size you need inside the array ?>
					<?php endif; ?>
					<!-- /post thumbnail -->
					<!-- post title -->
					<h3>
						<?php the_title(); ?>
					</h3>
					<!-- /post title -->
					<?php the_content(); ?>
					<?php edit_post_link(); ?>
					<div class="clearfix"></div>
				</article>
				<!-- /article -->
			<?php endwhile; ?>
			<?php else: ?>
				<!-- article -->
				<article>
					<h2><?php _e( 'Disculpe, no hay nada para mostrar.', 'html5blank' ); ?></h2>
				</article>
				<!-- /article -->
			<?php endif; ?>
		</div>
	</div>
	<?php
		$args = array(
			'post_type' => 'news-ABA',
			'posts_per_page' => '1'
		);
		// The Query
		$the_query2 = new WP_Query( $args );

		// The Loop
		if ( $the_query2->have_posts() ) {
	?>
	<div id="news">
		<div class="box">

			
			      <?php while ( $the_query2->have_posts() ) {
			        $the_query2->the_post();
			      ?>
					<?php the_post_thumbnail('blog', array('class' => 'left blockPhone')); // Declare pixel size you need inside the array ?>
        			<h3>
        				<?php the_title(); ?> 
        			</h3>
        			<?php the_content(); ?>
        			<?php if(qtranxf_getLanguage()=='es'){ ?>
                        <a href="#book" class="btn">Consultar</a>
                    <?php } else if(qtranxf_getLanguage()=='en'){ ?>
                        <a href="#book" class="btn">Enquire</a>
                     <?php } else { ?>
                        <a href="#book" class="btn">Consultar</a>
                    <?php } ?>
        			
					<div class="clearfix"></div>
	           	<?php
					}
		        ?>
			  
		</div>
	</div>
	<?php
			    } else {
			  ?>
			  
			 <!-- <h2>< ?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>-->
			<?php
			  }
			  /* Restore original Post Data */
			  wp_reset_postdata();
			?>
	<?php get_template_part('book'); ?>
	<div id="location">
		<?php get_template_part('map'); ?>
	</div>

<?php get_footer(); ?>
