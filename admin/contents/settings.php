<?php 

	require __DIR__ . "/config.php";
	
	(new backend())->title("settings")->output(function() {
		
		global $universal, $helper, $uss_options;
		
?>

	<!------------------------------ [ THE ENTRY FORM ] --------------------------->
	
	<hr class="mb-4">
	
	<div class="row g-4 settings-section">
	
		<div class="col-12 col-md-4">
			<h3 class="section-title">Basic</h3>
			<div class="section-intro">
				The basic settings contains information about this website.
			</div>
		</div>
		
		<div class="col-12 col-md-8">
			<div class="app-card app-card-settings shadow-sm p-4">
				
				<div class="app-card-body">
					<!--- FORM ELEMENT --->
					<form class="settings-form" onsubmit="return false" id="__basic" enctype="multipart/form-data">
						<div class="mb-3">
							<label class="form-label">
								Site Name
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The official name of this platform">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="text" class="form-control" name='basic[site_name]' value="<?php echo $uss_options->get("site_name"); ?>" required>
						</div>
						<div class="mb-3">
							<label class="form-label">
								Headline
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The business email of this platform.">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="text" class="form-control" name='basic[site_headline]' value="<?php echo $uss_options->get("site_headline"); ?>">
						</div>
						<div class="mb-3">
							<label class="form-label">
								Icon
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The official logo of this platform">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<div class="py-2 row">
								<div class='col-6'>
									<img src="<?php echo $uss_options->get("site_icon") ?? $universal->site->logo; ?>" class="img-fluid img-thumbnail --icon" data-filer>
								</div>
								<div class='col-6 text-end'>
									<input type="file" class="form-control" name='site_icon' style='display: none;' data-filer accept="image/jpg, image/jpeg, image/png, image/gif">
									<button class="btn btn-sm btn-outline-secondary btn-mini" type="button" data-filer>
										Upload
									</button>
								</div>
							</div>
						</div>
						<hr class="">
						<div class="mb-3">
							<label class="form-label">
								Description
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="About this business">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<textarea class='form-control' rows="4" name='basic[site_description]'><?php echo $uss_options->get("site_description"); ?></textarea>
						</div>
						<div data-v-potty="as-auth"><?php echo (new potty())->assign("admin-settings"); ?></div>
						<button type="submit" class="btn app-btn-primary" >Save Changes</button>
					</form>
				</div><!--//app-card-body-->
				
			</div><!--//app-card-->
		</div>
	</div><!--//row-->	
	
	
	<!-------------------------- [ THE NEXT FORM ] -------------------------->
	
	<hr class="mb-4">
	
	<div class="row g-4 settings-section">
	
		<div class="col-12 col-md-4">
			<h3 class="section-title">Geography</h3>
			<div class="section-intro">
				This is required for accurate statistics measurement such as registration date overviews.
			</div>
		</div>
		
		<div class="col-12 col-md-8">
			<div class="app-card app-card-settings shadow-sm p-4">
				
				<div class="app-card-body">
					<!--- FORM ELEMENT --->
					<form class="settings-form" onsubmit="return false" id="__geography" enctype="multipart/form-data">
						<div class="mb-3">
							<label class="form-label">
								Country
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The country where you run this business">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<select class="form-select" name='geo[site_country]' required>
								<?php 
									$selected_country = $uss_options->get("site_country");
									foreach( $helper->get_countries() as $country ): 
										$selected = ( $selected_country == $country['iso_2'] ) ? "selected" : null;
								?>
								<option value="<?php echo $country['iso_2']; ?>" <?php echo $selected; ?>>
									<?php echo $country['name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="mb-3">
							<div class="alert alert-dark font-size-12 text-center">
								You may need to reload this page after saving to see your current timezone.
							</div>
							<?php 
								if( is_null($selected_country) ) $timezone = "...";
								else $timezone = "&mdash; " . $helper->get_timezone_by_country( $selected_country );
							?>
							Your current timezone is <span class='font-weight-500'><?php echo $timezone; ?></span>
						</div>
						<div data-v-potty="as-auth"><?php echo (new potty())->assign("admin-settings"); ?></div>
						<button type="submit" class="btn app-btn-primary" >Save Changes</button>
					</form>
				</div><!--//app-card-body-->
				
			</div><!--//app-card-->
		</div>
	</div><!--//row-->

	
	<!----------------------------- [ THE NEXT FORM ] ----------------------------->
	
	
	<hr class="mb-4">
	
	<div class="row g-4 settings-section">
	
		<div class="col-12 col-md-4">
			<h3 class="section-title">Email</h3>
			<div class="section-intro">
				The email settings is used to send mails from this platform. Thus, contains information about Mail Server. If the SMTP information is incorrectly set, mail from this platform will always fail.
			</div>
		</div>
		
		<div class="col-12 col-md-8">
			<div class="app-card app-card-settings shadow-sm p-4">
				
				<div class="app-card-body">
					<!--- FORM ELEMENT --->
					<form class="settings-form" id="__smtp" onsubmit="return false">
						<div class="mb-3">
							<label class="form-label">
								Business Email
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The official email address by which people can request support">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="email" class="form-control" name="email[site_email]" value="<?php echo $uss_options->get("site_email"); ?>" placeholder="e.g admin@<?php echo $_SERVER['SERVER_NAME']; ?>">
						</div>
						<div class="mb-3">
							<label class="form-label">
								Email Per Hour
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The max number of emails that can be sent in an hour. Every server has email sending limit. Trying to send mails beyond that will cause further email to bounce back or be marked as spam">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="number" class="form-control" name="email[emails_per_hour]" value="<?php echo $uss_options->get("emails_per_hour"); ?>" placeholder="e.g 50">
						</div>
						<hr class="">
						<div class="alert alert-primary font-size-13 text-center">
							The information below is only required if mails need to be sent from SMTP.
						</div>
						<div class="mb-3">
							<label class="form-label">
								SMTP Server
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The SMTP server (URL)">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="text" class="form-control" name="email[smtp_server]" placeholder="e.g smtp.gmail.com" value="<?php echo $uss_options->get("smtp_server"); ?>">
						</div>
						<div class="mb-3">
							<label class="form-label">
								SMTP Login
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The email address of the SMTP server">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="email" class="form-control" name="email[smtp_login]" placeholder="e.g user@email.com" value="<?php echo $uss_options->get("smtp_login"); ?>">
						</div>
						<div class="mb-3">
							<label class="form-label">
								SMTP Password
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The password to connect to the SMTP server">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="text" class="form-control" name="email[smtp_password]" placeholder="..." value="<?php echo $uss_options->get("smtp_password"); ?>" >
						</div>
						<div class="mb-3">
							<label class="form-label">
								SMTP Port
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The port of the SMTP server. Standard is 25">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="number" class="form-control" name="email[smtp_port]" placeholder="e.g 25" value="<?php echo $uss_options->get("smtp_port"); ?>">
							<div data-v-potty="as-auth"><?php echo (new potty())->assign("admin-settings"); ?></div>
						</div>
						<button type="submit" class="btn app-btn-primary" >Save Changes</button>
					</form>
				</div><!--//app-card-body-->
				
			</div><!--//app-card-->
		</div>
	</div><!--//row-->
	
	
	<!----------------------------- [ THE NEXT FORM ] ----------------------------->
	
	
	<hr class="mb-4">
	
	<div class="row g-4 settings-section">
	
		<div class="col-12 col-md-4">
			<h3 class="section-title">User Profile</h3>
			<div class="section-intro">
				This manages info required from new users. By default, only Email is collected at the point of registration
			</div>
		</div>
		
		<div class="col-12 col-md-8">
			<div class="app-card app-card-settings shadow-sm p-4">
				
				<div class="app-card-body">
					<!--- FORM ELEMENT --->
					<form class="settings-form" id="__form_profile" onsubmit="return false">
						<div class="form-check mb-3">
							<input type="hidden" name="users[disable_signup]" value="0">
							<input class="form-check-input" type="checkbox" value="1" <?php if( $universal->site->disable_signup ) echo 'checked'; ?> name="users[disable_signup]" id="disable-signup">
							<label class="form-check-label" for="disable-signup">
								Disable Signup
							</label>
						</div>
						<div class="form-check mb-3">
							<input type="hidden" name="users[get_reg_username]" value="0">
							<input class="form-check-input" type="checkbox" value="1" <?php if( $uss_options->get("get_reg_username") ) echo 'checked'; ?> name="users[get_reg_username]" id="get-reg-username">
							<label class="form-check-label" for="get-reg-username">
								Collect username at registration
							</label>
						</div>
						<div class="form-check mb-3">
							<input type="hidden" name="users[confirm_user_reg_email]" value="0">
							<input class="form-check-input" type="checkbox" value="1" <?php if( $uss_options->get("confirm_user_reg_email") || $universal->site->confirm_user_reg_email ) echo 'checked'; ?> name="users[confirm_user_reg_email]" id="confirm-user-reg-email">
							<label class="form-check-label" for="confirm-user-reg-email">
								Confirm users email on registration
							</label>
						</div>
						<div class="form-check mb-3">
							<input type="hidden" name="users[stop_email_update]"  value="0">
							<input class="form-check-input" type="checkbox" value="1" <?php if( $uss_options->get("stop_email_update") ) echo 'checked'; ?> name="users[stop_email_update]" id="stop-email-update">
							<label class="form-check-label" for="stop-email-update">
								Stop users from updating their email
							</label>
						</div>
						<div class="form-check mb-3">
							<input type="hidden" value="0" name="users[stop_avatar_update]">
							<input class="form-check-input" type="checkbox" value="1" <?php if( $uss_options->get("stop_avatar_update") ) echo 'checked'; ?> name="users[stop_avatar_update]" id="stop-avatar-update">
							<label class="form-check-label" for="stop-avatar-update">
								Stop users from changing avatar
							</label>
						</div>
						<div class="mb-3">
							<label class="form-label">
								Registration Role
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="The default user role at the point of registration">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<select class="form-control" name="users[default_user_role]" value="<?php echo $uss_options->get("default_user_role"); ?>">
								<?php 
									foreach( $universal->site->roles as $role ): 
										if( $role == 'admin' ) continue;
										$selector = ( $uss_options->get("default_user_role") == $role ) ? "selected" : null;
								?>
									<option value="<?php echo $role; ?>" <?php echo $selector; ?>>
										<?php echo ucwords($role); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="mb-3">
							<label class="form-label">
								Avatar Upload: Max Size
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="Maximum file size of image that a user can upload as avatar. Measured in Kilobytes">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input type="number" class="form-control" name="users[max_avatar_size]" value="<?php echo $uss_options->get("max_avatar_size"); ?>" placeholder="100 KB">
						</div>
						<div data-v-potty="as-auth"><?php echo (new potty())->assign("admin-settings"); ?></div>
						<div class="mt-3">
							<button type="submit" class="btn app-btn-primary" >Save Changes</button>
						</div>
					</form>
				</div><!--//app-card-body-->
				
			</div><!--//app-card-->
		</div>
	</div><!--//row-->
	
	
	<!----------------------------- [ THE NEXT FORM ] ----------------------------->
		
		
	<hr class="my-4"> <!--- [] --->
	
	<?php events::exec("admin.layout:settings"); ?>

<?php 
	
	# -- [ add javascript ]
	
	events::addListener("backend-foot", function() use($helper) {
		$script_path = $helper->server_to_url( ADMIN_PATH ) . "/assets/js";
		echo "<script src='{$script_path}/settings-general.js'></script>";
	});

});
