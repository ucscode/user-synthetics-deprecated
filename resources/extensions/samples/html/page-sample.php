	<!---------------- [ CREATE PAGE ] ----------------->
	
	<?php 
		sample_box('fas fa-file', 'Create Page', function($id, $otp)  {
			global $helper;
	?>
		<form onsubmit="return false">
		
			<div class='mb-3 row'>
			
				<div :class="form_group">
					<label class="form-label">Pagename:
						<?php label("A unique name for the new page"); ?>
					</label>
					<input type='text' name='page_name' class='form-control' value='page_name' v-model="page_name">
				</div>
			
				<div :class="form_group">
					<label class="form-label">Panel:
						<?php label("The panel where the page should be created."); ?>
					</label>
					<select name='page_panel' class='form-select' v-model="page_panel"> 
						<option value='client'>Client</option>
						<option value='admin'>Admin</option>
					</select>
				</div>
			
				<div :class="form_group">
					<label class="form-label">Sidebar:
						<?php label("Select `FALSE` if you want a full page?"); ?>
					</label>
					<select name='page_sider' class='form-select' v-model="page_sider"> 
						<option value='true'>True</option>
						<option value='false'>False</option>
					</select>
				</div>
			
				<div :class="form_group">
					<label class="form-label">Blank:
						<?php label("Select `TRUE` if you want a completely blank page?"); ?>
					</label>
					<select name='page_blank' class='form-select' v-model="page_blank"> 
						<option value='true'>True</option>
						<option value='false' selected>False</option>
					</select>
				</div>
			
				<div :class="form_group">
					<label class="form-label">BodyClass:
						<?php label("Add a custom class to the body of the new page"); ?>
					</label>
					<input type='text' name='page_class' class='form-control' value='p-5' v-model="page_class">
				</div>
			
				<div class="col-12">
					<label class="form-label">Page Content:
						<?php label("The custom content that should display in the new page. You can use HTML! However, it is not allowed here"); ?>
					</label>
					<textarea name='page_content' class='form-control' v-model="page_content" rows="5">Hello World</textarea>
				</div>
				
				<div data-v-auth="otp"><?php echo $otp; ?></div>
				<input type="hidden" name="area" value="<?php echo $id; ?>">
				
			</div>
			
	<?php 
			sample_code( highlight_string("<?php 
	plugins::create_page('{{page_name}}', array(
		'role' => '{{page_panel}}',
		'sidebar' => __page_sider__,
		'blank' => __page_blank__,
		'bodyclass' => '{{page_class}}',
		'output' => function() {
			echo '{{page_content}}';
		}
	));", true)); 
	
		$admin_panel = $helper->server_to_url( ADMIN_PATH );
		$client_panel = $helper->server_to_url( ROOT_PATH );
	?>
	
		<div class="mt-3">
			<label class='form-label'>Page URL
				<?php label("The generated url that will link to the page. If the page already exists, it will not be created"); ?>
			</label>
			<div class='fs-13'>{{page_panel == 'admin' ? '<?php echo $admin_panel; ?>' : '<?php echo $client_panel; ?>'}}/{{page_name}}</div>
		</div>
		
			<div class="app-card-footer py-4 mt-auto">
			   <button class="btn app-btn-secondary" type="submit">Execute Code</button>
			</div><!--//app-card-footer-->
		
		</form>
		
		<?php if( $_SESSION['page'] ?? false ): ?>
			
			<h5>Pages you created</h5>
			
			<ul class='list-unstyled fs-13'>
			
			<?php foreach( array_reverse($_SESSION['page']) as $data ):
				
					$link = ( $data['page_panel'] == 'admin' ) ? $admin_panel : $client_panel;
					$link .= "/{$data['page_name']}";
			?>
				<li><a href='<?php echo $link; ?>' target='_blank'>
					<?php echo $link; ?>
				</a></li>
			<?php endforeach; ?>
			
			</ul>
		
		<?php endif; ?>
		
	<?php }, 'page'); ?>
		