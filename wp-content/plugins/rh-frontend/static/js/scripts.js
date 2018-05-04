/*!
 * imagesLoaded v3.1.8
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */
(function(){function e(){}function t(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function n(e){return function(){return this[e].apply(this,arguments)}}var i=e.prototype,r=this,o=r.EventEmitter;i.getListeners=function(e){var t,n,i=this._getEvents();if("object"==typeof e){t={};for(n in i)i.hasOwnProperty(n)&&e.test(n)&&(t[n]=i[n])}else t=i[e]||(i[e]=[]);return t},i.flattenListeners=function(e){var t,n=[];for(t=0;e.length>t;t+=1)n.push(e[t].listener);return n},i.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},i.addListener=function(e,n){var i,r=this.getListenersAsObject(e),o="object"==typeof n;for(i in r)r.hasOwnProperty(i)&&-1===t(r[i],n)&&r[i].push(o?n:{listener:n,once:!1});return this},i.on=n("addListener"),i.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},i.once=n("addOnceListener"),i.defineEvent=function(e){return this.getListeners(e),this},i.defineEvents=function(e){for(var t=0;e.length>t;t+=1)this.defineEvent(e[t]);return this},i.removeListener=function(e,n){var i,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&(i=t(o[r],n),-1!==i&&o[r].splice(i,1));return this},i.off=n("removeListener"),i.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},i.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},i.manipulateListeners=function(e,t,n){var i,r,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(i=n.length;i--;)o.call(this,t,n[i]);else for(i in t)t.hasOwnProperty(i)&&(r=t[i])&&("function"==typeof r?o.call(this,i,r):s.call(this,i,r));return this},i.removeEvent=function(e){var t,n=typeof e,i=this._getEvents();if("string"===n)delete i[e];else if("object"===n)for(t in i)i.hasOwnProperty(t)&&e.test(t)&&delete i[t];else delete this._events;return this},i.removeAllListeners=n("removeEvent"),i.emitEvent=function(e,t){var n,i,r,o,s=this.getListenersAsObject(e);for(r in s)if(s.hasOwnProperty(r))for(i=s[r].length;i--;)n=s[r][i],n.once===!0&&this.removeListener(e,n.listener),o=n.listener.apply(this,t||[]),o===this._getOnceReturnValue()&&this.removeListener(e,n.listener);return this},i.trigger=n("emitEvent"),i.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},i.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},i._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},i._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return r.EventEmitter=o,e},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):"object"==typeof module&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function t(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var n=document.documentElement,i=function(){};n.addEventListener?i=function(e,t,n){e.addEventListener(t,n,!1)}:n.attachEvent&&(i=function(e,n,i){e[n+i]=i.handleEvent?function(){var n=t(e);i.handleEvent.call(i,n)}:function(){var n=t(e);i.call(e,n)},e.attachEvent("on"+n,e[n+i])});var r=function(){};n.removeEventListener?r=function(e,t,n){e.removeEventListener(t,n,!1)}:n.detachEvent&&(r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(i){e[t+n]=void 0}});var o={bind:i,unbind:r};"function"==typeof define&&define.amd?define("eventie/eventie",o):e.eventie=o}(this),function(e,t){"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,i){return t(e,n,i)}):"object"==typeof exports?module.exports=t(e,require("wolfy87-eventemitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(window,function(e,t,n){function i(e,t){for(var n in t)e[n]=t[n];return e}function r(e){return"[object Array]"===d.call(e)}function o(e){var t=[];if(r(e))t=e;else if("number"==typeof e.length)for(var n=0,i=e.length;i>n;n++)t.push(e[n]);else t.push(e);return t}function s(e,t,n){if(!(this instanceof s))return new s(e,t);"string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=i({},this.options),"function"==typeof t?n=t:i(this.options,t),n&&this.on("always",n),this.getImages(),a&&(this.jqDeferred=new a.Deferred);var r=this;setTimeout(function(){r.check()})}function f(e){this.img=e}function c(e){this.src=e,v[e]=this}var a=e.jQuery,u=e.console,h=u!==void 0,d=Object.prototype.toString;s.prototype=new t,s.prototype.options={},s.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;t>e;e++){var n=this.elements[e];"IMG"===n.nodeName&&this.addImage(n);var i=n.nodeType;if(i&&(1===i||9===i||11===i))for(var r=n.querySelectorAll("img"),o=0,s=r.length;s>o;o++){var f=r[o];this.addImage(f)}}},s.prototype.addImage=function(e){var t=new f(e);this.images.push(t)},s.prototype.check=function(){function e(e,r){return t.options.debug&&h&&u.log("confirm",e,r),t.progress(e),n++,n===i&&t.complete(),!0}var t=this,n=0,i=this.images.length;if(this.hasAnyBroken=!1,!i)return this.complete(),void 0;for(var r=0;i>r;r++){var o=this.images[r];o.on("confirm",e),o.check()}},s.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},s.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){if(t.emit(e,t),t.emit("always",t),t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},a&&(a.fn.imagesLoaded=function(e,t){var n=new s(this,e,t);return n.jqDeferred.promise(a(this))}),f.prototype=new t,f.prototype.check=function(){var e=v[this.img.src]||new c(this.img.src);if(e.isConfirmed)return this.confirm(e.isLoaded,"cached was confirmed"),void 0;if(this.img.complete&&void 0!==this.img.naturalWidth)return this.confirm(0!==this.img.naturalWidth,"naturalWidth"),void 0;var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},f.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var v={};return c.prototype=new t,c.prototype.check=function(){if(!this.isChecked){var e=new Image;n.bind(e,"load",this),n.bind(e,"error",this),e.src=this.src,this.isChecked=!0}},c.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},c.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},c.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},c.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},c.prototype.unbindProxyEvents=function(e){n.unbind(e.target,"load",this),n.unbind(e.target,"error",this)},s});

/*!
 * qTip2 v2.2.0 tips
 * http://qtip2.com
 * Licensed MIT, GPL
 */
!function(a,b,c){!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):jQuery&&!jQuery.fn.qtip&&a(jQuery)}(function(d){"use strict";function e(a,b,c,e){this.id=c,this.target=a,this.tooltip=E,this.elements={target:a},this._id=Q+"-"+c,this.timers={img:{}},this.options=b,this.plugins={},this.cache={event:{},target:d(),disabled:D,attr:e,onTooltip:D,lastClass:""},this.rendered=this.destroyed=this.disabled=this.waiting=this.hiddenDuringWait=this.positioning=this.triggering=D}function f(a){return a===E||"object"!==d.type(a)}function g(a){return!(d.isFunction(a)||a&&a.attr||a.length||"object"===d.type(a)&&(a.jquery||a.then))}function h(a){var b,c,e,h;return f(a)?D:(f(a.metadata)&&(a.metadata={type:a.metadata}),"content"in a&&(b=a.content,f(b)||b.jquery||b.done?b=a.content={text:c=g(b)?D:b}:c=b.text,"ajax"in b&&(e=b.ajax,h=e&&e.once!==D,delete b.ajax,b.text=function(a,b){var f=c||d(this).attr(b.options.content.attr)||"Loading...",g=d.ajax(d.extend({},e,{context:b})).then(e.success,E,e.error).then(function(a){return a&&h&&b.set("content.text",a),a},function(a,c,d){b.destroyed||0===a.status||b.set("content.text",c+": "+d)});return h?f:(b.set("content.text",f),g)}),"title"in b&&(f(b.title)||(b.button=b.title.button,b.title=b.title.text),g(b.title||D)&&(b.title=D))),"position"in a&&f(a.position)&&(a.position={my:a.position,at:a.position}),"show"in a&&f(a.show)&&(a.show=a.show.jquery?{target:a.show}:a.show===C?{ready:C}:{event:a.show}),"hide"in a&&f(a.hide)&&(a.hide=a.hide.jquery?{target:a.hide}:{event:a.hide}),"style"in a&&f(a.style)&&(a.style={classes:a.style}),d.each(P,function(){this.sanitize&&this.sanitize(a)}),a)}function i(a,b){for(var c,d=0,e=a,f=b.split(".");e=e[f[d++]];)d<f.length&&(c=e);return[c||a,f.pop()]}function j(a,b){var c,d,e;for(c in this.checks)for(d in this.checks[c])(e=new RegExp(d,"i").exec(a))&&(b.push(e),("builtin"===c||this.plugins[c])&&this.checks[c][d].apply(this.plugins[c]||this,b))}function k(a){return T.concat("").join(a?"-"+a+" ":" ")}function l(c){return c&&{type:c.type,pageX:c.pageX,pageY:c.pageY,target:c.target,relatedTarget:c.relatedTarget,scrollX:c.scrollX||a.pageXOffset||b.body.scrollLeft||b.documentElement.scrollLeft,scrollY:c.scrollY||a.pageYOffset||b.body.scrollTop||b.documentElement.scrollTop}||{}}function m(a,b){return b>0?setTimeout(d.proxy(a,this),b):(a.call(this),void 0)}function n(a){return this.tooltip.hasClass($)?D:(clearTimeout(this.timers.show),clearTimeout(this.timers.hide),this.timers.show=m.call(this,function(){this.toggle(C,a)},this.options.show.delay),void 0)}function o(a){if(this.tooltip.hasClass($))return D;var b=d(a.relatedTarget),c=b.closest(U)[0]===this.tooltip[0],e=b[0]===this.options.show.target[0];if(clearTimeout(this.timers.show),clearTimeout(this.timers.hide),this!==b[0]&&"mouse"===this.options.position.target&&c||this.options.hide.fixed&&/mouse(out|leave|move)/.test(a.type)&&(c||e))try{a.preventDefault(),a.stopImmediatePropagation()}catch(f){}else this.timers.hide=m.call(this,function(){this.toggle(D,a)},this.options.hide.delay,this)}function p(a){return this.tooltip.hasClass($)||!this.options.hide.inactive?D:(clearTimeout(this.timers.inactive),this.timers.inactive=m.call(this,function(){this.hide(a)},this.options.hide.inactive),void 0)}function q(a){this.rendered&&this.tooltip[0].offsetWidth>0&&this.reposition(a)}function r(a,c,e){d(b.body).delegate(a,(c.split?c:c.join(fb+" "))+fb,function(){var a=x.api[d.attr(this,S)];a&&!a.disabled&&e.apply(a,arguments)})}function s(a,c,f){var g,i,j,k,l,m=d(b.body),n=a[0]===b?m:a,o=a.metadata?a.metadata(f.metadata):E,p="html5"===f.metadata.type&&o?o[f.metadata.name]:E,q=a.data(f.metadata.name||"qtipopts");try{q="string"==typeof q?d.parseJSON(q):q}catch(r){}if(k=d.extend(C,{},x.defaults,f,"object"==typeof q?h(q):E,h(p||o)),i=k.position,k.id=c,"boolean"==typeof k.content.text){if(j=a.attr(k.content.attr),k.content.attr===D||!j)return D;k.content.text=j}if(i.container.length||(i.container=m),i.target===D&&(i.target=n),k.show.target===D&&(k.show.target=n),k.show.solo===C&&(k.show.solo=i.container.closest("body")),k.hide.target===D&&(k.hide.target=n),k.position.viewport===C&&(k.position.viewport=i.container),i.container=i.container.eq(0),i.at=new z(i.at,C),i.my=new z(i.my),a.data(Q))if(k.overwrite)a.qtip("destroy",!0);else if(k.overwrite===D)return D;return a.attr(R,c),k.suppress&&(l=a.attr("title"))&&a.removeAttr("title").attr(ab,l).attr("title",""),g=new e(a,k,c,!!j),a.data(Q,g),a.one("remove.qtip-"+c+" removeqtip.qtip-"+c,function(){var a;(a=d(this).data(Q))&&a.destroy(!0)}),g}function t(a){return a.charAt(0).toUpperCase()+a.slice(1)}function u(a,b){var d,e,f=b.charAt(0).toUpperCase()+b.slice(1),g=(b+" "+qb.join(f+" ")+f).split(" "),h=0;if(pb[b])return a.css(pb[b]);for(;d=g[h++];)if((e=a.css(d))!==c)return pb[b]=d,e}function v(a,b){return Math.ceil(parseFloat(u(a,b)))}function w(a,b){this._ns="tip",this.options=b,this.offset=b.offset,this.size=[b.width,b.height],this.init(this.qtip=a)}var x,y,z,A,B,C=!0,D=!1,E=null,F="x",G="y",H="width",I="height",J="top",K="left",L="bottom",M="right",N="center",O="shift",P={},Q="qtip",R="data-hasqtip",S="data-qtip-id",T=["ui-widget","ui-tooltip"],U="."+Q,V="click dblclick mousedown mouseup mousemove mouseleave mouseenter".split(" "),W=Q+"-fixed",X=Q+"-default",Y=Q+"-focus",Z=Q+"-hover",$=Q+"-disabled",_="_replacedByqTip",ab="oldtitle",bb={ie:function(){for(var a=3,c=b.createElement("div");(c.innerHTML="<!--[if gt IE "+ ++a+"]><i></i><![endif]-->")&&c.getElementsByTagName("i")[0];);return a>4?a:0/0}(),iOS:parseFloat((""+(/CPU.*OS ([0-9_]{1,5})|(CPU like).*AppleWebKit.*Mobile/i.exec(navigator.userAgent)||[0,""])[1]).replace("undefined","3_2").replace("_",".").replace("_",""))||D};y=e.prototype,y._when=function(a){return d.when.apply(d,a)},y.render=function(a){if(this.rendered||this.destroyed)return this;var b,c=this,e=this.options,f=this.cache,g=this.elements,h=e.content.text,i=e.content.title,j=e.content.button,k=e.position,l=("."+this._id+" ",[]);return d.attr(this.target[0],"aria-describedby",this._id),this.tooltip=g.tooltip=b=d("<div/>",{id:this._id,"class":[Q,X,e.style.classes,Q+"-pos-"+e.position.my.abbrev()].join(" "),width:e.style.width||"",height:e.style.height||"",tracking:"mouse"===k.target&&k.adjust.mouse,role:"alert","aria-live":"polite","aria-atomic":D,"aria-describedby":this._id+"-content","aria-hidden":C}).toggleClass($,this.disabled).attr(S,this.id).data(Q,this).appendTo(k.container).append(g.content=d("<div />",{"class":Q+"-content",id:this._id+"-content","aria-atomic":C})),this.rendered=-1,this.positioning=C,i&&(this._createTitle(),d.isFunction(i)||l.push(this._updateTitle(i,D))),j&&this._createButton(),d.isFunction(h)||l.push(this._updateContent(h,D)),this.rendered=C,this._setWidget(),d.each(P,function(a){var b;"render"===this.initialize&&(b=this(c))&&(c.plugins[a]=b)}),this._unassignEvents(),this._assignEvents(),this._when(l).then(function(){c._trigger("render"),c.positioning=D,c.hiddenDuringWait||!e.show.ready&&!a||c.toggle(C,f.event,D),c.hiddenDuringWait=D}),x.api[this.id]=this,this},y.destroy=function(a){function b(){if(!this.destroyed){this.destroyed=C;var a=this.target,b=a.attr(ab);this.rendered&&this.tooltip.stop(1,0).find("*").remove().end().remove(),d.each(this.plugins,function(){this.destroy&&this.destroy()}),clearTimeout(this.timers.show),clearTimeout(this.timers.hide),this._unassignEvents(),a.removeData(Q).removeAttr(S).removeAttr(R).removeAttr("aria-describedby"),this.options.suppress&&b&&a.attr("title",b).removeAttr(ab),this._unbind(a),this.options=this.elements=this.cache=this.timers=this.plugins=this.mouse=E,delete x.api[this.id]}}return this.destroyed?this.target:(a===C&&"hide"!==this.triggering||!this.rendered?b.call(this):(this.tooltip.one("tooltiphidden",d.proxy(b,this)),!this.triggering&&this.hide()),this.target)},A=y.checks={builtin:{"^id$":function(a,b,c,e){var f=c===C?x.nextid:c,g=Q+"-"+f;f!==D&&f.length>0&&!d("#"+g).length?(this._id=g,this.rendered&&(this.tooltip[0].id=this._id,this.elements.content[0].id=this._id+"-content",this.elements.title[0].id=this._id+"-title")):a[b]=e},"^prerender":function(a,b,c){c&&!this.rendered&&this.render(this.options.show.ready)},"^content.text$":function(a,b,c){this._updateContent(c)},"^content.attr$":function(a,b,c,d){this.options.content.text===this.target.attr(d)&&this._updateContent(this.target.attr(c))},"^content.title$":function(a,b,c){return c?(c&&!this.elements.title&&this._createTitle(),this._updateTitle(c),void 0):this._removeTitle()},"^content.button$":function(a,b,c){this._updateButton(c)},"^content.title.(text|button)$":function(a,b,c){this.set("content."+b,c)},"^position.(my|at)$":function(a,b,c){"string"==typeof c&&(a[b]=new z(c,"at"===b))},"^position.container$":function(a,b,c){this.rendered&&this.tooltip.appendTo(c)},"^show.ready$":function(a,b,c){c&&(!this.rendered&&this.render(C)||this.toggle(C))},"^style.classes$":function(a,b,c,d){this.rendered&&this.tooltip.removeClass(d).addClass(c)},"^style.(width|height)":function(a,b,c){this.rendered&&this.tooltip.css(b,c)},"^style.widget|content.title":function(){this.rendered&&this._setWidget()},"^style.def":function(a,b,c){this.rendered&&this.tooltip.toggleClass(X,!!c)},"^events.(render|show|move|hide|focus|blur)$":function(a,b,c){this.rendered&&this.tooltip[(d.isFunction(c)?"":"un")+"bind"]("tooltip"+b,c)},"^(show|hide|position).(event|target|fixed|inactive|leave|distance|viewport|adjust)":function(){if(this.rendered){var a=this.options.position;this.tooltip.attr("tracking","mouse"===a.target&&a.adjust.mouse),this._unassignEvents(),this._assignEvents()}}}},y.get=function(a){if(this.destroyed)return this;var b=i(this.options,a.toLowerCase()),c=b[0][b[1]];return c.precedance?c.string():c};var cb=/^position\.(my|at|adjust|target|container|viewport)|style|content|show\.ready/i,db=/^prerender|show\.ready/i;y.set=function(a,b){if(this.destroyed)return this;{var c,e=this.rendered,f=D,g=this.options;this.checks}return"string"==typeof a?(c=a,a={},a[c]=b):a=d.extend({},a),d.each(a,function(b,c){if(e&&db.test(b))return delete a[b],void 0;var h,j=i(g,b.toLowerCase());h=j[0][j[1]],j[0][j[1]]=c&&c.nodeType?d(c):c,f=cb.test(b)||f,a[b]=[j[0],j[1],c,h]}),h(g),this.positioning=C,d.each(a,d.proxy(j,this)),this.positioning=D,this.rendered&&this.tooltip[0].offsetWidth>0&&f&&this.reposition("mouse"===g.position.target?E:this.cache.event),this},y._update=function(a,b){var c=this,e=this.cache;return this.rendered&&a?(d.isFunction(a)&&(a=a.call(this.elements.target,e.event,this)||""),d.isFunction(a.then)?(e.waiting=C,a.then(function(a){return e.waiting=D,c._update(a,b)},E,function(a){return c._update(a,b)})):a===D||!a&&""!==a?D:(a.jquery&&a.length>0?b.empty().append(a.css({display:"block",visibility:"visible"})):b.html(a),this._waitForContent(b).then(function(a){a.images&&a.images.length&&c.rendered&&c.tooltip[0].offsetWidth>0&&c.reposition(e.event,!a.length)}))):D},y._waitForContent=function(a){var b=this.cache;return b.waiting=C,(d.fn.imagesLoaded?a.imagesLoaded():d.Deferred().resolve([])).done(function(){b.waiting=D}).promise()},y._updateContent=function(a,b){this._update(a,this.elements.content,b)},y._updateTitle=function(a,b){this._update(a,this.elements.title,b)===D&&this._removeTitle(D)},y._createTitle=function(){var a=this.elements,b=this._id+"-title";a.titlebar&&this._removeTitle(),a.titlebar=d("<div />",{"class":Q+"-titlebar "+(this.options.style.widget?k("header"):"")}).append(a.title=d("<div />",{id:b,"class":Q+"-title","aria-atomic":C})).insertBefore(a.content).delegate(".qtip-close","mousedown keydown mouseup keyup mouseout",function(a){d(this).toggleClass("ui-state-active ui-state-focus","down"===a.type.substr(-4))}).delegate(".qtip-close","mouseover mouseout",function(a){d(this).toggleClass("ui-state-hover","mouseover"===a.type)}),this.options.content.button&&this._createButton()},y._removeTitle=function(a){var b=this.elements;b.title&&(b.titlebar.remove(),b.titlebar=b.title=b.button=E,a!==D&&this.reposition())},y.reposition=function(c,e){if(!this.rendered||this.positioning||this.destroyed)return this;this.positioning=C;var f,g,h=this.cache,i=this.tooltip,j=this.options.position,k=j.target,l=j.my,m=j.at,n=j.viewport,o=j.container,p=j.adjust,q=p.method.split(" "),r=i.outerWidth(D),s=i.outerHeight(D),t=0,u=0,v=i.css("position"),w={left:0,top:0},x=i[0].offsetWidth>0,y=c&&"scroll"===c.type,z=d(a),A=o[0].ownerDocument,B=this.mouse;if(d.isArray(k)&&2===k.length)m={x:K,y:J},w={left:k[0],top:k[1]};else if("mouse"===k)m={x:K,y:J},!B||!B.pageX||!p.mouse&&c&&c.pageX?c&&c.pageX||((!p.mouse||this.options.show.distance)&&h.origin&&h.origin.pageX?c=h.origin:(!c||c&&("resize"===c.type||"scroll"===c.type))&&(c=h.event)):c=B,"static"!==v&&(w=o.offset()),A.body.offsetWidth!==(a.innerWidth||A.documentElement.clientWidth)&&(g=d(b.body).offset()),w={left:c.pageX-w.left+(g&&g.left||0),top:c.pageY-w.top+(g&&g.top||0)},p.mouse&&y&&B&&(w.left-=(B.scrollX||0)-z.scrollLeft(),w.top-=(B.scrollY||0)-z.scrollTop());else{if("event"===k?c&&c.target&&"scroll"!==c.type&&"resize"!==c.type?h.target=d(c.target):c.target||(h.target=this.elements.target):"event"!==k&&(h.target=d(k.jquery?k:this.elements.target)),k=h.target,k=d(k).eq(0),0===k.length)return this;k[0]===b||k[0]===a?(t=bb.iOS?a.innerWidth:k.width(),u=bb.iOS?a.innerHeight:k.height(),k[0]===a&&(w={top:(n||k).scrollTop(),left:(n||k).scrollLeft()})):P.imagemap&&k.is("area")?f=P.imagemap(this,k,m,P.viewport?q:D):P.svg&&k&&k[0].ownerSVGElement?f=P.svg(this,k,m,P.viewport?q:D):(t=k.outerWidth(D),u=k.outerHeight(D),w=k.offset()),f&&(t=f.width,u=f.height,g=f.offset,w=f.position),w=this.reposition.offset(k,w,o),(bb.iOS>3.1&&bb.iOS<4.1||bb.iOS>=4.3&&bb.iOS<4.33||!bb.iOS&&"fixed"===v)&&(w.left-=z.scrollLeft(),w.top-=z.scrollTop()),(!f||f&&f.adjustable!==D)&&(w.left+=m.x===M?t:m.x===N?t/2:0,w.top+=m.y===L?u:m.y===N?u/2:0)}return w.left+=p.x+(l.x===M?-r:l.x===N?-r/2:0),w.top+=p.y+(l.y===L?-s:l.y===N?-s/2:0),P.viewport?(w.adjusted=P.viewport(this,w,j,t,u,r,s),g&&w.adjusted.left&&(w.left+=g.left),g&&w.adjusted.top&&(w.top+=g.top)):w.adjusted={left:0,top:0},this._trigger("move",[w,n.elem||n],c)?(delete w.adjusted,e===D||!x||isNaN(w.left)||isNaN(w.top)||"mouse"===k||!d.isFunction(j.effect)?i.css(w):d.isFunction(j.effect)&&(j.effect.call(i,this,d.extend({},w)),i.queue(function(a){d(this).css({opacity:"",height:""}),bb.ie&&this.style.removeAttribute("filter"),a()})),this.positioning=D,this):this},y.reposition.offset=function(a,c,e){function f(a,b){c.left+=b*a.scrollLeft(),c.top+=b*a.scrollTop()}if(!e[0])return c;var g,h,i,j,k=d(a[0].ownerDocument),l=!!bb.ie&&"CSS1Compat"!==b.compatMode,m=e[0];do"static"!==(h=d.css(m,"position"))&&("fixed"===h?(i=m.getBoundingClientRect(),f(k,-1)):(i=d(m).position(),i.left+=parseFloat(d.css(m,"borderLeftWidth"))||0,i.top+=parseFloat(d.css(m,"borderTopWidth"))||0),c.left-=i.left+(parseFloat(d.css(m,"marginLeft"))||0),c.top-=i.top+(parseFloat(d.css(m,"marginTop"))||0),g||"hidden"===(j=d.css(m,"overflow"))||"visible"===j||(g=d(m)));while(m=m.offsetParent);return g&&(g[0]!==k[0]||l)&&f(g,1),c};var eb=(z=y.reposition.Corner=function(a,b){a=(""+a).replace(/([A-Z])/," $1").replace(/middle/gi,N).toLowerCase(),this.x=(a.match(/left|right/i)||a.match(/center/)||["inherit"])[0].toLowerCase(),this.y=(a.match(/top|bottom|center/i)||["inherit"])[0].toLowerCase(),this.forceY=!!b;var c=a.charAt(0);this.precedance="t"===c||"b"===c?G:F}).prototype;eb.invert=function(a,b){this[a]=this[a]===K?M:this[a]===M?K:b||this[a]},eb.string=function(){var a=this.x,b=this.y;return a===b?a:this.precedance===G||this.forceY&&"center"!==b?b+" "+a:a+" "+b},eb.abbrev=function(){var a=this.string().split(" ");return a[0].charAt(0)+(a[1]&&a[1].charAt(0)||"")},eb.clone=function(){return new z(this.string(),this.forceY)},y.toggle=function(a,c){var e=this.cache,f=this.options,g=this.tooltip;if(c){if(/over|enter/.test(c.type)&&/out|leave/.test(e.event.type)&&f.show.target.add(c.target).length===f.show.target.length&&g.has(c.relatedTarget).length)return this;e.event=l(c)}if(this.waiting&&!a&&(this.hiddenDuringWait=C),!this.rendered)return a?this.render(1):this;if(this.destroyed||this.disabled)return this;var h,i,j,k=a?"show":"hide",m=this.options[k],n=(this.options[a?"hide":"show"],this.options.position),o=this.options.content,p=this.tooltip.css("width"),q=this.tooltip.is(":visible"),r=a||1===m.target.length,s=!c||m.target.length<2||e.target[0]===c.target;return(typeof a).search("boolean|number")&&(a=!q),h=!g.is(":animated")&&q===a&&s,i=h?E:!!this._trigger(k,[90]),this.destroyed?this:(i!==D&&a&&this.focus(c),!i||h?this:(d.attr(g[0],"aria-hidden",!a),a?(e.origin=l(this.mouse),d.isFunction(o.text)&&this._updateContent(o.text,D),d.isFunction(o.title)&&this._updateTitle(o.title,D),!B&&"mouse"===n.target&&n.adjust.mouse&&(d(b).bind("mousemove."+Q,this._storeMouse),B=C),p||g.css("width",g.outerWidth(D)),this.reposition(c,arguments[2]),p||g.css("width",""),m.solo&&("string"==typeof m.solo?d(m.solo):d(U,m.solo)).not(g).not(m.target).qtip("hide",d.Event("tooltipsolo"))):(clearTimeout(this.timers.show),delete e.origin,B&&!d(U+'[tracking="true"]:visible',m.solo).not(g).length&&(d(b).unbind("mousemove."+Q),B=D),this.blur(c)),j=d.proxy(function(){a?(bb.ie&&g[0].style.removeAttribute("filter"),g.css("overflow",""),"string"==typeof m.autofocus&&d(this.options.show.autofocus,g).focus(),this.options.show.target.trigger("qtip-"+this.id+"-inactive")):g.css({display:"",visibility:"",opacity:"",left:"",top:""}),this._trigger(a?"visible":"hidden")},this),m.effect===D||r===D?(g[k](),j()):d.isFunction(m.effect)?(g.stop(1,1),m.effect.call(g,this),g.queue("fx",function(a){j(),a()})):g.fadeTo(90,a?1:0,j),a&&m.target.trigger("qtip-"+this.id+"-inactive"),this))},y.show=function(a){return this.toggle(C,a)},y.hide=function(a){return this.toggle(D,a)},y.focus=function(a){if(!this.rendered||this.destroyed)return this;var b=d(U),c=this.tooltip,e=parseInt(c[0].style.zIndex,10),f=x.zindex+b.length;return c.hasClass(Y)||this._trigger("focus",[f],a)&&(e!==f&&(b.each(function(){this.style.zIndex>e&&(this.style.zIndex=this.style.zIndex-1)}),b.filter("."+Y).qtip("blur",a)),c.addClass(Y)[0].style.zIndex=f),this},y.blur=function(a){return!this.rendered||this.destroyed?this:(this.tooltip.removeClass(Y),this._trigger("blur",[this.tooltip.css("zIndex")],a),this)},y.disable=function(a){return this.destroyed?this:("toggle"===a?a=!(this.rendered?this.tooltip.hasClass($):this.disabled):"boolean"!=typeof a&&(a=C),this.rendered&&this.tooltip.toggleClass($,a).attr("aria-disabled",a),this.disabled=!!a,this)},y.enable=function(){return this.disable(D)},y._createButton=function(){var a=this,b=this.elements,c=b.tooltip,e=this.options.content.button,f="string"==typeof e,g=f?e:"Close tooltip";b.button&&b.button.remove(),b.button=e.jquery?e:d("<a />",{"class":"qtip-close "+(this.options.style.widget?"":Q+"-icon"),title:g,"aria-label":g}).prepend(d("<span />",{"class":"ui-icon ui-icon-close",html:"&times;"})),b.button.appendTo(b.titlebar||c).attr("role","button").click(function(b){return c.hasClass($)||a.hide(b),D})},y._updateButton=function(a){if(!this.rendered)return D;var b=this.elements.button;a?this._createButton():b.remove()},y._setWidget=function(){var a=this.options.style.widget,b=this.elements,c=b.tooltip,d=c.hasClass($);c.removeClass($),$=a?"ui-state-disabled":"qtip-disabled",c.toggleClass($,d),c.toggleClass("ui-helper-reset "+k(),a).toggleClass(X,this.options.style.def&&!a),b.content&&b.content.toggleClass(k("content"),a),b.titlebar&&b.titlebar.toggleClass(k("header"),a),b.button&&b.button.toggleClass(Q+"-icon",!a)},y._storeMouse=function(a){(this.mouse=l(a)).type="mousemove"},y._bind=function(a,b,c,e,f){var g="."+this._id+(e?"-"+e:"");b.length&&d(a).bind((b.split?b:b.join(g+" "))+g,d.proxy(c,f||this))},y._unbind=function(a,b){d(a).unbind("."+this._id+(b?"-"+b:""))};var fb="."+Q;d(function(){r(U,["mouseenter","mouseleave"],function(a){var b="mouseenter"===a.type,c=d(a.currentTarget),e=d(a.relatedTarget||a.target),f=this.options;b?(this.focus(a),c.hasClass(W)&&!c.hasClass($)&&clearTimeout(this.timers.hide)):"mouse"===f.position.target&&f.hide.event&&f.show.target&&!e.closest(f.show.target[0]).length&&this.hide(a),c.toggleClass(Z,b)}),r("["+S+"]",V,p)}),y._trigger=function(a,b,c){var e=d.Event("tooltip"+a);return e.originalEvent=c&&d.extend({},c)||this.cache.event||E,this.triggering=a,this.tooltip.trigger(e,[this].concat(b||[])),this.triggering=D,!e.isDefaultPrevented()},y._bindEvents=function(a,b,c,e,f,g){if(e.add(c).length===e.length){var h=[];b=d.map(b,function(b){var c=d.inArray(b,a);return c>-1?(h.push(a.splice(c,1)[0]),void 0):b}),h.length&&this._bind(c,h,function(a){var b=this.rendered?this.tooltip[0].offsetWidth>0:!1;(b?g:f).call(this,a)})}this._bind(c,a,f),this._bind(e,b,g)},y._assignInitialEvents=function(a){function b(a){return this.disabled||this.destroyed?D:(this.cache.event=l(a),this.cache.target=a?d(a.target):[c],clearTimeout(this.timers.show),this.timers.show=m.call(this,function(){this.render("object"==typeof a||e.show.ready)},e.show.delay),void 0)}var e=this.options,f=e.show.target,g=e.hide.target,h=e.show.event?d.trim(""+e.show.event).split(" "):[],i=e.hide.event?d.trim(""+e.hide.event).split(" "):[];/mouse(over|enter)/i.test(e.show.event)&&!/mouse(out|leave)/i.test(e.hide.event)&&i.push("mouseleave"),this._bind(f,"mousemove",function(a){this._storeMouse(a),this.cache.onTarget=C}),this._bindEvents(h,i,f,g,b,function(){clearTimeout(this.timers.show)}),(e.show.ready||e.prerender)&&b.call(this,a)},y._assignEvents=function(){var c=this,e=this.options,f=e.position,g=this.tooltip,h=e.show.target,i=e.hide.target,j=f.container,k=f.viewport,l=d(b),m=(d(b.body),d(a)),r=e.show.event?d.trim(""+e.show.event).split(" "):[],s=e.hide.event?d.trim(""+e.hide.event).split(" "):[];d.each(e.events,function(a,b){c._bind(g,"toggle"===a?["tooltipshow","tooltiphide"]:["tooltip"+a],b,null,g)}),/mouse(out|leave)/i.test(e.hide.event)&&"window"===e.hide.leave&&this._bind(l,["mouseout","blur"],function(a){/select|option/.test(a.target.nodeName)||a.relatedTarget||this.hide(a)}),e.hide.fixed?i=i.add(g.addClass(W)):/mouse(over|enter)/i.test(e.show.event)&&this._bind(i,"mouseleave",function(){clearTimeout(this.timers.show)}),(""+e.hide.event).indexOf("unfocus")>-1&&this._bind(j.closest("html"),["mousedown","touchstart"],function(a){var b=d(a.target),c=this.rendered&&!this.tooltip.hasClass($)&&this.tooltip[0].offsetWidth>0,e=b.parents(U).filter(this.tooltip[0]).length>0;b[0]===this.target[0]||b[0]===this.tooltip[0]||e||this.target.has(b[0]).length||!c||this.hide(a)}),"number"==typeof e.hide.inactive&&(this._bind(h,"qtip-"+this.id+"-inactive",p),this._bind(i.add(g),x.inactiveEvents,p,"-inactive")),this._bindEvents(r,s,h,i,n,o),this._bind(h.add(g),"mousemove",function(a){if("number"==typeof e.hide.distance){var b=this.cache.origin||{},c=this.options.hide.distance,d=Math.abs;(d(a.pageX-b.pageX)>=c||d(a.pageY-b.pageY)>=c)&&this.hide(a)}this._storeMouse(a)}),"mouse"===f.target&&f.adjust.mouse&&(e.hide.event&&this._bind(h,["mouseenter","mouseleave"],function(a){this.cache.onTarget="mouseenter"===a.type}),this._bind(l,"mousemove",function(a){this.rendered&&this.cache.onTarget&&!this.tooltip.hasClass($)&&this.tooltip[0].offsetWidth>0&&this.reposition(a)})),(f.adjust.resize||k.length)&&this._bind(d.event.special.resize?k:m,"resize",q),f.adjust.scroll&&this._bind(m.add(f.container),"scroll",q)},y._unassignEvents=function(){var c=[this.options.show.target[0],this.options.hide.target[0],this.rendered&&this.tooltip[0],this.options.position.container[0],this.options.position.viewport[0],this.options.position.container.closest("html")[0],a,b];this._unbind(d([]).pushStack(d.grep(c,function(a){return"object"==typeof a})))},x=d.fn.qtip=function(a,b,e){var f=(""+a).toLowerCase(),g=E,i=d.makeArray(arguments).slice(1),j=i[i.length-1],k=this[0]?d.data(this[0],Q):E;return!arguments.length&&k||"api"===f?k:"string"==typeof a?(this.each(function(){var a=d.data(this,Q);if(!a)return C;if(j&&j.timeStamp&&(a.cache.event=j),!b||"option"!==f&&"options"!==f)a[f]&&a[f].apply(a,i);else{if(e===c&&!d.isPlainObject(b))return g=a.get(b),D;a.set(b,e)}}),g!==E?g:this):"object"!=typeof a&&arguments.length?void 0:(k=h(d.extend(C,{},a)),this.each(function(a){var b,c;return c=d.isArray(k.id)?k.id[a]:k.id,c=!c||c===D||c.length<1||x.api[c]?x.nextid++:c,b=s(d(this),c,k),b===D?C:(x.api[c]=b,d.each(P,function(){"initialize"===this.initialize&&this(b)}),b._assignInitialEvents(j),void 0)}))},d.qtip=e,x.api={},d.each({attr:function(a,b){if(this.length){var c=this[0],e="title",f=d.data(c,"qtip");if(a===e&&f&&"object"==typeof f&&f.options.suppress)return arguments.length<2?d.attr(c,ab):(f&&f.options.content.attr===e&&f.cache.attr&&f.set("content.text",b),this.attr(ab,b))}return d.fn["attr"+_].apply(this,arguments)},clone:function(a){var b=(d([]),d.fn["clone"+_].apply(this,arguments));return a||b.filter("["+ab+"]").attr("title",function(){return d.attr(this,ab)}).removeAttr(ab),b}},function(a,b){if(!b||d.fn[a+_])return C;var c=d.fn[a+_]=d.fn[a];d.fn[a]=function(){return b.apply(this,arguments)||c.apply(this,arguments)}}),d.ui||(d["cleanData"+_]=d.cleanData,d.cleanData=function(a){for(var b,c=0;(b=d(a[c])).length;c++)if(b.attr(R))try{b.triggerHandler("removeqtip")}catch(e){}d["cleanData"+_].apply(this,arguments)}),x.version="2.2.0",x.nextid=0,x.inactiveEvents=V,x.zindex=15e3,x.defaults={prerender:D,id:D,overwrite:C,suppress:C,content:{text:C,attr:"title",title:D,button:D},position:{my:"top left",at:"bottom right",target:D,container:D,viewport:D,adjust:{x:0,y:0,mouse:C,scroll:C,resize:C,method:"flipinvert flipinvert"},effect:function(a,b){d(this).animate(b,{duration:200,queue:D})}},show:{target:D,event:"mouseenter",effect:C,delay:90,solo:D,ready:D,autofocus:D},hide:{target:D,event:"mouseleave",effect:C,delay:0,fixed:D,inactive:D,leave:"window",distance:D},style:{classes:"",widget:D,width:D,height:D,def:C},events:{render:E,move:E,show:E,hide:E,toggle:E,visible:E,hidden:E,focus:E,blur:E}};var gb,hb="margin",ib="border",jb="color",kb="background-color",lb="transparent",mb=" !important",nb=!!b.createElement("canvas").getContext,ob=/rgba?\(0, 0, 0(, 0)?\)|transparent|#123456/i,pb={},qb=["Webkit","O","Moz","ms"];if(nb)var rb=a.devicePixelRatio||1,sb=function(){var a=b.createElement("canvas").getContext("2d");return a.backingStorePixelRatio||a.webkitBackingStorePixelRatio||a.mozBackingStorePixelRatio||a.msBackingStorePixelRatio||a.oBackingStorePixelRatio||1}(),tb=rb/sb;else var ub=function(a,b,c){return"<qtipvml:"+a+' xmlns="urn:schemas-microsoft.com:vml" class="qtip-vml" '+(b||"")+' style="behavior: url(#default#VML); '+(c||"")+'" />'};d.extend(w.prototype,{init:function(a){var b,c;c=this.element=a.elements.tip=d("<div />",{"class":Q+"-tip"}).prependTo(a.tooltip),nb?(b=d("<canvas />").appendTo(this.element)[0].getContext("2d"),b.lineJoin="miter",b.miterLimit=1e5,b.save()):(b=ub("shape",'coordorigin="0,0"',"position:absolute;"),this.element.html(b+b),a._bind(d("*",c).add(c),["click","mousedown"],function(a){a.stopPropagation()},this._ns)),a._bind(a.tooltip,"tooltipmove",this.reposition,this._ns,this),this.create()},_swapDimensions:function(){this.size[0]=this.options.height,this.size[1]=this.options.width},_resetDimensions:function(){this.size[0]=this.options.width,this.size[1]=this.options.height},_useTitle:function(a){var b=this.qtip.elements.titlebar;return b&&(a.y===J||a.y===N&&this.element.position().top+this.size[1]/2+this.options.offset<b.outerHeight(C))},_parseCorner:function(a){var b=this.qtip.options.position.my;return a===D||b===D?a=D:a===C?a=new z(b.string()):a.string||(a=new z(a),a.fixed=C),a},_parseWidth:function(a,b,c){var d=this.qtip.elements,e=ib+t(b)+"Width";return(c?v(c,e):v(d.content,e)||v(this._useTitle(a)&&d.titlebar||d.content,e)||v(d.tooltip,e))||0},_parseRadius:function(a){var b=this.qtip.elements,c=ib+t(a.y)+t(a.x)+"Radius";return bb.ie<9?0:v(this._useTitle(a)&&b.titlebar||b.content,c)||v(b.tooltip,c)||0},_invalidColour:function(a,b,c){var d=a.css(b);return!d||c&&d===a.css(c)||ob.test(d)?D:d},_parseColours:function(a){var b=this.qtip.elements,c=this.element.css("cssText",""),e=ib+t(a[a.precedance])+t(jb),f=this._useTitle(a)&&b.titlebar||b.content,g=this._invalidColour,h=[];return h[0]=g(c,kb)||g(f,kb)||g(b.content,kb)||g(b.tooltip,kb)||c.css(kb),h[1]=g(c,e,jb)||g(f,e,jb)||g(b.content,e,jb)||g(b.tooltip,e,jb)||b.tooltip.css(e),d("*",c).add(c).css("cssText",kb+":"+lb+mb+";"+ib+":0"+mb+";"),h},_calculateSize:function(a){var b,c,d,e=a.precedance===G,f=this.options.width,g=this.options.height,h="c"===a.abbrev(),i=(e?f:g)*(h?.5:1),j=Math.pow,k=Math.round,l=Math.sqrt(j(i,2)+j(g,2)),m=[this.border/i*l,this.border/g*l];return m[2]=Math.sqrt(j(m[0],2)-j(this.border,2)),m[3]=Math.sqrt(j(m[1],2)-j(this.border,2)),b=l+m[2]+m[3]+(h?0:m[0]),c=b/l,d=[k(c*f),k(c*g)],e?d:d.reverse()},_calculateTip:function(a,b,c){c=c||1,b=b||this.size;var d=b[0]*c,e=b[1]*c,f=Math.ceil(d/2),g=Math.ceil(e/2),h={br:[0,0,d,e,d,0],bl:[0,0,d,0,0,e],tr:[0,e,d,0,d,e],tl:[0,0,0,e,d,e],tc:[0,e,f,0,d,e],bc:[0,0,d,0,f,e],rc:[0,0,d,g,0,e],lc:[d,0,d,e,0,g]};return h.lt=h.br,h.rt=h.bl,h.lb=h.tr,h.rb=h.tl,h[a.abbrev()]},_drawCoords:function(a,b){a.beginPath(),a.moveTo(b[0],b[1]),a.lineTo(b[2],b[3]),a.lineTo(b[4],b[5]),a.closePath()},create:function(){var a=this.corner=(nb||bb.ie)&&this._parseCorner(this.options.corner);return(this.enabled=!!this.corner&&"c"!==this.corner.abbrev())&&(this.qtip.cache.corner=a.clone(),this.update()),this.element.toggle(this.enabled),this.corner},update:function(b,c){if(!this.enabled)return this;var e,f,g,h,i,j,k,l,m=this.qtip.elements,n=this.element,o=n.children(),p=this.options,q=this.size,r=p.mimic,s=Math.round;b||(b=this.qtip.cache.corner||this.corner),r===D?r=b:(r=new z(r),r.precedance=b.precedance,"inherit"===r.x?r.x=b.x:"inherit"===r.y?r.y=b.y:r.x===r.y&&(r[b.precedance]=b[b.precedance])),f=r.precedance,b.precedance===F?this._swapDimensions():this._resetDimensions(),e=this.color=this._parseColours(b),e[1]!==lb?(l=this.border=this._parseWidth(b,b[b.precedance]),p.border&&1>l&&!ob.test(e[1])&&(e[0]=e[1]),this.border=l=p.border!==C?p.border:l):this.border=l=0,k=this.size=this._calculateSize(b),n.css({width:k[0],height:k[1],lineHeight:k[1]+"px"}),j=b.precedance===G?[s(r.x===K?l:r.x===M?k[0]-q[0]-l:(k[0]-q[0])/2),s(r.y===J?k[1]-q[1]:0)]:[s(r.x===K?k[0]-q[0]:0),s(r.y===J?l:r.y===L?k[1]-q[1]-l:(k[1]-q[1])/2)],nb?(g=o[0].getContext("2d"),g.restore(),g.save(),g.clearRect(0,0,6e3,6e3),h=this._calculateTip(r,q,tb),i=this._calculateTip(r,this.size,tb),o.attr(H,k[0]*tb).attr(I,k[1]*tb),o.css(H,k[0]).css(I,k[1]),this._drawCoords(g,i),g.fillStyle=e[1],g.fill(),g.translate(j[0]*tb,j[1]*tb),this._drawCoords(g,h),g.fillStyle=e[0],g.fill()):(h=this._calculateTip(r),h="m"+h[0]+","+h[1]+" l"+h[2]+","+h[3]+" "+h[4]+","+h[5]+" xe",j[2]=l&&/^(r|b)/i.test(b.string())?8===bb.ie?2:1:0,o.css({coordsize:k[0]+l+" "+(k[1]+l),antialias:""+(r.string().indexOf(N)>-1),left:j[0]-j[2]*Number(f===F),top:j[1]-j[2]*Number(f===G),width:k[0]+l,height:k[1]+l}).each(function(a){var b=d(this);b[b.prop?"prop":"attr"]({coordsize:k[0]+l+" "+(k[1]+l),path:h,fillcolor:e[0],filled:!!a,stroked:!a}).toggle(!(!l&&!a)),!a&&b.html(ub("stroke",'weight="'+2*l+'px" color="'+e[1]+'" miterlimit="1000" joinstyle="miter"'))})),a.opera&&setTimeout(function(){m.tip.css({display:"inline-block",visibility:"visible"})},1),c!==D&&this.calculate(b,k)},calculate:function(a,b){if(!this.enabled)return D;var c,e,f=this,g=this.qtip.elements,h=this.element,i=this.options.offset,j=(g.tooltip.hasClass("ui-widget"),{});return a=a||this.corner,c=a.precedance,b=b||this._calculateSize(a),e=[a.x,a.y],c===F&&e.reverse(),d.each(e,function(d,e){var h,k,l;e===N?(h=c===G?K:J,j[h]="50%",j[hb+"-"+h]=-Math.round(b[c===G?0:1]/2)+i):(h=f._parseWidth(a,e,g.tooltip),k=f._parseWidth(a,e,g.content),l=f._parseRadius(a),j[e]=Math.max(-f.border,d?k:i+(l>h?l:-h)))}),j[a[c]]-=b[c===F?0:1],h.css({margin:"",top:"",bottom:"",left:"",right:""}).css(j),j
},reposition:function(a,b,d){function e(a,b,c,d,e){a===O&&j.precedance===b&&k[d]&&j[c]!==N?j.precedance=j.precedance===F?G:F:a!==O&&k[d]&&(j[b]=j[b]===N?k[d]>0?d:e:j[b]===d?e:d)}function f(a,b,e){j[a]===N?p[hb+"-"+b]=o[a]=g[hb+"-"+b]-k[b]:(h=g[e]!==c?[k[b],-g[b]]:[-k[b],g[b]],(o[a]=Math.max(h[0],h[1]))>h[0]&&(d[b]-=k[b],o[b]=D),p[g[e]!==c?e:b]=o[a])}if(this.enabled){var g,h,i=b.cache,j=this.corner.clone(),k=d.adjusted,l=b.options.position.adjust.method.split(" "),m=l[0],n=l[1]||l[0],o={left:D,top:D,x:0,y:0},p={};this.corner.fixed!==C&&(e(m,F,G,K,M),e(n,G,F,J,L),j.string()===i.corner.string()||i.cornerTop===k.top&&i.cornerLeft===k.left||this.update(j,D)),g=this.calculate(j),g.right!==c&&(g.left=-g.right),g.bottom!==c&&(g.top=-g.bottom),g.user=this.offset,(o.left=m===O&&!!k.left)&&f(F,K,M),(o.top=n===O&&!!k.top)&&f(G,J,L),this.element.css(p).toggle(!(o.x&&o.y||j.x===N&&o.y||j.y===N&&o.x)),d.left-=g.left.charAt?g.user:m!==O||o.top||!o.left&&!o.top?g.left+this.border:0,d.top-=g.top.charAt?g.user:n!==O||o.left||!o.left&&!o.top?g.top+this.border:0,i.cornerLeft=k.left,i.cornerTop=k.top,i.corner=j.clone()}},destroy:function(){this.qtip._unbind(this.qtip.tooltip,this._ns),this.qtip.elements.tip&&this.qtip.elements.tip.find("*").remove().end().remove()}}),gb=P.tip=function(a){return new w(a,a.options.style.tip)},gb.initialize="render",gb.sanitize=function(a){if(a.style&&"tip"in a.style){var b=a.style.tip;"object"!=typeof b&&(b=a.style.tip={corner:b}),/string|boolean/i.test(typeof b.corner)||(b.corner=C)}},A.tip={"^position.my|style.tip.(corner|mimic|border)$":function(){this.create(),this.qtip.reposition()},"^style.tip.(height|width)$":function(a){this.size=[a.width,a.height],this.update(),this.qtip.reposition()},"^content.title|style.(classes|widget)$":function(){this.update()}},d.extend(C,x.defaults,{style:{tip:{corner:C,mimic:D,width:6,height:6,border:C,offset:0}}})})}(window,document);

