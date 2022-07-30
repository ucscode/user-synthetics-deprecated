<?php 
	
	require_once __DIR__ . "/config.php";

	(new backend())->title("Overview")->output(function( $end ) { 
		
		
		events::exec("admin.layout:overview:start"); // leave an event track
		
		
		# --- [ start ] --
		
		global $universal, $ucsqli, $uss_options, $helper;
		
		$timezone = $helper->get_timezone_by_country( $universal->site->country );
		$datetimezone = new DateTimeZone($timezone);
		
		
		# ----- [ process card ] ------
		
		$users = $ucsqli->select( DB_PREFIX . "users" );
		
		$verified = $ucsqli->select( DB_PREFIX . "users", 'id', "status <> 'unverified'" );
		
		$_1day = (new datetime("-1 day", $datetimezone))->getTimestamp();
		$r_signup = $ucsqli->select( DB_PREFIX . "users", 'id', "register_time > {$_1day}" );
		
		$ls_minutes = (new datetime("-{$universal->temp->last_seen_minute} minutes", $datetimezone))->getTimestamp();
		$online = $ucsqli->select( DB_PREFIX . 'users', 'id', "last_seen > {$ls_minutes}" );
		
		
?>			    
	
	<div class="row g-4 mb-4">
	
		<div class="col-6 col-lg-3">
			<div class="app-card app-card-stat shadow-sm h-100">
				<div class="app-card-body p-3 p-lg-4">
					<h4 class="stats-type mb-1">Total Users</h4>
					<div class="stats-figure">
						<?php echo number_format($users->num_rows); ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-6 col-lg-3">
			<div class="app-card app-card-stat shadow-sm h-100">
				<div class="app-card-body p-3 p-lg-4">
					<h4 class="stats-type mb-1">Verified Users</h4>
					<div class="stats-figure">
						<?php echo number_format($verified->num_rows); ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-6 col-lg-3">
			<div class="app-card app-card-stat shadow-sm h-100">
				<div class="app-card-body p-3 p-lg-4">
					<h4 class="stats-type mb-1">Today Signup</h4>
					<div class="stats-figure">
						<?php echo number_format($r_signup->num_rows); ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-6 col-lg-3">
			<div class="app-card app-card-stat shadow-sm h-100">
				<div class="app-card-body p-3 p-lg-4">
					<h4 class="stats-type mb-1">Online Users</h4>
					<div class="stats-figure">
						<?php echo number_format($online->num_rows); ?>
					</div>
				</div>
			</div>
		</div>
		
	</div>
				
	<?php events::exec("admin.layout:overview:cards"); // event executor  ?>
	
	<div class="row g-4 mb-4">

		<div class="col-12 col-lg-8">
			<div class="app-card app-card-chart h-100 shadow-sm">
				<div class="app-card-header p-3">
					<div class="row justify-content-between align-items-center">
						<div class="col-auto">
							<h4 class="app-card-title">Signup Records</h4>
						</div><!--//col-->
					</div><!--//row-->
				</div><!--//app-card-header-->
				<div class="app-card-body p-3 p-lg-4">
					<div class="chart-container">
						<canvas id="canvas-barchart" ></canvas>
					</div>
				</div><!--//app-card-body-->
			</div><!--//app-card-->
		</div><!--//col-->
		
		<?php 
			events::addListener('backend-foot', function() use($timezone) { 
			
				global $helper, $universal, $ucsqli;
				
				$chartz = array( "days" => [], "bars" => [] );
				
				for( $x = -6; $x <= 0; $x++ ):
				
					$dtzone = new DateTimeZone($timezone);
					
					$date = (new DateTime("{$x} day midnight", $dtzone));
					$daytime = $date->getTimestamp();
					
					$nextdaytime = (new DateTime( $x + 1 . ' days midnight', $dtzone ))->getTimestamp();
					
					$regs = $ucsqli->select( 
						DB_PREFIX . 'users', 
						'id', 
						"register_time >= {$daytime} AND register_time < {$nextdaytime}" 
					);
					
					$chartz['bars'][] = $regs->num_rows;
					$chartz['days'][] = $date->format("D");
					
				endfor;
				
		?>
			<script src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/plugins/chart.js/chart.min.js"; ?>"></script> 
			<script>
				$(function() {
					const chartz = uss.meth.JSONParser(<?php echo "'" . json_encode($chartz) . "'"; ?>);// get 2d canvas context;
					// execute the chart
					execBarChart({
						el: '#canvas-barchart',
						data: chartz.bars,
						labels: chartz.days,
						text: 'Members\' Signup',
						hoverText: 'New users'
					});
				});
			</script>
		<?php }); ?>
		
		<div class="col-12 col-lg-4">
			<div class="app-card app-card-stats-table h-100 shadow-sm">
				<div class="app-card-header p-3">
					<div class="row justify-content-between align-items-center">
						<div class="col-auto">
							<h4 class="app-card-title">Last Signup</h4>
						</div>
					</div>
				</div>
				<div class="app-card-body p-3 p-lg-4">
					<div class="table-responsive">
						<table class="table table-borderless mb-0">
							<thead>
								<tr>
									<th class="meta">User</th>
									<th class="meta stat-cell">Verified</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									$lastreg = $ucsqli->select( DB_PREFIX . 'users', '*', "1 ORDER BY id DESC LIMIT 6" );
									while( $user = $lastreg->fetch_assoc() ):
										if( $user['status'] == 'unverified' ) $icon = 'fas fa-times text-danger';
										else $icon = 'fas fa-check-circle text-success';
								?>
								<tr>
									<td>
										<a href="<?php echo $helper->server_to_url( ADMIN_PATH ) . '/user_edit?ucode=' . $user['id']; ?>">
											<?php echo (empty($user['username'])) ? $user['email'] : $user['username']; ?>
										</a>
									</td>
									<td class="stat-cell">
										<i class='<?php echo $icon; ?>'></i>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div><!--//table-responsive-->
				</div><!--//app-card-body-->
			</div><!--//app-card-->
		</div><!--//col-->
	
	</div><!--//row-->
	
<?php 
	events::exec("admin.layout:overview:end"); // leave a trace
});