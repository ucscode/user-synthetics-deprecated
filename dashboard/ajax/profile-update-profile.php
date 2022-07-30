<?php

(defined('ROOT_PATH') && !empty($uss_user) ) or die;


# --- [ success message ] ---

$universal->ajax->message = "<i class='fas fa-check'></i> - Profile updated";


# --- [ avoid spam ] ---

$inspect = (new potty())->auth( "u_profile", $_POST['u_profile'] ?? '', true, true );


# --- [ update username if empty ] ---

if( empty($uss_user['username']) && !empty($_POST['username']) ) {
	
	$valid = preg_match($helper->regex('word'), $_POST['username']);
	
	if( !$valid )
		$universal->ajax->error = "<i class='fas fa-question'></i> - Username is not valid";
	
	else {
		$exists = $ucsqli->select( DB_PREFIX . "users", 'username', "username = '{$_POST['username']}'" )->num_rows;
		if( $exists ) {
			$universal->ajax->error = "<i class='fas fa-exclamation'></i> - The username already exists";
		};
	};
	
	if( !$universal->ajax->error ) {
		
		$updated = $ucsqli->update( 
			DB_PREFIX . 'users', 
			array( 'username' => $_POST['username'] ), 
			"id={$uss_user['id']}" 
		);
		
		if( !$updated ) 
			$universal->ajax->error = "<i class='fas fa-check'></i> - The username could not be updated";
		
	};
	
	if( $universal->ajax->error )
		$helper->jsonify( false, $universal->ajax->error, null, true );
	
};


# -- [ Temporary Confirmation Message ] --

$universal->temp->confirm_new_email = function( $keygen ) {
	global $uss_user, $helper, $universal;
	$link = $helper->server_to_url( ROOT_PATH . "/{$universal->temp->system_page}?content=email&uid={$uss_user['id']}&code={$keygen}" );
	$message = "
		<p>Hi %{username},</p>
		<p>You made a request to update your email. Please click the link below to verify this email address in {$universal->site->name}</p>
		<p><a href='{$link}'>{$link}</a></p>
	";
	$PHPMailer = $helper->PHPMailer_Instance();
	$PHPMailer->Body = $helper->dedicated_user_string($message, $uss_user['id']);
	$PHPMailer->Subject = "Confirm Email";
	$PHPMailer->addAddress( $_POST['email'] );
	return $PHPMailer->send();
};


# --- [ update email ] ---

if( $uss_user['email'] != $_POST['email'] ) {

	$in_use = $helper->_data( DB_PREFIX . 'users', $_POST['email'], 'email' );
	
	if( $in_use ) {
		
		$universal->ajax->message = "<i class='fas fa-exclamation-circle'></i> - Email already in use";
		$universal->ajax->status = false;
		
	} else {
		
		# -- [ save as temporary email ] --
		$temp_email = $usermeta->set("user.email", $_POST['email'], $uss_user['id']);

		# -- [ generate activation key ] --
		$keygen = $helper->generate_activation_key( $uss_user['id'] );

		if( $temp_email && $keygen ) {
			
			$universal->ajax->status = ($universal->temp->confirm_new_email)($keygen);
			
			if( !$universal->ajax->status ) {
				$universal->ajax->message = "<i class='fas fa-time'></i> - Something went wrong";
			} else $universal->ajax->message .= "<br> <i class='fas fa-envelope'></i> - A confirmation message has been sent to the new email you submitted";

		} else $universal->ajax->message = "<i class='fas fa-times'></i> - The email address could not be updated";

	};
	
} else $universal->ajax->status = true;


