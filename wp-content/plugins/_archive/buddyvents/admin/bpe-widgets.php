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

class Buddyvents_Admin_Widgets
{
	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function __construct()
	{
		if( defined( 'BP_VERSION' ) )
			add_action( 'wp_dashboard_setup', array( &$this, 'init' ), 1 );
	}
	
	/**
	 * Set it up
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function init()
	{
		global $bpe;

		if( ! is_super_admin() )
			return false;
		
		if( bpe_get_option( 'enable_tickets' ) === true )
		{
			if( current_user_can( 'bpe_manage_event_sales' ) )
				wp_add_dashboard_widget( 'bpe_dashboard_earnings', sprintf( __( 'Ticket Sales %s', 'events' ), zeroise( date( 'm' ), 2 ) .'/'. date( 'Y' ) ),  array( &$this, 'chart_widget' ) );
	
			if( current_user_can( 'bpe_manage_event_invoices' ) )
				wp_add_dashboard_widget( 'bpe_dashboard_invoices', __( 'Unpaid Invoices', 'events' ),  array( &$this, 'invoice_widget' ) );
		}

		if( bpe_get_option( 'approve_events' ) == true )
		{
			if( current_user_can( 'bpe_manage_event_approvals' ) )
				wp_add_dashboard_widget( 'bpe_dashboard_approve_events', __( 'Events to Approve', 'events' ),  array( &$this, 'approve_widget' ) );
		}

		if( current_user_can( 'bpe_manage_events' ) )
			wp_add_dashboard_widget( 'bpe_dashboard_latest_events', __( 'Latest Events', 'events' ),  array( &$this, 'events_widget' ) );
	}

	/**
	 * Display the latest 10 unpaid invoices
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function invoice_widget()
	{
		$url = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-invoices' ), 'admin.php' ) );
		$result = bpe_get_invoices( array( 'settled' => 'no', 'sent' => 'yes' ) );
		?>
        <style type="text/css">#unpaid-invoices td{line-height: 24px;}</style>
        <table id="unpaid-invoices" cellspacing="0" class="widefat post fixed">
            <thead>
                <tr>
                    <th class="manage-column" scope="col"><?php _e( 'User', 'events' ); ?></th>
                    <th class="manage-column" scope="col"><?php _e( 'Month', 'events' ); ?></th>
                    <th class="manage-column" scope="col"><?php _e( 'Amount', 'events' ); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="3" class="manage-column" scope="col"><a class="button" href="<?php echo esc_url( $url ) ?>"><?php _e( 'Invoices Overview', 'events' ); ?></a></th>
                </tr>
            </tfoot>
            <tbody>
            <?php if( ! empty( $result['invoices'] ) ) : ?>            
            
                <?php
                $counter = 1;
				foreach( $result['invoices'] as $invoice ) :

					foreach( (array)$invoice->datasets as $sale_entry ) :
						$amount += bpe_sale_get_commission( $sale_entry );
					endforeach;
    
                    $class = ( $counter % 2 == 0 ) ? '' : 'alternate';
                    ?>
                    <tr class="<?php echo $class ?>">
                        <td><strong><?php echo bp_core_get_userlink( $invoice->user_id ) ?></strong></td>
                        <td><?php echo $invoice->month ?></td>
                        <td><?php echo $invoice->datasets[0]->currency .' '. esc_html( number_format( $amount, 2 ) ) ?></td>
                    </tr>

                <?php
                $counter++;
				endforeach; ?>
                
            <?php else: ?>
            
                <tr><td colspan="3" style="text-align:center"><?php _e( 'No unpaid invoices were found.', 'events' ); ?></td></tr>
                
            <?php endif; ?>
            </tbody>
        </table>
        <?php
	}
	
	/**
	 * Display a chart with the latest earnings
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function chart_widget()
	{
		$month = date( 'm' );
		$year = date( 'Y' );
		$info = bpe_get_sales( array( 'month' => $month, 'year' => $year, 'status' => 'completed', 'page' => false, 'per_page' => false, ) );
		
		$entries = array();
		foreach( $info['sales'] as $sale ) :
			if( ! isset( $entries[$sale->currency] ) )
				$entries[$sale->currency]  = bpe_sale_get_commission( $sale );
			else
				$entries[$sale->currency] += bpe_sale_get_commission( $sale );
		endforeach;
		
		$cur_arr = array();
		foreach( $entries as $currency => $amount )
			 $cur_arr[] = $currency .' '. $amount;
		?>
        <div id="sales-chart">
        	<div id="choices"></div>
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
        <?php
		if( count( $cur_arr ) > 0 ) 
			printf( __( '<p>Totals: %s</p>', 'events' ), join( ' - ', (array)$cur_arr ) );
	}

	/**
	 * Display a widget with the latest events
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function events_widget()
	{
		$url = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER ), 'admin.php' ) );
		$result = bpe_get_events( array( 'restrict' => false, 'per_page' => 10, 'spam' => 2, 'future' => false, 'sort' => 'start_date_desc' ) );
		?>
        <style type="text/css">#latest-events td{line-height: 24px;}</style>
        <table id="latest-events" cellspacing="0" class="widefat post fixed">
            <thead>
                <tr>
                    <th class="manage-column" scope="col"><?php _e( 'Title', 'events' ); ?></th>
                    <th class="manage-column" scope="col"><?php _e( 'Creator', 'events' ); ?></th>
                    <th class="manage-column" scope="col"><?php _e( 'Category', 'events' ); ?></th>
                    <th class="manage-column" scope="col"><?php _e( 'Start Date', 'events' ); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="4" class="manage-column" scope="col"><a class="button" href="<?php echo esc_url( $url ) ?>"><?php _e( 'Events Overview', 'events' ); ?></a></th>
                </tr>
            </tfoot>
            <tbody>
            <?php if( ! empty( $result['events'] ) ) : ?>            
            
                <?php
                $counter = 1;
				foreach( $result['events'] as $event ) :
    
                    $class = ( $counter % 2 == 0 ) ? '' : 'alternate';
                    ?>
                    <tr class="<?php echo $class ?>">
                        <td><strong><a href="<?php echo esc_url( $url . '&paged=1&event='. bpe_get_event_id( $event ) ); ?>"><?php bpe_event_name( $event ) ?></a></strong></td>
                        <td><?php echo bp_core_get_userlink( bpe_get_event_user_id( $event ) ) ?></td>
                        <td><?php bpe_event_category( $event ) ?></td>
                        <td><?php bpe_event_start_date( $event ) ?></td>
                    </tr>

                <?php
                $counter++;
				endforeach; ?>
                
            <?php else: ?>
            
                <tr><td colspan="4" style="text-align:center"><?php _e( 'No events were found.', 'events' ); ?></td></tr>
                
            <?php endif; ?>
            </tbody>
        </table>
        <?php
	}

	/**
	 * Display a widget with the latest to be approved events
	 * 
	 * @package Admin
	 * @since 	2.0
	 */
	public function approve_widget()
	{
		$url = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-approve' ), 'admin.php' ) );

		$result = bpe_get_events( array(
			'restrict' 	=> false,
			'per_page'  => false,
			'page' 		=> 1,
			'future' 	=> false,
			'approved' 	=> 0
		) );
		?>
        <style type="text/css">
		.appdec{width:24px;height:24px;display:inline-block;text-indent:-9999px;}
		.approve{background:url(<?php echo EVENT_URLPATH .'admin/images/approve.png' ?>) no-repeat;}
		.decline{background:url(<?php echo EVENT_URLPATH .'admin/images/decline.png' ?>) no-repeat;}
		 #bpe-approve-events td{line-height: 24px;}
		</style>
        <table id="bpe-approve-events" cellspacing="0" class="widefat post fixed">
            <thead>
                <tr>
                    <th class="manage-column" scope="col"><?php _e( 'Title', 'events' ); ?></th>
                    <th class="manage-column" scope="col"><?php _e( 'Creator', 'events' ); ?></th>
                    <th class="manage-column" scope="col"><?php _e( 'Actions', 'events' ); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="manage-column" colspan="3" scope="col"><?php printf( _n( '%d Event to approve', '%d Events to approve', (int)$result['total'], 'events' ), (int)$result['total'] ) ?></th>
                </tr>
            </tfoot>
            <tbody>
            <?php if( ! empty( $result['events'] ) ) : ?>            
            
                <?php
                $counter = 1;
				foreach( $result['events'] as $event ) :
    
                    $class = ( $counter % 2 == 0 ) ? '' : 'alternate';
                    ?>
                    <tr class="<?php echo $class ?>">
                        <td><strong><a class="toggle-desc" href="#"><?php bpe_event_name( $event ) ?></a></strong></td>
                        <td><?php echo bp_core_get_userlink( bpe_get_event_user_id( $event ) ) ?></td>
                        <td>
                            <a class="appdec approve" href="<?php echo wp_nonce_url( $url .'&approved=true&eid='. bpe_get_event_id( $event ), 'bpe_approve_event' ) ?>"><?php _e( 'Approve', 'events' ); ?></a>
                            <a class="appdec decline" href="<?php echo wp_nonce_url( $url .'&approved=false&eid='. bpe_get_event_id( $event ), 'bpe_delete_event' ) ?>"><?php _e( 'Delete', 'events' ); ?></a>
                        </td>
                    </tr>
    
                <?php
                $counter++;
				endforeach; ?>
                
            <?php else: ?>
            
                <tr><td colspan="3" style="text-align:center"><?php _e( 'No events to be approved right now.', 'events' ); ?></td></tr>
                
            <?php endif; ?>
            </tbody>
        </table>
        <?php
	}
}
new Buddyvents_Admin_Widgets();
?>