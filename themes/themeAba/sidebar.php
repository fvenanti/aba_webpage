<!-- sidebar -->
<div class="colRight blockPhone">
<h3>
	<?php if(qtranxf_getLanguage() == 'en'): ?>
	    Our fleet
	<?php endif; ?>
	<?php if(qtranxf_getLanguage() == 'es'): ?>
	    Nuestra flota
	<?php endif; ?>
	<?php if(qtranxf_getLanguage() == 'pt'): ?>
	    Nossa frota
	<?php endif; ?>
</h3>

<?php
//list terms in a given taxonomy (useful as a widget for twentyten)
$taxonomy = 'abacategory';
$tax_terms = get_terms($taxonomy);
?>

<ul>
<?php
foreach ($tax_terms as $tax_term) {
echo '<li>' . '<a href="' . esc_attr(get_term_link($tax_term, $taxonomy)) . '" title="' . sprintf( __( "View all posts in %s" ), $tax_term->name ) . '" ' . '>' . $tax_term->name.'</a></li>';
}
?>
</ul>
		<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('widget-area-1')) ?>
</div>
<!-- /sidebar -->
