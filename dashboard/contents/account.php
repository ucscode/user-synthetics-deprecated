<?php 

	require_once __DIR__ . "/config.php";
	
	if( $uss_user ) 
		$universal->js_var->max_avatar_size = $uss_options->get("max_avatar_size") ?? 790; // kilobyte
	
	(new backend())->output(function() {
	
		global $uss_user, $usermeta, $universal, $uss_options, $helper;
		
		$stop_avatar_update = !!$uss_options->get('stop_avatar_update');
		$stop_email_update = !!$uss_options->get("stop_email_update");
		$get_reg_username = !!$uss_options->get("get_reg_username");
		
?>

<div class="row gy-4">

	<!--- [ PROFILE SECTION ] --->
	
	<div class="col-12 col-lg-6">
		<div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">

			<!--- [ Profile ICON ] --->
			
			<div class="app-card-header p-3 border-bottom-0">
				<div class="row align-items-center gx-3">
				
					<div class="col-auto">
						<div class="app-icon-holder">
							<i class='fas fa-user'></i>
						</div>
					</div>
					
					<div class="col-auto">
						<h4 class="app-card-title">Profile</h4>
					</div>
					
				</div>
			</div>
			
			
			<!--- [ Profile Content ] --->
			
			<div class="app-card-body px-4 pb-4 w-100">
			
				<div class="item border-bottom py-3">
					<div class="row justify-content-between align-items-center">
					
						<!--- [ update avatar form ] --->
						
						<form id="profile_avatar" onsubmit="return false" enctype="multipart/form-data">
						
							<div class="col-auto">
								<?php if( !empty($uss_user['username'])): ?>
								<div class="item-label mb-3">
									<strong><?php echo ucfirst($uss_user['username']); ?></strong>
								</div>
								<?php endif; ?>
								<div class="item-data">
									<img class="profile-image" src="<?php echo $universal->user->avatar; ?>" alt="">
								</div>
							</div><!--//col-->
							
							<?php if( !$stop_avatar_update ): ?>
							
								<div data-v-auth="profile_avatar"><?php echo (new potty())->assign("profile_avatar"); ?></div>
								
								<div class="col text-end">
									<input type="file" name="avatar" class="toast hide" accept="image/*">
									<button class="btn btn-sm app-btn-secondary" type="button" id='change-avatar'>Change</button>
									<button class="btn btn-sm app-btn-secondary" type="submit">save</button>
								</div><!--//col-->
							
							<?php endif; ?>
						</form>
						
					</div>
				</div>
				
				<!--- [ Update Email Form ] --->
				
				<form id="profile_email" onsubmit="return false">
				
					<?php 
						if( empty($uss_user['username']) && $get_reg_username ):
							$u_attr = array(
								'name' => 'username',
								'type' => 'text',
								'class' => "form-control",
								'required' => 'required',
								"pattern" => "^\s*\w{3,}\s*$"
							);
							if( !empty($uss_user['username']) ) $u_attr['readonly'] = 'readonly';
					?>
						<div class="form-group pt-3">
							<label class="form-label font-weight-500 font-size-14">
								Username
								<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="Should contain only text, numbers and underscore. Once updated, it can never be changed.">
									<i class="fas fa-info-circle"></i>
								</span>
							</label>
							<input <?php echo $helper->array_to_html_attrs($u_attr); ?>>
						</div>
					<?php endif; ?>
					
					<?php 
						$e_attr = array(
							"name" => 'email',
							'class' => 'form-control',
							'type' => 'email',
							'placeholder' => $uss_user['email'],
							'value' => $uss_user['email'],
							'required' => 'required',
						);
						if( $stop_email_update ) $e_attr['readonly'] = 'readonly';
					?>
					<div class="form-group pt-3">
						<label class="form-label font-weight-500 font-size-14">Email</label>
						<input <?php echo $helper->array_to_html_attrs($e_attr); ?>>
					</div>
				
					<?php 
					
						$show_button = !$stop_email_update || ($get_reg_username && empty($uss_user['username']));
						
						if( $show_button ): 
					?>
						
						<div class="form-group pt-3">
						   <button class="btn app-btn-secondary">Update Profile</button>
						</div>
						
						<div data-v-auth="u_profile"><?php echo (new potty())->assign("u_profile"); ?></div>
						
					<?php endif; ?>
					
				</form>
				
			</div>
			
			<!--- [ //Profile Content ] -->
		   
		</div>
	</div>

	
	<!--- [ PASSWORD SECTION ] --->
	
	<div class="col-12 col-lg-6">
		<div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
			
			<!--- [ Password ICON ] --->
			
			<div class="app-card-header p-3 border-bottom-0">
				<div class="row align-items-center gx-3">
				
					<div class="col-auto">
						<div class="app-icon-holder">
							<i class='fas fa-key'></i>
						</div>
					</div>
					
					<div class="col-auto">
						<h4 class="app-card-title">Password</h4>
					</div><!--//col-->
					
				</div>
			</div>
			
			
			<!--- [ Password Content ] --->
			
			<div class="app-card-body px-4 pb-4 w-100">
				
				<!--- [ Password Form ] --->
				
				<form id="profile_password" onsubmit="return false">
				
					<div class="form-group py-2">
						<label class="form-label font-weight-500 font-size-14">Old Password</label>
						<input type="password" name='prev-password' class="form-control" placeholder="******" required>
					</div>
					
					<div class="row">
					
						<div class="form-group py-2 col-md-6">
							<label class="form-label font-weight-500 font-size-14">New Password</label>
							<input type="password" name='new-password' class="form-control" placeholder="" required>
						</div>
						
						<div class="form-group py-2 col-md-6">
							<label class="form-label font-weight-500 font-size-14">Confirm Password</label>
							<input type="password" id="new-password" class="form-control" placeholder="" required>
						</div>
						
					</div>
					
					<div data-v-potty="profile_password"><?php echo (new potty())->assign("profile_password"); ?></div>
					
					<div class="form-group pt-3 mt-auto text-md-right">
						<button class="btn app-btn-secondary">
							Update Password
						</button>
					</div>
				
				</form>
				
			</div>
		   
			<!--- [ //Password Content ] --->
			
		</div>
	</div>

</div><!--//row-->


<!--- [ PROFILE JAVASCRIPT ] --->

	<?php events::addListener("backend-foot", function() use($helper) { ?>
		<script type="text/javascript" src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/js/userprofile.js"; ?>"></script>
	<?php }); ?>

<!--- [ //PROFILE JAVASCRIPT ] --->

<?php });