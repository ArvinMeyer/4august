<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper'
		ng-hide="isActiveName('ct_column')&&iframeScope.isEditing('media')">
		<label class='oxygen-control-label'><?php _e("Width", "component-theme"); ?></label>
		
		<div class='oxygen-control'
			ng-hide="isActiveName('ct_column')">
			<?php $this->measure_box('width'); ?>
		</div>

		<div class='oxygen-control'
			ng-show="isActiveName('ct_column')">
			<div class='oxygen-measure-box'
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'width')}">
				<input type="text" spellcheck="false"
					ng-change="iframeScope.setOption(iframeScope.component.active.id,'ct_column','width'); iframeScope.updateColumnsOnChange(component.active.id,{{iframeScope.component.options[iframeScope.component.active.id]['model']['width']}})"
					<?php $this->ng_attributes('width',"class,model"); ?>/>
				<div class='oxygen-measure-box-unit-selector'><div>{{iframeScope.getOptionUnit('width')}}</div></div>
			</div>
		</div>
	</div>

	<?php $this->measure_box_with_wrapper("min-width", __("Min-width", "oxygen"), 'px,%,em,vw,vh'); ?>
	<?php $this->measure_box_with_wrapper("max-width", __("Max-width", "oxygen"), 'px,%,em,vw,vh'); ?>

</div>

<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper'
		ng-hide="isActiveName('ct_column')&&iframeScope.isEditing('media')">
		<label class='oxygen-control-label'><?php _e("Height", "component-theme"); ?></label>
		
		<div class='oxygen-control'>

			<?php $this->measure_box('height'); ?>

		</div>
	</div>

	<?php $this->measure_box_with_wrapper("min-height", __("Min-height", "oxygen"), 'px,%,em,vw,vh'); ?>
	<?php $this->measure_box_with_wrapper("max-height", __("Max-height", "oxygen"), 'px,%,em,vw,vh'); ?>
</div>

<div class="oxygen-control-row"
	ng-show="isActiveName('ct_section')">
	<div class="oxygen-control-wrapper">
		<label class="oxygen-control-label"><?php _e("Section Container Width","component-theme");?></label>
		<div class="oxygen-control">
		
			<div class="oxygen-select oxygen-select-box-wrapper">
				<div class="oxygen-select-box"
					ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'section-width')}">
					<div class="oxygen-select-box-current">{{iframeScope.getOption('section-width')}}</div>
					<div class="oxygen-select-box-dropdown"></div>
				</div>
				<div class="oxygen-select-box-options">
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('section-width','page-width')">
						page width</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('section-width','full-width')">
						full width</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('section-width','custom')">
						custom</div>
				</div>	
			</div>
		
		</div>
	</div>
</div>

<div class="oxygen-control-row"
	ng-show="isActiveName('ct_section')&&iframeScope.getOption('section-width')=='custom'">
	<div class="oxygen-control-wrapper">
		<label class="oxygen-control-label"><?php _e("Custom Container Width"); ?></label>
		<div class="oxygen-control">

			<div class="oxygen-measure-box"
				ng-class="{'oxygen-measure-box-unit-auto':iframeScope.getOptionUnit('custom-width')=='auto'}">
				<input type="text" spellcheck="false"
					<?php $this->ng_attributes('custom-width'); ?> />
				<div class="oxygen-measure-box-unit-selector">
					<div class='oxygen-measure-box-selected-unit'>{{iframeScope.getOptionUnit('custom-width')}}</div>
					<?php $this->measure_type_select('custom-width'); ?>
				</div>
			</div>

		</div>
	</div>
</div>