/*
Plugin Name: Anti-Splog
Description: The ultimate plugin to stop and kill splogs in WPMU
Author: Aaron Edwards (Incsub)
Author URI: http://uglyrobot.com

Copyright 2010 Incsub (http://incsub.com)
*/
jQuery(document).ready(function($) {
  //single ignore
  $(".row-actions a.ust_ignore").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		url: $(this).attr("href")
  	};

  	$(this).parents("tr.blog-row").css({backgroundColor:"#9DCF8C"}).fadeOut("slow", function() {$(this).parents("tr.blog-row").remove();});

    //send ajax
  	$.post(ajaxurl, data);
  	return false;
  });

  //single unignore
  $(".row-actions a.ust_unignore").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		url: $(this).attr("href")
  	};

  	$(this).parents("tr.blog-row").css({backgroundColor:"#F9F3B5"}).fadeOut("slow", function() {$(this).parents("tr.blog-row").remove();});

    //send ajax
  	$.post(ajaxurl, data);
  	return false;
  });

  //single spam
  $(".row-actions a.ust_spam").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		url: $(this).attr("href")
  	};

  	$(this).parents("tr.blog-row").css({backgroundColor:"#FF7374"}).fadeOut("slow", function() {$(this).parents("tr.blog-row").remove();});

    //send ajax
  	$.post(ajaxurl, data);
  	return false;
  });

  //single unspam
  $(".row-actions a.ust_unspam").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		url: $(this).attr("href")
  	};

  	$(this).parents("tr.blog-row").css({backgroundColor:"#9DCF8C"}).fadeOut("slow", function() {$(this).parents("tr.blog-row").remove();});

    //send ajax
  	$.post(ajaxurl, data);
  	return false;
  });

  //ignore multiple blogs
	$("form#form-blog-list input.allblog_ignore").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		allblogs: $("form#form-blog-list").serialize(),
  		allblog_ignore: 1
  	};

    //send ajax
  	$.post(ajaxurl, data, function(response) {
  		if (response) {
        return false;
      } else {
        return true;
      }
  	});

  	$("form#form-blog-list tr.blog-row:has(input:checked)").css({backgroundColor:"#9DCF8C"}).fadeOut("slow", function() {$(this).remove();});
  	return false;
  });

  //unignore multiple blogs
	$("form#form-blog-list input.allblog_unignore").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		allblogs: $("form#form-blog-list").serialize(),
  		allblog_unignore: 1
  	};

    //send ajax
  	$.post(ajaxurl, data, function(response) {
  		if (response) {
        return false;
      } else {
        return true;
      }
  	});

  	$("form#form-blog-list tr.blog-row:has(input:checked)").css({backgroundColor:"#F9F3B5"}).fadeOut("slow", function() {$(this).remove();});
  	return false;
  });

  //spam multiple blogs
	$("form#form-blog-list input.allblog_spam").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		allblogs: $("form#form-blog-list").serialize(),
  		allblog_spam: 1
  	};

    //send ajax
  	$.post(ajaxurl, data, function(response) {
  		if (response) {
        return false;
      } else {
        return true;
      }
  	});

  	$("form#form-blog-list tr.blog-row:has(input:checked)").css({backgroundColor:"#FF7374"}).fadeOut("slow", function() {$(this).remove();});
  	return false;
  });

  //unspam multiple blogs
	$("form#form-blog-list input.allblog_notspam").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		allblogs: $("form#form-blog-list").serialize(),
  		allblog_notspam: 1
  	};

    //send ajax
  	$.post(ajaxurl, data, function(response) {
  		if (response) {
        return false;
      } else {
        return true;
      }
  	});

  	$("form#form-blog-list tr.blog-row:has(input:checked)").css({backgroundColor:"#9DCF8C"}).fadeOut("slow", function() {$(this).remove();});
  	return false;
  });

  //spam an IP
  $("a.ust_spamip").click(function() {
    //create post data
    var data = {
  		action: 'ust_ajax',
  		check_ip: $(this).attr("href")
  	};

    //preview results
  	$.post(ajaxurl, data, function(response) {
  		if (response.num) {
  		  var answer = confirm("You are about to mark "+response.num+" blog(s) as spam! There are currently "+response.numspam+" blog(s) already marked as spam for this IP ("+response.ip+").\n\nAre you sure you want to do this?");
  		  if (answer) {
          //create post data
          var data2 = {
        		action: 'ust_ajax',
        		url: data.check_ip
        	};
        	var row = "tr#bid-" + response.bid;
          $(row).css({backgroundColor:"#FF7374"}).fadeOut("slow", function() {$(this).remove();});
        	$("tr.blog-row:contains('"+response.ip+"')").css({backgroundColor:"#FF7374"}).fadeOut("slow", function() {$(this).remove();});
          //send ajax
        	$.post(ajaxurl, data2);
        }
      }
  	}, "json");
  	return false;
  });

  //spam a user's blogs
  $("a.ust_spamuser").click(function() {

    var answer = confirm("You are about to spam all blogs this user is a member of! Are you sure you want to do this?");
	  if (answer) {
      //create post data
      var data = {
    		action: 'ust_ajax',
    		url: $(this).attr("href")
    	};

    	//hide rows
      $(this).parents("tr.blog-row").css({backgroundColor:"#FF7374"}).fadeOut("slow", function() {$(this).remove();});
    	$("tr.blog-row:has('a.ust_spamuser[href=\'"+data.url+"\']')").css({backgroundColor:"#FF7374"}).fadeOut("slow", function() {$(this).remove();});
      //send ajax
    	$.post(ajaxurl, data);
  	}
  	return false;
  });

	//test regex ajax
  $("#ust-test-regex").click(function() {
		if ($("#regex").val().length < 3)
			return false;
		
		$(this).prop('disabled', true);
		$("#test-results span.description").html($("#regex").val());
		$("#test-results .inside div#results").html('');
		$("#test-results .inside p").show();
		$("#test-results").slideDown();
		
		//create post data
    var data = {
  		action: 'ust_test_regex',
  		regex: $("#regex").val(),
  		type: $("#type").val()
  	};

    //send ajax
  	$.post(ajaxurl, data, function(response) {
			if (!response.status) {
				$("#test-results").hide();
				alert(response.data);
			} else {
				$("#test-results .inside p").hide();
				$("#test-results .inside div#results").html(response.data);
			}
			$("#ust-test-regex").prop('disabled', false);
  	}, 'json');

  	return false;
  });
	$("#type").change(function() {
		if ($(this).val() == 'title') {
			$("#ust-test-regex").prop('disabled', true);
		} else {
			$("#ust-test-regex").prop('disabled', false);
		}
	});
});