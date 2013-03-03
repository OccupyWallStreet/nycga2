<?php
if ( !current_user_can('manage_sites') )
	wp_die('You dont have permissions for this page');
	
global $wpdb;

//blogs by hour
$history = $wpdb->get_results("SELECT CONCAT(YEAR(`registered`), '-', MONTH( `registered` ), '-', DAY( `registered` ), ' ', HOUR( `registered` ), ':00:00') as time, count( * ) AS count
	FROM `$wpdb->blogs`
	WHERE spam = 0
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
	GROUP BY YEAR( registered ) , MONTH( registered ), DAY( registered ), HOUR( `registered` )
	ORDER BY registered ASC");
$blog_stats_week_ham = array();
foreach ($history as $stat) {
	$time = strtotime($stat->time) * 1000;
	$blog_stats_week_ham["$time"] = $stat->count;
}
$history = $wpdb->get_results("SELECT CONCAT(YEAR(`registered`), '-', MONTH( `registered` ), '-', DAY( `registered` ), ' ', HOUR( `registered` ), ':00:00') as time, count( * ) AS count
	FROM `$wpdb->blogs`
	WHERE spam = 1
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
	GROUP BY YEAR( registered ) , MONTH( registered ), DAY( registered ), HOUR( `registered` )
	ORDER BY registered ASC");
$blog_stats_week_spam = array();
foreach ($history as $stat) {
	$time = strtotime($stat->time) * 1000;
	$blog_stats_week_spam["$time"] = $stat->count;
}

//blogs by day
$history = $wpdb->get_results("SELECT CONCAT(YEAR(`registered`), '-', MONTH( `registered` ), '-', DAY( `registered` ), ' 00:00:00') as time, count( * ) AS count
	FROM `$wpdb->blogs`
	WHERE spam = 0
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
	GROUP BY YEAR( registered ) , MONTH( registered ), DAY( registered )
	ORDER BY registered ASC");
$blog_stats_month_ham = array();
foreach ($history as $stat) {
	$time = strtotime($stat->time) * 1000;
	$blog_stats_month_ham["$time"] = $stat->count;
}
$history = $wpdb->get_results("SELECT CONCAT(YEAR(`registered`), '-', MONTH( `registered` ), '-', DAY( `registered` ), ' 00:00:00') as time, count( * ) AS count
	FROM `$wpdb->blogs`
	WHERE spam = 1
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
	GROUP BY YEAR( registered ) , MONTH( registered ), DAY( registered )
	ORDER BY registered ASC");
$blog_stats_month_spam = array();
foreach ($history as $stat) {
	$time = strtotime($stat->time) * 1000;
	$blog_stats_month_spam["$time"] = $stat->count;
}

//blogs by month
$history = $wpdb->get_results("SELECT CONCAT(YEAR(`registered`), '-', MONTH( `registered` ), '-01 00:00:00') as time, count( * ) AS count
	FROM `$wpdb->blogs`
	WHERE spam = 0
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
	GROUP BY YEAR( registered ) , MONTH( registered )
	ORDER BY registered ASC");
$blog_stats_year_ham = array();
foreach ($history as $stat) {
	$time = strtotime($stat->time) * 1000;
	$blog_stats_year_ham["$time"] = $stat->count;
}
$history = $wpdb->get_results("SELECT CONCAT(YEAR(`registered`), '-', MONTH( `registered` ), '-01 00:00:00') as time, count( * ) AS count
	FROM `$wpdb->blogs`
	WHERE spam = 1
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
	GROUP BY YEAR( registered ) , MONTH( registered )
	ORDER BY registered ASC");
$blog_stats_year_spam = array();
foreach ($history as $stat) {
	$time = strtotime($stat->time) * 1000;
	$blog_stats_year_spam["$time"] = $stat->count;
}

//hourly averages
$history = $wpdb->get_results("SELECT HOUR( `registered` ) as time, count( * )/30 AS count
	FROM `$wpdb->blogs`
	WHERE spam = 0
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
	GROUP BY HOUR( `registered` )
	ORDER BY HOUR( `registered` ) ASC");
$blog_stats_hourly_ham = array();
foreach ($history as $stat) {
	$blog_stats_hourly_ham[$stat->time] = $stat->count;
}
$history = $wpdb->get_results("SELECT HOUR( `registered` ) as time, count( * )/30 AS count
	FROM `$wpdb->blogs`
	WHERE spam = 1
	AND registered >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
	GROUP BY HOUR( `registered` )
	ORDER BY HOUR( `registered` ) ASC");
$blog_stats_hourly_spam = array();
foreach ($history as $stat) {
	$blog_stats_hourly_spam[$stat->time] = $stat->count;
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	
	<?php	
	$ham_data = array();
	foreach ($blog_stats_week_ham as $time => $count) {
		$ham_data[] = "[$time, $count]";
	}
	$spam_data = array();
	foreach ($blog_stats_week_spam as $time => $count) {
		$spam_data[] = "[$time, $count]";
	}
	echo 'var blog_stats_week = [{ label: "Ham", color: 3, data: ['.implode(',', $ham_data).'] }, { label: "Spam", color: 2, data: ['.implode(',', $spam_data).'] }];'."\n";
	?>
	
	<?php	
	$ham_data = array();
	foreach ($blog_stats_month_ham as $time => $count) {
		$ham_data[] = "[$time, $count]";
	}
	$spam_data = array();
	foreach ($blog_stats_month_spam as $time => $count) {
		$spam_data[] = "[$time, $count]";
	}
	echo 'var blog_stats_month = [{ label: "Spam", color: 2, data: ['.implode(',', $spam_data).'] }, { label: "Ham", color: 3, data: ['.implode(',', $ham_data).'] }];'."\n";
	?>	
	
	<?php	
	$ham_data = array();
	foreach ($blog_stats_year_ham as $time => $count) {
		$ham_data[] = "[$time, $count]";
	}
	$spam_data = array();
	foreach ($blog_stats_year_spam as $time => $count) {
		$spam_data[] = "[$time, $count]";
	}
	echo 'var blog_stats_year = [{ label: "Spam", color: 2, data: ['.implode(',', $spam_data).'] }, { label: "Ham", color: 3, data: ['.implode(',', $ham_data).'] }];'."\n";
	?>
	
	<?php	
	$ham_data = array();
	foreach ($blog_stats_hourly_ham as $time => $count) {
		$ham_data[] = "[$time, $count]";
	}
	$spam_data = array();
	foreach ($blog_stats_hourly_spam as $time => $count) {
		$spam_data[] = "[$time, $count]";
	}
	echo 'var blog_stats_hourly = [{ label: "Spam", color: 2, data: ['.implode(',', $spam_data).'] }, { label: "Ham", color: 3, data: ['.implode(',', $ham_data).'] }];'."\n";
	?>
	
	var graph_options_hour = {
		xaxis: { mode: "time", timeformat: "%m/%d %h:00%p", twelveHourClock: true },
		yaxis: { min: 0, minTickSize: 1, tickDecimals: 0 },
		lines: { show: true },
		points: { show: false },
		legend: { show: true, backgroundOpacity: 0.5, position: "nw" },
		grid: { hoverable: true, clickable: false }
	};
	
	var graph_options_day = {
		xaxis: { mode: "time", timeformat: "%b %d" },
		yaxis: { min: 0, minTickSize: 1, tickDecimals: 0 },
		lines: { show: true, fill: true },
		series: { stack: true },
		points: { show: true },
		legend: { show: true, backgroundOpacity: 0.5, position: "nw" },
		grid: { hoverable: true, clickable: false }
	};
	
	var graph_options_month = {
		xaxis: { mode: "time", minTickSize: [1, "month"], timeformat: "%b %y" },
		yaxis: { min: 0, minTickSize: 1, tickDecimals: 0 },
		lines: { show: true, fill: true },
		series: { stack: true },
		points: { show: true },
		legend: { show: true, backgroundOpacity: 0.5, position: "nw" },
		grid: { hoverable: true, clickable: false }
	};
	
	var graph_options_hourly = {
		xaxis: { tickSize: 1, ticks: [ [0, "12AM"], [1, "1AM"], [2, "2AM"], [3, "3AM"], [4, "4AM"], [5, "5AM"], [6, "6AM"], [7, "7AM"], [8, "8AM"], [9, "9AM"], [10, "10AM"], [11, "11AM"], [12, "12PM"], [13, "1PM"], [14, "2PM"], [15, "3PM"], [16, "4PM"], [17, "5PM"], [18, "6PM"], [19, "7PM"], [20, "8PM"], [21, "9PM"], [22, "10PM"], [23, "11PM"] ] },
		lines: { show: true, fill: true },
		series: { stack: true },
		points: { show: false },
		legend: { show: true, backgroundOpacity: 0.5, position: "nw" }
	};
	
	//plot graphs
	jQuery.plot(jQuery("#blog_stats_week"), blog_stats_week, graph_options_hour);
	jQuery.plot(jQuery("#blog_stats_month"), blog_stats_month, graph_options_day);
	jQuery.plot(jQuery("#blog_stats_year"), blog_stats_year, graph_options_month);
	jQuery.plot(jQuery("#blog_stats_hourly"), blog_stats_hourly, graph_options_hourly);
	
	//handle window resizing
	jQuery(window).resize(function($) {
		jQuery.plot(jQuery("#blog_stats_week"), blog_stats_week, graph_options_hour);
		jQuery.plot(jQuery("#blog_stats_month"), blog_stats_month, graph_options_day);
		jQuery.plot(jQuery("#blog_stats_year"), blog_stats_year, graph_options_month);
		jQuery.plot(jQuery("#blog_stats_hourly"), blog_stats_hourly, graph_options_hourly);
	});
	
	//tooltips
	function showTooltip(x, y, contents) {
		$('<div id="tooltip">' + contents + '</div>').css( {
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

	var previousPoint = null;
	$("#blog_stats_month").bind("plothover", function (event, pos, item) {
		if (item) {
			if (previousPoint != item.datapoint) {
				previousPoint = item.datapoint;

				$("#tooltip").remove();
				var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);
				
				var label = (item.series.label == 'Ham') ? 'Total Blogs' : item.series.label;
				
				var dt = new Date(parseInt(x));
				var monthname=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
				var dayname=new Array("Sun","Mon","Tues","Wed","Thur","Fri","Sat");
				var date = dayname[dt.getDay()] + ",<br>" + monthname[dt.getMonth()] + " " + dt.getDate();
				showTooltip(item.pageX, item.pageY, label + " on " + date + ": " + parseInt(y));
			}
		} else {
			$("#tooltip").remove();
			previousPoint = null;
		}
	});
	$("#blog_stats_year").bind("plothover", function (event, pos, item) {
		if (item) {
			if (previousPoint != item.datapoint) {
				previousPoint = item.datapoint;

				$("#tooltip").remove();
				var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);
				
				var label = (item.series.label == 'Ham') ? 'Total Blogs' : item.series.label;
				
				var dt = new Date(parseInt(x));
				var monthname=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
				var date = monthname[dt.getMonth()] + " " + dt.getFullYear();
				showTooltip(item.pageX, item.pageY, label + " in<br>" + date + ": " + parseInt(y));
			}
		} else {
			$("#tooltip").remove();
			previousPoint = null;
		}
	});
});
</script>
<div class="wrap">
<div class="icon32"><img src="<?php echo plugins_url('/anti-splog/includes/icon-large.png'); ?>" /></div>
<h2><?php _e('Anti-Splog Statistics', 'ust') ?></h2>
<p><?php _e("These are site creation statistics for your multisite install.", 'ust') ?></p>	
<div class="metabox-holder">
	
	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Week Activity Summary', 'ust') ?></span></h3>
		<div class="inside">
			<div id="blog_stats_week" style="margin:20px;height:300px"><?php _e('No data available yet', 'ust') ?></div>
		</div>
	</div>
	
	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Month Activity Summary', 'ust') ?></span></h3>
		<div class="inside">
			<div id="blog_stats_month" style="margin:20px;height:300px"><?php _e('No data available yet', 'ust') ?></div>
		</div>
	</div>

	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Year Activity Summary', 'ust') ?></span></h3>
		<div class="inside">
			<div id="blog_stats_year" style="margin:20px;height:300px"><?php _e('No data available yet', 'ust') ?></div>
		</div>
	</div>

	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Hourly Averages (over the last month)', 'ust') ?></span></h3>
		<div class="inside">
			<div id="blog_stats_hourly" style="margin:20px;height:300px"><?php _e('No data available yet', 'ust') ?></div>
		</div>
	</div>
	
</div>
</div>