<div class='oxygen-sidebar-advanced-subtab'
	ng-click="switchTab('effects', 'opacity')"
	ng-hide="hasOpenTabs('effects')"
	ng-class="{'oxy-styles-present' : iframeScope.isTabHasOptions('effects','opacity')}">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/opacity.svg' />
		<span><?php _e("Opacity","oxygen"); ?></span>
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
</div>

<div class='oxygen-sidebar-advanced-subtab'
	ng-click="switchTab('effects', 'transition')"
	ng-hide="hasOpenTabs('effects')"
	ng-class="{'oxy-styles-present' : iframeScope.isTabHasOptions('effects','transition')}">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/transition.svg' />
		<span><?php _e("Transition","oxygen"); ?></span>
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
</div>

<div class='oxygen-sidebar-advanced-subtab'
	ng-click="switchTab('effects', 'box-shadow')"
	ng-hide="hasOpenTabs('effects')"
	ng-class="{'oxy-styles-present' : iframeScope.isTabHasOptions('effects','box-shadow')}">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/box-shadow.svg' />
		<span><?php _e("Box Shadow","oxygen"); ?></span>
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
</div>

<div class='oxygen-sidebar-advanced-subtab'
	ng-click="switchTab('effects', 'text-shadow')"
	ng-hide="hasOpenTabs('effects')"
	ng-class="{'oxy-styles-present' : iframeScope.isTabHasOptions('effects','text-shadow')}">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/text-shadow.svg' />
		<span><?php _e("Text Shadow","oxygen"); ?></span>
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
</div>

<div class='oxygen-sidebar-advanced-subtab'
	ng-click="switchTab('effects', 'css-filter')"
	ng-hide="hasOpenTabs('effects')"
	ng-class="{'oxy-styles-present' : iframeScope.isTabHasOptions('effects','css-filter')}">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/filters.svg' />
		<span><?php _e("Filter","oxygen"); ?></span>
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg' />
</div>

<div class="oxygen-sidebar-flex-panel" ng-if="isShowTab('effects', 'opacity')">
	
	<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb" >
		<div class="oxygen-sidebar-breadcrumb-icon" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
		</div>
		<div class="oxygen-sidebar-breadcrumb-all-styles" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);"><?php _e("Effects","oxygen"); ?></div>
		<div class="oxygen-sidebar-breadcrumb-separator">/</div>
		<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Opacity","oxygen"); ?></div>
	</div>

	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('opacity', __('Opacity','oxygen'), "", 0, 1, true, 0.1); ?>
	</div>

</div><!-- ng-if="isShowTab('effects', 'opacity')" -->


<div class="oxygen-sidebar-flex-panel" ng-if="isShowTab('effects', 'transition')">
	
	<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb" >
		<div class="oxygen-sidebar-breadcrumb-icon" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
		</div>
		<div class="oxygen-sidebar-breadcrumb-all-styles" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);"><?php _e("Effects","oxygen"); ?></div>
		<div class="oxygen-sidebar-breadcrumb-separator">/</div>
		<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Transition","oxygen"); ?></div>
	</div>

	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('transition-duration',__('Transition Duration','oxygen'), 'sec', 0, 1, true, 0.1); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->simple_input_with_wrapper('transition-timing-function',__('Timing Function','oxygen')); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->simple_input_with_wrapper('transition-property',__('CSS Property','oxygen')); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('transition-delay',__('Delay','oxygen'), 'sec', 0, 1, true, 0.1); ?>
	</div>

</div><!-- ng-if="isShowTab('effects', 'transition')" -->


<div class="oxygen-sidebar-flex-panel" ng-if="isShowTab('effects', 'box-shadow')">
	
	<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb" >
		<div class="oxygen-sidebar-breadcrumb-icon" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
		</div>
		<div class="oxygen-sidebar-breadcrumb-all-styles" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);"><?php _e("Effects","oxygen"); ?></div>
		<div class="oxygen-sidebar-breadcrumb-separator">/</div>
		<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Box Shadow","oxygen"); ?></div>
	</div>
	
	<div class="oxygen-control-row">
		<div class="oxygen-control-wrapper">
			<label class="oxygen-checkbox">
				<input type="checkbox"
					ng-true-value="'inset'" 
					ng-false-value="'false'"
					<?php $this->ng_attributes('box-shadow-inset'); ?>> 
				<div class='oxygen-checkbox-checkbox'
					ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('box-shadow-inset')=='inset','oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'box-shadow-inset')}">
					Inset
				</div>
			</label>
		</div>
	</div>
	
	<div class="oxygen-control-row">
        <?php $this->colorpicker_with_wrapper("box-shadow-color", __("Color", "oxygen") ); ?>
    </div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('box-shadow-horizontal-offset',__('Horizontal Offset','oxygen'), 'px'); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('box-shadow-vertical-offset',__('Vertical Offset','oxygen'), 'px'); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('box-shadow-blur',__('Blur','oxygen'), 'px'); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('box-shadow-spread',__('Spread','oxygen'), 'px'); ?>
	</div>

