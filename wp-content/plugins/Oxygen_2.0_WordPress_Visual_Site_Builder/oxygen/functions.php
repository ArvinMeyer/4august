<?php

/*
Plugin Name: Oxygen
Author: Soflyy
Author URI: https://oxygenbuilder.com
Description: If you can do it with WordPress, you can design it with Oxygen.
Version: 2.0
Text Domain: oxygen
*/

define("CT_VERSION", 	"2.0");
define("CT_FW_PATH", 	plugin_dir_path( __FILE__ )  . 	"component-framework" );
define("CT_FW_URI", 	plugin_dir_url( __FILE__ )  . 	"component-framework" );
define("CT_PLUGIN_MAIN_FILE", __FILE__ );	

global $ct_component_categories;
$ct_component_categories = array(
	'Headers',
    'Heros & Titles',
    'Content',
    'Showcase',
    'Social Proof',
    'People',
    'Pricing',
    'Call To Action',
    'Contact',
    'Sliders, Tabs, & Accordions',
    'Blog',
    'Footers'
);

global $ct_source_sites;

$source_sites = get_option('oxygen_vsb_source_sites');

$ct_source_sites = array();

if($source_sites) {
	
	$lines = explode("\r\n", $source_sites);

	foreach($lines as $line) {

		$line = trim($line);

		if(empty($line)) {
			continue;
		}

		if(!empty($line) && strpos($line, '=>') !== false) {
			$exploded = explode('=>', $line);
			$ct_source_sites[trim($exploded[0])] = trim($exploded[1]);
		}
	}

}

require_once("component-framework/component-init.php");
