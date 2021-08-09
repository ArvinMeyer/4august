<?php


Class CT_Image extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_visual", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_image] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		return '<img id="' . esc_attr( $options['selector'] ) . '" alt="' . esc_attr( base64_decode( $options['alt'] ) ) . '" src="' . esc_attr( $options['src'] ) . '" class="' . esc_attr( $options['classes'] ) . '"/>';
	}
}

/**
 * Create Image Component Instance
 * 
 * @since 0.1.2
 */

$button = new CT_Image ( 

		array( 
			'name' 		=> 'Image',
			'tag' 		=> 'ct_image',
			'params' 	=> array(
					array(
						"type" 			=> "mediaurl",
						"heading" 		=> __("Image URL"),
						"param_name" 	=> "src",
						"value" 		=> "http://placehold.it/1600x900",
						"css"			=> false
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Width"),
						"param_name" 	=> "width",
						"value" 		=> "",
						"hide_wrapper_end" => true,
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Height"),
						"param_name" 	=> "height",
						"value" 		=> "",
						"hide_wrapper_start" => true,
					),
					array(
						"param_name" 	=> "width-unit",
						"value" 		=> "auto",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "height-unit",
						"value" 		=> "auto",
						"hidden" 		=> true,
					),
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Alt Text"),
						"param_name" 	=> "alt",
						"value" 		=> "",
						"css" 			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertShortcodeToImageAlt">data</div>'
					)
			),
			'advanced' => array(
				"size" => array(
						"values" 	=> array (
							'max-width' 	=> '100',
							'max-width-unit' 	=> '%',
							)
					),
				'allowed_html' => 'post',
				'allow_shortcodes' => false,
			)
		)
);

?>