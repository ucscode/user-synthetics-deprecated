<?php

global $universal, $uss_options, $uss_user, $helper;

if( $uss_options->get("get_reg_username") ?? false ):
	events::addListener("signup:form-input:start", function() { ?>
		<div class="email mb-3">
			<label class="sr-only" for="signup-username">Your Username</label>
			<input id="signup-username" name="username" type="text" class="form-control signup-username" placeholder="Username" required="required" pattern="^\s*\w{3,}\s*$" title="Should be at least 3 characters containing only letters, numbers and underscore">
		</div> 
	<?php });
endif;

