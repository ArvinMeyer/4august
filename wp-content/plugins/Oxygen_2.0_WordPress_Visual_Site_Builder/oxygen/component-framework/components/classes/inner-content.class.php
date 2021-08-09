<?php 

Class CT_Inner_Content extends CT_Component {

	var $shortcode_options;
	var $shortcode_atts;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		add_filter( 'template_include', array( $this, 'ct_innercontent_template'), 100 );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}
	}

	/**
	 * Add a [ct_inner_content] shortcode to WordPress
	 *
	 * @since 1.2.0
	 */

	function add_shortcode( $atts, $content, $name ) {
		
		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );

		$post_id = get_the_ID();

		ob_start();

		echo "<div id='" . esc_attr( $options['selector'] ) . "' class='" . esc_attr( $options['classes'] ) . "'>";

		$shortcodes = get_post_meta( $post_id, 'ct_builder_shortcodes', true );
		
		// for oxy urls, resolve them inline before doing the shortcodes

		$count = 0; // safety switch
		while(strpos($shortcodes, '[oxygen ') !== false && $count < 9) {
			$count++;
			$shortcodes = preg_replace_callback('/(\")(url|src|map_address|alt|background-image)(\":\"[^\"]*)\[oxygen ([^\]]*)\]([^\"\[\s]*)/i', 'ct_resolve_oxy_url', $shortcodes);
		}

		if(empty(trim($shortcodes))) {

			// find the template that has been assigned to innercontent
			$template = ct_get_inner_content_template();

			if($template) {
				$shortcodes = get_post_meta($template->ID, 'ct_builder_shortcodes', true);
			}

			if($shortcodes) {
				echo do_shortcode($shortcodes);
			}
			else {
				// RENDER default content
				if(function_exists('is_woocommerce') && is_woocommerce()) {
					woocommerce_content();	
				}
				else {
				    // Use WordPress post content as inner content
				    // if(!in_the_loop()) {
			            while ( have_posts() ) {
			                the_post();
			                the_content();
			            }
			        // }
			        // else {
			        // 	the_content();
			        // }
		        }
		    }

        } else {
		    // Use Oxygen designed inner content
            $content .= $shortcodes;
        }

        if ( ! empty( $content ) ) {

	        echo do_shortcode( $content );
        }

        echo "</div>";

		return ob_get_clean();
	}
	
	function ct_innercontent_template( $template ) {
		$new_template = '';

		if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_render_innercontent') {
			$nonce  	= $_REQUEST['nonce'];
			$post_id 	= $_REQUEST['post_id'];
			
			// check nonce
			if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
			    // This nonce is not valid.
			    die( 'Security check' );
			}
			
			if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'innercontent.php') ) {
				$new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'innercontent.php';
			}
		}

		if ( '' != $new_template ) {
				return $new_template ;
			}

		return $template;
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 0.1
	 */
	function component_button() { 

		$post_type = get_post_type();
		
		if ( $post_type != "ct_template") {
			return;
		} ?>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addComponent('<?php echo esc_attr( $this->options['tag'] ); ?>')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/section.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/section-active.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }
}




// Create instance
$html = new CT_Inner_Content( array( 
			'name' 		=> 'Inner Content',
			'tag' 		=> 'ct_inner_content',			
			'advanced' 	=> array(
					"positioning" => array(
						"values" 	=> array (
							'width' 	 => '100',
							'width-unit' => '%',
							)
					),
			        'allow_shortcodes' => true,
                )
			)
		);