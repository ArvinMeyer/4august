<div class="oxygen-control-row oxygen-control-row-bottom-bar"
	ng-show="!iframeScope.selectorDetector.mode&&(isSelectorComponent('ct_widget')||iframeScope.hasOxyDataInside())&&!isShowTab('easyPosts','templateCSS')&&!isShowTab('easyPosts','templatePHP')&&!isShowTab('easyPosts','count')&&!isShowTab('easyPosts','query')&&!isShowTab('easyPosts','postType')&&!isShowTab('easyPosts','filtering')&&!isShowTab('easyPosts','order')&&!isShowTab('commentsList', 'templateCSS')&&!isShowTab('commentsList', 'templatePHP')">
	<div class="oxygen-selector-detector-style-button"
		ng-click="enableSelectorDetectorMode()">
		<?php _e("Style Output", "oxygen"); ?>
	</div>
</div>