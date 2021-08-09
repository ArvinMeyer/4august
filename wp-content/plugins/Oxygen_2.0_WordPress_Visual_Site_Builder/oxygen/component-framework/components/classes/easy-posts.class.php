<?php

/**
 * Easy Posts Component Class
 * 
 * @since 2.0
 */

class Oxygen_VSB_Easy_Posts extends CT_Component {

    public $param_array = array();
    public $css_util;
    public $query;
    public $action_name = "oxy_render_easy_posts";
    public $template_file = "easy-posts.php"; 

    function __construct($options) {

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_dynamic", array( $this, "component_button" ) );

        // output styles
        add_filter("ct_footer_styles", array( $this, "template_css" ) );
        add_filter("ct_footer_styles", array( $this, "params_css" ) );

        // add specific options to Basic Styles tab
        add_action("ct_toolbar_component_settings", array( $this, "settings") );
        
        // output list of templates
        add_action("ct_builder_ng_init", array( $this, "templates_list") );

        // render preveiew with AJAX
        add_filter("template_include", array( $this, "single_template"), 100 );
    }

    
    /**
     * Add a [oxy_posts_grid] shortcode to WordPress
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

        $this->register_properties($options['id']);

        $this->param_array[$options['id']] = shortcode_atts(
            array(
                "template" => 'grid-image-standard',
                "code_php" => '',
                "code_css" => '',
                "wp_query" => 'default',
                "query_args" => 'author_name=admin&category_name=uncategorized&posts_per_page=2',
                "title_size" => '36',
                "title_size_unit" => 'px',
                "title_color" => 'blue',
                "title_hover_color" => 'red',
                "meta_size" => '12',
                "meta_size_unit" => 'px',
                "meta_color" => 'black',
                "content_size" => '21',
                "content_size_unit" => 'px',
                "content_color" => 'black',
                "read_more_display_as" => 'button',
                "read_more_size" => '16',
                "read_more_size_unit" => 'px',
                "read_more_text_color" => 'blue',
                "read_more_text_hover_color" => 'black',
                "read_more_button_color" => 'green',
                "read_more_button_hover_color" => '#8888ff',
                "paginate_color" => '#00aa00',
                "paginate_alignment" => 'center',
                "paginate_link_color" => 'blue',
                "paginate_link_hover_color" => 'orange',
                "posts_per_page" => 7,
                "posts_5050_below" => 'tablet',
                "posts_100_below" => 'phone-landscape',
                "query_post_types" => '',
                "query_post_ids" => '',
                "query_taxonomies_any" => '',
                "query_taxonomies_all" => '',
                "query_order_by" => '',
                "query_order" => '',
                "query_authors" => '',
                "query_count" => '',
                "query_all_posts" => '',
                "query_ignore_sticky_posts" => 'true',
            ), $options, $this->options['tag'] );

        $this->param_array[$options['id']]["selector"] = esc_attr($options['selector']);

        $posts = $this->get_the_posts($options['id']);

        // make sure errors are shown
        $error_reporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $display_errors = ini_get('display_errors');
        ini_set('display_errors', 1); 

        ob_start(); ?>
        
        <?php if ($atts['preview']!='true') : ?>
        <div id="<?php echo esc_attr($options['selector']); ?>" class='oxy-easy-posts <?php echo esc_attr($options['classes']); ?>'>
        <?php endif; ?>
            <div class='oxy-posts'>
                <?php while ($this->query->have_posts()) {
                    $this->query->the_post();
                    eval("?> ".$options['code_php']."<?php ");
                } ?>
            </div>
            <?php if (!$this->param_array[$options['id']]['query_count']) : ?>
            <div class='oxy-easy-posts-pages'>
                <?php
                $big = 999999999; // need an unlikely integer
                echo paginate_links( array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '?paged=%#%',
                    'current' => max( 1, get_query_var('paged') ),
                    'total' => $this->query->max_num_pages,
                ) );
                ?>
            </div>
            <?php endif; ?>
        <?php if ($atts['preview']!='true') : ?>
        </div>
        <?php endif; ?>
        <?php

        // set errors params back
        ini_set('display_errors', $display_errors); 
        error_reporting($error_reporting);

        // output template CSS for builder preview only
        if ($atts['preview']=='true' || $_REQUEST['action'] == "ct_get_post_data") {
            $code_css   = $options['code_css'];
            $code_css   = str_replace("%%EPID%%", "#".$options['selector'], $code_css);

            echo "<style type=\"text/css\" class='oxygen-easy-posts-ajax-styles'>";
            echo $code_css;
            echo "</style>\r\n";
        }

        return ob_get_clean();
    }


    /**
     * Map parameters to CSS properties
     *
     * @since 2.0
     * @author Louis
     */

