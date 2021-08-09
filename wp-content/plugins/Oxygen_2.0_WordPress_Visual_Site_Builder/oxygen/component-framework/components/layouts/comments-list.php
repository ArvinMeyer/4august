<?php 

/**
 * Get Comments List instance and return rendered HTML
 * Editing something here also edit it in ajax.php!
 * 
 * @since 2.0
 * @author Ilya K.
 */

oxygen_vsb_ajax_request_header_check();

$component_json = file_get_contents('php://input');
$component 		= json_decode( $component_json, true );
$options 		= $component['options']['original'];

echo do_shortcode("[oxy_comments preview=true ct_options='{\"original\":{\"code-php\":\"".base64_encode($options['code-php'])."\",\"code-css\":\"".base64_encode($options['code-css'])."\"}}']");