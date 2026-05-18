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
			
			<a href="<?php echo $post_linkAba; ?>" class="flota">
				<span class="icon">
					<?php echo '<?xml version="1.0" encoding="utf8"?>'; ?>
					<!-- Generator: Adobe Illustrator 19.1.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
					<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
						 viewBox="-155 279.7 283.1 283.1" style="enable-background:new -155 279.7 283.1 283.1;" xml:space="preserve">
					<style type="text/css">
						.st0{fill:#FFFFFF;}
					</style>
					<g>
						<path class="st0" d="M52.6,421.3H-78.8c-2.8,0-5.1,2.3-5.1,5.1c0,2.8,2.3,5.1,5.1,5.1H52.6c2.8,0,5.1-2.3,5.1-5.1
							C57.7,423.5,55.4,421.3,52.6,421.3z"/>
						<path class="st0" d="M-94.5,456.7c-2.8,0-5.1,2.3-5.1,5.1c0,2.7-2.2,4.9-4.9,4.9c-2.7,0-4.9-2.2-4.9-4.9c0-2.7,2.2-4.9,4.9-4.9
							c2.8,0,5.1-2.3,5.1-5.1c0-2.8-2.3-5.1-5.1-5.1c-8.3,0-15,6.7-15,15c0,8.3,6.7,15,15,15c8.3,0,15-6.7,15-15
							C-89.4,459-91.7,456.7-94.5,456.7z"/>
						<path class="st0" d="M87.8,456.7c-2.8,0-5.1,2.3-5.1,5.1c0,2.7-2.2,4.9-4.9,4.9c-2.7,0-4.9-2.2-4.9-4.9c0-2.7,2.2-4.9,4.9-4.9
							c2.8,0,5.1-2.3,5.1-5.1c0-2.8-2.3-5.1-5.1-5.1c-8.3,0-15,6.7-15,15c0,8.3,6.7,15,15,15c8.3,0,15-6.7,15-15
							C92.8,459,90.5,456.7,87.8,456.7z"/>
						<path class="st0" d="M105.6,403.2c-0.1,0-0.2-0.1-0.4-0.1c-1.4-0.5-2.9-1-4.5-1.5c-18.6-5.3-57.2-5.3-58.8-5.3h-8.2l-16-26.9
							c-7.3-12.2-14.2-18.8-29.5-18.8h-95.8c-12.3,0-24.4,8.3-31.8,21.5c0,0.1-0.1,0.1-0.1,0.2l-14.3,28.9c-0.1,0.2-0.2,0.5-0.3,0.7
							c-1.1,3.4-1,7.6-1,8.4v30c0,13.7,11.5,20.3,20.3,22.6c0.6,16.2,14,29.2,30.3,29.2c15,0,27.5-11,29.9-25.3H47.9
							c2.4,14.3,14.9,25.3,29.9,25.3c15,0,27.5-11,29.9-25.3h15.3c2.8,0,5.1-2.3,5.1-5.1v-29.4C128.1,422.7,124.1,410.1,105.6,403.2z
							 M114.9,421.6l-11.8,0v-5c0-0.6,0.4-1.8,1.4-3C109.1,415.7,112.6,418.3,114.9,421.6z M22.1,396.2H-88l6.7-14
							c2.3-4.6,5.6-6.6,11.2-6.6H9.8L22.1,396.2z M-130.4,376.8c0.2-0.4,0.4-0.8,0.7-1.2c0,0,0,0,0,0h21.9l-10.2,20.3h-21.8L-130.4,376.8
							z M-144.9,410c0-0.9,0-2.6,0.3-3.9h5.2v15.5h-5.4v-11.4C-144.9,410.2-144.9,410.1-144.9,410z M-104.4,482
							c-11.2,0-20.2-9.1-20.2-20.2c0-11.2,9.1-20.2,20.2-20.2c11.2,0,20.2,9.1,20.2,20.2C-84.2,472.9-93.2,482-104.4,482z M77.8,482
							c-11.2,0-20.2-9.1-20.2-20.2c0-11.2,9.1-20.2,20.2-20.2c11.2,0,20.2,9.1,20.2,20.2C98.1,472.9,89,482,77.8,482z M118,456.7h-10.3
							c-2.4-14.3-14.9-25.3-29.9-25.3c-15,0-27.5,11-29.9,25.3H-74.5c-2.4-14.3-14.9-25.3-29.9-25.3c-13.6,0-25.1,9-29,21.3
							c-4.7-1.5-11.5-4.9-11.5-12.4v-8.6h5.4c5.6,0,10.1-4.5,10.1-10.1v-15.5h14.4c1.9,0,3.7-1.1,4.5-2.8l15.3-30.4
							c0.8-1.6,0.7-3.4-0.2-4.9c-0.9-1.5-2.6-2.4-4.3-2.4h-21.2c4.2-3.2,8.8-5,13.4-5h95.8c6.6,0,10.6,1.4,14.3,5h-72.6
							c-9.5,0-16.3,4.1-20.3,12.3l-10.1,21c-0.3,0.7-0.5,1.4-0.5,2.2v0.2c0,2.8,2.3,5.1,5.1,5.1H42c0.4,0,34.1,0.1,52.5,4.1
							c-1,2.1-1.5,4.3-1.5,6.2v10c0,1.3,0.5,2.6,1.5,3.6c0.9,0.9,2.2,1.5,3.6,1.5c0,0,0,0,0,0l20,0c0,0.2,0,0.4,0,0.6L118,456.7
							L118,456.7z"/>
					</g>
					</svg>
				</span>
				<?php if(qtranxf_getLanguage() == 'en'): ?>
				    Vehicles
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'es'): ?>
				    Nuestra flota
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'pt'): ?>
				    Vista Frota
				<?php endif; ?>
			</a>
			<a href="https://api.whatsapp.com/send/?phone=5492944604766&text&type=phone_number&app_absent=0" class="tarifas">
				<span class="icon">
					<?php echo '<?xml version="1.0" encoding="utf8"?>'; ?>
					<!-- Generator: Adobe Illustrator 19.1.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
					<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
						 viewBox="-238 361.7 118.2 118.2" style="enable-background:new -238 361.7 118.2 118.2;" xml:space="preserve">
					<style type="text/css">
						.st0{fill:#FFFFFF;}
					</style>
					<g>
						<path class="st0" d="M-122.8,369.5c-0.8-2.7-3.3-7.3-11.3-7.8c-1.8-0.1-9.2,0.5-17.9,15.4l-31.2,0h0c-0.5,0-1,0.1-1.5,0.4
							c-0.4,0.2-0.7,0.4-1,0.7l-49,52.1c-1.2,1.3-1.2,3.4,0.1,4.7l46.9,44.1c0.6,0.6,1.4,0.9,2.3,0.9c0.9,0,1.8-0.3,2.4-1l48.8-51.9
							c0.7-0.7,1.1-1.6,1.1-2.7l-1.8-29.4C-125.7,387.2-120.6,376.8-122.8,369.5z M-185.5,471.9l-42.1-39.6l45.8-48.7l26.4,0
							c-1.3,2.7-2.6,5.8-3.9,9.3c-3-0.2-6,0.8-8.1,3c-3.8,4-3.6,10.4,0.4,14.2c1.9,1.8,4.3,2.7,6.9,2.7c2.8,0,5.4-1.2,7.3-3.2
							c1.8-2,2.8-4.5,2.7-7.2c-0.1-2.7-1.2-5.2-3.2-7c-0.4-0.4-0.8-0.6-1.2-0.9c1.5-3.7,3.2-7.4,4.9-10.9l7.4,0l2.5,39.5L-185.5,471.9z
							 M-155.2,402.7c0,1.3-0.4,2.6-1.4,3.6c-1.8,1.9-5.1,2-7.1,0.2c-2-1.9-2.1-5.1-0.2-7.1c0.7-0.8,1.6-1.3,2.6-1.5
							c-0.5,1.3-0.9,2.7-1.4,4.1c-0.4,1.3,0.3,2.8,1.6,3.2c0.3,0.1,0.5,0.1,0.8,0.1c1.1,0,2.1-0.7,2.4-1.8c0.4-1.3,0.9-2.7,1.4-4
							C-155.7,400.5-155.2,401.6-155.2,402.7z M-135.8,380.2c-0.1-1.7-1.5-3.1-3.3-3.1l-6.8,0c3.9-6.3,8-10.6,11.5-10.4
							c3.7,0.2,6,1.6,6.8,4.2c1.2,4.1-1.4,11.1-7.7,17.4L-135.8,380.2z"/>
						<path class="st0" d="M-177.9,425l-2.8-2.6l-2.5,2.7c-3.6-2.2-7.1-1.9-9.5,0.5c-2.6,2.7-1.8,6.1,0.2,9.9c1.4,2.7,1.7,4.2,0.7,5.3
							c-1.1,1.1-2.7,0.7-4.3-0.8c-1.8-1.7-2.9-3.9-3.5-5.6l-3.9,2.5c0.5,1.6,1.8,3.9,3.6,5.7l-2.5,2.7l2.8,2.6l2.7-2.9
							c3.8,2.5,7.5,2,9.9-0.5c2.4-2.5,2.5-5.4,0.3-9.7c-1.5-3.1-2-4.7-1.1-5.6c0.7-0.8,2.1-1,3.9,0.7c2,1.9,2.7,3.8,3.1,4.8l3.8-2.4
							c-0.5-1.3-1.4-2.9-3.2-4.8L-177.9,425z"/>
						<path class="st0" d="M-188.5,400.3c-0.4-0.4-1.1-0.4-1.5,0c-0.4,0.4-0.4,1.1,0,1.5l33,31.1c0.2,0.2,0.5,0.3,0.7,0.3
							c0.3,0,0.6-0.1,0.8-0.3c0.4-0.4,0.4-1.1,0-1.5L-188.5,400.3z"/>
					</g>
					</svg>
				</span>
				<?php if(qtranxf_getLanguage() == 'en'): ?>
				    Ask for our Rates
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'es'): ?>
				    Pedí tu Presupuesto
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'pt'): ?>
				    Confira nossas tarifas
				<?php endif; ?>
			</a>
			<a href="#book" class="reservas">
				<span class="icon">
					<?php echo '<?xml version="1.0" encoding="utf8"?>'; ?>
					<!-- Generator: Adobe Illustrator 19.1.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
					<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
						 viewBox="-230 353.3 134.6 134.6" style="enable-background:new -230 353.3 134.6 134.6;" xml:space="preserve">
					<style type="text/css">
						.st0{fill:#FFFFFF;}
					</style>
					<g>
						<path class="st0" d="M-192.1,408h-12.6c-2.5,0-4.2,1.7-4.2,4.2c0,2.5,1.7,4.2,4.2,4.2h12.6c2.5,0,4.2-1.7,4.2-4.2
							C-187.9,409.7-189.6,408-192.1,408z"/>
						<path class="st0" d="M-133.3,412.2c0,2.5,1.7,4.2,4.2,4.2h16.8c2.5,0,4.2-1.7,4.2-4.2c0-2.5-1.7-4.2-4.2-4.2h-16.8
							C-131.6,408-133.3,409.7-133.3,412.2z"/>
						<path class="st0" d="M-141.7,408h-12.6c-2.5,0-4.2,1.7-4.2,4.2c0,2.5,1.7,4.2,4.2,4.2h12.6c2.5,0,4.2-1.7,4.2-4.2
							C-137.5,409.7-139.2,408-141.7,408z"/>
						<path class="st0" d="M-166.9,408h-12.6c-2.5,0-4.2,1.7-4.2,4.2c0,2.5,1.7,4.2,4.2,4.2h12.6c2.5,0,4.2-1.7,4.2-4.2
							C-162.7,409.7-164.4,408-166.9,408z"/>
						<path class="st0" d="M-192.1,429h-12.6c-2.5,0-4.2,1.7-4.2,4.2c0,2.5,1.7,4.2,4.2,4.2h12.6c2.5,0,4.2-1.7,4.2-4.2
							C-187.9,430.7-189.6,429-192.1,429z"/>
						<path class="st0" d="M-112.3,429h-16.8c-2.5,0-4.2,1.7-4.2,4.2c0,2.5,1.7,4.2,4.2,4.2h16.8c2.5,0,4.2-1.7,4.2-4.2
							C-108.1,430.7-109.8,429-112.3,429z"/>
						<path class="st0" d="M-108.5,361.7h-8v-4.2c0-2.5-1.7-4.2-4.2-4.2s-4.2,1.7-4.2,4.2v4.2h-16.8v-4.2c0-2.5-1.7-4.2-4.2-4.2
							s-4.2,1.7-4.2,4.2v4.2h-16.8v-4.2c0-2.5-1.7-4.2-4.2-4.2s-4.2,1.7-4.2,4.2v4.2h-16.8v-4.2c0-2.5-1.7-4.2-4.2-4.2
							c-2.5,0-4.2,1.7-4.2,4.2v4.2h-12.2c-8,0-17.2,6.7-17.2,15.1v87.9c0,8.8,8.8,14.7,17.2,14.7h29c0.4,0,0.4,0,0.8,0
							c5.9,5.5,13.9,8.4,22.3,8.4s16.4-3.4,22.3-8.4c0.4,0,0.4,0,0.8,0h29c8,0,13-5.9,13-14.7v-87.9C-95.5,368-100.6,361.7-108.5,361.7z
							 M-160.6,479.5c-13.9,0-25.2-11.4-25.2-25.2c0-13.9,11.3-25.2,25.2-25.2c13.9,0,25.2,11.4,25.2,25.2
							C-135.4,468.1-146.8,479.5-160.6,479.5z M-103.9,387h-8.4c-2.5,0-4.2,1.7-4.2,4.2c0,2.5,1.7,4.2,4.2,4.2h8.4v69.4
							c0,6.3-3.4,6.3-4.6,6.3h-23.1c2.1-3.8,3.8-8,4.2-12.6h15.1c2.5,0,4.2-1.7,4.2-4.2c0-2.5-1.7-4.2-4.2-4.2h-15.1
							c-2.1-16.4-16.4-29.4-33.2-29.4s-31.1,12.6-33.2,29.4h-10.9c-2.5,0-4.2,1.7-4.2,4.2c0,2.5,1.7,4.2,4.2,4.2h10.9
							c0.4,4.6,2.1,8.8,4.2,12.6h-23.1c-3.8,0-8.8-2.9-8.8-6.3v-69.4h92.4c2.5,0,4.2-1.7,4.2-4.2s-1.7-4.2-4.2-4.2h-92.4v-10.1
							c0-2.9,4.6-6.7,8.8-6.7h12.2v4.2c0,2.5,1.7,4.2,4.2,4.2s4.2-1.7,4.2-4.2v-4.2h16.8v4.2c0,2.5,1.7,4.2,4.2,4.2s4.2-1.7,4.2-4.2v-4.2
							h16.8v4.2c0,2.5,1.7,4.2,4.2,4.2s4.2-1.7,4.2-4.2v-4.2h16.8v4.2c0,2.5,1.7,4.2,4.2,4.2s4.2-1.7,4.2-4.2v-4.2h8
							c4.2,0,4.6,4.2,4.6,6.7L-103.9,387z"/>
						<path class="st0" d="M-151,442.9l-13.9,13.9l-5.5-5.5c-1.7-1.7-4.2-1.7-5.9,0c-1.7,1.7-1.7,4.2,0,5.9l8.4,8.4
							c0.4,0.4,0.4,0.4,0.8,0.4l0.4,0.4c0.4,0,0.8,0.4,1.7,0.4c0.4,0,1.3,0,1.7-0.4l0.4-0.4c0.4,0,0.4-0.4,0.8-0.4l16.8-16.8
							c1.7-1.7,1.7-4.2,0-5.9C-146.8,441.2-149.3,441.2-151,442.9z"/>
					</g>
					</svg>
				</span>
				<?php if(qtranxf_getLanguage() == 'en'): ?>
				    Enquire/Reservations
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'es'): ?>
				    Consultas/Reservas
				<?php endif; ?>
				<?php if(qtranxf_getLanguage() == 'pt'): ?>
				    Consultas/Reservas
				<?php endif; ?>
			</a>
			<div class="clearfix"></div>
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
