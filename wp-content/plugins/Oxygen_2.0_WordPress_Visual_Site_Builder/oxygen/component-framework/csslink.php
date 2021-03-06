<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// Need to output CSS for 404 page
if ( is_404() ) {
	status_header( 200 );
}

if ( !isset( $_REQUEST['xlink'] ) || stripslashes( $_REQUEST['xlink'] ) != 'css' ) {
	exit;
}

// Check if need to include CSS for classes, selectors, stylesheets, etc.
if ( isset( $_REQUEST['nouniversal'] ) && stripslashes( $_REQUEST['nouniversal'] ) == 'true' ) {
	$nouniversal = true;
}
else {
	$nouniversal = false;
}

// Set the correct MIME type
header("Content-type: text/css");

global $ct_template_id;

/**
 * Shortcodes and Classes
 */

$styles = false;
$id = isset($_REQUEST['tid'])?intval($_REQUEST['tid']):false;

if ( ! $styles ) {
	
	// start buffer again
	ob_start();

	ct_template_output();
	
	// output shortcode styles
	do_action('ct_footer_styles');
	
	// get shortcodes styles
	$styles = ob_get_clean();
	$styles = oxygen_css_minify( $styles );
	echo $styles;
}


/**
 * Stylesheets
 */

$styles = false;
if ( !$styles && $nouniversal == false ) {
	$styles = oxygen_vsb_get_stylesheet_styles();
	$styles = oxygen_css_minify( $styles );
	echo $styles;
}


/**
 * Custom selectors
 */

if ( $nouniversal == false ) {
	$css = oxygen_vsb_get_custom_selectors_styles();
	$css = oxygen_css_minify($css);
	echo $css;
}