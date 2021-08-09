<div id="oxygen-ui"
	ng-class="{'oxygen-editing-media':iframeScope.isEditing('media'), 'oxygen-editing-class':iframeScope.isEditing('class'), 'oxygen-editing-state':iframeScope.isEditing('state'), 'oxygen-editing-special':iframeScope.isEditing('media')||iframeScope.isEditing('class')||iframeScope.isEditing('state'), 'oxygen-content-editing':actionTabs['contentEditing']}" >
	
	<div id="oxygen-topbar" class="oxygen-toolbar">

		<div class="oxygen-add-button oxygen-toolbar-button"
			ng-click="switchActionTab('componentBrowser')"
			ng-dblclick="switchTab('components', 'fundamentals')">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/add.svg">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/add--hover.svg">
			<span><?php _e("Add", "oxygen"); ?></span>
		</div>

		<div class='oxygen-toolbar-panels'>

			<div class='oxygen-toolbar-panel'>
				<?php ct_template_builder_settings() ?>
			</div>

			<div class="oxygen-toolbar-panel oxygen-formatting-toolbar-panel">
				<?php require_once "views/editor-panel.view.php"; ?>
			</div>
			
			<div class='oxygen-toolbar-panel'>
				<div class='oxygen-zoom-control'
					ng-show="viewportScale<1||viewportScaleLocked">
					<label><?php _e("Zoom", "oxygen"); ?></label>
					<span class='oxygen-zoom-control-zoom-amount'>{{viewportScale * 100 | number : 0}}%</span>
					<span class='oxygen-zoom-icon'
						ng-class="{'oxygen-zoom-icon-active':viewportScaleLocked}"
						ng-click="lockViewportScale()">
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/zoom-lock.svg' />
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/zoom-lock--active.svg' />
					</span>
				</div>
			</div>

		</div>
		<!-- .oxygen-toolbar-panels -->

		<div class="oxygen-dom-tree-button oxygen-toolbar-button"
			ng-click="switchTab('sidePanel','DOMTree')">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/dom-tree.svg" />
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/dom-tree--hover.svg" />
			<span><?php _e("Structure", "oxygen"); ?></span>
		</div>

		<div class="oxygen-toolbar-menus">

			<div class="oxygen-manage-menu oxygen-toolbar-button oxygen-select">
				<div class="oxygen-toolbar-button-dropdown">
					<div class="oxygen-toolbar-button-dropdown-option"
						ng-click="toggleSettingsPanel()">
							<?php _e("Settings","oxygen");?></div>
					<div class="oxygen-toolbar-button-dropdown-option"
						ng-click="switchTab('sidePanel','styleSheets');">
							<?php _e("Stylesheets","oxygen");?></div>
					<div class="oxygen-toolbar-button-dropdown-option"
						ng-click="switchTab('sidePanel','selectors');">
							<?php _e("Selectors","oxygen");?></div>
				</div>
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/manage.svg" />
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/manage--hover.svg" />
				<span><?php _e("Manage", "oxygen"); ?></span>
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg">
			</div>

			<?php
			/* Load the admin bar class code ready for instantiation */
			require_once( ABSPATH . WPINC . '/class-wp-admin-bar.php' );
			$admin_bar_class = apply_filters( 'wp_admin_bar_class', 'WP_Admin_Bar' );
			if ( class_exists( $admin_bar_class ) ) {
				$admin_bar = new $admin_bar_class;
				wp_admin_bar_edit_menu($admin_bar);
				$admin_url = $admin_bar->get_node('edit')->href;
			}
			else {
				$admin_url = admin_url();
			}
			?>

			<div class="oxygen-back-to-wp-menu oxygen-toolbar-button oxygen-select">
				<div class="oxygen-toolbar-button-dropdown">
					<a class="oxygen-toolbar-button-dropdown-option"
						ng-href="<?php echo esc_url( $admin_url );?>">
						<?php _e("Admin","oxygen");?>
					</a>
					<a class="oxygen-toolbar-button-dropdown-option"
						ng-hide="iframeScope.ajaxVar.ctTemplate"
						ng-href="{{iframeScope.ajaxVar.frontendURL}}">
						<?php _e("Frontend","oxygen");?>
					</a>
					<a class="oxygen-toolbar-button-dropdown-option"
						ng-show="iframeScope.ajaxVar.ctTemplate"
						ng-href="{{iframeScope.template.postData.frontendURL}}">
						<?php _e("Frontend","oxygen");?>
					</a>
				</div>
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/back-to-wp.svg" />
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/back-to-wp--hover.svg" />
				<span><?php _e("Back to WP", "oxygen"); ?></span>
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/dropdown-arrow.svg">
			</div>

		</div>

		<div class="oxygen-save-button oxygen-toolbar-button"
			ng-click="iframeScope.savePage()">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/save.svg">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/save--hover.svg">
			<span><?php _e("Save", "oxygen"); ?></span>
		</div>
		
	</div><!-- #oxygen-topbar -->

	<div class='oxygen-add-section-library-flyout-panel'>
		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, designSet) in iframeScope.experimental_components track by key"
			id='category-designset-{{key}}-pages'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, page) in designSet.pages track by index"
				ng-click="iframeScope.showAddItemDialog(page.id, 'page', '0', '', page.source, '', page.name, '', key)">
				<img ng-src='{{page.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'>
					{{page.name}}
				</div>
			</div>
		</div>
		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, designSet) in iframeScope.experimental_components track by key"
			id='category-designset-{{key}}-templates'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, page) in designSet.templates track by index"
				ng-click="iframeScope.showAddItemDialog(page.id, 'template', '0', '', page.source, '', page.name, '', key)">
				<img ng-src='{{page.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'>
					{{page.name}}
				</div>
			</div>
		</div>

		<div ng-repeat="(key, designSet) in iframeScope.experimental_components track by key">
			
			<div class='oxygen-add-section-library-flyout-category'
				ng-repeat="(catKey, category) in designSet.items track by category.slug"
				id='category-category-{{key}}-{{category.slug}}'>

				<div class='oxygen-add-section-library-addable'
					ng-repeat="(index, component) in category.contents track by index"
					ng-click="iframeScope.showAddItemDialog(component.id, 'component', '0', '', component.source, component.page, component.name, catKey, key)">
					<img ng-src='{{component.screenshot_url}}' />
					<div class='oxygen-add-section-library-addable-details'>
						{{component.name}}
					</div>
				</div>
			</div>
		</div>
		

		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, components) in iframeScope.libraryCategories track by components.slug"
			id='category-category-{{components.slug}}'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, component) in components.contents track by index"
				ng-click="iframeScope.showAddItemDialog(component.id, 'component', '0', '', component.source, component.page, component.name, key)">
				<img ng-src='{{component.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'>
					{{component.name}}
				</div>
			</div>
		</div>

		<div class='oxygen-add-section-library-flyout-category'
			ng-repeat="(key, pages) in iframeScope.libraryPages track by pages.slug"
			id='category-page-{{pages.slug}}'>

			<div class='oxygen-add-section-library-addable'
				ng-repeat="(index, page) in pages.contents track by index"
				ng-click="iframeScope.showAddItemDialog(page.id, 'page', '0', '', page.source, '', page.name, key)">
				<img ng-src='{{page.screenshot_url}}' />
				<div class='oxygen-add-section-library-addable-details'>
					{{page.name}}
				</div>
			</div>
		</div>

	</div><!-- .oxygen-add-section-library-flyout-panel -->

	<div id="oxygen-sidebar" 
		ng-init="showLinkDataDialog = false"
		ng-class="{'oxygen-selector-detector-mode':iframeScope.selectorDetector.mode==true}">

		<div class='oxygen-editing-stylesheet-message' ng-if="iframeScope.selectedNodeType==='stylesheet'">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Style Sheet", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.stylesheetToEdit['name']}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						title="<?php _e("Remove Stylesheet", "oxygen"); ?>"
						ng-click="iframeScope.deleteStyleSheet(iframeScope.stylesheetToEdit, $event)"/>
				</div>

				
			</div>
		</div>

		<div class='oxygen-editing-folder-message' 
			ng-show="iframeScope.selectedNodeType==='selectorfolder'">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Selector Folder", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.selectedSelectorFolder && iframeScope.selectedSelectorFolder!==-1?iframeScope.selectedSelectorFolder:'Uncategorized'}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						ng-if="iframeScope.selectedSelectorFolder && iframeScope.selectedSelectorFolder!==-1"
						title="<?php _e("Remove Folder", "oxygen"); ?>"
						ng-click="iframeScope.deleteSelectorFolder(iframeScope.selectedSelectorFolder,$event); iframeScope.selectorFolderMenuOpen = false"/>
				</div>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-click="iframeScope.styleFolders[iframeScope.selectedSelectorFolder].status = (iframeScope.styleFolders[iframeScope.selectedSelectorFolder].status == 1 ? 0 : 1); iframeScope.selectorFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()">
					{{iframeScope.styleFolders[iframeScope.selectedSelectorFolder].status?'<?php _e("Disable Folder", "oxygen"); ?>':'<?php _e("Enable Folder", "oxygen"); ?>'}}</a>

				
			</div>
		</div>

		<div class='oxygen-editing-folder-message' 
			ng-show="iframeScope.selectedNodeType==='cssfolder'">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Stylesheet Folder", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.selectedCSSFolder && iframeScope.selectedCSSFolder!==-1?iframeScope.getCSSFolder(iframeScope.selectedCSSFolder)['name']:'Uncategorized'}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						ng-if="iframeScope.selectedCSSFolder && iframeScope.selectedCSSFolder!==-1"
						title="<?php _e("Remove Folder", "oxygen"); ?>"
						ng-click="iframeScope.deleteStyleSheet(iframeScope.getCSSFolder(iframeScope.selectedCSSFolder),$event); iframeScope.selectorFolderMenuOpen = false"/>
				</div>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-if="!iframeScope.selectedCSSFolder || iframeScope.selectedCSSFolder===-1"
					ng-click="iframeScope.toggleUncategorizedStyleSheets(iframeScope.selectedCSSFolder !== -1); iframeScope.cssFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()">
					{{iframeScope.selectedCSSFolder !== -1?'<?php _e("Disable Folder", "oxygen"); ?>':'<?php _e("Enable Folder", "oxygen"); ?>'}}</a>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-if="iframeScope.selectedCSSFolder && iframeScope.selectedCSSFolder!==-1"
					ng-click="iframeScope.getCSSFolder(iframeScope.selectedCSSFolder).status = (iframeScope.getCSSFolder(iframeScope.selectedCSSFolder).status == 1 ? 0 : 1); iframeScope.cssFolderMenuOpen = false; iframeScope.classesCached = false; iframeScope.outputCSSOptions()">
					{{iframeScope.getCSSFolder(iframeScope.selectedCSSFolder).status === 1?'<?php _e("Disable Folder", "oxygen"); ?>':'<?php _e("Enable Folder", "oxygen"); ?>'}}</a>

				
			</div>
		</div>

		<div class='oxygen-editing-styleset-message' 
			ng-show="iframeScope.selectedNodeType==='styleset'">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("Style Set", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.selectedStyleSet}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						title="<?php _e("Remove Component", "oxygen"); ?>"
						ng-show="iframeScope.selectedStyleSet!=='Uncategorized Custom Selectors'"
						ng-click="$parent.deleteStyleSet(iframeScope.selectedStyleSet)"/>
				</div>

				
			</div>
		</div>

		<div class='oxygen-editing-reusable-message' 
			ng-show="isActiveName('ct_reusable') && !isActiveActionTab('componentBrowser')">

			<div class="oxygen-sidebar-template">
				<h2><?php _e("REUSABLE PART", "oxygen"); ?></h2>
				
				<div class="oxygen-reusable-title">
					<h1>{{iframeScope.component.options[iframeScope.component.active.id]['nicename']}}</h1>
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
						title="<?php _e("Remove Component", "oxygen"); ?>"
						ng-show="iframeScope.component.active.id > 0 && !isActiveName('oxy_header_left') && !isActiveName('oxy_header_center') && !isActiveName('oxy_header_right')"
						ng-click="iframeScope.removeActiveComponent()"/>
				</div>

				<div class='oxygen-active-element-breadcrumb'
					ng-if="!iframeScope.isEditing('custom-selector')">
					<span ng-repeat='item in iframeScope.selectAncestors'>
						<span ng-if="item.id > 0 && item.id < 100000" ng-click="iframeScope.activateComponent(item.id, item.tag)">{{item.name}}</span>
						<span ng-if="item.id > 0 && item.id < 100000" class="oxygen-active-element-breadcrumb-arrow">&gt;</span>
						<span ng-if="item.id == 0" class='oxygen-active-element-breadcrumb-active'>{{item.name}}</span>
					</span>
				</div>

				<a href="#" class="oxygen-sidebar-template-button"
					ng-href="{{iframeScope.reusableEditLinks[iframeScope.component.active.id].replace('&amp;', '&')}}">
					<?php _e("Open &amp; Edit Reusable Part", "oxygen"); ?></a>
			</div>
		</div><!-- .oxygen-editing-reusable-message -->

		<div class='oxygen-editing-template-message' 
			ng-if="isActiveName('ct_template') && !isActiveActionTab('componentBrowser')">

			<div class="oxygen-sidebar-template">

				<h2><?php _e("TEMPLATE", "oxygen"); ?></h2>
				<h1>{{iframeScope.outerTemplateData['template_name']}}</h1>

				<a href="#" ng-href="{{iframeScope.outerTemplateData['edit_link']}}" class="oxygen-sidebar-template-button"><?php _e("Open &amp; Edit Template", "oxygen"); ?></a>

			</div>
		</div>

		<div class='oxygen-editing-template-message' 
			ng-if="isActiveName('ct_inner_content') && !isActiveActionTab('componentBrowser')">

				<div class="oxygen-sidebar-template">
					<div class="oxygen-reusable-title">
						<h2><?php _e("INNER CONTENT", "oxygen"); ?></h2>
						<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/currently-editing/delete.svg'
							title="<?php _e("Remove Component", "oxygen"); ?>"
							ng-show="iframeScope.component.active.id > 0 && !isActiveName('oxy_header_left') && !isActiveName('oxy_header_center') && !isActiveName('oxy_header_right')"
							ng-click="iframeScope.removeActiveComponent()"/>
					</div>
					
					<h1>{{template.postData.post_title}}</h1>

					<div class='oxygen-active-element-breadcrumb'
						ng-if="!iframeScope.isEditing('custom-selector')">
						<span ng-repeat='item in iframeScope.selectAncestors'>
							<span ng-if="item.id > 0 && item.id < 100000" ng-click="iframeScope.activateComponent(item.id, item.tag)">{{item.name}}</span>
							<span ng-if="item.id > 0 && item.id < 100000" class="oxygen-active-element-breadcrumb-arrow">&gt;</span>
							<span ng-if="item.id == 0" class='oxygen-active-element-breadcrumb-active'>{{item.name}}</span>
						</span>
					</div>

					<a href="#" ng-if="iframeScope.template.postData.edit_link" ng-href="{{iframeScope.template.postData.edit_link}}" class="oxygen-sidebar-template-button"><?php _e("Open &amp; Edit Inner Content", "oxygen"); ?></a>

				</div>
		</div>

		<div class="oxygen-sidebar-top">

			<div class='oxygen-sidebar-currently-editing'
				ng-show="!iframeScope.styleSetActive 
						&& iframeScope.selectedNodeType!=='selectorfolder'
						&& iframeScope.selectedNodeType!=='cssfolder'
						&& iframeScope.component.active.name 
						&& iframeScope.component.active.name!='root' 
						&& iframeScope.component.active.name!='ct_inner_content' 
						&& !iframeScope.isEditing('style-sheet') 
						&& !isActiveName('ct_reusable') 
						&& !isActiveName('ct_template') 
						&& !isActiveActionTab('componentBrowser')">
				<!-- !iframeScope.selectedNodeType || iframeScope.selectedNodeType==='selector' || iframeScope.selectedNodeType==='class'  -->
				<?php do_action("ct_toolbar_component_header"); ?>

			</div>

			<div class='oxygen-sidebar-currently-editing oxygen-sidebar-currently-editing-top'
				ng-if="iframeScope.selectedNodeType==='styleset' || iframeScope.selectedNodeType==='class'">
				<div class="oxygen-control-row">
					<div class="oxygen-control-wrapper">
						<label class="oxygen-control-label">Folder</label>

						<div class="oxygen-control">
							<div class='oxygen-select oxygen-select-box-wrapper oxygen-style-set-dropdown'>
								<div class='oxygen-select-box' id="oxygen-selector-folder-dropdown">
									<div class="oxygen-select-box-current">{{iframeScope.currentActiveFolder !== '-1' && iframeScope.currentActiveFolder !== -1 ? iframeScope.currentActiveFolder : ''}}</div>
									<div class="oxygen-select-box-dropdown"></div>
								</div>
								
								<div class="oxygen-select-box-options">
									<div class="oxygen-select-box-option" ng-click="iframeScope.setCurrentSelectorFolder('');">
											<span>
												<?php _e('None', 'oxygen');?>
											</span>
									</div>

									<div class="oxygen-select-box-option" ng-repeat="(folderName, folder) in iframeScope.styleFolders track by folderName"
										ng-click="iframeScope.setCurrentSelectorFolder(folderName);">
											
											<span>
												{{folderName}}
											</span>
									</div>
								</div>
							</div>
						</div>

					   <!-- <span ng-if="iframeScope.selectedStyleSet && !iframeScope.isEditing('class')"> for Style Set {{iframeScope.selectedStyleSet}}</span>
					    <span ng-if="iframeScope.isEditing('class')"> for class {{iframeScope.currentClass}}</span>-->
					</div>
			    </div>
			</div>


			<div class='oxygen-sidebar-currently-editing oxygen-sidebar-currently-editing-top'
				ng-if="iframeScope.selectedNodeType==='stylesheet'">
				<div class="oxygen-control-row">
				  <div class="oxygen-control-wrapper">
				    <label class="oxygen-control-label">Folder</label>
				    <div class="oxygen-control">
					    <div class='oxygen-select oxygen-select-box-wrapper oxygen-style-set-dropdown'>
					      <div class='oxygen-select-box' id="oxygen-selector-folder-dropdown">
					        <div class="oxygen-select-box-current">{{iframeScope.currentActiveStylesheetFolder}}</div>
					        <div class="oxygen-select-box-dropdown"></div>
					      </div>
					      <div class="oxygen-select-box-options">
					        <div class="oxygen-select-box-option" ng-click="iframeScope.setCurrentStylesheetFolder(0);">
					            
					            <span>
					              None
					            </span>
					           
					        </div>
					        <div class="oxygen-select-box-option" ng-repeat="folder in iframeScope.styleSheets | filter : { folder : 1 } track by folder.id"
					          ng-click="iframeScope.setCurrentStylesheetFolder(folder.id);">
					            
					            <span>
					              {{folder.name}}
					            </span>
					           
					        </div>
					      </div>
					    </div>
					</div>
				  </div>
				</div>

			</div>

			<div ng-if="iframeScope.selectedNodeType==='stylesheet' && iframeScope.isEditing('style-sheet')">
				<?php require_once "views/style-sheet.view.php" ;?>
			</div>
			
		</div>
		

		<div class="oxygen-sidebar-top" 
			ng-show="!iframeScope.styleSetActive 
					&& iframeScope.selectedNodeType!=='selectorfolder'
					&& iframeScope.selectedNodeType!=='cssfolder'
					&& iframeScope.component.active.name 
					&& iframeScope.component.active.name!='root' 
					&& iframeScope.component.active.name!='ct_inner_content' 
					&& !iframeScope.isEditing('style-sheet') 
					&& !isActiveName('ct_reusable') 
					&& !isActiveName('ct_template') 
					&& !isActiveActionTab('componentBrowser')">
			<?php 
			$tabs = "";
			foreach ($this->component_with_tabs as $key => $tab) {
				$tabs .= ",'$tab'"; 
			} 
			?>
			<div class='oxygen-sidebar-tabs'>
				<div class='oxygen-sidebar-tabs-tab'
					ng-click="styleTabAdvance=false;closeTabs(['easyPosts','slider','navMenu','effects','gallery'<?php echo $tabs; ?>]);toggleSidebar(true)" 
					ng-class="{'oxygen-sidebar-tabs-tab-active':!styleTabAdvance}"><?php _e("Primary", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-tabs-tab'
					ng-click="showAllStylesFunc(); styleTabAdvance=true" 
					ng-class="{'oxygen-sidebar-tabs-tab-active':styleTabAdvance,'oxy-styles-present':iframeScope.isTabHasOptions()}"><?php _e("Advanced", "oxygen"); ?>
				</div>
			</div>
			<!-- .oxygen-sidebar-tabs -->
			
		</div>


		<!-- .oxygen-sidebar-top -->

			<div class='oxygen-sidebar-breadcrumb'
				ng-class="{'oxygen-sidebar-breadcrumb-fill': isShowTab('advanced','background-gradient')}"
				ng-show="!iframeScope.styleSetActive && showAllStyles==false && (styleTabAdvance==true||isActiveName('ct_inner_content')) && !hasOpenTabs('effects')">
				<div class='oxygen-sidebar-breadcrumb-icon'
					ng-click="showAllStylesFunc();">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg' />
				</div>
				<div class='oxygen-sidebar-breadcrumb-all-styles'
					ng-click="showAllStylesFunc();"><?php _e("All Styles", "oxygen"); ?></div>
				<?php foreach ( $this->options['advanced'] as $key => $tab ) : ?>
					<div class='oxygen-sidebar-breadcrumb-separator' 
						ng-if="isShowTab('advanced','<?php echo $key; ?>')">/</div>
					<div class='oxygen-sidebar-breadcrumb-current' 
						ng-if="isShowTab('advanced','<?php echo $key; ?>')"><?php echo $tab['heading']; ?></div>
				<?php endforeach; ?>

				<!-- Exception for Background Gradient -->
				<div class='oxygen-sidebar-breadcrumb-separator' 
					ng-show="isShowTab('advanced','background-gradient')">/</div>

				<div class='oxygen-sidebar-breadcrumb-all-styles'
					ng-show="isShowTab('advanced','background-gradient')"
					ng-click="switchTab('advanced', 'background');"><?php _e("Background", "oxygen"); ?></div>

				<div class='oxygen-sidebar-breadcrumb-separator' 
					ng-show="isShowTab('advanced','background-gradient')">/</div>

				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-show="isShowTab('advanced','background-gradient')"><?php _e("Gradient", "oxygen"); ?></div>
				<!-- Exception for Background Gradient ENDS -->
			</div>
			<!-- .oxygen-sidebar-breadcrumb -->

		<?php 
		$tabs = "";
		foreach ($this->component_with_tabs as $key => $tab) {
			$tabs .= "||hasOpenTabs('$tab')"; 
		} 
		?>
		<div class="oxygen-sidebar-control-panel oxygen-sidebar-control-panel-basic-styles" 
			ng-class="{'oxygen-widget-controls':isActiveName('ct_widget'),'oxygen-selector-detector-controls':iframeScope.selectorDetector.mode==true,'oxygen-basic-styles-subtub':hasOpenTabs('navMenu')||hasOpenTabs('slider')||hasOpenTabs('easyPosts')||hasOpenTabs('gallery')<?php echo $tabs; ?>}"

			ng-show="!iframeScope.styleSetActive 
				&& iframeScope.selectedNodeType!=='selectorfolder'
				&& iframeScope.selectedNodeType!=='cssfolder'
				&& iframeScope.component.active.name 
				&& iframeScope.component.active.id!=0 
				&& !iframeScope.isEditing('style-sheet') 
				&& !styleTabAdvance 
				&& !isActiveActionTab('componentBrowser') 
				&& !isActiveName('ct_reusable') 
				&& !isActiveName('ct_inner_content')">
			<?php do_action("ct_toolbar_component_settings"); ?>
			<div ng-show="showSidebarLoader" class="oxygen-sidebar-loader"><i class="fa fa-cog fa-4x fa-spin"></i></div>
		</div>
		<!-- .oxygen-sidebar-control-panel-basic-styles -->

		<div class='oxygen-sidebar-control-panel'
			ng-class="{'oxygen-sidebar-advanced-home':showAllStyles}"
			ng-show="!iframeScope.styleSetActive 
					&& iframeScope.selectedNodeType!=='selectorfolder'
					&& iframeScope.selectedNodeType!=='cssfolder'
					&& (iframeScope.component.active.name 
					&& iframeScope.component.active.name!='root' 
					&& !iframeScope.isEditing('style-sheet') 
					&& (styleTabAdvance||iframeScope.component.active.name=='ct_inner_content') 
					&& !isActiveActionTab('componentBrowser') 
					&& !isActiveName('ct_reusable') )">
		 		<?php do_action("ct_toolbar_advanced_settings"); ?>
		</div>
		<!-- .oxygen-sidebar-control-panel -->
			
		<div class="oxygen-no-item-message" 
			ng-hide="(iframeScope.component.active.name && iframeScope.component.active.id!=0) || iframeScope.isEditing('style-sheet') || isActiveActionTab('componentBrowser')">
				<?php _e("No Item Selected","oxygen"); ?>
		</div>			
		<!-- .oxygen-no-item-message -->

		<?php global $oxygen_meta_keys;?>
		<div id="oxygen-link-data-dialog-wrap" style="display: none;">
			<div id="oxygen-link-data-dialog-opener" class="oxygen-dynamic-data-browse" ng-mousedown = "showLinkDataDialog = !showLinkDataDialog" >Data</div>
			<div ng-show="showLinkDataDialog" id="oxygen-link-data-dialog" class="oxygen-data-dialog oxygen-data-dialog-link">
				<h1>Insert Dynamic Data</h1>
				<span class="oxygen-data-dialog-close" ng-mousedown="showLinkDataDialog=false">x</span>
				<div>
					<div class='oxygen-data-dialog-data-picker'>
						<h2>Post</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"permalink\"]")'>Permalink</li>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"comments_link\"]")'>Comments Link</li>
							<li ng-init="showOptionsPanel=false">
								<span ng-mousedown='showOptionsPanel = "postMeta"'>Meta / Custom Field</span>
								<div ng-show='showOptionsPanel === "postMeta"' class='oxygen-data-dialog-options'>
									<span class="oxygen-data-dialog-close" ng-mousedown="showOptionsPanel=false">x</span>
									<h3>Meta / Custom Field Options</h3>
									<div>
										<label>meta_key</label>
										<select ng-model='key'>
										<?php foreach($oxygen_meta_keys as $key) { ?>
											<option><?php echo $key ;?></option>
										<?php } ?>
										</select>
										<input type="text" ng-model='key' />
									</div>

									<button ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"meta\""+(key?(" key=\""+key+"\""):"")+"]")'>INSERT</button>
								</div>
							</li>
						</ul>
					</div>

					<div class='oxygen-data-dialog-data-picker'>
						<h2>Featured Image</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"featured_image\"]")'>Featured Image URL</li>
						</ul>
					</div>

					<div class='oxygen-data-dialog-data-picker'>
						<h2>Author</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"author_website_url\"]")'>Author Website URL</li>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"author_posts_url\"]")'>Author Posts URL</li>
							<li ng-init="showOptionsPanel=false">
								<span ng-mousedown='showOptionsPanel = "authorPostMeta"'>Meta / Custom Field</span>
								<div ng-show='showOptionsPanel === "authorPostMeta"' class='oxygen-data-dialog-options'>
								<span class="oxygen-data-dialog-close" ng-mousedown="showOptionsPanel=false">x</span>
									<h3>Author Meta / Custom Field Options</h3>
									<div>
										<label>meta_key</label>
										
										<input type="text" ng-model='key' />
									</div>

									<button ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"author_meta\""+(key?(" key=\""+key+"\""):"")+"]")'>INSERT</button>
								</div>
							</li>
						</ul>
					</div>

					<div class='oxygen-data-dialog-data-picker'>
						<h2>Current User</h2>
						<ul>
							<li ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"user_website_url\"]")'>User Website URL</li>
							<li ng-init="showOptionsPanel=false">
								<span ng-mousedown='showOptionsPanel = "userPostMeta"'>Meta / Custom Field</span>
								<div ng-show='showOptionsPanel === "userPostMeta"' class='oxygen-data-dialog-options'>
									<span class="oxygen-data-dialog-close" ng-mousedown="showOptionsPanel=false">x</span>
									<h3>User Meta / Custom Field Options</h3>
									<div>
										<label>meta_key</label>
										<input type="text" ng-model='key' />
									</div>

									<button ng-mousedown='showOptionsPanel = false; showLinkDataDialog = false; insertShortcodeToLink("[oxygen data=\"user_meta\""+(key?(" key=\""+key+"\""):"")+"]")'>INSERT</button>
								</div>
							</li>
						</ul>
					</div>

				</div>
			</div>
		
		</div>

		<div id="oxygen-add-sidebar" class="oxygen-add-sidebar" 
			ng-show="isActiveActionTab('componentBrowser')">
			<div class='oxygen-add-panels'>
				<?php do_action("ct_toolbar_components_list"); ?>
			</div>
		</div><!-- #oxygen-add-sidebar -->

		<div id="oxygen-global-settings" class="oxygen-global-settings" 
			ng-show="showSettingsPanel"
			ng-class="{'oxygen-show-settings-panel':showSettingsPanel,'oxygen-global-settings-all-settings':!hasOpenTabs('settings')}">

			<div class="oxygen-sidepanel-header-row">
				<?php _e("Settings","oxygen"); ?>
				<svg class="oxygen-close-icon"
					ng-click="toggleSettingsPanel()"><use xlink:href="#oxy-icon-cross"></use></svg>
			</div>

			<div class='oxygen-settings-breadcrumb'
				ng-show="hasOpenTabs('settings')">

				<div class='oxygen-sidebar-breadcrumb-icon'
					ng-click="tabs.settings=[]"
					ng-hide="hasOpenChildTabs('settings','default-styles')||hasOpenChildTabs('settings','links')">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg' />
				</div>
				<div class='oxygen-sidebar-breadcrumb-all-styles'
					ng-hide="hasOpenChildTabs('settings','default-styles')||hasOpenChildTabs('settings','links')"
					ng-click="tabs.settings=[]">
					<?php _e("All Settings", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-separator'
					ng-hide="hasOpenChildTabs('settings','default-styles')||hasOpenChildTabs('settings','links')">/</div>
				
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowTab('settings','page')">
					<?php _e("Page settings", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowTab('settings','fonts')">
					<?php _e("Fonts", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowTab('settings','editor')">
					<?php _e("Editor settings", "oxygen"); ?>
				</div>

				<div class='oxygen-sidebar-breadcrumb-icon'
					ng-click="switchTab('settings', 'default-styles');"
					ng-show="hasOpenChildTabs('settings','default-styles')">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg' />
				</div>
				<div
					ng-if="isShowTab('settings','default-styles')"
					ng-click="switchTab('settings', 'default-styles');"
					ng-class="{'oxygen-sidebar-breadcrumb-all-styles':hasOpenChildTabs('settings','default-styles'),'oxygen-sidebar-breadcrumb-current':!isShowChildTab('settings','default-styles','headings')&&!isShowChildTab('settings','default-styles','body-text')&&!isShowChildTab('settings','default-styles','links')&&!isShowChildTab('settings','default-styles','fonts')&&!isShowChildTab('settings','default-styles','page-width')&&!isShowChildTab('settings','default-styles','sections')}">
					<?php _e("Global Styles", "oxygen"); ?> 
				</div>
				
				<div class='oxygen-sidebar-breadcrumb-separator'
					ng-show="isShowChildTab('settings','default-styles','fonts')||isShowChildTab('settings','default-styles','headings')||isShowChildTab('settings','default-styles','body-text')||isShowChildTab('settings','default-styles','links')||isShowChildTab('settings','default-styles','page-width')||isShowChildTab('settings','default-styles','sections')">/</div>

				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowChildTab('settings','default-styles','fonts')">
					<?php _e("Fonts", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowChildTab('settings','default-styles','headings')">
					<?php _e("Headings", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowChildTab('settings','default-styles','body-text')">
					<?php _e("Body Text", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-icon'
					ng-click="switchTab('settings','links')"
					ng-if="hasOpenChildTabs('settings','links')">
					<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg' />
				</div>
				<div ng-if="isShowTab('settings','links')"
					ng-class="{'oxygen-sidebar-breadcrumb-all-styles':hasOpenChildTabs('settings','links'),'oxygen-sidebar-breadcrumb-current':!hasOpenChildTabs('settings','links')}"
					ng-click="switchTab('settings','links')">
					<?php _e("Links", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowChildTab('settings','default-styles','page-width')">
					<?php _e("Page Width", "oxygen"); ?>
				</div>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-if="isShowChildTab('settings','default-styles','sections')">
					<?php _e("Sections", "oxygen"); ?>
				</div>

				<div class='oxygen-sidebar-breadcrumb-separator'
					ng-show="hasOpenChildTabs('settings','links')">/</div>
				
				<?php $links = array(	
								"all" => __("All","oxygen"),
								"text_link" => __("Text Link","oxygen"),
								"link_wrapper" => __("Link Wrapper","oxygen"),
								"button" => __("Button","oxygen") );

				foreach ($links as $link => $title) : ?>
				<div class='oxygen-sidebar-breadcrumb-current' 
					ng-show="isShowChildTab('settings','links','<?php echo $link; ?>')">
					<?php echo $title; ?>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="oxygen-settings-content">

				<div class="oxygen-sidebar-advanced-subtab oxygen-settings-main-tab" 
					ng-click="switchTab('settings', 'page');"
					ng-hide="hasOpenTabs('settings')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/pagesettings.svg">
					<?php _e("Page settings", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
				</div>

				<div class="oxygen-sidebar-advanced-subtab oxygen-settings-main-tab" 
					ng-click="switchTab('settings', 'editor');"
					ng-hide="hasOpenTabs('settings')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
					<?php _e("Editor settings", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
				</div>

				<div class="oxygen-sidebar-advanced-subtab oxygen-settings-main-tab" 
					ng-click="switchTab('settings', 'default-styles');"
					ng-hide="hasOpenTabs('settings')">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/visual.svg">
					<?php _e("Global Styles", "oxygen"); ?>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
				</div>

				<div ng-if="isShowTab('settings','page')">
					<?php do_action("ct_toolbar_page_settings"); ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','fonts')">
					<?php do_action("ct_toolbar_global_fonts_settings"); ?>
				</div>

				<div ng-if="isShowTab('settings','editor')">
					<div class="oxygen-control-row" >
						<div class="oxygen-control-wrapper">
							<label class="oxygen-control-label"><?php _e("Indicate Parents","oxygen"); ?></label>
								<div class="oxygen-control ">
									<label class="oxygen-checkbox">
										<input type="checkbox" 
											ng-true-value="'true'" 
											ng-false-value="'false'"
											ng-model="$parent.iframeScope.globalSettings.indicateParents"
											ng-change="$parent.iframeScope.unsavedChanges();$parent.iframeScope.adjustResizeBox();">
										<div class="oxygen-checkbox-checkbox"
											ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.globalSettings.indicateParents=='true'}">
										</div>
									</label>
								</div>
							<!-- .oxygen-control -->
						</div>
						<!-- .oxygen-control-wrapper -->					
					</div>
				</div>

				<div class="oxygen-sidebar-flex-panel"
					ng-if="isShowTab('settings','default-styles')"
					ng-hide="isShowChildTab('settings','default-styles','fonts')||isShowChildTab('settings','default-styles','headings')||isShowChildTab('settings','default-styles','body-text')||isShowChildTab('settings','default-styles','links')||isShowChildTab('settings','default-styles','page-width')||isShowChildTab('settings','default-styles','sections')">

					<div class="oxygen-sidebar-advanced-subtab" 
						ng-click="switchChildTab('settings', 'default-styles', 'fonts');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg">
						<?php _e("Fonts", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
					</div>
					
					<div class="oxygen-sidebar-advanced-subtab" 
						ng-click="switchChildTab('settings', 'default-styles', 'headings');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/typography.svg">
						<?php _e("Headings", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
					</div>

					<div class="oxygen-sidebar-advanced-subtab" 
						ng-click="switchChildTab('settings', 'default-styles', 'body-text');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/bodytext.svg">
						<?php _e("Body Text", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
					</div>

					<div class="oxygen-sidebar-advanced-subtab" 
						ng-click="switchTab('settings', 'links');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/links.svg">
						<?php _e("Links", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
					</div>

					<div class="oxygen-sidebar-advanced-subtab" 
						ng-click="switchChildTab('settings', 'default-styles', 'page-width');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
						<?php _e("Page Width", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
					</div>

					<div class="oxygen-sidebar-advanced-subtab" 
						ng-click="switchChildTab('settings', 'default-styles', 'sections');">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
						<?php _e("Sections", "oxygen"); ?>
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
					</div>

					<div class="oxygen-control-row oxygen-control-row-bottom-bar">
						<a href="#" class="oxygen-apply-button" ng-click="iframeScope.resetGlobalStylesToDefault()">
							<?php _e("Reset to Default"); ?></a>
					</div>

				</div>

				<div ng-if="isShowChildTab('settings','default-styles','headings')">
					<?php do_action("oxygen_toolbar_settings_headings"); ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','body-text')">
					<?php do_action("oxygen_toolbar_settings_body_text"); ?>
				</div>

				<div ng-if="isShowTab('settings','links')">
					<?php do_action("oxygen_toolbar_settings_links"); ?>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','page-width')">
					<div class="oxygen-control-row">
						<div class='oxygen-control-wrapper'>
							<label class='oxygen-control-label'><?php _e("Page Width","oxygen"); ?></label>
							<div class='oxygen-measure-box'>
								<input type="text" spellcheck="false"
									ng-model="iframeScope.globalSettings['max-width']"
									ng-change="iframeScope.pageSettingsUpdate()"/>
								<div class='oxygen-measure-box-unit-selector'>
									<div class='oxygen-measure-box-selected-unit'>px</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div ng-if="isShowChildTab('settings','default-styles','sections')">
					<div class="oxygen-control-row">
						<div class='oxygen-control-wrapper'>
							<label class='oxygen-control-label'><?php _e("Container Padding","oxygen"); ?></label>
							<div class='oxygen-control'>
								<div class='oxygen-four-sides-measure-box'>
									<div class='oxygen-measure-box'>
										<input type="text" spellcheck="false"
											data-option="container-padding-top"
											ng-model="iframeScope.globalSettings.sections['container-padding-top']"
											ng-model-options="{ debounce: 10 }"/>
										<div class='oxygen-measure-box-unit-selector'>
											<div class='oxygen-measure-box-selected-unit'>{{iframeScope.globalSettings.sections['container-padding-top-unit']}}</div>
											<div class="oxygen-measure-box-units">
												<div class="oxygen-measure-box-unit"
													ng-click="iframeScope.globalSettings.sections['container-padding-top-unit']='px'"
													ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-top-unit']=='px'}">
													px
												</div>
												<div class="oxygen-measure-box-unit"
													ng-click="iframeScope.globalSettings.sections['container-padding-top-unit']='%'"
													ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-top-unit']=='%'}">
													&#37;
												</div>
												<div class="oxygen-measure-box-unit"
													ng-click="iframeScope.globalSettings.sections['container-padding-top-unit']='em'"
													ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-top-unit']=='em'}">
													em
												</div>
											</div>
										</div>
									</div>
									<div class='oxygen-four-sides-measure-box-left-right'>
										<div class='oxygen-measure-box'>
											<input type="text" spellcheck="false"
												data-option="container-padding-left"
												ng-model="iframeScope.globalSettings.sections['container-padding-left']"
												ng-model-options="{ debounce: 10 }"/>
											<div class='oxygen-measure-box-unit-selector'>
												<div class='oxygen-measure-box-selected-unit'>{{iframeScope.globalSettings.sections['container-padding-left-unit']}}</div>
												<div class="oxygen-measure-box-units">
													<div class="oxygen-measure-box-unit"
														ng-click="iframeScope.globalSettings.sections['container-padding-left-unit']='px'"
														ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-left-unit']=='px'}">
														px
													</div>
													<div class="oxygen-measure-box-unit"
														ng-click="iframeScope.globalSettings.sections['container-padding-left-unit']='%'"
														ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-left-unit']=='%'}">
														&#37;
													</div>
													<div class="oxygen-measure-box-unit"
														ng-click="iframeScope.globalSettings.sections['container-padding-left-unit']='em'"
														ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-left-unit']=='em'}">
														em
													</div>
												</div>
											</div>
										</div><div class='oxygen-measure-box'>
											<input type="text" spellcheck="false"
												data-option="container-padding-right"
												ng-model="iframeScope.globalSettings.sections['container-padding-right']"
												ng-model-options="{ debounce: 10 }"/>
											<div class='oxygen-measure-box-unit-selector'>
												<div class='oxygen-measure-box-selected-unit'>{{iframeScope.globalSettings.sections['container-padding-right-unit']}}</div>
												<div class="oxygen-measure-box-units">
													<div class="oxygen-measure-box-unit"
														ng-click="iframeScope.globalSettings.sections['container-padding-right-unit']='px'"
														ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-right-unit']=='px'}">
														px
													</div>
													<div class="oxygen-measure-box-unit"
														ng-click="iframeScope.globalSettings.sections['container-padding-right-unit']='%'"
														ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-right-unit']=='%'}">
														&#37;
													</div>
													<div class="oxygen-measure-box-unit"
														ng-click="iframeScope.globalSettings.sections['container-padding-right-unit']='em'"
														ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-right-unit']=='em'}">
														em
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class='oxygen-measure-box'>
										<input type="text" spellcheck="false"
											data-option="container-padding-bottom"
											ng-model="iframeScope.globalSettings.sections['container-padding-bottom']"
											ng-model-options="{ debounce: 10 }"/>
										<div class='oxygen-measure-box-unit-selector'>
											<div class='oxygen-measure-box-selected-unit'>{{iframeScope.globalSettings.sections['container-padding-bottom-unit']}}</div>
											<div class="oxygen-measure-box-units">
												<div class="oxygen-measure-box-unit"
													ng-click="iframeScope.globalSettings.sections['container-padding-bottom-unit']='px'"
													ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-bottom-unit']=='px'}">
													px
												</div>
												<div class="oxygen-measure-box-unit"
													ng-click="iframeScope.globalSettings.sections['container-padding-bottom-unit']='%'"
													ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-bottom-unit']=='%'}">
													&#37;
												</div>
												<div class="oxygen-measure-box-unit"
													ng-click="iframeScope.globalSettings.sections['container-padding-bottom-unit']='em'"
													ng-class="{'oxygen-measure-box-unit-active':iframeScope.globalSettings.sections['container-padding-bottom-unit']=='em'}">
													em
												</div>
											</div>
										</div>
									</div>
									<div class="oxygen-apply-all-trigger">
										<?php _e("apply all »", "oxygen"); ?>
									</div>
								</div>
								<!-- .oxygen-four-sides-measure-box -->
							</div>
						</div>
					</div>
				</div>

			</div><!-- .oxygen-settings-content -->

		</div><!-- .oxygen-global-settings -->
		
		<?php require_once "views/side-panel.view.php"; ?>
		<?php require_once "views/dialog-window.view.php";?>
		<?php require_once "views/notice-modal.view.php"; ?>

		<?php 
			/**
			 * Hook for add-ons to add UI elements inside the toolbar
			 *
			 * @since 1.4
			 */
			do_action("oxygen_before_toolbar_close"); 
		?>

	</div><!-- #oxygen-sidebar -->

</div><!-- #oxygen-ui -->


<?php 
	/**
	 * Hook for add-ons to add UI elements outside the toolbar
	 *
	 * @since 1.4
	 */
	do_action("oxygen_after_toolbar"); 
?>

<div id="resize-overlay"></div>

<div id="ct-page-overlay" class="ct-page-overlay"><i class="fa fa-cog fa-4x fa-spin"></i></div><!-- #ct-page-overlay -->

<div id="oxy-no-class-msg" class="oxygen-overlay-property-msg oxy-no-class-msg">
	<?php _e("This property is not available for classes. It will be set in the element/ID.","oxygen"); ?>
</div>
<div id="oxy-no-media-msg" class="oxygen-overlay-property-msg oxy-no-media-msg">
	<?php _e("This property is not available for media queries. It will be set for 'All devices’.","oxygen"); ?>
</div>
<div id="oxy-no-class-no-media-msg" class="oxygen-overlay-property-msg oxy-no-class-msg oxy-no-media-msg">
	<?php _e("This property is not available for media queries or classes. It will be set for 'All devices’ in the element/ID.","oxygen"); ?>
</div>