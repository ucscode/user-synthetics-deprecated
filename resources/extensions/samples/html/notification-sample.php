	<!---------------- [ MANAGE NOTIFICATION ] ----------------->
	
	<?php 
		sample_box('fas fa-bell', 'Add notification', function()  {

			sample_code( highlight_string("<?php 
	\$helper->add_notification(array(
		'receiver' => 1, // userid
		'message' => 'You have just received a new instant price'
	));", true) ); 
	
	?>
			<div class="alert alert-dark br-1 fs-13">
				No permission to execute this code at the moment
			</div>
		
	<?php }, 'notice'); ?>