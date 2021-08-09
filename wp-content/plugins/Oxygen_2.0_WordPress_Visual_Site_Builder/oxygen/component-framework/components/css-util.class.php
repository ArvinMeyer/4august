<?php

/**
 * Easy Posts Component Class
 * 
 * @since 2.0
 * @author Louis
 */

if (!class_exists('Oxygen_VSB_CSS_Util')) {
    
    class Oxygen_VSB_CSS_Util {
        
        public $css_selectors = null;
        public $contingency_functions = null;
        public $css_output_functions = null;

        function register_selector($css_selector) {
            $this->css_selectors[$css_selector] = array();
        }

        function map_property($property_name, $css_property, $css_selector) {
            $this->css_selectors[$css_selector][$css_property] = $property_name;
        }

        function register_contingency_function($function_name) {
            $this->contingency_functions[] = $function_name;
        }

        function call_contingency_functions($id) {
            if (!is_array($this->contingency_functions)) {
                return;
            }
            foreach ($this->contingency_functions as $contingency_function) {
                $this->css_selectors = call_user_func($contingency_function, $this->css_selectors, $id);
            }
        }

        function register_css_output_function($function_name) {
            $this->css_output_functions[] = $function_name;
        }

        function call_css_output_functions($id) {
            if (!$this->css_output_functions){
                return "";
            }
            $css = '';
            foreach ($this->css_output_functions as $css_output_function) {
                $css .= call_user_func($css_output_function, $id);
            }
            return $css;
        }

        function merge_param_values($param_array) {

            foreach ($this->css_selectors as $css_selector => $css_properties) {

                foreach ($css_properties as $css_property_name => $param_name) {

                    $unit = "";
                    if (isset($param_array[$param_name."_unit"])) {
                        $unit = $param_array[$param_name."_unit"];
                    }
                    if (isset($param_array[$param_name."-unit"])) {
                        $unit = $param_array[$param_name."-unit"];
                    }

                    if ($css_property_name) {
                        $merged_css_selectors[$css_selector][$css_property_name] = $param_array[$param_name].$unit;
                    }
                }
            }

            $this->css_selectors = $merged_css_selectors;
        }

        function generate_css($param_array, $id = false) {

            $this->merge_param_values($param_array);
            $this->call_contingency_functions($id);

            ob_start();

            foreach ($this->css_selectors as $css_selector => $css_properties) {

                $css_selector = explode(",", $css_selector);

                foreach ($css_selector as $key => $value) {
                    $css_selector[$key] = "#".$param_array["selector"]." ".$value;
                }

                echo "\n\n".implode(",",$css_selector)." {\n";

                foreach ($css_properties as $css_property_name => $css_property_value) {

                    if ($css_property_name && $css_property_value) {
                        echo "\t".$css_property_name.": ".$css_property_value.";\n";
                    }

                }

                echo "}\n";
            }

            echo $this->call_css_output_functions($id);

            return ob_get_clean();
        }

    }
    // Oxygen_VSB_CSS_Util

}