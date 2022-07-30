<?php require_once __DIR__ . "/config.php";

	(new backend())->output(function() {
		
		global $universal, $helper, $uss_options;
		
		$r_username = $uss_options->get("get_reg_username") ?? false;
		
		events::exec("admin.layout:users_add:start");
		
?>

	<div class="row">
		<div class="col-lg-7">
			<div class="app-card app-card-chart shadow-sm">
			
				<div class="app-card-header p-3">
					<div class="row justify-content-between align-items-center">
						<div class="col-auto">
							<h4 class="app-card-title">
								<i class='fas fa-user-plus me-2'></i> Add User
							</h4>
						</div><!--//col-->
					</div>
				</div>
				
				<div class="app-card-body p-3 p-lg-4">
					<form id="user-add-form">
					
						<div class="mb-3 d-flex">   
							<div class="ms-auto">
								<label class="form-label me-1">Role:</label>
								<select class="form-select form-select-sm d-inline-flex w-auto" name="role">
								<?php 
									$default_role = $uss_options->get("default_user_role") ?? 'member';
									foreach( $universal->site->roles as $role ): 
										$select = ($role == $default_role) ? 'selected' : null;
								?>
									<option value="<?php echo $role; ?>" <?php echo $select; ?>>
										<?php echo ucfirst($role); ?>
									</option>
								<?php endforeach; ?>
								</select>
							</div>
						</div>
						
						<div class="mb-3">
							<label class="form-label">Username:</label>
							<input type="text" name="username" class="form-control" <?php if( $r_username ) echo "required=''"; ?>>
						</div>
						<div class="mb-3">
							<label class="form-label">Email:</label>
							<input type="email" name="email" class="form-control" placeholder="required" required>
						</div>
						<div class="mb-3">
							<label class="form-label">Password:</label>
							<input type="text" name="password" class="form-control" placeholder="required" required>
						</div>
						
						<div class="mb-3">
							<label class="form-label me-1">Status:</label>
							<select class="form-select" name="status">
								<option value="unverified">unverified</option>
								<option value="verified">verified</option>
							</select>
						</div>
						
						<div class="form-check mb-3 border-bottom py-3">
							<label class="form-check-label" for="notice">Notify user by email</label>
							<input type="hidden" name="notify" value="0">
							<input type="checkbox" name="notify" class="form-check-input" id="notice" value="1">
						</div>
						
						<?php events::exec("admin.layout:users_add:input"); ?>
						
						<div data-v-potty="um_auth"><?php echo (new potty())->assign("um_auth"); ?></div>
						<input type="hidden" name="action" value="create">
						
						<div class="text-end">
							<button class="btn btn-success" type="submit">
								Create account
							</button>
						</div>
						
					</form>
				</div><!--//app-card-body-->
				
			</div>
		</div>
		
		<div class="col-lg-5">
			<?php events::exec("admin.layout:users_add:col_lg_5"); ?>
		</div>
		
	</div>
	
<?php 

	events::exec("admin.layout:users_add:end");
	
	events::addListener("backend-foot", function() use($helper) {
		$src = $helper->server_to_url( ADMIN_PATH ) . "/assets/js/users.js";
		echo "<script src='{$src}'></script>";
	});
	
});

