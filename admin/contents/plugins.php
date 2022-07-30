<?php require __DIR__ . "/config.php";

# -- [ javascript variable ] --

$universal->js_var->admin_ajax = $helper->server_to_url( ADMIN_PATH ) . "/ajax.php";


# -- [ manage plugins ] --

$plugins_list = plugins::overview();
$potty = new potty();

foreach( $plugins_list as $key => $info ) {
	$plugins_list[ $key ]['otp'] = $potty->assign("uss-plugin-{$key}");
	if( empty($info['author']) ) $plugins_list[$key]['author'] = 'unknown';
}

# -- [ filter by query ] --

if( !empty($_GET) ) {
	
	$plugins_list = array_filter($plugins_list, function($data, $key) {
		
		global $uss_options;
		
		# -- [ filter by type ] --
		if( $_GET['type'] ?? false ) {
			$active_plugins = $uss_options->get("active_plugins");
			switch($_GET['type']) {
				case "active":
					if( !in_array($key, $active_plugins) ) return false;
					break;
				case "disabled":
					if( in_array($key, $active_plugins) ) return false;
					break;
			}
		};
		
		# -- [ filter by search ] --
		if( $_GET['search'] ?? false ) {
			$expression = preg_replace(["/\s+/", "/\//"], ["|", "\\/"], $_GET['search']);
			$matches = preg_grep("/{$expression}/i", array($data['title'], $data['author']));
			return !empty($matches);
		};
		
		# -- [ else ] --
		return true;
		
	}, ARRAY_FILTER_USE_BOTH);
	
};


# -- [ display ] --

