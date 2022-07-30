<?php defined("AJAX_MODE") or die;


# --- [ authentication ] ---

$inspect = (new potty())->auth( 'login', $_POST['auth'] ?? '', false, true );


# -- [ sanitize input ] --

foreach( $_POST as $key => $value ) {
	$_POST[$key] = $helper->sanitize($value);
	if( $key == 'login' ) $_POST[$key] = strtolower($_POST[$key]);
}


# --- [ validate input ] ---

if( preg_match("/@/", $_POST['login']) ):
	$regex = $helper->regex("email");
	$loginType = 'email';
else: 
	$regex = $helper->regex("word");
	$loginType = 'username';
endif;

$valid = preg_match( $regex, $_POST['login'] );
if( !$valid ) $helper->jsonify( false, "Invalid {$loginType}", null, true );


# --- [ error message ] --

$universal->ajax->message = "<i class='fas fa-question-circle'></i> - The login credential is incorrect!";
	
	
# --- [ check if login detail is correct ] ---

$_POST['password'] = $helper->passify( $_POST['password'] );

$result = $ucsqli->select( 
	DB_PREFIX . "users", 
	"*", 
	"{$loginType}='{$_POST['login']}' AND password='{$_POST['password']}'" 
);

if( !$result->num_rows ) $helper->jsonify( false, $universal->ajax->message, null, true );

$user = $result->fetch_assoc();


# --- [ confirm email verification ] ---

if( $user['status'] == 'unverified' ) 
	$helper->jsonify( false, "<i class='fas fa-shield-alt'></i> - Please verify your email to proceed!", null, true );


# --- [ process login ] ---

do {
	
	$_SESSION['login'] = array(
		"token" => $helper->keygen(),
		"time" => time()
	);
	
	$non_unique_token = $ucsqli->select( DB_PREFIX . 'users', 'id', "logintoken='{$_SESSION['login']['token']}'" )->fetch_assoc();
	
} while( $non_unique_token );

$universal->ajax->status = $ucsqli->update( 
	DB_PREFIX . 'users', 
	array( 
		'logintoken' => $_SESSION['login']['token'],
		'remote_addr' => $_SERVER['REMOTE_ADDR']
	),
	"id='{$user['id']}'"
);


if( $universal->ajax->status ) {
	
	$universal->ajax->message = "<p class='font-weight-500 mb-2'></i> <i class='fas fa-check text-success'></i> - Success!</p>";
	
	$universal->ajax->message .= "<p><i class='fas fa-info-circle'></i> - You will be redirected shortly</p>";
	
	# --- [ process cookie ] ---
	
	if( isset($_POST['remember']) )
		$cookie = setrawcookie("_ussl", sha1(sha1($_SESSION['login']['token'])), strtotime("9 days"));
	
};