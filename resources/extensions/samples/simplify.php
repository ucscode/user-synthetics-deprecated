<?php 

function sample_box(string $icon, string $title, callable $content, $id = null) { 
	$otp = ( $id ) ? (new potty())->assign("{$id}") : null;
?>

	<div class="col-12 col-lg-12">
		<div class="app-card app-card-basic d-flex flex-column align-items-start shadow-sm">
			<div class="app-card-header p-3 border-bottom-0">
				<div class="row align-items-center gx-3">
					<div class="col-auto">
						<div class="app-icon-holder">
							<i class='<?php echo $icon; ?>'></i>
						</div><!--//icon-holder-->
					</div><!--//col-->
					<div class="col-auto">
						<h4 class="app-card-title text-capitalize"><?php echo $title; ?></h4>
					</div><!--//col-->
				</div><!--//row-->
			</div><!--//app-card-header-->
			<div class="app-card-body px-4 w-100">
				<div class="intro" id='<?php echo $id; ?>' data-php>
					<?php $content($id, $otp); ?>
				</div>
			</div><!--//app-card-body-->
		</div><!--//app-card-->
	</div><!--//col-->
	
<?php };

function sample_code( string $code) {
	$code = preg_replace("/__(\w+)__/i", "{{\$1}}", $code);
	echo "<div class='codesample py-2 px-3 mb-2'>{$code}</div>";
}

function label( string $text ) { ?>
	<span class="ms-2" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="<?php echo $text; ?>">
		<i class="fas fa-info-circle"></i>
	</span>
<?php }