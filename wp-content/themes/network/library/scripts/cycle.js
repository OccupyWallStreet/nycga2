/*
 * jQuery Cycle Plugin for light-weight slideshows
 * Examples and documentation at: http://malsup.com/jquery/cycle/
 * Copyright (c) 2007 M. Alsup
 * Version: 1.93 (8/29/2007)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Requires: jQuery v1.1.3.1 or later
 *
 * Based on the work of:
 *  1) Matt Oakes (http://portfolio.gizone.co.uk/applications/slideshow/)
 *  2) Torsten Baldes (http://medienfreunde.com/lab/innerfade/)
 *  3) Benjamin Sterling (http://www.benjaminsterling.com/experiments/jqShuffle/)
 */
(function($) {

var ver = '1.93';

$.fn.cycle = function(options) {
    return this.each(function() {
        if (options && options.constructor == String) {
            switch(options) {
            case 'stop':
                if (this.cycleTimeout) clearTimeout(this.cycleTimeout);
                this.cycleTimeout = 0;
                return;
            case 'pause':
                this.cyclePause = 1;
                return;
            case 'resume':
                this.cyclePause = 0;
                return;
            default:
                options = { fx: options };
            };
        }
        var $cont = $(this), $slides = $cont.children(), els = $slides.get();
        if (els.length < 2) return; // don't bother

        var opts = $.extend({}, $.fn.cycle.defaults, options || {}, $.meta ? $cont.data() : {});
        if (opts.autostop) 
            opts.countdown = els.length;
            
        opts.before = opts.before ? [opts.before] : [];
        opts.after = opts.after ? [opts.after] : [];
        opts.after.unshift(function(){ opts.busy=0; });

        // allow shorthand overrides of width, height and timeout
        var cls = this.className;
        var w = parseInt((cls.match(/w:(\d+)/)||[])[1]) || opts.width;
        var h = parseInt((cls.match(/h:(\d+)/)||[])[1]) || opts.height;
        opts.timeout = parseInt((cls.match(/t:(\d+)/)||[])[1]) || opts.timeout;

        if ($cont.css('position') == 'static') 
            $cont.css('position', 'relative');
        if (w) 
            $cont.width(w);
        if (h && h != 'auto') 
            $cont.height(h);

        $slides.each(function(i){ $(this).css('z-index', els.length-i) }).css('position','absolute').hide();
        $(els[0]).show();
        if (opts.fit && w) 
            $slides.width(w);
        if (opts.fit && h && h != 'auto') 
            $slides.height(h);
        if (opts.pause) 
            $cont.hover(function(){this.cyclePause=1;}, function(){this.cyclePause=0;});

        // run transition init fn
        var init = $.fn.cycle.transitions[opts.fx];
        if ($.isFunction(init))
            init($cont, $slides, opts);

        $slides.each(function() {
            var $el = $(this);
            this.cycleH = (opts.fit && h) ? h : $el.height();
            this.cycleW = (opts.fit && w) ? w : $el.width();
        });

        opts.cssBefore = opts.cssBefore || {};
        opts.animIn = opts.animIn || {};
        opts.animOut = opts.animOut || {};

        $slides.not(':eq(0)').css(opts.cssBefore);
        if (opts.cssFirst)
            $($slides[0]).css(opts.cssFirst);

        if (opts.timeout) {
            // ensure that timeout and speed settings are sane
            if (opts.speed.constructor == String)
                opts.speed = {slow: 600, fast: 200}[opts.speed] || 400;
            if (!opts.sync)
                opts.speed = opts.speed / 2;
            while((opts.timeout - opts.speed) < 250)
                opts.timeout += opts.speed;
        }
        if (opts.easing) 
            opts.easeIn = opts.easeOut = opts.easing;
        if (!opts.speedIn) 
            opts.speedIn = opts.speed;
        if (!opts.speedOut) 
            opts.speedOut = opts.speed;

        opts.nextSlide = opts.random ? (Math.floor(Math.random() * (els.length-1)))+1 : 1;
        opts.currSlide = 0;

        // fire artificial events
        var e0 = $slides[0];
        if (opts.before.length)
            opts.before[0].apply(e0, [e0, e0, opts, true]);
        if (opts.after.length > 1)
            opts.after[1].apply(e0, [e0, e0, opts, true]);
        
        if (opts.click && !opts.next)
            opts.next = opts.click;
        if (opts.next)
            $(opts.next).bind('click', function(){return advance(els,opts,opts.rev?-1:1)});
        if (opts.prev)
            $(opts.prev).bind('click', function(){return advance(els,opts,opts.rev?1:-1)});
        if (opts.pager)
            buildPager(els,opts);
        if (opts.timeout)
            this.cycleTimeout = setTimeout(function(){go(els,opts,0,!opts.rev)}, opts.timeout + (opts.delay||0));
    });
};

function go(els, opts, manual, fwd) {
    if (opts.busy) return;

    var p = els[0].parentNode, curr = els[opts.currSlide], next = els[opts.nextSlide];
    if (p.cycleTimeout === 0 && !manual) 
        return;

    if (!manual && !p.cyclePause && opts.autostop && (--opts.countdown <= 0)) 
        return;
        
    if (opts.before.length)
        $.each(opts.before, function(i,o) { o.apply(next, [curr, next, opts, fwd]); });
    var after = function() {
        $.each(opts.after, function(i,o) { o.apply(next, [curr, next, opts, fwd]); });
    };

    if (manual || !p.cyclePause) {
        if (opts.nextSlide != opts.currSlide) {
            opts.busy = 1;
            if (opts.fxFn)
                opts.fxFn(curr, next, opts, after);
            else if ($.isFunction($.fn.cycle[opts.fx]))
                $.fn.cycle[opts.fx](curr, next, opts, after);
            else
                $.fn.cycle.custom(curr, next, opts, after);
        }
        if (opts.random) {
            opts.currSlide = opts.nextSlide;
            while (opts.nextSlide == opts.currSlide)
                opts.nextSlide = Math.floor(Math.random() * els.length);
        }
        else { // sequence
            var roll = (opts.nextSlide + 1) == els.length;
            opts.nextSlide = roll ? 0 : opts.nextSlide+1;
            opts.currSlide = roll ? els.length-1 : opts.nextSlide-1;
        }
        if (opts.pager)
            $(opts.pager).find('a').removeClass('activeSlide').filter('a:eq('+opts.currSlide+')').addClass('activeSlide');
    }
    if (opts.timeout)
        p.cycleTimeout = setTimeout(function() { go(els,opts,0,!opts.rev) }, opts.timeout);
};

// advance slide forward or back
function advance(els, opts, val) {
    var p = els[0].parentNode, timeout = p.cycleTimeout;
    if (timeout) {
        clearTimeout(timeout);
        p.cycleTimeout = 0;
    }
    opts.nextSlide = opts.currSlide + val;
    if (opts.nextSlide < 0)
        opts.nextSlide = els.length - 1;
    else if (opts.nextSlide >= els.length)
        opts.nextSlide = 0;
    go(els, opts, 1, val>=0);
    return false;
};

function buildPager(els, opts) {
    var $p = $(opts.pager);
    $.each(els, function(i,o) {
        var $a = $('<a href="#">'+(i+1)+'</a>').appendTo($p).bind('click',function() {
            opts.nextSlide = i;
            go(els,opts,1,!opts.rev);
            return false;
        });
        if (i == 0) 
            $a.addClass('activeSlide');
    });
};

$.fn.cycle.custom = function(curr, next, opts, cb) {
    var $l = $(curr), $n = $(next);
    $n.css(opts.cssBefore);
    var fn = function() {$n.animate(opts.animIn, opts.speedIn, opts.easeIn, cb)};
    $l.animate(opts.animOut, opts.speedOut, opts.easeOut, function() {
        if (opts.cssAfter) $l.css(opts.cssAfter);
        if (!opts.sync) fn();
    });
    if (opts.sync) fn();
};

$.fn.cycle.transitions = {
    fade: function($cont, $slides, opts) {
        $slides.not(':eq(0)').css('opacity',0);
        opts.before.push(function() { $(this).show() });
        opts.animIn    = { opacity: 1 };
        opts.animOut   = { opacity: 0 };
        opts.cssAfter  = { display: 'none' };
    }
};

$.fn.cycle.ver = function() { return ver; };

// override these globally if you like (they are all optional)
$.fn.cycle.defaults = {
    fx:         'fade', // one of: fade, shuffle, zoom, slideX, slideY, scrollUp/Down/Left/Right
    timeout:     4000,  // milliseconds between slide transitions (0 to disable auto advance)
    speed:       1000,  // speed of the transition (any valid fx speed value)
    speedIn:     null,  // speed of the 'in' transition
    speedOut:    null,  // speed of the 'out' transition
    click:       null,  // @deprecated; please use the 'next' option
    next:        null,  // id of element to use as click trigger for next slide
    prev:        null,  // id of element to use as click trigger for previous slide
    pager:       null,  // id of element to use as pager container
    before:      null,  // transition callback (scope set to element to be shown)
    after:       null,  // transition callback (scope set to element that was shown)
    easing:      null,  // easing method for both in and out transitions
    easeIn:      null,  // easing for "in" transition
    easeOut:     null,  // easing for "out" transition
    shuffle:     null,  // coords for shuffle animation, ex: { top:15, left: 200 }
    animIn:      null,  // properties that define how the slide animates in
    animOut:     null,  // properties that define how the slide animates out
    cssBefore:   null,  // properties that define the initial state of the slide before transitioning in
    cssAfter:    null,  // properties that defined the state of the slide after transitioning out
    fxFn:        null,  // function used to control the transition
    height:     'auto', // container height
    sync:        1,     // true if in/out transitions should occur simultaneously
    random:      0,     // true for random, false for sequence (not applicable to shuffle fx)
    fit:         0,     // force slides to fit container
    pause:       0,     // true to enable "pause on hover"
    autostop:    0,     // true to end slideshow after X transitions (where X == slide count)
    delay:       0      // additional delay (in ms) for first transition (hint: can be negative)
};

})(jQuery);

