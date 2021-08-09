<?php

/**
 * Icon box Component Class
 * 
 * @since 2.0
 * @author Louis & Ilya
 */


class Oxygen_VSB_Pricing_Box extends CT_Component{

    public $param_array = array();
    public $data_array = array();

    function __construct($options) {

        // run initialization
        $this->init( $options );

        // Add shortcodes
        add_shortcode( $this->options['tag'], array( $this, 'shortcode' ) );

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_composite", array( $this, "component_button" ) );

        // add white list options
        add_filter("oxy_options_white_list", array( $this, "white_list_options") );
        // all the options can be unset, so we can use the same white_list_options callback
        add_filter("oxy_allowed_empty_options_list", array( $this, "white_list_options") );
        add_filter("oxy_allowed_empty_options_list", array( $this, "allowed_empty_options") );

        // output styles
        add_filter("oxygen_default_classes_output", array( $this, "css_output" ) );

        // generate defaults styles class
        add_action("oxygen_default_classes_output", array( $this, "generate_defaults_css" ) );

        // generate user styles class
        add_filter("oxygen_user_classes_output", array( $this, "generate_classes_css"), 10, 7);

        // generate #id stlyes
        add_filter("oxy_component_css_styles", array( $this, "generate_id_css"), 10, 5);

        // add specific options
        add_action("ct_toolbar_component_settings", array( $this, "header_settings"), 9 );
    }


    /**
     * Generate CSS for arrays parameters only
     * 
     * @since 2.0
     * @author Ilya
     */

