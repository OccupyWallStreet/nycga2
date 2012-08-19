/**	
*	smoothSlideshow v2.7.5 jQuery script developed by Maxim Palianytsia.
*
*	v2.3 fixes issue with thumbnail scrolling in Chrome browsers
*	v2.4 fixes issue with non-centered images in IE
*	v2.5 fixes issue with imgLink disappearing when showArrows is off
*	v2.6 15/11/2010 adds new property slideInfoZoneStatic
*	v2.7 30/11/2010 fixes Adblock browser add-on conflict
*	v2.7.5 01/12/2010 fixes vertical image alignment when images are smaller than gallery dimensions
*
*/
(function ($) {
    $.fn.extend({
        smoothSlideshow: function (wrapper, options) {
            var _this = this;
            window._T = {};
            _T.slideshow = function (options) {
                this.options = {};
                this.options.delay = 10000;
                this.options.infoSpeed = this.options.imgSpeed = this.options.delay / 1000;
                this.options.carouselOpacity = this.options.navHover = 70;
                this.options.navOpacity = 25;
                this.options.thumbScrollSpeed = 5000;
                this.options.letterbox = '#000';
                this.options.slideInfoZoneOpacity = 0.7;
                this.options.slideInfoZoneStatic = false;
                var _th = this;
                $.each(options, function (key, val) {
                    _th.options[key] = val
                });
                this.options.thumbOpacity = this.options.carouselOpacity * 100;
                if (this.options.infoContainerSelector) {
                    $(this.options.infoContainerSelector).css('opacity', this.options.slideInfoZoneOpacity);
                    $(this.options.infoContainerSelector).get(0).style.filter = 'alpha(opacity=' + (this.options.slideInfoZoneOpacity * 100) + ')'
                }
                this.n = "_T._s";
                this.c = 0;
                this.a = []
            };
            _T.slideshow.prototype = {
                init: function (s, options) {
                    var _this = this;
                    var z = options.imgContainer;
                    var b = options.imgPrevBtn;
                    var f = options.imgNextBtn;
                    var q = options.imgLinkBtn;
                    s = $(s);
                    var m = s.find(this.options.elementSelector),
                        i = 0,
                        w = 0;
                    this.l = m.length;
                    $(q).height($(q).parent().height());
                    this.q = $(q).get(0);
                    this.f = $(z).get(0);
                    this.r = $(this.options.infoContainerSelector).get(0);
                    this.o = parseInt(_T.style.val(z, 'width')) || this.f.offsetWidth;
                    if (!this.options.showArrows) {
                        if (this.options.imgPrevBtn) $(this.options.imgPrevBtn).hide();
                        if (this.options.imgNextBtn) $(this.options.imgNextBtn).hide()
                    }
                    if (this.options.showCarousel) {
                        this.p = $(this.options.thumbnailContainerSelector).css({
                            'white-space': 'nowrap',
                            'padding-right': '10px'
                        }).get(0)
                    }
                    for (i; i < this.l; i++) {
                        this.a[i] = {};
                        var h = m[i],
                            a = this.a[i];
                        a.t = $(h).find(this.options.titleSelector).get(0).innerHTML;
                        a.d = $(h).find(this.options.subtitleSelector).get(0).innerHTML;
                        a.l = $(h).find(this.options.linkSelector).get(0) ? $(h).find(this.options.linkSelector).get(0).href : '';
                        a.p = $(h).find(this.options.imageSelector).attr("src");
                        if (this.options.showCarousel) {
                            var g = $(h).find(this.options.thumbnailSelector).get(0);
                            this.p.appendChild(g);
                            if (i != this.l - 1) {
                                g.style.marginRight = this.options.thumbSpacing + 'px'
                            }
                            g.style.opacity = this.options.thumbOpacity / 100;
                            g.style.filter = 'alpha(opacity=' + this.options.thumbOpacity + ')';
                            $(g).mouseover(new Function('_T.alpha.set(this,100,5)'));
                            (function (t) {
                                $(g).mouseover(function () {
                                    $(_this.options.thumbnailInfoSelector).html(t)
                                })
                            })(a.t);
                            $(g).mouseout(new Function('_T.alpha.set(this,' + this.options.thumbOpacity + ',5)'));
                            g.onclick = new Function(this.n + '.pr(' + i + ',1)');
                            $(g).click(function () {
                                $(_this.options.carouselSlideDownSelector).click()
                            })
                        }
                    }
                    if (this.options.showCarousel) {
                        $(this.options.carouselSlideDownSelector).click(function () {
                            var el = this;
                            $(_this.options.carouselContainerSelector).animate({
                                top: el.opened ? -110 : 0
                            }, _this.options.carouselSlideDownSpeed, null, function () {
                                el.opened = !el.opened
                            })
                        });
                        $(this.options.thumbnailContainerSelector).mouseover(function (e) {
                            var x = e.pageX - $(_this.options.thumbnailContainerSelector).parent().offset().left;
                            if (x < 180) {
                                (new Function('_T.scroll.init("' + _this.options.thumbnailContainerSelector + '",-1,' + _this.options.thumbScrollSpeed + ')'))()
                            } else if (x > $(_this.options.thumbnailContainerSelector).parent().width() - 180) {
                                (new Function('_T.scroll.init("' + _this.options.thumbnailContainerSelector + '",1,' + _this.options.thumbScrollSpeed + ')'))()
                            } else {
                                (new Function('_T.scroll.cl("' + _this.options.thumbnailContainerSelector + '")'))()
                            }
                        }).mouseout(function () {
                            (new Function('_T.scroll.cl("' + _this.options.thumbnailContainerSelector + '")'))()
                        })
                    } else {
                        $(this.options.carouselContainerSelector).hide()
                    }
                    if (b && f) {
                        b = $(b).get(0);
                        f = $(f).get(0);
                        b.style.opacity = f.style.opacity = this.options.navOpacity / 100;
                        b.style.filter = f.style.filter = 'alpha(opacity=' + this.options.navOpacity + ')';
                        b.onmouseover = f.onmouseover = new Function('_T.alpha.set(this,' + this.options.navHover + ',5)');
                        b.onmouseout = f.onmouseout = new Function('_T.alpha.set(this,' + this.options.navOpacity + ',5)');
                        b.onclick = new Function(this.n + '.mv(-1,1)');
                        f.onclick = new Function(this.n + '.mv(1,1)')
                    }
                    this.options.timed ? this.is(0, 0) : this.is(0, 1)
                },
                mv: function (d, c) {
                    var t = this.c + d;
                    this.c = t = t < 0 ? this.l - 1 : t > this.l - 1 ? 0 : t;
                    this.pr(t, c)
                },
                pr: function (t, c) {
                    clearTimeout(this.lt);
                    if (c) {
                        clearTimeout(this.at);
                        this.at = setTimeout(new Function(this.n + '.mv(1,0)'), this.options.delay)
                    }
                    this.c = t;
                    this.is(t, c)
                },
                is: function (s, c) {
                    if (this.options.showInfopane && !this.options.slideInfoZoneStatic) {
                        _T.height.set(this.r, 1, this.options.infoSpeed / 2, -1)
                    }
                    this.i = document.createElement('img');
                    this.i.style.opacity = 0;
                    this.i.style.filter = 'alpha(opacity=0)';
                    this.i.onload = new Function(this.n + '.le(' + s + ',' + c + ')');
                    this.i.src = this.a[s].p;
                    if (this.options.showCarousel) {
                        var a = $(this.p).find('img'),
                            l = a.length,
                            x = 0;
                        for (x; x < l; x++) {
                            a.get(x).style.borderColor = x != s ? '' : this.options.borderActive
                        }
                    }
                },
                le: function (s, c) {
                    if (this.i.getAttribute('width')) this.i.removeAttribute('width');
                    if (this.i.getAttribute('height')) this.i.removeAttribute('height');
					this.f.appendChild(this.i);
					if($(this.i).width()>0){
						var w = this.o - $(this.i).width();
						var m = $(this.f).find('img');
						var _this = this;
						if (m.length > 2) {
							this.f.removeChild(m.get(0))
						}
						if (w > 0) {
							var l = Math.floor(w / 2);
							this.i.style.marginLeft = l + 'px';
							this.i.style.marginRight = (w - l) + 'px';
							this.i.style.marginTop = ($(this.f).parent().height() - $(this.i).height())/2 + 'px';
							m = $(this.f).find('img');
							if (m.length > 1) {
								_T.alpha.set(m.get(0), 0, this.options.imgSpeed, function () {
									try {
										_this.f.removeChild(m.get(0))
									} catch (e) {}
								})
							}
						}
						_T.alpha.set(this.i, 100, this.options.imgSpeed);
						var n = new Function(this.n + '.nf(' + s + ')');
						if(this.options.slideInfoZoneStatic){
							n();
						}else{
							this.lt = setTimeout(n, this.options.imgSpeed * 100);
						}
						if (!c) {
							this.at = setTimeout(new Function(this.n + '.mv(1,0)'), this.options.delay)
						}
						if (this.a[s].l != '') {
							this.q.onclick = new Function('window.location="' + this.a[s].l + '"');
							this.q.onmouseover = new Function('this.className="' + this.options.link + '"');
							this.q.onmouseout = new Function('this.className=""');
							this.q.style.cursor = 'pointer'
						} else {
							this.q.onclick = this.q.onmouseover = null;
							this.q.style.cursor = 'default'
						}
					}else{
						var t = this;
						setTimeout(function(){t.le(s,c)}, 50);
					}
                },
                nf: function (s) {
                    if (this.options.showInfopane) {
                        s = this.a[s];
                        $(this.r).find(this.options.titleSelector).html(s.t);
                        if (this.options.showCarousel && !$(this.options.carouselSlideDownSelector).get(0).opened) $(this.options.thumbnailInfoSelector).html(s.t);
                        $(this.r).find(this.options.subtitleSelector).html(s.d);
                        this.r.style.height = 'auto';
                        var h = parseInt(this.r.offsetHeight);
                        if(!this.options.slideInfoZoneStatic){
							this.r.style.height = 0;
							_T.height.set(this.r, h, this.options.infoSpeed, 0);
						}else{
							$(this.r).height(h);
						}
                    }
                }
            };
            _T.scroll = function () {
                return {
                    init: function (e, d, s) {
                        if (_T.isScrolling) return;
                        e = typeof e == 'object' ? e : $(e).get(0);
                        var p = e.style.left || _T.style.val(e, 'left');
                        e.style.left = p;
                        var l = d == 1 ? parseInt(e.offsetWidth) - parseInt(e.parentNode.offsetWidth) : 0;
                        e.si = setInterval(function () {
                            _T.scroll.mv(e, l, d, s)
                        }, 20)
                    },
                    mv: function (e, l, d, s) {
                        _T.isScrolling = true;
                        var c = parseInt(e.style.left);
                        if (c == l) {
                            _T.scroll.cl(e)
                        } else {
                            var i = Math.abs(l + c);
                            i = i < s ? i : s;
                            var n = c - i * d;
                            e.style.left = n + 'px'
                        }
                    },
                    cl: function (e) {
                        _T.isScrolling = false;
                        e = typeof e == 'object' ? e : $(e).get(0);
                        clearInterval(e.si)
                    }
                }
            }();
            _T.height = function () {
                return {
                    set: function (e, h, s, d) {
                        e = typeof e == 'object' ? e : $("#" + e).get(0);
                        var oh = e.offsetHeight,
                            ho = e.style.height || _T.style.val(e, 'height');
                        ho = oh - parseInt(ho);
                        var hd = oh - ho > h ? -1 : 1;
                        clearInterval(e.si);
                        e.si = setInterval(function () {
                            _T.height.tw(e, h, ho, hd, s)
                        }, 20)
                    },
                    tw: function (e, h, ho, hd, s) {
                        var oh = e.offsetHeight - ho;
                        if (!e) return;
                        if (oh == h) {
                            clearInterval(e.si)
                        } else if (oh != h) {
                            e.style.height = oh + (Math.ceil(Math.abs(h - oh) / s) * hd) + 'px'
                        }
                    }
                }
            }();
            _T.alpha = function () {
                return {
                    set: function (e, a, s, callback) {
                        e = typeof e == 'object' ? e : $("#" + e).get(0);
                        var o = e.style.opacity || _T.style.val(e, 'opacity'),
                            d = a > o * 100 ? 1 : -1;
                        e.style.opacity = o;
                        clearInterval(e.ai);
                        e.ai = setInterval(function () {
                            _T.alpha.tw(e, a, d, s, callback)
                        }, 20)
                    },
                    tw: function (e, a, d, s, callback) {
                        var o = Math.round(e.style.opacity * 100);
                        if (o == a) {
                            clearInterval(e.ai);
                            if (callback) callback()
                        } else {
                            var n = o + Math.ceil(Math.abs(a - o) / s) * d;
                            e.style.opacity = n / 100;
                            e.style.filter = 'alpha(opacity=' + n + ')'
                        }
                    }
                }
            }();
            _T.style = function () {
                return {
                    val: function (e, p) {
                        e = typeof e == 'object' ? e : $(e).get(0);
                        return e.currentStyle ? e.currentStyle[p] : document.defaultView.getComputedStyle(e, null).getPropertyValue(p)
                    }
                }
            }();
            $(wrapper).show();
            _T._s = new _T.slideshow(options);
            _T._s.init(_this, options);
            $(this).hide()
        }
    })
})(jQuery);