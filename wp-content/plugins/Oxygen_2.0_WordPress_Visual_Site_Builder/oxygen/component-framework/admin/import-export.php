<?php

/**
 * Callback to show "Import/Export" on settings page
 *
 * @since 0.2.3
 */

function ct_export_import_callback() {

	// get saved options
	$classes 			= get_option("ct_components_classes", array() );
	$custom_selectors 	= get_option("ct_custom_selectors", array() );
	$style_sheets 		= get_option("ct_style_sheets", array() );
	$global_settings 	= get_option("ct_global_settings", array() );


	if (!is_array($classes)) {
		$classes = array();
	}

	if (!is_array($custom_selectors)) {
		$custom_selectors = array();
	}

	if (!is_array($style_sheets)) {
		$style_sheets = array();
	}

	// import
	if ( isset( $_POST['ct_import_json'] ) ) {

		$import_json = sanitize_text_field( stripcslashes( $_POST['ct_import_json'] ) );

		// check if empty
		if ( empty( $import_json ) ) {
			$import_errors[] = __("Empty Import");
		}
		else {
			// try to decode
			$import_array = json_decode( $import_json, true );

			// update options
			if ( $import_array ) {
				
				// classes
				$classes = array_merge( $classes, $import_array['classes'] );

				if(is_array($classes)) {
					update_option("ct_components_classes", $classes );
				}

				// custom selectors
				$custom_selectors = array_merge( $custom_selectors, $import_array['custom_selectors'] );

				if(is_array($custom_selectors)) {
					update_option("ct_custom_selectors", $custom_selectors );
				}

				// style sheets
				$style_sheets = array_merge( $style_sheets, $import_array['style_sheets'] );

				if(is_array($style_sheets)) {
					update_option("ct_style_sheets", $style_sheets);
				}

				// global settings
				if(is_array($import_array['global_settings'])) {
					update_option("ct_global_settings", $import_array['global_settings']);
				}

				$import_success[] = __("Import success", "component-theme");
			}
			else {
				$import_errors[] = __("Wrong JSON Format", "component-theme");
			}
		}
	}

	// generate export JSON
	$export_json['classes'] 			= $classes;
  	$export_json['custom_selectors'] 	= $custom_selectors;
  	$export_json['style_sheets'] 		= $style_sheets;
  	$export_json['global_settings'] 	= $global_settings;

  	// generate JSON object
	$export_json = json_encode( $export_json );	

	require('views/import-export-page.php');
}

?>
