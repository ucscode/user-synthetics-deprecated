	
	<!---------------- [ ADD HEADER / FOOTER TEXT ] ----------------->
	
	<?php 
		sample_box('fas fa-heading', 'Header &amp; Footer', function($id, $otp)  {
	?>
		<form onsubmit="return false">
		
			<div class="mb-3 row">
				<div class="col-12 col-md-6 mb-2">
					<label>Header:
						<?php label("A text to show at top of every page"); ?>
					</label>
					<input type="text" name="h1_head" class='form-control' value='Visible at the TOP of every page' v-model='h1_head'>
				</div>
				<div class="col-12 col-md-6 mb-2">
					<label>Footer:
						<?php label("A text to show at bottom of every page"); ?>
					</label>
					<input type="text" name="h1_foot" class='form-control' value='Visible at the BOTTOM of every page' v-model='h1_foot'>
				</div>
			</div>
			
			<input type="hidden" name="area" value="<?php echo $id; ?>">
			<input type="hidden" name="otp" value="<?php echo $otp; ?>">
			
	<?php
			sample_code( highlight_string("<?php 
	events::addListener('backend-body:start', function() {
		echo '<h2>{{h1_head}}</h2>';
	});", true)); 

				sample_code( highlight_string("<?php 
	events::addListener('backend-body:end', function() {
		echo '<h2>{{h1_foot}}</h2>';
	});", true)); 
	
	?>
		
			<div class="app-card-footer py-4 mt-auto">
			   <button class="btn app-btn-secondary" type="submit">Execute Code</a>
			</div><!--//app-card-footer-->
			
		</form>
		
	<?php }, 'head_foot'); ?>