//Featured Content Glider: By http://www.dynamicdrive.com
//Created: Dec 22nd, 07'
//Updated (Jan 29th, 08): Added four possible slide directions: "updown", "downup", "leftright", or "rightleft"
//Updated (Feb 1st, 08): Changed glide behavior to reverse direction when previous button is clicked
//Updated (Feb 12th, 08): Added ability to retrieve gliding contents from an external file using Ajax ("remotecontent" variable added to configuration)
//Updated (July 21st, 09): Updated to work in jQuery 1.3.x
//Updated (Dec 13th, 09): Added keyboard navigation, so left/ right arrow keys now move glider. Fixed bug with auto rotation when "next" link isn't defined.

jQuery.noConflict()

var featuredcontentglider={
	leftrightkeys: [37, 39], //keycodes to move glider back and forth 1 slide, respectively. Enter [] to disable feature.
	csszindex: 100,
	ajaxloadingmsg: '<b>Fetching Content. Please wait...</b>',

	glide:function(config, showpage, isprev){
		var selected=parseInt(showpage)
		if (selected>=config.$contentdivs.length){ //if no content exists at this index position
			alert("No content exists at page "+(selected+1)+"! Loading 1st page instead.")
			selected=0
		}
		var $target=config.$contentdivs.eq(selected)
		//Test for toggler not being initialized yet, or user clicks on the currently selected page):
		if (config.$togglerdiv.attr('lastselected')==null || parseInt(config.$togglerdiv.attr('lastselected'))!=selected){
			var $selectedlink=config.$toc.eq(selected)
			config.nextslideindex=(selected<config.$contentdivs.length-1)? selected+1 : 0
			config.prevslideindex=(selected==0)? config.$contentdivs.length-1 : selected-1
			config.$next.attr('loadpage', config.nextslideindex+"pg") //store slide index to go to when "next" link is clicked on
			config.$prev.attr('loadpage', config.prevslideindex+'pg')
			var startpoint=(isprev=="previous")? -config.startpoint : config.startpoint
			$target.css(config.leftortop, startpoint).css("zIndex", this.csszindex++) //hide content so it's just out of view before animating it
			var endpoint=(config.leftortop=="left")? {left:0} : {top:0} //animate it into view
			$target.animate(endpoint, config.speed)
			config.$toc.removeClass('selected')
			$selectedlink.addClass('selected')
			config.$togglerdiv.attr('lastselected', selected+'pg')
		}
	},

	getremotecontent:function($, config){
		config.$glider.html(this.ajaxloadingmsg)
		$.ajax({
			url: config.remotecontent,
			error:function(ajaxrequest){
				config.$glider.html('Error fetching content.<br />Server Response: '+ajaxrequest.responseText)
			},
			success:function(content){
				config.$glider.html(content)
				featuredcontentglider.setuptoggler($, config)
			}
		})
	},

	aligncontents:function($, config){
		config.$contentdivs=$("#"+config.gliderid+" ."+config.contentclass)
		config.$contentdivs.css(config.leftortop, config.startpoint).css({height: config.$glider.height(), visibility: 'visible'}) //position content divs so they're out of view:
	},

	setuptoggler:function($, config){
		this.aligncontents($, config)
		config.$togglerdiv.hide()
		config.$toc.each(function(index){
				$(this).attr('pagenumber', index+'pg')
				if (index > (config.$contentdivs.length-1))
					$(this).css({display: 'none'}) //hide redundant "toc" links
		})
		var $nextandprev=$("#"+config.togglerid+" .next, #"+config.togglerid+" .prev")
		$nextandprev.click(function(event){ //Assign click behavior to 'next' and 'prev' links
			featuredcontentglider.glide(config, this.getAttribute('loadpage'), this.getAttribute('buttontype'))
			event.preventDefault() //cancel default link action
		})
		config.$toc.click(function(event){ //Assign click behavior to 'toc' links
			featuredcontentglider.glide(config, this.getAttribute('pagenumber'))
			event.preventDefault()
		})
		config.$togglerdiv.fadeIn(1000, function(){
			featuredcontentglider.glide(config, config.selected)
			if (config.autorotate==true){ //auto rotate contents?
				config.stepcount=0 //set steps taken
				config.totalsteps=config.$contentdivs.length*config.autorotateconfig[1] //Total steps limit: num of contents x num of user specified cycles)
				featuredcontentglider.autorotate(config)
			}
		})
		config.$togglerdiv.click(function(){
			featuredcontentglider.cancelautorotate(config.togglerid)
		})
		if (this.leftrightkeys.length==2){
			$(document).bind('keydown', function(e){
				featuredcontentglider.keyboardnav(config, e.keyCode)
			})
		}
	},

	autorotate:function(config){
		var rotatespeed=config.speed+config.autorotateconfig[0]
		window[config.togglerid+"timer"]=setInterval(function(){
			if (config.totalsteps>0 && config.stepcount>=config.totalsteps){
				clearInterval(window[config.togglerid+"timer"])
			}
			else{
				featuredcontentglider.glide(config, config.nextslideindex, "next")
				config.stepcount++
			}
		}, rotatespeed)
	},

	cancelautorotate:function(togglerid){
		if (window[togglerid+"timer"])
			clearInterval(window[togglerid+"timer"])
	},

	keyboardnav:function(config, keycode){
		if (keycode==this.leftrightkeys[0])
			featuredcontentglider.glide(config, config.prevslideindex, "previous")
		else if (keycode==this.leftrightkeys[1])
			featuredcontentglider.glide(config, config.nextslideindex, "next")
		if (keycode==this.leftrightkeys[0] || keycode==this.leftrightkeys[1])
			featuredcontentglider.cancelautorotate(config.togglerid)
	},

	getCookie:function(Name){ 
		var re=new RegExp(Name+"=[^;]+", "i") //construct RE to search for target name/value pair
		if (document.cookie.match(re)) //if cookie found
			return document.cookie.match(re)[0].split("=")[1] //return its value
		return null
	},

	setCookie:function(name, value){
		document.cookie = name+"="+value
	},

	init:function(config){
		jQuery(document).ready(function($){
			config.$glider=$("#"+config.gliderid)
			config.$togglerdiv=$("#"+config.togglerid)
			config.$toc=config.$togglerdiv.find('.toc')
			config.$next=config.$togglerdiv.find('.next')
			config.$prev=config.$togglerdiv.find('.prev')
			config.$prev.attr('buttontype', 'previous')
			var selected=(config.persiststate)? featuredcontentglider.getCookie(config.gliderid) : config.selected
			config.selected=(isNaN(parseInt(selected))) ? config.selected : selected //test for cookie value containing null (1st page load) or "undefined" string	
			config.leftortop=(/up/i.test(config.direction))? "top" : "left" //set which CSS property to manipulate based on "direction"
			config.heightorwidth=(/up/i.test(config.direction))? config.$glider.height() : config.$glider.width() //Get glider height or width based on "direction"
			config.startpoint=(/^(left|up)/i.test(config.direction))? -config.heightorwidth : config.heightorwidth //set initial position of contents based on "direction"
			if (typeof config.remotecontent!="undefined" && config.remotecontent.length>0)
				featuredcontentglider.getremotecontent($, config)
			else
				featuredcontentglider.setuptoggler($, config)
			$(window).bind('unload', function(){ //clean up and persist
				config.$togglerdiv.unbind('click')
				config.$toc.unbind('click')
				config.$next.unbind('click')
				config.$prev.unbind('click')
				if (config.persiststate)
					featuredcontentglider.setCookie(config.gliderid, config.$togglerdiv.attr('lastselected'))
				config=null
				
			})
		})
	}
}