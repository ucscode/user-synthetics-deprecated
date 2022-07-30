<?php 

	require_once __DIR__ . "/config.php";
	
	
	## -- [ add authentication code to javascript for only admin ] --

	if( $uss_user && $uss_user['role'] == 'admin' ) 
	$universal->js_var->otp = (new potty())->assign("um_auth");


	## -- [ output backend ] --
	
	(new backend())->title("List users")->output(function() {
		
		global $ucsqli, $helper, $uss_options;
		
		# -- [ create a tabledata object: (required) ] --
		$tdata = new TableData( 'users' );
		
		
		# -- [ create a mysqli result: (required) ] --
		if( !empty($_GET['search']) ) {
			$SEARCH = "CONCAT_WS(' ',username, email, status, role, usercode, remote_addr)";
			$SEARCH .= " LIKE '%{$_GET['search']}%'";
		} else $SEARCH = 1;
		$result = $ucsqli->select( DB_PREFIX . "users", '*', "{$SEARCH} ORDER BY id DESC" );
		
		
		# -- [ pass the mysqli result to the tabledata object: (required) ] --
		$tdata->use_mysqli_result( $result );
		
		
		/*
			-- [ explain the columns that should be displayed: (required) ] --
			
			When $column is an array it means:
			
				- search for $column[0] in the mysqli result
				- but show the title as $column[1] to the user
				
				see example - A.1
		*/
		
		$columns = array(
			$uss_options->get("get_reg_username") ? "username" : null,
			"email",
			"status",
			"role",
			["usercode", 'identity'], // This is example - A.1
			["remote_addr", "Login IP"], // Another example - A.1
			"action"
		);
		
		# -- [ pass the columns to the tabledata object: (required) ] --
		$tdata->set_columns( $columns, true ); // param 2 = true; - add columns to footer;
		
		
		/*
			-- [ set the primary column of the mysql result: (optional) ] --
			The column value will become the value of each checkbox
		*/
		
		$tdata->primary_key = "id";
		
		
		/*
			-- [ set dropdown options: (optional) ] --
			The options that display immediatly after the search box;
		*/
		
		$options = array(
			"delete",
			"block",
			"unblock"
		);
		
		# -- [ pass the options to tabledata object: (optional) ] --
		$tdata->set_options( $options );
		
		
		# -- [ set how many rows should display per page: (required) ] --
		$tdata->rows_per_page = 10;
		
		
		# -- [ set the current page: (required) ] --
		$tdata->current_page = $_GET['paged'] ?? 1;
		
		
		# -- [ message for empty table set ] --
		$tdata->emptiness = "<div class='text-center mt-3'>No result was found</div>";
		
		
		# -- [ list the data: (your choice) ] --
		
		$tdata->list_rows(function($data) {
			$data['action'] = '
				<div class="dropdown uss-td-drop" data-uid="' . $data['id'] . '">
					<button class="dropdown-toggle btn app-btn-secondary" id="user-tdata-toggle" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="fas fa-ellipsis-v"></i>
					</button>
					<ul class="dropdown-menu" aria-labelledby="user-tdata-toggle">
						<li><a class="dropdown-item" href="user_edit?ucode=' . $data['id'] . '">
								<i class="fas fa-edit me-1"></i> Manage
						</a></li>
						<li><a class="dropdown-item" href="javascript:void(0)" data-utd-action="delete">
								<i class="fas fa-trash-alt me-1"></i> Delete
						</a></li>
					</ul>
				</div>
			';
			return $data;
		});
		
		
		/*
		
			TableData::list_rows() method also accept a function as parameter.
			
			With this function, you can return a different value for each column;
			
			Example:
			
				$tdata->list_rows(function($data) {
					if( $data['status'] == 'verified' ) $data['status'] = 'awesome';
					return $data;
				});
				
			
			Now the table rows will be like:
			
			==================================================
			|  EMAIL          |    STATUS      |    ROLE     |
			==================================================
			| them@email.com  |   unverified   |    member   |
			--------------------------------------------------
			| you@gmail.com   |    awesome     |    member   |
			--------------------------------------------------
			| me@ucscode.com  |    awesome     |    admin    |
			--------------------------------------------------
			| user@gmail.com  |   unverified   |    member   |
			--------------------------------------------------
			
			So instead of writing "verified", it was replaced with "awesome"
			
		*/
		
		
	events::addListener("backend-foot", function() use($helper) {
		$src = $helper->server_to_url( ADMIN_PATH ) . "/assets/js/users.js";
		echo "<script src='{$src}'></script>";
	});
	
});
	