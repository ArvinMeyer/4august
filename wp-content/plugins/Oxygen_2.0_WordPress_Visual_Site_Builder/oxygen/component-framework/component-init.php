<?php

require_once("admin/cpt-templates.php");
require_once("admin/admin.php");
require_once("admin/pages.php");
require_once("admin/svg-icons.php");
require_once("admin/import-export.php");
require_once("admin/updater/edd-updater.php");

require_once("includes/ajax.php");
require_once("includes/api.php");
require_once("includes/tree-shortcodes.php");
require_once("includes/templates.php");
require_once("includes/wpml-support.php");

require_once("includes/typekit/oxygen-typekit.php");
require_once("includes/selector-detector/selector-detector.php");


// init global API instance
global $oxygen_api;
$oxygen_api = new CT_API();

// init media queries sizes
global $media_queries_list;
$media_queries_list = array (
	"default" 	=> array (
					"maxSize" 	=> "100%",
					"title" 	=> "All devices"
				),
	
	"page-width" 	=> array (
					"maxSize" 	=> "", // set when actually use
					"title" 	=> "Page container and below"
				),

	"tablet" 	=> array (
					"maxSize" 	=> '992px', 
					"title" 	=> "Less than 992px"
				),
	"phone-landscape" 
				=> array (
					"maxSize" 	=> '768px', 
					"title" 	=> "Less than 768px"
				),
	"phone-portrait"
				=> array (
					"maxSize" 	=> '480px', 
					"title" 	=> "Less than 480px"
				),
);

global $media_queries_list_above;
$media_queries_list_above = array (
	"default" 	=> array (
					"minSize" 	=> "100%",
					"title" 	=> "All devices"
				),
	
	"page-width" 	=> array (
					"minSize" 	=> "", // set when actually use
					"title" 	=> "Above page container"
				),

	"tablet" 	=> array (
					"minSize" 	=> '992px', 
					"title" 	=> "Above than 992px"
				),
	"phone-landscape" 
				=> array (
					"minSize" 	=> '762px', 
					"title" 	=> "Above than 768px"
				),
	"phone-portrait"
				=> array (
					"minSize" 	=> '482px', 
					"title" 	=> "Above than 480px"
				),
);

// Include Signature Class
require_once("signature.class.php");

// Include Component Class
require_once("components/component.class.php");

// Include CSS Util
require_once("components/css-util.class.php");

// Add components in certain order
include_once("components/classes/section.class.php");
include_once("components/classes/div-block.class.php");
include_once("components/classes/new-columns.class.php");
include_once("components/classes/headline.class.php");
include_once("components/classes/text-block.class.php");
include_once("components/classes/rich-text.class.php");
include_once("components/classes/link-text.class.php");
include_once("components/classes/link-wrapper.class.php");
include_once("components/classes/link-button.class.php");
include_once("components/classes/image.class.php");
include_once("components/classes/video.class.php");
include_once("components/classes/svg-icon.class.php");
include_once("components/classes/fancy-icon.class.php");
include_once("components/classes/code-block.class.php");
include_once("components/classes/inner-content.class.php");
include_once("components/classes/slide.class.php");
include_once("components/classes/menu.class.php");
include_once("components/classes/shortcode.class.php");
include_once("components/classes/comments-list.class.php");
include_once("components/classes/comment-form.class.php");
include_once("components/classes/login-form.class.php");
include_once("components/classes/search-form.class.php");
include_once("components/classes/tabs-contents.class.php");
include_once("components/classes/tab.class.php");
include_once("components/classes/tab-content.class.php");

// Helpers
include_once("components/classes/header.class.php");
include_once("components/classes/header-row.class.php");
include_once("components/classes/header-row-center.class.php");
include_once("components/classes/header-row-left.class.php");
include_once("components/classes/header-row-right.class.php");
include_once("components/classes/social-icons.class.php");
include_once("components/classes/testimonial.class.php");
include_once("components/classes/icon-box.class.php");
include_once("components/classes/pricing-box.class.php");
include_once("components/classes/progress-bar.class.php");

include_once("components/classes/easy-posts.class.php");
include_once("components/classes/gallery.class.php");

include_once("components/classes/slider.class.php");
include_once("components/classes/tabs.class.php");
include_once("components/classes/superbox.class.php");
include_once("components/classes/toggle.class.php");

include_once("components/classes/map.class.php");
include_once("components/classes/soundcloud.class.php");

// not shown in fundamentals
include_once("components/classes/reusable.class.php");
include_once("components/classes/selector.class.php");
include_once("components/classes/separator.class.php");
include_once("components/classes/span.class.php");
include_once("components/classes/widget.class.php");
//include_once("components/classes/data.comment-form.class.php");
include_once("components/classes/sidebar.class.php");

// removed in v2.0
include_once("components/classes/columns.class.php");
include_once("components/classes/column.class.php");
include_once("components/classes/paragraph.class.php");
include_once("components/classes/ul.class.php");
include_once("components/classes/li.class.php");

include_once("includes/oxygen-dynamic-shortcodes.php");


add_action('admin_menu', 'oxygen_vsb_add_setup_wizard');

function oxygen_vsb_add_setup_wizard() {
	add_dashboard_page( '', '', 'manage_options', 'oxygen-vsb-setup', 'oxygen_vsb_setup_wizard_content' );

	if(isset($_GET['page']) && sanitize_text_field($_GET['page']) == 'oxygen-vsb-setup') {
		wp_enqueue_style( 'oxygen_vsb_setup_wizard_styles', CT_FW_URI . "/admin/setup_wizard.css");
	}
}

add_action( 'admin_notices', 'oxygen_vsb_admin_notice' ); 


function oxygen_vsb_admin_notice() {
    if( get_transient( 'oxygen-vsb-admin-notice-transient' ) ) {
        ?>
        <div class="updated notice is-dismissible">
            <p><?php echo get_transient( 'oxygen-vsb-admin-notice-transient' )?></p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'oxygen-vsb-admin-notice-transient' );
    }
    
    $ver = (float)phpversion();
	
	if($ver < 5.6) {
		?>
		<div class="updated error is-dismissible">
            <p>Error: your PHP version must be 5.6 or above to use Oxygen. Please contact your web hosting provider.</p>

			<p>Multiple years have passed since the PHP Group ceased support for versions of PHP below 5.6. If your web hosting provider's default PHP version is still below 5.6, you should switch to a modern, reliable, and secure web host.</p>
        </div>

		<?php
	}

    
}



function oxygen_vsb_is_touched_install() {
	
	$touched = false;

	if(get_option('ct_components_classes')) {
		$touched = true;
	}
	if(!$touched && get_option('ct_custom_selectors')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_global_settings')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_style_folders')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_style_sets')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_style_sheets')) {
		$touched = true;
	}

	return $touched;
	
}

function oxygen_vsb_setup_wizard_content() {
	global $ct_source_sites;
	?>
	<div id='oxygen-setup-wizard' class='oxygen-metabox'>
		<div class='inside'>

			<img src='https://oxygenapp.com/wp-content/uploads/2016/08/oxygen-logo-white-2.png' class='oxygen-setup-wizard-logo' />

			<div class='oxygen-wizard-wrapper'>

				<div class='oxygen-wizard-title'>
					<h1><?php esc_html_e( 'Welcome to Oxygen.', 'component-theme' ); ?></h1>
					<h1><?php esc_html_e( 'Please choose an installation type.', 'component-theme' ); ?></h1>
				</div>

				<div class='oxygen-wizard-content'>
					
					<div class='oxygen-wizard-install-types'>

						<div class='oxygen-wizard-install-type'>
							<h4><?php esc_html_e( 'Premade Website', 'component-theme' ); ?></h4>
							<h2><?php esc_html_e( 'Recommended', 'component-theme' ); ?></h2>
							<p><?php esc_html_e( 'Load a complete, premade website from our design library, then customize.', 'component-theme' ); ?></p>
							<div class="oxygen-wizard-button-bar">
							<?php
								$browse_library = add_query_arg('page', 'ct_install_wiz', get_admin_url());
								$default_install = $browse_library;

								if(isset($ct_source_sites['atomic'])) {
									$default_install = add_query_arg('default', 'atomic', $default_install);
								}
							?>
								<a href="<?php echo $default_install;?>" class="button button-large open-oxygen-button button-primary"><?php esc_html_e( 'Default Install', 'component-theme' ); ?></a>
								<a href="<?php echo $browse_library;?>" class="oxygen-wizard-other-website"><?php esc_html_e( 'Browse Library &raquo;', 'component-theme' ); ?></a>
							</div>
						</div>

						<div class='oxygen-wizard-install-type'>
							<h4><?php esc_html_e( 'Blank Installation', 'component-theme' ); ?></h4>
							<h2><?php esc_html_e( 'For Pro Designers', 'component-theme' ); ?></h2>
							<p><?php esc_html_e( 'Start with a completely blank canvas and build something from scratch.', 'component-theme' ); ?></p>
							<a href="<?php echo esc_url( admin_url() ); ?>" class="button button-large open-oxygen-button button-primary"><?php esc_html_e( 'Blank Install', 'component-theme' ); ?></a>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}

add_action('admin_init', 'oxygen_vsb_redirect_setup_wizard');

function oxygen_vsb_redirect_setup_wizard() {

	$isActivated = get_option('oxygen-vsb-activated');
	if(!$isActivated && get_transient('oxygen-vsb-just-activated') == '1') {
		delete_transient('oxygen-vsb-just-activated');
		add_option('oxygen-vsb-activated', true);
		wp_safe_redirect( admin_url( 'index.php?page=oxygen-vsb-setup' ) );
		exit;
	}
}

/**
 * Hook for addons to add fundamental components
 *
 * @since 1.4
 */
do_action("oxygen_after_add_components");


/**
 * Run plugin setup
 * 
 * @since 0.3.3
 * @author Ilya K.
 */

function ct_plugin_setup() {

	$oxygen_vsb_source_sites = "atomic => http://atomic.oxy.host\r\nsaas2 => http://saas2.oxy.host\r\nhyperion => http://hyperion.oxy.host/\r\ndentist => http://dentist.oxy.host\r\nbnb => http://bnb.oxy.host\r\nwinery => http://winery.oxy.host\r\nonepage2 => http://onepage2.oxy.host\r\nfinancial => http://financial.oxy.host\r\nfreelance => http://freelance.oxy.host/\r\n";
	update_option("oxygen_vsb_source_sites", $oxygen_vsb_source_sites );

	/**
	 * Setup default SVG Set
	 * 
	 */
	
	//delete_option("ct_svg_sets");
	$svg_sets = get_option("ct_svg_sets", array() );

	if ( empty( $svg_sets ) ) {

		$sets = array(
			"fontawesome" => "Font Awesome",
			"linearicons" => "Linearicons"
		);
		
		foreach ($sets as $key => $name) {
			
			// import default file	
			$file_content = file_get_contents( CT_FW_PATH . "/admin/includes/$key/symbol-defs.svg" );

			$xml = simplexml_load_string($file_content);

			foreach($xml->children() as $def) {
				if($def->getName() == 'defs') {

					foreach($def->children() as $symbol) {
						
						if($symbol->getName() == 'symbol') {
							$symbol['id'] = str_replace(' ', '', $name).$symbol['id'];
							
						}
					}
				}
				
			}
			$file_content = $xml->asXML();

			$svg_sets[$name] = $file_content;
		}

		// save SVG sets to DB
		update_option("ct_svg_sets", $svg_sets );
	}
}
add_action('admin_init', 'ct_plugin_setup', 9);


/**
 * Echo all components styles in one <style>
 * 
 * @since 0.1.6
 */

function ct_footer_styles_hook() {
	
	ob_start();
	do_action("ct_footer_styles");
	$ct_footer_css = ob_get_clean();

	if ( defined("SHOW_CT_BUILDER") ) {
		echo "<style type=\"text/css\" id=\"ct-footer-css\">\r\n";
		echo $ct_footer_css;
		echo "</style>\r\n";
	}
}


function ct_wp_link_dialog() {
    require_once ABSPATH . "wp-includes/class-wp-editor.php";
	_WP_Editors::wp_link_dialog();
}

/**
 * Check if we are in builder mode
 * 
 * @since 0.1
 * @author Ilya K.
 */

function ct_is_show_builder() {

	// check if builder activated
    if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] ) {

		if ( !is_user_logged_in() || !current_user_can( 'manage_options' )) { 
		   auth_redirect();
		}
		
		if ( !current_user_can('edit_pages') ) {
			wp_die(__('You do not have sufficient permissions to edit the layout', 'component-theme'));
		}
		
    	define("SHOW_CT_BUILDER", true);

    	add_action("wp_footer", "ct_wp_link_dialog");
		add_action("wp_head", "ct_footer_styles_hook");
		
		add_filter("document_title_parts", "ct_builder_wp_title", 10, 1);
    }

    // check if we are in iframe
    if ( isset( $_GET['oxygen_iframe'] ) && $_GET['oxygen_iframe'] ) {
    	define("OXYGEN_IFRAME", true);
    }

    // good place do define global classes list
	global $oxygen_vsb_css_classes;
	$oxygen_vsb_css_classes = get_option("ct_components_classes", array());
}
add_action('init','ct_is_show_builder', 1 );


/**
 * Callback for 'document_title_parts' filter
 *
 * @since ?
 * @author ?
 */

function ct_builder_wp_title( $title = array() ) {
 	$title['title'] = __( 'Oxygen Visual Editor', 'component-theme' ).(isset($title['title'])?' - '.$title['title']:'');
    return $title;
}

/**
 * Check if user has rights to open this post/page in builder
 * 
 * @since 1.0.1
 * @author Ilya K.
 */

function ct_check_user_caps() {

	// check if builder activated
    if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] ) {

    	// check if user is logged in
    	if ( !is_user_logged_in() ) {
			auth_redirect();
		}
		
		global $post;

		// if user can edit this post
		if ( $post !== null && ! current_user_can( 'edit_post', $post->ID ) ) {
			auth_redirect();
		}
    }
}
add_action('wp','ct_check_user_caps', 1 );

function ct_oxygen_admin_menu() {

	global $wp_admin_bar, $wp_the_query;

	$post = $wp_the_query->get_queried_object();

	if(is_admin())
		return;

	if(!current_user_can( 'manage_options' )) {
		return;
	}

	$wp_admin_bar->add_menu( array( 'id' => 'oxygen_admin_bar_menu', 'title' => __( 'Oxygen', 'component-theme' ), 'href' => FALSE ) );


	$post_id = false;
	$template = false;
	$is_template = false;
	// get archive template
	if ( is_archive() || is_search() || is_404() || is_home() || is_front_page() ) {

		if ( is_front_page() ) {
			$post_id 	= get_option('page_on_front');
		}
		else if ( is_home() ) {
			$post_id 	= get_option('page_for_posts');
		}
		else 
		{
			$template 	= ct_get_archives_template();

			if($template) {
				$is_template = true;
			}
		}
	} 
	
	if($post_id || (!$template && is_singular())) {
		
		if($post_id == false)
			$post_id = $post->ID;

		$ct_other_template = get_post_meta( $post_id, "ct_other_template", true );
		
		$template = false;
		
		if(!empty($ct_other_template) && $ct_other_template > 0) { // no template is specified
			// try getting default template
			$template = get_post($ct_other_template);
		}
		elseif($ct_other_template != -1) { // try getting default template if not explicitly set to not use any template at all
			if(intval($post_id) == intval(get_option('page_on_front')) || intval($post_id) == intval(get_option('page_for_posts')))
				$template = ct_get_archives_template( $post_id );
			else {
				$template = ct_get_posts_template( $post_id );

			}
		}

		if($template) {
			$is_template = true;
		} else {
			$is_template = false;
		}

	} elseif(!$template) {

		$template 	= ct_get_archives_template();

		if($template) {
			$is_template = true;
		}
	}
	
	$contains_inner_content = false;
	if($is_template) {
		$shortcodes = get_post_meta( $template->ID, "ct_builder_shortcodes", true );
		if($shortcodes) {
			$contains_inner_content = (strpos($shortcodes, '[ct_inner_content') !== false);
		}
	}

	if($is_template) {
		
		$postShortcodes = get_post_meta($post->ID, 'ct_builder_shortcodes', true);

		if($contains_inner_content && $postShortcodes) {
			$wp_admin_bar->add_menu( array( 'id' => 'edit_post_template', 'parent' => 'oxygen_admin_bar_menu', 'title' => __( 'Edit with Oxygen', 'component-theme' ), 'href' => esc_url(ct_get_post_builder_link( $post->ID )).(($shortcodes && strpos($shortcodes, '[ct_inner_content') !== false)?'&ct_inner=true':'')) );
		}
		else {
			$wp_admin_bar->add_menu( array( 'id' => 'edit_template', 'parent' => 'oxygen_admin_bar_menu', 'title' => __( 'Edit '.$template->post_title.' Template', 'component-theme' ), 'href' => esc_url(get_edit_post_link( $template->ID )) ) );
		}
	}
	else {
		if(is_object($post)) {
			$wp_admin_bar->add_menu( array( 'id' => 'edit_post_template', 'parent' => 'oxygen_admin_bar_menu', 'title' => __( 'Edit with Oxygen', 'component-theme' ), 'href' => esc_url(ct_get_post_builder_link( $post->ID ))) );
		}
	}

}

