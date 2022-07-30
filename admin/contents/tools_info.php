<?php require_once __DIR__ . "/config.php";

(new backend())->output(function() {
	
	global $universal;
	
?>
	<div class="app-card">
		<div class="app-card-header p-3">
			<h4 class="app-card-title">
				INFORMATION <i class='fas fa-info-circle'></i>
			</h4>
		</div>
		<div class="app-card-body p-4 px-md-5">
			<div class="table-responsive font-size-13">
				<table class="table table-striped">
					<tbody>
					<?php
						foreach( $universal->site->info as $key => $value ):
							$title = ucwords(str_replace("_", " ", $key));
					?>
						<tr>
							<td><?php echo $title; ?></td>
							<td><?php echo $value; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>	
	</div>
	
<?php });