<?php defined("AJAX_MODE") or DIE;

$universal->ajax->message = "
	<p class='mb-2'>
		<i class='fas fa-check text-success'></i> 
		- Your account password has been successfully changed
	</p>
	<p>Please login with your new password.</p>
";


# --- [ authenticate ] ---

$inspect = (new potty())->auth( "change-password", $_POST['auth'] ?? '', false, true );


# --- [ authenticate onetime password ] ---

if( !isset($_SESSION['nonce']) || !isset($_POST['nonce']) || sha1($_SESSION['nonce']['key']) != $_POST['nonce'] ) {
	$universal->ajax->message = "<i class='fas fa-ban text-danger'></i> - Cannot verify user access token";
	$helper->jsonify( false, $universal->ajax->message, null, true );
}


# --- [ authenticate user ] ----

$user = $helper->_data( DB_PREFIX . "users", $_SESSION['nonce']['usercode'], "usercode" );

if( !$user ) {
	$universal->ajax->message = "<i class='fas fa-exclamation-ban text-danger'></i> - User authentication failed!";
	$helper->jsonify( false, $universal->ajax->message, null, true );
};


# --- [ let's do this ] ---

$_POST['password'] = $helper->passify( $_POST['password'] );

$universal->ajax->status = $ucsqli->update( 
	DB_PREFIX . "users",
	array( 
		"password" => $_POST['password'],
		"logintoken" => null
	),
	"id={$user['id']}"
);

if( !$universal->ajax->status ) 
	$universal->ajax->message = "<i class='fas fa-times text-danger'></i> - Your account password could not be changed";

else {
	$usermeta->remove("reset.password", $user['id']);
	$universal->ajax->data['redirect'] = $universal->src->login_url;
}