add_action( 'admin_bar_menu', 'ct_oxygen_admin_menu', 1000 );

/**
 * Set CT parameters to recognize on fronted and builder
 * 
 * @since 0.2.0
 * @author Ilya K.
 */

function ct_editing_template() {

    if ( get_post_type() == "ct_template" ) {

    	define("OXY_TEMPLATE_EDIT", true);
		
		// below returns nothing since 2.0 Do we need to remove this?
    	$template_type = get_post_meta( get_the_ID(), 'ct_template_type', true );

    	if ( $template_type != "reusable_part" ) {
    		define("CT_TEMPLATE_EDIT", true);	
    	}

    	if ( $template_type == "archive" ) {
    		define("CT_TEMPLATE_ARCHIVE_EDIT", true);	
    	}

    	if ( $template_type == "single_post" ) {
    		define("CT_TEMPLATE_SINGLE_EDIT", true);	
    	}
    }
}
add_action('wp','ct_editing_template', 1 );


/**
 * Get current request URL
 * 
 * @since ?
 * @author gagan goraya
 */

function ct_get_current_url($more_query) {

	$request_uri = '';

	$request = explode('?', $_SERVER["REQUEST_URI"]);

	if(isset($request[1])) {
		$request_uri = $_SERVER["REQUEST_URI"].'&'.$more_query;
	}
	else {
		$request_uri = $_SERVER["REQUEST_URI"].'?'.$more_query;	
	}

	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	//if ($_SERVER["SERVER_PORT"] != "80") {
	//  $pageURL .= $_SERVER["HTTP_HOST"].":".$_SERVER["SERVER_PORT"].$request_uri;
	//} else {
	  $pageURL .= $_SERVER["HTTP_HOST"].$request_uri;
	//}
	
	return $pageURL;
}


/**
 * Include Scripts and Styles for frontend and builder
 * 
 * @since 0.1
 * @author Ilya K.
 */

function ct_enqueue_scripts() {

	// include normalize.css
	wp_enqueue_style("normalize", CT_FW_URI . "/vendor/normalize.css");

	wp_enqueue_style("oxygen", CT_FW_URI . "/style.css", array(), CT_VERSION );

	wp_enqueue_script("jquery");

	/**
	 * Add-on hook for scripts that should be displayed both frontend and builder
	 *
	 * @since 1.4
	 */
	do_action("oxygen_enqueue_scripts");

	// only for frontend
	if ( !defined("SHOW_CT_BUILDER") ) {

		/**
		 * Add-on hook
		 *
		 * @since 1.4
		 */
		do_action("oxygen_enqueue_frontend_scripts");

		// check whether to load universal css or not
		if ( get_option("oxygen_vsb_universal_css_cache")=='true' && get_option("oxygen_vsb_universal_css_cache_success")==true 
			 // TODO: check if there other are cases that may load universal CSS into builder
			 && stripslashes($_REQUEST['action']) !== 'ct_render_widget' ) {
			
			wp_enqueue_style("oxygen-styles", ct_get_current_url('xlink=css&nouniversal=true') );
			
			$universal_css_url = get_option('oxygen_vsb_universal_css_url');
			$universal_css_url = add_query_arg("cache", get_option("oxygen_vsb_last_save_time"), $universal_css_url);
			
			wp_enqueue_style("oxygen-universal-styles",  $universal_css_url);
		}
		else {
			wp_enqueue_style("oxygen-styles", ct_get_current_url( 'xlink=css' ) );
		}

		// anything beyond this is for builder
		return;
	}

	// include Unslider
	wp_enqueue_script( 'oxygen-unslider', 		CT_FW_URI . '/vendor/unslider/unslider-min.js');
	wp_enqueue_script( 'oxygen-event-move', 	CT_FW_URI . '/vendor/unslider/jquery.event.move.js');
	wp_enqueue_script( 'oxygen-event-swipe', 	CT_FW_URI . '/vendor/unslider/jquery.event.swipe.js');
	wp_enqueue_style ( 'oxygen-unslider', 		CT_FW_URI . '/vendor/unslider/unslider.css');

	// Font Loader
	wp_enqueue_script("font-loader", "//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js");

	// jQuery UI
	wp_enqueue_script("jquery-ui", "//code.jquery.com/ui/1.11.3/jquery-ui.js", array(), '1.11.3');
	wp_enqueue_style("jquery-ui-css", "//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css", array());

	wp_enqueue_script('jquery-ui-sortable');

	// WordPress Media
	wp_enqueue_media();

	// link manager
	wp_enqueue_script( 'wplink' );
	wp_enqueue_style( 'editor-buttons' );

	// add Gravity Forms if registered
	wp_enqueue_script( 'gform_gravityforms' );

	// FontAwesome
	wp_enqueue_style("font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css", array(), '4.3.0');

	// AngularJS
	wp_enqueue_script("angular", 			"//ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular.js", array(), '1.4.2');
	wp_enqueue_script("angular-animate", 	"//ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular-animate.js", array(), '1.4.2');

	// Dragula
 	wp_enqueue_script("dragula", 						CT_FW_URI . "/vendor/dragula/angular-dragula.js");
	wp_enqueue_style ("dragula", 						CT_FW_URI . "/vendor/dragula/dragula.min.css");

	// Select2
 	wp_enqueue_script( 'select2', CT_FW_URI . "/vendor/select2/select2.full.min.js", array( 'jquery' ) );
    wp_enqueue_style ( 'select2', CT_FW_URI . "/vendor/select2/select2.min.css" );

	// nuSelectable
	//wp_enqueue_script("nu-selectable", 					CT_FW_URI . "/vendor/nuSelectable/jquery.nu-selectable.js");

	// Codemirror
	wp_enqueue_script("ct-codemirror", 					CT_FW_URI . "/vendor/codemirror/codemirror.js");
	wp_enqueue_style ("ct-codemirror", 					CT_FW_URI . "/vendor/codemirror/codemirror.css");

	wp_enqueue_script("ui-codemirror", 					CT_FW_URI . "/vendor/ui-codemirror/ui-codemirror.js");

	wp_enqueue_script("ct-codemirror-html",				CT_FW_URI . "/vendor/codemirror/htmlmixed/htmlmixed.js");
	wp_enqueue_script("ct-codemirror-xml",				CT_FW_URI . "/vendor/codemirror/xml/xml.js");
	wp_enqueue_script("ct-codemirror-js", 				CT_FW_URI . "/vendor/codemirror/javascript/javascript.js");
	wp_enqueue_script("ct-codemirror-css",				CT_FW_URI . "/vendor/codemirror/css/css.js");
	wp_enqueue_script("ct-codemirror-clike",			CT_FW_URI . "/vendor/codemirror/clike/clike.js");
	wp_enqueue_script("ct-codemirror-php",				CT_FW_URI . "/vendor/codemirror/php/php.js");
	
	wp_enqueue_script("ct-common-directives",			CT_FW_URI . "/angular/common.directives.js", array(), CT_VERSION);

	wp_enqueue_script("ct-ui-sortable",					CT_FW_URI . "/vendor/ui-sortable/sortable.js", array(), CT_VERSION);

	// iframe files
	if ( defined("OXYGEN_IFRAME") ) {

		// drag-and-drop-lists library
		wp_enqueue_script("ct-angular-dragdroplist", CT_FW_URI . "/vendor/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js");

		wp_enqueue_style ("ct-iframe", 						CT_FW_URI . "/toolbar/UI/css/iframe.css");

		wp_enqueue_script("ct-angular-main", 				CT_FW_URI . "/angular/controllers/controller.main.js", 			array(), CT_VERSION);
		wp_enqueue_script("ct-angular-tree", 				CT_FW_URI . "/angular/controllers/controller.tree.js", 			array(), CT_VERSION);
		wp_enqueue_script("ct-angular-states", 				CT_FW_URI . "/angular/controllers/controller.states.js", 		array(), CT_VERSION);
		wp_enqueue_script("ct-angular-navigation", 			CT_FW_URI . "/angular/controllers/controller.navigation.js", 	array(), CT_VERSION);
		wp_enqueue_script("ct-angular-columns", 			CT_FW_URI . "/angular/controllers/controller.columns.js", 		array(), CT_VERSION);
		wp_enqueue_script("ct-angular-ajax", 				CT_FW_URI . "/angular/controllers/controller.ajax.js", 			array(), CT_VERSION);
		wp_enqueue_script("ct-angular-header-builder", 		CT_FW_URI . "/angular/controllers/controller.header.js",		array(), CT_VERSION);
		wp_enqueue_script("ct-angular-classes", 			CT_FW_URI . "/angular/controllers/controller.classes.js", 		array(), CT_VERSION);
		wp_enqueue_script("ct-angular-options", 			CT_FW_URI . "/angular/controllers/controller.options.js", 		array(), CT_VERSION);
		wp_enqueue_script("ct-angular-fonts", 				CT_FW_URI . "/angular/controllers/controller.fonts.js", 		array(), CT_VERSION);
		wp_enqueue_script("ct-angular-svg", 				CT_FW_URI . "/angular/controllers/controller.svg.js", 			array(), CT_VERSION);
		wp_enqueue_script("ct-angular-css",					CT_FW_URI . "/angular/controllers/controller.css.js", 			array(), CT_VERSION);
		wp_enqueue_script("ct-angular-templates",			CT_FW_URI . "/angular/controllers/controller.templates.js", 	array(), CT_VERSION);
		wp_enqueue_script("ct-angular-media-queries",		CT_FW_URI . "/angular/controllers/controller.media-queries.js", array(), CT_VERSION);
		wp_enqueue_script("ct-angular-api",					CT_FW_URI . "/angular/controllers/controller.api.js", 			array(), CT_VERSION);
		wp_enqueue_script("ct-angular-drag-drop",			CT_FW_URI . "/angular/controllers/controller.dragdroplists.js", array(), CT_VERSION);
		
		wp_enqueue_script("ct-angular-directives",			CT_FW_URI . "/angular/builder.directives.js", 					array(), CT_VERSION);
		wp_enqueue_script("ct-angular-slider-directive", 	CT_FW_URI . "/angular/slider.directive.js",						array(), CT_VERSION);

		/**
		 * Add-on hook
		 *
		 * @since 1.4
		 */
		do_action("oxygen_enqueue_iframe_scripts");
	}
	else {
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script(
	        'iris',
	        admin_url( 'js/iris.min.js' ),
	        array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
	        false,
	        1
	    );
	    
		wp_enqueue_script(
	        'wp-color-picker',
	        admin_url( 'js/color-picker.min.js' ),
	        //CT_FW_URI . '/vendor/alpha-color-picker/color-picker.min.js',
	        array( 'iris' ),
	        false,
	        1
	    );
	    wp_enqueue_script(
	        'ct-color-picker',
	        //admin_url( 'js/color-picker.min.js' ),
	        CT_FW_URI . '/vendor/alpha-color-picker/color-picker.min.js',
	        array( 'iris', 'wp-color-picker' ),
	        false,
	        1
	    );

	    $colorpicker_l10n = array(
	        'clear' => __( 'Clear' ),
	        'defaultString' => __( 'Default' ),
	        'pick' => __( 'Select Color' ),
	        'current' => __( 'Current Color' ),
	    );
	    wp_localize_script( 'ct-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

	    wp_enqueue_script(
			'alpha-color-picker',
			CT_FW_URI . '/vendor/alpha-color-picker/alpha-color-picker.js', // Update to where you put the file.
			array( 'jquery', 'ct-color-picker' ), // You must include these here.
			null,
			true
		);

		wp_enqueue_style(
			'alpha-color-picker',
			CT_FW_URI . '/vendor/alpha-color-picker/alpha-color-picker.css', // Update to where you put the file.
			array( 'wp-color-picker' ) // You must include these here.
		);

		wp_enqueue_script("ct-angular-ui", 			CT_FW_URI . "/angular/controllers/controller.ui.js", array('alpha-color-picker'), CT_VERSION);
		wp_enqueue_script("ct-angular-drag-n-drop",	CT_FW_URI . "/angular/controllers/controller.drag-n-drop.js", 	array(), CT_VERSION);
		wp_enqueue_script("ct-slider-controller", 	CT_FW_URI . "/angular/controllers/controller.slider.js", 		array(), CT_VERSION);


		// jQuery menu aim to be used in the library add plus
		wp_enqueue_script('ct-jquery-menu-aim', CT_FW_URI . "/vendor/jquery-menu-aim/jquery.menu-aim.js", array('jquery'), CT_VERSION);
		/**
		 * Add-on hook
		 *
		 * @since 1.4
		 */
		do_action("oxygen_enqueue_ui_scripts");
	}

	// Add some variables needed for AJAX requests
	global $post;
	global $wp_query;

	$options = array ( 
		'ajaxUrl' 		=> admin_url( 'admin-ajax.php' ),
		'permalink' 	=> get_permalink(),
		'frontendURL' 	=> get_permalink(),
		'query' 		=> $wp_query->query,
		'googleMapsAPIKey' => get_option('oxygen_vsb_google_maps_api_key', '')
	);

	// verify http vs https for hosts that force https
	$siteurl = get_option('siteurl');
	$is_https = (strpos($siteurl, "https://") === 0) ? "1" : "0";

	if ($is_https==="0") {
		$options['frontendURL'] = str_replace("https://", "http://", $options['frontendURL']);
	}
	
	if($post) {
		$postid = $post->ID;

		if (is_front_page()) {
			$postid 		= get_option('page_on_front');
		}
		else if(is_home()) {
			$postid 		= get_option('page_for_posts');
		}

		$nonce = wp_create_nonce( 'oxygen-nonce-' . $postid );

		$options['postId'] 	= $postid;
		$options['nonce'] 	= $nonce;
	}

	if ( defined("OXY_TEMPLATE_EDIT") ) {
		$options["oxyTemplate"] = true;
	}

	// below 3 constanst never defined since 2.0
	if ( defined("CT_TEMPLATE_EDIT") ) {
		$options["ctTemplate"] = true;
	}

	if ( defined("CT_TEMPLATE_ARCHIVE_EDIT") ) {
		$options["ctTemplateArchive"] = true;
	}

	if ( defined("CT_TEMPLATE_SINGLE_EDIT") ) {
		$options["ctTemplateSingle"] = true;
	}

	$options["ctSiteUrl"] 			= get_site_url();
	$options["oxyFrameworkURI"] 	= CT_FW_URI;

	global $ct_component_categories;
	if ( isset($ct_component_categories) ) {
		$options["componentCategories"] = $ct_component_categories;
	}

	// provide the meta keys to the builder
	global $wpdb, $oxygen_meta_keys;

	$query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key) 
        FROM $wpdb->postmeta WHERE 1;
    ";

    $meta = $wpdb->get_results($query);

	if(is_array($meta)) {

		function oxygen_return_meta_keys($val) {
			return $val->meta_key;
		}

		function oxygen_filter_meta_keys($val) {
			return (strpos($val, '_') !== 0);
		}

		$oxygen_meta_keys = array_map('oxygen_return_meta_keys', $meta);

		// filter out keys starting with _

		$oxygen_meta_keys = array_filter($oxygen_meta_keys, 'oxygen_filter_meta_keys');
	}

	$options["oxygenMetaKeys"] = $oxygen_meta_keys;

	wp_localize_script( "ct-angular-main", 'CtBuilderAjax', $options);

	wp_localize_script("wplink", 'ajaxurl', $options['ajaxUrl']);


}
add_action( 'wp_enqueue_scripts', 'ct_enqueue_scripts' );


/**
 * Init
 * 
 * @since 0.2.5
 */

function ct_init() {

	// check if builder activated
    if ( defined("SHOW_CT_BUILDER") ) {
    	add_action("ct_builder_ng_init", "ct_init_default_options");
    	add_action("ct_builder_ng_init", "ct_init_default_values");
    	add_action("ct_builder_ng_init", "ct_init_not_css_options");
    	add_action("ct_builder_ng_init", "ct_init_options_white_list");
    	add_action("ct_builder_ng_init", "ct_init_allowed_empty_options_list");
    	add_action("ct_builder_ng_init", "ct_init_nice_names");
    	add_action("ct_builder_ng_init", "ct_init_settings");
    	add_action("ct_builder_ng_init", "ct_init_components_classses");
    	add_action("ct_builder_ng_init", "ct_init_custom_selectors");
    	add_action("ct_builder_ng_init", "ct_init_style_sheets");
    	add_action("ct_builder_ng_init", "ct_init_api_token");
    	add_action("ct_builder_ng_init", "ct_init_api_components");
    	add_action("ct_builder_ng_init", "ct_init_folders");
    	add_action("ct_builder_ng_init", "ct_init_elegant_custom_fonts");
    	
    	add_action("ct_builder_ng_init", "ct_components_tree_init", 100 );

    	// Include Toolbar
    	if ( !defined("OXYGEN_IFRAME") ) {
			require_once("toolbar/toolbar.class.php");
    	}
    }
}
add_action('init','ct_init', 2);


