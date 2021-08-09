<?php
/**
 * Add Dashboard pages/subpages for different settings
 *
 */


/**
 * Main Page
 * 
 * @since 0.2.0
 */

add_action('admin_menu', 'ct_dashboard_main_page');

function ct_dashboard_main_page(){

	if(!current_user_can( 'manage_options' )) {
		return;
	}

	add_menu_page( 	'Oxygen', // page <title>
					'Oxygen', // menu item name
					'manage_options', // capability
					'ct_dashboard_page', // get param
					'ct_oxygen_home_page_view',
					'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIzODFweCIgaGVpZ2h0PSIzODVweCIgdmlld0JveD0iMCAwIDM4MSAzODUiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+ICAgICAgICA8dGl0bGU+VW50aXRsZWQgMzwvdGl0bGU+ICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPiAgICA8ZGVmcz4gICAgICAgIDxwb2x5Z29uIGlkPSJwYXRoLTEiIHBvaW50cz0iMC4wNiAzODQuOTQgMzgwLjgwNSAzODQuOTQgMzgwLjgwNSAwLjYyOCAwLjA2IDAuNjI4Ij48L3BvbHlnb24+ICAgIDwvZGVmcz4gICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+ICAgICAgICA8ZyBpZD0iT3h5Z2VuLUljb24tQ01ZSyI+ICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0id2hpdGUiPiAgICAgICAgICAgICAgICA8dXNlIHhsaW5rOmhyZWY9IiNwYXRoLTEiPjwvdXNlPiAgICAgICAgICAgIDwvbWFzaz4gICAgICAgICAgICA8ZyBpZD0iQ2xpcC0yIj48L2c+ICAgICAgICAgICAgPHBhdGggZD0iTTI5Ny41MDgsMzQ5Ljc0OCBDMjc1LjQ0MywzNDkuNzQ4IDI1Ny41NTYsMzMxLjg2IDI1Ny41NTYsMzA5Ljc5NiBDMjU3LjU1NiwyODcuNzMxIDI3NS40NDMsMjY5Ljg0NCAyOTcuNTA4LDI2OS44NDQgQzMxOS41NzMsMjY5Ljg0NCAzMzcuNDYsMjg3LjczMSAzMzcuNDYsMzA5Ljc5NiBDMzM3LjQ2LDMzMS44NiAzMTkuNTczLDM0OS43NDggMjk3LjUwOCwzNDkuNzQ4IEwyOTcuNTA4LDM0OS43NDggWiBNMjIyLjMwNCwzMDkuNzk2IEMyMjIuMzA0LDMxMi4wMzkgMjIyLjQ0NywzMTQuMjQ3IDIyMi42MzksMzE2LjQ0MSBDMjEyLjMzLDMxOS4wOTIgMjAxLjUyOCwzMjAuNTA1IDE5MC40MDMsMzIwLjUwNSBDMTE5LjAxLDMyMC41MDUgNjAuOTI5LDI2Mi40MjMgNjAuOTI5LDE5MS4wMzEgQzYwLjkyOSwxMTkuNjM4IDExOS4wMSw2MS41NTcgMTkwLjQwMyw2MS41NTcgQzI2MS43OTQsNjEuNTU3IDMxOS44NzcsMTE5LjYzOCAzMTkuODc3LDE5MS4wMzEgQzMxOS44NzcsMjA2LjgzMyAzMTcuMDIsMjIxLjk3OCAzMTEuODE1LDIzNS45OSBDMzA3LjE3OSwyMzUuMDk3IDMwMi40MDQsMjM0LjU5MiAyOTcuNTA4LDIzNC41OTIgQzI1NS45NzQsMjM0LjU5MiAyMjIuMzA0LDI2OC4yNjIgMjIyLjMwNCwzMDkuNzk2IEwyMjIuMzA0LDMwOS43OTYgWiBNMzgwLjgwNSwxOTEuMDMxIEMzODAuODA1LDg2LjA0MiAyOTUuMzkyLDAuNjI4IDE5MC40MDMsMC42MjggQzg1LjQxNCwwLjYyOCAwLDg2LjA0MiAwLDE5MS4wMzEgQzAsMjk2LjAyIDg1LjQxNCwzODEuNDMzIDE5MC40MDMsMzgxLjQzMyBDMjEyLjQ5OCwzODEuNDMzIDIzMy43MDgsMzc3LjYwOSAyNTMuNDU2LDM3MC42NTcgQzI2NS44NDUsMzc5LjY0MSAyODEuMDM0LDM4NSAyOTcuNTA4LDM4NSBDMzM5LjA0MiwzODUgMzcyLjcxMiwzNTEuMzMgMzcyLjcxMiwzMDkuNzk2IEMzNzIuNzEyLDI5Ni4wOTIgMzY4Ljk4OCwyODMuMjgzIDM2Mi41ODQsMjcyLjIxOSBDMzc0LjI1MSwyNDcuNTc1IDM4MC44MDUsMjIwLjA1OCAzODAuODA1LDE5MS4wMzEgTDM4MC44MDUsMTkxLjAzMSBaIiBpZD0iRmlsbC0xIiBmaWxsPSIjMDBCM0MxIiBtYXNrPSJ1cmwoI21hc2stMikiPjwvcGF0aD4gICAgICAgIDwvZz4gICAgPC9nPjwvc3ZnPg==' ); 
	
	add_submenu_page( 	'ct_dashboard_page', 
						'Home', 
						'Home', 
						'manage_options', 
						'ct_dashboard_page');
}

