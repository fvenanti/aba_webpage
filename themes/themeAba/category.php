<?php get_header(); ?>
<main role="main">
<section id="news" class="blog">
		<div class="box">
			<h4 class="subTitle">Sección de Novedades</h4>
			<h1><?php _e( '', 'html5blank' ); single_cat_title(); ?></h1>
		</div>

		<?php get_template_part('loopAba'); ?>
		<?php get_template_part('pagination'); ?>

		<div class="clearfix"></div>
		</section>
	</main>

<?php get_template_part('book'); ?>
<?php get_footer(); ?>
