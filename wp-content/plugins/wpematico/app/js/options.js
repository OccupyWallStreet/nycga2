jQuery(document).ready( function($) {

	$('.handlediv').click(function(){
		$(this).parent().children('.inside').toggle();
	});

	$('.tag').click(function(){
		$('#campaign_template').attr('value',$('#campaign_template').attr('value')+$(this).html());
	});

	$('#campaign_imgcache').click(function() {
		if ( true == $('#campaign_imgcache').is(':checked')) {
			$('#nolinkimg').show();
		} else {
			$('#nolinkimg').hide();
		}
	});


	$('#checkfeeds').click(function() {
		$('.feedinput').each(function (el,item) {
			$('#ruedita').show();
			feed = $(item).attr('value');
			$(item).attr('style','Background:#CCC;');
			var data = {
				action: "test_feed",
				url: feed, 
				'cookie': encodeURIComponent(document.cookie)
			};
			$.post(ajaxurl, data, function(str){
				if(str==0){
					$(item).attr('style','Background:Red;');
					alert("Feed error: "+feed );
				}else{
					$(item).attr('style','Background:#75EC77;');
				}
				$('#ruedita').hide();
			});		
		}); 
		
	});
	$('.feedinput').focus(function() {
		$(this).attr('style','Background:#FFFFFF;');
	});

 if ( $('#title').val() == '' )
		$('#title').siblings('#title-prompt-text').css('visibility', '');
	$('#title-prompt-text').click(function(){
		$(this).css('visibility', 'hidden').siblings('#title').focus();
	});
	$('#title').blur(function(){
		if (this.value == '')
			$(this).siblings('#title-prompt-text').css('visibility', '');
	}).focus(function(){
		$(this).siblings('#title-prompt-text').css('visibility', 'hidden');
	}).keydown(function(e){
		$(this).siblings('#title-prompt-text').css('visibility', 'hidden');
		$(this).unbind(e);
	});

	$('.delete_label').click(function(){
		finput = $(this).attr('for');
		if (confirm('Are you sure you want delete this item from this campaign?')) {
			$('#'+finput).attr('value','');
			$(this).parent().hide();
		}
	});
	 //css('background-color', 'red');
	$('.w2cregex').click(function() {
		var cases = $(this).parent().children('.w2ccases');
		//if ( true == $(this).attr('checked')) {
		if ( true == $(this).is(':checked')) {
			cases.attr('checked','checked');
			cases.attr('disabled','disabled');
		}else{
			cases.removeAttr('checked');
			cases.removeAttr('disabled');
		}
	});
});

function wpe_addrewrite(text1,text2,text3,text4) {
	rewid=parseInt(jQuery('#rewid').attr('value'));
	var cad='<li class="jobtype-select" style="border-color:#CEE1EF; border-style:solid; border-width:2px; width:80%; margin:5px 0px 5px 40px; padding:0.5em 0.5em;"><label for="campaign_word_origin_'+rewid+'">'+text1+'</label><textarea class="large-text" id="campaign_word_origin_'+rewid+'" name="campaign_word_origin['+rewid+']" /></textarea><input name="campaign_word_option_regex['+rewid+']" id="campaign_word_option_regex_'+rewid+'" class="checkbox" value="1" type="checkbox" /><label for="campaign_word_option_regex_'+rewid+'"> '+text2+'</label><hr style="border-color:#CEE1EF; border-style:solid; border-width:2px;">';
	cad=cad+'<label for="campaign_word_option_rewrite_'+rewid+'"> '+text3+'</label><input name="campaign_word_option_rewrite['+rewid+']" id="campaign_word_option_rewrite_'+rewid+'" class="checkbox" value="1" type="checkbox" /><textarea class="large-text" id="campaign_word_rewrite_'+rewid+'" name="campaign_word_rewrite['+rewid+']" /></textarea><hr style="border-color:#CEE1EF; border-style:solid; border-width:2px;">';
	cad=cad+'<label for="campaign_word_option_relink_'+rewid+'"> '+text4+'</label><input name="campaign_word_option_relink['+rewid+']" id="campaign_word_option_relink_'+rewid+'" class="checkbox" value="1" type="checkbox" /><textarea class="large-text" id="campaign_word_relink_'+rewid+'" name="campaign_word_relink['+rewid+']" /></textarea></li>';
	jQuery('#rewrites_edit').append(cad);
	jQuery('#rewid').attr('value',rewid+1);
}
