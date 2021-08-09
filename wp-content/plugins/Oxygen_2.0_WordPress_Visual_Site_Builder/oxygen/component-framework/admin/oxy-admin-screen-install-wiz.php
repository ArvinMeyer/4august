<?php

	global $ct_source_sites;
	$screenshots = array();

	$defaultSet = isset($_REQUEST['default'])?$_REQUEST['default']:false;

	if(!$defaultSet) {
		foreach($ct_source_sites as $key => $item) {
			$result = ct_new_api_remote_get($item.'/wp-json/oxygen-vsb-connection/v1/screenshot/');
			$info = json_decode($result, true);
			$screenshots[$key]= $info['screenshot'];
		}
	}


?>

<script type="text/javascript">

jQuery(document).ready(function($){

	var selectedSite = "<?php echo $defaultSet?$defaultSet:'';?>", interrupted;

	$('a.oxygen-vsb-design-set-install-button').on('click', function(e) {

		e.preventDefault();

		selectedSite = $(this).data('site');
		$('span#site-name').text(selectedSite);
		$('a#oxygen-vsb-default-install').text('Confirm & Install');
		$('#keep').prop('checked', true);
		$('#delete').prop('checked', '');
		$('input#delete-confirmation').val('');

		$('.oxygen-vsb-design-set-install-wizard-confirmation').show();

	})

	$('#keep').on('change', function() {
		if($(this).prop('checked')) {
			$('input#delete-confirmation').val('');
		}
	})

	<?php
	if(!$defaultSet) {
	?>
		$('a.oxygen-vsb-design-set-install-wizard-button-back').on('click', function(e) {
			
			e.preventDefault();

			interrupted = true;

			$('.oxygen-vsb-design-set-install-wizard-confirmation').hide();
			
		})

	<?php
	}
	?>



	function setup_default_data(type) {

		if(interrupted) {
			return;
		}

		if(typeof(type) === 'undefined') {
			type = 'Stylesheets'; // starting point
		}

		var data = {
			action: 'ct_new_style_api_call',
			call_type: 'setup_default_data',
			site: selectedSite,
			type: type
		}

		if($('#delete').prop('checked')) {
			if($('input#delete-confirmation').val() === 'delete') {
				data['delete'] = 'delete';
			}
			else {
				alert('Type “delete” in the textbox below to confirm');
				return false;
			}
		}

		$('a#oxygen-vsb-default-install').text('Loading '+type);

		jQuery.post(ajaxurl, data, function(response) {

			if(typeof(response.done) === 'undefined' && response.next) {
				setup_default_data(response.next)
			}
			else {
				$('a#oxygen-vsb-default-install').text('Loading completed');
				$('a#oxygen-vsb-default-install').removeAttr('disabled');
				window.location.replace($('a#ct-back-to-oxygen-home').attr('href'));
			}
		});
	}

	$('a#oxygen-vsb-default-install').on('click', function(e) {
		e.preventDefault();

		$(this).attr('disabled', 'true');
		
		interrupted = false;
		setup_default_data();
		
	});

});

</script>

<div class='oxygen-admin-screen-design-set-install-wizard'>

	<div class='oxygen-vsb-design-set-install-wizard-confirmation' <?php echo !$defaultSet ? "style='display: none;'" : ""; ?> >

		<div class='oxygen-vsb-design-set-install-wizard-confirmation-content'>

			<h2><span id="site-name"><?php echo $defaultSet ? $defaultSet : ""; ?></span> will be installed</h2>
			
			<div class='oxygen-vsb-design-set-install-wizard-confirmation-touched' <?php echo oxygen_vsb_is_touched_install()?"":"style='display: none'"?>>

				<h3>What would you like to do with the templates, classes, custom selectors, and stylesheets that are currently present?</h3>

				<div class='oxygen-vsb-design-set-install-wizard-confirmation-content-form'>
					<label><input type='radio' name='keepornot' id='keep' checked /> Keep &amp; Deactivate <div class='oxy-tooltip'><div class='oxy-tooltip-text'>All classes, custom selectors, stylesheets, and their folders will be deactivated. All template options will be unset, so that they do not apply anywhere. Templates will be renamed with a prefix of 'inactive'. Global styles will be overwritten.</div></div></label>
					<label><input type='radio' name='keepornot' id='delete' /> Delete <div class='oxy-tooltip'><div class='oxy-tooltip-text'>All classes, custom selectors, stylesheets, and their folders will be deleted. All templates will be deleted from Oxygen &gt; Templates. Global styles will be overwritten.</div></div>

						<div class='oxygen-vsb-design-set-install-wizard-confirmation-content-form-delete-confirmation'>
							This can not be undone. Type “delete” in the textbox below to confirm.
							<input type='text' name='confirmation' id="delete-confirmation" placeholder='delete'></input>
						</div>

					</label>

				</div>

			</div>
			

			<div class='oxygen-vsb-design-set-install-wizard-confirmation-blank' <?php echo oxygen_vsb_is_touched_install()?"style='display: none'":""?>>
				<h3>Templates, classes, custom selectors, and stylesheets, pages, and global styles will be loaded.</h3>
			</div>

			

			<div class='oxygen-vsb-design-set-install-wizard-confirmation-nav'>
				<a href='' id='oxygen-vsb-default-install' class='oxygen-vsb-design-set-install-wizard-button'>Confirm &amp; Install</a>
				<a href='<?php echo $defaultSet ? add_query_arg('page', 'ct_install_wiz', get_admin_url()):"";?>' class='oxygen-vsb-design-set-install-wizard-button-back'>Go Back</a>
			</div>


		</div>

	</div>


	<div class='oxygen-vsb-design-set-install-wizard'>

		<div class='oxygen-vsb-design-set-install-wizard-header'>
			<div>
				<h1>Choose a Design Set</h1>
				<a id="ct-back-to-oxygen-home" href='<?php menu_page_url('ct_dashboard_page');?>'>&laquo; Go Back</a>
			</div>

			<img src='https://oxygenapp.com/wp-content/uploads/2016/08/oxygen-logo-white-2.png' />
		</div>


		<div class='oxygen-vsb-design-set-install-wizard-design-sets'>



	<?php
	global $ct_source_sites;

	if(is_array($ct_source_sites)) {

		foreach($ct_source_sites as $key => $site) {
			?>

			<div class='oxygen-vsb-design-set-install-wizard-design-sets-design-set'>
				<div data-source='<?php echo $key;?>' class='oxygen-vsb-design-set-install-wizard-design-sets-design-set-image' style='background-image: url(<?php echo isset($screenshots[$key])?$screenshots[$key]:'https://oxygenapp.com/wp-content/uploads/2017/05/next-design-set-main-demo.png';?>);'>
				</div>
				<div class='oxygen-vsb-design-set-install-wizard-design-sets-design-set-info'>
					<h2><?php echo $key;?></h2>
					<a href='<?php echo $site;?>' target='_blank'>View Demo</a>
				</div>
				<a href='' data-site='<?php echo $key;?>' class='oxygen-vsb-design-set-install-wizard-button oxygen-vsb-design-set-install-button'>Install</a>
			</div>

			<?php
		}
	}
	?>

		</div>

	</div>



</div>
