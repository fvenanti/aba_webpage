<?php get_header(); ?>
<div class="titlePage twoLines">
	<div class="box">
		<h4 class="subTitle">
				<?php if(qtranxf_getLanguage() == 'en'): ?>
				    Fleet
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'es'): ?>
				    Flota
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'pt'): ?>
				    Frota
				<?php endif; ?>	
			</h4>
			<h1>
				<?php if(qtranxf_getLanguage() == 'en'): ?>
				    Our vehicles
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'es'): ?>
				    Nuestros vehículos
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'pt'): ?>
				    Nossos veículos
				<?php endif; ?>	
			</h1>
	</div>
</div>
	<div id="news" class="blog">
		
		<div class="box">
			<ul id="og-grid" class="og-grid">
				<?php
				  query_posts( array( 'post_type' => 'autos', 'showposts' => 40 ) );
				  if ( have_posts() ) : while ( have_posts() ) : the_post();
				?>
				<?php
					$thumb_id = get_post_thumbnail_id();
					$thumb_url = wp_get_attachment_image_src($thumb_id,'large', true);
				?>
				<li class="carLi" id="post-<?php the_ID(); ?>">
					<?php $terms = get_the_terms( $post->ID , 'abacategory' ); 
			            foreach ( $terms as $term ) {
			                $term_link = get_term_link( $term, 'abacategory' );
			                if( is_wp_error( $term_link ) )
			                continue;
			            /*echo '<a href="' . $term_link . '">' . $term->name . '</a>';*/
			            } 
			        ?>
					<a href="#" data-largesrc="<?php echo $thumb_url[0]; ?>" data-title="<?php the_title(); ?>" data-description="
						<strong class='catInner'><?php echo $term->name; ?></strong>
						<?php the_content(); ?><span class='btnsBottom'><a href='<?php echo get_page_link(29); ?>' class='btn tarifas'>
						<?php if(qtranxf_getLanguage() == 'en'): ?>View Rates<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'es'): ?>Ver Tarifas<?php endif; ?>
						<?php if(qtranxf_getLanguage() == 'pt'): ?>Ver Tarifas<?php endif; ?></a>
						<a href='#book' class='btn'><?php if(qtranxf_getLanguage()=='es'){ ?>
						Consultar <?php } else if(qtranxf_getLanguage()=='en'){ ?>Enquire <?php } else { ?>Consultar<?php } ?></a></span>" class="carLink">
						<span class="imgCar" style="background: url(<?php echo $thumb_url[0]; ?>);"></span>
						<span class="carHover">
							<h3>
								<strong><?php the_title(); ?></strong><span class="carCat"><?php echo $term->name; ?></span>
							</h3>
						</span>
					</a>
					
				</li>
			<?php endwhile; ?>

			<?php else: ?>

				<!-- article -->
				<article>
					<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
				</article>
				<!-- /article -->

			<?php endif; ?>
				
			</ul>
		</div>
		
		<div class="clearfix"></div>
	</div>
<?php get_template_part('services'); ?>
<?php get_template_part('book'); ?>
<?php get_footer(); ?>