/**
 * Make API call to get the token and output to builder
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_init_api_token() {
		
	global $oxygen_api;
	$user = wp_get_current_user();
	//delete_transient('oxygen-token-check-user-' . $user->ID);
	$token_check = get_transient( 'oxygen-token-check-user-' . $user->ID );
	
	if ( ! $token_check ) {

		$token_check = $oxygen_api->check_api_token();

		if ( isset ( $token_check["ID"] ) ) {
			set_transient( 'oxygen-token-check-user-' . $user->ID, $token_check, 12 * HOUR_IN_SECONDS );
		}
	}
		
	$auth_info = array (
			"token_check" => $token_check
		);

	echo "authInfo=" . htmlspecialchars( json_encode( $auth_info ), ENT_QUOTES ) . ";";
}


/**
 * Get list of all components
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_init_api_components() {

	global $api_components;
	global $api_pages;
	global $api_design_sets;

	global $experimental_components;

	//unset( $api_components["status"] );
	$api_components = htmlspecialchars( json_encode( $api_components ) );
	echo "api_components=$api_components;";

	//unset( $api_pages["status"] );
	$api_pages = htmlspecialchars( json_encode( $api_pages ) );
	echo "api_pages=$api_pages;";

	$api_design_sets = htmlspecialchars( json_encode( $api_design_sets ) );
	echo "api_design_sets=$api_design_sets;";

	//unset( $api_components["status"] );
	// $components = array();
	// foreach($experimental_components as $item) {
	// 	$components = array_merge($components, $item['items']);
	// }

	// $components = htmlspecialchars( json_encode( $components ) );
	$experimental_components = htmlspecialchars( json_encode( $experimental_components ) );
	echo "experimental_components=$experimental_components;";
}


/**
 * Make folders structure availbale on frontend
 *
 * @since 0.4.0
 */

function ct_init_folders() {

	global $oxygen_add_plus;

	$output = json_encode( $oxygen_add_plus );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "folders=$output;";
}


/**
 * Make folders structure availbale on frontend
 *
 * @since 0.4.0
 */

function ct_init_elegant_custom_fonts() {

	if (class_exists('ECF_Plugin')) {
		$font_family_list = ECF_Plugin::get_font_families();
	} else {
		$font_family_list = "";
	}

	$output = json_encode( $font_family_list );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "elegantCustomFonts=$output;";
}


/**
 * Get categories, pages, components
 *
 * @since 1.0.1
 * @author Ilya K.
 */

function ct_get_base() {

	if ( ! defined("SHOW_CT_BUILDER") ) {
		return;
	}
	
	global $oxygen_api;
	
	global $oxygen_add_plus;
	// global $api_components;
	// global $api_pages;
	// global $api_design_sets;

	global $experimental_components;
	global $ct_source_sites;

	// $response = $oxygen_api->get_base();

	
	$experimental_components = array();
	foreach($ct_source_sites as $key => $source) {

		$experimental_components[$key] = array(
			'id' => 0,
			'name' => $key,
			'type' => 'folder',
			'fresh' => true,
			'items' => array()//$json_components
		);	
	}

	


	// if ( $response["status"] == "ok" ) {
	// 	$api_pages 			= $response["pages"];
	// 	$api_components 	= $response["components"];
	// 	$categories 		= $response["categories"];
	// 	$page_categories 	= $response["page_categories"];
	// 	$api_design_sets 	= $response["design_sets"];
	// } 
	// else {
	// 	$api_pages 			= array();
	// 	$api_components 	= array();
	// 	$categories 		= array();
	// 	$page_categories 	= array();
	// 	$api_design_sets 	= array();
	// }

	// build Add+ section
	// $components = array();
	// $components["id"] 			= "components";
	// $components["name"] 		= "Components";

	$experimental = array();
	$experimental["id"]		= "experimental";
	$experimental["name"]	= "Design Sets";
	$experimental["children"] = $experimental_components;

	// $design_sets = array();
	// $design_sets["id"] 	 		= "design_sets";
	// $design_sets["name"] 		= "Design Sets old";
	// $design_sets["children"] 	= array();

	$libraryCats = array();
	$libraryCats["id"] 	 		= "categories";
	$libraryCats["name"] 		= "Categories";
	$libraryCats["children"] 	= array();
	
	// Components
	// if(is_array($api_components)) {
	// 	foreach ($api_components as $key => $component) {
	// 		$component = (array) $component;
			
	// 		if ( !isset( $design_sets["children"][$component["design_set_id"]] ) ) {
	// 			$design_sets["children"][$component["design_set_id"]] = array(
	// 				"id" => $component["design_set_id"],
	// 				"name" => $component["design_set_name"],
	// 				"children" => array()
	// 			);
	// 		}

	// 		// create components array first time
	// 		if ( !isset   ( $design_sets["children"][$component["design_set_id"]]["children"]["component"] ) ||
	// 			 !is_array( $design_sets["children"][$component["design_set_id"]]["children"]["component"] ) ) {
	// 			$design_sets["children"][$component["design_set_id"]]["children"]["component"] = array(
	// 				"name" 	=> "Components",
	// 				"type" 	=> "component",
	// 				"id" 	=> $component["design_set_id"],
	// 				"items" => array()
	// 			);
	// 		}

	// 		// push component to certain design set
	// 		$design_sets["children"][$component["design_set_id"]]["children"]["component"]["items"][] = $component;

	// 		// add to categories array
	// 		if(is_array($categories)) {
	// 			foreach ($categories as $key => $category) {
	// 				if ( !isset( $categories[$key]["items"] ) ) {
	// 					$categories[$key]["items"] = array();
	// 				}

	// 				// add component
	// 				if ( $category["id"] == $component["category_id"] ) {
	// 					$categories[$key]["items"][] = $component;
	// 				}
	// 			}
	// 		}
	// 	}
	// }
	
	// build categories tree
	// $new = array();

	// if(is_array($categories)) {
	// 	foreach ($categories as $category){
	// 	    $new[$category['parent_id']][] = $category;
	// 	}
	// }

	// function ct_build_categories_tree(&$list, $parent){
	// 	$tree = array();
	// 	if(is_array($parent)) {
	// 		foreach ($parent as $k=>$l){
	// 			if(isset($list[$l['id']])){
	// 				$l['children'] = ct_build_categories_tree($list, $list[$l['id']]);
	// 			}
	// 			$tree[] = $l;
	// 		}
	// 	}
	// 	return $tree;
	// }

	// if(isset($new[0])) {
	// 	$tree = ct_build_categories_tree($new, $new[0]);
	// 	$components["children"] = $tree;
	// }

	//if (isset($api_pages["status"]) && $api_pages["status"] != "error") {
		// Pages
		// if(is_array($api_pages)) {
		// 	foreach ($api_pages as $key => $page) {

		// 		$page = (array) $page;

		// 		// create pages array first time
		// 		if (!isset   ( $design_sets["children"][$page["design_set_id"]]["children"]["page"] ) || 
		// 			!is_array( $design_sets["children"][$page["design_set_id"]]["children"]["page"] ) ) {
		// 			$design_sets["children"][$page["design_set_id"]]["children"]["page"] = array(
		// 				"name" 	=> "Pages",
		// 				"type" 	=> "page",
		// 				"id" 	=> $page["design_set_id"],
		// 				"items" => array()
		// 			);
		// 		}

		// 		// check pages category
		// 		if ( $page["category_id"] ) {

		// 			// create category folder for the first time
		// 			if (!isset   ($design_sets["children"][$page["design_set_id"]]["children"]["page"]["children"][$page["category_id"]]) || 
		// 				!is_array($design_sets["children"][$page["design_set_id"]]["children"]["page"]["children"][$page["category_id"]]) ) {
		// 				// add to categories array
		// 				if(is_array($page_categories)) {
		// 					foreach ($page_categories as $key => $category) {
		// 						// add component
		// 						if ( $category["id"] == $page["category_id"] ) {
		// 							$name = $category["name"];
		// 						}
		// 					}
		// 				}
						
		// 				$design_sets["children"][$page["design_set_id"]]["children"]["page"]["children"][$page["category_id"]] 
		// 				= array( 	
		// 					"id" 	=> $page["category_id"]."_".$page["design_set_id"],
		// 					"type" 	=> "page",
		// 					"name" 	=> $name );
		// 			}

		// 			$design_sets["children"][$page["design_set_id"]]["children"]["page"]["children"][$page["category_id"]]["items"][] = $page;
		// 		}
		// 		else {
		// 			// push page to certain design set
		// 			$design_sets["children"][$page["design_set_id"]]["children"]["page"]["items"][] = $page;
		// 		}
		// 	}
		// }
	//}

	$installedSet = get_option('ct_last_installed_default_data', false);

	if($installedSet) {
	
		ob_start();?>

			<div class="oxygen-add-section-subsection" ng-click="iframeScope.openLoadFolder('<?php echo $installedSet ;?>-0', '<?php echo $installedSet ;?>', true, $event)">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-generic.svg" class="oxygen-add-section-subsection-icon">
				<?php echo $installedSet ;?>									<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-arrow.svg">
			</div>

		<?php

		$installedSet = array('key' => $installedSet, 'code' => ob_get_clean());

	}

	$children =	array(
			// "components" => $components, 
			// "design_sets" => $design_sets,
			"experimental" => $experimental,
			//"categories" => $libraryCats
			 );
	
	if($installedSet) {
		$children = array_merge(array($installedSet['installedSet'] => $installedSet), $children);
	}

	$oxygen_add_plus = array(
			"status" 	=> false,
			"library" => array(
							"name" 	=> "Library",
							"children" => $children));

	// if ( $response["status"] == "error" && is_array($response["error"]["errors"])) {
	// 	$oxygen_add_plus["errors"] = reset($response["error"]["errors"]);
	// }

	// make design sets ids to be keys
	// foreach ( $api_design_sets as $design_set ) {
	//     $new_design_sets[$design_set['id']] = $design_set;
	// }
	// if ( isset($new_design_sets) && is_array( $new_design_sets ) ) {
	// 	$api_design_sets = $new_design_sets;
	// }

	// if ( $response["status"] == "error" && isset($response["message"]) ) {
	// 	$oxygen_add_plus["message"] = $response["message"];
	// }
}
add_action("wp", "ct_get_base");



/**
 * Output all Components (shortcodes) default params to ng-init directive
 *
 * @since 0.1
 */

function ct_init_default_options() {

	$components = apply_filters( "ct_component_default_params", array() );

	$all_defaults = call_user_func_array('array_merge', $components);

	$components["all"] = $all_defaults;

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "defaultOptions = $output;";
}


/**
 *
 * 
 * @since 2.0
 * @author Gagan
 */

function ct_init_default_values() {

	$components = apply_filters( "ct_component_default_values", array() );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "defaultValues = $output;";
}


/**
 * Output array of all not CSS options for each component
 *
 * @since 0.3.2
 */

function ct_init_not_css_options() {

	$components = apply_filters( "ct_not_css_options", array() );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "notCSSOptions = $output;";
}


/**
 * Output white list options to be used for media/states/clases 
 *
 * @since 2.0
 */

function ct_init_options_white_list() {

	$components = apply_filters( "oxy_options_white_list", CT_Component::$options_white_list );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "optionsWhiteList = $output;";

	$components = apply_filters( "oxy_options_white_list_no_media", CT_Component::$options_white_list_no_media );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "optionsWhiteListNoMedia = $output;";
}


/**
 * Output options that can be be unset/empty
 *
 * @since 2.0
 */

function ct_init_allowed_empty_options_list() {

	$components = apply_filters( "oxy_allowed_empty_options_list", CT_Component::$allowed_empty_options_list );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "allowedEmptyOptions=$output;";
}


/**
 * Pass Components Tree JSON to ng-init directive
 *
 * @since 0.1
 */

function ct_components_tree_init() {

	echo "init();";
}


/**
 * Output Components nice names
 *
 * @since 0.1.2
 */

function ct_init_nice_names() {

	$names = apply_filters( "ct_components_nice_names", array() );

	$names['root'] = "Root";

	$output = json_encode($names);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "niceNames = $output;";
}


/**
 * Output Page and Global Settings
 *
 * @since 0.1.3
 */

function ct_init_settings() { 

	// Page settings
	$output = json_encode( ct_get_page_settings( get_the_ID() ) );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "pageSettings = $output;";

	// Global settings
	$output = json_encode(ct_get_global_settings());
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "globalSettings = $output;";

	// Global defaults
	$output = json_encode(ct_get_global_settings(true));
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "globalSettingsDefaults = $output;";
}


/**
 * Output CSS Classes
 *
 * @since 0.1.7
 */

function ct_init_components_classses() { 
	
	$classes = ct_get_components_classes();

	$output = json_encode( $classes, JSON_FORCE_OBJECT );

	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "classes = $output;";
}

function ct_get_components_classes($return_js = false) {

	global $oxygen_vsb_css_classes;

	$classes = $oxygen_vsb_css_classes;

	if ( ! is_array( $classes ) )
		return array();
	
	// base64_decode the custom-css and custom-js
	$classes = ct_base64_decode_selectors($classes, $return_js);

	return $classes;
}


/**
 * base64 decode classes and custom selectors custom ccs/js
 *
 * @since 1.3
 * @author Ilya/Gagan
 */

function ct_base64_decode_selectors($selectors, $return_js = false) {

	$selecotrs_js = array();

	foreach($selectors as $key => $class) {
		foreach($class as $statekey => $state) {
			if($statekey == 'media') {
				foreach($state as $bpkey => $bp) {
					foreach($bp as $bpstatekey => $bpstate) {
						if(isset($bpstate['custom-css']) && strpos($bpstate['custom-css'], ' ') === false && strpos($bpstate['custom-css'], ':') === false)
		  					$selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-css'] = base64_decode($bpstate['custom-css']);
		  				if(isset($bpstate['custom-js'])) {
		  					if(strpos($bpstate['custom-js'], ' ') === false && strpos($bpstate['custom-js'], '(') === false)
		  						$selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-js'] = base64_decode($bpstate['custom-js']);
		  					// output js to the footer
		  					$classes_js[implode("_", array($key, $statekey, $bpkey, $bpstatekey))] = $states[$key][$mediakey][$mediastatekey]['custom-js'];	
		  				}
					}
				}
			}
			else {
		  		if(isset($class[$statekey]['custom-css']) && strpos($class[$statekey]['custom-css'], ' ') === false && strpos($class[$statekey]['custom-css'], ':') === false)
		  			$selectors[$key][$statekey]['custom-css'] = base64_decode($class[$statekey]['custom-css']);
		  		if(isset($class[$statekey]['custom-js'])) {
		  			if(strpos($class[$statekey]['custom-js'], ' ') === false && strpos($class[$statekey]['custom-js'], '(') === false)
						$selectors[$key][$statekey]['custom-js'] = base64_decode($class[$statekey]['custom-js']);
		  			
		  			// output js to the footer
		  			$selecotrs_js[implode("_", array($key, $statekey))] = $selectors[$key][$statekey]['custom-js'];
		  		}
		  	}
	  	}
  	}

  	if($return_js)
  		return $selecotrs_js;
  	else
  		return $selectors;
}


/**
 * Init custom selectors styles
 *
 * @since 1.3
 */

function ct_init_custom_selectors() {
	
	//update_option( "ct_custom_selectors", array() );
	$selectors = get_option( "ct_custom_selectors", array() );

	// make sure this is an array if we have empty string saved somehow
	if ($selectors == "") {
		$selectors = array();
	}

	$selectors = ct_base64_decode_selectors($selectors);

	$selectors = json_encode( $selectors, JSON_FORCE_OBJECT );
	$selectors = htmlspecialchars( $selectors, ENT_QUOTES );
	
	echo "customSelectors = $selectors;";

	$style_sets = get_option( "ct_style_sets", array() );

	// make sure this is an array if we have empty string saved somehow
	if ($style_sets == "") {
		$style_sets = array();
	}

	// if it does not contain default style set, add it
	if(!isset($style_sets['Uncategorized Custom Selectors']) || !isset($style_sets['Uncategorized Custom Selectors']['key'])) {
		$style_sets['Uncategorized Custom Selectors'] = array(
			'key' => 'Uncategorized Custom Selectors'
		);
	}

	$style_sets = json_encode( $style_sets, JSON_FORCE_OBJECT );
	$style_sets = htmlspecialchars( $style_sets, ENT_QUOTES );

	echo "styleSets=$style_sets;";

	$style_folders = get_option( "ct_style_folders", array() );

	// make sure this is an array if we have empty string saved somehow
	if ($style_folders == "") {
		$style_folders = array();
	}

	$style_folders = json_encode( $style_folders, JSON_FORCE_OBJECT );
	$style_folders = htmlspecialchars( $style_folders, ENT_QUOTES );
	
	echo "styleFolders = $style_folders;";
}

/**
 * retreive shortcodes
 *
 * @since 1.3
 */

