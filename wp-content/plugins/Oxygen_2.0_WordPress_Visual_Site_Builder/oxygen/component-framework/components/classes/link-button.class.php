<?php

/**
 * Button Component Class
 * 
 * @since 1.5
 */

Class CT_Link_Button extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_links", array( $this, "component_button" ) );
	}


	/**
	 * Add a [ct_link_button] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content ) {

		$options = $this->set_options( $atts );

		ob_start(); 

		?><a id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" href="<?php echo esc_attr($options['url']) ?>" <?php echo ($options['target']) ? "target=\"".esc_attr($options['target'])."\"" : ""; ?>><?php echo do_shortcode( $content ); ?></a><?php

		return ob_get_clean();
	}

}

// Create toolbar inctances
$button = new CT_Link_Button ( 

		array( 
			'name' 		=> __("Button","oxygen"),
			'tag' 		=> 'ct_link_button',
			'params' 	=> array(
					array(
						"type" 			=> "hyperlink",
						"heading" 		=> __("URL","oxygen"),
						"param_name" 	=> "url",
						"value" 		=> "http://",
						"css"			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.insertShortcodeToUrl">data</div>'
					),
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> "Double-click to edit button text.",
						"css"			=> false
					),
					array(
						"type" 			=> "radio",
						"heading" 		=> __("Button Style", "oxygen"),
						"param_name" 	=> "button-style",
						"value" 		=> array(
											1 	=> __("Solid", "oxygen"),
											2 	=> __("Outline", "oxygen")
										),
						"css"			=> false,
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Button Size", "oxygen"),
						"param_name" 	=> "button-size",
						"value" 		=> 10,
						"min"			=> "5",
						"max"			=> "25",
						"css"			=> false
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Button Color", "oxygen"),
						"param_name" 	=> "button-color",
						"value" 		=> "#1e73be",
						"css"			=> false,
						"hide_wrapper_end" => true,
					),
					array(
						"type" 				=> "colorpicker",
						"param_name" 		=> "button-hover_color",
						"heading" 			=> __("Button Hover Color", "oxygen"),
						"hide_wrapper_start"=> true,
						"state_condition" 	=> "!=hover"
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Text Color"),
						"param_name" 	=> "button-text-color",
						"value" 		=> "#ffffff",
						"condition"		=> "button-style!=2",
						"css"			=> false
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Text Size", "oxygen"),
						"param_name" 	=> "font-size",
						"min"			=> "10",
						"max"			=> "100",
					),
					array(
						"type" 			=> "font-family",
						"heading" 		=> __("Font Family", "oxygen"),
					),
					array(
						"type" 			=> "dropdown",
						"heading" 		=> __("Font Weight", "oxygen"),
						"param_name" 	=> "font-weight",
						"value" 		=> array (
											"" 	  => "&nbsp;",
											"100" => "100",
											"200" => "200",
											"300" => "300",
											"400" => "400",
											"500" => "500",
											"600" => "600",
											"700" => "700",
											"800" => "800",
											"900" => "900",
										),
					),
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Target","oxygen"),
						"param_name" 	=> "target",
						"value" 		=> "_self",
						"hidden"		=> true,
						"css"			=> false
					),
				),
			'advanced' 	=> array(
				"positioning" => array(
					"values" => array (
						'display' => 'inline-block',
					)
				),
				'typography' => array(
					'values' 	=> array (
						'text-align' 	=> "center",
					)
				),
			),
			'content_editable' => true,
		)
);

?>