function ct_oxygen_home_page_view(){
	if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    $css = "<style>".file_get_contents(plugin_dir_path(__FILE__)."oxy-admin-screen-home.css")."</style>";

    echo $css;

    include(plugin_dir_path(__FILE__)."oxy-admin-screen-home.php");
    
}

add_action('admin_menu', 'ct_install_wiz_page');

function ct_install_wiz_page() {

	if(!current_user_can( 'manage_options' )) {
		return;
	}

	add_submenu_page( 	'', //ct_dashboard_page to show it in the sub-menu
						'Install Wizard', 
						'Install Wizard', 
						'manage_options', 
						'ct_install_wiz', 
						'ct_install_wiz_callback' );
}

function ct_install_wiz_callback() {
	if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    $css = "<style>".file_get_contents(plugin_dir_path(__FILE__)."oxy-admin-screen-install-wiz.css")."</style>";

    echo $css;

    include(plugin_dir_path(__FILE__)."oxy-admin-screen-install-wiz.php");
    
}

add_action('admin_menu', 'ct_templates_page');

function ct_templates_page() {

	if(!current_user_can( 'manage_options' )) {
		return;
	}

	add_submenu_page( 	'ct_dashboard_page', 
						'Templates', 
						'Templates', 
						'manage_options', 
						'edit.php?post_type=ct_template');

	
}

add_action( 'admin_enqueue_scripts', 'ct_templates_admin_scripts' );

function ct_templates_admin_scripts($hook) {

	global $post;

	if(!is_object($post) || !property_exists($post, 'post_type')) {
		return;
	}

    if ( $hook == 'post.php' || $hook == 'edit.php' ) {
        if ( 'ct_template' === $post->post_type ) {
        	wp_register_script('ct_template_edit_add', CT_FW_URI.'/admin/ct_template_edit_add.js');
        	wp_localize_script( 'ct_template_edit_add', 'ct_template_add_reusable_link', add_query_arg(array('post_type'=>'ct_template', 'is_reusable'=>'true'),admin_url('post-new.php')) ) ;
            wp_enqueue_script(  'ct_template_edit_add' );
        }
    }
	
}




/**
 * SVG Sets
 * 
 * @since 0.2.1
 */

add_action('admin_menu', 'ct_svg_sets_page');

function ct_svg_sets_page() {

	if(!current_user_can( 'manage_options' )) {
		return;
	}

	add_submenu_page( 	'ct_dashboard_page', 
						'SVG Sets', 
						'SVG Sets', 
						'manage_options', 
						'ct_svg_sets', 
						'ct_svg_sets_callback' );
}


/**
 * Export/Import
 * 
 * @since 0.2.1
 */

add_action('admin_menu', 'ct_export_import_page');

