<?php 


events::addListener("copyright.footer", function() { 
	global $universal;

?>

	<div class="container text-center py-2">
		<small class="copyright">
			Copyright &copy; <?php echo (new datetime())->format("Y"); ?> <span class="mx-2">&mdash;</span>
			<a class="app-link" href="<?php echo $universal->src->root_url; ?>">
				<?php echo $universal->site->name; ?>
			</a>
		</small>
	</div>
	
	<div class="container text-center">
		<small>
			<p>Developed by <a href='https://github.com/ucscode' target="_blank">UCSCODE</a></p>
		</small>
	</div>
	
<?php }); 