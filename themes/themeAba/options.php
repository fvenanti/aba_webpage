<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
function optionsframework_option_name() {
	// Change this to use your theme slug
	return 'options-framework-theme';
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'theme-textdomain'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {


	$options = array();

	$options[] = array(
		'name' => __('Opciones generales', 'options_check'),
		'type' => 'heading');

	$options[] = array(
		'name' => __( 'Datos Footer (es)', 'theme-textdomain' ),
		'desc' => __( 'Información de contacto español', 'theme-textdomain' ),
		'id' => 'footer_textarea_es',
		'std' => 'Default Text',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( 'Datos Footer (en)', 'theme-textdomain' ),
		'desc' => __( 'Información de contacto ingles', 'theme-textdomain' ),
		'id' => 'footer_textarea_en',
		'std' => 'Default Text',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( 'Datos Footer (br)', 'theme-textdomain' ),
		'desc' => __( 'Información de contacto portugues', 'theme-textdomain' ),
		'id' => 'footer_textarea_br',
		'std' => 'Default Text',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __('facebook', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'facebook',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('twitter', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'twitter',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('linkedin', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'linkedin',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('googleplus', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'googleplus',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('youtube', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'youtube',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('pinterest', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'pinterest',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('instagram', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'instagram',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('tel', 'options_check'),
		'desc' => __('', 'options_check'),
		'id' => 'tel',
		'std' => '',
		'type' => 'text');


	$options[] = array(
		'name' => __('Home', 'options_check'),
		'type' => 'heading');

	$options[] = array(
		'name' => __( 'Frase home (es)', 'theme-textdomain' ),
		'desc' => __( 'Frase home en español', 'theme-textdomain' ),
		'id' => 'home_frase_es',
		'std' => 'Default Text',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( 'Frase home (en)', 'theme-textdomain' ),
		'desc' => __( 'Frase home en ingles', 'theme-textdomain' ),
		'id' => 'home_frase_en',
		'std' => 'Default Text',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( 'Frase home (br)', 'theme-textdomain' ),
		'desc' => __( 'Frase home en portugues', 'theme-textdomain' ),
		'id' => 'home_frase_pt',
		'std' => 'Default Text',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( 'Imagen home', 'theme-textdomain' ),
		'desc' => __( 'This creates a full size uploader that previews the image.', 'theme-textdomain' ),
		'id' => 'imgHome_uploader',
		'type' => 'upload'
	);

	return $options;
}