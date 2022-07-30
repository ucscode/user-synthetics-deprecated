<?php 

	defined("ROOT_PATH") or die;
	
	global $admin_menu, $backend_menu, $uss_user;
	
	$MainMenu = ( $this->panel(true) == ADMIN_PATH ) ? $admin_menu : $backend_menu;
	
	if( $uss_user && $uss_user['role'] == 'admin' ):
	
		# ---- [ switch ] ----
		
		$MainMenu->add("switch", array(
			"icon" => "fas fa-exchange",
			"label" => "switch panel",
			"link" => $this->panel() != 'admin' ? $universal->src->admin_url : $universal->src->root_url
		));
	
	endif;
	
?>

<div id="app-sidepanel" class="app-sidepanel"> 

	<div id="sidepanel-drop" class="sidepanel-drop"></div>
	
	<div class="sidepanel-inner d-flex flex-column">
	
		<a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none">&times;</a>
		
		<div class="app-branding">
			<a class="app-logo" href="<?php echo $MainMenu->url; ?>">
				<span class="logo-text">
					<?php echo $MainMenu->title; ?>
				</span>
			</a>
		</div><!--//app-branding-->  
		
		<nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1 user-select-none">
			<ul class="app-menu list-unstyled accordion" id="menu-accordion">
			
				<?php 
					$MainMenu->enlist(null, function($data, $name, $MainMenu) {
						
						global $helper;
						
						$menu_name = $name . "-" . $data['depth'];
						$data['submenu'] = array_filter($data['submenu'] ?? [], function($subdata) {
							$hidden = $subdata['hidden'] ?? false;
							return !$hidden;
						});
						$has_submenu = !empty($data['submenu']);
						$active = ($MainMenu->is_active)($data);
						
						# -- For <a /> --
						$anchor_attrs = array( 
							"class" => "nav-link", 
							"href" => !$has_submenu ? $data['link'] : 'javascript:void(0)',
							"aria-expanded" => "false",
							"aria-controls" => $menu_name,
							"title" => $data['title'] ?? ''
						);
						
						if( $has_submenu ):
							$anchor_attrs['class'] .= " submenu-toggle";
							$anchor_attrs['data-bs-toggle'] = "collapse";
							$anchor_attrs['data-bs-target'] = "#{$menu_name}";
						endif;
						
						if( $active ) $anchor_attrs['class'] .= " active";
						
						if( $data['hidden'] ?? false ) return;
						
						ob_start();
				?>
				
					<li class="nav-item <?php if( $has_submenu ) echo 'has-submenu'; ?>">
					
						<a <?php echo $helper->array_to_html_attrs($anchor_attrs); ?>>
						
							<span class="nav-icon">
								<i class="<?php echo !empty($data['icon']) ? $data['icon'] : ( $has_submenu ? "fal fa-folder-open" : "far fa-question-circle" ); ?> fa-lg"></i>
							</span>
							
							<span class="nav-link-text">
								<?php echo ucfirst($data['label']); ?>
							</span>
							
							<?php if( $has_submenu ): ?>
							<span class="submenu-arrow">
								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
								</svg>
							</span><!--//submenu-arrow-->
							 <?php endif; ?>
							 
						</a><!--//nav-link-->
						
						<?php if( $has_submenu ): ?>
						
							<div id="<?php echo $menu_name; ?>" class="collapse submenu <?php echo $menu_name; ?>" data-bs-parent="#menu-accordion">
								<ul class="submenu-list list-unstyled">
								
									<?php 
										$MainMenu->enlist($data['submenu'], function($data, $name, $MainMenu) {
											$data['label'] = ucwords($data['label']);
											$active = ($MainMenu->is_active)($data) ? 'active' : null;
											if( $data['hidden'] ?? false ) return;
											$submenu = "
												<li class='submenu-item'>
													<a class='submenu-link {$active}' href='{$data['link']}'>
														{$data['label']}
													</a>
												</li>
											";
											return $submenu;
										});
									?>
									
								</ul>
							</div>
							
						<?php endif; ?>
						
					</li><!--//nav-item-->
				
				<?php 
					$content = ob_get_clean();
					return $content; 
					});
				?>

			</ul><!--//app-menu-->
		</nav><!--//app-nav-->

	</div><!--//sidepanel-inner-->
</div><!--//app-sidepanel-->
