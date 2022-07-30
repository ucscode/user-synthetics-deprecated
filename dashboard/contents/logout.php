<?php 

require_once __DIR__ . "/config.php";


# --- [ clear cookie to prevent auto login ] ---

setrawcookie("_ussl", 0, 0);


# --- [ destroy login session ] ---

session_destroy();


# --- [ redirect to login page ] ---

header( "location: " . $universal->src->login_url );
