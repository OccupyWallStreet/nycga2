function showbox(elmnt) {
	document.getElementById('hiddencats').style.display = 'block';
}

function hidebox() {
	document.getElementById('hiddencats').style.display = 'none';
}

function fixURL() {
	parts = window.location.href.split("#/");
	if(parts.length > 1) {
		window.location.href = parts[parts.length - 1];
	}
}

function changeURL(href) {
	href = (href == "") ? "/" : href;
	uri = window.location.href.split("#/");
	window.location.href = uri[0] + "#/" + href;
}

if(typeof jQuery == "function") {
	jQuery(document).ready(function($) {
		fixURL();
		$("#post-container").css("display", "block");
		init(jQuery, "body");
	});
	
	function init($, id) {
		id = (typeof(id) == 'undefined') ? "body" : id;
		
		$(".morecomments a").click(function() {
			href = $(this).attr('href');
			uri = href.split("#")[0];
			
			stylesheet_uri = $("link[rel='stylesheet']:eq(0)").attr("href").split("/");
			stylesheet_uri.length--;
			img = "<img src='"+stylesheet_uri.join("/") + "/images/indicator_small.gif"+"' alt='Loading' />";
			$(this).parent().css("text-align", "center").html(img).parent().load(uri + " div#comments #commentlist");
			return false;
		});
		
		$("a.respondlink").click(function() {
			post_id = $(this).attr('id').split("-")[1];
			if(typeof(post_id) != "undefined") {
				$(this).parent().next().next().css("display", "none");
				$("#commentform-" + post_id).css("display", "block");
				$("#commentform-" + post_id + " .focus:first").focus();
			} else {
				$(".respondtext").parent().css("display", "none");
				$("div#comment_form").css("display", "block");
				$("#commentform .focus:first").focus();
			}
			return false;
		});
		
		$(".respondtext").click(function() {
			$(this).parent().css("display", "none");
			if($(this).hasClass("single")) {
				$("a.respondlink").click();
			} else {
				$(this).parent().prev().prev().children("a.respondlink").click();
			}
		});
		
		function nextpost() {
			$(this).unbind('click', nextpost);
			stylesheet_uri = $("link[rel='stylesheet']:eq(0)").attr("href").split("/");
			stylesheet_uri.length--;
			img = "&nbsp;<img src='"+stylesheet_uri.join("/") + "/images/indicator_small.gif"+"' alt='Loading' />";
			$(this).after(img);
			href = $(this).attr("href");
			link = $(this);
			$(".post-list").removeClass("post-list");
			$(this).parent().parent().before("<div class='older'></div>");
			$(this).parent().parent().prev().load(href + " .post-list", {}, function() {
				init(jQuery, ".post-list");
				link.next().remove();
				nextpostslink = (typeof($("#nextpage").attr("value")) == "undefined") ? "mbuh" : $("#nextpage").attr("value");
				$("#nextpage").remove();
				if(nextpostslink == "mbuh") {
					link.remove();
				} else {
					link.attr("href", nextpostslink);
				}
			});
			return false;
		}
		$("a.nextpost").click(nextpost);
		
		$(id + " a").not(".nextpost").not(".notajax").each(function() {
			site = $("meta[name='home']").attr("content");
			dashboard = $("meta[name='url']").attr("content") + "/wp-admin";
			wplogin = $("meta[name='url']").attr("content") + "/wp-login.php";
			if (
				$(this).attr('href') != '#' && //it's not a '#' only link
				$(this).attr('href').indexOf(site) == 0 && //it's an internal link
				$(this).attr('href').indexOf(dashboard) == -1 && //it's not a link to dashboard
				$(this).attr('href').indexOf(wplogin) == -1 //it's not a link to wp-login.php
			) {
				$(this).click(function() {
					hidebox();
					hrefs = $(this).attr("href").split("#");
					href = hrefs[0];
					if($(this).parent().hasClass("cat-item")) {
						$(".cat-item").removeClass("current-cat");
						$(this).parent().addClass("current-cat");
					}
					
					stylesheet_uri = $("link[rel='stylesheet']:eq(0)").attr("href").split("/");
					stylesheet_uri.length--;
					img = "<img src='"+stylesheet_uri.join("/") + "/images/indicator_large.gif"+"' alt='Loading' />";
					$("#post-container").html(img).load(href + " #posts", {}, function() {
						if($("#post-container").html() == "") {
							window.location.href = href;
							return false;
						}
						
						document.title = $("input[type='hidden'][name='title']").attr("value");
						site = (site.charAt(site.length - 1) == "/") ? site : site + "/";
						href = href.replace(site, "");
						if(hrefs.length > 1)
							href += "#" + hrefs[hrefs.length - 1];
						changeURL(href);
						init($, "#posts"); //re-init the database
					});
					return false;
				});
			}
		});
		
		$('textarea').not(".respondtext").autogrow({
			minHeight: 30
		});
	}
}
