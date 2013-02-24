/* Customize from here downwards */
jQuery(document).ready( function($) {
 	//Oh well... I guess we have to use jQuery ... if you are a javascript developer, consider MooTools if you have a choice, it's great!
	$('#LoginWithAjax_Form').submit(function(event){
		//Stop event, add loading pic...
		event.preventDefault();
		$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax');
		//Sort out url
		var url = $('#LoginWithAjax_Form').attr('action');
		//Get POST data
		var postData = getPostData('#LoginWithAjax_Form *[name]');
		postData['login-with-ajax'] = 'login'; //So that there is a JS fallback mechanism
		//Make Ajax Call
		$.post(url, postData, function(data){
			lwaAjax( data, 'LoginWithAjax_Status', '#login-with-ajax' );
			if(data.result === true){
				//Login Successful - Extra stuff to do
				if( data.widget != null ){
					$.get( data.widget, function(widget_result) {
						$('#LoginWithAjax').replaceWith(widget_result);
						$('#LoginWithAjax_Title').replaceWith($('#LoginWithAjax_Title_Substitute').text());
					});
				}else{
					if(data.redirect == null){
						window.location.reload();
					}else{
						window.location = data.redirect;
					}
				}
			}
		}, "json");
	});	
	
 	$('#LoginWithAjax_Remember').submit(function(event){
		//Stop event, add loading pic...
 		event.preventDefault();
		$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax');
		//Sort out url
		var url = $('#LoginWithAjax_Remember').attr('action');
		//Get POST data
		var postData = getPostData('#LoginWithAjax_Remember *[name]');
		//Make Ajax Call
		$.post(url, postData, function(data){
			lwaAjax( data, 'LoginWithAjax_Status', '#login-with-ajax' );
		}, "json");
	}); 	

	$('#LoginWithAjax_Register form').submit(function(event){
		//Stop event, add loading pic...
		event.preventDefault();
		$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax_Register');
		//Sort out url
		var url = $('#LoginWithAjax_Register form').attr('action');
		//Get POST data
		var postData = getPostData('#LoginWithAjax_Register form *[name]');
		$.post(url, postData, function(data){
			//variable status not here anymore
			lwaAjax( data, 'LoginWithAjax_Register_Status', '#LoginWithAjax_Register' );
		}, "json");
		/*$.ajax({
		  type: 'POST',
		  url: url,
		  data: postData,
		  success: function(data){
				//variable status not here anymore
				lwaAjax( data, 'LoginWithAjax_Register_Status', '#LoginWithAjax_Register' );
		  },
		  dataType: "json"
		});*/
	});	
 	
	//Visual Effects for hidden items
	//Remember
	$('#LoginWithAjax_Remember').hide();
	$('#LoginWithAjax_Links_Remember').click(function(event){
		event.preventDefault();
		$('#LoginWithAjax_Remember').show('slow');
	});
	$('#LoginWithAjax_Links_Remember_Cancel').click(function(event){
		event.preventDefault();
		$('#LoginWithAjax_Remember').hide('slow');
	});
	
	//Handle a AJAX call for Login, RememberMe or Registration
	function lwaAjax( data, statusElement, prependTo ){
		$('#LoginWithAjax_Loading').remove();
		if( data.result === true || data.result === false ){
			if(data.result === true){
				//Login Successful
				if( $('#'+statusElement).length > 0 ){
					$('#'+statusElement).attr('class','confirm').html(data.message);
				}else{
					$('<span id="'+statusElement+'" class="confirm">'+data.message+'</span>').prependTo( prependTo );
				}
			}else{
				//Login Failed
				//If there already is an error element, replace text contents, otherwise create a new one and insert it
				if( $('#'+statusElement).length > 0 ){
					$('#'+statusElement).attr('class','invalid').html(data.error);
				}else{
					$('<span id="'+statusElement+'" class="invalid">'+data.error+'</span>').prependTo( prependTo );
				}
				//We assume a link in the status message is for a forgotten password
				$('#'+statusElement).click(function(event){
					event.preventDefault();
					$('#LoginWithAjax_Remember').show('slow');
				});
			}
		}else{	
			//If there already is an error element, replace text contents, otherwise create a new one and insert it
			if( $('#'+statusElement).length > 0 ){
				$('#'+statusElement).attr('class','invalid').html('An error has occured. Please try again.');
			}else{
				$('<span id="'+statusElement+'" class="invalid">An error has occured. Please try again.</span>').prependTo( prependTo );
			}
		}
	}
	
	//Get all POSTable data from form.
	function getPostData(selector){
		var postData = {};
		$.each($(selector), function(index,el){
			el = $(el);
			postData[el.attr('name')] = el.attr('value');
		});
		return postData
	}
});

