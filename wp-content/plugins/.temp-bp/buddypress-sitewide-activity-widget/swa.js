jQuery(document).ready(function(){
var j=jQuery;
var jq=jQuery;
j(".widget_bp_swa_widget div.pagination-links a").live("click",function(){
     var parent=j(this).parents(".widget_bp_swa_widget").get(0);
     parent=j(parent);//cast as jquery object
	var page=get_var_in_url(j(this).attr("href"),"acpage");
	//determine current scope
	var scope='';
	if(j("#activity-filter-links li",parent).get(0))
	{var scope_anchor=j("#activity-filter-links li.selected a",parent);
	if(scope_anchor.get(0))
		scope=get_var_in_url(j(scope_anchor).attr('href'),"afilter");

	}
fetch_and_show_activity(page,scope,parent);
return false;
});

function fetch_and_show_activity(page,scope,local_scope){
local_scope=j(local_scope);

	var per_page=j("#swa_per_page",local_scope).val();
	var max_items=j("#swa_max_items",local_scope).val();
	var included_components=j("#swa_included_components",local_scope).val();
	var excluded_components=j("#swa_excluded_components",local_scope).val();
	var show_avatar=j("#swa_show_avatar",local_scope).val();
	var show_filters=j("#swa_show_filters",local_scope).val();
	var is_personal=j("#swa_is_personal",local_scope).val();
	var is_blog_admin_activity=j("#swa_is_blog_admin_activity",local_scope).val();
	var show_post_form=j("#swa_show_post_form",local_scope).val();
			
j.post( ajaxurl, {
			'action': 'swa_fetch_content',
			'cookie': encodeURIComponent(document.cookie),
			'page': page,
			'scope': scope,
			'max'  :max_items,
			'per_page':per_page,
                        'show_avatar':show_avatar,
                        'show_filters':show_filters,
                        'is_personal':is_personal,
                        'is_blog_admin_activity':is_blog_admin_activity,
                        'included_components':included_components,
                        'excluded_components':excluded_components,
                        'show_post_form':show_post_form
				},
		function(response){
			j(".swa-wrap",local_scope).replaceWith(response);
                         j('form.swa-ac-form').hide();
			j("#activity-filter-links li#afilter-"+scope,local_scope).addClass("selected");

			});//for pagination

}


//for filters
j(".widget_bp_swa_widget #activity-filter-links li a").live("click",function(){
     var parent=j(this).parents(".widget_bp_swa_widget").get(0);
     parent=j(parent);//cast as jquery object
	var page=1;//when ever someone clicks on a filter link, start by showing the first
	var scope=get_var_in_url(j(this).attr("href"),"afilter");//'get_current_scope';
	fetch_and_show_activity(page,scope,parent);
	//make the current filter selected

	return false;
});


/*for oposting form*/

	/* New posts */
        //copied from bp-default global.js
	j("input#swa-whats-new-submit").live( 'click',function() {
		var button = j(this);
		var form = button.parent().parent().parent().parent();
                var parent=j(this).parents(".widget_bp_swa_widget").get(0);//GET THE PARENT FOR SCOPING
                parent=j(parent);//convert to jquery object
                
                             
		form.children().each( function() {
			if ( j.nodeName(this, "textarea") || j.nodeName(this, "input") )
				j(this).prop( 'disabled', 'disabled' );
		});
                //disabled
		j( 'form#' + form.attr('id') + ' span.ajax-loader' ,parent).show();

		/* Remove any errors */
		j('div.error',parent).remove();
		button.prop('disabled','disabled');

		/* Default POST values */
		var object = '';
		var item_id = j("#swa-whats-new-post-in",parent).val();
		var content = j("textarea#swa-whats-new",parent).val();
              
		/* Set object for non-profile posts */
		if ( item_id > 0 ) {
			object = j("#swa-whats-new-post-object",parent).val();
		}
                var show_avatar=j("#swa_show_avatar",parent).val();
		j.post( ajaxurl, {
			action: 'swa_post_update',
			'cookie': encodeURIComponent(document.cookie),
			'_wpnonce_swa_post_update': j("input#_wpnonce_swa_post_update").val(),
			'content': content,
			'object': object,
			'item_id': item_id,
                        'show_avatar':show_avatar
		},
		function(response)
		{
			j( 'form#' + form.attr('id') + ' span.ajax-loader',parent ).hide();

			form.children().each( function() {
				if ( j.nodeName(this, "textarea") || j.nodeName(this, "input") )
					j(this).prop( 'disabled', '' );
			});

			/* Check for errors and append if found. */
			if ( response[0] + response[1] == '-1' ) {
				form.prepend( response.substr( 2, response.length ) );
				j( 'form#' + form.attr('id') + ' div.error',parent).hide().fadeIn( 200 );
				button.prop("disabled", '');
			} else {
				if ( 0 == j("ul.swa-activity-list",parent).length ) {
					j("div.error",parent).slideUp(100).remove();
					j("div#message",parent).slideUp(100).remove();
					j("div.activity",parent).append( '<ul id="activity-stream" class="site-wide-stream swa-activity-list">' );
				}

				j("ul.swa-activity-list",parent).prepend(response);
				j("ul.swa-activity-list li:first",parent).addClass('new-update');
				j("li.new-update",parent).hide().slideDown( 300 );
				j("li.new-update",parent).removeClass( 'new-update' );
				j("textarea#swa-whats-new",parent).val('');

				/* Re-enable the submit button after 8 seconds. */
				setTimeout( function() { button.prop("disabled", ''); }, 8000 );
			}
		});

		return false;
	});

//for activity comment reply
 jq('form.swa-ac-form').hide();
/* Activity list event delegation */
	/* Activity list event delegation */
	jq('ul.swa-activity-list').live('click', function(event) {
		var target = jq(event.target);

		/* Comment / comment reply links */
		if ( target.attr('class') == 'acomment-reply' || target.parent().attr('class') == 'acomment-reply' ) {
			if ( target.parent().attr('class') == 'acomment-reply' )
				target = target.parent();

			var id = target.attr('id');
			ids = id.split('-');

			var a_id = ids[2]
			var c_id = target.attr('href').substr( 10, target.attr('href').length );
			var form = jq( '#swa-ac-form-' + a_id );

			

			form.css( 'display', 'none' );
			form.removeClass('root');
			jq('.swa-ac-form').hide();

			/* Hide any error messages */
			form.children('div').each( function() {
				if ( jq(this).hasClass( 'error' ) )
					jq(this).hide();
			});

			if ( ids[1] != 'comment' ) {
				jq('div.swa-activity-comments li#acomment-' + c_id).append( form );
			} else {
				jq('ul.swa-activity-list li#activity-' + a_id + ' div.swa-activity-comments').append( form );
			}

	 		if ( form.parent().attr( 'class' ) == 'swa-activity-comments' )
				form.addClass('root');

			form.slideDown( 200 );
			jq.scrollTo( form, 500, { offset:-100, easing:'easeOutQuad' } );
			jq('#swa-ac-form-' + ids[2] + ' textarea').focus();

			return false;
		}

		/* Activity comment posting */
		if ( target.attr('name') == 'swa_ac_form_submit' ) {
			var form = target.parent().parent();
			var form_parent = form.parent();
			var form_id = form.attr('id').split('-');
                        
			if ( 'swa-activity-comments' !== form_parent.attr('class') ) {
				var tmp_id = form_parent.attr('id').split('-');
				var comment_id = tmp_id[1];
			} else {
				var comment_id = form_id[3];
			}

			/* Hide any error messages */
			jq( 'ul.swa-activity-list form#' + form + ' div.error').hide();
			form.addClass('loading');
			target.css('disabled', 'disabled');

			jq.post( ajaxurl, {
				action: 'new_activity_comment',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce_new_activity_comment': jq("input#_wpnonce_new_activity_comment").val(),
				'comment_id': comment_id,
				'form_id': form_id[3],
				'content': jq('ul.swa-activity-list form#' + form.attr('id') + ' textarea').val()
			},
			function(response)
			{
				form.removeClass('loading');

				/* Check for errors and append if found. */
				if ( response[0] + response[1] == '-1' ) {
					form.append( response.substr( 2, response.length ) ).hide().fadeIn( 200 );
					target.prop("disabled", '');
				} else {
					form.fadeOut( 200,
						function() {
							if ( 0 == form.parent().children('ul').length ) {
								if ( form.parent().attr('class') == 'swa-activity-comments' )
									form.parent().prepend('<ul></ul>');
								else
									form.parent().append('<ul></ul>');
							}

							form.parent().children('ul').append(response).hide().fadeIn( 200 );
							form.children('textarea').val('');
							form.parent().parent().addClass('has-comments');
						}
					);
					jq( 'ul.swa-activity-list form#' + form + ' textarea').val('');

					/* Increase the "Reply (X)" button count */
					jq('li#activity-' + form_id[2] + ' a.acomment-reply span').html( Number( jq('li#activity-' + form_id[2] + ' a.acomment-reply span').html() ) + 1 );

					/* Re-enable the submit button after 5 seconds. */
					setTimeout( function() { target.prop("disabled", ''); }, 5000 );
				}
			});

			return false;
		}

		/* Deleting an activity comment */
		if ( target.hasClass('acomment-delete') ) {
			var link_href = target.attr('href');
			var comment_li = target.parent().parent();
			var form = comment_li.parents('div.swa-activity-comments').children('form');

			var nonce = link_href.split('_wpnonce=');
				nonce = nonce[1];

			var comment_id = link_href.split('cid=');
				comment_id = comment_id[1].split('&');
				comment_id = comment_id[0];

			target.addClass('loading');

			/* Remove any error messages */
			jq('div.swa-activity-comments ul div.error').remove();

			/* Reset the form position */
			comment_li.parents('div.swa-activity-comments').append(form);

			jq.post( ajaxurl, {
				action: 'delete_activity_comment',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce': nonce,
				'id': comment_id
			},
			function(response)
			{
				/* Check for errors and append if found. */
				if ( response[0] + response[1] == '-1' ) {
					comment_li.prepend( response.substr( 2, response.length ) ).hide().fadeIn( 200 );
				} else {
					var children = jq( 'li#' + comment_li.attr('id') + ' ul' ).children('li');
					var child_count = 0;
					jq(children).each( function() {
						if ( !jq(this).is(':hidden') )
							child_count++;
					});
					comment_li.fadeOut(200);

					/* Decrease the "Reply (X)" button count */
					var parent_li = comment_li.parents('ul#activity-stream > li');
					jq('li#' + parent_li.attr('id') + ' a.acomment-reply span').html( jq('li#' + parent_li.attr('id') + ' a.acomment-reply span').html() - ( 1 + child_count ) );
				}
			});

			return false;
		}

		/* Showing hidden comments - pause for half a second */
		if ( target.parent().hasClass('show-all') ) {
			target.parent().addClass('loading');

			setTimeout( function() {
				target.parent().parent().children('li').fadeIn(200, function() {
					target.parent().remove();
				});
			}, 600 );

			return false;
		}
	});

	/* Escape Key Press for cancelling comment forms */
	jq(document).keydown( function(e) {
		e = e || window.event;
		if (e.target)
			element = e.target;
		else if (e.srcElement)
			element = e.srcElement;

		if( element.nodeType == 3)
			element = element.parentNode;

		if( e.ctrlKey == true || e.altKey == true || e.metaKey == true )
			return;

		var keyCode = (e.keyCode) ? e.keyCode : e.which;

		if ( keyCode == 27 ) {
			if (element.tagName == 'TEXTAREA') {
				if ( jq(element).attr('class') == 'ac-input' )
					jq(element).parent().parent().parent().slideUp( 200 );
			}
		}
	});

	

function get_var_in_url(url,name){
    var urla=url.split("?");
    var qvars=urla[1].split("&");//so we have an arry of name=val,name=val
    for(var i=0;i<qvars.length;i++){
        var qv=qvars[i].split("=");
        if(qv[0]==name)
            return qv[1];
      }
}
});