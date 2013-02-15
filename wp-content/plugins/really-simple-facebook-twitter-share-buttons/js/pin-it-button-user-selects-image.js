//Added function for firing off pinmarklet.js for "user selects image" button type

function execPinmarklet() {
	var e=document.createElement('script');
	e.setAttribute('type','text/javascript');
	e.setAttribute('charset','UTF-8');
	e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r=' + Math.random()*99999999);
	document.body.appendChild(e);
}


//Modified version of http://assets.pinterest.com/js/pinit.js
//Pinterest last updated 4/6/2012
//Points to local iFrame

(function (o, q, c) {
  var s = function (h) {
      var e = c.pinit,
        m = "?",
        a, i, f, b;
      f = [];
      b = [];
      var j = {},
        g = o.createElement("IFRAME"),
        r = h.getAttribute(c.att.count) || false,
        n = h.getAttribute(c.att.layout) || "horizontal";
      if (q.location.protocol === "https:") e = c.pinit_secure;
      f = h.href.split("?")[1].split("#")[0].split("&");
      a = 0;
      for (i = f.length; a < i; a += 1) {
        b = f[a].split("=");
        j[b[0]] = b[1]
      }
      a = f = 0;
      for (i = c.vars.req.length; a < i; a += 1) {
        b = c.vars.req[a];
        if (j[b]) {
          e = e + m + b + "=" + j[b];
          m = "&"
        }
        f += 1
      }
      if (j.media && j.media.indexOf("http") !== 0) f = 0;
      if (f === i) {
        a = 0;
        for (i = c.vars.opt.length; a < i; a += 1) {
          b = c.vars.opt[a];
          if (j[b]) e = e + m + b + "=" + j[b]
        }
        e = e + "&layout=" + n;
        if (r !== false) e += "&count=1";
        g.setAttribute("src", e);
        g.setAttribute("scrolling", "no");
        g.allowTransparency = true;
        g.frameBorder = 0;
        g.style.border = "none";
        g.style.width = c.layout[n].width + "px";
        g.style.height = c.layout[n].height + "px";
        h.parentNode.replaceChild(g, h)
      } else h.parentNode.removeChild(h)
    },
    p = o.getElementsByTagName("A"),
    l, d, k = [];
  d = 0;
  for (l = p.length; d < l; d += 1) k.push(p[d]);
  d = 0;
  for (l = k.length; d < l; d += 1) k[d].href && k[d].href.indexOf(c.button) !== -1 && s(k[d])
})

(document, window, {
  att: {
    layout: "count-layout",
    count: "always-show-count"
  },

  //pinit:"http://pinit-cdn.pinterest.com/pinit.html",
  //pinit_secure:"https://assets.pinterest.com/pinit.html",

  //Point to local iFrame
  pinit: iFrameBtnUrl,
  pinit_secure: iFrameBtnUrl,  
  
  button: "//pinterest.com/pin/create/button/?",
  vars: {
    req: ["url", "media"],
    opt: ["title", "description"]
  },
  layout: {
    none: {
      width: 43,
      height: 20
    },
    vertical: {
      width: 43,
      height: 58
    },
    horizontal: {
      width: 90,
      height: 20
    }
  }
});
