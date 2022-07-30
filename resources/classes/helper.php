<?php 

/**
	* Name: HELPER
	* Version: 3.6.2
	
	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode

	* Description: Made to offer little help but suprisingly grew to become one of the cores library of User Synthetics. This class helps perform task in one shot that needs to be done repulsively with excessive coding.
	
**/

# - This class helps solve issue that need to be executed frequently;

class helper {
	
	# - Convert server path to HTTP URL
	
	public function server_to_url( ?string $server_path = null, $mini = false ) {
		if( empty($server_path) ) $server_path = INST_PATH;
		$server_path = str_replace("\\", "/", $server_path);
		$server_url = preg_replace( "~^{$_SERVER['DOCUMENT_ROOT']}~i", $_SERVER['SERVER_NAME'], $server_path );
		$protocol = ($_SERVER['REQUEST_SCHEME'] ?? ($_SERVER['SERVER_PORT'] == '80' ? 'http' : 'https'));
		return (!$mini ? ($protocol . "://") : '/') . $server_url;
	}
	
	# - extract all the numbers 
	
	public function only_numbers( $string, bool $unsigned = false ) {
		$opera = $unsigned ? null : "+\-";
		$numbers = preg_replace("/[^0-9.{$opera}]/i", null, $string);
		return $numbers;
	}
	
	# - Clean Query String
	
	public function sanitize( $data ) {
		$filter = function($string) {
			return trim( htmlspecialchars($string) );
		};
		if( !is_array($data) ) return $filter($data);
		else {
			foreach( $data as $key => $value ) {
				if( is_array($value) || is_object($value) ) continue;
				$data[ $key ] = $filter( $value );
			};
			return $data;
		}
	}
	
	# - print and return a json string;
	# - useful for API or similar technology
	
	public function jsonify( bool $status, string $message, ?array $data = array(), $print_and_die = false ) {
		$combine = array(
			"status" => $status,
			"message" => $message,
			"data" => $data
		);
		$json = json_encode( $combine );
		if( $print_and_die ) {
			print_r( $json );
			exit;
		};
		return $json;
	}
	
	# - convert string to password;
	
	public function passify( string $string, $len = 30 ) {
		$case1 = md5($string);
		$case2 = sha1($string);
		$case3 = substr($case1, -25) . substr($case2, 0, 25);
		$finally = sha1($case3 . $case2 . $case1);
		return substr($finally, 0, $len);
	}
	
	# - some basic regular expressions to save time;
	
	public function regex( $expression, $start_and_end_with_regex = true ) {
		
		if( $start_and_end_with_regex ) {
			$A = "^"; 
			$E= "$";
		} else $A = $E = null;
		
		switch( strtolower($expression) ) {
			case "word":
				$regex = "/{$A}\s*\w+\s*{$E}/i";
				break;
			case "text":
				$regex = "/{$A}[a-z0-9_\s\-.,:;\?@!]+{$E}/i";
				break;
			case "number":
				$regex = "/{$A}\s*\-?\d+(?:\.\d+)?\s*{$E}/";
				break;
			case "email":
				$regex = '/' . $A . '(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))' . $E . '/';
				break;
			case "url":
				$regex = "/{$A}(?:https?:\/\/)?(?:[\w.-]+(?:(?:\.[\w\.-]+)+)|(?:localhost(:\d{1,4})?\/))[\w\-\._~:\/?#[\]@!\$&'\(\)\*\+,;=.%]+{$E}/i";
				break;
			case "date":
				$regex = "/{$A}(0[1-9]|[1-2][0-9]|3[0-1])(?:\-|\/)(0[1-9]|1[0-2])(?:\-|\/)[0-9]{4}{$E}/i";
				break;
			case "btc":
				$regex = "/{$A}[13][a-km-zA-HJ-NP-Z0-9]{26,33}{$E}/i";
				break;
			case "required":
				$regex = '/^\s*\S+[\s\S]*$/';
				break;
			default:
				$regex = $expression;
		};
		return $regex;
	}

	# - convert variables to user data;
	
