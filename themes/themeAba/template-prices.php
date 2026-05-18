<?php /* Template Name: Prices template */ get_header(); ?>
<div class="titlePage">
	<div class="box">
		<h1><?php the_title(); ?></h1>
	</div>
</div>
<div id="news" class="article">
	<div class="newBox">
		<div class="box">
			<main class="tarifas">
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
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="newBox carBox">
		<div class="box">
			<div class="tableBox mdl-shadow--2dp">
				<div class="tableHeader hidden-phone">
					<div class="cell cellbig">
						<?php if(qtranxf_getLanguage() == 'en'): ?>						    
							Categories
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>
						    Categorías
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>
						    Categorias
						<?php endif; ?>	
						
					</div>
					<!--<div class="cell cellSmall">
						< ?php if(qtranxf_getLanguage() == 'en'): ?>						    
							2 Days <br> <span>500 Km Free</span>
						< ?php endif; ?>
						< ?php if(qtranxf_getLanguage() == 'es'): ?>
						    2 Días <br> <span>500 Km Libres</span>
						< ?php endif; ?>
						< ?php if(qtranxf_getLanguage() == 'pt'): ?>
						    2 Dias <br> <span>Livre 500 km</span>
						< ?php endif; ?>	
					</div>-->
					<div class="cell cellSmall">
						<?php if(qtranxf_getLanguage() == 'en'): ?>						    
							3 Days <br> <span>700 Km Free</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>
						    3 Días <br> <span>700 Km Libres</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>
						    3 Dias <br> <span>Livre 700 km</span>
						<?php endif; ?>	
					</div>
					<div class="cell cellSmall">
						<?php if(qtranxf_getLanguage() == 'en'): ?>						    
							4 Days <br> <span>800 Km Free</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>
						    4 Días <br> <span>800 Km Libres</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>
						    4 Dias <br> <span>Livre 800 km</span>
						<?php endif; ?>	
					</div>
					<div class="cell cellSmall">
						<?php if(qtranxf_getLanguage() == 'en'): ?>						    
							5 Days <br> <span>1000 Km Free</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>
						    5 Días <br> <span>1000 Km Libres</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>
						    5 Dias <br> <span>Livre 1000 km</span>
						<?php endif; ?>	
					</div>
					<div class="cell cellSmall">
						<?php if(qtranxf_getLanguage() == 'en'): ?>						    
							6 Days <br> <span>1200 Km Free</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>
						    6 Días <br> <span>1200 Km Libres</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>
						    6 Dias <br> <span>Livre 1200 km</span>
						<?php endif; ?>	
					</div>
					<div class="cell cellSmall">
						<?php if(qtranxf_getLanguage() == 'en'): ?>						    
							7 Days <br> <span>1400 Km Free</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>
						    7 Días <br> <span>1400 Km Libres</span>
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>
						    7 Dias <br> <span>Livre 1400 km</span>
						<?php endif; ?>	
					</div>
					<div class="cell cellSmall">
						<?php if(qtranxf_getLanguage() == 'en'): ?>						    
							Km Exc
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>
						    Km Adic
						<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>
						    Km Exc
						<?php endif; ?>	
					</div>
				</div>
			
				<?php
				$taxonomy = 'abacategory';
				$cars_terms = get_terms($taxonomy);
				
				foreach($cars_terms as $car_term) {
					    wp_reset_query();
					    $args = array('post_type' => 'autos',
					        'tax_query' => array(
					            array(
					                'taxonomy' => $taxonomy,
					                'field' => 'slug',
					                'terms' => $car_term->slug,
					            ),
					        ),
					     );
						echo "<div class='rowTable'>";
						     $loop = new WP_Query($args);
						     if($loop->have_posts()) {
						     	echo "<div class='cell cellbig cellVehicle'>";
							        echo '<h4>'.$car_term->name.'</h4>';
							
							        while($loop->have_posts()) : $loop->the_post();
							            echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
							        endwhile;
						        echo "</div>";
						
						        /*echo "<div class='cell cellSmall'><strong class='visible-phone'>2 Días</strong>".get_term_meta( $car_term->term_id, 'days2', true )."</div>";*/
								echo "<div class='cell cellSmall'><strong class='visible-phone'>3 Días</strong>".get_term_meta( $car_term->term_id, 'days3', true )."</div>";
								echo "<div class='cell cellSmall'><strong class='visible-phone'>4 Días</strong>".get_term_meta( $car_term->term_id, 'days4', true )."</div>";
								echo "<div class='cell cellSmall'><strong class='visible-phone'>5 Días</strong>".get_term_meta( $car_term->term_id, 'days5', true )."</div>";
								echo "<div class='cell cellSmall'><strong class='visible-phone'>6 Días</strong>".get_term_meta( $car_term->term_id, 'days6', true )."</div>";
								echo "<div class='cell cellSmall'><strong class='visible-phone'>7 Días</strong>".get_term_meta( $car_term->term_id, 'days7', true )."</div>";
								echo "<div class='cell cellSmall'><strong class='visible-phone'>Km Exc</strong>".get_term_meta( $car_term->term_id, 'kmexc', true )."</div>";
						     }
					    echo "</div>";
					}
				
				?>

			</div>
		</div>
	</div>
</div>

<?php get_template_part('services'); ?>
<?php get_template_part('book'); ?>
<?php get_footer(); ?>
