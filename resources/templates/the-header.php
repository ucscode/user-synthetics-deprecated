<?php 

	/*
		- Always check for "ROOT_PATH" declaration 
		- Whenever "config.php" is not called directly
	*/
	
	defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); 
	
?>
<!DOCTYPE html>
<html lang="en"> 
<head>

	<?php events::exec("backend-head:before"); ?>
	
    <title><?php echo $universal->site->name; ?></title>
    
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="description" content="Portal - Bootstrap 5 Admin Dashboard Template For Developers">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">    
    <link rel="shortcut icon" href="<?php echo $universal->site->logo; ?>"> 
	
    <link rel="stylesheet" href="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/plugins/fontawesome-6.1.1/css/all.min.css"; ?>">
    <link rel="stylesheet" href="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/plugins/bootstrap-5.2.0/css/bootstrap.min.css"; ?>">
    <link rel="stylesheet" href="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/css/style.css"; ?>">
    <link rel="stylesheet" href="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/css/main.css"; ?>">
	
	<script>const uss = {prop: {}, meth: {}, srv: JSON.parse(<?php echo "'".json_encode($universal->js_var)."'"; ?>)};</script>
	
	<?php events::exec("backend-head"); ?>
	
</head> 

<body class="<?php echo implode(" ", $this->bodyclass); ?>"> 

	<?php events::exec("backend-body"); ?>
	
	<div class="app-preload" style="background-color: white; display: <?php echo $universal->enableLoader ? 'block' : 'none'; ?>">
		<div class="app-preload-wrapper">
			<img src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/images/load1.gif"; ?>" class="img-fluid">
		</div>
	</div>
	
	<?php if( !$this->blank ): ?>
	
    <header class="app-header fixed-top">	
	
        <div class="app-header-inner">  
	        <div class="container-fluid py-2">
		        <div class="app-header-content"> 
		            <div class="row justify-content-between align-items-center">
			        
						<div class="col-auto">
							<?php if( $this->sidebar ): ?>
								<a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="javascript:void(0)">
									<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img"><title>Menu</title><path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"></path></svg>
								</a>
							<?php endif; ?>
						</div><!--//col-->
						
						<?php if( false ): ?>
							<div class="search-mobile-trigger d-sm-none col">
								<i class="search-mobile-trigger-icon fas fa-search"></i>
							</div><!--//col-->
							
							<div class="app-search-box col"> 
								<form class="app-search-form">   
									<input type="text" placeholder="Search..." name="search" class="form-control search-input">
									<button type="submit" class="btn search-btn btn-primary" value="Search"><i class="fas fa-search"></i></button> 
								</form>
							</div><!--//app-search-box-->
						<?php endif; ?>
						
						<div class="app-utilities col-auto d-flex align-items-center user-select-none">
							
							<?php 
								$this->get_template( "util.nav-grid.php" ); 
								$this->get_template( "util.notifications.php" ); 
								$this->get_template( "util.user.php" ); 
							?>
							
						</div><!--//app-utilities-->
						
					</div><!--//row-->
	            </div><!--//app-header-content-->
	        </div><!--//container-fluid-->
        </div><!--//app-header-inner-->
        
		<?php 
			if( $this->sidebar ) 
				$this->get_template( "sidebar.php" ); 
		?>
		
   </header><!--//app-header-->
    
    <div class="app-wrapper <?php if( !$this->sidebar ) echo 'm-0'; ?>">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl pb-5">
			
				<?php if( $this->title ): ?>
					<h1 class="app-page-title"><?php echo $this->title; ?></h1>
				<?php endif; ?>
			
				<?php events::exec("backend-body:start"); ?>
		
<?php endif; // blank; ?>
