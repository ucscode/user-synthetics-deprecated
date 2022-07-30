	<!------------------ [ MANAGE MENU ] ----------------->
	
	<?php 
		sample_box('fas fa-list', 'Create Menu', function($id, $otp)  {
			
			global $backend_menu, $admin_menu, $grid_menu;
			
			$menus = array(
				"backend_menu" => $backend_menu, 
				"admin_menu" => $admin_menu, 
				"grid_menu" => $grid_menu
			);
							
	?>
		<form class='form' onsubmit="return false">
			<div class='mb-2 row'>
				
				<div :class="form_group">
					<label class='form-label'>Name:
						<?php label("A unique name for the menu"); ?>
					</label>
					<input type='text' class='form-control' placeholder='No space' pattern="\w+" value="menu_name" v-model="name" name="menu_name">
				</div>
				
				<div :class="form_group">
					<label class='form-label'>Label:
						<?php label("The label or title that will be visible in the menu list"); ?>
					</label>
					<input type='text' class='form-control' placeholder='label' value="Gallery" v-model="label" name="menu_label">
				</div>
				
				<div :class="form_group">
					<label class='form-label'>Icon:
						<?php label("The font awesome icon! Sub-menu does not use icon though"); ?>
					</label>
					<input type='text' class='form-control' placeholder='fas fa-icon' value="fas fa-camera" v-model="icon" name="menu_icon">
				</div>
				
				<div :class="form_group">
					<label class='form-label'>Placement:
						<?php label("Where the menu should be placed"); ?>
					</label>
					<select class='form-select' v-model="uss_class" name="menu_placement">
						<?php 
							foreach( $menus as $key => $value ): 
								$name = ucfirst(str_replace("_menu", null, $key));
								if( $name == 'Backend' ) $name = 'Client';
						?>
						<option value="<?php echo $key; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				
				<div :class="form_group">
					<label class='form-label'>Parent:
						<?php label("Add the menu item as a sub-menu of..."); ?>
					</label>
					<select class='form-select' v-model="parent" name="menu_parent">
						<option value="">-- None --</option>
						<?php 
							foreach( $menus as $key => $menu ): 
								if( $key == 'grid_menu' ) continue;
						?>
						<optgroup v-if="<?php echo "uss_class == '{$key}'"; ?>">
							<?php foreach( $menu->get() as $key => $value ): ?>
							<option value="<?php echo "'$key', "; ?>">
								<?php echo ucwords($value['label']); ?>
							</option>
							<?php endforeach; ?>
						</optgroup>
						<?php endforeach; ?>
					</select>
				</div>
				
				<div data-v-auth="otp"><?php echo $otp; ?></div>
				<input type="hidden" name="area" value="<?php echo $id; ?>">
				
			</div>
	<?php 
			sample_code( highlight_string("<?php 
	\$__uss_class__->__uss_method__(__parent__'{{name}}', array(
		'label' => '{{label}}',
		'icon' => '{{icon}}'
	));", true) ); 
	
	?>
	
			<div class="alert alert-dark fs-13 mb-0">
				Grid menu is at the top right just beside notification bell - <i class="fas fa-th-large"></i> 
			</div>
			
			<div class="app-card-footer py-4 mt-auto">
			   <button class="btn app-btn-secondary" type="submit">Execute Code</a>
			</div><!--//app-card-footer-->
			
		</form>
	<?php }, 'menu'); ?>