function ct_export_import_page() {

	if(!current_user_can( 'manage_options' )) {
		return;
	}
	
	add_submenu_page( 	'ct_dashboard_page', 
						'Export/Import', 
						'Export/Import', 
						'manage_options', 
						'ct_export_import', 
						'ct_export_import_callback' );
}

add_action('admin_menu', 'ct_admin_settings');


/**
 * Settings page
 *
 * @since 2.0
 */ 

function oxygen_vsb_register_settings() {

   add_option( 'oxygen_vsb_source_sites', '');
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_source_sites' );
   
   add_option( 'oxygen_vsb_preview_dropdown_limit', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_preview_dropdown_limit' );

   add_option( 'oxygen_vsb_enable_selector_detector', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_enable_selector_detector' );

   add_option( 'oxygen_vsb_google_maps_api_key', "" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_google_maps_api_key' );

   // added with "register_activation_hook"
   add_option( 'oxygen_vsb_universal_css_cache', "true" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_universal_css_cache' );
}
add_action( 'admin_init', 'oxygen_vsb_register_settings' );

function oxygen_vsb_options_page() {
?>
  <div>
	  <?php screen_icon(); ?>
	  <h2>Oxygen Settings</h2>

	  <form method="post" action="options.php">
	  <?php settings_fields( 'oxygen_vsb_options_group' ); ?>
      <?php do_settings_sections( 'oxygen_vsb_options_group' ); ?>
		  <table>
			  <tr valign="top" style='display: none;'>
				  <th scope="row"><label for="oxygen_vsb_source_sites">Source Sites</label></th>
				  <td><textarea id="oxygen_vsb_source_sites" name="oxygen_vsb_source_sites"><?php echo get_option('oxygen_vsb_source_sites'); ?></textarea></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_preview_dropdown_limit">Preview Dropdown Limit</label></th>
				  <td><input type="number" id="oxygen_vsb_preview_dropdown_limit" name="oxygen_vsb_preview_dropdown_limit" value="<?php echo get_option('oxygen_vsb_preview_dropdown_limit'); ?>"></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_enable_selector_detector"><?php _e("Enable Selector Detector","oxygen"); ?></label></th>
				  <td><input type="checkbox" id="oxygen_vsb_enable_selector_detector" name="oxygen_vsb_enable_selector_detector" value="true" <?php checked(get_option('oxygen_vsb_enable_selector_detector'), "true"); ?>></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_universal_css_cache"><?php _e("Cache Universal CSS","oxygen"); ?></label></th>
				  <td><input type="checkbox" id="oxygen_vsb_universal_css_cache" name="oxygen_vsb_universal_css_cache" value="true" <?php checked(get_option('oxygen_vsb_universal_css_cache'), "true"); ?>></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_google_maps_api_key"><?php _e("Google Maps API key","oxygen"); ?></label></th>
				  <td><input type="text" id="oxygen_vsb_google_maps_api_key" name="oxygen_vsb_google_maps_api_key" value="<?php echo get_option('oxygen_vsb_google_maps_api_key'); ?>"></td>
			  </tr>
		  </table>
		  <?php submit_button(); ?>
	  </form>
  </div>

<?php

}



function ct_admin_settings() {

	add_submenu_page(
			'ct_dashboard_page',
			'Settings',
			'Settings',
			'manage_options',
			'oxygen_vsb_settings',
			'oxygen_vsb_options_page');
}


/**
 * License page
 * 
 * @since 1.4
 */

add_action('admin_menu', 'ct_license_page');

function ct_license_page() {
	add_submenu_page( 	'ct_dashboard_page', 
						'License', 
						'License', 
						'manage_options', 
						'ct_license_page', 
						'ct_license_page_callback' );
}
function ct_license_page_callback() { ?>
	
	<div class="wrap">

		<h2><?php _e("Oxygen License", "component-theme"); ?></h2>
	
		<?php 
		
		/**
		 * Hook for addons to show things in Oxygen admin
		 *
		 * 10 - Oxygen
		 * 20 - Selector Detector
		 * 30 - Typekit
		 *
		 * @since 1.4
		 */
		
		do_action("oxygen_license_admin_screen");
		
		?>

	</div>
	
<?php }
