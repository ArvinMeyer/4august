<?php 

Class CT_Sidebar extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// remove component button
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		
		// add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// add toolbar folder
		add_action("ct_toolbar_sidebars_folder", array( $this, "sidebars_list") );
	}


	/**
	 * Add a [ct_sidebar] shortcode to WordPress
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function add_shortcode( $atts, $content ) {

		$options = $this->set_options( $atts );

		ob_start();

		if ( is_active_sidebar( $options["sidebar_id"] ) ) {
			dynamic_sidebar( $options["sidebar_id"] );
		}
		else {
			echo "No active \"".$options["sidebar_id"]."\" sidebar";
		}

		return ob_get_clean();
	}


	/**
	 * Display all sidebars
	 *
	 * @since  2.0
	 * @author Ilya K.
	 */

	function sidebars_list() {
		
		foreach ( $GLOBALS['wp_registered_sidebars'] as $id => $sidebar ) { ?>

			<div class="oxygen-add-section-element" title="<?php echo $sidebar['description']; ?>"
				ng-click="iframeScope.addSidebar('<?php echo $sidebar['id']; ?>')">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/sidebars.svg' />
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/sidebars-active.svg' />
				<?php echo $sidebar['name']; ?>
			</div>

		<?php }
	}
}


// Create inctance
$sidebar = new CT_Sidebar( array( 
			'name' 		=> 'Sidebar',
			'tag' 		=> 'ct_sidebar',
			'params' 	=> array(
					array(
						"type" 			=> "textfield",
						"param_name" 	=> "sidebar_id",
						"hidden" 		=> true,
						"css" 			=> false,
					),
				),
			'advanced' => false
			)
		); 