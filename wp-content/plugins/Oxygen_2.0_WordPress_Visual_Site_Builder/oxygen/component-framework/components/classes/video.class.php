<?php


Class CT_Video extends CT_Component {

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
	 * Add a [ct_video] shortcode to WordPress
	 *
	 * @since 1.5
	 */

	function add_shortcode( $atts, $content ) {

		$options = $this->set_options( $atts );

        ob_start();
        
        ?><div id="<?php echo esc_attr($options['selector']) ?>" class="<?php echo esc_attr($options['classes']); ?>">
        <?php
        	if($options['use_custom'] !== '1') {
        ?>
        <div class="oxygen-vsb-responsive-video-wrapper"><iframe src="<?php echo esc_attr($options['embed_src']); ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
        <?php
        	}
        	else {
        ?>
		<div class="oxygen-vsb-responsive-video-wrapper oxygen-vsb-responsive-video-wrapper-custom"><?php echo base64_decode($options['custom_code']); ?></div>
		<?php
			}
		?>
        </div><?php

		return ob_get_clean();
	}
}

/**
 * Create Video Component Instance
 * 
 * @since 1.5
 */

$video = new CT_Video ( 

		array( 
			'name' 		=> 'Video',
			'tag' 		=> 'ct_video',
			'params' 	=> array(
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("YouTube / Vimeo URL", "oxygen"),
						"param_name" 	=> "src",
						"value" 		=> "https://www.youtube.com/watch?v=oHg5SJYRHA0",
						"css"			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesCustomFieldMode" callback="iframeScope.insertShortcodeToSrc">data</div>'
					),
					array(
						"param_name"	=> "embed_src",
						"value"			=> "https://www.youtube.com/embed/oHg5SJYRHA0",
						"css"			=> false,
						"hidden"		=> true
					),
					array(
						"type" 			=> "radio",
						"heading" 		=> __("Video Aspect Ratio", "oxygen"),
						"param_name" 	=> "video-padding-bottom",
						"value" 		=> array(
											'75%' 	=> __("4:3 (standard)", "oxygen"),
											'56.25%' 	=> __("16:9 (standard widescreen)", "oxygen"),
											'41.84%' 	=> __("21:9 (Cinematic widescreen)", "oxygen")
										),
						"default"		=> '56.25%',
						"css"			=> false,
						"line_breaks"	=> true,
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> __("Embed Iframe", "oxygen"),
						"param_name" 	=> "use-custom",
						"value" 		=> "0",
						"true_value" 	=> "1",
						"false_value" 	=> "0",
						"label" 		=> __("Manually Paste Iframe Code", "oxygen"),
						"css" 			=> false,
					),

					array(
						"type" 			=> "textarea",
						"heading" 		=> __("Custom Code Here"),
						"param_name" 	=> "custom-code",
						"value" 		=> "",
						"css" 			=> false,
						"condition" 	=> "use-custom=1"
					),
					
				)
		)
);

?>