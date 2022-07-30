<?php 

	defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); 
	
	if( !$this->blank ):
	
?>
				<?php events::exec("backend-body:end"); ?>
				
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
	    
	    <footer class="app-footer">
			<?php events::exec("copyright.footer"); ?>
	    </footer><!--//app-footer-->
	    
    </div><!--//app-wrapper-->    					
	
<?php endif; ?>
	
	<?php require_once TEMP_PATH . "/modal.php"; ?>
	
	<?php events::exec("backend-foot:before"); ?>
	
    <!-- Javascript -->  
  
    <script src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/plugins/jquery-3.6.0.min.js"; ?>"></script>     
    <script src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/plugins/popper.min.js"; ?>"></script>
    <script src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/plugins/bootstrap-5.2.0/js/bootstrap.min.js"; ?>"></script>
    <script src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/plugins/vue-2.7.4/vue.js"; ?>"></script>
    <!-- Page Specific JS -->
    <script src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/js/app.js"; ?>"></script> 
    <script src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/js/app.object.js"; ?>"></script> 
	
	<?php events::exec("backend-foot"); ?>
	
</body>
</html> 

