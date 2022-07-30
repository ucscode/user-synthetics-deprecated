<?php 

	defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); 
	
	# --- [ newest notifications ] --
	
	$notifications = $ucsqli->select( 
		DB_PREFIX . 'notifications', 
		'*', 
		"receiver = '{$uss_user['id']}' ORDER BY id DESC LIMIT 6" 
	);
	
	# -- [ total unclicked notifications ] --
	
	$SQL = "
		SELECT COUNT(id) as unclicked
		FROM " . DB_PREFIX . "notifications 
		WHERE receiver = '{$uss_user['id']}' AND clicked = 0
		GROUP BY receiver
	";
	
	$result = $ucsqli->query( $SQL );
	$new_alert = ( $result->num_rows ) ? $result->fetch_assoc()['unclicked'] : 0;
	
	# -- [ a function to easily add new block ] --
	
	$__notification_block = function( array $data ) {
		
?>
	<div class="notice-item">
		<div class="item p-3 <?php if( empty($data['clicked']) ) echo '_new'; ?>">
			<div class="row gx-2 justify-content-between align-items-center">
				<div class="col-auto">
					<div class="app-icon-holder">
						<i class='fas'></i>
					</div>
				</div>
				<div class="col">
					<div class="info"> 
						<div class="desc text-overflow-vertical lc-2">
							<?php echo substr(strip_tags($data['message']), 0, 80); ?>
						</div>
						<div class="meta">
							<?php echo $data['time']; ?>
						</div>
					</div>
				</div>
			</div>
			<a class="link-mask" href="<?php echo !empty($data['redirect']) ? $data['redirect'] : 'javascript:void(0)'; ?>" data-x="check"></a>
			<input type='hidden' value='<?php echo $data['id']; ?>'>
	   </div><!--//item-->
	</div>

<?php } ?>
	
<div class="app-utility-item app-notifications-dropdown dropdown" id="uss-nav-notix">
						
	<a class="dropdown-toggle no-toggle-arrow" id="notifications-dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-expanded="false" title="Notifications">
		<i class='far fa-bell app-nav-icon'></i>
		<span class="icon-badge fa-bounce" id="notix-badge" v-show="counter" v-text='counter'>
			<?php echo $new_alert; ?>
		</span>
	</a><!--//dropdown-toggle-->
					        
	<div class="dropdown-menu p-0 shadow-sm" aria-labelledby="notifications-dropdown-toggle">
		<div class="dropdown-menu-header p-3">
			<h5 class="dropdown-menu-title mb-0">Notifications</h5>
		</div>
		<div class="dropdown-menu-content">

			<?php 
			
				if( $notifications->num_rows ): 
					
					while( $notification = $notifications->fetch_assoc() ):
					
						$notification['time'] = $helper->sometime_ago( $notification['period'], false );
						$__notification_block( $notification );
						
					endwhile;
					
				else: 
				
					$__notification_block([
						"message" => "You have no notification",
						"time" => "<i class='fas fa-history'></i>",
						'clicked' => 1,
						'id' => null
					]);
					
				endif;
				
			?>
			
		</div>
		
		<?php if( $notifications->num_rows ): ?>
			<div class="dropdown-menu-footer p-2 text-center">
				<a href="<?php echo $helper->server_to_url( ROOT_PATH ) . "/notifications"; ?>">View all</a>
			</div>
		<?php endif; ?>	
		
	</div>					        

</div>