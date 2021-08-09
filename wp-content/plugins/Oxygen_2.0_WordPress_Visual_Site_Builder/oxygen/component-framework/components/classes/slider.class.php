<?php 

Class CT_Slider extends CT_Component {

	var $js_css_added = false;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		for ( $i = 2; $i <= 16; $i++ ) {
			add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
		}

		// add specific options
		add_action("ct_toolbar_component_settings", array( $this, "slider_settings") );
		
		// change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_interactive", array( $this, "component_button" ) );
	}


	/**
	 * Add a toolbar button
	 *
	 * @since 0.1
	 */
	
	function component_button() { ?>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>');addSlides()">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/slider.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/slider-active.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }


	/**
	 * Add a [ct_slider] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content ) {

		// add JS/CSS to footer only once
		if ($this->js_css_added === false) {
			echo "<link rel='stylesheet' id='oxygen-unslider-css'  href='" . CT_FW_URI . "/vendor/unslider/unslider.css' type='text/css' media='all'/>";
			add_action("wp_footer", array( $this, "js_css_output") );
			$this->js_css_added = true;
		}

		$options = $this->set_options( $atts );

		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>"><div class="oxygen-unslider-container"><ul><?php echo do_shortcode( $content ); ?></ul></div></div><script>jQuery(document).ready(function($){$('#<?php echo esc_attr($options['selector']); ?> .oxygen-unslider-container').unslider({ autoplay: <?php echo ($options['slider_autoplay']=='yes') ? "true" : "false"; ?>, delay: <?php echo $options['slider_autoplay_delay']; ?>, animation: '<?php echo $options['slider_animation']; ?>', speed : <?php echo $options['slider_animation_speed']; ?>, arrows: <?php echo ($options['slider_show_arrows']=='yes') ? "true" : "false" ?>, nav: <?php echo ($options['slider_show_dots']=='yes') ? "true" : "false"; ?>})});</script><?php

		return ob_get_clean();
	}

	/**
	 * Output settings
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function slider_settings() { 

		global $oxygen_toolbar; ?>

		<div class="oxygen-sidebar-flex-panel"
			ng-show="isActiveName('ct_slider')">

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('slider', 'styling')" 
				ng-show="!hasOpenTabs('slider')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
					<?php _e("Styling", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>

			<div class="oxygen-sidebar-advanced-subtab" 
				ng-click="switchTab('slider', 'configuration')" 
				ng-show="!hasOpenTabs('slider')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
					<?php _e("Configuration", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
			</div>
		
			<div ng-if="isShowTab('slider','styling')">
				
				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.slider=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.slider=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Styling","oxygen"); ?></div>
				</div>

				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Arrow Color","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-button-list'>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-arrow-color')=='darker'}">
										<input type="radio" name="slider-arrow-color" value="darker"
											<?php $this->ng_attributes('slider-arrow-color', 'model,change'); ?>
											ng-change="iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										darker
								</label>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-arrow-color')=='lighter'}">
										<input type="radio" name="slider-arrow-color" value="lighter"
											<?php $this->ng_attributes('slider-arrow-color', 'model,change'); ?>
											ng-change="iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										lighter
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="oxygen-control-row">
					<?php $oxygen_toolbar->colorpicker_with_wrapper("slider-dot-color", __("Dot Color", "oxygen")); ?>
				</div>
			
			</div>

			<div ng-if="isShowTab('slider','configuration')">

				<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
					<div class="oxygen-sidebar-breadcrumb-icon" 
						ng-click="tabs.slider=[]">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
					</div>
					<div class="oxygen-sidebar-breadcrumb-all-styles" 
						ng-click="tabs.slider=[]"><?php _e("All Styles","oxygen"); ?></div>
					<div class="oxygen-sidebar-breadcrumb-separator">/</div>
					<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Configuration","oxygen"); ?></div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-show-arrows']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-show-arrows');iframeScope.rebuildDOM(iframeScope.component.active.id)">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-show-arrows')=='yes'}">
								<?php _e("Show Arrows","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>
				
				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-show-dots']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-show-dots');iframeScope.rebuildDOM(iframeScope.component.active.id)">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-show-dots')=='yes'}">
								<?php _e("Show Dots","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class="oxygen-checkbox">
							<input type="checkbox"
								ng-true-value="'yes'" 
								ng-false-value="'no'"
								ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-autoplay']"
								ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-autoplay');iframeScope.rebuildDOM(iframeScope.component.active.id)">
							<div class='oxygen-checkbox-checkbox'
								ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('slider-autoplay')=='yes'}">
								<?php _e("Autoplay","oxygen"); ?>
							</div>
						</label>
					</div>
				</div>

				<div class="oxygen-control-row"
					ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-autoplay'] == 'yes'">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Delay (milliseconds)","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-input'>
								<input type="text" spellcheck="false"
									ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-autoplay-delay']" 
									ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-autoplay-delay');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
							</div>
						</div>
					</div>
				</div>

				<div class='oxygen-control-row'>
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Animation","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-button-list'>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-animation')=='horizontal'}">
										<input type="radio" name="slider-animation" value="horizontal"
											ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-animation']" 
											ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-animation');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										horizontal
								</label>
								<label class='oxygen-button-list-button'
									ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('slider-animation')=='fade'}">
										<input type="radio" name="slider-animation" value="fade"
											ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-animation']" 
											ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-animation');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
										fade
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="oxygen-control-row">
					<div class='oxygen-control-wrapper'>
						<label class='oxygen-control-label'><?php _e("Animation Speed (milliseconds)","oxygen"); ?></label>
						<div class='oxygen-control'>
							<div class='oxygen-input'>
								<input type="text" spellcheck="false"
									ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['slider-animation-speed']" 
									ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_slider','slider-animation-speed');iframeScope.rebuildDOM(iframeScope.component.active.id)"/>
							</div>
						</div>
					</div>
				</div>
			
			</div>

		</div>
	
	<?php }


	
	/**
	 * Output JS/CSS to footer
	 *
	 * @since 2.0
	 */

	function js_css_output() {

		// include Unslider
		wp_enqueue_script( 'oxygen-unslider', 		CT_FW_URI . '/vendor/unslider/unslider-min.js', array('jquery') );
		wp_enqueue_script( 'oxygen-event-move', 	CT_FW_URI . '/vendor/unslider/jquery.event.move.js');
		wp_enqueue_script( 'oxygen-event-swipe', 	CT_FW_URI . '/vendor/unslider/jquery.event.swipe.js');
	}
}


// Create Slider instance
$oxygen_slider = new CT_Slider( array( 
			'name' 		=> __('Slider','oxygen'),
			'tag' 		=> 'ct_slider',
			'advanced' 	=> array(
				'styles' => array(
					'values' => array(
							'slider-arrow-color' 	=> "darker",
							'slider-dot-color' 		=> "#ffffff",
						)
				),
				'configuration' => array(
					'values' => array(
							'slider-show-arrows' 	=> "yes",
							'slider-show-dots' 		=> "yes",
							'slider-autoplay' 		=> "no",
							'slider-autoplay-delay' => "3000",
							'slider-animation' 		=> "horizontal",
							'slider-animation-speed'=> "750",
						)
				),
				'size' => array(
					'values' => array(
							'width' 		 => '100',
							'width-unit'  	 => '%'
					)
				)
			),
			'not_css_params' => array(
				'slider-show-dots', 
				'slider-show-arrows', 
				'slider-autoplay', 
				'slider-autoplay-delay', 
				'slider-animation', 
				'slider-animation-speed'
			)
		)
);