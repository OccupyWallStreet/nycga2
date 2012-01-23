jQuery(document).ready(function($) {

	/* Settings Form Tabs */
	$('#tabctnr').tabs({ fx: {  opacity: 'toggle' } });
	
/*
  $('form#bp-groupblog-setup').submit(function() {
      var data = $(this).serialize();
      $.post(ajaxurl, data, function(response) {
          if (window.onbeforeunload) {
              window.onbeforeunload = null;
          }
          show_message(response);
      });
      return false;
  });

  $("form#bp-groupblog-setup :input").bind("change", function() {
      if (!window.onbeforeunload) {
          window.onbeforeunload = checkUnsavedPage;
      }
  });
*/
  
  /* Show P2 Theme Info */  
	if($('#theme :selected').val() == "p2|p2-buddypress") {
		$('#p2-settings').show();
	} else {
		$('#p2-settings').hide();
	}
	
	/* Show Template Page Info */ 
	if ($('.info-on').is(':checked')) {
		$('.info').addClass('shown').fadeIn( 300 );
		$('.enabled').show();
		$('.disabled').hide();		
	}	
	else if ($('.info-off').is(':checked')) {
		$('.info').addClass('hidden').hide();
		$('.enabled').hide();
		$('.disabled').show();
	}
	$('.info-on').click(function() {
		if ($('.info').hasClass('hidden')) {
			$('.info').addClass('shown').removeClass('hidden').hide().fadeIn( 300 );
			$('.enabled').show();
			$('.disabled').hide();			
		}
	});
	$('.info-off').click(function() {
		if ($('.info').hasClass('shown')) {
			$('.info').addClass('hidden').removeClass('shown').fadeOut( 300 );
			$('.enabled').hide();
			$('.disabled').show();
		}
	});
	
	/* Show Initialize Notification and Set Checkbox Value */                
	// Get the initial value
	var $el = $('#bp_groupblog_page_title');
	$el.data('oldVal',  $el.val() );
	$('.notice').css({'background-color': '#ffff66', 'padding': '5px 7px'})
	
	$el.focus(function(){
		$('.notice').fadeIn( 300 );
	});
	$el.blur(function(){
		$('.notice').fadeOut( 300 );
	});
		
/*
	$('form#bp-groupblog-setup').submit(function() {
	  //store new value
	  var $this = $('#bp_groupblog_page_title');
	  var newValue = $this.val();
  	if (newValue == $this.data('oldVal')) {
   		$('#bp_groupblog_intialize_redirect').val(0);            
  	} else {
  		$('#bp_groupblog_intialize_redirect').val(1);            
  	}
	});
*/

});

/* Settings Form Messages */
function show_message(n) {
    var html = jQuery(n).hide();
    jQuery('#wpbody').append(html);
    jQuery('#dialog').dialog({
        draggable: false,
        resizable: false,
        show: 'blind',                 
        hide: 'drop',
        close: function() {jQuery(this).remove()}, 
        autoOpen: true,
        modal: true
    });
    
    t = window.setTimeout(hide_message, 2500);
}
function checkUnsavedPage(e) {
    return 'You have unsaved changes!';    
}
function hide_message() {
    jQuery('#dialog').dialog('close');
	clearTimeout(t);
};