/*!
 * jQuery Validation Plugin
 * http://jqueryvalidation.org/
 * Copyright (c) 2014 Jörn Zaefferer; Licensed MIT
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(jQuery)}(function(a){a.extend(a.fn,{validate:function(b){if(!this.length)return void(b&&b.debug&&window.console&&console.warn("Nothing selected, can't validate, returning nothing."));var c=a.data(this[0],"validator");return c?c:(this.attr("novalidate","novalidate"),c=new a.validator(b,this[0]),a.data(this[0],"validator",c),c.settings.onsubmit&&(this.validateDelegate(":submit","click",function(b){c.settings.submitHandler&&(c.submitButton=b.target),a(b.target).hasClass("cancel")&&(c.cancelSubmit=!0),void 0!==a(b.target).attr("formnovalidate")&&(c.cancelSubmit=!0)}),this.submit(function(b){function d(){var d;return c.settings.submitHandler?(c.submitButton&&(d=a("<input type='hidden'/>").attr("name",c.submitButton.name).val(a(c.submitButton).val()).appendTo(c.currentForm)),c.settings.submitHandler.call(c,c.currentForm,b),c.submitButton&&d.remove(),!1):!0}return c.settings.debug&&b.preventDefault(),c.cancelSubmit?(c.cancelSubmit=!1,d()):c.form()?c.pendingRequest?(c.formSubmitted=!0,!1):d():(c.focusInvalid(),!1)})),c)},valid:function(){var b,c;return a(this[0]).is("form")?b=this.validate().form():(b=!0,c=a(this[0].form).validate(),this.each(function(){b=c.element(this)&&b})),b},removeAttrs:function(b){var c={},d=this;return a.each(b.split(/\s/),function(a,b){c[b]=d.attr(b),d.removeAttr(b)}),c},rules:function(b,c){var d,e,f,g,h,i,j=this[0];if(b)switch(d=a.data(j.form,"validator").settings,e=d.rules,f=a.validator.staticRules(j),b){case"add":a.extend(f,a.validator.normalizeRule(c)),delete f.messages,e[j.name]=f,c.messages&&(d.messages[j.name]=a.extend(d.messages[j.name],c.messages));break;case"remove":return c?(i={},a.each(c.split(/\s/),function(b,c){i[c]=f[c],delete f[c],"required"===c&&a(j).removeAttr("aria-required")}),i):(delete e[j.name],f)}return g=a.validator.normalizeRules(a.extend({},a.validator.classRules(j),a.validator.attributeRules(j),a.validator.dataRules(j),a.validator.staticRules(j)),j),g.required&&(h=g.required,delete g.required,g=a.extend({required:h},g),a(j).attr("aria-required","true")),g.remote&&(h=g.remote,delete g.remote,g=a.extend(g,{remote:h})),g}}),a.extend(a.expr[":"],{blank:function(b){return!a.trim(""+a(b).val())},filled:function(b){return!!a.trim(""+a(b).val())},unchecked:function(b){return!a(b).prop("checked")}}),a.validator=function(b,c){this.settings=a.extend(!0,{},a.validator.defaults,b),this.currentForm=c,this.init()},a.validator.format=function(b,c){return 1===arguments.length?function(){var c=a.makeArray(arguments);return c.unshift(b),a.validator.format.apply(this,c)}:(arguments.length>2&&c.constructor!==Array&&(c=a.makeArray(arguments).slice(1)),c.constructor!==Array&&(c=[c]),a.each(c,function(a,c){b=b.replace(new RegExp("\\{"+a+"\\}","g"),function(){return c})}),b)},a.extend(a.validator,{defaults:{messages:{},groups:{},rules:{},errorClass:"error",validClass:"valid",errorElement:"label",focusInvalid:!0,errorContainer:a([]),errorLabelContainer:a([]),onsubmit:!0,ignore:":hidden",ignoreTitle:!1,onfocusin:function(a){this.lastActive=a,this.settings.focusCleanup&&!this.blockFocusCleanup&&(this.settings.unhighlight&&this.settings.unhighlight.call(this,a,this.settings.errorClass,this.settings.validClass),this.hideThese(this.errorsFor(a)))},onfocusout:function(a){this.checkable(a)||!(a.name in this.submitted)&&this.optional(a)||this.element(a)},onkeyup:function(a,b){(9!==b.which||""!==this.elementValue(a))&&(a.name in this.submitted||a===this.lastElement)&&this.element(a)},onclick:function(a){a.name in this.submitted?this.element(a):a.parentNode.name in this.submitted&&this.element(a.parentNode)},highlight:function(b,c,d){"radio"===b.type?this.findByName(b.name).addClass(c).removeClass(d):a(b).addClass(c).removeClass(d)},unhighlight:function(b,c,d){"radio"===b.type?this.findByName(b.name).removeClass(c).addClass(d):a(b).removeClass(c).addClass(d)}},setDefaults:function(b){a.extend(a.validator.defaults,b)},messages:{required:"This field is required.",remote:"Please fix this field.",email:"Please enter a valid email address.",url:"Please enter a valid URL.",date:"Please enter a valid date.",dateISO:"Please enter a valid date ( ISO ).",number:"Please enter a valid number.",digits:"Please enter only digits.",creditcard:"Please enter a valid credit card number.",equalTo:"Please enter the same value again.",maxlength:a.validator.format("Please enter no more than {0} characters."),minlength:a.validator.format("Please enter at least {0} characters."),rangelength:a.validator.format("Please enter a value between {0} and {1} characters long."),range:a.validator.format("Please enter a value between {0} and {1}."),max:a.validator.format("Please enter a value less than or equal to {0}."),min:a.validator.format("Please enter a value greater than or equal to {0}.")},autoCreateRanges:!1,prototype:{init:function(){function b(b){var c=a.data(this[0].form,"validator"),d="on"+b.type.replace(/^validate/,""),e=c.settings;e[d]&&!this.is(e.ignore)&&e[d].call(c,this[0],b)}this.labelContainer=a(this.settings.errorLabelContainer),this.errorContext=this.labelContainer.length&&this.labelContainer||a(this.currentForm),this.containers=a(this.settings.errorContainer).add(this.settings.errorLabelContainer),this.submitted={},this.valueCache={},this.pendingRequest=0,this.pending={},this.invalid={},this.reset();var c,d=this.groups={};a.each(this.settings.groups,function(b,c){"string"==typeof c&&(c=c.split(/\s/)),a.each(c,function(a,c){d[c]=b})}),c=this.settings.rules,a.each(c,function(b,d){c[b]=a.validator.normalizeRule(d)}),a(this.currentForm).validateDelegate(":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'] ,[type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], [type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], [type='radio'], [type='checkbox']","focusin focusout keyup",b).validateDelegate("select, option, [type='radio'], [type='checkbox']","click",b),this.settings.invalidHandler&&a(this.currentForm).bind("invalid-form.validate",this.settings.invalidHandler),a(this.currentForm).find("[required], [data-rule-required], .required").attr("aria-required","true")},form:function(){return this.checkForm(),a.extend(this.submitted,this.errorMap),this.invalid=a.extend({},this.errorMap),this.valid()||a(this.currentForm).triggerHandler("invalid-form",[this]),this.showErrors(),this.valid()},checkForm:function(){this.prepareForm();for(var a=0,b=this.currentElements=this.elements();b[a];a++)this.check(b[a]);return this.valid()},element:function(b){var c=this.clean(b),d=this.validationTargetFor(c),e=!0;return this.lastElement=d,void 0===d?delete this.invalid[c.name]:(this.prepareElement(d),this.currentElements=a(d),e=this.check(d)!==!1,e?delete this.invalid[d.name]:this.invalid[d.name]=!0),a(b).attr("aria-invalid",!e),this.numberOfInvalids()||(this.toHide=this.toHide.add(this.containers)),this.showErrors(),e},showErrors:function(b){if(b){a.extend(this.errorMap,b),this.errorList=[];for(var c in b)this.errorList.push({message:b[c],element:this.findByName(c)[0]});this.successList=a.grep(this.successList,function(a){return!(a.name in b)})}this.settings.showErrors?this.settings.showErrors.call(this,this.errorMap,this.errorList):this.defaultShowErrors()},resetForm:function(){a.fn.resetForm&&a(this.currentForm).resetForm(),this.submitted={},this.lastElement=null,this.prepareForm(),this.hideErrors(),this.elements().removeClass(this.settings.errorClass).removeData("previousValue").removeAttr("aria-invalid")},numberOfInvalids:function(){return this.objectLength(this.invalid)},objectLength:function(a){var b,c=0;for(b in a)c++;return c},hideErrors:function(){this.hideThese(this.toHide)},hideThese:function(a){a.not(this.containers).text(""),this.addWrapper(a).hide()},valid:function(){return 0===this.size()},size:function(){return this.errorList.length},focusInvalid:function(){if(this.settings.focusInvalid)try{a(this.findLastActive()||this.errorList.length&&this.errorList[0].element||[]).filter(":visible").focus().trigger("focusin")}catch(b){}},findLastActive:function(){var b=this.lastActive;return b&&1===a.grep(this.errorList,function(a){return a.element.name===b.name}).length&&b},elements:function(){var b=this,c={};return a(this.currentForm).find("input, select, textarea").not(":submit, :reset, :image, [disabled]").not(this.settings.ignore).filter(function(){return!this.name&&b.settings.debug&&window.console&&console.error("%o has no name assigned",this),this.name in c||!b.objectLength(a(this).rules())?!1:(c[this.name]=!0,!0)})},clean:function(b){return a(b)[0]},errors:function(){var b=this.settings.errorClass.split(" ").join(".");return a(this.settings.errorElement+"."+b,this.errorContext)},reset:function(){this.successList=[],this.errorList=[],this.errorMap={},this.toShow=a([]),this.toHide=a([]),this.currentElements=a([])},prepareForm:function(){this.reset(),this.toHide=this.errors().add(this.containers)},prepareElement:function(a){this.reset(),this.toHide=this.errorsFor(a)},elementValue:function(b){var c,d=a(b),e=b.type;return"radio"===e||"checkbox"===e?a("input[name='"+b.name+"']:checked").val():"number"===e&&"undefined"!=typeof b.validity?b.validity.badInput?!1:d.val():(c=d.val(),"string"==typeof c?c.replace(/\r/g,""):c)},check:function(b){b=this.validationTargetFor(this.clean(b));var c,d,e,f=a(b).rules(),g=a.map(f,function(a,b){return b}).length,h=!1,i=this.elementValue(b);for(d in f){e={method:d,parameters:f[d]};try{if(c=a.validator.methods[d].call(this,i,b,e.parameters),"dependency-mismatch"===c&&1===g){h=!0;continue}if(h=!1,"pending"===c)return void(this.toHide=this.toHide.not(this.errorsFor(b)));if(!c)return this.formatAndAdd(b,e),!1}catch(j){throw this.settings.debug&&window.console&&console.log("Exception occurred when checking element "+b.id+", check the '"+e.method+"' method.",j),j}}if(!h)return this.objectLength(f)&&this.successList.push(b),!0},customDataMessage:function(b,c){return a(b).data("msg"+c.charAt(0).toUpperCase()+c.substring(1).toLowerCase())||a(b).data("msg")},customMessage:function(a,b){var c=this.settings.messages[a];return c&&(c.constructor===String?c:c[b])},findDefined:function(){for(var a=0;a<arguments.length;a++)if(void 0!==arguments[a])return arguments[a];return void 0},defaultMessage:function(b,c){return this.findDefined(this.customMessage(b.name,c),this.customDataMessage(b,c),!this.settings.ignoreTitle&&b.title||void 0,a.validator.messages[c],"<strong>Warning: No message defined for "+b.name+"</strong>")},formatAndAdd:function(b,c){var d=this.defaultMessage(b,c.method),e=/\$?\{(\d+)\}/g;"function"==typeof d?d=d.call(this,c.parameters,b):e.test(d)&&(d=a.validator.format(d.replace(e,"{$1}"),c.parameters)),this.errorList.push({message:d,element:b,method:c.method}),this.errorMap[b.name]=d,this.submitted[b.name]=d},addWrapper:function(a){return this.settings.wrapper&&(a=a.add(a.parent(this.settings.wrapper))),a},defaultShowErrors:function(){var a,b,c;for(a=0;this.errorList[a];a++)c=this.errorList[a],this.settings.highlight&&this.settings.highlight.call(this,c.element,this.settings.errorClass,this.settings.validClass),this.showLabel(c.element,c.message);if(this.errorList.length&&(this.toShow=this.toShow.add(this.containers)),this.settings.success)for(a=0;this.successList[a];a++)this.showLabel(this.successList[a]);if(this.settings.unhighlight)for(a=0,b=this.validElements();b[a];a++)this.settings.unhighlight.call(this,b[a],this.settings.errorClass,this.settings.validClass);this.toHide=this.toHide.not(this.toShow),this.hideErrors(),this.addWrapper(this.toShow).show()},validElements:function(){return this.currentElements.not(this.invalidElements())},invalidElements:function(){return a(this.errorList).map(function(){return this.element})},showLabel:function(b,c){var d,e,f,g=this.errorsFor(b),h=this.idOrName(b),i=a(b).attr("aria-describedby");g.length?(g.removeClass(this.settings.validClass).addClass(this.settings.errorClass),g.html(c)):(g=a("<"+this.settings.errorElement+">").attr("id",h+"-error").addClass(this.settings.errorClass).html(c||""),d=g,this.settings.wrapper&&(d=g.hide().show().wrap("<"+this.settings.wrapper+"/>").parent()),this.labelContainer.length?this.labelContainer.append(d):this.settings.errorPlacement?this.settings.errorPlacement(d,a(b)):d.insertAfter(b),g.is("label")?g.attr("for",h):0===g.parents("label[for='"+h+"']").length&&(f=g.attr("id"),i?i.match(new RegExp("\b"+f+"\b"))||(i+=" "+f):i=f,a(b).attr("aria-describedby",i),e=this.groups[b.name],e&&a.each(this.groups,function(b,c){c===e&&a("[name='"+b+"']",this.currentForm).attr("aria-describedby",g.attr("id"))}))),!c&&this.settings.success&&(g.text(""),"string"==typeof this.settings.success?g.addClass(this.settings.success):this.settings.success(g,b)),this.toShow=this.toShow.add(g)},errorsFor:function(b){var c=this.idOrName(b),d=a(b).attr("aria-describedby"),e="label[for='"+c+"'], label[for='"+c+"'] *";return d&&(e=e+", #"+d.replace(/\s+/g,", #")),this.errors().filter(e)},idOrName:function(a){return this.groups[a.name]||(this.checkable(a)?a.name:a.id||a.name)},validationTargetFor:function(a){return this.checkable(a)&&(a=this.findByName(a.name).not(this.settings.ignore)[0]),a},checkable:function(a){return/radio|checkbox/i.test(a.type)},findByName:function(b){return a(this.currentForm).find("[name='"+b+"']")},getLength:function(b,c){switch(c.nodeName.toLowerCase()){case"select":return a("option:selected",c).length;case"input":if(this.checkable(c))return this.findByName(c.name).filter(":checked").length}return b.length},depend:function(a,b){return this.dependTypes[typeof a]?this.dependTypes[typeof a](a,b):!0},dependTypes:{"boolean":function(a){return a},string:function(b,c){return!!a(b,c.form).length},"function":function(a,b){return a(b)}},optional:function(b){var c=this.elementValue(b);return!a.validator.methods.required.call(this,c,b)&&"dependency-mismatch"},startRequest:function(a){this.pending[a.name]||(this.pendingRequest++,this.pending[a.name]=!0)},stopRequest:function(b,c){this.pendingRequest--,this.pendingRequest<0&&(this.pendingRequest=0),delete this.pending[b.name],c&&0===this.pendingRequest&&this.formSubmitted&&this.form()?(a(this.currentForm).submit(),this.formSubmitted=!1):!c&&0===this.pendingRequest&&this.formSubmitted&&(a(this.currentForm).triggerHandler("invalid-form",[this]),this.formSubmitted=!1)},previousValue:function(b){return a.data(b,"previousValue")||a.data(b,"previousValue",{old:null,valid:!0,message:this.defaultMessage(b,"remote")})}},classRuleSettings:{required:{required:!0},email:{email:!0},url:{url:!0},date:{date:!0},dateISO:{dateISO:!0},number:{number:!0},digits:{digits:!0},creditcard:{creditcard:!0}},addClassRules:function(b,c){b.constructor===String?this.classRuleSettings[b]=c:a.extend(this.classRuleSettings,b)},classRules:function(b){var c={},d=a(b).attr("class");return d&&a.each(d.split(" "),function(){this in a.validator.classRuleSettings&&a.extend(c,a.validator.classRuleSettings[this])}),c},attributeRules:function(b){var c,d,e={},f=a(b),g=b.getAttribute("type");for(c in a.validator.methods)"required"===c?(d=b.getAttribute(c),""===d&&(d=!0),d=!!d):d=f.attr(c),/min|max/.test(c)&&(null===g||/number|range|text/.test(g))&&(d=Number(d)),d||0===d?e[c]=d:g===c&&"range"!==g&&(e[c]=!0);return e.maxlength&&/-1|2147483647|524288/.test(e.maxlength)&&delete e.maxlength,e},dataRules:function(b){var c,d,e={},f=a(b);for(c in a.validator.methods)d=f.data("rule"+c.charAt(0).toUpperCase()+c.substring(1).toLowerCase()),void 0!==d&&(e[c]=d);return e},staticRules:function(b){var c={},d=a.data(b.form,"validator");return d.settings.rules&&(c=a.validator.normalizeRule(d.settings.rules[b.name])||{}),c},normalizeRules:function(b,c){return a.each(b,function(d,e){if(e===!1)return void delete b[d];if(e.param||e.depends){var f=!0;switch(typeof e.depends){case"string":f=!!a(e.depends,c.form).length;break;case"function":f=e.depends.call(c,c)}f?b[d]=void 0!==e.param?e.param:!0:delete b[d]}}),a.each(b,function(d,e){b[d]=a.isFunction(e)?e(c):e}),a.each(["minlength","maxlength"],function(){b[this]&&(b[this]=Number(b[this]))}),a.each(["rangelength","range"],function(){var c;b[this]&&(a.isArray(b[this])?b[this]=[Number(b[this][0]),Number(b[this][1])]:"string"==typeof b[this]&&(c=b[this].replace(/[\[\]]/g,"").split(/[\s,]+/),b[this]=[Number(c[0]),Number(c[1])]))}),a.validator.autoCreateRanges&&(b.min&&b.max&&(b.range=[b.min,b.max],delete b.min,delete b.max),b.minlength&&b.maxlength&&(b.rangelength=[b.minlength,b.maxlength],delete b.minlength,delete b.maxlength)),b},normalizeRule:function(b){if("string"==typeof b){var c={};a.each(b.split(/\s/),function(){c[this]=!0}),b=c}return b},addMethod:function(b,c,d){a.validator.methods[b]=c,a.validator.messages[b]=void 0!==d?d:a.validator.messages[b],c.length<3&&a.validator.addClassRules(b,a.validator.normalizeRule(b))},methods:{required:function(b,c,d){if(!this.depend(d,c))return"dependency-mismatch";if("select"===c.nodeName.toLowerCase()){var e=a(c).val();return e&&e.length>0}return this.checkable(c)?this.getLength(b,c)>0:a.trim(b).length>0},email:function(a,b){return this.optional(b)||/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(a)},url:function(a,b){return this.optional(b)||/^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(a)},date:function(a,b){return this.optional(b)||!/Invalid|NaN/.test(new Date(a).toString())},dateISO:function(a,b){return this.optional(b)||/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(a)},number:function(a,b){return this.optional(b)||/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(a)},digits:function(a,b){return this.optional(b)||/^\d+$/.test(a)},creditcard:function(a,b){if(this.optional(b))return"dependency-mismatch";if(/[^0-9 \-]+/.test(a))return!1;var c,d,e=0,f=0,g=!1;if(a=a.replace(/\D/g,""),a.length<13||a.length>19)return!1;for(c=a.length-1;c>=0;c--)d=a.charAt(c),f=parseInt(d,10),g&&(f*=2)>9&&(f-=9),e+=f,g=!g;return e%10===0},minlength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(a.trim(b),c);return this.optional(c)||e>=d},maxlength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(a.trim(b),c);return this.optional(c)||d>=e},rangelength:function(b,c,d){var e=a.isArray(b)?b.length:this.getLength(a.trim(b),c);return this.optional(c)||e>=d[0]&&e<=d[1]},min:function(a,b,c){return this.optional(b)||a>=c},max:function(a,b,c){return this.optional(b)||c>=a},range:function(a,b,c){return this.optional(b)||a>=c[0]&&a<=c[1]},equalTo:function(b,c,d){var e=a(d);return this.settings.onfocusout&&e.unbind(".validate-equalTo").bind("blur.validate-equalTo",function(){a(c).valid()}),b===e.val()},remote:function(b,c,d){if(this.optional(c))return"dependency-mismatch";var e,f,g=this.previousValue(c);return this.settings.messages[c.name]||(this.settings.messages[c.name]={}),g.originalMessage=this.settings.messages[c.name].remote,this.settings.messages[c.name].remote=g.message,d="string"==typeof d&&{url:d}||d,g.old===b?g.valid:(g.old=b,e=this,this.startRequest(c),f={},f[c.name]=b,a.ajax(a.extend(!0,{url:d,mode:"abort",port:"validate"+c.name,dataType:"json",data:f,context:e.currentForm,success:function(d){var f,h,i,j=d===!0||"true"===d;e.settings.messages[c.name].remote=g.originalMessage,j?(i=e.formSubmitted,e.prepareElement(c),e.formSubmitted=i,e.successList.push(c),delete e.invalid[c.name],e.showErrors()):(f={},h=d||e.defaultMessage(c,"remote"),f[c.name]=g.message=a.isFunction(h)?h(b):h,e.invalid[c.name]=!0,e.showErrors(f)),g.valid=j,e.stopRequest(c,j)}},d)),"pending")}}}),a.format=function(){throw"$.format has been deprecated. Please use $.validator.format instead."};var b,c={};a.ajaxPrefilter?a.ajaxPrefilter(function(a,b,d){var e=a.port;"abort"===a.mode&&(c[e]&&c[e].abort(),c[e]=d)}):(b=a.ajax,a.ajax=function(d){var e=("mode"in d?d:a.ajaxSettings).mode,f=("port"in d?d:a.ajaxSettings).port;return"abort"===e?(c[f]&&c[f].abort(),c[f]=b.apply(this,arguments),c[f]):b.apply(this,arguments)}),a.extend(a.fn,{validateDelegate:function(b,c,d){return this.bind(c,function(c){var e=a(c.target);return e.is(b)?d.apply(e,arguments):void 0})}})});

/**
 * @package   PickMeUp - jQuery datepicker plugin
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @author    Stefan Petre <www.eyecon.ro>
 * @copyright Copyright (c) 2013-2016, Nazar Mokrynskyi
 * @copyright Copyright (c) 2008-2009, Stefan Petre
 * @license   MIT License, see license.txt
 */