	public function dedicated_user_string( string $the_string, string $user_identity, string $reference_by = 'id' ) {
		
		/*
			This method matches variables in a string and converts it to a user based value; For example:
			
			$string = "Hi, your username is %{username} and your email is %{email}";
			$string .= "your first name is %{:firstname} and your last name is %{:lastname}";
			
			$userid = 7;
			
			$result = (new helper())->dedicated_user_string( $string, $userid );
			
			echo $result ; 
				// Hi, your username is ucscode and your email is uche23mail@gmail.com
				// your firstname is uchenna and your lastname is ajah
				
			--------- [ types of variables ] ---------
			
			%{var} - checks for data in users table
			%{:var} - checks for data in usermeta table
			
		*/
		
		$var_string = preg_replace_callback("/\\\\?%\{(:?[a-z0-9_\-\.]+)\}/i", function($match) use($user_identity, $reference_by) {
			
			global $ucsqli, $usermeta;
			
			if( substr($match[0], 0, 1) == '\\' ) return substr($match[0], 1);
			
			$db_users = DB_PREFIX . 'users';
			$db_usermeta = DB_PREFIX . 'usermeta';
			
			if( substr($match[1], 0, 1) == ':' ) {
				$metakey = substr($match[1], 1);
				$SQL = "SELECT meta_key, meta_value AS _value FROM {$db_usermeta} INNER JOIN {$db_users} ON {$db_usermeta}.meta_id = {$db_users}.id WHERE {$db_users}.{$reference_by} = '{$user_identity}' AND {$db_usermeta}.meta_key = '{$metakey}'";
			} else {
				$metakey = $match[1];
				$SQL = "SELECT {$metakey} AS _value FROM {$db_users} WHERE {$db_users}.{$reference_by} = '{$user_identity}'";
			};
			
			$result = $ucsqli->query( $SQL );
			
			if( $result->num_rows ):
				$result = $result->fetch_assoc();
				return $result['_value'];
			endif;
			
			return '``';
			
		}, $the_string);
		
		return $var_string;
		
	}

	# - check if namespace exists;
	
	public function namespace_exists($namespace) {
		// credit to stackoverflow
		$namespace .= '\\';
		foreach( get_declared_classes() as $classname )
			if( strpos($classname, $namespace) === 0 ) return true;
		return false;
	}
	
	# - return a PHPMailer instance;
	
	public function PHPMailer_Instance() {
		global $universal, $uss_options;
		$PHPMailer = new \PHPMailer\PHPMailer\PHPMailer();
		$PHPMailer->isHTML();
		$PHPMailer->setFrom( $universal->site->email, $universal->site->name );
		call_user_func(function() use(&$PHPMailer, $uss_options) {
			foreach( ['server', 'login', 'password', 'port'] as $key ) {
				if( !$uss_options->get("smtp_{$key}") ) return;
			};
			$PHPMailer->SMTPDebug = SMTP::DEBUG_SERVER;
			$PHPMailer->isSMTP();
			$PHPMailer->Host = $uss_options->get("smtp_server");
			$PHPMailer->SMTPAuth = true;
			$PHPMailer->Username = $uss_options->get("smtp_login");
			$PHPMailer->Password = $uss_options->get("smtp_password");
			$PHPMailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$PHPMailer->Port = $uss_options->get("smtp_port");
		});
		return $PHPMailer;
	}
	
	# - generate a key 
	
	public function keygen( $len = 20 ) {
		
		$keygroup = [];
		
		$arrays = [
			range(0,9), 
			range('a','z'), 
			array_map('strtoupper', range('a','z')),
			array('~', '!', '@', '#', '$', '^', '*', '(', ')', '_', '-', '.')
		];
		
		foreach( $arrays as $array )
			foreach( $array as $value ) $keygroup[] = $value;
		
		$newkey = [];
		
		for( $x = 0; $x < $len; $x++ ) {
			shuffle($keygroup);
			$newkey[] = $keygroup[0];
		};
		
		return implode('', $newkey);
		
	}
	
	# - generate an activation code;
	
	public function generate_activation_key( int $userid ) {
		$activation_key = substr(sha1($this->keygen()), 0, 20);
		global $ucsqli;
		$assigned = $ucsqli->update( DB_PREFIX . "users", array( "activation_key" => $activation_key ), "id={$userid}" );
		return ( $assigned ) ? $activation_key : false;
	}
	
