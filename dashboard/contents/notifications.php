<?php require_once __DIR__ . "/config.php";

class notix extends paginate {
	public function max_pages() {
		return $this->max_pages;
	}
};

(new backend())->output(function() {
	
	global $ucsqli, $universal, $uss_user, $helper;
	
	$mysql_result = $ucsqli->select( 
		DB_PREFIX . 'notifications',
		'*',
		"receiver = '{$uss_user['id']}' ORDER BY id DESC"
	);
	
	$notix = new notix();
	$notix->use_mysqli_result( $mysql_result );
	$notix->rows_per_page = 12;
	$notix->current_page = (int)($_GET['paged'] ?? 1);
	
?>
	
	<div class="row">
		<div class="col-lg-8 col-md-10 m-auto app-notifications-main" id="uss-main-notix">
		
			<div class="position-relative mb-3">
				<div class="row g-3 justify-content-between">
					<div class="col-auto d-none d-sm-block">
						<h1 class="app-page-title mb-0">Notifications</h1>
					</div>
					<div class="col-auto mx-auto mx-sm-0">
						<div class="page-utilities">
							<button class="btn btn-success app-btn-success" id="mark-as-read">
								<i class='fas fa-check me-1'></i> Mark all as read
							</button>
						</div><!--//page-utilities-->
					</div>
				</div>
			</div>
			
			<?php 
			
				$notix->iterate(function($data) use($helper) { 
				
					$time = $helper->sometime_ago( $data['period'] );
					
			?>
			
				<div class="app-card app-card-notification shadow-sm mb-2 notice-item">
				
					<div class="app-card-body p-3 position-relative <?php if( empty($data['clicked']) ) echo '_new'; ?>">
						<div class="notification-content mb-1">
							<?php echo strip_tags($data['message']); ?>
						</div>
						<a class='link-mask' href='<?php echo !empty($data['redirect']) ? strip_tags($data['redirect']) : 'javascript:void(0)'; ?>' data-x="check"></a>
						<small class='font-size-11 float-end text-muted'><?php echo $time; ?></small>
					</div><!--//app-card-body-->
					
					<div class="app-card-footer py-2 px-3">
						<a class="action-link font-size-12" href="javascript:void(0)" data-x="check">
							Mark as read <i class='ml-1 fas fa-question-circle'></i> 
						</a>
						<button class='btn btn-sm font-size-11 float-end' title="Remove this notification" data-x="trash">
							<i class='fas fa-trash'></i>
						</button>
					</div><!--//app-card-footer-->
					<input type='hidden' value='<?php echo $data['id']; ?>'>
				</div><!--//app-card-->
	
			<?php }); ?>

			<?php 
				
				# -- [ if there is no notification ] --

				if( !$mysql_result->num_rows ): 

			?>
				<div class="app-card app-card-notification shadow-sm mb-2 notice-item">				
					<div class="app-card-body p-3 position-relative row align-items-center">
						<div class="notification-content mb-1 col-11">
							You have no notification
						</div>
						<small class='font-size-11 float-end text-muted col-1'>
							<i class='fas fa-history'></i>
						</small>
					</div>
				</div><!--//app-card-->
			<?php endif; ?>

			<?php 

				# -- [ enable navigation to older notifications ] --
				
				$prev = $notix->current_page - 1;
				if( $prev < 1 ) $prev = null;

				$next = $notix->current_page + 1;
				if( $next > $notix->max_pages() ) $next = null;

			?>

			<div class="text-center mt-4">

				<?php if( $next ): ?>
					<a class="btn app-btn-secondary" href="?paged=<?php echo $next; ?>">
						<i class='fas fa-angle-double-left mr-1'></i> Older
					</a>
				<?php endif; ?>

				<?php if( $prev ): ?>
					<a class="btn app-btn-secondary" href="?paged=<?php echo $prev; ?>">
						Recent <i class="fas fa-angle-double-right"></i>
					</a>
				<?php endif; ?>

			</div>
			
		</div>
	</div>
	
<?php }); 