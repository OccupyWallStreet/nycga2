/*
 * jQuery plugin: autoCompletefb(AutoComplete Facebook)
 * @requires jQuery v1.2.2 or later
 * using plugin:jquery.autocomplete.js
 *
 * Credits:
 * - Idea: Facebook
 * - Guillermo Rauch: Original MooTools script
 * - InteRiders <http://interiders.com/>
 *
 * Copyright (c) 2008 Widi Harsojo <wharsojo@gmail.com>, http://wharsojo.wordpress.com/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
jQuery.fn.autoCompletefb=function(b){var c=this;var d={ul:c,urlLookup:[""],acOptions:{},foundClass:".friend-tab",inputClass:".send-to-input"};if(b){jQuery.extend(d,b)}var a={params:d,removeFind:function(e){a.removeUsername(e);jQuery(e).unbind("click").parent().remove();jQuery(d.inputClass,c).focus();return c.acfb},removeUsername:function(f){var e=f.parentNode.id.split("-");jQuery("#send-to-usernames").removeClass(e[1])}};jQuery(d.foundClass+" img.p").click(function(){a.removeFind(this)});jQuery(d.inputClass,c).autocomplete(d.urlLookup,d.acOptions);jQuery(d.inputClass,c).result(function(n,o,m){var m=d.foundClass.replace(/\./,"");var o=String(o).split(" (");var j=o[1].substr(0,o[1].length-1);if(0===jQuery(d.inputClass).siblings("#un-"+j).length){var k="#link-"+j;var h=jQuery(k).attr("href");var i='<li class="'+m+'" id="un-'+j+'"><span><a href="'+h+'">'+o[0]+'</a></span> <span class="p">X</span></li>';var g=jQuery(d.inputClass,c).before(i);jQuery("#send-to-usernames").addClass(j);jQuery(".p",g[0].previousSibling).click(function(){a.removeFind(this)})}jQuery(d.inputClass,c).val("")});jQuery(d.inputClass,c).focus();return a};