<?php

/*
 * Oxygen Dynamic Shortcodes
 * Author:      Louis
*/

class Oxygen_VSB_Dynamic_Shortcodes {

	private $query;

	function oxygen_vsb_add_shortcode() {
		add_shortcode('oxygen', array($this, 'oxygen_vsb_dynamic_shortcode'));
	}

	function oxygen_vsb_dynamic_shortcode($atts) {

		// replace single quotes in atts
		foreach($atts as $key => $item) {
			$atts[$key] = str_replace('__SINGLE_QUOTE__', "'", $item);
		}

		global $wp_query;

		$query_vars = $wp_query->query_vars;

		$this->query = new WP_Query($query_vars);

		if(!is_page()) {
			$this->query->the_post();
		}
		
		$handler = 'oxygen_'.$atts['data'];

		if (method_exists($this, $handler)) {

			$output = call_user_func(array($this, $handler), $atts);

		} else {

			return "No such function ".$handler;

		}

		/* if link parameter is set, wrap output with an <a> tag and set the link URL to whatever is returned by the function with the name of the value of the link parameter */
		$link_handler = 'oxygen_'.$atts['link'];

		if (method_exists($this, $link_handler)) {
			$link_output = call_user_func(array($this, $link_handler), $atts);

			if ($link_output) {
				return "<a href='".$link_output."'>".$output."</a>";
			} else {
				return $output;
			}
		} else {
			return $output;
		}

	}

	function oxygen_phpfunction($atts) {

		$my_function = $atts['function'];

		$args = explode(',', $atts['arguments']);

		if(function_exists($my_function)) {
			
			return call_user_func_array($my_function, $args);
			
		} else {
			return 'function does not exist';
		}

	}

	function oxygen_title($atts) {
		return get_the_title();
	}

	function oxygen_content($atts) {
		global $post;
		ob_start();
		// When called "do_shortcode" from within the edit post/page in WordPress backend, we are not in the loop and the_content() returns an empty string
		if( !in_the_loop() ) {
			// When permalinks are set to "plain", global $post variable is null
			if( is_null( $post ) && !empty( $_GET[ 'post' ] ) ) $post = get_post( filter_var( $_GET[ 'post' ], FILTER_SANITIZE_NUMBER_INT) );
			// Simulate a loop
			setup_postdata( $post, null, false );
		}
		the_content();
		return ob_get_clean();

	}

	function oxygen_archive_title($atts) {
		return get_the_archive_title();
	}

	function oxygen_archive_description($atts) {
		return get_the_archive_description();
	}

	function oxygen_excerpt($atts) {
		return get_the_excerpt();
	}


	function oxygen_terms($atts) {
		$separator = $atts['separator'];
		$taxonomy = $atts['taxonomy'];

		return get_the_term_list(get_the_ID(), $taxonomy, null, $separator, null );

	}


	function oxygen_featured_image($atts) {
		$size = $atts['size'];

		if (strpos($size, ",")!==FALSE) {
			$size = explode(',', $size);
		}

		// user can either pass size as 200,100, i.e. width,height, or a registered thumbnail size, i.e. "large" or whatever

		$thumbnail = get_the_post_thumbnail_url(null, $size);

		if (!$thumbnail) {
			return $atts['default'];
		} else {
			return $thumbnail;
		}

	}


	function oxygen_featured_image_title($atts) {
		return @get_post(get_post_thumbnail_id())->post_title;
	}

	function oxygen_featured_image_caption($atts) {
		return @get_post(get_post_thumbnail_id())->post_excerpt;
	}

	function oxygen_featured_image_alt($atts) {
	    return @get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
	}


	function oxygen_comments_link($atts) {
	    return get_comments_link();
	}





	function oxygen_comments_number($atts) {
		$zero = $atts['zero'];
		$one = $atts['one'];
		$more = $atts['more'];

		ob_start();

		if ($zero && $one && $more) {
			comments_number( $zero, $one, $more );
		} else {
			comments_number();
		}

		return ob_get_clean();
	}

	function oxygen_meta($atts) {
		return get_post_meta(get_the_ID(), $atts['key'], true);
	}

	function oxygen_date($atts) {
		return get_the_date($atts['format']);
	}

	function oxygen_permalink($atts) {
		return get_permalink();
	}

	function oxygen_author($atts) {
		return get_the_author();
	}

	function oxygen_author_website_url($atts) {
		return get_the_author_meta('url');
	}

	function oxygen_author_posts_url($atts) {
		return get_author_posts_url(get_the_author_meta('ID'));
	}

	function oxygen_author_bio($atts) {
		return get_the_author_meta('description');
	}

	function oxygen_author_pic($atts) {
		return get_avatar_url(get_the_author_meta('email'), $atts['size']);
	}

	function oxygen_author_meta($atts) {
		return get_the_author_meta($atts['meta_key']);
	}

	function oxygen_bloginfo($atts) {
		return get_bloginfo($atts['show']);
	}



	function oxygen_get_userdata($id) {
		if (!$id) {
			$id = get_current_user_id();
		}

		$userdata = get_userdata($id);

		return $userdata;
	}


	function oxygen_user($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);
		if($userdata)
			return $userdata->user_nicename;
		else
			return '';

	}

	function oxygen_user_website_url($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);

		if($userdata)
			return $userdata->user_url;
		else
			return '';

	}

	function oxygen_user_bio($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);

		if($userdata)
			return $userdata->user_description;
		else
			return '';

	}

	function oxygen_user_pic($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);
		
		return get_avatar_url($userdata->user_email, $atts['size']);

	}

	function oxygen_user_meta($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);

		return get_user_meta($userdata->ID, $atts['meta_key'], true);

	}
}

$oxygen_VSB_Dynamic_Shortcodes = new Oxygen_VSB_Dynamic_Shortcodes();

$oxygen_VSB_Dynamic_Shortcodes->oxygen_vsb_add_shortcode();