function ct_template_shortcodes() {

	$post_id = false;
	$template = false;
	$is_template = false;
	// get archive template
	if ( is_archive() || is_search() || is_404() || is_home() || is_front_page() ) {

		if ( is_front_page() ) {
			$post_id 	= get_option('page_on_front');
			//$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
		}
		else if ( is_home() ) {
			$post_id 	= get_option('page_for_posts');
			//$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
		}
		else //if ( !isset($shortcodes) || !$shortcodes ) {
		{
			$template 	= ct_get_archives_template();

			$shortcodes = $template?get_post_meta( $template->ID, "ct_builder_shortcodes", true ):false;

			if($template) {
				$is_template = true;
			}
		}
	} 
	//else
	// get single template
	if($post_id || (!$template && is_singular())) {
		// get post type
		// $post_id = get_the_ID();
		if($post_id == false)
			$post_id = get_the_ID();

		$ct_other_template = get_post_meta( $post_id, "ct_other_template", true );
		
		$template = false;
		
		if(!empty($ct_other_template) && $ct_other_template > 0) { // no template is specified
			// try getting default template
			$template = get_post($ct_other_template);
		}
		elseif($ct_other_template != -1) { // try getting default template if not explicitly set to not use any template at all
			if(intval($post_id) == intval(get_option('page_on_front')) || intval($post_id) == intval(get_option('page_for_posts'))) {
				$template = ct_get_archives_template( $post_id );
				if(!$template) {
					$template = ct_get_posts_template( $post_id );
				}
			}
			else {

				$template = ct_get_posts_template( $post_id );

				// if(!$template) {
				// 	$template = ct_get_archives_template( $post_id );
				// }
			}
		}

		if($template) {
			$is_template = true;
		} else {
			// does not even have a default template
			// then use it as a standalone custom view
			$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
		}

	} elseif(!$template) {

		$template 	= ct_get_archives_template();
		$shortcodes = $template?get_post_meta( $template->ID, "ct_builder_shortcodes", true ):false;

		if($template) {
			$is_template = true;
		}
	}

	// if it is a template, traverse the family tree 
	if($is_template) {

		$tree = array();
		
		$templateID = $template->ID;
		
		// in case, its a preview of a template using the given preview link, then enforce the usage of the template
		if(isset($_REQUEST['screenshot_template']) && is_numeric($_REQUEST['screenshot_template'])) {
			$templateID = intval($_REQUEST['screenshot_template']);
		}
		
		$combinedCodes = oxygen_get_combined_shortcodes($templateID);

		$tree['children'] = $combinedCodes['content'];
	
		$shortcodes_json = json_encode($tree);
	
		$shortcodes = components_json_to_shortcodes($shortcodes_json);
	}

	// in case it is a request to generate a screenshot for a single component, then the rendered page should not be wrapped with the outer template
	if(!$is_template && isset($_REQUEST['render_component_screenshot']) && stripslashes($_REQUEST['render_component_screenshot']) == 'true' && isset($_REQUEST['selector'])) {
		
		$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
		
	}
	
	if($shortcodes)
		return $shortcodes;
	else
		return false;

}

function oxygen_get_combined_shortcodes($template, $retainInnerContent = false) {

	$shortcodes = get_post_meta( $template, "ct_builder_shortcodes", true );
	$shortcodes = parse_shortcodes($shortcodes, false);
	// does this template inherits another template
	$parent = get_post_meta( $template, "ct_parent_template", true);
	
	if($parent) {

		// embed $shortcodes inside parent's shortcodes
		// first get the parent's shortcodes
		$parent_shortcodes = oxygen_get_combined_shortcodes($parent); // this takes care of multilevels

		//$parent_shortcodes = parse_shortcodes( $parent_shortcodes ); // validity

		//recursively obfuscate_ids: ct_id and ct_parent of all elements in $parsed, also obfuscate_selectors
		$ctDepthParser = new CT_Depth_Parser();

		$prepared_outer_content = ct_prepare_outer_template($parent_shortcodes['content'], $ctDepthParser);
		
		$parent_shortcodes['content'] = $prepared_outer_content['content'];

		$container_id = $prepared_outer_content['container_id'];

		// REPLACE inner_content shortcode altogether with the inner components

		$parent_id = $prepared_outer_content['parent_id'];
		
		if(!empty($shortcodes['content'])) {
			$shortcodes['content'] = ct_prepare_inner_content($shortcodes['content'], $container_id, $ctDepthParser->getDepths());
			if($retainInnerContent) {
				$parent_shortcodes['content'] = ct_embed_inner_content($parent_shortcodes['content'], $shortcodes['content']);
			}
			else {
				$parent_shortcodes['content'] = ct_replace_inner_content($parent_shortcodes['content'], $shortcodes['content']);
			}
		}
		
		return $parent_shortcodes;
	}

	return $shortcodes;
}

/**
 * Init style sheets
 *
 * @since 0.3.4
 * @author gagan goraya
 */

function ct_init_style_sheets() {
	
	$style_sheets = get_option( "ct_style_sheets", array() ); 

	// it was returning 'string (0) ""' first time, don't know why
	if ( !is_array( $style_sheets ) )
		$style_sheets = array();
	
	$newSheets = array();
	$id = 0;

	foreach($style_sheets as $value) {
		if(is_array($value) && isset($value['id']) && intval($value['id']) > $id) {
			$id = intval($value['id']);
		}
	}
	//base 64 decode
	foreach($style_sheets as $key => $value) {
		if(!is_array($value)) { // if it is the old style sheets data
			$newSheets[] = array( 'id' => ++$id, 'name' => $key, 'css' => base64_decode($value), 'parent' => 0, 'status' => 1 );
		}
		else {
			if(isset($value['css'])) {
				$value['css'] = base64_decode($value['css']);
			}
			
			$newSheets[] = $value;
		}
	}
	
	$output = json_encode( $newSheets );
	
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "styleSheets = $output;";
}


/**
 * Place the page width media at right position
 *
 * @author Ilya K.
 * @since 2.0
 */

function ct_sort_media_queries() {

	global $media_queries_list;
	global $ct_template_id;

	foreach ( $media_queries_list as $media_name => $media ) {
		
		if ( $media_name == "default" || $media_name == "page-width" )
			continue;

		if ( (intval($media_queries_list[$media_name]['maxSize']) >= oxygen_vsb_get_page_width($ct_template_id)) || $page_width_added ) {
			$medias[$media_name] = $media_queries_list[$media_name];
		}
		else {
			$medias['page-width'] = $media_queries_list['page-width'];
			$medias[$media_name] = $media_queries_list[$media_name];
			$page_width_added = true;
		} 
	}

	if (!$page_width_added) {
		$medias['page-width'] = $media_queries_list['page-width'];
	}

	return $medias;
}


/**
 * Place the page width media at right position
 *
 * @author Ilya K.
 * @since 2.0
 */

function oxygen_vsb_get_page_width($id="") {

	$page_settings 		= ct_get_page_settings($id);
	$global_settings 	= ct_get_global_settings();

	if ( isset($page_settings['max-width']) && $page_settings['max-width'] != "" ) {
		return $page_settings['max-width'];
	}
	else {
		return $global_settings['max-width'];
	}

}


/**
 * Output all saved CSS styles to frontend
 *
 * @since 0.1.3
 */

function ct_css_styles() {

	// Global settings
	$global_settings = ct_get_global_settings();

	$components_defaults = apply_filters("ct_component_default_params", array() );

	global $fake_properties;

	$fake_properties = array( 
			'overlay-color', 
			'background-position-left', 
			'background-position-top',
			'background-size-width',
			'background-size-height',
			'ct_content',
			'tag',
			'url',
			'src',
			'alt',
			'target',
			'icon-id',
			"section-width",
			"custom-width",
			"container-padding-top",
			"container-padding-right",
			"container-padding-bottom",
			"container-padding-left",
			"custom-css",
			"custom-js",
			"code-css",
			"code-js",
			"code-php",
			"gutter",
			'border-all-color',
			'border-all-style',
			'border-all-width',
			'function_name',
			'friendly_name',
			'shortcode_tag',
			'id',
			// flex
			'flex-reverse',
			// ct_video related
			'video-padding-bottom',
			'use-custom',
			'custom-code',
			'embed-src',
			// ct_link_button related
			'button-style',
			'button-size',
			'button-color',
			'button-text-color',
			// background related
			'gradient',
			"background",
			"overlay-color",
			// ct_icon related
			"icon-size",
			"icon-style",
			"icon-background-color",

			// new columns
			'reverse-column-order',
			'set-columns-width-50',
			'stack-columns-vertically',

			// shadows
			"box-shadow-horizontal-offset",
    		"box-shadow-vertical-offset",
    		"box-shadow-blur",
    		"box-shadow-spread",
    		"box-shadow-color",
    		"box-shadow-inset",
    		"text-shadow-horizontal-offset",
    		"text-shadow-vertical-offset",
    		"text-shadow-blur",
    		"text-shadow-color",

    		// filter
    		'filter-amount-blur',
			'filter-amount-brightness',
			'filter-amount-contrast',
			'filter-amount-grayscale',
			'filter-amount-hue-rotate',
			'filter-amount-invert',
			'filter-amount-saturate',
			'filter-amount-sepia',
			'filter-amount-blur-unit',
			'filter-amount-brightness-unit',
			'filter-amount-contrast-unit',
			'filter-amount-grayscale-unit',
			'filter-amount-hue-rotate-unit',
			'filter-amount-invert-unit',
			'filter-amount-saturate-unit',
			'filter-amount-sepia-unit',

			// tabs
			'tabs_wrapper',
			'tabs_contents_wrapper',
			'active_tab_class',
		);

	// Output all components default styles
	foreach ( $components_defaults as $component_name => $values ) {
		
		$component_name = str_replace( "_", "-", $component_name );
		$styles = "";
		
		if(is_array($values)) {
			foreach ( $values as $name => $value ) {

				// Output only some options since 2.0
				if ( !in_array($name, array("margin-top","margin-bottom","padding-left","text-decoration","max-width","position","width","display","flex-direction", "flex-wrap", "align-items","justify-content","text-align","background-size","background-repeat",
					// icon defaults
					"icon-color"
					))) {
					continue;
				}
				if ($name=="display"&&$value=="block") {
					continue;
				}
				if ($name=="width"&&$value!="100") {
					continue;
				}
				if ($name=="max-width"&&$value!="100") {
					continue;
				}
				if ($name=="position"&&$value=="static") {
					continue;
				}
				if ($name=="text-decoration"&&$value=="none"&&$component_name!="ct-link"&&$component_name!="ct-link-button") {
					continue;
				}
				if (($name=="margin-top"||$name=="margin-bottom"||$name=="padding-left")&&$component_name!="ct-ul") {
					continue;
				}
				if (($name=="background-size"||$name=="background-repeat")&&$component_name!="ct-section") {
					continue;
				}

				// old output before 2.0
				// skip uints
				if ( strpos( $name, "-unit") ) {
					continue;
				}

				// skip empty values
				if ( $value === "" ) {
					continue;
				}

				// skip fake properties
				if ( in_array( $name, $fake_properties ) ) {
					continue;
				}

				// apply for inner wrap
				if ($component_name=="ct-section" && in_array($name, array("display","flex-direction", "flex-wrap", "align-items","justify-content"))) {
					continue;
				} 

				// handle global fonts
				if ( $name == "font-family" && is_array( $value ) ) {
					$value = $global_settings['fonts'][$value[1]];

					if ( strpos($value, ",") === false && strtolower($value) != "inherit" ) {
						$value = "'$value'";
					}
				}

				// handle unit options
				if ( isset($values[$name.'-unit']) && $values[$name.'-unit'] ) {
					// set to auto
					if ( $values[$name.'-unit'] == 'auto' ) {
						$value = 'auto';
					}
					// or add unit
					else {
						$value .= $values[$name.'-unit'];
					}
				}

				$name = str_replace("icon-", "", $name);

				if ( $value !== "" ) {
					$styles .= "$name:$value;\r\n";
				}

			}
		}

		if ($styles!=="") {
			echo ( $component_name == "ct-paragraph" ) ? ".$component_name p {\r\n" : ".$component_name {\r\n";
			echo $styles;
			echo "}\r\n";
		}

		if ( $component_name == "ct-fancy-icon" ) {
			echo ".$component_name>svg {\r\n";
			echo "width:".$values['icon-size'].$values['icon-size-unit'].";";
			echo "height:".$values['icon-size'].$values['icon-size-unit'].";";
			echo "}\r\n";
		}

		if ( $component_name == "ct-link-button" ) {
			echo ".$component_name {\r\n";
			echo "background-color: " . $values['button-color'] . ";\r\n";
			echo "border: 1px solid " .  $values['button-color'] . ";\r\n";
			echo "color: " . $values['button-text-color'] . ";\r\n";
			$substracted = $values['button-style'] == 2 ? 1 : 0;
			echo "padding: " . (intval($values['button-size'])-$substracted) . 'px ' . (intval($values['button-size'])*1.6-$substracted) . "px;\r\n";
			echo "}\r\n";
		}

		if ($component_name=="ct-section") {
			echo ".$component_name>.ct-section-inner-wrap {\r\n";

			// flex since 2.0
			echo "display:" . $values['display'] . ";\r\n";
			echo "flex-direction:" . $values['flex-direction'] . ";\r\n";
			echo "align-items:" . $values['align-items'] . ";\r\n";
			//echo "justify-content:" . $values['justify-content'] . ";\r\n";

			echo "}\r\n";
		}
	}

	/**
	 * Make it possible to add defaults from components Class or any other place
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	do_action("oxygen_default_classes_output");

	// Below is only for frontend
	if ( defined("SHOW_CT_BUILDER") )
		return;
	
	$css = "";

	$page_settings = false;
	$post_id = false;
	$template = false;

	// get archive template
	if ( is_archive() || is_search() || is_404() || is_home() || is_front_page() ) {

		if (is_front_page()) {
			$post_id = get_option('page_on_front');
		}
		else if(is_home()) {
			$post_id = get_option('page_for_posts');
		}

		// do not apply any templates if there is a custom view
		//else {

			$template = ct_get_archives_template();

			$page_settings = ct_get_page_settings( $template->ID );
			$page_width = oxygen_vsb_get_page_width( $template->ID );
		//}
	} 
	
	// get single template
	if($post_id || (!$template && is_singular())) {

		if($post_id == false)
			$post_id = get_the_ID();

		$ct_render_post_using = get_post_meta( $post_id, "ct_render_post_using", true );

		if($ct_render_post_using != 'other_template'){
			$custom_view = get_post_meta( $post_id, "ct_builder_shortcodes", true );
		}
		else {
			$custom_view = false;
		}

		// do not apply any templates if there is a custom view
		if ( $custom_view ) {
			$page_settings = ct_get_page_settings( $post_id );
			$page_width = oxygen_vsb_get_page_width( $post_id );
		}
		else {

			$template_id = get_post_meta( $post_id, 'ct_other_template', true );

			if($template_id) {
				$template = get_post($template_id);
			}
			else {

				if(is_front_page() || is_home())
					$template = ct_get_archives_template( $post_id );
				else
					$template = ct_get_posts_template( $post_id );
			}

			// get template shortcodes
			$page_settings = ct_get_page_settings( $template->ID );
			$page_width = oxygen_vsb_get_page_width( $template->ID );
		}

	}

	// if no page settings so far, check if we are using a header/footer template
	
	if(!$page_settings) {

		global $ct_template_header_footer_id;

		if(isset($ct_template_header_footer_id)) {

			$page_settings = ct_get_page_settings( $ct_template_header_footer_id );
			$page_width = oxygen_vsb_get_page_width( $ct_template_header_footer_id );

		}
	}

	$css .= ".ct-section-inner-wrap, .oxy-header-container{\r\n  max-width: ".$page_width."px;\r\n}\r\n";

	/**
	 * Check if need to include CSS for classes
	 *
	 * @since 2.0
	 */

	if ( !isset( $_REQUEST['nouniversal'] ) || stripslashes( $_REQUEST['nouniversal'] ) != 'true' ) {
		$css .= oxygen_vsb_get_global_styles();
		$css .= oxygen_vsb_get_classes_styles();
	}

	
	// output CSS
	echo $css;
}
add_action("ct_footer_styles", "ct_css_styles");


/**
 * Function to generate classes CSS output
 *
 * @since 2.0
 * @author Ilya K.
 */

function oxygen_vsb_get_classes_styles() {

	global $media_queries_list;
	global $oxygen_vsb_css_classes;

	$css = "";

	$classes = $oxygen_vsb_css_classes;


	$styleFolders 	= get_option( "ct_style_folders");

	if ( is_array( $classes ) ) {
		foreach ( $classes as $class => $states ) {
			//if the parent folder is disabled?
			if(!(!isset($states['parent']) || !isset($styleFolders[$states['parent']]) || intval($styleFolders[$states['parent']]['status']) === 1)) {
				continue;
	    	}
	    	// if set under disabled uncategorized
			if(intval($states['parent']) === -1) {
    			continue;
			}

			$style = "";
			foreach ( $states as $state => $options ) {

				if (in_array($state, array("set_name", "key", "parent", "status", "friendly_name"))) {
					continue;
				}	

				if ( $state == 'media' ) {

					$sorted_media_queries_list = ct_sort_media_queries();

					foreach ( $sorted_media_queries_list as $media_name => $media ) {

						if ($media_name == "page-width") {
							$max_width = $page_width.'px';
						}
						else {
							$max_width = $media_queries_list[$media_name]['maxSize'];
						}

						if ( isset($options[$media_name]) && $media_name != "default") {

							$style .= "@media (max-width: $max_width) {\n";
								foreach ( $options[$media_name] as $media_state => $media_options ) {
									$style .= ct_generate_class_states_css($class, $media_state, $media_options, true);
								}
							$style .= "}\n\n";
						}
					}
				}
				else {
					$style = ct_generate_class_states_css($class, $state, $options).$style;
				}
			}

			$css .= $style;
		}
	}

	return $css;
}

