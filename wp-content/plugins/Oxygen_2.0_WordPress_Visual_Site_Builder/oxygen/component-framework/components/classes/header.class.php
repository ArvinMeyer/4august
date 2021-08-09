<?php 

/**
 * Header Builder component
 *
 * @since 2.0
 * @author Ilya K.
 */

Class Oxy_Header_Builder extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "header_settings"), 9 );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_helpers_components_composite", array( $this, "component_button" ) );
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 2.0
	 */
	function component_button() { ?>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addComponents('<?php echo esc_attr($this->options['tag']); ?>','oxy_header_row')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header-active.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * Add a [oxy_header] shortcode to WordPress
	 *
	 * @since 2.0
	 */

	function add_shortcode( $atts, $content ) {

		$options = $this->set_options( $atts );

		global $media_queries_list_above;
		$min_size = $media_queries_list_above[$options['sticky_media']]['minSize'];
		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="oxy-header-wrapper <?php echo ($options["sticky_header"]=="yes") ? "oxy-sticky-header " : ""; ?><?php echo esc_attr($options['classes']); ?>"><?php echo do_shortcode( $content ); ?></div>
		<?php if ($options["sticky_header"]=="yes") : ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var selector = "#<?php echo esc_attr($options['selector']); ?>",
					scrollval = parseInt("<?php echo esc_attr($options['sticky_scroll_distance']); ?>");
				if (!scrollval || scrollval < 1) {
					if (jQuery(window).width() > <?php echo intval($min_size); ?>){
						jQuery(selector).addClass("oxy-sticky-header-active");
						jQuery("body").css("margin-top", jQuery(selector).height());
					}
				}
				else {
					jQuery(window).scroll(function() {
						if (jQuery(this).scrollTop() > scrollval){
							if (jQuery(window).width() > <?php echo intval($min_size); ?>){
								jQuery(selector).addClass("oxy-sticky-header-active");
								jQuery("body").css("margin-top", jQuery(selector).height());
							}
						}
						else {
							jQuery(selector).removeClass("oxy-sticky-header-active");
							jQuery("body").css("margin-top", "");
						}
					})
				}
			});
		</script><?php
		endif;

		return ob_get_clean();
	}


	/**
	 * Output special settings in Basic Styles tab
	 *
	 * @since 2.0
	 */

	function header_settings() { 

		global $oxygen_toolbar; ?>

		<div class="oxygen-control-row"
			ng-show="isActiveName('oxy_header')">
			<div class="oxygen-control-wrapper">
				<div id="oxygen-add-another-row" class="oxygen-add-section-element"
					ng-click="iframeScope.addComponent('oxy_header_row')">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header.svg' />
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/header-active.svg' />
					<?php _e("Add Another Row","oxygen"); ?>
				</div>
			</div>
		</div>

		<div ng-show="isActiveName('<?php echo $this->options['tag']; ?>')">
			<?php $oxygen_toolbar->media_queries_list_with_wrapper("stack-header-vertically", __("Stack Vertically Below","oxygen"), true); ?>
		</div>

	<?php }
}


// Create Header Builder instance
$oxy_header_builder = new Oxy_Header_Builder( array( 
			'name' 		=> __('Header Builder','oxygen'),
			'tag' 		=> 'oxy_header',
			'params' 	=> array(
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Background color"),
					"param_name" 	=> "background-color",
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> __("Sticky","oxygen"),
					"param_name" 	=> "sticky_header",
					"value" 		=> "no",
					"true_value" 	=> "yes",
					"false_value" 	=> "no",
					"css" 			=> false
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Scroll Distance (px)","oxygen"),
					"param_name" 	=> "sticky_scroll_distance",
					"value" 		=> "300",
					"condition"		=> "sticky_header=yes",
					"css" 			=> false
				),
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Sticky Background Color","oxygen"),
					"param_name" 	=> "sticky-background-color",
					"condition"		=> "sticky_header=yes",
					"css" 			=> false
				),
				array(
					"type" 			=> "medialist_above",
					"heading" 		=> __("Sticky Above","oxygen"),
					"value" 		=> "page-width",
					"param_name" 	=> "sticky-media",
					"condition"		=> "sticky_header=yes",
					"css" 			=> false
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Sticky Box Shadow","oxygen"),
					"param_name" 	=> "sticky-box-shadow",
					"value" 		=> "0px 0px 10px rgba(0,0,0,0.3);",
					"condition"		=> "sticky_header=yes",
					"css" 			=> false
				),
			),
			'advanced' => array(
					"positioning" => array(
						"values" 	=> array(
							)
					)
			),
			'not_css_params' => array(
				'stack-header-vertically'
			)
		)
);