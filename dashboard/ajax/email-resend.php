<?php 

( defined("AJAX_MODE") && isset($_POST['email']) ) OR DIE;


# -- [ authenticate ] --

(new potty())->auth( "re-mail", $_POST['re-mail'] ?? '', false, true );


# -- [ validate email ] --

if( !preg_match($helper->regex('email'), $_POST['email']) ) 
	$universal->ajax->message = "Invalid email address";

else {
	
	# -- [ check account ] --

	$user = $helper->_data( DB_PREFIX . 'users', $_POST['email'], 'email' );

	if( !$user ) $universal->ajax->message = "No account is associated to the email";

	# -- [ confirm verification ] --

	else if( $user['status'] != 'unverified' )
		$universal->ajax->message = "The email address has already been confirmed";
	
	# -- [ send confirmation email ] --
	
	else {
		
		if( $universal->ajax->status = ($universal->methods->verify_email)( $user['id'] ) ) {
			
			$universal->ajax->message = "
				<p>
					<i class='far fa-envelope'></i> 
					- A confirmation link has been sent to your email address.
				</p>
			";
			
		} else {
			$universal->ajax->message = "
				<p>
					<i class='far fa-exclamation-triangle'></i> 
					- Confirmation email could not be sent!.
				</p>
			";
		}
		
	};

}