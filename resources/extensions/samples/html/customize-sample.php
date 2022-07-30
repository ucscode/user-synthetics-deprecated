	<!---------------- [ STYLE SHEET ] ----------------->
	
	<?php 
		sample_box('fas fa-code', 'Customize', function()  {
	?>
		
		<div class='mb-3'>
			<div class="">
				<label class="form-label">CSS Code:
					<?php label("Your custom css code"); ?>
				</label>
				<textarea class="form-control" name="" v-model="css_code" rows="5">body.app { /* background-color: var(--bs-danger); */ }</textarea>
			</div>
		</div>
		
	<?php
			sample_code( highlight_string("<?php 
	events::addListener('backend-head', function() {
		echo '<style>
			{{css_code}}
		</style>'
	});", true) ); 
	
	?>
	
		<div class="alert alert-dark fs-13 br-1">
			<p> Well! This probably doesn't need some kind of special code execution. I guess... </p>
			<div class='text-danger bg-light p-2 mt-1'>
				<i class='fas fa-exclamation-triangle'></i> - Any css code added above will affect the appearance of this page
			</div>
		</div>
		
	<?php }, 'customize'); ?>
	
	