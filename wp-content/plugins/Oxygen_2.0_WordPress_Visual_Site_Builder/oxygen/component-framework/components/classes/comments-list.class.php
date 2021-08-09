<?php

/**
 * Comment List Component Class
 * 
 * @since 2.0
 */

class Oxygen_VSB_Comments_List extends CT_Component {

    public $param_array;

    function __construct($options) {

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxy_folder_wordpress_components", array( $this, "component_button" ) );

        // output styles
        add_filter("ct_footer_styles", array( $this, "template_css" ) );

        // add specific options to Basic Styles tab
        add_action("ct_toolbar_component_settings", array( $this, "settings") );

        // render preveiew with AJAX
        add_filter("template_include", array( $this, "single_template"), 100 );
        
        // output list of templates
        add_action("ct_builder_ng_init", array( $this, "templates_list") );
    }

    
    /**
     * Add a [oxy_comments] shortcode to WordPress
     *
     * @since 2.0
     * @author Louis & Ilya
     */

    function add_shortcode( $atts ) {

        $options = $this->set_options( $atts );

        if(isset(json_decode($atts['ct_options'])->original)) {
            if(isset(json_decode($atts['ct_options'])->original->{'code-php'}) ) {
                $options['code_php'] =  base64_decode($options['code_php']);
            }
            if(isset(json_decode($atts['ct_options'])->original->{'code-css'}) ) {
                $options['code_css'] =  base64_decode($options['code_css']);
            }
        }

        $this->param_array = shortcode_atts(
            array(
                "template" => 'default',
                "code_php" => '',
                "code_css" => '',
            ), $options, $this->options['tag'] );

        $this->param_array["selector"] = esc_attr($options['selector']);

        // make sure errors are shown
        $error_reporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $display_errors = ini_get('display_errors');
        ini_set('display_errors', 1); 
        $output = '';
        ob_start(); ?>

        <?php if (!$atts['preview']) : ?>
        <div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>">
        <?php endif;

            $GLOBALS['Oxygen_VSB_Current_Comments_Class'] = $this;

            add_filter( "comments_template", array($this, 'comments_template') );
            comments_template('Louis Reingold is the best human to ever live.');
            remove_filter( "comments_template", array($this, 'comments_template') );

            unset($GLOBALS['Oxygen_VSB_Current_Comments_Class']);

        if (!$atts['preview']) : ?>
        </div>
        <?php endif; 
        $output = ob_get_clean();
        if(empty(trim($output))) {
            $output = '<div class="oxygen-empty-comments-list"></div>';
        }
        
        ob_start();
        
        // output template CSS for builder preview only
        if ($atts['preview']=='true' || $_REQUEST['action'] == "ct_get_post_data") {
            echo "<style>".file_get_contents(plugin_dir_path(__FILE__)."comments-list-templates/comments.css");

            $code_css   = $this->param_array['code_css'];
            $code_css   = str_replace("%%ELEMENT_ID%%", $options['selector'], $code_css);

            echo $code_css."</style>";
        }

        return $output.ob_get_clean();
    }

    
    /**
     * Output specific template CSS
     *
     * @since 2.0
     * @author Louis
     */

    function comments_template( $comment_template ) {

        return plugin_dir_path(__FILE__)."comments-list-templates/comments.php";
    }


    /**
     * Output specific template CSS
     *
     * @since 2.0
     * @author Louis
     */

    function template_css() {

        // required default styles
        echo file_get_contents(plugin_dir_path(__FILE__)."comments-list-templates/comments.css");

        $code_css   = $this->param_array['code_css'];
        $code_css   = str_replace("%%ELEMENT_ID%%", isset($this->options['selector'])?$this->options['selector']:'', $code_css);

        echo $code_css;
    }

    
    /**
     * Output comments title
     *
     * @since 2.0
     * @author Louis
     */

    function util_title() {

        if (get_comments_number() == 1) {
            return sprintf(__('One comment on &#8220;%s&#8221;'), get_the_title());
        } else {
            return number_format_i18n(get_comments_number()).sprintf(__(' comments on &#8220;%s&#8221;'), get_the_title());
        }

    }


    /**
     * Basic Styles settings
     *
     * @since 2.0
     * @author Ilya K.
     */

