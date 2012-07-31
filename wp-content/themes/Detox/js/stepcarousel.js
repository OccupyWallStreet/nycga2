//** Step Carousel Viewer- (c) Dynamic Drive DHTML code library: http://www.dynamicdrive.com
//** Script Download/ http://www.dynamicdrive.com/dynamicindex4/stepcarousel.htm
//** Usage Terms: http://www.dynamicdrive.com/notice.htm
//** Current version 1.91 (Aug 15th, 11'): See http://www.dynamicdrive.com/dynamicindex4/stepcarouselchangelog.txt for details


jQuery.noConflict()

var stepcarousel={
	ajaxloadingmsg: '<div style="margin: 1em; font-weight: bold"><img src="ajaxloadr.gif" style="vertical-align: middle" /> Fetching Content. Please wait...</div>', //customize HTML to show while fetching Ajax content
	defaultbuttonsfade: 0.4, //Fade degree for disabled nav buttons (0=completely transparent, 1=completely opaque)
	configholder: {},

	getCSSValue:function(val){ //Returns either 0 (if val contains 'auto') or val as an integer
		return (val=="auto")? 0 : parseInt(val)
	},

	getremotepanels:function($, config){ //function to fetch external page containing the panel DIVs
		config.$belt.html(this.ajaxloadingmsg)
		$.ajax({
			url: config.contenttype[1], //path to external content
			async: true,
			error:function(ajaxrequest){
				config.$belt.html('Error fetching content.<br />Server Response: '+ajaxrequest.responseText)
			},
			success:function(content){
				config.$belt.html(content)
				config.$panels=config.$gallery.find('.'+config.panelclass)
				stepcarousel.alignpanels($, config)
			}
		})
	},

	getoffset:function(what, offsettype){
		return (what.offsetParent)? what[offsettype]+this.getoffset(what.offsetParent, offsettype) : what[offsettype]
	},

	getCookie:function(Name){ 
		var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
		if (document.cookie.match(re)) //if cookie found
			return document.cookie.match(re)[0].split("=")[1] //return its value
		return null
	},

	setCookie:function(name, value){
		document.cookie = name+"="+value
	},

	fadebuttons:function(config, currentpanel){
		config.$leftnavbutton.fadeTo('fast', currentpanel==0? this.defaultbuttonsfade : 1)
		config.$rightnavbutton.fadeTo('fast', currentpanel==config.lastvisiblepanel? this.defaultbuttonsfade : 1)
		if (currentpanel==config.lastvisiblepanel){
			stepcarousel.stopautostep(config)
		}

	},

	addnavbuttons:function($, config, currentpanel){
		config.$leftnavbutton=$('<img src="'+config.defaultbuttons.leftnav[0]+'">').css({zIndex:50, position:'absolute', left:config.offsets.left+config.defaultbuttons.leftnav[1]+'px', top:config.offsets.top+config.defaultbuttons.leftnav[2]+'px', cursor:'hand', cursor:'pointer'}).attr({title:'Back '+config.defaultbuttons.moveby+' panels'}).appendTo('body')
		config.$rightnavbutton=$('<img src="'+config.defaultbuttons.rightnav[0]+'">').css({zIndex:50, position:'absolute', left:config.offsets.left+config.$gallery.get(0).offsetWidth+config.defaultbuttons.rightnav[1]+'px', top:config.offsets.top+config.defaultbuttons.rightnav[2]+'px', cursor:'hand', cursor:'pointer'}).attr({title:'Forward '+config.defaultbuttons.moveby+' panels'}).appendTo('body')
		config.$leftnavbutton.bind('click', function(){ //assign nav button event handlers
			stepcarousel.stepBy(config.galleryid, -config.defaultbuttons.moveby)
		})
		config.$rightnavbutton.bind('click', function(){ //assign nav button event handlers
			stepcarousel.stepBy(config.galleryid, config.defaultbuttons.moveby)
		})
		if (config.panelbehavior.wraparound==false){ //if carousel viewer should stop at first or last panel (instead of wrap back or forth)
			this.fadebuttons(config, currentpanel)
		}
		return config.$leftnavbutton.add(config.$rightnavbutton)
	},

	alignpanels:function($, config){
		var paneloffset=0
		config.paneloffsets=[paneloffset] //array to store upper left offset of each panel (1st element=0)
		config.panelwidths=[] //array to store widths of each panel
		config.$panels.each(function(index){ //loop through panels
			var $currentpanel=$(this)
			$currentpanel.css({float: 'none', position: 'absolute', left: paneloffset+'px'}) //position panel
			$currentpanel.bind('click', function(e){return config.onpanelclick(e.target)}) //bind onpanelclick() to onclick event
			paneloffset+=stepcarousel.getCSSValue($currentpanel.css('marginRight')) + parseInt($currentpanel.get(0).offsetWidth || $currentpanel.css('width')) //calculate next panel offset
			config.paneloffsets.push(paneloffset) //remember this offset
			config.panelwidths.push(paneloffset-config.paneloffsets[config.paneloffsets.length-2]) //remember panel width
		})
		config.paneloffsets.pop() //delete last offset (redundant)
		var addpanelwidths=0
		var lastpanelindex=config.$panels.length-1
		config.lastvisiblepanel=lastpanelindex
		for (var i=config.$panels.length-1; i>=0; i--){
			addpanelwidths+=(i==lastpanelindex? config.panelwidths[lastpanelindex] : config.paneloffsets[i+1]-config.paneloffsets[i])
			if (config.gallerywidth>addpanelwidths){
				config.lastvisiblepanel=i //calculate index of panel that when in 1st position reveals the very last panel all at once based on gallery width
			}
		}
		config.$belt.css({width: paneloffset+'px'}) //Set Belt DIV to total panels' widths
		config.currentpanel=(config.panelbehavior.persist)? parseInt(this.getCookie(config.galleryid+"persist")) : 0 //determine 1st panel to show by default
		config.currentpanel=(typeof config.currentpanel=="number" && config.currentpanel<config.$panels.length)? config.currentpanel : 0
		var endpoint=config.paneloffsets[config.currentpanel]+(config.currentpanel==0? 0 : config.beltoffset)
		config.$belt.css({left: -endpoint+'px'})
		if (config.defaultbuttons.enable==true){ //if enable default back/forth nav buttons
			var $navbuttons=this.addnavbuttons($, config, config.currentpanel)
			$(window).bind("load resize", function(){ //refresh position of nav buttons when page loads/resizes, in case offsets weren't available document.oncontentload
				config.offsets={left:stepcarousel.getoffset(config.$gallery.get(0), "offsetLeft"), top:stepcarousel.getoffset(config.$gallery.get(0), "offsetTop")}
				config.$leftnavbutton.css({left:config.offsets.left+config.defaultbuttons.leftnav[1]+'px', top:config.offsets.top+config.defaultbuttons.leftnav[2]+'px'})
				config.$rightnavbutton.css({left:config.offsets.left+config.$gallery.get(0).offsetWidth+config.defaultbuttons.rightnav[1]+'px', top:config.offsets.top+config.defaultbuttons.rightnav[2]+'px'})
			})
		}
		if (config.autostep && config.autostep.enable){ //enable auto stepping of Carousel?		
			var $carouselparts=config.$gallery.add(typeof $navbuttons!="undefined"? $navbuttons : null)
			$carouselparts.bind('click', function(){
				config.autostep.status="stopped"
				stepcarousel.stopautostep(config)
			})
			$carouselparts.hover(function(){ //onMouseover
				stepcarousel.stopautostep(config)
				config.autostep.hoverstate="over"
			}, function(){ //onMouseout
				if (config.steptimer && config.autostep.hoverstate=="over" && config.autostep.status!="stopped"){
					config.steptimer=setInterval(function(){stepcarousel.autorotate(config.galleryid)}, config.autostep.pause)
					config.autostep.hoverstate="out"
				}
			})
			config.steptimer=setInterval(function(){stepcarousel.autorotate(config.galleryid)}, config.autostep.pause) //automatically rotate Carousel Viewer
		} //end enable auto stepping check
		this.createpaginate($, config)
		this.statusreport(config.galleryid)
		config.oninit()
		config.onslideaction(this)
	},

	stepTo:function(galleryid, pindex){ /*User entered pindex starts at 1 for intuitiveness. Internally pindex still starts at 0 */
		var config=stepcarousel.configholder[galleryid]
		if (typeof config=="undefined"){
			//alert("There's an error with your set up of Carousel Viewer \""+galleryid+ "\"!")
			return
		}
		stepcarousel.stopautostep(config)
		var pindex=Math.min(pindex-1, config.paneloffsets.length-1)
		var endpoint=config.paneloffsets[pindex]+(pindex==0? 0 : config.beltoffset)
		if (config.panelbehavior.wraparound==false && config.defaultbuttons.enable==true){ //if carousel viewer should stop at first or last panel (instead of wrap back or forth)
			this.fadebuttons(config, pindex)
		}
		config.$belt.animate({left: -endpoint+'px'}, config.panelbehavior.speed, function(){config.onslideaction(this)})
		config.currentpanel=pindex
		this.statusreport(galleryid)
	},

	stepBy:function(galleryid, steps, isauto){
		var config=stepcarousel.configholder[galleryid]
		if (typeof config=="undefined"){
			//alert("There's an error with your set up of Carousel Viewer \""+galleryid+ "\"!")
			return
		}
		if (!isauto) //if stepBy() function isn't called by autorotate() function
			stepcarousel.stopautostep(config)
		var direction=(steps>0)? 'forward' : 'back' //If "steps" is negative, that means backwards
		var pindex=config.currentpanel+steps //index of panel to stop at
		if (config.panelbehavior.wraparound==false){ //if carousel viewer should stop at first or last panel (instead of wrap back or forth)
			pindex=(direction=="back" && pindex<=0)? 0 : (direction=="forward")? Math.min(pindex, config.lastvisiblepanel) : pindex
			if (config.defaultbuttons.enable==true){ //if default nav buttons are enabled, fade them in and out depending on if at start or end of carousel
				stepcarousel.fadebuttons(config, pindex)
			}	
		}
		else{ //else, for normal stepBy behavior
			if (pindex>config.lastvisiblepanel && direction=="forward"){
				//if destination pindex is greater than last visible panel, yet we're currently not at the end of the carousel yet
				pindex=(config.currentpanel<config.lastvisiblepanel)? config.lastvisiblepanel : 0
			}
			else if (pindex<0 && direction=="back"){
				//if destination pindex is less than 0, yet we're currently not at the beginning of the carousel yet
				pindex=(config.currentpanel>0)? 0 : config.lastvisiblepanel /*wrap around left*/
			}
		}
		var endpoint=config.paneloffsets[pindex]+(pindex==0? 0 : config.beltoffset) //left distance for Belt DIV to travel to
		if (config.panelbehavior.wraparound==true && config.panelbehavior.wrapbehavior=="pushpull" && (pindex==0 && direction=='forward' || config.currentpanel==0 && direction=='back')){ //decide whether to apply "push pull" effect
			config.$belt.animate({left: -config.paneloffsets[config.currentpanel]-(direction=='forward'? 100 : -30)+'px'}, 'normal', function(){
				config.$belt.animate({left: -endpoint+'px'}, config.panelbehavior.speed, function(){config.onslideaction(this)})
			})
		}
		else
			config.$belt.animate({left: -endpoint+'px'}, config.panelbehavior.speed, function(){config.onslideaction(this)})
		config.currentpanel=pindex
		this.statusreport(galleryid)
	},

	autorotate:function(galleryid){
		var config=stepcarousel.configholder[galleryid]
		config.$belt.stop(true, true)
		this.stepBy(galleryid, config.autostep.moveby, true)
	},

	stopautostep:function(config){
		clearTimeout(config.steptimer)
	},

	statusreport:function(galleryid){
		var config=stepcarousel.configholder[galleryid]
		if (config.statusvars.length==3){ //if 3 status vars defined
			var startpoint=config.currentpanel //index of first visible panel 
			var visiblewidth=0
			for (var endpoint=startpoint; endpoint<config.paneloffsets.length; endpoint++){ //index (endpoint) of last visible panel
				visiblewidth+=config.panelwidths[endpoint]
				if (visiblewidth>config.gallerywidth){
					break
				}
			}
			startpoint+=1 //format startpoint for user friendiness
			endpoint=(endpoint+1==startpoint)? startpoint : endpoint //If only one image visible on the screen and partially hidden, set endpoint to startpoint
			var valuearray=[startpoint, endpoint, config.panelwidths.length]
			for (var i=0; i<config.statusvars.length; i++){
				window[config.statusvars[i]]=valuearray[i] //Define variable (with user specified name) and set to one of the status values
				config.$statusobjs[i].text(valuearray[i]+" ") //Populate element on page with ID="user specified name" with one of the status values
			}
		}
		stepcarousel.selectpaginate(jQuery, galleryid)
	},

	createpaginate:function($, config){
		if (config.$paginatediv.length==1){
			var $templateimg=config.$paginatediv.find('img["data-over"]:eq(0)') //reference first matching image on page
			var controlpoints=[], controlsrc=[], imgarray=[], moveby=$templateimg.attr("data-moveby") || 1
			var asize=(moveby==1? 0:1) + Math.floor((config.lastvisiblepanel+1) / moveby) //calculate # of pagination links to create
			var imghtml=$('<div>').append($templateimg.clone()).html() //get HTML of first matching image
			srcs=[$templateimg.attr('src'), $templateimg.attr('data-over'), $templateimg.attr('data-select')] //remember control's over and out, and selected image src
			for (var i=0; i<asize; i++){
				var moveto=Math.min(i*moveby, config.lastvisiblepanel)
				imgarray.push(imghtml.replace(/>$/, ' data-index="'+i+'" data-moveto="'+moveto+'" title="Move to Panel '+(moveto+1)+'">') +'\n')
				controlpoints.push(moveto) //store panel index each control goes to when clicked on
			}
			var $controls=$('<span></span>').replaceAll($templateimg).append(imgarray.join('')).find('img') //replace template link with links and return them
			$controls.css({cursor:'pointer'})
			config.$paginatediv.bind('click', function(e){
				var $target=$(e.target)
				if ($target.is('img') && $target.attr('data-over')){
					stepcarousel.stepTo(config.galleryid, parseInt($target.attr('data-moveto'))+1)
				}
			})
			config.$paginatediv.bind('mouseover mouseout', function(e){
				var $target=$(e.target)
				if ($target.is('img') && $target.attr('data-over')){
					if (parseInt($target.attr('data-index')) != config.pageinfo.curselected) //if this isn't the selected link
						$target.attr('src', srcs[(e.type=="mouseover")? 1 : 0])
				}
			})
			config.pageinfo={controlpoints:controlpoints, $controls:$controls,  srcs:srcs, prevselected:null, curselected:null}
		}
	},

	
	selectpaginate:function($, galleryid){
		var config=stepcarousel.configholder[galleryid]
		if (config.$paginatediv.length==1){
			for (var i=0; i<config.pageinfo.controlpoints.length; i++){
				if (config.pageinfo.controlpoints[i] <= config.currentpanel) //find largest control point that's less than or equal to current panel shown
					config.pageinfo.curselected=i
			}
			if (typeof config.pageinfo.prevselected!=null) //deselect previously selected link (if found)
				config.pageinfo.$controls.eq(config.pageinfo.prevselected).attr('src', config.pageinfo.srcs[0])
			config.pageinfo.$controls.eq(config.pageinfo.curselected).attr('src', config.pageinfo.srcs[2]) //select current paginate link
			config.pageinfo.prevselected=config.pageinfo.curselected //set current selected link to previous
		}
	},


	loadcontent:function(galleryid, url){
		var config=stepcarousel.configholder[galleryid]
		config.contenttype=['ajax', url]
		stepcarousel.stopautostep(config)
		stepcarousel.resetsettings($, config)
		stepcarousel.init(jQuery, config)

	},

	init:function($, config){
		config.gallerywidth=config.$gallery.width()
		config.offsets={left:stepcarousel.getoffset(config.$gallery.get(0), "offsetLeft"), top:stepcarousel.getoffset(config.$gallery.get(0), "offsetTop")}
		config.$belt=config.$gallery.find('.'+config.beltclass) //Find Belt DIV that contains all the panels
		config.$panels=config.$gallery.find('.'+config.panelclass) //Find Panel DIVs that each contain a slide
		config.panelbehavior.wrapbehavior=config.panelbehavior.wrapbehavior || "pushpull" //default wrap behavior to "pushpull"
		config.$paginatediv=$('#'+config.galleryid+'-paginate') //get pagination DIV (if defined)
		if (config.autostep)
			config.autostep.pause+=config.panelbehavior.speed
		config.onpanelclick=(typeof config.onpanelclick=="undefined")? function(target){} : config.onpanelclick //attach custom "onpanelclick" event handler
		config.onslideaction=(typeof config.onslide=="undefined")? function(){} : function(beltobj){$(beltobj).stop(); config.onslide()} //attach custom "onslide" event handler
		config.oninit=(typeof config.oninit=="undefined")? function(){} : config.oninit //attach custom "oninit" event handler
		config.beltoffset=stepcarousel.getCSSValue(config.$belt.css('marginLeft')) //Find length of Belt DIV's left margin
		config.statusvars=config.statusvars || []  //get variable names that will hold "start", "end", and "total" slides info
		config.$statusobjs=[$('#'+config.statusvars[0]), $('#'+config.statusvars[1]), $('#'+config.statusvars[2])]
		config.currentpanel=0
		stepcarousel.configholder[config.galleryid]=config //store config parameter as a variable
		if (config.contenttype[0]=="ajax" && typeof config.contenttype[1]!="undefined") //fetch ajax content?
			stepcarousel.getremotepanels($, config)
		else
			stepcarousel.alignpanels($, config) //align panels and initialize gallery
	},

	resetsettings:function($, config){
		config.$gallery.unbind()
		config.$belt.stop()
		config.$panels.remove()
		if (config.$leftnavbutton){
			config.$leftnavbutton.remove()
			config.$rightnavbutton.remove()
		}
		if (config.$paginatediv.length==1){
			config.$paginatediv.unbind()
			config.pageinfo.$controls.eq(0).attr('src', config.pageinfo.srcs[0]).removeAttr('data-index').removeAttr('data-moveto').removeAttr('title') //reset first pagination link so it acts as template again
			.end().slice(1).remove() //then remove all but first pagination link
		}
		if (config.autostep)
			config.autostep.status=null
		if (config.panelbehavior.persist){
			stepcarousel.setCookie(window[config.galleryid+"persist"], 0) //set initial panel to 0, overridden w/ current panel if window.unload is invoked
		}
	},

	setup:function(config){
		//Disable Step Gallery scrollbars ASAP dynamically (enabled for sake of users with JS disabled)
		document.write('<style type="text/css">\n#'+config.galleryid+'{overflow: hidden;}\n</style>')
		jQuery(document).ready(function($){
			config.$gallery=$('#'+config.galleryid)
			stepcarousel.init($, config)
		}) //end document.ready
		jQuery(window).bind('unload', function(){ //clean up on page unload
			stepcarousel.resetsettings($, config)
			if (config.panelbehavior.persist)
				stepcarousel.setCookie(config.galleryid+"persist", config.currentpanel)
			jQuery.each(config, function(ai, oi){
				oi=null
			})
			config=null
		})
	}
}


