<?php
//Builds a table of bookings, still work in progress...
class EM_Bookings_Table{
	/**
	 * associative array of collumns that'll be shown in order from left to right
	 * 
	 * * key - collumn name in the databse, what will be used when searching
	 * * value - label for use in collumn headers 
	 * @var array
	 */
	protected $cols = array('display_name','event_name','user_email','dbem_phone','booking_spaces','booking_status','actions');
	/**
	 * Index key used for looking up status information we're filtering in the booking table 
	 * @var string
	 */
	protected $string = 'needs-attention';
	/**
	 * Associative array of status information.
	 * 
	 * * key - status index value
	 * * value - associative array containing keys
	 * ** label - the label for use in filter forms
	 * ** search - array or integer status numbers to search 
	 * 
	 * @var array
	 */
	protected $statuses = array();
	/**
	 * Maximum number of rows to show
	 * @var int
	 */
	public $limit = 20;
	public $scope = 'future';
	
	function __construct(){
		$this->statuses = array(
			'all' => array('label'=>__('All','dbem'), 'search'=>false),
			'pending' => array('label'=>__('Pending','dbem'), 'search'=>0),
			'confirmed' => array('label'=>__('Confirmed','dbem'), 'search'=>1),
			'cancelled' => array('label'=>__('Cancelled','dbem'), 'search'=>2),
			'rejected' => array('label'=>__('Rejected','dbem'), 'search'=>3),
			'needs-attention' => array('label'=>__('Needs Attention','dbem'), 'search'=>array(0,5)),
			'incomplete' => array('label'=>__('Incomplete Bookings','dbem'), 'search'=>array(0,4,5)),
			'awaiting-online' => array('label'=>__('Awaiting Online Payment','dbem'), 'search'=>4),
			'awaiting-payment' => array('label'=>__('Awaiting Offline Payment','dbem'), 'search'=>5)
		);
		//Set basic vars
		$this->order = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'ASC';
		$this->orderby = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'booking_name';
		$this->limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
		$this->page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
		$this->offset = ( $this->page > 1 ) ? ($this->page-1)*$this->limit : 0;
		$this->scope = ( !empty($_REQUEST['scope']) && array_key_exists($_REQUEST ['scope'], em_get_scopes()) ) ? $_REQUEST ['scope']:get_option('dbem_default_bookings_search','future');
		$this->status = ( !empty($_REQUEST['status']) && array_key_exists($_REQUEST['status'], $this->statuses) ) ? $_REQUEST['status']:get_option('dbem_default_bookings_search','needs-attention');
		//build template of possible collumns
		$this->cols_template = apply_filters('em_bookings_table_cols_template', array(
			'display_name'=>__('Booker','dbem'),
			'event_name'=>__('Event','dbem'),
			'user_email'=>__('E-mail','dbem'),
			'dbem_phone'=>__('Phone Number','dbem'),
			'booking_spaces'=>__('Spaces','dbem'),
			'booking_status'=>__('Status','dbem'),
			'booking_price'=>__('Price','dbem'),
			'actions' => __('Actions','dbem')
		));
		//calculate collumns if post requests		
		if( !empty($_REQUEST ['cols']) && !is_array($_REQUEST ['cols']) ){
			$this->cols = explode(',',$_REQUEST['cols']);
		}elseif( !empty($_REQUEST ['cols']) ){
			$this->cols = $_REQUEST['cols'];
		}
		foreach($this->cols as $col_key){
			if( !array_key_exists($col_key, $this->cols_template)){
				unset($this->cols[$col_key]);
			}
		}
	}

	
	/**
	 * @return EM_Person|false
	 */
	function get_person(){
		global $EM_Person;
		if( !empty($this->person) && is_object($this->person) ){
			return $this->person;
		}elseif( !empty($_REQUEST['person_id']) && !empty($EM_Person) && is_object($EM_Person) ){
			return $EM_Person;
		}
		return false;
	}
	/**
	 * @return EM_Ticket|false
	 */
	function get_ticket(){
		global $EM_Ticket;
		if( !empty($this->ticket) && is_object($this->ticket) ){
			return $this->ticket;
		}elseif( !empty($EM_Ticket) && is_object($EM_Ticket) ){
			return $EM_Ticket;
		}
		return false;
	}
	/**
	 * @return $EM_Event|false
	 */
	function get_event(){
		global $EM_Event;
		if( !empty($this->event) && is_object($this->event) ){
			return $this->event;
		}elseif( !empty($EM_Event) && is_object($EM_Event) ){
			return $EM_Event;
		}
		return false;
	}
	
