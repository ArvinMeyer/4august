<?php $this->colorpicker_with_wrapper("background-color", __("Background Color", "oxygen")); ?>
<div class='oxygen-control-wrapper'>
	<label class='oxygen-control-label'><?php _e("Background Image","oxygen"); ?></label>
	<div class='oxygen-control'>
		<div class="oxygen-file-input">
			<input type="text" spellcheck="false" 
				ng-change="iframeScope.setOptionModel('background-image', iframeScope.component.options[iframeScope.component.active.id]['model']['background-image'])" 
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'background-image')}" 
				ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['background-image']" 
				ng-model-options="{ debounce: 10 }" 
				class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

			<div class="oxygen-file-input-browse" 
				data-mediatitle="Select Image" 
				data-mediabutton="Select Image" 
				data-mediaproperty="background-image" 
				data-mediatype="mediaUrl"><?php _e("browse","oxygen"); ?></div>

			<div ng-if="iframeScope.currentClass === false" class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesImageMode" callback="iframeScope.insertShortcodeToBackground">data</div>
		</div>
	</div>
</div>

<?php $this->colorpicker_with_wrapper("overlay-color", __("Image Overlay Color", "oxygen")); ?>

<!-- background-size -->
<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
		<label class='oxygen-control-label'><?php _e("Background Size", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('background-size','auto'); ?>
				<?php $this->button_list_button('background-size','cover'); ?>
				<?php $this->button_list_button('background-size','contain'); ?>
				<?php $this->button_list_button('background-size','manual'); ?>

			</div>
		</div>
	</div>
</div>

<div class="oxygen-control-row" ng-show="iframeScope.component.options[iframeScope.component.active.id]['model']['background-size'] == 'manual'">
	<?php $this->measure_box_with_wrapper("background-size-width", __("Width", "oxygen"), 'px,%,em'); ?>
	<?php $this->measure_box_with_wrapper("background-size-height", __("Height", "oxygen"), 'px,%,em'); ?>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
		<label class='oxygen-control-label'><?php _e("Background Repeat", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('background-repeat','no-repeat'); ?>
				<?php $this->button_list_button('background-repeat','repeat'); ?>
				<?php $this->button_list_button('background-repeat','repeat-x'); ?>
				<?php $this->button_list_button('background-repeat','repeat-y'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
		<label class='oxygen-control-label'><?php _e("Background Attachment (Parallax)", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('background-attachment','scroll'); ?>
				<?php $this->button_list_button('background-attachment','fixed'); ?>

			</div>
		</div>
	</div>
</div>

<div class="oxygen-control-row">
	<?php $this->measure_box_with_wrapper("background-position-left", __("Left", "oxygen"), 'px,%,em'); ?>
	<?php $this->measure_box_with_wrapper("background-position-top", __("Top", "oxygen"), 'px,%,em'); ?>
</div>

<div class='oxygen-control-row'>
	<div class='oxygen-control-wrapper' id='oxygen-control-layout-display'>
		<label class='oxygen-control-label'><?php _e("Background Clip", "oxygen"); ?></label>
		<div class='oxygen-control'>
			<div class='oxygen-button-list'>

				<?php $this->button_list_button('background-clip','border-box'); ?>
				<?php $this->button_list_button('background-clip','padding-box'); ?>
				<?php $this->button_list_button('background-clip','content-box'); ?>

			</div>
		</div>
	</div>
</div>

<div class='oxygen-control-row'
	ng-show="isActiveName('ct_section')">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Video Background URL (.mp4 / .webm)","oxygen"); ?></label>
		<div class='oxygen-control oxygen-special-property'>
			<div class="oxygen-file-input">
				<input type="text" spellcheck="false" 
					ng-change="iframeScope.setOptionModel('video_background', iframeScope.component.options[iframeScope.component.active.id]['model']['video_background']); iframeScope.rebuildDOM(iframeScope.component.active.id);" 
					ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'video_background')}" 
					ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['video_background']"
					class="ng-valid oxygen-option-default ng-dirty ng-valid-parse ng-touched">

				<div class="oxygen-file-input-browse" 
					data-mediatitle="Select Video" 
					data-mediabutton="Select Video" 
					data-mediaproperty="video_background" 
					data-mediacontent="video"
					data-mediatype="videoUrl"><?php _e("browse","oxygen"); ?></div>
			</div>
		</div>
	</div>
</div>

<div class="oxygen-control-row"
	ng-show="isActiveName('ct_section')">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Hide Video Below","oxygen") ?></label>
		<div class='oxygen-control oxygen-special-property'>
			<?php $this->media_queries_list("video_background_hide") ?>
		</div>
	</div>
</div>

<div class="oxygen-control-row"
	ng-show="isActiveName('ct_section')">
	<div class='oxygen-control-wrapper'>
		<label class='oxygen-control-label'><?php _e("Video Overlay","oxygen") ?></label>
		<div class='oxygen-control oxygen-special-property'>
			<?php $this->colorpicker("video_background_overlay"); ?>
		</div>
	</div>
</div>