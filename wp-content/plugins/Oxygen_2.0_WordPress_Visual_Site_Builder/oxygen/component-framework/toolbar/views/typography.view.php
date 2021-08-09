<!-- font family & size-->
<div class='oxygen-control-row'>
	<?php $this->font_family_dropdown(); ?>
	<?php $this->measure_box_with_wrapper("font-size", __("Font size", "oxygen"), 'px,%,em'); ?>
</div>

<div class="oxygen-control-row">
	<?php $this->colorpicker_with_wrapper("color", __("Color", "oxygen"), 'oxygen-typography-font-color'); ?>
</div>

<div class="oxygen-control-row">

	<div class='oxygen-control-wrapper' id='oxygen-typography-font-family'>
		<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
		<div class='oxygen-control'>

			<div class="oxygen-select oxygen-select-box-wrapper">
				<div class="oxygen-select-box"
					ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'font-weight')}">
					<div class="oxygen-select-box-current">{{iframeScope.getOption('font-weight')}}</div>
					<div class="oxygen-select-box-dropdown"></div>
				</div>
				<div class="oxygen-select-box-options">
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','')">&nbsp;</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','100')">100</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','200')">200</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','300')">300</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','400')">400</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','500')">500</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','600')">600</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','700')">700</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','800')">800</div>
					<div class="oxygen-select-box-option" 
						ng-click="iframeScope.setOptionModel('font-weight','900')">900</div>
				</div>
			</div>
		</div>
	</div>

</div>

<!-- text align -->
<div class="oxygen-control-row">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Text Align","oxygen"); ?></label>
		<div class='oxygen-control oxygen-control-text-align'>
			<div class='oxygen-icon-button-list'>

				<?php $this->icon_button_list_button('text-align','left','text-align/left.svg','text-align/left--active.svg'); ?>
				<?php $this->icon_button_list_button('text-align','center','text-align/center.svg','text-align/center--active.svg'); ?>
				<?php $this->icon_button_list_button('text-align','right','text-align/right.svg','text-align/right--active.svg'); ?>
				<?php $this->icon_button_list_button('text-align','justify','text-align/justify.svg','text-align/justify--active.svg'); ?>
				
			</div>
		</div>
	</div>
</div>

<!-- line height & letter spacing -->
<div class='oxygen-control-row'>
	<?php $this->simple_input_with_wrapper('line-height',__('Line Height','oxygen')); ?>
	<?php $this->measure_box_with_wrapper('letter-spacing',__('Letter Spacing','oxygen')); ?>
</div>

<!-- text decoration and font style -->
<div class='oxygen-control-row oxygen-control-row-text-decoration-font-style'>

	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Text Decoration"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('text-decoration','none','none'); ?>
				<?php $this->button_list_button('text-decoration','underline','U', 'oxygen-text-decoration-underline'); ?>
				<?php $this->button_list_button('text-decoration','overline','O', 'oxygen-text-decoration-overline'); ?>
				<?php $this->button_list_button('text-decoration','line-through','S', 'oxygen-text-decoration-linethrough'); ?>

			</div>
		</div>
	</div>

	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Font Style"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('font-style','normal','normal'); ?>
				<?php $this->button_list_button('font-style','italic','I', 'oxygen-font-style-italic'); ?>
				
			</div>
		</div>
	</div>
</div>

<!-- text transform -->
<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Text Transform","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('text-transform','none'); ?>
				<?php $this->button_list_button('text-transform','capitalize'); ?>
				<?php $this->button_list_button('text-transform','uppercase'); ?>
				<?php $this->button_list_button('text-transform','lowercase'); ?>
			
			</div>
		</div>
	</div>
</div>

<!-- font smoothing -->
<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Font Smoothing","oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>
				
				<?php $this->button_list_button('-webkit-font-smoothing','initial'); ?>
				<?php $this->button_list_button('-webkit-font-smoothing','antialiased'); ?>
				<?php $this->button_list_button('-webkit-font-smoothing','subpixel-antialiased'); ?>
			
			</div>
		</div>
	</div>
</div>