    function register_properties($id) {

        $this->cssutil[$id] = new Oxygen_VSB_CSS_Util;

        $this->cssutil[$id]->register_selector('.oxy-post-title');
        $this->cssutil[$id]->register_selector('.oxy-post-title:hover');
        $this->cssutil[$id]->register_selector('.oxy-post-meta');
        $this->cssutil[$id]->register_selector('.oxy-post-content');
        $this->cssutil[$id]->register_selector('.oxy-read-more');
        $this->cssutil[$id]->register_selector('.oxy-read-more:hover');
        $this->cssutil[$id]->register_selector('.oxy-easy-posts-pages');
        $this->cssutil[$id]->register_selector('.oxy-easy-posts-pages a.page-numbers');
        $this->cssutil[$id]->register_selector('.oxy-easy-posts-pages a.page-numbers:hover');

        $this->cssutil[$id]->map_property('title_size', 'font-size',                         '.oxy-post-title');
        $this->cssutil[$id]->map_property('title_color', 'color',                            '.oxy-post-title');
        $this->cssutil[$id]->map_property('title_hover_color', 'color',                      '.oxy-post-title:hover');
        $this->cssutil[$id]->map_property('meta_size', 'font-size',                          '.oxy-post-meta');
        $this->cssutil[$id]->map_property('meta_color', 'color',                             '.oxy-post-meta');
        $this->cssutil[$id]->map_property('content_size', 'font-size',                       '.oxy-post-content');
        $this->cssutil[$id]->map_property('content_color', 'color',                          '.oxy-post-content');
        $this->cssutil[$id]->map_property('read_more_size', 'font-size',                     '.oxy-read-more');
        $this->cssutil[$id]->map_property('read_more_text_color', 'color',                   '.oxy-read-more');
        $this->cssutil[$id]->map_property('read_more_button_color', 'background-color',      '.oxy-read-more');
        $this->cssutil[$id]->map_property('read_more_text_hover_color', 'color',             '.oxy-read-more:hover');
        $this->cssutil[$id]->map_property('read_more_button_hover_color', 'background-color','.oxy-read-more:hover');
        $this->cssutil[$id]->map_property('paginate_color', 'color',                         '.oxy-easy-posts-pages');
        $this->cssutil[$id]->map_property('paginate_alignment', 'text-align',                '.oxy-easy-posts-pages');
        $this->cssutil[$id]->map_property('paginate_link_color', 'color',                    '.oxy-easy-posts-pages a.page-numbers');
        $this->cssutil[$id]->map_property('paginate_link_hover_color', 'color',              '.oxy-easy-posts-pages a.page-numbers:hover');

        $this->cssutil[$id]->register_contingency_function(array($this, 'read_more_button_contingency'));
        $this->cssutil[$id]->register_css_output_function(array($this, 'responsive'));
    }

    
    /**
     * Output specific button CSS
     *
     * @since 2.0
     * @author Louis
     */

    function read_more_button_contingency($selectors, $id) {

        $readmore = $selectors['.oxy-read-more'];
        $readmorehover = $selectors['.oxy-read-more:hover'];

        if ($this->param_array[$id]['read_more_display_as'] == 'button') {

            $readmore['text-decoration'] = 'none';
            $readmore['padding'] = '0.75em 1.5em';
            $readmore['line-height'] = '1';
            $readmore['border-radius'] = '3px';
            $readmore['display'] = 'inline-block';

            $readmorehover['text-decoration'] = 'none';

        } else {
            unset($readmore['background-color']);
            unset($readmorehover['background-color']);
        }

        $selectors['.oxy-read-more'] = $readmore;
        $selectors['.oxy-read-more:hover'] = $readmorehover;

        return $selectors;
    }


    /**
     * Output specific responsive CSS
     *
     * @since 2.0
     * @author Louis & Ilya
     */