    function settings () { 

        global $oxygen_toolbar; ?>

        <div class="oxygen-sidebar-flex-panel"
            ng-hide="!isActiveName('oxy_comments')">

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('commentsList', 'templates')" 
                ng-show="!hasOpenTabs('commentsList')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/code.svg">
                    <?php _e("Templates", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div ng-show="!hasOpenTabs('commentsList')">
                <div class='oxygen-control-row' style="margin-top:30px">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Load Preset Template","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-select oxygen-select-box-wrapper oxygen-presets-dropdown">
                                <div class="oxygen-select-box">
                                    <div class="oxygen-select-box-current">{{iframeScope.lastSetCommentsListTemplate}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                   <div class="oxygen-select-box-option"
                                        ng-repeat="(id,template) in iframeScope.commentsListDefaultTemplates"
                                        ng-click="$parent.iframeScope.setCommentsListTemplate(template);"
                                        title="<?php _e("Load Template", "oxygen"); ?>">
                                            {{template.name}}
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-repeat="(id,template) in iframeScope.commentsListCustomTemplates"
                                        ng-click="$parent.iframeScope.setCommentsListTemplate(template);"
                                        title="<?php _e("Load Template", "oxygen"); ?>">
                                            <div>{{template.name}}</div>
                                            <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/remove_icon.svg'
                                                title="<?php _e("Remove template", "oxygen"); ?>"
                                                ng-click="iframeScope.deleteCommentsListTemplate(id,$event)"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Save Current as Preset","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-input-with-button">
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.newCommentsListTemplate"/>
                                <div class="oxygen-input-button"
                                    ng-click="iframeScope.addCommentsListTemplate()">
                                    <?php _e("save","oxygen"); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-if="isShowTab('commentsList','templates')">
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.commentsList=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.commentsList=[]"><?php _e("Comments List","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Templates","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('commentsList', 'templatePHP');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/phphtml.svg">
                        <?php _e("Template PHP", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('commentsList', 'templateCSS');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/css.svg">
                        <?php _e("Template CSS", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('commentsList', 'templatePHP')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('commentsList', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('commentsList', 'templates')"><?php _e("Templates","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("PHP","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-code-editor-wrap">
                    <textarea ui-codemirror="{
                        lineNumbers: true,
                        newlineAndIndent: false,
                        mode: 'php',
                        type: 'php',
                        onLoad : codemirrorLoaded
                    }" <?php $this->ng_attributes('code-php'); ?>></textarea>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
                    <a href="#" class="oxygen-code-editor-apply"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_comments_list')">
                        <?php _e("Apply Code", "oxygen"); ?>
                    </a>
                    <a href="#" class="oxygen-code-editor-expand"
                        data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
                        ng-click="toggleSidebar()">
                        <?php _e("Expand Editor", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('commentsList', 'templateCSS')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('commentsList', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('commentsList', 'templates')"><?php _e("Templates","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("CSS","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-code-editor-wrap">
                    <textarea ui-codemirror="{
                        lineNumbers: true,
                        newlineAndIndent: false,
                        mode: 'css',
                        type: 'css',
                        onLoad : codemirrorLoaded
                    }" <?php $this->ng_attributes('code-css'); ?>></textarea>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
                    <a href="#" class="oxygen-code-editor-apply"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_comments_list')">
                        <?php _e("Apply Code", "oxygen"); ?>
                    </a>
                    <a href="#" class="oxygen-code-editor-expand"
                        data-collapse="<?php _e("Collapse Editor", "oxygen"); ?>" data-expand="<?php _e("Expand Editor", "oxygen"); ?>"
                        ng-click="toggleSidebar()">
                        <?php _e("Expand Editor", "oxygen"); ?>
                    </a>
                </div>

            </div>

        </div>

    <?php }


    /**
     * This function hijacks the template to return special template that renders the code results
     * for the [oxy_comments] element to load the content into the builder for preview
     * 
     * @since 0.4.0
     * @author gagan goraya
     */
    
    function single_template( $template ) {

        $new_template = '';

        if( isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'oxy_render_comments_list') {
            
            if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'comments-list.php') ) {
                $new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'comments-list.php';
            }
        }

        if ( '' != $new_template ) {
            return $new_template ;
        }

        return $template;
    }


    /**
     * Output list of all available templates
     *
     * @since 2.0
     * @author Ilya K.
     */

    function templates_list() {
        
        define("OXYGEN_VSB_COMPONENTS_LIST_TEMPLATES_PATH", plugin_dir_path(__FILE__)."comments-list-templates/"); 

        // defaults
        $default_templates = array(
                'default' => array(
                        "name" => __("Default","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_COMPONENTS_LIST_TEMPLATES_PATH."default.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_COMPONENTS_LIST_TEMPLATES_PATH."default.css"),
                    ),
                'white-blocks' => array(
                        "name" => __("White Blocks","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_COMPONENTS_LIST_TEMPLATES_PATH."default.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_COMPONENTS_LIST_TEMPLATES_PATH."white-blocks.css"),
                    ),
                'grey-highlight' => array(
                        "name" => __("Grey Highlight","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_COMPONENTS_LIST_TEMPLATES_PATH."default.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_COMPONENTS_LIST_TEMPLATES_PATH."grey-highlight.css"),
                    ),
            );

        //update_option("oxygen_vsb_comments_list_templates",array());
        $custom_templates = get_option("oxygen_vsb_comments_list_templates",array());

        $output = json_encode( $default_templates );
        $output = htmlspecialchars( $output, ENT_QUOTES );

        echo "commentsListDefaultTemplates=$output;";

        foreach ($custom_templates as $key => $value) {
            $custom_templates[$key]['code_php'] = base64_decode($custom_templates[$key]['code_php']);
            $custom_templates[$key]['code_css'] = base64_decode($custom_templates[$key]['code_css']);
        }

        $output = json_encode( $custom_templates );
        $output = htmlspecialchars( $output, ENT_QUOTES );

        echo "commentsListCustomTemplates=$output;";
    }

}

// Create component instance
$comments_list = new Oxygen_VSB_Comments_List( array(
            'name'  => __('Comments List','oxygen'),
            'tag'   => 'oxy_comments',
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                "typography" => array(
                    "values" => array(
                        "text-align" => 'left',
                    )
                ),
                "other" => array(
                    "values" => array(
                        "template" => 'default',
                        "code_php" => '',
                        "code_css" => '',
                    )
                )
            ),
            'not_css_params' => array(
                "template",
            )
        ));