<?php 

(defined("AJAX_MODE") && $uss_user) or die;

# [ authenticate ];

(new potty())->auth( "um_auth", $_POST['um_auth'] ?? '', true, true );


# [ process ];

switch( strtolower($_POST['action']) ):

	case "delete":
			
			$sql = "DELETE FROM " . DB_PREFIX . "users WHERE id IN(" . implode(",", $_POST['uids']) . ") AND role <> 'admin'";
			$universal->ajax->status = $ucsqli->query( $sql );
			
		break;
	
	
	case "block":
	case "unblock":
	
			$status = [];
			foreach( $_POST['uids'] as $uid ):
				if( strtolower($_POST['action']) == 'block' ) 
					$status[] = $usermeta->set("user.blocked", 1, $uid);
				else $status[] = $usermeta->remove("user.blocked", $uid);
			endforeach;
			$universal->ajax->status = !in_array(false, $status);
			
		break;
	
	
	case "edit":
		
			$uid = $_POST['uids'][0];
			
			# [prepare data]
			$data = array();
			foreach( ['role', 'email', 'status'] as $key ) {
				$data[$key] = $_POST[$key];
			};
			
			if( $data['role'] != 'admin' ) {
				$admins = $ucsqli->select( DB_PREFIX . 'users', '*', "role = 'admin'" )->num_rows;
				if( $admins < 2 ) {
					$universal->ajax->message = "There must be at least one admin in the system";
					break;
				};
			}
			
			# [set username]
			if( isset($_POST['username']) && !empty($_POST['username']) ):
				$universal->temp->user = $helper->_data( DB_PREFIX . 'users', $uid );
				if( $universal->temp->user && empty($universal->temp->user['username']) ) {
					$data['username'] = $_POST['username'];
					$exists = $helper->_data( DB_PREFIX . 'users', $data['username'], 'username' );
					if( $exists ) $universal->ajax->message = 'The username already exists';
					else if( !preg_match($helper->regex('word'), $data['username']) ) {
						$universal->ajax->message = "The username is not valid";
					};
					if( isset($universal->ajax->message) ) break;
				};
			endif;
			
			# [save info]
			$result = $ucsqli->update( 
				DB_PREFIX . 'users',
				$data,
				"id = '{$uid}'"
			);
			
			# [block user];
			if( empty($_POST['block']) ) $sub_result = $usermeta->remove("user.blocked", $uid);
			else $sub_result = $usermeta->set("user.blocked", 1, $uid);
			
			# [set status]
			$universal->ajax->status = $result && $sub_result;
		
		break;
		
		
	case "create":
	
			$data = array();
			
			# [validate username]
			if( empty($_POST['username']) ) {
				if( $uss_options->get("get_reg_username") )
					$universal->ajax->message = "Username is required";
			} else {
				if( !preg_match($helper->regex('word'), $_POST['username']) )
					$universal->ajax->message = "The username is not valid";
				else {
					$exists = $helper->_data( DB_PREFIX . 'users', $_POST['username'], 'username' );
					if( $exists ) $universal->ajax->message = "The username already exists";
					else $data['username'] = $_POST['username'];
				};
			};
			
			if( !empty($universal->ajax->message) ) break;
			
			# [validate email]
			if( !preg_match($helper->regex('email'), $_POST['email']) )
				$universal->ajax->message = "The email is not valid";
			else {
				$exists = $helper->_data( DB_PREFIX . 'users', $_POST['email'], 'email' );
				if( $exists ) $universal->ajax->message = "The email already exists";
				else $data['email'] = $_POST['email'];
			};
			
			if( !empty($universal->ajax->message) ) break;
			
			# [encrypt password]
			if( empty($_POST['password']) ) $universal->ajax->message = "Password is required";
			else $data['password'] = $helper->passify($_POST['password']);
			
			if( !empty($universal->ajax->message) ) break;
			
			$data['status'] = $_POST['status'];
			$data['register_time'] = time();
			$data['role'] = $_POST['role'];
			$data['usercode'] = ($universal->methods->new_usercode)();
			
			$universal->ajax->status = $ucsqli->insert( DB_PREFIX . 'users', $data );
			
			if( $universal->ajax->status ) {
				
				$universal->ajax->message = "<p><i class='fas fa-user-plus'></i> - Account successfully created</p>";
				
				if( $_POST['notify'] ) {
					
					$PHPMailer = $helper->PHPMailer_Instance();
					$PHPMailer->Body = "
						<p>Hi %{username},</p>
						<p>A new account was recently created for you in {$universal->site->name}. Your login credentials are given below:</p>
						<table><tbody>
							<tr>
								<th>Email:</th>
								<td>%{email}</td>
							</tr>
							<tr>
								<th>Password:</th>
								<td>{$_POST['password']}</td>
							</tr>
						</tbody></table>
						<p>Click the link below to login right now or contact {$universal->site->name} team for further enquires</p>
						<p><a href='{$universal->src->login_url}'>{$universal->src->login_url}</a></p>
						<p><strong>Regards</strong></p>
					";
					$PHPMailer->Body = $helper->dedicated_user_string( $PHPMailer->Body, $data['email'], 'email' );
					$PHPMailer->Subject = "New account created";
					$PHPMailer->addAddress( $data['email'] );
					
					$universal->ajax->message .= "<p><i class='fas fa-envelope'></i> - Email notification ";
					$universal->ajax->message .= ($PHPMailer->send()) ? "successfully sent" : "could not be sent";
					$universal->ajax->message .= "</p>";
					
				}
				
			}
			
		break;
			
			
	default:
		$universal->ajax->status = false;
		
endswitch;


if( empty($universal->ajax->message) ) {
	$universal->ajax->message = 
		($universal->ajax->status) ? "The request was successful" : "The request failed";
};
