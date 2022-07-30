<?php defined("AJAX_MODE") or DIE;

$universal->ajax->message = "<i class='fas fa-link'></i> - A password reset link has been sent to your email";


# --- [ check for spam ] ---

$inspect = (new potty())->auth( 'reset-password', $_POST['auth'] ?? '', false, true );


# --- [ get data ] ---

$_POST['email'] = strtolower( $_POST['email'] );

if( !preg_match($helper->regex("email"), $_POST['email']) ) 
	$helper->jsonify( false, "<i class='fas fa-ban'></i> - The email address is not valid", null, true );

$user = $helper->_data( DB_PREFIX . "users", $_POST['email'], 'email' );

if( !$user ):
	$helper->jsonify( false, 
		"
			<i class='fas fa-question-circle'></i> 
			- No account with such email was found!
		", null, 
		true );
endif;


# --- [ execute the process ] ---

$universal->ajax->status = ($universal->methods->reset_password)( $user['id'] );

if( !$universal->ajax->status ) 
	$universal->ajax->message = "<i class='fas fa-times'></i> - The request was not successful";