(function(d){function getMaxDays(){var tmpDate=new Date(this.toString()),d=28,m=tmpDate.getMonth();while(tmpDate.getMonth()==m){++d;tmpDate.setDate(d);}return d-1;}d.addDays=function(n){this.setDate(this.getDate()+n);};d.addMonths=function(n){var day=this.getDate();this.setDate(1);this.setMonth(this.getMonth()+n);this.setDate(Math.min(day,getMaxDays.apply(this)));};d.addYears=function(n){var day=this.getDate();this.setDate(1);this.setFullYear(this.getFullYear()+n);this.setDate(Math.min(day,getMaxDays.apply(this)));};d.getDayOfYear=function(){var now=new Date(this.getFullYear(),this.getMonth(),this.getDate(),0,0,0);var then=new Date(this.getFullYear(),0,0,0,0,0);var time=now-then;return Math.floor(time/24*60*60*1000);};})(Date.prototype);(function(factory){if(typeof define==="function"&&define.amd){define(["jquery"],factory);}else{if(typeof exports==="object"){factory(require("jquery"));}else{factory(jQuery);}}}(function($){var instances_count=0;$.pickmeup=$.extend($.pickmeup||{},{date:new Date,default_date:new Date,flat:false,first_day:1,prev:"&#9664;",next:"&#9654;",mode:"single",select_year:true,select_month:true,select_day:true,view:"days",calendars:1,format:"d-m-Y",title_format:"B, Y",position:"bottom",trigger_event:"click touchstart",class_name:"",separator:" - ",hide_on_select:false,min:null,max:null,render:function(){},change:function(){return true;},before_show:function(){return true;},show:function(){return true;},hide:function(){return true;},fill:function(){return true;},locale:{days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],daysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun"],daysMin:["Su","Mo","Tu","We","Th","Fr","Sa","Su"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]}});var views={years:"pmu-view-years",months:"pmu-view-months",days:"pmu-view-days"},tpl={wrapper:'<div class="pickmeup" />',head:function(d){var result="";for(var i=0;i<7;++i){result+="<div>"+d.day[i]+"</div>";}return'<div class="pmu-instance"><nav><div class="pmu-prev pmu-button">'+d.prev+'</div><div class="pmu-month pmu-button" /><div class="pmu-next pmu-button">'+d.next+'</div></nav><nav class="pmu-day-of-week">'+result+"</nav></div>";},body:function(elements,container_class_name){var result="";for(var i=0;i<elements.length;++i){result+='<div class="'+elements[i].class_name+' pmu-button">'+elements[i].text+"</div>";}return'<div class="'+container_class_name+'">'+result+"</div>";}};function fill(){var options=$(this).data("pickmeup-options"),pickmeup=this.pickmeup,current_cal=Math.floor(options.calendars/2),actual_date=options.date,current_date=options.current,min_date=options.min?new Date(options.min):null,max_date=options.max?new Date(options.max):null,local_date,header,html,instance,today=(new Date).setHours(0,0,0,0).valueOf(),shown_date_from,shown_date_to,tmp_date;if(min_date){min_date.setDate(1);min_date.addMonths(1);min_date.addDays(-1);}if(max_date){max_date.setDate(1);max_date.addMonths(1);max_date.addDays(-1);}pickmeup.find(".pmu-instance > :not(nav)").remove();for(var i=0;i<options.calendars;i++){local_date=new Date(current_date);instance=pickmeup.find(".pmu-instance").eq(i);if(pickmeup.hasClass("pmu-view-years")){local_date.addYears((i-current_cal)*12);header=(local_date.getFullYear()-6)+" - "+(local_date.getFullYear()+5);}else{if(pickmeup.hasClass("pmu-view-months")){local_date.addYears(i-current_cal);header=local_date.getFullYear();}else{if(pickmeup.hasClass("pmu-view-days")){local_date.addMonths(i-current_cal);header=formatDate(local_date,options.title_format,options.locale);}}}if(!shown_date_to){if(max_date){tmp_date=new Date(local_date);if(options.select_day){tmp_date.addMonths(options.calendars-1);}else{if(options.select_month){tmp_date.addYears(options.calendars-1);}else{tmp_date.addYears((options.calendars-1)*12);}}if(tmp_date>max_date){--i;current_date.addMonths(-1);shown_date_to=undefined;continue;}}}shown_date_to=new Date(local_date);if(!shown_date_from){shown_date_from=new Date(local_date);shown_date_from.setDate(1);shown_date_from.addMonths(1);shown_date_from.addDays(-1);if(min_date&&min_date>shown_date_from){--i;current_date.addMonths(1);shown_date_from=undefined;continue;}}instance.find(".pmu-month").text(header);html="";var is_year_selected=function(year){return(options.mode=="range"&&year>=new Date(actual_date[0]).getFullYear()&&year<=new Date(actual_date[1]).getFullYear())||(options.mode=="multiple"&&actual_date.reduce(function(prev,current){prev.push(new Date(current).getFullYear());return prev;},[]).indexOf(year)!==-1)||new Date(actual_date).getFullYear()==year;};var is_months_selected=function(year,month){var first_year=new Date(actual_date[0]).getFullYear(),lastyear=new Date(actual_date[1]).getFullYear(),first_month=new Date(actual_date[0]).getMonth(),last_month=new Date(actual_date[1]).getMonth();return(options.mode=="range"&&year>first_year&&year<lastyear)||(options.mode=="range"&&year==first_year&&year<lastyear&&month>=first_month)||(options.mode=="range"&&year>first_year&&year==lastyear&&month<=last_month)||(options.mode=="range"&&year==first_year&&year==lastyear&&month>=first_month&&month<=last_month)||(options.mode=="multiple"&&actual_date.reduce(function(prev,current){current=new Date(current);prev.push(current.getFullYear()+"-"+current.getMonth());return prev;},[]).indexOf(year+"-"+month)!==-1)||(new Date(actual_date).getFullYear()==year&&new Date(actual_date).getMonth()==month);};(function(){var years=[],start_from_year=local_date.getFullYear()-6,min_year=new Date(options.min).getFullYear(),max_year=new Date(options.max).getFullYear(),year;for(var j=0;j<12;++j){year={text:start_from_year+j,class_name:[]};if((options.min&&year.text<min_year)||(options.max&&year.text>max_year)){year.class_name.push("pmu-disabled");}else{if(is_year_selected(year.text)){year.class_name.push("pmu-selected");}}year.class_name=year.class_name.join(" ");years.push(year);}html+=tpl.body(years,"pmu-years");})();(function(){var months=[],current_year=local_date.getFullYear(),min_year=new Date(options.min).getFullYear(),min_month=new Date(options.min).getMonth(),max_year=new Date(options.max).getFullYear(),max_month=new Date(options.max).getMonth(),month;for(var j=0;j<12;++j){month={text:options.locale.monthsShort[j],class_name:[]};if((options.min&&(current_year<min_year||(j<min_month&&current_year==min_year)))||(options.max&&(current_year>max_year||(j>max_month&&current_year>=max_year)))){month.class_name.push("pmu-disabled");}else{if(is_months_selected(current_year,j)){month.class_name.push("pmu-selected");}}month.class_name=month.class_name.join(" ");months.push(month);}html+=tpl.body(months,"pmu-months");})();(function(){var days=[],current_month=local_date.getMonth(),day;(function(){local_date.setDate(1);var day=(local_date.getDay()-options.first_day)%7;local_date.addDays(-(day+(day<0?7:0)));})();for(var j=0;j<42;++j){day={text:local_date.getDate(),class_name:[]};if(current_month!=local_date.getMonth()){day.class_name.push("pmu-not-in-month");}if(local_date.getDay()==0){day.class_name.push("pmu-sunday");}else{if(local_date.getDay()==6){day.class_name.push("pmu-saturday");}}var from_user=options.render(new Date(local_date))||{},val=local_date.valueOf(),disabled=(options.min&&options.min>local_date)||(options.max&&options.max<local_date);if(from_user.disabled||disabled){day.class_name.push("pmu-disabled");}else{if(from_user.selected||options.date==val||$.inArray(val,options.date)!==-1||(options.mode=="range"&&val>=options.date[0]&&val<=options.date[1])){day.class_name.push("pmu-selected");}}if(val==today){day.class_name.push("pmu-today");}if(from_user.class_name){day.class_name.push(from_user.class_name);}day.class_name=day.class_name.join(" ");days.push(day);local_date.addDays(1);}html+=tpl.body(days,"pmu-days");})();instance.append(html);}shown_date_from.setDate(1);shown_date_to.setDate(1);shown_date_to.addMonths(1);shown_date_to.addDays(-1);pickmeup.find(".pmu-prev").css("visibility",options.min&&options.min>=shown_date_from?"hidden":"visible");pickmeup.find(".pmu-next").css("visibility",options.max&&options.max<=shown_date_to?"hidden":"visible");options.fill.apply(this);}function parseDate(date,format,separator,locale){if(date.constructor==Date){return date;}else{if(!date){return new Date;}}var splitted_date=date.split(separator);if(splitted_date.length>1){splitted_date.forEach(function(element,index,array){array[index]=parseDate($.trim(element),format,separator,locale);});return splitted_date;}var months_text=locale.monthsShort.join(")(")+")("+locale.months.join(")("),separator=new RegExp("[^0-9a-zA-Z("+months_text+")]+"),parts=date.split(separator),against=format.split(separator),d,m,y,h,min,now=new Date();for(var i=0;i<parts.length;i++){switch(against[i]){case"b":m=locale.monthsShort.indexOf(parts[i]);break;case"B":m=locale.months.indexOf(parts[i]);break;case"d":case"e":d=parseInt(parts[i],10);break;case"m":m=parseInt(parts[i],10)-1;break;case"Y":case"y":y=parseInt(parts[i],10);y+=y>100?0:(y<29?2000:1900);break;case"H":case"I":case"k":case"l":h=parseInt(parts[i],10);break;case"P":case"p":if(/pm/i.test(parts[i])&&h<12){h+=12;}else{if(/am/i.test(parts[i])&&h>=12){h-=12;}}break;case"M":min=parseInt(parts[i],10);break;}}var parsed_date=new Date(y===undefined?now.getFullYear():y,m===undefined?now.getMonth():m,d===undefined?now.getDate():d,h===undefined?now.getHours():h,min===undefined?now.getMinutes():min,0);if(isNaN(parsed_date*1)){parsed_date=new Date;}return parsed_date;}function formatDate(date,format,locale){var m=date.getMonth();var d=date.getDate();var y=date.getFullYear();var w=date.getDay();var s={};var hr=date.getHours();var pm=(hr>=12);var ir=(pm)?(hr-12):hr;var dy=date.getDayOfYear();if(ir==0){ir=12;}var min=date.getMinutes();var sec=date.getSeconds();var parts=format.split(""),part;for(var i=0;i<parts.length;i++){part=parts[i];switch(part){case"a":part=locale.daysShort[w];break;case"A":part=locale.days[w];break;case"b":part=locale.monthsShort[m];break;case"B":part=locale.months[m];break;case"C":part=1+Math.floor(y/100);break;case"d":part=(d<10)?("0"+d):d;break;case"e":part=d;break;case"H":part=(hr<10)?("0"+hr):hr;break;case"I":part=(ir<10)?("0"+ir):ir;break;case"j":part=(dy<100)?((dy<10)?("00"+dy):("0"+dy)):dy;break;case"k":part=hr;break;case"l":part=ir;break;case"m":part=(m<9)?("0"+(1+m)):(1+m);break;case"M":part=(min<10)?("0"+min):min;break;case"p":case"P":part=pm?"PM":"AM";break;case"s":part=Math.floor(date.getTime()/1000);break;case"S":part=(sec<10)?("0"+sec):sec;break;case"u":part=w+1;break;case"w":part=w;break;case"y":part=(""+y).substr(2,2);break;case"Y":part=y;break;}parts[i]=part;}return parts.join("");}function update_date(){var $this=$(this),options=$this.data("pickmeup-options"),current_date=options.current,new_value;switch(options.mode){case"multiple":new_value=current_date.setHours(0,0,0,0).valueOf();if($.inArray(new_value,options.date)!==-1){$.each(options.date,function(index,value){if(value==new_value){options.date.splice(index,1);return false;}return true;});}else{options.date.push(new_value);}break;case"range":if(!options.lastSel){options.date[0]=current_date.setHours(0,0,0,0).valueOf();}new_value=current_date.setHours(0,0,0,0).valueOf();if(new_value<=options.date[0]){options.date[1]=options.date[0];options.date[0]=new_value;}else{options.date[1]=new_value;}options.lastSel=!options.lastSel;break;default:options.date=current_date.valueOf();break;}var prepared_date=prepareDate(options);if($this.is("input")){$this.val(options.mode=="single"?prepared_date[0]:prepared_date[0].join(options.separator));}options.change.apply(this,prepared_date);if(!options.flat&&options.hide_on_select&&(options.mode!="range"||!options.lastSel)){options.binded.hide();return false;}}function click(e){var el=$(e.target);if(!el.hasClass("pmu-button")){el=el.closest(".pmu-button");}if(el.length){if(el.hasClass("pmu-disabled")){return false;}var $this=$(this),options=$this.data("pickmeup-options"),instance=el.parents(".pmu-instance").eq(0),root=instance.parent(),instance_index=$(".pmu-instance",root).index(instance);if(el.parent().is("nav")){if(el.hasClass("pmu-month")){options.current.addMonths(instance_index-Math.floor(options.calendars/2));if(root.hasClass("pmu-view-years")){if(options.mode!="single"){options.current=new Date(options.date[options.date.length-1]);}else{options.current=new Date(options.date);}if(options.select_day){root.removeClass("pmu-view-years").addClass("pmu-view-days");}else{if(options.select_month){root.removeClass("pmu-view-years").addClass("pmu-view-months");}}}else{if(root.hasClass("pmu-view-months")){if(options.select_year){root.removeClass("pmu-view-months").addClass("pmu-view-years");}else{if(options.select_day){root.removeClass("pmu-view-months").addClass("pmu-view-days");}}}else{if(root.hasClass("pmu-view-days")){if(options.select_month){root.removeClass("pmu-view-days").addClass("pmu-view-months");}else{if(options.select_year){root.removeClass("pmu-view-days").addClass("pmu-view-years");}}}}}}else{if(el.hasClass("pmu-prev")){options.binded.prev(false);}else{options.binded.next(false);}}}else{if(!el.hasClass("pmu-disabled")){if(root.hasClass("pmu-view-years")){options.current.setFullYear(parseInt(el.text(),10));if(options.select_month){root.removeClass("pmu-view-years").addClass("pmu-view-months");}else{if(options.select_day){root.removeClass("pmu-view-years").addClass("pmu-view-days");}else{options.binded.update_date();}}}else{if(root.hasClass("pmu-view-months")){options.current.setMonth(instance.find(".pmu-months .pmu-button").index(el));options.current.setFullYear(parseInt(instance.find(".pmu-month").text(),10));if(options.select_day){root.removeClass("pmu-view-months").addClass("pmu-view-days");}else{options.binded.update_date();}options.current.addMonths(Math.floor(options.calendars/2)-instance_index);}else{var val=parseInt(el.text(),10);options.current.addMonths(instance_index-Math.floor(options.calendars/2));if(el.hasClass("pmu-not-in-month")){options.current.addMonths(val>15?-1:1);}options.current.setDate(val);options.binded.update_date();}}}}options.binded.fill();}return false;}function prepareDate(options){var result;if(options.mode=="single"){result=new Date(options.date);return[formatDate(result,options.format,options.locale),result];}else{result=[[],[]];$.each(options.date,function(nr,val){var date=new Date(val);result[0].push(formatDate(date,options.format,options.locale));result[1].push(date);});return result;}}function show(force){var pickmeup=this.pickmeup;if(force||!pickmeup.is(":visible")){var $this=$(this),options=$this.data("pickmeup-options"),pos=$this.offset(),viewport={l:document.documentElement.scrollLeft,t:document.documentElement.scrollTop,w:document.documentElement.clientWidth,h:document.documentElement.clientHeight},top=pos.top,left=pos.left;options.binded.fill();if($this.is("input")){$this.pickmeup("set_date",parseDate($this.val()?$this.val():options.default_date,options.format,options.separator,options.locale)).keydown(function(e){if(e.which==9){$this.pickmeup("hide");}});options.lastSel=false;}options.before_show();if(options.show()==false){return;}if(!options.flat){switch(options.position){case"top":top-=pickmeup.outerHeight();break;case"left":left-=pickmeup.outerWidth();break;case"right":left+=this.offsetWidth;break;case"bottom":top+=this.offsetHeight;break;}if(top+pickmeup.offsetHeight>viewport.t+viewport.h){top=pos.top-pickmeup.offsetHeight;}if(top<viewport.t){top=pos.top+this.offsetHeight+pickmeup.offsetHeight;}if(left+pickmeup.offsetWidth>viewport.l+viewport.w){left=pos.left-pickmeup.offsetWidth;}if(left<viewport.l){left=pos.left+this.offsetWidth;}pickmeup.css({display:"inline-block",top:top+"px",left:left+"px"});$(document).on("mousedown"+options.events_namespace+" touchstart"+options.events_namespace,options.binded.hide).on("resize"+options.events_namespace,[true],options.binded.forced_show);}}}function forced_show(){show.call(this,true);}function hide(e){if(!e||!e.target||(e.target!=this&&!(this.pickmeup.get(0).compareDocumentPosition(e.target)&16))){var pickmeup=this.pickmeup,options=$(this).data("pickmeup-options");if(options.hide()!=false){pickmeup.hide();$(document).off("mousedown touchstart",options.binded.hide).off("resize",options.binded.forced_show);options.lastSel=false;}}}function update(){var options=$(this).data("pickmeup-options");$(document).off("mousedown",options.binded.hide).off("resize",options.binded.forced_show);options.binded.forced_show();}function clear(){var options=$(this).data("pickmeup-options");if(options.mode!="single"){options.date=[];options.lastSel=false;options.binded.fill();}}function prev(fill){if(typeof fill=="undefined"){fill=true;}var root=this.pickmeup;var options=$(this).data("pickmeup-options");if(root.hasClass("pmu-view-years")){options.current.addYears(-12);}else{if(root.hasClass("pmu-view-months")){options.current.addYears(-1);}else{if(root.hasClass("pmu-view-days")){options.current.addMonths(-1);}}}if(fill){options.binded.fill();}}function next(fill){if(typeof fill=="undefined"){fill=true;}var root=this.pickmeup;var options=$(this).data("pickmeup-options");if(root.hasClass("pmu-view-years")){options.current.addYears(12);}else{if(root.hasClass("pmu-view-months")){options.current.addYears(1);}else{if(root.hasClass("pmu-view-days")){options.current.addMonths(1);}}}if(fill){options.binded.fill();}}function get_date(formatted){var options=$(this).data("pickmeup-options"),prepared_date=prepareDate(options);if(typeof formatted==="string"){var date=prepared_date[1];if(date.constructor==Date){return formatDate(date,formatted,options.locale);}else{return date.map(function(value){return formatDate(value,formatted,options.locale);});}}else{return prepared_date[formatted?0:1];}}function set_date(date){var $this=$(this),options=$this.data("pickmeup-options");options.date=date;if(typeof options.date==="string"){options.date=parseDate(options.date,options.format,options.separator,options.locale).setHours(0,0,0,0);}else{if(options.date.constructor==Date){options.date.setHours(0,0,0,0);}}if(!options.date){options.date=new Date;options.date.setHours(0,0,0,0);}if(options.mode!="single"){if(options.date.constructor!=Array){options.date=[options.date.valueOf()];if(options.mode=="range"){options.date.push(((new Date(options.date[0])).setHours(0,0,0,0)).valueOf());}}else{for(var i=0;i<options.date.length;i++){options.date[i]=(parseDate(options.date[i],options.format,options.separator,options.locale).setHours(0,0,0,0)).valueOf();}if(options.mode=="range"){options.date[1]=((new Date(options.date[1])).setHours(0,0,0,0)).valueOf();}}}else{if($this.val()||options.default_date!==false){options.date=options.date.constructor==Array?options.date[0].valueOf():options.date.valueOf();}}options.current=new Date(options.mode!="single"?options.date[0]:options.date);options.binded.fill();if($this.is("input")){var prepared_date=prepareDate(options);$this.val(options.mode=="single"?(options.default_date===false?$this.val():prepared_date[0]):prepared_date[0].join(options.separator));}}function destroy(){var $this=$(this),options=$this.data("pickmeup-options");$this.removeData("pickmeup-options");$this.off(options.events_namespace);$(document).off(options.events_namespace);$(this.pickmeup).remove();}$.fn.pickmeup=function(initial_options){if(typeof initial_options==="string"){var data,parameters=Array.prototype.slice.call(arguments,1);switch(initial_options){case"hide":case"show":case"clear":case"update":case"prev":case"next":case"destroy":this.each(function(){data=$(this).data("pickmeup-options");if(data){data.binded[initial_options]();}});break;case"get_date":data=this.data("pickmeup-options");if(data){return data.binded.get_date(parameters[0]);}else{return null;}break;case"set_date":this.each(function(){data=$(this).data("pickmeup-options");if(data){data.binded[initial_options].apply(this,parameters);}});}return this;}return this.each(function(){var $this=$(this);if($this.data("pickmeup-options")){return;}var i,option,options=$.extend({},$.pickmeup,initial_options||{});for(i in options){option=$this.data("pmu-"+i);if(typeof option!=="undefined"){options[i]=option;}}if(options.view=="days"&&!options.select_day){options.view="months";}if(options.view=="months"&&!options.select_month){options.view="years";}if(options.view=="years"&&!options.select_year){options.view="days";}if(options.view=="days"&&!options.select_day){options.view="months";}options.calendars=Math.max(1,parseInt(options.calendars,10)||1);options.mode=/single|multiple|range/.test(options.mode)?options.mode:"single";if(typeof options.min==="string"){options.min=parseDate(options.min,options.format,options.separator,options.locale).setHours(0,0,0,0);}else{if(options.min&&options.min.constructor==Date){options.min.setHours(0,0,0,0);}}if(typeof options.max==="string"){options.max=parseDate(options.max,options.format,options.separator,options.locale).setHours(0,0,0,0);}else{if(options.max&&options.max.constructor==Date){options.max.setHours(0,0,0,0);}}if(!options.select_day){if(options.min){options.min=new Date(options.min);options.min.setDate(1);options.min=options.min.valueOf();}if(options.max){options.max=new Date(options.max);options.max.setDate(1);options.max=options.max.valueOf();}}if(typeof options.date==="string"){options.date=parseDate(options.date,options.format,options.separator,options.locale).setHours(0,0,0,0);}else{if(options.date.constructor==Date){options.date.setHours(0,0,0,0);}}if(!options.date){options.date=new Date;options.date.setHours(0,0,0,0);}if(options.mode!="single"){if(options.date.constructor!=Array){options.date=[options.date.valueOf()];if(options.mode=="range"){options.date.push(((new Date(options.date[0])).setHours(0,0,0,0)).valueOf());}}else{for(i=0;i<options.date.length;i++){options.date[i]=(parseDate(options.date[i],options.format,options.separator,options.locale).setHours(0,0,0,0)).valueOf();}if(options.mode=="range"){options.date[1]=((new Date(options.date[1])).setHours(0,0,0,0)).valueOf();}}options.current=new Date(options.date[0]);if(!options.select_day){for(i=0;i<options.date.length;++i){options.date[i]=new Date(options.date[i]);options.date[i].setDate(1);options.date[i]=options.date[i].valueOf();if(options.mode!="range"&&options.date.indexOf(options.date[i])!==i){delete options.date.splice(i,1);--i;}}}}else{options.date=options.date.valueOf();options.current=new Date(options.date);if(!options.select_day){options.date=new Date(options.date);options.date.setDate(1);options.date=options.date.valueOf();}}options.current.setDate(1);options.current.setHours(0,0,0,0);var cnt,pickmeup=$(tpl.wrapper);this.pickmeup=pickmeup;if(options.class_name){pickmeup.addClass(options.class_name);}var html="";for(i=0;i<options.calendars;i++){cnt=options.first_day;html+=tpl.head({prev:options.prev,next:options.next,day:[options.locale.daysMin[(cnt++)%7],options.locale.daysMin[(cnt++)%7],options.locale.daysMin[(cnt++)%7],options.locale.daysMin[(cnt++)%7],options.locale.daysMin[(cnt++)%7],options.locale.daysMin[(cnt++)%7],options.locale.daysMin[(cnt++)%7]]});}$this.data("pickmeup-options",options);for(i in options){if(["render","change","before_show","show","hide"].indexOf(i)!=-1){options[i]=options[i].bind(this);}}options.binded={fill:fill.bind(this),update_date:update_date.bind(this),click:click.bind(this),show:show.bind(this),forced_show:forced_show.bind(this),hide:hide.bind(this),update:update.bind(this),clear:clear.bind(this),prev:prev.bind(this),next:next.bind(this),get_date:get_date.bind(this),set_date:set_date.bind(this),destroy:destroy.bind(this)};options.events_namespace=".pickmeup-"+(++instances_count);pickmeup.on("click touchstart",options.binded.click).addClass(views[options.view]).append(html).on($.support.selectstart?"selectstart":"mousedown",function(e){e.preventDefault();});options.binded.fill();if(options.flat){pickmeup.appendTo(this).css({position:"relative",display:"inline-block"});}else{pickmeup.appendTo(document.body);var trigger_event=options.trigger_event.split(" ");for(i=0;i<trigger_event.length;++i){trigger_event[i]+=options.events_namespace;}trigger_event=trigger_event.join(" ");$this.on(trigger_event,options.binded.show);}});};}));

/* Hooks and costomizations */

function wpfepp_str_word_count(s){
	if(!s.length)
		return 0;
	s = s.replace(/(<([^>]+)>)/ig,"");
	return s.trim().replace(/\s+/gi, ' ').split(' ').length;
}
function wpfepp_str_symbol_count(s){
	if(!s.length)
		return 0;
	return s.length;
}
function wpfepp_segment_count(s){
	if(!s.length)
		return 0;
	return s.split(',').length;
}
function wpfepp_link_count(s){
	var matches = s.match(/<\s*\ba\b.*?href/g, s);
	if(matches)
		return matches.length;
	return 0;
}
function wpfepp_scroll_to(item){
	if( item.offset().top < jQuery(window).scrollTop() ){
		jQuery('html, body').animate({ scrollTop: item.offset().top-10 }, 'slow');
	}
}

jQuery(document).ready(function($){

	$('.wpfepp-form-field-container[style*="width"]').each(function(){
		if($(this).length > 0){
			$(this).css("display", "inline-block");
		}
	});

	//Initialize qTips on page load
	$('.wpfepp-form-field-container').qtip({
		content: ' ',
    	prerender: true,
		overwrite: true,
	    position: { my: 'left center', at: 'right center', adjust: { y: 15 } },
	    show: { event: false, ready: false },
	    hide: { event: false },
	    style: { classes: 'wpfepp-tooltip' }
	});

	if(typeof wpfepp_set_content_restrictions == 'function')
		wpfepp_set_content_restrictions($);

	//Add custom methods for jquery valdation plugin
	$.validator.addMethod(
		"minwords",
		function(value, element, param) { return (this.optional(element) || wpfepp_str_word_count(value) >= param); },
		$.validator.format(wpfepp_errors.min_words)
	);
	$.validator.addMethod(
		"maxwords",
		function(value, element, param) { return (this.optional(element) || wpfepp_str_word_count(value) <= param); },
		$.validator.format(wpfepp_errors.max_words)
	);
	$.validator.addMethod(
		"minsymbols",
		function(value, element, param) { return (this.optional(element) || wpfepp_str_symbol_count(value) >= param); },
		$.validator.format(wpfepp_errors.min_symbols)
	);
	$.validator.addMethod(
		"maxsymbols",
		function(value, element, param) { return (this.optional(element) || wpfepp_str_symbol_count(value) <= param); },
		$.validator.format(wpfepp_errors.max_symbols)
	);
	$.validator.addMethod(
		"maxlinks",
		function(value, element, param) { return (this.optional(element) || wpfepp_link_count(value) <= param); },
		$.validator.format(wpfepp_errors.max_links)
	);
	$.validator.addMethod(
		"minsegments",
		function(value, element, param) { return (this.optional(element) || wpfepp_segment_count(value) >= param); },
		$.validator.format(wpfepp_errors.min_segments)
	);
	$.validator.addMethod(
		"maxsegments",
		function(value, element, param) { return (this.optional(element) || wpfepp_segment_count(value) <= param); },
		$.validator.format(wpfepp_errors.max_segments)
	);
	$.validator.addMethod(
		"hiddenrequired",
		function(value, element, param) { return ($(element).val() != '-1' ); },
		$.validator.format(wpfepp_errors.required)
	);
	$.extend($.validator.messages, {
		required: wpfepp_errors.required,
		email: wpfepp_errors.invalid_email,
		url: wpfepp_errors.invalid_url
	});

	//Hook the validator plugin to our forms
	var validator = $('.wpfepp-form').submit(function(e){
		e.preventDefault();
		if(typeof(tinyMCE) != 'undefined')
			tinyMCE.triggerSave();
	}).validate({
		ignore: ".ignore",
		focusInvalid: false,
		//This function is called when there are errors in a particular field
		errorPlacement: function(label, element) {
			//By default each error is wrapped in an HTML label tag
			if($(label).text() != ''){
				//Find the container, set its qTip's text to our error and make the qTip visible
				var parent = $(element).closest('.wpfepp-form-field-container');
				parent.qtip('api').set('content.text', $(label).text());
				parent.qtip('api').show();
			}
		},
		//This function is called when the user has resolved the errors in a particular field
		success: function (label, element) {
			var parent = $(element).closest('.wpfepp-form-field-container');
			parent.qtip('api').set('content.text', '');
			parent.qtip('api').hide();
		},
		//This function is called when the user tries to submit an invalid form
		invalidHandler: function(event, validator) {
			wpfepp_forms.get( $(this) ).show_errors();
		},
		//This function is called when the user submits a valid form
		submitHandler: function(form) {
			wpfepp_forms.get( $(form) ).submit_post();
		}
	});
	
	$('.wpfepp-media').each(function () {
		$(this).wp_media_lib_element(
			$(this).data()
		);
	});

	//Create a form collection object on page load.
	var wpfepp_forms = new WPFEPP_Form_Collection();

	//Definition of WPFEPP_Form_Collection class. For each form on the page it creates a WPFEPP_Form object and saves all the instaces in an array
	function WPFEPP_Form_Collection(){
		var self 	= this;
		//The WPFEPP_Form objects
		this.items 	= [];
		$('.wpfepp-form').each(function(){
			self.items.push( new WPFEPP_Form( this ) );
		});
		//Get a particular WPFEPP_Form instance from jQuery object
		this.get 	= function( jq_obj ){
			for (var i = self.items.length - 1; i >= 0; i--) {
				if(jq_obj.get(0) == self.items[i].element)
					return self.items[i];
			};
		}
	}

	//JS representation of a form
	function WPFEPP_Form(element) {
		var self = this;
		this.element = element;
		this.jq_obj = $(element);
		this.id = this.jq_obj.find('.wpfepp-form-id-field').first().val();
		this.form_message = this.jq_obj.find('.wpfepp-message').first();
		this.fields_container = this.jq_obj.find('.wpfepp-form-fields').first();
		this.post_id_field = this.jq_obj.find('.wpfepp-post-id-field').first();
		this.submit_button = this.jq_obj.find('.wpfepp-submit-button').first();
		this.save_button = this.jq_obj.find('.wpfepp-save-button').first();
		this.submit_button_icon = this.jq_obj.find('.dashicons-update').first();
		this.thumb_container 	= this.jq_obj.find('.wpfepp-thumbnail-container').first();
		this.thumb_id_field = this.jq_obj.find('.wpfepp-thumbnail-id').first();
		this.offer_product_url = this.jq_obj.find('.wpfepp-rehub_offer_product_url-field').first();
		this.captcha = this.jq_obj.find('.g-recaptcha').first();

		this.hide_captcha = function(){
			this.captcha.hide();
		}
		
		if( this.thumb_container.html() != '' ) {
			this.thumb_container.siblings(".wpfepp-thumbnail-close").show();
		}
			
		//A simple wrapper for jQuery's ajax function
		this.make_request = function(_data, successCallback, errorCallback){
			$.ajax({ type:'POST', dataType: 'json', url: wpfepp.ajaxurl, data: _data, success: successCallback, error: errorCallback });
		}
		//Serializes a form using jQuery's serialize() function and appends request type (submit or save).
		this.serialize = function(req_type){
			return this.jq_obj.serialize()+'&req_type='+req_type;
		}
		this.hide_form = function(){
			this.fields_container.hide();
		}
		//By default the value of the id field is -1. This function updates it.
		this.update_id = function(id){
			this.post_id_field.val(id);
		}
		this.hide_tips = function(){
			this.jq_obj.find('.wpfepp-form-field-container').each(function(){
				var container 	= $(this);
				container.qtip('api').set('content.text', ' ');
				container.qtip('api').hide();
			});
		}
		//Displays all of the form's errors. The main error is in a simple div while the errors for individual fields are displayed as qTips
		this.show_errors = function(errors){
			var form_errors = (errors && errors.form) ? errors.form : wpfepp_errors.form;
			this.form_message.addClass('error').removeClass('success').html(form_errors).show();
			this.jq_obj.find('.wpfepp-form-field-container').each(function(){
				var container = $(this);
				if( container.find('.wpfepp-form-field').first().attr('name') !== undefined ) {
					var field_name = container.find('.wpfepp-form-field').first().attr('name').replace('[]', '');
					if(errors){
						if(errors[field_name]){
							container.qtip('api').set('content.text', errors[field_name]);
							container.qtip('api').show();
						}
						else{
							container.qtip('api').set('content.text', ' ');
							container.qtip('api').hide();
						}	
					}
				}
			});
			wpfepp_scroll_to(this.form_message);
		}
		//Displays a success message and optionally hides the form.
		this.show_success = function(messages, hide_form){
			this.form_message.addClass('success').removeClass('error').html(messages.form).show();
			if($('.rh_wpeff_noticebox').length > 0){
				$('.rh_wpeff_noticebox').remove();
			}
			if(hide_form)
				this.hide_form();
			wpfepp_scroll_to(this.form_message);
		}
		//Disables all the form buttons and displays animation on one.
		this.disable_buttons = function(animated_btn){
			this.submit_button.attr('disabled', true);
			this.save_button.attr('disabled', true);
			this.submit_button_icon.css("opacity", "1");
		}
		//Enables all the buttons and hides the animation.
		this.enable_buttons = function(){
			this.submit_button.attr('disabled', false);
			this.save_button.attr('disabled', false);
			this.submit_button_icon.css("opacity", "0");
		}
		this.save_draft = function(){
			this.disable_buttons('save');
			this.make_request(
				this.serialize('save'),
				function(data){
					self.enable_buttons();
					if(data.success){
						self.hide_tips();
						self.show_success(data.errors, false);
						self.update_id(data.post_id);

						// Reset the captcha
						self.hide_captcha();
					}
					else
						self.show_errors(data.errors);
				},
				function(jqXHR, textStatus, errorThrown){
					self.enable_buttons();
					alert(errorThrown);
				}
			);
		}
		this.submit_post = function(){
			this.disable_buttons('submit');
			this.make_request(
				this.serialize('submit'),
				function(data){
					self.enable_buttons();
					if(data.success){
						self.hide_tips();
						self.show_success(data.errors, true);
						self.update_id(data.post_id);
						self.save_button.hide();
						if(data.redirect_url)
							window.location = data.redirect_url;

						// Reset the captcha
						self.hide_captcha();
					}
					else
						self.show_errors(data.errors);
				},
				function(jqXHR, textStatus, errorThrown){
					self.enable_buttons();
					alert(errorThrown);
				}
			);
		}
		//Fetches the thumb via ajax and loads it in the form.
		this.load_thumb = function(thumb_id){
			this.thumb_id_field.val(thumb_id);
			this.thumb_id_field.valid();
			this.make_request(
				{ action: 'wpfepp_get_thumbnail', id: thumb_id },
				function(data){
					if(data.success){
						self.thumb_container.html(data.image);
					}
				},
				function(jqXHR, textStatus, errorThrown){
					alert(errorThrown);
				}
			);
		}
		
		//Removes the thumb and resets the value of the hidden field.
		this.reset_thumb = function() {
			this.thumb_id_field.val('-1');
			this.thumb_container.html('');
		}
		this.continue_editing = function(){
			this.form_message.hide();
			this.fields_container.fadeIn();
		}
		
		//Fetches the external pictures via ajax and loads it in the form.
		this.load_pictures = function(ext_url,imageItems){
			if(this.offer_product_url.valid()==0)
				return;
			imageItems.html('<p class="blink">'+ wpfepp.parsing +'</p>');
			this.make_request(
				{ action: 'wpfepp_get_parser_thumbnail', ext_url: ext_url },
				function(data){
					if(data.success){
						if(data.pictures=='no_url'){
							imageItems.html('');
						}else if(data.pictures.length===0){
							imageItems.html('<p style="color:red">'+ wpfepp.noselectimg +'</p>');
						}else{
							preloadImages(data);
						}
					}else{
						imageItems.html('<p style="color:red">'+ data.errors +'</p>');
					}
				},
				function(jqXHR, textStatus, errorThrown){
					alert(errorThrown);
				}
			);
		}
	}; 
	// end WPFEPP_Form function

	$('.wpfepp-save-button').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		wpfepp_forms.get( $(this).closest('.wpfepp-form') ).save_draft();
	});
		
	$('.wpfepp-thumbnail-link').click(function(e){
		e.preventDefault();
		var clicked = $(this);
		custom_uploader = wp.media.frames.file_frame = wp.media({
            title: wpfepp.chooseimg,
            button: {
                text: wpfepp.chooseimg
            },
            multiple: false
        });
		custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            wpfepp_forms.get( $(clicked).closest('.wpfepp-form') ).load_thumb(attachment.id);
        });
		custom_uploader.open();
		clicked.siblings(".wpfepp-thumbnail-close").show();
	});

	$('.wpfepp-thumbnail-close').click(function(e){
		e.preventDefault();
		wpfepp_forms.get( $(this).closest('.wpfepp-form') ).reset_thumb();
		$(this).hide();
	});

	$('body').on('click', '.wpfepp-continue-editing', function(e){
		e.preventDefault();
		wpfepp_forms.get( $(this).closest('.wpfepp-form') ).continue_editing();
	});

	$('.wpfepp-row .post-delete a').click(function(e){
		e.preventDefault();
		var deletion_link = $(this);
		if( deletion_link.hasClass('processing-req') ) return;
		deletion_link.addClass('processing-req');
		var icon = deletion_link.children('span.dashicons');
		icon.removeClass('dashicons-trash').addClass('dashicons-update');
		var row = deletion_link.closest('tr.wpfepp-row');

		var _post_id = $(this).siblings('.post-id').first().val();
		var _nonce = $(this).siblings('#wpfepp-delete-post-'+_post_id+'-nonce').first().val();
		$.ajax({
			type:'POST',
			dataType: 'json',
			url: wpfepp.ajaxurl,
			data: {
				action: 'wpfepp_delete_post',
				post_id: _post_id,
				delete_nonce: _nonce
			},
			success: function(data,textStatus,XMLHttpRequest){
				icon.removeClass('dashicons-update').addClass('dashicons-trash');
				deletion_link.removeClass('processing-req');
				var message_container = $('.wpfepp-posts .wpfepp-message');
				if(data.success){
					row.slideUp('slow').remove();
					message_container.removeClass('error').addClass('success');
				}
				else
					message_container.removeClass('success').addClass('error');

				message_container.html(data.message).fadeIn();
				if( message_container.offset().top < $(window).scrollTop() ){
					$('html, body').animate({ scrollTop: message_container.offset().top-10 }, 'slow');
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
	});

	// Apply a calendar to Date field
	$('.wpfepp-form-field-date').pickmeup({
    	format  : 'Y-m-d',
    	hide_on_select : true
	});
	
	// Apply Select2 to taxonomy select tag
	if($('.wpfepp-hierarchical-taxonomy-field').length > 0){
		$('.wpfepp-hierarchical-taxonomy-field').select2();
	}
	
	//Google map options
	if ( "undefined" !== typeof wpfeppl && '1' == wpfeppl.enable_map ) {
		if ( '1' == wpfeppl.enable_city_suggest ) {
			jQuery( '#wpfepp_map_start_location' ).geo_tag_text();
		}
		jQuery( '#wpfepp_map_start_location' ).mapify({
			mapHeight : '200px',
			startGeoLat : wpfeppl.start_geo_lat, 
			startGeoLng : wpfeppl.start_geo_long, 
			latInputId : 'wpfepp_start_geo_lat', 
			lngInputId : 'wpfepp_start_geo_long'
		});
		jQuery(document).on('change', '#wpfepp_map_start_location', function() {
			var changemapfield = jQuery('#wpfepp_map_start_location').val();
			jQuery('#rh_map_hidden_adress').val(changemapfield);
		});
	}

	// Custom field File Upload
	$('.wpfepp-image-url-button').on('click', function(e){
		e.preventDefault();
		$(this).customUploaderOpen({field_id:'.wpfepp-form-field'});
		return false;
	});
	
	// Downlaodable product options
	$( '.wpfepp-product_options-field-container' ).on( 'click','.wpfepp-downloadable_files-container a.insert', function() {
		$( this ).closest( '.wpfepp-downloadable_files-container' ).find( 'tbody' ).append( $( this ).data( 'row' ) );
		$('.wpfepp-file-url-button').on('click', function(e){
			e.preventDefault();
			$(this).customUploaderOpen({field_id:'.wpfepp-product_options-url-field'});
		});
		return false;
	});
	
	$( '.wpfepp-product_options-field-container' ).on( 'click','.wpfepp-downloadable_files-container a.delete',function() {
		$( this ).closest( 'tr' ).remove();
		return false;
	});
	
	// Image Parsing functions
	var iCountEvents = 0;
	var iParsedEvents = 0;
	var imageArray;
	var inputParser = $('input[name|="parser_rehub_offer_product_url"]');
	var imageItems = $('.wpfepp-form-image-items');
	var thumbnailCont = $('.wpfepp-thumbnail-field-container');
	
	$('input[name|="rehub_offer_product_url"]').on('input', function(e){
		e.preventDefault();
		var inputURL = $(this);
		if(inputParser.length==1){
			var ext_url = inputURL.val();
			wpfepp_forms.get( $(inputURL).closest('.wpfepp-form') ).load_pictures(ext_url,imageItems);
		}
	});

	function preloadImages(data) {
		imageArray = [];
		if(data.pictures.length===0) {
			return;
		}
		$(data.pictures).each(function () {
			var imageLoads = new Image();
			imageLoads.onload = parseImage;
			imageLoads.onerror = function (e) {
				iCountEvents--;
			};
			imageLoads.src = this;
			iCountEvents++;
		});
	}
		
	function parseImage(e) {
		iParsedEvents++;
		var image = this;
		var img_size = inputParser.data('img-size');
		var withRatio = image.width / image.height;
		var heightRatio = image.height / image.width;
		if ((image.width >= img_size || image.height >= img_size) && (withRatio < 1.8 && heightRatio < 1.8)) {
			imageArray.push(image);
		}
		if (imageArray.length == 10 || iParsedEvents >= iCountEvents) {
			setImages(imageArray);
			return;
		}
	}

	function setImages(imageArray) {
		if (imageArray.length === 0) {
			imageItems.html('<p style="color:red">'+ wpfepp.noselectimg +'</p>');
			return;
		}
		var i = 0;
		if(thumbnailCont.length==1){
			selectMessage = wpfepp.selectimg+' '+wpfepp.orselectimg;
		}else{
			selectMessage =wpfepp.selectimg;
		}
		var image_block = '<p>'+ selectMessage +'</p>';
		$.each(imageArray, function (key, value) {
			selected = "";
			image_block += '<div class="wpfepp-form-image-item ' + selected + '"><img src="' + value.src + '" alt="" data-key="' + key + '"></div>';
			i++;
		});
		imageItems.html(image_block);
	}

	$('.wpfepp-form-image-items').on('click', '.wpfepp-form-image-item', function(e){
		$('.wpfepp-form-image-item').removeClass('selected');
		$(this).addClass('selected');
		var parserURL = $(this).find('img').attr('src');
		inputParser.val(parserURL);
		return false;
	});
	// End Image Parsing functions
	
}); 
// End Document.ready

// WP Media Uploader
(function($) {
	var defaults = { field_id:'.wpfepp-url-field' };

    var options;
	
	$.fn.customUploaderOpen = function (params) {
		options = $.extend({}, defaults, options, params);
		
			var clicked = $(this);
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: wpfepp.choosefile,
				button: {
					text: wpfepp.choosefile
				},
				multiple: false
			});
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				var fieldContainer = clicked.parent().prev(); // chooses the previous div container with the input field oject
				var unputField = fieldContainer.find(options.field_id);
				
				if(fieldContainer.hasClass("wpfepp-attid")) {
					unputField.val(attachment.id);
				} else {
					unputField.val(attachment.url);
				}
			});
			custom_uploader.open();
			return this;
	}
})(jQuery);