    function white_list_options($options) {

        $options_to_add = array(
            // TODO: make it fetch from params automatically based on some flag

            "pricing_box_global_alignment",

            "pricing_box_graphic_background",
            "pricing_box_graphic_alignment",

            "pricing_box_title_background",
            "pricing_box_title_alignment",

            "pricing_box_price_background",
            "pricing_box_price_alignment",
            "pricing_box_price_layout",
            "pricing_box_price_sale_space_below",

            "pricing_box_content_background",
            "pricing_box_content_alignment",

            "pricing_box_cta_background",
            "pricing_box_cta_alignment",

            "pricing_box_global_padding-top",
            "pricing_box_global_padding-top-unit",
            "pricing_box_global_padding-left",
            "pricing_box_global_padding-left-unit",
            "pricing_box_global_padding-right",
            "pricing_box_global_padding-right-unit",
            "pricing_box_global_padding-bottom",
            "pricing_box_global_padding-bottom-unit",

                            // Graphic padding

            "pricing_box_graphic_padding-top",
            "pricing_box_graphic_padding-top-unit",
            "pricing_box_graphic_padding-left",
            "pricing_box_graphic_padding-left-unit",
            "pricing_box_graphic_padding-right",
            "pricing_box_graphic_padding-right-unit",
            "pricing_box_graphic_padding-bottom",
            "pricing_box_graphic_padding-bottom-unit",
                            
                            // Graphic border

            "pricing_box_graphic_border-all-width",
            "pricing_box_graphic_border-all-width-unit",
            "pricing_box_graphic_border-all-style",
            "pricing_box_graphic_border-all-color",
                            
            "pricing_box_graphic_border-top-width",
            "pricing_box_graphic_border-top-width-unit",
            "pricing_box_graphic_border-top-style",
            "pricing_box_graphic_border-top-color",
                            
            "pricing_box_graphic_border-left-width",
            "pricing_box_graphic_border-left-width-unit",
            "pricing_box_graphic_border-left-style",
            "pricing_box_graphic_border-left-color",
                            
            "pricing_box_graphic_border-right-width",
            "pricing_box_graphic_border-right-width-unit",
            "pricing_box_graphic_border-right-style",
            "pricing_box_graphic_border-right-color",
                            
            "pricing_box_graphic_border-bottom-width",
            "pricing_box_graphic_border-bottom-width-unit",
            "pricing_box_graphic_border-bottom-style",
            "pricing_box_graphic_border-bottom-color",

                            // Title padding

            "pricing_box_title_padding-top",
            "pricing_box_title_padding-top-unit",
            "pricing_box_title_padding-left",
            "pricing_box_title_padding-left-unit",
            "pricing_box_title_padding-right",
            "pricing_box_title_padding-right-unit",
            "pricing_box_title_padding-bottom",
            "pricing_box_title_padding-bottom-unit",
                            
                            // Title border

            "pricing_box_title_border-all-width",
            "pricing_box_title_border-all-width-unit",
            "pricing_box_title_border-all-style",
            "pricing_box_title_border-all-color",
                            
            "pricing_box_title_border-top-width",
            "pricing_box_title_border-top-width-unit",
            "pricing_box_title_border-top-style",
            "pricing_box_title_border-top-color",
                            
            "pricing_box_title_border-left-width",
            "pricing_box_title_border-left-width-unit",
            "pricing_box_title_border-left-style",
            "pricing_box_title_border-left-color",
                            
            "pricing_box_title_border-right-width",
            "pricing_box_title_border-right-width-unit",
            "pricing_box_title_border-right-style",
            "pricing_box_title_border-right-color",
                            
            "pricing_box_title_border-bottom-width",
            "pricing_box_title_border-bottom-width-unit",
            "pricing_box_title_border-bottom-style",
            "pricing_box_title_border-bottom-color",

                            // Price padding

            "pricing_box_price_padding-top",
            "pricing_box_price_padding-top-unit",
            "pricing_box_price_padding-left",
            "pricing_box_price_padding-left-unit",
            "pricing_box_price_padding-right",
            "pricing_box_price_padding-right-unit",
            "pricing_box_price_padding-bottom",
            "pricing_box_price_padding-bottom-unit",
                            
                            // Price border

            "pricing_box_price_border-all-width",
            "pricing_box_price_border-all-width-unit",
            "pricing_box_price_border-all-style",
            "pricing_box_price_border-all-color",
                            
            "pricing_box_price_border-top-width",
            "pricing_box_price_border-top-width-unit",
            "pricing_box_price_border-top-style",
            "pricing_box_price_border-top-color",
                            
            "pricing_box_price_border-left-width",
            "pricing_box_price_border-left-width-unit",
            "pricing_box_price_border-left-style",
            "pricing_box_price_border-left-color",
                            
            "pricing_box_price_border-right-width",
            "pricing_box_price_border-right-width-unit",
            "pricing_box_price_border-right-style",
            "pricing_box_price_border-right-color",
                            
            "pricing_box_price_border-bottom-width",
            "pricing_box_price_border-bottom-width-unit",
            "pricing_box_price_border-bottom-style",
            "pricing_box_price_border-bottom-color",

                            // Content padding

            "pricing_box_content_padding-top",
            "pricing_box_content_padding-top-unit",
            "pricing_box_content_padding-left",
            "pricing_box_content_padding-left-unit",
            "pricing_box_content_padding-right",
            "pricing_box_content_padding-right-unit",
            "pricing_box_content_padding-bottom",
            "pricing_box_content_padding-bottom-unit",
                            
                            // Content border

            "pricing_box_content_border-all-width",
            "pricing_box_content_border-all-width-unit",
            "pricing_box_content_border-all-style",
            "pricing_box_content_border-all-color",
                            
            "pricing_box_content_border-top-width",
            "pricing_box_content_border-top-width-unit",
            "pricing_box_content_border-top-style",
            "pricing_box_content_border-top-color",
                            
            "pricing_box_content_border-left-width",
            "pricing_box_content_border-left-width-unit",
            "pricing_box_content_border-left-style",
            "pricing_box_content_border-left-color",
                            
            "pricing_box_content_border-right-width",
            "pricing_box_content_border-right-width-unit",
            "pricing_box_content_border-right-style",
            "pricing_box_content_border-right-color",
                            
            "pricing_box_cpntent_border-bottom-width",
            "pricing_box_cpntent_border-bottom-width-unit",
            "pricing_box_cpntent_border-bottom-style",
            "pricing_box_cpntent_border-bottom-color",

                            // CTA padding

            "pricing_box_cta_padding-top",
            "pricing_box_cta_padding-top-unit",
            "pricing_box_cta_padding-left",
            "pricing_box_cta_padding-left-unit",
            "pricing_box_cta_padding-right",
            "pricing_box_cta_padding-right-unit",
            "pricing_box_cta_padding-bottom",
            "pricing_box_cta_padding-bottom-unit",
                            
                            // CTA border

            "pricing_box_cta_border-all-width",
            "pricing_box_cta_border-all-width-unit",
            "pricing_box_cta_border-all-style",
            "pricing_box_cta_border-all-color",
                            
            "pricing_box_cta_border-top-width",
            "pricing_box_cta_border-top-width-unit",
            "pricing_box_cta_border-top-style",
            "pricing_box_cta_border-top-color",
                            
            "pricing_box_cta_border-left-width",
            "pricing_box_cta_border-left-width-unit",
            "pricing_box_cta_border-left-style",
            "pricing_box_cta_border-left-color",
                            
            "pricing_box_cta_border-right-width",
            "pricing_box_cta_border-right-width-unit",
            "pricing_box_cta_border-right-style",
            "pricing_box_cta_border-right-color",
                            
            "pricing_box_cta_border-bottom-width",
            "pricing_box_cta_border-bottom-width-unit",
            "pricing_box_cta_border-bottom-style",
            "pricing_box_cta_border-bottom-color",

            'pricing_box_title_typography_font-family',
            'pricing_box_title_typography_font-size',
            'pricing_box_title_typography_color',
            'pricing_box_title_typography_font-weight',
            'pricing_box_title_typography_line-height',
            'pricing_box_title_typography_letter-spacing',
            'pricing_box_title_typography_text-decoration',
            'pricing_box_title_typography_font-style',
            'pricing_box_title_typography_text-transform',

            'pricing_box_subtitle_typography_font-family',
            'pricing_box_subtitle_typography_font-size',
            'pricing_box_subtitle_typography_color',
            'pricing_box_subtitle_typography_font-weight',
            'pricing_box_subtitle_typography_line-height',
            'pricing_box_subtitle_typography_letter-spacing',
            'pricing_box_subtitle_typography_text-decoration',
            'pricing_box_subtitle_typography_font-style',
            'pricing_box_subtitle_typography_text-transform',

            'pricing_box_price_amount_currency_typography_font-family',
            'pricing_box_price_amount_currency_typography_font-size',
            'pricing_box_price_amount_currency_typography_color',
            'pricing_box_price_amount_currency_typography_font-weight',
            'pricing_box_price_amount_currency_typography_line-height',
            'pricing_box_price_amount_currency_typography_letter-spacing',
            'pricing_box_price_amount_currency_typography_text-decoration',
            'pricing_box_price_amount_currency_typography_font-style',
            'pricing_box_price_amount_currency_typography_text-transform',

            'pricing_box_price_amount_main_typography_font-family',
            'pricing_box_price_amount_main_typography_font-size',
            'pricing_box_price_amount_main_typography_color',
            'pricing_box_price_amount_main_typography_font-weight',
            'pricing_box_price_amount_main_typography_line-height',
            'pricing_box_price_amount_main_typography_letter-spacing',
            'pricing_box_price_amount_main_typography_text-decoration',
            'pricing_box_price_amount_main_typography_font-style',
            'pricing_box_price_amount_main_typography_text-transform',

            'pricing_box_price_amount_decimal_typography_font-family',
            'pricing_box_price_amount_decimal_typography_font-size',
            'pricing_box_price_amount_decimal_typography_color',
            'pricing_box_price_amount_decimal_typography_font-weight',
            'pricing_box_price_amount_decimal_typography_line-height',
            'pricing_box_price_amount_decimal_typography_letter-spacing',
            'pricing_box_price_amount_decimal_typography_text-decoration',
            'pricing_box_price_amount_decimal_typography_font-style',
            'pricing_box_price_amount_decimal_typography_text-transform',

            'pricing_box_price_amount_term_typography_font-family',
            'pricing_box_price_amount_term_typography_font-size',
            'pricing_box_price_amount_term_typography_color',
            'pricing_box_price_amount_term_typography_font-weight',
            'pricing_box_price_amount_term_typography_line-height',
            'pricing_box_price_amount_term_typography_letter-spacing',
            'pricing_box_price_amount_term_typography_text-decoration',
            'pricing_box_price_amount_term_typography_font-style',
            'pricing_box_price_amount_term_typography_text-transform',

            'pricing_box_price_sale_typography_font-family',
            'pricing_box_price_sale_typography_font-size',
            'pricing_box_price_sale_typography_color',
            'pricing_box_price_sale_typography_font-weight',
            'pricing_box_price_sale_typography_line-height',
            'pricing_box_price_sale_typography_letter-spacing',
            'pricing_box_price_sale_typography_text-decoration',
            'pricing_box_price_sale_typography_font-style',
            'pricing_box_price_sale_typography_text-transform',

            'pricing_box_price_sale_typography_font-family',
            'pricing_box_price_sale_typography_font-size',
            'pricing_box_price_sale_typography_color',
            'pricing_box_price_sale_typography_font-weight',
            'pricing_box_price_sale_typography_line-height',
            'pricing_box_price_sale_typography_letter-spacing',
            'pricing_box_price_sale_typography_text-decoration',
            'pricing_box_price_sale_typography_font-style',
            'pricing_box_price_sale_typography_text-transform',

            'pricing_box_content_typography_font-family',
            'pricing_box_content_typography_font-size',
            'pricing_box_content_typography_color',
            'pricing_box_content_typography_font-weight',
            'pricing_box_content_typography_line-height',
            'pricing_box_content_typography_letter-spacing',
            'pricing_box_content_typography_text-decoration',
            'pricing_box_content_typography_font-style',
            'pricing_box_content_typography_text-transform',

        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }

    /**
     * Generate CSS for arrays parameters only
     * 
     * @since 2.0
     * @author Ilya
     */

    function allowed_empty_options($options) {

        $options_to_add = array(
            "pricing_box_price_amount_main",
            "pricing_box_price_amount_decimal",
            "pricing_box_price_amount_currency",
            "pricing_box_price_amount_term",
            "pricing_box_package_subtitle",
            "pricing_box_package_regular",
            "pricing_box_content"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }


    /**
     * Generate CSS based on shortcode params
     * 
     * @since 2.0
     * @author Ilya
     */

    function generate_defaults_css() {
        
        $options  = $this->set_options(
                // use fake JSON object to prevent set_options from stop due to empty object
                // TODO: make proper changes to set_options to avoid this hack
                array("ct_options"=>'{"key":1}')
            );

        $options['selector'] = ".oxy-pricing-box";

        echo $this->generate_css($options, true);
    }


    /**
     * Generate CSS for user custom classes
     * 
     * @since 2.0
     * @author Ilya
     */

    function generate_classes_css($css, $class, $state, $options, $is_media = false, $is_selector = false, $defaults) {

        if ($is_selector) {
            return $css;
        }

        $is_icon_box = false;

        foreach ($options as $key => $value) {
            if (strpos($key,"pricing_box")!==false) {
                $is_icon_box = true;
                break;
            }
        }

        $options['selector'] = ".".$class;
        if ($is_icon_box) {
            $css .= $this->generate_css($options, true, $defaults[$this->options['tag']]);
            $is_icon_box = false;
        }

        return $css;
    }


    /**
     * Generate ID styles
     * 
     * @since 2.0
     * @author Ilya
     */

    function generate_id_css($styles, $states, $selector, $class_obj, $defaults) {

        if ($class_obj->options['tag'] != $this->options['tag']){
            return $styles;
        }
        
        $params = $states['original'];
        $params['selector'] = $selector;

        return $styles . $this->generate_css($params, false, $defaults);
    }

    
    /**
     * Generate CSS for arrays parameters only
     * 
     * @since 2.0
     * @author Louis
     */

    function generate_css($params=false, $class=false, $defaults=array()) {

        global $media_queries_list;

        if ($params===false) {
            $params = $this->param_array;
        }

        if ($class===false) {
            $params["selector"] = "#".$params["selector"];
        }

        if ($params["pricing_box_price_layout"]=='vertical') {
            $price_flex_direction = "column";
            if ($params["pricing_box_price_alignment"]=='left') {
                $price_flex_alignment = "flex-start";
            } else if ($params["pricing_box_price_alignment"]=='center') {
                $price_flex_alignment = "center";
            } else if ($params["pricing_box_price_alignment"]=='right') {
                $price_flex_alignment = "flex-end";
            }
        } else if ($params["pricing_box_price_layout"]=='horizontal') {
            $price_flex_direction = "row";
        }

        if ($params["pricing_box_price_layout"]=='horizontal') {
            if ($params["pricing_box_price_alignment"]=='left') {
                $price_justify_content = "flex-start";
            } else if ($params["pricing_box_price_alignment"]=='center') {
                $price_justify_content = "center";
            } else if ($params["pricing_box_price_alignment"]=='right') {
                $price_justify_content = "flex-end";
            }
        }

        if ($params["pricing_box_global_alignment"]=='left') {
            $global_justify_content = "flex-start";
        } else if ($params["pricing_box_global_alignment"]=='center') {
            $global_justify_content = "center";
        } else if ($params["pricing_box_global_alignment"]=='right') {
            $global_justify_content = "flex-end";
        }


        if ($params["pricing_box_graphic_alignment"]=='left') {
            $image_justify_content = "flex-start";
        } else if ($params["pricing_box_graphic_alignment"]=='center') {
            $image_justify_content = "center";
        } else if ($params["pricing_box_graphic_alignment"]=='right') {
            $image_justify_content = "flex-end";
        } elseif ( $global_justify_content ) {
            $image_justify_content = $global_justify_content;
        }


        if ($params["pricing_box_cta_alignment"]=='left') {
            $cta_justify_content = "flex-start";
        } else if ($params["pricing_box_cta_alignment"]=='center') {
            $cta_justify_content = "center";
        } else if ($params["pricing_box_cta_alignment"]=='right') {
            $cta_justify_content = "flex-end";
        } elseif ( $global_justify_content ) {
            $cta_justify_content = $global_justify_content;
        }


        ob_start(); ?>

            /* GLOBALS */

            <?php echo $params["selector"]; ?> .oxy-pricing-box-section {
                <?php echo $this->options_array_to_css($params, 'pricing_box_global', $defaults); ?>
                <?php $this->output_single_css_property("text-align", $params['pricing_box_global_alignment']); ?>
            }

            <?php echo $params["selector"]; ?> .oxy-pricing-box-section.oxy-pricing-box-price {
                <?php $this->output_single_css_property("justify-content", $global_justify_content); ?>
            }

            /* IMAGE */

            <?php echo $params["selector"]; ?> .oxy-pricing-box-section.oxy-pricing-box-graphic {
                <?php echo $this->options_array_to_css($params, 'pricing_box_graphic', $defaults); ?>
                <?php $this->output_single_css_property("background-color", $params['pricing_box_graphic_background']); ?>
                <?php $this->output_single_css_property("text-align", $params['pricing_box_graphic_alignment']); ?>
                <?php $this->output_single_css_property("justify-content", $image_justify_content); ?>
            }

            /* TITLE */

            <?php echo $params["selector"]; ?> .oxy-pricing-box-section.oxy-pricing-box-title {
                <?php echo $this->options_array_to_css($params, 'pricing_box_title', $defaults); ?>
                <?php $this->output_single_css_property("text-align", $params['pricing_box_title_alignment']); ?>
                <?php $this->output_single_css_property("background-color", $params['pricing_box_title_background']); ?>
            }

            <?php echo $params["selector"]; ?> .oxy-pricing-box-title-title {
                <?php echo $this->typography_to_css($params, 'pricing_box_title_typography', $defaults); ?>
            }

            <?php echo $params["selector"]; ?> .oxy-pricing-box-title-subtitle {
                <?php echo $this->typography_to_css($params, 'pricing_box_subtitle_typography', $defaults); ?>
            }


            /* PRICE */

            <?php echo $params["selector"]; ?> .oxy-pricing-box-section.oxy-pricing-box-price {
                <?php echo $this->options_array_to_css($params, 'pricing_box_price', $defaults); ?>
                <?php $this->output_single_css_property("text-align", $params['pricing_box_price_alignment']); ?>
                <?php $this->output_single_css_property("background-color", $params['pricing_box_price_background']); ?>
                <?php $this->output_single_css_property("flex-direction", $price_flex_direction); ?>
                <?php $this->output_single_css_property("justify-content", $price_justify_content); ?>
                <?php $this->output_single_css_property("align-items", $price_flex_alignment); ?>
            }

            <?php echo $params["selector"]; ?> .oxy-pricing-box-currency {
                <?php echo $this->typography_to_css($params, 'pricing_box_price_amount_currency_typography', $defaults); ?>
            }

            <?php echo $params["selector"]; ?> .oxy-pricing-box-amount-main {
                <?php echo $this->typography_to_css($params, 'pricing_box_price_amount_main_typography', $defaults); ?>
            }

            <?php echo $params["selector"]; ?> .oxy-pricing-box-amount-decimal {
                <?php echo $this->typography_to_css($params, 'pricing_box_price_amount_decimal_typography', $defaults); ?>
            }
            
            <?php echo $params["selector"]; ?> .oxy-pricing-box-term {
                <?php echo $this->typography_to_css($params, 'pricing_box_price_amount_term_typography', $defaults); ?>
            }

            <?php echo $params["selector"]; ?> .oxy-pricing-box-sale-price {
                <?php echo $this->typography_to_css($params, 'pricing_box_price_sale_typography', $defaults); ?>
                <?php $this->output_single_css_property("margin-bottom", $params['pricing_box_price_sale_space_below'], $this->get_css_unit('pricing_box_price_sale_space_below', $params, $defaults)); ?>
            }

            /* CONTENT */

            <?php echo $params["selector"]; ?> .oxy-pricing-box-section.oxy-pricing-box-content {
                <?php echo $this->options_array_to_css($params, 'pricing_box_content', $defaults); ?>
                <?php $this->output_single_css_property("text-align", $params['pricing_box_content_alignment']); ?>
                <?php $this->output_single_css_property("background-color", $params['pricing_box_content_background']); ?>
                <?php echo $this->typography_to_css($params, 'pricing_box_content_typography', $defaults); ?>
            }

            /* CTA */

            <?php echo $params["selector"]; ?> .oxy-pricing-box-section.oxy-pricing-box-cta {
                <?php echo $this->options_array_to_css($params, 'pricing_box_cta', $defaults); ?>
                <?php $this->output_single_css_property("text-align", $params['pricing_box_cta_alignment']); ?>
                <?php $this->output_single_css_property("background-color", $params['pricing_box_cta_background']); ?>
                <?php $this->output_single_css_property("justify-content", $cta_justify_content); ?>
            }

        <?php

        return ob_get_clean();
    }


    /**
     * Default CSS output
     * 
     * @since 2.0
     * @author Ilya
     */

    function css_output() {

        $defaultcss = file_get_contents(plugin_dir_path(__FILE__)."pricing-box/pricing-box.css");

        echo $defaultcss;
    }


    /**
     * Shortcode output
     * 
     * @since 2.0
     * @author Louis & Ilya
     */

    function shortcode($atts, $content) {

        $options = $this->set_options( $atts );

        $this->param_array[$options['id']] = $options;

        ob_start(); ?>

        <div id='<?php echo esc_attr($options['selector']); ?>' class='<?php echo esc_attr($options['classes']); ?>'>
            
            <?php if ( $this->param_array[$options['id']]['pricing_box_include_graphic'] == 'yes' ) : ?>
            <div class='oxy-pricing-box-graphic oxy-pricing-box-section'>
                <?php $this->output_builtin_shortcodes( $content ); ?>
            </div>
            <?php endif; ?>
            
            <div class='oxy-pricing-box-title oxy-pricing-box-section'>
                <div class='oxy-pricing-box-title-title'>
                    <?php echo base64_decode($this->param_array[$options['id']]['pricing_box_package_title']); ?>
                </div>
                <div class='oxy-pricing-box-title-subtitle'>
                    <?php echo base64_decode($this->param_array[$options['id']]['pricing_box_package_subtitle']); ?>
                </div>
            </div>

            <?php if ( $this->param_array[$options['id']]['pricing_box_include_features'] != 'no' ) : ?>
            <div class='oxy-pricing-box-content oxy-pricing-box-section'>
                <?php echo base64_decode($this->param_array[$options['id']]['pricing_box_content']); ?>
            </div>
            <?php endif; ?>

            <div class='oxy-pricing-box-price oxy-pricing-box-section'>
                <span class='oxy-pricing-box-sale-price'>
                    <?php echo base64_decode($this->param_array[$options['id']]['pricing_box_package_regular']); ?>
                </span>

                <span class='oxy-pricing-box-amount'>
                    <span class='oxy-pricing-box-currency'><?php echo $this->param_array[$options['id']]['pricing_box_price_amount_currency']; ?></span><span class='oxy-pricing-box-amount-main'><?php echo $this->param_array[$options['id']]['pricing_box_price_amount_main']; ?></span><span class='oxy-pricing-box-amount-decimal'><?php echo $this->param_array[$options['id']]['pricing_box_price_amount_decimal']; ?></span>
                </span>

                <span class='oxy-pricing-box-term'>
                    <?php echo $this->param_array[$options['id']]['pricing_box_price_amount_term']; ?>
                </span>

            </div>

            <div class='oxy-pricing-box-cta oxy-pricing-box-section'>
                <?php $this->output_builtin_shortcodes( $content, false ); ?>
            </div>

        </div>

        <?php $html = ob_get_clean();

        return $html;
    }

    /**
     * Output special settings in Basic Styles tab
     *
     * @since 2.0
     */

    function header_settings() { ?>

        <div class="oxygen-control-row"
            ng-show="isActiveName('<?php echo $this->options['tag']; ?>')&&!hasOpenTabs('<?php echo $this->options['tag']; ?>')&&(iframeScope.getOption('pricing_box_package_regular')==''||iframeScope.getOption('pricing_box_package_subtitle')==''||iframeScope.getOption('pricing_box_content')==''||iframeScope.getOption('pricing_box_content')=='<br>')">
            <div class="oxygen-control-wrapper">
                <div id="oxygen-add-another-row" class="oxygen-add-section-element"
                    ng-click="iframeScope.reEnablePricingBoxText()">
                    <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg' />
                    <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg' />
                    <?php _e("Re-enable Deleted Text","oxygen"); ?>
                </div>
            </div>
        </div>

    <?php }
}

$Pricing_Box = new Oxygen_VSB_Pricing_Box( array(
            'name'  => __('Pricing Box','oxygen'),
            'tag'   => 'oxy_pricing_box',
            'params'=> array(
                    array(
                        "type"          => "textfield",
                        "heading"       => __("Price Amount","oxygen"),
                        "param_name"    => "pricing_box_price_amount_main",
                        "value"         => "59",
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => __("Price Decimal","oxygen"),
                        "param_name"    => "pricing_box_price_amount_decimal",
                        "value"         => "99",
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => __("Currency Symbol","oxygen"),
                        "param_name"    => "pricing_box_price_amount_currency",
                        "value"         => "$",
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => __("Term","oxygen"),
                        "param_name"    => "pricing_box_price_amount_term",
                        "value"         => "monthly",
                    ),

            ),
            'tabs'  => array(
                'global_padding_alignment' => array(
                    'heading' => __('Padding & Alignment','oxygen'),
                    'params' => array(
                        
                        array(
                            "type"          => "padding",
                            "heading"       => __("Section Padding", "oxygen"),
                            "param_name"    => "pricing_box_global_padding",
                        ),

                        array(
                            "type"          => "radio",
                            "heading"       => __("Global Alignment", "oxygen"),
                            "param_name"    => "pricing_box_global_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "default"       => "center"
                        ),
                    ),
                ),
                'graphic' => array(
                    'heading' => __('Graphic','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "radio",
                            "heading"       => __("Include Image", "oxygen"),
                            "param_name"    => "pricing_box_include_graphic",
                            "value"         => array(
                                                 'yes'      => __("yes", "oxygen"),
                                                 'no'       => __("no", "oxygen"),
                                            ),
                            "default"       => 'no'
                        ),
                        array(
                            "type"          => "colorpicker",
                            "heading"       => __("Background", "oxygen"),
                            "param_name"    => "pricing_box_graphic_background",
                            "value"         => "",
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Graphic Alignment", "oxygen"),
                            "param_name"    => "pricing_box_graphic_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "default"       => ""
                        ),
                    ),
                    'tabs' => array(
                        'graphic_padding' => array(
                            'heading' => __('Padding','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "padding",
                                    "heading"       => __("Padding", "oxygen"),
                                    "param_name"    => "pricing_box_graphic_padding",
                                ),
                            )
                        ),
                        'graphic_border' => array(
                            'heading' => __('Borders','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "border",
                                    "heading"       => __("Borders", "oxygen"),
                                    "param_name"    => "pricing_box_graphic",    
                                )
                            )
                        ),
                    )
                ),

                'title' => array(
                    'heading' => __('Title','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "colorpicker",
                            "heading"       => __("Background", "oxygen"),
                            "param_name"    => "pricing_box_title_background",
                            "value"         => "",
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Alignment", "oxygen"),
                            "param_name"    => "pricing_box_title_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "default"       => ""
                        ),
                    ),
                    'tabs' => array(
                        'title_padding' => array(
                            'heading' => __('Padding','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "padding",
                                    "heading"       => __("Padding", "oxygen"),
                                    "param_name"    => "pricing_box_title_padding",
                                ),
                            )
                        ),
                        'title_border' => array(
                            'heading' => __('Borders','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "border",
                                    "heading"       => __("Borders", "oxygen"),
                                    "param_name"    => "pricing_box_title",    
                                )
                            )
                        ),
                        'title_typography_tab' => array(
                            'heading' => __('Typography','oxygen'),
                            'tabs' => array(
                                'title_typography' => array(
                                    'heading' => __("Title Typography","oxygen"),
                                    'params' => array(
                                        array(
                                            "type"          => "typography",
                                            "param_name"    => "pricing_box_title_typography",
                                            "param_values"  => array(
                                                                'font-family'           => '',
                                                                'font-size'             => '48',
                                                                'font-size-unit'        => 'px',
                                                                'color'                 => '',
                                                                'font-weight'           => '',
                                                                'line-height'           => '',
                                                                'letter-spacing'        => '',
                                                                'letter-spacing-unit'   => 'px',
                                                                'text-decoration'       => '',
                                                                'font-style'            => '',
                                                                'text-transform'        => '',
                                                            ),
                                            "css"           => false,
                                        ),
                                    )
                                ),
                                'subtitle_typography' => array(
                                    'heading' => __("Subtitle Typography","oxygen"),
                                    'params' => array(
                                        array(
                                            "type"          => "typography",
                                            "param_name"    => "pricing_box_subtitle_typography",
                                            "param_values"  => array(
                                                                'font-family'           => '',
                                                                'font-size'             => '24',
                                                                'font-size-unit'        => 'px',
                                                                'color'                 => '',
                                                                'font-weight'           => '',
                                                                'line-height'           => '',
                                                                'letter-spacing'        => '',
                                                                'letter-spacing-unit'   => 'px',
                                                                'text-decoration'       => '',
                                                                'font-style'            => '',
                                                                'text-transform'        => '',
                                                            ),
                                            "css"           => false,
                                        ),
                                    )
                                ),
                            )
                        )
                    )
                ),

                'price' => array(
                    'heading' => __('Price','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "colorpicker",
                            "heading"       => __("Background", "oxygen"),
                            "param_name"    => "pricing_box_price_background",
                            "value"         => "",
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Alignment", "oxygen"),
                            "param_name"    => "pricing_box_price_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "default"       => ""
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Layout", "oxygen"),
                            "param_name"    => "pricing_box_price_layout",
                            "value"         => array(
                                                 'horizontal'   => __("horizontal", "oxygen"),
                                                 'vertical'     => __("vertical", "oxygen"),
                                            ),
                        ),
                        array(
                            "type"          => "slider-measurebox",
                            "heading"       => __("Price Sale Space Below", "oxygen"),
                            "param_name"    => "pricing_box_price_sale_space_below",
                            "value"         => "20",
                        ),
                        array(
                            "param_name"    => "pricing_box_price_sale_space_below-unit",
                            "value"         => "px",
                            "hidden"        => true,
                        ),
                    ),
                    'tabs' => array(
                        'price_padding' => array(
                            'heading' => __('Padding','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "padding",
                                    "heading"       => __("Padding", "oxygen"),
                                    "param_name"    => "pricing_box_price_padding",
                                ),
                            )
                        ),
                        'price_border' => array(
                            'heading' => __('Borders','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "border",
                                    "heading"       => __("Borders", "oxygen"),
                                    "param_name"    => "pricing_box_price",    
                                )
                            )
                        ),
                        'price_typography' => array(
                            'heading' => __('Typography','oxygen'),
                            'tabs' => array(
                                'price_amount_currency_typography' => array(
                                    'heading' => __("Amount Currency","oxygen"),
                                    'params' => array(
                                        array(
                                            "type"          => "typography",
                                            "param_name"    => "pricing_box_price_amount_currency_typography",
                                            "param_values"  => array(
                                                                'font-family'           => '',
                                                                'font-size'             => '28',
                                                                'font-size-unit'        => 'px',
                                                                'color'                 => '',
                                                                'font-weight'           => '',
                                                                'line-height'           => '',
                                                                'letter-spacing'        => '',
                                                                'letter-spacing-unit'   => 'px',
                                                                'text-decoration'       => '',
                                                                'font-style'            => '',
                                                                'text-transform'        => '',
                                                            ),
                                            "css"           => false,
                                        ),
                                    )
                                ),
                                'price_amount_main_typography' => array(
                                    'heading' => __("Amount Main","oxygen"),
                                    'params' => array(
                                        array(
                                            "type"          => "typography",
                                            "param_name"    => "pricing_box_price_amount_main_typography",
                                            "param_values"  => array(
                                                                'font-family'           => '',
                                                                'font-size'             => '80',
                                                                'font-size-unit'        => 'px',
                                                                'color'                 => '',
                                                                'font-weight'           => '',
                                                                'line-height'           => '0.7',
                                                                'letter-spacing'        => '',
                                                                'letter-spacing-unit'   => 'px',
                                                                'text-decoration'       => '',
                                                                'font-style'            => '',
                                                                'text-transform'        => '',
                                                            ),
                                            "css"           => false,
                                        ),
                                    )
                                ),
                                'price_amount_decimal_typography' => array(
                                    'heading' => __("Amount Decimal","oxygen"),
                                    'params' => array(
                                        array(
                                            "type"          => "typography",
                                            "param_name"    => "pricing_box_price_amount_decimal_typography",
                                            "param_values"  => array(
                                                                'font-family'           => '',
                                                                'font-size'             => '13',
                                                                'font-size-unit'        => 'px',
                                                                'color'                 => '',
                                                                'font-weight'           => '',
                                                                'line-height'           => '',
                                                                'letter-spacing'        => '',
                                                                'letter-spacing-unit'   => 'px',
                                                                'text-decoration'       => '',
                                                                'font-style'            => '',
                                                                'text-transform'        => '',
                                                            ),
                                            "css"           => false,
                                        ),
                                    )
                                ),
                                'price_amount_term_typography' => array(
                                    'heading' => __("Term","oxygen"),
                                    'params' => array(
                                        array(
                                            "type"          => "typography",
                                            "param_name"    => "pricing_box_price_amount_term_typography",
                                            "param_values"  => array(
                                                                'font-family'           => '',
                                                                'font-size'             => '16',
                                                                'font-size-unit'        => 'px',
                                                                'color'                 => '',
                                                                'font-weight'           => '',
                                                                'line-height'           => '',
                                                                'letter-spacing'        => '',
                                                                'letter-spacing-unit'   => 'px',
                                                                'text-decoration'       => '',
                                                                'font-style'            => '',
                                                                'text-transform'        => '',
                                                            ),
                                            "css"           => false,
                                        ),
                                    )
                                ),
                                'price_sale_typography' => array(
                                    'heading' => __("Sale","oxygen"),
                                    'params' => array(
                                        array(
                                            "type"          => "typography",
                                            "param_name"    => "pricing_box_price_sale_typography",
                                            "param_values"  => array(
                                                                'font-family'           => '',
                                                                'font-size'             => '12',
                                                                'font-size-unit'        => 'px',
                                                                'color'                 => 'rgba(0,0,0,0.5)',
                                                                'font-weight'           => '',
                                                                'line-height'           => '',
                                                                'letter-spacing'        => '',
                                                                'letter-spacing-unit'   => 'px',
                                                                'text-decoration'       => '',
                                                                'font-style'            => '',
                                                                'text-transform'        => '',
                                                            ),
                                            "css"           => false,
                                        ),
                                    )
                                ),
                            )
                        )
                    )
                ),

                'content' => array(
                    'heading' => __('Features','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "radio",
                            "heading"       => __("Include Features", "oxygen"),
                            "param_name"    => "pricing_box_include_features",
                            "value"         => array(
                                                 'yes'      => __("yes", "oxygen"),
                                                 'no'       => __("no", "oxygen"),
                                            ),
                        ),
                        array(
                            "type"          => "colorpicker",
                            "heading"       => __("Background", "oxygen"),
                            "param_name"    => "pricing_box_content_background",
                            "value"         => "",
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Alignment", "oxygen"),
                            "param_name"    => "pricing_box_content_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "default"       => ""
                        ),
                    ),
                    'tabs' => array(
                        'content_padding' => array(
                            'heading' => __('Padding','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "padding",
                                    "heading"       => __("Padding", "oxygen"),
                                    "param_name"    => "pricing_box_content_padding",
                                ),
                            )
                        ),
                        'content_border' => array(
                            'heading' => __('Borders','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "border",
                                    "heading"       => __("Borders", "oxygen"),
                                    "param_name"    => "pricing_box_content",    
                                )
                            )
                        ),
                        'content_typography' => array(
                            'heading' => __("Typography","oxygen"),
                            'params' => array(
                                array(
                                    "type"          => "typography",
                                    "param_name"    => "pricing_box_content_typography",
                                    "param_values"  => array(
                                                        'font-family'           => '',
                                                        'font-size'             => '16',
                                                        'font-size-unit'        => 'px',
                                                        'color'                 => 'rgba(0,0,0,0.5)',
                                                        'font-weight'           => '',
                                                        'line-height'           => '',
                                                        'letter-spacing'        => '',
                                                        'letter-spacing-unit'   => 'px',
                                                        'text-decoration'       => '',
                                                        'font-style'            => '',
                                                        'text-transform'        => '',
                                                    ),
                                    "css"           => false,
                                ),
                            )
                        ),
                    )
                ),

                'cta' => array(
                    'heading' => __('Call To Action','oxygen'),
                    'params' => array(
                        array(
                            "type"          => "colorpicker",
                            "heading"       => __("Background", "oxygen"),
                            "param_name"    => "pricing_box_cta_background",
                            "value"         => "",
                        ),
                        array(
                            "type"          => "radio",
                            "heading"       => __("Alignment", "oxygen"),
                            "param_name"    => "pricing_box_cta_alignment",
                            "value"         => array(
                                                 'left'     => __("left", "oxygen"),
                                                 'center'   => __("center", "oxygen"),
                                                 'right'    => __("right", "oxygen"),
                                            ),
                            "default"       => ""
                        ),
                    ),
                    'tabs' => array(
                        'cta_padding' => array(
                            'heading' => __('Padding','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "padding",
                                    "heading"       => __("Padding", "oxygen"),
                                    "param_name"    => "pricing_box_cta_padding",
                                ),
                            )
                        ),
                        'cta_border' => array(
                            'heading' => __('Borders','oxygen'),
                            'params' => array(
                                array(
                                    "type"          => "border",
                                    "heading"       => __("Borders", "oxygen"),
                                    "param_name"    => "pricing_box_cta",    
                                )
                            )
                        ),
                    )
                ),
            ),
            'advanced'  => array(
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                "other" => array(
                        "values"    => array (

                            // Global padding

                            "pricing_box_global_padding-top"           => "20",
                            "pricing_box_global_padding-top-unit"      => "px",
                            "pricing_box_global_padding-left"          => "20",
                            "pricing_box_global_padding-left-unit"     => "px",
                            "pricing_box_global_padding-right"         => "20",
                            "pricing_box_global_padding-right-unit"    => "px",
                            "pricing_box_global_padding-bottom"        => "20",
                            "pricing_box_global_padding-bottom-unit"   => "px",

                            // Graphic padding

                            "pricing_box_graphic_padding-top"           => "",
                            "pricing_box_graphic_padding-top-unit"      => "px",
                            "pricing_box_graphic_padding-left"          => "",
                            "pricing_box_graphic_padding-left-unit"     => "px",
                            "pricing_box_graphic_padding-right"         => "",
                            "pricing_box_graphic_padding-right-unit"    => "px",
                            "pricing_box_graphic_padding-bottom"        => "",
                            "pricing_box_graphic_padding-bottom-unit"   => "px",
                            
                            // Graphic border

                            "pricing_box_graphic_border-all-width"          => '',
                            "pricing_box_graphic_border-all-width-unit"     => 'px',
                            "pricing_box_graphic_border-all-style"          => '',
                            "pricing_box_graphic_border-all-color"          => '',
                            
                            "pricing_box_graphic_border-top-width"          => '',
                            "pricing_box_graphic_border-top-width-unit"     => 'px',
                            "pricing_box_graphic_border-top-style"          => '',
                            "pricing_box_graphic_border-top-color"          => '',
                            
                            "pricing_box_graphic_border-left-width"         => '',
                            "pricing_box_graphic_border-left-width-unit"    => 'px',
                            "pricing_box_graphic_border-left-style"         => '',
                            "pricing_box_graphic_border-left-color"         => '',
                            
                            "pricing_box_graphic_border-right-width"        => '',
                            "pricing_box_graphic_border-right-width-unit"   => 'px',
                            "pricing_box_graphic_border-right-style"        => '',
                            "pricing_box_graphic_border-right-color"        => '',
                            
                            "pricing_box_graphic_border-bottom-width"       => '',
                            "pricing_box_graphic_border-bottom-width-unit"  => 'px',
                            "pricing_box_graphic_border-bottom-style"       => '',
                            "pricing_box_graphic_border-bottom-color"       => '',

                            // Title padding

                            "pricing_box_title_padding-top"           => "",
                            "pricing_box_title_padding-top-unit"      => "px",
                            "pricing_box_title_padding-left"          => "",
                            "pricing_box_title_padding-left-unit"     => "px",
                            "pricing_box_title_padding-right"         => "",
                            "pricing_box_title_padding-right-unit"    => "px",
                            "pricing_box_title_padding-bottom"        => "",
                            "pricing_box_title_padding-bottom-unit"   => "px",
                            
                            // Title border

                            "pricing_box_title_border-all-width"          => '',
                            "pricing_box_title_border-all-width-unit"     => 'px',
                            "pricing_box_title_border-all-style"          => '',
                            "pricing_box_title_border-all-color"          => '',
                            
                            "pricing_box_title_border-top-width"          => '',
                            "pricing_box_title_border-top-width-unit"     => 'px',
                            "pricing_box_title_border-top-style"          => '',
                            "pricing_box_title_border-top-color"          => '',
                            
                            "pricing_box_title_border-left-width"         => '',
                            "pricing_box_title_border-left-width-unit"    => 'px',
                            "pricing_box_title_border-left-style"         => '',
                            "pricing_box_title_border-left-color"         => '',
                            
                            "pricing_box_title_border-right-width"        => '',
                            "pricing_box_title_border-right-width-unit"   => 'px',
                            "pricing_box_title_border-right-style"        => '',
                            "pricing_box_title_border-right-color"        => '',
                            
                            "pricing_box_title_border-bottom-width"       => '',
                            "pricing_box_title_border-bottom-width-unit"  => 'px',
                            "pricing_box_title_border-bottom-style"       => '',
                            "pricing_box_title_border-bottom-color"       => '',

                            // Price padding

                            "pricing_box_price_padding-top"           => "",
                            "pricing_box_price_padding-top-unit"      => "px",
                            "pricing_box_price_padding-left"          => "",
                            "pricing_box_price_padding-left-unit"     => "px",
                            "pricing_box_price_padding-right"         => "",
                            "pricing_box_price_padding-right-unit"    => "px",
                            "pricing_box_price_padding-bottom"        => "",
                            "pricing_box_price_padding-bottom-unit"   => "px",
                            
                            // Price border

                            "pricing_box_price_border-all-width"          => '',
                            "pricing_box_price_border-all-width-unit"     => 'px',
                            "pricing_box_price_border-all-style"          => '',
                            "pricing_box_price_border-all-color"          => '',
                            
                            "pricing_box_price_border-top-width"          => '',
                            "pricing_box_price_border-top-width-unit"     => 'px',
                            "pricing_box_price_border-top-style"          => '',
                            "pricing_box_price_border-top-color"          => '',
                            
                            "pricing_box_price_border-left-width"         => '',
                            "pricing_box_price_border-left-width-unit"    => 'px',
                            "pricing_box_price_border-left-style"         => '',
                            "pricing_box_price_border-left-color"         => '',
                            
                            "pricing_box_price_border-right-width"        => '',
                            "pricing_box_price_border-right-width-unit"   => 'px',
                            "pricing_box_price_border-right-style"        => '',
                            "pricing_box_price_border-right-color"        => '',
                            
                            "pricing_box_price_border-bottom-width"       => '',
                            "pricing_box_price_border-bottom-width-unit"  => 'px',
                            "pricing_box_price_border-bottom-style"       => '',
                            "pricing_box_price_border-bottom-color"       => '',

                            // Content padding

                            "pricing_box_content_padding-top"           => "",
                            "pricing_box_content_padding-top-unit"      => "px",
                            "pricing_box_content_padding-left"          => "",
                            "pricing_box_content_padding-left-unit"     => "px",
                            "pricing_box_content_padding-right"         => "",
                            "pricing_box_content_padding-right-unit"    => "px",
                            "pricing_box_content_padding-bottom"        => "",
                            "pricing_box_content_padding-bottom-unit"   => "px",
                            
                            // Content border

                            "pricing_box_content_border-all-width"          => '',
                            "pricing_box_content_border-all-width-unit"     => 'px',
                            "pricing_box_content_border-all-style"          => '',
                            "pricing_box_content_border-all-color"          => '',
                            
                            "pricing_box_content_border-top-width"          => '',
                            "pricing_box_content_border-top-width-unit"     => 'px',
                            "pricing_box_content_border-top-style"          => '',
                            "pricing_box_content_border-top-color"          => '',
                            
                            "pricing_box_content_border-left-width"         => '',
                            "pricing_box_content_border-left-width-unit"    => 'px',
                            "pricing_box_content_border-left-style"         => '',
                            "pricing_box_content_border-left-color"         => '',
                            
                            "pricing_box_content_border-right-width"        => '',
                            "pricing_box_content_border-right-width-unit"   => 'px',
                            "pricing_box_content_border-right-style"        => '',
                            "pricing_box_content_border-right-color"        => '',
                            
                            "pricing_box_cpntent_border-bottom-width"       => '',
                            "pricing_box_cpntent_border-bottom-width-unit"  => 'px',
                            "pricing_box_cpntent_border-bottom-style"       => '',
                            "pricing_box_cpntent_border-bottom-color"       => '',

                            // CTA padding

                            "pricing_box_cta_padding-top"           => "",
                            "pricing_box_cta_padding-top-unit"      => "px",
                            "pricing_box_cta_padding-left"          => "",
                            "pricing_box_cta_padding-left-unit"     => "px",
                            "pricing_box_cta_padding-right"         => "",
                            "pricing_box_cta_padding-right-unit"    => "px",
                            "pricing_box_cta_padding-bottom"        => "",
                            "pricing_box_cta_padding-bottom-unit"   => "px",
                            
                            // CTA border

                            "pricing_box_cta_border-all-width"          => '',
                            "pricing_box_cta_border-all-width-unit"     => 'px',
                            "pricing_box_cta_border-all-style"          => '',
                            "pricing_box_cta_border-all-color"          => '',
                            
                            "pricing_box_cta_border-top-width"          => '',
                            "pricing_box_cta_border-top-width-unit"     => 'px',
                            "pricing_box_cta_border-top-style"          => '',
                            "pricing_box_cta_border-top-color"          => '',
                            
                            "pricing_box_cta_border-left-width"         => '',
                            "pricing_box_cta_border-left-width-unit"    => 'px',
                            "pricing_box_cta_border-left-style"         => '',
                            "pricing_box_cta_border-left-color"         => '',
                            
                            "pricing_box_cta_border-right-width"        => '',
                            "pricing_box_cta_border-right-width-unit"   => 'px',
                            "pricing_box_cta_border-right-style"        => '',
                            "pricing_box_cta_border-right-color"        => '',
                            
                            "pricing_box_cta_border-bottom-width"       => '',
                            "pricing_box_cta_border-bottom-width-unit"  => 'px',
                            "pricing_box_cta_border-bottom-style"       => '',
                            "pricing_box_cta_border-bottom-color"       => '',

                        )
                    ),
                "data" => array(
                        "values"    => array (
                            "pricing_box_package_title"     => 'Basic Hosting',
                            "pricing_box_package_subtitle"  => 'for small business',
                            "pricing_box_package_regular"   => 'normally $399',
                            "pricing_box_content"           => 'my features<br />another feature<br />my last feature<br />',
                            )
                    ),
            )
        )
);


$param_array = array(

    //"content_alignment" => "left", // left | center | right
    //"icon_position" => "left", // top | left | right | bottom
    //"icon_vertical_alignment" => "center", // If icon position is left or right, icon vertical alignment: flex-start | center | flex-end (labels are top, middle, bottom)
    
    // If Icon Position is left or right, Icon Space Before = left, Icon Space After = Right. If Icon Position is top or bottom, before = top, after = bottom
    //"icon_space_before" => "0px", 
    //"icon_space_after" => "30px",

    "heading_typography" => array('font-size' => '30px'),
    "text_typography" => array('font-size' => '16px'),

    //"heading_space_above" => '0px',
    //"heading_space_below" => '20px',

    //"text_space_above" => '0px',
    //"text_space_below" => '20px',

    //"link_space_above" => '0px',
    //"link_space_below" => '0px',

    "vertical_layout_below" => '992px',
    "mobile_content_alignment" => "left",

);


