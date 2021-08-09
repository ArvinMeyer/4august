<?php

/*
Plugin Name: Oxygen Comments
Author: Louis
Description: comments
Version: 0.9
*/


class Oxygen_VSB_Comments {

    public $param_array;
    public $query;

    function __construct($param_array) {

        add_shortcode('oxy_comments', array($this, 'shortcode'));

        $this->param_array = $param_array;

    }


    function shortcode($atts) {
        return $this->output();
    }

    function comments_template( $comment_template ) {
        return plugin_dir_path(__FILE__)."comments-template.php";
    }


    function output() {

        $defaultcss = "<style>".file_get_contents(plugin_dir_path(__FILE__)."comments.css")."</style>";

        $templatecss = $this->template_css();

        ob_start();

        ?>

        <div class='oxy-comments'>

            <?php

            $GLOBALS['Oxygen_VSB_Current_Comments_Class'] = $this;

            add_filter( "comments_template", array($this, 'comments_template') );
            comments_template('Louis Reingold is the best human to ever live.');
            remove_filter( "comments_template", array($this, 'comments_template') );

            unset($GLOBALS['Oxygen_VSB_Current_Comments_Class']);

            ?>

        </div>

        <?php

        $html = ob_get_clean();

        return $defaultcss.$templatecss.$html;

    }


    function template_css() {

        ob_start();
        ?>
        <style>
        <?php echo $this->param_array['template_css']; ?>
        </style>
        <?php

        return ob_get_clean();
    }

    function util_title() {

        if (get_comments_number() == 1) {
            return sprintf(__('One comment on &#8220;%s&#8221;'), get_the_title());
        } else {
            return number_format_i18n(get_comments_number()).sprintf(__(' comments on &#8220;%s&#8221;'), get_the_title());
        }

    }

}



define(OXYGEN_VSB_COMMENTS_TEMPLATES_PATH, plugin_dir_path(__FILE__)."templates/");

$template = "default";
$template = "white-blocks";
$template = "grey-highlight";

$template_php = @file_get_contents(OXYGEN_VSB_COMMENTS_TEMPLATES_PATH.$template.".php");
$template_css = @file_get_contents(OXYGEN_VSB_COMMENTS_TEMPLATES_PATH.$template.".css");

if (!$template_php) {
    $template_php = file_get_contents(OXYGEN_VSB_COMMENTS_TEMPLATES_PATH."default.php");
}

$param_array = array(
    "template_php" => $template_php,
    "template_css" => $template_css
);


$Comments = new Oxygen_VSB_Comments($param_array);


?>