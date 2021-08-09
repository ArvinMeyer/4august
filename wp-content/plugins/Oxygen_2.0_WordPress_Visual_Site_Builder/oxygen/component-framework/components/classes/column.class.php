<?php 

/**
 * Deprecated in Oxygen 2.0
 *
 * Do not remove to keep old designs work!
 */

Class CT_Column extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_column] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>"><?php echo do_shortcode( $content ); ?></div><?php

		return ob_get_clean();
	}
}


// Create inctance
$column = new CT_Column( array( 
			'name' 		=> 'Column',
			'tag' 		=> 'ct_column',
			'params' 	=> array(
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Background Color", "oxygen"),
						"param_name" 	=> "background-color",
						"value" 		=> "",
					),
					array(
						"type" 			=> "columnwidth",
						"heading" 		=> __("Width", "oxygen"),
						"param_name" 	=> "width",
						"value" 		=> "50.00",
						"css" 			=> false
					),
				),
			'advanced' 	=> array(
					"positioning" => array(
						"values" 	=> array (
							'width-unit' => '%',
							)
					),
                    'allow_shortcodes' => true,
				)
		)
	);