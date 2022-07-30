<?php 

/* 
	- This is a sample of an extension!
	
	- An extension folder requires an "index.php" file to run.
	
	- The "index.php" file will always run automatically.
	
	- Thus, your can include or require other file into the "index.php" to futher expand functionality
	
*/

require __DIR__ . "/do-codes.php";

$universal->js_var->sample_ajax = $helper->server_to_url( __DIR__ . "/ajax.php" );

events::addListener("backend-head", function() {
	echo "<style>
		.codesample {
			border: 1px solid var(--bs-gray-400);
			background-color: var(--bs-gray-100);
			border-radius: 5px;
		}
	</style><style id='v-hook'></style>";
	
});

events::addListener("backend-foot", function() {
	global $helper;
	$script = $helper->server_to_url( __DIR__ . "/script.js" );
	echo "<script src='{$script}'></script>";
});

events::addListener('client:home', function() {
	
		global $uss_user, $backend;
		
		$welcome_text = "
		
			<h4>Hi dear</h4>
			
			<h6>Thank you for your interest in user synthetics &mdash; <i class='fas fa-heart text-danger'></i></h6>
			
			<h6>Copyright &copy; 2022</h6>
			
			<hr>
			
			<p>User synthetics is a free powerful open-source user management system designed to simplify programming task for PHP developers. This user management system enables you to add more custom features and develope your own project without need to worry about basic user feature such as login, registration, forgot password etc.. </p>
			
			<p>The platform is loaded with lot of amazing classes, variables and methods that can help your fulfill your task in just a matter of time. It allows you to develop and install extensions or plugins that will maximize the features of this platform. You can build almost anything using user sythetics. This is not limited to creating social media channels, loan system, ecommerce system or whatsoever.</p>
			
			<p>In fact, it is very easy for you to edit this page your are reading right now into something totally different and amazing with just little amount of code. User synthetics gives you absolute relief of developing any user based projects. If you find this interesting and you want to know more about it, please encourage this development by following me and sharing your reviews.</p>
			
			<hr>
			
			<h6>My name is <a href='https://ucscode.com'>Uchenna Ajah</a> ~ (UCSCODE)</h6>
			
			<ul class='list-unstyled d-flex flex-wrap font-size-30'>
				<li class='me-4'>
					<a href='https://facebook.com/ucscode' class='text-primary' target='_blank'>
						<i class='fab fa-facebook-square'></i>
					</a>
				</li>
				<li class='me-4'>
					<a href='https://github.com/ucscode' class='text-dark' target='_blank'>
						<i class='fab fa-github'></i>
					</a>
				</li>
				<li class='me-4'>
					<a href='https://www.youtube.com/channel/UCPlGBkdI0ydlgAZWoLdmOFg' class='text-danger' target='_blank'>
						<i class='fab fa-youtube'></i>
					</a>
				</li>
			</ul>
			
		";
	
		$backend->liteDOM->alert(null, $welcome_text, 'intro'); 
		

		$backend->liteDOM->alert(
			"Alright! Let's play with some &lt;/code&gt;", 
			"Please note that everything you do here is temporary and will be restored back to default after your login session expires. But before that, take some time to enjoy the beauty and simplicity of this system."
		);
	
		require_once __DIR__ . '/simplify.php';
		
?>

	<div class="row g-4 mb-4">
		
		<?php 
			require __DIR__ . "/html/menu-sample.php";
			require __DIR__ . "/html/notification-sample.php";
			require __DIR__ . "/html/page-sample.php";
			require __DIR__ . "/html/customize-sample.php";
			require __DIR__ . "/html/header-footer-sample.php";
		?>

	</div><!--//row-->

	<hr>
	
	<?php 
		$backend->liteDOM->alert("Those are just a few... Seriously!", "
			<p>There are lot more you can do with very easy coding.<br> The whole functionality of this page was executed by an extension called <span class='text-danger fw-500'>``Samples``</span>. <br> Deleting that extension will render this page completely blank and return the system back to default behaviour</p>
			
			<p>You can create your own extension or plugin to alter the behaviour of this page or any other page.<br> 
			You can also edit the main <span class='text-info'>index.php</span> file if you don't want to create or use an extensions. <br> 
			User synthetics gives you the absolute freedom to modify contents and functionality to your own taste and complete any task in no time.</p>
		");
	?>
	
<?php });