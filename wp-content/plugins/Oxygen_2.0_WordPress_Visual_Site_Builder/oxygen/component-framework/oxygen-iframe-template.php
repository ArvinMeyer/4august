<!DOCTYPE html>
<html <?php language_attributes(); ?> ng-app="CTFrontendBuilder">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<!-- WP_HEAD() START -->
<?php wp_head(); ?>
<!-- END OF WP_HEAD() -->
<style id="ct-id-styles" class="ct-css-location"></style>
<style id="oxygen-global-settings-styles"></style>
<style id="ct-class-styles" class="ct-css-location"></style>
</head>
<?php
	$ct_inner = isset($_REQUEST['ct_inner'])?'ct_inner':'';
?>
<body <?php body_class($ct_inner); ?> ng-controller="BuilderController">
	<style class="ct-css-location test" ng-repeat="stylesheet in styleSheets | filter : filterStylesheets track by stylesheet.id">
		{{stylesheet.css}}
	</style>
	<div id="ct-builder" class="ct-builder oxygen-body"
		is-nestable="true" 
		ng-builder-wrap 
		ng-attr-component-id="0" 
		ng-init="<?php do_action("ct_builder_ng_init"); ?>"
		ng-class="{'ct-highlite-outer-template' : parentScope.isActiveId(0)}"
		ng-mousedown="selectorDetector.bubble=false"
		<?php if (!isset($_REQUEST['ct_inner'])) : ?>
		dnd-list="" 
		dnd-allowed-types="getDNDAllowedTypes('ct-builder')"
		dnd-dragover="dragoverCallback('ct-builder', external, type)"
		<?php endif; ?>>

		<?php do_action("ct_builder_start"); ?>
		<?php do_action("ct_builder_end"); ?>

	</div><!-- #ct-builder -->
	<div id="oxygen-resize-box" oxygen-resize-box>
		<div id="oxygen-resize-box-titlebar" class="oxygen-resize-box-titlebar">
			<div id="oxygen-resize-box-drag-handler" class="oxygen-resize-box-drag oxygen-resize-box-icon"
				ng-hide="isBuiltIn()"
				 dnd-draggable=""
				 dnd-effect-allowed="move"
				 dnd-type="'{{selectedDragElementDNDType}}'"
				 dnd-dragstart="dragstartResizeBoxCallback(event)"
				 dnd-dragend="dragendResizeBoxCallback(event)"
				 dnd-disable-if="isLastRow()"></div>
			<div class="oxygen-resize-box-breadcrumbs">
				<span class="oxygen-resize-box-breadcrumb">{{niceNames[component.active.name]}}<span ng-if="component.active.parent.id > 0" class="oxygen-resize-box-top oxygen-resize-box-icon" ng-click="activateComponent(component.active.parent.id, component.active.parent.name)"></span></span>
			</div>
		</div>
		<div id="oxygen-resize-box-parent-titlebar" class="oxygen-resize-box-titlebar oxygen-resize-box-parent-titlebar">
			<div id="oxygen-resize-box-parent-drag-handler" class="oxygen-resize-box-drag oxygen-resize-box-icon"
				 dnd-draggable=""
				 dnd-effect-allowed="move"
				 dnd-type="'{{selectedDragElementDNDType}}'"
				 dnd-dragstart="dragstartResizeBoxCallback(event,component.active.parent.id)"
				 dnd-dragend="dragendResizeBoxCallback(event,component.active.parent.id)"
				 dnd-disable-if="isLastRow(component.active.parent.id)"></div>
			<div class="oxygen-resize-box-breadcrumbs">
				<span class="oxygen-resize-box-breadcrumb"
					ng-click="activateComponent(component.active.parent.id, component.active.parent.name)">
					{{niceNames[component.active.parent.name]}}</span>
			</div>
		</div>
	</div>
	<!-- #oxygen-resize-box -->
<?php wp_footer(); ?>
</body>
</html>
