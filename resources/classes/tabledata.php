<?php 

/**

	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Description: This class is solely dedicated to User Synthetics project for create HTML Tables.
	
**/

require_once __DIR__ . "/abst.paginate.php";

class TableData extends paginate {

	protected $columns;
	protected $options = array();
	private $tfoot;
	
	private $keys = array();
	protected $tablename;
	
	public $primary_key;
	protected $beforeList;
	protected $afterList;
	
	public function __construct( string $tablename ) {
		# -- [ tablename should be unique ] --
		$this->tablename = $tablename;
	}
	
	public function set_columns( array $columns, bool $add_Tfoot = false ) {
		$this->columns = $columns;
		$this->tfoot = $add_Tfoot;
	}
	
	public function set_options( array $options ) {
		$this->options = $options;
	}
	
	public function beforeList( callable $func ) {
		$this->beforeList = $func;
	}
	
	public function afterList( callable $func ) {
		$this->afterList = $func;
	}
	
	public function list_rows( ?callable $func = null ) {
		
?>
		
	<div class="" data-uss-simple-tabledata="<?php echo $this->tablename; ?>">
	
		<div class="row g-2 mb-3 justify-content-end">
		
			<div class="col-12 col-sm-8 col-md-6">
				<form data-uss-tabledata-search='<?php echo $this->tablename; ?>'>
					<div class="input-group">
						<input type="search" class="form-control" name="" value="<?php echo $_GET['search'] ?? null; ?>">
						<button type="submit" class="btn app-btn-secondary">Search</button>
					</div>
				</form>
			</div><!--//col-->
			
			<?php if( !empty($this->options) ): ?>
				<div class="col-9 col-sm-4 col-md-3">
					<div class="input-group" data-uss-tabledata-options="<?php echo $this->tablename; ?>">
						<select class="form-select" >
							<option disabled selected> -- select -- </option>
							<?php 
								foreach( $this->options as $option ):
									if( is_array($option) ) {
										$option = array_values($option);
										$key = $option[0];
										$value = $option[1] ?? $option[0];
									} else $key = $value = $option;
							?>
								<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
						<button class="btn app-btn-secondary" type="button" id="uss-tabledata-apply-<?php echo $this->tablename; ?>">Apply</button>
					</div>
				</div>
			<?php endif; ?>
			
		</div>
		
		<?php if( is_callable($this->beforeList) ) ($this->beforeList)(); ?>
		
		<!-------- [ TABLE DATA ] ------->
		
		<div class="app-card app-card-orders-table shadow-sm">
			<div class="app-card-body p-3">
				<div class="table-responsive">
					<table class="table table-hover mb-0 text-left" id="uss-tabledata-table-<?php echo $this->tablename; ?>">
						<thead>
							<?php $_thead = function() { ?>
							<tr>
								<th class="cell">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" data-uss-checked="th" value="">
									</div>
								</th>
								<?php
									foreach( $this->columns as $column ): 
										if( is_array($column) ) {
											$column = array_values($column);
											$column_title = ($column[1] ?? $column[0]);
											$the_key = $column[0];
										}else $the_key = $column_title = (string)$column;
										if( is_null($the_key) || trim($the_key) == '' ) continue;
										else $this->keys[] = $the_key;
								?>
								<th class="cell"><?php echo ucfirst($column_title); ?></th>
								<?php endforeach; ?>
							</tr>
							<?php }; $_thead(); ?>
						</thead>
						<tbody>
							<?php
							
								$result = $this->iterate(function($data) use($func) {
									
									# -- [ check for unknown keys added to the columns ] --
									$diff = array_diff($this->keys,  array_keys($data));
									
									# -- [ if unknown key exists, add it to the data as NULL ] --
									if( !empty($diff) )
										foreach( $diff as $fake_key ) $data[ $fake_key ] = null;
									
									# -- [ give programmer an opportunity to modify the content ] --
									$__data = ( is_callable($func) ) ? $func($data) : $data;
									
									# -- [ if no modifications were returned, use the default data ] --
									if( empty($__data) ) $__data = $data;
									
									# -- [ check for primary key ] --
									$primary_value = empty($this->primary_key) ? null : ($__data[$this->primary_key] ?? null);
								
							?>
							<tr data-uss-tr="<?php echo $primary_value; ?>">
								<td class="cell">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" data-uss-checked="td" value="<?php echo $primary_value; ?>">
									</div>
								</td>
								<?php foreach( $this->keys as $key ): ?>
									<td class="cell"><?php echo $__data[ $key ] ?? null; ?></td>
								<?php endforeach; ?>
							</tr>
							<?php }); ?>
						</tbody>
						<?php if( $this->tfoot && !empty($result) ): ?>
							<tfoot><?php $_thead(); ?></tfoot>
						<?php endif; ?>
					</table>
				</div>
				<?php if( empty($result) && $this->emptiness ) echo $this->emptiness; ?>
			</div>
		</div>
		
		<?php if( is_callable($this->afterList) ) ($this->afterList)(); ?>
		
		<!-------- [ PAGINATOR ] -------->
		<?php 
			$pager = $this->pager();
			if( $pager ):
		?>
			<div class="row justify-content-center justify-content-sm-end mt-4">
				
				<div class="col-auto">
					<nav class="app-pagination">
						<ul class="pagination justify-content-center">
							<?php 
								foreach( $pager as $pageinfo ): 
									if( !$pageinfo[1] ) $pageinfo[1] = $pageinfo[0];
									else $pageinfo[1] = "<i class='fas fa-{$pageinfo[1]}'></i>";
									if( !$pageinfo[0] ) continue;
							?>
								<li class="page-item">
									<a class="page-link px-3" href="javascript:void(0)" data-uss-paged="<?php echo $pageinfo[0]; ?>" data-uss-nav-control="<?php echo $this->tablename; ?>">
										<?php echo $pageinfo[1]; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav><!--//app-pagination-->
				</div>
				
				<div class="col-8 col-sm-3">
					<div class="input-group" data-uss-page-control="<?php echo $this->tablename; ?>">
						<select class="form-select">
							<?php for( $x = 1; $x <= $this->max_pages; $x++ ): ?>
							<option value="<?php echo $x; ?>">
								<?php echo $x; ?>
							</option>
							<?php endfor; ?>
						</select>
						<button class="btn app-btn-primary">Goto</button>
					</div>
				</div>
			
			</div>
		<?php endif; ?>
		
	</div>
	
<?php	}

	private function pager() {
		
		if( $this->max_pages === 1 ) return;
		
		$pager = array();
		
		# -- [first page] --
		$pager['first'] = [ ( $this->current_page === 1 ) ? null : 1, 'angle-double-left' ];
		
		# -- [prev page] --
		$pager['prev'] = [ $this->current_page - 1, 'angle-left' ];
		if( $pager['prev'][0] < 1 ) $pager['prev'][0] = null;
		
		# -- [current page] -- 
		$pager['current'] = [ $this->current_page, null ];
		
		# -- [next page] --
		$pager['next'] = [ $this->current_page + 1, 'angle-right' ];
		if( $pager['next'][0] > $this->max_pages ) $pager['next'][0] = null;
		
		# -- [last page] --
		$pager['last'] = [ ( $this->current_page == $this->max_pages ) ? null : $this->max_pages, 'angle-double-right' ];
		
		return $pager;
		
	}
	
	public function get_error() {
		return $this->error;
	}

}