<?php require __DIR__ . "/config.php";

$backend = new backend();

## -- [ get the user ] --

if( !isset($_GET['ucode']) ) $_GET['ucode'] = '-undefined-';

$universal->temp->user = $helper->_data( DB_PREFIX . 'users', $_GET['ucode'] );
	
	
## -- [ if no user, set blank page ] --

$backend->blank( !$universal->temp->user );


## -- [ add authentication code to javascript for only admin ] --

if( $uss_user && $uss_user['role'] == 'admin' ) 
	$universal->js_var->otp = (new potty())->assign("um_auth");


$backend->title("edit user")->output(function($backend) {
	
	global $universal, $helper, $usermeta;
	
	if( $universal->temp->user ): 
	
		events::exec("admin.layout:user_edit:start");
	
?>

	<div class="row gy-4">
	
		<div class="col-12 col-lg-8">
			<div class="app-card app-card-account shadow-sm ">

				<div class='app-card-header p-3 w-100 text-end'>
					<h5 class='text-header'>
						usercode: <?php echo $universal->temp->user['usercode']; ?>
					</h5>
				</div>
				
				<form method="" id='user-edit-form'>
				
					<div class="app-card-body p-4 w-100">
		
						<div class='row align-items-center mb-4'>
							<label class='form-label col-sm-3 col-md-2'>Username:</label>
							<div class='col-sm-9 col-md-8 col-lg-10'>
								<?php
									$uattr = array(
										'value' => $universal->temp->user['username'],
										'type' => 'text',
										'class' => 'form-control'
									);
									if( !empty($uattr['value']) ) $uattr['disabled'] = 'disabled';
									else {
										$uattr['name'] = 'username';
										$uattr['pattern'] = "^\\s*\\w{3,}\\s*$";
									};
								?>
								<input <?php echo $helper->array_to_html_attrs($uattr); ?>>
							</div>
						</div>
					
						<div class='row align-items-center mb-4'>
							<label class='form-label col-sm-3 col-md-2'>Role:</label>
							<div class='col-sm-9 col-md-8 col-lg-10'>
								<select class='form-select' name='role'>
									<?php 
										foreach( $universal->site->roles as $role ): 
											$selected = ( $role == $universal->temp->user['role'] ) ? 'selected' : null;
									?>
									<option value="<?php echo $role; ?>" <?php echo $selected; ?>>
										<?php echo $role; ?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					
						<div class='row align-items-center mb-4'>
							<label class='form-label col-sm-3 col-md-2'>Email:</label>
							<div class='col-sm-9 col-md-8 col-lg-10'>
								<input class='form-control' name='email' type='email' value='<?php echo $universal->temp->user['email']; ?>' required>
							</div>
						</div>
					
						<div class='row align-items-center mb-4'>
							<label class='form-label col-sm-3 col-md-2'>Status:</label>
							<div class='col-sm-9 col-md-8 col-lg-10'>
								<select class='form-select' name='status'>
									<?php 
										foreach( ['verified', 'unverified'] as $status ):
										$selected = ( $status == $universal->temp->user['status'] ) ? 'selected' : null;
									?>
									<option value="<?php echo $status; ?>" <?php echo $selected; ?>>
										<?php echo ucfirst($status); ?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					
						<?php events::exec("admin.layout:user_edit:input"); ?>
						
						<div class='align-items-center mb-4'>
							<input type="hidden" name="block" value="0">
							<?php 
								$blocked = $usermeta->get('user.blocked', $universal->temp->user['id']);
								if( $blocked ) $blocked = 'checked';
							?>
							<input type="checkbox" name="block" class="form-check-input" id="fci" <?php echo $blocked; ?> value='1'>
							<label class='form-label form-check-label col-3 col-md-2 ms-2' for="fci">Blocked</label>
						</div>
						
						<div data-v-auth="um_auth"><?php echo (new potty())->assign("um_auth"); ?></div>
						<input type='hidden' name='action' value='edit'>
						<input type='hidden' name='uids[]' value='<?php echo $universal->temp->user['id']; ?>'>
						
					</div>
					
					<div class="app-card-footer p-4 mt-auto">
						<button class="btn app-btn-secondary" type="submit">
							Save Changes
						</button>
					</div><!--//app-card-footer-->
			   
				</form>
				
			</div><!--//app-card-->
		</div>
	
		<div class="col-12 col-lg-4">
			<div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
				
				<div class='app-card-header p-3 w-100 text-end'>
					<h5 class='text-header'>Details</h5>
				</div>
				
				<div class='app-card-body p-4'>
					<?php 
					
						$userinfo = [
						
							array(
								'far fa-star', 
								'role', 
								$universal->temp->user['role']
							),
							
							array(
								'far fa-gem', 
								'status', 
								$universal->temp->user['status']
							),
							
							array(
								'fas fa-shield', 
								'blocked', 
								$blocked ? "yes" : "no"
							),
							
							array(
								'fas fa-circle', 
								'online', 
								(int)$universal->temp->user['last_seen'] > strtotime("-{$universal->temp->last_seen_minute} minutes") ? 'Yes' : "No"
							),
							
							array(
								'fas fa-user-plus', 
								'registered', 
								(new dateTime())->setTimestamp($universal->temp->user['register_time'])->format($universal->site->datetime_format)
							),
							
							array(
								'fas fa-eye', 
								'last seen', 
								(int)((time() - (int)$universal->temp->user['last_seen']) / 3600 ) . " hrs ago"
							),
							
							array(
								'fas fa-location', 
								'Login IP', 
								$universal->temp->user['remote_addr']
							),
							
						];
						
						foreach( $userinfo as $info ):
					?>
					<p>
						<i class='<?php echo $info[0]; ?>'></i> - 
						<?php echo ucwords($info[1]) . ": " . ucfirst($info[2]); ?>
					</p>
					<?php endforeach; ?>
					
					<?php events::exec("admin.layout:user_edit:info"); ?>
					
					<hr class='mb-4'>
					<button class='btn btn-danger w-100' data-temp-user-delete="<?php echo $universal->temp->user['id']; ?>" data-return="users_list">
						Delete user
					</button>
				</div>
				
			</div>
		</div>
		
	</div>
	
	<?php 
	
		events::addListener("backend-foot", function() use($helper) {
			$src = $helper->server_to_url( ADMIN_PATH ) . "/assets/js/users.js";
			echo "<script src='{$src}'></script>";
		});
		
		events::exec("admin.layout:user_edit:end"); 

	else:

		$users_list = $helper->server_to_url( ADMIN_PATH . "/users_list" );
		
		$button = "
			<a class='btn btn-primary' href='{$users_list}'>
				Return to list
			</a>
		";
		
		$backend->liteDOM->billboard("OOPS!", "No user was found", trim($button));
		
	endif;
	
});