function ct_generate_class_states_css( $class, $state, $options, $is_media = false, $is_selector = false ) {
	
	global $fake_properties;
	global $oxygen_vsb_css_classes;
	//global $font_families_list;
	$css = "";

	global $media_queries_list;
	$media_queries_list["page-width"]["maxSize"] = oxygen_vsb_get_page_width().'px';

	$components_defaults = apply_filters("ct_component_default_params", array() );
	$defaults = call_user_func_array('array_merge', $components_defaults);
	$global_settings 	= get_option("ct_global_settings");

	if ( !$is_selector ) {
		if ( $state != 'original' ) {
			$css .= ".$class:$state{\r\n";
		}
		else {
			$css .= ".$class {\r\n";
		}
	}
	else {
		if ( $state != 'original' ) {
			$css .= "$class:$state{\r\n";
		}
		else {
			$css .= "$class{\r\n";	
		}
	}

	$content_included = false;

	// handle units
	if(is_array($options)) {
		foreach ( $options as $name => $value ) {
			// handle unit options
			if ( isset($defaults[$name.'-unit']) && $defaults[$name.'-unit'] ) {

				if ( isset($options[$name.'-unit']) && $options[$name.'-unit'] ) {
					// set to auto
					if ( $options[$name.'-unit'] == 'auto' ) {
						$options[$name] = 'auto';
					}
					// or add unit
					else {
						$options[$name] .= $options[$name.'-unit'];
					}
				}
				else {
					$options[$name] .= $defaults[$name.'-unit'];
				}
			}
			else {
	            if ( $options[$name] == 'auto' ) {
	            	$name = str_replace("-unit", "", $name);
	                $options[$name] = 'auto';
	            }
	            if ($name == 'container-padding-top'||
                        $name == 'container-padding-bottom'||
                        $name == 'container-padding-left'||
                        $name == 'container-padding-right') {
                        $unit = ( $options[$name.'-unit'] ) ? $options[$name.'-unit'] : $global_settings['sections'][$name.'-unit'];
                        if ( $options[$name] ) {
                            $options[$name] .= $unit;
                        }
                    }
			}
		}
	}

	// handle background-position option
	if ( (isset($options['background-position-left']) && $options['background-position-left']) || (isset($options['background-position-top']) && $options['background-position-top']) ) {

		$left = $options['background-position-left'] ? $options['background-position-left'] : "0%";
		$top  = $options['background-position-top'] ? $options['background-position-top'] : "0%";
		$options['background-position'] = $left . " " . $top;
	}

	// handle background-size option
	if ( isset($options['background-size']) && $options['background-size'] == "manual" ) {

		$width = $options['background-size-width'] ? $options['background-size-width'] : "auto";
		$height = $options['background-size-height'] ? $options['background-size-height'] : "auto";
		$options['background-size'] = $width . " " . $height;
	}

	// handle box-shadow options
	if ( isset($options['box-shadow-color']) ) {

		$inset 	= ($options['box-shadow-inset']=='inset') 		? $options['box-shadow-inset']." " : "";
		$hor 	= (isset($options['box-shadow-horizontal-offset'])) 	? $options['box-shadow-horizontal-offset']."px " : "";
		$ver 	= (isset($options['box-shadow-vertical-offset'])) 		? $options['box-shadow-vertical-offset']."px " : "";
		$blur 	= (isset($options['box-shadow-blur'])) 					? $options['box-shadow-blur']."px " : "0px ";
		$spread = (isset($options['box-shadow-spread'])) 				? $options['box-shadow-spread']."px " : "";
				
		$options['box-shadow'] = $inset.$hor.$ver.$blur.$spread.$options['box-shadow-color'];
	}

	// handle text-shadow options
	if ( isset($options['text-shadow-color']) ) {

		$hor 	= (isset($options['text-shadow-horizontal-offset'])) 	? $options['text-shadow-horizontal-offset']."px " : "";
		$ver 	= (isset($options['text-shadow-vertical-offset'])) 		? $options['text-shadow-vertical-offset']."px " : "";
		$blur 	= (isset($options['text-shadow-blur'])) 				? $options['text-shadow-blur']."px " : "0px ";
				
		$options['text-shadow'] = $hor.$ver.$blur.$options['text-shadow-color'];
	}

	/**
	 * Handle specific Icon styles to support classes
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$all_classes = $oxygen_vsb_css_classes;

	if (isset($options['icon-style'])||isset($options['icon-color'])||isset($options['icon-background-color'])||isset($options['icon-padding'])||isset($options['icon-size'])) {

		// save the class name to apply later
		$classname = $css;

		if (!$is_media && $state == "original") {

			$iconStyle 			= (isset($options['icon-style'])) ? $options['icon-style'] : $components_defaults['ct_fancy_icon']['icon-style'];
			$iconColor 			= (isset($options['icon-color'])) ? $options['icon-color'] : $components_defaults['ct_fancy_icon']['icon-color'];
			$iconBackgroundColor= (isset($options['icon-background-color'])) ? $options['icon-background-color'] : $components_defaults['ct_fancy_icon']['icon-background-color'] ;
			$iconPadding 		= (isset($options['icon-padding'])) ? $options['icon-padding'] : $components_defaults['ct_fancy_icon']['icon-padding'] . $components_defaults['ct_fancy_icon']['icon-padding-unit'];
			$iconSize 			= (isset($options['icon-size'])) ? $options['icon-size'] : $components_defaults['ct_fancy_icon']['icon-size'] . $components_defaults['ct_fancy_icon']['icon-size-unit'];
		}
		else {
			$iconStyle 			= (isset($all_classes[$class]['original']['icon-style'])) ? $all_classes[$class]['original']['icon-style'] : $components_defaults['ct_fancy_icon']['icon-style'];
			$iconColor 			= $options['icon-color'];
			$iconBackgroundColor= $options['icon-background-color'];
			$iconPadding 		= $options['icon-padding'];
			$iconSize 			= $options['icon-size'];
		}

		if ( $iconStyle == "1") {
			$css .= "border: 1px solid;\r\n";
		}
					
		if ($iconStyle == "2") {
			if ( isset($iconBackgroundColor) ) {
				$css .= "background-color: " . $iconBackgroundColor . ";\r\n";
				$css .= "border: 1px solid " . $iconBackgroundColor . ";\r\n";
			}
		}

		if ( $iconStyle == "1" || $iconStyle == "2") {
			$css .= "padding: " . $iconPadding . ";";
		}

		if ( $iconColor ) {
		 	$css .= "color: " . $iconColor . ";";
		}

		$css .= "}";

		if ( $iconSize ) {
			$css .= str_replace("{","",$classname).">svg {";
		 	$css .= "width: " . $iconSize . ";";
		 	$css .= "height: " . $iconSize . ";";
		 	$css .= "}";
		}

		// add classname back so options below also work fine
		$css .= $classname;
	}


	/**
	 * Handle specific Button styles to support classes
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$all_classes = $oxygen_vsb_css_classes;

	if (isset($options['button-style'])||isset($options['button-color'])||isset($options['button-text-color'])||isset($options['button-size'])) {

		// save the class name to apply later
		$classname = $css;

		if (!$is_media && $state == "original") {
			$buttonStyle 		= (isset($options['button-style'])) ? $options['button-style'] : $components_defaults['ct_link_button']['button-style'];
			$buttonColor 		= (isset($options['button-color'])) ? $options['button-color'] : $components_defaults['ct_link_button']['button-color'];
			$buttonTextColor 	= (isset($options['button-text-color'])) ? $options['button-text-color'] : $components_defaults['ct_link_button']['button-text-color'] ;
			$buttonSize 		= (isset($options['button-size'])) ? $options['button-size'] : $components_defaults['ct_link_button']['button-size'];
		}
		else {
			$buttonStyle 		= isset($all_classes[$class]['original']['button-style']) ? $all_classes[$class]['original']['button-style'] : $components_defaults['ct_link_button']['button-style'];
			$buttonColor 		= $options['button-color'];
			$buttonTextColor 	= $options['button-text-color'];
			$buttonSize 		= $options['button-size'];
		}

		if ( $buttonStyle == 1 && isset($buttonColor)) {
			$css .= "background-color :" . $buttonColor . ";\r\n";
			$css .= "border: 1px solid " .  $buttonColor . ";\r\n";
			if($buttonTextColor) {
				$css .= "color: " . $buttonTextColor . ";\r\n";
			}
		}

		if ( $buttonStyle == 2 ) {
			$css .= "background-color: transparent;\r\n";
			if ( isset($buttonColor) ) {
				$css .= "border: 1px solid " .  $buttonColor . ";\r\n";
				$css .= "color: " . $buttonColor . ";\r\n";
			}
		}

		if ( isset($buttonSize) ) {
			$substracted = $buttonStyle == 2 ? 1 : 0;
			$css .= "padding: " . (intval($buttonSize)-$substracted) . 'px ' . (intval($buttonSize)*1.6-$substracted) . "px;\r\n";
		}

		$css .= "}";

		// add classname back so options below also work fine
		$css .= $classname;
	}

	// loop all other options
	if(is_array($options)) {
		foreach ( $options as $name => $value ) {

			// skip units
			if ( strpos( $name, "-unit") ) {
				continue;
			}

			// skip empty values
			if ( $value === "" ) {
				continue;
			}

			if ( $name == "font-family") {

				if ( $value[0] == 'global' ) {
						$settings 	= get_option("ct_global_settings");
						$value 		= isset($settings['fonts'][$value[1]]) ? $settings['fonts'][$value[1]]: '';
					}

				//$font_families_list[] = $value;

				if ( strpos($value, ",") === false && strtolower($value) != "inherit") {
					$value = "'$value'";
				}
			}

			// update options array values if there was modifications
			$options[$name] = $value;

			// skip fake properties
			if (is_array($fake_properties) && in_array( $name, $fake_properties ) ) {
				continue;
			}

			// add flex later for innerwrap. since 2.0
			if ( in_array($name, ["display","flex-direction","flex-wrap","align-items","align-content","justify-content"]) &&
				 !$is_selector ) {
				continue;
			}

			// handle image urls
			if ( $name == "background-image") {
				
				$value = "url(".do_shortcode($value).")";
				// trick for overlay color
	            if ( isset( $options['overlay-color'] ) ) {
	                $value = "linear-gradient(" . $options['overlay-color'] . "," . $options['overlay-color'] . "), " . $value;
	            }
			}
			
			// add quotes for content for :before and :after
			if ( $name == "content" ) {
				//$value = addslashes( $value );
				$value = str_replace('"', '\"', $value);
				$value = "\"$value\"";
				$content_included = true;
			}

			// css filter property
			if ( $name == "filter" && $options["filter-amount-".$value] ) {
				$value .= "(".$options["filter-amount-".$value].")";
			} 
			else if ( $name == "filter" ) {
				continue;
			}

			// finally add to CSS
			if ($name != "background-layers") {
				$css .= " $name:$value;\r\n";
			}

			if ($name == "-webkit-font-smoothing") {
				$css .=  '-moz-osx-font-smoothing' . ":" . ($value === 'antialiased' ? 'greyscale' : 'unset') . ";";
			}

		}
	}
	
	if ( !$content_included && ( $state == "before" || $state == "after" ) && !$is_media ) {
		$css .= "  content:\"\";\r\n";
	}

	// add custom CSS to the end
	if ( isset($options["custom-css"]) && $options["custom-css"] ) {
		if( strpos($options["custom-css"], ' ') === false 
		 	&& strpos($options["custom-css"], ':') === false
		 	&& strpos($options["custom-css"], ';') === false  ) {
			// this is most probably base 64 encoded css (old data)
			$css .= base64_decode( $options["custom-css"] ) . "\r\n";	
		}
		else {
			$css .= $options["custom-css"] . "\r\n";	
		}
	}

	$css .= "}\r\n";

	// handle container padding for classes
	if ( (isset($options['container-padding-top']) && $options['container-padding-top']) 	 ||
		 (isset($options['container-padding-right']) && $options['container-padding-right'])  ||
		 (isset($options['container-padding-bottom']) && $options['container-padding-bottom']) ||
		 (isset($options['container-padding-left']) && $options['container-padding-left']) ) {

		$css .= ".$class .ct-section-inner-wrap {\r\n";
		
		if ( isset($options['container-padding-top']) && $options['container-padding-top'] ) {
			$css .= "padding-top: " . $options['container-padding-top'] . ";\r\n";
		}
		if ( isset($options['container-padding-right']) && $options['container-padding-right'] ) {
			$css .= "padding-right: " . $options['container-padding-right'] . ";\r\n";
		}
		if ( isset($options['container-padding-bottom']) && $options['container-padding-bottom'] ) {
			$css .= "padding-bottom: " . $options['container-padding-bottom'] . ";\r\n";
		}
		if ( isset($options['container-padding-left']) && $options['container-padding-left'] ) {
			$css .= "padding-left: " . $options['container-padding-left'] . ";\r\n";
		}

		$css .= "}\r\n";
	}
	
	$pre_styles = "";
	
	// flex options since 2.0
	if ( isset($options['display']) ) {
		$pre_styles .= "display:" . $options['display'] . ";\r\n";
	}

	$reverse = ($options['flex-reverse'] == 'reverse') ? "-reverse" : "";
	if ( isset($options['flex-direction']) ) {
		$pre_styles .= "flex-direction:" . $options['flex-direction'] . $reverse . ";\r\n";
	}
	if ( isset($options['flex-wrap']) ) {
		$pre_styles .= "flex-wrap:" . $options['flex-wrap'] . ";\r\n";
	}
	if ( isset($options['align-items']) ) {
		$pre_styles .= "align-items:" . $options['align-items'] . ";\r\n";
	}
	if ( isset($options['align-content']) ) {
		$pre_styles .= "align-content:" . $options['align-content'] . ";\r\n";
	}
	if ( isset($options['justify-content']) ) {
		$pre_styles .= "justify-content:" . $options['justify-content'] . ";\r\n";
	}
	
	if($pre_styles != '' && !$is_selector) {
		
		if ( $state != 'original' ) {
			$css .= ".$class:not(.ct-section):$state,\r\n";
			if ( is_pseudo_element($state) ) {
				$css .= ".$class.ct-section .ct-section-inner-wrap:$state{\r\n";
			}
			else {
				$css .= ".$class.ct-section:$state .ct-section-inner-wrap{\r\n";
			}
		}
		else {
			$css .= ".$class:not(.ct-section),\r\n";
			$css .= ".$class.ct-section .ct-section-inner-wrap{\r\n";
		}
		
		$css .= $pre_styles;
		$css .= "}\r\n";
	}

	/**
	 * Make it possible to apply custom classes logic from components Classes or other places
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$css = apply_filters("oxygen_user_classes_output", $css, $class, $state, $options, $is_media, $is_selector, $components_defaults);

	return $css;
}


/**
 * Function to generate global settigns CSS output
 *
 * @since 2.0
 * @author Ilya K.
 */

function oxygen_vsb_get_global_styles() {

	$global_settings = ct_get_global_settings();

	$css = "";

	$text_font 		= $global_settings['fonts']['Text'];
	$display_font 	= $global_settings['fonts']['Display'];

	if ( !is_array($value) && strpos($text_font, ",") === false && strtolower($value) != "inherit" ) {
		$text_font = "'$text_font'";
	}
	if ( !is_array($value) && strpos($display_font, ",") === false && strtolower($value) != "inherit" ) {
		$display_font = "'$display_font'";
	}

	// Global settings since 2.0
	// Body Text
	$css .= "body {";
	$css .= "font-family: ".$text_font.";";
	$css .= "line-height: ".$global_settings["body_text"]["line-height"].";";
	$css .= "font-size: ".$global_settings["body_text"]["font-size"].$global_settings["body_text"]["font-size-unit"].";";
	$css .= "font-weight: ".$global_settings["body_text"]["font-weight"].";";
	$css .= "color: ".$global_settings["body_text"]["color"].";";
	$css .= "}";

	$css .= ".oxy-nav-menu-hamburger-line {";
	$css .= "background-color: ".$global_settings["body_text"]["color"].";";
	$css .= "}";

	// Headings
	$css .= "h1, h2, h3, h4, h5, h6 {";
    $css .= "font-family: ".$display_font.";";
    
    if ($global_settings["headings"]["H1"]["font-size"] !== "") { 
    	$css .= "font-size: ".$global_settings["headings"]["H1"]["font-size"].$global_settings["headings"]["H1"]["font-size-unit"].";";
    }
    if ($global_settings["headings"]["H1"]["font-weight"] !== "") { 
    	$css .= "font-weight: ".$global_settings["headings"]["H1"]["font-weight"].";";
    }
    if ($global_settings["headings"]["H1"]["color"] !== "") { 
    	$css .= "color: ".$global_settings["headings"]["H1"]["color"].";";
    }
    
    $css .= "}";

    $selector = "h2, h3, h4, h5, h6";

    foreach($global_settings["headings"] as $heading => $options) { 

		$heading_css = "";
		
		if ($heading=="H1") {
			continue;
		}
		
		if ($options["font-size"] !== "") {
			$heading_css .= "font-size: ".$options["font-size"].$options["font-size-unit"].";";
		}
		if ($options["font-weight"] !== "") {
			$heading_css .= "font-weight: ".$options["font-weight"].";";
			}
			if ($options["color"] !== "") {
				$heading_css .= "color: ".$options["color"].";";
		}
			if ( $heading_css !== "" ) {
			$css .= $selector . "{";
			$css .= $heading_css;
			$css .= "}";
		}

		// update selector
		$selector = str_replace(strtolower($heading).", ", "", $selector);
	}
	
	$links = array(
		"all" => "a",
		"text_link" => ".ct-link-text",
		"link_wrapper" => ".ct-link",
		"button" => ".ct-link-button"
	);

	foreach($links as $key => $selector) { 
	
		// Links
		$links_css = "";
		if ($global_settings["links"][$key]["color"] !== "") {
			$links_css .= "color: ".$global_settings["links"][$key]["color"].";";
		}
		if ($global_settings["links"][$key]["font-weight"] !== "") {
			$links_css .= "font-weight: ".$global_settings["links"][$key]["font-weight"].";";
		}
		if ($global_settings["links"][$key]["text-decoration"] ) {
			$links_css .= "text-decoration: ".$global_settings["links"][$key]["text-decoration"].";";
		}
		if ($global_settings["links"][$key]["border-radius"] ) {
			$links_css .= "border-radius: ".$global_settings["links"][$key]["border-radius"].$global_settings["links"][$key]["border-radius-unit"].";";
		}

		if ( $links_css !== "" ) {
			$css .= $selector." {";
			$css .= $links_css;
			$css .= "}";
		}

		$links_css = "";
		if ($global_settings["links"][$key]["hover_color"] !== "") {
			$links_css .= "color: ".$global_settings["links"][$key]["hover_color"].";";
		}
		if ($global_settings["links"][$key]["hover_text-decoration"] ) {
			$links_css .= "text-decoration: ".$global_settings["links"][$key]["hover_text-decoration"].";";
		}

		if ( $links_css !== "" ) {
			$css .= $selector.":hover {";
			$css .= $links_css;
			$css .= "}";
		}
	}

	// Sections container padding
	$css .= ".ct-section-inner-wrap {\r\n";
			
			if ( isset($global_settings['sections']['container-padding-top']) && $global_settings['sections']['container-padding-top'] ) {
				$css .= "padding-top: " . $global_settings['sections']['container-padding-top'] . $global_settings['sections']['container-padding-top-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-right']) && $global_settings['sections']['container-padding-right'] ) {
				$css .= "padding-right: " . $global_settings['sections']['container-padding-right'] . $global_settings['sections']['container-padding-right-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-bottom']) && $global_settings['sections']['container-padding-bottom'] ) {
				$css .= "padding-bottom: " . $global_settings['sections']['container-padding-bottom'] . $global_settings['sections']['container-padding-bottom-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-left']) && $global_settings['sections']['container-padding-left'] ) {
				$css .= "padding-left: " . $global_settings['sections']['container-padding-left'] . $global_settings['sections']['container-padding-left-unit'] . ";\r\n";
			}
	$css .= "}";

	// Sections container padding
	$css .= ".oxy-header-container {\r\n";
			
			if ( isset($global_settings['sections']['container-padding-right']) && $global_settings['sections']['container-padding-right'] ) {
				$css .= "padding-right: " . $global_settings['sections']['container-padding-right'] . $global_settings['sections']['container-padding-right-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-left']) && $global_settings['sections']['container-padding-left'] ) {
				$css .= "padding-left: " . $global_settings['sections']['container-padding-left'] . $global_settings['sections']['container-padding-left-unit'] . ";\r\n";
			}
	$css .= "}";


	// make columns fullwidth on mobile
	$css .= "@media (max-width: 992px) {
				.ct-columns-inner-wrap {
					display: block !important;
				}
				.ct-columns-inner-wrap:after {
					display: table;
					clear: both;
					content: \"\";
				}
				.ct-column {
					width: 100% !important;
					margin: 0 !important;
				}
				.ct-columns-inner-wrap {
					margin: 0 !important;
				}
			}\r\n";

	return $css;
}


