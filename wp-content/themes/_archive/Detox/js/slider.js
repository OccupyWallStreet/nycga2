
//** Created: March 19th, 08'
//** Aug 16th, 08'- Updated to v 1.4:
	//1) Adds ability to set speed/duration of panel animation (in milliseconds)
	//2) Adds persistence support, so the last viewed panel is recalled when viewer returns within same browser session
	//3) Adds ability to specify whether panels should stop at the very last and first panel, or wrap around and start all over again
	//4) Adds option to specify two navigational image links positioned to the left and right of the Carousel Viewer to move the panels back and forth

//** Aug 27th, 08'- Nav buttons (if enabled) also repositions themselves now if window is resized

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
	},

	addnavbuttons:function(config, currentpanel){
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
		config.currentpanel=(config.panelbehavior.persist)? parseInt(this.getCookie(window[config.galleryid+"persist"])) : 0 //determine 1st panel to show by default
		config.currentpanel=(typeof config.currentpanel=="number" && config.currentpanel<config.$panels.length)? config.currentpanel : 0
		if (config.currentpanel!=0){
			var endpoint=config.paneloffsets[config.currentpanel]+(config.currentpanel==0? 0 : config.beltoffset)
			config.$belt.css({left: -endpoint+'px'})
		}
		if (config.defaultbuttons.enable==true){ //if enable default back/forth nav buttons
			this.addnavbuttons(config, config.currentpanel)
			$(window).bind("load, resize", function(){ //refresh position of nav buttons when page loads/resizes, in case offsets weren't available document.oncontentload
				config.offsets={left:stepcarousel.getoffset(config.$gallery.get(0), "offsetLeft"), top:stepcarousel.getoffset(config.$gallery.get(0), "offsetTop")}
				config.$leftnavbutton.css({left:config.offsets.left+config.defaultbuttons.leftnav[1]+'px', top:config.offsets.top+config.defaultbuttons.leftnav[2]+'px'})
				config.$rightnavbutton.css({left:config.offsets.left+config.$gallery.get(0).offsetWidth+config.defaultbuttons.rightnav[1]+'px', top:config.offsets.top+config.defaultbuttons.rightnav[2]+'px'})
			})
		}
		this.statusreport(config.galleryid)
		config.oninit()
		config.onslideaction(this)
	},

	stepTo:function(galleryid, pindex){ /*User entered pindex starts at 1 for intuitiveness. Internally pindex still starts at 0 */
		var config=stepcarousel.configholder[galleryid]
		if (typeof config=="undefined"){
			alert("There's an error with your set up of Carousel Viewer \""+galleryid+ "\"!")
			return
		}
		var pindex=Math.min(pindex-1, config.paneloffsets.length-1)
		var endpoint=config.paneloffsets[pindex]+(pindex==0? 0 : config.beltoffset)
		if (config.panelbehavior.wraparound==false && config.defaultbuttons.enable==true){ //if carousel viewer should stop at first or last panel (instead of wrap back or forth)
			this.fadebuttons(config, pindex)
		}
		config.$belt.animate({left: -endpoint+'px'}, config.panelbehavior.speed, function(){config.onslideaction(this)})
		config.currentpanel=pindex
		this.statusreport(galleryid)
	},

	stepBy:function(galleryid, steps){
		var config=stepcarousel.configholder[galleryid]
		if (typeof config=="undefined"){
			alert("There's an error with your set up of Carousel Viewer \""+galleryid+ "\"!")
			return
		}
		var direction=(steps>0)? 'forward' : 'back' //If "steps" is negative, that means backwards
		var pindex=config.currentpanel+steps //index of panel to stop at
		if (config.panelbehavior.wraparound==false){ //if carousel viewer should stop at first or last panel (instead of wrap back or forth)
			pindex=(direction=="back" && pindex<=0)? 0 : (direction=="forward")? Math.min(pindex, config.lastvisiblepanel) : pindex
			if (config.defaultbuttons.enable==true){ //if default nav buttons are enabled, fade them in and out depending on if at start or end of carousel
				stepcarousel.fadebuttons(config, pindex)
			}	
		}
		else{ //else, for normal stepBy behavior
		pindex=(pindex>config.paneloffsets.length-1 || pindex<0 && pindex-steps>0)? 0 : (pindex<0)? config.paneloffsets.length+steps : pindex //take into account end or starting panel and step direction
		}
		var endpoint=config.paneloffsets[pindex]+(pindex==0? 0 : config.beltoffset) //left distance for Belt DIV to travel to
		if (pindex==0 && direction=='forward' || config.currentpanel==0 && direction=='back' && config.panelbehavior.wraparound==true){ //decide whether to apply "push pull" effect
			config.$belt.animate({left: -config.paneloffsets[config.currentpanel]-(direction=='forward'? 100 : -30)+'px'}, 'normal', function(){
				config.$belt.animate({left: -endpoint+'px'}, config.panelbehavior.speed, function(){config.onslideaction(this)})
			})
		}
		else
			config.$belt.animate({left: -endpoint+'px'}, config.panelbehavior.speed, function(){config.onslideaction(this)})
		config.currentpanel=pindex
		this.statusreport(galleryid)
	},

	statusreport:function(galleryid){
		var config=stepcarousel.configholder[galleryid]
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
	},

	setup:function(config){
		//Disable Step Gallery scrollbars ASAP dynamically (enabled for sake of users with JS disabled)
		document.write('<style type="text/css">\n#'+config.galleryid+'{overflow: hidden;}\n</style>')
		jQuery(document).ready(function($){
			config.$gallery=$('#'+config.galleryid)
			config.gallerywidth=config.$gallery.width()
			config.offsets={left:stepcarousel.getoffset(config.$gallery.get(0), "offsetLeft"), top:stepcarousel.getoffset(config.$gallery.get(0), "offsetTop")}
			config.$belt=config.$gallery.find('.'+config.beltclass) //Find Belt DIV that contains all the panels
			config.$panels=config.$gallery.find('.'+config.panelclass) //Find Panel DIVs that each contain a slide
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
		}) //end document.ready
		jQuery(window).bind('unload', function(){ //clean up
			if (config.panelbehavior.persist){
				stepcarousel.setCookie(window[config.galleryid+"persist"], config.currentpanel)
			}
			jQuery.each(config, function(ai, oi){
				oi=null
			})
			config=null
		})
	}
}


