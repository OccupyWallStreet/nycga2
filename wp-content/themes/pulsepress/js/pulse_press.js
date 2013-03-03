var loggedin = false;
var author_avatar = null;
jQuery(function($) {

	edCanvas = document.getElementById('posttext');
	jQuery('#comment-submit').live( 'click', function() {
		if (loggedin == true)
			window.onbeforeunload = null;
	});
	
	// counter.js 
	if(pulse_presstxt.show_twitter == 1){
		pulse_press_disable_submit();
		$("#posttext").keydown(pulse_press_disable_submit).focusout(pulse_press_disable_submit);
	}
	if(pulse_presstxt.limit_comments == 1) {
		
		$("#comment").live('keydown',function(e){
			var el = $(this);
			var span =  $(this).siblings('.limit_comments');
			
			setTimeout( function() {
				var remainder = 140 - el.val().length;
				span.text(remainder +" characters left");
			
			},50);
		}).after("<span class='limit_comments'></span>")
	
		$("#cancel-comment-reply-link").click(function(e){
			$(".limit_comments").text("");
		})
	
	} //
	// if FORCE_SSL_ADMIN is defined true that will not work! 
	if (isUserLoggedIn) {
		// Checks if you are logged in and try to input data (To fix for ONLY private posts.)
		jQuery('#comment, .comment-reply-link, #comment-submit,# posttext,#submit').click(function() {
			jQuery.ajax({
				type: "POST",
				url: ajaxUrl +'&action=logged_in_out&_loggedin=' + nonce,
				success: function(result) {			
					if (result != 'logged_in') {
						newNotification('Please login again.');
						// window.location = login_url;
					} else {
						loggedin = true;
					}
				}
			});
		});
	}

	window.onbeforeunload = function (e) {
		if (jQuery('#posttext').val() || jQuery('#comment').val()) {
	  		var e = e || window.event;
	  		if (e) { // For IE and Firefox
	    		e.returnValue = pulse_presstxt.unsaved_changes;
	  		}
	  		return pulse_presstxt.unsaved_changes;   // For Safari
		}
	};
	// set the author_avatar 
	pulse_presstxt.author_avatar = $("#postbox img.avatar").attr('src');
	// check for anonomous 
	$('#post-anonymous').change(function() {
		if( $(this).is(":checked") ){
			$(this).parent().siblings('img').attr('src',pulse_presstxt.anonymous_avatar);
			
		}else{
			$(this).parent().siblings('img').attr('src',pulse_presstxt.author_avatar);
			
		}
	});
	
		
	/*
	* Insert new comment inline
	*/
	function insertCommentInline(postParent, comment_parent, commentHtml, showNotification) {
		postParent = "#"+postParent;
		 $(postParent).children('ul.commentlist').show();
		 $(postParent).children('.discussion').hide();
		if (0 == comment_parent) {
			if (0 == $(postParent).children('ul.commentlist').length) {
				$(postParent).append('<ul class="commentlist inlinecomments"></ul>');
				commentsLists = $("ul.commentlist");
			}
			$(postParent).children('ul.commentlist').append('<div class="temp_newComments_cnt"></div>');
			var newComment =  $(postParent).children('ul.commentlist').children('div.temp_newComments_cnt');
		} else {
			comment_parent = '#comment-' + comment_parent;
			//$(comment_parent).toggle();
			if (0 == $(comment_parent).children('ul.children').length) {
				$(comment_parent).append('<ul class="children"></ul>');
			}
			$(comment_parent).children('ul.children').append('<div class="temp_newComments_cnt"></div>');
			var newComment =  $(comment_parent).children('ul.children').children('div.temp_newComments_cnt');
		}

		newComment.html(commentHtml);
		var newCommentsLi = newComment.children('li');
		newCommentsLi.addClass("newcomment");
		newCommentsLi.slideDown( 200, function() {
			var cnt = newComment.contents();
			newComment.children('li.newcomment').each(function() {
				if (isElementVisible(this) && !showNotification) {
					$(this).animate({backgroundColor:'transparent'}, {duration: 1000}, function(){
						$(this).removeClass('newcomment');
					});
				}
				bindActions(this, 'comment');
			});
			localizeMicroformatDates(newComment);
			newComment.replaceWith(cnt);
		});
	}

	/*
	* Insert and animate new comments into recent comments widget
	*/
	function insertCommentWidget(widgetHtml) {
		$("table.pulse_press-recent-comments").each(function() {
			var t = $(this);
			var avatar_size = t.attr('avatar');
			if (avatar_size == '-1') widgetHtml = widgetHtml.replace(/<td.*?<\/td>/, '');
			$("tbody", t).html( widgetHtml + $("tbody", t).html())
			var newCommentsElement = $("tbody tr:first", t);
			newCommentsElement.fadeIn("slow");
			$("tbody tr:last", t).fadeOut("slow").remove();
			tooltip($("tbody tr:first td a.tooltip", t));
			if (isElementVisible(newCommentsElement)) {
				$(newCommentsElement).removeClass('newcomment');
			}
		});
	}

	/*
	* Check for new posts and loads them inline
	*/
	function getPosts(showNotification){
		if (showNotification == null) {
			showNotification = true;
		}
		toggleUpdates('unewposts');
		var queryString = ajaxUrl +'&action=get_latest_posts&load_time=' + pageLoadTime + '&frontpage=' + isFirstFrontPage + '&vp=' + postsOnPageQS;
		ajaxCheckPosts = $.getJSON(queryString, function(newPosts){
			if (newPosts != null) {
				pageLoadTime = newPosts.lastposttime;
				if (!isFirstFrontPage || (typeof newPosts.html == "undefined") ) {
					newUnseenUpdates = newUnseenUpdates+newPosts.numberofnewposts;
					message = pulse_presstxt.n_new_updates.replace('%d', newUnseenUpdates) + " <a href=\"" + wpUrl +"\">"+pulse_presstxt.goto_homepage+"</a>";
					newNotification(message);
				} else {
					$("#main > ul > li:first").before(newPosts.html);
					var newUpdatesLi = $("#main > ul > li:first");
					newUpdatesLi.hide().slideDown(200, function() {
						$(this).addClass('newupdates');
					});
					var counter = 0;
					$('#posttext_error, #commenttext_error').hide();
					newUpdatesLi.each(function() {
						// Add post to postsOnPageQS  list
						var thisId = $(this).attr("id");
						vpostId = thisId.substring(thisId.indexOf('-')+1);
						postsOnPageQS+= "&vp[]=" + vpostId;
						if (!(thisId in postsOnPage))
							postsOnPage.unshift(thisId);
						// Bind actions to new elements
						bindActions(this, 'post');
						if (isElementVisible(this) && !showNotification) {
							$(this).animate({backgroundColor:'transparent'}, 2500, function(){
								$(this).removeClass('newupdates');
								titleCount();
							});
						}
						localizeMicroformatDates(this);
						counter++;
					});
					if (counter >= newPosts.numberofnewposts && showNotification) {
						var updatemsg = isElementVisible('#main > ul >li:first') ? "" :  "<a href=\"#\"  onclick=\"jumpToTop();\" \">"+pulse_presstxt.jump_to_top+"</a>" ;
						newNotification(pulse_presstxt.n_new_updates.replace('%d', counter) + " " + updatemsg);
						titleCount();
					}
				}
				$('.newupdates > h4, .newupdates > div').hover( removeYellow, removeYellow );
			}
		});
		//Turn updates back on
		toggleUpdates('unewposts');
	}

	/*
	* Check for new comments and loads them inline and into the recent-comments widgets
	*/
	function getComments(showNotification){
		if (showNotification == null) {
			showNotification = true;
		}
		toggleUpdates('unewcomments');
		var queryString = ajaxUrl +'&action=get_latest_comments&load_time=' + pageLoadTime + '&lcwidget=' + lcwidget;
		queryString += postsOnPageQS;

		ajaxCheckComments = $.getJSON(queryString, function(newComments) {
			if (newComments != null) {
				$.each(newComments.comments, function(i,comment) {
					pageLoadTime = newComments.lastcommenttime;
					if (comment.widgetHtml) {
						insertCommentWidget(comment.widgetHtml);
					}
					if (comment.html != '') {
						var thisParentId = 'pulse_press-'+comment.postID;
						insertCommentInline(thisParentId, comment.commentParent, comment.html, showNotification);
					}
				});
				if (showNotification) {
					newNotification(pulse_presstxt.n_new_comments.replace('%d', newComments.numberofnewcomments));
				}
			}
		});
		toggleUpdates('unewcomments');
	}
	
	
	/*
	* Check for new Votes and loads them inline
	*/
	
	function getVotes(showNotification){
		if (showNotification == null) {
			showNotification = true;
		}
		toggleUpdates('unewvotes');
		
		
		var queryString = ajaxUrl +'&action=get_latest_votes&load_time=' + pageLoadTime;
		queryString += postsOnPageQS;

		ajaxCheckComments = $.getJSON(queryString, function(newVotes) {
			if (newVotes != null) {
				pageLoadTime = newVotes.lastVotesUpdate;
				var count_votes = 0;
				$.each(newVotes.votes, function(i,vote) {
					
					vote_shell 	= $('#votes-'+vote.post_id)    // count
					vote_up 	= $('#votes-up-'+vote.post_id) // positive votes
					vote_down   = $('#votes-down-'+vote.post_id) // negative votes
					
					if(vote_shell.data('total') != vote.total || vote_shell.html() !=vote.count) {
						count_votes++;
						
						if(vote.count > 0) {
							negative_vote = ((vote.total-vote.count)/2);
							positive_vote = vote.total - negative_vote;
						} else if( vote.count	 == 0 ){
							negative_vote = vote.total/2;
							positive_vote = negative_vote;
						} else{
							negative_vote = ((vote.total - vote.count)/2);
							positive_vote = vote.total - negative_vote;
						}
						
						
						
						
						vote_shell.html(vote.count);
						vote_up.html(positive_vote);
						vote_down.html(negative_vote);
						vote_shell.data('total',vote.total);
						vote_shell.parent().addClass('newupdates').animate( { 'backgroundColor' : '#FFFFFF'}, 3500, function() {
							$(this).removeClass('newupdates');
						});
					}
						
				});
				if (showNotification && count_votes >0 ) {
				newNotification("Votes Have Been Updated");
				
				}
				
				
			}
		});
		toggleUpdates('unewvotes');
	}
	
	var thisForm;
	/*
	* Submits a new post via ajax
	*/
	function newPost(trigger) {
		thisForm = $(trigger.target);
		var thisFormElements = $('#posttext, #tags, :input',thisForm).not('input[type=hidden]');

		var submitProgress = thisForm.find('span.progress');

		var posttext = $.trim($('#posttext').val());
		

		if(jQuery('.no-posts')) jQuery('.no-posts').hide();

		if ("" == posttext) {
			$("label#posttext_error").text('This field is required').show().focus();
			return false;
		}

		toggleUpdates('unewposts');
		if (typeof ajaxCheckPosts != "undefined")
			ajaxCheckPosts.abort();
		$("label#posttext_error").hide();
		thisFormElements.attr('disabled', true);
		thisFormElements.addClass('disabled');

		submitProgress.show();
		var tags = $('#tags').val();
		if (tags == pulse_presstxt.tagit) tags = '';
		var post_cat = $('#post_cat').val();
		var post_title = $('#posttitle').val();
		var post_citation = $('#postcitation').val();
		
		
		// if anonymouse is enabled 
		if( $('#post-anonymous').is(':checked') ) {
			var args = {action: 'new_post', _ajax_post:nonce, posttext: posttext, tags: tags, post_cat: post_cat, post_title: post_title, post_citation: post_citation, anonymous:1 };
		} 
		else {
			var args = {action: 'new_post', _ajax_post:nonce, posttext: posttext, tags: tags, post_cat: post_cat, post_title: post_title, post_citation: post_citation };
		}
			

		
		var errorMessage = '';
		$.ajax({
			type: "POST",
			url: ajaxUrl,
			data: args,
			success: function(result) {
				if ("0" == result)
					errorMessage = pulse_presstxt.not_posted_error;

				$('#posttext').val('');
				$('#tags').val(pulse_presstxt.tagit);
				if(errorMessage != '')
					newNotification(errorMessage);

				if ($.suggest)
					$('ul.ac_results').css('display', 'none'); // Hide tag suggestion box if displayed

				if (isFirstFrontPage && result != "0") {
					getPosts(false);
				} else if (!isFirstFrontPage && result != "0") {
					newNotification(pulse_presstxt.update_posted);
				}
				submitProgress.fadeOut();
				thisFormElements.attr('disabled', false);
				thisFormElements.removeClass('disabled');
			  },
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				submitProgress.fadeOut();
				thisFormElements.attr('disabled', false);
				thisFormElements.removeClass('disabled');
			},
			timeout: 60000
		});
		thisFormElements.blur();
		toggleUpdates('unewposts');
	}

	

	/*
	* Submits a new comment via ajax
	*/
	function newComment(trigger) {
		var thisForm = $(trigger.target);
		var thisFormElements = $('#comment, #comment-submit, :input', thisForm).not('input[type=hidden]');
		var submitProgress = thisForm.find('span.progress');
		var commenttext = $.trim($('#comment', thisForm).val());

		if ('' == commenttext) {
			$("label#commenttext_error").text('This field is required').show().focus();
			return false;
		}

		if (!isPage)
			toggleUpdates('unewcomments');

		if (typeof ajaxCheckComments != "undefined")
			ajaxCheckComments.abort();

		if ('inline' == $("label#commenttext_error").css('display'))
			$("label#commenttext_error").hide();

		thisFormElements.attr('disabled', true);
		thisFormElements.addClass('disabled');

		submitProgress.show();
		var comment_post_ID = $('#comment_post_ID').val();
		var comment_parent = $('#comment_parent').val();
		var subscribe_blog = ( $('#subscribe_blog').attr('checked') ) ? 'subscribe' : 'false';
		var subscribe = ( $('#subscribe').attr('checked') ) ? 'subscribe' : 'false';
		var dataString = {action: 'new_comment' , _ajax_post: nonce, comment: commenttext,  comment_parent: comment_parent, comment_post_ID: comment_post_ID, subscribe: subscribe, subscribe_blog: subscribe_blog};
		if (!isUserLoggedIn) {
			dataString['author'] = $('#author').val();
			dataString['email'] = $('#email').val();
			dataString['url'] = $('#url').val();
		}
		var errorMessage = '';
		$.ajax({
			type: "POST",
			url: ajaxUrl,
			data: dataString,
			success: function(result) {
				submitProgress.fadeOut();
				$("#respond").slideUp( 200, function() {
					var lastComment = $("#respond").prev("li");
					if (isNaN(result) || 0 == result || 1 == result)
						errorMessage = result;
					$('#comment').val('');
					if (errorMessage != "")
						newNotification(errorMessage);
					getComments(false);

					if (!isPage)
						toggleUpdates('unewcomments');

					thisFormElements.attr('disabled', false);
					thisFormElements.removeClass('disabled');
				});

			  }
		});
	}

	/* 
	 * Vote for your Post via Ajax 
	 */ 
	function newVoteUp(trigger,id) {
	
		var queryString = ajaxUrl +'&'+id.attr('href').substring(1);
		
		$.ajax({
			type: "GET",
			url: queryString,
			data: {_ajax_post: nonce, do_ajax:'true'},
			success: function(result) {
								
				if ("voted" == result) {
					
					var p_id = id.attr('id').substring(7);
					var votes = parseInt($("#votes-"+p_id).html());
					var positive_votes = parseInt($("#votes-up-"+p_id).html());
					var total = $("#votes-"+p_id).data('total');
					
					// update the UI 
					if(id.hasClass('vote-up-set')){ // the user previously voted up - setting things back
						id.html("<span>Vote Up</span>").removeClass("vote-up-set").attr('title',"Vote Up");
						
						// remove the vote
						$("#votes-"+p_id).html(votes-1); 
						$("#votes-up-"+p_id).html(positive_votes-1); 
						$("#votes-"+p_id).data('total',total-1);
						// negative vote count stays the same
						
					
					}else{ // the user votes up 
						id.html( "<span>Unvote</span>" ).addClass( "vote-up-set" ).attr( 'title',"Unvote" );
						var count = 1;
						
						if($("#votedw-"+p_id).hasClass('vote-down-set')) { // the user previously voted down
							$("#votedw-"+p_id).html("<span>Vote Down</span>").removeClass("vote-down-set").attr('title',"Vote Down");
							count = 2;
							var negative_votes = parseInt($("#votes-down-"+p_id).html());
							$("#votes-down-"+p_id).html(negative_votes-1);
						}
						$("#votes-"+p_id).html(votes+count);
						$("#votes-up-"+p_id).html(positive_votes+1);
						if(count == 1){ // total only changes if the hasn't voted before
							$("#votes-"+p_id).data('total',total+1); // total votes goes up by 1
						}
					}
				}
				
			  },
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				// if something goes wrong
			},
			timeout: 15000
		});
		
		// toggleUpdates('unewposts');
	
	}
	
	function newVoteDown(trigger,id) {
	
		var queryString = ajaxUrl +'&'+id.attr('href').substring(1);
		
		$.ajax({
			type: "GET",
			url: queryString,
			data: {_ajax_post: nonce, do_ajax:'true'},
			success: function(result) {
				
				if ("voted" == result) {
					var p_id = id.attr('id').substring(7);
					var votes = parseInt($("#votes-"+p_id).html());
					var negative_votes = parseInt($("#votes-down-"+p_id).html());
					var total = $("#votes-"+p_id).data('total');
					
					// update the UI 
					if(id.hasClass('vote-down-set')){ // the user clicked and he/she just took back their vote 
						
						id.html("<span>Vote Down</span>").removeClass("vote-down-set").attr('title',"Vote Down");
			
						$("#votes-"+p_id).html(votes+1); // sum goes up 
						$("#votes-down-"+p_id).html(negative_votes-1); // negative count goes down
						$("#votes-"+p_id).data('total',total-1); // total votes goes down 
						// number of positive votes stays the same
					}else{ // the user just clicked and they are voting down
						id.html( "<span>Unvote</span>" );
						id.addClass( "vote-down-set" );
						id.attr( 'title',"Unvote" );
						var count = 1;
						
						if($("#voteup-"+p_id).hasClass('vote-up-set')) { // the user has voted up before. 
							var positive_votes = parseInt($("#votes-up-"+p_id).html());
							$("#voteup-"+p_id).html("<span>Vote Up</span>").removeClass("vote-up-set").attr('title',"Vote Up");
							$("#votes-up-"+p_id).html(positive_votes-1); // positive votes goes down 
							count = 2;
						}
						$("#votes-"+p_id).html(votes-count); // the sum could go up by one or 2 
						$("#votes-down-"+p_id).html(negative_votes+1); // the negative votes goes up by 1
						if(count == 1){ // total only changes if the hasn't voted before
							$("#votes-"+p_id).data('total',total+1); // total votes goes up by 1
						}
					}
				}
				
			  },
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				// if something goes wrong
			},
			timeout: 15000
		});
		
		// toggleUpdates('unewposts');
	
	}
	/* 
	 * Star an update for later 
	 */
	function newStar(trigger,id) {
		var queryString = ajaxUrl +'&'+id.attr('href').substring(1);
		var p_id = id.attr('id').substring(5);
		
		$.ajax({
			type: "GET",
			url: queryString,
			data: {_ajax_post: nonce, do_ajax:'true'},
			success: function(result) {
				if ("star" == result) {
					// update the UI 
					if(id.hasClass('star')){
						id.html("<span>Unstar</span>");
						id.addClass( "unstar");
						id.removeClass("star");
						id.attr('title',"Unstar");
						// remove the 
						
					}else{
						id.html("<span>Star</span>");
						id.addClass( "star");
						id.removeClass("unstar");
						id.attr('title',"Star");		
					}
				}
			  },
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				
			},
			timeout: 60000
		});
		
		// toggleUpdates('unewposts');
	
	}


	/*
	* Handles tooltips for the recent-comment widget
	* param: anchor link
	*/
	function tooltip(alink){
		xOffset = 10;
		yOffset = 20;
		alink.hover(function(e){
			this.t = this.title;
			this.title = "";
			$("body").append("<div id='tooltip'>"+ this.t +"</div>");
			$("#tooltip")
				.css("top",(e.pageY - yOffset) + "px")
				.css("left",(e.pageX + xOffset) + "px")
				.fadeIn("fast");
	    },
		function(){
			this.title = this.t;
			$("#tooltip").remove();
	    });
		alink.mousemove(function(e){
			$("#tooltip")
				.css("top",(e.pageY - yOffset) + "px")
				.css("left",(e.pageX + xOffset) + "px");
		});
	};

	function isElementVisible(elem) {
	    elem = $(elem);
		if (!elem.length) {
	        return false;
	    }
	    var docViewTop = $(window).scrollTop();
	    var docViewBottom = docViewTop + $(window).height();

	    var elemTop = elem.offset().top;
	    var elemBottom = elemTop + elem.height();
		var isVisible = ((elemBottom >= docViewTop) && (elemTop <= docViewBottom)  && (elemBottom <= docViewBottom) &&  (elemTop >= docViewTop) );
	    return isVisible;
	}

	function toggleUpdates(updater){
		switch (updater) {
			case "unewposts":
				if (0 == getPostsUpdate) {
					getPostsUpdate = setInterval(getPosts, updateRate);
				}
				else {
					clearInterval(getPostsUpdate);
					getPostsUpdate = '0';
				}
				break;

			case "unewcomments":
				if (0 == getCommentsUpdate) {
					getCommentsUpdate = setInterval(getComments, updateRate);
				}
				else {
					clearInterval(getCommentsUpdate);
					getCommentsUpdate = '0';
				}
				break;
				
			case "unewvotes":
				
				if (0 == getVotesUpdate) {
					getVotesUpdate = setInterval(getVotes, updateRate);
				}
				else {
					clearInterval(getVotesUpdate);
					getVotesUpdate = '0';
				}
				break;
			
		}
	}

	function titleCount() {
		if (isFirstFrontPage) {
			var n = $('li.newupdates').length;
		} else {
			var n = newUnseenUpdates;
		}
		if ( n <= 0 ) {
			if (document.title.match(/\([\d+]\)/)) {
				document.title = document.title.replace(/(.*)\([\d]+\)(.*)/, "$1$2");
			}
			fluidBadge("");
		} else {
			if (document.title.match(/\((\d+)\)/)) {
				document.title = document.title.replace(/\((\d+)\)/ , "(" + n + ")" );
			} else {
				document.title = '(1) ' + document.title;
			}
			fluidBadge(n);
		}
	}

	/**
	 * Sets the badge for Fluid app enclosed PulsePress. See http://fluidapp.com/
	 */
	function fluidBadge(value) {
		if (window.fluid) window.fluid.dockBadge = value;
	}

	/**
	 * Sets up a jQuery-collection of textareas to expand vertically based
	 * on their content.
	 */
	function autgrow(textareas, min) {
		function sizeToContent(textarea) {
			textarea.style.height = min + 'em';
			if (textarea.scrollHeight > textarea.clientHeight) {
				textarea.style.height = textarea.scrollHeight + 'px';
			}
		}
		textareas.css('overflow', 'hidden');

		function resizeSoon(e) {
			var textarea = this;
			setTimeout(function() {
				sizeToContent(textarea);
			}, 1);
		}
		textareas.keydown(resizeSoon); // Catch regular character keys
		textareas.keypress(resizeSoon); // Catch enter/backspace in IE, and held-down repeated keys
		textareas.focus(resizeSoon);
	}

	function jumpToTop() {
		$.scrollTo('#main', 150);
	}


	function inlineEditPost(postId, element) {
		// Set up editor
		// if the edit exits don't creatae one again
		
		
		function defaultText(input, text) {
			function onFocus() {
				if (this.value == text) {
					this.value = '';
				}
			}
			function onBlur() {
				if (!this.value) {
					this.value = text;
				}
			}
			jQuery(input).focus(onFocus).blur(onBlur);
			onBlur.call(input);
		}

		jQuery.getJSON(ajaxUrl, {action: 'get_post', _inline_edit: nonce, post_ID: 'content-' + postId}, function(post) {
			var jqe = jQuery(element);
			jqe.addClass('inlineediting');
			jqe.find('.tags').css({display: 'none'});
			jqe.find('.postcontent > *').hide();

			var postContent = jqe.find('.postcontent');

			var titleDiv = document.createElement('div');
			/*if (post.type == 'post') {
				var titleInput = titleDiv.appendChild(
					document.createElement('input'));
				titleInput.type = 'text';
				titleInput.className = 'title';
				defaultText(titleInput, pulse_presstxt.title);
				titleInput.value = post.title;
				postContent.append(titleDiv);
			}
			*/
			var cite = '';
			if (post.type == 'quote') {
				var tmpDiv = document.createElement('div');
				tmpDiv.innerHTML = post.content;
				var cite = $(tmpDiv).find('cite').remove().html();
				if (tmpDiv.childNodes.length == 1 && tmpDiv.firstChild.nodeType == 1) {
					// This _should_ be the case, else below is
					// to handle an unexpected condition.
					post.content = tmpDiv.firstChild.innerHTML;
				} else {
					post.content = tmpDiv.innerHTML;
				}
			}

			var editor = document.createElement('textarea');
			editor.className = 'posttext';
			editor.value = post.content;
			autgrow($(editor), 3);
			jqe.find('.postcontent').append(editor);

			var citationDiv = document.createElement('div');
			if (post.type == 'quote') {
				var citationInput = citationDiv.appendChild(
					document.createElement('input'));
				citationInput.type = 'text';
				citationInput.value = cite;
				defaultText(citationInput, pulse_presstxt.citation);
				postContent.append(citationDiv);
			}

			var bottomDiv = document.createElement('div');
			bottomDiv.className = 'row2';

			var tagsInput = document.createElement('input');
			tagsInput.name = 'tags';
			tagsInput.className = 'tags';
			tagsInput.type = 'text';
		   	tagsInput.value = post.tags.join(', ');
			defaultText(tagsInput, pulse_presstxt.tagit);
			bottomDiv.appendChild(tagsInput);

			function tearDownEditor() {
			
				$(titleDiv).remove();
				$(bottomDiv).remove();
				$(citationDiv).remove();
				$(editor).remove();
				 jqe.find('.tags').css({display: ''});
			   	 jqe.find('.postcontent > *').show();
				 jqe.removeClass('inlineediting');
			}
			var buttonsDiv = document.createElement('div');
			buttonsDiv.className = 'buttons';
			var saveButton = document.createElement('button');
			saveButton.innerHTML = pulse_presstxt.save;
			jQuery(saveButton).click(function() {
				var tags = tagsInput.value == pulse_presstxt.tagit ? '' : tagsInput.value;
				var args = {
					action:'save_post',
					_inline_edit: nonce,
					post_ID: 'content-' + postId,
					content: editor.value,
					tags: tags
				}

				/*if (post.type == 'post') {
					args.title = titleInput.value == pulse_presstxt.title ? '' : titleInput.value;
				} else */
				if (post.type == 'quote') {
					args.citation = citationInput.value == pulse_presstxt.citation ? '' : citationInput.value;
				}
				
				jQuery.post(
					ajaxUrl,
					args,
					function(result) {
						// Preserve existing H2 for posts
						jqe.find('.postcontent').html(
							(post.type == 'post') ?
							jqe.find('h2').first() : '');
						if (post.type == 'quote') {
							jqe.find('.postcontent').append(
								'<blockquote>' + result.content + '</blockquote>');
						} else {	
							jqe.find('.postcontent').append(result.content);
						}
						jqe.find('span.tags').html(result.tags);
						jqe.find('h2 a').html(result.title);
						tearDownEditor();
					},
					'json');
				saveButton.parentNode.insertBefore(document.createElement('img'), saveButton).src = templateDir +'/i/indicator.gif';
			});
			var cancelButton = document.createElement('button');
			cancelButton.innerHTML = pulse_presstxt.cancel;
			jQuery(cancelButton).click(tearDownEditor);
			buttonsDiv.appendChild(saveButton);
			buttonsDiv.appendChild(cancelButton);
			bottomDiv.appendChild(buttonsDiv);
			jqe.find('.postcontent').append(bottomDiv);
			// Trigger event handlers
			editor.focus();
			jQuery('input[name="tags"]').suggest(ajaxUrl + '&action=tag_search', { delay: 350, minchars: 2, multiple: true, multipleSep: ", " });
		});
	}


	function bindActions(element, type) {
		
		$(element).find('a.comment-reply-link').click( function() {
			$('*').removeClass('replying');
			$(this).parents("li").eq(0).addClass('replying');
			$("#respond").show();
			$("#respond").addClass('replying').show();
			$("#comment").focus();
		});

		switch (type) {
			case "comment" :
				var thisCommentEditArea;
				$(element).hover( removeYellow, removeYellow );
				if (inlineEditComments != 0 && isUserLoggedIn) {
					thisCommentEditArea = $(element).find('div.comment-edit').eq(0);
					$(element).find('a.comment-edit-link:first').click( function() {
						thisCommentEditArea.trigger('edit');
						return false;
					});
					thisCommentEditArea.editable(ajaxUrl, {event: 'edit', loadurl: ajaxUrl + '&action=get_comment&_inline_edit=' + nonce,
						id: 'comment_ID', name: 'comment_content', type    : 'autogrow', cssclass: 'textedit', rows: '3',
						indicator : '<img src="' + templateDir +'/i/indicator.gif">', loadtext: pulse_presstxt.loading, cancel: pulse_presstxt.cancel,
						submit  : pulse_presstxt.save, tooltip   : '', width: '90%', onblur: 'ignore',
						submitdata: {action:'save_comment',_inline_edit: nonce}});
				}
				$(".single #postlist li > div.postcontent, .single #postlist li > h4, li[id^='pulse_press'] > div.postcontent, li[id^='comment'] > div.commentcontent, li[id^='pulse_press'] > h4, li[id^='comment'] > h4").hover(function() {
					$(this).parents("li").eq(0).addClass('selected');
				}, function() {
					$(this).parents("li").eq(0).removeClass('selected');
				});
				break;

			case "post" :
				var thisPostEditArea;
				if (inlineEditPosts != 0 && isUserLoggedIn) {
					thisPostEditArea = $(element).children('div.editarea').eq(0);
					jQuery(element).find('a.edit-post-link:first').click(
						function(e) {
							var postId = $(this).data('postid');
							inlineEditPost(postId, element);
							return false;
						});
				}
				$(".single #postlist li > div.postcontent, .single #postlist li > h4, li[id^='pulse_press'] > div.postcontent, li[id^='comment'] > div.commentcontent, li[id^='pulse_press'] > h4, li[id^='comment'] > h4").hover(function() {
					$(this).parents("li").eq(0).addClass('selected');
				}, function() {
					$(this).parents("li").eq(0).removeClass('selected');
				});
				break;
		}
	}

	function localizeMicroformatDates(scopeElem) {
		(scopeElem? $('abbr', scopeElem) : $('abbr')).each(function() {
			var t = $(this);
			var d = locale.parseISO8601(t.attr('title'));
			if (d) t.html(pulse_presstxt.date_time_format.replace('%1$s', locale.date(pulse_presstxt.time_format, d)).replace('%2$s', locale.date(pulse_presstxt.date_format, d)));

		});
	}


	/* On-load */

	commentsLists = $(".commentlist");

	locale = new wp.locale(wp_locale_txt);

	if(!window.location.href.match('#'))
		$('#posttext').focus();

	$(".single #postlist li > div.postcontent, .single #postlist li > h4, li[id^='pulse_press'] > div.postcontent, li[id^='comment'] > div.commentcontent, li[id^='pulse_press'] > h4, li[id^='comment'] > h4").hover(function() {
		$(this).parents("li").eq(0).addClass('selected');
	}, function() {
		$(this).parents("li").eq(0).removeClass('selected');
	});

	$.ajaxSetup({
	  timeout: updateRate - 2000,
	  cache: false
	});

	$("#directions-keyboard").click(function(){
		$('#help').toggle();
		return false;
	});

	$("#help").click(function() {
		$(this).toggle();
	});

	// Activate inline editing plugin
	if ((inlineEditPosts || inlineEditComments ) && isUserLoggedIn) {
		$.editable.addInputType('autogrow', {
		    element : function(settings, original) {
		        var textarea = $('<textarea class="expand" />');
		        if (settings.rows) {
		            textarea.attr('rows', settings.rows);
		        } else {
		            textarea.attr('rows', 4);
		        }
		        if (settings.cols) {
		            textarea.attr('cols', settings.cols);
		        } else {
		            textarea.attr('cols', 45);
		        }
				textarea.width('95%');
		        $(this).append(textarea);
		        return(textarea);
		    },
		    plugin : function(settings, original) {
				autgrow($('textarea', this), 3);
		    }
		});
	}

	// Set tabindex on all forms
	var tabindex = 4;
	$('form').each(function() {
		$(':input',this).not('#subscribe, input[type=hidden]').each(function() {
        	var $input = $(this);
			var tabname = $input.attr("name");
			var tabnum = $input.attr("tabindex");
			if(tabnum > 0) {
				index = tabnum;
			} else {
				$input.attr("tabindex", tabindex);
			}
			tabindex++;
		});
     });

	// Turn on automattic updating
	if (pulse_pressPostsUpdates) {
		toggleUpdates('unewposts');
	}
	if (pulse_pressCommentsUpdates) {
			toggleUpdates('unewcomments');
	}
	if(pulse_pressVotesUpdates){
			toggleUpdates('unewvotes');
	}

	// Check which posts are visibles and add to array and comment querystring
	$("#main > ul > li").each(function() {
		var thisId = $(this).attr("id");
		vpostId = thisId.substring(thisId.indexOf('-') + 1);
		postsOnPage.push(thisId);
		postsOnPageQS += "&vp[]=" + vpostId;
	});


	// Bind actions to comments and posts
	
	jQuery('.post, .page').each(function() { bindActions(this, 'post'); });
	

	jQuery('body .comment').each(function() { bindActions(this, 'comment'); });


	$('#cancel-comment-reply-link').click(function() {
		$('#comment').val('');
		if (!isSingle) $("#respond").hide();
		$(this).parents("li").removeClass('replying');
		$(this).parents('#respond').prev("li").removeClass('replying');
		$("#respond").removeClass('replying');
	});

	function removeYellow() {
		$('li.newcomment, tr.newcomment').each(function() {
			if (isElementVisible(this)) {
				$(this).animate({backgroundColor:'transparent'}, {duration: 2500}, function(){
					$(this).removeClass('newcomment');
				});
			}
		});
		if (isFirstFrontPage) {
			$('#main > ul > li.newupdates').each(function() {
				if (isElementVisible(this)) {
					$(this).animate({backgroundColor:'transparent'}, {duration: 2500});
					$(this).removeClass('newupdates');
				}
			});
		}
		titleCount();
	}

	// Activate keyboard navigation
	if (!isSingle)	{
		document.onkeydown = function(e) {
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

			if (keyCode && (keyCode != 27 && (element.tagName == 'INPUT' || element.tagName == 'TEXTAREA') ) )
				return;

			switch(keyCode) {

				//  "c" key
				case 67:
					if (isFrontPage && isUserLoggedIn) {
						if (commentLoop) {
							$('#'+commentsOnPost[currComment]).removeClass('keyselected');
							$('#'+postsOnPage[currPost]).removeClass('commentloop').addClass('keyselected');
							commentLoop = false;
						} else {
							$('#'+postsOnPage[currPost]).removeClass('keyselected');
							currPost =- 1;
						}
					if (!isElementVisible("#postbox"))
						$.scrollTo('#postbox', 50);
					$("#posttext").focus();
					if (e.preventDefault)
						e.preventDefault();
					else
						e.returnValue = false;
					}
					break;

				//  "k" key
				case 75:
					if (!commentLoop) {
						if (currPost > 0) {
							$('#'+postsOnPage[currPost]).removeClass('keyselected').children('h4').trigger('mouseleave');
							currPost--;
							if (0 != $('#'+postsOnPage[currPost]).children('ul.commentlist').length && !hidecomments) {
								commentLoop = true;
								commentsOnPost.length = 0;
								$('#'+postsOnPage[currPost]).find("li[id^='comment']").each(function() {
									var thisId = $(this).attr("id");
									commentsOnPost.push(thisId);
								});
								currComment = commentsOnPost.length-1;
								$('#'+commentsOnPost[currComment]).addClass('keyselected').children('h4').trigger('mouseenter');
								if (!isElementVisible('#'+commentsOnPost[currComment]))
									$.scrollTo('#'+commentsOnPost[currComment], 150);
								return;
							}
							if (!isElementVisible('#'+postsOnPage[currPost]))
								$.scrollTo('#'+postsOnPage[currPost], 50);
							$('#'+postsOnPage[currPost]).addClass('keyselected').children('h4').trigger('mouseenter');
						} else {
							if (currPost <= 0) {
								$('#'+postsOnPage[currPost]).removeClass('keyselected').children('h4').trigger('mouseleave');
								$.scrollTo('#'+postsOnPage[postsOnPage.length-1], 50);
								currPost = postsOnPage.length-1;
								$('#'+postsOnPage[currPost]).addClass('keyselected').children('h4').trigger('mouseenter');
								return;
							}
						}
					} else {
						if (currComment > 0) {
							$('#'+commentsOnPost[currComment]).removeClass('keyselected').children('h4').trigger('mouseleave');
							currComment--;
							if (!isElementVisible('#'+commentsOnPost[currComment]))
								$.scrollTo('#'+commentsOnPost[currComment], 50);
							$('#'+commentsOnPost[currComment]).addClass('keyselected').children('h4').trigger('mouseenter');
						}
						else {
							if (currComment <= 0) {
								$('#'+commentsOnPost[currComment]).removeClass('keyselected').children('h4').trigger('mouseleave');
								$('#'+postsOnPage[currPost]).addClass('keyselected').children('h4').trigger('mouseenter');
								if (!isElementVisible('#'+postsOnPage[currPost]))
									$.scrollTo('#'+postsOnPage[currPost], 50);
								commentLoop = false;
								return
							}
						}
					}
					break;

				// "j" key
				case 74:
					removeYellow();
					if (!commentLoop) {
						if (0 != $('#'+postsOnPage[currPost]).children('ul.commentlist').length && !hidecomments) {
							$.scrollTo('#'+postsOnPage[currPost], 150);
							commentLoop = true;
							currComment = 0;
							commentsOnPost.length = 0;
							$('#'+postsOnPage[currPost]).find("li[id^='comment']").each(function() {
								var thisId = $(this).attr("id");
								commentsOnPost.push(thisId);
							});
							$('#'+postsOnPage[currPost]).removeClass('keyselected').children('h4').trigger('mouseleave');
							$('#'+commentsOnPost[currComment]).addClass('keyselected').children('h4').trigger('mouseenter');
							return;
						}
						if (currPost < postsOnPage.length-1) {
							$('#'+postsOnPage[currPost]).removeClass('keyselected').children('h4').trigger('mouseleave');
							currPost++;
							if (!isElementVisible('#'+postsOnPage[currPost]))
								$.scrollTo('#'+postsOnPage[currPost], 50);
							$('#'+postsOnPage[currPost]).addClass('keyselected').children('h4').trigger('mouseenter');
						}
						else if (currPost >= postsOnPage.length-1){
							$('#'+postsOnPage[currPost]).removeClass('keyselected').children('h4').trigger('mouseleave');
							$.scrollTo('#'+postsOnPage[0], 50);
							currPost = 0;
							$('#'+postsOnPage[currPost]).addClass('keyselected').children('h4').trigger('mouseenter');
							return;
						}
					}
					else {
						if (currComment < commentsOnPost.length-1) {
							$('#'+commentsOnPost[currComment]).removeClass('keyselected').children('h4').trigger('mouseleave');
							currComment++;
							if (!isElementVisible('#'+commentsOnPost[currComment]))
								$.scrollTo('#'+commentsOnPost[currComment], 50);
							$('#'+commentsOnPost[currComment]).addClass('keyselected').children('h4').trigger('mouseenter');
						}
						else if (currComment == commentsOnPost.length-1){
							$('#'+commentsOnPost[currComment]).removeClass('keyselected').children('h4').trigger('mouseleave');
							currPost++;
							$('#'+postsOnPage[currPost]).addClass('keyselected').children('h4').trigger('mouseenter');
							commentLoop = false;
							return
						}
					}
					break;
				case 72:
					$("#help").toggle();
					break;
				case 76:
					if (!isUserLoggedIn)
						window.location.href = login_url;
					break;
				// "r" key
				case 82:
					if (!commentLoop) {
						$('#'+postsOnPage[currPost]).removeClass('keyselected').children('h4').trigger('mouseleave');
						$('#'+postsOnPage[currPost]).find('a.comment-reply-link:first').click();
					} else {
						$('#'+commentsOnPost[currComment]).removeClass('keyselected').children('h4').trigger('mouseleave');
						$('#'+commentsOnPost[currComment]).find('a.comment-reply-link').click();
					}
					removeYellow();
					if (e.preventDefault)
						e.preventDefault();
					else
						e.returnValue = false;
					break;
				// "e" key
				case 69:
					if (!commentLoop) {
						$('#'+postsOnPage[currPost]).find('a.edit-post-link:first').click();
					}
					else {
						$('#'+commentsOnPost[currComment]).find('a.comment-edit-link:first').click();
					}
					if (e.preventDefault)
						e.preventDefault();
					else
						e.returnValue = false;
					break;
				// "o" key
				case 79:
					$("#togglecomments").click();
					if (typeof postsOnPage[currPost] != "undefined") {
						if (!isElementVisible('#'+postsOnPage[currPost])) {
							$.scrollTo('#'+postsOnPage[currPost], 150);
						}
					}
					break;
					// "t" key
				case 84:
					jumpToTop();
					break;
				// "esc" key
				case 27:
					if (element.tagName == 'INPUT' || element.tagName == 'TEXTAREA') {
						$('#cancel-comment-reply-link').click();
						$(element).blur();
					}
					else {
						$('#'+commentsOnPost[currComment]).each(function(e) {
							$(this).removeClass('keyselected');
						});

						$('#'+postsOnPage[currPost]).each(function(e) {
							$(this).addClass('keyselected');
						});

						commentLoop = false;
						$('#'+postsOnPage[currPost]).each(function(e) {
							$(this).removeClass('keyselected');
						});
						currPost =- 1;
					}
						$('#help').hide();

					break;
				case 0,191:
					$("#help").toggle();
					if (e.preventDefault)
						e.preventDefault();
					else
						e.returnValue = false;
					break;
			}
		}
	}


	// Check if recent comments widget is loaded
	if	($("table.pulse_press-recent-comments").length != 0) {
		lcwidget = true;
	}

	// Activate Tag Suggestions for logged users on front page
	if (isFrontPage && pulse_pressTagsuggest && isUserLoggedIn)
		$('input[name="tags"]').suggest(ajaxUrl + '&action=tag_search', { delay: 350, minchars: 2, multiple: true, multipleSep: ", " } );

	// Actvate autgrow on textareas
	if (isFrontPage) {
		autgrow($('#posttext, #comment'), 4);
	}

	// Activate tooltips on recent-comments widget
	tooltip($("table.pulse_press-recent-comments a.tooltip"));

	// Catch new posts submit
	$("#new_post").submit(function(trigger) {
		newPost(trigger);
		trigger.preventDefault();
	});
	

	// Catch new comment submit
	$("#commentform").bind( 'submit', function(trigger) {
		newComment(trigger);
		trigger.preventDefault();
		$(this).parents("li").removeClass('replying');
		$(this).parents('#respond').prev("li").removeClass('replying');
	});
	
	
	$('.vote-up').live('click',function(trigger){
		// vote in the background 
		newVoteUp(trigger,$(this));
		trigger.preventDefault();
	
	});
	$('.vote-down').live('click',function(trigger){
		// vote in the background 
		newVoteDown(trigger,$(this));
		trigger.preventDefault();
	
	});
	
	$('.action-star a').live('click',function(trigger){
		// favorite in the background 
		newStar(trigger,$(this));
		trigger.preventDefault();
	
	});
	
	// Activate tooltips on star and vote actions
	// tooltip($('.vote a'));
	// tooltip($('.action-star a'));


	// Hide error messages on load
	$('#posttext_error, #commenttext_error').hide();

 	// Check if new comments or updates appear on scroll and fade out
	$(window).scroll(function() { removeYellow(); });
	
	if(window.innerWidth < 601){

	$('#postlist li.post').live('click',function(){
		
		var h4 = $(this).children("h4");
		if(h4.hasClass("showup")){
		
		h4.removeClass("showup");
		}else{
			h4.addClass("showup");
		}
		
	});
	}
	localizeMicroformatDates();
});

function send_to_editor( media ) {
	if ( jQuery('textarea#posttext').length ) {
		jQuery('textarea#posttext').val( jQuery('textarea#posttext').val() + media );
		tb_remove();
	}
}

function newNotification(message) {
	jQuery("#notify").stop(true).prepend(message + '<br/>')
		.fadeIn()
		.animate({opacity: 0.7}, 3000)
		.fadeOut('5000', function() {
			jQuery("#notify").html('');
		}).click(function() {
			jQuery(this).stop(true).fadeOut('fast').html('');
		});
}
function pulse_press_disable_submit(){
	// we need sometime out to have a better reading of what is really there. 
	if(jQuery("#posttext").val()){
	setTimeout(function() {
		
		
		
		var remainder = 140 - jQuery("#posttext").val().length
		if(remainder < 0) {
			 jQuery('#submit').attr('disabled','disabled').addClass('disabled');
			
		} else{
			  jQuery('#submit').removeAttr('disabled').removeClass('disabled');
		}
		// update the counter
		jQuery('#post-count').html(remainder);
		
	},50);
	}
}