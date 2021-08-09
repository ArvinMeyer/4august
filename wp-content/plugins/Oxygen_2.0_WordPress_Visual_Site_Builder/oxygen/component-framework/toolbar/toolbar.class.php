<?php

/**
 * Toolbar Class
 *
 * @since 0.1
 */

Class CT_Toolbar {

	function __construct() {

		add_action("wp", array( $this, "toolbar_init" ) );
	}

	function toolbar_init() {

		// TODO: check if user can edit this exact post?
		if ( current_user_can("edit_posts") && defined("SHOW_CT_BUILDER") ) {
			add_action("ct_before_builder", array( $this, "toolbar_view") );
		}

		global $oxygen_api;
		global $oxygen_add_plus;

		$this->folders = $oxygen_add_plus;

		$this->options['advanced'] = array(
											"background" => array (
												"heading" 	=> __("Background", "oxygen"),
												"tab_icon" 	=> "background",
											),

											"position" => array (
												"heading" 	=> __("Size & Spacing", "oxygen"),
												"tab_icon" 	=> "size_spacing",
											),

											"layout" => array (
												"heading" 	=> __("Layout", "oxygen"),
												"tab_icon" 	=> "layout",
											),

											"typography" => array (
												"heading" 	=> __("Typography", "oxygen"),
												"tab_icon" 	=> "typography",
											),

											"borders" => array (
												"heading" 	=> __("Borders", "oxygen"),
												"tab_icon" 	=> "borders",
											),

											"effects" => array (
												"heading" 	=> __("Effects", "oxygen"),
												"tab_icon" 	=> "effects",
											),

											"code-php" => array (
												"heading" 	=> __("PHP & HTML", "oxygen"),
												"tab_icon" 	=> "borders",
											),

											"code-css" => array (
												"heading" 	=> __("CSS", "oxygen"),
												"tab_icon" 	=> "css",
											),

											"code-js" => array (
												"heading" 	=> __("JavaScript", "oxygen"),
												"tab_icon" 	=> "js",
											),

											"custom-css" => array (
												"heading" 	=> __("Custom CSS", "oxygen"),
												"tab_icon" 	=> "css",
											),

											"custom-js" => array (
												"heading" 	=> __("JavaScript", "oxygen"),
												"tab_icon" 	=> "js",
											),
										);

		//$this->options['advanced'] = apply_filters("ct_component_advanced_options", $this->options['advanced']);
		
		// get list of all components that has Basic Styles tabs
		$this->component_with_tabs = apply_filters("oxygen_component_with_tabs", array());

		// include styles
		add_action("wp_enqueue_scripts", array( $this, "enqueue_scripts" ) );

		// output main toolbar elements
		add_action("ct_toolbar_component_header",			array( $this, "component_header") );
		add_action("ct_toolbar_advanced_settings", 			array( $this, "advanced_settings") );

		add_action("ct_toolbar_components_list",			array( $this, "components_list") );
		add_action("ct_toolbar_components_anchors", 		array( $this, "components_anchors") );

		add_action("ct_toolbar_reusable_parts", 			array( $this, "ct_reusable_parts") );

		add_action("ct_toolbar_page_settings", 				array( $this, "ct_show_page_settings" ) );
		add_action("ct_toolbar_global_fonts_settings", 		array( $this, "ct_show_global_fonts_settings") );
		add_action("ct_dialog_window", 						array( $this, "dialog_window") );

		add_action("oxygen_toolbar_settings_headings", 		array( $this, "settings_headings") );
		add_action("oxygen_toolbar_settings_body_text", 	array( $this, "settings_body_text") );
		add_action("oxygen_toolbar_settings_links", 		array( $this, "settings_links") );
		add_action("oxygen_before_toolbar_close", 			array( $this, "tiny_mce") );

		add_action("ct_toolbar_data_folder", 				array( $this, "data_folder"), 9 );
	}


	/**
	 * Enqueue scripts and styles
	 *
	 * @since 0.1.4
	 */

	function enqueue_scripts() {
		wp_enqueue_style ("ct-ui", 			CT_FW_URI . "/toolbar/UI/css/default.css");
		wp_enqueue_style ("flex-ui", 		CT_FW_URI . "/toolbar/UI/css/flex-ui.css");
		wp_enqueue_style ("ct-dom-tree", 	CT_FW_URI . "/toolbar/UI/css/domtree.css");
	}


	/**
	 * Include toolbar view file
	 *
	 * @since 0.1.4
	 */

	function toolbar_view() {
		require_once("toolbar.view.php");
	}


	/**
	 * Echo ng attributes needed for component settings
	 *
	 * @since 0.1.7
	 */

	function ng_attributes( $param_name, $attributes = "class,model,change") {

		$param_name = sanitize_text_field($param_name);
		
		if ( isset($this->options['shortcode']) && $this->options['shortcode'] ) {
			$shortcode_arg = ", true";
		}

		$attributes = explode(',', $attributes );

		if ( in_array('class-fake', $attributes) ) { ?>
			ng-class="iframeScope.checkOptionChanged(iframeScope.component.active.id,'<?php echo $param_name; ?>')"
		<?php }

		if ( in_array('model', $attributes) ) { ?>
			ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['<?php echo $param_name; ?>']"
			ng-model-options="{ debounce: 10 }"
		<?php }

		if ( in_array('change', $attributes) ) { ?>
			ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'<?php echo $param_name; ?>'<?php echo isset($shortcode_arg)?$shortcode_arg:''; ?>);iframeScope.checkResizeBoxOptions('<?php echo $param_name; ?>')"
		<?php }

		if ( in_array('keypress', $attributes) ) { ?>
			ng-keypress="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'<?php echo $param_name; ?>'<?php echo isset($shortcode_arg)?$shortcode_arg:''; ?>);iframeScope.checkResizeBoxOptions('<?php echo $param_name; ?>')"
		<?php }

	}


	/**
	 * Selector box
	 *
	 * @since 0.1.4
	 */

	function component_header() { ?>

		<div class='oxygen-active-element'
			ng-if="!iframeScope.isEditing('custom-selector')">

			<div class='oxygen-active-element-name'>
				<div ng-bind="iframeScope.component.options[iframeScope.component.active.id]['nicename']"></div>
			</div>

			<div class='oxygen-active-element-icons'
				ng-show="iframeScope.component.active.id < 100000 && !iframeScope.isEditing('style-sheet') && !iframeScope.isEditing('custom-selector') && !iframeScope.isBuiltinComponent()">
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/link.svg'
					title="<?php _e("Link Settings", "oxygen"); ?>"
					data-linkProperty="url" data-linkTarget="target"
					class="oxygen-link-button"
					ng-class="{'ct-link-button-highlight' : iframeScope.getLinkId()}"
					ng-show="!isActiveName('ct_selector') && !isActiveName('ct_widget') && !isActiveName('ct_shortcode') && !isActiveName('ct_code_block')"
					ng-click="processLink()"/>					

				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/duplicate.svg'
					title="<?php _e("Duplicate Component", "oxygen"); ?>"
					ng-show="iframeScope.component.active.id > 0 && iframeScope.component.active.name != 'ct_span'"
					ng-click="iframeScope.duplicateComponent()"/>

				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
					title="<?php _e("Remove Component", "oxygen"); ?>"
					ng-show="iframeScope.component.active.id > 0 && !isActiveName('oxy_header_left') && !isActiveName('oxy_header_center') && !isActiveName('oxy_header_right')"
					ng-click="iframeScope.removeActiveComponent()"/>
			</div>
		</div>
		<!-- .oxygen-active-element -->

		<div class='oxygen-active-element-breadcrumb'
			ng-if="!iframeScope.isEditing('custom-selector')">
			<span ng-repeat='item in iframeScope.selectAncestors'>
				<span ng-if="item.id > 0 && item.id < 100000" ng-click="iframeScope.activateComponent(item.id, item.tag)">{{item.name}}</span>
				<span ng-if="item.id > 0 && item.id < 100000" class="oxygen-active-element-breadcrumb-arrow">&gt;</span>
				<span ng-if="item.id == 0" class='oxygen-active-element-breadcrumb-active'>{{item.name}}</span>
			</span>
		</div>
		<!-- .oxygen-active-element-breadcrumb -->

		<div class='oxygen-media-query-and-selector-wrapper'>
			
			<div class='oxygen-select oxygen-media-query-box-wrapper'>
				<div class='oxygen-media-query-box'
					ng-class="{'oxy-styles-present':iframeScope.isHasMedias()}">
					<img ng-src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/media-query/{{iframeScope.currentMedia}}.svg' />
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg'/>
				</div>
				<ul class="oxygen-media-query-dropdown">
					<li ng-repeat="name in iframeScope.sortedMediaList()"
						ng-click="iframeScope.setCurrentMedia(name);">
							<img ng-src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/media-query/{{name}}.svg'/>
							<span
								ng-class="{'oxy-styles-present':iframeScope.isHasMedia(name),'oxygen-current-media-query':iframeScope.getCurrentMedia()==name}">
								{{iframeScope.getMediaTitle(name)}}
							</span>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/remove_icon.svg'
								title="<?php _e("Remove media styles from component", "oxygen"); ?>"
								ng-click="iframeScope.removeComponentMedia(name); event.stopPropagation()"
								ng-show="iframeScope.isHasMedia(name)"/>
					</li>
				</ul>
			</div>
			<!-- .oxygen-media-query-box -->

			<div class='oxygen-select oxygen-active-selector-box-wrapper'>
				<div class='oxygen-active-selector-box'
					ng-if="iframeScope.isNotSelectedYet(iframeScope.component.active.id)&&!iframeScope.isEditing('custom-selector')"
					ng-click="iframeScope.onSelectorDropdown()">
						<input type='text' spellcheck="false" value="<?php _e( "Choose selector to edit...", "oxygen" ); ?>"/>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg'/>
				</div>
				<div class='oxygen-active-selector-box'
					ng-if="!iframeScope.isNotSelectedYet(iframeScope.component.active.id)"
					ng-click="iframeScope.onSelectorDropdown()">
					
					<div class='oxygen-active-selector-box-id'
						ng-show="iframeScope.isEditing('id')">id</div>
					<div class='oxygen-active-selector-box-class'
						ng-show="iframeScope.isEditing('class')&&!iframeScope.isEditing('custom-selector')">class</div>

					<input type='text' spellcheck="false"
						ng-show="iframeScope.isEditing('id')"
						ng-model="iframeScope.component.options[iframeScope.component.active.id]['selector']"
						ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name, 'selector')"/>

					<input type="text" spellcheck="false"
						ng-show="iframeScope.isEditing('class')&&!iframeScope.isEditing('custom-selector')"
						ng-model="iframeScope.currentClass">

					<input type="text" spellcheck="false"
						ng-show="iframeScope.isEditing('custom-selector')"
						ng-model="iframeScope.selectorToEdit"
						ng-change="selectorChange('{{iframeScope.selectorToEdit}}')">

					<div class='oxygen-active-selector-box-state'
						ng-class="{'oxy-styles-present' : iframeScope.isStatesHasOptions()}">
						{{(iframeScope.currentState=="original") ? "state" : ":"+iframeScope.currentState}}
					</div>
				</div>

				<ul class="oxygen-states-dropdown">
					<li title="<?php _e("Edit original state", "oxygen"); ?>"
						ng-click="iframeScope.switchState('original');">
							<?php _e("original", "oxygen"); ?>
					</li>
					<li title="<?php _e("Edit this state", "oxygen"); ?>"
						ng-repeat="state in iframeScope.getComponentStatesList()"
						ng-click="iframeScope.switchState('original'); iframeScope.switchState(state);"
						ng-class="{'oxy-styles-present':iframeScope.isStateHasOptions(state)}">
							<div>:{{state}}</div>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/remove_icon.svg'
								title="<?php _e("Remove state from component", "oxygen"); ?>"
								ng-click="iframeScope.tryDeleteComponentState(state,$event)"/>

					<li ng-click="iframeScope.addState()">
						<span class="oxygen-states-dropdown-add-state">
							<?php _e("add state...", "oxygen"); ?>
						</span>
					</li>
				</ul>
				<!-- .oxygen-states-dropdown -->

				<ul class="oxygen-classes-dropdown"
					ng-if="!iframeScope.isEditing('custom-selector')">
					<li>
						<input type="text" class="oxygen-classes-dropdown-input"
							placeholder="<?php _e( "Enter class name...", "oxygen" ); ?>"
							ng-model="iframeScope.newcomponentclass.name"
							ng-keypress="iframeScope.processClassNameInput($event, iframeScope.component.active.id)"
							focus-me="$parent.ctSelectBoxFocus" />
						<div class="oxygen-classes-dropdown-add-class"
							ng-click="iframeScope.tryAddClassToComponent(iframeScope.component.active.id)">
							<?php _e("add class...", "oxygen"); ?>
						</div>
					</li>
					<li ng-click="iframeScope.switchEditToId(true)">
						<div class='oxygen-active-selector-box-id'>id</div>
						<div>{{iframeScope.getComponentSelector()}}</div>
					</li>
					<li ng-repeat="(key,className) in iframeScope.componentsClasses[iframeScope.component.active.id]"
						title="<?php _e("Edit this class", "oxygen"); ?>"
						ng-click="iframeScope.setCurrentClass(className)">
							<div class='oxygen-active-selector-box-class'>class</div>
							<div>{{className}}</div>
							<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/remove_icon.svg'
								title="<?php _e("Remove class from component", "oxygen"); ?>"
								ng-click="iframeScope.removeComponentClass(className)"/>
					</li>
				</ul>
				<!-- .oxygen-classes-dropdown -->

				<div class='oxygen-back-to-selector-detector'
					ng-if="iframeScope.isEditing('custom-selector')&&!iframeScope.isEditing('class')&&disableSelectorDetectorMode"
					ng-click="toggleSelectorDetectorMode();">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/other/pencil.svg'
						title="<?php _e( "Selector Detector Mode", "oxygen" ); ?>"/>
				</div>
				<!-- .oxygen-back-to-selector-detector -->
			</div>
			<!-- .oxygen-active-selector-box -->

		</div>
		<!-- .oxygen-media-query-and-selector-wrapper -->

		<div class="oxygen-measure-box ct-noheader" 
			ng-if="iframeScope.isEditing('pseudo-element')&&!iframeScope.isEditing('custom-selector')">
			
				<input type="text" class="ct-expand ct-no-animate" placeholder="<?php _e("content...", "oxygen"); ?>" spellcheck="false"
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['content']"
					ng-change="iframeScope.setOption(iframeScope.component.active.id,iframeScope.component.active.name,'content')"/>
			
		</div>

	<?php }


	/**
	 * Add component advanced settings tabs
	 *
	 * @since 0.1.1
	 */

	function advanced_settings() {

		foreach ( $this->options['advanced'] as $key => $tab ) :

			//$ng_click = ( $key == "cssjs" ) ? "possibleSwitchToCodeEditor('advanced', '$key')" : "switchTab('advanced', '$key');";
			$ng_class = "iframeScope.isTabHasOptions('$key')";

			if ( $key == "custom-js" || $key == "custom-css" ) {
				$ng_show = "&& !isActiveName('ct_code_block')";
				$ng_class = "!iframeScope.isInherited(iframeScope.component.active.id,'$key')";
			}
			if ( $key == "custom-js" ) {
				$ng_show .= "&& !isActiveName('ct_selector')";
			}
			if ( $key == "code-js" || $key == "code-css" || $key == "code-php" ) {
				$ng_show = "&& isActiveName('ct_code_block')";
				$ng_class = "!iframeScope.isInherited(iframeScope.component.active.id,'$key')";
			}
			if ( $key == "effects" ) {
				$ng_show .= "&& !hasOpenTabs('effects')";
			}

			?>

			<div class='oxygen-sidebar-advanced-subtab'
				ng-show="showAllStyles<?php if(isset($ng_show)) echo $ng_show; ?>"
				ng-click="switchTab('advanced', '<?php echo $key; ?>')"
				ng-class="{'oxy-styles-present' : <?php echo $ng_class; ?>}">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/<?php echo $tab['tab_icon']; ?>.svg' />
					<span><?php echo $tab['heading']; ?></span>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
			</div>

			<?php

			if ( strpos( $key, "code" ) !== false || strpos( $key, "cssjs" ) !== false ) {
				$classes = "oxygen-sidebar-code-editor-panel";
			}

			if ( $key == "effects" ) {
				$classes = "oxygen-effects-tab";
			}

			?>

				<div class="<?php echo isset($classes)?$classes:''; ?> <?php echo $key ;?>" ng-if="isShowTab('advanced', '<?php echo $key; ?>')">
					<?php if ( file_exists( CT_FW_PATH . "/toolbar/views/$key.view.php" ) ) :
						include( "views/$key.view.php");
					else : ?>
						<span><?php printf( __( 'Wrong parameter type: %s', 'oxygen' ), "$key" ); ?></span>
					<?php endif; ?>
				</div>

			<?php /*endif;*/

		endforeach;
			?>
				<div class="<?php echo isset($classes)?$classes:''; ?> background-gradient" ng-if="isShowTab('advanced', 'background-gradient')">
					<?php if ( file_exists( CT_FW_PATH . "/toolbar/views/background/background.gradient.view.php" ) ) :
						include( "views/background/background.gradient.view.php");
					else : ?>
						<span><?php printf( __( 'Wrong parameter type: %s', 'oxygen' ), "background-gradient" ); ?></span>
					<?php endif; ?>
				</div>
			<?php
	}


	/**
	 * Output Global Settings
	 *
	 * @since 0.1.9
	 */

	function ct_show_global_fonts_settings() { ?>

		<div class='oxygen-control-wrapper' 
			ng-repeat="(name,font) in iframeScope.globalSettings.fonts">
			<label class='oxygen-control-label'>{{name}} font</label>
			<div class='oxygen-control oxygen-control-global-font'>

				<div class="oxygen-select oxygen-select-box-wrapper">
					<div class="oxygen-select-box">
						<div class="oxygen-select-box-current">{{iframeScope.globalSettings.fonts[name]}}</div>
						<div class="oxygen-select-box-dropdown"></div>
					</div>
					<div class="oxygen-select-box-options">

						<div class="oxygen-select-box-option">
							<input type="text" value="" placeholder="<?php _e("Search...", "oxygen"); ?>" spellcheck="false"
								ng-model="iframeScope.fontsFilter"/>
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="font in iframeScope.elegantCustomFonts | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setGlobalFont(name, font);"
							title="<?php _e("Apply this font family", "oxygen"); ?>">
								{{font}}
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="font in iframeScope.typeKitFonts | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setGlobalFont(name, font.slug);"
							title="<?php _e('Apply this font family', 'oxygen'); ?>">
								{{font.name}}
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="font in iframeScope.fontsList | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setGlobalFont(name,font);"
							title="<?php _e('Apply this font family', 'oxygen'); ?>">
								{{font}}
						</div>

					</div>
					<!-- .oxygen-select-box-options -->
				</div>
				<!-- .oxygen-select.oxygen-select-box-wrapper -->
				<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
					title="<?php _e('Remove Font', 'oxygen'); ?>"
					ng-show="name!='Display'&&name!='Text'"
					ng-click="iframeScope.deleteGlobalFont(name)"/>

			</div>
		</div>
		<!-- #oxygen-typography-font-family -->

		<div class="oxygen-add-global-font" 
			ng-click="iframeScope.addGlobalFont()">
			<?php _e('Add font', 'oxygen'); ?>
		</div>

	<?php }


	/**
	 * Toolbar settings / Defaults Styles / Headings
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings_headings() {

		$headings = array("H1","H2","H3","H4","H5","H6");
		
		foreach ($headings as $heading) : ?>
			<div class="oxygen-settings-section-heading">
				<?php echo $heading; ?>
			</div>

			<div class="oxygen-control-row">
				<div class="oxygen-control-wrapper">
					<label class="oxygen-control-label"><?php _e("Font Size","oxygen"); ?></label>
					<div class="oxygen-control">
						
						<div class="oxygen-measure-box">
							<input type="text" spellcheck="false" 
								ng-model="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size']" 
								ng-model-options="{ debounce: 10 }">
							<div class="oxygen-measure-box-unit-selector">
								<div class="oxygen-measure-box-selected-unit">px</div>
								<div class="oxygen-measure-box-units">
									<div class="oxygen-measure-box-unit oxygen-measure-box-unit-active" 
										ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size-unit'] = 'px'" 
										ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size-unit']=='px'}">
											px
									</div>
									<div class="oxygen-measure-box-unit" 
										ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size-unit'] = '%'" 
										ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size-unit']=='%'}">
											%
									</div>
									<div class="oxygen-measure-box-unit" 
										ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size-unit'] = 'em'" 
										ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-size-unit']=='em'}">
											em
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
					<div class='oxygen-control'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box">
								<div class="oxygen-select-box-current">{{$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']=''">&nbsp;</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='100'">100</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='200'">200</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='300'">300</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='400'">400</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='500'">500</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='600'">600</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='700'">700</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='800'">800</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['font-weight']='900'">900</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
					<div class='oxygen-control'>

						<div class='oxygen-color-picker'>
							<div class="oxygen-color-picker-color">
								<input ctiriscolorpicker=""
									class="ct-iris-colorpicker"
									type="text" spellcheck="false"
									ng-model="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['color']"
									ng-style="{'background-color':$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['color']}"/>
							</div>
							<input type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.headings['<?php echo $heading; ?>']['color']"/>
						</div>
					</div>
				</div>
			</div>

		<?php endforeach;

	}


	/**
	 * Toolbar settings / Defaults Styles / Body Text
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings_body_text() { ?>

		<div class="oxygen-control-row">
			<div class="oxygen-control-wrapper">
				<label class="oxygen-control-label"><?php _e("Font Size","oxygen"); ?></label>
				<div class="oxygen-control">
					
					<div class="oxygen-measure-box">
						<input type="text" spellcheck="false" 
							ng-model="$parent.iframeScope.globalSettings.body_text['font-size']" 
							ng-model-options="{ debounce: 10 }">
						<div class="oxygen-measure-box-unit-selector">
							<div class="oxygen-measure-box-selected-unit">{{$parent.iframeScope.globalSettings.body_text['font-size-unit']}}</div>
							<div class="oxygen-measure-box-units">
								<div class="oxygen-measure-box-unit oxygen-measure-box-unit-active" 
									ng-click="$parent.iframeScope.globalSettings.body_text['font-size-unit'] = 'px'" 
									ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.body_text['font-size-unit']=='px'}">
										px
								</div>
								<div class="oxygen-measure-box-unit" 
									ng-click="$parent.iframeScope.globalSettings.body_text['font-size-unit'] = '%'" 
									ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.body_text['font-size-unit']=='%'}">
										%
								</div>
								<div class="oxygen-measure-box-unit" 
									ng-click="$parent.iframeScope.globalSettings.body_text['font-size-unit'] = 'em'" 
									ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.body_text['font-size-unit']=='em'}">
										em
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
				<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
				<div class='oxygen-control'>

					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box">
							<div class="oxygen-select-box-current">{{$parent.iframeScope.globalSettings.body_text['font-weight']}}</div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']=''">&nbsp;</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='100'">100</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='200'">200</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='300'">300</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='400'">400</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='500'">500</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='600'">600</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='700'">700</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='800'">800</div>
							<div class="oxygen-select-box-option" 
								ng-click="$parent.iframeScope.globalSettings.body_text['font-weight']='900'">900</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="oxygen-control-row">
			<div class="oxygen-control-wrapper">
				<label class="oxygen-control-label"><?php _e("Line Height","oxygen"); ?></label>
				<div class="oxygen-control">
					<div class="oxygen-input">
						<input type="text" spellcheck="false" 
							ng-model="$parent.iframeScope.globalSettings.body_text['line-height']" 
							ng-model-options="{ debounce: 10 }">
					</div>
				</div>
			</div>
		</div>

		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
				<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
				<div class='oxygen-control'>

					<div class='oxygen-color-picker'>
						<div class="oxygen-color-picker-color">
							<input ctiriscolorpicker=""
								class="ct-iris-colorpicker"
								type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.body_text['color']"
								ng-style="{'background-color':$parent.iframeScope.globalSettings.body_text['color']}"/>
						</div>
						<input type="text" spellcheck="false"
							ng-model="$parent.iframeScope.globalSettings.body_text['color']"/>
					</div>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Toolbar settings / Defaults Styles / Links
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function settings_links() { ?>

		<?php $links = array(	"all" => __("All","oxygen"),
								"text_link" => __("Text Link","oxygen"),
								"link_wrapper" => __("Link Wrapper","oxygen"),
								"button" => __("Button","oxygen") );

		foreach ($links as $link => $title) : ?>

		<div class="oxygen-sidebar-advanced-subtab" 
			ng-hide="<?php foreach($links as $link2 => $title2):?>isShowChildTab('settings','links','<?php echo $link2 ?>')<?php if ($link2!="button") echo "||"; endforeach; ?>"
			ng-click="switchChildTab('settings', 'links', '<?php echo $link ?>');">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/styles.svg">
			<?php echo $title; ?>
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
		</div>

		<div
			ng-show="isShowChildTab('settings','links','<?php echo $link ?>')">
					
			<?php if ($link!="button") : ?>
			<div class="oxygen-settings-section-heading"><?php _e("Normal","oxygen"); ?></div>
			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
					<div class='oxygen-control'>
						<div class='oxygen-color-picker'>
							<div class="oxygen-color-picker-color">
								<input ctiriscolorpicker=""
									class="ct-iris-colorpicker"
									type="text" spellcheck="false"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['color']"
									ng-style="{'background-color':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['color']}"/>
							</div>
							<input type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['color']"/>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
					<div class='oxygen-control'>

						<div class="oxygen-select oxygen-select-box-wrapper">
							<div class="oxygen-select-box">
								<div class="oxygen-select-box-current">{{$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']}}</div>
								<div class="oxygen-select-box-dropdown"></div>
							</div>
							<div class="oxygen-select-box-options">
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']=''">&nbsp;</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='100'">100</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='200'">200</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='300'">300</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='400'">400</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='500'">500</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='600'">600</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='700'">700</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='800'">800</div>
								<div class="oxygen-select-box-option" 
									ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['font-weight']='900'">900</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<?php if ($link!="button") : ?>
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Text Decoration"); ?></label>
				<div class='oxygen-control'>
					<div class='oxygen-button-list'>

						<label class='oxygen-button-list-button'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='none'}">
								<input type="radio" name="text-decoration" value="none"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'none')"/>
								none
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-underline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='underline'}">
								<input type="radio" name="text-decoration" value="underline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'underline')"/>
								U
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-overline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='overline'}">
								<input type="radio" name="text-decoration" value="overline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'overline')"/>
								O
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-linethrough'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']=='line-through'}">
								<input type="radio" name="text-decoration" value="line-through"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'text-decoration', 'line-through')"/>
								S
						</label>

					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if ($link=="button") : ?>
			<div class="oxygen-control-row">
				<div class="oxygen-control-wrapper">
					<label class="oxygen-control-label"><?php _e("Border radius","oxygen"); ?></label>
					<div class="oxygen-control">
						
						<div class="oxygen-measure-box">
							<input type="text" spellcheck="false" 
								ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius']" 
								ng-model-options="{ debounce: 10 }">
							<div class="oxygen-measure-box-unit-selector">
								<div class="oxygen-measure-box-selected-unit">{{$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius-unit']}}</div>
								<div class="oxygen-measure-box-units">
									<div class="oxygen-measure-box-unit"
										ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius-unit']='px'"
										ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius-unit']=='px'}">
										px
									</div>
									<div class="oxygen-measure-box-unit"
										ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius-unit']='%'"
										ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius-unit']=='%'}">
										&#37;
									</div>
									<div class="oxygen-measure-box-unit"
										ng-click="$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius-unit']='em'"
										ng-class="{'oxygen-measure-box-unit-active':$parent.iframeScope.globalSettings.links['<?php echo $link; ?>']['border-radius-unit']=='em'}">
										em
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($link!="button") : ?>
			<div class="oxygen-settings-section-heading"><?php _e("Hover","oxygen"); ?></div>

			<div class="oxygen-control-row">
				<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
					<label class='oxygen-control-label'><?php _e("Color","oxygen"); ?></label>
					<div class='oxygen-control'>
						<div class='oxygen-color-picker'>
							<div class="oxygen-color-picker-color">
								<input ctiriscolorpicker=""
									class="ct-iris-colorpicker"
									type="text" spellcheck="false"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_color']"
									ng-style="{'background-color':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_color']}"/>
							</div>
							<input type="text" spellcheck="false"
								ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_color']"/>
						</div>
					</div>
				</div>
			</div>

			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Text Decoration"); ?></label>
				<div class='oxygen-control'>
					<div class='oxygen-button-list'>

						<label class='oxygen-button-list-button'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='none'}">
								<input type="radio" name="text-decoration" value="none"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'none')"/>
								none
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-underline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='underline'}">
								<input type="radio" name="text-decoration" value="underline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'underline')"/>
								U
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-overline'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='overline'}">
								<input type="radio" name="text-decoration" value="overline"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'overline')"/>
								O
						</label>

						<label class='oxygen-button-list-button oxygen-text-decoration-linethrough'
							ng-class="{'oxygen-button-list-button-active':$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']=='line-through'}">
								<input type="radio" name="text-decoration" value="line-through"
									ng-model="$parent.iframeScope.globalSettings.links['<?php echo $link ?>']['hover_text-decoration']"
									ng-model-options="{ debounce: 10 }" 
									ng-click="$parent.globalSettingsRadioButtonClick($parent.iframeScope.globalSettings.links['<?php echo $link ?>'], 'hover_text-decoration', 'line-through')"/>
								S
						</label>

					</div>
				</div>
			</div>
			<?php endif; ?>

		</div>

		<?php endforeach;
	}


	/**
	 * Components Browser tabs anchors
	 *
	 * @since 0.2.3
	 */

	function components_anchors() { ?>

		<?php
			if ( $this->folders["status"] == "ok" ) {
				$this->output_top_folders_anchors( $this->folders );
			}
			/*elseif (!get_option('oxygen_license_key')) {
				// do nothing
			}
			elseif ( $this->folders["status"] == "error" && isset($this->folders["message"])) {
				echo "<span class=\"ct-folders-anchors-error\">".sanitize_text_field($this->folders["message"])."</span>";
			}
			elseif ( $this->folders["status"] == "error" && is_array($this->folders["errors"])) {
				echo "<span class=\"ct-folders-anchors-error\">".sanitize_text_field($this->folders["errors"][0])."</span>";
			}
			else {
				var_dump( $this->folders );
			}*/
		?>

		<?php
	}


	/**
	 * Recursively output all folders' content
	 *
	 * @since 0.4.0
	 * @author Ilya K.
	 */

	function output_folders_content( $folders, $main_key = "", $title = "", $path="", $depth = 0 ) {

		if ( !is_array( $folders ) )
			return;

		$depth++;

		unset($folders["status"]);

		if ( $main_key ) {
			$path = "switchTab('components','" . esc_attr( $main_key ) . "')";
		}

		if ( $main_key && $depth > 2) {
			$path = "iframeScope.openFolder('" . esc_attr( $main_key ) . "')";
		}

		global $folder_type;
		global $folder_class;

		foreach ( $folders as $key => $folder ) :

			if ( !is_array( $folder ) )
				continue;

			$slug = (isset($folder["name"]) ? sanitize_title($folder["name"]):'') . "-" . (isset($folder["id"])?$folder["id"]:'');

			// show only top anchors
			if ($title==""&&$path=="") { ?>

				<div class='oxygen-add-section-accordion'
					ng-click="switchTab('components', '<?php echo $slug; ?>');"
					ng-hide="iframeScope.hasOpenFolders()">
					<?php echo sanitize_text_field($folder["name"]); ?>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg' />
				</div>

			<?php }

			if ( isset($folder["id"]) && ($folder["id"] === "design_sets" || $folder["id"] === "components" || $folder["id"] === "pages") ) {
				$folder_type = isset($folder["id"])?$folder["id"]:false;
				$folder_class = "ct-api-items";
			}

			if ( isset($folder["name"]) && $folder["name"] === "WordPress" ) {
				$folder_class = "";
			}

			if ($path !== "") : ?>

				<div class="oxygen-sidebar-breadcrumb"
					ng-if="iframeScope.isShowFolder('<?php echo $slug; ?>') && iframeScope.designSetSubTab !== 1 && iframeScope.designSetSubTab !== 2">

						<div class="oxygen-sidebar-breadcrumb-icon">
							<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg" 
								ng-click="<?php echo $path; ?>">
						</div>
						<div class="oxygen-sidebar-breadcrumb-all-styles"
							ng-click="<?php echo $path; ?>">
							<?php echo esc_html( $title ) ?>		
						</div>
						<div class="oxygen-sidebar-breadcrumb-separator">/</div>
						<div class="oxygen-sidebar-breadcrumb-current"><?php echo sanitize_text_field($folder["name"]); ?></div>
				</div>

				<div class="oxygen-sidebar-breadcrumb"
					ng-if="iframeScope.isShowFolder('<?php echo $slug; ?>') && (iframeScope.designSetSubTab === 1 || iframeScope.designSetSubTab === 2)">
						<div class="oxygen-sidebar-breadcrumb-icon">
							<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg" 
								ng-click="iframeScope.designSetSubTab=0">
						</div>
						<div class="oxygen-sidebar-breadcrumb-all-styles"
							ng-click="iframeScope.designSetSubTab=0">
							<?php echo sanitize_text_field($folder["name"]); ?>
						</div>
						<div class="oxygen-sidebar-breadcrumb-separator">/</div>
						<div class="oxygen-sidebar-breadcrumb-current">{{(iframeScope.designSetSubTab===1?'Components':'Pages')}}</div>
				</div>

				<div class="oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad oxygen-folder-<?php echo $slug; ?> <?php echo $folder_class; ?>" 
					ng-class="{'oxygen-folder-no-padding': !iframeScope.isShowFolder('design-sets-experimental') 
																	&& !iframeScope.isShowFolder('dynamic-data-data')
																	&& !iframeScope.isShowFolder('widgets-widgets')
																	&& !iframeScope.isShowFolder('sidebars-sidebars') }"
					ng-if="iframeScope.isShowFolder('<?php echo $slug; ?>')" >	

					
					<div class='oxygen-add-section-library-menu'
						ng-if="iframeScope.isShowFolder('categories-categories')">
						<div class='oxygen-add-section-library-menu-category'>
							<h1>Sections &amp; Elements</h1>
							<div class='oxygen-add-section-library-menu-subcategories'>
								<a ng-repeat="(key, category) in iframeScope.libraryCategories track by key" data-cat='category-{{category.slug}}'>{{key}}<span class='oxygen-add-section-library-count'>{{category.contents.length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
							</div>
						</div>
					</div>

					<div class='oxygen-add-section-library-menu'
						ng-if="iframeScope.isShowFolder('categories-categories')">
						<div class='oxygen-add-section-library-menu-category'>
							<h1>Pages</h1>
							<div class='oxygen-add-section-library-menu-subcategories'>
								<a ng-repeat="(key, category) in iframeScope.libraryPages track by key" data-cat='page-{{category.slug}}'>{{key}}<span class='oxygen-add-section-library-count'>{{category.contents.length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
							</div>
						</div>
					</div>
					
					<div class='oxygen-add-section-library-menu'
						ng-show="iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']] && iframeScope.designSetSubTab!==1 && iframeScope.designSetSubTab!==2">
						<div class='oxygen-add-section-library-menu-category'>
							<div class='oxygen-add-section-library-menu-subcategories'>
								<a data-cat='designset-{{iframeScope.openFolders["<?php echo $slug; ?>"]}}-pages' class="oxygen-add-designset-pages">Pages<span class='oxygen-add-section-library-count'>{{iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['pages'].length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
								<a data-cat='designset-{{iframeScope.openFolders["<?php echo $slug; ?>"]}}-templates' class="oxygen-add-designset-templates">Templates<span class='oxygen-add-section-library-count'>{{iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['templates'].length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
							</div>
						</div>
					</div>


					<div style="margin: 20px;" ng-show="iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']] && iframeScope.designSetSubTab!==1 && iframeScope.designSetSubTab!==2" class="oxygen-add-section-subsection" ng-click="iframeScope.designSetSubTab=1; applyMenuAim()">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-components.svg" class="oxygen-add-section-subsection-icon">
						Sections &amp; Elements
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-arrow.svg">
					</div>

					<div ng-show="iframeScope.designSetSubTab===1" class="oxygen-folder-no-padding oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad oxygen-folder-<?php echo $slug; ?> <?php echo $folder_class; ?>"  >	

					
						<div class='oxygen-add-section-library-menu'>
							<div class='oxygen-add-section-library-menu-category'>
								
								<div class='oxygen-add-section-library-menu-subcategories'>
									<a ng-repeat="(key, category) in iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['items'] track by key" data-cat='category-{{iframeScope.openFolders["<?php echo $slug; ?>"]}}-{{category.slug}}'>{{key}}<span class='oxygen-add-section-library-count'>{{category.contents.length}}</span><img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg"></a>
								</div>
							</div>
						</div>

					</div>
					
					<div ng-show="iframeScope.designSetSubTab===2" class="oxygen-add-section-designed-component" ng-repeat = "item in iframeScope.experimental_components[iframeScope.openFolders['<?php echo $slug; ?>']]['pages']"> 
						<div 
							ng-click="iframeScope.showAddItemDialog(item.id, 'component', '0', '', item.source, null, iframeScope.openFolders['<?php echo $slug; ?>'])">
							<div class="oxygen-add-section-designed-component-header">
								<span class="oxygen-add-section-designed-component-title">{{item.name}}</span>
								<span class="oxygen-add-section-designed-component-design-label"></span>
								<span class="oxygen-add-section-designed-component-add-icon" title="<?php _e("Add now","oxygen")?>"
									ng-click="iframeScope.addItem(item.id, 'page', $event, item.source)"></span>
							</div>
							<img class="ct-add-item-button-image" data-src="{{item.screenshot_url}}">
						</div>
					</div>

					
			<?php else: ?>

				<div class="oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad oxygen-folder-<?php echo $slug; ?> <?php echo $folder_class; ?>" 
					ng-if="isShowTab('components','<?php echo $slug; ?>')">

			<?php endif; ?>


				<?php if ( isset($folder["id"]) && $folder["id"] === "widgets" ) : ?>
					<?php do_action("ct_toolbar_widgets_folder"); ?>

				<?php elseif ( isset($folder["id"]) && $folder["id"] === "data" ) : ?>
					<?php do_action("ct_toolbar_data_folder"); ?>

				<?php elseif ( isset($folder["id"]) && $folder["id"] === "sidebars" ) : ?>
					<?php do_action("ct_toolbar_sidebars_folder"); ?>

				<?php else : ?>

					<?php if ( isset($folder["name"]) && $folder["name"] === "WordPress" ) : ?>
						<?php do_action("oxy_folder_wordpress_components"); ?>
					<?php endif; ?>

					<?php if ( isset($folder["children"]) && $folder["children"] ) : ?>
						<?php foreach ( $folder["children"] as $subkey => $subfolder ) :

							if(isset($subfolder['code'])) {
								echo $subfolder['code'];
								continue;
							}
							
							$subslug = sanitize_title($subfolder["name"]) . "-" . $subfolder["id"];
							$icon = str_replace(" ", "", strtolower($subfolder["name"]));
							// check if icon exist
							if (!in_array($icon, array("components","designsets","dynamicdata","sidebars","widgets"))) {
								$icon = "generic";
							}
						?>
			
							<?php if ( isset($subfolder["component"]) && $subfolder["component"] ) : ?>
								<?php do_action("ct_folder_component_" . $subslug); ?>
							<?php else : ?>
								
								<div class="oxygen-add-section-subsection"
								<?php if(isset($subfolder["fresh"])) { ?>
									ng-click="iframeScope.openLoadFolder('<?php echo $subslug; ?>', '<?php echo $subfolder['name'];?>');tabs['components']=[]"
								<?php } else { ?>
									ng-click="iframeScope.openFolder('<?php echo $subslug; ?>');tabs['components']=[]"
								<?php } ?>
									>
									<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-<?php echo $icon; ?>.svg" class="oxygen-add-section-subsection-icon">
									<?php echo sanitize_text_field( $subfolder["name"]) ; ?>
									<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-arrow.svg">
								</div>
	
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if ( isset($folder["items"]) && $folder["items"] ) : ?>
						<?php foreach ( $folder["items"] as $subkey => $subfolder ) :
							if ( empty( $subfolder ) || ! is_array( $subfolder ) ) {
								continue;
							}
							$subslug = sanitize_title( $subfolder["name"] ) . "-" . $subfolder["id"];

							// update screenshot to use imgix
							if ( $subfolder["screenshot_url"] && strpos( $subfolder["screenshot_url"], "s3.amazonaws.com") !== false ) {
								$subfolder["screenshot_url"] = str_replace(
																	"https://s3.amazonaws.com/asset-dev-testing/",
																	"https://oxygen.imgix.net/", $subfolder["screenshot_url"]);
								$subfolder["screenshot_url"] .= "?w=520";
							}
						?>

							<?php if ( isset ( $subfolder["component"] ) ) : ?>
								<?php do_action("ct_folder_component_".$subslug ); ?>
							<?php else : ?>
								<div class="oxygen-add-section-designed-component"
									ng-click="iframeScope.showAddItemDialog(<?php echo sanitize_text_field($subfolder["id"]); ?>, '<?php echo sanitize_text_field($folder["type"]); ?>', '<?php echo sanitize_text_field($folder["id"]); ?>', '<?php echo sanitize_text_field($folder_type); ?>'<?php echo isset($subfolder["source"])?", '".sanitize_text_field($subfolder["source"])."'":""; echo isset($subfolder["page"])?", '".sanitize_text_field($subfolder["page"])."'":"";?>)">
									<div class="oxygen-add-section-designed-component-header">
										<span class="oxygen-add-section-designed-component-title"><?php echo sanitize_text_field( $subfolder["name"] ); ?></span>
										<span class="oxygen-add-section-designed-component-design-label"><?php echo sanitize_text_field( $subfolder["design_set_name"] ); ?></span>
										<span class="oxygen-add-section-designed-component-add-icon" title="<?php _e("Add now","oxygen")?>"
											ng-click="iframeScope.addItem(<?php echo sanitize_text_field($subfolder["id"]); ?>, '<?php echo sanitize_text_field($folder["type"]); ?>', $event<?php echo isset($subfolder["source"])?", '".sanitize_text_field($subfolder["source"])."'":""; echo isset($subfolder["page"])?", '".sanitize_text_field($subfolder["page"])."'":"";?>)"></span>
									</div>
									<img class="ct-add-item-button-image" data-src="<?php echo esc_url($subfolder["screenshot_url"]); ?>">
								</div>
							<?php endif; ?>

						<?php endforeach; ?>
					<?php endif; ?>

					<?php if ( isset($folder["id"]) && $folder["id"] === "design_sets" ) : ?>

						<div class="ct-add-component-button" ng-if="iframeScope.isDev()" ng-click="iframeScope.showCreateDesignSet()">
							<div class="ct-add-component-icon">
								<span class="ct-icon"></span>
							</div>
							<?php echo "Add Design Set..."; ?>
						</div>

					<?php endif; ?>

				<?php endif; ?>
			</div>

			<?php $this->output_folders_content( isset($folder["children"])?$folder["children"]:null, $slug, isset($folder["name"])?$folder["name"]:null, $path, $depth ); ?>

		<?php endforeach;

	}


	/**
	 * Components Browser tabs
	 *
	 * @since 0.2.3
	 */

	function components_list() { ?>

		<div class='oxygen-add-section-accordion'
			ng-click="switchTab('components', 'fundamentals');"
			ng-hide="iframeScope.hasOpenFolders()">
			<?php _e("Basics", "oxygen") ?>
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg'/>
		</div>
		<div class='oxygen-add-section-accordion-contents'
			ng-if="isShowTab('components','fundamentals')">
			<h2><?php _e("Containers", "oxygen");?></h2>
			<?php do_action("oxygen_basics_components_containers"); ?>
			
			<h2><?php _e("Text", "oxygen");?></h2>
			<?php do_action("oxygen_basics_components_text"); ?>
			
			<h2><?php _e("Links", "oxygen");?></h2>
			<?php do_action("oxygen_basics_components_links"); ?>
			
			<h2><?php _e("Visual", "oxygen");?></h2>
			<?php do_action("oxygen_basics_components_visual"); ?>
			
			<h2><?php _e("Other", "oxygen");?></h2>
			<?php do_action("ct_toolbar_fundamentals_list"); ?>
		</div>

		<div class='oxygen-add-section-accordion'
			ng-click="switchTab('components', 'smart');"
			ng-hide="iframeScope.hasOpenFolders()">
			<?php _e("Helpers", "oxygen") ?>
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg'/>
		</div>
		<div class='oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad'
			ng-if="isShowTab('components','smart')">
			<h2><?php _e("Composite", "oxygen");?></h2>
			<?php do_action("oxygen_helpers_components_composite"); ?>
			
			<h2><?php _e("Dynamic", "oxygen");?></h2>
			<?php do_action("oxygen_helpers_components_dynamic"); ?>
			
			<h2><?php _e("Interactive", "oxygen");?></h2>
			<?php do_action("oxygen_helpers_components_interactive"); ?>
			
			<h2><?php _e("External", "oxygen");?></h2>
			<?php do_action("oxygen_helpers_components_external"); ?>
		</div>

		<?php $this->output_folders_content( array(
												"wordpress" => array(
													"name" 	=> "WordPress",
													"children" => array(
														array(
															"name" 	=> "Dynamic Data",
															"id" 	=> "data" ),
														array(
															"name" 	=> "Widgets",
															"id" 	=> "widgets" ),
														array(
															"name" 	=> "Sidebars",
															"id" 	=> "sidebars" )
													)
												)
											) , "", "" ); ?>
		

		<?php 
		//if ( $this->folders["status"] == "ok" ) {
			$this->output_folders_content( $this->folders, "", "");
//		} 
		?>

		<div class='oxygen-add-section-accordion'
			ng-click="switchTab('components', 'reusable_parts');"
			ng-hide="iframeScope.hasOpenFolders()">
			<?php _e("Reusable", "oxygen") ?>
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dropdown-arrow.svg'/>
		</div>
		<div class='oxygen-add-section-accordion-contents oxygen-add-section-accordion-contents-toppad'
			ng-if="isShowTab('components','reusable_parts')">
			<?php do_action("ct_toolbar_reusable_parts"); ?>
		</div>
	<?php }


	/**
	 * Add all "Re-usable parts" to Components browser
	 *
	 * @since  0.2.3
	 */

	function ct_reusable_parts() {

		// Get all archive templates
		$args = array(
			'posts_per_page'	=> -1,
			'orderby' 			=> 'date',
			'order' 			=> 'DESC',
			'post_type' 		=> 'ct_template',
			'post_status' 		=> 'publish',
			'meta_key'   		=> 'ct_template_type',
			'meta_value' 		=> 'reusable_part'
		);

		$templates = new WP_Query( $args );

		foreach ( $templates->posts as $template ) : ?>

			<div class="oxygen-add-section-element">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/reusable.svg">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/reusable-active.svg">
				<?php echo esc_html( $template->post_title ); ?>
				<div class="oxygen-add-section-element-options">
					<div class="oxygen-add-section-element-option" title="<?php _e("Add Re-usable part as single component", "oxygen")?>"
						ng-click="iframeScope.loadReusablePart(<?php echo esc_attr( $template->ID ); ?>)">
						<?php _e("Single", "oxygen"); ?>
					</div>
					<div class="oxygen-add-section-element-option" title="<?php _e("Add Re-usable part as editable fundamentals", "oxygen")?>"
						ng-click="iframeScope.loadReusablePart(<?php echo esc_attr( $template->ID ); ?>, iframeScope.component.active.id)">
						<?php _e("Editable", "oxygen"); ?>
					</div>
				</div>
			</div>	

		<?php endforeach;

	}


	/**
	 * Output Page Settings in Builders Toolbar
	 *
	 * @since 0.1.3
	 */

	function ct_show_page_settings() { ?>
		
		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php _e("Page Width","oxygen"); ?></label>
				<div class='oxygen-measure-box'>
					<input type="text" spellcheck="false"
						ng-model="iframeScope.pageSettings['max-width']"
						ng-change="iframeScope.pageSettingsUpdate()"/>
					<div class='oxygen-measure-box-unit-selector'>
						<div class='oxygen-measure-box-selected-unit'>px</div>
					</div>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Output .measure-type-select element
	 *
	 * @since 0.3.0
	 */

	static public function measure_type_select_layers($option, $param = 'layer', $types = "px,%,em,auto") {

		$types = explode(",", $types);

		?>

		<div class="ct-measure-type-select">
			<?php if (in_array("px", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = 'px'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='px'}">
				<span class="ct-bullet"></span> PX
			</div>
			<?php endif; ?>
			<?php if (in_array("%", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = '%'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='%'}">
				<span class="ct-bullet"></span> &#37;
			</div>
			<?php endif; ?>
			<?php if (in_array("em", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = 'em'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='em'}">
				<span class="ct-bullet"></span> EM
			</div>
			<?php endif; ?>
			<?php if (in_array("auto", $types)) : ?>
			<div class="ct-button ct-measure-type-option"
				ng-click="<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] = 'auto'; setOptionForBGLayers()"
				ng-class="{'ct-active':<?php echo $param; ?>['<?php echo esc_attr( $option ); ?>-unit'] =='auto'}">
				<span class="ct-bullet"></span> <?php _e("Auto", "oxygen"); ?>
			</div>
			<?php endif; ?>
		</div>

	<?php }

	/**
	 * Output .measure-type-select element
	 *
	 * @since 0.3.0
	 */

	static public function measure_type_select($option, $types = "px,%,em,auto,vw,vh") {

		if ( $types === "" || $types === NULL ) {
			$types = "px,%,em,auto,vw,vh";
		}

		$types = explode(",", $types);

		?>

		<div class="oxygen-measure-box-units">
			<?php if (in_array("px", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'px')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='px'}">
				px
			</div>
			<?php endif; ?>
			<?php if (in_array("%", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', '%')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='%'}">
				&#37;
			</div>
			<?php endif; ?>
			<?php if (in_array("em", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'em')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='em'}">
				em
			</div>
			<?php endif; ?>
			<?php if (in_array("auto", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'auto')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'}">
				<?php _e("auto", "oxygen"); ?>
			</div>
			<?php endif; ?>
			<?php if (in_array("vw", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'vw')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='vw'}">
				vw
			</div>
			<?php endif; ?>
			<?php if (in_array("vh", $types)) : ?>
			<div class="oxygen-measure-box-unit"
				ng-click="iframeScope.setOptionUnit('<?php echo esc_attr( $option ); ?>', 'vh')"
				ng-class="{'oxygen-measure-box-unit-active':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='vh'}">
				vh
			</div>
			<?php endif; ?>
		</div>

	<?php }


	/**
	 * Output .oxygen-measure-box-options element
	 *
	 * @since 0.3.0
	 */

	function measure_box_options( $option, $units = "" ) { ?>

		<div class="oxygen-measure-box-options">

			<label>
			<?php /*	<input class="oxygen-apply-opposite-trigger" type="radio" name="<?php echo esc_attr( $option ); ?>_measure"
					data-option="<?php echo esc_attr( $option ); ?>"
					data-opposite-option="<?php echo $opposite_option; ?>"/>
				<span><?php echo $text ?></span>*/ ?>
			</label>

			<div class='oxygen-measure-box'
				ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'}">
				<input type='text' type="text" spellcheck="false"
					<?php $this->ng_attributes($option); ?>/>
				<div class='oxygen-measure-box-unit-selector'>
					<div class='oxygen-measure-box-selected-unit'>{{iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')}}</div>
					<?php $this->measure_type_select($option, $units); ?>
				</div>
			</div>

			<label>
				<input class="oxygen-apply-all-trigger" type="radio" name="<?php echo esc_attr( $option ); ?>_measure"
					data-option="<?php echo esc_attr( $option ); ?>"/>
				<span><?php _e("Apply All", "oxygen"); ?></span>
			</label>

		</div>
	<?php }


	/**
	 * Output dialog window settings
	 *
	 * @since 0.2.4
	 */

	function dialog_window() { ?>

		<?php
			// TODO: avoid additional API call here
			global $oxygen_api;
			$categories = $oxygen_api->get_categories();
			unset($categories["status"]);
			array_walk($categories, function(&$value, &$key) {
				if(isset($value["name"]))
			    	$value["name"] 	= sanitize_text_field($value["name"]);
			    if(isset($value["id"]))
			    	$value["id"] 	= sanitize_text_field($value["id"]);
			});
		?>

		<div ng-if="dialogForms['showComponentizeForm']">
			<div id="ct-dialog-componentize-form" class="ct-dialog-componentize-form">
				ID (keep empty for new component) <br/>
				<input type="text" class="ct-textbox ct-textbox-huge" ng-model="iframeScope.componentizeOptions.idToUpdate"><br/><br/>
				Name <br/>
				<input type="text" class="ct-textbox ct-textbox-huge" ng-model="iframeScope.componentizeOptions.name"><br/><br/>
				Category <br/>
				<select class="ct-select" ng-model="iframeScope.componentizeOptions.categoryId">
					<?php foreach ($categories as $key => $category) : ?>
						<option value="<?php echo isset($category["id"])? esc_attr( $category["id"] ):''; ?>"><?php echo isset($category["name"])? esc_html( $category["name"] ):''; ?></option>
					<?php endforeach; ?>
				</select><br/>
				Design Set ID <br/>
				<input type="text" class="ct-textbox" ng-model="iframeScope.componentizeOptions.designSetId"><br/>
				Screenshot <br/>
				<input type="file" file-model="iframeScope.componentizeOptions.screenshot"><br/>
			</div>
			<br/>
			<button class="ct-action-button" ng-click="iframeScope.componentize()"><?php echo "Submit"; ?></button>
		</div>

		<div ng-if="dialogForms['showPageComponentizeForm']">
			<div id="ct-dialog-page-componentize-form" class="ct-dialog-page-componentize-form">
				Name <br/>
				<input type="text" class="ct-textbox ct-textbox-huge" ng-model="iframeScope.componentizeOptions.pageName"><br/><br/>
				Design Set ID <br/>
				<input type="text" class="ct-textbox" ng-model="iframeScope.componentizeOptions.designSetId"><br/>
				Screenshot <br/>
				<input type="file" file-model="iframeScope.componentizeOptions.screenshot"><br/>
			</div>
			<br/>
			<button class="ct-action-button" ng-click="iframeScope.tryPageComponentize()"><?php echo "Submit"; ?></button>
		</div>

		<div ng-if="dialogForms['showAddItemDialogForm']" id='ct-add-component-page-dialog'>
			<div id="ct-dialog-add-item-form" class="ct-dialog-add-item-form">
				<div class="clearfix">
					<span class="ct-component-title">{{iframeScope.stripSlashes(iframeScope.itemOptions.currentItem['name'])}}</span>
					<span class="ct-dialog-item-design-label" ng-if="iframeScope.itemOptions.currentItem['design_set_name']">{{iframeScope.stripSlashes(iframeScope.itemOptions.currentItem['design_set_name'])}}</span>
					<button class="ct-action-button ct-add-form-button" ng-click="iframeScope.addItem()"><?php echo "Add Component to Page"; ?></button>
				</div>
				<div class='ct-component-img-container'>
					<img ng-src="{{iframeScope.itemOptions.currentItem['screenshot_url']}}" alt="<?php _e("Item screenshot", "oxygen"); ?>" />
				</div>
				<div class="ct-component-nav-arrows clearfix">
					<span class="ct-component-nav-arrow ct-component-nav-left"
						ng-click="iframeScope.switchComponent(null,'left')"><?php _e("&laquo; Previous", "oxygen"); ?></span>
					<span class="ct-component-nav-arrow ct-component-nav-right"
						ng-click="iframeScope.switchComponent(null,'right')"><?php _e("Next &raquo;", "oxygen"); ?></span>
				</div>
				<button ng-if="iframeScope.isDev()" class="ct-action-button ct-upload-screenshot-button" ng-click="iframeScope.showUpdateScreenshot()"><?php echo "Update Screenshot"; ?></button>
			</div>
		</div>

		<div ng-if="dialogForms['showUploadAsset']">
			<div id="ct-dialog-upload-asset-form" class="ct-dialog-upload-asset-form">
				Screenshot <br/>
				<input type="file" file-model="iframeScope.componentizeOptions.screenshot"><br/>
			</div>
			<br/>
			<button class="ct-action-button" ng-click="iframeScope.updateScreenshot()"><?php echo "Submit"; ?></button>
		</div>

		<div ng-if="dialogForms['showAddDesignSet']">
			<div id="ct-dialog-design-set-form" class="ct-dialog-design-set-form">
				Name <br/>
				<input type="text" class="ct-textbox ct-textbox-huge" ng-model="iframeScope.componentizeOptions.setName"><br/><br/>
				Status <br/>
				<select class="ct-select" ng-model="iframeScope.componentizeOptions.status">
					<option value="public">public</option>
					<option value="dev">dev</option>
				</select><br/>
			</div>
			<br/>
			<button class="ct-action-button" ng-click="iframeScope.createDesignSet()"><?php echo "Submit"; ?></button>
		</div>

		<div ng-if="dialogForms['stylesheet']">
			<div id="ct-dialog-stylesheet-form" class="ct-dialog-stylesheet-form">
				Design Set ID <br/>
				<input type="text" class="ct-textbox" ng-model="iframeScope.componentizeOptions.designSetId"><br/>
			</div>
			<br/>
			<button class="ct-action-button" ng-click="iframeScope.postStyleSheet()"><?php echo "Submit"; ?></button>
		</div>

	<?php }


	/**
	 * Output button list single button
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function button_list_button($option, $value, $value_nice_name = false, $class = "") { ?>

		<label class='oxygen-button-list-button <?php echo $class; ?>'
			ng-class="{'oxygen-button-list-button-active':iframeScope.getOption('<?php echo esc_attr( $option ); ?>')=='<?php echo $value; ?>','oxygen-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'<?php echo esc_attr( $option ); ?>','<?php echo $value; ?>')==true}">
				<input type="radio" name="<?php echo esc_attr( $option ); ?>" value="<?php echo $value; ?>"
					<?php $this->ng_attributes($option, 'model,change'); ?>
					ng-click="radioButtonClick(iframeScope.component.active.name, '<?php echo esc_attr( $option ); ?>', '<?php echo $value; ?>')"/>
				<?php echo ( $value_nice_name ) ? $value_nice_name : $value; ?>
		</label>
	<?php }


	/**
	 * Output icon button list single button
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function icon_button_list_button($option, $value, $icon, $icon_active, $label = false, $ng_click = "") { ?>

		<label class='oxygen-icon-button-list-option'
			ng-class="{'oxygen-icon-button-list-option-active':iframeScope.getOption('<?php echo esc_attr( $option ); ?>')=='<?php echo $value; ?>','oxygen-icon-button-list-button-default':iframeScope.isInherited(iframeScope.component.active.id,'<?php echo esc_attr( $option ); ?>','<?php echo $value; ?>')==true}">
				<div class="oxygen-icon-button-list-option-icon-wrapper">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/<?php echo $icon; ?>' />
					<input type="radio" name="<?php echo esc_attr( $option ); ?>" value="<?php echo $value; ?>"
						<?php $this->ng_attributes($option, 'model,change'); ?>
						ng-click="radioButtonClick(iframeScope.component.active.name, '<?php echo esc_attr( $option ); ?>', '<?php echo $value; ?>');<?php echo $ng_click; ?>"/>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/<?php echo $icon_active; ?>' />
				</div>
				<?php if ( $label ) : ?>
				<div class='oxygen-icon-button-list-option-label'>
					<?php echo $label; ?>
				</div>
				<?php endif; ?>
		</label>

	<?php }


	/**
	 * Output measure box with label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function measure_box_with_wrapper($option,$label,$units="") { ?>

		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<?php self::measure_box($option,$units); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output measure box
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function measure_box($option,$units="",$with_options = false, $default = true, $attributes = false) { 

		if ($default) {
			$default_class = ",'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '".esc_attr( $option )."')";
		}
		else {
			$default_class = "";
		}

		?>

		<div class='oxygen-measure-box'
			ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'<?php echo $default_class; ?>}">
			<input type="text" spellcheck="false"
				data-option="<?php echo esc_attr( $option ); ?>"
				<?php if ($attributes) $this->ng_attributes($option,$attributes); else $this->ng_attributes($option);?>/>
			<div class='oxygen-measure-box-unit-selector'>
				<?php if (strpos($units, ",")===false&&strlen($units)>0) : ?>
					<div class='oxygen-measure-box-selected-unit'><?php echo $units; ?></div>
				<?php else: ?>
					<div class='oxygen-measure-box-selected-unit'>{{iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')}}</div>
					<?php self::measure_type_select($option,$units); ?>
				<?php endif; ?>
			</div>
			<?php if ($with_options) : ?>
				<?php //$this->measure_box_options($option,$units); ?>
			<?php endif; ?>
		</div>

	<?php }


	/**
	 * Output slider measure box with label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function slider_measure_box_with_wrapper($option,$label,$units="",$min=0,$max=100,$default=true,$step=false) { ?>

		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<?php self::slider_measure_box($option,$units,$min,$max,$default,$step); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output measure box with slider
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function slider_measure_box($option,$units="",$min=0,$max=100,$default=true,$step=false) { 

		if ($default) {
			$default_class = ",'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '".esc_attr( $option )."')";
		}
		else {
			$default_class = "";
		}

		?>

		<div class="oxygen-slider-measure-box"
			ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('<?php echo esc_attr( $option ); ?>')=='auto'<?php echo $default_class; ?>}">
			<?php self::measure_box($option, $units, false, $default); ?>
			<div class="oxygen-measure-box-slider">
				<input type="range" 
					min="<?php echo ($min!==null&&$min!=='') ? $min : 0; ?>" 
					max="<?php echo ($max!==null&&$max!=='') ? $max : 100; ?>" 
					<?php echo ($step!==null&&$step!=='') ?  "step=\"$step\"": ""; ?>" 
					<?php $this->ng_attributes($option); ?>>
			</div>
		</div>

	<?php }


	/**
	 * Output font family dropdown
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function font_family_dropdown($option = false, $hide_wrapper = false) { 

		if (!$option) {
			$option = 'font-family';
		}; 

		?>

		<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
			<label class='oxygen-control-label'><?php _e("Font Family","oxygen"); ?></label>
			<div class='oxygen-control'>

				<div class="oxygen-select oxygen-select-box-wrapper">
					<div class="oxygen-select-box"
						ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $option; ?>')}">
						<div class="oxygen-select-box-current">{{iframeScope.getComponentFont(iframeScope.component.active.id, true, '', '<?php echo $option; ?>')}}</div>
						<div class="oxygen-select-box-dropdown"></div>
					</div>
					<div class="oxygen-select-box-options">

						<div class="oxygen-select-box-option">
							<input type="text" value="" placeholder="<?php _e("Search...", "oxygen"); ?>" spellcheck="false"
								ng-model="iframeScope.fontsFilter"/>
						</div>
						<div class="oxygen-select-box-option"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, '', '<?php echo $option; ?>');"
							title="<?php _e("Unset font", "oxygen"); ?>">
								<?php _e("Default", "oxygen"); ?>
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="(name,font) in iframeScope.globalSettings.fonts | filter:{font:iframeScope.fontsFilter}"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, ['global', name], '<?php echo $option; ?>');"
							title="<?php _e("Apply global font", "oxygen"); ?>">
								{{name}} ({{font}})
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="name in ['Inherit'] | filter:iframeScope.fontsFilter"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, name, '<?php echo $option; ?>');"
							title="<?php _e("Use parent element font", "oxygen"); ?>">
								Inherit
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="name in iframeScope.elegantCustomFonts | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, name, '<?php echo $option; ?>');"
							title="<?php _e("Apply this font family", "oxygen"); ?>">
								{{name}}
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="font in iframeScope.typeKitFonts | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font.slug, '<?php echo $option; ?>');"
							title="<?php _e("Apply this font family", "oxygen"); ?>">
								{{font.name}}
						</div>
						<div class="oxygen-select-box-option"
							ng-repeat="font in iframeScope.fontsList | filter:iframeScope.fontsFilter | limitTo: 20"
							ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font, '<?php echo $option; ?>');"
							title="<?php _e("Apply this font family", "oxygen"); ?>">
								{{font}}
						</div>

					</div>
					<!-- .oxygen-select-box-options -->
				</div>
				<!-- .oxygen-select.oxygen-select-box-wrapper -->
			</div>
		</div>
		<!-- #oxygen-typography-font-family -->

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function simple_input_with_wrapper($option,$label) { ?>

		<div class='oxygen-control-wrapper'>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<div class='oxygen-input'>
					<input type="text" spellcheck="false"
						<?php $this->ng_attributes($option); ?>/>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function colorpicker_with_wrapper($option,$label,$id="",$html_attr="") { ?>

		<div class='oxygen-control-wrapper' id='<?php echo $id; ?>' <?php echo $html_attr; ?>>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<?php self::colorpicker($option); ?>
			</div>
		</div>

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function colorpicker($option) { ?>

		<div class='oxygen-color-picker'
			ng-class="{'oxygen-option-default':$parent.iframeScope.isInherited($parent.iframeScope.component.active.id, '<?php echo esc_attr( $option ); ?>')}">
			<div class="oxygen-color-picker-color">
				<input ctiriscolorpicker=""
					class="ct-iris-colorpicker"
					 type="text" spellcheck="false"
					 <?php $this->ng_attributes($option, 'change'); ?>
					ng-model="$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['<?php echo esc_attr( $option ); ?>']"
					ng-style="{'background-color':$parent.iframeScope.getOption('<?php echo esc_attr( $option ); ?>')}"/>
			</div>
			<input type="text" spellcheck="false"
				<?php $this->ng_attributes($option, 'change'); ?>
				ng-model="$parent.iframeScope.component.options[$parent.iframeScope.component.active.id]['model']['<?php echo esc_attr( $option ); ?>']"
				/>
		</div>

	<?php }


	/**
	 * Output mediaurl with wrapper and label
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function mediaurl_with_wrapper($option,$label,$id="") { ?>

		<div class='oxygen-control-wrapper' id='<?php echo $id; ?>'>
			<label class='oxygen-control-label'><?php echo $label; ?></label>
			<div class='oxygen-control'>
				<?php self::mediaurl($option); ?>
			</div>
		</div>

	<?php }

	function hyperlink($option, $param = array()) {
		?>
		<div class="oxygen-file-input"
			ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo esc_attr( $option ); ?>')}">
			<input type="text" spellcheck="false"
				<?php $this->ng_attributes($option); ?>/>
			<div class="oxygen-set-link"
				data-linkproperty="<?php echo esc_attr( $option ); ?>" 
				data-linktarget="target"
				ng-click="processLink()"><?php _e("set","oxygen"); ?></div>
			<?php if(isset($param['dynamicdatacode'])) {
					echo $param['dynamicdatacode'];
			} ?>
		</div>
		<?php
	}


	/**
	 * Media queries list
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function media_queries_list_with_wrapper($option,$heading,$above=false) { ?>

		<div class="oxygen-control-row">
			<div class='oxygen-control-wrapper'>
				<label class='oxygen-control-label'><?php echo $heading; ?></label>
				<div class='oxygen-control oxygen-special-property not-available-for-media not-available-for-classes'>
					<?php self::media_queries_list($option,$heading,$above=false) ?>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Media queries list
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function media_queries_list($option,$heading="",$above=false) { ?>

		<div class="oxygen-select oxygen-select-box-wrapper">
			<div class="oxygen-select-box"
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo $option; ?>')}">
				<div class="oxygen-select-box-current">{{iframeScope.getMediaTitle(iframeScope.getOption('<?php echo $option; ?>')<?php echo ($above) ? ", true" : ""?>)}}</div>
				<div class="oxygen-select-box-dropdown"></div>
			</div>
			<div class="oxygen-select-box-options">
				<div class="oxygen-select-box-option" 
					ng-repeat="name in iframeScope.<?php echo ($above) ? "sortedMediaList(true)" : "sortedMediaList()" ?>"
					ng-if="name!='default'"
					ng-click="iframeScope.setOptionModel('<?php echo $option; ?>',name)"
					ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('<?php echo $option; ?>')==name}">
					{{iframeScope.getMediaTitle(name<?php echo ($above) ? ", true" : ""?>)}}
				</div>
				<div class="oxygen-select-box-option" 
					ng-click="iframeScope.setOptionModel('<?php echo $option; ?>','never')"
					ng-class="{'oxygen-select-box-option-active':iframeScope.getOption('<?php echo $option; ?>')=='never'}">
					<?php _e("Never","oxygen"); ?>
				</div>
			</div>
		</div>

	<?php }


	/**
	 * Output simple input textbox with wrapper and label
	 *
	 * @since 2.0
	 */

	function mediaurl($option) { 
		global $oxygen_meta_keys;
		?>

		<div class="oxygen-file-input">
			<input type="text" spellcheck="false"
				ng-change = "iframeScope.setOption(iframeScope.component.active.id,'ct_image','<?php echo esc_attr( $option ); ?>'); iframeScope.parseImageShortcode()"
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, '<?php echo esc_attr( $option ); ?>')}"
				<?php $this->ng_attributes($option); ?>/>
			<div class="oxygen-file-input-browse"
				data-mediaTitle="Select Image" 
				data-mediaButton="Select Image" 
				data-mediaProperty="<?php echo esc_attr( $option ); ?>"
				data-mediaType="mediaUrl"><?php _e("browse","oxygen"); ?></div>
			<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesImageMode" callback="iframeScope.insertShortcodeToImage">data</div>
		</div>

	<?php }


	/**
	 * List predifened data components
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function data_folder() { ?>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addDynamicContent('ct_headline', '[oxygen data=\'title\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Title","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'content\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Content","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'date\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Date","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'terms\' taxonomy=\'category\' separator=\', \']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Categories","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'terms\' taxonomy=\'post_tag\' separator=\', \']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Tags","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addComponent('ct_image');iframeScope.insertShortcodeToImage('[oxygen data=\'featured_image\']')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Featured Image","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addDynamicContent('ct_text_block', '[oxygen data=\'author\']');">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Author","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addComponent('ct_image');iframeScope.insertShortcodeToImage('[oxygen data=\'author_pic\']')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Author Avatar","oxygen"); ?>
		</div>

		<div class="oxygen-add-section-element"
			ng-click="iframeScope.addCustomFieldComponent()">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata-active.svg' />			
			<?php _e("Custom field","oxygen"); ?>
		</div>

	<?php }


	/**
	 * List predifened data components
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	function tiny_mce() { ?>
	
		<div class="oxygen-tinymce-dialog-wrap" ng-show="tinyMCEWindow">
			<div class="oxygen-data-dialog">
				<h1><?php _e("Edit text", "oxygen"); ?></h1>
				<?php wp_editor("", "oxygen_vsb_tinymce", $settings = array(
					"media_buttons" => false,
					"editor_height" => 350
					)); ?>
				<br/>
				<span class="oxygen-apply-button" 
					ng-click="closeTinyMCEDialog()"><?php _e("Save & Close", "oxygen"); ?></span>
			</div>
			<div class="oxygen-data-dialog-bg"
				ng-show="tinyMCEWindow"
				ng-click="closeTinyMCEDialog()"></div>
		</div>
	
	<?php }

}

// Create toolbar instance
if ( defined("SHOW_CT_BUILDER") ) {
	global $oxygen_toolbar;
	$oxygen_toolbar = new CT_Toolbar();
}
