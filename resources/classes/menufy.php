<?php 

/**
	* Name: MENUFY
	* Version: 1.0.0
	
	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Title: Why edit the script?
	
	* Description: This class organizes the formation of MENU. It allows you to add, edit or remove menu items without editing the menu script.
	
**/

class menufy {

	private $menu = array();
	private $delimeter = '.';
	private $stdout;
	
	## - required menu data: [ label & link ]
	## - optional data: [ icon ]
	
	public function add(string $name, array $data) {
		if( array_key_exists($name, $this->menu) ) throw new Exception( "$name - Menu already exists" );
		$this->menu[$name] = $this->validate("Menu:{$name}", $data);
	}
	
	public function add_submenu(string $menupath, ?string $name = null, array $data) {
		$result = $this->search( $menupath );
		$menu = &$result[0];
		if( !$menu ) throw new Exception( "{$result[1]} -  Menu not found" );
		// submenu will be overridden if it exists
		if( is_null($name) ) {
			// generate an index as key;
			$keys = array_keys($menu['submenu']);
			$name = empty($keys) ? -1 : max($keys);
			if( is_nan($name) ) $name = count($menu['submenu']);
			else $name = (int)$name + 1;
		}
		$menu['submenu'][$name] = $this->validate("SubMenu:{$menupath}.{$name}", $data, ($menu['depth'] + 1));
	}
	
	private function validate( $info, $menu, $depth = 0 ) {
		if( empty($menu['label']) ) throw new Exception("{$info} - Label is required");
		else if( !isset($menu['link']) ) $menu['link'] = 'javascript:void(0)';
		$menu['submenu'] = array();
		$menu['depth'] = $depth;
		return $menu;
	}
	
	public function detach( string $menupath ) {
		$parentpath = explode($this->delimeter, $menupath);
		$key = array_pop($parentpath);
		$parentpath = implode($this->delimeter, $parentpath);
		$result = $this->search( $parentpath );
		if( empty($result[0]) ) $parentmenu = &$this->menu;
		else $parentmenu = &$result[0]['submenu'];
		if( array_key_exists($key, $parentmenu) ) unset($parentmenu[$key]);
	}
	
	public function search( string $menupath ) {
		$menupath = array_filter(explode($this->delimeter, $menupath), function($key) {
			return (trim($key) != ''); 
		});
		$pathloop = '';
		$parent = null;
		foreach( $menupath as $menukey ) {
			$pathloop .= $menukey . ".";
			if( !$parent ) $parent = &$this->menu[$menukey];
			else {
				$parent = &$parent['submenu'][$menukey];
				if( !$parent ) break;
			}
		};
		return [ &$parent, substr($pathloop, 0, -1) ];
	}
	
	public function get() {
		return $this->menu;
	}
	
	public function enlist( ?array $menu = null, ?callable $func = null ) {
		
		if( is_null($menu) ) $menu = $this->menu;
		if( !$this->stdout && !is_null($func) ) $this->stdout = $func;
		
		if( !$this->stdout && !$func ) {
			throw new Exception( 
				__class__ . "::" . __function__ . " requires at least one output function in parameter 2" 
			);
		};
		
		foreach( $menu as $name => $data ):
		
			$is_valid = is_array($data) && 
				isset($data['label']) && !empty($data['label']) &&
				isset($data['link']) && 
				isset($data['submenu']) && is_array($data['submenu']);
				
			if( !$is_valid ) throw new Exception( "Invalid menu properties" );
			if( is_null($func) ) $func = $this->stdout;
			
			echo $func($data, $name, $this);
			
		endforeach;
		
	}
	
	/*
		enlist(): usage sample
		
		$menu->enlist(null, function($data, $name, $menu) {
			ob_start();
		?>
			<li class="menu-item">
				<a href="<?php echo $data['link']; ?>"><?php echo $data['label']; ?></a>
				<?php if( !empty($data['submenu']) ): ?>
					<ul class="dropdown"><?php $menu->enlist($data['submenu']); ?></ul>
				<?php endif; ?>	
			</li>
		<?php 
			$menu_item = ob_get_clean();
			return $menu_item;
		});
		
	*/

}