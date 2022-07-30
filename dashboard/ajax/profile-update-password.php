<?php 

(defined("ROOT_PATH") && $uss_user) OR DIE;

$inspect = (new potty())->auth( "profile_password", $_POST['profile_password'] ?? '', true, true );


# -- [ get ready ] --;

foreach( $_POST as $key => $value ) {
	# --- [ Let's work it out ] ---
	$_POST[ $key ] = $helper->passify( $value );
};


# -- [ test & secure password ] --

if( $_POST['prev-password'] !== $uss_user['password'] )
	
	$universal->ajax->error = "<i class='fas fa-times'></i> - Old password is wrong";
	
else if( $_POST['new-password'] == $uss_user['password'] )
	
	$universal->ajax->error = "<i class='fas fa-exclamation-circle'></i> - Old and new password cannot be the same.";


if( !$universal->ajax->error ):

	# -- [ update password ] --;

	$universal->ajax->status = $ucsqli->update( 
		DB_PREFIX . "users", 
		array("password" => $_POST['new-password']), 
		"id={$uss_user['id']}" 
	);

	$universal->ajax->message = ( $universal->ajax->status ) ? "<i class='fas fa-lock'></i> - Password updated successfully" : "<i class='fas fa-unlock'></i> - Password update failed";

else:

	$universal->ajax->message = $universal->ajax->error;
	
endif;


