<?php

/**
 * Link Text Component Class
 * 
 * @since 0.3.1
 */

Class CT_Link_Text extends CT_Component {

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
	 * Add a [ct_link_text] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );
		
		ob_start(); 

		?><a id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" href="<?php echo esc_url($options['url']) ?>" <?php echo ($options['target']) ? "target=\"".esc_attr($options['target'])."\"" : ""; ?>><?php echo do_shortcode( $content ); ?></a><?php

		return ob_get_clean();
	}

}


// Create toolbar inctances
$link = new CT_Link_Text ( 

		array( 
			'name' 		=> 'Text Link',
			'tag' 		=> 'ct_link_text',
			'params' 	=> array(

					array(
						"type" 			=> "hyperlink",
						"heading" 		=> __("URL"),
						"param_name" 	=> "url",
						"value" 		=> "http://",
						"css" 			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.insertShortcodeToUrl">data</div>'
					),
					array(
						"type" 			=> "content",
						"param_name" 	=> "ct_content",
						"value" 		=> "Double-click to edit link text.",
						"css" 			=> false,
					),
					array(
						"type" 			=> "font-family",
						"heading" 		=> __("Font Family", "oxygen"),
						"css" 			=> false,
					),
					array(
						"type" 				=> "colorpicker",
						"param_name" 		=> "color",
						"heading" 			=> __("Text Color", "oxygen"),
						"hide_wrapper_end" 	=> true,
					),
					array(
						"type" 				=> "colorpicker",
						"param_name" 		=> "hover_color",
						"heading" 			=> __("Hover Color", "oxygen"),
						"hide_wrapper_start"=> true,
						"state_condition" 	=> "!=hover"
					),
					array(
						"type" 			=> "slider-measurebox",
						"heading" 		=> __("Font Size", "oxygen"),
						"param_name" 	=> "font-size",
					),
					array(
						"type" 			=> "dropdown",
						"heading" 		=> __("Font Weight", "oxygen"),
						"param_name" 	=> "font-weight",
						"value" 		=> array (
											"" 		=> "&nbsp;",
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
						"type" 			=> "checkbox",
						"heading" 		=> __("Underline", "oxygen"),
						"label" 		=> __("Underline link text", "oxygen"),
						"param_name" 	=> "text-decoration",
						"value" 		=> "none",
						"true_value" 	=> "underline",
						"false_value" 	=> "none",
					),
					
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Target"),
						"param_name" 	=> "target",
						"value" 		=> "_self",
						"hidden"		=> true,
						"css" 			=> false,
					),
				),
			'advanced' 	=> array(
				"positioning" => array(
					"values" => array (
						'display' => 'inline-block',
					)
				),
				'typography' => array(
					'values' => array (
						'font-size' 	=> "",
						'font-weight' 	=> "",
					)
				),
				'allowed_html'      => 'post',
                'allow_shortcodes'  => false,
			),
			'content_editable' => true,
		)
);

?>