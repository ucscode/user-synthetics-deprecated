<?php 

	defined("ROOT_PATH") or DIE;
	
	global $grid_menu;
	
	# Grid Menu does not support submenu (i.e child menu)
	
	$grid_list = array_filter($grid_menu->get(), function($menu) {
		$hidden = $menu['hidden'] ?? false;
		return !$hidden;
	});
	
	if( !empty($grid_list) ):
?>
<div class="app-utility-item app-grid-dropdown dropdown">   
 
	<a class="dropdown-toggle no-toggle-arrow show" id="app-nav-grid" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="true" title="Grid Menu">
		<i class="fas app-nav-icon fa-th-large"></i>
	</a>
					        
	<ul class="dropdown-menu dropdown-menu-right connection-dropdown shadow">
		<li class="connection-list container-fluid">
			<div class="row px-2">
			
				<?php 
					$grid_menu->enlist(null, function($data) {
						
						global $helper;
						
						if( empty($data['icon']) && empty($data['thumbnail']) )
							$data['thumbnail'] = $helper->server_to_url( ROOT_PATH . "/assets/images/user.png" );
						
						$anchor_attr = array(
							"class" => "connection-item",
							"href" => $data['link'],
							"target" => $data['target'] ?? ''
						);
						
				?>
				
					<div class="col-4 mb-1 p-1">
						<a <?php echo $helper->array_to_html_attrs($anchor_attr); ?>>
							<div class='iconic'>
							<?php if( !empty($data['thumbnail']) ): ?>
								<img src="<?php echo $data['thumbnail']; ?>" alt=""> 
							<?php else: ?>
								<i class='<?php echo $data['icon']; ?> faw'></i>
							<?php endif; ?>
							</div>
							<span class='font-weight-500 mt-1'><?php echo $data['label']; ?></span>
						</a>
					</div>
					
				<?php }); ?>
				
			</div>
		</li>
	</ul>

</div>
<?php endif; ?>