	function get_bookings($force_refresh = true){	
		if( empty($this->bookings) || $force_refresh ){
			$this->events = array();
			$EM_Ticket = $this->get_ticket();
			$EM_Event = $this->get_event();
			$EM_Person = $this->get_person();
			if( $EM_Ticket !== false ){
				//searching bookings with a specific ticket
				$this->bookings = $EM_Ticket->get_bookings();
				$this->bookings_count = (is_array($this->bookings->bookings)) ? count($this->bookings->bookings):0;
				$this->events[$EM_Ticket->event_id] = $EM_Ticket->get_event();
			}elseif( $EM_Event !== false ){
				//bookings for an event
				$args = array('event'=>$EM_Event->event_id,'scope'=>false,'status'=>$this->get_status_search(),'order'=>$this->order,'orderby'=>$this->orderby);
				$this->bookings_count = EM_Bookings::count($args);
				$this->bookings = EM_Bookings::get(array_merge($args, array('limit'=>$this->limit,'offset'=>$this->offset)));
				$this->events[$EM_Event->event_id] = $EM_Event;
			}elseif( $EM_Person !== false ){
				$args = array('person'=>$EM_Person->ID,'scope'=>$this->scope,'status'=>$this->get_status_search(),'order'=>$this->order,'orderby'=>$this->orderby);
				$this->bookings_count = EM_Bookings::count($args);
				$this->bookings = EM_Bookings::get(array_merge($args, array('limit'=>$this->limit,'offset'=>$this->offset)));
				foreach($this->bookings->bookings as $EM_Booking){
					//create event
					if( !array_key_exists($EM_Booking->event_id,$this->events) ){
						$this->events[$EM_Booking->event_id] = new EM_Event($EM_Booking->event_id);
					}
				}
			}else{
				//all bookings for a status
				$args = array('status'=>$this->get_status_search(),'scope'=>$this->scope,'order'=>$this->order,'orderby'=>$this->orderby);
				$this->bookings_count = EM_Bookings::count($args);
				$this->bookings = EM_Bookings::get(array_merge($args, array('limit'=>$this->limit,'offset'=>$this->offset)));
				//Now let's create events and bookings for this instead of giving each booking an event
				foreach($this->bookings->bookings as $EM_Booking){
					//create event
					if( !array_key_exists($EM_Booking->event_id,$this->events) ){
						$this->events[$EM_Booking->event_id] = new EM_Event($EM_Booking->event_id);
					}
				}
			}
		}
		return $this->bookings;
	}
	
	function get_count(){
		return $this->bookings_count;
	}
	
	function get_status_search(){
		if(is_array($this->statuses[$this->status]['search'])){
			return implode(',',$this->statuses[$this->status]['search']);
		}
		return $this->statuses[$this->status]['search'];
	}
	
	function output(){
		do_action('em_bookings_table_header',$this); //won't be overwritten by JS	
		$this->output_content();
		do_action('em_bookings_table_footer',$this); //won't be overwritten by JS	
	}
	
