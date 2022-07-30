<?php 

(defined("AJAX_MODE") && $uss_user && isset($_POST['type']) ) OR DIE('?');

$_where = "receiver = '{$uss_user['id']}' AND id IN(" . implode(",", $_POST['values']) . ")";

switch( $_POST['type'] ) {
	
	case "check":

			$universal->ajax->status = $ucsqli->update( 
				DB_PREFIX . 'notifications', array( "clicked" => 1 ), $_where
			);

		break;

	case "trash":
	
		$SQL = "DELETE FROM " . DB_PREFIX . "notifications WHERE {$_where}";
		$universal->ajax->status = $ucsqli->query( $SQL );

		break;
	
}


if( empty($universal->ajax->message) && is_null($universal->ajax->status) ):

	$universal->ajax->message = "No demand could be captured";

elseif( $universal->ajax->status ):

	$universal->ajax->data[] = $ucsqli->select( 
		DB_PREFIX . 'notifications',
		'id', 
		"receiver = {$uss_user['id']} AND clicked = 0" 
	)->num_rows;

endif;