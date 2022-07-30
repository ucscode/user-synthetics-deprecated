<?php 

(defined("ROOT_PATH") && $uss_user) OR DIE;

$universal->ajax->message = "The image was successfully saved";


# [ authenticate ]

$inspect = (new potty())->auth( 'profile_avatar', $_POST['profile_avatar'] ?? '', true, true );


# [ check error in uploaded file ]

if( empty($_FILES) || empty($_FILES['avatar']) || $_FILES['avatar']['error'] ) {
	$helper->jsonify( false, "Something went wrong", null );
};


# -- [ get the file ] --;
$IMAGE = $_FILES['avatar'];


# -- [ filepath: where to save file ] --
$directory = strtolower( ROOT_PATH . "/uploads/images/" . (new DateTime())->format("M") );
if( !is_dir($directory) ) mkdir($directory);


# -- [ Dedicate file to user: Avoid name confilict from different users ] --
$filepath = strtolower( $directory . "/{$uss_user['usercode']}-{$IMAGE['name']}" );


# -- [ get image attributes ] --
$imageAttr = getimagesize($IMAGE['tmp_name']);


# -- [ check if the file is an image ] --
if( empty($imageAttr) || !preg_match("/image\/\w+/i", $imageAttr['mime']) ) {
	$helper->jsonify( false, "Unaccepted file type", null, true );
};


# -- [ check if the file size has exceeded ] --
$max_avatar_size = $uss_options->get("max_avatar_size") ?? 790;
$excess = (($IMAGE['size'] / 1024) > $max_avatar_size );
if( $excess ) $helper->jsonify( true, "Image size should not be larger than {$max_avatar_size}KB", null, true );


# -- [ limit file extension: e.g image/svg is dangerous ] --
$filetypes = ['jpg', 'jpeg', 'png', 'gif'];
if( !in_array( pathinfo($filepath, PATHINFO_EXTENSION), $filetypes ) ) {
	$last = array_pop( $filetypes );
	$helper->jsonify( false, "Sorry! Unsupported file extension. <br/> Only " . implode(", ", $filetypes) . " and {$last} files are allowed", null, true );
};


# -- [ upload the file ] --
$uploaded = move_uploaded_file($IMAGE['tmp_name'], $filepath);
$updated = null;


# -- [ save as new image ] --
if( !$uploaded ) $universal->ajax->message = "The file could not be uploaded";
else {
	$fileurl = $helper->server_to_url( $filepath );
	$updated = $usermeta->set("user.avatar", $fileurl, $uss_user['id']);
	if( !$updated ) $universal->ajax->message = "The image could not be saved";
};


# -- send feedback --

$universal->ajax->status = ($uploaded && $updated);


