<?php 

defined("ROOT_PATH") or die;

# -- [ authenticate ] --  

$status = (new potty())->auth( "signup", $_POST['auth'] ?? '', false, true );

unset($_POST['auth']);


# -- [ prevent registration ] --

if( $universal->site->disable_signup ) 
	$helper->jsonify( false, "<i class='fas fa-shield'></i> - Sorry! Registration is closed", null, true );


# -- [ case insensitivity ] --

foreach( $_POST as $key => $value ) {
	$_POST[$key] = $helper->sanitize($value);
	if( in_array($key, ['username', 'email']) ) $_POST[$key] = strtolower($_POST[$key]);
};


# -- [ validate user email ] --

if( !preg_match( $helper->regex('email'), $_POST['email'] ) ) 
	$helper->jsonify( false, "<i class='fas fa-ban'></i> - The email address is not valid", null, true );


$db_users = DB_PREFIX . "users";

# --- [ check if email exists ] ---;

$result = $helper->_data( $db_users, $_POST['email'], 'email' );
if( $result) {
	$helper->jsonify( false, "The email already exists", null, true );
};


# --- [ check if username exists ] ---;

if( isset($_POST['username']) ) {
	
	if( !preg_match( $helper->regex('word'), $_POST['username'] ) )
		$helper->jsonify( false, "<i class='fas fa-ban'></i> - The username is not valid", null, true );
	
	$result = $helper->_data( $db_users, $_POST['username'], 'username' );
	if( $result ) $helper->jsonify( false, "The username already exists", null, true );
	
};


# --- [ insert into database ] ---

$_POST['password'] = $helper->passify( $_POST['password'] );
$_POST['register_time'] = time();
$_POST['id'] = ($helper)->nextid( DB_PREFIX . "users" );
$_POST['role'] = $universal->site->default_user_role;
$_POST['usercode'] = ($universal->methods->new_usercode)();

### -- [ auto verification: though not recommended ] --
if( !$universal->site->confirm_user_reg_email ) $_POST['status'] = 'verified';


# --- [ add user ] ---
$universal->ajax->status = $ucsqli->insert( $db_users, $_POST );


if( !$universal->ajax->status ) {
	
	$universal->ajax->message = "
		<p class='mb-2 text-danger font-weight-500'>
			<i class='fas fa-times'></i> - Error 
		</p>
		<p>
			<i class='fas fa-exclamation-triangle'></i> 
			- The request failed! Please try again!
		</p>
	";
	
} else {
	
	$universal->ajax->message =  "
		<p class='mb-2 text-success font-weight-500'>
			<i class='far fa-star'></i> - Success! 
		</p>
	";

	$universal->ajax->data['redirect'] = $universal->src->login_url;
	
	if( $universal->site->confirm_user_reg_email ) {
		
		$verimail = ($universal->methods->verify_email)( $_POST['id'] );
		
		$emailMsg = [];
		
		$emailMsg["success"] = "
			<p>
				<i class='far fa-envelope'></i> 
				- A confirmation link has been sent to your email address.
			</p>
		";
		
		$emailMsg["error"] = "
			<p>
				<i class='far fa-exclamation-triangle'></i> 
				- Confirmation email not sent! Visit login page to request a new email link.
			</p>
		";
		
		$universal->ajax->message .= $verimail ? $emailMsg['success'] : $emailMsg['error'];
		
	} else
		
		$universal->ajax->message .= "<i class='fas fa-check'></i> - You can login now!";
	
}
	
	
