<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

class Buddyvents_Admin_Sales extends Buddyvents_Admin_Core
{
	private $filepath;

	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
    public function __construct()
	{
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-sales' ), 'admin.php' ) );
		
		parent::__construct();
    }

	/**
	 * Content of the sales tab
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
    protected function content()
	{
		global $bpe, $wpdb;
		
		$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
		if( $paged < 1 ) $paged = 1;
		
		$user_id 	= ( isset( $_GET['user'] 	) ) ? $_GET['user']   : false;
		$year 		= ( isset( $_GET['year'] 	) ) ? $_GET['year']   : '';
		$month 		= ( isset( $_GET['month'] 	) ) ? $_GET['month']  : '';
		$status 	= ( isset( $_GET['status'] 	) ) ? $_GET['status'] : false;
		$per_page 	= ( isset( $month ) ) ? 0 : 20;
		$page_links = '';

		$result = bpe_get_sales( array( 
			'month' 	=> $month,
			'year' 		=> $year,
			'seller_id' => $user_id,
			'status' 	=> 'completed',
			'requested' => $status,
			'page' 		=> $paged,
			'per_page' 	=> $per_page
		) );

		if( $per_page > 0 ):
			$page_links = paginate_links( array(
				'base' 		=> add_query_arg( 'paged', '%#%' ),
				'format' 	=> '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' 	=> ceil( $result['total'] / $per_page ),
				'current' 	=> $paged
			));
	
			$page_links_text = sprintf( '<div class="tablenav-pages"><span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s</div>',
					number_format_i18n( ( $paged - 1 ) * $per_page + 1 ),
					number_format_i18n( min( $paged * $per_page, $result['total'] ) ),
					number_format_i18n( $result['total'] ),
					$page_links
			);
		endif;
		
		$years = $wpdb->get_col( $wpdb->prepare( "SELECT year(sale_date) as year FROM {$bpe->tables->sales} WHERE status = 'completed' ORDER BY sale_date DESC" ) );
		$years = array_unique( (array)$years );

		$users = bpe_get_event_users();
		?>

        <form method="get" action="" id="filter-sales">
            <p class="filter-box">
                <input type="hidden" name="page" value="buddyvents-sales" />
                <input type="hidden" name="paged" value="<?php echo $paged ?>" />
                
                <select name="user" id="user">
                    <option value=""><?php _e( 'User', 'events' ); ?></option>
                    <?php foreach( (array)$users as $val ) { ?>
                        <option<?php if( $user_id == $val->user_id ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $val->user_id ) ?>"><?php echo esc_html( bp_core_get_user_displayname( $val->user_id ) ) ?></option>
                    <?php } ?>
                </select>

                <select name="month" id="month">
                    <option value=""><?php _e( 'Month', 'events' ); ?></option>
                    <?php for( $i = 1; $i <= 12; $i++ ) :
						$new_i = ( $i < 10 ) ? '0'. $i : $i;
						?>
                    	<option<?php if( $month == $i ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $new_i ) ?>"><?php echo esc_html( $this->month( $new_i ) ) ?></option>
					<?php endfor; ?>
                </select>
                
                <select name="year" id="year">
                    <option value=""><?php _e( 'Year', 'events' ); ?></option>
                    <?php foreach( $years as $y ) : ?>
                    	<option<?php if( $year == $y ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $y ) ?>"><?php echo esc_html( $y ) ?></option>
					<?php endforeach; ?>
                </select>

                <select name="status" id="status">
                    <option value=""><?php _e( 'Status', 'events' ); ?></option>
                   	<option<?php if( $status == 'open' ) echo ' selected="selected"'; ?> value="open"><?php _e( 'Open', 'events' ); ?></option>
                   	<option<?php if( $status == 'requested' ) echo ' selected="selected"'; ?> value="settled"><?php _e( 'Requested', 'events' ); ?></option>
                </select>

                <input type="submit" class="button" value="<?php _e( 'Filter sales', 'events' ); ?>"> <?php $this->standard_button() ?>
            </p>      
        </form>

        <form method="post" action="" id="posts-filter">
        
            <?php wp_nonce_field( 'bpe_sales_table' ) ?>
            
            <input type="hidden" name="month" value="<?php echo esc_attr( $month ) ?>" />
            <input type="hidden" name="year" value="<?php echo esc_attr( $year ) ?>" />
            <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ) ?>" />

			<?php if( ! empty( $month ) && ! empty( $year ) && $result['total'] > 0 ) :
				
				$non_requested = true;
				// only display if non-requested are present
				foreach( $result['sales'] as $sale )
				{
					if( $sale->requested == 1 )
					{
						$non_requested = false;
						break;	
					}
				}
				
				$current_timestamp = strtotime( 'now' );
				$then_timestamp = strtotime( '+5 days', mktime( 23, 59, 59, $month, date( 't', mktime( 0, 0, 0, $month, 1, $year ) ), $year ) );
				
				if( $current_timestamp >= $then_timestamp && $non_requested === true ) : ?>
            	<input type="submit" class="button-primary" id="send-invoices" name="send-invoices" value="<?php echo _n( 'Create invoice', 'Create invoices', $user_id, 'events' ); ?>" />
           		<?php endif; ?>
            <?php endif; ?>

            <div class="tablenav">
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
            <table cellspacing="0" class="widefat post fixed">
                <thead>
                    <tr>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Date', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Quantity', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Currency', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Single Price', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Commission', 'events' ); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Date', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Status', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Quantity', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Currency', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Single Price', 'events' ); ?></th>
                        <th class="manage-column" scope="col"><?php _e( 'Commission', 'events' ); ?></th>
                    </tr>
                </tfoot>
                <tbody>
				<?php if( ! empty( $result['sales'] ) ) : ?>            
                
					<?php
                    $counter = 1;
					foreach( $result['sales'] as $sale ) :
        
						$class = ( $counter % 2 == 0 ) ? '' : 'alternate';
						?>
                        <tr class="<?php echo $class ?>">
                            <td>
								<?php echo bp_core_get_userlink( $sale->seller_id ) ?>
                                <?php if( $sale->requested == false ) : ?><input type="hidden" name="sale_ids[]" value="<?php echo esc_attr( $sale->id ) ?>" /><?php endif; ?>
                            </td>
                            <td><?php echo mysql2date( bpe_get_option( 'date_format' ), $sale->sale_date ) ?></td>
                            <td><?php echo ( $sale->requested == true ) ? __( 'Requested', 'events' ) : __( 'Open', 'events' ); ?></td>
                            <td><?php echo $sale->quantity ?></td>
                            <td><?php echo $sale->currency ?></td>
                            <td><?php echo number_format( $sale->single_price, 2 ) ?></td>
                            <td><?php echo $sale->currency .' '. number_format( bpe_sale_get_commission( $sale ), 2 ) ?></td>
                       </tr>
        
                    <?php
                    $counter++;
					endforeach; ?>
                    
				<?php else: ?>
                
                    <tr><td colspan="7" style="text-align:center"><?php _e( 'No sales were found.', 'events' ); ?></td></tr>
                    
                <?php endif; ?>
                </tbody>
            </table>
            <div class="tablenav">
                <?php if( $page_links ) : ?><div class="tablenav-pages"><?php echo $page_links_text ?></div><?php endif; ?>
            </div>
            
            <?php
			$info = bpe_get_sales( array(
				'month' 	=> $month,
				'year' 		=> $year,
				'seller_id' => $user_id,
				'status' 	=> 'completed',
				'requested' => $status,
				'page' 		=> false,
				'per_page' 	=> false
			) );
			
			$entries = array();
			foreach( (array)$info['sales'] as $sale ) :
				if( ! isset( $entries[$sale->currency] ) )
					$entries[$sale->currency]  = bpe_sale_get_commission( $sale );
				else
					$entries[$sale->currency] += bpe_sale_get_commission( $sale );
			endforeach;
			
			$com = ( ( ! empty( $month ) ) ? $this->month( $month ) .' ' : '' ) . $year . ( ( ! empty( $user_id ) ) ? ' '. sprintf( __( 'for %s', 'events' ), bp_core_get_user_displayname( $user_id ) ) : '' );
			$settled = ( ! empty( $status ) ) ? ' ('. ( ( $status == 'open' ) ? __( 'open', 'events' ) : __( 'settled', 'events' ) ) .')' : '';
			
			if( ! $com )
				$title = sprintf( __( 'All-time Commission%s', 'events' ), $settled );
			else
				$title = sprintf( __( 'Total Commission: %s%s', 'events' ), $com, $settled );
			?>
			<table id="currency-overview" class="widefat" cellspacing="0">
				<thead>
					<tr>
						<th class="manage-column" scope="col" colspan="2"><?php echo esc_html( $title ) ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class="manage-column" scope="col" colspan="2">&nbsp;</th>
					</tr>
				</tfoot>
				<tbody>
				<?php
				if( $entries ) :
					$counter = 1;
					foreach( $entries as $currency => $commission ) :
						$class = ( $counter % 2 == 0 ) ? '' : 'alternate';
						?>
                        <tr class="<?php echo $class ?>">
							<td><?php echo esc_html( $currency ) ?></td>
							<td><?php echo esc_html( number_format( $commission, 2 ) ) ?></td>
						</tr>
						<?php
						$counter++;
					endforeach;
				else :
					?>
					<tr>
						<td colspan="2" style="text-align:center"><?php _e( 'No commission.', 'events' ) ?></td>
					</tr>
					<?php					
				endif;
				?>
				</tbody>
			</table>
            
            <div id="sales-chart">
                <div id="choices" style="padding-left:16px;"></div>
            	<div id="chart-placeholder" style="width:100%;height:300px;"></div>
            </div>

            <script type="text/javascript">
			jQuery(document).ready(function() {
				var datasets = {
					<?php bpe_sales_chart_points( $info['sales'], $month, $year ) ?>
				};
				var i = 0;
				jQuery.each(datasets, function(key, val) {
					val.color = i;
					++i;
				});

				var display = '';
				if( i <= 1 ) {
					display = ' style="display:none"';
				}
				
				var choiceContainer = jQuery("#choices");
				jQuery.each(datasets, function(key, val) {
					choiceContainer.append('<label for="id' + key + '"'+ display +'><input type="checkbox" name="'+ key +'" checked="checked" id="id' + key + '"> '+ val.label + '</label>');
				});
				choiceContainer.find("input").click(plotAccordingToChoices);

				function plotAccordingToChoices() {
					var data = [];

					var options = {
						series: {
							lines: { show: true },
							points: { show: true }
						},
						grid: {
							backgroundColor: { colors: ["#fff", "#eee"] },
							clickable: true,
							hoverable: true
						},
						xaxis: { mode: "time", timeformat: "%d/%m/%y" },
						yaxis: { min: 0 }
					};
			
					choiceContainer.find("input:checked").each(function () {
						var key = jQuery(this).attr("name");
						if (key && datasets[key])
							data.push(datasets[key]);
					});
			
					if (data.length > 0)
						jQuery.plot(jQuery('#chart-placeholder'), data, options);
				}
			
				plotAccordingToChoices();

				var previousPoint = null;
				jQuery("#chart-placeholder").bind("plothover", function (event, pos, item) {
					if (item) {
						if (previousPoint != item.dataIndex) {
							previousPoint = item.dataIndex;
							jQuery("#tooltip").remove();
							var x = item.datapoint[0].toFixed(2), y = item.datapoint[1].toFixed(2), d = new Date(item.datapoint[0]);
							var month = d.getMonth() + 1;
							displayTT(item.pageX, item.pageY, item.series.label +' '+ y +' - '+ d.getDate() +'/'+ month +'/'+ d.getFullYear() );
						}
					} else {
						jQuery("#tooltip").remove();
						previousPoint = null;
					}
				});
				
				function displayTT(x, y, contents) {
					jQuery('<div id="tooltip">' + contents + '</div>').css( {
						position: 'absolute',
						display: 'none',
						top: y + 5,
						left: x + 5,
						border: '1px solid #fdd',
						padding: '2px',
						'background-color': '#fee',
						opacity: 0.80
					}).appendTo("body").fadeIn(200);
				} 
			});
			</script>
        </form>
		<?php
	}
	
	/**
	 * Helper to get translatable month names
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	private function month( $index )
	{
		$months = array(
			'01' => __( 'January',  'events' ),
			'02' => __( 'February', 'events' ),
			'03' => __( 'March', 	'events' ),
			'04' => __( 'April', 	'events' ),
			'05' => __( 'May', 		'events' ),
			'06' => __( 'June', 	'events' ),
			'07' => __( 'July', 	'events' ),
			'08' => __( 'August', 	'events' ),
			'09' => __( 'September','events' ),
			'10' => __( 'October',	'events' ),
			'11' => __( 'November', 'events' ),
			'12' => __( 'December', 'events' )
		);
		
		return $months[$index];	
	}
	
	/**
	 * Display some standard links
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	private function standard_button()
	{
		$user = ( isset( $_GET['user'] ) ) ? $_GET['user'] : '';
		
		echo ' <a class="button" href="'. $this->filepath . '&paged=1&month='. date( 'm' ) .'&year='. date( 'Y' ) .'&user='. $user .'">'. __( 'Current Month', 'events' ) .'</a>';
		echo ' <a class="button" href="'. $this->filepath . '&paged=1">'. __( 'All-Time', 'events' ) .'</a>';
	}
}
?>