/*
 * jQuery Cycle Plugin Transition Definitions
 * This script is a plugin for the jQuery Cycle Plugin
 * Examples and documentation at: http://malsup.com/jquery/cycle/
 * Copyright (c) 2007 M. Alsup
 * Version:  1.93
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

//
// These functions define one-time slide initialization for the named
// transitions. To save file size feel free to remove any of these that you 
// don't need.
//

// scrollUp/Down/Left/Right
jQuery.fn.cycle.transitions.scrollUp = function($cont, $slides, opts) {
    $cont.css('overflow','hidden');
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.cssBefore.top = next.offsetHeight;
        opts.animOut.top = 0-curr.offsetHeight;
    });
    opts.cssFirst = { top: 0 };
    opts.animIn   = { top: 0 };
    opts.cssAfter = { display: 'none' };
};
jQuery.fn.cycle.transitions.scrollDown = function($cont, $slides, opts) {
    $cont.css('overflow','hidden');
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.cssBefore.top = 0-next.offsetHeight;
        opts.animOut.top = curr.offsetHeight;
    });
    opts.cssFirst = { top: 0 };
    opts.animIn   = { top: 0 };
    opts.cssAfter = { display: 'none' };
};
jQuery.fn.cycle.transitions.scrollLeft = function($cont, $slides, opts) {
    $cont.css('overflow','hidden');
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.cssBefore.left = next.offsetWidth;
        opts.animOut.left = 0-curr.offsetWidth;
    });
    opts.cssFirst = { left: 0 };
    opts.animIn   = { left: 0 };
};
jQuery.fn.cycle.transitions.scrollRight = function($cont, $slides, opts) {
    $cont.css('overflow','hidden');
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.cssBefore.left = 0-next.offsetWidth;
        opts.animOut.left = curr.offsetWidth;
    });
    opts.cssFirst = { left: 0 };
    opts.animIn   = { left: 0 };
};
jQuery.fn.cycle.transitions.scrollHorz = function($cont, $slides, opts) {
    $cont.css('overflow','hidden').width();
//    $slides.show();
    opts.before.push(function(curr, next, opts, fwd) {
        $(this).show();
        var currW = curr.offsetWidth, nextW = next.offsetWidth;
        opts.cssBefore = fwd ? { left: nextW } : { left: -nextW };
        opts.animIn.left = 0;
        opts.animOut.left = fwd ? -currW : currW;
        $slides.not(curr).css(opts.cssBefore);
    });
    opts.cssFirst = { left: 0 };
    opts.cssAfter = { display: 'none' }
};
jQuery.fn.cycle.transitions.scrollVert = function($cont, $slides, opts) {
    $cont.css('overflow','hidden');
//    $slides.show();
    opts.before.push(function(curr, next, opts, fwd) {
        $(this).show();
        var currH = curr.offsetHeight, nextH = next.offsetHeight;
        opts.cssBefore = fwd ? { top: -nextH } : { top: nextH };
        opts.animIn.top = 0;
        opts.animOut.top = fwd ? currH : -currH;
        $slides.not(curr).css(opts.cssBefore);
    });
    opts.cssFirst = { top: 0 };
    opts.cssAfter = { display: 'none' }
};

// slideX/slideY
jQuery.fn.cycle.transitions.slideX = function($cont, $slides, opts) {
    opts.animIn  = { width: 'show' };
    opts.animOut = { width: 'hide' };
};
jQuery.fn.cycle.transitions.slideY = function($cont, $slides, opts) {
    opts.animIn  = { height: 'show' };
    opts.animOut = { height: 'hide' };
};

// shuffle
jQuery.fn.cycle.transitions.shuffle = function($cont, $slides, opts) {
    var w = $cont.css('overflow', 'visible').width();
    $slides.css({left: 0, top: 0});
    opts.before.push(function() { $(this).show() });
    opts.speed = opts.speed / 2; // shuffle has 2 transitions        
    opts.random = 0;
    opts.shuffle = opts.shuffle || {left:-w, top:15};
    opts.els = [];
    for (var i=0; i < $slides.length; i++)
        opts.els.push($slides[i]);

    // custom transition fn (hat tip to Benjamin Sterling for this bit of sweetness!)
    opts.fxFn = function(curr, next, opts, cb) {
        var $el = jQuery(curr);
        $el.animate(opts.shuffle, opts.speedIn, opts.easeIn, function() {
            opts.els.push(opts.els.shift());
            for (var i=0, len=opts.els.length; i < len; i++)
                jQuery(opts.els[i]).css('z-index', len-i);
            $el.animate({left:0, top:0}, opts.speedOut, opts.easeOut, function() {
                $(this).hide();
                if (cb) cb();
            });
        });
    };
};

// turnUp/Down/Left/Right
jQuery.fn.cycle.transitions.turnUp = function($cont, $slides, opts) {
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.cssBefore.top = next.cycleH;
        opts.animIn.height = next.cycleH;
    });
    opts.cssFirst  = { top: 0 };
    opts.cssBefore = { height: 0 };
    opts.animIn    = { top: 0 };
    opts.animOut   = { height: 0 };
    opts.cssAfter  = { display: 'none' };
};
jQuery.fn.cycle.transitions.turnDown = function($cont, $slides, opts) {
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.animIn.height = next.cycleH;
        opts.animOut.top   = curr.cycleH;
    });
    opts.cssFirst  = { top: 0 };
    opts.cssBefore = { top: 0, height: 0 };
    opts.animOut   = { height: 0 };
    opts.cssAfter  = { display: 'none' };
};
jQuery.fn.cycle.transitions.turnLeft = function($cont, $slides, opts) {
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.cssBefore.left = next.cycleW;
        opts.animIn.width = next.cycleW;
    });
    opts.cssBefore = { width: 0 };
    opts.animIn    = { left: 0 };
    opts.animOut   = { width: 0 };
    opts.cssAfter  = { display: 'none' };
};
jQuery.fn.cycle.transitions.turnRight = function($cont, $slides, opts) {
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.animIn.width = next.cycleW;
        opts.animOut.left = curr.cycleW;
    });
    opts.cssBefore = { left: 0, width: 0 };
    opts.animIn    = { left: 0 };
    opts.animOut   = { width: 0 };
    opts.cssAfter  = { display: 'none' };
};

// zoom
jQuery.fn.cycle.transitions.zoom = function($cont, $slides, opts) {
    opts.cssFirst = { top:0, left: 0 }; 
    opts.cssAfter = { display: 'none' };
    
    opts.before.push(function(curr, next, opts) {
        $(this).show();
        opts.cssBefore = { width: 0, height: 0, top: next.cycleH/2, left: next.cycleW/2 };
        opts.animIn    = { top: 0, left: 0, width: next.cycleW, height: next.cycleH };
        opts.animOut   = { width: 0, height: 0, top: curr.cycleH/2, left: curr.cycleW/2 };
    });    
};