<?php get_header(); ?>

	<div id="news" class="article">
		<div class="box">
			<h1><?php _e( 'Latest Posts', 'html5blank' ); ?></h1>
			<span class="line"></span>
		</div>
		<div class="newBox">
			<div class="box">
				<main class="colLeft">
					<?php
					  query_posts( array( 'post_type' => 'autos', 'showposts' => 10 ) );
					  if ( have_posts() ) : while ( have_posts() ) : the_post();
					?>

					  <h3><?php the_title(); ?></h3>

					<?php endwhile; endif; wp_reset_query(); ?>
					<?php get_template_part('loop'); ?>
					<a href="#" class="btn">Consultar</a>
					<a href="#" class="btn">Realizar reserva</a>
					<?php get_template_part('pagination'); ?>
				</main>
				<?php get_sidebar(); ?>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div id="frase">
			<div class="box">
				<h5>Recomendaciones y Tips </h5>
				<h4>Localidades que brindamos Servicio</h4>
				<p>ABA Rent a Car tiene su central en San Carlos de Bariloche. El servicio de retiro o entrega en otras localidades tiene un costo de acuerdo a los kilómetros de distancia. Consulte. </p>
			</div>
		</div>
	</div>
<?php get_template_part('book'); ?>
<?php get_footer(); ?>
