<?php

/**
 * Link Component Class
 * 
 * @since 0.1.5
 */

Class CT_Link_Wrapper extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		
		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_links", array( $this, "component_button" ) );
	}

	
	/**
	 * Add a toolbar button
	 *
	 * @since 0.1.5
	 */

	function component_button_old() { ?>
		
		<div class="ct-add-component-button ct-action-button" ng-click="addComponents('<?php echo $this->options['tag']; ?>', 'ct_text_block')">
			<?php echo $this->options['name']; ?>
		</div>

	<?php }


	/**
	 * Add a [ct_link] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start(); 

		?><a id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" href="<?php echo esc_url($options['url']) ?>" target="<?php echo esc_attr($options['target']) ?>"><?php echo do_shortcode( $content ); ?></a><?php

		return ob_get_clean();
	}

}


// Create toolbar inctances
$link = new CT_Link_Wrapper ( 

		array( 
			'name' 		=> 'Link Wrapper',
			'tag' 		=> 'ct_link',
			'params' 	=> array(
					array(
						"type" 			=> "hyperlink",
						"heading" 		=> __("URL","oxygen"),
						"param_name" 	=> "url",
						"value" 		=> "http://",
						"css" 			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.insertShortcodeToUrl">data</div>'

					),
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Target","oxygen"),
						"param_name" 	=> "target",
						"value" 		=> "_self",
						"hidden"		=> true,
						"css" 			=> false,
					),
					array(
						"type" 			=> "flex-layout",
						"heading" 		=> __("Layout Child Elements", "oxygen"),
						"param_name" 	=> "flex-direction",
						"css" 			=> true,
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> __("Allow multiline"),
						"param_name" 	=> "flex-wrap",
						"value" 		=> "nowrap",
						"true_value" 	=> "wrap",
						"false_value" 	=> "nowrap",
						"condition" 	=> "flex-direction=row"
					),
					array(
						"type" => "positioning",
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Width"),
						"param_name" 	=> "width",
						"value" 		=> "",
					),
					array(
						"type" 			=> "colorpicker",
						"heading" 		=> __("Background color"),
						"param_name" 	=> "background-color",
					),
				),
			'advanced' 	=> array(
					'typography' => array(
						'values' 	=> array (
								'font-family' 	=> "",
								'font-size' 	=> "",
								'font-weight' 	=> "",
							)
					),
					'flex' => array(
						'values' 	=> array (
								'display' 		 => 'flex',
								'flex-direction' => 'column',
								'align-items' 	 => 'center',
								'justify-content'=> 'center',
								'text-align' 	 => 'center'
							)
					),
                	'allowed_html'      => 'post',
                    'allow_shortcodes'  => true,
			)
		)
);

?>