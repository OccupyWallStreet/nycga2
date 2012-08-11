jQuery(document).ready(function($) {
	$('.pinItButton').click(function(e){
		exec_pinmarklet();
		e.preventDefault();
	});
	$('.test-stats').click(function(){
	$('.stats-row').each(function(){
		var fb = $('.connect_widget_button_count_count',this).text();
		var gp = $('#aggregateCount.tW',this).text();
		var tw = $('.t-count #count',this).text();
		var tot = fb+gp+tw;
		$('td.stats-btn.total').html(tot);
	});
	});
	$('.stats-title a').click(function(){
		this.target = "_blank";
    });
	$('.dcssb-select-stats').change(function(){
		var $form = $(this).parent('form');
		$('.dcssb-loading',$form).show();
		var url = $form.attr('action');
		$.post(url, $form.serialize() ,function(data){
			$('.dcwp-response',$form).html(data);
			$('.dcssb-loading',$form).fadeOut(100,function(){
				var url=document.URL.split('&')[0];
				if(url != ''){
					window.location = url;
				} else {
					location.reload();
				}
			});
		});
	});
	$('#dcssb-stats tbody tr:odd').addClass('odd');

	mapColor('#blue');
	function heatMap(yr,yg,yb){
	
	// Function to get the Max value in Array
    Array.max = function( array ){
        return Math.max.apply( Math, array );
    };

    // get all values
    var counts= $('#dcssb-stats .stats-row.heatmap .stats-btn.single span').map(function() {
        return parseInt($(this).text());
    }).get();
	
	// return max value
	var max = Array.max(counts);
	
	xr = 255;
    xg = 255;
    xb = 255;
    n = 100;
	
	// add classes to cells based on nearest 10 value
	$('#dcssb-stats .stats-row.heatmap .stats-btn.single span').each(function(){
		var val = parseInt($(this).text());
		var pos = parseInt((Math.round((val/max)*100)).toFixed(0));
		red = parseInt((xr + (( pos * (yr - xr)) / (n-1))).toFixed(0));
		green = parseInt((xg + (( pos * (yg - xg)) / (n-1))).toFixed(0));
		blue = parseInt((xb + (( pos * (yb - xb)) / (n-1))).toFixed(0));
		clr = 'rgb('+red+','+green+','+blue+')';
		$(this).parent().css({backgroundColor:clr});
	});

}
function mapColor(color){

	switch(color)
		{
			case '#blue':
			yr = 84;
			yg = 117;
			yb = 171;
			break;
			case '#yellow':
			yr = 250;
			yg = 237;
			yb = 37;
			break;
			case '#green':
			yr = 118;
			yg = 246;
			yb = 68;
			break;
			case '#grey':
			yr = 100;
			yg = 100;
			yb = 100;
			break;
			default:
			yr = 243;
			yg = 32;
			yb = 117;
			break;
		}
	heatMap(yr,yg,yb);
}
});
