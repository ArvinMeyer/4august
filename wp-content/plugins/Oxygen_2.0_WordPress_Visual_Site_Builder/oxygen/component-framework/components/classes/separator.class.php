<?php 

Class CT_Separator extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
	}


	/**
	 * Add a [ct_separator] shortcode to WordPress
	 *
	 * @since 0.3.1
	 */

	function add_shortcode( $atts, $content, $name ) {
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		ob_start();
		
		?><div class="ct-separator"></div><?php

		return ob_get_clean();
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 0.1
	 */
	function component_button() { 

		$template_type = get_post_meta( get_the_ID(), 'ct_template_type', true ); 

		if ( $template_type != "header_footer") {
			return;
		} ?>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>'); addSeparator();"
			ng-show="!separatorAdded">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/section.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/section-active.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }

}


// Create instance
$separator = new CT_Separator( array( 
				'name' 		=> 'Separator',
				'tag' 		=> 'ct_separator',
				'advanced' 	=> array(
				        'allowed_html'      => 'post',
                        'allow_shortcodes'  => false,
                )
			)
		);