/**
 * Check if state is pseudo-element by it's name
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function is_pseudo_element( $name ) {
	
	if ( 
            strpos($name, "before")       === false &&
            strpos($name, "after")        === false &&
            strpos($name, "first-letter") === false &&
            strpos($name, "first-line")   === false &&
            strpos($name, "selection")    === false
        ) 
    {
        return false;
    }
    else {
        return true;
    }
}

/**
 * Generate font familes list to load
 *
 * @since  0.2.3
 */

function ct_get_font_families_string( $font_families ){

	if ( ! $font_families ) {
		return "";
	}

	$web_safe_fonts = array(
			'inherit',
			'Inherit',
			'Georgia, serif',
			'Times New Roman, Times, serif',
			'Arial, Helvetica, sans-serif',
			'Arial Black, Gadget, sans-serif',
			'Tahoma, Geneva, sans-serif',
			'Verdana, Geneva, sans-serif',
			'Courier New, Courier, monospace'
		);

	// don't load web safe fonts
	$font_families = array_diff( $font_families, $web_safe_fonts );

	// don't load typekit fonts
	$typekit_fonts = get_option("oxygen_vsb_latest_typekit_fonts", array());

	foreach ($typekit_fonts as $typekit_font) {
		$key = array_search($typekit_font['slug'], $font_families);
		if ($key) {
			unset ($font_families[$key]);
		}
	}

	// don't load ECF fonts
	if (class_exists('ECF_Plugin')) {
		$ecf_fonts = ECF_Plugin::get_font_families();
	} else {
		$ecf_fonts = array();
	}

	foreach ($ecf_fonts as $ecf_font) {
		$key = array_search($ecf_font, $font_families);
		if ($key) {
			unset ($font_families[$key]);
		}
	}

	// filter array for empty values
	$font_families = array_filter( $font_families, function( $font ) {
						return $font !== '';
					});

	// filter array for duplicate values
	$font_families = array_unique( $font_families );

	// add font weights
	$font_families = array_map( function( $font ) {
						return $font . ':100,200,300,400,500,600,700,800,900';
					}, $font_families );

	// add "" quotes
	$font_families = array_map( function( $font ) {
						return '"' . $font . '"';
					}, $font_families );		

	// create fonts string to pass into JS
	$font_families = implode(",", $font_families);

	return $font_families;
}


/**
 * Echo all components JS like web fonts etc
 * 
 * @since 0.1.9
 */

function ct_footer_script_hook() {
	echo "<script type=\"text/javascript\" id=\"ct-footer-js\">";
		do_action("ct_footer_js");
	echo "</script>";


	$footer_js = ct_get_components_classes(true);
	if(is_array($footer_js)) {
		foreach($footer_js as $key => $val) {
			echo "<script type=\"text/javascript\" id=\"$key\">";
				echo $val;
			echo "</script>";		
		}
	}

}
add_action("wp_footer", "ct_footer_script_hook");


/**
 * Displays a warning for non-chrome browsers in the builder
 * 
 * @since 0.3.4
 * @author gagan goraya
 */

function ct_chrome_modal() {

	if ( defined("SHOW_CT_BUILDER") )  {
		$dismissed = get_option("ct_chrome_modal", false );

		$warningMessage = __("<h2><span class='ct-icon-warning'></span> Warning: we recommend Google Chrome when designing pages</h2><p>The designs you create using Oxygen will work properly in all modern browsers including but not limited to Chrome, Firefox, Safari, and Internet Explorer/Edge.</p><p>But for the best, most stable experience when using Oxygen to design pages, we recommend using Google Chrome.</p><p>We've done most of our testing with Chrome and expect that you will encounter minor bugs in the builder when using Firefox or Safari. Please report those to us by e-mailing at support@oxygenapp.com.</p><p>We have no intention of making the builder work well in Internet Explorer.</p><p>Again, this message only applies to the builder itself. The pages you create with Oxygen will render correctly in all modern browsers.</p><p>Best Regards,<br />The Oxygen Team</p>", 'component-theme' );

		$hideMessage = __("hide this notice", 'component-theme' );

		if(!$dismissed) {


			echo "<div ng-click=\"removeChromeModal(\$event)\" class=\"ct-chrome-modal-bg\"><div class=\"ct-chrome-modal\"><a href=\"#\" class=\"ct-chrome-modal-hide\">".$hideMessage."</a>"."</div></div>";

		?>
			<script type="text/javascript">
			
				jQuery(document).ready(function(){
					var warningMessage = "<?php echo $warningMessage; ?>";
					
			        var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
			        
			        var chromeModalWrap = jQuery('.ct-chrome-modal-bg');

			        if(isChrome) {
			        	chromeModalWrap.remove();
					}
			        else {
						chromeModalWrap.css('display', 'block');
			           	var chromeModal = jQuery('.ct-chrome-modal');
			            chromeModal.append(warningMessage);
			        }

			    });
			
			</script>

			<?php
		}
	}

}

//add_action("wp_footer", "ct_chrome_modal");



/**
 * Fix for <p></p> tags around component shortocdes
 * 
 * @since 0.1.6
 */

//remove_filter("the_content", "wpautop");

/**
 * Turn off wptexturize https://codex.wordpress.org/Function_Reference/wptexturize
 * 
 * @since 0.1.6
 */

add_filter("run_wptexturize", "__return_false");


/**
 * Add support for certain WordPress features
 * 
 * @since 0.2.3
 */

function ct_theme_support() {

	add_theme_support("menus"); 
	add_theme_support("post-thumbnails");
	add_theme_support("title-tag");
}
add_action("init", "ct_theme_support");


/**
 * Add support for certain WordPress features
 * 
 * @since 2.0
 */