/* Do not edit after here*/
/* jQuery Tools 1.2.5 Overlay & Expose */
(function(a){function t(d,b){var c=this,j=d.add(c),o=a(window),k,f,m,g=a.tools.expose&&(b.mask||b.expose),n=Math.random().toString().slice(10);if(g){if(typeof g=="string")g={color:g};g.closeOnClick=g.closeOnEsc=false}var p=b.target||d.attr("rel");f=p?a(p):d;if(!f.length)throw"Could not find Overlay: "+p;d&&d.index(f)==-1&&d.click(function(e){c.load(e);return e.preventDefault()});a.extend(c,{load:function(e){if(c.isOpened())return c;var h=q[b.effect];if(!h)throw'Overlay: cannot find effect : "'+b.effect+
'"';b.oneInstance&&a.each(s,function(){this.close(e)});e=e||a.Event();e.type="onBeforeLoad";j.trigger(e);if(e.isDefaultPrevented())return c;m=true;g&&a(f).expose(g);var i=b.top,r=b.left,u=f.outerWidth({margin:true}),v=f.outerHeight({margin:true});if(typeof i=="string")i=i=="center"?Math.max((o.height()-v)/2,0):parseInt(i,10)/100*o.height();if(r=="center")r=Math.max((o.width()-u)/2,0);h[0].call(c,{top:i,left:r},function(){if(m){e.type="onLoad";j.trigger(e)}});g&&b.closeOnClick&&a.mask.getMask().one("click",
c.close);b.closeOnClick&&a(document).bind("click."+n,function(l){a(l.target).parents(f).length||c.close(l)});b.closeOnEsc&&a(document).bind("keydown."+n,function(l){l.keyCode==27&&c.close(l)});return c},close:function(e){if(!c.isOpened())return c;e=e||a.Event();e.type="onBeforeClose";j.trigger(e);if(!e.isDefaultPrevented()){m=false;q[b.effect][1].call(c,function(){e.type="onClose";j.trigger(e)});a(document).unbind("click."+n).unbind("keydown."+n);g&&a.mask.close();return c}},getOverlay:function(){return f},
getTrigger:function(){return d},getClosers:function(){return k},isOpened:function(){return m},getConf:function(){return b}});a.each("onBeforeLoad,onStart,onLoad,onBeforeClose,onClose".split(","),function(e,h){a.isFunction(b[h])&&a(c).bind(h,b[h]);c[h]=function(i){i&&a(c).bind(h,i);return c}});k=f.find(b.close||".close");if(!k.length&&!b.close){k=a('<a class="close"></a>');f.prepend(k)}k.click(function(e){c.close(e)});b.load&&c.load()}a.tools=a.tools||{version:"1.2.5"};a.tools.overlay={addEffect:function(d,
b,c){q[d]=[b,c]},conf:{close:null,closeOnClick:true,closeOnEsc:true,closeSpeed:"fast",effect:"default",fixed:!a.browser.msie||a.browser.version>6,left:"center",load:false,mask:null,oneInstance:true,speed:"normal",target:null,top:"10%"}};var s=[],q={};a.tools.overlay.addEffect("default",function(d,b){var c=this.getConf(),j=a(window);if(!c.fixed){d.top+=j.scrollTop();d.left+=j.scrollLeft()}d.position=c.fixed?"fixed":"absolute";this.getOverlay().css(d).fadeIn(c.speed,b)},function(d){this.getOverlay().fadeOut(this.getConf().closeSpeed,
d)});a.fn.overlay=function(d){var b=this.data("overlay");if(b)return b;if(a.isFunction(d))d={onBeforeLoad:d};d=a.extend(true,{},a.tools.overlay.conf,d);this.each(function(){b=new t(a(this),d);s.push(b);a(this).data("overlay",b)});return d.api?b:this}})(jQuery);
(function(b){function k(){if(b.browser.msie){var a=b(document).height(),d=b(window).height();return[window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,a-d<20?d:a]}return[b(document).width(),b(document).height()]}function h(a){if(a)return a.call(b.mask)}b.tools=b.tools||{version:"1.2.5"};var l;l=b.tools.expose={conf:{maskId:"exposeMask",loadSpeed:"slow",closeSpeed:"fast",closeOnClick:true,closeOnEsc:true,zIndex:9998,opacity:0.8,startOpacity:0,color:"#fff",onLoad:null,
onClose:null}};var c,i,e,g,j;b.mask={load:function(a,d){if(e)return this;if(typeof a=="string")a={color:a};a=a||g;g=a=b.extend(b.extend({},l.conf),a);c=b("#"+a.maskId);if(!c.length){c=b("<div/>").attr("id",a.maskId);b("body").append(c)}var m=k();c.css({position:"absolute",top:0,left:0,width:m[0],height:m[1],display:"none",opacity:a.startOpacity,zIndex:a.zIndex});a.color&&c.css("backgroundColor",a.color);if(h(a.onBeforeLoad)===false)return this;a.closeOnEsc&&b(document).bind("keydown.mask",function(f){f.keyCode==
27&&b.mask.close(f)});a.closeOnClick&&c.bind("click.mask",function(f){b.mask.close(f)});b(window).bind("resize.mask",function(){b.mask.fit()});if(d&&d.length){j=d.eq(0).css("zIndex");b.each(d,function(){var f=b(this);/relative|absolute|fixed/i.test(f.css("position"))||f.css("position","relative")});i=d.css({zIndex:Math.max(a.zIndex+1,j=="auto"?0:j)})}c.css({display:"block"}).fadeTo(a.loadSpeed,a.opacity,function(){b.mask.fit();h(a.onLoad);e="full"});e=true;return this},close:function(){if(e){if(h(g.onBeforeClose)===
false)return this;c.fadeOut(g.closeSpeed,function(){h(g.onClose);i&&i.css({zIndex:j});e=false});b(document).unbind("keydown.mask");c.unbind("click.mask");b(window).unbind("resize.mask")}return this},fit:function(){if(e){var a=k();c.css({width:a[0],height:a[1]})}},getMask:function(){return c},isLoaded:function(a){return a?e=="full":e},getConf:function(){return g},getExposed:function(){return i}};b.fn.mask=function(a){b.mask.load(a);return this};b.fn.expose=function(a){b.mask.load(a,this);return this}})(jQuery);