</div><!-- ng-if="isShowTab('effects', 'box-shadow')" -->


<div class="oxygen-sidebar-flex-panel" ng-if="isShowTab('effects', 'text-shadow')">
	
	<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb" >
		<div class="oxygen-sidebar-breadcrumb-icon" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
		</div>
		<div class="oxygen-sidebar-breadcrumb-all-styles" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);"><?php _e("Effects","oxygen"); ?></div>
		<div class="oxygen-sidebar-breadcrumb-separator">/</div>
		<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Text Shadow","oxygen"); ?></div>
	</div>
	
	<div class="oxygen-control-row">
        <?php $this->colorpicker_with_wrapper("text-shadow-color", __("Color", "oxygen") ); ?>
    </div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('text-shadow-horizontal-offset',__('Horizontal Offset','oxygen'), 'px'); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('text-shadow-vertical-offset',__('Vertical Offset','oxygen'), 'px'); ?>
	</div>
	<div class='oxygen-control-row'>
		<?php $this->slider_measure_box_with_wrapper('text-shadow-blur',__('Blur','oxygen'), 'px'); ?>
	</div>

</div><!-- ng-if="isShowTab('effects', 'text-shadow')" -->


<div class="oxygen-sidebar-flex-panel" ng-if="isShowTab('effects', 'css-filter')">
	
	<div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb" >
		<div class="oxygen-sidebar-breadcrumb-icon" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/back.svg">
		</div>
		<div class="oxygen-sidebar-breadcrumb-all-styles" ng-click="$parent.$parent.switchTab('advanced', 'effects');$parent.$parent.closeTabs(['effects']);"><?php _e("Effects","oxygen"); ?></div>
		<div class="oxygen-sidebar-breadcrumb-separator">/</div>
		<div class="oxygen-sidebar-breadcrumb-current"><?php _e("Filter","oxygen"); ?></div>
	</div>
	
	<div class='oxygen-control-row'>
		<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
			<label class='oxygen-control-label'><?php _e("Filter","oxygen"); ?></label>
			<div class='oxygen-control'>
				<div class="oxygen-select oxygen-select-box-wrapper">
					<div class="oxygen-select-box">
						<div class="oxygen-select-box-current">{{$parent.iframeScope.getOption('filter')}}</div>
						<div class="oxygen-select-box-dropdown"></div>
					</div>
					<div class="oxygen-select-box-options">
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','')">&nbsp;</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','blur')">blur()</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','brightness')">brightness()</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','contrast')">contrast()</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','grayscale')">grayscale()</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','hue-rotate')">hue-rotate()</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','invert')">invert()</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','saturate')">saturate()</div>
						<div class="oxygen-select-box-option" 
							ng-click="$parent.iframeScope.setOptionModel('filter','sepia')">sepia()</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="blur"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-blur',__('Filter Amount','oxygen'), 'px', 0, 100); ?>
	</div>
	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="brightness"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-brightness',__('Filter Amount','oxygen'), '%', 0, 200); ?>
	</div>
	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="contrast"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-contrast',__('Filter Amount','oxygen'), '%', 0, 200); ?>
	</div>
	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="grayscale"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-grayscale',__('Filter Amount','oxygen'), '%', 0, 100); ?>
	</div>
	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="hue-rotate"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-hue-rotate',__('Filter Amount','oxygen'), 'deg', 0, 360); ?>
	</div>
	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="invert"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-invert',__('Filter Amount','oxygen'), '%', 0, 100); ?>
	</div>
	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="saturate"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-saturate',__('Filter Amount','oxygen'), '%', 0, 200); ?>
	</div>
	<div class='oxygen-control-row' ng-show='$parent.iframeScope.getOption("filter")=="sepia"'>
		<?php $this->slider_measure_box_with_wrapper('filter-amount-sepia',__('Filter Amount','oxygen'), '%', 0, 100); ?>
	</div>

</div><!-- ng-if="isShowTab('effects', 'filter')" -->