    function responsive($id) {

        global $media_queries_list;

        ob_start();

        if ($this->param_array[$id]['posts_5050_below']) { ?>
            @media (max-width: <?php echo $media_queries_list[$this->param_array[$id]['posts_5050_below']]['maxSize']; ?>) {
                #<?php echo $this->param_array[$id]["selector"]; ?> .oxy-post {
                    width: 50% !important;
                }
            }
            <?php
        }

        if ($this->param_array[$id]['posts_100_below']) { ?>
            @media (max-width: <?php echo $media_queries_list[$this->param_array[$id]['posts_100_below']]['maxSize']; ?>) {
                #<?php echo $this->param_array[$id]["selector"]; ?> .oxy-post {
                    width: 100% !important;
                }
            }
            <?php
        }

        return ob_get_clean();
    }


    /**
     * Output CSS based on user params
     *
     * @since 2.0
     * @author Louis
     */

    function params_css() {

        foreach ($this->param_array as $id => $params) {

            echo $this->cssutil[$id]->generate_css($params, $id);
        }
    }


    /**
     * Output specific template CSS
     *
     * @since 2.0
     * @author Louis
     */

    function template_css() {

        foreach ($this->param_array as $params) {

            $code_css   = $params['code_css'];
            $code_css   = str_replace("%%EPID%%", "#".$params['selector'], $code_css);

            echo $code_css;
        }
    }


    /**
     * Setup the query 
     *
     * @since 2.0
     * @author Ilya K.
     */

    function get_the_posts($id) {

        // manual
        if ($this->param_array[$id]['query_args']&&$this->param_array[$id]['wp_query']=='manual') {

            $args = $this->param_array[$id]['query_args'];
            /* https://wordpress.stackexchange.com/questions/120407/how-to-fix-pagination-for-custom-loops 
            apparently doesn't work on static front pages? */
            $args .= get_query_var( 'paged' ) ? '&paged='.get_query_var( 'paged' ) : '';
        }

        // query builder
        elseif ($this->param_array[$id]['query_args']&&$this->param_array[$id]['wp_query']=='custom') {
            
            $args = array();
            
            // post type
            if ($this->param_array[$id]['query_post_ids']) {
                $args['post__in'] = explode(",",$this->param_array[$id]['query_post_ids']);
            }
            else {
                $args['post_type'] = $this->param_array[$id]['query_post_types'];
            }

            // filtering
            if (is_array($this->param_array[$id]['query_taxonomies_any'])) {
                
                $taxonomies = array();
                $args['tax_query'] = array(
                    'relation' => 'OR',
                );

                // sort IDs by taxonomy slug
                foreach ($this->param_array[$id]['query_taxonomies_any'] as $value) {
                    $value = explode(",",$value);
                    $key = $value[0];
                    if ($key == "tag") {
                        $key = "post_tag";
                    }
                    $taxonomies[$key][] = $value[1];
                }

                foreach ($taxonomies as $key => $value) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $key,
                        'terms'    => $value,
                    );
                }
            }
            if (is_array($this->param_array[$id]['query_taxonomies_all'])&&!empty($this->param_array[$id]['query_taxonomies_all'])) {
                
                $taxonomies = array();
                $args['tax_query'] = array(
                    'relation' => 'AND',
                );

                // sort IDs by taxonomy slug
                foreach ($this->param_array[$id]['query_taxonomies_all'] as $value) {
                    $value = explode(",",$value);
                    $key = $value[0];
                    if ($key == "tag") {
                        $key = "post_tag";
                    }
                    $taxonomies[$key][] = $value[1];
                }

                foreach ($taxonomies as $key => $value) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $key,
                        'terms'    => $value,
                        'operator' => 'AND'
                    );
                }
            }
            if ($this->param_array[$id]['query_authors']) {
                $args['author__in'] = $this->param_array[$id]['query_authors'];
            }

            // order
            $args['order']   = $this->param_array[$id]['query_order'];
            $args['orderby'] = $this->param_array[$id]['query_order_by'];

            if ($this->param_array[$id]['query_all_posts']==='true') {
                $args['nopaging'] = true;
            }

            if ($this->param_array[$id]['query_ignore_sticky_posts']==='true') {
                $args['ignore_sticky_posts'] = true;
            }

            if ($this->param_array[$id]['query_count']) {
                $args['posts_per_page'] = $this->param_array[$id]['query_count'];
            }
            
            // pagination
            if (get_query_var('paged')&&!$this->param_array[$id]['query_count']) {
                $args['paged'] = get_query_var( 'paged' );
            }
        }

        // default
        else {
            // use current query
            global $wp_query;
            $this->query = $wp_query;

            return;
            // $args = $wp_query->query;
            
            // // pagination
            // //$args['posts_per_page'] = $this->param_array[$options['id']]['posts_per_page'];
            // if (get_query_var('paged')) {
            //     $args['paged'] = get_query_var( 'paged' );
            // }
        }

        $this->query = new WP_Query($args);
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
            ng-hide="!isActiveName('oxy_posts_grid')">

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('easyPosts', 'query')" 
                ng-show="!hasOpenTabs('easyPosts')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Query", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('easyPosts', 'styles')" 
                ng-show="!hasOpenTabs('easyPosts')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                    <?php _e("Styles", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('easyPosts', 'templates')" 
                ng-show="!hasOpenTabs('easyPosts')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/code.svg">
                    <?php _e("Templates", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div
                ng-show="!hasOpenTabs('easyPosts')">
                <div class='oxygen-control-row' style="margin-top:30px">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Load Settings from Preset","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-select oxygen-select-box-wrapper oxygen-presets-dropdown">
                                <div class="oxygen-select-box">
                                    <div class="oxygen-select-box-current">{{iframeScope.lastSetEasyPostsTemplate}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                   <div class="oxygen-select-box-option"
                                        ng-repeat="(id,template) in iframeScope.easyPostsDefaultTemplates"
                                        ng-click="$parent.iframeScope.setEasyPostsTemplate(template);"
                                        title="<?php _e("Load Template", "oxygen"); ?>">
                                            {{template.name}}
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-repeat="(id,template) in iframeScope.easyPostsCustomTemplates"
                                        ng-click="$parent.iframeScope.setEasyPostsTemplate(template);"
                                        title="<?php _e("Load Template", "oxygen"); ?>">
                                            <div>{{template.name}}</div>
                                            <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/remove_icon.svg'
                                                title="<?php _e("Remove template", "oxygen"); ?>"
                                                ng-click="iframeScope.deleteEasyPostsTemplate(id,$event)"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Save Current Settings as Preset","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-input-with-button oxygen-new-easy-posts-preset">
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.newEasyPostsTemplate"/>
                                <div class="oxygen-input-button"
                                    ng-click="iframeScope.addEasyPostsTemplate()">
                                    <?php _e("save","oxygen"); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-if="isShowTab('easyPosts','styles')">
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.easyPosts=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.easyPosts=[]"><?php _e("Easy Posts","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Styles","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('easyPosts', 'title')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Title", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('easyPosts', 'meta')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Meta", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('easyPosts', 'content')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Content", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('easyPosts', 'readMore')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Read More", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('easyPosts', 'responsive')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                        <?php _e("Responsive", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>

            <div ng-if="isShowTab('easyPosts', 'title')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Title","oxygen"); ?></div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('title_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("title_color", __("Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("title_hover_color", __("Hover Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('easyPosts', 'meta')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Meta","oxygen"); ?></div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('meta_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("meta_color", __("Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('easyPosts', 'content')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Content","oxygen"); ?></div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('content_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("content_color", __("Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('easyPosts', 'readMore')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Read More","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Display as","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('read_more_display_as','button'); ?>
                                <?php $oxygen_toolbar->button_list_button('read_more_display_as','text link'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('read_more_size',__('Font size','oxygen')); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_text_color", __("Text Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_text_hover_color", __("Text Hover Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_button_color", __("Button Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("read_more_button_hover_color", __("Button Hover Color", "oxygen") ); ?>
                </div>

            </div>

            <div ng-if="isShowTab('easyPosts', 'responsive')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'styles')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'styles')"><?php _e("Styles","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Responsive","oxygen"); ?></div>
                </div>

                <?php $oxygen_toolbar->media_queries_list_with_wrapper("posts_5050_below", __("Posts are 50% Width Below","oxygen"), true); ?>
                
                <?php $oxygen_toolbar->media_queries_list_with_wrapper("posts_100_below", __("Posts are 100% Width Below","oxygen"), true); ?>

            </div>

            <div ng-if="isShowTab('easyPosts','templates')">
                
                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.easyPosts=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.easyPosts=[]"><?php _e("Easy Posts","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Templates","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('easyPosts', 'templatePHP');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/phphtml.svg">
                        <?php _e("Template PHP", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

                <div class="oxygen-sidebar-advanced-subtab" 
                    ng-click="switchTab('easyPosts', 'templateCSS');expandSidebar();">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/css.svg">
                        <?php _e("Template CSS", "oxygen"); ?>
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('easyPosts', 'templatePHP')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'templates')"><?php _e("Templates","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("PHP","oxygen"); ?></div>
                </div>

                <div class="oxygen-sidebar-code-editor-wrap">
                    <textarea ui-codemirror="{
                        lineNumbers: true,
                        newlineAndIndent: false,
                        mode: 'php',
                        onLoad : codemirrorLoaded
                    }" <?php $this->ng_attributes('code-php'); ?>></textarea>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar oxygen-control-row-bottom-bar-code-editor">
                    <a href="#" class="oxygen-code-editor-apply"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
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
                ng-if="isShowTab('easyPosts', 'templateCSS')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'templates')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'templates')"><?php _e("Templates","oxygen"); ?></div>
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
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
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
                ng-if="isShowTab('easyPosts', 'query')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.easyPosts=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.easyPosts=[]"><?php _e("Easy Posts","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Query","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("WP Query","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('wp_query', 'default'); ?>
                                <?php $oxygen_toolbar->button_list_button('wp_query', 'custom'); ?>
                                <?php $oxygen_toolbar->button_list_button('wp_query', 'manual'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query']=='manual'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Query Params","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-textarea">
                                <textarea class="oxygen-textarea-textarea"
                                    <?php $this->ng_attributes('query_args'); ?>></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['wp_query']=='custom'">
                    
                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('easyPosts', 'postType')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Post Type", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('easyPosts', 'filtering')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Filtering", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('easyPosts', 'order')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Order", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                    <div class="oxygen-sidebar-advanced-subtab" 
                        ng-click="switchTab('easyPosts', 'count')">
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
                            <?php _e("Count", "oxygen"); ?>
                            <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
                    </div>

                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('easyPosts', 'postType')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Post Type","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Post Type", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <select id="oxy-easy-posts-post-type" name="oxy-easy-posts-post-type[]" multiple="multiple"
                                ng-init="initSelect2('oxy-easy-posts-post-type','Choose custom post types...')"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_post_types']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_post_types')">
                                <?php $custom_post_types = get_post_types();
                                $exclude_types  = array( "ct_template", "nav_menu_item", "revision" );
                                foreach($custom_post_types as $item) {
                                    if(!in_array($item, $exclude_types)) {?>
                                        <option value="<?php echo esc_attr( $item ); ?>"><?php echo sanitize_text_field( $item ); ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Or manually specify IDs (comma separated)", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_post_ids']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_post_ids')">
                            </div>
                        </div>
                    </div>

                </div>
                    
                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('easyPosts', 'filtering')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Filtering","oxygen"); ?></div>
                </div>

                <?php
                    $query_taxonomies = array(
                        'query_taxonomies_any' => __("In Any of the Following Taxonomies", "oxygen"),
                        'query_taxonomies_all' => __("Or In All of the Following Taxonomies", "oxygen")
                    );
                ?>

                <?php foreach ($query_taxonomies as $key => $value) : ?>
                    
                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php echo $value; ?></label>
                        <div class='oxygen-control'>
                            <select name="oxy-easy-posts-<?php echo $key; ?>[]" id="oxy-easy-posts-<?php echo $key; ?>" multiple="multiple"
                                ng-init="initSelect2('oxy-easy-posts-<?php echo $key; ?>','Choose taxonomies...')"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo $key; ?>']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','<?php echo $key; ?>')">
                                <?php 
                                // get default post categories
                                $default_categories = get_categories(array('hide_empty' => 0));
                                ?>
                                    <optgroup label="<?php echo __('Categories', 'component-theme'); ?>">
                                        <?php 
                                        foreach ( $default_categories as $category ) : ?>
                                            <option value="<?php echo ((!isset($alloption) || !$alloption)?'category,':'').esc_attr( $category->term_id ); ?>">
                                                <?php echo sanitize_text_field( $category->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php
                                // get default post tags
                                $default_tags = get_tags(array('hide_empty' => 0));
                                ?>
                                    <optgroup label="<?php echo __('Tags', 'component-theme'); ?>">
                                        <?php 
                                        foreach ( $default_tags as $tag ) : ?>
                                            <option value="<?php echo ((!isset($alloption) || !$alloption)?'tag,':'').esc_attr( $tag->term_id ); ?>">
                                                <?php echo sanitize_text_field( $tag->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php
                                // get custom taxonomies
                                $args = array(
                                        "_builtin" => false
                                    );
                                $taxonomies = get_taxonomies( $args, 'object' );
                                foreach ( $taxonomies as $taxonomy ) : 
                                    $args = array(
                                        'hide_empty'    => 0,
                                        'taxonomy'      => $taxonomy->name,
                                    );
                                    $categories = get_categories( $args );
                                    if ( !isset($selected_items[$taxonomy->name]) || !$selected_items[$taxonomy->name] ) {
                                        $selected_items[$taxonomy->name] = array();
                                    }
                                    ?>
                                    <optgroup label="<?php echo sanitize_text_field( $taxonomy->labels->name . " (" . $taxonomy->name . ")" ); ?>">
                                        <?php foreach ( $categories as $category ) : ?>
                                            <option value="<?php echo ((!isset($alloption) || !$alloption)?$category->taxonomy.',':'').esc_attr( $category->term_id ); ?>">
                                                <?php echo sanitize_text_field( $category->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("By the following authors", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <select id="oxy-easy-posts-authors" name="oxy-easy-posts-authors[]" multiple="multiple"
                                ng-init="initSelect2('oxy-easy-posts-authors','Choose authors...')"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_authors']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_authors')">
                                <?php // get all users to loop
                                $authors = get_users( array( 'who' => 'authors' ) );
                                foreach ( $authors as $author ) : ?>
                                    <option value="<?php echo esc_attr( $author->ID ); ?>">
                                        <?php echo sanitize_text_field( $author->user_login ); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php $custom_post_types = get_post_types(); ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('easyPosts', 'order')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Order","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Order By","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box">
                                    <div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('query_order_by')}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','');"
                                        title="<?php _e("Unset order by", "oxygen"); ?>">
                                        &nbsp;
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','date');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Date", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','modified');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Date Last Modified", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','title');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Title", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','comment_count');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Comment Count", "oxygen"); ?>
                                    </div>
                                    <div class="oxygen-select-box-option"
                                        ng-click="$parent.iframeScope.setOptionModel('query_order_by','menu_order');"
                                        title="<?php _e("Set order by", "oxygen"); ?>">
                                        <?php _e("Menu Order", "oxygen"); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Order","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('query_order', 'ASC', 'ascending'); ?>
                                <?php $oxygen_toolbar->button_list_button('query_order', 'DESC', 'descending'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>

            </div>


            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('easyPosts', 'count')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="switchTab('easyPosts', 'query')">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="switchTab('easyPosts', 'query')"><?php _e("Query","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Count","oxygen"); ?></div>
                </div>

                <div class="oxygen-control-row">
                    <label class='oxygen-control-label'><?php _e("How Many Posts?", "oxygen"); ?></label>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class="oxygen-checkbox">
                            <input type="checkbox"
                                ng-true-value="'true'" 
                                ng-false-value="'false'"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_all_posts']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_all_posts')">
                            <div class='oxygen-checkbox-checkbox'
                                ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('query_all_posts')=='true'}">
                                <?php _e("All","oxygen"); ?>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="oxygen-control-row">
                    <div class='oxygen-control-wrapper'>
                        <label class="oxygen-checkbox">
                            <input type="checkbox"
                                ng-true-value="'true'" 
                                ng-false-value="'false'"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_ignore_sticky_posts']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_ignore_sticky_posts')">
                            <div class='oxygen-checkbox-checkbox'
                                ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('query_ignore_sticky_posts')=='true'}">
                                <?php _e("Ignore Sticky Posts","oxygen"); ?>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class='oxygen-control-wrapper'>
                    <label class='oxygen-control-label'><?php _e("or specify the number", "oxygen"); ?></label>
                    <div class='oxygen-control'>
                        <div class='oxygen-input'>
                            <input type="text" spellcheck="false"
                                ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['query_count']"
                                ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_posts_grid','query_count')">
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row oxygen-control-row-bottom-bar">
                    <a href="#" class="oxygen-apply-button"
                        ng-click="iframeScope.renderComponentWithAJAX('oxy_render_easy_posts')">
                        <?php _e("Apply Query Params", "oxygen"); ?>
                    </a>
                </div>
            </div>

        </div>

    <?php }


    /**
     * Output list of all available templates
     *
     * @since 2.0
     * @author Ilya K.
     */

    function templates_list() {
        
        define("OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH", plugin_dir_path(__FILE__)."easy-posts-templates/"); 

        // defaults
        $default_templates = array(
                'grid-image-standard' => array(
                        "name" => __("Grid - Image - Standard","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-standard.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-standard.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"page-width","posts_100_below":"phone-landscape"}
',
                    ),
                'grid-image-w-animated-dark-gradient-overlay' => array(
                        "name" => __("Grid - Image w/ Animated Dark Gradient Overlay","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-animated-dark-gradient-overlay.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-animated-dark-gradient-overlay.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"white","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"white","read_more_display_as":"button","read_more_size":"","read_more_size-unit":"px","read_more_text_color":"","read_more_text_hover_color":"","read_more_button_color":"","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"page-width","posts_100_below":"phone-landscape"}',
                    ),
                'grid-image-w-gradient-overlap' => array(
                        "name" => __("Grid - Image w/ Gradient Overlap","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-gradient-overlap.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-gradient-overlap.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"page-width","posts_100_below":"phone-landscape"}',
                    ),
                'grid-image-w-rectangle-overlap' => array(
                        "name" => __("Grid - Image w/ Rectangle Overlap","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-rectangle-overlap.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-rectangle-overlap.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"page-width","posts_100_below":"tablet"}',
                    ),
                'grid-image-w-title-overlay' => array(
                        "name" => __("Grid - Image w/ Title Overlay","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-title-overlay.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."grid-image-w-title-overlay.css"),
                        "styles_json" => '{"title_size":"2","title_size-unit":"em","title_color":"white","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"","read_more_size-unit":"px","read_more_text_color":"","read_more_text_hover_color":"","read_more_button_color":"","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"page-width","posts_100_below":"phone-landscape"}',
                    ),
                
                'list-image-on-left' => array(
                        "name" => __("List - Image on Left","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-image-on-left.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-image-on-left.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"text link","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"","read_more_text_hover_color":"","read_more_button_color":"","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}'
                    ),
                'list-standard-centered' => array(
                        "name" => __("List - Standard (Centered)","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-standard-centered.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-standard-centered.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}'
                    ),
                'list-standard-left' => array(
                        "name" => __("List - Standard (Left)","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-standard-left.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-standard-left.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}'
                    ),
                'list-w-rectangle-overlap-centered' => array(
                        "name" => __("List w/ Rectangle Overlap (Centered)","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-w-rectangle-overlap-centered.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-w-rectangle-overlap-centered.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}'
                    ),
                'list-w-rectangle-overlap-left' => array(
                        "name" => __("List w/ Rectangle Overlap (left)","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-w-rectangle-overlap-left.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."list-w-rectangle-overlap-left.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}'
                    ),

                'masonry-image-standard' => array(
                        "name" => __("Masonry - Image - Standard","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-standard.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-standard.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}
',
                    ),
                'masonry-image-w-gradient-overlap' => array(
                        "name" => __("Masonry - Image w/ Gradient Overlap","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-w-gradient-overlap.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-w-gradient-overlap.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}
',
                    ),
                'masonry-image-w-rectangle-overlap' => array(
                        "name" => __("Masonry - Image w/ Rectangle Overlap","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-w-rectangle-overlap.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-w-rectangle-overlap.css"),
                        "styles_json" => '{"title_size":"","title_size-unit":"px","title_color":"","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"0.8","read_more_size-unit":"em","read_more_text_color":"white","read_more_text_hover_color":"","read_more_button_color":"black","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}
',
                    ),
                'masonry-image-w-title-overlay' => array(
                        "name" => __("Masonry - Image w/ Title Overlay","oxygen"),
                        "code_php" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-w-title-overlay.php"),
                        "code_css" => file_get_contents(OXYGEN_VSB_EASY_POSTS_TEMPLATES_PATH."masonry-image-w-title-overlay.css"),
                        "styles_json" => '{"title_size":"2","title_size-unit":"em","title_color":"white","title_hover_color":"","meta_size":"","meta_size-unit":"px","meta_color":"","content_size":"","content_size-unit":"px","content_color":"","read_more_display_as":"button","read_more_size":"","read_more_size-unit":"px","read_more_text_color":"","read_more_text_hover_color":"","read_more_button_color":"","read_more_button_hover_color":"","paginate_color":"","paginate_alignment":"center","paginate_link_color":"","paginate_link_hover_color":"","posts_5050_below":"never","posts_100_below":"never"}',
                    ),
                
            );

        //update_option("oxygen_vsb_easy_posts_templates",array());
        $custom_templates = get_option("oxygen_vsb_easy_posts_templates",array());

        foreach ($default_templates as $key => $value) {
            $styles = json_decode($default_templates[$key]['styles_json'], true);
            //var_dump($styles);
            if (is_array($styles)) {
                $default_templates[$key] = array_merge($default_templates[$key],$styles);
            }
            unset($default_templates[$key]['styles_json']);
        }

        $output = json_encode( $default_templates );
        $output = htmlspecialchars( $output, ENT_QUOTES );

        echo "easyPostsDefaultTemplates=$output;";

        foreach ($custom_templates as $key => $value) {
            $custom_templates[$key]['code_php'] = base64_decode($custom_templates[$key]['code_php']);
            $custom_templates[$key]['code_css'] = base64_decode($custom_templates[$key]['code_css']);
        }

        $output = json_encode( $custom_templates );
        $output = htmlspecialchars( $output, ENT_QUOTES );

        echo "easyPostsCustomTemplates=$output;";
    }

}

// Create component instance
$easy_posts = new Oxygen_VSB_Easy_Posts( array(
            'name'  => __('Easy Posts','oxygen'),
            'tag'   => 'oxy_posts_grid',
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                "other" => array(
                    "values" => array(
                        "template" => 'grid-image-standard',
                        "code_php" => '',
                        "code_css" => '',
                        "wp_query" => 'default',
                        "query_args" => 'author_name=admin&category_name=uncategorized&posts_per_page=2',
                        // styles
                        "title_size" => '',
                        "title_size-unit" => 'px',
                        "title_color" => '',
                        "title_hover_color" => '',
                        "meta_size" => '',
                        "meta_size-unit" => 'px',
                        "meta_color" => '',
                        "content_size" => '',
                        "content_size-unit" => 'px',
                        "content_color" => '',
                        "read_more_display_as" => 'button',
                        "read_more_size" => '',
                        "read_more_size-unit" => 'px',
                        "read_more_text_color" => '',
                        "read_more_text_hover_color" => '',
                        "read_more_button_color" => '',
                        "read_more_button_hover_color" => '',
                        "paginate_color" => '',
                        "paginate_alignment" => '',
                        "paginate_link_color" => '',
                        "paginate_link_hover_color" => '',
                        "posts_per_page" => '',
                        "posts_5050_below" => '',
                        "posts_100_below" => '',
                        // query
                        "query_post_types" => '',
                        "query_post_ids" => '',
                        "query_taxonomies_all" => '',
                        "query_taxonomies_any" => '',
                        "query_order_by" => '',
                        "query_order" => '',
                        "query_authors" => '',
                        "query_count" => '',
                        "query_all_posts" => '',
                        "query_ignore_sticky_posts" => 'true',
                    )
                )
            ),
            'not_css_params' => array(
                        "template",
                        "wp_query",
                        "query_args",
                        "title_size",
                        "title_size-unit",
                        "title_color",
                        "title_hover_color",
                        "meta_size",
                        "meta_size-unit",
                        "meta_color",
                        "content_size",
                        "content_size-unit",
                        "content_color",
                        "read_more_display_as",
                        "read_more_size",
                        "read_more_size-unit",
                        "read_more_text_color",
                        "read_more_text_hover_color",
                        "read_more_button_color",
                        "read_more_button_hover_color",
                        "paginate_color",
                        "paginate_alignment",
                        "paginate_link_color",
                        "paginate_link_hover_color",
                        "posts_per_page",
                        "posts_5050_below",
                        "posts_100_below",
                        "query_post_types",
                        "query_post_ids",
                        "query_taxonomies_all",
                        "query_taxonomies_any",
                        "query_order_by",
                        "query_order",
                        "query_authors",
                        "query_count",
                        "query_all_posts",
                        "query_ignore_sticky_posts",
            )
        ));