(new backend())->output(function() use(&$plugins_list) {
	
	global $helper, $uss_options, $universal;
	
?>
	

	<div class="row g-3 mb-4 align-items-center justify-content-between">
		<!----- [ TITLE ] ----->
		<div class="col-auto">
			<h1 class="app-page-title mb-0">Plugins</h1>
		</div>
		<!----- [ SEARCH ] ----->
		<div class="col-auto">
			 <div class="page-utilities">
				<div class="row g-2 justify-content-start justify-content-md-end align-items-center">
					<div class="col-auto">
						<form class="docs-search-form row gx-1 align-items-center">
							<div class="col-auto">
								<input type="text" id="search" name="search" class="form-control search-docs" placeholder="Search" value="<?php echo $_GET['search'] ?? null; ?>">
							</div>
							<div class="col-auto">
								<button type="submit" class="btn app-btn-secondary">Search</button>
							</div>
						</form>
					</div><!--//col-->
					<div class="col-auto">
						<select class="form-select w-auto" id='plug-type'>
							<?php 
								foreach(['', 'active', 'disabled'] as $key):	
									$value = empty($key) ? 'All' : $key;
									$selected = ( isset($_GET['type']) && $key == $_GET['type'] ) ? 'selected' : null;
							?>
								<option value="<?php echo $key; ?>" <?php echo $selected; ?>>
									<?php echo ucfirst($value); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-auto d-none">
						<a class="btn app-btn-primary" href="javascript:void(0)">
							<i class='fas fa-upload'></i> Upload File
						</a>
					</div>
				</div><!--//row-->
			</div><!--//table-utilities-->
		</div><!--//col-auto-->
	</div><!--//row-->
	

	<!-- =================== [ PLUGINS ] ===================== -->

	<div class="row g-4">

		<?php 

			$active_plugins = $uss_options->get("active_plugins") ?? array();
			
			foreach( $plugins_list as $key => $info ): 

				foreach(['jpg', 'png', 'jpeg'] as $ext) {
					$imageFile = $info['plugin_dir'] . "/image.{$ext}";
					if( is_file($imageFile) ) {
						$size = getimagesize($imageFile);
						if( $size ) {
							$info['image'] = $helper->server_to_url( $imageFile );
							break;
						};
					};
				};
			
				$author_uri = array( 
					'href' => !empty($info['author_uri']) ? $info['author_uri'] : 'javascript:void(0)',
					'class' => 'd-inline-block text-capitalize',
					'title' => ucwords($info['author']),
					'class' => 'text-truncate text-capitalize'
				);
				if( !empty($info['author_uri']) ) $author_uri['target'] = '_blank';
				
				$status = in_array($key, $active_plugins) ? 'active' : null;
				
		?>

			<div class="col-12 col-sm-6 col-xl-4 col-xxl-3">
				<div class="app-card app-card-doc app-card-plugin shadow-sm  h-100 <?php echo $status; ?>" v-bind:class="{loading: loading}">
					
					<div class="app-card-thumb-holder p-3">
						<?php if( empty($info['image']) ): ?>
							<span class="icon-holder">
								<i class="fas fa-plug zip-file"></i>
							</span>
						<?php else: ?>
							<div class="app-card-thumb">
								<img class="thumb-image" src="<?php echo $info['image']; ?>" alt="">
							</div>
						<?php endif; ?>
					</div>

					<div class="app-card-body p-3 has-card-actions">
						
						<h4 class="app-doc-title truncate mb-1 text-capitalize">
							<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="<?php echo $info['title']; ?>">
								<?php echo $info['title']; ?>
							</span>
						</h4>
						
						<div class="app-doc-meta border-top pt-2">
						
							<ul class="list-unstyled mb-0 d-flex">
								<li class='text-nowrap'>
									<span class="text-muted">v</span> 
									<?php echo !empty($info['version']) ? $info['version'] : '1.0.0'; ?>
								</li>
								<li class='mx-1'>~</li>
								<li class='d-inline-flex text-truncate'>
									<span class="text-muted d-inline-block me-1">by</span> 
									<a <?php echo $helper->array_to_html_attrs($author_uri); ?>>
										<?php echo !empty($info['author']) ? $info['author'] : 'unknown'; ?>
									</a>
								</li>
							</ul>
							
							<div class='app-card-footer mt-1 font-size-12 font-weight-bold border-top pt-2 mt-2 d-flex'>
								<?php if( !empty($info['description']) ): ?>
									<div class=''>
										<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="<?php echo $info['description']; ?>">
											<i class='fas fa-info-circle'></i>
										</span>
									</div>
								<?php endif; ?>
								<div class='w-100 text-end'>
									<div class='d-inline-block cursor-default' v-bind:title="active ? 'enabled' : 'disabled'">
										status: <span class='ms-1' v-bind:class="active ? 'text-success' : 'text-danger'"> <i v-bind:class="active ? 'fa fa-check' : 'fa fa-times-circle'"></i> </span>
									</div>
								</div>
							</div>
							
						</div><!--//app-doc-meta-->
						
						<div class="app-card-actions user-select-none">
							<div class="dropdown">
								<div class="dropdown-toggle no-toggle-arrow" data-bs-toggle="dropdown" aria-expanded="false">
									<i class='fas fa-ellipsis-v'></i>
								</div><!--//dropdown-toggle-->

								<ul class="dropdown-menu">
									<li>
										<a class="dropdown-item" href="javascript:void(0)" v-on:click="state">	
											<i class='fas me-1' v-bind:class="active ? `fa-times-circle text-danger` : `fa-check text-success`"></i> {{active ? 'Deactivate' : 'Activate'}}
										</a>
									</li>
									<li class='toast hide'>
										<a class="dropdown-item" href="javascript:void(0)">	
											<i class='far fa-eye me-1'></i> Details
										</a>
									</li>
									<?php 
										if( !empty($info['plugin_uri']) && 
											preg_match($helper->regex("url"), $info['plugin_uri'])): 
									?>
									<li>
										<a class="dropdown-item" href="<?php echo $info['plugin_uri']; ?>" target="_blank">	
											<i class='fas fa-link me-1'></i> Source
										</a>
									</li>
									<?php endif; ?>
									<li><hr class="dropdown-divider"></li>
									<li>
										<a class="dropdown-item" href="javascript:void(0)" v-on:click='trash'>	
											<i class='fas fa-trash me-1'></i> Delete
										</a>
									</li>
								</ul>
							</div><!--//dropdown-->
						</div><!--//app-card-actions-->
							
						
					</div><!--//app-card-body-->
					
					<div class='loader'>
						<span class='icon anim-rainbow'>
						<i class='fas fa-spinner fa-spin font-size-45'></i>
						</span>
					</div>
					
					<input type='hidden' name="plugin" value='<?php echo $key; ?>'>
					<input type='hidden' name="otp" value='<?php echo $info['otp']; ?>'>
					
				</div><!--//app-card-->
			</div><!--//col-->

		<?php endforeach; ?>
	
	</div><!--//row-->
	
<?php 

	events::addListener("backend-foot", function() use($helper) {
		$script = $helper->server_to_url( ADMIN_PATH ) . "/assets/js/plugins.js";
		$tag = "<script src='{$script}'></script>";
		print_r( $tag );
	});
		
		
	# -- [ spoof ] --
	
	if( empty($plugins_list) ):
		
		if( empty(plugins::overview()) ) {
			$error = "No plugin has been installed";
		} else {
			$error = "No matching result was found";
		}
?>
	
	<div class="row py-4">
		<div class="col-md-8 m-auto">
			<div class="card py-5">
				<div class="card-body text-center">
					<h4><?php echo $error; ?></h4>
				</div>
			</div>
		</div>
	</div>
	
<?php

	endif;
	
});