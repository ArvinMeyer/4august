<?php

if ( isset( $_POST["ct_typekit_token"] ) ) {
	update_option( "ct_typekit_token", trim( $_POST["ct_typekit_token"] ) );
}

$token = get_option( "ct_typekit_token", "" );

if ( isset( $_POST["ct_typekit_kit_id"] ) && $token ) {
	update_option( "ct_typekit_kit_id", trim( $_POST["ct_typekit_kit_id"] ) );

	$kit_response = wp_remote_get( 'https://typekit.com/api/v1/json/kits/'.trim( $_POST["ct_typekit_kit_id"] ).'/published?token='.$token, 
			array( 	'timeout' => 120, 
					'httpversion' => '1.1' ) );
	$kit_data = json_decode( $kit_response['body'], true );
	$kit_data = $kit_data['kit'];

	$domain = $_SERVER["SERVER_NAME"];

	// add current domain to the kit if needed
	if ( is_array( $kit_data["domains"] ) && !in_array( $domain, $kit_data["domains"] ) ) {
		
		if ( count( $kit_data["domains"] ) < 10 ) {
			$kit_data["domains"][] = $domain;

			$typekit_response = wp_remote_post( 'https://typekit.com/api/v1/json/kits/'.trim( $_POST["ct_typekit_kit_id"] ).'?token='.$token, 
				array( 	'timeout' => 120, 
						'httpversion' => '1.1',
						'body' => array( 
									'domains' => $kit_data["domains"], 
								) 
						) 
				);

			// publish the kit
			$typekit_response = wp_remote_post( 'https://typekit.com/api/v1/json/kits/'.trim( $_POST["ct_typekit_kit_id"] ).'/publish?token='.$token);

			$response_data = json_decode( $typekit_response['body'], true );

			if ( $response_data['published'] ) { ?>

				<div class="notice notice-warning">
					<p><?php printf( __("We have added <b>%s</b> to the kit's list of allowed domains. It may take up to 5 minutes for Typekit's servers to update and the fonts to be available for use with Oxygen.", "component-theme"), $domain ); ?></p>
				</div>

			<?php }
		}
		else { ?>

			<div class="notice notice-warning">
				<p><?php printf( __("<b>%s</b> cannot be added to the list of allowed domains due to Typekit's 10 domain per kit limit. Please login to your Typekit account, remove a domain from the kit, and manually add <b>%s</b> to the kit.", "component-theme"), $domain ); ?></p>
			</div>

		<?php }
	}
}

// Get user kits
if ( $token ) {
	$response = wp_remote_get( 'https://typekit.com/api/v1/json/kits?token='.$token, 
		array( 	'timeout' => 120, 
				'httpversion' => '1.1' ) );

	$response = json_decode( $response["body"], true );
}

echo "<pre>";
//var_dump( $response );
echo "</pre>";

?>

<div class="wrap">

	<h2><?php _e("Adobe Typekit Options", "component-theme"); ?></h2>
	<p class="description">
		<a target="_blank" href="https://oxygenapp.com/user-guide/typekit"><?php _e("Instructions on setting up Typekit with Oxygen", "component-theme"); ?></a>
	</p>

	<?php if ( isset( $response["errors"] ) && $response["errors"] ) : ?>
		
		<div id="message" class="error notice below-h2">
		<?php foreach ( $response["errors"] as $error ) : ?>
			<p><?php echo $error; ?></p>
		<?php endforeach; ?>
		</div>
	
	<?php endif; ?>

	<form action="" method="post">
		<p>
			<?php _e( "Typekit API Token", "component-theme" ); ?> <input type="text" size="45" name="ct_typekit_token" value="<?php echo $token; ?>"> 
		</p>

		<div>
			<?php if ( isset( $response["kits"] ) && $response["kits"] ) : 

				$kit_id = get_option( "ct_typekit_kit_id", "" ); 
				
				?>
				
				<h3><?php _e("Kit to use with Oxygen", "component-theme"); ?></h3>

				<select name="ct_typekit_kit_id">
					<option value=""></option>
					<?php foreach ( $response["kits"] as $kit ) : 

						$kit_response = wp_remote_get( 'https://typekit.com/api/v1/json/kits/'.$kit["id"].'?token='.$token, 
										array( 	'timeout' => 120, 
												'httpversion' => '1.1' ) );

						$kit_data = json_decode( $kit_response['body'], true );
						$kit_data = $kit_data['kit'];
						?>
						<option value="<?php echo $kit["id"]; ?>" <?php selected( $kit_id, $kit["id"] );?>>
							<?php echo $kit_data["name"] . " (" . implode(", ", $kit_data["domains"]) . ")"; ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</div>

		<p>
			<input type="submit" class="button button-primary" value="<?php _e("Submit", "component-theme"); ?>" name="submit">
		</p>
	
	</form>
</div>