function oxygen_vsb_woo_theme_support() {
	add_theme_support("woocommerce");
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action("after_setup_theme", "oxygen_vsb_woo_theme_support"); 


/**
 * Uses a dedicated template to render CSS only that can be loaded from external links
 * or Oxygen main template to show builder or builder designed page
 *
 * @author gagan goraya
 * @since 0.3.4
 */

function ct_css_output( $template ) {
	
	$new_template = '';
	
	if ( $template != get_page_template() && $template != get_index_template() ) {
		global $ct_replace_render_template;
		$ct_replace_render_template = $template;
	}

	if ( isset( $_REQUEST['xlink'] ) && stripslashes( $_REQUEST['xlink'] ) == 'css' ) {
		if ( file_exists( dirname( __FILE__) . '/csslink.php' ) ) {
			$new_template = dirname( __FILE__ ) . '/csslink.php';
		}
	}
	else {
		// if there is saved template or if we are in builder mode

		//if ( ct_template_output( true ) || defined( "SHOW_CT_BUILDER" ) ) {
			
			if ( defined("OXYGEN_IFRAME") ) {
				$new_template =  plugin_dir_path( __FILE__ ) . "/oxygen-iframe-template.php";
			}
			else
			if ( file_exists(plugin_dir_path( __FILE__ ) . "/oxygen-main-template.php") ) {
				$new_template =  plugin_dir_path( __FILE__ ) . "/oxygen-main-template.php";
			}

		//}
	}
	
	if ( '' != $new_template ) {
		return $new_template;
	}
		
	return $template;
}
add_filter( 'template_include', 'ct_css_output', 99 );

function ct_determine_render_template( $template ) {
	
	$new_template = '';

	if ( defined( "SHOW_CT_BUILDER" ) ) {
		return get_index_template();
	}

	$post_id 	 = get_the_ID();
	$custom_view = false;

	if ( !is_archive() ) {
		$custom_view = get_post_meta( $post_id, "ct_builder_shortcodes", true );
	}
	
	if ( $custom_view || ct_template_output( true ) ) {
		return get_page_template();
	}
	
	return $template;
}
add_filter( 'template_include', 'ct_determine_render_template', 98 );


/**
 * Try to get CSS styles before WP run to speed up page load
 * 
 * @since 1.1.1
 * @author Ilya K.
 */

function ct_css_link( $template ) {

	if ( isset( $_REQUEST['action'] ) && stripslashes( $_REQUEST['action'] ) == 'save-css' ) {
		return;
	}

	if ( ! isset( $_GET['ct_builder'] ) || ! $_GET['ct_builder'] ) {
		if ( isset( $_REQUEST['xlink'] ) && stripslashes( $_REQUEST['xlink'] ) == 'css' ) {
			ob_start();
			include 'csslink.php';
			ob_end_clean();
		}
	}
}
//add_action("after_setup_theme", "ct_css_link");


/**
 * Get template as soon as possible
 * 
 * @since 1.1.1
 * @author Ilya K.
 */

function ct_pre_template_output( $template ) {

	// support for elementor plugin
	if ( isset( $_REQUEST['elementor-preview'] ) ) {
		return;
	}

	global $template_content;
	$template_content = ct_template_output();
}
//add_action("wp", "ct_pre_template_output");


/**
 * Registers all the widgets to be rendered to the WP globals
 *
 * @author gagan goraya
 * @since 0.3.4
 */
	
function ct_register_widgets( ) {
	global $_wp_sidebars_widgets, $shortcode_tags;

	if(!(isset($_wp_sidebars_widgets['ct-virtual-sidebar'])))
		$_wp_sidebars_widgets['ct-virtual-sidebar'] = array();

	$content = ct_template_output(true);

	// Find all registered tag names in $content.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
	$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

	if(!array_search('ct_widget', $tagnames))
		return;
	
	$pattern = get_shortcode_regex( array('ct_widget') );
	
	preg_match_all( "/".$pattern."/", $content, $matches );

	foreach($matches[3] as $widgetOptions) {
		preg_match('@\"id_base\":\"([^\"]*)\"@', $widgetOptions, $opMatches);
		array_push($_wp_sidebars_widgets['ct-virtual-sidebar'], $opMatches[1]);
	}
}
add_filter( 'template_redirect', 'ct_register_widgets', 19 );


/**
 * Add Cache-Control headers to force page refresh 
 * on browser's back button click
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_add_headers() {

	if ( defined("SHOW_CT_BUILDER") ) {
		header_remove("Cache-Control");
		header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0"); // HTTP 1.1.
	}
}
add_action( 'send_headers', 'ct_add_headers' );


/**
 * Add 'oxygen-body' class for frontend only
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_body_class($classes) {

	if ( ! defined("SHOW_CT_BUILDER") ) {
		$classes[] = 'oxygen-body';
	}
	else {
		$classes[] = 'oxygen-builder-body';	
	}

	return $classes;
}
add_filter('body_class', 'ct_body_class');


/**
 * Loading webfonts for the front end, in the <head> section
 *
 * @since 0.3.4
 * @author gagan goraya
 */

function add_web_font() {

	if ( defined("SHOW_CT_BUILDER") ) {
		return;
	}

	global $header_font_families;
	$header_font_families = array();

	$global_settings = ct_get_global_settings();
	$shortcodes = false;
	// add default globals
	foreach ( $global_settings['fonts'] as $key => $value ) {
		$header_font_families[] = $value;
	}
	
	$shortcodes = ct_template_shortcodes();

	
	global $shortcode_tags;

	// Find all registered tag names in $content.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $shortcodes, $matches );
	$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

	$pattern = get_shortcode_regex( $tagnames );

	$i = 0;
	while(strpos($shortcodes, '[') !== false) {
		$i++;
		$new_shortcodes = preg_replace_callback( "/$pattern/", 'get_shortcode_font', $shortcodes );
		// content will stop to change when all shortcodes parsed
		if ($new_shortcodes!==$shortcodes) {
			// update content and continue parsing
			$shortcodes = $new_shortcodes;
		}
		else {
			// all parsed, stop the loop
			break;
		}
		// bulletproof way to stop the loop, I doubt anyone will have 100000+ shortcodes on one page 
		if ($i > 100000) break;
	}
	
	// class based fonts
	$classes = get_option( "ct_components_classes", array() );
	
	if (!$classes) {
		$classes = array();
	}

	// and also custom selectors fonts
	$selectors = get_option( "ct_custom_selectors", array() );
	
	if (!$selectors) {
		$selectors = array();
	}
	
	$classes = array_merge($classes,$selectors);

	if(is_array($classes)) {
		foreach($classes as $key => $class) {
			foreach($class as $statekey => $state) {
				if($statekey == 'media') {
					foreach($state as $bpkey => $bp) {
						foreach($bp as $bpstatekey => $bpstate) {
							foreach($bpstate as $property_key => $value) {
								if(strpos($property_key, 'font-family')!==false) {
									if ( is_array( $value ) ) {
										// handle global fonts
										if ( $value[0] == 'global' ) {
											
											$settings 	= get_option("ct_global_settings"); 
											$value 		= $settings['fonts'][$value[1]];
										}
									}
									else {
										$value = htmlspecialchars_decode($value, ENT_QUOTES);
									}

									// skip empty values
									if ( $value === "" ) {
										continue;
									}

									// make font family accessible for web fonts loader
									$header_font_families[] = "$value";
								}
							}
						}
					}
				}
				else {
					if (is_array($class[$statekey])) {
						foreach ($class[$statekey] as $property_key => $value) {	
					  		if(strpos($property_key, 'font-family')!==false) {
								$value = $class[$statekey][$property_key];
								if ( is_array( $value ) ) {
									// handle global fonts
									if ( $value[0] == 'global' ) {
										
										$settings 	= get_option("ct_global_settings"); 
										$value 		= isset($settings['fonts'][$value[1]])?$settings['fonts'][$value[1]]:'';
									}
								}
								else {
									$value = htmlspecialchars_decode($value, ENT_QUOTES);
								}

								// skip empty values
								if ( $value === "" ) {
									continue;
								}

								// make font family accessible for web fonts loader
								$header_font_families[] = "$value";			  			
					  		}
					  	}
					}
			  	}
		  	}
	  	}
	}

	$font_families = ct_get_font_families_string( $header_font_families );

	if ( $font_families ) {

		echo "
		<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js'></script>
		<script type=\"text/javascript\">
		WebFont.load({
			google: {
				families: [$font_families]
			}
		});
		</script>
		";
	}
	
}

function get_shortcode_font($m) {

	global $header_font_families;

	$parsed_atts= shortcode_parse_atts( $m[3] );

	if (!isset($parsed_atts['ct_options'])) {
		return substr($m[0], 1, -1);
	}
	$decoded_atts = json_decode( $parsed_atts['ct_options'], true );

	if(!is_array($decoded_atts))
		return substr($m[0], 1, -1);
	
	$states = array();

	// get states styles (original, :hover, ...) from shortcode atts
	foreach ( $decoded_atts as $key => $state_params ) {
		if ( is_array( $state_params ) ) {
			$states[$key] = $state_params;
		}
	}

	foreach ( $states as $key => $atts ) {
		
		//echo $key."\n";
		if ( in_array($key, array("classes", "name", "selector") ) ) {
			continue;
		}

		if( $key == 'media') {

			foreach($atts as $bpkey => $bp) {
				foreach($bp as $bpstatekey => $bpstate) {
					foreach($bpstate as $property_key => $value) {
						if(strpos($property_key, 'font-family')!==false) {
							if ( is_array( $value ) ) {
								// handle global fonts
								if ( $value[0] == 'global' ) {
									
									$settings 	= get_option("ct_global_settings"); 
									$value 		= $settings['fonts'][$value[1]];
								}
							}
							else {
								$value = htmlspecialchars_decode($value, ENT_QUOTES);
							}

							// skip empty values
							if ( $value === "" ) {
								continue;
							}

							// make font family accessible for web fonts loader
							$header_font_families[] = "$value";
		  				}
					}
				}
			}
		}
		else {
			// loop trough properties (background, color, ...)
			foreach ( $atts as $prop => $value ) {					

				if ( is_array( $value ) ) {
					// handle global fonts
					if ( $prop == "font-family" && $value[0] == 'global' ) {
						
						$settings 	= get_option("ct_global_settings"); 
						$value 		= $settings['fonts'][$value[1]];
					}
				}
				else {
					$value = htmlspecialchars_decode($value, ENT_QUOTES);
				}

				// skip empty values
				if ( $value === "" ) {
					continue;
				}

				// make font family accessible for web fonts loader
				if (strpos($prop, 'font-family')!==false ) {
					$header_font_families[] = "$value";
				}

			} // endforeach
		}
		
	}
	
	return substr($m[0], 1, -1);
	
}
add_action( 'wp_head', 'add_web_font', 0 );

/**
 * Set site hash if not exist
 */

function oxygen_update_license_hash() {

	//delete_option("oxygen_license_updated");
	if ( ! get_option("oxygen_license_updated") ) {
		
		$old = get_option( 'oxygen_license_key' );

		if ( $old ) {

			global $oxygen_edd_updater;
			
			update_option( 'oxygen_license_key', '' );
			$oxygen_edd_updater->activate_license();

			update_option( 'oxygen_license_key', $old );
			$oxygen_edd_updater->activate_license();
		}
		else {
			update_option( 'oxygen_license_key', '' );
		}

		update_option("oxygen_license_updated", true);
	}
}
add_action( 'after_setup_theme', 'oxygen_update_license_hash' );


/**
 * Get global settings
 *
 * @since 1.1.1
 * @author Ilya K.
 */

function ct_get_global_settings($return_defaults = false) {

	// get saved settings
	//update_option("ct_global_settings",array());
	$settings = get_option("ct_global_settings",array());
	
	// defaults
	$defaults = array ( 
				"fonts" => array(
						'Text' 		=> 'Open Sans',
						'Display' 	=> 'Source Sans Pro' 
					),
				"indicateParents" => 'true',
				"headings" => array( 
						'H1' => array( 'font-size' => '36', 'font-size-unit' => 'px', 'font-weight' => 700, 'color' => '' ),
						'H2' => array( 'font-size' => '30', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H3' => array( 'font-size' => '24', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H4' => array( 'font-size' => '20', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H5' => array( 'font-size' => '18', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H6' => array( 'font-size' => '16', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						
					),
				"body_text" => 
					array( 
						'font-size' 		=> '16', 
						'font-size-unit' 	=> 'px', 
						'font-weight' 		=> '400', 
						'line-height' 		=> '1.6', 
						'color' 			=> '#404040' 
					),
				"links" => array(
					"all" => 
						array( 
							'color' 			=> '#0074db',
							'font-weight' 		=> '', 
							'text-decoration'	=> 'none',
							'hover_color' 		=> '',
							'hover_text-decoration' => 'none'						
						),
					"text_link" => 
						array( 
							'color' 			=> '',
							'font-weight' 		=> '', 
							'text-decoration'	=> '',
							'hover_color' 		=> '',
							'hover_text-decoration' => ''						
						),
					"link_wrapper" => 
						array( 
							'color' 			=> '',
							'font-weight' 		=> '', 
							'text-decoration'	=> '',
							'hover_color' 		=> '',
							'hover_text-decoration' => ''						
						),
					"button" => 
						array( 
							'font-weight' 			=> '', 
							'border-radius' 		=> '3',
							'border-radius-unit' 	=> 'px',
						),
					),
				"sections" => 
					array( 
						'container-padding-top' 		=> '75',
						'container-padding-top-unit' 	=> 'px',
						'container-padding-bottom' 		=> '75',
						'container-padding-bottom-unit' => 'px',
						'container-padding-left' 		=> '20',
						'container-padding-left-unit' 	=> 'px',
						'container-padding-right' 		=> '20',
						'container-padding-right-unit' 	=> 'px',
					),
				"max-width" => 1120
			);

	if ($return_defaults) {
		return $defaults;
	}
	else {
		return wp_parse_args($settings, $defaults);
	}
}


/**
 * Get page settings
 *
 * @since 1.1.1
 * @author Ilya K.
 */

function ct_get_page_settings( $id ) {

	// get saved settings

	// if it is builder mode, and the aim is to edit inner_content layout, let the outer template's page settings apply
	$ct_inner = defined("SHOW_CT_BUILDER") && isset($_REQUEST['ct_inner'])? true:false;
	if($ct_inner) {

		// get the outer template, either it would be the one defined by ct_render_post_using or generic view
		$ct_render_post_using = get_post_meta($id, 'ct_render_post_using', true);
		$ct_other_template = false;

		if($ct_render_post_using && $ct_render_post_using == 'other_template') {
			$ct_other_template = get_post_meta($id, 'ct_other_template', true);
		}

		if(!$ct_other_template || $ct_other_template == 0) { // get the generic template
			
			if(intval($id) == intval(get_option('page_on_front')) || intval($id) == intval(get_option('page_for_posts'))) {
				$template = ct_get_archives_template( $id );
			}
			else {
				$template = ct_get_posts_template( $id );
			}

			if($template) {
				$id = $template->ID;
			}
		}
		else
			$id = $ct_other_template;
	}

	$settings = get_post_meta( $id, "ct_page_settings", true );

	// defaults
	$settings = wp_parse_args( 
		$settings,
		array(
			"max-width" => ""
		)
	);

	return $settings;
}


/**
 * Minify CSS
 *
 * @since 1.1.1
 * @author Ilya K.
 */

function oxygen_css_minify( $css ) {
	
	// Remove comments
	$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

	// Remove space after colons
	$css = str_replace(': ', ':', $css);

	// Remove new lines and tabs
	$css = str_replace(array("\r\n", "\r", "\n", "\t"), '', $css);

	// Remove excessive spaces
	$css = str_replace(array("     ", "    ", "   ", "  "), ' ', $css);

	// Remove space near commas
	$css = str_replace(', ', ',', $css);
	$css = str_replace(' ,', ',', $css);

	// Remove space before/after brackets
	$css = str_replace('{ ', '{', $css);
	$css = str_replace('} ', '}', $css);
	$css = str_replace(' {', '{', $css);
	$css = str_replace(' }', '}', $css);

	// Remove last semicolon
	$css = str_replace(';}', '}', $css);

	// Remove spaces after semicolon
	$css = str_replace('; ', ';', $css);

	return $css;
}

/**
 * Return body class string in response to GET request
 *
 * @since 1.4
 * @author Ilya K. 
 */

function ct_get_body_class() {

	if ( isset( $_GET['ct_get_body_class'] ) && $_GET['ct_get_body_class'] ) {
		echo join( ' ', get_body_class() );
		die();
	}
}
add_action( 'template_redirect', 'ct_get_body_class' );


/**
 * returns the query_vars for the provided single ID
 * used the logic from $wp->process_request()
 *
 * @since 0.3.4
 * @author gagan goraya
 */

function get_query_vars_from_id($id = false) {

	if(!$id)
		return array();
	
	global $wp_rewrite, $wp;

	$public_query_vars = $wp->public_query_vars;
	$private_query_vars = $wp->private_query_vars;

	$permalink = get_permalink($id);
	$extra_query_vars = '';

	// if permalinks not enabeld
	if(!get_option('permalink_structure')) {
		list($temp, $extra_query_vars) = explode('?', $permalink);
	}

	$query_vars = array();
	$post_type_query_vars = array();

	if ( !is_array( $extra_query_vars ) && !empty( $extra_query_vars )) {
		parse_str( $extra_query_vars, $extra_query_vars );
	}
	// Process PATH_INFO, REQUEST_URI, and 404 for permalinks.

	// Fetch the rewrite rules.
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	if ( ! empty($rewrite) ) {
		// If we match a rewrite rule, this will be cleared.
		$error = '404';
		

		$pathinfo = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
		list( $pathinfo ) = explode( '?', $pathinfo );
		$pathinfo = str_replace( "%", "%25", $pathinfo );

		list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );
		$req_uri = str_replace(get_site_url(), '', $permalink);
		
		$home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
		$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

		// Trim path info from the end and the leading home path from the
		// front. For path info requests, this leaves us with the requesting
		// filename, if any. For 404 requests, this leaves us with the
		// requested permalink.
		$req_uri = str_replace($pathinfo, '', $req_uri);
		$req_uri = trim($req_uri, '/');
		$req_uri = preg_replace( $home_path_regex, '', $req_uri );
		$req_uri = trim($req_uri, '/');
		$pathinfo = trim($pathinfo, '/');
		$pathinfo = preg_replace( $home_path_regex, '', $pathinfo );
		$pathinfo = trim($pathinfo, '/');
		
		

		// The requested permalink is in $pathinfo for path info requests and
		//  $req_uri for other requests.
		if ( ! empty($pathinfo) && !preg_match('|^.*' . $wp_rewrite->index . '$|', $pathinfo) ) {
			$request = $pathinfo;
		} else {
			// If the request uri is the index, blank it out so that we don't try to match it against a rule.
			if ( $req_uri == $wp_rewrite->index )
				$req_uri = '';
			$request = $req_uri;
		}

		// Look for matches.
		$request_match = $request;
		if ( empty( $request_match ) ) {

			// An empty request could only match against ^$ regex
			if ( isset( $rewrite['$'] ) ) {
				$matched_rule = '$';
				$query = $rewrite['$'];
				$matches = array('');
			}
		} else {

			foreach ( (array) $rewrite as $match => $query ) {
				// If the requesting file is the anchor of the match, prepend it to the path info.
				if ( ! empty($req_uri) && strpos($match, $req_uri) === 0 && $req_uri != $request )
					$request_match = $req_uri . '/' . $request;

				if ( preg_match("#^$match#", $request_match, $matches) ||
					preg_match("#^$match#", urldecode($request_match), $matches) ) {

					if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
						// This is a verbose page match, let's check to be sure about it.
						$page = get_page_by_path( $matches[ $varmatch[1] ] );
						if ( ! $page ) {
					 		continue;
						}

						$post_status_obj = get_post_status_object( $page->post_status );
						if ( ! $post_status_obj->public && ! $post_status_obj->protected
							&& ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
							continue;
						}
					}

					// Got a match.
					$matched_rule = $match;
					break;
				}
			}

		}

		if ( isset( $matched_rule ) ) {
			// Trim the query of everything up to the '?'.
			$query = preg_replace("!^.+\?!", '', $query);

			// Substitute the substring matches into the query.
			$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

			$matched_query = $query;

			// Parse the query.
			parse_str($query, $perma_query_vars);

			// If we're processing a 404 request, clear the error var since we found something.
			if ( '404' == $error )
				unset( $error, $_GET['error'] );
		}

		// If req_uri is empty or if it is a request for ourself, unset error.
		if ( empty($request) || $req_uri == 'index.php' || strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false ) {
			unset( $error, $_GET['error'] );
		}
	}

	$public_query_vars = apply_filters( 'query_vars', $public_query_vars );

	foreach ( get_post_types( array(), 'objects' ) as $post_type => $t ) {
		if ( is_post_type_viewable( $t ) && $t->query_var ) {
			$post_type_query_vars[$t->query_var] = $post_type;
		}
	}

	foreach ( $public_query_vars as $wpvar ) {

		if ( isset( $extra_query_vars[$wpvar] ) )
			$query_vars[$wpvar] = $extra_query_vars[$wpvar];
		elseif ( isset( $_POST[$wpvar] ) )
			$query_vars[$wpvar] = $_POST[$wpvar];
		elseif ( isset( $_GET[$wpvar] ) )
			$query_vars[$wpvar] = $_GET[$wpvar];
		elseif ( isset( $perma_query_vars[$wpvar] ) )
			$query_vars[$wpvar] = $perma_query_vars[$wpvar];

		if ( !empty( $query_vars[$wpvar] ) ) {
			if ( ! is_array( $query_vars[$wpvar] ) ) {
				$query_vars[$wpvar] = (string) $query_vars[$wpvar];
			} else {
				foreach ( $query_vars[$wpvar] as $vkey => $v ) {
					if ( !is_object( $v ) ) {
						$query_vars[$wpvar][$vkey] = (string) $v;
					}
				}
			}

			if ( isset($post_type_query_vars[$wpvar] ) ) {
				$query_vars['post_type'] = $post_type_query_vars[$wpvar];
				$query_vars['name'] = $query_vars[$wpvar];
			}
		}
	}

	// Convert urldecoded spaces back into +
	foreach ( get_taxonomies( array() , 'objects' ) as $taxonomy => $t )
		if ( $t->query_var && isset( $query_vars[$t->query_var] ) )
			$query_vars[$t->query_var] = str_replace( ' ', '+', $query_vars[$t->query_var] );

	// Don't allow non-public taxonomies to be queried from the front-end.
	if ( ! is_admin() ) {
		foreach ( get_taxonomies( array( 'public' => false ), 'objects' ) as $taxonomy => $t ) {
			/*
			 * Disallow when set to the 'taxonomy' query var.
			 * Non-public taxonomies cannot register custom query vars. See register_taxonomy().
			 */
			if ( isset( $query_vars['taxonomy'] ) && $taxonomy === $query_vars['taxonomy'] ) {
				unset( $query_vars['taxonomy'], $query_vars['term'] );
			}
		}
	}

	// Limit publicly queried post_types to those that are publicly_queryable
	if ( isset( $query_vars['post_type']) ) {
		$queryable_post_types = get_post_types( array('publicly_queryable' => true) );
		if ( ! is_array( $query_vars['post_type'] ) ) {
			if ( ! in_array( $query_vars['post_type'], $queryable_post_types ) )
				unset( $query_vars['post_type'] );
		} else {
			$query_vars['post_type'] = array_intersect( $query_vars['post_type'], $queryable_post_types );
		}
	}

	// Resolve conflicts between posts with numeric slugs and date archive queries.
	$query_vars = wp_resolve_numeric_slug_conflicts( $query_vars );

	foreach ( (array) $private_query_vars as $var) {
		if ( isset($extra_query_vars[$var]) )
			$query_vars[$var] = $extra_query_vars[$var];
	}

	if ( isset($error) )
		$query_vars['error'] = $error;

	
	$query_vars = apply_filters( 'request', $query_vars );

	return $query_vars;
}


/**
 * This is used to offset the IDs of outer template, when inner_content component is used
 *
 * @since 1.2.0
 * @author Gagan S Goraya.
 */

function obfuscate_ids($matches) {
	return $matches[1].((intval($matches[2]) > 0)?(intval($matches[2])+100000):0);
}

function obfuscate_selectors($matches) {
	$id =  intval(substr($matches[2], strrpos($matches[2], '_')+1 , strlen($matches[2])-strrpos($matches[2], '_')-1));
	$prefix = substr($matches[2] , 0, strrpos($matches[2], '_')+1);
	return $matches[1].$prefix.(($id > 0)?($id+100000):0).'_post_';
}

function ct_bgLayersFilterCallback($color) {
	return isset($color['value']) && strlen($color['value']) > 0;
}
function ct_BgLayersMapColorStrings($color) {
	return $color['value'] . ($color['position'] ? ' ' . $color['position'] . $color['position-unit']: '');
}

function ct_filterGradientColors($color) {
	return isset($color['value']) && $color['value'];
}


/**
 * This is used to offset the depths of inner_content shortcodes when it has to be contained within an outer template
 *
 * @since 1.2.0
 * @author Gagan S Goraya.
 */

function ct_offsetDepths($matches) {
	global $ct_offsetDepths_source;
	//print_r($matches);
	$tag = $matches[2];

	$depth = is_numeric($matches[3])?intval($matches[3]):1;
	$newdepth = $depth;
	// if tag has a trailing _, remove it
	if(substr($tag, strlen($tag)-1, 1) == '_')
		$tag = substr($tag, 0, strlen($tag)-1);

	if(isset($ct_offsetDepths_source[$tag])) {
		$newdepth += $ct_offsetDepths_source[$tag];
	}

	return $matches[1].$tag.(($newdepth > 1)?'_'.$newdepth:'');
	
}

function ct_undoOffsetDepths($matches) {
	global $ct_offsetDepths_source;
	//print_r($matches);
	$tag = $matches[2];
	$depth = is_numeric($matches[3])?intval($matches[3]):1;
	$newdepth = $depth;
	// if tag has a trailing _, remove it
	if(substr($tag, strlen($tag)-1, 1) == '_')
		$tag = substr($tag, 0, strlen($tag)-1);

	if(isset($ct_offsetDepths_source[$tag])) {
		$newdepth -= $ct_offsetDepths_source[$tag];
	}
	return $matches[1].$tag.(($newdepth > 1)?'_'.$newdepth:'');
	
}

function set_ct_offsetDepths_source($parent_id, $shortcodes) {

	global $ct_offsetDepths_source;
	$ct_offsetDepths_source = array();
	$last_parent_id = false;
	$matches = array();
	while($parent_id > 0 && $parent_id !== $last_parent_id) {
		
		preg_match_all("/\[(ct_[^\s\[\]\d]*)[_]?([0-9]?)[^\]]*ct_id[\"|\']?:$parent_id\,[\"|\']?ct_parent[\"|\']?:(\d*)\,/", $shortcodes, $matches);
		
		$last_parent_id = $parent_id;
		$parent_id = intval($matches[3][0]);
		$depth = is_numeric($matches[2][0])?intval($matches[2][0]):1;
		$tag = $matches[1][0];

		// if tag has a trailing _, remove it
		if(substr($tag, strlen($tag)-1, 1) == '_')
			$tag = substr($tag, 0, strlen($tag)-1);
		//echo $tag."  ".$depth."  ".$parent_id."\n";

		if(isset($ct_offsetDepths_source[$tag]) ) {
			if($ct_offsetDepths_source[$tag] < $depth) {
				$ct_offsetDepths_source[$tag] = $depth;
			}
		}
		else {
			$ct_offsetDepths_source[$tag] = $depth;
		}

	}
}


/**
 * If post/page has Oxygen template applied return empty stylesheet URL, so theme functions.php never run   
 *
 * @since 1.4
 * @author Ilya K.
 */

function ct_disable_theme_load( $stylesheet_dir ) {

	// disable theme entirely for now
	return "fake";

	if ( isset( $_GET["has_oxygen_template"] ) && $_GET["has_oxygen_template"] ) {
		return $stylesheet_dir;
	}

	if ( defined("HAS_OXYGEN_TEMPLATE") && HAS_OXYGEN_TEMPLATE ) {
		return "";
	}
	else {
		return $stylesheet_dir;
	}
}
// Need to remove for both parent and child themes
add_filter("template_directory", "ct_disable_theme_load", 1, 1);
add_filter("stylesheet_directory", "ct_disable_theme_load", 1, 1);


/**
 * Filter template name so plugins don't confuse Oxygen with any other theme  
 *
 * @since 1.4.1
 * @author Ilya K.
 */

function ct_oxygen_template_name($template) {
	return "oxygen-is-not-a-theme";
}
add_filter("template", "ct_oxygen_template_name");


/**
 * Disable theme validation
 *
 * @since 1.4.1
 * @author Ilya K.
 */

add_filter("validate_current_theme", "__return_false");


/**
 * Send a GET request to that same URL to check if Oxygen template applies
 *
 * @since 1.4
 * @author Ilya K.
 */

function ct_send_has_oxygen_template() {

	if ( defined( 'DOING_AJAX' ) ) {
		return;
	}

	// no need to check when trying to get styles
	if ( isset( $_GET["xlink"] ) && $_GET["xlink"] == "css" ) {
		return;
	}

	// disable theme for builder
	if ( isset( $_GET["ct_builder"] ) && $_GET["ct_builder"] ) {
		define("HAS_OXYGEN_TEMPLATE", true );
		return;
	}

	// prevent a loop
	if ( isset( $_GET["has_oxygen_template"] ) && $_GET["has_oxygen_template"] ) {
		return;
	}

	$response = wp_remote_get( ct_get_current_url( "has_oxygen_template=true" ) );

	if ( ! is_array( $response ) ) {
		define("HAS_OXYGEN_TEMPLATE", false );
	}
	//var_dump($response);
	if ( $response["body"] == "true") {
		define("HAS_OXYGEN_TEMPLATE", true );
	}
	else {
		define("HAS_OXYGEN_TEMPLATE", false );
	}
}
//add_action("plugins_loaded", "ct_send_has_oxygen_template");


/**
 * @param string $type Type of content to filter.  Current options: page_settings, global_settings, style_sheets, 'classes', 'custom_selectors', 'style_sets'
 * @param array $content
 *
 * @return array Filtered array of content
 */
function ct_filter_content( $type, $content = array() ) {

	$filter_keys = false;
	switch ( $type ) {
		case 'page_settings':
			$allowed_content = array( 'max-width' => 'sanitize_text_field' );
			$filter_keys = false;
			break;
		case 'global_settings':
			$allowed_content = array(
				'fonts' => array(
					'Text' => 'sanitize_text_field',
					'Display' => 'sanitize_text_field',
					'/^.*$/' => 'sanitize_text_field'
				),
				'indicateParents' => 'sanitize_text_field',
				'headings' => array(
					'/^.*$/' => array(
						'/^.*$/' => 'sanitize_text_field'
					),
				),
				'body_text' => array(
					'/^.*$/' => 'sanitize_text_field'
				),
				'links' => array(
					'/^.*$/' => array(
						'/^.*$/' => 'sanitize_text_field'
					),
				),
				'sections' => array(
					'/^.*$/' => 'sanitize_text_field'
				),
				'max-width' => 'sanitize_text_field'
			);
			$filter_keys = false;
			break;
		case 'style_sheets':
			$allowed_content = array(
				'/^.*$/' => array(
					'css' => 'base64_encode',
					'id' => 'intval',
					'name' => 'sanitize_html_class',
					'status' => 'intval',
					'parent' => 'intval',
					'folder' => 'intval',
					'/^.*$/' => 'sanitize_text_field'
				)
			);
			//$filter_keys     = 'sanitize_html_class';
			break;
		case 'classes':
			$allowed_content = array(
				'/^.*$/' => array(  // Class name
					'key' => 'sanitize_html_class',
					'parent' => 'sanitize_text_field',
					'media' => array(
						'/^.*$/' => array(  // breakpoints
							'/^.*$/' => array(  // States
								'/font-family$/'  => 'font-family',
								'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
							)
						)
					),
					'/^.*$/' => array(  // States
						'/font-family$/'  => 'font-family',
						'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
					)
				)
			);
			$filter_keys = 'sanitize_html_class';
			break;
		case 'custom_selectors':
			$allowed_content = array(
				'/^.+$/' => array(  // Class name
					'key' => 'sanitize_text_field',
					'status' => 'intval',
					'set_name' => 'sanitize_text_field',
					'friendly_name' => 'sanitize_text_field',
					'parent' => 'sanitize_text_field',
					'media' => array(
						'/^.*$/' => array(  // breakpoints
							'/^.*$/' => array(  // States
								'/font-family$/'  => 'font-family',
								'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
							),
						)
					),
					'/^.*$/' => array(  // States
						'/font-family$/' => 'font-family',
						'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
					)
				)
			);
			$filter_keys = 'sanitize_text_field';
			break;
		case 'style_sets':
			$allowed_content = array(
				'/^.*$/' => array(
					'key' => 'sanitize_text_field',
					'parent' => 'sanitize_text_field',
					'status' => 'intval'
				)
			);
			$filter_keys = 'sanitize_text_field';
			break;
		case 'style_folders':
			$allowed_content = array(
				'/^.*$/' => array(
					'key' => 'sanitize_text_field',
					'status' => 'intval'
				)
			);
			$filter_keys = 'sanitize_text_field';
			break;
		case 'easy_posts_templates':
		case 'comments_list_templates':
			$allowed_content = array('/^.*$/' => array(
					'code_css' => 'base64_encode',
					'code_php' => 'base64_encode',
					'name' => 'sanitize_text_field',
					'/^.*$/' => 'sanitize_text_field'
				));
			$filter_keys = false;
			break;
		case 'typekit_fonts':
			$allowed_content = array( 
				'/^.*$/' => array(
					'/^.*$/' => 'sanitize_text_field'
				),
			);
			$filter_keys = false;
			break;
		default:
		    $allowed_content = array();
            $filter_keys = false;

	}
	// Allow plugins to adjust the filters of content
	$allowed_content =  apply_filters( 'oxygen_vsb_component_filter_content_allowed', $allowed_content, $type, $content, $filter_keys );
	
	$new_content = ct_filter_array_recursive( $content, $allowed_content, $filter_keys );
	
	// Allow plugins to expand content that are allowed to be used
	return apply_filters( 'oxygen_vsb_component_filter_content', $new_content, $type, $content, $filter_keys );
}

/**
 * Filter a single piece of content
 * @param string $data Content to be filtered
 * @param string|boolean $filter Name of callable function to use for filtering
 *
 * @return bool|mixed Filtered content
 */
function ct_filter_single_content( $data, $filter ) {
	if($filter == 'unset') {
		return '';
	}
	elseif($filter == 'font-family') {
		if ( is_array($data) ) {
			return ct_filter_array_recursive($data, array('/^.*$/' => 'sanitize_text_field'));
		}
		else {
			return sanitize_text_field($data);
		}
	}
	elseif ( is_callable( $filter ) ) {
		return call_user_func( $filter, $data );
	} elseif ( false === $filter ) {
		return false;
	}
	return $data;
}

/**
 * Recursively filter $data array with functions in $filter array
 *
 * @param string|array $data Array to be filtered
 * @param string|array $filter Array containing filters
 * @param string|boolean $filter_keyname Function to call to filter name of keys or false to not filter
 *
 * @return array Filtered array
 */
function ct_filter_array_recursive( $data, $filter, $filter_keyname = false ) {

	if ( is_array( $filter ) ) {
		$new_data = array();
		foreach ( $filter as $filter_key => $filter_value ) {
			// Walk filter array matching regexp and absolute matches)
			if ( isset( $data[ $filter_key ] ) ) {
				// Handle literal filters
				if ( isset( $filter_keyname ) && is_callable( $filter_keyname ) ) {
					$new_key = call_user_func( $filter_keyname, $filter_key );
				} else {
					$new_key = $filter_key;
				}

				if ( is_array( $filter_value ) ) {
					$new_data[ $new_key ] = ct_filter_array_recursive( $data[ $filter_key ], $filter_value, $filter_keyname );
				} else {
					$new_data[ $new_key ] = ct_filter_single_content( $data[ $filter_key ], $filter_value );
				}
			} elseif ( '/' === $filter_key[0] && is_array($data) && sizeof($data) > 0) {
				// Key regexp
				$matched_keys = preg_grep( $filter_key, array_keys( $data ) );
				foreach ( $matched_keys as $key ) {
					if ( isset( $filter_keyname ) && is_callable( $filter_keyname ) ) {
						$new_key = call_user_func( $filter_keyname, $key );
					} else {
						$new_key = $key;
					}
					if ( !isset( $new_data[ $new_key ] ) ) {
					    // Only allow entry to be filtered by first match
						$new_data[ $new_key ] = ct_filter_array_recursive( $data[ $key ], $filter_value, $filter_keyname );
					}
				}
			}

		}
	} else {
		return ct_filter_single_content( $data, $filter );
	}
	return $new_data;

}

/**
 * Listen for a template check, return proper flag and exit the script
 *
 * @since 1.4
 * @author Ilya K.
 */

function ct_has_oxygen_template() {
	if ( isset( $_GET["has_oxygen_template"] ) && $_GET["has_oxygen_template"] ) {
		echo ( ct_template_output(true) ) ? "true" : "false";
		die;
	}
}
//add_action("wp", "ct_has_oxygen_template");


/**
 * Hook to run on Oxygen plugin activation
 *
 * @since 1.4.1
 * @author Ilya K.
 */

function oxygen_activate_plugin() {

	set_transient('oxygen-vsb-just-activated', '1');

	// Register CPT the right way
	ct_add_templates_cpt(); // it also hooked into 'init'
	flush_rewrite_rules();
	// set flag
	update_option("oxygen_rewrite_rules_updated", "1");
}
register_activation_hook( CT_PLUGIN_MAIN_FILE, 'oxygen_activate_plugin' );
// flush rules on deactivation
register_deactivation_hook( CT_PLUGIN_MAIN_FILE, 'flush_rewrite_rules' );

add_action( 'wp_insert_post', 'ct_post_meta_on_new_reusable' );

function ct_post_meta_on_new_reusable( $post_id ) {
    $post_type = get_post_type($post_id);

    if($post_type === 'ct_template') {
    	$is_reusable = isset($_REQUEST['is_reusable'])?true: false;

    	if($is_reusable) {
    		add_post_meta( $post_id, 'ct_template_type', 'reusable_part' );
    	}
    
    }

}


/**
 * Get all Stylesheets CSS
 * Taken from csslink.php and wraped in a function
 *
 * @since 2.0
 * @author Ilya K.
 */

function oxygen_vsb_get_stylesheet_styles() {
	
	$styles = "";
	$style_sheets = get_option( "ct_style_sheets", array() );

	if ( is_array( $style_sheets ) ) {

		foreach( $style_sheets as $key => $value ) {

			if(!is_array($value)) { // if it is the old style sheets data
				$styles .= base64_decode( $style_sheets[$key] );
			}
			else {
				$disabled = false;
				
				if( !$disabled && intval($style_sheets[$key]['parent']) === -1) {
					$disabled = true;
				}

				if( !$disabled && $style_sheets[$key]['parent'] !== 0 ) {
					// get the parent
					foreach($style_sheets as $item) {
						if($item['id'] === $style_sheets[$key]['parent']) { // this is the parent
							if($item['status'] === 0) {
								$disabled = true;
							}
						}
					}
				}

				if(!$disabled) {
					$styles .= base64_decode( $style_sheets[$key]['css'] );
				}
			}
		}
	}

	return $styles;
}


/**
 * Get all Custom Selectors CSS
 * Taken from csslink.php and wraped in a function
 *
 * @since 2.0
 * @author Ilya/Gagan
 */

function oxygen_vsb_get_custom_selectors_styles() {

	global $media_queries_list;

	$selectors = get_option( "ct_custom_selectors" );
	$styleSets = get_option( "ct_style_sets" );
	$styleFolders = get_option( "ct_style_folders");
	$css = "";

	if ( is_array( $selectors ) ) {
		foreach ( $selectors as $selector => $states ) {

			if(!(

				(!isset($states['set_name']) || !isset($styleSets[$states['set_name']]) || !isset($styleSets[$states['set_name']]['parent']) || !isset($styleFolders[$styleSets[$states['set_name']]['parent']]) || !isset($styleFolders[$styleSets[$states['set_name']]['parent']]['status']) || intval($styleFolders[$styleSets[$states['set_name']]['parent']]['status']) === 1)
				
			)) {
				continue;
			}

			if(isset($styleSets[$states['set_name']]) && intval($styleSets[$states['set_name']]['parent']) === -1) {
				continue;
			}

			foreach ( $states as $state => $options ) {

				if (in_array($state, array("set_name", "key", "parent", "status", "friendly_name"))) {
					continue;
				}	

				if ( $state == 'media' ) {

					$sorted_media_queries_list = ct_sort_media_queries();
					
					foreach ( $sorted_media_queries_list as $media_name => $media ) {

						if ($media_name == "page-width") {
							$max_width = oxygen_vsb_get_page_width($ct_template_id).'px';
						}
						else {
							$max_width = $media_queries_list[$media_name]['maxSize'];
						}
						
						if ( $options[$media_name] && $media_name != "default") {
							$css .= "@media (max-width: $max_width) {\n";
							foreach ( $options[$media_name] as $media_state => $media_options ) {
								$css .= ct_generate_class_states_css($selector, $media_state, $media_options, true, true);
							}
							$css .= "}\n\n";
						}
					}
				}
				else {
					$css = ct_generate_class_states_css($selector, $state, $options, false, true).$css;
				}
			}
		}
	}

	return $css;
}
