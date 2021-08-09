<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php if ( defined("SHOW_CT_BUILDER") ) : ?>ng-app="CTFrontendBuilderUI"<?php endif; ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<!-- WP_HEAD() START -->
<?php wp_head(); ?>
<!-- END OF WP_HEAD() -->
</head>
<?php
	$ct_inner = isset($_REQUEST['ct_inner'])?'ct_inner':'';
?>
<body <?php body_class($ct_inner); ?> <?php if ( defined("SHOW_CT_BUILDER") ) : ?>id="ct-controller-ui" ng-controller="ControllerUI"<?php endif; ?>>

	<?php do_action("ct_before_builder"); ?>
	<?php if ( defined("SHOW_CT_BUILDER") ) : ?>
	<div id="ct-viewport-container">
		<iframe id="ct-artificial-viewport" data-src="<?php echo ct_get_current_url( "oxygen_iframe=true" ); ?>"></iframe>
		<div id="ct-viewport-ruller-wrap">
			<div id="ct-viewport-ruller">
				<label>0</label>
				<label>100</label>
				<label>200</label>
				<label>300</label>
				<label>400</label>
				<label>500</label>
				<label>600</label>
				<label>700</label>
				<label>800</label>
				<label>900</label>
				<label>1000</label>
				<label>1100</label>
				<label>1200</label>
				<label>1300</label>
				<label>1400</label>
				<label>1500</label>
				<label>1600</label>
				<label>1700</label>
				<label>1800</label>
				<label>1900</label>
				<label>2000</label>	
				<label>2100</label>	
				<label>2200</label>	
				<label>2300</label>	
				<label>2400</label>	
				<label>2500</label>	
				<label>2600</label>
				<label>2700</label>
				<label>2800</label>
				<label>2900</label>
			</div>
			<div id="ct-viewport-handle"></div>
		</div>
		<div id="oxygen-status-bar" ng-class="{'oxygen-status-bar-active':statusBarActive}">{{statusMessage}}</div>
	</div><!-- #ct-viewport-container -->
	<?php else: ?>
		<?php do_action("ct_builder_start"); ?>
		<?php do_action("ct_builder_end"); ?>
	<?php endif; ?>
<!-- WP_FOOTER -->
<?php wp_footer(); ?>
<!-- /WP_FOOTER --> 
</body>
</html>