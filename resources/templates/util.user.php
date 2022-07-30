<?php defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); ?>

<div class="app-utility-item app-user-dropdown dropdown">

	<a class="dropdown-toggle" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
		<img src="<?php echo $universal->user->avatar; ?>" alt="user profile">
	</a>
	
	<ul class="dropdown-menu shadow-sm" aria-labelledby="user-dropdown-toggle">
		<li>
			<a class="dropdown-item" href="<?php echo $helper->server_to_url( ROOT_PATH ) . "/account"; ?>">
				Account
			</a>
		</li>
		<?php events::exec("nav-util:user-dropdown"); ?>
		<li><hr class="dropdown-divider"></li>
		<li>
			<a class="dropdown-item" href="<?php echo $universal->src->logout_url; ?>">
				Log Out
			</a>
		</li>
	</ul>
	
</div><!--//app-user-dropdown--> 