	/**
	 * Used to output AJAX content as well as normal in-table content 
	 */
	function output_content(){
		$EM_Ticket = $this->get_ticket();
		$EM_Event = $this->get_event();
		$EM_Person = $this->get_person();
		$this->get_bookings(true); //get bookings and refresh
		?>
		<div class='em-bookings-table em_obj' id="em-bookings-table">
			<div id="em-bookings-table-settings" class="em-bookings-table-overlay" style="display:none;">
				<form id="em-bookings-table-settings-form" class="em-bookings-table-form" action="" method="post">
					<h4><?php _e('Bookings Table Settings','dbem'); ?></h4>
					<p><?php _e('Modify what information is displayed in this booking table.','dbem') ?></p>
					<div id="em-bookings-table-settings-form-cols">
					<p><strong><?php _e('Collumns to show','dbem')?></strong></p>
					<?php foreach( $this->cols_template as $col_key => $col_data ): ?>
						<input id="em-bookings-col-<?php echo $col_key; ?>" type="checkbox" name="cols[]" value="<?php echo $col_key; ?>" <?php if( in_array($col_key, $this->cols) ) echo 'checked="checked"'; ?> />
						<label for="em-bookings-col-<?php echo $col_key; ?>"><?php echo $col_data; ?></label>
						<br />
					<?php endforeach; ?>
					</div>
					<p><input type="submit" class="button-primary" value="<?php echo __('Save Settings','dbem'); ?>" /></p>
				</form>
			</div>
			<?php /*
			<div id="em-bookings-table-export" class="em-bookings-table-overlay" style="display:none;">
				<form id="em-bookings-table-export-form" class="em-bookings-table-form" action="" method="post">
					<h4><?php _e('Export Bookings','dbem'); ?></h4>
					<p><?php _e('Select the options below and export all the bookings you have currently filtered into a CSV spreadsheet format.','dbem') ?></p>
					<p><strong><?php _e('Collumns to export','dbem')?></strong></p>
					<div id="em-bookings-table-settings-form-mode">
						<input type="radio" name="export_mode" value="tickets" /> <?php _e('Tickets','dbem'); ?>&nbsp;&nbsp;
						<input type="radio" name="export_mode" value="bookings" /> <?php _e('Bookings (Grouped)','dbem'); ?>
					</div>
					<div id="em-bookings-table-settings-form-cols">
						<p><strong><?php _e('Collumns to export','dbem')?></strong></p>
						<?php foreach( $this->cols_template as $col_key => $col_data ): ?>
							<input id="em-bookings-col-<?php echo $col_key; ?>" type="checkbox" name="cols[]" value="<?php echo $col_key; ?>" <?php if( in_array($col_key, $this->cols) ) echo 'checked="checked"'; ?> />
							<label for="em-bookings-col-<?php echo $col_key; ?>"><?php echo $col_data; ?></label>
							<br />
						<?php endforeach; ?>
					</div>
					<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('export_bookings_csv'); ?>" />
					<input type="hidden" name="action" value="export_bookings_csv" />
					<p><input type="submit" class="button-primary" value="<?php echo __('Export Bookings Report','dbem'); ?>" /></p>
				</form>
			</div>
			*/ ?>
			<form class='bookings-filter' method='post' action='<?php bloginfo('wpurl') ?>/wp-admin/edit.php'>
				<?php if( $EM_Event !== false ): ?>
				<input type="hidden" name="event_id" value='<?php echo $EM_Event->event_id; ?>' />
				<?php endif; ?>
				<?php if( $EM_Ticket !== false ): ?>
				<input type="hidden" name="ticket_id" value='<?php echo $EM_Ticket->ticket_id; ?>' />
				<?php endif; ?>
				<?php if( $EM_Person !== false ): ?>
				<input type="hidden" name="person_id" value='<?php echo $EM_Person->ID; ?>' />
				<?php endif; ?>
				<input type="hidden" name="pno" value='<?php echo $this->page ?>' />
				<input type="hidden" name="order" value='<?php echo $this->order ?>' />
				<input type="hidden" name="orderby" value='<?php echo $this->orderby ?>' />
				<input type="hidden" name="_wpnonce" value="<?php echo ( !empty($_REQUEST['_wpnonce']) ) ? $_REQUEST['_wpnonce']:wp_create_nonce('em_bookings_table'); ?>" />
				<input type="hidden" name="action" value="em_bookings_table" />
				<input type="hidden" name="cols" value="<?php echo implode(',', $this->cols); ?>" />
				
				<div class='tablenav'>
					<div class="alignleft actions">
						<?php /*
						<a href="#" class="em-bookings-table-export" id="em-bookings-table-export-trigger" rel="#em-bookings-table-export"></a>
						*/ ?>
						<a href="#" class="em-bookings-table-settings" id="em-bookings-table-settings-trigger" rel="#em-bookings-table-settings"></a>
						<?php if( $EM_Event === false ): ?>
						<select name="scope">
							<?php
							foreach ( em_get_scopes() as $key => $value ) {
								$selected = "";
								if ($key == $this->scope)
									$selected = "selected='selected'";
								echo "<option value='$key' $selected>$value</option>  ";
							}
							?>
						</select>
						<?php endif; ?>
						<select name="limit">
							<option value="<?php echo $this->limit ?>"><?php echo sprintf(__('%s Rows','dbem'),$this->limit); ?></option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="25">25</option>
							<option value="50">50</option>
							<option value="100">100</option>
						</select>
						<select name="status">
							<?php
							foreach ( $this->statuses as $key => $value ) {
								$selected = "";
								if ($key == $this->status)
									$selected = "selected='selected'";
								echo "<option value='$key' $selected>{$value['label']}</option>  ";
							}
							?>
						</select>
						<input id="post-query-submit" class="button-secondary" type="submit" value="<?php _e ( 'Filter' )?>" />
						<?php if( $EM_Event !== false ): ?>
						<?php _e('Displaying Event','dbem'); ?> : <?php echo $EM_Event->name; ?>
						<?php elseif( $EM_Person !== false ): ?>
						<?php _e('Displaying User','dbem'); echo ' : '.$EM_Person->get_name(); ?>
						<?php endif; ?>
					</div>
					<?php 
					if ( $this->bookings_count >= $this->limit ) {
						$bookings_nav = em_admin_paginate( $this->bookings_count, $this->limit, $this->page, array(),'#%#%','#');
						echo $bookings_nav;
					}
					?>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<div class='table-wrap'>
				<table id='dbem-bookings-table' class='widefat post '>
					<thead>
						<tr>
							<?php /*						
							<th class='manage-column column-cb check-column' scope='col'>
								<input class='select-all' type="checkbox" value='1' />
							</th>
							*/ ?>
							<th class='manage-column' scope='col'><?php echo implode("</th><th class='manage-column' scope='col'>", $this->get_headers()); ?></th>
						</tr>
					</thead>
					<?php if( $this->bookings_count > 0 ): ?>
					<tbody>
						<?php 
						$rowno = 0;
						$event_count = (!empty($event_count)) ? $event_count:0;
						foreach ($this->bookings->bookings as $EM_Booking) {
							?>
							<tr>
								<?php  /*
								<th scope="row" class="check-column" style="padding:7px 0px 7px;"><input type='checkbox' value='<?php echo $EM_Booking->booking_id ?>' name='bookings[]'/></th>
								*/ ?>
								<td><?php echo implode('</td><td>', $this->get_row($EM_Booking)); ?></td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<?php else: ?>
						<tbody>
							<tr><td scope="row" colspan="<?php echo count($this->cols); ?>"><?php _e('No bookings.', 'dbem'); ?></td></tr>
						</tbody>
					<?php endif; ?>
				</table>
				</div>
			</form>
			<?php if( !empty($bookings_nav) && $this->bookings_count >= $this->limit ) : ?>
			<div class='tablenav'>
				<?php echo $bookings_nav; ?>
				<div class="clear"></div>
			</div>
			<?php endif; ?>
		</div>
		<br class="clear" />
		<?php
	}
	
	function get_headers(){
		$headers = array();
		foreach($this->cols as $col){
			if( $col == 'actions' ){
				$headers[$col] = '&nbsp;';
			}elseif(array_key_exists($col, $this->cols_template)){
				/* for later - col ordering!
				if($this->orderby == $col){
					if($this->order == 'ASC'){
						$headers[] = '<a class="em-bookings-orderby" href="#'.$col.'">'.$this->cols_template[$col].' (^)</a>';
					}else{
						$headers[] = '<a class="em-bookings-orderby" href="#'.$col.'">'.$this->cols_template[$col].' (d)</a>';
					}
				}else{
					$headers[] = '<a class="em-bookings-orderby" href="#'.$col.'">'.$this->cols_template[$col].'</a>';
				}
				*/
				$headers[$col] = $this->cols_template[$col];
			}else{
				$header = apply_filters('em_bookings_table_headers_'.$col, '', $this);
				if( !empty($header) ){
					$headers[$col] = $header;
				}
			}
		}
		return $headers;
	}
	
	/**
	 * @param EM_Booking $EM_Booking
	 * @return array()
	 */
	function get_row($EM_Booking, $csv = false){
		$cols = array();
		foreach($this->cols as $col){
			//is col a user col or else?
			//TODO fix urls so this works in all pages in front as well
			if( $col == 'user_email' ){
				$cols[] = $EM_Booking->get_person()->user_email;
			}elseif($col == 'dbem_phone'){
				$cols[] = $EM_Booking->get_person()->phone;
			}elseif($col == 'display_name'){
				if( $csv ){
					$cols[] = $EM_Booking->get_person()->get_name();
				}else{
					$cols[] = '<a href="'.EM_ADMIN_URL.'&amp;page=events-manager-bookings&amp;person_id='.$EM_Booking->person->ID.'">'. $EM_Booking->person->get_name() .'</a>';
				}
			}elseif($col == 'event_name'){
				if( $csv ){
					$cols[] = $EM_Booking->get_event()->event_name;
				}else{
					$cols[] = '<a href="'.EM_ADMIN_URL.'&amp;page=events-manager-bookings&amp;event_id='.$EM_Booking->event_id.'">'. $this->events[$EM_Booking->event_id]->name .'</a>';
				}
			}elseif($col == 'booking_price'){
				$cols[] = $EM_Booking->get_price(false,true);
			}elseif($col == 'booking_status'){
				$cols[] = $EM_Booking->get_status(true);
			}elseif($col == 'actions'){
				$cols[] = implode(' | ', $this->get_booking_actions($EM_Booking));
			}elseif( in_array($col, array('booking_spaces')) ){
				$cols[] = $EM_Booking->$col;
			}else{
				$col = apply_filters('em_bookings_table_rows_col_'.$col, '', $EM_Booking, $this, $csv);
				if( !empty($col) ){
					$cols[] = $col;
				}
			}	
		}
		return $cols;
	}
	
	function get_row_csv($EM_Booking){
		return $this->get_row($EM_Booking, true);
	}
	
	/**
	 * @param EM_Booking $EM_Booking
	 * @return mixed
	 */
	function get_booking_actions($EM_Booking){
		$booking_actions = array();
		switch($EM_Booking->booking_status){
			case 0:
				$booking_actions = array(
					'approve' => '<a class="em-bookings-approve" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_approve', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Approve','dbem').'</a>',
					'reject' => '<a class="em-bookings-reject" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_reject', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Reject','dbem').'</a>',
					'delete' => '<span class="trash"><a class="em-bookings-delete" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_delete', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','dbem').'</a></span>',
					'edit' => '<a class="em-bookings-edit" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','dbem').'</a>',
				);
				break;
			case 1:
				$booking_actions = array(
					'unapprove' => '<a class="em-bookings-unapprove" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_unapprove', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Unpprove','dbem').'</a>',
					'reject' => '<a class="em-bookings-reject" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_reject', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Reject','dbem').'</a>',
					'delete' => '<span class="trash"><a class="em-bookings-delete" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_delete', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','dbem').'</a></span>',
					'edit' => '<a class="em-bookings-edit" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','dbem').'</a>',
				);
				break;
			case 2:
				$booking_actions = array(
					'unapprove' => '<a class="em-bookings-unapprove" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_unapprove', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Unpprove','dbem').'</a>',
					'approve' => '<a class="em-bookings-approve" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_approve', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Approve','dbem').'</a>',
					'delete' => '<span class="trash"><a class="em-bookings-delete" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_delete', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','dbem').'</a></span>',
					'edit' => '<a class="em-bookings-edit" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','dbem').'</a>',
				);
				break;
			case 3:
				$booking_actions = array(
					'unapprove' => '<a class="em-bookings-unapprove" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_unapprove', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Unpprove','dbem').'</a>',
					'approve' => '<a class="em-bookings-approve" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_approve', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Approve','dbem').'</a>',
					'delete' => '<span class="trash"><a class="em-bookings-delete" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_delete', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','dbem').'</a></span>',
					'edit' => '<a class="em-bookings-edit" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','dbem').'</a>',
				);
				break;
			case 4:
				$booking_actions = apply_filters('em_bookings_table_booking_actions_4',array(
					'delete' => '<span class="trash"><a class="em-bookings-delete" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_delete', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','dbem').'</a></span>',
				), $EM_Booking);
				break;
			case 5:
				$booking_actions = apply_filters('em_bookings_table_booking_actions_5',array(
					'delete' => '<span class="trash"><a class="em-bookings-delete" href="'.em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>'bookings_delete', 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','dbem').'</a></span>',
				), $EM_Booking);
				break;
			default:
				$booking_actions = apply_filters('em_bookings_table_booking_actions_'.$EM_Booking->booking_status ,array(), $EM_Booking);
				break;
				
		}
		return apply_filters('em_bookings_table_cols_col_action', $booking_actions, $EM_Booking);
	}
}
?>