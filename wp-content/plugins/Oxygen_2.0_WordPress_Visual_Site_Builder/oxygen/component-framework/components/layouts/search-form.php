<?php 

/**
 * Get Search Form instance and return rendered HTML.
 * Editing something here also edit it in ajax.php!
 * 
 * @since 2.0
 * @author Ilya K.
 */

oxygen_vsb_ajax_request_header_check();
echo do_shortcode("[oxy_search_form preview=true]");