	# - get the next id of a table row
	
	public function nextid(string $tablename, string $column = "id") {
		global $ucsqli;
		$SQL = "SELECT MAX($column) AS $column FROM $tablename";
		$result = $ucsqli->query($SQL);
		if( !$result->num_rows ) return 1;
		$value = $result->fetch_assoc()[$column];
		if( !is_numeric($value) ) return 1;
		return ( (int)$value + 1 );
	}
	
	# - get a single row of data from database
	
	public function _data( string $tablename, $value, $column = 'id' ) {
		global $ucsqli;
		$data = $ucsqli->select( $tablename, '*', "{$column}='{$value}'" );
		if( $data ) return $data->fetch_assoc();
	}
	
	# - convert array to HTML attributes 
	
	public function array_to_html_attrs( array $array ) {
		return implode(" ", array_map(function($key, $value) {
			if( is_array($value) ) $value = implode(",", $value);
			return "{$key}=\"{$value}\"";
		}, array_keys($array), array_values($array)));
	}
	
	# - get countries
	
	public function get_countries( string $iso = null, string $key = null ) {
		$datamemo = new datamemo( "country-list.json", RES_PATH . "/json" );
		$countries = $datamemo->data();
		if( is_null($iso) ) return $countries;
		$isotype = strlen($iso) == 2 ? "iso_2" : "iso_3";
		$iso = strtoupper($iso);
		foreach( $countries as $country ):
			if( $country[ $isotype ] == $iso ) {
				if( empty($key) ) return $country;
				return $country[ $key ] ?? null;
			}
		endforeach;
	}
	
	# - get country timezone;
	
	public function get_timezone_by_country( $ISO2 = "US", $return_all_indexes = false ) {
		$timezones = DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $ISO2 );
		if( !empty($timezones) ) 
			return !$return_all_indexes ? $timezones[0] : $timezones;
	}
	
	# - insert new notifications;
	
	public function add_notification( array $data ) {
		
		global $ucsqli;
		
		if( empty($data['receiver']) )
			throw new Exception( "User id of receiver is required" );
		
		else if( empty($data['message']) )
			throw new Exception( "Notification message is required" );
		
		$data['period'] = time();
		$data['id'] = 0;
		
		$inserted = $ucsqli->insert( DB_PREFIX . 'notifications', $data );
		return $inserted;
		
	}
	
	# -- some time ago --
	
	public function sometime_ago( $datetime, ?bool $full = null ) {
		 
		$country = $GLOBALS['uss_options']->get("site_country") ?? 'NG';
		$TimeZone = $this->get_timezone_by_country( $country );
		$TimeZone = (new DateTimeZone($TimeZone));
		
		$now = new DateTime("now", $TimeZone);
		
		if( $datetime instanceof DateTime ) $the_time = $datetime;
		else if( !is_numeric($datetime) ) $the_time = new DateTime( $datetime, $TimeZone );
		else $the_time = (new DateTime("now", $TimeZone))->setTimestamp($datetime);
		
		$diff = $now->diff($the_time);
		
		$diff->w = floor($diff->d /7);
		$diff->d -= $diff->w * 7;
		
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second'
		);
		
		foreach( $string as $k => &$v ) {
			if($diff->$k)
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			else
				unset($string[$k]);
		};
		
		if( !$full ) {
			$string = array_slice( $string, 0, 1 );
			if( $full === false && $string ) {
				$string = array_values($string);
				preg_match("/\d+\s\w/i", $string[0], $match);
				return str_replace(" ", '', $match[0]);
			}
		}
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	
	# -- [ delete a directory and all its content ] --
	# -- [ use with caution ] --
	
	public function deldir( $directory ) {
		if( !is_dir($directory) ) return false;
		$dirIter = new directoryIterator( $directory );
		foreach( $dirIter as $iterable ) {
			if( $iterable->isDot() ) continue;
			else if( $iterable->isDir() ) $this->deldir( $iterable->getPathname() );
			else unlink( $iterable->getPathname() );
		};
		return !!rmdir( $directory );
	}
	
}