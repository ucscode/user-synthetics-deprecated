<?php 

# --- [ send email confirmation link ] ---

$universal->methods->verify_email = function( int $userid ) {
	
	global $helper, $uss_options, $universal;
	
	$site_name = $uss_options->get("site_name"); 
	
	$activation_key = $helper->generate_activation_key( $userid );
	
	$the_link = $helper->server_to_url( ROOT_PATH ) . "/login?verify={$activation_key}&u={$userid}" ;
	
	$intro = ( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username']) ) ? "%{username}" : "Dear";
	
	$message = "
		<p>Hi {$intro},</p>
		<p>Thank you for signing up to {$site_name}. To continue, please click the following link to <a href='{$the_link}'>verify this email address</a>.</p>
		<p>If the above link cannot be clicked, please copy-and-paste the URL below into an open web browser to complete the verification process:</p>
		<p><a href='{$the_link}'>{$the_link}</a></p>
		<p>This email was sent to %{email}. If you don't recognize this activity, please ignore the link</p>
		<p>Regards from {$site_name}</p>
	";
	
	$user = $helper->_data( DB_PREFIX . 'users', $userid );
	
	if( !$user ) return false;
	
	$PHPMailer = $helper->PHPMailer_Instance();
	$PHPMailer->setFrom( $uss_options->get("site_email"), $uss_options->get("site_name") );
	$PHPMailer->addAddress( $user['email'] );
	$PHPMailer->Body = $helper->dedicated_user_string($message, $userid);
	$PHPMailer->Subject = "Verify Your Email";
	
	return $PHPMailer->send();
	
};


# --- [ verify the email confirmation link ] ---

$universal->methods->confirm_verification = function( string $status ) {
	$urlget = [];
	$helper = new helper();
	foreach( $_GET as $key => $value ) $urlget[$key] = $helper->sanitize($value);
	$activation_key = $urlget['verify'] ?? null;
	$userid = $urlget['u'] ?? null;
	if( !$userid || !$activation_key ) return;
	global $ucsqli;
	$user = $helper->_data( DB_PREFIX . 'users', $userid );
	if( !$user ) return 0;
	if( $user['status'] != 'unverified' ) return;
	else if( $user['activation_key'] != $activation_key ) return 0;
	$result = $ucsqli->update( DB_PREFIX . 'users', array("status" => $status), "id={$user['id']}" );
	return $result;
};


# --- [ send reset password email ] ---

$universal->methods->reset_password = function( string $userid ) {
	
	global $usermeta;
	
	$helper = new helper();
	$user = $helper->_data( DB_PREFIX . "users", $userid );
	if( !$user ) return false;
	
	$reset_key = substr(sha1($helper->keygen()), 0, 20);
	$insert_key = $usermeta->set("reset.password", $reset_key, $user['id']);
	$the_link = $helper->server_to_url( ROOT_PATH ) . "/reset-password?verify=%{:reset.password}&u={$user['id']}";
	
	$INTRO = !empty($user['username']) ? "Hi %{username}" : "Hello again";
	
	$message = "
		<p>{$INTRO},</p>
		<p>We received a request to reset the password of your account. If you are aware of this request, please click the link below to proceed:</p>
		<p><a href='{$the_link}'>{$the_link}</a></p>
		<p>Please note that the link will expire in one hour</p>
	";
	
	$PHPMailer = $helper->PHPMailer_Instance();
	$PHPMailer->addAddress( $user['email'] );
	$PHPMailer->Subject = "Reset Password";
	$PHPMailer->Body = $helper->dedicated_user_string($message, $user['id']);
	
	return $PHPMailer->send();
	
};


$universal->methods->new_usercode = function() {
	global $helper;
	do {
		$usercode = substr(sha1($helper->keygen()), 0, 7);
		$not_unique = $helper->_data( DB_PREFIX . 'users', $usercode, 'usercode' );
	} while( $not_unique );
	return $usercode;
};