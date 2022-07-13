!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self).Mustache=t()}(this,(function(){"use strict";var e=Object.prototype.toString,t=Array.isArray||function(t){return"[object Array]"===e.call(t)};function n(e){return"function"==typeof e}function r(e){return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}function i(e,t){return null!=e&&"object"==typeof e&&t in e}var o=RegExp.prototype.test;var a=/\S/;function s(e){return!function(e,t){return o.call(e,t)}(a,e)}var c={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;","`":"&#x60;","=":"&#x3D;"};var p=/\s*/,u=/\s+/,l=/\s*=/,h=/\s*\}/,f=/#|\^|\/|>|\{|&|=|!/;function g(e){this.string=e,this.tail=e,this.pos=0}function d(e,t){this.view=e,this.cache={".":this.view},this.parent=t}function v(){this.templateCache={_cache:{},set:function(e,t){this._cache[e]=t},get:function(e){return this._cache[e]},clear:function(){this._cache={}}}}g.
prototype.eos=function(){return""===this.tail},g.prototype.scan=function(e){var t=this.tail.match(e);if(!t||0!==t.index)return"";var n=t[0];return this.tail=this.tail.substring(n.length),this.pos+=n.length,n},g.prototype.scanUntil=function(e){var t,n=this.tail.search(e);switch(n){case-1:t=this.tail,this.tail="";break;case 0:t="";break;default:t=this.tail.substring(0,n),this.tail=this.tail.substring(n)}return this.pos+=t.length,t},d.prototype.push=function(e){return new d(e,this)},d.prototype.lookup=function(e){var t,r,o,a=this.cache;if(a.hasOwnProperty(e))t=a[e];else{for(var s,c,p,u=this,l=!1;u;){if(e.indexOf(".")>0)for(s=u.view,c=e.split("."),p=0;null!=s&&p<c.length;)p===c.length-1&&(l=i(s,c[p])||(r=s,o=c[p],null!=r&&"object"!=typeof r&&r.hasOwnProperty&&r.hasOwnProperty(o))),s=s[c[p++]];else s=u.view[e],l=i(u.view,e);if(l){t=s;break}u=u.parent}a[e]=t}return n(t)&&(t=t.call(this.view)),t},v.prototype.clearCache=function(){void 0!==this.templateCache&&this.templateCache.clear()},v.
prototype.parse=function(e,n){var i=this.templateCache,o=e+":"+(n||y.tags).join(":"),a=void 0!==i,c=a?i.get(o):void 0;return null==c&&(c=function(e,n){if(!e)return[];var i,o,a,c=!1,d=[],v=[],w=[],m=!1,b=!1,C="",k=0;function x(){if(m&&!b)for(;w.length;)delete v[w.pop()];else w=[];m=!1,b=!1}function E(e){if("string"==typeof e&&(e=e.split(u,2)),!t(e)||2!==e.length)throw new Error("Invalid tags: "+e);i=new RegExp(r(e[0])+"\\s*"),o=new RegExp("\\s*"+r(e[1])),a=new RegExp("\\s*"+r("}"+e[1]))}E(n||y.tags);for(var T,j,U,S,P,V,O=new g(e);!O.eos();){if(T=O.pos,U=O.scanUntil(i))for(var A=0,I=U.length;A<I;++A)s(S=U.charAt(A))?(w.push(v.length),C+=S):(b=!0,c=!0,C+=" "),v.push(["text",S,T,T+1]),T+=1,"\n"===S&&(x(),C="",k=0,c=!1);if(!O.scan(i))break;if(m=!0,j=O.scan(f)||"name",O.scan(p),"="===j?(U=O.scanUntil(l),O.scan(l),O.scanUntil(o)):"{"===j?(U=O.scanUntil(a),O.scan(h),O.scanUntil(o),j="&"):U=O.scanUntil(o),!O.scan(o))throw new Error("Unclosed tag at "+O.pos);if(P=">"==j?[j,U,T,O.pos,C,k,c]:[j,U,
T,O.pos],k++,v.push(P),"#"===j||"^"===j)d.push(P);else if("/"===j){if(!(V=d.pop()))throw new Error('Unopened section "'+U+'" at '+T);if(V[1]!==U)throw new Error('Unclosed section "'+V[1]+'" at '+T)}else"name"===j||"{"===j||"&"===j?b=!0:"="===j&&E(U)}if(x(),V=d.pop())throw new Error('Unclosed section "'+V[1]+'" at '+O.pos);return function(e){for(var t,n=[],r=n,i=[],o=0,a=e.length;o<a;++o)switch((t=e[o])[0]){case"#":case"^":r.push(t),i.push(t),r=t[4]=[];break;case"/":i.pop()[5]=t[2],r=i.length>0?i[i.length-1][4]:n;break;default:r.push(t)}return n}(function(e){for(var t,n,r=[],i=0,o=e.length;i<o;++i)(t=e[i])&&("text"===t[0]&&n&&"text"===n[0]?(n[1]+=t[1],n[3]=t[3]):(r.push(t),n=t));return r}(v))}(e,n),a&&i.set(o,c)),c},v.prototype.render=function(e,t,n,r){var i=this.getConfigTags(r),o=this.parse(e,i),a=t instanceof d?t:new d(t,void 0);return this.renderTokens(o,a,n,e,r)},v.prototype.renderTokens=function(e,t,n,r,i){for(var o,a,s,c="",p=0,u=e.length;p<u;++p)s=void 0,"#"===(a=(o=e[p])[0])?s=
this.renderSection(o,t,n,r,i):"^"===a?s=this.renderInverted(o,t,n,r,i):">"===a?s=this.renderPartial(o,t,n,i):"&"===a?s=this.unescapedValue(o,t):"name"===a?s=this.escapedValue(o,t,i):"text"===a&&(s=this.rawValue(o)),void 0!==s&&(c+=s);return c},v.prototype.renderSection=function(e,r,i,o,a){var s=this,c="",p=r.lookup(e[1]);if(p){if(t(p))for(var u=0,l=p.length;u<l;++u)c+=this.renderTokens(e[4],r.push(p[u]),i,o,a);else if("object"==typeof p||"string"==typeof p||"number"==typeof p)c+=this.renderTokens(e[4],r.push(p),i,o,a);else if(n(p)){if("string"!=typeof o)throw new Error("Cannot use higher-order sections without the original template");null!=(p=p.call(r.view,o.slice(e[3],e[5]),(function(e){return s.render(e,r,i,a)})))&&(c+=p)}else c+=this.renderTokens(e[4],r,i,o,a);return c}},v.prototype.renderInverted=function(e,n,r,i,o){var a=n.lookup(e[1]);if(!a||t(a)&&0===a.length)return this.renderTokens(e[4],n,r,i,o)},v.prototype.indentPartial=function(e,t,n){for(var r=t.replace(/[^ \t]/g,""),i=e.
split("\n"),o=0;o<i.length;o++)i[o].length&&(o>0||!n)&&(i[o]=r+i[o]);return i.join("\n")},v.prototype.renderPartial=function(e,t,r,i){if(r){var o=this.getConfigTags(i),a=n(r)?r(e[1]):r[e[1]];if(null!=a){var s=e[6],c=e[5],p=e[4],u=a;0==c&&p&&(u=this.indentPartial(a,p,s));var l=this.parse(u,o);return this.renderTokens(l,t,r,u,i)}}},v.prototype.unescapedValue=function(e,t){var n=t.lookup(e[1]);if(null!=n)return n},v.prototype.escapedValue=function(e,t,n){var r=this.getConfigEscape(n)||y.escape,i=t.lookup(e[1]);if(null!=i)return"number"==typeof i&&r===y.escape?String(i):r(i)},v.prototype.rawValue=function(e){return e[1]},v.prototype.getConfigTags=function(e){return t(e)?e:e&&"object"==typeof e?e.tags:void 0},v.prototype.getConfigEscape=function(e){return e&&"object"==typeof e&&!t(e)?e.escape:void 0};var y={name:"mustache.js",version:"4.2.0",tags:["{{","}}"],clearCache:void 0,escape:void 0,parse:void 0,render:void 0,Scanner:void 0,Context:void 0,Writer:void 0,set templateCache(e){w.
templateCache=e},get templateCache(){return w.templateCache}},w=new v;return y.clearCache=function(){return w.clearCache()},y.parse=function(e,t){return w.parse(e,t)},y.render=function(e,n,r,i){if("string"!=typeof e)throw new TypeError('Invalid template! Template should be a "string" but "'+((t(o=e)?"array":typeof o)+'" was given as the first argument for mustache#render(template, view, partials)'));var o;return w.render(e,n,r,i)},y.escape=function(e){return String(e).replace(/[&<>"'`=\/]/g,(function(e){return c[e]}))},y.Scanner=g,y.Context=d,y.Writer=v,y}));!function(e,t,n){function r(e,t){return typeof e===t}function o(){var e,t,n,o,a,i,s;for(var c in w)if(w.hasOwnProperty(c)){if(e=[],t=w[c],t.name&&(e.push(t.name.toLowerCase()),t.options&&t.options.aliases&&t.options.aliases.length))for(n=0;n<t.options.aliases.length;n++)e.push(t.options.aliases[n].toLowerCase());for(o=r(t.fn,"function")?t.fn():t.fn,a=0;a<e.length;a++)i=e[a],s=i.split("."),1===s.length?Modernizr[s[0]]=o:(!Modernizr[
s[0]]||Modernizr[s[0]]instanceof Boolean||(Modernizr[s[0]]=new Boolean(Modernizr[s[0]])),Modernizr[s[0]][s[1]]=o),x.push((o?"":"no-")+s.join("-"))}}function a(e){var t=k.className,n=Modernizr._config.classPrefix||"";if(P&&(t=t.baseVal),Modernizr._config.enableJSClass){var r=new RegExp("(^|\\s)"+n+"no-js(\\s|$)");t=t.replace(r,"$1"+n+"js$2")}Modernizr._config.enableClasses&&(t+=" "+n+e.join(" "+n),P?k.className.baseVal=t:k.className=t)}function i(){return"function"!=typeof t.createElement?t.createElement(arguments[0]):P?t.createElementNS.call(t,"http://www.w3.org/2000/svg",arguments[0]):t.createElement.apply(t,arguments)}function s(e,t){return!!~(""+e).indexOf(t)}function c(){var e=t.body;return e||(e=i(P?"svg":"body"),e.fake=!0),e}function l(e,n,r,o){var a,s,l,d,u="modernizr",f=i("div"),p=c();if(parseInt(r,10))for(;r--;)l=i("div"),l.id=o?o[r]:u+(r+1),f.appendChild(l);return a=i("style"),a.type="text/css",a.id="s"+u,(p.fake?p:f).appendChild(a),p.appendChild(f),a.styleSheet?a.styleSheet.
cssText=e:a.appendChild(t.createTextNode(e)),f.id=u,p.fake&&(p.style.background="",p.style.overflow="hidden",d=k.style.overflow,k.style.overflow="hidden",k.appendChild(p)),s=n(f,e),p.fake?(p.parentNode.removeChild(p),k.style.overflow=d,k.offsetHeight):f.parentNode.removeChild(f),!!s}function d(e){return e.replace(/([a-z])-([a-z])/g,function(e,t,n){return t+n.toUpperCase()}).replace(/^-/,"")}function u(e,t){if("object"==typeof e)for(var n in e)q(e,n)&&u(n,e[n]);else{e=e.toLowerCase();var r=e.split("."),o=Modernizr[r[0]];if(2==r.length&&(o=o[r[1]]),"undefined"!=typeof o)return Modernizr;t="function"==typeof t?t():t,1==r.length?Modernizr[r[0]]=t:(!Modernizr[r[0]]||Modernizr[r[0]]instanceof Boolean||(Modernizr[r[0]]=new Boolean(Modernizr[r[0]])),Modernizr[r[0]][r[1]]=t),a([(t&&0!=t?"":"no-")+r.join("-")]),Modernizr._trigger(e,t)}return Modernizr}function f(e,t){return function(){return e.apply(t,arguments)}}function p(e,t,n){var o;for(var a in e)if(e[a]in t)return n===!1?e[a]:(o=t[e[a]],r(
o,"function")?f(o,n||t):o);return!1}function m(e){return e.replace(/([A-Z])/g,function(e,t){return"-"+t.toLowerCase()}).replace(/^ms-/,"-ms-")}function g(t,n,r){var o;if("getComputedStyle"in e){o=getComputedStyle.call(e,t,n);var a=e.console;if(null!==o)r&&(o=o.getPropertyValue(r));else if(a){var i=a.error?"error":"log";a[i].call(a,"getComputedStyle returning null, its possible modernizr test results are inaccurate")}}else o=!n&&t.currentStyle&&t.currentStyle[r];return o}function h(t,r){var o=t.length;if("CSS"in e&&"supports"in e.CSS){for(;o--;)if(e.CSS.supports(m(t[o]),r))return!0;return!1}if("CSSSupportsRule"in e){for(var a=[];o--;)a.push("("+m(t[o])+":"+r+")");return a=a.join(" or "),l("@supports ("+a+") { #modernizr { position: absolute; } }",function(e){return"absolute"==g(e,null,"position")})}return n}function v(e,t,o,a){function c(){u&&(delete G.style,delete G.modElem)}if(a=r(a,"undefined")?!1:a,!r(o,"undefined")){var l=h(e,o);if(!r(l,"undefined"))return l}for(var u,f,p,m,g,v=[
"modernizr","tspan","samp"];!G.style&&v.length;)u=!0,G.modElem=i(v.shift()),G.style=G.modElem.style;for(p=e.length,f=0;p>f;f++)if(m=e[f],g=G.style[m],s(m,"-")&&(m=d(m)),G.style[m]!==n){if(a||r(o,"undefined"))return c(),"pfx"==t?m:!0;try{G.style[m]=o}catch(y){}if(G.style[m]!=g)return c(),"pfx"==t?m:!0}return c(),!1}function y(e,t,n,o,a){var i=e.charAt(0).toUpperCase()+e.slice(1),s=(e+" "+U.join(i+" ")+i).split(" ");return r(t,"string")||r(t,"undefined")?v(s,t,o,a):(s=(e+" "+O.join(i+" ")+i).split(" "),p(s,t,n))}function b(e,t,r){return y(e,n,n,t,r)}function T(e,t){var n=e.deleteDatabase(t);n.onsuccess=function(){u("indexeddb.deletedatabase",!0)},n.onerror=function(){u("indexeddb.deletedatabase",!1)}}var x=[],w=[],S={_version:"3.6.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,t){var n=this;setTimeout(function(){t(n[e])},0)},addTest:function(e,t,n){w.push({name:e,fn:t,options:n})},addAsyncTest:function(e){w.push({name:null,fn:e})}},
Modernizr=function(){};Modernizr.prototype=S,Modernizr=new Modernizr,Modernizr.addTest("applicationcache","applicationCache"in e),Modernizr.addTest("geolocation","geolocation"in navigator),Modernizr.addTest("history",function(){var t=navigator.userAgent;return-1===t.indexOf("Android 2.")&&-1===t.indexOf("Android 4.0")||-1===t.indexOf("Mobile Safari")||-1!==t.indexOf("Chrome")||-1!==t.indexOf("Windows Phone")||"file:"===location.protocol?e.history&&"pushState"in e.history:!1}),Modernizr.addTest("postmessage","postMessage"in e),Modernizr.addTest("svg",!!t.createElementNS&&!!t.createElementNS("http://www.w3.org/2000/svg","svg").createSVGRect);var C=!1;try{C="WebSocket"in e&&2===e.WebSocket.CLOSING}catch(E){}Modernizr.addTest("websockets",C),Modernizr.addTest("localstorage",function(){var e="modernizr";try{return localStorage.setItem(e,e),localStorage.removeItem(e),!0}catch(t){return!1}}),Modernizr.addTest("sessionstorage",function(){var e="modernizr";try{return sessionStorage.setItem(e,e)
,sessionStorage.removeItem(e),!0}catch(t){return!1}}),Modernizr.addTest("websqldatabase","openDatabase"in e),Modernizr.addTest("webworkers","Worker"in e);var _=S._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];S._prefixes=_;var k=t.documentElement,P="svg"===k.nodeName.toLowerCase();P||!function(e,t){function n(e,t){var n=e.createElement("p"),r=e.getElementsByTagName("head")[0]||e.documentElement;return n.innerHTML="x<style>"+t+"</style>",r.insertBefore(n.lastChild,r.firstChild)}function r(){var e=b.elements;return"string"==typeof e?e.split(" "):e}function o(e,t){var n=b.elements;"string"!=typeof n&&(n=n.join(" ")),"string"!=typeof e&&(e=e.join(" ")),b.elements=n+" "+e,l(t)}function a(e){var t=y[e[h]];return t||(t={},v++,e[h]=v,y[v]=t),t}function i(e,n,r){if(n||(n=t),u)return n.createElement(e);r||(r=a(n));var o;return o=r.cache[e]?r.cache[e].cloneNode():g.test(e)?(r.cache[e]=r.createElem(e)).cloneNode():r.createElem(e),!o.canHaveChildren||m.test(e)||o.tagUrn?o:r.frag
.appendChild(o)}function s(e,n){if(e||(e=t),u)return e.createDocumentFragment();n=n||a(e);for(var o=n.frag.cloneNode(),i=0,s=r(),c=s.length;c>i;i++)o.createElement(s[i]);return o}function c(e,t){t.cache||(t.cache={},t.createElem=e.createElement,t.createFrag=e.createDocumentFragment,t.frag=t.createFrag()),e.createElement=function(n){return b.shivMethods?i(n,e,t):t.createElem(n)},e.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+r().join().replace(/[\w\-:]+/g,function(e){return t.createElem(e),t.frag.createElement(e),'c("'+e+'")'})+");return n}")(b,t.frag)}function l(e){e||(e=t);var r=a(e);return!b.shivCSS||d||r.hasCSS||(r.hasCSS=!!n(e,"article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}")),u||c(e,r),e}var d,u,f="3.7.3",p=e.html5||{},m=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,g=
/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,h="_html5shiv",v=0,y={};!function(){try{var e=t.createElement("a");e.innerHTML="<xyz></xyz>",d="hidden"in e,u=1==e.childNodes.length||function(){t.createElement("a");var e=t.createDocumentFragment();return"undefined"==typeof e.cloneNode||"undefined"==typeof e.createDocumentFragment||"undefined"==typeof e.createElement}()}catch(n){d=!0,u=!0}}();var b={elements:p.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output picture progress section summary template time video",version:f,shivCSS:p.shivCSS!==!1,supportsUnknownElements:u,shivMethods:p.shivMethods!==!1,type:"default",shivDocument:l,createElement:i,createDocumentFragment:s,addElements:o};e.html5=b,l(t),"object"==typeof module&&module.exports&&(module.exports=b)}("undefined"!=typeof e?e:this,t);var N="Moz O ms Webkit",O=S._config.usePrefixes
?N.toLowerCase().split(" "):[];S._domPrefixes=O;var z=function(){function e(e,t){var o;return e?(t&&"string"!=typeof t||(t=i(t||"div")),e="on"+e,o=e in t,!o&&r&&(t.setAttribute||(t=i("div")),t.setAttribute(e,""),o="function"==typeof t[e],t[e]!==n&&(t[e]=n),t.removeAttribute(e)),o):!1}var r=!("onblur"in t.documentElement);return e}();S.hasEvent=z,Modernizr.addTest("hashchange",function(){return z("hashchange",e)===!1?!1:t.documentMode===n||t.documentMode>7}),Modernizr.addTest("audio",function(){var e=i("audio"),t=!1;try{t=!!e.canPlayType,t&&(t=new Boolean(t),t.ogg=e.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/,""),t.mp3=e.canPlayType('audio/mpeg; codecs="mp3"').replace(/^no$/,""),t.opus=e.canPlayType('audio/ogg; codecs="opus"')||e.canPlayType('audio/webm; codecs="opus"').replace(/^no$/,""),t.wav=e.canPlayType('audio/wav; codecs="1"').replace(/^no$/,""),t.m4a=(e.canPlayType("audio/x-m4a;")||e.canPlayType("audio/aac;")).replace(/^no$/,""))}catch(n){}return t}),Modernizr.
addTest("canvas",function(){var e=i("canvas");return!(!e.getContext||!e.getContext("2d"))}),Modernizr.addTest("canvastext",function(){return Modernizr.canvas===!1?!1:"function"==typeof i("canvas").getContext("2d").fillText}),Modernizr.addTest("video",function(){var e=i("video"),t=!1;try{t=!!e.canPlayType,t&&(t=new Boolean(t),t.ogg=e.canPlayType('video/ogg; codecs="theora"').replace(/^no$/,""),t.h264=e.canPlayType('video/mp4; codecs="avc1.42E01E"').replace(/^no$/,""),t.webm=e.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,""),t.vp9=e.canPlayType('video/webm; codecs="vp9"').replace(/^no$/,""),t.hls=e.canPlayType('application/x-mpegURL; codecs="avc1.42E01E"').replace(/^no$/,""))}catch(n){}return t}),Modernizr.addTest("webgl",function(){var t=i("canvas"),n="probablySupportsContext"in t?"probablySupportsContext":"supportsContext";return n in t?t[n]("webgl")||t[n]("experimental-webgl"):"WebGLRenderingContext"in e}),Modernizr.addTest("cssgradients",function(){for(var e,t=
"background-image:",n="gradient(linear,left top,right bottom,from(#9f9),to(white));",r="",o=0,a=_.length-1;a>o;o++)e=0===o?"to ":"",r+=t+_[o]+"linear-gradient("+e+"left top, #9f9, white);";Modernizr._config.usePrefixes&&(r+=t+"-webkit-"+n);var s=i("a"),c=s.style;return c.cssText=r,(""+c.backgroundImage).indexOf("gradient")>-1}),Modernizr.addTest("multiplebgs",function(){var e=i("a").style;return e.cssText="background:url(https://),url(https://),red url(https://)",/(url\s*\(.*?){3}/.test(e.background)}),Modernizr.addTest("opacity",function(){var e=i("a").style;return e.cssText=_.join("opacity:.55;"),/^0.55$/.test(e.opacity)}),Modernizr.addTest("rgba",function(){var e=i("a").style;return e.cssText="background-color:rgba(150,255,150,.5)",(""+e.backgroundColor).indexOf("rgba")>-1}),Modernizr.addTest("inlinesvg",function(){var e=i("div");return e.innerHTML="<svg/>","http://www.w3.org/2000/svg"==("undefined"!=typeof SVGRect&&e.firstChild&&e.firstChild.namespaceURI)});var R=i("input"),A=
"autocomplete autofocus list placeholder max min multiple pattern required step".split(" "),M={};Modernizr.input=function(t){for(var n=0,r=t.length;r>n;n++)M[t[n]]=!!(t[n]in R);return M.list&&(M.list=!(!i("datalist")||!e.HTMLDataListElement)),M}(A);var $="search tel url email datetime date month week time datetime-local number range color".split(" "),B={};Modernizr.inputtypes=function(e){for(var r,o,a,i=e.length,s="1)",c=0;i>c;c++)R.setAttribute("type",r=e[c]),a="text"!==R.type&&"style"in R,a&&(R.value=s,R.style.cssText="position:absolute;visibility:hidden;",/^range$/.test(r)&&R.style.WebkitAppearance!==n?(k.appendChild(R),o=t.defaultView,a=o.getComputedStyle&&"textfield"!==o.getComputedStyle(R,null).WebkitAppearance&&0!==R.offsetHeight,k.removeChild(R)):/^(search|tel)$/.test(r)||(a=/^(url|email)$/.test(r)?R.checkValidity&&R.checkValidity()===!1:R.value!=s)),B[e[c]]=!!a;return B}($),Modernizr.addTest("hsla",function(){var e=i("a").style;return e.cssText=
"background-color:hsla(120,40%,100%,.5)",s(e.backgroundColor,"rgba")||s(e.backgroundColor,"hsla")});var j="CSS"in e&&"supports"in e.CSS,L="supportsCSS"in e;Modernizr.addTest("supports",j||L);var D={}.toString;Modernizr.addTest("svgclippaths",function(){return!!t.createElementNS&&/SVGClipPath/.test(D.call(t.createElementNS("http://www.w3.org/2000/svg","clipPath")))}),Modernizr.addTest("smil",function(){return!!t.createElementNS&&/SVGAnimate/.test(D.call(t.createElementNS("http://www.w3.org/2000/svg","animate")))});var F=function(){var t=e.matchMedia||e.msMatchMedia;return t?function(e){var n=t(e);return n&&n.matches||!1}:function(t){var n=!1;return l("@media "+t+" { #modernizr { position: absolute; } }",function(t){n="absolute"==(e.getComputedStyle?e.getComputedStyle(t,null):t.currentStyle).position}),n}}();S.mq=F;var I=S.testStyles=l,W=function(){var e=navigator.userAgent,t=e.match(/w(eb)?osbrowser/gi),n=e.match(/windows phone/gi)&&e.match(/iemobile\/([0-9])+/gi)&&parseFloat(RegExp.$1)
>=9;return t||n}();W?Modernizr.addTest("fontface",!1):I('@font-face {font-family:"font";src:url("https://")}',function(e,n){var r=t.getElementById("smodernizr"),o=r.sheet||r.styleSheet,a=o?o.cssRules&&o.cssRules[0]?o.cssRules[0].cssText:o.cssText||"":"",i=/src/i.test(a)&&0===a.indexOf(n.split(" ")[0]);Modernizr.addTest("fontface",i)}),I('#modernizr{font:0/0 a}#modernizr:after{content:":)";visibility:hidden;font:7px/1 a}',function(e){Modernizr.addTest("generatedcontent",e.offsetHeight>=6)}),Modernizr.addTest("touchevents",function(){var n;if("ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch)n=!0;else{var r=["@media (",_.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");I(r,function(e){n=9===e.offsetTop})}return n});var U=S._config.usePrefixes?N.split(" "):[];S._cssomPrefixes=U;var V=function(t){var r,o=_.length,a=e.CSSRule;if("undefined"==typeof a)return n;if(!t)return!1;if(t=t.replace(/^@/,""),r=t.replace(/-/g,"_").toUpperCase()+
"_RULE",r in a)return"@"+t;for(var i=0;o>i;i++){var s=_[i],c=s.toUpperCase()+"_"+r;if(c in a)return"@-"+s.toLowerCase()+"-"+t}return!1};S.atRule=V;var q;!function(){var e={}.hasOwnProperty;q=r(e,"undefined")||r(e.call,"undefined")?function(e,t){return t in e&&r(e.constructor.prototype[t],"undefined")}:function(t,n){return e.call(t,n)}}(),S._l={},S.on=function(e,t){this._l[e]||(this._l[e]=[]),this._l[e].push(t),Modernizr.hasOwnProperty(e)&&setTimeout(function(){Modernizr._trigger(e,Modernizr[e])},0)},S._trigger=function(e,t){if(this._l[e]){var n=this._l[e];setTimeout(function(){var e,r;for(e=0;e<n.length;e++)(r=n[e])(t)},0),delete this._l[e]}},Modernizr._q.push(function(){S.addTest=u});var H={elem:i("modernizr")};Modernizr._q.push(function(){delete H.elem});var G={style:H.elem.style};Modernizr._q.unshift(function(){delete G.style});var J=S.testProp=function(e,t,r){return v([e],n,t,r)};Modernizr.addTest("textshadow",J("textShadow","1px 1px")),S.testAllProps=y,S.testAllProps=b,Modernizr.
addTest("cssanimations",b("animationName","a",!0)),Modernizr.addTest("backgroundsize",b("backgroundSize","100%",!0)),Modernizr.addTest("borderimage",b("borderImage","url() 1",!0)),Modernizr.addTest("borderradius",b("borderRadius","0px",!0)),Modernizr.addTest("boxshadow",b("boxShadow","1px 1px",!0)),function(){Modernizr.addTest("csscolumns",function(){var e=!1,t=b("columnCount");try{e=!!t,e&&(e=new Boolean(e))}catch(n){}return e});for(var e,t,n=["Width","Span","Fill","Gap","Rule","RuleColor","RuleStyle","RuleWidth","BreakBefore","BreakAfter","BreakInside"],r=0;r<n.length;r++)e=n[r].toLowerCase(),t=b("column"+n[r]),("breakbefore"===e||"breakafter"===e||"breakinside"==e)&&(t=t||b(n[r])),Modernizr.addTest("csscolumns."+e,t)}(),Modernizr.addTest("flexbox",b("flexBasis","1px",!0)),Modernizr.addTest("flexboxlegacy",b("boxDirection","reverse",!0)),Modernizr.addTest("cssreflections",b("boxReflect","above",!0)),Modernizr.addTest("csstransforms",function(){return-1===navigator.userAgent.indexOf(
"Android 2.")&&b("transform","scale(1)",!0)}),Modernizr.addTest("csstransforms3d",function(){return!!b("perspective","1px",!0)}),Modernizr.addTest("csstransitions",b("transition","all",!0));var K=S.prefixed=function(e,t,n){return 0===e.indexOf("@")?V(e):(-1!=e.indexOf("-")&&(e=d(e)),t?y(e,t,n):y(e,"pfx"))};Modernizr.addAsyncTest(function(){var t;try{t=K("indexedDB",e)}catch(n){}if(t){var r="modernizr-"+Math.random(),o=t.open(r);o.onerror=function(){o.error&&"InvalidStateError"===o.error.name?u("indexeddb",!1):(u("indexeddb",!0),T(t,r))},o.onsuccess=function(){u("indexeddb",!0),T(t,r)}}else u("indexeddb",!1)}),Modernizr.addTest("forcetouch",function(){return z(K("mouseforcewillbegin",e,!1),e)?MouseEvent.WEBKIT_FORCE_AT_MOUSE_DOWN&&MouseEvent.WEBKIT_FORCE_AT_FORCE_MOUSE_DOWN:!1}),o(),a(x),delete S.addTest,delete S.addAsyncTest;for(var Z=0;Z<Modernizr._q.length;Z++)Modernizr._q[Z]();e.Modernizr=Modernizr}(window,document);!function(t){"use strict";var e='[data-toggle="dropdown"]',n=
function(e){t(e).on("click.bs.dropdown",this.toggle)};function o(e){var n=e.attr("data-target");n||(n=(n=e.attr("href"))&&/#[A-Za-z]/.test(n)&&n.replace(/.*(?=#[^\s]*$)/,""));var o="#"!==n?t(document).find(n):null;return o&&o.length?o:e.parent()}function r(n){n&&3===n.which||(t(".dropdown-backdrop").remove(),t(e).each((function(){var e=t(this),r=o(e),a={relatedTarget:this};r.hasClass("open")&&(n&&"click"==n.type&&/input|textarea/i.test(n.target.tagName)&&t.contains(r[0],n.target)||(r.trigger(n=t.Event("hide.bs.dropdown",a)),n.isDefaultPrevented()||(e.attr("aria-expanded","false"),r.removeClass("open").trigger(t.Event("hidden.bs.dropdown",a)))))})))}n.VERSION="3.4.1",n.prototype.toggle=function(e){var n=t(this);if(!n.is(".disabled, :disabled")){var a=o(n),d=a.hasClass("open");if(r(),!d){"ontouchstart"in document.documentElement&&!a.closest(".navbar-nav").length&&t(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(t(this)).on("click",r);var i={relatedTarget:this};
if(a.trigger(e=t.Event("show.bs.dropdown",i)),e.isDefaultPrevented())return;n.trigger("focus").attr("aria-expanded","true"),a.toggleClass("open").trigger(t.Event("shown.bs.dropdown",i))}return!1}},n.prototype.keydown=function(n){if(/(38|40|27|32)/.test(n.which)&&!/input|textarea/i.test(n.target.tagName)){var r=t(this);if(n.preventDefault(),n.stopPropagation(),!r.is(".disabled, :disabled")){var a=o(r),d=a.hasClass("open");if(!d&&27!=n.which||d&&27==n.which)return 27==n.which&&a.find(e).trigger("focus"),r.trigger("click");var i=a.find(".dropdown-menu li:not(.disabled):visible a");if(i.length){var s=i.index(n.target);38==n.which&&s>0&&s--,40==n.which&&s<i.length-1&&s++,~s||(s=0),i.eq(s).trigger("focus")}}}};var a=t.fn.dropdown;t.fn.dropdown=function(e){return this.each((function(){var o=t(this),r=o.data("bs.dropdown");r||o.data("bs.dropdown",r=new n(this)),"string"==typeof e&&r[e].call(o)}))},t.fn.dropdown.Constructor=n,t.fn.dropdown.noConflict=function(){return t.fn.dropdown=a,this},t(
document).on("click.bs.dropdown.data-api",r).on("click.bs.dropdown.data-api",".dropdown form",(function(t){t.stopPropagation()})).on("click.bs.dropdown.data-api",e,n.prototype.toggle).on("keydown.bs.dropdown.data-api",e,n.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",n.prototype.keydown)}(jQuery);!function(n){"use strict";n.fn.emulateTransitionEnd=function(t){var i=!1,r=this;n(this).one("bsTransitionEnd",(function(){i=!0}));return setTimeout((function(){i||n(r).trigger(n.support.transition.end)}),t),this},n((function(){n.support.transition=function(){var n=document.createElement("bootstrap"),t={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var i in t)if(void 0!==n.style[i])return{end:t[i]};return!1}(),n.support.transition&&(n.event.special.bsTransitionEnd={bindType:n.support.transition.end,delegateType:n.support.transition.end,handle:function(t){if(n(t.target).is(
this))return t.handleObj.handler.apply(this,arguments)}})}))}(jQuery);!function(t){"use strict";var a=function(a){this.element=t(a)};function e(e){return this.each((function(){var n=t(this),i=n.data("bs.tab");i||n.data("bs.tab",i=new a(this)),"string"==typeof e&&i[e]()}))}a.VERSION="3.4.1",a.TRANSITION_DURATION=150,a.prototype.show=function(){var a=this.element,e=a.closest("ul:not(.dropdown-menu)"),n=a.data("target");if(n||(n=(n=a.attr("href"))&&n.replace(/.*(?=#[^\s]*$)/,"")),!a.parent("li").hasClass("active")){var i=e.find(".active:last a"),r=t.Event("hide.bs.tab",{relatedTarget:a[0]}),s=t.Event("show.bs.tab",{relatedTarget:i[0]});if(i.trigger(r),a.trigger(s),!s.isDefaultPrevented()&&!r.isDefaultPrevented()){var d=t(document).find(n);this.activate(a.closest("li"),e),this.activate(d,d.parent(),(function(){i.trigger({type:"hidden.bs.tab",relatedTarget:a[0]}),a.trigger({type:"shown.bs.tab",relatedTarget:i[0]})}))}}},a.prototype.activate=function(e,n,i){var r=n.find("> .active"),s=i&&t.
support.transition&&(r.length&&r.hasClass("fade")||!!n.find("> .fade").length);function d(){r.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),e.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),s?(e[0].offsetWidth,e.addClass("in")):e.removeClass("fade"),e.parent(".dropdown-menu").length&&e.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),i&&i()}r.length&&s?r.one("bsTransitionEnd",d).emulateTransitionEnd(a.TRANSITION_DURATION):d(),r.removeClass("in")};var n=t.fn.tab;t.fn.tab=e,t.fn.tab.Constructor=a,t.fn.tab.noConflict=function(){return t.fn.tab=n,this};var i=function(a){a.preventDefault(),e.call(t(this),"show")};t(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',i).on("click.bs.tab.data-api",'[data-toggle="pill"]',i)}(jQuery);!function(t){"use strict";var e=function(e,i){this.options=i,this.$body=t(document.body),
this.$element=t(e),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.fixedContent=".navbar-fixed-top, .navbar-fixed-bottom",this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,t.proxy((function(){this.$element.trigger("loaded.bs.modal")}),this))};function i(i,o){return this.each((function(){var s=t(this),n=s.data("bs.modal"),a=t.extend({},e.DEFAULTS,s.data(),"object"==typeof i&&i);n||s.data("bs.modal",n=new e(this,a)),"string"==typeof i?n[i](o):a.show&&n.show(o)}))}e.VERSION="3.4.1",e.TRANSITION_DURATION=300,e.BACKDROP_TRANSITION_DURATION=150,e.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},e.prototype.toggle=function(t){return this.isShown?this.hide():this.show(t)},e.prototype.show=function(i){var o=this,s=t.Event("show.bs.modal",{relatedTarget:i});this.$element.trigger(s),this.isShown||s.isDefaultPrevented()||(this.isShown=!0,this.
checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',t.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",(function(){o.$element.one("mouseup.dismiss.bs.modal",(function(e){t(e.target).is(o.$element)&&(o.ignoreBackdropClick=!0)}))})),this.backdrop((function(){var s=t.support.transition&&o.$element.hasClass("fade");o.$element.parent().length||o.$element.appendTo(o.$body),o.$element.show().scrollTop(0),o.adjustDialog(),s&&o.$element[0].offsetWidth,o.$element.addClass("in"),o.enforceFocus();var n=t.Event("shown.bs.modal",{relatedTarget:i});s?o.$dialog.one("bsTransitionEnd",(function(){o.$element.trigger("focus").trigger(n)})).emulateTransitionEnd(e.TRANSITION_DURATION):o.$element.trigger("focus").trigger(n)})))},e.prototype.hide=function(i){i&&i.preventDefault(),i=t.Event("hide.bs.modal"),this.$element.trigger(i),this.isShown&&!i.isDefaultPrevented()&&(this.
isShown=!1,this.escape(),this.resize(),t(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),t.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",t.proxy(this.hideModal,this)).emulateTransitionEnd(e.TRANSITION_DURATION):this.hideModal())},e.prototype.enforceFocus=function(){t(document).off("focusin.bs.modal").on("focusin.bs.modal",t.proxy((function(t){document===t.target||this.$element[0]===t.target||this.$element.has(t.target).length||this.$element.trigger("focus")}),this))},e.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",t.proxy((function(t){27==t.which&&this.hide()}),this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},e.prototype.resize=function(){this.isShown?t(window).on("resize.bs.modal",t.proxy(this.handleUpdate,this)):t(window).off(
"resize.bs.modal")},e.prototype.hideModal=function(){var t=this;this.$element.hide(),this.backdrop((function(){t.$body.removeClass("modal-open"),t.resetAdjustments(),t.resetScrollbar(),t.$element.trigger("hidden.bs.modal")}))},e.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},e.prototype.backdrop=function(i){var o=this,s=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var n=t.support.transition&&s;if(this.$backdrop=t(document.createElement("div")).addClass("modal-backdrop "+s).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",t.proxy((function(t){this.ignoreBackdropClick?this.ignoreBackdropClick=!1:t.target===t.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide())}),this)),n&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!i)return;n?this.$backdrop.one("bsTransitionEnd",i).emulateTransitionEnd(e.BACKDROP_TRANSITION_DURATION):i()}else if(!this.
isShown&&this.$backdrop){this.$backdrop.removeClass("in");var a=function(){o.removeBackdrop(),i&&i()};t.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",a).emulateTransitionEnd(e.BACKDROP_TRANSITION_DURATION):a()}else i&&i()},e.prototype.handleUpdate=function(){this.adjustDialog()},e.prototype.adjustDialog=function(){var t=this.$element[0].scrollHeight>document.documentElement.clientHeight;this.$element.css({paddingLeft:!this.bodyIsOverflowing&&t?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!t?this.scrollbarWidth:""})},e.prototype.resetAdjustments=function(){this.$element.css({paddingLeft:"",paddingRight:""})},e.prototype.checkScrollbar=function(){var t=window.innerWidth;if(!t){var e=document.documentElement.getBoundingClientRect();t=e.right-Math.abs(e.left)}this.bodyIsOverflowing=document.body.clientWidth<t,this.scrollbarWidth=this.measureScrollbar()},e.prototype.setScrollbar=function(){var e=parseInt(this.$body.css("padding-right"
)||0,10);this.originalBodyPad=document.body.style.paddingRight||"";var i=this.scrollbarWidth;this.bodyIsOverflowing&&(this.$body.css("padding-right",e+i),t(this.fixedContent).each((function(e,o){var s=o.style.paddingRight,n=t(o).css("padding-right");t(o).data("padding-right",s).css("padding-right",parseFloat(n)+i+"px")})))},e.prototype.resetScrollbar=function(){this.$body.css("padding-right",this.originalBodyPad),t(this.fixedContent).each((function(e,i){var o=t(i).data("padding-right");t(i).removeData("padding-right"),i.style.paddingRight=o||""}))},e.prototype.measureScrollbar=function(){var t=document.createElement("div");t.className="modal-scrollbar-measure",this.$body.append(t);var e=t.offsetWidth-t.clientWidth;return this.$body[0].removeChild(t),e};var o=t.fn.modal;t.fn.modal=i,t.fn.modal.Constructor=e,t.fn.modal.noConflict=function(){return t.fn.modal=o,this},t(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',(function(e){var o=t(this),s=o.attr("href"),n=o.attr(
"data-target")||s&&s.replace(/.*(?=#[^\s]+$)/,""),a=t(document).find(n),d=a.data("bs.modal")?"toggle":t.extend({remote:!/#/.test(s)&&s},a.data(),o.data());o.is("a")&&e.preventDefault(),a.one("show.bs.modal",(function(t){t.isDefaultPrevented()||a.one("hidden.bs.modal",(function(){o.is(":visible")&&o.trigger("focus")}))})),i.call(a,d,this)}))}(jQuery);!function(t){"use strict";var e=["sanitize","whiteList","sanitizeFn"],i=["background","cite","href","itemtype","longdesc","poster","src","xlink:href"],o={"*":["class","dir","id","lang","role",/^aria-[\w-]*$/i],a:["target","href","title","rel"],area:[],b:[],br:[],col:[],code:[],div:[],em:[],hr:[],h1:[],h2:[],h3:[],h4:[],h5:[],h6:[],i:[],img:["src","alt","title","width","height"],li:[],ol:[],p:[],pre:[],s:[],small:[],span:[],sub:[],sup:[],strong:[],u:[],ul:[]},n=/^(?:(?:https?|mailto|ftp|tel|file):|[^&:/?#]*(?:[/?#]|$))/gi,s=
/^data:(?:image\/(?:bmp|gif|jpeg|jpg|png|tiff|webp)|video\/(?:mpeg|mp4|ogg|webm)|audio\/(?:mp3|oga|ogg|opus));base64,[a-z0-9+/]+=*$/i;function r(e,o){var r=e.nodeName.toLowerCase();if(-1!==t.inArray(r,o))return-1===t.inArray(r,i)||Boolean(e.nodeValue.match(n)||e.nodeValue.match(s));for(var a=t(o).filter((function(t,e){return e instanceof RegExp})),l=0,p=a.length;l<p;l++)if(r.match(a[l]))return!0;return!1}function a(e,i,o){if(0===e.length)return e;if(o&&"function"==typeof o)return o(e);if(!document.implementation||!document.implementation.createHTMLDocument)return e;var n=document.implementation.createHTMLDocument("sanitization");n.body.innerHTML=e;for(var s=t.map(i,(function(t,e){return e})),a=t(n.body).find("*"),l=0,p=a.length;l<p;l++){var h=a[l],f=h.nodeName.toLowerCase();if(-1!==t.inArray(f,s))for(var u=t.map(h.attributes,(function(t){return t})),c=[].concat(i["*"]||[],i[f]||[]),d=0,m=u.length;d<m;d++)r(u[d],c)||h.removeAttribute(u[d].nodeName);else h.parentNode.removeChild(h)}
return n.body.innerHTML}var l=function(t,e){this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",t,e)};l.VERSION="3.4.1",l.TRANSITION_DURATION=150,l.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0},sanitize:!0,sanitizeFn:null,whiteList:o},l.prototype.init=function(e,i,o){if(this.enabled=!0,this.type=e,this.$element=t(i),this.options=this.getOptions(o),this.$viewport=this.options.viewport&&t(document).find(t.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={click:!1,hover:!1,focus:!1},this.$element[0]instanceof document.constructor&&!this.options.selector)throw new Error(
"`selector` option must be specified when initializing "+this.type+" on the window.document object!");for(var n=this.options.trigger.split(" "),s=n.length;s--;){var r=n[s];if("click"==r)this.$element.on("click."+this.type,this.options.selector,t.proxy(this.toggle,this));else if("manual"!=r){var a="hover"==r?"mouseenter":"focusin",l="hover"==r?"mouseleave":"focusout";this.$element.on(a+"."+this.type,this.options.selector,t.proxy(this.enter,this)),this.$element.on(l+"."+this.type,this.options.selector,t.proxy(this.leave,this))}}this.options.selector?this._options=t.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},l.prototype.getDefaults=function(){return l.DEFAULTS},l.prototype.getOptions=function(i){var o=this.$element.data();for(var n in o)o.hasOwnProperty(n)&&-1!==t.inArray(n,e)&&delete o[n];return(i=t.extend({},this.getDefaults(),o,i)).delay&&"number"==typeof i.delay&&(i.delay={show:i.delay,hide:i.delay}),i.sanitize&&(i.template=a(i.template,i.whiteList,i.
sanitizeFn)),i},l.prototype.getDelegateOptions=function(){var e={},i=this.getDefaults();return this._options&&t.each(this._options,(function(t,o){i[t]!=o&&(e[t]=o)})),e},l.prototype.enter=function(e){var i=e instanceof this.constructor?e:t(e.currentTarget).data("bs."+this.type);if(i||(i=new this.constructor(e.currentTarget,this.getDelegateOptions()),t(e.currentTarget).data("bs."+this.type,i)),e instanceof t.Event&&(i.inState["focusin"==e.type?"focus":"hover"]=!0),i.tip().hasClass("in")||"in"==i.hoverState)i.hoverState="in";else{if(clearTimeout(i.timeout),i.hoverState="in",!i.options.delay||!i.options.delay.show)return i.show();i.timeout=setTimeout((function(){"in"==i.hoverState&&i.show()}),i.options.delay.show)}},l.prototype.isInStateTrue=function(){for(var t in this.inState)if(this.inState[t])return!0;return!1},l.prototype.leave=function(e){var i=e instanceof this.constructor?e:t(e.currentTarget).data("bs."+this.type);if(i||(i=new this.constructor(e.currentTarget,this.
getDelegateOptions()),t(e.currentTarget).data("bs."+this.type,i)),e instanceof t.Event&&(i.inState["focusout"==e.type?"focus":"hover"]=!1),!i.isInStateTrue()){if(clearTimeout(i.timeout),i.hoverState="out",!i.options.delay||!i.options.delay.hide)return i.hide();i.timeout=setTimeout((function(){"out"==i.hoverState&&i.hide()}),i.options.delay.hide)}},l.prototype.show=function(){var e=t.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(e);var i=t.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);if(e.isDefaultPrevented()||!i)return;var o=this,n=this.tip(),s=this.getUID(this.type);this.setContent(),n.attr("id",s),this.$element.attr("aria-describedby",s),this.options.animation&&n.addClass("fade");var r="function"==typeof this.options.placement?this.options.placement.call(this,n[0],this.$element[0]):this.options.placement,a=/\s?auto?\s?/i,p=a.test(r);p&&(r=r.replace(a,"")||"top"),n.detach().css({top:0,left:0,display:"block"}).
addClass(r).data("bs."+this.type,this),this.options.container?n.appendTo(t(document).find(this.options.container)):n.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);var h=this.getPosition(),f=n[0].offsetWidth,u=n[0].offsetHeight;if(p){var c=r,d=this.getPosition(this.$viewport);r="bottom"==r&&h.bottom+u>d.bottom?"top":"top"==r&&h.top-u<d.top?"bottom":"right"==r&&h.right+f>d.width?"left":"left"==r&&h.left-f<d.left?"right":r,n.removeClass(c).addClass(r)}var m=this.getCalculatedOffset(r,h,f,u);this.applyPlacement(m,r);var g=function(){var t=o.hoverState;o.$element.trigger("shown.bs."+o.type),o.hoverState=null,"out"==t&&o.leave(o)};t.support.transition&&this.$tip.hasClass("fade")?n.one("bsTransitionEnd",g).emulateTransitionEnd(l.TRANSITION_DURATION):g()}},l.prototype.applyPlacement=function(e,i){var o=this.tip(),n=o[0].offsetWidth,s=o[0].offsetHeight,r=parseInt(o.css("margin-top"),10),a=parseInt(o.css("margin-left"),10);isNaN(r)&&(r=0),isNaN(a)&&(a=0),e.top+=r,e.
left+=a,t.offset.setOffset(o[0],t.extend({using:function(t){o.css({top:Math.round(t.top),left:Math.round(t.left)})}},e),0),o.addClass("in");var l=o[0].offsetWidth,p=o[0].offsetHeight;"top"==i&&p!=s&&(e.top=e.top+s-p);var h=this.getViewportAdjustedDelta(i,e,l,p);h.left?e.left+=h.left:e.top+=h.top;var f=/top|bottom/.test(i),u=f?2*h.left-n+l:2*h.top-s+p,c=f?"offsetWidth":"offsetHeight";o.offset(e),this.replaceArrow(u,o[0][c],f)},l.prototype.replaceArrow=function(t,e,i){this.arrow().css(i?"left":"top",50*(1-t/e)+"%").css(i?"top":"left","")},l.prototype.setContent=function(){var t=this.tip(),e=this.getTitle();this.options.html?(this.options.sanitize&&(e=a(e,this.options.whiteList,this.options.sanitizeFn)),t.find(".tooltip-inner").html(e)):t.find(".tooltip-inner").text(e),t.removeClass("fade in top bottom left right")},l.prototype.hide=function(e){var i=this,o=t(this.$tip),n=t.Event("hide.bs."+this.type);function s(){"in"!=i.hoverState&&o.detach(),i.$element&&i.$element.removeAttr(
"aria-describedby").trigger("hidden.bs."+i.type),e&&e()}if(this.$element.trigger(n),!n.isDefaultPrevented())return o.removeClass("in"),t.support.transition&&o.hasClass("fade")?o.one("bsTransitionEnd",s).emulateTransitionEnd(l.TRANSITION_DURATION):s(),this.hoverState=null,this},l.prototype.fixTitle=function(){var t=this.$element;(t.attr("title")||"string"!=typeof t.attr("data-original-title"))&&t.attr("data-original-title",t.attr("title")||"").attr("title","")},l.prototype.hasContent=function(){return this.getTitle()},l.prototype.getPosition=function(e){var i=(e=e||this.$element)[0],o="BODY"==i.tagName,n=i.getBoundingClientRect();null==n.width&&(n=t.extend({},n,{width:n.right-n.left,height:n.bottom-n.top}));var s=window.SVGElement&&i instanceof window.SVGElement,r=o?{top:0,left:0}:s?null:e.offset(),a={scroll:o?document.documentElement.scrollTop||document.body.scrollTop:e.scrollTop()},l=o?{width:t(window).width(),height:t(window).height()}:null;return t.extend({},n,a,l,r)},l.prototype.
getCalculatedOffset=function(t,e,i,o){return"bottom"==t?{top:e.top+e.height,left:e.left+e.width/2-i/2}:"top"==t?{top:e.top-o,left:e.left+e.width/2-i/2}:"left"==t?{top:e.top+e.height/2-o/2,left:e.left-i}:{top:e.top+e.height/2-o/2,left:e.left+e.width}},l.prototype.getViewportAdjustedDelta=function(t,e,i,o){var n={top:0,left:0};if(!this.$viewport)return n;var s=this.options.viewport&&this.options.viewport.padding||0,r=this.getPosition(this.$viewport);if(/right|left/.test(t)){var a=e.top-s-r.scroll,l=e.top+s-r.scroll+o;a<r.top?n.top=r.top-a:l>r.top+r.height&&(n.top=r.top+r.height-l)}else{var p=e.left-s,h=e.left+s+i;p<r.left?n.left=r.left-p:h>r.right&&(n.left=r.left+r.width-h)}return n},l.prototype.getTitle=function(){var t=this.$element,e=this.options;return t.attr("data-original-title")||("function"==typeof e.title?e.title.call(t[0]):e.title)},l.prototype.getUID=function(t){do{t+=~~(1e6*Math.random())}while(document.getElementById(t));return t},l.prototype.tip=function(){if(!this.$tip&&(
this.$tip=t(this.options.template),1!=this.$tip.length))throw new Error(this.type+" `template` option must consist of exactly 1 top-level element!");return this.$tip},l.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},l.prototype.enable=function(){this.enabled=!0},l.prototype.disable=function(){this.enabled=!1},l.prototype.toggleEnabled=function(){this.enabled=!this.enabled},l.prototype.toggle=function(e){var i=this;e&&((i=t(e.currentTarget).data("bs."+this.type))||(i=new this.constructor(e.currentTarget,this.getDelegateOptions()),t(e.currentTarget).data("bs."+this.type,i))),e?(i.inState.click=!i.inState.click,i.isInStateTrue()?i.enter(i):i.leave(i)):i.tip().hasClass("in")?i.leave(i):i.enter(i)},l.prototype.destroy=function(){var t=this;clearTimeout(this.timeout),this.hide((function(){t.$element.off("."+t.type).removeData("bs."+t.type),t.$tip&&t.$tip.detach(),t.$tip=null,t.$arrow=null,t.$viewport=null,t.$element=null}))},l.prototype.
sanitizeHtml=function(t){return a(t,this.options.whiteList,this.options.sanitizeFn)};var p=t.fn.tooltip;t.fn.tooltip=function(e){return this.each((function(){var i=t(this),o=i.data("bs.tooltip"),n="object"==typeof e&&e;!o&&/destroy|hide/.test(e)||(o||i.data("bs.tooltip",o=new l(this,n)),"string"==typeof e&&o[e]())}))},t.fn.tooltip.Constructor=l,t.fn.tooltip.noConflict=function(){return t.fn.tooltip=p,this}}(jQuery);!function(t,e){"object"==typeof exports&&"object"==typeof module?module.exports=e():"function"==typeof define&&define.amd?define([],e):"object"==typeof exports?exports.Raphael=e():t.Raphael=e()}(window,(function(){return function(t){var e={};function r(i){if(e[i])return e[i].exports;var n=e[i]={i:i,l:!1,exports:{}};return t[i].call(n.exports,n,n.exports,r),n.l=!0,n.exports}return r.m=t,r.c=e,r.d=function(t,e,i){r.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:i})},r.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag
,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.t=function(t,e){if(1&e&&(t=r(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(r.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var n in t)r.d(i,n,function(e){return t[e]}.bind(null,n));return i},r.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(e,"a",e),e},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r.p="",r(r.s="./dev/raphael.amd.js")}({"./dev/raphael.amd.js":function(t,e,r){var i,n;i=[r("./dev/raphael.core.js"),r("./dev/raphael.svg.js"),r("./dev/raphael.vml.js")],void 0===(n=function(t){return t}.apply(e,i))||(t.exports=n)},"./dev/raphael.core.js":function(t,e,r){var i,n;i=[r("./node_modules/eve-raphael/eve.js")],n=function(t){function e(i){if(e.is(i,"function"))return r?i():t.on("raphael.DOMload",i);if(e.is(i,T))return e._engine.create[c](e,i.
splice(0,3+e.is(i[0],C))).add(i);var n=Array.prototype.slice.call(arguments,0);if(e.is(n[n.length-1],"function")){var a=n.pop();return r?a.call(e._engine.create[c](e,n)):t.on("raphael.DOMload",(function(){a.call(e._engine.create[c](e,n))}))}return e._engine.create[c](e,arguments)}e.version="2.3.0",e.eve=t;var r,i,n=/[, ]+/,a={circle:1,rect:1,path:1,ellipse:1,text:1,image:1},s=/\{(\d+)\}/g,o="hasOwnProperty",l={doc:document,win:window},h={was:Object.prototype[o].call(l.win,"Raphael"),is:l.win.Raphael},u=function(){this.ca=this.customAttributes={}},c="apply",f="ontouchstart"in window||window.TouchEvent||window.DocumentTouch&&document instanceof DocumentTouch,p="",d=" ",g=String,v="click dblclick mousedown mousemove mouseout mouseover mouseup touchstart touchmove touchend touchcancel".split(d),x={mousedown:"touchstart",mousemove:"touchmove",mouseup:"touchend"},y=g.prototype.toLowerCase,m=Math,b=m.max,_=m.min,w=m.abs,k=m.pow,B=m.PI,C="number",S="string",T="array",A=Object.prototype.
toString,M=(e._ISURL=/^url\(['"]?(.+?)['"]?\)$/i,/^\s*((#[a-f\d]{6})|(#[a-f\d]{3})|rgba?\(\s*([\d\.]+%?\s*,\s*[\d\.]+%?\s*,\s*[\d\.]+%?(?:\s*,\s*[\d\.]+%?)?)\s*\)|hsba?\(\s*([\d\.]+(?:deg|\xb0|%)?\s*,\s*[\d\.]+%?\s*,\s*[\d\.]+(?:%?\s*,\s*[\d\.]+)?)%?\s*\)|hsla?\(\s*([\d\.]+(?:deg|\xb0|%)?\s*,\s*[\d\.]+%?\s*,\s*[\d\.]+(?:%?\s*,\s*[\d\.]+)?)%?\s*\))\s*$/i),E={NaN:1,Infinity:1,"-Infinity":1},N=/^(?:cubic-)?bezier\(([^,]+),([^,]+),([^,]+),([^\)]+)\)/,L=m.round,P=parseFloat,j=parseInt,z=g.prototype.toUpperCase,F=e._availableAttrs={"arrow-end":"none","arrow-start":"none",blur:0,"clip-rect":"0 0 1e9 1e9",cursor:"default",cx:0,cy:0,fill:"#fff","fill-opacity":1,font:'10px "Arial"',"font-family":'"Arial"',"font-size":"10","font-style":"normal","font-weight":400,gradient:0,height:0,href:"http://raphaeljs.com/","letter-spacing":0,opacity:1,path:"M0,0",r:0,rx:0,ry:0,src:"",stroke:"#000","stroke-dasharray":"","stroke-linecap":"butt","stroke-linejoin":"butt","stroke-miterlimit":0,"stroke-opacity":1,
"stroke-width":1,target:"_blank","text-anchor":"middle",title:"Raphael",transform:"",width:0,x:0,y:0,class:""},R=e._availableAnimAttrs={blur:C,"clip-rect":"csv",cx:C,cy:C,fill:"colour","fill-opacity":C,"font-size":C,height:C,opacity:C,path:"path",r:C,rx:C,ry:C,stroke:"colour","stroke-opacity":C,"stroke-width":C,transform:"transform",width:C,x:C,y:C},I=/[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*/,q={hs:1,rg:1},D=/,?([achlmqrstvxz]),?/gi,O=
/([achlmrqstvz])[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029,]*((-?\d*\.?\d*(?:e[\-+]?\d+)?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*)+)/gi,V=/([rstm])[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029,]*((-?\d*\.?\d*(?:e[\-+]?\d+)?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*)+)/gi,W=
/(-?\d*\.?\d*(?:e[\-+]?\d+)?)[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,?[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*/gi,Y=(e._radial_gradient=/^r(?:\(([^,]+?)[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*,[\x09\x0a\x0b\x0c\x0d\x20\xa0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\u2028\u2029]*([^\)]+?)\))?/,{}),G=function(t,e){return P(t)-P(e)},H=function(t){return t},X=e._rectPath=function(t,e,r,i,n){return n?[["M",t+n,e],["l",r-2*n,0],["a",n,n,0,0,1,n,n],["l",0,i-2*n],["a",n,n,0,0,1,-n,n],["l",2*n-r,0],["a",n,n,0,0,1,-n,-n],["l",0,2*n-i],["a",n,n,0,0,1,n,-n],["z"]]:[["M",t,e],["l",r,0],["l",0,i],["l",-r,0],["z"]]},U=function(t,e,r,i){return null==i&&(i=r
),[["M",t,e],["m",0,-i],["a",r,i,0,1,1,0,2*i],["a",r,i,0,1,1,0,-2*i],["z"]]},$=e._getPath={path:function(t){return t.attr("path")},circle:function(t){var e=t.attrs;return U(e.cx,e.cy,e.r)},ellipse:function(t){var e=t.attrs;return U(e.cx,e.cy,e.rx,e.ry)},rect:function(t){var e=t.attrs;return X(e.x,e.y,e.width,e.height,e.r)},image:function(t){var e=t.attrs;return X(e.x,e.y,e.width,e.height)},text:function(t){var e=t._getBBox();return X(e.x,e.y,e.width,e.height)},set:function(t){var e=t._getBBox();return X(e.x,e.y,e.width,e.height)}},Z=e.mapPath=function(t,e){if(!e)return t;var r,i,n,a,s,o,l;for(n=0,s=(t=Tt(t)).length;n<s;n++)for(a=1,o=(l=t[n]).length;a<o;a+=2)r=e.x(l[a],l[a+1]),i=e.y(l[a],l[a+1]),l[a]=r,l[a+1]=i;return t};if(e._g=l,e.type=l.win.SVGAngle||l.doc.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure","1.1")?"SVG":"VML","VML"==e.type){var Q,J=l.doc.createElement("div");if(J.innerHTML='<v:shape adj="1"/>',(Q=J.firstChild).style.behavior=
"url(#default#VML)",!Q||"object"!=typeof Q.adj)return e.type=p;J=null}function K(t){if("function"==typeof t||Object(t)!==t)return t;var e=new t.constructor;for(var r in t)t[o](r)&&(e[r]=K(t[r]));return e}e.svg=!(e.vml="VML"==e.type),e._Paper=u,e.fn=i=u.prototype=e.prototype,e._id=0,e.is=function(t,e){return"finite"==(e=y.call(e))?!E[o](+t):"array"==e?t instanceof Array:"null"==e&&null===t||e==typeof t&&null!==t||"object"==e&&t===Object(t)||"array"==e&&Array.isArray&&Array.isArray(t)||A.call(t).slice(8,-1).toLowerCase()==e},e.angle=function(t,r,i,n,a,s){if(null==a){var o=t-i,l=r-n;return o||l?(180+180*m.atan2(-l,-o)/B+360)%360:0}return e.angle(t,r,a,s)-e.angle(i,n,a,s)},e.rad=function(t){return t%360*B/180},e.deg=function(t){return Math.round(180*t/B%360*1e3)/1e3},e.snapTo=function(t,r,i){if(i=e.is(i,"finite")?i:10,e.is(t,T)){for(var n=t.length;n--;)if(w(t[n]-r)<=i)return t[n]}else{var a=r%(t=+t);if(a<i)return r-a;if(a>t-i)return r-a+t}return r};var tt,et;e.createUUID=(tt=/[xy]/g,et=
function(t){var e=16*m.random()|0;return("x"==t?e:3&e|8).toString(16)},function(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(tt,et).toUpperCase()});e.setWindow=function(r){t("raphael.setWindow",e,l.win,r),l.win=r,l.doc=l.win.document,e._engine.initWin&&e._engine.initWin(l.win)};var rt=function(t){if(e.vml){var r,i=/^\s+|\s+$/g;try{var n=new ActiveXObject("htmlfile");n.write("<body>"),n.close(),r=n.body}catch(t){r=createPopup().document.body}var a=r.createTextRange();rt=ht((function(t){try{r.style.color=g(t).replace(i,p);var e=a.queryCommandValue("ForeColor");return"#"+("000000"+(e=(255&e)<<16|65280&e|(16711680&e)>>>16).toString(16)).slice(-6)}catch(t){return"none"}}))}else{var s=l.doc.createElement("i");s.title="Raphaël Colour Picker",s.style.display="none",l.doc.body.appendChild(s),rt=ht((function(t){return s.style.color=t,l.doc.defaultView.getComputedStyle(s,p).getPropertyValue("color")}))}return rt(t)},it=function(){return"hsb("+[this.h,this.s,this.b]+")"},nt=function(){
return"hsl("+[this.h,this.s,this.l]+")"},at=function(){return this.hex},st=function(t,r,i){if(null==r&&e.is(t,"object")&&"r"in t&&"g"in t&&"b"in t&&(i=t.b,r=t.g,t=t.r),null==r&&e.is(t,S)){var n=e.getRGB(t);t=n.r,r=n.g,i=n.b}return(t>1||r>1||i>1)&&(t/=255,r/=255,i/=255),[t,r,i]},ot=function(t,r,i,n){var a={r:t*=255,g:r*=255,b:i*=255,hex:e.rgb(t,r,i),toString:at};return e.is(n,"finite")&&(a.opacity=n),a};function lt(t,e){for(var r=0,i=t.length;r<i;r++)if(t[r]===e)return t.push(t.splice(r,1)[0])}function ht(t,e,r){return function i(){var n=Array.prototype.slice.call(arguments,0),a=n.join("␀"),s=i.cache=i.cache||{},l=i.count=i.count||[];return s[o](a)?(lt(l,a),r?r(s[a]):s[a]):(l.length>=1e3&&delete s[l.shift()],l.push(a),s[a]=t[c](e,n),r?r(s[a]):s[a])}}e.color=function(t){var r;return e.is(t,"object")&&"h"in t&&"s"in t&&"b"in t?(r=e.hsb2rgb(t),t.r=r.r,t.g=r.g,t.b=r.b,t.hex=r.hex):e.is(t,"object")&&"h"in t&&"s"in t&&"l"in t?(r=e.hsl2rgb(t),t.r=r.r,t.g=r.g,t.b=r.b,t.hex=r.hex):(e.is(t,
"string")&&(t=e.getRGB(t)),e.is(t,"object")&&"r"in t&&"g"in t&&"b"in t?(r=e.rgb2hsl(t),t.h=r.h,t.s=r.s,t.l=r.l,r=e.rgb2hsb(t),t.v=r.b):(t={hex:"none"}).r=t.g=t.b=t.h=t.s=t.v=t.l=-1),t.toString=at,t},e.hsb2rgb=function(t,e,r,i){var n,a,s,o,l;return this.is(t,"object")&&"h"in t&&"s"in t&&"b"in t&&(r=t.b,e=t.s,i=t.o,t=t.h),o=(l=r*e)*(1-w((t=(t*=360)%360/60)%2-1)),n=a=s=r-l,ot(n+=[l,o,0,0,o,l][t=~~t],a+=[o,l,l,o,0,0][t],s+=[0,0,o,l,l,o][t],i)},e.hsl2rgb=function(t,e,r,i){var n,a,s,o,l;return this.is(t,"object")&&"h"in t&&"s"in t&&"l"in t&&(r=t.l,e=t.s,t=t.h),(t>1||e>1||r>1)&&(t/=360,e/=100,r/=100),o=(l=2*e*(r<.5?r:1-r))*(1-w((t=(t*=360)%360/60)%2-1)),n=a=s=r-l/2,ot(n+=[l,o,0,0,o,l][t=~~t],a+=[o,l,l,o,0,0][t],s+=[0,0,o,l,l,o][t],i)},e.rgb2hsb=function(t,e,r){var i,n;return t=(r=st(t,e,r))[0],e=r[1],r=r[2],{h:((0==(n=(i=b(t,e,r))-_(t,e,r))?null:i==t?(e-r)/n:i==e?(r-t)/n+2:(t-e)/n+4)+360)%6*60/360,s:0==n?0:n/i,b:i,toString:it}},e.rgb2hsl=function(t,e,r){var i,n,a,s;return t=(r=st(t,e,r))[0],e
=r[1],r=r[2],i=((n=b(t,e,r))+(a=_(t,e,r)))/2,{h:((0==(s=n-a)?null:n==t?(e-r)/s:n==e?(r-t)/s+2:(t-e)/s+4)+360)%6*60/360,s:0==s?0:i<.5?s/(2*i):s/(2-2*i),l:i,toString:nt}},e._path2string=function(){return this.join(",").replace(D,"$1")};e._preload=function(t,e){var r=l.doc.createElement("img");r.style.cssText="position:absolute;left:-9999em;top:-9999em",r.onload=function(){e.call(this),this.onload=null,l.doc.body.removeChild(this)},r.onerror=function(){l.doc.body.removeChild(this)},l.doc.body.appendChild(r),r.src=t};function ut(){return this.hex}function ct(t,e){for(var r=[],i=0,n=t.length;n-2*!e>i;i+=2){var a=[{x:+t[i-2],y:+t[i-1]},{x:+t[i],y:+t[i+1]},{x:+t[i+2],y:+t[i+3]},{x:+t[i+4],y:+t[i+5]}];e?i?n-4==i?a[3]={x:+t[0],y:+t[1]}:n-2==i&&(a[2]={x:+t[0],y:+t[1]},a[3]={x:+t[2],y:+t[3]}):a[0]={x:+t[n-2],y:+t[n-1]}:n-4==i?a[3]=a[2]:i||(a[0]={x:+t[i],y:+t[i+1]}),r.push(["C",(-a[0].x+6*a[1].x+a[2].x)/6,(-a[0].y+6*a[1].y+a[2].y)/6,(a[1].x+6*a[2].x-a[3].x)/6,(a[1].y+6*a[2].y-a[3].y)/6,a[2].x,a[2]
.y])}return r}e.getRGB=ht((function(t){if(!t||(t=g(t)).indexOf("-")+1)return{r:-1,g:-1,b:-1,hex:"none",error:1,toString:ut};if("none"==t)return{r:-1,g:-1,b:-1,hex:"none",toString:ut};!q[o](t.toLowerCase().substring(0,2))&&"#"!=t.charAt()&&(t=rt(t));var r,i,n,a,s,l,h=t.match(M);return h?(h[2]&&(n=j(h[2].substring(5),16),i=j(h[2].substring(3,5),16),r=j(h[2].substring(1,3),16)),h[3]&&(n=j((s=h[3].charAt(3))+s,16),i=j((s=h[3].charAt(2))+s,16),r=j((s=h[3].charAt(1))+s,16)),h[4]&&(l=h[4].split(I),r=P(l[0]),"%"==l[0].slice(-1)&&(r*=2.55),i=P(l[1]),"%"==l[1].slice(-1)&&(i*=2.55),n=P(l[2]),"%"==l[2].slice(-1)&&(n*=2.55),"rgba"==h[1].toLowerCase().slice(0,4)&&(a=P(l[3])),l[3]&&"%"==l[3].slice(-1)&&(a/=100)),h[5]?(l=h[5].split(I),r=P(l[0]),"%"==l[0].slice(-1)&&(r*=2.55),i=P(l[1]),"%"==l[1].slice(-1)&&(i*=2.55),n=P(l[2]),"%"==l[2].slice(-1)&&(n*=2.55),("deg"==l[0].slice(-3)||"°"==l[0].slice(-1))&&(r/=360),"hsba"==h[1].toLowerCase().slice(0,4)&&(a=P(l[3])),l[3]&&"%"==l[3].slice(-1)&&(a/=100),e.
hsb2rgb(r,i,n,a)):h[6]?(l=h[6].split(I),r=P(l[0]),"%"==l[0].slice(-1)&&(r*=2.55),i=P(l[1]),"%"==l[1].slice(-1)&&(i*=2.55),n=P(l[2]),"%"==l[2].slice(-1)&&(n*=2.55),("deg"==l[0].slice(-3)||"°"==l[0].slice(-1))&&(r/=360),"hsla"==h[1].toLowerCase().slice(0,4)&&(a=P(l[3])),l[3]&&"%"==l[3].slice(-1)&&(a/=100),e.hsl2rgb(r,i,n,a)):((h={r:r,g:i,b:n,toString:ut}).hex="#"+(16777216|n|i<<8|r<<16).toString(16).slice(1),e.is(a,"finite")&&(h.opacity=a),h)):{r:-1,g:-1,b:-1,hex:"none",error:1,toString:ut}}),e),e.hsb=ht((function(t,r,i){return e.hsb2rgb(t,r,i).hex})),e.hsl=ht((function(t,r,i){return e.hsl2rgb(t,r,i).hex})),e.rgb=ht((function(t,e,r){function i(t){return t+.5|0}return"#"+(16777216|i(r)|i(e)<<8|i(t)<<16).toString(16).slice(1)})),e.getColor=function(t){var e=this.getColor.start=this.getColor.start||{h:0,s:1,b:t||.75},r=this.hsb2rgb(e.h,e.s,e.b);return e.h+=.075,e.h>1&&(e.h=0,e.s-=.2,e.s<=0&&(this.getColor.start={h:0,s:1,b:e.b})),r.hex},e.getColor.reset=function(){delete this.start},e.
parsePathString=function(t){if(!t)return null;var r=ft(t);if(r.arr)return mt(r.arr);var i={a:7,c:6,h:1,l:2,m:2,r:4,q:4,s:4,t:2,v:1,z:0},n=[];return e.is(t,T)&&e.is(t[0],T)&&(n=mt(t)),n.length||g(t).replace(O,(function(t,e,r){var a=[],s=e.toLowerCase();if(r.replace(W,(function(t,e){e&&a.push(+e)})),"m"==s&&a.length>2&&(n.push([e].concat(a.splice(0,2))),s="l",e="m"==e?"l":"L"),"r"==s)n.push([e].concat(a));else for(;a.length>=i[s]&&(n.push([e].concat(a.splice(0,i[s]))),i[s]););})),n.toString=e._path2string,r.arr=mt(n),n},e.parseTransformString=ht((function(t){if(!t)return null;var r=[];return e.is(t,T)&&e.is(t[0],T)&&(r=mt(t)),r.length||g(t).replace(V,(function(t,e,i){var n=[];y.call(e);i.replace(W,(function(t,e){e&&n.push(+e)})),r.push([e].concat(n))})),r.toString=e._path2string,r}),this,(function(t){if(!t)return t;for(var e=[],r=0;r<t.length;r++){for(var i=[],n=0;n<t[r].length;n++)i.push(t[r][n]);e.push(i)}return e}));var ft=function(t){var e=ft.ps=ft.ps||{};return e[t]?e[t].sleep=100:e
[t]={sleep:100},setTimeout((function(){for(var r in e)e[o](r)&&r!=t&&(e[r].sleep--,!e[r].sleep&&delete e[r])})),e[t]};function pt(t,e,r,i,n){return t*(t*(-3*e+9*r-9*i+3*n)+6*e-12*r+6*i)-3*e+3*r}function dt(t,e,r,i,n,a,s,o,l){null==l&&(l=1);for(var h=(l=l>1?1:l<0?0:l)/2,u=[-.1252,.1252,-.3678,.3678,-.5873,.5873,-.7699,.7699,-.9041,.9041,-.9816,.9816],c=[.2491,.2491,.2335,.2335,.2032,.2032,.1601,.1601,.1069,.1069,.0472,.0472],f=0,p=0;p<12;p++){var d=h*u[p]+h,g=pt(d,t,r,n,s),v=pt(d,e,i,a,o),x=g*g+v*v;f+=c[p]*m.sqrt(x)}return h*f}function gt(t,e,r,i,n,a,s,o){if(!(b(t,r)<_(n,s)||_(t,r)>b(n,s)||b(e,i)<_(a,o)||_(e,i)>b(a,o))){var l=(t-r)*(a-o)-(e-i)*(n-s);if(l){var h=((t*i-e*r)*(n-s)-(t-r)*(n*o-a*s))/l,u=((t*i-e*r)*(a-o)-(e-i)*(n*o-a*s))/l,c=+h.toFixed(2),f=+u.toFixed(2);if(!(c<+_(t,r).toFixed(2)||c>+b(t,r).toFixed(2)||c<+_(n,s).toFixed(2)||c>+b(n,s).toFixed(2)||f<+_(e,i).toFixed(2)||f>+b(e,i).toFixed(2)||f<+_(a,o).toFixed(2)||f>+b(a,o).toFixed(2)))return{x:h,y:u}}}}function vt(t,r,i){var n=e
.bezierBBox(t),a=e.bezierBBox(r);if(!e.isBBoxIntersect(n,a))return i?0:[];for(var s=dt.apply(0,t),o=dt.apply(0,r),l=b(~~(s/5),1),h=b(~~(o/5),1),u=[],c=[],f={},p=i?0:[],d=0;d<l+1;d++){var g=e.findDotsAtSegment.apply(e,t.concat(d/l));u.push({x:g.x,y:g.y,t:d/l})}for(d=0;d<h+1;d++)g=e.findDotsAtSegment.apply(e,r.concat(d/h)),c.push({x:g.x,y:g.y,t:d/h});for(d=0;d<l;d++)for(var v=0;v<h;v++){var x=u[d],y=u[d+1],m=c[v],k=c[v+1],B=w(y.x-x.x)<.001?"y":"x",C=w(k.x-m.x)<.001?"y":"x",S=gt(x.x,x.y,y.x,y.y,m.x,m.y,k.x,k.y);if(S){if(f[S.x.toFixed(4)]==S.y.toFixed(4))continue;f[S.x.toFixed(4)]=S.y.toFixed(4);var T=x.t+w((S[B]-x[B])/(y[B]-x[B]))*(y.t-x.t),A=m.t+w((S[C]-m[C])/(k[C]-m[C]))*(k.t-m.t);T>=0&&T<=1.001&&A>=0&&A<=1.001&&(i?p++:p.push({x:S.x,y:S.y,t1:_(T,1),t2:_(A,1)}))}}return p}function xt(t,r,i){t=e._path2curve(t),r=e._path2curve(r);for(var n,a,s,o,l,h,u,c,f,p,d=i?0:[],g=0,v=t.length;g<v;g++){var x=t[g];if("M"==x[0])n=l=x[1],a=h=x[2];else{"C"==x[0]?(f=[n,a].concat(x.slice(1)),n=f[6],a=f[7]):(
f=[n,a,n,a,l,h,l,h],n=l,a=h);for(var y=0,m=r.length;y<m;y++){var b=r[y];if("M"==b[0])s=u=b[1],o=c=b[2];else{"C"==b[0]?(p=[s,o].concat(b.slice(1)),s=p[6],o=p[7]):(p=[s,o,s,o,u,c,u,c],s=u,o=c);var _=vt(f,p,i);if(i)d+=_;else{for(var w=0,k=_.length;w<k;w++)_[w].segment1=g,_[w].segment2=y,_[w].bez1=f,_[w].bez2=p;d=d.concat(_)}}}}}return d}e.findDotsAtSegment=function(t,e,r,i,n,a,s,o,l){var h=1-l,u=k(h,3),c=k(h,2),f=l*l,p=f*l,d=u*t+3*c*l*r+3*h*l*l*n+p*s,g=u*e+3*c*l*i+3*h*l*l*a+p*o,v=t+2*l*(r-t)+f*(n-2*r+t),x=e+2*l*(i-e)+f*(a-2*i+e),y=r+2*l*(n-r)+f*(s-2*n+r),b=i+2*l*(a-i)+f*(o-2*a+i),_=h*t+l*r,w=h*e+l*i,C=h*n+l*s,S=h*a+l*o,T=90-180*m.atan2(v-y,x-b)/B;return(v>y||x<b)&&(T+=180),{x:d,y:g,m:{x:v,y:x},n:{x:y,y:b},start:{x:_,y:w},end:{x:C,y:S},alpha:T}},e.bezierBBox=function(t,r,i,n,a,s,o,l){e.is(t,"array")||(t=[t,r,i,n,a,s,o,l]);var h=St.apply(null,t);return{x:h.min.x,y:h.min.y,x2:h.max.x,y2:h.max.y,width:h.max.x-h.min.x,height:h.max.y-h.min.y}},e.isPointInsideBBox=function(t,e,r){return e>=t.x&&
e<=t.x2&&r>=t.y&&r<=t.y2},e.isBBoxIntersect=function(t,r){var i=e.isPointInsideBBox;return i(r,t.x,t.y)||i(r,t.x2,t.y)||i(r,t.x,t.y2)||i(r,t.x2,t.y2)||i(t,r.x,r.y)||i(t,r.x2,r.y)||i(t,r.x,r.y2)||i(t,r.x2,r.y2)||(t.x<r.x2&&t.x>r.x||r.x<t.x2&&r.x>t.x)&&(t.y<r.y2&&t.y>r.y||r.y<t.y2&&r.y>t.y)},e.pathIntersection=function(t,e){return xt(t,e)},e.pathIntersectionNumber=function(t,e){return xt(t,e,1)},e.isPointInsidePath=function(t,r,i){var n=e.pathBBox(t);return e.isPointInsideBBox(n,r,i)&&xt(t,[["M",r,i],["H",n.x2+10]],1)%2==1},e._removedFactory=function(e){return function(){t("raphael.log",null,"Raphaël: you are calling to method “"+e+"” of removed object",e)}};var yt=e.pathBBox=function(t){var e=ft(t);if(e.bbox)return K(e.bbox);if(!t)return{x:0,y:0,width:0,height:0,x2:0,y2:0};for(var r,i=0,n=0,a=[],s=[],o=0,l=(t=Tt(t)).length;o<l;o++)if("M"==(r=t[o])[0])i=r[1],n=r[2],a.push(i),s.push(n);else{var h=St(i,n,r[1],r[2],r[3],r[4],r[5],r[6]);a=a.concat(h.min.x,h.max.x),s=s.concat(h.min.y,h.
max.y),i=r[5],n=r[6]}var u=_[c](0,a),f=_[c](0,s),p=b[c](0,a),d=b[c](0,s),g=p-u,v=d-f,x={x:u,y:f,x2:p,y2:d,width:g,height:v,cx:u+g/2,cy:f+v/2};return e.bbox=K(x),x},mt=function(t){var r=K(t);return r.toString=e._path2string,r},bt=e._pathToRelative=function(t){var r=ft(t);if(r.rel)return mt(r.rel);e.is(t,T)&&e.is(t&&t[0],T)||(t=e.parsePathString(t));var i=[],n=0,a=0,s=0,o=0,l=0;"M"==t[0][0]&&(s=n=t[0][1],o=a=t[0][2],l++,i.push(["M",n,a]));for(var h=l,u=t.length;h<u;h++){var c=i[h]=[],f=t[h];if(f[0]!=y.call(f[0]))switch(c[0]=y.call(f[0]),c[0]){case"a":c[1]=f[1],c[2]=f[2],c[3]=f[3],c[4]=f[4],c[5]=f[5],c[6]=+(f[6]-n).toFixed(3),c[7]=+(f[7]-a).toFixed(3);break;case"v":c[1]=+(f[1]-a).toFixed(3);break;case"m":s=f[1],o=f[2];default:for(var p=1,d=f.length;p<d;p++)c[p]=+(f[p]-(p%2?n:a)).toFixed(3)}else{c=i[h]=[],"m"==f[0]&&(s=f[1]+n,o=f[2]+a);for(var g=0,v=f.length;g<v;g++)i[h][g]=f[g]}var x=i[h].length;switch(i[h][0]){case"z":n=s,a=o;break;case"h":n+=+i[h][x-1];break;case"v":a+=+i[h][x-1];break;
default:n+=+i[h][x-2],a+=+i[h][x-1]}}return i.toString=e._path2string,r.rel=mt(i),i},_t=e._pathToAbsolute=function(t){var r=ft(t);if(r.abs)return mt(r.abs);if(e.is(t,T)&&e.is(t&&t[0],T)||(t=e.parsePathString(t)),!t||!t.length)return[["M",0,0]];var i=[],n=0,a=0,s=0,o=0,l=0;"M"==t[0][0]&&(s=n=+t[0][1],o=a=+t[0][2],l++,i[0]=["M",n,a]);for(var h,u,c=3==t.length&&"M"==t[0][0]&&"R"==t[1][0].toUpperCase()&&"Z"==t[2][0].toUpperCase(),f=l,p=t.length;f<p;f++){if(i.push(h=[]),(u=t[f])[0]!=z.call(u[0]))switch(h[0]=z.call(u[0]),h[0]){case"A":h[1]=u[1],h[2]=u[2],h[3]=u[3],h[4]=u[4],h[5]=u[5],h[6]=+(u[6]+n),h[7]=+(u[7]+a);break;case"V":h[1]=+u[1]+a;break;case"H":h[1]=+u[1]+n;break;case"R":for(var d=[n,a].concat(u.slice(1)),g=2,v=d.length;g<v;g++)d[g]=+d[g]+n,d[++g]=+d[g]+a;i.pop(),i=i.concat(ct(d,c));break;case"M":s=+u[1]+n,o=+u[2]+a;default:for(g=1,v=u.length;g<v;g++)h[g]=+u[g]+(g%2?n:a)}else if("R"==u[0])d=[n,a].concat(u.slice(1)),i.pop(),i=i.concat(ct(d,c)),h=["R"].concat(u.slice(-2));else for(var
x=0,y=u.length;x<y;x++)h[x]=u[x];switch(h[0]){case"Z":n=s,a=o;break;case"H":n=h[1];break;case"V":a=h[1];break;case"M":s=h[h.length-2],o=h[h.length-1];default:n=h[h.length-2],a=h[h.length-1]}}return i.toString=e._path2string,r.abs=mt(i),i},wt=function(t,e,r,i){return[t,e,r,i,r,i]},kt=function(t,e,r,i,n,a){var s=1/3,o=2/3;return[s*t+o*r,s*e+o*i,s*n+o*r,s*a+o*i,n,a]},Bt=function(t,e,r,i,n,a,s,o,l,h){var u,c=120*B/180,f=B/180*(+n||0),p=[],d=ht((function(t,e,r){return{x:t*m.cos(r)-e*m.sin(r),y:t*m.sin(r)+e*m.cos(r)}}));if(h)S=h[0],T=h[1],k=h[2],C=h[3];else{t=(u=d(t,e,-f)).x,e=u.y,o=(u=d(o,l,-f)).x,l=u.y;m.cos(B/180*n),m.sin(B/180*n);var g=(t-o)/2,v=(e-l)/2,x=g*g/(r*r)+v*v/(i*i);x>1&&(r*=x=m.sqrt(x),i*=x);var y=r*r,b=i*i,_=(a==s?-1:1)*m.sqrt(w((y*b-y*v*v-b*g*g)/(y*v*v+b*g*g))),k=_*r*v/i+(t+o)/2,C=_*-i*g/r+(e+l)/2,S=m.asin(((e-C)/i).toFixed(9)),T=m.asin(((l-C)/i).toFixed(9));(S=t<k?B-S:S)<0&&(S=2*B+S),(T=o<k?B-T:T)<0&&(T=2*B+T),s&&S>T&&(S-=2*B),!s&&T>S&&(T-=2*B)}var A=T-S;if(w(A)>c){var M=T,E
=o,N=l;T=S+c*(s&&T>S?1:-1),o=k+r*m.cos(T),l=C+i*m.sin(T),p=Bt(o,l,r,i,n,0,s,E,N,[T,M,k,C])}A=T-S;var L=m.cos(S),P=m.sin(S),j=m.cos(T),z=m.sin(T),F=m.tan(A/4),R=4/3*r*F,I=4/3*i*F,q=[t,e],D=[t+R*P,e-I*L],O=[o+R*z,l-I*j],V=[o,l];if(D[0]=2*q[0]-D[0],D[1]=2*q[1]-D[1],h)return[D,O,V].concat(p);for(var W=[],Y=0,G=(p=[D,O,V].concat(p).join().split(",")).length;Y<G;Y++)W[Y]=Y%2?d(p[Y-1],p[Y],f).y:d(p[Y],p[Y+1],f).x;return W},Ct=function(t,e,r,i,n,a,s,o,l){var h=1-l;return{x:k(h,3)*t+3*k(h,2)*l*r+3*h*l*l*n+k(l,3)*s,y:k(h,3)*e+3*k(h,2)*l*i+3*h*l*l*a+k(l,3)*o}},St=ht((function(t,e,r,i,n,a,s,o){var l,h=n-2*r+t-(s-2*n+r),u=2*(r-t)-2*(n-r),f=t-r,p=(-u+m.sqrt(u*u-4*h*f))/2/h,d=(-u-m.sqrt(u*u-4*h*f))/2/h,g=[e,o],v=[t,s];return w(p)>"1e12"&&(p=.5),w(d)>"1e12"&&(d=.5),p>0&&p<1&&(l=Ct(t,e,r,i,n,a,s,o,p),v.push(l.x),g.push(l.y)),d>0&&d<1&&(l=Ct(t,e,r,i,n,a,s,o,d),v.push(l.x),g.push(l.y)),h=a-2*i+e-(o-2*a+i),f=e-i,p=(-(u=2*(i-e)-2*(a-i))+m.sqrt(u*u-4*h*f))/2/h,d=(-u-m.sqrt(u*u-4*h*f))/2/h,w(p)>"1e12"&&(p=.5
),w(d)>"1e12"&&(d=.5),p>0&&p<1&&(l=Ct(t,e,r,i,n,a,s,o,p),v.push(l.x),g.push(l.y)),d>0&&d<1&&(l=Ct(t,e,r,i,n,a,s,o,d),v.push(l.x),g.push(l.y)),{min:{x:_[c](0,v),y:_[c](0,g)},max:{x:b[c](0,v),y:b[c](0,g)}}})),Tt=e._path2curve=ht((function(t,e){var r=!e&&ft(t);if(!e&&r.curve)return mt(r.curve);for(var i=_t(t),n=e&&_t(e),a={x:0,y:0,bx:0,by:0,X:0,Y:0,qx:null,qy:null},s={x:0,y:0,bx:0,by:0,X:0,Y:0,qx:null,qy:null},o=function(t,e,r){var i,n;if(!t)return["C",e.x,e.y,e.x,e.y,e.x,e.y];switch(!(t[0]in{T:1,Q:1})&&(e.qx=e.qy=null),t[0]){case"M":e.X=t[1],e.Y=t[2];break;case"A":t=["C"].concat(Bt[c](0,[e.x,e.y].concat(t.slice(1))));break;case"S":"C"==r||"S"==r?(i=2*e.x-e.bx,n=2*e.y-e.by):(i=e.x,n=e.y),t=["C",i,n].concat(t.slice(1));break;case"T":"Q"==r||"T"==r?(e.qx=2*e.x-e.qx,e.qy=2*e.y-e.qy):(e.qx=e.x,e.qy=e.y),t=["C"].concat(kt(e.x,e.y,e.qx,e.qy,t[1],t[2]));break;case"Q":e.qx=t[1],e.qy=t[2],t=["C"].concat(kt(e.x,e.y,t[1],t[2],t[3],t[4]));break;case"L":t=["C"].concat(wt(e.x,e.y,t[1],t[2]));break;case
"H":t=["C"].concat(wt(e.x,e.y,t[1],e.y));break;case"V":t=["C"].concat(wt(e.x,e.y,e.x,t[1]));break;case"Z":t=["C"].concat(wt(e.x,e.y,e.X,e.Y))}return t},l=function(t,e){if(t[e].length>7){t[e].shift();for(var r=t[e];r.length;)u[e]="A",n&&(f[e]="A"),t.splice(e++,0,["C"].concat(r.splice(0,6)));t.splice(e,1),v=b(i.length,n&&n.length||0)}},h=function(t,e,r,a,s){t&&e&&"M"==t[s][0]&&"M"!=e[s][0]&&(e.splice(s,0,["M",a.x,a.y]),r.bx=0,r.by=0,r.x=t[s][1],r.y=t[s][2],v=b(i.length,n&&n.length||0))},u=[],f=[],p="",d="",g=0,v=b(i.length,n&&n.length||0);g<v;g++){i[g]&&(p=i[g][0]),"C"!=p&&(u[g]=p,g&&(d=u[g-1])),i[g]=o(i[g],a,d),"A"!=u[g]&&"C"==p&&(u[g]="C"),l(i,g),n&&(n[g]&&(p=n[g][0]),"C"!=p&&(f[g]=p,g&&(d=f[g-1])),n[g]=o(n[g],s,d),"A"!=f[g]&&"C"==p&&(f[g]="C"),l(n,g)),h(i,n,a,s,g),h(n,i,s,a,g);var x=i[g],y=n&&n[g],m=x.length,_=n&&y.length;a.x=x[m-2],a.y=x[m-1],a.bx=P(x[m-4])||a.x,a.by=P(x[m-3])||a.y,s.bx=n&&(P(y[_-4])||s.x),s.by=n&&(P(y[_-3])||s.y),s.x=n&&y[_-2],s.y=n&&y[_-1]}return n||(r.curve=mt(i))
,n?[i,n]:i}),null,mt),At=(e._parseDots=ht((function(t){for(var r=[],i=0,n=t.length;i<n;i++){var a={},s=t[i].match(/^([^:]*):?([\d\.]*)/);if(a.color=e.getRGB(s[1]),a.color.error)return null;a.opacity=a.color.opacity,a.color=a.color.hex,s[2]&&(a.offset=s[2]+"%"),r.push(a)}for(i=1,n=r.length-1;i<n;i++)if(!r[i].offset){for(var o=P(r[i-1].offset||0),l=0,h=i+1;h<n;h++)if(r[h].offset){l=r[h].offset;break}l||(l=100,h=n);for(var u=((l=P(l))-o)/(h-i+1);i<h;i++)o+=u,r[i].offset=o+"%"}return r})),e._tear=function(t,e){t==e.top&&(e.top=t.prev),t==e.bottom&&(e.bottom=t.next),t.next&&(t.next.prev=t.prev),t.prev&&(t.prev.next=t.next)}),Mt=(e._tofront=function(t,e){e.top!==t&&(At(t,e),t.next=null,t.prev=e.top,e.top.next=t,e.top=t)},e._toback=function(t,e){e.bottom!==t&&(At(t,e),t.next=e.bottom,t.prev=null,e.bottom.prev=t,e.bottom=t)},e._insertafter=function(t,e,r){At(t,r),e==r.top&&(r.top=t),e.next&&(e.next.prev=t),t.next=e.next,t.prev=e,e.next=t},e._insertbefore=function(t,e,r){At(t,r),e==r.bottom&&(r
.bottom=t),e.prev&&(e.prev.next=t),t.prev=e.prev,e.prev=t,t.next=e},e.toMatrix=function(t,e){var r=yt(t),i={_:{transform:p},getBBox:function(){return r}};return Et(i,e),i.matrix}),Et=(e.transformPath=function(t,e){return Z(t,Mt(t,e))},e._extractTransform=function(t,r){if(null==r)return t._.transform;r=g(r).replace(/\.{3}|\u2026/g,t._.transform||p);var i,n,a=e.parseTransformString(r),s=0,o=1,l=1,h=t._,u=new Pt;if(h.transform=a||[],a)for(var c=0,f=a.length;c<f;c++){var d,v,x,y,m,b=a[c],_=b.length,w=g(b[0]).toLowerCase(),k=b[0]!=w,B=k?u.invert():0;"t"==w&&3==_?k?(d=B.x(0,0),v=B.y(0,0),x=B.x(b[1],b[2]),y=B.y(b[1],b[2]),u.translate(x-d,y-v)):u.translate(b[1],b[2]):"r"==w?2==_?(m=m||t.getBBox(1),u.rotate(b[1],m.x+m.width/2,m.y+m.height/2),s+=b[1]):4==_&&(k?(x=B.x(b[2],b[3]),y=B.y(b[2],b[3]),u.rotate(b[1],x,y)):u.rotate(b[1],b[2],b[3]),s+=b[1]):"s"==w?2==_||3==_?(m=m||t.getBBox(1),u.scale(b[1],b[_-1],m.x+m.width/2,m.y+m.height/2),o*=b[1],l*=b[_-1]):5==_&&(k?(x=B.x(b[3],b[4]),y=B.y(b[3],b[4]),
u.scale(b[1],b[2],x,y)):u.scale(b[1],b[2],b[3],b[4]),o*=b[1],l*=b[2]):"m"==w&&7==_&&u.add(b[1],b[2],b[3],b[4],b[5],b[6]),h.dirtyT=1,t.matrix=u}t.matrix=u,h.sx=o,h.sy=l,h.deg=s,h.dx=i=u.e,h.dy=n=u.f,1==o&&1==l&&!s&&h.bbox?(h.bbox.x+=+i,h.bbox.y+=+n):h.dirtyT=1}),Nt=function(t){var e=t[0];switch(e.toLowerCase()){case"t":return[e,0,0];case"m":return[e,1,0,0,1,0,0];case"r":return 4==t.length?[e,0,t[2],t[3]]:[e,0];case"s":return 5==t.length?[e,1,1,t[3],t[4]]:3==t.length?[e,1,1]:[e,1]}},Lt=e._equaliseTransform=function(t,r){r=g(r).replace(/\.{3}|\u2026/g,t),t=e.parseTransformString(t)||[],r=e.parseTransformString(r)||[];for(var i,n,a,s,o=b(t.length,r.length),l=[],h=[],u=0;u<o;u++){if(a=t[u]||Nt(r[u]),s=r[u]||Nt(a),a[0]!=s[0]||"r"==a[0].toLowerCase()&&(a[2]!=s[2]||a[3]!=s[3])||"s"==a[0].toLowerCase()&&(a[3]!=s[3]||a[4]!=s[4]))return;for(l[u]=[],h[u]=[],i=0,n=b(a.length,s.length);i<n;i++)i in a&&(l[u][i]=a[i]),i in s&&(h[u][i]=s[i])}return{from:l,to:h}};function Pt(t,e,r,i,n,a){null!=t?(this.a
=+t,this.b=+e,this.c=+r,this.d=+i,this.e=+n,this.f=+a):(this.a=1,this.b=0,this.c=0,this.d=1,this.e=0,this.f=0)}e._getContainer=function(t,r,i,n){var a;if(null!=(a=null!=n||e.is(t,"object")?t:l.doc.getElementById(t)))return a.tagName?null==r?{container:a,width:a.style.pixelWidth||a.offsetWidth,height:a.style.pixelHeight||a.offsetHeight}:{container:a,width:r,height:i}:{container:1,x:t,y:r,width:i,height:n}},e.pathToRelative=bt,e._engine={},e.path2curve=Tt,e.matrix=function(t,e,r,i,n,a){return new Pt(t,e,r,i,n,a)},function(t){function r(t){return t[0]*t[0]+t[1]*t[1]}function i(t){var e=m.sqrt(r(t));t[0]&&(t[0]/=e),t[1]&&(t[1]/=e)}t.add=function(t,e,r,i,n,a){var s,o,l,h,u=[[],[],[]],c=[[this.a,this.c,this.e],[this.b,this.d,this.f],[0,0,1]],f=[[t,r,n],[e,i,a],[0,0,1]];for(t&&t instanceof Pt&&(f=[[t.a,t.c,t.e],[t.b,t.d,t.f],[0,0,1]]),s=0;s<3;s++)for(o=0;o<3;o++){for(h=0,l=0;l<3;l++)h+=c[s][l]*f[l][o];u[s][o]=h}this.a=u[0][0],this.b=u[1][0],this.c=u[0][1],this.d=u[1][1],this.e=u[0][2],this.f=
u[1][2]},t.invert=function(){var t=this,e=t.a*t.d-t.b*t.c;return new Pt(t.d/e,-t.b/e,-t.c/e,t.a/e,(t.c*t.f-t.d*t.e)/e,(t.b*t.e-t.a*t.f)/e)},t.clone=function(){return new Pt(this.a,this.b,this.c,this.d,this.e,this.f)},t.translate=function(t,e){this.add(1,0,0,1,t,e)},t.scale=function(t,e,r,i){null==e&&(e=t),(r||i)&&this.add(1,0,0,1,r,i),this.add(t,0,0,e,0,0),(r||i)&&this.add(1,0,0,1,-r,-i)},t.rotate=function(t,r,i){t=e.rad(t),r=r||0,i=i||0;var n=+m.cos(t).toFixed(9),a=+m.sin(t).toFixed(9);this.add(n,a,-a,n,r,i),this.add(1,0,0,1,-r,-i)},t.x=function(t,e){return t*this.a+e*this.c+this.e},t.y=function(t,e){return t*this.b+e*this.d+this.f},t.get=function(t){return+this[g.fromCharCode(97+t)].toFixed(4)},t.toString=function(){return e.svg?"matrix("+[this.get(0),this.get(1),this.get(2),this.get(3),this.get(4),this.get(5)].join()+")":[this.get(0),this.get(2),this.get(1),this.get(3),0,0].join()},t.toFilter=function(){return"progid:DXImageTransform.Microsoft.Matrix(M11="+this.get(0)+", M12="+this.
get(2)+", M21="+this.get(1)+", M22="+this.get(3)+", Dx="+this.get(4)+", Dy="+this.get(5)+", sizingmethod='auto expand')"},t.offset=function(){return[this.e.toFixed(4),this.f.toFixed(4)]},t.split=function(){var t={};t.dx=this.e,t.dy=this.f;var n=[[this.a,this.c],[this.b,this.d]];t.scalex=m.sqrt(r(n[0])),i(n[0]),t.shear=n[0][0]*n[1][0]+n[0][1]*n[1][1],n[1]=[n[1][0]-n[0][0]*t.shear,n[1][1]-n[0][1]*t.shear],t.scaley=m.sqrt(r(n[1])),i(n[1]),t.shear/=t.scaley;var a=-n[0][1],s=n[1][1];return s<0?(t.rotate=e.deg(m.acos(s)),a<0&&(t.rotate=360-t.rotate)):t.rotate=e.deg(m.asin(a)),t.isSimple=!(+t.shear.toFixed(9)||t.scalex.toFixed(9)!=t.scaley.toFixed(9)&&t.rotate),t.isSuperSimple=!+t.shear.toFixed(9)&&t.scalex.toFixed(9)==t.scaley.toFixed(9)&&!t.rotate,t.noRotation=!+t.shear.toFixed(9)&&!t.rotate,t},t.toTransformString=function(t){var e=t||this.split();return e.isSimple?(e.scalex=+e.scalex.toFixed(4),e.scaley=+e.scaley.toFixed(4),e.rotate=+e.rotate.toFixed(4),(e.dx||e.dy?"t"+[e.dx,e.dy]:p)+(1!=e
.scalex||1!=e.scaley?"s"+[e.scalex,e.scaley,0,0]:p)+(e.rotate?"r"+[e.rotate,0,0]:p)):"m"+[this.get(0),this.get(1),this.get(2),this.get(3),this.get(4),this.get(5)]}}(Pt.prototype);for(var jt=function(){this.returnValue=!1},zt=function(){return this.originalEvent.preventDefault()},Ft=function(){this.cancelBubble=!0},Rt=function(){return this.originalEvent.stopPropagation()},It=function(t){var e=l.doc.documentElement.scrollTop||l.doc.body.scrollTop,r=l.doc.documentElement.scrollLeft||l.doc.body.scrollLeft;return{x:t.clientX+r,y:t.clientY+e}},qt=l.doc.addEventListener?function(t,e,r,i){var n=function(t){var e=It(t);return r.call(i,t,e.x,e.y)};if(t.addEventListener(e,n,!1),f&&x[e]){var a=function(e){for(var n=It(e),a=e,s=0,o=e.targetTouches&&e.targetTouches.length;s<o;s++)if(e.targetTouches[s].target==t){(e=e.targetTouches[s]).originalEvent=a,e.preventDefault=zt,e.stopPropagation=Rt;break}return r.call(i,e,n.x,n.y)};t.addEventListener(x[e],a,!1)}return function(){return t.
removeEventListener(e,n,!1),f&&x[e]&&t.removeEventListener(x[e],a,!1),!0}}:l.doc.attachEvent?function(t,e,r,i){var n=function(t){t=t||l.win.event;var e=l.doc.documentElement.scrollTop||l.doc.body.scrollTop,n=l.doc.documentElement.scrollLeft||l.doc.body.scrollLeft,a=t.clientX+n,s=t.clientY+e;return t.preventDefault=t.preventDefault||jt,t.stopPropagation=t.stopPropagation||Ft,r.call(i,t,a,s)};return t.attachEvent("on"+e,n),function(){return t.detachEvent("on"+e,n),!0}}:void 0,Dt=[],Ot=function(e){for(var r,i=e.clientX,n=e.clientY,a=l.doc.documentElement.scrollTop||l.doc.body.scrollTop,s=l.doc.documentElement.scrollLeft||l.doc.body.scrollLeft,o=Dt.length;o--;){if(r=Dt[o],f&&e.touches){for(var h,u=e.touches.length;u--;)if((h=e.touches[u]).identifier==r.el._drag.id){i=h.clientX,n=h.clientY,(e.originalEvent?e.originalEvent:e).preventDefault();break}}else e.preventDefault();var c,p=r.el.node,d=p.nextSibling,g=p.parentNode,v=p.style.display;l.win.opera&&g.removeChild(p),p.style.display="none",
c=r.el.paper.getElementByPoint(i,n),p.style.display=v,l.win.opera&&(d?g.insertBefore(p,d):g.appendChild(p)),c&&t("raphael.drag.over."+r.el.id,r.el,c),i+=s,n+=a,t("raphael.drag.move."+r.el.id,r.move_scope||r.el,i-r.el._drag.x,n-r.el._drag.y,i,n,e)}},Vt=function(r){e.unmousemove(Ot).unmouseup(Vt);for(var i,n=Dt.length;n--;)(i=Dt[n]).el._drag={},t("raphael.drag.end."+i.el.id,i.end_scope||i.start_scope||i.move_scope||i.el,r);Dt=[]},Wt=e.el={},Yt=v.length;Yt--;)!function(t){e[t]=Wt[t]=function(r,i){return e.is(r,"function")&&(this.events=this.events||[],this.events.push({name:t,f:r,unbind:qt(this.shape||this.node||l.doc,t,r,i||this)})),this},e["un"+t]=Wt["un"+t]=function(r){for(var i=this.events||[],n=i.length;n--;)i[n].name!=t||!e.is(r,"undefined")&&i[n].f!=r||(i[n].unbind(),i.splice(n,1),!i.length&&delete this.events);return this}}(v[Yt]);Wt.data=function(r,i){var n=Y[this.id]=Y[this.id]||{};if(0==arguments.length)return n;if(1==arguments.length){if(e.is(r,"object")){for(var a in r)r[o](a
)&&this.data(a,r[a]);return this}return t("raphael.data.get."+this.id,this,n[r],r),n[r]}return n[r]=i,t("raphael.data.set."+this.id,this,i,r),this},Wt.removeData=function(t){return null==t?delete Y[this.id]:Y[this.id]&&delete Y[this.id][t],this},Wt.getData=function(){return K(Y[this.id]||{})},Wt.hover=function(t,e,r,i){return this.mouseover(t,r).mouseout(e,i||r)},Wt.unhover=function(t,e){return this.unmouseover(t).unmouseout(e)};var Gt=[];Wt.drag=function(r,i,n,a,s,o){function h(h){(h.originalEvent||h).preventDefault();var u=h.clientX,c=h.clientY,p=l.doc.documentElement.scrollTop||l.doc.body.scrollTop,d=l.doc.documentElement.scrollLeft||l.doc.body.scrollLeft;if(this._drag.id=h.identifier,f&&h.touches)for(var g,v=h.touches.length;v--;)if(g=h.touches[v],this._drag.id=g.identifier,g.identifier==this._drag.id){u=g.clientX,c=g.clientY;break}this._drag.x=u+d,this._drag.y=c+p,!Dt.length&&e.mousemove(Ot).mouseup(Vt),Dt.push({el:this,move_scope:a,start_scope:s,end_scope:o}),i&&t.on(
"raphael.drag.start."+this.id,i),r&&t.on("raphael.drag.move."+this.id,r),n&&t.on("raphael.drag.end."+this.id,n),t("raphael.drag.start."+this.id,s||a||this,this._drag.x,this._drag.y,h)}return this._drag={},Gt.push({el:this,start:h}),this.mousedown(h),this},Wt.onDragOver=function(e){e?t.on("raphael.drag.over."+this.id,e):t.unbind("raphael.drag.over."+this.id)},Wt.undrag=function(){for(var r=Gt.length;r--;)Gt[r].el==this&&(this.unmousedown(Gt[r].start),Gt.splice(r,1),t.unbind("raphael.drag.*."+this.id));!Gt.length&&e.unmousemove(Ot).unmouseup(Vt),Dt=[]},i.circle=function(t,r,i){var n=e._engine.circle(this,t||0,r||0,i||0);return this.__set__&&this.__set__.push(n),n},i.rect=function(t,r,i,n,a){var s=e._engine.rect(this,t||0,r||0,i||0,n||0,a||0);return this.__set__&&this.__set__.push(s),s},i.ellipse=function(t,r,i,n){var a=e._engine.ellipse(this,t||0,r||0,i||0,n||0);return this.__set__&&this.__set__.push(a),a},i.path=function(t){t&&!e.is(t,S)&&!e.is(t[0],T)&&(t+=p);var r=e._engine.path(e.
format[c](e,arguments),this);return this.__set__&&this.__set__.push(r),r},i.image=function(t,r,i,n,a){var s=e._engine.image(this,t||"about:blank",r||0,i||0,n||0,a||0);return this.__set__&&this.__set__.push(s),s},i.text=function(t,r,i){var n=e._engine.text(this,t||0,r||0,g(i));return this.__set__&&this.__set__.push(n),n},i.set=function(t){!e.is(t,"array")&&(t=Array.prototype.splice.call(arguments,0,arguments.length));var r=new he(t);return this.__set__&&this.__set__.push(r),r.paper=this,r.type="set",r},i.setStart=function(t){this.__set__=t||this.set()},i.setFinish=function(t){var e=this.__set__;return delete this.__set__,e},i.getSize=function(){var t=this.canvas.parentNode;return{width:t.offsetWidth,height:t.offsetHeight}},i.setSize=function(t,r){return e._engine.setSize.call(this,t,r)},i.setViewBox=function(t,r,i,n,a){return e._engine.setViewBox.call(this,t,r,i,n,a)},i.top=i.bottom=null,i.raphael=e;function Ht(){return this.x+d+this.y+d+this.width+" × "+this.height}i.getElementByPoint
=function(t,e){var r,i,n,a,s,o,h,u=this,c=u.canvas,f=l.doc.elementFromPoint(t,e);if(l.win.opera&&"svg"==f.tagName){var p=(i=(r=c).getBoundingClientRect(),n=r.ownerDocument,a=n.body,s=n.documentElement,o=s.clientTop||a.clientTop||0,h=s.clientLeft||a.clientLeft||0,{y:i.top+(l.win.pageYOffset||s.scrollTop||a.scrollTop)-o,x:i.left+(l.win.pageXOffset||s.scrollLeft||a.scrollLeft)-h}),d=c.createSVGRect();d.x=t-p.x,d.y=e-p.y,d.width=d.height=1;var g=c.getIntersectionList(d,null);g.length&&(f=g[g.length-1])}if(!f)return null;for(;f.parentNode&&f!=c.parentNode&&!f.raphael;)f=f.parentNode;return f==u.canvas.parentNode&&(f=c),f=f&&f.raphael?u.getById(f.raphaelid):null},i.getElementsByBBox=function(t){var r=this.set();return this.forEach((function(i){e.isBBoxIntersect(i.getBBox(),t)&&r.push(i)})),r},i.getById=function(t){for(var e=this.bottom;e;){if(e.id==t)return e;e=e.next}return null},i.forEach=function(t,e){for(var r=this.bottom;r;){if(!1===t.call(e,r))return this;r=r.next}return this},i.
getElementsByPoint=function(t,e){var r=this.set();return this.forEach((function(i){i.isPointInside(t,e)&&r.push(i)})),r},Wt.isPointInside=function(t,r){var i=this.realPath=$[this.type](this);return this.attr("transform")&&this.attr("transform").length&&(i=e.transformPath(i,this.attr("transform"))),e.isPointInsidePath(i,t,r)},Wt.getBBox=function(t){if(this.removed)return{};var e=this._;return t?(!e.dirty&&e.bboxwt||(this.realPath=$[this.type](this),e.bboxwt=yt(this.realPath),e.bboxwt.toString=Ht,e.dirty=0),e.bboxwt):((e.dirty||e.dirtyT||!e.bbox)&&(!e.dirty&&this.realPath||(e.bboxwt=0,this.realPath=$[this.type](this)),e.bbox=yt(Z(this.realPath,this.matrix)),e.bbox.toString=Ht,e.dirty=e.dirtyT=0),e.bbox)},Wt.clone=function(){if(this.removed)return null;var t=this.paper[this.type]().attr(this.attr());return this.__set__&&this.__set__.push(t),t},Wt.glow=function(t){if("text"==this.type)return null;var e={width:((t=t||{}).width||10)+(+this.attr("stroke-width")||1),fill:t.fill||!1,opacity:
null==t.opacity?.5:t.opacity,offsetx:t.offsetx||0,offsety:t.offsety||0,color:t.color||"#000"},r=e.width/2,i=this.paper,n=i.set(),a=this.realPath||$[this.type](this);a=this.matrix?Z(a,this.matrix):a;for(var s=1;s<r+1;s++)n.push(i.path(a).attr({stroke:e.color,fill:e.fill?e.color:"none","stroke-linejoin":"round","stroke-linecap":"round","stroke-width":+(e.width/r*s).toFixed(3),opacity:+(e.opacity/r).toFixed(3)}));return n.insertBefore(this).translate(e.offsetx,e.offsety)};var Xt=function(t,r,i,n,a,s,o,l,h){return null==h?dt(t,r,i,n,a,s,o,l):e.findDotsAtSegment(t,r,i,n,a,s,o,l,function(t,e,r,i,n,a,s,o,l){if(!(l<0||dt(t,e,r,i,n,a,s,o)<l)){var h,u=.5,c=1-u;for(h=dt(t,e,r,i,n,a,s,o,c);w(h-l)>.01;)h=dt(t,e,r,i,n,a,s,o,c+=(h<l?1:-1)*(u/=2));return c}}(t,r,i,n,a,s,o,l,h))},Ut=function(t,r){return function(i,n,a){for(var s,o,l,h,u,c="",f={},p=0,d=0,g=(i=Tt(i)).length;d<g;d++){if("M"==(l=i[d])[0])s=+l[1],o=+l[2];else{if(p+(h=Xt(s,o,l[1],l[2],l[3],l[4],l[5],l[6]))>n){if(r&&!f.start){if(c+=["C"+(u=
Xt(s,o,l[1],l[2],l[3],l[4],l[5],l[6],n-p)).start.x,u.start.y,u.m.x,u.m.y,u.x,u.y],a)return c;f.start=c,c=["M"+u.x,u.y+"C"+u.n.x,u.n.y,u.end.x,u.end.y,l[5],l[6]].join(),p+=h,s=+l[5],o=+l[6];continue}if(!t&&!r)return{x:(u=Xt(s,o,l[1],l[2],l[3],l[4],l[5],l[6],n-p)).x,y:u.y,alpha:u.alpha}}p+=h,s=+l[5],o=+l[6]}c+=l.shift()+l}return f.end=c,(u=t?p:r?f:e.findDotsAtSegment(s,o,l[0],l[1],l[2],l[3],l[4],l[5],1)).alpha&&(u={x:u.x,y:u.y,alpha:u.alpha}),u}},$t=Ut(1),Zt=Ut(),Qt=Ut(0,1);e.getTotalLength=$t,e.getPointAtLength=Zt,e.getSubpath=function(t,e,r){if(this.getTotalLength(t)-r<1e-6)return Qt(t,e).end;var i=Qt(t,r,1);return e?Qt(i,e).end:i},Wt.getTotalLength=function(){var t=this.getPath();if(t)return this.node.getTotalLength?this.node.getTotalLength():$t(t)},Wt.getPointAtLength=function(t){var e=this.getPath();if(e)return Zt(e,t)},Wt.getPath=function(){var t,r=e._getPath[this.type];if("text"!=this.type&&"set"!=this.type)return r&&(t=r(this)),t},Wt.getSubpath=function(t,r){var i=this.getPath();
if(i)return e.getSubpath(i,t,r)};var Jt=e.easing_formulas={linear:function(t){return t},"<":function(t){return k(t,1.7)},">":function(t){return k(t,.48)},"<>":function(t){var e=.48-t/1.04,r=m.sqrt(.1734+e*e),i=r-e,n=-r-e,a=k(w(i),1/3)*(i<0?-1:1)+k(w(n),1/3)*(n<0?-1:1)+.5;return 3*(1-a)*a*a+a*a*a},backIn:function(t){var e=1.70158;return t*t*((e+1)*t-e)},backOut:function(t){var e=1.70158;return(t-=1)*t*((e+1)*t+e)+1},elastic:function(t){return t==!!t?t:k(2,-10*t)*m.sin(2*B*(t-.075)/.3)+1},bounce:function(t){var e=7.5625,r=2.75;return t<1/r?e*t*t:t<2/r?e*(t-=1.5/r)*t+.75:t<2.5/r?e*(t-=2.25/r)*t+.9375:e*(t-=2.625/r)*t+.984375}};Jt.easeIn=Jt["ease-in"]=Jt["<"],Jt.easeOut=Jt["ease-out"]=Jt[">"],Jt.easeInOut=Jt["ease-in-out"]=Jt["<>"],Jt["back-in"]=Jt.backIn,Jt["back-out"]=Jt.backOut;var Kt=[],te=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||function(t){setTimeout(t,16)},ee=
function(){for(var r=+new Date,i=0;i<Kt.length;i++){var n=Kt[i];if(!n.el.removed&&!n.paused){var a,s,l=r-n.start,h=n.ms,u=n.easing,c=n.from,f=n.diff,p=n.to,g=(n.t,n.el),v={},x={};if(n.initstatus?(l=(n.initstatus*n.anim.top-n.prev)/(n.percent-n.prev)*h,n.status=n.initstatus,delete n.initstatus,n.stop&&Kt.splice(i--,1)):n.status=(n.prev+(n.percent-n.prev)*(l/h))/n.anim.top,!(l<0))if(l<h){var y=u(l/h);for(var m in c)if(c[o](m)){switch(R[m]){case C:a=+c[m]+y*h*f[m];break;case"colour":a="rgb("+[re(L(c[m].r+y*h*f[m].r)),re(L(c[m].g+y*h*f[m].g)),re(L(c[m].b+y*h*f[m].b))].join(",")+")";break;case"path":a=[];for(var b=0,_=c[m].length;b<_;b++){a[b]=[c[m][b][0]];for(var w=1,k=c[m][b].length;w<k;w++)a[b][w]=+c[m][b][w]+y*h*f[m][b][w];a[b]=a[b].join(d)}a=a.join(d);break;case"transform":if(f[m].real)for(a=[],b=0,_=c[m].length;b<_;b++)for(a[b]=[c[m][b][0]],w=1,k=c[m][b].length;w<k;w++)a[b][w]=c[m][b][w]+y*h*f[m][b][w];else{var B=function(t){return+c[m][t]+y*h*f[m][t]};a=[["m",B(0),B(1),B(2),B(3),B(4)
,B(5)]]}break;case"csv":if("clip-rect"==m)for(a=[],b=4;b--;)a[b]=+c[m][b]+y*h*f[m][b];break;default:var S=[].concat(c[m]);for(a=[],b=g.paper.customAttributes[m].length;b--;)a[b]=+S[b]+y*h*f[m][b]}v[m]=a}g.attr(v),function(e,r,i){setTimeout((function(){t("raphael.anim.frame."+e,r,i)}))}(g.id,g,n.anim)}else{if(function(r,i,n){setTimeout((function(){t("raphael.anim.frame."+i.id,i,n),t("raphael.anim.finish."+i.id,i,n),e.is(r,"function")&&r.call(i)}))}(n.callback,g,n.anim),g.attr(p),Kt.splice(i--,1),n.repeat>1&&!n.next){for(s in p)p[o](s)&&(x[s]=n.totalOrigin[s]);n.el.attr(x),ae(n.anim,n.el,n.anim.percents[0],null,n.totalOrigin,n.repeat-1)}n.next&&!n.stop&&ae(n.anim,n.el,n.next,null,n.totalOrigin,n.repeat)}}}Kt.length&&te(ee)},re=function(t){return t>255?255:t<0?0:t};function ie(t,e,r,i,n,a){var s=3*e,o=3*(i-e)-s,l=1-s-o,h=3*r,u=3*(n-r)-h,c=1-h-u;function f(t){return((l*t+o)*t+s)*t}return function(t,e){var r=function(t,e){var r,i,n,a,h,u;for(n=t,u=0;u<8;u++){if(a=f(n)-t,w(a)<e)return n;if(w
(h=(3*l*n+2*o)*n+s)<1e-6)break;n-=a/h}if(i=1,(n=t)<(r=0))return r;if(n>i)return i;for(;r<i;){if(a=f(n),w(a-t)<e)return n;t>a?r=n:i=n,n=(i-r)/2+r}return n}(t,e);return((c*r+u)*r+h)*r}(t,1/(200*a))}function ne(t,e){var r=[],i={};if(this.ms=e,this.times=1,t){for(var n in t)t[o](n)&&(i[P(n)]=t[n],r.push(P(n)));r.sort(G)}this.anim=i,this.top=r[r.length-1],this.percents=r}function ae(r,i,a,s,l,h){a=P(a);var u,c,f,p,d,v,x=r.ms,y={},m={},b={};if(s)for(w=0,k=Kt.length;w<k;w++){var _=Kt[w];if(_.el.id==i.id&&_.anim==r){_.percent!=a?(Kt.splice(w,1),f=1):c=_,i.attr(_.totalOrigin);break}}else s=+m;for(var w=0,k=r.percents.length;w<k;w++){if(r.percents[w]==a||r.percents[w]>s*r.top){a=r.percents[w],d=r.percents[w-1]||0,x=x/r.top*(a-d),p=r.percents[w+1],u=r.anim[a];break}s&&i.attr(r.anim[r.percents[w]])}if(u){if(c)c.initstatus=s,c.start=new Date-c.ms*s;else{for(var B in u)if(u[o](B)&&(R[o](B)||i.paper.customAttributes[o](B)))switch(y[B]=i.attr(B),null==y[B]&&(y[B]=F[B]),m[B]=u[B],R[B]){case C:b[B]=(m[B
]-y[B])/x;break;case"colour":y[B]=e.getRGB(y[B]);var S=e.getRGB(m[B]);b[B]={r:(S.r-y[B].r)/x,g:(S.g-y[B].g)/x,b:(S.b-y[B].b)/x};break;case"path":var T=Tt(y[B],m[B]),A=T[1];for(y[B]=T[0],b[B]=[],w=0,k=y[B].length;w<k;w++){b[B][w]=[0];for(var M=1,E=y[B][w].length;M<E;M++)b[B][w][M]=(A[w][M]-y[B][w][M])/x}break;case"transform":var L=i._,j=Lt(L[B],m[B]);if(j)for(y[B]=j.from,m[B]=j.to,b[B]=[],b[B].real=!0,w=0,k=y[B].length;w<k;w++)for(b[B][w]=[y[B][w][0]],M=1,E=y[B][w].length;M<E;M++)b[B][w][M]=(m[B][w][M]-y[B][w][M])/x;else{var z=i.matrix||new Pt,I={_:{transform:L.transform},getBBox:function(){return i.getBBox(1)}};y[B]=[z.a,z.b,z.c,z.d,z.e,z.f],Et(I,m[B]),m[B]=I._.transform,b[B]=[(I.matrix.a-z.a)/x,(I.matrix.b-z.b)/x,(I.matrix.c-z.c)/x,(I.matrix.d-z.d)/x,(I.matrix.e-z.e)/x,(I.matrix.f-z.f)/x]}break;case"csv":var q=g(u[B]).split(n),D=g(y[B]).split(n);if("clip-rect"==B)for(y[B]=D,b[B]=[],w=D.length;w--;)b[B][w]=(q[w]-y[B][w])/x;m[B]=q;break;default:for(q=[].concat(u[B]),D=[].concat(y[B]),b[
B]=[],w=i.paper.customAttributes[B].length;w--;)b[B][w]=((q[w]||0)-(D[w]||0))/x}var O=u.easing,V=e.easing_formulas[O];if(!V)if((V=g(O).match(N))&&5==V.length){var W=V;V=function(t){return ie(t,+W[1],+W[2],+W[3],+W[4],x)}}else V=H;if(_={anim:r,percent:a,timestamp:v=u.start||r.start||+new Date,start:v+(r.del||0),status:0,initstatus:s||0,stop:!1,ms:x,easing:V,from:y,diff:b,to:m,el:i,callback:u.callback,prev:d,next:p,repeat:h||r.times,origin:i.attr(),totalOrigin:l},Kt.push(_),s&&!c&&!f&&(_.stop=!0,_.start=new Date-x*s,1==Kt.length))return ee();f&&(_.start=new Date-_.ms*s),1==Kt.length&&te(ee)}t("raphael.anim.start."+i.id,i,r)}}function se(t){for(var e=0;e<Kt.length;e++)Kt[e].el.paper==t&&Kt.splice(e--,1)}Wt.animateWith=function(t,r,i,n,a,s){var o=this;if(o.removed)return s&&s.call(o),o;var l=i instanceof ne?i:e.animation(i,n,a,s);ae(l,o,l.percents[0],null,o.attr());for(var h=0,u=Kt.length;h<u;h++)if(Kt[h].anim==r&&Kt[h].el==t){Kt[u-1].start=Kt[h].start;break}return o},Wt.onAnimation=
function(e){return e?t.on("raphael.anim.frame."+this.id,e):t.unbind("raphael.anim.frame."+this.id),this},ne.prototype.delay=function(t){var e=new ne(this.anim,this.ms);return e.times=this.times,e.del=+t||0,e},ne.prototype.repeat=function(t){var e=new ne(this.anim,this.ms);return e.del=this.del,e.times=m.floor(b(t,0))||1,e},e.animation=function(t,r,i,n){if(t instanceof ne)return t;!e.is(i,"function")&&i||(n=n||i||null,i=null),t=Object(t),r=+r||0;var a,s,l={};for(s in t)t[o](s)&&P(s)!=s&&P(s)+"%"!=s&&(a=!0,l[s]=t[s]);if(a)return i&&(l.easing=i),n&&(l.callback=n),new ne({100:l},r);if(n){var h=0;for(var u in t){var c=j(u);t[o](u)&&c>h&&(h=c)}!t[h+="%"].callback&&(t[h].callback=n)}return new ne(t,r)},Wt.animate=function(t,r,i,n){var a=this;if(a.removed)return n&&n.call(a),a;var s=t instanceof ne?t:e.animation(t,r,i,n);return ae(s,a,s.percents[0],null,a.attr()),a},Wt.setTime=function(t,e){return t&&null!=e&&this.status(t,_(e,t.ms)/t.ms),this},Wt.status=function(t,e){var r,i,n=[],a=0;if(null
!=e)return ae(t,this,-1,_(e,1)),this;for(r=Kt.length;a<r;a++)if((i=Kt[a]).el.id==this.id&&(!t||i.anim==t)){if(t)return i.status;n.push({anim:i.anim,status:i.status})}return t?0:n},Wt.pause=function(e){for(var r=0;r<Kt.length;r++)Kt[r].el.id!=this.id||e&&Kt[r].anim!=e||!1!==t("raphael.anim.pause."+this.id,this,Kt[r].anim)&&(Kt[r].paused=!0);return this},Wt.resume=function(e){for(var r=0;r<Kt.length;r++)if(Kt[r].el.id==this.id&&(!e||Kt[r].anim==e)){var i=Kt[r];!1!==t("raphael.anim.resume."+this.id,this,i.anim)&&(delete i.paused,this.status(i.anim,i.status))}return this},Wt.stop=function(e){for(var r=0;r<Kt.length;r++)Kt[r].el.id!=this.id||e&&Kt[r].anim!=e||!1!==t("raphael.anim.stop."+this.id,this,Kt[r].anim)&&Kt.splice(r--,1);return this},t.on("raphael.remove",se),t.on("raphael.clear",se),Wt.toString=function(){return"Raphaël’s object"};var oe,le,he=function(t){if(this.items=[],this.length=0,this.type="set",t)for(var e=0,r=t.length;e<r;e++)!t[e]||t[e].constructor!=Wt.constructor&&t[e]
.constructor!=he||(this[this.items.length]=this.items[this.items.length]=t[e],this.length++)},ue=he.prototype;for(var ce in ue.push=function(){for(var t,e,r=0,i=arguments.length;r<i;r++)!(t=arguments[r])||t.constructor!=Wt.constructor&&t.constructor!=he||(this[e=this.items.length]=this.items[e]=t,this.length++);return this},ue.pop=function(){return this.length&&delete this[this.length--],this.items.pop()},ue.forEach=function(t,e){for(var r=0,i=this.items.length;r<i;r++)if(!1===t.call(e,this.items[r],r))return this;return this},Wt)Wt[o](ce)&&(ue[ce]=function(t){return function(){var e=arguments;return this.forEach((function(r){r[t][c](r,e)}))}}(ce));return ue.attr=function(t,r){if(t&&e.is(t,T)&&e.is(t[0],"object"))for(var i=0,n=t.length;i<n;i++)this.items[i].attr(t[i]);else for(var a=0,s=this.items.length;a<s;a++)this.items[a].attr(t,r);return this},ue.clear=function(){for(;this.length;)this.pop()},ue.splice=function(t,e,r){t=t<0?b(this.length+t,0):t,e=b(0,_(this.length-t,e));var i,n=[]
,a=[],s=[];for(i=2;i<arguments.length;i++)s.push(arguments[i]);for(i=0;i<e;i++)a.push(this[t+i]);for(;i<this.length-t;i++)n.push(this[t+i]);var o=s.length;for(i=0;i<o+n.length;i++)this.items[t+i]=this[t+i]=i<o?s[i]:n[i-o];for(i=this.items.length=this.length-=e-o;this[i];)delete this[i++];return new he(a)},ue.exclude=function(t){for(var e=0,r=this.length;e<r;e++)if(this[e]==t)return this.splice(e,1),!0},ue.animate=function(t,r,i,n){(e.is(i,"function")||!i)&&(n=i||null);var a,s,o=this.items.length,l=o,h=this;if(!o)return this;n&&(s=function(){!--o&&n.call(h)}),i=e.is(i,S)?i:s;var u=e.animation(t,r,i,s);for(a=this.items[--l].animate(u);l--;)this.items[l]&&!this.items[l].removed&&this.items[l].animateWith(a,u,u),this.items[l]&&!this.items[l].removed||o--;return this},ue.insertAfter=function(t){for(var e=this.items.length;e--;)this.items[e].insertAfter(t);return this},ue.getBBox=function(){for(var t=[],e=[],r=[],i=[],n=this.items.length;n--;)if(!this.items[n].removed){var a=this.items[n].
getBBox();t.push(a.x),e.push(a.y),r.push(a.x+a.width),i.push(a.y+a.height)}return{x:t=_[c](0,t),y:e=_[c](0,e),x2:r=b[c](0,r),y2:i=b[c](0,i),width:r-t,height:i-e}},ue.clone=function(t){t=this.paper.set();for(var e=0,r=this.items.length;e<r;e++)t.push(this.items[e].clone());return t},ue.toString=function(){return"Raphaël‘s set"},ue.glow=function(t){var e=this.paper.set();return this.forEach((function(r,i){var n=r.glow(t);null!=n&&n.forEach((function(t,r){e.push(t)}))})),e},ue.isPointInside=function(t,e){var r=!1;return this.forEach((function(i){if(i.isPointInside(t,e))return r=!0,!1})),r},e.registerFont=function(t){if(!t.face)return t;this.fonts=this.fonts||{};var e={w:t.w,face:{},glyphs:{}},r=t.face["font-family"];for(var i in t.face)t.face[o](i)&&(e.face[i]=t.face[i]);if(this.fonts[r]?this.fonts[r].push(e):this.fonts[r]=[e],!t.svg)for(var n in e.face["units-per-em"]=j(t.face["units-per-em"],10),t.glyphs)if(t.glyphs[o](n)){var a=t.glyphs[n];if(e.glyphs[n]={w:a.w,k:{},d:a.d&&"M"+a.d.
replace(/[mlcxtrv]/g,(function(t){return{l:"L",c:"C",x:"z",t:"m",r:"l",v:"c"}[t]||"M"}))+"z"},a.k)for(var s in a.k)a[o](s)&&(e.glyphs[n].k[s]=a.k[s])}return t},i.getFont=function(t,r,i,n){if(n=n||"normal",i=i||"normal",r=+r||{normal:400,bold:700,lighter:300,bolder:800}[r]||400,e.fonts){var a,s=e.fonts[t];if(!s){var l=new RegExp("(^|\\s)"+t.replace(/[^\w\d\s+!~.:_-]/g,p)+"(\\s|$)","i");for(var h in e.fonts)if(e.fonts[o](h)&&l.test(h)){s=e.fonts[h];break}}if(s)for(var u=0,c=s.length;u<c&&((a=s[u]).face["font-weight"]!=r||a.face["font-style"]!=i&&a.face["font-style"]||a.face["font-stretch"]!=n);u++);return a}},i.print=function(t,r,i,a,s,o,l,h){o=o||"middle",l=b(_(l||0,1),-1),h=b(_(h||1,3),1);var u,c=g(i).split(p),f=0,d=0,v=p;if(e.is(a,"string")&&(a=this.getFont(a)),a){u=(s||16)/a.face["units-per-em"];for(var x=a.face.bbox.split(n),y=+x[0],m=x[3]-x[1],w=0,k=+x[1]+("baseline"==o?m+ +a.face.descent:m/2),B=0,C=c.length;B<C;B++){if("\n"==c[B])f=0,T=0,d=0,w+=m*h;else{var S=d&&a.glyphs[c[B-1]]||
{},T=a.glyphs[c[B]];f+=d?(S.w||a.w)+(S.k&&S.k[c[B]]||0)+a.w*l:0,d=1}T&&T.d&&(v+=e.transformPath(T.d,["t",f*u,w*u,"s",u,u,y,k,"t",(t-y)/u,(r-k)/u]))}}return this.path(v).attr({fill:"#000",stroke:"none"})},i.add=function(t){if(e.is(t,"array"))for(var r,i=this.set(),n=0,s=t.length;n<s;n++)r=t[n]||{},a[o](r.type)&&i.push(this[r.type]().attr(r));return i},e.format=function(t,r){var i=e.is(r,T)?[0].concat(r):arguments;return t&&e.is(t,S)&&i.length-1&&(t=t.replace(s,(function(t,e){return null==i[++e]?p:i[e]}))),t||p},e.fullfill=(oe=/\{([^\}]+)\}/g,le=/(?:(?:^|\.)(.+?)(?=\[|\.|$|\()|\[('|")(.+?)\2\])(\(\))?/g,function(t,e){return String(t).replace(oe,(function(t,r){return function(t,e,r){var i=r;return e.replace(le,(function(t,e,r,n,a){e=e||n,i&&(e in i&&(i=i[e]),"function"==typeof i&&a&&(i=i()))})),i=(null==i||i==r?t:i)+""}(t,r,e)}))}),e.ninja=function(){if(h.was)l.win.Raphael=h.is;else{window.Raphael=void 0;try{delete window.Raphael}catch(t){}}return e},e.st=ue,t.on("raphael.DOMload",(
function(){r=!0})),function(t,r,i){null==t.readyState&&t.addEventListener&&(t.addEventListener(r,i=function(){t.removeEventListener(r,i,!1),t.readyState="complete"},!1),t.readyState="loading"),function r(){/in/.test(t.readyState)?setTimeout(r,9):e.eve("raphael.DOMload")}()}(document,"DOMContentLoaded"),e}.apply(e,i),void 0===n||(t.exports=n)},"./dev/raphael.svg.js":function(t,e,r){var i,n;i=[r("./dev/raphael.core.js")],n=function(t){if(!t||t.svg){var e="hasOwnProperty",r=String,i=parseFloat,n=parseInt,a=Math,s=a.max,o=a.abs,l=a.pow,h=/[, ]+/,u=t.eve,c="",f=" ",p="http://www.w3.org/1999/xlink",d={block:"M5,0 0,2.5 5,5z",classic:"M5,0 0,2.5 5,5 3.5,3 3.5,2z",diamond:"M2.5,0 5,2.5 2.5,5 0,2.5z",open:"M6,1 1,3.5 6,6",oval:"M2.5,0A2.5,2.5,0,0,1,2.5,5 2.5,2.5,0,0,1,2.5,0z"},g={};t.toString=function(){return"Your browser supports SVG.\nYou are running Raphaël "+this.version};var v=function(i,n){if(n)for(var a in"string"==typeof i&&(i=v(i)),n)n[e](a)&&("xlink:"==a.substring(0,6)?i.
setAttributeNS(p,a.substring(6),r(n[a])):i.setAttribute(a,r(n[a])));else(i=t._g.doc.createElementNS("http://www.w3.org/2000/svg",i)).style&&(i.style.webkitTapHighlightColor="rgba(0,0,0,0)");return i},x=function(e,n){var h="linear",u=e.id+n,f=.5,p=.5,d=e.node,g=e.paper,x=d.style,m=t._g.doc.getElementById(u);if(!m){if(n=(n=r(n).replace(t._radial_gradient,(function(t,e,r){if(h="radial",e&&r){f=i(e);var n=2*((p=i(r))>.5)-1;l(f-.5,2)+l(p-.5,2)>.25&&(p=a.sqrt(.25-l(f-.5,2))*n+.5)&&.5!=p&&(p=p.toFixed(5)-1e-5*n)}return c}))).split(/\s*\-\s*/),"linear"==h){var b=n.shift();if(b=-i(b),isNaN(b))return null;var _=[0,0,a.cos(t.rad(b)),a.sin(t.rad(b))],w=1/(s(o(_[2]),o(_[3]))||1);_[2]*=w,_[3]*=w,_[2]<0&&(_[0]=-_[2],_[2]=0),_[3]<0&&(_[1]=-_[3],_[3]=0)}var k=t._parseDots(n);if(!k)return null;if(u=u.replace(/[\(\)\s,\xb0#]/g,"_"),e.gradient&&u!=e.gradient.id&&(g.defs.removeChild(e.gradient),delete e.gradient),!e.gradient){m=v(h+"Gradient",{id:u}),e.gradient=m,v(m,"radial"==h?{fx:f,fy:p}:{x1:_[0],y1:_[1
],x2:_[2],y2:_[3],gradientTransform:e.matrix.invert()}),g.defs.appendChild(m);for(var B=0,C=k.length;B<C;B++)m.appendChild(v("stop",{offset:k[B].offset?k[B].offset:B?"100%":"0%","stop-color":k[B].color||"#fff","stop-opacity":isFinite(k[B].opacity)?k[B].opacity:1}))}}return v(d,{fill:y(u),opacity:1,"fill-opacity":1}),x.fill=c,x.opacity=1,x.fillOpacity=1,1},y=function(t){if((e=document.documentMode)&&(9===e||10===e))return"url('#"+t+"')";var e,r=document.location;return"url('"+(r.protocol+"//"+r.host+r.pathname+r.search)+"#"+t+"')"},m=function(t){var e=t.getBBox(1);v(t.pattern,{patternTransform:t.matrix.invert()+" translate("+e.x+","+e.y+")"})},b=function(i,n,a){if("path"==i.type){for(var s,o,l,h,u,f=r(n).toLowerCase().split("-"),p=i.paper,x=a?"end":"start",y=i.node,m=i.attrs,b=m["stroke-width"],_=f.length,w="classic",k=3,B=3,C=5;_--;)switch(f[_]){case"block":case"classic":case"oval":case"diamond":case"open":case"none":w=f[_];break;case"wide":B=5;break;case"narrow":B=2;break;case"long":k
=5;break;case"short":k=2}if("open"==w?(k+=2,B+=2,C+=2,l=1,h=a?4:1,u={fill:"none",stroke:m.stroke}):(h=l=k/2,u={fill:m.stroke,stroke:"none"}),i._.arrows?a?(i._.arrows.endPath&&g[i._.arrows.endPath]--,i._.arrows.endMarker&&g[i._.arrows.endMarker]--):(i._.arrows.startPath&&g[i._.arrows.startPath]--,i._.arrows.startMarker&&g[i._.arrows.startMarker]--):i._.arrows={},"none"!=w){var S="raphael-marker-"+w,T="raphael-marker-"+x+w+k+B+"-obj"+i.id;t._g.doc.getElementById(S)?g[S]++:(p.defs.appendChild(v(v("path"),{"stroke-linecap":"round",d:d[w],id:S})),g[S]=1);var A,M=t._g.doc.getElementById(T);M?(g[T]++,A=M.getElementsByTagName("use")[0]):(M=v(v("marker"),{id:T,markerHeight:B,markerWidth:k,orient:"auto",refX:h,refY:B/2}),A=v(v("use"),{"xlink:href":"#"+S,transform:(a?"rotate(180 "+k/2+" "+B/2+") ":c)+"scale("+k/C+","+B/C+")","stroke-width":(1/((k/C+B/C)/2)).toFixed(4)}),M.appendChild(A),p.defs.appendChild(M),g[T]=1),v(A,u);var E=l*("diamond"!=w&&"oval"!=w);a?(s=i._.arrows.startdx*b||0,o=t.
getTotalLength(m.path)-E*b):(s=E*b,o=t.getTotalLength(m.path)-(i._.arrows.enddx*b||0)),(u={})["marker-"+x]="url(#"+T+")",(o||s)&&(u.d=t.getSubpath(m.path,s,o)),v(y,u),i._.arrows[x+"Path"]=S,i._.arrows[x+"Marker"]=T,i._.arrows[x+"dx"]=E,i._.arrows[x+"Type"]=w,i._.arrows[x+"String"]=n}else a?(s=i._.arrows.startdx*b||0,o=t.getTotalLength(m.path)-s):(s=0,o=t.getTotalLength(m.path)-(i._.arrows.enddx*b||0)),i._.arrows[x+"Path"]&&v(y,{d:t.getSubpath(m.path,s,o)}),delete i._.arrows[x+"Path"],delete i._.arrows[x+"Marker"],delete i._.arrows[x+"dx"],delete i._.arrows[x+"Type"],delete i._.arrows[x+"String"];for(u in g)if(g[e](u)&&!g[u]){var N=t._g.doc.getElementById(u);N&&N.parentNode.removeChild(N)}}},_={"-":[3,1],".":[1,1],"-.":[3,1,1,1],"-..":[3,1,1,1,1,1],". ":[1,3],"- ":[4,3],"--":[8,3],"- .":[4,3,1,3],"--.":[8,3,1,3],"--..":[8,3,1,3,1,3]},w=function(t,e,i){if(e=_[r(e).toLowerCase()]){for(var n=t.attrs["stroke-width"]||"1",a={round:n,square:n,butt:0}[t.attrs["stroke-linecap"]||i[
"stroke-linecap"]]||0,s=[],o=e.length;o--;)s[o]=e[o]*n+(o%2?1:-1)*a;v(t.node,{"stroke-dasharray":s.join(",")})}else v(t.node,{"stroke-dasharray":"none"})},k=function(i,a){var l=i.node,u=i.attrs,f=l.style.visibility;for(var d in l.style.visibility="hidden",a)if(a[e](d)){if(!t._availableAttrs[e](d))continue;var g=a[d];switch(u[d]=g,d){case"blur":i.blur(g);break;case"title":var y=l.getElementsByTagName("title");if(y.length&&(y=y[0]))y.firstChild.nodeValue=g;else{y=v("title");var _=t._g.doc.createTextNode(g);y.appendChild(_),l.appendChild(y)}break;case"href":case"target":var k=l.parentNode;if("a"!=k.tagName.toLowerCase()){var C=v("a");k.insertBefore(C,l),C.appendChild(l),k=C}"target"==d?k.setAttributeNS(p,"show","blank"==g?"new":g):k.setAttributeNS(p,d,g);break;case"cursor":l.style.cursor=g;break;case"transform":i.transform(g);break;case"arrow-start":b(i,g);break;case"arrow-end":b(i,g,1);break;case"clip-rect":var S=r(g).split(h);if(4==S.length){i.clip&&i.clip.parentNode.parentNode.
removeChild(i.clip.parentNode);var T=v("clipPath"),A=v("rect");T.id=t.createUUID(),v(A,{x:S[0],y:S[1],width:S[2],height:S[3]}),T.appendChild(A),i.paper.defs.appendChild(T),v(l,{"clip-path":"url(#"+T.id+")"}),i.clip=A}if(!g){var M=l.getAttribute("clip-path");if(M){var E=t._g.doc.getElementById(M.replace(/(^url\(#|\)$)/g,c));E&&E.parentNode.removeChild(E),v(l,{"clip-path":c}),delete i.clip}}break;case"path":"path"==i.type&&(v(l,{d:g?u.path=t._pathToAbsolute(g):"M0,0"}),i._.dirty=1,i._.arrows&&("startString"in i._.arrows&&b(i,i._.arrows.startString),"endString"in i._.arrows&&b(i,i._.arrows.endString,1)));break;case"width":if(l.setAttribute(d,g),i._.dirty=1,!u.fx)break;d="x",g=u.x;case"x":u.fx&&(g=-u.x-(u.width||0));case"rx":if("rx"==d&&"rect"==i.type)break;case"cx":l.setAttribute(d,g),i.pattern&&m(i),i._.dirty=1;break;case"height":if(l.setAttribute(d,g),i._.dirty=1,!u.fy)break;d="y",g=u.y;case"y":u.fy&&(g=-u.y-(u.height||0));case"ry":if("ry"==d&&"rect"==i.type)break;case"cy":l.
setAttribute(d,g),i.pattern&&m(i),i._.dirty=1;break;case"r":"rect"==i.type?v(l,{rx:g,ry:g}):l.setAttribute(d,g),i._.dirty=1;break;case"src":"image"==i.type&&l.setAttributeNS(p,"href",g);break;case"stroke-width":1==i._.sx&&1==i._.sy||(g/=s(o(i._.sx),o(i._.sy))||1),l.setAttribute(d,g),u["stroke-dasharray"]&&w(i,u["stroke-dasharray"],a),i._.arrows&&("startString"in i._.arrows&&b(i,i._.arrows.startString),"endString"in i._.arrows&&b(i,i._.arrows.endString,1));break;case"stroke-dasharray":w(i,g,a);break;case"fill":var N=r(g).match(t._ISURL);if(N){T=v("pattern");var L=v("image");T.id=t.createUUID(),v(T,{x:0,y:0,patternUnits:"userSpaceOnUse",height:1,width:1}),v(L,{x:0,y:0,"xlink:href":N[1]}),T.appendChild(L),function(e){t._preload(N[1],(function(){var t=this.offsetWidth,r=this.offsetHeight;v(e,{width:t,height:r}),v(L,{width:t,height:r})}))}(T),i.paper.defs.appendChild(T),v(l,{fill:"url(#"+T.id+")"}),i.pattern=T,i.pattern&&m(i);break}var P=t.getRGB(g);if(P.error){if(("circle"==i.type||
"ellipse"==i.type||"r"!=r(g).charAt())&&x(i,g)){if("opacity"in u||"fill-opacity"in u){var j=t._g.doc.getElementById(l.getAttribute("fill").replace(/^url\(#|\)$/g,c));if(j){var z=j.getElementsByTagName("stop");v(z[z.length-1],{"stop-opacity":("opacity"in u?u.opacity:1)*("fill-opacity"in u?u["fill-opacity"]:1)})}}u.gradient=g,u.fill="none";break}}else delete a.gradient,delete u.gradient,!t.is(u.opacity,"undefined")&&t.is(a.opacity,"undefined")&&v(l,{opacity:u.opacity}),!t.is(u["fill-opacity"],"undefined")&&t.is(a["fill-opacity"],"undefined")&&v(l,{"fill-opacity":u["fill-opacity"]});P[e]("opacity")&&v(l,{"fill-opacity":P.opacity>1?P.opacity/100:P.opacity});case"stroke":P=t.getRGB(g),l.setAttribute(d,P.hex),"stroke"==d&&P[e]("opacity")&&v(l,{"stroke-opacity":P.opacity>1?P.opacity/100:P.opacity}),"stroke"==d&&i._.arrows&&("startString"in i._.arrows&&b(i,i._.arrows.startString),"endString"in i._.arrows&&b(i,i._.arrows.endString,1));break;case"gradient":("circle"==i.type||"ellipse"==i.type||
"r"!=r(g).charAt())&&x(i,g);break;case"opacity":u.gradient&&!u[e]("stroke-opacity")&&v(l,{"stroke-opacity":g>1?g/100:g});case"fill-opacity":if(u.gradient){(j=t._g.doc.getElementById(l.getAttribute("fill").replace(/^url\(#|\)$/g,c)))&&(z=j.getElementsByTagName("stop"),v(z[z.length-1],{"stop-opacity":g}));break}default:"font-size"==d&&(g=n(g,10)+"px");var F=d.replace(/(\-.)/g,(function(t){return t.substring(1).toUpperCase()}));l.style[F]=g,i._.dirty=1,l.setAttribute(d,g)}}B(i,a),l.style.visibility=f},B=function(i,a){if("text"==i.type&&(a[e]("text")||a[e]("font")||a[e]("font-size")||a[e]("x")||a[e]("y"))){var s=i.attrs,o=i.node,l=o.firstChild?n(t._g.doc.defaultView.getComputedStyle(o.firstChild,c).getPropertyValue("font-size"),10):10;if(a[e]("text")){for(s.text=a.text;o.firstChild;)o.removeChild(o.firstChild);for(var h,u=r(a.text).split("\n"),f=[],p=0,d=u.length;p<d;p++)h=v("tspan"),p&&v(h,{dy:1.2*l,x:s.x}),h.appendChild(t._g.doc.createTextNode(u[p])),o.appendChild(h),f[p]=h}else for(p=0,
d=(f=o.getElementsByTagName("tspan")).length;p<d;p++)p?v(f[p],{dy:1.2*l,x:s.x}):v(f[0],{dy:0});v(o,{x:s.x,y:s.y}),i._.dirty=1;var g=i._getBBox(),x=s.y-(g.y+g.height/2);x&&t.is(x,"finite")&&v(f[0],{dy:x})}},C=function(t){return t.parentNode&&"a"===t.parentNode.tagName.toLowerCase()?t.parentNode:t},S=function(e,r){this[0]=this.node=e,e.raphael=!0,this.id=("0000"+(Math.random()*Math.pow(36,5)<<0).toString(36)).slice(-5),e.raphaelid=this.id,this.matrix=t.matrix(),this.realPath=null,this.paper=r,this.attrs=this.attrs||{},this._={transform:[],sx:1,sy:1,deg:0,dx:0,dy:0,dirty:1},!r.bottom&&(r.bottom=this),this.prev=r.top,r.top&&(r.top.next=this),r.top=this,this.next=null},T=t.el;S.prototype=T,T.constructor=S,t._engine.path=function(t,e){var r=v("path");e.canvas&&e.canvas.appendChild(r);var i=new S(r,e);return i.type="path",k(i,{fill:"none",stroke:"#000",path:t}),i},T.rotate=function(t,e,n){if(this.removed)return this;if((t=r(t).split(h)).length-1&&(e=i(t[1]),n=i(t[2])),t=i(t[0]),null==n&&(e=n)
,null==e||null==n){var a=this.getBBox(1);e=a.x+a.width/2,n=a.y+a.height/2}return this.transform(this._.transform.concat([["r",t,e,n]])),this},T.scale=function(t,e,n,a){if(this.removed)return this;if((t=r(t).split(h)).length-1&&(e=i(t[1]),n=i(t[2]),a=i(t[3])),t=i(t[0]),null==e&&(e=t),null==a&&(n=a),null==n||null==a)var s=this.getBBox(1);return n=null==n?s.x+s.width/2:n,a=null==a?s.y+s.height/2:a,this.transform(this._.transform.concat([["s",t,e,n,a]])),this},T.translate=function(t,e){return this.removed||((t=r(t).split(h)).length-1&&(e=i(t[1])),t=i(t[0])||0,e=+e||0,this.transform(this._.transform.concat([["t",t,e]]))),this},T.transform=function(r){var i=this._;if(null==r)return i.transform;if(t._extractTransform(this,r),this.clip&&v(this.clip,{transform:this.matrix.invert()}),this.pattern&&m(this),this.node&&v(this.node,{transform:this.matrix}),1!=i.sx||1!=i.sy){var n=this.attrs[e]("stroke-width")?this.attrs["stroke-width"]:1;this.attr({"stroke-width":n})}return this},T.hide=function(){
return this.removed||(this.node.style.display="none"),this},T.show=function(){return this.removed||(this.node.style.display=""),this},T.remove=function(){var e=C(this.node);if(!this.removed&&e.parentNode){var r=this.paper;for(var i in r.__set__&&r.__set__.exclude(this),u.unbind("raphael.*.*."+this.id),this.gradient&&r.defs.removeChild(this.gradient),t._tear(this,r),e.parentNode.removeChild(e),this.removeData(),this)this[i]="function"==typeof this[i]?t._removedFactory(i):null;this.removed=!0}},T._getBBox=function(){if("none"==this.node.style.display){this.show();var t=!0}var e,r=!1;this.paper.canvas.parentElement?e=this.paper.canvas.parentElement.style:this.paper.canvas.parentNode&&(e=this.paper.canvas.parentNode.style),e&&"none"==e.display&&(r=!0,e.display="");var i={};try{i=this.node.getBBox()}catch(t){i={x:this.node.clientLeft,y:this.node.clientTop,width:this.node.clientWidth,height:this.node.clientHeight}}finally{i=i||{},r&&(e.display="none")}return t&&this.hide(),i},T.attr=function
(r,i){if(this.removed)return this;if(null==r){var n={};for(var a in this.attrs)this.attrs[e](a)&&(n[a]=this.attrs[a]);return n.gradient&&"none"==n.fill&&(n.fill=n.gradient)&&delete n.gradient,n.transform=this._.transform,n}if(null==i&&t.is(r,"string")){if("fill"==r&&"none"==this.attrs.fill&&this.attrs.gradient)return this.attrs.gradient;if("transform"==r)return this._.transform;for(var s=r.split(h),o={},l=0,c=s.length;l<c;l++)(r=s[l])in this.attrs?o[r]=this.attrs[r]:t.is(this.paper.customAttributes[r],"function")?o[r]=this.paper.customAttributes[r].def:o[r]=t._availableAttrs[r];return c-1?o:o[s[0]]}if(null==i&&t.is(r,"array")){for(o={},l=0,c=r.length;l<c;l++)o[r[l]]=this.attr(r[l]);return o}if(null!=i){var f={};f[r]=i}else null!=r&&t.is(r,"object")&&(f=r);for(var p in f)u("raphael.attr."+p+"."+this.id,this,f[p]);for(p in this.paper.customAttributes)if(this.paper.customAttributes[e](p)&&f[e](p)&&t.is(this.paper.customAttributes[p],"function")){var d=this.paper.customAttributes[p].apply(
this,[].concat(f[p]));for(var g in this.attrs[p]=f[p],d)d[e](g)&&(f[g]=d[g])}return k(this,f),this},T.toFront=function(){if(this.removed)return this;var e=C(this.node);e.parentNode.appendChild(e);var r=this.paper;return r.top!=this&&t._tofront(this,r),this},T.toBack=function(){if(this.removed)return this;var e=C(this.node),r=e.parentNode;r.insertBefore(e,r.firstChild),t._toback(this,this.paper);this.paper;return this},T.insertAfter=function(e){if(this.removed||!e)return this;var r=C(this.node),i=C(e.node||e[e.length-1].node);return i.nextSibling?i.parentNode.insertBefore(r,i.nextSibling):i.parentNode.appendChild(r),t._insertafter(this,e,this.paper),this},T.insertBefore=function(e){if(this.removed||!e)return this;var r=C(this.node),i=C(e.node||e[0].node);return i.parentNode.insertBefore(r,i),t._insertbefore(this,e,this.paper),this},T.blur=function(e){var r=this;if(0!=+e){var i=v("filter"),n=v("feGaussianBlur");r.attrs.blur=e,i.id=t.createUUID(),v(n,{stdDeviation:+e||1.5}),i.appendChild(
n),r.paper.defs.appendChild(i),r._blur=i,v(r.node,{filter:"url(#"+i.id+")"})}else r._blur&&(r._blur.parentNode.removeChild(r._blur),delete r._blur,delete r.attrs.blur),r.node.removeAttribute("filter");return r},t._engine.circle=function(t,e,r,i){var n=v("circle");t.canvas&&t.canvas.appendChild(n);var a=new S(n,t);return a.attrs={cx:e,cy:r,r:i,fill:"none",stroke:"#000"},a.type="circle",v(n,a.attrs),a},t._engine.rect=function(t,e,r,i,n,a){var s=v("rect");t.canvas&&t.canvas.appendChild(s);var o=new S(s,t);return o.attrs={x:e,y:r,width:i,height:n,rx:a||0,ry:a||0,fill:"none",stroke:"#000"},o.type="rect",v(s,o.attrs),o},t._engine.ellipse=function(t,e,r,i,n){var a=v("ellipse");t.canvas&&t.canvas.appendChild(a);var s=new S(a,t);return s.attrs={cx:e,cy:r,rx:i,ry:n,fill:"none",stroke:"#000"},s.type="ellipse",v(a,s.attrs),s},t._engine.image=function(t,e,r,i,n,a){var s=v("image");v(s,{x:r,y:i,width:n,height:a,preserveAspectRatio:"none"}),s.setAttributeNS(p,"href",e),t.canvas&&t.canvas.appendChild(
s);var o=new S(s,t);return o.attrs={x:r,y:i,width:n,height:a,src:e},o.type="image",o},t._engine.text=function(e,r,i,n){var a=v("text");e.canvas&&e.canvas.appendChild(a);var s=new S(a,e);return s.attrs={x:r,y:i,"text-anchor":"middle",text:n,"font-family":t._availableAttrs["font-family"],"font-size":t._availableAttrs["font-size"],stroke:"none",fill:"#000"},s.type="text",k(s,s.attrs),s},t._engine.setSize=function(t,e){return this.width=t||this.width,this.height=e||this.height,this.canvas.setAttribute("width",this.width),this.canvas.setAttribute("height",this.height),this._viewBox&&this.setViewBox.apply(this,this._viewBox),this},t._engine.create=function(){var e=t._getContainer.apply(0,arguments),r=e&&e.container;if(!r)throw new Error("SVG container not found.");var i,n=e.x,a=e.y,s=e.width,o=e.height,l=v("svg"),h="overflow:hidden;";return n=n||0,a=a||0,v(l,{height:o=o||342,version:1.1,width:s=s||512,xmlns:"http://www.w3.org/2000/svg","xmlns:xlink":"http://www.w3.org/1999/xlink"}),1==r?(l.
style.cssText=h+"position:absolute;left:"+n+"px;top:"+a+"px",t._g.doc.body.appendChild(l),i=1):(l.style.cssText=h+"position:relative",r.firstChild?r.insertBefore(l,r.firstChild):r.appendChild(l)),(r=new t._Paper).width=s,r.height=o,r.canvas=l,r.clear(),r._left=r._top=0,i&&(r.renderfix=function(){}),r.renderfix(),r},t._engine.setViewBox=function(t,e,r,i,n){u("raphael.setViewBox",this,this._viewBox,[t,e,r,i,n]);var a,o,l=this.getSize(),h=s(r/l.width,i/l.height),c=this.top,p=n?"xMidYMid meet":"xMinYMin";for(null==t?(this._vbSize&&(h=1),delete this._vbSize,a="0 0 "+this.width+f+this.height):(this._vbSize=h,a=t+f+e+f+r+f+i),v(this.canvas,{viewBox:a,preserveAspectRatio:p});h&&c;)o="stroke-width"in c.attrs?c.attrs["stroke-width"]:1,c.attr({"stroke-width":o}),c._.dirty=1,c._.dirtyT=1,c=c.prev;return this._viewBox=[t,e,r,i,!!n],this},t.prototype.renderfix=function(){var t,e=this.canvas,r=e.style;try{t=e.getScreenCTM()||e.createSVGMatrix()}catch(r){t=e.createSVGMatrix()}var i=-t.e%1,n=-t.f%1;(i
||n)&&(i&&(this._left=(this._left+i)%1,r.left=this._left+"px"),n&&(this._top=(this._top+n)%1,r.top=this._top+"px"))},t.prototype.clear=function(){t.eve("raphael.clear",this);for(var e=this.canvas;e.firstChild;)e.removeChild(e.firstChild);this.bottom=this.top=null,(this.desc=v("desc")).appendChild(t._g.doc.createTextNode("Created with Raphaël "+t.version)),e.appendChild(this.desc),e.appendChild(this.defs=v("defs"))},t.prototype.remove=function(){for(var e in u("raphael.remove",this),this.canvas.parentNode&&this.canvas.parentNode.removeChild(this.canvas),this)this[e]="function"==typeof this[e]?t._removedFactory(e):null};var A=t.st;for(var M in T)T[e](M)&&!A[e](M)&&(A[M]=function(t){return function(){var e=arguments;return this.forEach((function(r){r[t].apply(r,e)}))}}(M))}}.apply(e,i),void 0===n||(t.exports=n)},"./dev/raphael.vml.js":function(t,e,r){var i,n;i=[r("./dev/raphael.core.js")],n=function(t){if(!t||t.vml){var e="hasOwnProperty",r=String,i=parseFloat,n=Math,a=n.round,s=n.max,o=
n.min,l=n.abs,h="fill",u=/[, ]+/,c=t.eve,f=" ",p="",d={M:"m",L:"l",C:"c",Z:"x",m:"t",l:"r",c:"v",z:"x"},g=/([clmz]),?([^clmz]*)/gi,v=/ progid:\S+Blur\([^\)]+\)/g,x=/-?[^,\s-]+/g,y="position:absolute;left:0;top:0;width:1px;height:1px;behavior:url(#default#VML)",m=21600,b={path:1,rect:1,image:1},_={circle:1,ellipse:1},w=function(e,r,i){var n=t.matrix();return n.rotate(-e,.5,.5),{dx:n.x(r,i),dy:n.y(r,i)}},k=function(t,e,r,i,n,a){var s=t._,o=t.matrix,u=s.fillpos,c=t.node,p=c.style,d=1,g="",v=m/e,x=m/r;if(p.visibility="hidden",e&&r){if(c.coordsize=l(v)+f+l(x),p.rotation=a*(e*r<0?-1:1),a){var y=w(a,i,n);i=y.dx,n=y.dy}if(e<0&&(g+="x"),r<0&&(g+=" y")&&(d=-1),p.flip=g,c.coordorigin=i*-v+f+n*-x,u||s.fillsize){var b=c.getElementsByTagName(h);b=b&&b[0],c.removeChild(b),u&&(y=w(a,o.x(u[0],u[1]),o.y(u[0],u[1])),b.position=y.dx*d+f+y.dy*d),s.fillsize&&(b.size=s.fillsize[0]*l(e)+f+s.fillsize[1]*l(r)),c.appendChild(b)}p.visibility="visible"}};t.toString=function(){return"Your browser doesn’t support SVG. Falling down to VML.\nYou are running Raphaël "
+this.version};var B,C=function(t,e,i){for(var n=r(e).toLowerCase().split("-"),a=i?"end":"start",s=n.length,o="classic",l="medium",h="medium";s--;)switch(n[s]){case"block":case"classic":case"oval":case"diamond":case"open":case"none":o=n[s];break;case"wide":case"narrow":h=n[s];break;case"long":case"short":l=n[s]}var u=t.node.getElementsByTagName("stroke")[0];u[a+"arrow"]=o,u[a+"arrowlength"]=l,u[a+"arrowwidth"]=h},S=function(n,l){n.attrs=n.attrs||{};var c=n.node,v=n.attrs,y=c.style,w=b[n.type]&&(l.x!=v.x||l.y!=v.y||l.width!=v.width||l.height!=v.height||l.cx!=v.cx||l.cy!=v.cy||l.rx!=v.rx||l.ry!=v.ry||l.r!=v.r),S=_[n.type]&&(v.cx!=l.cx||v.cy!=l.cy||v.r!=l.r||v.rx!=l.rx||v.ry!=l.ry),A=n;for(var M in l)l[e](M)&&(v[M]=l[M]);if(w&&(v.path=t._getPath[n.type](n),n._.dirty=1),l.href&&(c.href=l.href),l.title&&(c.title=l.title),l.target&&(c.target=l.target),l.cursor&&(y.cursor=l.cursor),"blur"in l&&n.blur(l.blur),(l.path&&"path"==n.type||w)&&(c.path=function(e){var i=/[ahqstv]/gi,n=t.
_pathToAbsolute;if(r(e).match(i)&&(n=t._path2curve),i=/[clmz]/g,n==t._pathToAbsolute&&!r(e).match(i)){var s=r(e).replace(g,(function(t,e,r){var i=[],n="m"==e.toLowerCase(),s=d[e];return r.replace(x,(function(t){n&&2==i.length&&(s+=i+d["m"==e?"l":"L"],i=[]),i.push(a(t*m))})),s+i}));return s}var o,l,h=n(e);s=[];for(var u=0,c=h.length;u<c;u++){o=h[u],"z"==(l=h[u][0].toLowerCase())&&(l="x");for(var v=1,y=o.length;v<y;v++)l+=a(o[v]*m)+(v!=y-1?",":p);s.push(l)}return s.join(f)}(~r(v.path).toLowerCase().indexOf("r")?t._pathToAbsolute(v.path):v.path),n._.dirty=1,"image"==n.type&&(n._.fillpos=[v.x,v.y],n._.fillsize=[v.width,v.height],k(n,1,1,0,0,0))),"transform"in l&&n.transform(l.transform),S){var E=+v.cx,N=+v.cy,L=+v.rx||+v.r||0,P=+v.ry||+v.r||0;c.path=t.format("ar{0},{1},{2},{3},{4},{1},{4},{1}x",a((E-L)*m),a((N-P)*m),a((E+L)*m),a((N+P)*m),a(E*m)),n._.dirty=1}if("clip-rect"in l){var j=r(l["clip-rect"]).split(u);if(4==j.length){j[2]=+j[2]+ +j[0],j[3]=+j[3]+ +j[1];var z=c.clipRect||t._g.doc.
createElement("div"),F=z.style;F.clip=t.format("rect({1}px {2}px {3}px {0}px)",j),c.clipRect||(F.position="absolute",F.top=0,F.left=0,F.width=n.paper.width+"px",F.height=n.paper.height+"px",c.parentNode.insertBefore(z,c),z.appendChild(c),c.clipRect=z)}l["clip-rect"]||c.clipRect&&(c.clipRect.style.clip="auto")}if(n.textpath){var R=n.textpath.style;l.font&&(R.font=l.font),l["font-family"]&&(R.fontFamily='"'+l["font-family"].split(",")[0].replace(/^['"]+|['"]+$/g,p)+'"'),l["font-size"]&&(R.fontSize=l["font-size"]),l["font-weight"]&&(R.fontWeight=l["font-weight"]),l["font-style"]&&(R.fontStyle=l["font-style"])}if("arrow-start"in l&&C(A,l["arrow-start"]),"arrow-end"in l&&C(A,l["arrow-end"],1),null!=l.opacity||null!=l.fill||null!=l.src||null!=l.stroke||null!=l["stroke-width"]||null!=l["stroke-opacity"]||null!=l["fill-opacity"]||null!=l["stroke-dasharray"]||null!=l["stroke-miterlimit"]||null!=l["stroke-linejoin"]||null!=l["stroke-linecap"]){var I=c.getElementsByTagName(h);if(!(I=I&&I[0])&&(I=
B(h)),"image"==n.type&&l.src&&(I.src=l.src),l.fill&&(I.on=!0),null!=I.on&&"none"!=l.fill&&null!==l.fill||(I.on=!1),I.on&&l.fill){var q=r(l.fill).match(t._ISURL);if(q){I.parentNode==c&&c.removeChild(I),I.rotate=!0,I.src=q[1],I.type="tile";var D=n.getBBox(1);I.position=D.x+f+D.y,n._.fillpos=[D.x,D.y],t._preload(q[1],(function(){n._.fillsize=[this.offsetWidth,this.offsetHeight]}))}else I.color=t.getRGB(l.fill).hex,I.src=p,I.type="solid",t.getRGB(l.fill).error&&(A.type in{circle:1,ellipse:1}||"r"!=r(l.fill).charAt())&&T(A,l.fill,I)&&(v.fill="none",v.gradient=l.fill,I.rotate=!1)}if("fill-opacity"in l||"opacity"in l){var O=((+v["fill-opacity"]+1||2)-1)*((+v.opacity+1||2)-1)*((+t.getRGB(l.fill).o+1||2)-1);O=o(s(O,0),1),I.opacity=O,I.src&&(I.color="none")}c.appendChild(I);var V=c.getElementsByTagName("stroke")&&c.getElementsByTagName("stroke")[0],W=!1;!V&&(W=V=B("stroke")),(l.stroke&&"none"!=l.stroke||l["stroke-width"]||null!=l["stroke-opacity"]||l["stroke-dasharray"]||l["stroke-miterlimit"]||
l["stroke-linejoin"]||l["stroke-linecap"])&&(V.on=!0),("none"==l.stroke||null===l.stroke||null==V.on||0==l.stroke||0==l["stroke-width"])&&(V.on=!1);var Y=t.getRGB(l.stroke);V.on&&l.stroke&&(V.color=Y.hex),O=((+v["stroke-opacity"]+1||2)-1)*((+v.opacity+1||2)-1)*((+Y.o+1||2)-1);var G=.75*(i(l["stroke-width"])||1);if(O=o(s(O,0),1),null==l["stroke-width"]&&(G=v["stroke-width"]),l["stroke-width"]&&(V.weight=G),G&&G<1&&(O*=G)&&(V.weight=1),V.opacity=O,l["stroke-linejoin"]&&(V.joinstyle=l["stroke-linejoin"]||"miter"),V.miterlimit=l["stroke-miterlimit"]||8,l["stroke-linecap"]&&(V.endcap="butt"==l["stroke-linecap"]?"flat":"square"==l["stroke-linecap"]?"square":"round"),"stroke-dasharray"in l){var H={"-":"shortdash",".":"shortdot","-.":"shortdashdot","-..":"shortdashdotdot",". ":"dot","- ":"dash","--":"longdash","- .":"dashdot","--.":"longdashdot","--..":"longdashdotdot"};V.dashstyle=H[e](l["stroke-dasharray"])?H[l["stroke-dasharray"]]:p}W&&c.appendChild(V)}if("text"==A.type){A.paper.canvas.
style.display=p;var X=A.paper.span,U=v.font&&v.font.match(/\d+(?:\.\d*)?(?=px)/);y=X.style,v.font&&(y.font=v.font),v["font-family"]&&(y.fontFamily=v["font-family"]),v["font-weight"]&&(y.fontWeight=v["font-weight"]),v["font-style"]&&(y.fontStyle=v["font-style"]),U=i(v["font-size"]||U&&U[0])||10,y.fontSize=100*U+"px",A.textpath.string&&(X.innerHTML=r(A.textpath.string).replace(/</g,"&#60;").replace(/&/g,"&#38;").replace(/\n/g,"<br>"));var $=X.getBoundingClientRect();A.W=v.w=($.right-$.left)/100,A.H=v.h=($.bottom-$.top)/100,A.X=v.x,A.Y=v.y+A.H/2,("x"in l||"y"in l)&&(A.path.v=t.format("m{0},{1}l{2},{1}",a(v.x*m),a(v.y*m),a(v.x*m)+1));for(var Z=["x","y","text","font","font-family","font-weight","font-style","font-size"],Q=0,J=Z.length;Q<J;Q++)if(Z[Q]in l){A._.dirty=1;break}switch(v["text-anchor"]){case"start":A.textpath.style["v-text-align"]="left",A.bbx=A.W/2;break;case"end":A.textpath.style["v-text-align"]="right",A.bbx=-A.W/2;break;default:A.textpath.style["v-text-align"]="center",A.bbx=
0}A.textpath.style["v-text-kern"]=!0}},T=function(e,a,s){e.attrs=e.attrs||{};e.attrs;var o=Math.pow,l="linear",h=".5 .5";if(e.attrs.gradient=a,a=(a=r(a).replace(t._radial_gradient,(function(t,e,r){return l="radial",e&&r&&(e=i(e),r=i(r),o(e-.5,2)+o(r-.5,2)>.25&&(r=n.sqrt(.25-o(e-.5,2))*(2*(r>.5)-1)+.5),h=e+f+r),p}))).split(/\s*\-\s*/),"linear"==l){var u=a.shift();if(u=-i(u),isNaN(u))return null}var c=t._parseDots(a);if(!c)return null;if(e=e.shape||e.node,c.length){e.removeChild(s),s.on=!0,s.method="none",s.color=c[0].color,s.color2=c[c.length-1].color;for(var d=[],g=0,v=c.length;g<v;g++)c[g].offset&&d.push(c[g].offset+f+c[g].color);s.colors=d.length?d.join():"0% "+s.color,"radial"==l?(s.type="gradientTitle",s.focus="100%",s.focussize="0 0",s.focusposition=h,s.angle=0):(s.type="gradient",s.angle=(270-u)%360),e.appendChild(s)}return 1},A=function(e,r){this[0]=this.node=e,e.raphael=!0,this.id=t._oid++,e.raphaelid=this.id,this.X=0,this.Y=0,this.attrs={},this.paper=r,this.matrix=t.matrix(),
this._={transform:[],sx:1,sy:1,dx:0,dy:0,deg:0,dirty:1,dirtyT:1},!r.bottom&&(r.bottom=this),this.prev=r.top,r.top&&(r.top.next=this),r.top=this,this.next=null},M=t.el;A.prototype=M,M.constructor=A,M.transform=function(e){if(null==e)return this._.transform;var i,n=this.paper._viewBoxShift,a=n?"s"+[n.scale,n.scale]+"-1-1t"+[n.dx,n.dy]:p;n&&(i=e=r(e).replace(/\.{3}|\u2026/g,this._.transform||p)),t._extractTransform(this,a+e);var s,o=this.matrix.clone(),l=this.skew,h=this.node,u=~r(this.attrs.fill).indexOf("-"),c=!r(this.attrs.fill).indexOf("url(");if(o.translate(1,1),c||u||"image"==this.type)if(l.matrix="1 0 0 1",l.offset="0 0",s=o.split(),u&&s.noRotation||!s.isSimple){h.style.filter=o.toFilter();var d=this.getBBox(),g=this.getBBox(1),v=d.x-g.x,x=d.y-g.y;h.coordorigin=v*-m+f+x*-m,k(this,1,1,v,x,0)}else h.style.filter=p,k(this,s.scalex,s.scaley,s.dx,s.dy,s.rotate);else h.style.filter=p,l.matrix=r(o),l.offset=o.offset();return null!==i&&(this._.transform=i,t._extractTransform(this,i)),this}
,M.rotate=function(t,e,n){if(this.removed)return this;if(null!=t){if((t=r(t).split(u)).length-1&&(e=i(t[1]),n=i(t[2])),t=i(t[0]),null==n&&(e=n),null==e||null==n){var a=this.getBBox(1);e=a.x+a.width/2,n=a.y+a.height/2}return this._.dirtyT=1,this.transform(this._.transform.concat([["r",t,e,n]])),this}},M.translate=function(t,e){return this.removed||((t=r(t).split(u)).length-1&&(e=i(t[1])),t=i(t[0])||0,e=+e||0,this._.bbox&&(this._.bbox.x+=t,this._.bbox.y+=e),this.transform(this._.transform.concat([["t",t,e]]))),this},M.scale=function(t,e,n,a){if(this.removed)return this;if((t=r(t).split(u)).length-1&&(e=i(t[1]),n=i(t[2]),a=i(t[3]),isNaN(n)&&(n=null),isNaN(a)&&(a=null)),t=i(t[0]),null==e&&(e=t),null==a&&(n=a),null==n||null==a)var s=this.getBBox(1);return n=null==n?s.x+s.width/2:n,a=null==a?s.y+s.height/2:a,this.transform(this._.transform.concat([["s",t,e,n,a]])),this._.dirtyT=1,this},M.hide=function(){return!this.removed&&(this.node.style.display="none"),this},M.show=function(){return!this
.removed&&(this.node.style.display=p),this},M.auxGetBBox=t.el.getBBox,M.getBBox=function(){var t=this.auxGetBBox();if(this.paper&&this.paper._viewBoxShift){var e={},r=1/this.paper._viewBoxShift.scale;return e.x=t.x-this.paper._viewBoxShift.dx,e.x*=r,e.y=t.y-this.paper._viewBoxShift.dy,e.y*=r,e.width=t.width*r,e.height=t.height*r,e.x2=e.x+e.width,e.y2=e.y+e.height,e}return t},M._getBBox=function(){return this.removed?{}:{x:this.X+(this.bbx||0)-this.W/2,y:this.Y-this.H,width:this.W,height:this.H}},M.remove=function(){if(!this.removed&&this.node.parentNode){for(var e in this.paper.__set__&&this.paper.__set__.exclude(this),t.eve.unbind("raphael.*.*."+this.id),t._tear(this,this.paper),this.node.parentNode.removeChild(this.node),this.shape&&this.shape.parentNode.removeChild(this.shape),this)this[e]="function"==typeof this[e]?t._removedFactory(e):null;this.removed=!0}},M.attr=function(r,i){if(this.removed)return this;if(null==r){var n={};for(var a in this.attrs)this.attrs[e](a)&&(n[a]=this.
attrs[a]);return n.gradient&&"none"==n.fill&&(n.fill=n.gradient)&&delete n.gradient,n.transform=this._.transform,n}if(null==i&&t.is(r,"string")){if(r==h&&"none"==this.attrs.fill&&this.attrs.gradient)return this.attrs.gradient;for(var s=r.split(u),o={},l=0,f=s.length;l<f;l++)(r=s[l])in this.attrs?o[r]=this.attrs[r]:t.is(this.paper.customAttributes[r],"function")?o[r]=this.paper.customAttributes[r].def:o[r]=t._availableAttrs[r];return f-1?o:o[s[0]]}if(this.attrs&&null==i&&t.is(r,"array")){for(o={},l=0,f=r.length;l<f;l++)o[r[l]]=this.attr(r[l]);return o}var p;for(var d in null!=i&&((p={})[r]=i),null==i&&t.is(r,"object")&&(p=r),p)c("raphael.attr."+d+"."+this.id,this,p[d]);if(p){for(d in this.paper.customAttributes)if(this.paper.customAttributes[e](d)&&p[e](d)&&t.is(this.paper.customAttributes[d],"function")){var g=this.paper.customAttributes[d].apply(this,[].concat(p[d]));for(var v in this.attrs[d]=p[d],g)g[e](v)&&(p[v]=g[v])}p.text&&"text"==this.type&&(this.textpath.string=p.text),S(this,
p)}return this},M.toFront=function(){return!this.removed&&this.node.parentNode.appendChild(this.node),this.paper&&this.paper.top!=this&&t._tofront(this,this.paper),this},M.toBack=function(){return this.removed||this.node.parentNode.firstChild!=this.node&&(this.node.parentNode.insertBefore(this.node,this.node.parentNode.firstChild),t._toback(this,this.paper)),this},M.insertAfter=function(e){return this.removed||(e.constructor==t.st.constructor&&(e=e[e.length-1]),e.node.nextSibling?e.node.parentNode.insertBefore(this.node,e.node.nextSibling):e.node.parentNode.appendChild(this.node),t._insertafter(this,e,this.paper)),this},M.insertBefore=function(e){return this.removed||(e.constructor==t.st.constructor&&(e=e[0]),e.node.parentNode.insertBefore(this.node,e.node),t._insertbefore(this,e,this.paper)),this},M.blur=function(e){var r=this.node.runtimeStyle,i=r.filter;return i=i.replace(v,p),0!=+e?(this.attrs.blur=e,r.filter=i+f+" progid:DXImageTransform.Microsoft.Blur(pixelradius="+(+e||1.5)+")",
r.margin=t.format("-{0}px 0 0 -{0}px",a(+e||1.5))):(r.filter=i,r.margin=0,delete this.attrs.blur),this},t._engine.path=function(t,e){var r=B("shape");r.style.cssText=y,r.coordsize=m+f+m,r.coordorigin=e.coordorigin;var i=new A(r,e),n={fill:"none",stroke:"#000"};t&&(n.path=t),i.type="path",i.path=[],i.Path=p,S(i,n),e.canvas&&e.canvas.appendChild(r);var a=B("skew");return a.on=!0,r.appendChild(a),i.skew=a,i.transform(p),i},t._engine.rect=function(e,r,i,n,a,s){var o=t._rectPath(r,i,n,a,s),l=e.path(o),h=l.attrs;return l.X=h.x=r,l.Y=h.y=i,l.W=h.width=n,l.H=h.height=a,h.r=s,h.path=o,l.type="rect",l},t._engine.ellipse=function(t,e,r,i,n){var a=t.path();a.attrs;return a.X=e-i,a.Y=r-n,a.W=2*i,a.H=2*n,a.type="ellipse",S(a,{cx:e,cy:r,rx:i,ry:n}),a},t._engine.circle=function(t,e,r,i){var n=t.path();n.attrs;return n.X=e-i,n.Y=r-i,n.W=n.H=2*i,n.type="circle",S(n,{cx:e,cy:r,r:i}),n},t._engine.image=function(e,r,i,n,a,s){var o=t._rectPath(i,n,a,s),l=e.path(o).attr({stroke:"none"}),u=l.attrs,c=l.node,f=
c.getElementsByTagName(h)[0];return u.src=r,l.X=u.x=i,l.Y=u.y=n,l.W=u.width=a,l.H=u.height=s,u.path=o,l.type="image",f.parentNode==c&&c.removeChild(f),f.rotate=!0,f.src=r,f.type="tile",l._.fillpos=[i,n],l._.fillsize=[a,s],c.appendChild(f),k(l,1,1,0,0,0),l},t._engine.text=function(e,i,n,s){var o=B("shape"),l=B("path"),h=B("textpath");i=i||0,n=n||0,s=s||"",l.v=t.format("m{0},{1}l{2},{1}",a(i*m),a(n*m),a(i*m)+1),l.textpathok=!0,h.string=r(s),h.on=!0,o.style.cssText=y,o.coordsize=m+f+m,o.coordorigin="0 0";var u=new A(o,e),c={fill:"#000",stroke:"none",font:t._availableAttrs.font,text:s};u.shape=o,u.path=l,u.textpath=h,u.type="text",u.attrs.text=r(s),u.attrs.x=i,u.attrs.y=n,u.attrs.w=1,u.attrs.h=1,S(u,c),o.appendChild(h),o.appendChild(l),e.canvas.appendChild(o);var d=B("skew");return d.on=!0,o.appendChild(d),u.skew=d,u.transform(p),u},t._engine.setSize=function(e,r){var i=this.canvas.style;return this.width=e,this.height=r,e==+e&&(e+="px"),r==+r&&(r+="px"),i.width=e,i.height=r,i.clip=
"rect(0 "+e+" "+r+" 0)",this._viewBox&&t._engine.setViewBox.apply(this,this._viewBox),this},t._engine.setViewBox=function(e,r,i,n,a){t.eve("raphael.setViewBox",this,this._viewBox,[e,r,i,n,a]);var s,o,l=this.getSize(),h=l.width,u=l.height;return a&&(i*(s=u/n)<h&&(e-=(h-i*s)/2/s),n*(o=h/i)<u&&(r-=(u-n*o)/2/o)),this._viewBox=[e,r,i,n,!!a],this._viewBoxShift={dx:-e,dy:-r,scale:l},this.forEach((function(t){t.transform("...")})),this},t._engine.initWin=function(t){var e=t.document;e.styleSheets.length<31?e.createStyleSheet().addRule(".rvml","behavior:url(#default#VML)"):e.styleSheets[0].addRule(".rvml","behavior:url(#default#VML)");try{!e.namespaces.rvml&&e.namespaces.add("rvml","urn:schemas-microsoft-com:vml"),B=function(t){return e.createElement("<rvml:"+t+' class="rvml">')}}catch(t){B=function(t){return e.createElement("<"+t+' xmlns="urn:schemas-microsoft.com:vml" class="rvml">')}}},t._engine.initWin(t._g.win),t._engine.create=function(){var e=t._getContainer.apply(0,arguments),r=e.
container,i=e.height,n=e.width,a=e.x,s=e.y;if(!r)throw new Error("VML container not found.");var o=new t._Paper,l=o.canvas=t._g.doc.createElement("div"),h=l.style;return a=a||0,s=s||0,n=n||512,i=i||342,o.width=n,o.height=i,n==+n&&(n+="px"),i==+i&&(i+="px"),o.coordsize="21600000 21600000",o.coordorigin="0 0",o.span=t._g.doc.createElement("span"),o.span.style.cssText="position:absolute;left:-9999em;top:-9999em;padding:0;margin:0;line-height:1;",l.appendChild(o.span),h.cssText=t.format("top:0;left:0;width:{0};height:{1};display:inline-block;position:relative;clip:rect(0 {0} {1} 0);overflow:hidden",n,i),1==r?(t._g.doc.body.appendChild(l),h.left=a+"px",h.top=s+"px",h.position="absolute"):r.firstChild?r.insertBefore(l,r.firstChild):r.appendChild(l),o.renderfix=function(){},o},t.prototype.clear=function(){t.eve("raphael.clear",this),this.canvas.innerHTML=p,this.span=t._g.doc.createElement("span"),this.span.style.cssText=
"position:absolute;left:-9999em;top:-9999em;padding:0;margin:0;line-height:1;display:inline;",this.canvas.appendChild(this.span),this.bottom=this.top=null},t.prototype.remove=function(){for(var e in t.eve("raphael.remove",this),this.canvas.parentNode.removeChild(this.canvas),this)this[e]="function"==typeof this[e]?t._removedFactory(e):null;return!0};var E=t.st;for(var N in M)M[e](N)&&!E[e](N)&&(E[N]=function(t){return function(){var e=arguments;return this.forEach((function(r){r[t].apply(r,e)}))}}(N))}}.apply(e,i),void 0===n||(t.exports=n)},"./node_modules/eve-raphael/eve.js":function(t,e,r){var i,n,a,s,o,l,h,u,c,f,p,d,g,v,x;s="0.5.0",o="hasOwnProperty",l=/[\.\/]/,h=/\s*,\s*/,u=function(t,e){return t-e},c={n:{}},f=function(){for(var t=0,e=this.length;t<e;t++)if(void 0!==this[t])return this[t]},p=function(){for(var t=this.length;--t;)if(void 0!==this[t])return this[t]},d=Object.prototype.toString,g=String,v=Array.isArray||function(t){return t instanceof Array||"[object Array]"==d.call(t
)},x=function(t,e){var r,i=a,s=Array.prototype.slice.call(arguments,2),o=x.listeners(t),l=0,h=[],c={},d=[],g=n;d.firstDefined=f,d.lastDefined=p,n=t,a=0;for(var v=0,y=o.length;v<y;v++)"zIndex"in o[v]&&(h.push(o[v].zIndex),o[v].zIndex<0&&(c[o[v].zIndex]=o[v]));for(h.sort(u);h[l]<0;)if(r=c[h[l++]],d.push(r.apply(e,s)),a)return a=i,d;for(v=0;v<y;v++)if("zIndex"in(r=o[v]))if(r.zIndex==h[l]){if(d.push(r.apply(e,s)),a)break;do{if((r=c[h[++l]])&&d.push(r.apply(e,s)),a)break}while(r)}else c[r.zIndex]=r;else if(d.push(r.apply(e,s)),a)break;return a=i,n=g,d},x._events=c,x.listeners=function(t){var e,r,i,n,a,s,o,h,u=v(t)?t:t.split(l),f=c,p=[f],d=[];for(n=0,a=u.length;n<a;n++){for(h=[],s=0,o=p.length;s<o;s++)for(r=[(f=p[s].n)[u[n]],f["*"]],i=2;i--;)(e=r[i])&&(h.push(e),d=d.concat(e.f||[]));p=h}return d},x.separator=function(t){t?(t="["+(t=g(t).replace(/(?=[\.\^\]\[\-])/g,"\\"))+"]",l=new RegExp(t)):l=/[\.\/]/},x.on=function(t,e){if("function"!=typeof e)return function(){};for(var r=v(t)?v(t[0])?t:[
t]:g(t).split(h),i=0,n=r.length;i<n;i++)!function(t){for(var r,i=v(t)?t:g(t).split(l),n=c,a=0,s=i.length;a<s;a++)n=(n=n.n).hasOwnProperty(i[a])&&n[i[a]]||(n[i[a]]={n:{}});for(n.f=n.f||[],a=0,s=n.f.length;a<s;a++)if(n.f[a]==e){r=!0;break}!r&&n.f.push(e)}(r[i]);return function(t){+t==+t&&(e.zIndex=+t)}},x.f=function(t){var e=[].slice.call(arguments,1);return function(){x.apply(null,[t,null].concat(e).concat([].slice.call(arguments,0)))}},x.stop=function(){a=1},x.nt=function(t){var e=v(n)?n.join("."):n;return t?new RegExp("(?:\\.|\\/|^)"+t+"(?:\\.|\\/|$)").test(e):e},x.nts=function(){return v(n)?n:n.split(l)},x.off=x.unbind=function(t,e){if(t){var r=v(t)?v(t[0])?t:[t]:g(t).split(h);if(r.length>1)for(var i=0,n=r.length;i<n;i++)x.off(r[i],e);else{r=v(t)?t:g(t).split(l);var a,s,u,f,p,d=[c];for(i=0,n=r.length;i<n;i++)for(f=0;f<d.length;f+=u.length-2){if(u=[f,1],a=d[f].n,"*"!=r[i])a[r[i]]&&u.push(a[r[i]]);else for(s in a)a[o](s)&&u.push(a[s]);d.splice.apply(d,u)}for(i=0,n=d.length;i<n;i++)for(
a=d[i];a.n;){if(e){if(a.f){for(f=0,p=a.f.length;f<p;f++)if(a.f[f]==e){a.f.splice(f,1);break}!a.f.length&&delete a.f}for(s in a.n)if(a.n[o](s)&&a.n[s].f){var y=a.n[s].f;for(f=0,p=y.length;f<p;f++)if(y[f]==e){y.splice(f,1);break}!y.length&&delete a.n[s].f}}else for(s in delete a.f,a.n)a.n[o](s)&&a.n[s].f&&delete a.n[s].f;a=a.n}}}else x._events=c={n:{}}},x.once=function(t,e){var r=function(){return x.off(t,r),e.apply(this,arguments)};return x.on(t,r)},x.version=s,x.toString=function(){return"You are running Eve 0.5.0"},t.exports?t.exports=x:void 0===(i=function(){return x}.apply(e,[]))||(t.exports=i)}})}));!function(t){var e=function(e,i){var n=i.getElementsByClassName(e)[0];if(!n&&((n=document.createElement("canvas")).className=e,n.style.direction="ltr",n.style.position="absolute",n.style.left="0px",n.style.top="0px",i.appendChild(n),!n.getContext))throw new Error("Canvas is not available.");this.element=n;var r=this.context=n.getContext("2d");this.pixelRatio=t.plot.browser.getPixelRatio
(r);var l=t(i).width(),o=t(i).height();this.resize(l,o),this.SVGContainer=null,this.SVG={},this._textCache={}};function i(t,e){t.transform.baseVal.clear(),e&&e.forEach((function(e){t.transform.baseVal.appendItem(e)}))}e.prototype.resize=function(t,e){t=t<10?10:t,e=e<10?10:e;var i=this.element,n=this.context,r=this.pixelRatio;this.width!==t&&(i.width=t*r,i.style.width=t+"px",this.width=t),this.height!==e&&(i.height=e*r,i.style.height=e+"px",this.height=e),n.restore(),n.save(),n.scale(r,r)},e.prototype.clear=function(){this.context.clearRect(0,0,this.width,this.height)},e.prototype.render=function(){var t=this._textCache;for(var e in t)if(hasOwnProperty.call(t,e)){var i=this.getSVGLayer(e),n=t[e],r=i.style.display;for(var l in i.style.display="none",n)if(hasOwnProperty.call(n,l)){var o=n[l];for(var s in o)if(hasOwnProperty.call(o,s)){for(var a,h=o[s],d=h.positions,p=0;d[p];p++)if((a=d[p]).active)a.rendered||(i.appendChild(a.element),a.rendered=!0);else if(d.splice(p--,1),a.rendered){for(
;a.element.firstChild;)a.element.removeChild(a.element.firstChild);a.element.parentNode.removeChild(a.element)}0===d.length&&(h.measured?h.measured=!1:delete o[s])}}i.style.display=r}},e.prototype.getSVGLayer=function(t){var e,i=this.SVG[t];i||(this.SVGContainer?e=this.SVGContainer.firstChild:(this.SVGContainer=document.createElement("div"),this.SVGContainer.className="flot-svg",this.SVGContainer.style.position="absolute",this.SVGContainer.style.top="0px",this.SVGContainer.style.left="0px",this.SVGContainer.style.height="100%",this.SVGContainer.style.width="100%",this.SVGContainer.style.pointerEvents="none",this.element.parentNode.appendChild(this.SVGContainer),(e=document.createElementNS("http://www.w3.org/2000/svg","svg")).style.width="100%",e.style.height="100%",this.SVGContainer.appendChild(e)),(i=document.createElementNS("http://www.w3.org/2000/svg","g")).setAttribute("class",t),i.style.position="absolute",i.style.top="0px",i.style.left="0px",i.style.bottom="0px",i.style.right=
"0px",e.appendChild(i),this.SVG[t]=i);return i},e.prototype.getTextInfo=function(t,e,i,r,l){var o,s,a,h;e=""+e,o="object"==typeof i?i.style+" "+i.variant+" "+i.weight+" "+i.size+"px/"+i.lineHeight+"px "+i.family:i,null==(s=this._textCache[t])&&(s=this._textCache[t]={}),null==(a=s[o])&&(a=s[o]={});var d=function(t){return t.replace(/0|1|2|3|4|5|6|7|8|9/g,"0")}(e);if(!(h=a[d])){var p=document.createElementNS("http://www.w3.org/2000/svg","text");if(-1!==e.indexOf("<br>"))n(e,p,-9999);else{var c=document.createTextNode(e);p.appendChild(c)}p.style.position="absolute",p.style.maxWidth=l,p.setAttributeNS(null,"x",-9999),p.setAttributeNS(null,"y",-9999),"object"==typeof i?(p.style.font=o,p.style.fill=i.fill):"string"==typeof i&&p.setAttribute("class",i),this.getSVGLayer(t).appendChild(p);var f=p.getBBox();for(h=a[d]={width:f.width,height:f.height,measured:!0,element:p,positions:[]};p.firstChild;)p.removeChild(p.firstChild);p.parentNode.removeChild(p)}return h.measured=!0,h},e.prototype.addText
=function(t,e,r,l,o,s,a,h,d,p){var c=this.getTextInfo(t,l,o,s,a),f=c.positions;"center"===h?e-=c.width/2:"right"===h&&(e-=c.width),"middle"===d?r-=c.height/2:"bottom"===d&&(r-=c.height),r+=.75*c.height;for(var y,m=0;f[m];m++){if((y=f[m]).x===e&&y.y===r&&y.text===l)return y.active=!0,void i(y.element,p);if(!1===y.active)return y.active=!0,y.text=l,-1!==l.indexOf("<br>")?(r-=.25*c.height,n(l,y.element,e)):y.element.textContent=l,y.element.setAttributeNS(null,"x",e),y.element.setAttributeNS(null,"y",r),y.x=e,y.y=r,void i(y.element,p)}y={active:!0,rendered:!1,element:f.length?c.element.cloneNode():c.element,text:l,x:e,y:r},f.push(y),-1!==l.indexOf("<br>")?(r-=.25*c.height,n(l,y.element,e)):y.element.textContent=l,y.element.setAttributeNS(null,"x",e),y.element.setAttributeNS(null,"y",r),y.element.style.textAlign=h,i(y.element,p)};var n=function(t,e,i){var n,r,l,o=t.split("<br>");for(r=0;r<o.length;r++)e.childNodes[r]?n=e.childNodes[r]:(n=document.createElementNS("http://www.w3.org/2000/svg"
,"tspan"),e.appendChild(n)),n.textContent=o[r],l=(0===r?0:1)+"em",n.setAttributeNS(null,"dy",l),n.setAttributeNS(null,"x",i)};e.prototype.removeText=function(t,e,i,n,r,l){var o,s;if(null==n){var a=this._textCache[t];if(null!=a)for(var h in a)if(hasOwnProperty.call(a,h)){var d=a[h];for(var p in d)if(hasOwnProperty.call(d,p)){var c=d[p].positions;c.forEach((function(t){t.active=!1}))}}}else(c=(o=this.getTextInfo(t,n,r,l)).positions).forEach((function(t){s=i+.75*o.height,t.x===e&&t.y===s&&t.text===n&&(t.active=!1)}))},e.prototype.clearCache=function(){var t=this._textCache;for(var e in t)if(hasOwnProperty.call(t,e))for(var i=this.getSVGLayer(e);i.firstChild;)i.removeChild(i.firstChild);this._textCache={}},window.Flot||(window.Flot={}),window.Flot.Canvas=e}(jQuery);!function(r){r.color={},r.color.make=function(e,a,n,t){var o={};return o.r=e||0,o.g=a||0,o.b=n||0,o.a=null!=t?t:1,o.add=function(r,e){for(var a=0;a<r.length;++a)o[r.charAt(a)]+=e;return o.normalize()},o.scale=function(r,e){for(
var a=0;a<r.length;++a)o[r.charAt(a)]*=e;return o.normalize()},o.toString=function(){return o.a>=1?"rgb("+[o.r,o.g,o.b].join(",")+")":"rgba("+[o.r,o.g,o.b,o.a].join(",")+")"},o.normalize=function(){function r(r,e,a){return e<r?r:e>a?a:e}return o.r=r(0,parseInt(o.r),255),o.g=r(0,parseInt(o.g),255),o.b=r(0,parseInt(o.b),255),o.a=r(0,o.a,1),o},o.clone=function(){return r.color.make(o.r,o.b,o.g,o.a)},o.normalize()},r.color.extract=function(e,a){var n;do{if(""!==(n=e.css(a).toLowerCase())&&"transparent"!==n)break;e=e.parent()}while(e.length&&!r.nodeName(e.get(0),"body"));return"rgba(0, 0, 0, 0)"===n&&(n="transparent"),r.color.parse(n)},r.color.parse=function(a){var n,t=r.color.make;if(n=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(a))return t(parseInt(n[1],10),parseInt(n[2],10),parseInt(n[3],10));if(n=/rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(a))return t(parseInt(n[1],10),parseInt(n[2],10),parseInt(n[3],10),
parseFloat(n[4]));if(n=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)%\s*,\s*([0-9]+(?:\.[0-9]+)?)%\s*,\s*([0-9]+(?:\.[0-9]+)?)%\s*\)/.exec(a))return t(2.55*parseFloat(n[1]),2.55*parseFloat(n[2]),2.55*parseFloat(n[3]));if(n=/rgba\(\s*([0-9]+(?:\.[0-9]+)?)%\s*,\s*([0-9]+(?:\.[0-9]+)?)%\s*,\s*([0-9]+(?:\.[0-9]+)?)%\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(a))return t(2.55*parseFloat(n[1]),2.55*parseFloat(n[2]),2.55*parseFloat(n[3]),parseFloat(n[4]));if(n=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(a))return t(parseInt(n[1],16),parseInt(n[2],16),parseInt(n[3],16));if(n=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(a))return t(parseInt(n[1]+n[1],16),parseInt(n[2]+n[2],16),parseInt(n[3]+n[3],16));var o=r.trim(a).toLowerCase();return"transparent"===o?t(255,255,255,0):t((n=e[o]||[0,0,0])[0],n[1],n[2])};var e={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],
darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0]}}(jQuery);!function(t){"use strict";var i=window.Flot.Canvas;function o(i){var o,n=[],e=t.plot.saturated.saturate(t.plot.saturated.floorInBase(i.min,i.tickSize)),a=0,r=Number.NaN;e===-Number.MAX_VALUE&&(n.push(e),e=t.plot.saturated.floorInBase(i.min+i.tickSize,i.tickSize));do{o=r,r=t.plot.saturated.multiplyAdd(i.
tickSize,a,e),n.push(r),++a}while(r<i.max&&r!==o);return n}function n(t,i,o){var n=i.tickDecimals;if(-1!==(""+t).indexOf("e"))return e(t,i,o);o>0&&(i.tickDecimals=o);var a=i.tickDecimals?parseFloat("1e"+i.tickDecimals):1,r=""+Math.round(t*a)/a;if(null!=i.tickDecimals){var l=r.indexOf("."),s=-1===l?0:r.length-l-1;if(s<i.tickDecimals)r=(s?r:r+".")+(""+a).substr(1,i.tickDecimals-s)}return i.tickDecimals=n,r}function e(t,i,o){var n=(""+t).indexOf("e"),e=parseInt((""+t).substr(n+1)),r=-1!==n?e:t>0?Math.floor(Math.log(t)/Math.LN10):0,l=parseFloat("1e"+r),s=t/l;if(o){var c=a(t,o);return(t/l).toFixed(c)+"e"+r}return i.tickDecimals>0?s.toFixed(a(t,i.tickDecimals))+"e"+r:s.toFixed()+"e"+r}function a(t,i){var o=Math.log(Math.abs(t))*Math.LOG10E,n=Math.abs(o+i);return n<=20?Math.floor(n):20}function r(e,a,r,l){var s=[],c={colors:["#edc240","#afd8f8","#cb4b4b","#4da74d","#9440ed"],xaxis:{show:null,position:"bottom",mode:null,font:null,color:null,tickColor:null,transform:null,inverseTransform:null,
min:null,max:null,autoScaleMargin:null,autoScale:"exact",windowSize:null,growOnly:null,ticks:null,tickFormatter:null,showTickLabels:"major",labelWidth:null,labelHeight:null,reserveSpace:null,tickLength:null,showMinorTicks:null,showTicks:null,gridLines:null,alignTicksWithAxis:null,tickDecimals:null,tickSize:null,minTickSize:null,offset:{below:0,above:0},boxPosition:{centerX:0,centerY:0}},yaxis:{autoScaleMargin:.02,autoScale:"loose",growOnly:null,position:"left",showTickLabels:"major",offset:{below:0,above:0},boxPosition:{centerX:0,centerY:0}},xaxes:[],yaxes:[],series:{points:{show:!1,radius:3,lineWidth:2,fill:!0,fillColor:"#ffffff",symbol:"circle"},lines:{lineWidth:1,fill:!1,fillColor:null,steps:!1},bars:{show:!1,lineWidth:2,horizontal:!1,barWidth:.8,fill:!0,fillColor:null,align:"left",zero:!0},shadowSize:3,highlightColor:null},grid:{show:!0,aboveData:!1,color:"#545454",backgroundColor:null,borderColor:null,tickColor:null,margin:0,labelMargin:5,axisMargin:8,borderWidth:1,minBorderMargin
:null,markings:null,markingsColor:"#f4f4f4",markingsLineWidth:2,clickable:!1,hoverable:!1,autoHighlight:!0,mouseActiveRadius:15},interaction:{redrawOverlayInterval:1e3/60},hooks:{}},u=null,f=null,h=null,d=null,p=null,m=[],x=[],g={left:0,right:0,top:0,bottom:0},b=0,v=0,k={processOptions:[],processRawData:[],processDatapoints:[],processOffset:[],setupGrid:[],adjustSeriesDataRange:[],setRange:[],drawBackground:[],drawSeries:[],drawAxis:[],draw:[],findNearbyItems:[],axisReserveSpace:[],bindEvents:[],drawOverlay:[],resize:[],shutdown:[]},y=this,w={},M=null;y.setData=W,y.setupGrid=P,y.draw=G,y.getPlaceholder=function(){return e},y.getCanvas=function(){return u.element},y.getSurface=function(){return u},y.getEventHolder=function(){return h[0]},y.getPlotOffset=function(){return g},y.width=function(){return b},y.height=function(){return v},y.offset=function(){var t=h.offset();return t.left+=g.left,t.top+=g.top,t},y.getData=function(){return s},y.getAxes=function(){var i={};return t.each(m.
concat(x),(function(t,o){o&&(i[o.direction+(1!==o.n?o.n:"")+"axis"]=o)})),i},y.getXAxes=function(){return m},y.getYAxes=function(){return x},y.c2p=function(t){var i,o,n={};for(i=0;i<m.length;++i)(o=m[i])&&o.used&&(n["x"+o.n]=o.c2p(t.left));for(i=0;i<x.length;++i)(o=x[i])&&o.used&&(n["y"+o.n]=o.c2p(t.top));void 0!==n.x1&&(n.x=n.x1);void 0!==n.y1&&(n.y=n.y1);return n},y.p2c=function(t){var i,o,n,e={};for(i=0;i<m.length;++i)if((o=m[i])&&o.used&&(n="x"+o.n,null==t[n]&&1===o.n&&(n="x"),null!=t[n])){e.left=o.p2c(t[n]);break}for(i=0;i<x.length;++i)if((o=x[i])&&o.used&&(n="y"+o.n,null==t[n]&&1===o.n&&(n="y"),null!=t[n])){e.top=o.p2c(t[n]);break}return e},y.getOptions=function(){return c},y.triggerRedrawOverlay=Z,y.pointOffset=function(t){return{left:parseInt(m[I(t,"x")-1].p2c(+t.x)+g.left,10),top:parseInt(x[I(t,"y")-1].p2c(+t.y)+g.top,10)}},y.shutdown=L,y.destroy=function(){L(),e.removeData("plot").empty(),s=[],c=null,u=null,f=null,h=null,d=null,p=null,m=[],x=[],k=null,y=null},y.resize=
function(){var t=e.width(),i=e.height();u.resize(t,i),f.resize(t,i),N(k.resize,[t,i])},y.clearTextCache=function(){u.clearCache(),f.clearCache()},y.autoScaleAxis=O,y.computeRangeForDataSeries=function(t,i,o){for(var n=t.datapoints.points,e=t.datapoints.pointsize,a=t.datapoints.format,r=Number.POSITIVE_INFINITY,l=Number.NEGATIVE_INFINITY,s={xmin:r,ymin:r,xmax:l,ymax:l},c=0;c<n.length;c+=e)if(null!==n[c]&&("function"!=typeof o||o(n[c])))for(var u=0;u<e;++u){var f=n[c+u],h=a[u];null!=h&&(("function"!=typeof o||o(f))&&(i||h.computeRange)&&f!==1/0&&f!==-1/0&&(!0===h.x&&(f<s.xmin&&(s.xmin=f),f>s.xmax&&(s.xmax=f)),!0===h.y&&(f<s.ymin&&(s.ymin=f),f>s.ymax&&(s.ymax=f))))}return s},y.adjustSeriesDataRange=function(t,i){if(t.bars.show){var o,n=t.bars.barWidth[1];t.datapoints&&t.datapoints.points&&!n&&function(t){var i=[],o=t.datapoints.pointsize,n=Number.MAX_VALUE;t.datapoints.points.length<=o&&(n=1);for(let n=t.bars.horizontal?1:0;n<t.datapoints.points.length;n+=o)isFinite(t.datapoints.points[n]
)&&null!==t.datapoints.points[n]&&i.push(t.datapoints.points[n]);function e(t,i,o){return o.indexOf(t)===i}(i=i.filter(e)).sort((function(t,i){return t-i}));for(let t=1;t<i.length;t++){var a=Math.abs(i[t]-i[t-1]);a<n&&isFinite(a)&&(n=a)}"number"==typeof t.bars.barWidth?t.bars.barWidth=t.bars.barWidth*n:t.bars.barWidth[0]=t.bars.barWidth[0]*n}(t);var e=t.bars.barWidth[0]||t.bars.barWidth;switch(t.bars.align){case"left":o=0;break;case"right":o=-e;break;default:o=-e/2}t.bars.horizontal?(i.ymin+=o,i.ymax+=o+e):(i.xmin+=o,i.xmax+=o+e)}if(t.bars.show&&t.bars.zero||t.lines.show&&t.lines.zero){t.datapoints.pointsize<=2&&(i.ymin=Math.min(0,i.ymin),i.ymax=Math.max(0,i.ymax))}return i},y.findNearbyItem=function(t,i,o,n,e){var a=Q(t,i,o,n,e);return void 0!==a[0]?a[0]:null},y.findNearbyItems=Q,y.findNearbyInterpolationPoint=function(t,i,o){var n,e,a,r,l,c,u,f=Number.MAX_VALUE;for(n=0;n<s.length;++n){if(!o(n))continue;var h=s[n].datapoints.points;c=s[n].datapoints.pointsize;const g=h[h.length-c]<h[0
]?function(t,i){return t>i}:function(t,i){return i>t};if(!g(t,h[0])){for(e=c;e<h.length&&!g(t,h[e]);e+=c);var d=h[e-c],p=h[e-c+1],m=h[e],x=h[e+1];void 0!==d&&void 0!==m&&void 0!==p&&void 0!==x&&(i=d===m?x:p+(x-p)*(t-d)/(m-d),r=Math.abs(s[n].xaxis.p2c(m)-t),l=Math.abs(s[n].yaxis.p2c(x)-i),(a=r*r+l*l)<f&&(f=a,u=[t,i,n,e]))}}if(u)return n=u[2],e=u[3],c=s[n].datapoints.pointsize,h=s[n].datapoints.points,d=h[e-c],p=h[e-c+1],m=h[e],x=h[e+1],{datapoint:[u[0],u[1]],leftPoint:[d,p],rightPoint:[m,x],seriesIndex:n};return null},y.computeValuePrecision=R,y.computeTickSize=E,y.addEventHandler=function(t,i,o,n){var e=o+t,a=w[e]||[];a.push({event:t,handler:i,eventHolder:o,priority:n}),a.sort(((t,i)=>i.priority-t.priority)),a.forEach((t=>{t.eventHolder.unbind(t.event,t.handler),t.eventHolder.bind(t.event,t.handler)})),w[e]=a},y.hooks=k;var S=t.plot.uiConstants.MINOR_TICKS_COUNT_CONSTANT,T=t.plot.uiConstants.TICK_LENGTH_CONSTANT;function N(t,i){i=[y].concat(i);for(var o=0;o<t.length;++o)t[o].apply(this
,i)}function W(i){var o=s;s=function(i){for(var o=[],n=0;n<i.length;++n){var e=t.extend(!0,{},c.series);null!=i[n].data?(e.data=i[n].data,delete i[n].data,t.extend(!0,e,i[n]),i[n].data=e.data):e.data=i[n],o.push(e)}return o}(i),function(){var i,o=s.length,n=-1;for(i=0;i<s.length;++i){var e=s[i].color;null!=e&&(o--,"number"==typeof e&&e>n&&(n=e))}o<=n&&(o=n+1);var a,r=[],l=c.colors,u=l.length,f=0,h=Math.max(0,s.length-o);for(i=0;i<o;i++)a=t.color.parse(l[(h+i)%u]||"#666"),i%u==0&&i&&(f=f>=0?f<.5?-f-.2:0:-f),r[i]=a.scale("rgb",1+f);var d,p=0;for(i=0;i<s.length;++i){if(null==(d=s[i]).color?(d.color=r[p].toString(),++p):"number"==typeof d.color&&(d.color=r[d.color].toString()),null==d.lines.show){var g,b=!0;for(g in d)if(d[g]&&d[g].show){b=!1;break}b&&(d.lines.show=!0)}null==d.lines.zero&&(d.lines.zero=!!d.lines.fill),d.xaxis=C(m,I(d,"x")),d.yaxis=C(x,I(d,"y"))}}(),function(i){var o,n,e,a,r,l,c,u,f,h,d,p,m=Number.POSITIVE_INFINITY,x=Number.NEGATIVE_INFINITY;function g(t,i,o){i<t.datamin&&i
!==-1/0&&(t.datamin=i),o>t.datamax&&o!==1/0&&(t.datamax=o)}function b(t,i){return t&&t[i]&&t[i].datapoints&&t[i].datapoints.points?t[i].datapoints.points:[]}for(t.each(z(),(function(t,i){!0!==i.options.growOnly?(i.datamin=m,i.datamax=x):(void 0===i.datamin&&(i.datamin=m),void 0===i.datamax&&(i.datamax=x)),i.used=!1})),o=0;o<s.length;++o)(r=s[o]).datapoints={points:[]},0===r.datapoints.points.length&&(r.datapoints.points=b(i,o)),N(k.processRawData,[r,r.data,r.datapoints]);for(o=0;o<s.length;++o){if(d=(r=s[o]).data,!(p=r.datapoints.format)){if((p=[]).push({x:!0,y:!1,number:!0,required:!0,computeRange:"none"!==r.xaxis.options.autoScale,defaultValue:null}),p.push({x:!1,y:!0,number:!0,required:!0,computeRange:"none"!==r.yaxis.options.autoScale,defaultValue:null}),r.stack||r.bars.show||r.lines.show&&r.lines.fill)(null!=r.datapoints.pointsize?r.datapoints.pointsize:r.data&&r.data[0]&&r.data[0].length?r.data[0].length:3)>2&&p.push({x:r.bars.horizontal,y:!r.bars.horizontal,number:!0,required:!1
,computeRange:"none"!==r.yaxis.options.autoScale,defaultValue:0});r.datapoints.format=p}if(r.xaxis.used=r.yaxis.used=!0,null==r.datapoints.pointsize){for(r.datapoints.pointsize=p.length,c=r.datapoints.pointsize,l=r.datapoints.points,n=e=0;n<d.length;++n,e+=c){var v=null==(h=d[n]);if(!v)for(a=0;a<c;++a)u=h[a],(f=p[a])&&(f.number&&null!=u&&(u=+u,isNaN(u)&&(u=null)),null==u&&(f.required&&(v=!0),null!=f.defaultValue&&(u=f.defaultValue))),l[e+a]=u;if(v)for(a=0;a<c;++a)null!=(u=l[e+a])&&(f=p[a]).computeRange&&(f.x&&g(r.xaxis,u,u),f.y&&g(r.yaxis,u,u)),l[e+a]=null}l.length=e}}for(o=0;o<s.length;++o)r=s[o],N(k.processDatapoints,[r,r.datapoints]);for(o=0;o<s.length;++o)if(!(p=(r=s[o]).datapoints.format).every((function(t){return!t.computeRange}))){var w=y.adjustSeriesDataRange(r,y.computeRangeForDataSeries(r));N(k.adjustSeriesDataRange,[r,w]),g(r.xaxis,w.xmin,w.xmax),g(r.yaxis,w.ymin,w.ymax)}t.each(z(),(function(t,i){i.datamin===m&&(i.datamin=null),i.datamax===x&&(i.datamax=null)}))}(o)}function
I(t,i){var o=t[i+"axis"];return"object"==typeof o&&(o=o.n),"number"!=typeof o&&(o=1),o}function z(){return m.concat(x).filter((function(t){return t}))}function C(i,o){return i[o-1]||(i[o-1]={n:o,direction:i===m?"x":"y",options:t.extend(!0,{},i===m?c.xaxis:c.yaxis)}),i[o-1]}function L(){M&&clearTimeout(M),N(k.shutdown,[h])}function D(i){function o(t){return t}var n,e,a=i.options.transform||o,r=i.options.inverseTransform;"x"===i.direction?(n=isFinite(a(i.max)-a(i.min))?i.scale=b/Math.abs(a(i.max)-a(i.min)):i.scale=1/Math.abs(t.plot.saturated.delta(a(i.min),a(i.max),b)),e=Math.min(a(i.max),a(i.min))):(n=-(n=isFinite(a(i.max)-a(i.min))?i.scale=v/Math.abs(a(i.max)-a(i.min)):i.scale=1/Math.abs(t.plot.saturated.delta(a(i.min),a(i.max),v))),e=Math.max(a(i.max),a(i.min))),i.p2c=a===o?function(t){return isFinite(t-e)?(t-e)*n:(t/4-e/4)*n*4}:function(t){var i=a(t);return isFinite(i-e)?(i-e)*n:(i/4-e/4)*n*4},i.c2p=r?function(t){return r(e+t/n)}:function(t){return e+t/n}}function F(i){N(k.
axisReserveSpace,[i]);var o=i.labelWidth,n=i.labelHeight,e=i.options.position,a="x"===i.direction,r=i.options.tickLength,l=i.options.showTicks,s=i.options.showMinorTicks,f=i.options.gridLines,h=c.grid.axisMargin,d=c.grid.labelMargin,p=!0,b=!0,v=!1;t.each(a?m:x,(function(t,o){o&&(o.show||o.reserveSpace)&&(o===i?v=!0:o.options.position===e&&(v?b=!1:p=!1))})),b&&(h=0),null==r&&(r=T),null==l&&(l=!0),null==s&&(s=!0),null==f&&(f=!!p),isNaN(+r)||(d+=l?+r:0),a?(n+=d,"bottom"===e?(g.bottom+=n+h,i.box={top:u.height-g.bottom,height:n}):(i.box={top:g.top+h,height:n},g.top+=n+h)):(o+=d,"left"===e?(i.box={left:g.left+h,width:o},g.left+=o+h):(g.right+=o+h,i.box={left:u.width-g.right,width:o})),i.position=e,i.tickLength=r,i.showMinorTicks=s,i.showTicks=l,i.gridLines=f,i.box.padding=d,i.innermost=p}function A(t,i,o){"x"===t.direction?("bottom"===t.position&&o(i.bottom)&&(t.box.top-=Math.ceil(i.bottom)),"top"===t.position&&o(i.top)&&(t.box.top+=Math.ceil(i.top))):("left"===t.position&&o(i.left)&&(t.box.
left+=Math.ceil(i.left)),"right"===t.position&&o(i.right)&&(t.box.left-=Math.ceil(i.right)))}function P(i){var e,a,r=z(),l=c.grid.show;for(a in g)g[a]=0;for(a in N(k.processOffset,[g]),g)"object"==typeof c.grid.borderWidth?g[a]+=l?c.grid.borderWidth[a]:0:g[a]+=l?c.grid.borderWidth:0;if(t.each(r,(function(o,e){var a=e.options;e.show=null==a.show?e.used:a.show,e.reserveSpace=null==a.reserveSpace?e.show:a.reserveSpace,function(t){var i=t.options;t.tickFormatter||("function"==typeof i.tickFormatter?t.tickFormatter=function(){var t=Array.prototype.slice.call(arguments);return""+i.tickFormatter.apply(null,t)}:t.tickFormatter=n)}(e),N(k.setRange,[e,i]),function(i,o){var n="number"==typeof i.options.min?i.options.min:i.min,e="number"==typeof i.options.max?i.options.max:i.max,a=i.options.offset;o&&(O(i),n=i.autoScaledMin,e=i.autoScaledMax);if(n=(null!=n?n:-1)+(a.below||0),e=(null!=e?e:1)+(a.above||0),n>e){var r=n;n=e,e=r,i.options.offset={above:0,below:0}}i.min=t.plot.saturated.saturate(n),i.
max=t.plot.saturated.saturate(e)}(e,i)})),l){b=u.width-g.left-g.right,v=u.height-g.bottom-g.top;var f=t.grep(r,(function(t){return t.show||t.reserveSpace}));for(t.each(f,(function(i,n){!function(i){var n,e=i.options;n=j(i.direction,u,e.ticks),i.delta=t.plot.saturated.delta(i.min,i.max,n);var a=y.computeValuePrecision(i.min,i.max,i.direction,n,e.tickDecimals);i.tickDecimals=Math.max(0,null!=e.tickDecimals?e.tickDecimals:a),i.tickSize=function(t,i,o,n,e){var a;a="number"==typeof n.ticks&&n.ticks>0?n.ticks:.3*Math.sqrt("x"===o?u.width:u.height);var r=E(t,i,a,e);null!=n.minTickSize&&r<n.minTickSize&&(r=n.minTickSize);return n.tickSize||r}(i.min,i.max,i.direction,e,e.tickDecimals),i.tickGenerator||("function"==typeof e.tickGenerator?i.tickGenerator=e.tickGenerator:i.tickGenerator=o);if(null!=e.alignTicksWithAxis){var r=("x"===i.direction?m:x)[e.alignTicksWithAxis-1];if(r&&r.used&&r!==i){var l=i.tickGenerator(i,y);if(l.length>0&&(null==e.min&&(i.min=Math.min(i.min,l[0])),null==e.max&&l.
length>1&&(i.max=Math.max(i.max,l[l.length-1]))),i.tickGenerator=function(t){var i,o,n=[];for(o=0;o<r.ticks.length;++o)i=(r.ticks[o].v-r.min)/(r.max-r.min),i=t.min+i*(t.max-t.min),n.push(i);return n},!i.mode&&null==e.tickDecimals){var s=Math.max(0,1-Math.floor(Math.log(i.delta)/Math.LN10)),c=i.tickGenerator(i,y);c.length>1&&/\..*0$/.test((c[1]-c[0]).toFixed(s))||(i.tickDecimals=s)}}}}(n),function(i){var o,n,e=i.options.ticks,a=[];null==e||"number"==typeof e&&e>0?a=i.tickGenerator(i,y):e&&(a=t.isFunction(e)?e(i):e);for(i.ticks=[],o=0;o<a.length;++o){var r=null,l=a[o];"object"==typeof l?(n=+l[0],l.length>1&&(r=l[1])):n=+l,isNaN(n)||i.ticks.push(H(n,r,i,"major"))}}(n),function(t,i,o){var n=function(t){return t.some((t=>t.datapoints.points.length>0))};"loose"===t.options.autoScale&&i.length>0&&n(o)&&(t.min=Math.min(t.min,i[0].v),t.max=Math.max(t.max,i[i.length-1].v))}(n,n.ticks,s),D(n),function(t,i){(function(t,i){if("endpoints"===t.options.showTickLabels)return!0;if("all"===t.options.
showTickLabels){var o=i.filter((function(i){return i.bars.horizontal?i.yaxis===t:i.xaxis===t})),n=o.some((function(t){return!t.bars.show}));return 0===o.length||n}if("major"===t.options.showTickLabels||"none"===t.options.showTickLabels)return!1})(t,i)&&(t.ticks.unshift(H(t.min,null,t,"min")),t.ticks.push(H(t.max,null,t,"max")))}(n,s),function(t){for(var i=t.options,o="none"!==i.showTickLabels&&t.ticks?t.ticks:[],n="major"===i.showTickLabels||"all"===i.showTickLabels,e="endpoints"===i.showTickLabels||"all"===i.showTickLabels,a=i.labelWidth||0,r=i.labelHeight||0,l=t.direction+"Axis "+t.direction+t.n+"Axis",s="flot-"+t.direction+"-axis flot-"+t.direction+t.n+"-axis "+l,c=i.font||"flot-tick-label tickLabel",f=0;f<o.length;++f){var h=o[f],d=h.label;if(h.label&&!(!1===n&&f>0&&f<o.length-1)&&(!1!==e||0!==f&&f!==o.length-1)){"object"==typeof h.label&&(d=h.label.name);var p=u.getTextInfo(s,d,c);a=Math.max(a,p.width),r=Math.max(r,p.height)}}t.labelWidth=i.labelWidth||a,t.labelHeight=i.
labelHeight||r}(n)})),e=f.length-1;e>=0;--e)F(f[e]);!function(){var i,o=c.grid.minBorderMargin;if(null==o)for(o=0,i=0;i<s.length;++i)o=Math.max(o,2*(s[i].points.radius+s[i].points.lineWidth/2));var n,e={},a={left:o,right:o,top:o,bottom:o};for(n in t.each(z(),(function(t,i){i.reserveSpace&&i.ticks&&i.ticks.length&&("x"===i.direction?(a.left=Math.max(a.left,i.labelWidth/2),a.right=Math.max(a.right,i.labelWidth/2)):(a.bottom=Math.max(a.bottom,i.labelHeight/2),a.top=Math.max(a.top,i.labelHeight/2)))})),a)e[n]=a[n]-g[n];t.each(m.concat(x),(function(t,i){A(i,e,(function(t){return t>0}))})),g.left=Math.ceil(Math.max(a.left,g.left)),g.right=Math.ceil(Math.max(a.right,g.right)),g.top=Math.ceil(Math.max(a.top,g.top)),g.bottom=Math.ceil(Math.max(a.bottom,g.bottom))}(),t.each(f,(function(t,i){!function(t){"x"===t.direction?(t.box.left=g.left-t.labelWidth/2,t.box.width=u.width-g.left-g.right+t.labelWidth):(t.box.top=g.top-t.labelHeight/2,t.box.height=u.height-g.bottom-g.top+t.labelHeight)}(i)}))}if
(c.grid.margin){for(a in g){var h=c.grid.margin||0;g[a]+="number"==typeof h?h:h[a]||0}t.each(m.concat(x),(function(t,i){A(i,c.grid.margin,(function(t){return null!=t}))}))}b=u.width-g.left-g.right,v=u.height-g.bottom-g.top,t.each(r,(function(t,i){D(i)})),l&&t.each(z(),(function(t,i){var o,n,e,a,r,l,s,c=i.box,f=i.direction+"Axis "+i.direction+i.n+"Axis",h="flot-"+i.direction+"-axis flot-"+i.direction+i.n+"-axis "+f,d=i.options.font||"flot-tick-label tickLabel",p=3,m={x:NaN,y:NaN,width:NaN,height:NaN},x=[],b=function(t,i,o,n,e,a,r,l){return(t<=e&&e<=o||e<=t&&t<=r)&&(i<=a&&a<=n||a<=i&&i<=l)},v=function(t,i){return i.some((function(i){return b(t.x,t.y,t.x+t.width,t.y+t.height,i.x,i.y,i.x+i.width,i.y+i.height)}))},y=function(t,o){return!t||!t.label||t.v<i.min||t.v>i.max?m:(l=u.getTextInfo(h,t.label,d),"x"===i.direction?(a="center",n=g.left+i.p2c(t.v),"bottom"===i.position?e=c.top+c.padding-i.boxPosition.centerY:(e=c.top+c.height-c.padding+i.boxPosition.centerY,r="bottom"),s={x:n-l.width/2-p
,y:e-p,width:l.width+2*p,height:l.height+2*p}):(r="middle",e=g.top+i.p2c(t.v),"left"===i.position?(n=c.left+c.width-c.padding-i.boxPosition.centerX,a="right"):n=c.left+c.padding+i.boxPosition.centerX,s={x:n-l.width/2-p,y:e-p,width:l.width+2*p,height:l.height+2*p}),v(s,o)?m:(u.addText(h,n,e,t.label,d,null,null,a,r),s))};if(u.removeText(h),N(k.drawAxis,[i,u]),i.show)switch(i.options.showTickLabels){case"none":break;case"endpoints":x.push(y(i.ticks[0],x)),x.push(y(i.ticks[i.ticks.length-1],x));break;case"major":for(x.push(y(i.ticks[0],x)),x.push(y(i.ticks[i.ticks.length-1],x)),o=1;o<i.ticks.length-1;++o)x.push(y(i.ticks[o],x));break;case"all":for(x.push(y(i.ticks[0],[])),x.push(y(i.ticks[i.ticks.length-1],x)),o=1;o<i.ticks.length-1;++o)x.push(y(i.ticks[o],x))}})),N(k.setupGrid,[])}function O(i){var o,n=i.options,e=n.min,a=n.max,r=i.datamin,l=i.datamax;switch(n.autoScale){case"none":e=+(null!=n.min?n.min:r),a=+(null!=n.max?n.max:l);break;case"loose":if(null!=r&&null!=l){e=r,a=l,o=t.plot.
saturated.saturate(a-e);var s="number"==typeof n.autoScaleMargin?n.autoScaleMargin:.02;e=t.plot.saturated.saturate(e-o*s),a=t.plot.saturated.saturate(a+o*s),e<0&&r>=0&&(e=0)}else e=n.min,a=n.max;break;case"exact":e=null!=r?r:n.min,a=null!=l?l:n.max;break;case"sliding-window":l>a&&(a=l,e=Math.max(l-(n.windowSize||100),e))}var c=function(t,i){var o=void 0===t?null:t,n=void 0===i?null:i;if(0==n-o){var e=0===n?1:.01,a=null;null==o&&(a-=e),null!=n&&null==o||(n+=e),null!=a&&(o=a)}return{min:o,max:n}}(e,a);e=c.min,a=c.max,!0===n.growOnly&&"none"!==n.autoScale&&"sliding-window"!==n.autoScale&&(e=e<r?e:null!==r?r:e,a=a>l?a:null!==l?l:a),i.autoScaledMin=e,i.autoScaledMax=a}function R(i,o,n,e,a){var r=j(n,u,e),l=t.plot.saturated.delta(i,o,r),s=-Math.floor(Math.log(l)/Math.LN10);a&&s>a&&(s=a);var c=l/parseFloat("1e"+-s);return c>2.25&&c<3&&s+1<=a&&++s,isFinite(s)?s:0}function E(i,o,n,e){var a=t.plot.saturated.delta(i,o,n),r=-Math.floor(Math.log(a)/Math.LN10);e&&r>e&&(r=e);var l,s=parseFloat("1e"+-
r),c=a/s;return c<1.5?l=1:c<3?(l=2,c>2.25&&(null==e||r+1<=e)&&(l=2.5)):l=c<7.5?5:10,l*=s}function j(t,i,o){return"number"==typeof o&&o>0?o:.3*Math.sqrt("x"===t?i.width:i.height)}function H(t,i,o,n){if(null===i)switch(n){case"min":case"max":var e=function(t,i){var o=Math.floor(i.p2c(t)),n="x"===i.direction?o+1:o-1,e=i.c2p(o),a=i.c2p(n);return R(e,a,i.direction,1)}(t,o);isFinite(e),i=o.tickFormatter(t,o,e,y);break;case"major":i=o.tickFormatter(t,o,void 0,y)}return{v:t,label:i}}function G(){u.clear(),N(k.drawBackground,[d]);var t=c.grid;t.show&&t.backgroundColor&&(d.save(),d.translate(g.left,g.top),d.fillStyle=it(c.grid.backgroundColor,v,0,"rgba(255, 255, 255, 0)"),d.fillRect(0,0,b,v),d.restore()),t.show&&!t.aboveData&&U();for(var i=0;i<s.length;++i)N(k.drawSeries,[d,s[i],i,it]),K(s[i]);N(k.draw,[d]),t.show&&t.aboveData&&U(),u.render(),Z()}function V(t,i){for(var o,n,e,a,r=z(),l=0;l<r.length;++l)if((o=r[l]).direction===i&&(t[a=i+o.n+"axis"]||1!==o.n||(a=i+"axis"),t[a])){n=t[a].from,e=t[a]
.to;break}if(t[a]||(o="x"===i?m[0]:x[0],n=t[i+"1"],e=t[i+"2"]),null!=n&&null!=e&&n>e){var s=n;n=e,e=s}return{from:n,to:e,axis:o}}function _(t){var i=t.box,o=0,n=0;return"x"===t.direction?(o=0,n=i.top-g.top+("top"===t.position?i.height:0)):(n=0,o=i.left-g.left+("left"===t.position?i.width:0)+t.boxPosition.centerX),{x:o,y:n}}function X(t,i){return t%2!=0?Math.floor(i)+.5:i}function Y(t){d.lineWidth=1;var i=_(t),o=i.x,n=i.y;if(t.show){var e=0,a=0;d.strokeStyle=t.options.color,d.beginPath(),"x"===t.direction?e=b+1:a=v+1,"x"===t.direction?n=X(d.lineWidth,n):o=X(d.lineWidth,o),d.moveTo(o,n),d.lineTo(o+e,n+a),d.stroke()}}function q(t){var i=t.tickLength,o=t.showMinorTicks,n=S,e=_(t),a=e.x,r=e.y,l=0;for(d.strokeStyle=t.options.color,d.beginPath(),l=0;l<t.ticks.length;++l){var s,c=t.ticks[l].v,u=0,f=0,h=0,p=0;if(!isNaN(c)&&c>=t.min&&c<=t.max&&("x"===t.direction?(a=t.p2c(c),f=i,"top"===t.position&&(f=-f)):(r=t.p2c(c),u=i,"left"===t.position&&(u=-u)),"x"===t.direction?a=X(d.lineWidth,a):r=X(d.
lineWidth,r),d.moveTo(a,r),d.lineTo(a+u,r+f)),!0===o&&l<t.ticks.length-1){var m=t.ticks[l].v,x=(t.ticks[l+1].v-m)/(n+1);for(s=1;s<=n;s++){if("x"===t.direction){if(p=i/2,a=X(d.lineWidth,t.p2c(m+s*x)),"top"===t.position&&(p=-p),a<0||a>b)continue}else if(h=i/2,r=X(d.lineWidth,t.p2c(m+s*x)),"left"===t.position&&(h=-h),r<0||r>v)continue;d.moveTo(a,r),d.lineTo(a+h,r+p)}}}d.stroke()}function B(t){var i,o,n;for(d.strokeStyle=c.grid.tickColor,d.beginPath(),i=0;i<t.ticks.length;++i){var e=t.ticks[i].v,a=0,r=0,l=0,s=0;isNaN(e)||e<t.min||e>t.max||(o=e,n=void 0,(!("object"==typeof(n=c.grid.borderWidth)&&n[t.position]>0||n>0)||o!==t.min&&o!==t.max)&&("x"===t.direction?(l=t.p2c(e),s=v,r=-v):(l=0,s=t.p2c(e),a=b),"x"===t.direction?l=X(d.lineWidth,l):s=X(d.lineWidth,s),d.moveTo(l,s),d.lineTo(l+a,s+r)))}d.stroke()}function U(){var i,o;d.save(),d.translate(g.left,g.top),function(){var i,o,n=c.grid.markings;if(n)for(t.isFunction(n)&&((i=y.getAxes()).xmin=i.xaxis.min,i.xmax=i.xaxis.max,i.ymin=i.yaxis.min,i.
ymax=i.yaxis.max,n=n(i)),o=0;o<n.length;++o){var e=n[o],a=V(e,"x"),r=V(e,"y");if(null==a.from&&(a.from=a.axis.min),null==a.to&&(a.to=a.axis.max),null==r.from&&(r.from=r.axis.min),null==r.to&&(r.to=r.axis.max),!(a.to<a.axis.min||a.from>a.axis.max||r.to<r.axis.min||r.from>r.axis.max)){a.from=Math.max(a.from,a.axis.min),a.to=Math.min(a.to,a.axis.max),r.from=Math.max(r.from,r.axis.min),r.to=Math.min(r.to,r.axis.max);var l=a.from===a.to,s=r.from===r.to;if(!l||!s)if(a.from=Math.floor(a.axis.p2c(a.from)),a.to=Math.floor(a.axis.p2c(a.to)),r.from=Math.floor(r.axis.p2c(r.from)),r.to=Math.floor(r.axis.p2c(r.to)),l||s){var u=e.lineWidth||c.grid.markingsLineWidth,f=u%2?.5:0;d.beginPath(),d.strokeStyle=e.color||c.grid.markingsColor,d.lineWidth=u,l?(d.moveTo(a.to+f,r.from),d.lineTo(a.to+f,r.to)):(d.moveTo(a.from,r.to+f),d.lineTo(a.to,r.to+f)),d.stroke()}else d.fillStyle=e.color||c.grid.markingsColor,d.fillRect(a.from,r.to,a.to-a.from,r.from-r.to)}}}(),i=z(),o=c.grid.borderWidth;for(var n=0;n<i.length
;++n){var e=i[n];e.show&&(Y(e),!0===e.showTicks&&q(e),!0===e.gridLines&&B(e))}o&&function(){var t=c.grid.borderWidth,i=c.grid.borderColor;"object"==typeof t||"object"==typeof i?("object"!=typeof t&&(t={top:t,right:t,bottom:t,left:t}),"object"!=typeof i&&(i={top:i,right:i,bottom:i,left:i}),t.top>0&&(d.strokeStyle=i.top,d.lineWidth=t.top,d.beginPath(),d.moveTo(0-t.left,0-t.top/2),d.lineTo(b,0-t.top/2),d.stroke()),t.right>0&&(d.strokeStyle=i.right,d.lineWidth=t.right,d.beginPath(),d.moveTo(b+t.right/2,0-t.top),d.lineTo(b+t.right/2,v),d.stroke()),t.bottom>0&&(d.strokeStyle=i.bottom,d.lineWidth=t.bottom,d.beginPath(),d.moveTo(b+t.right,v+t.bottom/2),d.lineTo(0,v+t.bottom/2),d.stroke()),t.left>0&&(d.strokeStyle=i.left,d.lineWidth=t.left,d.beginPath(),d.moveTo(0-t.left/2,v+t.bottom),d.lineTo(0-t.left/2,0),d.stroke())):(d.lineWidth=t,d.strokeStyle=c.grid.borderColor,d.strokeRect(-t/2,-t/2,b+t,v+t))}(),d.restore()}function K(i){i.lines.show&&t.plot.drawSeries.drawSeriesLines(i,d,g,b,v,y.
drawSymbol,it),i.bars.show&&t.plot.drawSeries.drawSeriesBars(i,d,g,b,v,y.drawSymbol,it),i.points.show&&t.plot.drawSeries.drawSeriesPoints(i,d,g,b,v,y.drawSymbol,it)}function Q(t,i,o,n,e){for(var a=function(t,i,o,n,e){var a,r=[],l=[],c=n*n+1;for(a=s.length-1;a>=0;--a)if(o(a)){var u=s[a];if(!u.datapoints)return;var f=!1;if(u.lines.show||u.points.show){var h=$(u,t,i,n,e);h&&(l.push({seriesIndex:a,dataIndex:h.dataIndex,distance:h.distance}),f=!0)}if(u.bars.show&&!f){var d=J(u,t,i);d>=0&&l.push({seriesIndex:a,dataIndex:d,distance:c})}}for(a=0;a<l.length;a++){var p=l[a].seriesIndex,m=l[a].dataIndex,x=l[a].distance,g=s[p].datapoints.pointsize;r.push({datapoint:s[p].datapoints.points.slice(m*g,(m+1)*g),dataIndex:m,series:s[p],seriesIndex:p,distance:Math.sqrt(x)})}return r}(t,i,o,n,e),r=0;r<s.length;++r)o(r)&&N(k.findNearbyItems,[t,i,s,r,n,e,a]);return a.sort(((t,i)=>void 0===i.distance?-1:void 0===t.distance&&void 0!==i.distance?1:t.distance-i.distance))}function $(t,i,o,n,e){var a=t.xaxis.c2p
(i),r=t.yaxis.c2p(o),l=n/t.xaxis.scale,s=n/t.yaxis.scale,c=t.datapoints.points,u=t.datapoints.pointsize,f=Number.POSITIVE_INFINITY;t.xaxis.options.inverseTransform&&(l=Number.MAX_VALUE),t.yaxis.options.inverseTransform&&(s=Number.MAX_VALUE);for(var h=null,d=0;d<c.length;d+=u){var p=c[d],m=c[d+1];if(null!=p&&!(p-a>l||p-a<-l||m-r>s||m-r<-s)){var x=Math.abs(t.xaxis.p2c(p)-i),g=Math.abs(t.yaxis.p2c(m)-o),b=e?e(x,g):x*x+g*g;b<f&&(f=b,h={dataIndex:d/u,distance:b})}}return h}function J(t,i,o){var n,e,a=t.bars.barWidth[0]||t.bars.barWidth,r=t.xaxis.c2p(i),l=t.yaxis.c2p(o),s=t.datapoints.points,c=t.datapoints.pointsize;switch(t.bars.align){case"left":n=0;break;case"right":n=-a;break;default:n=-a/2}e=n+a;for(var u=t.bars.fillTowards||0,f=u>t.yaxis.min?Math.min(t.yaxis.max,u):t.yaxis.min,h=-1,d=0;d<s.length;d+=c){var p=s[d],m=s[d+1];if(null!=p){var x=3===c?s[d+2]:f;(t.bars.horizontal?r<=Math.max(x,p)&&r>=Math.min(x,p)&&l>=m+n&&l<=m+e:r>=p+n&&r<=p+e&&l>=Math.min(x,m)&&l<=Math.max(x,m))&&(h=d/c)}}
return h}function Z(){var t=c.interaction.redrawOverlayInterval;-1!==t?M||(M=setTimeout((function(){tt(y)}),t)):tt()}function tt(t){if(M=null,p){f.clear(),N(k.drawOverlay,[p,f]);var i=new CustomEvent("onDrawingDone");t.getEventHolder().dispatchEvent(i),t.getPlaceholder().trigger("drawingdone")}}function it(i,o,n,e){if("string"==typeof i)return i;for(var a=d.createLinearGradient(0,n,0,o),r=0,l=i.colors.length;r<l;++r){var s=i.colors[r];if("string"!=typeof s){var c=t.color.parse(e);null!=s.brightness&&(c=c.scale("rgb",s.brightness)),null!=s.opacity&&(c.a*=s.opacity),s=c.toString()}a.addColorStop(r/(l-1),s)}return a}!function(){for(var o={Canvas:i},n=0;n<l.length;++n){var e=l[n];e.init(y,o),e.options&&t.extend(!0,c,e.options)}}(),function(){e.css("padding",0).children().filter((function(){return!t(this).hasClass("flot-overlay")&&!t(this).hasClass("flot-base")})).remove(),"static"===e.css("position")&&e.css("position","relative");u=new i("flot-base",e[0]),f=new i("flot-overlay",e[0]),d=u.
context,p=f.context,h=t(f.element).unbind();var o=e.data("plot");o&&(o.shutdown(),f.clear());e.data("plot",y)}(),function(i){t.extend(!0,c,i),i&&i.colors&&(c.colors=i.colors);null==c.xaxis.color&&(c.xaxis.color=t.color.parse(c.grid.color).scale("a",.22).toString());null==c.yaxis.color&&(c.yaxis.color=t.color.parse(c.grid.color).scale("a",.22).toString());null==c.xaxis.tickColor&&(c.xaxis.tickColor=c.grid.tickColor||c.xaxis.color);null==c.yaxis.tickColor&&(c.yaxis.tickColor=c.grid.tickColor||c.yaxis.color);null==c.grid.borderColor&&(c.grid.borderColor=c.grid.color);null==c.grid.tickColor&&(c.grid.tickColor=t.color.parse(c.grid.color).scale("a",.22).toString());var o,n,a,r=e.css("font-size"),l=r?+r.replace("px",""):13,s={style:e.css("font-style"),size:Math.round(.8*l),variant:e.css("font-variant"),weight:e.css("font-weight"),family:e.css("font-family")};for(a=c.xaxes.length||1,o=0;o<a;++o)(n=c.xaxes[o])&&!n.tickColor&&(n.tickColor=n.color),n=t.extend(!0,{},c.xaxis,n),c.xaxes[o]=n,n.font
&&(n.font=t.extend({},s,n.font),n.font.color||(n.font.color=n.color),n.font.lineHeight||(n.font.lineHeight=Math.round(1.15*n.font.size)));for(a=c.yaxes.length||1,o=0;o<a;++o)(n=c.yaxes[o])&&!n.tickColor&&(n.tickColor=n.color),n=t.extend(!0,{},c.yaxis,n),c.yaxes[o]=n,n.font&&(n.font=t.extend({},s,n.font),n.font.color||(n.font.color=n.color),n.font.lineHeight||(n.font.lineHeight=Math.round(1.15*n.font.size)));for(o=0;o<c.xaxes.length;++o)C(m,o+1).options=c.xaxes[o];for(o=0;o<c.yaxes.length;++o)C(x,o+1).options=c.yaxes[o];for(var u in t.each(z(),(function(t,i){i.boxPosition=i.options.boxPosition||{centerX:0,centerY:0}})),k)c.hooks[u]&&c.hooks[u].length&&(k[u]=k[u].concat(c.hooks[u]));N(k.processOptions,[c])}(r),W(a),P(!0),G(),N(k.bindEvents,[h])}t.plot=function(i,o,n){return new r(t(i),o,n,t.plot.plugins)},t.plot.version="3.0.0",t.plot.plugins=[],t.fn.plot=function(i,o){return this.each((function(){t.plot(this,i,o)}))},t.plot.linearTickGenerator=o,t.plot.defaultTickFormatter=n,t.plot.
expRepTickFormatter=e}(jQuery);!function(t){"use strict";var r={saturate:function(t){return t===1/0?Number.MAX_VALUE:t===-1/0?-Number.MAX_VALUE:t},delta:function(t,r,u){return(r-t)/u==1/0?r/u-t/u:(r-t)/u},multiply:function(t,u){return r.saturate(t*u)},multiplyAdd:function(t,u,n){if(isFinite(t*u))return r.saturate(t*u+n);for(var e=n,a=0;a<u;a++)e+=t;return r.saturate(e)},floorInBase:function(t,r){return r*Math.floor(t/r)}};t.plot.saturated=r}(jQuery);!function(t){var i={tooltip:{show:!1,cssClass:"flotTip",content:"%s | X: %x | Y: %y",xDateFormat:null,yDateFormat:null,monthNames:null,dayNames:null,shifts:{x:10,y:20},defaultTheme:!0,snap:!0,lines:!1,clickTips:!1,onHover:function(t,i){},$compat:!1}};i.tooltipOpts=i.tooltip;var o=function(t){this.tipPosition={x:0,y:0},this.init(t)};o.prototype.init=function(i){var o=this,e=t.plot.plugins.length;if(this.plotPlugins=[],e)for(var s=0;s<e;s++)this.plotPlugins.push(t.plot.plugins[s].name);function a(t){var o={};o.x=t.pageX,o.y=t.pageY,i.
setTooltipPosition(o)}function n(e,s,a){o.clickmode?(t(i.getPlaceholder()).bind("plothover",p),i.hideTooltip(),o.clickmode=!1):(p(e,s,a),o.getDomElement().is(":visible")&&(t(i.getPlaceholder()).unbind("plothover",p),o.clickmode=!0))}function p(e,s,a){var n=function(t,i,o,e){return Math.sqrt((o-t)*(o-t)+(e-i)*(e-i))};if(a)i.showTooltip(a,o.tooltipOptions.snap?a:s);else if(o.plotOptions.series.lines.show&&!0===o.tooltipOptions.lines){var p=o.plotOptions.grid.mouseActiveRadius,r={distance:p+1},l=s;t.each(i.getData(),(function(t,e){for(var a=0,p=-1,d=1;d<e.data.length;d++)e.data[d-1][0]<=s.x&&e.data[d][0]>=s.x&&(a=d-1,p=d);if(-1!==p){var c={x:e.data[a][0],y:e.data[a][1]},x={x:e.data[p][0],y:e.data[p][1]},h=function(t,i,o,e,s,a,p){if(!p||(p=function(t,i,o,e,s,a){if(void 0!==o)return{x:o,y:i};if(void 0!==e)return{x:t,y:e};var n,p=-1/((a-e)/(s-o));return{x:n=(s*(t*p-i+e)+o*(t*-p+i-a))/(p*(s-o)+e-a),y:p*n-p*t+i}}(t,i,o,e,s,a),p.x>=Math.min(o,s)&&p.x<=Math.max(o,s)&&p.y>=Math.min(e,a)&&p.y<=
Math.max(e,a))){var r=e-a,l=s-o,d=o*a-e*s;return Math.abs(r*t+l*i+d)/Math.sqrt(r*r+l*l)}var c=n(t,i,o,e),x=n(t,i,s,a);return c>x?x:c}(e.xaxis.p2c(s.x),e.yaxis.p2c(s.y),e.xaxis.p2c(c.x),e.yaxis.p2c(c.y),e.xaxis.p2c(x.x),e.yaxis.p2c(x.y),!1);if(h<r.distance){var u=n(c.x,c.y,s.x,s.y)<n(s.x,s.y,x.x,x.y)?a:p,m=(e.datapoints.pointsize,[s.x,c.y+(x.y-c.y)*((s.x-c.x)/(x.x-c.x))]);r={distance:h,item:{datapoint:m,dataIndex:u,series:e,seriesIndex:t}},o.tooltipOptions.snap&&(l={pageX:e.xaxis.p2c(m[0]),pageY:e.yaxis.p2c(m[1])})}}else i.hideTooltip()})),r.distance<p+1?i.showTooltip(r.item,l):i.hideTooltip()}else i.hideTooltip()}i.hooks.bindEvents.push((function(i,e){if(o.plotOptions=i.getOptions(),"boolean"==typeof o.plotOptions.tooltip&&(o.plotOptions.tooltipOpts.show=o.plotOptions.tooltip,o.plotOptions.tooltip=o.plotOptions.tooltipOpts,delete o.plotOptions.tooltipOpts),!1!==o.plotOptions.tooltip.show&&void 0!==o.plotOptions.tooltip.show){o.tooltipOptions=o.plotOptions.tooltip,o.tooltipOptions.
$compat?(o.wfunc="width",o.hfunc="height"):(o.wfunc="innerWidth",o.hfunc="innerHeight");o.getDomElement();t(i.getPlaceholder()).bind("plothover",p),o.tooltipOptions.clickTips&&t(i.getPlaceholder()).bind("plotclick",n),o.clickmode=!1,t(e).bind("mousemove",a)}})),i.hooks.shutdown.push((function(i,o){t(i.getPlaceholder()).unbind("plothover",p),t(i.getPlaceholder()).unbind("plotclick",n),i.removeTooltip(),t(o).unbind("mousemove",a)})),i.setTooltipPosition=function(i){var e=o.getDomElement(),s=e.outerWidth()+o.tooltipOptions.shifts.x,a=e.outerHeight()+o.tooltipOptions.shifts.y;i.x-t(window).scrollLeft()>t(window)[o.wfunc]()-s&&(i.x-=s,i.x=Math.max(i.x,0)),i.y-t(window).scrollTop()>t(window)[o.hfunc]()-a&&(i.y-=a),isNaN(i.x)?o.tipPosition.x=o.tipPosition.xPrev:(o.tipPosition.x=i.x,o.tipPosition.xPrev=i.x),isNaN(i.y)?o.tipPosition.y=o.tipPosition.yPrev:(o.tipPosition.y=i.y,o.tipPosition.yPrev=i.y)},i.showTooltip=function(t,e,s){var a=o.getDomElement(),n=o.stringFormat(o.tooltipOptions.content
,t);""!==n&&(a.html(n),i.setTooltipPosition({x:o.tipPosition.x,y:o.tipPosition.y}),a.css({left:o.tipPosition.x+o.tooltipOptions.shifts.x,top:o.tipPosition.y+o.tooltipOptions.shifts.y}).show(),"function"==typeof o.tooltipOptions.onHover&&o.tooltipOptions.onHover(t,a))},i.hideTooltip=function(){o.getDomElement().hide().html("")},i.removeTooltip=function(){o.getDomElement().remove()}},o.prototype.getDomElement=function(){var i=t("<div>");return this.tooltipOptions&&this.tooltipOptions.cssClass&&0===(i=t("."+this.tooltipOptions.cssClass)).length&&((i=t("<div />").addClass(this.tooltipOptions.cssClass)).appendTo("body").hide().css({position:"absolute"}),this.tooltipOptions.defaultTheme&&i.css({background:"#fff","z-index":"1040",padding:"0.4em 0.6em","border-radius":"0.5em","font-size":"0.8em",border:"1px solid #111",display:"none","white-space":"nowrap"})),i},o.prototype.stringFormat=function(t,i){var o,e,s,a,n,p=/%s/,r=/%c/,l=/%lx/,d=/%ly/,c=/%x\.{0,1}(\d{0,})/,x=/%y\.{0,1}(\d{0,})/;if(
void 0!==i.series.threshold?(o=i.datapoint[0],e=i.datapoint[1],s=i.datapoint[2]):void 0!==i.series.curvedLines?(o=i.datapoint[0],e=i.datapoint[1]):void 0!==i.series.lines&&i.series.lines.steps?(o=i.series.datapoints.points[2*i.dataIndex],e=i.series.datapoints.points[2*i.dataIndex+1],s=""):(o=i.series.data[i.dataIndex][0],e=i.series.data[i.dataIndex][1],s=i.series.data[i.dataIndex][2]),null===i.series.label&&i.series.originSeries&&(i.series.label=i.series.originSeries.label),"function"==typeof t&&(t=t(i.series.label,o,e,i)),"boolean"==typeof t&&!t)return"";if(s&&(t=t.replace("%ct",s)),void 0!==i.series.percent?a=i.series.percent:void 0!==i.series.percents&&(a=i.series.percents[i.dataIndex]),"number"==typeof a&&(t=this.adjustValPrecision(/%p\.{0,1}(\d{0,})/,t,a)),i.series.hasOwnProperty("pie")&&void 0!==i.series.data[0][1]&&(n=i.series.data[0][1]),"number"==typeof n&&(t=t.replace("%n",n)),t=void 0!==i.series.label?t.replace(p,i.series.label):t.replace(p,""),t=void 0!==i.series.color?t.
replace(r,i.series.color):t.replace(r,""),t=this.hasAxisLabel("xaxis",i)?t.replace(l,i.series.xaxis.options.axisLabel):t.replace(l,""),t=this.hasAxisLabel("yaxis",i)?t.replace(d,i.series.yaxis.options.axisLabel):t.replace(d,""),this.isTimeMode("xaxis",i)&&this.isXDateFormat(i)&&(t=t.replace(c,this.timestampToDate(o,this.tooltipOptions.xDateFormat,i.series.xaxis.options))),this.isTimeMode("yaxis",i)&&this.isYDateFormat(i)&&(t=t.replace(x,this.timestampToDate(e,this.tooltipOptions.yDateFormat,i.series.yaxis.options))),"number"==typeof o&&(t=this.adjustValPrecision(c,t,o)),"number"==typeof e&&(t=this.adjustValPrecision(x,t,e)),void 0!==i.series.xaxis.ticks){var h;h=this.hasRotatedXAxisTicks(i)?"rotatedTicks":"ticks";var u=i.dataIndex+i.seriesIndex;for(var m in i.series.xaxis[h]){if(i.series.xaxis[h].hasOwnProperty(u)&&!this.isTimeMode("xaxis",i))(this.isCategoriesMode("xaxis",i)?i.series.xaxis[h][u].label:i.series.xaxis[h][u].v)===o&&(t=t.replace(c,i.series.xaxis[h][u].label.replace(/\$/g
,"$$$$")))}}if(void 0!==i.series.yaxis.ticks)for(var y in i.series.yaxis.ticks){if(i.series.yaxis.ticks.hasOwnProperty(y))(this.isCategoriesMode("yaxis",i)?i.series.yaxis.ticks[y].label:i.series.yaxis.ticks[y].v)===e&&(t=t.replace(x,i.series.yaxis.ticks[y].label.replace(/\$/g,"$$$$")))}return void 0!==i.series.xaxis.tickFormatter&&(t=t.replace("%x",i.series.xaxis.tickFormatter(o,i.series.xaxis).replace(/\$/g,"$$"))),void 0!==i.series.yaxis.tickFormatter&&(t=t.replace("%y",i.series.yaxis.tickFormatter(e,i.series.yaxis).replace(/\$/g,"$$"))),t},o.prototype.isTimeMode=function(t,i){return void 0!==i.series[t].options.mode&&"time"===i.series[t].options.mode},o.prototype.isXDateFormat=function(t){return void 0!==this.tooltipOptions.xDateFormat&&null!==this.tooltipOptions.xDateFormat},o.prototype.isYDateFormat=function(t){return void 0!==this.tooltipOptions.yDateFormat&&null!==this.tooltipOptions.yDateFormat},o.prototype.isCategoriesMode=function(t,i){return void 0!==i.series[t].options.mode
&&"categories"===i.series[t].options.mode},o.prototype.timestampToDate=function(i,o,e){var s=t.plot.dateGenerator(i,e);return t.plot.formatDate(s,o,this.tooltipOptions.monthNames,this.tooltipOptions.dayNames)},o.prototype.adjustValPrecision=function(t,i,o){var e;return null!==i.match(t)&&""!==RegExp.$1&&(e=RegExp.$1,o=o.toFixed(e),i=i.replace(t,o)),i},o.prototype.hasAxisLabel=function(i,o){return-1!==t.inArray("axisLabels",this.plotPlugins)&&void 0!==o.series[i].options.axisLabel&&o.series[i].options.axisLabel.length>0},o.prototype.hasRotatedXAxisTicks=function(i){return-1!==t.inArray("tickRotor",this.plotPlugins)&&void 0!==i.series.xaxis.rotatedTicks};t.plot.plugins.push({init:function(t){new o(t)},options:i,name:"tooltip",version:"0.8.5"})}(jQuery);!function(e,t,i){var n,a=[],r=e.resize=e.extend(e.resize,{}),o=!1,s="setTimeout",u="resize",h=u+"-special-event",m="pendingDelay",l="activeDelay",c="throttleWindow";function d(i){!0===o&&(o=i||1);for(var s=a.length-1;s>=0;s--){var l=e(a[s]
);if(l[0]==t||l.is(":visible")){var c=l.width(),f=l.height(),w=l.data(h);!w||c===w.w&&f===w.h||(l.trigger(u,[w.w=c,w.h=f]),o=i||!0)}else(w=l.data(h)).w=0,w.h=0}null!==n&&(o&&(null==i||i-o<1e3)?n=t.requestAnimationFrame(d):(n=setTimeout(d,r[m]),o=!1))}r[m]=200,r[l]=20,r[c]=!0,e.event.special[u]={setup:function(){if(!r[c]&&this[s])return!1;var t=e(this);a.push(this),t.data(h,{w:t.width(),h:t.height()}),1===a.length&&(n=i,d())},teardown:function(){if(!r[c]&&this[s])return!1;for(var t=e(this),i=a.length-1;i>=0;i--)if(a[i]==this){a.splice(i,1);break}t.removeData(h),a.length||(o?cancelAnimationFrame(n):clearTimeout(n),n=null)},add:function(t){if(!r[c]&&this[s])return!1;var n;function a(t,a,r){var o=e(this),s=o.data(h)||{};s.w=a!==i?a:o.width(),s.h=r!==i?r:o.height(),n.apply(this,arguments)}if(e.isFunction(t))return n=t,a;n=t.handler,t.handler=a}},t.requestAnimationFrame||(t.requestAnimationFrame=t.webkitRequestAnimationFrame||t.mozRequestAnimationFrame||t.oRequestAnimationFrame||t.
msRequestAnimationFrame||function(e,i){return t.setTimeout((function(){e((new Date).getTime())}),r[l])}),t.cancelAnimationFrame||(t.cancelAnimationFrame=t.webkitCancelRequestAnimationFrame||t.mozCancelRequestAnimationFrame||t.oCancelRequestAnimationFrame||t.msCancelRequestAnimationFrame||clearTimeout)}(jQuery,window),jQuery.plot.plugins.push({init:function(e){function t(){var t=e.getPlaceholder();0!==t.width()&&0!==t.height()&&(e.resize(),e.setupGrid(),e.draw())}e.hooks.bindEvents.push((function(e,i){e.getPlaceholder().resize(t)})),e.hooks.shutdown.push((function(e,i){e.getPlaceholder().unbind("resize",t)}))},options:{},name:"resize",version:"1.0"});!function(e){"use strict";var t=e.plot.saturated.floorInBase,i=function(e,t){var i=new e(t),o=i.setTime.bind(i);i.update=function(e){o(e),e=Math.round(1e3*e)/1e3,this.microseconds=1e3*(e-Math.floor(e))};var n=i.getTime.bind(i);return i.getTime=function(){return n()+this.microseconds/1e3},i.setTime=function(e){this.update(e)},i.
getMicroseconds=function(){return this.microseconds},i.setMicroseconds=function(e){var t=n()+e/1e3;this.update(t)},i.setUTCMicroseconds=function(e){this.setMicroseconds(e)},i.getUTCMicroseconds=function(){return this.getMicroseconds()},i.microseconds=null,i.microEpoch=null,i.update(t),i};function o(e,t,i,o){if("function"==typeof e.strftime)return e.strftime(t);var n,s=function(e,t){return t=""+(null==t?"0":t),1===(e=""+e).length?t+e:e},r=function(e,t,i){var o,n=1e3*e+t;if(i<6&&i>0){var s=parseFloat("1e"+(i-6));o=("00000"+(n=Math.round(Math.round(n*s)/s))).slice(-6,-(6-i))}else o=("00000"+(n=Math.round(n))).slice(-6);return o},a=[],c=!1,m=e.getHours(),u=m<12;i||(i=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]),o||(o=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"]),n=m>12?m-12:0===m?12:m;for(var d=-1,l=0;l<t.length;++l){var h=t.charAt(l);if(!isNaN(Number(h))&&Number(h)>0)d=Number(h);else if(c){switch(h){case"a":h=""+o[e.getDay()];break;case"b":h=""+i[e.getMonth()]
;break;case"d":h=s(e.getDate());break;case"e":h=s(e.getDate()," ");break;case"h":case"H":h=s(m);break;case"I":h=s(n);break;case"l":h=s(n," ");break;case"m":h=s(e.getMonth()+1);break;case"M":h=s(e.getMinutes());break;case"q":h=""+(Math.floor(e.getMonth()/3)+1);break;case"S":h=s(e.getSeconds());break;case"s":h=""+r(e.getMilliseconds(),e.getMicroseconds(),d);break;case"y":h=s(e.getFullYear()%100);break;case"Y":h=""+e.getFullYear();break;case"p":h=u?"am":"pm";break;case"P":h=u?"AM":"PM";break;case"w":h=""+e.getDay()}a.push(h),c=!1}else"%"===h?c=!0:a.push(h)}return a.join("")}function n(e){function t(e,t,i,o){e[t]=function(){return i[o].apply(i,arguments)}}var i={date:e};void 0!==e.strftime&&t(i,"strftime",e,"strftime"),t(i,"getTime",e,"getTime"),t(i,"setTime",e,"setTime");for(var o=["Date","Day","FullYear","Hours","Minutes","Month","Seconds","Milliseconds","Microseconds"],n=0;n<o.length;n++)t(i,"get"+o[n],e,"getUTC"+o[n]),t(i,"set"+o[n],e,"setUTC"+o[n]);return i}function s(e,t){var o=
864e13;if(t&&"seconds"===t.timeBase?e*=1e3:"microseconds"===t.timeBase&&(e/=1e3),e>o?e=o:e<-o&&(e=-o),"browser"===t.timezone)return i(Date,e);if(t.timezone&&"utc"!==t.timezone){if("undefined"!=typeof timezoneJS&&void 0!==timezoneJS.Date){var s=i(timezoneJS.Date,e);return s.setTimezone(t.timezone),s.setTime(e),s}return n(i(Date,e))}return n(i(Date,e))}var r={microsecond:1e-6,millisecond:.001,second:1,minute:60,hour:3600,day:86400,month:2592e3,quarter:7776e3,year:525949.2*60},a={microsecond:.001,millisecond:1,second:1e3,minute:6e4,hour:36e5,day:864e5,month:2592e6,quarter:7776e6,year:525949.2*60*1e3},c={microsecond:1,millisecond:1e3,second:1e6,minute:6e7,hour:36e8,day:864e8,month:2592e9,quarter:7776e9,year:31556951999999.996},m=[[1,"microsecond"],[2,"microsecond"],[5,"microsecond"],[10,"microsecond"],[25,"microsecond"],[50,"microsecond"],[100,"microsecond"],[250,"microsecond"],[500,"microsecond"],[1,"millisecond"],[2,"millisecond"],[5,"millisecond"],[10,"millisecond"],[25,"millisecond"],[
50,"millisecond"],[100,"millisecond"],[250,"millisecond"],[500,"millisecond"],[1,"second"],[2,"second"],[5,"second"],[10,"second"],[30,"second"],[1,"minute"],[2,"minute"],[5,"minute"],[10,"minute"],[30,"minute"],[1,"hour"],[2,"hour"],[4,"hour"],[8,"hour"],[12,"hour"],[1,"day"],[2,"day"],[3,"day"],[.25,"month"],[.5,"month"],[1,"month"],[2,"month"]],u=m.concat([[3,"month"],[6,"month"],[1,"year"]]),d=m.concat([[1,"quarter"],[2,"quarter"],[1,"year"]]);function l(e){var i,o=e.options,n=[],m=s(e.min,o),l=0,h=o.tickSize&&"quarter"===o.tickSize[1]||o.minTickSize&&"quarter"===o.minTickSize[1]?d:u;i="seconds"===o.timeBase?r:"microseconds"===o.timeBase?c:a,null!==o.minTickSize&&void 0!==o.minTickSize&&(l="number"==typeof o.tickSize?o.tickSize:o.minTickSize[0]*i[o.minTickSize[1]]);for(var f=0;f<h.length-1&&!(e.delta<(h[f][0]*i[h[f][1]]+h[f+1][0]*i[h[f+1][1]])/2&&h[f][0]*i[h[f][1]]>=l);++f);var M=h[f][0],g=h[f][1];if("year"===g){if(null!==o.minTickSize&&void 0!==o.minTickSize&&"year"===o.
minTickSize[1])M=Math.floor(o.minTickSize[0]);else{var k=parseFloat("1e"+Math.floor(Math.log(e.delta/i.year)/Math.LN10)),p=e.delta/i.year/k;M=p<1.5?1:p<3?2:p<7.5?5:10,M*=k}M<1&&(M=1)}e.tickSize=o.tickSize||[M,g];var y=e.tickSize[0],S=y*i[g=e.tickSize[1]];"microsecond"===g?m.setMicroseconds(t(m.getMicroseconds(),y)):"millisecond"===g?m.setMilliseconds(t(m.getMilliseconds(),y)):"second"===g?m.setSeconds(t(m.getSeconds(),y)):"minute"===g?m.setMinutes(t(m.getMinutes(),y)):"hour"===g?m.setHours(t(m.getHours(),y)):"month"===g?m.setMonth(t(m.getMonth(),y)):"quarter"===g?m.setMonth(3*t(m.getMonth()/3,y)):"year"===g&&m.setFullYear(t(m.getFullYear(),y)),S>=i.millisecond&&(S>=i.second?m.setMicroseconds(0):m.setMicroseconds(1e3*m.getMilliseconds())),S>=i.minute&&m.setSeconds(0),S>=i.hour&&m.setMinutes(0),S>=i.day&&m.setHours(0),S>=4*i.day&&m.setDate(1),S>=2*i.month&&m.setMonth(t(m.getMonth(),3)),S>=2*i.quarter&&m.setMonth(t(m.getMonth(),6)),S>=i.year&&m.setMonth(0);var v,T,z=0,b=Number.NaN;do{if(T
=b,v=m.getTime(),b=o&&"seconds"===o.timeBase?v/1e3:o&&"microseconds"===o.timeBase?1e3*v:v,n.push(b),"month"===g||"quarter"===g)if(y<1){m.setDate(1);var q=m.getTime();m.setMonth(m.getMonth()+("quarter"===g?3:1));var B=m.getTime();m.setTime(b+z*i.hour+(B-q)*y),z=m.getHours(),m.setHours(0)}else m.setMonth(m.getMonth()+y*("quarter"===g?3:1));else"year"===g?m.setFullYear(m.getFullYear()+y):"seconds"===o.timeBase?m.setTime(1e3*(b+S)):"microseconds"===o.timeBase?m.setTime((b+S)/1e3):m.setTime(b+S)}while(b<e.max&&b!==T);return n}e.plot.plugins.push({init:function(t){t.hooks.processOptions.push((function(t){e.each(t.getAxes(),(function(e,t){var i=t.options;if("time"===i.mode){if(t.tickGenerator=l,"tickFormatter"in i&&"function"==typeof i.tickFormatter)return;t.tickFormatter=function(e,t){var n=s(e,t.options);if(null!=i.timeformat)return o(n,i.timeformat,i.monthNames,i.dayNames);var m,u=t.options.tickSize&&"quarter"===t.options.tickSize[1]||t.options.minTickSize&&"quarter"===t.options.
minTickSize[1];m="seconds"===i.timeBase?r:"microseconds"===i.timeBase?c:a;var d,l,h=t.tickSize[0]*m[t.tickSize[1]],f=t.max-t.min,M=i.twelveHourClock?" %p":"",g=i.twelveHourClock?"%I":"%H";if(d="seconds"===i.timeBase?1:"microseconds"===i.timeBase?1e6:1e3,h<m.second){var k=-Math.floor(Math.log10(h/d));String(h).indexOf("25")>-1&&k++,l="%S.%"+k+"s"}else l=h<m.minute?g+":%M:%S"+M:h<m.day?f<2*m.day?g+":%M"+M:"%b %d "+g+":%M"+M:h<m.month?"%b %d":u&&h<m.quarter||!u&&h<m.year?f<m.year?"%b":"%b %Y":u&&h<m.year?f<m.year?"Q%q":"Q%q %Y":"%Y";return o(n,l,i.monthNames,i.dayNames)}}}))}))},options:{xaxis:{timezone:null,timeformat:null,twelveHourClock:!1,monthNames:null,timeBase:"seconds"},yaxis:{timeBase:"seconds"}},name:"time",version:"1.0"}),e.plot.formatDate=o,e.plot.dateGenerator=s,e.plot.dateTickGenerator=l,e.plot.makeUtcWrapper=n}(jQuery);!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof module&&module.exports?module.exports=function(t,n){return void 0===
n&&(n="undefined"!=typeof window?require("jquery"):require("jquery")(t)),e(n),n}:e(jQuery)}((function(e){var t=function(){if(e&&e.fn&&e.fn.select2&&e.fn.select2.amd)var t=e.fn.select2.amd;var n;return function(){var e,n,i;t&&t.requirejs||(t?n=t:t={},function(t){var r,o,s,a,l={},c={},u={},d={},p=Object.prototype.hasOwnProperty,h=[].slice,f=/\.js$/;function g(e,t){return p.call(e,t)}function m(e,t){var n,i,r,o,s,a,l,c,d,p,h,g=t&&t.split("/"),m=u.map,v=m&&m["*"]||{};if(e){for(s=(e=e.split("/")).length-1,u.nodeIdCompat&&f.test(e[s])&&(e[s]=e[s].replace(f,"")),"."===e[0].charAt(0)&&g&&(e=g.slice(0,g.length-1).concat(e)),d=0;d<e.length;d++)if("."===(h=e[d]))e.splice(d,1),d-=1;else if(".."===h){if(0===d||1===d&&".."===e[2]||".."===e[d-1])continue;d>0&&(e.splice(d-1,2),d-=2)}e=e.join("/")}if((g||v)&&m){for(d=(n=e.split("/")).length;d>0;d-=1){if(i=n.slice(0,d).join("/"),g)for(p=g.length;p>0;p-=1)if((r=m[g.slice(0,p).join("/")])&&(r=r[i])){o=r,a=d;break}if(o)break;!l&&v&&v[i]&&(l=v[i],c=d)}!o&&l
&&(o=l,a=c),o&&(n.splice(0,a,o),e=n.join("/"))}return e}function v(e,n){return function(){var i=h.call(arguments,0);return"string"!=typeof i[0]&&1===i.length&&i.push(null),o.apply(t,i.concat([e,n]))}}function y(e){return function(t){l[e]=t}}function _(e){if(g(c,e)){var n=c[e];delete c[e],d[e]=!0,r.apply(t,n)}if(!g(l,e)&&!g(d,e))throw new Error("No "+e);return l[e]}function w(e){var t,n=e?e.indexOf("!"):-1;return n>-1&&(t=e.substring(0,n),e=e.substring(n+1,e.length)),[t,e]}function $(e){return e?w(e):[]}function b(e){return function(){return u&&u.config&&u.config[e]||{}}}s=function(e,t){var n,i,r=w(e),o=r[0],s=t[1];return e=r[1],o&&(n=_(o=m(o,s))),o?e=n&&n.normalize?n.normalize(e,(i=s,function(e){return m(e,i)})):m(e,s):(o=(r=w(e=m(e,s)))[0],e=r[1],o&&(n=_(o))),{f:o?o+"!"+e:e,n:e,pr:o,p:n}},a={require:function(e){return v(e)},exports:function(e){var t=l[e];return void 0!==t?t:l[e]={}},module:function(e){return{id:e,uri:"",exports:l[e],config:b(e)}}},r=function(e,n,i,r){var o,u,p,h,f,m,w
,b=[],A=typeof i;if(m=$(r=r||e),"undefined"===A||"function"===A){for(n=!n.length&&i.length?["require","exports","module"]:n,f=0;f<n.length;f+=1)if("require"===(u=(h=s(n[f],m)).f))b[f]=a.require(e);else if("exports"===u)b[f]=a.exports(e),w=!0;else if("module"===u)o=b[f]=a.module(e);else if(g(l,u)||g(c,u)||g(d,u))b[f]=_(u);else{if(!h.p)throw new Error(e+" missing "+u);h.p.load(h.n,v(r,!0),y(u),{}),b[f]=l[u]}p=i?i.apply(l[e],b):void 0,e&&(o&&o.exports!==t&&o.exports!==l[e]?l[e]=o.exports:p===t&&w||(l[e]=p))}else e&&(l[e]=i)},e=n=o=function(e,n,i,l,c){if("string"==typeof e)return a[e]?a[e](n):_(s(e,$(n)).f);if(!e.splice){if((u=e).deps&&o(u.deps,u.callback),!n)return;n.splice?(e=n,n=i,i=null):e=t}return n=n||function(){},"function"==typeof i&&(i=l,l=c),l?r(t,e,n,i):setTimeout((function(){r(t,e,n,i)}),4),o},o.config=function(e){return o(e)},e._defined=l,(i=function(e,t,n){if("string"!=typeof e)throw new Error("See almond README: incorrect module build, no module name");t.splice||(n=t,t=[]),g
(l,e)||g(c,e)||(c[e]=[e,t,n])}).amd={jQuery:!0}}(),t.requirejs=e,t.require=n,t.define=i)}(),t.define("almond",(function(){})),t.define("jquery",[],(function(){var t=e||$;return null==t&&console&&console.error&&console.error("Select2: An instance of jQuery or a jQuery-compatible library was not found. Make sure that you are including jQuery before Select2 on your web page."),t})),t.define("select2/utils",["jquery"],(function(e){var t={};function n(e){var t=e.prototype,n=[];for(var i in t){"function"==typeof t[i]&&("constructor"!==i&&n.push(i))}return n}t.Extend=function(e,t){var n={}.hasOwnProperty;function i(){this.constructor=e}for(var r in t)n.call(t,r)&&(e[r]=t[r]);return i.prototype=t.prototype,e.prototype=new i,e.__super__=t.prototype,e},t.Decorate=function(e,t){var i=n(t),r=n(e);function o(){var n=Array.prototype.unshift,i=t.prototype.constructor.length,r=e.prototype.constructor;i>0&&(n.call(arguments,e.prototype.constructor),r=t.prototype.constructor),r.apply(this,arguments)}t.
displayName=e.displayName,o.prototype=new function(){this.constructor=o};for(var s=0;s<r.length;s++){var a=r[s];o.prototype[a]=e.prototype[a]}for(var l=function(e){var n=function(){};e in o.prototype&&(n=o.prototype[e]);var i=t.prototype[e];return function(){var e=Array.prototype.unshift;return e.call(arguments,n),i.apply(this,arguments)}},c=0;c<i.length;c++){var u=i[c];o.prototype[u]=l(u)}return o};var i=function(){this.listeners={}};i.prototype.on=function(e,t){this.listeners=this.listeners||{},e in this.listeners?this.listeners[e].push(t):this.listeners[e]=[t]},i.prototype.trigger=function(e){var t=Array.prototype.slice,n=t.call(arguments,1);this.listeners=this.listeners||{},null==n&&(n=[]),0===n.length&&n.push({}),n[0]._type=e,e in this.listeners&&this.invoke(this.listeners[e],t.call(arguments,1)),"*"in this.listeners&&this.invoke(this.listeners["*"],arguments)},i.prototype.invoke=function(e,t){for(var n=0,i=e.length;n<i;n++)e[n].apply(this,t)},t.Observable=i,t.generateChars=
function(e){for(var t="",n=0;n<e;n++){t+=Math.floor(36*Math.random()).toString(36)}return t},t.bind=function(e,t){return function(){e.apply(t,arguments)}},t._convertData=function(e){for(var t in e){var n=t.split("-"),i=e;if(1!==n.length){for(var r=0;r<n.length;r++){var o=n[r];(o=o.substring(0,1).toLowerCase()+o.substring(1))in i||(i[o]={}),r==n.length-1&&(i[o]=e[t]),i=i[o]}delete e[t]}}return e},t.hasScroll=function(t,n){var i=e(n),r=n.style.overflowX,o=n.style.overflowY;return(r!==o||"hidden"!==o&&"visible"!==o)&&("scroll"===r||"scroll"===o||(i.innerHeight()<n.scrollHeight||i.innerWidth()<n.scrollWidth))},t.escapeMarkup=function(e){var t={"\\":"&#92;","&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#47;"};return"string"!=typeof e?e:String(e).replace(/[&<>"'\/\\]/g,(function(e){return t[e]}))},t.appendMany=function(t,n){if("1.7"===e.fn.jquery.substr(0,3)){var i=e();e.map(n,(function(e){i=i.add(e)})),n=i}t.append(n)},t.__cache={};var r=0;return t.GetUniqueElementId=
function(e){var t=e.getAttribute("data-select2-id");return null==t&&(e.id?(t=e.id,e.setAttribute("data-select2-id",t)):(e.setAttribute("data-select2-id",++r),t=r.toString())),t},t.StoreData=function(e,n,i){var r=t.GetUniqueElementId(e);t.__cache[r]||(t.__cache[r]={}),t.__cache[r][n]=i},t.GetData=function(n,i){var r=t.GetUniqueElementId(n);return i?t.__cache[r]&&null!=t.__cache[r][i]?t.__cache[r][i]:e(n).data(i):t.__cache[r]},t.RemoveData=function(e){var n=t.GetUniqueElementId(e);null!=t.__cache[n]&&delete t.__cache[n],e.removeAttribute("data-select2-id")},t})),t.define("select2/results",["jquery","./utils"],(function(e,t){function n(e,t,i){this.$element=e,this.data=i,this.options=t,n.__super__.constructor.call(this)}return t.Extend(n,t.Observable),n.prototype.render=function(){var t=e('<ul class="select2-results__options" role="listbox"></ul>');return this.options.get("multiple")&&t.attr("aria-multiselectable","true"),this.$results=t,t},n.prototype.clear=function(){this.$results.empty(
)},n.prototype.displayMessage=function(t){var n=this.options.get("escapeMarkup");this.clear(),this.hideLoading();var i=e('<li role="alert" aria-live="assertive" class="select2-results__option"></li>'),r=this.options.get("translations").get(t.message);i.append(n(r(t.args))),i[0].className+=" select2-results__message",this.$results.append(i)},n.prototype.hideMessages=function(){this.$results.find(".select2-results__message").remove()},n.prototype.append=function(e){this.hideLoading();var t=[];if(null!=e.results&&0!==e.results.length){e.results=this.sort(e.results);for(var n=0;n<e.results.length;n++){var i=e.results[n],r=this.option(i);t.push(r)}this.$results.append(t)}else 0===this.$results.children().length&&this.trigger("results:message",{message:"noResults"})},n.prototype.position=function(e,t){t.find(".select2-results").append(e)},n.prototype.sort=function(e){return this.options.get("sorter")(e)},n.prototype.highlightFirstItem=function(){var e=this.$results.find(
".select2-results__option[aria-selected]"),t=e.filter("[aria-selected=true]");t.length>0?t.first().trigger("mouseenter"):e.first().trigger("mouseenter"),this.ensureHighlightVisible()},n.prototype.setClasses=function(){var n=this;this.data.current((function(i){var r=e.map(i,(function(e){return e.id.toString()}));n.$results.find(".select2-results__option[aria-selected]").each((function(){var n=e(this),i=t.GetData(this,"data"),o=""+i.id;null!=i.element&&i.element.selected||null==i.element&&e.inArray(o,r)>-1?n.attr("aria-selected","true"):n.attr("aria-selected","false")}))}))},n.prototype.showLoading=function(e){this.hideLoading();var t={disabled:!0,loading:!0,text:this.options.get("translations").get("searching")(e)},n=this.option(t);n.className+=" loading-results",this.$results.prepend(n)},n.prototype.hideLoading=function(){this.$results.find(".loading-results").remove()},n.prototype.option=function(n){var i=document.createElement("li");i.className="select2-results__option";var r={role:
"option","aria-selected":"false"},o=window.Element.prototype.matches||window.Element.prototype.msMatchesSelector||window.Element.prototype.webkitMatchesSelector;for(var s in(null!=n.element&&o.call(n.element,":disabled")||null==n.element&&n.disabled)&&(delete r["aria-selected"],r["aria-disabled"]="true"),null==n.id&&delete r["aria-selected"],null!=n._resultId&&(i.id=n._resultId),n.title&&(i.title=n.title),n.children&&(r.role="group",r["aria-label"]=n.text,delete r["aria-selected"]),r){var a=r[s];i.setAttribute(s,a)}if(n.children){var l=e(i),c=document.createElement("strong");c.className="select2-results__group";e(c);this.template(n,c);for(var u=[],d=0;d<n.children.length;d++){var p=n.children[d],h=this.option(p);u.push(h)}var f=e("<ul></ul>",{class:"select2-results__options select2-results__options--nested"});f.append(u),l.append(c),l.append(f)}else this.template(n,i);return t.StoreData(i,"data",n),i},n.prototype.bind=function(n,i){var r=this,o=n.id+"-results";this.$results.attr("id",o
),n.on("results:all",(function(e){r.clear(),r.append(e.data),n.isOpen()&&(r.setClasses(),r.highlightFirstItem())})),n.on("results:append",(function(e){r.append(e.data),n.isOpen()&&r.setClasses()})),n.on("query",(function(e){r.hideMessages(),r.showLoading(e)})),n.on("select",(function(){n.isOpen()&&(r.setClasses(),r.options.get("scrollAfterSelect")&&r.highlightFirstItem())})),n.on("unselect",(function(){n.isOpen()&&(r.setClasses(),r.options.get("scrollAfterSelect")&&r.highlightFirstItem())})),n.on("open",(function(){r.$results.attr("aria-expanded","true"),r.$results.attr("aria-hidden","false"),r.setClasses(),r.ensureHighlightVisible()})),n.on("close",(function(){r.$results.attr("aria-expanded","false"),r.$results.attr("aria-hidden","true"),r.$results.removeAttr("aria-activedescendant")})),n.on("results:toggle",(function(){var e=r.getHighlightedResults();0!==e.length&&e.trigger("mouseup")})),n.on("results:select",(function(){var e=r.getHighlightedResults();if(0!==e.length){var n=t.
GetData(e[0],"data");"true"==e.attr("aria-selected")?r.trigger("close",{}):r.trigger("select",{data:n})}})),n.on("results:previous",(function(){var e=r.getHighlightedResults(),t=r.$results.find("[aria-selected]"),n=t.index(e);if(!(n<=0)){var i=n-1;0===e.length&&(i=0);var o=t.eq(i);o.trigger("mouseenter");var s=r.$results.offset().top,a=o.offset().top,l=r.$results.scrollTop()+(a-s);0===i?r.$results.scrollTop(0):a-s<0&&r.$results.scrollTop(l)}})),n.on("results:next",(function(){var e=r.getHighlightedResults(),t=r.$results.find("[aria-selected]"),n=t.index(e)+1;if(!(n>=t.length)){var i=t.eq(n);i.trigger("mouseenter");var o=r.$results.offset().top+r.$results.outerHeight(!1),s=i.offset().top+i.outerHeight(!1),a=r.$results.scrollTop()+s-o;0===n?r.$results.scrollTop(0):s>o&&r.$results.scrollTop(a)}})),n.on("results:focus",(function(e){e.element.addClass("select2-results__option--highlighted")})),n.on("results:message",(function(e){r.displayMessage(e)})),e.fn.mousewheel&&this.$results.on(
"mousewheel",(function(e){var t=r.$results.scrollTop(),n=r.$results.get(0).scrollHeight-t+e.deltaY,i=e.deltaY>0&&t-e.deltaY<=0,o=e.deltaY<0&&n<=r.$results.height();i?(r.$results.scrollTop(0),e.preventDefault(),e.stopPropagation()):o&&(r.$results.scrollTop(r.$results.get(0).scrollHeight-r.$results.height()),e.preventDefault(),e.stopPropagation())})),this.$results.on("mouseup",".select2-results__option[aria-selected]",(function(n){var i=e(this),o=t.GetData(this,"data");"true"!==i.attr("aria-selected")?r.trigger("select",{originalEvent:n,data:o}):r.options.get("multiple")?r.trigger("unselect",{originalEvent:n,data:o}):r.trigger("close",{})})),this.$results.on("mouseenter",".select2-results__option[aria-selected]",(function(n){var i=t.GetData(this,"data");r.getHighlightedResults().removeClass("select2-results__option--highlighted"),r.trigger("results:focus",{data:i,element:e(this)})}))},n.prototype.getHighlightedResults=function(){return this.$results.find(
".select2-results__option--highlighted")},n.prototype.destroy=function(){this.$results.remove()},n.prototype.ensureHighlightVisible=function(){var e=this.getHighlightedResults();if(0!==e.length){var t=this.$results.find("[aria-selected]").index(e),n=this.$results.offset().top,i=e.offset().top,r=this.$results.scrollTop()+(i-n),o=i-n;r-=2*e.outerHeight(!1),t<=2?this.$results.scrollTop(0):(o>this.$results.outerHeight()||o<0)&&this.$results.scrollTop(r)}},n.prototype.template=function(t,n){var i=this.options.get("templateResult"),r=this.options.get("escapeMarkup"),o=i(t,n);null==o?n.style.display="none":"string"==typeof o?n.innerHTML=r(o):e(n).append(o)},n})),t.define("select2/keys",[],(function(){return{BACKSPACE:8,TAB:9,ENTER:13,SHIFT:16,CTRL:17,ALT:18,ESC:27,SPACE:32,PAGE_UP:33,PAGE_DOWN:34,END:35,HOME:36,LEFT:37,UP:38,RIGHT:39,DOWN:40,DELETE:46}})),t.define("select2/selection/base",["jquery","../utils","../keys"],(function(e,t,n){function i(e,t){this.$element=e,this.options=t,i.
__super__.constructor.call(this)}return t.Extend(i,t.Observable),i.prototype.render=function(){var n=e('<span class="select2-selection" role="combobox"  aria-haspopup="true" aria-expanded="false"></span>');return this._tabindex=0,null!=t.GetData(this.$element[0],"old-tabindex")?this._tabindex=t.GetData(this.$element[0],"old-tabindex"):null!=this.$element.attr("tabindex")&&(this._tabindex=this.$element.attr("tabindex")),n.attr("title",this.$element.attr("title")),n.attr("tabindex",this._tabindex),n.attr("aria-disabled","false"),this.$selection=n,n},i.prototype.bind=function(e,t){var i=this,r=e.id+"-results";this.container=e,this.$selection.on("focus",(function(e){i.trigger("focus",e)})),this.$selection.on("blur",(function(e){i._handleBlur(e)})),this.$selection.on("keydown",(function(e){i.trigger("keypress",e),e.which===n.SPACE&&e.preventDefault()})),e.on("results:focus",(function(e){i.$selection.attr("aria-activedescendant",e.data._resultId)})),e.on("selection:update",(function(e){i.
update(e.data)})),e.on("open",(function(){i.$selection.attr("aria-expanded","true"),i.$selection.attr("aria-owns",r),i._attachCloseHandler(e)})),e.on("close",(function(){i.$selection.attr("aria-expanded","false"),i.$selection.removeAttr("aria-activedescendant"),i.$selection.removeAttr("aria-owns"),i.$selection.trigger("focus"),i._detachCloseHandler(e)})),e.on("enable",(function(){i.$selection.attr("tabindex",i._tabindex),i.$selection.attr("aria-disabled","false")})),e.on("disable",(function(){i.$selection.attr("tabindex","-1"),i.$selection.attr("aria-disabled","true")}))},i.prototype._handleBlur=function(t){var n=this;window.setTimeout((function(){document.activeElement==n.$selection[0]||e.contains(n.$selection[0],document.activeElement)||n.trigger("blur",t)}),1)},i.prototype._attachCloseHandler=function(n){e(document.body).on("mousedown.select2."+n.id,(function(n){var i=e(n.target).closest(".select2");e(".select2.select2-container--open").each((function(){this!=i[0]&&t.GetData(this,
"element").select2("close")}))}))},i.prototype._detachCloseHandler=function(t){e(document.body).off("mousedown.select2."+t.id)},i.prototype.position=function(e,t){t.find(".selection").append(e)},i.prototype.destroy=function(){this._detachCloseHandler(this.container)},i.prototype.update=function(e){throw new Error("The `update` method must be defined in child classes.")},i.prototype.isEnabled=function(){return!this.isDisabled()},i.prototype.isDisabled=function(){return this.options.get("disabled")},i})),t.define("select2/selection/single",["jquery","./base","../utils","../keys"],(function(e,t,n,i){function r(){r.__super__.constructor.apply(this,arguments)}return n.Extend(r,t),r.prototype.render=function(){var e=r.__super__.render.call(this);return e.addClass("select2-selection--single"),e.html('<span class="select2-selection__rendered"></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>'),e},r.prototype.bind=function(e,t){var n=this;r.
__super__.bind.apply(this,arguments);var i=e.id+"-container";this.$selection.find(".select2-selection__rendered").attr("id",i).attr("role","textbox").attr("aria-readonly","true"),this.$selection.attr("aria-labelledby",i),this.$selection.on("mousedown",(function(e){1===e.which&&n.trigger("toggle",{originalEvent:e})})),this.$selection.on("focus",(function(e){})),this.$selection.on("blur",(function(e){})),e.on("focus",(function(t){e.isOpen()||n.$selection.trigger("focus")}))},r.prototype.clear=function(){var e=this.$selection.find(".select2-selection__rendered");e.empty(),e.removeAttr("title")},r.prototype.display=function(e,t){var n=this.options.get("templateSelection");return this.options.get("escapeMarkup")(n(e,t))},r.prototype.selectionContainer=function(){return e("<span></span>")},r.prototype.update=function(e){if(0!==e.length){var t=e[0],n=this.$selection.find(".select2-selection__rendered"),i=this.display(t,n);n.empty().append(i);var r=t.title||t.text;r?n.attr("title",r):n.
removeAttr("title")}else this.clear()},r})),t.define("select2/selection/multiple",["jquery","./base","../utils"],(function(e,t,n){function i(e,t){i.__super__.constructor.apply(this,arguments)}return n.Extend(i,t),i.prototype.render=function(){var e=i.__super__.render.call(this);return e.addClass("select2-selection--multiple"),e.html('<ul class="select2-selection__rendered"></ul>'),e},i.prototype.bind=function(t,r){var o=this;i.__super__.bind.apply(this,arguments),this.$selection.on("click",(function(e){o.trigger("toggle",{originalEvent:e})})),this.$selection.on("click",".select2-selection__choice__remove",(function(t){if(!o.isDisabled()){var i=e(this).parent(),r=n.GetData(i[0],"data");o.trigger("unselect",{originalEvent:t,data:r})}}))},i.prototype.clear=function(){var e=this.$selection.find(".select2-selection__rendered");e.empty(),e.removeAttr("title")},i.prototype.display=function(e,t){var n=this.options.get("templateSelection");return this.options.get("escapeMarkup")(n(e,t))},i.
prototype.selectionContainer=function(){return e('<li class="select2-selection__choice"><span class="select2-selection__choice__remove" role="presentation">&times;</span></li>')},i.prototype.update=function(e){if(this.clear(),0!==e.length){for(var t=[],i=0;i<e.length;i++){var r=e[i],o=this.selectionContainer(),s=this.display(r,o);o.append(s);var a=r.title||r.text;a&&o.attr("title",a),n.StoreData(o[0],"data",r),t.push(o)}var l=this.$selection.find(".select2-selection__rendered");n.appendMany(l,t)}},i})),t.define("select2/selection/placeholder",["../utils"],(function(e){function t(e,t,n){this.placeholder=this.normalizePlaceholder(n.get("placeholder")),e.call(this,t,n)}return t.prototype.normalizePlaceholder=function(e,t){return"string"==typeof t&&(t={id:"",text:t}),t},t.prototype.createPlaceholder=function(e,t){var n=this.selectionContainer();return n.html(this.display(t)),n.addClass("select2-selection__placeholder").removeClass("select2-selection__choice"),n},t.prototype.update=function
(e,t){var n=1==t.length&&t[0].id!=this.placeholder.id;if(t.length>1||n)return e.call(this,t);this.clear();var i=this.createPlaceholder(this.placeholder);this.$selection.find(".select2-selection__rendered").append(i)},t})),t.define("select2/selection/allowClear",["jquery","../keys","../utils"],(function(e,t,n){function i(){}return i.prototype.bind=function(e,t,n){var i=this;e.call(this,t,n),null==this.placeholder&&this.options.get("debug")&&window.console&&console.error&&console.error("Select2: The `allowClear` option should be used in combination with the `placeholder` option."),this.$selection.on("mousedown",".select2-selection__clear",(function(e){i._handleClear(e)})),t.on("keypress",(function(e){i._handleKeyboardClear(e,t)}))},i.prototype._handleClear=function(e,t){if(!this.isDisabled()){var i=this.$selection.find(".select2-selection__clear");if(0!==i.length){t.stopPropagation();var r=n.GetData(i[0],"data"),o=this.$element.val();this.$element.val(this.placeholder.id);var s={data:r};
if(this.trigger("clear",s),s.prevented)this.$element.val(o);else{for(var a=0;a<r.length;a++)if(s={data:r[a]},this.trigger("unselect",s),s.prevented)return void this.$element.val(o);this.$element.trigger("input").trigger("change"),this.trigger("toggle",{})}}}},i.prototype._handleKeyboardClear=function(e,n,i){i.isOpen()||n.which!=t.DELETE&&n.which!=t.BACKSPACE||this._handleClear(n)},i.prototype.update=function(t,i){if(t.call(this,i),!(this.$selection.find(".select2-selection__placeholder").length>0||0===i.length)){var r=this.options.get("translations").get("removeAllItems"),o=e('<span class="select2-selection__clear" title="'+r()+'">&times;</span>');n.StoreData(o[0],"data",i),this.$selection.find(".select2-selection__rendered").prepend(o)}},i})),t.define("select2/selection/search",["jquery","../utils","../keys"],(function(e,t,n){function i(e,t,n){e.call(this,t,n)}return i.prototype.render=function(t){var n=e(
'<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" /></li>');this.$searchContainer=n,this.$search=n.find("input");var i=t.call(this);return this._transferTabIndex(),i},i.prototype.bind=function(e,i,r){var o=this,s=i.id+"-results";e.call(this,i,r),i.on("open",(function(){o.$search.attr("aria-controls",s),o.$search.trigger("focus")})),i.on("close",(function(){o.$search.val(""),o.$search.removeAttr("aria-controls"),o.$search.removeAttr("aria-activedescendant"),o.$search.trigger("focus")})),i.on("enable",(function(){o.$search.prop("disabled",!1),o._transferTabIndex()})),i.on("disable",(function(){o.$search.prop("disabled",!0)})),i.on("focus",(function(e){o.$search.trigger("focus")})),i.on("results:focus",(function(e){e.data._resultId?o.$search.attr("aria-activedescendant",e.data._resultId):o.$search.
removeAttr("aria-activedescendant")})),this.$selection.on("focusin",".select2-search--inline",(function(e){o.trigger("focus",e)})),this.$selection.on("focusout",".select2-search--inline",(function(e){o._handleBlur(e)})),this.$selection.on("keydown",".select2-search--inline",(function(e){if(e.stopPropagation(),o.trigger("keypress",e),o._keyUpPrevented=e.isDefaultPrevented(),e.which===n.BACKSPACE&&""===o.$search.val()){var i=o.$searchContainer.prev(".select2-selection__choice");if(i.length>0){var r=t.GetData(i[0],"data");o.searchRemoveChoice(r),e.preventDefault()}}})),this.$selection.on("click",".select2-search--inline",(function(e){o.$search.val()&&e.stopPropagation()}));var a=document.documentMode,l=a&&a<=11;this.$selection.on("input.searchcheck",".select2-search--inline",(function(e){l?o.$selection.off("input.search input.searchcheck"):o.$selection.off("keyup.search")})),this.$selection.on("keyup.search input.search",".select2-search--inline",(function(e){if(l&&"input"===e.type)o.
$selection.off("input.search input.searchcheck");else{var t=e.which;t!=n.SHIFT&&t!=n.CTRL&&t!=n.ALT&&t!=n.TAB&&o.handleSearch(e)}}))},i.prototype._transferTabIndex=function(e){this.$search.attr("tabindex",this.$selection.attr("tabindex")),this.$selection.attr("tabindex","-1")},i.prototype.createPlaceholder=function(e,t){this.$search.attr("placeholder",t.text)},i.prototype.update=function(e,t){var n=this.$search[0]==document.activeElement;this.$search.attr("placeholder",""),e.call(this,t),this.$selection.find(".select2-selection__rendered").append(this.$searchContainer),this.resizeSearch(),n&&this.$search.trigger("focus")},i.prototype.handleSearch=function(){if(this.resizeSearch(),!this._keyUpPrevented){var e=this.$search.val();this.trigger("query",{term:e})}this._keyUpPrevented=!1},i.prototype.searchRemoveChoice=function(e,t){this.trigger("unselect",{data:t}),this.$search.val(t.text),this.handleSearch()},i.prototype.resizeSearch=function(){this.$search.css("width","25px");var e="";""
!==this.$search.attr("placeholder")?e=this.$selection.find(".select2-selection__rendered").width():e=.75*(this.$search.val().length+1)+"em";this.$search.css("width",e)},i})),t.define("select2/selection/eventRelay",["jquery"],(function(e){function t(){}return t.prototype.bind=function(t,n,i){var r=this,o=["open","opening","close","closing","select","selecting","unselect","unselecting","clear","clearing"],s=["opening","closing","selecting","unselecting","clearing"];t.call(this,n,i),n.on("*",(function(t,n){if(-1!==e.inArray(t,o)){n=n||{};var i=e.Event("select2:"+t,{params:n});r.$element.trigger(i),-1!==e.inArray(t,s)&&(n.prevented=i.isDefaultPrevented())}}))},t})),t.define("select2/translation",["jquery","require"],(function(e,t){function n(e){this.dict=e||{}}return n.prototype.all=function(){return this.dict},n.prototype.get=function(e){return this.dict[e]},n.prototype.extend=function(t){this.dict=e.extend({},t.all(),this.dict)},n._cache={},n.loadPath=function(e){if(!(e in n._cache)){var
i=t(e);n._cache[e]=i}return new n(n._cache[e])},n})),t.define("select2/diacritics",[],(function(){return{"Ⓐ":"A","Ａ":"A","À":"A","Á":"A","Â":"A","Ầ":"A","Ấ":"A","Ẫ":"A","Ẩ":"A","Ã":"A","Ā":"A","Ă":"A","Ằ":"A","Ắ":"A","Ẵ":"A","Ẳ":"A","Ȧ":"A","Ǡ":"A","Ä":"A","Ǟ":"A","Ả":"A","Å":"A","Ǻ":"A","Ǎ":"A","Ȁ":"A","Ȃ":"A","Ạ":"A","Ậ":"A","Ặ":"A","Ḁ":"A","Ą":"A","Ⱥ":"A","Ɐ":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ⓑ":"B","Ｂ":"B","Ḃ":"B","Ḅ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ɓ":"B","Ⓒ":"C","Ｃ":"C","Ć":"C","Ĉ":"C","Ċ":"C","Č":"C","Ç":"C","Ḉ":"C","Ƈ":"C","Ȼ":"C","Ꜿ":"C","Ⓓ":"D","Ｄ":"D","Ḋ":"D","Ď":"D","Ḍ":"D","Ḑ":"D","Ḓ":"D","Ḏ":"D","Đ":"D","Ƌ":"D","Ɗ":"D","Ɖ":"D","Ꝺ":"D","Ǳ":"DZ","Ǆ":"DZ","ǲ":"Dz","ǅ":"Dz","Ⓔ":"E","Ｅ":"E","È":"E","É":"E","Ê":"E","Ề":"E","Ế":"E","Ễ":"E","Ể":"E","Ẽ":"E","Ē":"E","Ḕ":"E","Ḗ":"E","Ĕ":"E","Ė":
"E","Ë":"E","Ẻ":"E","Ě":"E","Ȅ":"E","Ȇ":"E","Ẹ":"E","Ệ":"E","Ȩ":"E","Ḝ":"E","Ę":"E","Ḙ":"E","Ḛ":"E","Ɛ":"E","Ǝ":"E","Ⓕ":"F","Ｆ":"F","Ḟ":"F","Ƒ":"F","Ꝼ":"F","Ⓖ":"G","Ｇ":"G","Ǵ":"G","Ĝ":"G","Ḡ":"G","Ğ":"G","Ġ":"G","Ǧ":"G","Ģ":"G","Ǥ":"G","Ɠ":"G","Ꞡ":"G","Ᵹ":"G","Ꝿ":"G","Ⓗ":"H","Ｈ":"H","Ĥ":"H","Ḣ":"H","Ḧ":"H","Ȟ":"H","Ḥ":"H","Ḩ":"H","Ḫ":"H","Ħ":"H","Ⱨ":"H","Ⱶ":"H","Ɥ":"H","Ⓘ":"I","Ｉ":"I","Ì":"I","Í":"I","Î":"I","Ĩ":"I","Ī":"I","Ĭ":"I","İ":"I","Ï":"I","Ḯ":"I","Ỉ":"I","Ǐ":"I","Ȉ":"I","Ȋ":"I","Ị":"I","Į":"I","Ḭ":"I","Ɨ":"I","Ⓙ":"J","Ｊ":"J","Ĵ":"J","Ɉ":"J","Ⓚ":"K","Ｋ":"K","Ḱ":"K","Ǩ":"K","Ḳ":"K","Ķ":"K","Ḵ":"K","Ƙ":"K","Ⱪ":"K","Ꝁ":"K","Ꝃ":"K","Ꝅ":"K","Ꞣ":"K","Ⓛ":"L","Ｌ":"L","Ŀ":"L","Ĺ":"L","Ľ":"L","Ḷ":"L","Ḹ":"L","Ļ":"L","Ḽ":"L","Ḻ":"L","Ł":"L","Ƚ":"L","Ɫ":"L","Ⱡ":"L","Ꝉ":"L","Ꝇ":"L","Ꞁ":"L","Ǉ":"LJ","ǈ":"Lj","Ⓜ":"M","Ｍ":"M","Ḿ":"M",
"Ṁ":"M","Ṃ":"M","Ɱ":"M","Ɯ":"M","Ⓝ":"N","Ｎ":"N","Ǹ":"N","Ń":"N","Ñ":"N","Ṅ":"N","Ň":"N","Ṇ":"N","Ņ":"N","Ṋ":"N","Ṉ":"N","Ƞ":"N","Ɲ":"N","Ꞑ":"N","Ꞥ":"N","Ǌ":"NJ","ǋ":"Nj","Ⓞ":"O","Ｏ":"O","Ò":"O","Ó":"O","Ô":"O","Ồ":"O","Ố":"O","Ỗ":"O","Ổ":"O","Õ":"O","Ṍ":"O","Ȭ":"O","Ṏ":"O","Ō":"O","Ṑ":"O","Ṓ":"O","Ŏ":"O","Ȯ":"O","Ȱ":"O","Ö":"O","Ȫ":"O","Ỏ":"O","Ő":"O","Ǒ":"O","Ȍ":"O","Ȏ":"O","Ơ":"O","Ờ":"O","Ớ":"O","Ỡ":"O","Ở":"O","Ợ":"O","Ọ":"O","Ộ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Ɔ":"O","Ɵ":"O","Ꝋ":"O","Ꝍ":"O","Œ":"OE","Ƣ":"OI","Ꝏ":"OO","Ȣ":"OU","Ⓟ":"P","Ｐ":"P","Ṕ":"P","Ṗ":"P","Ƥ":"P","Ᵽ":"P","Ꝑ":"P","Ꝓ":"P","Ꝕ":"P","Ⓠ":"Q","Ｑ":"Q","Ꝗ":"Q","Ꝙ":"Q","Ɋ":"Q","Ⓡ":"R","Ｒ":"R","Ŕ":"R","Ṙ":"R","Ř":"R","Ȑ":"R","Ȓ":"R","Ṛ":"R","Ṝ":"R","Ŗ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꝛ":"R","Ꞧ":"R","Ꞃ":"R","Ⓢ":"S","Ｓ":"S","ẞ":"S","Ś":"S","Ṥ":"S","Ŝ":"S","Ṡ":"S"
,"Š":"S","Ṧ":"S","Ṣ":"S","Ṩ":"S","Ș":"S","Ş":"S","Ȿ":"S","Ꞩ":"S","Ꞅ":"S","Ⓣ":"T","Ｔ":"T","Ṫ":"T","Ť":"T","Ṭ":"T","Ț":"T","Ţ":"T","Ṱ":"T","Ṯ":"T","Ŧ":"T","Ƭ":"T","Ʈ":"T","Ⱦ":"T","Ꞇ":"T","Ꜩ":"TZ","Ⓤ":"U","Ｕ":"U","Ù":"U","Ú":"U","Û":"U","Ũ":"U","Ṹ":"U","Ū":"U","Ṻ":"U","Ŭ":"U","Ü":"U","Ǜ":"U","Ǘ":"U","Ǖ":"U","Ǚ":"U","Ủ":"U","Ů":"U","Ű":"U","Ǔ":"U","Ȕ":"U","Ȗ":"U","Ư":"U","Ừ":"U","Ứ":"U","Ữ":"U","Ử":"U","Ự":"U","Ụ":"U","Ṳ":"U","Ų":"U","Ṷ":"U","Ṵ":"U","Ʉ":"U","Ⓥ":"V","Ｖ":"V","Ṽ":"V","Ṿ":"V","Ʋ":"V","Ꝟ":"V","Ʌ":"V","Ꝡ":"VY","Ⓦ":"W","Ｗ":"W","Ẁ":"W","Ẃ":"W","Ŵ":"W","Ẇ":"W","Ẅ":"W","Ẉ":"W","Ⱳ":"W","Ⓧ":"X","Ｘ":"X","Ẋ":"X","Ẍ":"X","Ⓨ":"Y","Ｙ":"Y","Ỳ":"Y","Ý":"Y","Ŷ":"Y","Ỹ":"Y","Ȳ":"Y","Ẏ":"Y","Ÿ":"Y","Ỷ":"Y","Ỵ":"Y","Ƴ":"Y","Ɏ":"Y","Ỿ":"Y","Ⓩ":"Z","Ｚ":"Z","Ź":"Z","Ẑ":"Z","Ż":"Z","Ž":"Z","Ẓ":"Z","Ẕ":"Z","Ƶ":"Z","Ȥ":"Z","Ɀ":"Z","Ⱬ":"Z",
"Ꝣ":"Z","ⓐ":"a","ａ":"a","ẚ":"a","à":"a","á":"a","â":"a","ầ":"a","ấ":"a","ẫ":"a","ẩ":"a","ã":"a","ā":"a","ă":"a","ằ":"a","ắ":"a","ẵ":"a","ẳ":"a","ȧ":"a","ǡ":"a","ä":"a","ǟ":"a","ả":"a","å":"a","ǻ":"a","ǎ":"a","ȁ":"a","ȃ":"a","ạ":"a","ậ":"a","ặ":"a","ḁ":"a","ą":"a","ⱥ":"a","ɐ":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ⓑ":"b","ｂ":"b","ḃ":"b","ḅ":"b","ḇ":"b","ƀ":"b","ƃ":"b","ɓ":"b","ⓒ":"c","ｃ":"c","ć":"c","ĉ":"c","ċ":"c","č":"c","ç":"c","ḉ":"c","ƈ":"c","ȼ":"c","ꜿ":"c","ↄ":"c","ⓓ":"d","ｄ":"d","ḋ":"d","ď":"d","ḍ":"d","ḑ":"d","ḓ":"d","ḏ":"d","đ":"d","ƌ":"d","ɖ":"d","ɗ":"d","ꝺ":"d","ǳ":"dz","ǆ":"dz","ⓔ":"e","ｅ":"e","è":"e","é":"e","ê":"e","ề":"e","ế":"e","ễ":"e","ể":"e","ẽ":"e","ē":"e","ḕ":"e","ḗ":"e","ĕ":"e","ė":"e","ë":"e","ẻ":"e","ě":"e","ȅ":"e","ȇ":"e","ẹ":"e","ệ":"e","ȩ":"e","ḝ":"e","ę":
"e","ḙ":"e","ḛ":"e","ɇ":"e","ɛ":"e","ǝ":"e","ⓕ":"f","ｆ":"f","ḟ":"f","ƒ":"f","ꝼ":"f","ⓖ":"g","ｇ":"g","ǵ":"g","ĝ":"g","ḡ":"g","ğ":"g","ġ":"g","ǧ":"g","ģ":"g","ǥ":"g","ɠ":"g","ꞡ":"g","ᵹ":"g","ꝿ":"g","ⓗ":"h","ｈ":"h","ĥ":"h","ḣ":"h","ḧ":"h","ȟ":"h","ḥ":"h","ḩ":"h","ḫ":"h","ẖ":"h","ħ":"h","ⱨ":"h","ⱶ":"h","ɥ":"h","ƕ":"hv","ⓘ":"i","ｉ":"i","ì":"i","í":"i","î":"i","ĩ":"i","ī":"i","ĭ":"i","ï":"i","ḯ":"i","ỉ":"i","ǐ":"i","ȉ":"i","ȋ":"i","ị":"i","į":"i","ḭ":"i","ɨ":"i","ı":"i","ⓙ":"j","ｊ":"j","ĵ":"j","ǰ":"j","ɉ":"j","ⓚ":"k","ｋ":"k","ḱ":"k","ǩ":"k","ḳ":"k","ķ":"k","ḵ":"k","ƙ":"k","ⱪ":"k","ꝁ":"k","ꝃ":"k","ꝅ":"k","ꞣ":"k","ⓛ":"l","ｌ":"l","ŀ":"l","ĺ":"l","ľ":"l","ḷ":"l","ḹ":"l","ļ":"l","ḽ":"l","ḻ":"l","ſ":"l","ł":"l","ƚ":"l","ɫ":"l","ⱡ":"l","ꝉ":"l","ꞁ":"l","ꝇ":"l","ǉ":"lj","ⓜ":"m","ｍ":"m","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ɯ":"m","ⓝ":"n","ｎ":"n",
"ǹ":"n","ń":"n","ñ":"n","ṅ":"n","ň":"n","ṇ":"n","ņ":"n","ṋ":"n","ṉ":"n","ƞ":"n","ɲ":"n","ŉ":"n","ꞑ":"n","ꞥ":"n","ǌ":"nj","ⓞ":"o","ｏ":"o","ò":"o","ó":"o","ô":"o","ồ":"o","ố":"o","ỗ":"o","ổ":"o","õ":"o","ṍ":"o","ȭ":"o","ṏ":"o","ō":"o","ṑ":"o","ṓ":"o","ŏ":"o","ȯ":"o","ȱ":"o","ö":"o","ȫ":"o","ỏ":"o","ő":"o","ǒ":"o","ȍ":"o","ȏ":"o","ơ":"o","ờ":"o","ớ":"o","ỡ":"o","ở":"o","ợ":"o","ọ":"o","ộ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","ɔ":"o","ꝋ":"o","ꝍ":"o","ɵ":"o","œ":"oe","ƣ":"oi","ȣ":"ou","ꝏ":"oo","ⓟ":"p","ｐ":"p","ṕ":"p","ṗ":"p","ƥ":"p","ᵽ":"p","ꝑ":"p","ꝓ":"p","ꝕ":"p","ⓠ":"q","ｑ":"q","ɋ":"q","ꝗ":"q","ꝙ":"q","ⓡ":"r","ｒ":"r","ŕ":"r","ṙ":"r","ř":"r","ȑ":"r","ȓ":"r","ṛ":"r","ṝ":"r","ŗ":"r","ṟ":"r","ɍ":"r","ɽ":"r","ꝛ":"r","ꞧ":"r","ꞃ":"r","ⓢ":"s","ｓ":"s","ß":"s","ś":"s","ṥ":"s","ŝ":"s","ṡ":"s","š":"s","ṧ":"s","ṣ":"s","ṩ":"s","ș":"s","ş":"s","ȿ"
:"s","ꞩ":"s","ꞅ":"s","ẛ":"s","ⓣ":"t","ｔ":"t","ṫ":"t","ẗ":"t","ť":"t","ṭ":"t","ț":"t","ţ":"t","ṱ":"t","ṯ":"t","ŧ":"t","ƭ":"t","ʈ":"t","ⱦ":"t","ꞇ":"t","ꜩ":"tz","ⓤ":"u","ｕ":"u","ù":"u","ú":"u","û":"u","ũ":"u","ṹ":"u","ū":"u","ṻ":"u","ŭ":"u","ü":"u","ǜ":"u","ǘ":"u","ǖ":"u","ǚ":"u","ủ":"u","ů":"u","ű":"u","ǔ":"u","ȕ":"u","ȗ":"u","ư":"u","ừ":"u","ứ":"u","ữ":"u","ử":"u","ự":"u","ụ":"u","ṳ":"u","ų":"u","ṷ":"u","ṵ":"u","ʉ":"u","ⓥ":"v","ｖ":"v","ṽ":"v","ṿ":"v","ʋ":"v","ꝟ":"v","ʌ":"v","ꝡ":"vy","ⓦ":"w","ｗ":"w","ẁ":"w","ẃ":"w","ŵ":"w","ẇ":"w","ẅ":"w","ẘ":"w","ẉ":"w","ⱳ":"w","ⓧ":"x","ｘ":"x","ẋ":"x","ẍ":"x","ⓨ":"y","ｙ":"y","ỳ":"y","ý":"y","ŷ":"y","ỹ":"y","ȳ":"y","ẏ":"y","ÿ":"y","ỷ":"y","ẙ":"y","ỵ":"y","ƴ":"y","ɏ":"y","ỿ":"y","ⓩ":"z","ｚ":"z","ź":"z","ẑ":"z","ż":"z","ž":"z","ẓ":"z","ẕ":"z","ƶ":"z","ȥ":"z","ɀ":"z","ⱬ":"z","ꝣ":"z","Ά":"Α",
"Έ":"Ε","Ή":"Η","Ί":"Ι","Ϊ":"Ι","Ό":"Ο","Ύ":"Υ","Ϋ":"Υ","Ώ":"Ω","ά":"α","έ":"ε","ή":"η","ί":"ι","ϊ":"ι","ΐ":"ι","ό":"ο","ύ":"υ","ϋ":"υ","ΰ":"υ","ώ":"ω","ς":"σ","’":"'"}})),t.define("select2/data/base",["../utils"],(function(e){function t(e,n){t.__super__.constructor.call(this)}return e.Extend(t,e.Observable),t.prototype.current=function(e){throw new Error("The `current` method must be defined in child classes.")},t.prototype.query=function(e,t){throw new Error("The `query` method must be defined in child classes.")},t.prototype.bind=function(e,t){},t.prototype.destroy=function(){},t.prototype.generateResultId=function(t,n){var i=t.id+"-result-";return i+=e.generateChars(4),null!=n.id?i+="-"+n.id.toString():i+="-"+e.generateChars(4),i},t})),t.define("select2/data/select",["./base","../utils","jquery"],(function(e,t,n){function i(e,t){this.$element=e,this.options=t,i.__super__.constructor.call(this)}return t.Extend(i,e),i.prototype.current=
function(e){var t=[],i=this;this.$element.find(":selected").each((function(){var e=n(this),r=i.item(e);t.push(r)})),e(t)},i.prototype.select=function(e){var t=this;if(e.selected=!0,n(e.element).is("option"))return e.element.selected=!0,void this.$element.trigger("input").trigger("change");if(this.$element.prop("multiple"))this.current((function(i){var r=[];(e=[e]).push.apply(e,i);for(var o=0;o<e.length;o++){var s=e[o].id;-1===n.inArray(s,r)&&r.push(s)}t.$element.val(r),t.$element.trigger("input").trigger("change")}));else{var i=e.id;this.$element.val(i),this.$element.trigger("input").trigger("change")}},i.prototype.unselect=function(e){var t=this;if(this.$element.prop("multiple")){if(e.selected=!1,n(e.element).is("option"))return e.element.selected=!1,void this.$element.trigger("input").trigger("change");this.current((function(i){for(var r=[],o=0;o<i.length;o++){var s=i[o].id;s!==e.id&&-1===n.inArray(s,r)&&r.push(s)}t.$element.val(r),t.$element.trigger("input").trigger("change")}))}},i
.prototype.bind=function(e,t){var n=this;this.container=e,e.on("select",(function(e){n.select(e.data)})),e.on("unselect",(function(e){n.unselect(e.data)}))},i.prototype.destroy=function(){this.$element.find("*").each((function(){t.RemoveData(this)}))},i.prototype.query=function(e,t){var i=[],r=this;this.$element.children().each((function(){var t=n(this);if(t.is("option")||t.is("optgroup")){var o=r.item(t),s=r.matches(e,o);null!==s&&i.push(s)}})),t({results:i})},i.prototype.addOptions=function(e){t.appendMany(this.$element,e)},i.prototype.option=function(e){var i;e.children?(i=document.createElement("optgroup")).label=e.text:void 0!==(i=document.createElement("option")).textContent?i.textContent=e.text:i.innerText=e.text,void 0!==e.id&&(i.value=e.id),e.disabled&&(i.disabled=!0),e.selected&&(i.selected=!0),e.title&&(i.title=e.title);var r=n(i),o=this._normalizeItem(e);return o.element=i,t.StoreData(i,"data",o),r},i.prototype.item=function(e){var i={};if(null!=(i=t.GetData(e[0],"data")))
return i;if(e.is("option"))i={id:e.val(),text:e.text(),disabled:e.prop("disabled"),selected:e.prop("selected"),title:e.prop("title")};else if(e.is("optgroup")){i={text:e.prop("label"),children:[],title:e.prop("title")};for(var r=e.children("option"),o=[],s=0;s<r.length;s++){var a=n(r[s]),l=this.item(a);o.push(l)}i.children=o}return(i=this._normalizeItem(i)).element=e[0],t.StoreData(e[0],"data",i),i},i.prototype._normalizeItem=function(e){e!==Object(e)&&(e={id:e,text:e});return null!=(e=n.extend({},{text:""},e)).id&&(e.id=e.id.toString()),null!=e.text&&(e.text=e.text.toString()),null==e._resultId&&e.id&&null!=this.container&&(e._resultId=this.generateResultId(this.container,e)),n.extend({},{selected:!1,disabled:!1},e)},i.prototype.matches=function(e,t){return this.options.get("matcher")(e,t)},i})),t.define("select2/data/array",["./select","../utils","jquery"],(function(e,t,n){function i(e,t){this._dataToConvert=t.get("data")||[],i.__super__.constructor.call(this,e,t)}return t.Extend(i,e
),i.prototype.bind=function(e,t){i.__super__.bind.call(this,e,t),this.addOptions(this.convertToOptions(this._dataToConvert))},i.prototype.select=function(e){var t=this.$element.find("option").filter((function(t,n){return n.value==e.id.toString()}));0===t.length&&(t=this.option(e),this.addOptions(t)),i.__super__.select.call(this,e)},i.prototype.convertToOptions=function(e){var i=this,r=this.$element.find("option"),o=r.map((function(){return i.item(n(this)).id})).get(),s=[];function a(e){return function(){return n(this).val()==e.id}}for(var l=0;l<e.length;l++){var c=this._normalizeItem(e[l]);if(n.inArray(c.id,o)>=0){var u=r.filter(a(c)),d=this.item(u),p=n.extend(!0,{},c,d),h=this.option(p);u.replaceWith(h)}else{var f=this.option(c);if(c.children){var g=this.convertToOptions(c.children);t.appendMany(f,g)}s.push(f)}}return s},i})),t.define("select2/data/ajax",["./array","../utils","jquery"],(function(e,t,n){function i(e,t){this.ajaxOptions=this._applyDefaults(t.get("ajax")),null!=this.
ajaxOptions.processResults&&(this.processResults=this.ajaxOptions.processResults),i.__super__.constructor.call(this,e,t)}return t.Extend(i,e),i.prototype._applyDefaults=function(e){var t={data:function(e){return n.extend({},e,{q:e.term})},transport:function(e,t,i){var r=n.ajax(e);return r.then(t),r.fail(i),r}};return n.extend({},t,e,!0)},i.prototype.processResults=function(e){return e},i.prototype.query=function(e,t){var i=this;null!=this._request&&(n.isFunction(this._request.abort)&&this._request.abort(),this._request=null);var r=n.extend({type:"GET"},this.ajaxOptions);function o(){var o=r.transport(r,(function(r){var o=i.processResults(r,e);i.options.get("debug")&&window.console&&console.error&&(o&&o.results&&n.isArray(o.results)||console.error("Select2: The AJAX results did not return an array in the `results` key of the response.")),t(o)}),(function(){(!("status"in o)||0!==o.status&&"0"!==o.status)&&i.trigger("results:message",{message:"errorLoading"})}));i._request=o}"function"==
typeof r.url&&(r.url=r.url.call(this.$element,e)),"function"==typeof r.data&&(r.data=r.data.call(this.$element,e)),this.ajaxOptions.delay&&null!=e.term?(this._queryTimeout&&window.clearTimeout(this._queryTimeout),this._queryTimeout=window.setTimeout(o,this.ajaxOptions.delay)):o()},i})),t.define("select2/data/tags",["jquery"],(function(e){function t(t,n,i){var r=i.get("tags"),o=i.get("createTag");void 0!==o&&(this.createTag=o);var s=i.get("insertTag");if(void 0!==s&&(this.insertTag=s),t.call(this,n,i),e.isArray(r))for(var a=0;a<r.length;a++){var l=r[a],c=this._normalizeItem(l),u=this.option(c);this.$element.append(u)}}return t.prototype.query=function(e,t,n){var i=this;this._removeOldTags(),null!=t.term&&null==t.page?e.call(this,t,(function e(r,o){for(var s=r.results,a=0;a<s.length;a++){var l=s[a],c=null!=l.children&&!e({results:l.children},!0);if((l.text||"").toUpperCase()===(t.term||"").toUpperCase()||c)return!o&&(r.data=s,void n(r))}if(o)return!0;var u=i.createTag(t);if(null!=u){var d
=i.option(u);d.attr("data-select2-tag",!0),i.addOptions([d]),i.insertTag(s,u)}r.results=s,n(r)})):e.call(this,t,n)},t.prototype.createTag=function(t,n){var i=e.trim(n.term);return""===i?null:{id:i,text:i}},t.prototype.insertTag=function(e,t,n){t.unshift(n)},t.prototype._removeOldTags=function(t){this.$element.find("option[data-select2-tag]").each((function(){this.selected||e(this).remove()}))},t})),t.define("select2/data/tokenizer",["jquery"],(function(e){function t(e,t,n){var i=n.get("tokenizer");void 0!==i&&(this.tokenizer=i),e.call(this,t,n)}return t.prototype.bind=function(e,t,n){e.call(this,t,n),this.$search=t.dropdown.$search||t.selection.$search||n.find(".select2-search__field")},t.prototype.query=function(t,n,i){var r=this;n.term=n.term||"";var o=this.tokenizer(n,this.options,(function(t){var n=r._normalizeItem(t);if(!r.$element.find("option").filter((function(){return e(this).val()===n.id})).length){var i=r.option(n);i.attr("data-select2-tag",!0),r._removeOldTags(),r.
addOptions([i])}!function(e){r.trigger("select",{data:e})}(n)}));o.term!==n.term&&(this.$search.length&&(this.$search.val(o.term),this.$search.trigger("focus")),n.term=o.term),t.call(this,n,i)},t.prototype.tokenizer=function(t,n,i,r){for(var o=i.get("tokenSeparators")||[],s=n.term,a=0,l=this.createTag||function(e){return{id:e.term,text:e.term}};a<s.length;){var c=s[a];if(-1!==e.inArray(c,o)){var u=s.substr(0,a),d=l(e.extend({},n,{term:u}));null!=d?(r(d),s=s.substr(a+1)||"",a=0):a++}else a++}return{term:s}},t})),t.define("select2/data/minimumInputLength",[],(function(){function e(e,t,n){this.minimumInputLength=n.get("minimumInputLength"),e.call(this,t,n)}return e.prototype.query=function(e,t,n){t.term=t.term||"",t.term.length<this.minimumInputLength?this.trigger("results:message",{message:"inputTooShort",args:{minimum:this.minimumInputLength,input:t.term,params:t}}):e.call(this,t,n)},e})),t.define("select2/data/maximumInputLength",[],(function(){function e(e,t,n){this.maximumInputLength
=n.get("maximumInputLength"),e.call(this,t,n)}return e.prototype.query=function(e,t,n){t.term=t.term||"",this.maximumInputLength>0&&t.term.length>this.maximumInputLength?this.trigger("results:message",{message:"inputTooLong",args:{maximum:this.maximumInputLength,input:t.term,params:t}}):e.call(this,t,n)},e})),t.define("select2/data/maximumSelectionLength",[],(function(){function e(e,t,n){this.maximumSelectionLength=n.get("maximumSelectionLength"),e.call(this,t,n)}return e.prototype.bind=function(e,t,n){var i=this;e.call(this,t,n),t.on("select",(function(){i._checkIfMaximumSelected()}))},e.prototype.query=function(e,t,n){var i=this;this._checkIfMaximumSelected((function(){e.call(i,t,n)}))},e.prototype._checkIfMaximumSelected=function(e,t){var n=this;this.current((function(e){var i=null!=e?e.length:0;n.maximumSelectionLength>0&&i>=n.maximumSelectionLength?n.trigger("results:message",{message:"maximumSelected",args:{maximum:n.maximumSelectionLength}}):t&&t()}))},e})),t.define(
"select2/dropdown",["jquery","./utils"],(function(e,t){function n(e,t){this.$element=e,this.options=t,n.__super__.constructor.call(this)}return t.Extend(n,t.Observable),n.prototype.render=function(){var t=e('<span class="select2-dropdown"><span class="select2-results"></span></span>');return t.attr("dir",this.options.get("dir")),this.$dropdown=t,t},n.prototype.bind=function(){},n.prototype.position=function(e,t){},n.prototype.destroy=function(){this.$dropdown.remove()},n})),t.define("select2/dropdown/search",["jquery","../utils"],(function(e,t){function n(){}return n.prototype.render=function(t){var n=t.call(this),i=e('<span class="select2-search select2-search--dropdown"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" /></span>');return this.$searchContainer=i,this.$search=i.find("input"),n.prepend(i),n},n.prototype.bind=function(t,n,i){var r=this,o=
n.id+"-results";t.call(this,n,i),this.$search.on("keydown",(function(e){r.trigger("keypress",e),r._keyUpPrevented=e.isDefaultPrevented()})),this.$search.on("input",(function(t){e(this).off("keyup")})),this.$search.on("keyup input",(function(e){r.handleSearch(e)})),n.on("open",(function(){r.$search.attr("tabindex",0),r.$search.attr("aria-controls",o),r.$search.trigger("focus"),window.setTimeout((function(){r.$search.trigger("focus")}),0)})),n.on("close",(function(){r.$search.attr("tabindex",-1),r.$search.removeAttr("aria-controls"),r.$search.removeAttr("aria-activedescendant"),r.$search.val(""),r.$search.trigger("blur")})),n.on("focus",(function(){n.isOpen()||r.$search.trigger("focus")})),n.on("results:all",(function(e){null!=e.query.term&&""!==e.query.term||(r.showSearch(e)?r.$searchContainer.removeClass("select2-search--hide"):r.$searchContainer.addClass("select2-search--hide"))})),n.on("results:focus",(function(e){e.data._resultId?r.$search.attr("aria-activedescendant",e.data.
_resultId):r.$search.removeAttr("aria-activedescendant")}))},n.prototype.handleSearch=function(e){if(!this._keyUpPrevented){var t=this.$search.val();this.trigger("query",{term:t})}this._keyUpPrevented=!1},n.prototype.showSearch=function(e,t){return!0},n})),t.define("select2/dropdown/hidePlaceholder",[],(function(){function e(e,t,n,i){this.placeholder=this.normalizePlaceholder(n.get("placeholder")),e.call(this,t,n,i)}return e.prototype.append=function(e,t){t.results=this.removePlaceholder(t.results),e.call(this,t)},e.prototype.normalizePlaceholder=function(e,t){return"string"==typeof t&&(t={id:"",text:t}),t},e.prototype.removePlaceholder=function(e,t){for(var n=t.slice(0),i=t.length-1;i>=0;i--){var r=t[i];this.placeholder.id===r.id&&n.splice(i,1)}return n},e})),t.define("select2/dropdown/infiniteScroll",["jquery"],(function(e){function t(e,t,n,i){this.lastParams={},e.call(this,t,n,i),this.$loadingMore=this.createLoadingMore(),this.loading=!1}return t.prototype.append=function(e,t){this.
$loadingMore.remove(),this.loading=!1,e.call(this,t),this.showLoadingMore(t)&&(this.$results.append(this.$loadingMore),this.loadMoreIfNeeded())},t.prototype.bind=function(e,t,n){var i=this;e.call(this,t,n),t.on("query",(function(e){i.lastParams=e,i.loading=!0})),t.on("query:append",(function(e){i.lastParams=e,i.loading=!0})),this.$results.on("scroll",this.loadMoreIfNeeded.bind(this))},t.prototype.loadMoreIfNeeded=function(){var t=e.contains(document.documentElement,this.$loadingMore[0]);!this.loading&&t&&(this.$results.offset().top+this.$results.outerHeight(!1)+50>=this.$loadingMore.offset().top+this.$loadingMore.outerHeight(!1)&&this.loadMore())},t.prototype.loadMore=function(){this.loading=!0;var t=e.extend({},{page:1},this.lastParams);t.page++,this.trigger("query:append",t)},t.prototype.showLoadingMore=function(e,t){return t.pagination&&t.pagination.more},t.prototype.createLoadingMore=function(){var t=e(
'<li class="select2-results__option select2-results__option--load-more"role="option" aria-disabled="true"></li>'),n=this.options.get("translations").get("loadingMore");return t.html(n(this.lastParams)),t},t})),t.define("select2/dropdown/attachBody",["jquery","../utils"],(function(e,t){function n(t,n,i){this.$dropdownParent=e(i.get("dropdownParent")||document.body),t.call(this,n,i)}return n.prototype.bind=function(e,t,n){var i=this;e.call(this,t,n),t.on("open",(function(){i._showDropdown(),i._attachPositioningHandler(t),i._bindContainerResultHandlers(t)})),t.on("close",(function(){i._hideDropdown(),i._detachPositioningHandler(t)})),this.$dropdownContainer.on("mousedown",(function(e){e.stopPropagation()}))},n.prototype.destroy=function(e){e.call(this),this.$dropdownContainer.remove()},n.prototype.position=function(e,t,n){t.attr("class",n.attr("class")),t.removeClass("select2"),t.addClass("select2-container--open"),t.css({position:"absolute",top:-999999}),this.$container=n},n.prototype.
render=function(t){var n=e("<span></span>"),i=t.call(this);return n.append(i),this.$dropdownContainer=n,n},n.prototype._hideDropdown=function(e){this.$dropdownContainer.detach()},n.prototype._bindContainerResultHandlers=function(e,t){if(!this._containerResultsHandlersBound){var n=this;t.on("results:all",(function(){n._positionDropdown(),n._resizeDropdown()})),t.on("results:append",(function(){n._positionDropdown(),n._resizeDropdown()})),t.on("results:message",(function(){n._positionDropdown(),n._resizeDropdown()})),t.on("select",(function(){n._positionDropdown(),n._resizeDropdown()})),t.on("unselect",(function(){n._positionDropdown(),n._resizeDropdown()})),this._containerResultsHandlersBound=!0}},n.prototype._attachPositioningHandler=function(n,i){var r=this,o="scroll.select2."+i.id,s="resize.select2."+i.id,a="orientationchange.select2."+i.id,l=this.$container.parents().filter(t.hasScroll);l.each((function(){t.StoreData(this,"select2-scroll-position",{x:e(this).scrollLeft(),y:e(this).
scrollTop()})})),l.on(o,(function(n){var i=t.GetData(this,"select2-scroll-position");e(this).scrollTop(i.y)})),e(window).on(o+" "+s+" "+a,(function(e){r._positionDropdown(),r._resizeDropdown()}))},n.prototype._detachPositioningHandler=function(n,i){var r="scroll.select2."+i.id,o="resize.select2."+i.id,s="orientationchange.select2."+i.id;this.$container.parents().filter(t.hasScroll).off(r),e(window).off(r+" "+o+" "+s)},n.prototype._positionDropdown=function(){var t=e(window),n=this.$dropdown.hasClass("select2-dropdown--above"),i=this.$dropdown.hasClass("select2-dropdown--below"),r=null,o=this.$container.offset();o.bottom=o.top+this.$container.outerHeight(!1);var s={height:this.$container.outerHeight(!1)};s.top=o.top,s.bottom=o.top+s.height;var a=this.$dropdown.outerHeight(!1),l=t.scrollTop(),c=t.scrollTop()+t.height(),u=l<o.top-a,d=c>o.bottom+a,p={left:o.left,top:s.bottom},h=this.$dropdownParent;"static"===h.css("position")&&(h=h.offsetParent());var f={top:0,left:0};(e.contains(document
.body,h[0])||h[0].isConnected)&&(f=h.offset()),p.top-=f.top,p.left-=f.left,n||i||(r="below"),d||!u||n?!u&&d&&n&&(r="below"):r="above",("above"==r||n&&"below"!==r)&&(p.top=s.top-f.top-a),null!=r&&(this.$dropdown.removeClass("select2-dropdown--below select2-dropdown--above").addClass("select2-dropdown--"+r),this.$container.removeClass("select2-container--below select2-container--above").addClass("select2-container--"+r)),this.$dropdownContainer.css(p)},n.prototype._resizeDropdown=function(){var e={width:this.$container.outerWidth(!1)+"px"};this.options.get("dropdownAutoWidth")&&(e.minWidth=e.width,e.position="relative",e.width="auto"),this.$dropdown.css(e)},n.prototype._showDropdown=function(e){this.$dropdownContainer.appendTo(this.$dropdownParent),this._positionDropdown(),this._resizeDropdown()},n})),t.define("select2/dropdown/minimumResultsForSearch",[],(function(){function e(t){for(var n=0,i=0;i<t.length;i++){var r=t[i];r.children?n+=e(r.children):n++}return n}function t(e,t,n,i){this
.minimumResultsForSearch=n.get("minimumResultsForSearch"),this.minimumResultsForSearch<0&&(this.minimumResultsForSearch=1/0),e.call(this,t,n,i)}return t.prototype.showSearch=function(t,n){return!(e(n.data.results)<this.minimumResultsForSearch)&&t.call(this,n)},t})),t.define("select2/dropdown/selectOnClose",["../utils"],(function(e){function t(){}return t.prototype.bind=function(e,t,n){var i=this;e.call(this,t,n),t.on("close",(function(e){i._handleSelectOnClose(e)}))},t.prototype._handleSelectOnClose=function(t,n){if(n&&null!=n.originalSelect2Event){var i=n.originalSelect2Event;if("select"===i._type||"unselect"===i._type)return}var r=this.getHighlightedResults();if(!(r.length<1)){var o=e.GetData(r[0],"data");null!=o.element&&o.element.selected||null==o.element&&o.selected||this.trigger("select",{data:o})}},t})),t.define("select2/dropdown/closeOnSelect",[],(function(){function e(){}return e.prototype.bind=function(e,t,n){var i=this;e.call(this,t,n),t.on("select",(function(e){i.
_selectTriggered(e)})),t.on("unselect",(function(e){i._selectTriggered(e)}))},e.prototype._selectTriggered=function(e,t){var n=t.originalEvent;n&&(n.ctrlKey||n.metaKey)||this.trigger("close",{originalEvent:n,originalSelect2Event:t})},e})),t.define("select2/i18n/en",[],(function(){return{errorLoading:function(){return"The results could not be loaded."},inputTooLong:function(e){var t=e.input.length-e.maximum,n="Please delete "+t+" character";return 1!=t&&(n+="s"),n},inputTooShort:function(e){return"Please enter "+(e.minimum-e.input.length)+" or more characters"},loadingMore:function(){return"Loading more results…"},maximumSelected:function(e){var t="You can only select "+e.maximum+" item";return 1!=e.maximum&&(t+="s"),t},noResults:function(){return"No results found"},searching:function(){return"Searching…"},removeAllItems:function(){return"Remove all items"}}})),t.define("select2/defaults",["jquery","require","./results","./selection/single","./selection/multiple",
"./selection/placeholder","./selection/allowClear","./selection/search","./selection/eventRelay","./utils","./translation","./diacritics","./data/select","./data/array","./data/ajax","./data/tags","./data/tokenizer","./data/minimumInputLength","./data/maximumInputLength","./data/maximumSelectionLength","./dropdown","./dropdown/search","./dropdown/hidePlaceholder","./dropdown/infiniteScroll","./dropdown/attachBody","./dropdown/minimumResultsForSearch","./dropdown/selectOnClose","./dropdown/closeOnSelect","./i18n/en"],(function(e,t,n,i,r,o,s,a,l,c,u,d,p,h,f,g,m,v,y,_,w,$,b,A,x,D,S,C,E){function O(){this.reset()}return O.prototype.apply=function(u){if(null==(u=e.extend(!0,{},this.defaults,u)).dataAdapter){if(null!=u.ajax?u.dataAdapter=f:null!=u.data?u.dataAdapter=h:u.dataAdapter=p,u.minimumInputLength>0&&(u.dataAdapter=c.Decorate(u.dataAdapter,v)),u.maximumInputLength>0&&(u.dataAdapter=c.Decorate(u.dataAdapter,y)),u.maximumSelectionLength>0&&(u.dataAdapter=c.Decorate(u.dataAdapter,_)),u.
tags&&(u.dataAdapter=c.Decorate(u.dataAdapter,g)),null==u.tokenSeparators&&null==u.tokenizer||(u.dataAdapter=c.Decorate(u.dataAdapter,m)),null!=u.query){var d=t(u.amdBase+"compat/query");u.dataAdapter=c.Decorate(u.dataAdapter,d)}if(null!=u.initSelection){var E=t(u.amdBase+"compat/initSelection");u.dataAdapter=c.Decorate(u.dataAdapter,E)}}if(null==u.resultsAdapter&&(u.resultsAdapter=n,null!=u.ajax&&(u.resultsAdapter=c.Decorate(u.resultsAdapter,A)),null!=u.placeholder&&(u.resultsAdapter=c.Decorate(u.resultsAdapter,b)),u.selectOnClose&&(u.resultsAdapter=c.Decorate(u.resultsAdapter,S))),null==u.dropdownAdapter){if(u.multiple)u.dropdownAdapter=w;else{var O=c.Decorate(w,$);u.dropdownAdapter=O}if(0!==u.minimumResultsForSearch&&(u.dropdownAdapter=c.Decorate(u.dropdownAdapter,D)),u.closeOnSelect&&(u.dropdownAdapter=c.Decorate(u.dropdownAdapter,C)),null!=u.dropdownCssClass||null!=u.dropdownCss||null!=u.adaptDropdownCssClass){var T=t(u.amdBase+"compat/dropdownCss");u.dropdownAdapter=c.Decorate(u.
dropdownAdapter,T)}u.dropdownAdapter=c.Decorate(u.dropdownAdapter,x)}if(null==u.selectionAdapter){if(u.multiple?u.selectionAdapter=r:u.selectionAdapter=i,null!=u.placeholder&&(u.selectionAdapter=c.Decorate(u.selectionAdapter,o)),u.allowClear&&(u.selectionAdapter=c.Decorate(u.selectionAdapter,s)),u.multiple&&(u.selectionAdapter=c.Decorate(u.selectionAdapter,a)),null!=u.containerCssClass||null!=u.containerCss||null!=u.adaptContainerCssClass){var q=t(u.amdBase+"compat/containerCss");u.selectionAdapter=c.Decorate(u.selectionAdapter,q)}u.selectionAdapter=c.Decorate(u.selectionAdapter,l)}u.language=this._resolveLanguage(u.language),u.language.push("en");for(var j=[],L=0;L<u.language.length;L++){var I=u.language[L];-1===j.indexOf(I)&&j.push(I)}return u.language=j,u.translations=this._processTranslations(u.language,u.debug),u},O.prototype.reset=function(){function t(e){return e.replace(/[^\u0000-\u007E]/g,(function(e){return d[e]||e}))}this.defaults={amdBase:"./",amdLanguageBase:"./i18n/",
closeOnSelect:!0,debug:!1,dropdownAutoWidth:!1,escapeMarkup:c.escapeMarkup,language:{},matcher:function n(i,r){if(""===e.trim(i.term))return r;if(r.children&&r.children.length>0){for(var o=e.extend(!0,{},r),s=r.children.length-1;s>=0;s--){null==n(i,r.children[s])&&o.children.splice(s,1)}return o.children.length>0?o:n(i,o)}var a=t(r.text).toUpperCase(),l=t(i.term).toUpperCase();return a.indexOf(l)>-1?r:null},minimumInputLength:0,maximumInputLength:0,maximumSelectionLength:0,minimumResultsForSearch:0,selectOnClose:!1,scrollAfterSelect:!1,sorter:function(e){return e},templateResult:function(e){return e.text},templateSelection:function(e){return e.text},theme:"default",width:"resolve"}},O.prototype.applyFromElement=function(e,t){var n=e.language,i=this.defaults.language,r=t.prop("lang"),o=t.closest("[lang]").prop("lang"),s=Array.prototype.concat.call(this._resolveLanguage(r),this._resolveLanguage(n),this._resolveLanguage(i),this._resolveLanguage(o));return e.language=s,e},O.prototype.
_resolveLanguage=function(t){if(!t)return[];if(e.isEmptyObject(t))return[];if(e.isPlainObject(t))return[t];var n;n=e.isArray(t)?t:[t];for(var i=[],r=0;r<n.length;r++)if(i.push(n[r]),"string"==typeof n[r]&&n[r].indexOf("-")>0){var o=n[r].split("-")[0];i.push(o)}return i},O.prototype._processTranslations=function(t,n){for(var i=new u,r=0;r<t.length;r++){var o=new u,s=t[r];if("string"==typeof s)try{o=u.loadPath(s)}catch(e){try{s=this.defaults.amdLanguageBase+s,o=u.loadPath(s)}catch(e){n&&window.console&&console.warn&&console.warn('Select2: The language file for "'+s+'" could not be automatically loaded. A fallback will be used instead.')}}else o=e.isPlainObject(s)?new u(s):s;i.extend(o)}return i},O.prototype.set=function(t,n){var i={};i[e.camelCase(t)]=n;var r=c._convertData(i);e.extend(!0,this.defaults,r)},new O})),t.define("select2/options",["require","jquery","./defaults","./utils"],(function(e,t,n,i){function r(t,r){if(this.options=t,null!=r&&this.fromElement(r),null!=r&&(this.options
=n.applyFromElement(this.options,r)),this.options=n.apply(this.options),r&&r.is("input")){var o=e(this.get("amdBase")+"compat/inputData");this.options.dataAdapter=i.Decorate(this.options.dataAdapter,o)}}return r.prototype.fromElement=function(e){var n=["select2"];null==this.options.multiple&&(this.options.multiple=e.prop("multiple")),null==this.options.disabled&&(this.options.disabled=e.prop("disabled")),null==this.options.dir&&(e.prop("dir")?this.options.dir=e.prop("dir"):e.closest("[dir]").prop("dir")?this.options.dir=e.closest("[dir]").prop("dir"):this.options.dir="ltr"),e.prop("disabled",this.options.disabled),e.prop("multiple",this.options.multiple),i.GetData(e[0],"select2Tags")&&(this.options.debug&&window.console&&console.warn&&console.warn('Select2: The `data-select2-tags` attribute has been changed to use the `data-data` and `data-tags="true"` attributes and will be removed in future versions of Select2.'),i.StoreData(e[0],"data",i.GetData(e[0],"select2Tags")),i.StoreData(e[0]
,"tags",!0)),i.GetData(e[0],"ajaxUrl")&&(this.options.debug&&window.console&&console.warn&&console.warn("Select2: The `data-ajax-url` attribute has been changed to `data-ajax--url` and support for the old attribute will be removed in future versions of Select2."),e.attr("ajax--url",i.GetData(e[0],"ajaxUrl")),i.StoreData(e[0],"ajax-Url",i.GetData(e[0],"ajaxUrl")));var r={};function o(e,t){return t.toUpperCase()}for(var s=0;s<e[0].attributes.length;s++){var a=e[0].attributes[s].name,l="data-";if(a.substr(0,l.length)==l){var c=a.substring(l.length),u=i.GetData(e[0],c);r[c.replace(/-([a-z])/g,o)]=u}}t.fn.jquery&&"1."==t.fn.jquery.substr(0,2)&&e[0].dataset&&(r=t.extend(!0,{},e[0].dataset,r));var d=t.extend(!0,{},i.GetData(e[0]),r);for(var p in d=i._convertData(d))t.inArray(p,n)>-1||(t.isPlainObject(this.options[p])?t.extend(this.options[p],d[p]):this.options[p]=d[p]);return this},r.prototype.get=function(e){return this.options[e]},r.prototype.set=function(e,t){this.options[e]=t},r})),t.
define("select2/core",["jquery","./options","./utils","./keys"],(function(e,t,n,i){var r=function(e,i){null!=n.GetData(e[0],"select2")&&n.GetData(e[0],"select2").destroy(),this.$element=e,this.id=this._generateId(e),i=i||{},this.options=new t(i,e),r.__super__.constructor.call(this);var o=e.attr("tabindex")||0;n.StoreData(e[0],"old-tabindex",o),e.attr("tabindex","-1");var s=this.options.get("dataAdapter");this.dataAdapter=new s(e,this.options);var a=this.render();this._placeContainer(a);var l=this.options.get("selectionAdapter");this.selection=new l(e,this.options),this.$selection=this.selection.render(),this.selection.position(this.$selection,a);var c=this.options.get("dropdownAdapter");this.dropdown=new c(e,this.options),this.$dropdown=this.dropdown.render(),this.dropdown.position(this.$dropdown,a);var u=this.options.get("resultsAdapter");this.results=new u(e,this.options,this.dataAdapter),this.$results=this.results.render(),this.results.position(this.$results,this.$dropdown);var d=
this;this._bindAdapters(),this._registerDomEvents(),this._registerDataEvents(),this._registerSelectionEvents(),this._registerDropdownEvents(),this._registerResultsEvents(),this._registerEvents(),this.dataAdapter.current((function(e){d.trigger("selection:update",{data:e})})),e.addClass("select2-hidden-accessible"),e.attr("aria-hidden","true"),this._syncAttributes(),n.StoreData(e[0],"select2",this),e.data("select2",this)};return n.Extend(r,n.Observable),r.prototype._generateId=function(e){return"select2-"+(null!=e.attr("id")?e.attr("id"):null!=e.attr("name")?e.attr("name")+"-"+n.generateChars(2):n.generateChars(4)).replace(/(:|\.|\[|\]|,)/g,"")},r.prototype._placeContainer=function(e){e.insertAfter(this.$element);var t=this._resolveWidth(this.$element,this.options.get("width"));null!=t&&e.css("width",t)},r.prototype._resolveWidth=function(e,t){var n=/^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i;if("resolve"==t){var i=this._resolveWidth(e,"style");return null!=i?i:this.
_resolveWidth(e,"element")}if("element"==t){var r=e.outerWidth(!1);return r<=0?"auto":r+"px"}if("style"==t){var o=e.attr("style");if("string"!=typeof o)return null;for(var s=o.split(";"),a=0,l=s.length;a<l;a+=1){var c=s[a].replace(/\s/g,"").match(n);if(null!==c&&c.length>=1)return c[1]}return null}return"computedstyle"==t?window.getComputedStyle(e[0]).width:t},r.prototype._bindAdapters=function(){this.dataAdapter.bind(this,this.$container),this.selection.bind(this,this.$container),this.dropdown.bind(this,this.$container),this.results.bind(this,this.$container)},r.prototype._registerDomEvents=function(){var e=this;this.$element.on("change.select2",(function(){e.dataAdapter.current((function(t){e.trigger("selection:update",{data:t})}))})),this.$element.on("focus.select2",(function(t){e.trigger("focus",t)})),this._syncA=n.bind(this._syncAttributes,this),this._syncS=n.bind(this._syncSubtree,this),this.$element[0].attachEvent&&this.$element[0].attachEvent("onpropertychange",this._syncA);var
t=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver;null!=t?(this._observer=new t((function(t){e._syncA(),e._syncS(null,t)})),this._observer.observe(this.$element[0],{attributes:!0,childList:!0,subtree:!1})):this.$element[0].addEventListener&&(this.$element[0].addEventListener("DOMAttrModified",e._syncA,!1),this.$element[0].addEventListener("DOMNodeInserted",e._syncS,!1),this.$element[0].addEventListener("DOMNodeRemoved",e._syncS,!1))},r.prototype._registerDataEvents=function(){var e=this;this.dataAdapter.on("*",(function(t,n){e.trigger(t,n)}))},r.prototype._registerSelectionEvents=function(){var t=this,n=["toggle","focus"];this.selection.on("toggle",(function(){t.toggleDropdown()})),this.selection.on("focus",(function(e){t.focus(e)})),this.selection.on("*",(function(i,r){-1===e.inArray(i,n)&&t.trigger(i,r)}))},r.prototype._registerDropdownEvents=function(){var e=this;this.dropdown.on("*",(function(t,n){e.trigger(t,n)}))},r.prototype.
_registerResultsEvents=function(){var e=this;this.results.on("*",(function(t,n){e.trigger(t,n)}))},r.prototype._registerEvents=function(){var e=this;this.on("open",(function(){e.$container.addClass("select2-container--open")})),this.on("close",(function(){e.$container.removeClass("select2-container--open")})),this.on("enable",(function(){e.$container.removeClass("select2-container--disabled")})),this.on("disable",(function(){e.$container.addClass("select2-container--disabled")})),this.on("blur",(function(){e.$container.removeClass("select2-container--focus")})),this.on("query",(function(t){e.isOpen()||e.trigger("open",{}),this.dataAdapter.query(t,(function(n){e.trigger("results:all",{data:n,query:t})}))})),this.on("query:append",(function(t){this.dataAdapter.query(t,(function(n){e.trigger("results:append",{data:n,query:t})}))})),this.on("keypress",(function(t){var n=t.which;e.isOpen()?n===i.ESC||n===i.TAB||n===i.UP&&t.altKey?(e.close(t),t.preventDefault()):n===i.ENTER?(e.trigger(
"results:select",{}),t.preventDefault()):n===i.SPACE&&t.ctrlKey?(e.trigger("results:toggle",{}),t.preventDefault()):n===i.UP?(e.trigger("results:previous",{}),t.preventDefault()):n===i.DOWN&&(e.trigger("results:next",{}),t.preventDefault()):(n===i.ENTER||n===i.SPACE||n===i.DOWN&&t.altKey)&&(e.open(),t.preventDefault())}))},r.prototype._syncAttributes=function(){this.options.set("disabled",this.$element.prop("disabled")),this.isDisabled()?(this.isOpen()&&this.close(),this.trigger("disable",{})):this.trigger("enable",{})},r.prototype._isChangeMutation=function(t,n){var i=!1,r=this;if(!t||!t.target||"OPTION"===t.target.nodeName||"OPTGROUP"===t.target.nodeName){if(n)if(n.addedNodes&&n.addedNodes.length>0)for(var o=0;o<n.addedNodes.length;o++){n.addedNodes[o].selected&&(i=!0)}else n.removedNodes&&n.removedNodes.length>0?i=!0:e.isArray(n)&&e.each(n,(function(e,t){if(r._isChangeMutation(e,t))return i=!0,!1}));else i=!0;return i}},r.prototype._syncSubtree=function(e,t){var n=this.
_isChangeMutation(e,t),i=this;n&&this.dataAdapter.current((function(e){i.trigger("selection:update",{data:e})}))},r.prototype.trigger=function(e,t){var n=r.__super__.trigger,i={open:"opening",close:"closing",select:"selecting",unselect:"unselecting",clear:"clearing"};if(void 0===t&&(t={}),e in i){var o=i[e],s={prevented:!1,name:e,args:t};if(n.call(this,o,s),s.prevented)return void(t.prevented=!0)}n.call(this,e,t)},r.prototype.toggleDropdown=function(){this.isDisabled()||(this.isOpen()?this.close():this.open())},r.prototype.open=function(){this.isOpen()||this.isDisabled()||this.trigger("query",{})},r.prototype.close=function(e){this.isOpen()&&this.trigger("close",{originalEvent:e})},r.prototype.isEnabled=function(){return!this.isDisabled()},r.prototype.isDisabled=function(){return this.options.get("disabled")},r.prototype.isOpen=function(){return this.$container.hasClass("select2-container--open")},r.prototype.hasFocus=function(){return this.$container.hasClass(
"select2-container--focus")},r.prototype.focus=function(e){this.hasFocus()||(this.$container.addClass("select2-container--focus"),this.trigger("focus",{}))},r.prototype.enable=function(e){this.options.get("debug")&&window.console&&console.warn&&console.warn('Select2: The `select2("enable")` method has been deprecated and will be removed in later Select2 versions. Use $element.prop("disabled") instead.'),null!=e&&0!==e.length||(e=[!0]);var t=!e[0];this.$element.prop("disabled",t)},r.prototype.data=function(){this.options.get("debug")&&arguments.length>0&&window.console&&console.warn&&console.warn('Select2: Data can no longer be set using `select2("data")`. You should consider setting the value instead using `$element.val()`.');var e=[];return this.dataAdapter.current((function(t){e=t})),e},r.prototype.val=function(t){if(this.options.get("debug")&&window.console&&console.warn&&console.warn(
'Select2: The `select2("val")` method has been deprecated and will be removed in later Select2 versions. Use $element.val() instead.'),null==t||0===t.length)return this.$element.val();var n=t[0];e.isArray(n)&&(n=e.map(n,(function(e){return e.toString()}))),this.$element.val(n).trigger("input").trigger("change")},r.prototype.destroy=function(){this.$container.remove(),this.$element[0].detachEvent&&this.$element[0].detachEvent("onpropertychange",this._syncA),null!=this._observer?(this._observer.disconnect(),this._observer=null):this.$element[0].removeEventListener&&(this.$element[0].removeEventListener("DOMAttrModified",this._syncA,!1),this.$element[0].removeEventListener("DOMNodeInserted",this._syncS,!1),this.$element[0].removeEventListener("DOMNodeRemoved",this._syncS,!1)),this._syncA=null,this._syncS=null,this.$element.off(".select2"),this.$element.attr("tabindex",n.GetData(this.$element[0],"old-tabindex")),this.$element.removeClass("select2-hidden-accessible"),this.$element.attr(
"aria-hidden","false"),n.RemoveData(this.$element[0]),this.$element.removeData("select2"),this.dataAdapter.destroy(),this.selection.destroy(),this.dropdown.destroy(),this.results.destroy(),this.dataAdapter=null,this.selection=null,this.dropdown=null,this.results=null},r.prototype.render=function(){var t=e('<span class="select2 select2-container"><span class="selection"></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>');return t.attr("dir",this.options.get("dir")),this.$container=t,this.$container.addClass("select2-container--"+this.options.get("theme")),n.StoreData(t[0],"element",this.$element),t},r})),t.define("select2/compat/utils",["jquery"],(function(e){return{syncCssClasses:function(t,n,i){var r,o,s=[];(r=e.trim(t.attr("class")))&&e((r=""+r).split(/\s+/)).each((function(){0===this.indexOf("select2-")&&s.push(this)})),(r=e.trim(n.attr("class")))&&e((r=""+r).split(/\s+/)).each((function(){0!==this.indexOf("select2-")&&null!=(o=i(this))&&s.push(o)})),t.attr(
"class",s.join(" "))}}})),t.define("select2/compat/containerCss",["jquery","./utils"],(function(e,t){function n(e){return null}function i(){}return i.prototype.render=function(i){var r=i.call(this),o=this.options.get("containerCssClass")||"";e.isFunction(o)&&(o=o(this.$element));var s=this.options.get("adaptContainerCssClass");if(s=s||n,-1!==o.indexOf(":all:")){o=o.replace(":all:","");var a=s;s=function(e){var t=a(e);return null!=t?t+" "+e:e}}var l=this.options.get("containerCss")||{};return e.isFunction(l)&&(l=l(this.$element)),t.syncCssClasses(r,this.$element,s),r.css(l),r.addClass(o),r},i})),t.define("select2/compat/dropdownCss",["jquery","./utils"],(function(e,t){function n(e){return null}function i(){}return i.prototype.render=function(i){var r=i.call(this),o=this.options.get("dropdownCssClass")||"";e.isFunction(o)&&(o=o(this.$element));var s=this.options.get("adaptDropdownCssClass");if(s=s||n,-1!==o.indexOf(":all:")){o=o.replace(":all:","");var a=s;s=function(e){var t=a(e);return null
!=t?t+" "+e:e}}var l=this.options.get("dropdownCss")||{};return e.isFunction(l)&&(l=l(this.$element)),t.syncCssClasses(r,this.$element,s),r.css(l),r.addClass(o),r},i})),t.define("select2/compat/initSelection",["jquery"],(function(e){function t(e,t,n){n.get("debug")&&window.console&&console.warn&&console.warn("Select2: The `initSelection` option has been deprecated in favor of a custom data adapter that overrides the `current` method. This method is now called multiple times instead of a single time when the instance is initialized. Support will be removed for the `initSelection` option in future versions of Select2"),this.initSelection=n.get("initSelection"),this._isInitialized=!1,e.call(this,t,n)}return t.prototype.current=function(t,n){var i=this;this._isInitialized?t.call(this,n):this.initSelection.call(null,this.$element,(function(t){i._isInitialized=!0,e.isArray(t)||(t=[t]),n(t)}))},t})),t.define("select2/compat/inputData",["jquery","../utils"],(function(e,t){function n(e,t,n){
this._currentData=[],this._valueSeparator=n.get("valueSeparator")||",","hidden"===t.prop("type")&&n.get("debug")&&console&&console.warn&&console.warn("Select2: Using a hidden input with Select2 is no longer supported and may stop working in the future. It is recommended to use a `<select>` element instead."),e.call(this,t,n)}return n.prototype.current=function(t,n){function i(t,n){var r=[];return t.selected||-1!==e.inArray(t.id,n)?(t.selected=!0,r.push(t)):t.selected=!1,t.children&&r.push.apply(r,i(t.children,n)),r}for(var r=[],o=0;o<this._currentData.length;o++){var s=this._currentData[o];r.push.apply(r,i(s,this.$element.val().split(this._valueSeparator)))}n(r)},n.prototype.select=function(t,n){if(this.options.get("multiple")){var i=this.$element.val();i+=this._valueSeparator+n.id,this.$element.val(i),this.$element.trigger("input").trigger("change")}else this.current((function(t){e.map(t,(function(e){e.selected=!1}))})),this.$element.val(n.id),this.$element.trigger("input").trigger(
"change")},n.prototype.unselect=function(e,t){var n=this;t.selected=!1,this.current((function(e){for(var i=[],r=0;r<e.length;r++){var o=e[r];t.id!=o.id&&i.push(o.id)}n.$element.val(i.join(n._valueSeparator)),n.$element.trigger("input").trigger("change")}))},n.prototype.query=function(e,t,n){for(var i=[],r=0;r<this._currentData.length;r++){var o=this._currentData[r],s=this.matches(t,o);null!==s&&i.push(s)}n({results:i})},n.prototype.addOptions=function(n,i){var r=e.map(i,(function(e){return t.GetData(e[0],"data")}));this._currentData.push.apply(this._currentData,r)},n})),t.define("select2/compat/matcher",["jquery"],(function(e){return function(t){return function(n,i){var r=e.extend(!0,{},i);if(null==n.term||""===e.trim(n.term))return r;if(i.children){for(var o=i.children.length-1;o>=0;o--){var s=i.children[o];t(n.term,s.text,s)||r.children.splice(o,1)}if(r.children.length>0)return r}return t(n.term,i.text,i)?r:null}}})),t.define("select2/compat/query",[],(function(){function e(e,t,n){n.
get("debug")&&window.console&&console.warn&&console.warn("Select2: The `query` option has been deprecated in favor of a custom data adapter that overrides the `query` method. Support will be removed for the `query` option in future versions of Select2."),e.call(this,t,n)}return e.prototype.query=function(e,t,n){t.callback=n,this.options.get("query").call(null,t)},e})),t.define("select2/dropdown/attachContainer",[],(function(){function e(e,t,n){e.call(this,t,n)}return e.prototype.position=function(e,t,n){n.find(".dropdown-wrapper").append(t),t.addClass("select2-dropdown--below"),n.addClass("select2-container--below")},e})),t.define("select2/dropdown/stopPropagation",[],(function(){function e(){}return e.prototype.bind=function(e,t,n){e.call(this,t,n);this.$dropdown.on(["blur","change","click","dblclick","focus","focusin","focusout","input","keydown","keyup","keypress","mousedown","mouseenter","mouseleave","mousemove","mouseover","mouseup","search","touchend","touchstart"].join(" "),(
function(e){e.stopPropagation()}))},e})),t.define("select2/selection/stopPropagation",[],(function(){function e(){}return e.prototype.bind=function(e,t,n){e.call(this,t,n);this.$selection.on(["blur","change","click","dblclick","focus","focusin","focusout","input","keydown","keyup","keypress","mousedown","mouseenter","mouseleave","mousemove","mouseover","mouseup","search","touchend","touchstart"].join(" "),(function(e){e.stopPropagation()}))},e})),n=function(e){var t,n,i=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],r="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],o=Array.prototype.slice;if(e.event.fixHooks)for(var s=i.length;s;)e.event.fixHooks[i[--s]]=e.event.mouseHooks;var a=e.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var t=r.length;t;)this.addEventListener(r[--t],l,!1);else this.onmousewheel=l;e.data(this,"mousewheel-line-height",a.getLineHeight(this)),e.
data(this,"mousewheel-page-height",a.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var t=r.length;t;)this.removeEventListener(r[--t],l,!1);else this.onmousewheel=null;e.removeData(this,"mousewheel-line-height"),e.removeData(this,"mousewheel-page-height")},getLineHeight:function(t){var n=e(t),i=n["offsetParent"in e.fn?"offsetParent":"parent"]();return i.length||(i=e("body")),parseInt(i.css("fontSize"),10)||parseInt(n.css("fontSize"),10)||16},getPageHeight:function(t){return e(t).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};function l(i){var r=i||window.event,s=o.call(arguments,1),l=0,d=0,p=0,h=0,f=0,g=0;if((i=e.event.fix(r)).type="mousewheel","detail"in r&&(p=-1*r.detail),"wheelDelta"in r&&(p=r.wheelDelta),"wheelDeltaY"in r&&(p=r.wheelDeltaY),"wheelDeltaX"in r&&(d=-1*r.wheelDeltaX),"axis"in r&&r.axis===r.HORIZONTAL_AXIS&&(d=-1*p,p=0),l=0===p?d:p,"deltaY"in r&&(l=p=-1*r.deltaY),"deltaX"in r&&(d=r.deltaX,0===p&&(l=-1*d)),0!==p||0!==d){if(1===r.
deltaMode){var m=e.data(this,"mousewheel-line-height");l*=m,p*=m,d*=m}else if(2===r.deltaMode){var v=e.data(this,"mousewheel-page-height");l*=v,p*=v,d*=v}if(h=Math.max(Math.abs(p),Math.abs(d)),(!n||h<n)&&(n=h,u(r,h)&&(n/=40)),u(r,h)&&(l/=40,d/=40,p/=40),l=Math[l>=1?"floor":"ceil"](l/n),d=Math[d>=1?"floor":"ceil"](d/n),p=Math[p>=1?"floor":"ceil"](p/n),a.settings.normalizeOffset&&this.getBoundingClientRect){var y=this.getBoundingClientRect();f=i.clientX-y.left,g=i.clientY-y.top}return i.deltaX=d,i.deltaY=p,i.deltaFactor=n,i.offsetX=f,i.offsetY=g,i.deltaMode=0,s.unshift(i,l,d,p),t&&clearTimeout(t),t=setTimeout(c,200),(e.event.dispatch||e.event.handle).apply(this,s)}}function c(){n=null}function u(e,t){return a.settings.adjustOldDeltas&&"mousewheel"===e.type&&t%120==0}e.fn.extend({mousewheel:function(e){return e?this.bind("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.unbind("mousewheel",e)}})},"function"==typeof t.define&&t.define.amd?t.define(
"jquery-mousewheel",["jquery"],n):"object"==typeof exports?module.exports=n:n(e),t.define("jquery.select2",["jquery","jquery-mousewheel","./select2/core","./select2/defaults","./select2/utils"],(function(e,t,n,i,r){if(null==e.fn.select2){var o=["open","close","destroy"];e.fn.select2=function(t){if("object"==typeof(t=t||{}))return this.each((function(){var i=e.extend(!0,{},t);new n(e(this),i)})),this;if("string"==typeof t){var i,s=Array.prototype.slice.call(arguments,1);return this.each((function(){var e=r.GetData(this,"select2");null==e&&window.console&&console.error&&console.error("The select2('"+t+"') method was called on an element that is not using Select2."),i=e[t].apply(e,s)})),e.inArray(t,o)>-1?this:i}throw new Error("Invalid arguments for Select2: "+t)}}return null==e.fn.select2.defaults&&(e.fn.select2.defaults=i),n})),{define:t.define,require:t.require}}(),n=t.require("jquery.select2");return e.fn.select2.amd=t,n}));(function(factory){if(typeof define==="function"&&define.amd)
{define(["jquery"],factory);}else if(typeof exports==="object"){module.exports=factory;}else{factory(jQuery);}})(function($){var toFix=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],toBind=("onwheel"in window.document||window.document.documentMode>=9)?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],slice=Array.prototype.slice,nullLowestDeltaTimeout,lowestDelta;if($.event.fixHooks){for(var i=toFix.length;i;){$.event.fixHooks[toFix[--i]]=$.event.mouseHooks;}}var special=$.event.special.mousewheel={version:"3.2.0",setup:function(){if(this.addEventListener){for(var i=toBind.length;i;){this.addEventListener(toBind[--i],handler,false);}}else{this.onmousewheel=handler;}$.data(this,"mousewheel-line-height",special.getLineHeight(this));$.data(this,"mousewheel-page-height",special.getPageHeight(this));},teardown:function(){if(this.removeEventListener){for(var i=toBind.length;i;){this.removeEventListener(toBind[--i],handler,false);}}else{this.onmousewheel=null;}$.
removeData(this,"mousewheel-line-height");$.removeData(this,"mousewheel-page-height");},getLineHeight:function(elem){var $elem=$(elem),$parent=$elem["offsetParent"in $.fn?"offsetParent":"parent"]();if(!$parent.length){$parent=$("body");}return parseInt($parent.css("fontSize"),10)||parseInt($elem.css("fontSize"),10)||16;},getPageHeight:function(elem){return $(elem).height();},settings:{adjustOldDeltas:true,normalizeOffset:true}};$.fn.extend({mousewheel:function(fn){return fn?this.on("mousewheel",fn):this.trigger("mousewheel");},unmousewheel:function(fn){return this.off("mousewheel",fn);}});function handler(event){var orgEvent=event||window.event,args=slice.call(arguments,1),delta=0,deltaX=0,deltaY=0,absDelta=0;event=$.event.fix(orgEvent);event.type="mousewheel";if("detail"in orgEvent){deltaY=orgEvent.detail*-1;}if("wheelDelta"in orgEvent){deltaY=orgEvent.wheelDelta;}if("wheelDeltaY"in orgEvent){deltaY=orgEvent.wheelDeltaY;}if("wheelDeltaX"in orgEvent){deltaX=orgEvent.wheelDeltaX*-1;}if(
"axis"in orgEvent&&orgEvent.axis===orgEvent.HORIZONTAL_AXIS){deltaX=deltaY*-1;deltaY=0;}delta=deltaY===0?deltaX:deltaY;if("deltaY"in orgEvent){deltaY=orgEvent.deltaY*-1;delta=deltaY;}if("deltaX"in orgEvent){deltaX=orgEvent.deltaX;if(deltaY===0){delta=deltaX*-1;}}if(deltaY===0&&deltaX===0){return;}if(orgEvent.deltaMode===1){var lineHeight=$.data(this,"mousewheel-line-height");delta*=lineHeight;deltaY*=lineHeight;deltaX*=lineHeight;}else if(orgEvent.deltaMode===2){var pageHeight=$.data(this,"mousewheel-page-height");delta*=pageHeight;deltaY*=pageHeight;deltaX*=pageHeight;}absDelta=Math.max(Math.abs(deltaY),Math.abs(deltaX));if(!lowestDelta||absDelta<lowestDelta){lowestDelta=absDelta;if(shouldAdjustOldDeltas(orgEvent,absDelta)){lowestDelta/=40;}}if(shouldAdjustOldDeltas(orgEvent,absDelta)){delta/=40;deltaX/=40;deltaY/=40;}delta=Math[delta>=1?"floor":"ceil"](delta/lowestDelta);deltaX=Math[deltaX>=1?"floor":"ceil"](deltaX/lowestDelta);deltaY=Math[deltaY>=1?"floor":"ceil"](deltaY/lowestDelta
);if(special.settings.normalizeOffset&&this.getBoundingClientRect){var boundingRect=this.getBoundingClientRect();event.offsetX=event.clientX-boundingRect.left;event.offsetY=event.clientY-boundingRect.top;}event.deltaX=deltaX;event.deltaY=deltaY;event.deltaFactor=lowestDelta;event.deltaMode=0;args.unshift(event,delta,deltaX,deltaY);if(nullLowestDeltaTimeout){window.clearTimeout(nullLowestDeltaTimeout);}nullLowestDeltaTimeout=window.setTimeout(nullLowestDelta,200);return($.event.dispatch||$.event.handle).apply(this,args);}function nullLowestDelta(){lowestDelta=null;}function shouldAdjustOldDeltas(orgEvent,absDelta){return special.settings.adjustOldDeltas&&orgEvent.type==="mousewheel"&&absDelta%120===0;}});!function($,window,pluginName,undefined){var containerDefaults={drag:true,drop:true,exclude:"",nested:true,vertical:true},groupDefaults={afterMove:function($placeholder,container,$closestItemOrContainer){},containerPath:"",containerSelector:"ol, ul",distance:0,delay:0,handle:"",itemPath
:"",itemSelector:"li",bodyClass:"dragging",draggedClass:"dragged",isValidTarget:function($item,container){return true},onCancel:function($item,container,_super,event){},onDrag:function($item,position,_super,event){$item.css(position)
event.preventDefault()},onDragStart:function($item,container,_super,event){$item.css({height:$item.outerHeight(),width:$item.outerWidth()})
$item.addClass(container.group.options.draggedClass)
$("body").addClass(container.group.options.bodyClass)},onDrop:function($item,container,_super,event){$item.removeClass(container.group.options.draggedClass).removeAttr("style")
$("body").removeClass(container.group.options.bodyClass)},onMousedown:function($item,_super,event){if(!event.target.nodeName.match(/^(input|select|textarea)$/i)){if(event.type.match(/^mouse/))event.preventDefault()
return true}},placeholderClass:"placeholder",placeholder:'<li class="placeholder"></li>',pullPlaceholder:true,serialize:function($parent,$children,parentIsContainer){var result=$.extend({},$parent.data())
if(parentIsContainer){return[$children]}else if($children[0]){result.children=$children}delete result.subContainers
delete result.sortable
return result},tolerance:0},containerGroups={},groupCounter=0,emptyBox={left:0,top:0,bottom:0,right:0},eventNames={start:"touchstart.sortable mousedown.sortable",drop:"touchend.sortable touchcancel.sortable mouseup.sortable",drag:"touchmove.sortable mousemove.sortable",scroll:"scroll.sortable"},subContainerKey="subContainers"
function d(a,b){var x=Math.max(0,a[0]-b[0],b[0]-a[1]),y=Math.max(0,a[2]-b[1],b[1]-a[3])
return x+y;}function setDimensions(array,dimensions,tolerance,useOffset){var i=array.length,offsetMethod=useOffset?"offset":"position"
tolerance=tolerance||0
while(i--){var el=array[i].el?array[i].el:$(array[i]),pos=el[offsetMethod]()
pos.left+=parseInt(el.css('margin-left'),10)
pos.top+=parseInt(el.css('margin-top'),10)
dimensions[i]=[pos.left-tolerance,pos.left+el.outerWidth()+tolerance,pos.top-tolerance,pos.top+el.outerHeight()+tolerance]}}function getRelativePosition(pointer,element){var offset=element.offset()
return{left:pointer.left-offset.left,top:pointer.top-offset.top}}function sortByDistanceDesc(dimensions,pointer,lastPointer){pointer=[pointer.left,pointer.top]
lastPointer=lastPointer&&[lastPointer.left,lastPointer.top]
var dim,i=dimensions.length,distances=[]
while(i--){dim=dimensions[i]
distances[i]=[i,d(dim,pointer),lastPointer&&d(dim,lastPointer)]}distances=distances.sort(function(a,b){return b[1]-a[1]||b[2]-a[2]||b[0]-a[0]})
return distances}function ContainerGroup(options){this.options=$.extend({},groupDefaults,options)
this.containers=[]
if(!this.options.rootGroup){this.scrollProxy=$.proxy(this.scroll,this)
this.dragProxy=$.proxy(this.drag,this)
this.dropProxy=$.proxy(this.drop,this)
this.placeholder=$(this.options.placeholder)
if(!options.isValidTarget)this.options.isValidTarget=undefined}}ContainerGroup.get=function(options){if(!containerGroups[options.group]){if(options.group===undefined)options.group=groupCounter++
containerGroups[options.group]=new ContainerGroup(options)}return containerGroups[options.group]}
ContainerGroup.prototype={dragInit:function(e,itemContainer){this.$document=$(itemContainer.el[0].ownerDocument)
var closestItem=$(e.target).closest(this.options.itemSelector);if(closestItem.length){this.item=closestItem;this.itemContainer=itemContainer;if(this.item.is(this.options.exclude)||!this.options.onMousedown(this.item,groupDefaults.onMousedown,e)){return;}this.setPointer(e);this.toggleListeners('on');this.setupDelayTimer();this.dragInitDone=true;}},drag:function(e){if(!this.dragging){if(!this.distanceMet(e)||!this.delayMet)return
this.options.onDragStart(this.item,this.itemContainer,groupDefaults.onDragStart,e)
this.item.before(this.placeholder)
this.dragging=true}this.setPointer(e)
this.options.onDrag(this.item,getRelativePosition(this.pointer,this.item.offsetParent()),groupDefaults.onDrag,e)
var p=this.getPointer(e),box=this.sameResultBox,t=this.options.tolerance
if(!box||box.top-t>p.top||box.bottom+t<p.top||box.left-t>p.left||box.right+t<p.left)if(!this.searchValidTarget()){this.placeholder.detach()
this.lastAppendedItem=undefined}},drop:function(e){this.toggleListeners('off')
this.dragInitDone=false
if(this.dragging){if(this.placeholder.closest("html")[0]){this.placeholder.before(this.item).detach()}else{this.options.onCancel(this.item,this.itemContainer,groupDefaults.onCancel,e)}this.options.onDrop(this.item,this.getContainer(this.item),groupDefaults.onDrop,e)
this.clearDimensions()
this.clearOffsetParent()
this.lastAppendedItem=this.sameResultBox=undefined
this.dragging=false}},searchValidTarget:function(pointer,lastPointer){if(!pointer){pointer=this.relativePointer||this.pointer
lastPointer=this.lastRelativePointer||this.lastPointer}var distances=sortByDistanceDesc(this.getContainerDimensions(),pointer,lastPointer),i=distances.length
while(i--){var index=distances[i][0],distance=distances[i][1]
if(!distance||this.options.pullPlaceholder){var container=this.containers[index]
if(!container.disabled){if(!this.$getOffsetParent()){var offsetParent=container.getItemOffsetParent()
pointer=getRelativePosition(pointer,offsetParent)
lastPointer=getRelativePosition(lastPointer,offsetParent)}if(container.searchValidTarget(pointer,lastPointer))return true}}}if(this.sameResultBox)this.sameResultBox=undefined},movePlaceholder:function(container,item,method,sameResultBox){var lastAppendedItem=this.lastAppendedItem
if(!sameResultBox&&lastAppendedItem&&lastAppendedItem[0]===item[0])return;item[method](this.placeholder)
this.lastAppendedItem=item
this.sameResultBox=sameResultBox
this.options.afterMove(this.placeholder,container,item)},getContainerDimensions:function(){if(!this.containerDimensions)setDimensions(this.containers,this.containerDimensions=[],this.options.tolerance,!this.$getOffsetParent())
return this.containerDimensions},getContainer:function(element){return element.closest(this.options.containerSelector).data(pluginName)},$getOffsetParent:function(){if(this.offsetParent===undefined){var i=this.containers.length-1,offsetParent=this.containers[i].getItemOffsetParent()
if(!this.options.rootGroup){while(i--){if(offsetParent[0]!=this.containers[i].getItemOffsetParent()[0]){offsetParent=false
break;}}}this.offsetParent=offsetParent}return this.offsetParent},setPointer:function(e){var pointer=this.getPointer(e)
if(this.$getOffsetParent()){var relativePointer=getRelativePosition(pointer,this.$getOffsetParent())
this.lastRelativePointer=this.relativePointer
this.relativePointer=relativePointer}this.lastPointer=this.pointer
this.pointer=pointer},distanceMet:function(e){var currentPointer=this.getPointer(e)
return(Math.max(Math.abs(this.pointer.left-currentPointer.left),Math.abs(this.pointer.top-currentPointer.top))>=this.options.distance)},getPointer:function(e){var o=e.originalEvent,t=(e.originalEvent.touches&&e.originalEvent.touches[0])||{}
return{left:e.pageX||o.pageX||t.pageX,top:e.pageY||o.pageY||t.pageY}},setupDelayTimer:function(){var that=this
this.delayMet=!this.options.delay
if(!this.delayMet){clearTimeout(this._mouseDelayTimer);this._mouseDelayTimer=setTimeout(function(){that.delayMet=true},this.options.delay)}},scroll:function(e){this.clearDimensions()
this.clearOffsetParent()},toggleListeners:function(method){var that=this,events=['drag','drop','scroll']
$.each(events,function(i,event){that.$document[method](eventNames[event],that[event+'Proxy'])})},clearOffsetParent:function(){this.offsetParent=undefined},clearDimensions:function(){this.traverse(function(object){object._clearDimensions()})},traverse:function(callback){callback(this)
var i=this.containers.length
while(i--){this.containers[i].traverse(callback)}},_clearDimensions:function(){this.containerDimensions=undefined},_destroy:function(){containerGroups[this.options.group]=undefined}}
function Container(element,options){this.el=element
this.options=$.extend({},containerDefaults,options)
this.group=ContainerGroup.get(this.options)
this.rootGroup=this.options.rootGroup||this.group
this.handle=this.rootGroup.options.handle||this.rootGroup.options.itemSelector
var itemPath=this.rootGroup.options.itemPath
this.target=itemPath?this.el.find(itemPath):this.el
this.target.on(eventNames.start,this.handle,$.proxy(this.dragInit,this))
if(this.options.drop)this.group.containers.push(this)}Container.prototype={dragInit:function(e){var rootGroup=this.rootGroup
if(!this.disabled&&!rootGroup.dragInitDone&&this.options.drag&&this.isValidDrag(e)){rootGroup.dragInit(e,this)}},isValidDrag:function(e){return e.which==1||e.type=="touchstart"&&e.originalEvent.touches.length==1},searchValidTarget:function(pointer,lastPointer){var distances=sortByDistanceDesc(this.getItemDimensions(),pointer,lastPointer),i=distances.length,rootGroup=this.rootGroup,validTarget=!rootGroup.options.isValidTarget||rootGroup.options.isValidTarget(rootGroup.item,this)
if(!i&&validTarget){rootGroup.movePlaceholder(this,this.target,"append")
return true}else while(i--){var index=distances[i][0],distance=distances[i][1]
if(!distance&&this.hasChildGroup(index)){var found=this.getContainerGroup(index).searchValidTarget(pointer,lastPointer)
if(found)return true}else if(validTarget){this.movePlaceholder(index,pointer)
return true}}},movePlaceholder:function(index,pointer){var item=$(this.items[index]),dim=this.itemDimensions[index],method="after",width=item.outerWidth(),height=item.outerHeight(),offset=item.offset(),sameResultBox={left:offset.left,right:offset.left+width,top:offset.top,bottom:offset.top+height}
if(this.options.vertical){var yCenter=(dim[2]+dim[3])/2,inUpperHalf=pointer.top<=yCenter
if(inUpperHalf){method="before"
sameResultBox.bottom-=height/2}else sameResultBox.top+=height/2}else{var xCenter=(dim[0]+dim[1])/2,inLeftHalf=pointer.left<=xCenter
if(inLeftHalf){method="before"
sameResultBox.right-=width/2}else sameResultBox.left+=width/2}if(this.hasChildGroup(index))sameResultBox=emptyBox
this.rootGroup.movePlaceholder(this,item,method,sameResultBox)},getItemDimensions:function(){if(!this.itemDimensions){this.items=this.$getChildren(this.el,"item").filter(":not(."+this.group.options.placeholderClass+", ."+this.group.options.draggedClass+")").get()
setDimensions(this.items,this.itemDimensions=[],this.options.tolerance)}return this.itemDimensions},getItemOffsetParent:function(){var offsetParent,el=this.el
if(el.css("position")==="relative"||el.css("position")==="absolute"||el.css("position")==="fixed")offsetParent=el
else offsetParent=el.offsetParent()
return offsetParent},hasChildGroup:function(index){return this.options.nested&&this.getContainerGroup(index)},getContainerGroup:function(index){var childGroup=$.data(this.items[index],subContainerKey)
if(childGroup===undefined){var childContainers=this.$getChildren(this.items[index],"container")
childGroup=false
if(childContainers[0]){var options=$.extend({},this.options,{rootGroup:this.rootGroup,group:groupCounter++})
childGroup=childContainers[pluginName](options).data(pluginName).group}$.data(this.items[index],subContainerKey,childGroup)}return childGroup},$getChildren:function(parent,type){var options=this.rootGroup.options,path=options[type+"Path"],selector=options[type+"Selector"]
parent=$(parent)
if(path)parent=parent.find(path)
return parent.children(selector)},_serialize:function(parent,isContainer){var that=this,childType=isContainer?"item":"container",children=this.$getChildren(parent,childType).not(this.options.exclude).map(function(){return that._serialize($(this),!isContainer)}).get()
return this.rootGroup.options.serialize(parent,children,isContainer)},traverse:function(callback){$.each(this.items||[],function(item){var group=$.data(this,subContainerKey)
if(group)group.traverse(callback)});callback(this)},_clearDimensions:function(){this.itemDimensions=undefined},_destroy:function(){var that=this;this.target.off(eventNames.start,this.handle);this.el.removeData(pluginName)
if(this.options.drop)this.group.containers=$.grep(this.group.containers,function(val){return val!=that})
$.each(this.items||[],function(){$.removeData(this,subContainerKey)})}}
var API={enable:function(){this.traverse(function(object){object.disabled=false})},disable:function(){this.traverse(function(object){object.disabled=true})},serialize:function(){return this._serialize(this.el,true)},refresh:function(){this.traverse(function(object){object._clearDimensions()})},destroy:function(){this.traverse(function(object){object._destroy();})}}
$.extend(Container.prototype,API)
$.fn[pluginName]=function(methodOrOptions){var args=Array.prototype.slice.call(arguments,1)
return this.map(function(){var $t=$(this),object=$t.data(pluginName)
if(object&&API[methodOrOptions])return API[methodOrOptions].apply(object,args)||this
else if(!object&&(methodOrOptions===undefined||typeof methodOrOptions==="object"))$t.data(pluginName,new Container($t,methodOrOptions))
return this});};}(jQuery,window,'jqSortable');!function(e,t){"use strict";var n;if("object"==typeof exports){try{n=require("moment")}catch(e){}module.exports=t(n)}else"function"==typeof define&&define.amd?define((function(e){try{n=e("moment")}catch(e){}return t(n)})):e.Pikaday=t(e.moment)}(this,(function(e){"use strict";var t="function"==typeof e,n=!!window.addEventListener,a=window.document,i=window.setTimeout,s=function(e,t,a,i){n?e.addEventListener(t,a,!!i):e.attachEvent("on"+t,a)},o=function(e,t,a,i){n?e.removeEventListener(t,a,!!i):e.detachEvent("on"+t,a)},r=function(e,t){return-1!==(" "+e.className+" ").indexOf(" "+t+" ")},l=function(e,t){r(e,t)||(e.className=""===e.className?t:e.className+" "+t)},h=function(e,t){var n;e.className=(n=(" "+e.className+" ").replace(" "+t+" "," ")).trim?n.trim():n.replace(/^\s+|\s+$/g,"")},d=function(e){return/Array/.test(Object.prototype.toString.call(e))},u=function(e){return/Date/.test(Object.prototype.toString.call(e))&&!isNaN(e.getTime())},c=
function(e){var t=e.getDay();return 0===t||6===t},f=function(e){return e%4==0&&e%100!=0||e%400==0},g=function(e,t){return[31,f(e)?29:28,31,30,31,30,31,31,30,31,30,31][t]},m=function(e){u(e)&&e.setHours(0,0,0,0)},p=function(e,t){return e.getTime()===t.getTime()},y=function(e,t,n){var a,i;for(a in t)(i=void 0!==e[a])&&"object"==typeof t[a]&&null!==t[a]&&void 0===t[a].nodeName?u(t[a])?n&&(e[a]=new Date(t[a].getTime())):d(t[a])?n&&(e[a]=t[a].slice(0)):e[a]=y({},t[a],n):!n&&i||(e[a]=t[a]);return e},D=function(e,t,n){var i;a.createEvent?((i=a.createEvent("HTMLEvents")).initEvent(t,!0,!1),i=y(i,n),e.dispatchEvent(i)):a.createEventObject&&(i=a.createEventObject(),i=y(i,n),e.fireEvent("on"+t,i))},b=function(e){return e.month<0&&(e.year-=Math.ceil(Math.abs(e.month)/12),e.month+=12),e.month>11&&(e.year+=Math.floor(Math.abs(e.month)/12),e.month-=12),e},v={field:null,bound:void 0,ariaLabel:"Use the arrow keys to pick a date",position:"bottom left",reposition:!0,format:"YYYY-MM-DD",toString:null,
parse:null,defaultDate:null,setDefaultDate:!1,firstDay:0,firstWeekOfYearMinDays:4,formatStrict:!1,minDate:null,maxDate:null,yearRange:10,showWeekNumber:!1,pickWholeWeek:!1,minYear:0,maxYear:9999,minMonth:void 0,maxMonth:void 0,startRange:null,endRange:null,isRTL:!1,yearSuffix:"",showMonthAfterYear:!1,showDaysInNextAndPreviousMonths:!1,enableSelectionDaysInNextAndPreviousMonths:!1,numberOfMonths:1,mainCalendar:"left",container:void 0,blurFieldOnSelect:!0,i18n:{previousMonth:"Previous Month",nextMonth:"Next Month",months:["January","February","March","April","May","June","July","August","September","October","November","December"],weekdays:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],weekdaysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"]},theme:null,events:[],onSelect:null,onOpen:null,onClose:null,onDraw:null,keyboardInput:!0},_=function(e,t,n){for(t+=e.firstDay;t>=7;)t-=7;return n?e.i18n.weekdaysShort[t]:e.i18n.weekdays[t]},w=function(e){var t=[],n="false"
;if(e.isEmpty){if(!e.showDaysInNextAndPreviousMonths)return'<td class="is-empty"></td>';t.push("is-outside-current-month"),e.enableSelectionDaysInNextAndPreviousMonths||t.push("is-selection-disabled")}return e.isDisabled&&t.push("is-disabled"),e.isToday&&t.push("is-today"),e.isSelected&&(t.push("is-selected"),n="true"),e.hasEvent&&t.push("has-event"),e.isInRange&&t.push("is-inrange"),e.isStartRange&&t.push("is-startrange"),e.isEndRange&&t.push("is-endrange"),'<td data-day="'+e.day+'" class="'+t.join(" ")+'" aria-selected="'+n+'"><button class="pika-button pika-day" type="button" data-pika-year="'+e.year+'" data-pika-month="'+e.month+'" data-pika-day="'+e.day+'">'+e.day+"</button></td>"},k=function(n,a,i,s){var o=new Date(i,a,n),r=t?e(o).isoWeek():function(e,t){e.setHours(0,0,0,0);var n=e.getDate(),a=e.getDay(),i=t,s=i-1,o=function(e){return(e+7-1)%7};e.setDate(n+s-o(a));var r=new Date(e.getFullYear(),0,i),l=(e.getTime()-r.getTime())/864e5;return 1+Math.round((l-s+o(r.getDay()))/7)}(o,s
);return'<td class="pika-week">'+r+"</td>"},M=function(e,t,n,a){return'<tr class="pika-row'+(n?" pick-whole-week":"")+(a?" is-selected":"")+'">'+(t?e.reverse():e).join("")+"</tr>"},x=function(e,t,n,a,i,s){var o,r,l,h,u,c=e._o,f=n===c.minYear,g=n===c.maxYear,m='<div id="'+s+'" class="pika-title" role="heading" aria-live="assertive">',p=!0,y=!0;for(l=[],o=0;o<12;o++)l.push('<option value="'+(n===i?o-t:12+o-t)+'"'+(o===a?' selected="selected"':"")+(f&&o<c.minMonth||g&&o>c.maxMonth?' disabled="disabled"':"")+">"+c.i18n.months[o]+"</option>");for(h='<div class="pika-label">'+c.i18n.months[a]+'<select class="pika-select pika-select-month" tabindex="-1">'+l.join("")+"</select></div>",d(c.yearRange)?(o=c.yearRange[0],r=c.yearRange[1]+1):(o=n-c.yearRange,r=1+n+c.yearRange),l=[];o<r&&o<=c.maxYear;o++)o>=c.minYear&&l.push('<option value="'+o+'"'+(o===n?' selected="selected"':"")+">"+o+"</option>");return u='<div class="pika-label">'+n+c.yearSuffix+
'<select class="pika-select pika-select-year" tabindex="-1">'+l.join("")+"</select></div>",c.showMonthAfterYear?m+=u+h:m+=h+u,f&&(0===a||c.minMonth>=a)&&(p=!1),g&&(11===a||c.maxMonth<=a)&&(y=!1),0===t&&(m+='<button class="pika-prev'+(p?"":" is-disabled")+'" type="button">'+c.i18n.previousMonth+"</button>"),t===e._o.numberOfMonths-1&&(m+='<button class="pika-next'+(y?"":" is-disabled")+'" type="button">'+c.i18n.nextMonth+"</button>"),m+"</div>"},R=function(e,t,n){return'<table cellpadding="0" cellspacing="0" class="pika-table" role="grid" aria-labelledby="'+n+'">'+function(e){var t,n=[];for(e.showWeekNumber&&n.push("<th></th>"),t=0;t<7;t++)n.push('<th scope="col"><abbr title="'+_(e,t)+'">'+_(e,t,!0)+"</abbr></th>");return"<thead><tr>"+(e.isRTL?n.reverse():n).join("")+"</tr></thead>"}(e)+("<tbody>"+t.join("")+"</tbody></table>")},N=function(o){var l=this,h=l.config(o);l._onMouseDown=function(e){if(l._v){var t=(e=e||window.event).target||e.srcElement;if(t)if(r(t,"is-disabled")||(!r(t,
"pika-button")||r(t,"is-empty")||r(t.parentNode,"is-disabled")?r(t,"pika-prev")?l.prevMonth():r(t,"pika-next")&&l.nextMonth():(l.setDate(new Date(t.getAttribute("data-pika-year"),t.getAttribute("data-pika-month"),t.getAttribute("data-pika-day"))),h.bound&&i((function(){l.hide(),h.blurFieldOnSelect&&h.field&&h.field.blur()}),100))),r(t,"pika-select"))l._c=!0;else{if(!e.preventDefault)return e.returnValue=!1,!1;e.preventDefault()}}},l._onChange=function(e){var t=(e=e||window.event).target||e.srcElement;t&&(r(t,"pika-select-month")?l.gotoMonth(t.value):r(t,"pika-select-year")&&l.gotoYear(t.value))},l._onKeyChange=function(e){if(e=e||window.event,l.isVisible())switch(e.keyCode){case 13:case 27:h.field&&h.field.blur();break;case 37:l.adjustDate("subtract",1);break;case 38:l.adjustDate("subtract",7);break;case 39:l.adjustDate("add",1);break;case 40:l.adjustDate("add",7);break;case 8:case 46:l.setDate(null)}},l._parseFieldValue=function(){if(h.parse)return h.parse(h.field.value,h.format);if(t
){var n=e(h.field.value,h.format,h.formatStrict);return n&&n.isValid()?n.toDate():null}return new Date(Date.parse(h.field.value))},l._onInputChange=function(e){var t;e.firedBy!==l&&(t=l._parseFieldValue(),u(t)&&l.setDate(t),l._v||l.show())},l._onInputFocus=function(){l.show()},l._onInputClick=function(){l.show()},l._onInputBlur=function(){var e=a.activeElement;do{if(r(e,"pika-single"))return}while(e=e.parentNode);l._c||(l._b=i((function(){l.hide()}),50)),l._c=!1},l._onClick=function(e){var t=(e=e||window.event).target||e.srcElement,a=t;if(t){!n&&r(t,"pika-select")&&(t.onchange||(t.setAttribute("onchange","return;"),s(t,"change",l._onChange)));do{if(r(a,"pika-single")||a===h.trigger)return}while(a=a.parentNode);l._v&&t!==h.trigger&&a!==h.trigger&&l.hide()}},l.el=a.createElement("div"),l.el.className="pika-single"+(h.isRTL?" is-rtl":"")+(h.theme?" "+h.theme:""),s(l.el,"mousedown",l._onMouseDown,!0),s(l.el,"touchend",l._onMouseDown,!0),s(l.el,"change",l._onChange),h.keyboardInput&&s(a,
"keydown",l._onKeyChange),h.field&&(h.container?h.container.appendChild(l.el):h.bound?a.body.appendChild(l.el):h.field.parentNode.insertBefore(l.el,h.field.nextSibling),s(h.field,"change",l._onInputChange),h.defaultDate||(h.defaultDate=l._parseFieldValue(),h.setDefaultDate=!0));var d=h.defaultDate;u(d)?h.setDefaultDate?l.setDate(d,!0):l.gotoDate(d):l.gotoDate(new Date),h.bound?(this.hide(),l.el.className+=" is-bound",s(h.trigger,"click",l._onInputClick),s(h.trigger,"focus",l._onInputFocus),s(h.trigger,"blur",l._onInputBlur)):this.show()};return N.prototype={config:function(e){this._o||(this._o=y({},v,!0));var t=y(this._o,e,!0);t.isRTL=!!t.isRTL,t.field=t.field&&t.field.nodeName?t.field:null,t.theme="string"==typeof t.theme&&t.theme?t.theme:null,t.bound=!!(void 0!==t.bound?t.field&&t.bound:t.field),t.trigger=t.trigger&&t.trigger.nodeName?t.trigger:t.field,t.disableWeekends=!!t.disableWeekends,t.disableDayFn="function"==typeof t.disableDayFn?t.disableDayFn:null;var n=parseInt(t.
numberOfMonths,10)||1;if(t.numberOfMonths=n>4?4:n,u(t.minDate)||(t.minDate=!1),u(t.maxDate)||(t.maxDate=!1),t.minDate&&t.maxDate&&t.maxDate<t.minDate&&(t.maxDate=t.minDate=!1),t.minDate&&this.setMinDate(t.minDate),t.maxDate&&this.setMaxDate(t.maxDate),d(t.yearRange)){var a=(new Date).getFullYear()-10;t.yearRange[0]=parseInt(t.yearRange[0],10)||a,t.yearRange[1]=parseInt(t.yearRange[1],10)||a}else t.yearRange=Math.abs(parseInt(t.yearRange,10))||v.yearRange,t.yearRange>100&&(t.yearRange=100);return t},toString:function(n){return n=n||this._o.format,u(this._d)?this._o.toString?this._o.toString(this._d,n):t?e(this._d).format(n):this._d.toDateString():""},getMoment:function(){return t?e(this._d):null},setMoment:function(n,a){t&&e.isMoment(n)&&this.setDate(n.toDate(),a)},getDate:function(){return u(this._d)?new Date(this._d.getTime()):null},setDate:function(e,t){if(!e)return this._d=null,this._o.field&&(this._o.field.value="",D(this._o.field,"change",{firedBy:this})),this.draw();if("string"==
typeof e&&(e=new Date(Date.parse(e))),u(e)){var n=this._o.minDate,a=this._o.maxDate;u(n)&&e<n?e=n:u(a)&&e>a&&(e=a),this._d=new Date(e.getTime()),m(this._d),this.gotoDate(this._d),this._o.field&&(this._o.field.value=this.toString(),D(this._o.field,"change",{firedBy:this})),t||"function"!=typeof this._o.onSelect||this._o.onSelect.call(this,this.getDate())}},clear:function(){this.setDate(null)},gotoDate:function(e){var t=!0;if(u(e)){if(this.calendars){var n=new Date(this.calendars[0].year,this.calendars[0].month,1),a=new Date(this.calendars[this.calendars.length-1].year,this.calendars[this.calendars.length-1].month,1),i=e.getTime();a.setMonth(a.getMonth()+1),a.setDate(a.getDate()-1),t=i<n.getTime()||a.getTime()<i}t&&(this.calendars=[{month:e.getMonth(),year:e.getFullYear()}],"right"===this._o.mainCalendar&&(this.calendars[0].month+=1-this._o.numberOfMonths)),this.adjustCalendars()}},adjustDate:function(e,t){var n,a=this.getDate()||new Date,i=24*parseInt(t)*60*60*1e3;"add"===e?n=new Date(a
.valueOf()+i):"subtract"===e&&(n=new Date(a.valueOf()-i)),this.setDate(n)},adjustCalendars:function(){this.calendars[0]=b(this.calendars[0]);for(var e=1;e<this._o.numberOfMonths;e++)this.calendars[e]=b({month:this.calendars[0].month+e,year:this.calendars[0].year});this.draw()},gotoToday:function(){this.gotoDate(new Date)},gotoMonth:function(e){isNaN(e)||(this.calendars[0].month=parseInt(e,10),this.adjustCalendars())},nextMonth:function(){this.calendars[0].month++,this.adjustCalendars()},prevMonth:function(){this.calendars[0].month--,this.adjustCalendars()},gotoYear:function(e){isNaN(e)||(this.calendars[0].year=parseInt(e,10),this.adjustCalendars())},setMinDate:function(e){e instanceof Date?(m(e),this._o.minDate=e,this._o.minYear=e.getFullYear(),this._o.minMonth=e.getMonth()):(this._o.minDate=v.minDate,this._o.minYear=v.minYear,this._o.minMonth=v.minMonth,this._o.startRange=v.startRange),this.draw()},setMaxDate:function(e){e instanceof Date?(m(e),this._o.maxDate=e,this._o.maxYear=e.
getFullYear(),this._o.maxMonth=e.getMonth()):(this._o.maxDate=v.maxDate,this._o.maxYear=v.maxYear,this._o.maxMonth=v.maxMonth,this._o.endRange=v.endRange),this.draw()},setStartRange:function(e){this._o.startRange=e},setEndRange:function(e){this._o.endRange=e},draw:function(e){if(this._v||e){var t,n=this._o,a=n.minYear,s=n.maxYear,o=n.minMonth,r=n.maxMonth,l="";this._y<=a&&(this._y=a,!isNaN(o)&&this._m<o&&(this._m=o)),this._y>=s&&(this._y=s,!isNaN(r)&&this._m>r&&(this._m=r));for(var h=0;h<n.numberOfMonths;h++)t="pika-title-"+Math.random().toString(36).replace(/[^a-z]+/g,"").substr(0,2),l+='<div class="pika-lendar">'+x(this,h,this.calendars[h].year,this.calendars[h].month,this.calendars[0].year,t)+this.render(this.calendars[h].year,this.calendars[h].month,t)+"</div>";this.el.innerHTML=l,n.bound&&"hidden"!==n.field.type&&i((function(){n.trigger.focus()}),1),"function"==typeof this._o.onDraw&&this._o.onDraw(this),n.bound&&n.field.setAttribute("aria-label",n.ariaLabel)}},adjustPosition:
function(){var e,t,n,i,s,o,r,d,u,c,f,g;if(!this._o.container){if(this.el.style.position="absolute",t=e=this._o.trigger,n=this.el.offsetWidth,i=this.el.offsetHeight,s=window.innerWidth||a.documentElement.clientWidth,o=window.innerHeight||a.documentElement.clientHeight,r=window.pageYOffset||a.body.scrollTop||a.documentElement.scrollTop,f=!0,g=!0,"function"==typeof e.getBoundingClientRect)d=(c=e.getBoundingClientRect()).left+window.pageXOffset,u=c.bottom+window.pageYOffset;else for(d=t.offsetLeft,u=t.offsetTop+t.offsetHeight;t=t.offsetParent;)d+=t.offsetLeft,u+=t.offsetTop;(this._o.reposition&&d+n>s||this._o.position.indexOf("right")>-1&&d-n+e.offsetWidth>0)&&(d=d-n+e.offsetWidth,f=!1),(this._o.reposition&&u+i>o+r||this._o.position.indexOf("top")>-1&&u-i-e.offsetHeight>0)&&(u=u-i-e.offsetHeight,g=!1),this.el.style.left=d+"px",this.el.style.top=u+"px",l(this.el,f?"left-aligned":"right-aligned"),l(this.el,g?"bottom-aligned":"top-aligned"),h(this.el,f?"right-aligned":"left-aligned"),h(this.
el,g?"top-aligned":"bottom-aligned")}},render:function(e,t,n){var a=this._o,i=new Date,s=g(e,t),o=new Date(e,t,1).getDay(),r=[],l=[];m(i),a.firstDay>0&&(o-=a.firstDay)<0&&(o+=7);for(var h=0===t?11:t-1,d=11===t?0:t+1,f=0===t?e-1:e,y=11===t?e+1:e,D=g(f,h),b=s+o,v=b;v>7;)v-=7;b+=7-v;for(var _=!1,x=0,N=0;x<b;x++){var S=new Date(e,t,x-o+1),C=!!u(this._d)&&p(S,this._d),I=p(S,i),T=-1!==a.events.indexOf(S.toDateString()),Y=x<o||x>=s+o,E=x-o+1,O=t,j=e,W=a.startRange&&p(a.startRange,S),F=a.endRange&&p(a.endRange,S),A=a.startRange&&a.endRange&&a.startRange<S&&S<a.endRange;Y&&(x<o?(E=D+E,O=h,j=f):(E-=s,O=d,j=y));var L={day:E,month:O,year:j,hasEvent:T,isSelected:C,isToday:I,isDisabled:a.minDate&&S<a.minDate||a.maxDate&&S>a.maxDate||a.disableWeekends&&c(S)||a.disableDayFn&&a.disableDayFn(S),isEmpty:Y,isStartRange:W,isEndRange:F,isInRange:A,showDaysInNextAndPreviousMonths:a.showDaysInNextAndPreviousMonths,enableSelectionDaysInNextAndPreviousMonths:a.enableSelectionDaysInNextAndPreviousMonths};a.
pickWholeWeek&&C&&(_=!0),l.push(w(L)),7==++N&&(a.showWeekNumber&&l.unshift(k(x-o,t,e,a.firstWeekOfYearMinDays)),r.push(M(l,a.isRTL,a.pickWholeWeek,_)),l=[],N=0,_=!1)}return R(a,r,n)},isVisible:function(){return this._v},show:function(){this.isVisible()||(this._v=!0,this.draw(),h(this.el,"is-hidden"),this._o.bound&&(s(a,"click",this._onClick),this.adjustPosition()),"function"==typeof this._o.onOpen&&this._o.onOpen.call(this))},hide:function(){var e=this._v;!1!==e&&(this._o.bound&&o(a,"click",this._onClick),this._o.container||(this.el.style.position="static",this.el.style.left="auto",this.el.style.top="auto"),l(this.el,"is-hidden"),this._v=!1,void 0!==e&&"function"==typeof this._o.onClose&&this._o.onClose.call(this))},destroy:function(){var e=this._o;this.hide(),o(this.el,"mousedown",this._onMouseDown,!0),o(this.el,"touchend",this._onMouseDown,!0),o(this.el,"change",this._onChange),e.keyboardInput&&o(a,"keydown",this._onKeyChange),e.field&&(o(e.field,"change",this._onInputChange),e.bound
&&(o(e.trigger,"click",this._onInputClick),o(e.trigger,"focus",this._onInputFocus),o(e.trigger,"blur",this._onInputBlur))),this.el.parentNode&&this.el.parentNode.removeChild(this.el)}},N}));!function(e,t){"use strict";"object"==typeof exports?t(require("jquery"),require("pikaday")):"function"==typeof define&&define.amd?define(["jquery","pikaday"],t):t(e.jQuery,e.Pikaday)}(this,(function(e,t){"use strict";e.fn.pikaday=function(){var a=arguments;return a&&a.length||(a=[{}]),this.each((function(){var i=e(this),n=i.data("pikaday");if(n instanceof t)"string"==typeof a[0]&&"function"==typeof n[a[0]]&&(n[a[0]].apply(n,Array.prototype.slice.call(a,1)),"destroy"===a[0]&&i.removeData("pikaday"));else if("object"==typeof a[0]){var r=e.extend({},a[0]);r.field=i[0],i.data("pikaday",new t(r))}}))}}));;(function(){var $=window.jQuery,$win=$(window),$doc=$(document),$body;var svgNS='http://www.w3.org/2000/svg',svgSupported='SVGAngle'in window&&(function(){var supported,el=document.createElement('div')
;el.innerHTML='<svg/>';supported=(el.firstChild&&el.firstChild.namespaceURI)==svgNS;el.innerHTML='';return supported;})();var transitionSupported=(function(){var style=document.createElement('div').style;return'transition'in style||'WebkitTransition'in style||'MozTransition'in style||'msTransition'in style||'OTransition'in style;})();var touchSupported='ontouchstart'in window,mousedownEvent='mousedown'+(touchSupported?' touchstart':''),mousemoveEvent='mousemove.clockpicker'+(touchSupported?' touchmove.clockpicker':''),mouseupEvent='mouseup.clockpicker'+(touchSupported?' touchend.clockpicker':'');var vibrate=navigator.vibrate?'vibrate':navigator.webkitVibrate?'webkitVibrate':null;function createSvgElement(name){return document.createElementNS(svgNS,name);}function leadingZero(num){return(num<10?'0':'')+num;}var idCounter=0;function uniqueId(prefix){var id=++idCounter+'';return prefix?prefix+id:id;}var dialRadius=100,outerRadius=80,innerRadius=54,tickRadius=13,diameter=dialRadius*2,
duration=transitionSupported?350:1;var tpl=['<div class="popover clockpicker-popover">','<div class="arrow"></div>','<div class="popover-title">','<span class="clockpicker-span-hours text-primary"></span>',':','<span class="clockpicker-span-minutes"></span> ','<span class="clockpicker-span-am-pm"></span>','</div>','<div class="popover-content">','<div class="clockpicker-plate">','<div class="clockpicker-canvas"></div>','<div class="clockpicker-dial clockpicker-hours"></div>','<div class="clockpicker-dial clockpicker-minutes clockpicker-dial-out"></div>','</div>','<span class="clockpicker-am-pm-block">','</span>','</div>','</div>'].join('');function ClockPicker(element,options){var popover=$(tpl),plate=popover.find('.clockpicker-plate'),hoursView=popover.find('.clockpicker-hours'),minutesView=popover.find('.clockpicker-minutes'),amPmBlock=popover.find('.clockpicker-am-pm-block'),isInput=element.prop('tagName')==='INPUT',input=isInput?element:element.find('input'),addon=element.find(
'.input-group-addon'),self=this,timer;this.id=uniqueId('cp');this.element=element;this.options=options;this.isAppended=false;this.isShown=false;this.currentView='hours';this.isInput=isInput;this.input=input;this.addon=addon;this.popover=popover;this.plate=plate;this.hoursView=hoursView;this.minutesView=minutesView;this.amPmBlock=amPmBlock;this.spanHours=popover.find('.clockpicker-span-hours');this.spanMinutes=popover.find('.clockpicker-span-minutes');this.spanAmPm=popover.find('.clockpicker-span-am-pm');this.amOrPm="PM";if(options.twelvehour){var amPmButtonsTemplate=['<div class="clockpicker-am-pm-block">','<button type="button" class="btn btn-sm btn-secondary clockpicker-button clockpicker-am-button">','AM</button>','<button type="button" class="btn btn-sm btn-secondary clockpicker-button clockpicker-pm-button">','PM</button>','</div>'].join('');var amPmButtons=$(amPmButtonsTemplate);$('<button type="button" class="btn btn-sm btn-secondary clockpicker-button am-button">'+"AM"+
'</button>').on("click",function(){self.amOrPm="AM";$('.clockpicker-span-am-pm').empty().append('AM');}).appendTo(this.amPmBlock);$('<button type="button" class="btn btn-sm btn-secondary clockpicker-button pm-button">'+"PM"+'</button>').on("click",function(){self.amOrPm='PM';$('.clockpicker-span-am-pm').empty().append('PM');}).appendTo(this.amPmBlock);}if(!options.autoclose){$('<button type="button" class="btn btn-sm btn-secondary btn-block clockpicker-button">'+options.donetext+'</button>').click($.proxy(this.done,this)).appendTo(popover);}if((options.placement==='top'||options.placement==='bottom'||options.placement==='auto')&&(options.align==='top'||options.align==='bottom'))options.align='left';if((options.placement==='left'||options.placement==='right')&&(options.align==='left'||options.align==='right'))options.align='top';popover.addClass(options.placement);popover.addClass('clockpicker-align-'+options.align);this.spanHours.click($.proxy(this.toggleView,this,'hours'));this.
spanMinutes.click($.proxy(this.toggleView,this,'minutes'));input.on('focus.clockpicker click.clockpicker',$.proxy(this.show,this));addon.on('click.clockpicker',$.proxy(this.toggle,this));var tickTpl=$('<div class="clockpicker-tick"></div>'),i,tick,radian,radius;if(options.twelvehour){for(i=1;i<13;i+=1){tick=tickTpl.clone();radian=i/6*Math.PI;radius=outerRadius;tick.css('font-size','120%');tick.css({left:dialRadius+Math.sin(radian)*radius-tickRadius,top:dialRadius-Math.cos(radian)*radius-tickRadius});tick.html(i===0?'00':i);hoursView.append(tick);tick.on(mousedownEvent,mousedown);}}else{for(i=0;i<24;i+=1){tick=tickTpl.clone();radian=i/6*Math.PI;var inner=i>0&&i<13;radius=inner?innerRadius:outerRadius;tick.css({left:dialRadius+Math.sin(radian)*radius-tickRadius,top:dialRadius-Math.cos(radian)*radius-tickRadius});if(inner){tick.addClass('tick-inner');}tick.html(i===0?'00':i);hoursView.append(tick);tick.on(mousedownEvent,mousedown);}}for(i=0;i<60;i+=5){tick=tickTpl.clone();radian=i/30*Math
.PI;tick.css({left:dialRadius+Math.sin(radian)*outerRadius-tickRadius,top:dialRadius-Math.cos(radian)*outerRadius-tickRadius});tick.html(leadingZero(i));minutesView.append(tick);tick.on(mousedownEvent,mousedown);}plate.on(mousedownEvent,function(e){if($(e.target).closest('.clockpicker-tick').length===0){mousedown(e,true);}});function mousedown(e,space){var offset=plate.offset(),isTouch=/^touch/.test(e.type),x0=offset.left+dialRadius,y0=offset.top+dialRadius,dx=(isTouch?e.originalEvent.touches[0]:e).pageX-x0,dy=(isTouch?e.originalEvent.touches[0]:e).pageY-y0,z=Math.sqrt(dx*dx+dy*dy),moved=false;if(space&&(z<outerRadius-tickRadius||z>outerRadius+tickRadius)){return;}e.preventDefault();var movingTimer=setTimeout(function(){$body.addClass('clockpicker-moving');},200);if(svgSupported){plate.append(self.canvas);}self.setHand(dx,dy,!space,true);$doc.off(mousemoveEvent).on(mousemoveEvent,function(e){e.preventDefault();var isTouch=/^touch/.test(e.type),x=(isTouch?e.originalEvent.touches[0]:e).
pageX-x0,y=(isTouch?e.originalEvent.touches[0]:e).pageY-y0;if(!moved&&x===dx&&y===dy){return;}moved=true;self.setHand(x,y,false,true);});$doc.off(mouseupEvent).on(mouseupEvent,function(e){$doc.off(mouseupEvent);e.preventDefault();var isTouch=/^touch/.test(e.type),x=(isTouch?e.originalEvent.changedTouches[0]:e).pageX-x0,y=(isTouch?e.originalEvent.changedTouches[0]:e).pageY-y0;if((space||moved)&&x===dx&&y===dy){self.setHand(x,y);}if(self.currentView==='hours'){self.toggleView('minutes',duration/2);}else{if(options.autoclose){self.minutesView.addClass('clockpicker-dial-out');setTimeout(function(){self.done();},duration/2);}}plate.prepend(canvas);clearTimeout(movingTimer);$body.removeClass('clockpicker-moving');$doc.off(mousemoveEvent);});}if(svgSupported){var canvas=popover.find('.clockpicker-canvas'),svg=createSvgElement('svg');svg.setAttribute('class','clockpicker-svg');svg.setAttribute('width',diameter);svg.setAttribute('height',diameter);var g=createSvgElement('g');g.setAttribute(
'transform','translate('+dialRadius+','+dialRadius+')');var bearing=createSvgElement('circle');bearing.setAttribute('class','clockpicker-canvas-bearing');bearing.setAttribute('cx',0);bearing.setAttribute('cy',0);bearing.setAttribute('r',2);var hand=createSvgElement('line');hand.setAttribute('x1',0);hand.setAttribute('y1',0);var bg=createSvgElement('circle');bg.setAttribute('class','clockpicker-canvas-bg');bg.setAttribute('r',tickRadius);var fg=createSvgElement('circle');fg.setAttribute('class','clockpicker-canvas-fg');fg.setAttribute('r',3.5);g.appendChild(hand);g.appendChild(bg);g.appendChild(fg);g.appendChild(bearing);svg.appendChild(g);canvas.append(svg);this.hand=hand;this.bg=bg;this.fg=fg;this.bearing=bearing;this.g=g;this.canvas=canvas;}raiseCallback(this.options.init);}function raiseCallback(callbackFunction){if(callbackFunction&&typeof callbackFunction==="function"){callbackFunction();}}ClockPicker.DEFAULTS={'default':'',fromnow:0,placement:'bottom',align:'left',donetext:'Done'
,autoclose:false,twelvehour:false,vibrate:true};ClockPicker.prototype.toggle=function(){this[this.isShown?'hide':'show']();};ClockPicker.prototype.locate=function(){var element=this.element,popover=this.popover,offset=element.offset(),width=element.outerWidth(),height=element.outerHeight(),placement=this.options.placement,align=this.options.align,styles={},self=this,viewportHeight=window.innerHeight||document.documentElement.clientHeight,scrollTop=window.pageYOffset||document.body.scrollTop||document.documentElement.scrollTop;popover.show();if(placement==='auto'){if(offset.top+popover.outerHeight()>viewportHeight+scrollTop){placement='top';}else{placement='bottom';}}switch(placement){case'bottom':styles.top=offset.top+height;break;case'right':styles.left=offset.left+width;break;case'top':styles.top=offset.top-popover.outerHeight();break;case'left':styles.left=offset.left-popover.outerWidth();break;}switch(align){case'left':styles.left=offset.left;break;case'right':styles.left=offset.
left+width-popover.outerWidth();break;case'top':styles.top=offset.top;break;case'bottom':styles.top=offset.top+height-popover.outerHeight();break;}popover.css(styles);};ClockPicker.prototype.show=function(e){if(this.isShown){return;}raiseCallback(this.options.beforeShow);var self=this;if(!this.isAppended){$body=$(document.body).append(this.popover);$win.on('resize.clockpicker'+this.id,function(){if(self.isShown){self.locate();}});this.isAppended=true;}var value=((this.input.prop('value')||this.options['default']||'')+'');if(this.options.twelvehour){var amPmValue=value.split(' ');if(amPmValue[1]){value=amPmValue[0];this.amOrPm=amPmValue[1];}}value=value.split(':');if(value[0]==='now'){var now=new Date(+new Date()+this.options.fromnow);value=[now.getHours(),now.getMinutes()];}this.hours=+value[0]||0;this.minutes=+value[1]||0;this.spanHours.html(leadingZero(this.hours));this.spanMinutes.html(leadingZero(this.minutes));if(this.options.twelvehour){this.spanAmPm.html(this.amOrPm);}this.
toggleView('hours');this.locate();this.isShown=true;$doc.on('click.clockpicker.'+this.id+' focusin.clockpicker.'+this.id,function(e){var target=$(e.target);if(target.closest(self.popover).length===0&&target.closest(self.addon).length===0&&target.closest(self.input).length===0){self.hide();}});$doc.on('keyup.clockpicker.'+this.id,function(e){if(e.keyCode===27){self.hide();}});raiseCallback(this.options.afterShow);};ClockPicker.prototype.hide=function(){raiseCallback(this.options.beforeHide);this.isShown=false;$doc.off('click.clockpicker.'+this.id+' focusin.clockpicker.'+this.id);$doc.off('keyup.clockpicker.'+this.id);this.popover.hide();raiseCallback(this.options.afterHide);};ClockPicker.prototype.toggleView=function(view,delay){var raiseAfterHourSelect=false;if(view==='minutes'&&$(this.hoursView).css("visibility")==="visible"){raiseCallback(this.options.beforeHourSelect);raiseAfterHourSelect=true;}var isHours=view==='hours',nextView=isHours?this.hoursView:this.minutesView,hideView=
isHours?this.minutesView:this.hoursView;this.currentView=view;this.spanHours.toggleClass('text-primary',isHours);this.spanMinutes.toggleClass('text-primary',!isHours);hideView.addClass('clockpicker-dial-out');nextView.css('visibility','visible').removeClass('clockpicker-dial-out');this.resetClock(delay);clearTimeout(this.toggleViewTimer);this.toggleViewTimer=setTimeout(function(){hideView.css('visibility','hidden');},duration);if(raiseAfterHourSelect){raiseCallback(this.options.afterHourSelect);}};ClockPicker.prototype.resetClock=function(delay){var view=this.currentView,value=this[view],isHours=view==='hours',unit=Math.PI/(isHours?6:30),radian=value*unit,radius=isHours&&value>0&&value<13?innerRadius:outerRadius,x=Math.sin(radian)*radius,y=-Math.cos(radian)*radius,self=this;if(svgSupported&&delay){self.canvas.addClass('clockpicker-canvas-out');setTimeout(function(){self.canvas.removeClass('clockpicker-canvas-out');self.setHand(x,y);},delay);}else{this.setHand(x,y);}};ClockPicker.
prototype.setHand=function(x,y,roundBy5,dragging){var radian=Math.atan2(x,-y),isHours=this.currentView==='hours',unit=Math.PI/(isHours||roundBy5?6:30),z=Math.sqrt(x*x+y*y),options=this.options,inner=isHours&&z<(outerRadius+innerRadius)/2,radius=inner?innerRadius:outerRadius,value;if(options.twelvehour){radius=outerRadius;}if(radian<0){radian=Math.PI*2+radian;}value=Math.round(radian/unit);radian=value*unit;if(options.twelvehour){if(isHours){if(value===0){value=12;}}else{if(roundBy5){value*=5;}if(value===60){value=0;}}}else{if(isHours){if(value===12){value=0;}value=inner?(value===0?12:value):value===0?0:value+12;}else{if(roundBy5){value*=5;}if(value===60){value=0;}}}if(this[this.currentView]!==value){if(vibrate&&this.options.vibrate){if(!this.vibrateTimer){navigator[vibrate](10);this.vibrateTimer=setTimeout($.proxy(function(){this.vibrateTimer=null;},this),100);}}}this[this.currentView]=value;this[isHours?'spanHours':'spanMinutes'].html(leadingZero(value));if(!svgSupported){this[isHours
?'hoursView':'minutesView'].find('.clockpicker-tick').each(function(){var tick=$(this);tick.toggleClass('active',value===+tick.html());});return;}if(dragging||(!isHours&&value%5)){this.g.insertBefore(this.hand,this.bearing);this.g.insertBefore(this.bg,this.fg);this.bg.setAttribute('class','clockpicker-canvas-bg clockpicker-canvas-bg-trans');}else{this.g.insertBefore(this.hand,this.bg);this.g.insertBefore(this.fg,this.bg);this.bg.setAttribute('class','clockpicker-canvas-bg');}var cx=Math.sin(radian)*radius,cy=-Math.cos(radian)*radius;this.hand.setAttribute('x2',cx);this.hand.setAttribute('y2',cy);this.bg.setAttribute('cx',cx);this.bg.setAttribute('cy',cy);this.fg.setAttribute('cx',cx);this.fg.setAttribute('cy',cy);};ClockPicker.prototype.done=function(){raiseCallback(this.options.beforeDone);this.hide();var last=this.input.prop('value'),value=leadingZero(this.hours)+':'+leadingZero(this.minutes);if(this.options.twelvehour){value=value+' '+this.amOrPm;}this.input.prop('value',value);if(
value!==last){this.input.triggerHandler('change');if(!this.isInput){this.element.trigger('change');}}if(this.options.autoclose){this.input.trigger('blur');}raiseCallback(this.options.afterDone);};ClockPicker.prototype.remove=function(){this.element.removeData('clockpicker');this.input.off('focus.clockpicker click.clockpicker');this.addon.off('click.clockpicker');if(this.isShown){this.hide();}if(this.isAppended){$win.off('resize.clockpicker'+this.id);this.popover.remove();}};$.fn.clockpicker=function(option){var args=Array.prototype.slice.call(arguments,1);return this.each(function(){var $this=$(this),data=$this.data('clockpicker');if(!data){var options=$.extend({},ClockPicker.DEFAULTS,$this.data(),typeof option=='object'&&option);$this.data('clockpicker',new ClockPicker($this,options));}else{if(typeof data[option]==='function'){data[option].apply(data,args);}}});};}());+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.foundation===undefined)$.wn.foundation={}
$.wn.foundation._proxyCounter=0
var Base=function(){this.proxiedMethods={}}
Base.prototype.dispose=function(){for(var key in this.proxiedMethods){this.proxiedMethods[key]=null}this.proxiedMethods=null}
Base.prototype.proxy=function(method){if(method.ocProxyId===undefined){$.wn.foundation._proxyCounter++
method.ocProxyId=$.wn.foundation._proxyCounter}if(this.proxiedMethods[method.ocProxyId]!==undefined)return this.proxiedMethods[method.ocProxyId]
this.proxiedMethods[method.ocProxyId]=method.bind(this)
return this.proxiedMethods[method.ocProxyId]}
$.wn.foundation.base=Base;}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.foundation===undefined)$.wn.foundation={}
var Element={hasClass:function(el,className){if(el.classList)return el.classList.contains(className);return new RegExp('(^| )'+className+'( |$)','gi').test(el.className);},addClass:function(el,className){var classes=className.split(' ')
for(var i=0,len=classes.length;i<len;i++){var currentClass=classes[i].trim()
if(this.hasClass(el,currentClass))return
if(el.classList)el.classList.add(currentClass);else el.className+=' '+currentClass;}},removeClass:function(el,className){if(el.classList)el.classList.remove(className);else el.className=el.className.replace(new RegExp('(^|\\b)'+className.split(' ').join('|')+'(\\b|$)','gi'),' ');},toggleClass:function(el,className,add){if(add===undefined){if(this.hasClass(el,className)){this.removeClass(el,className)}else{this.addClass(el,className)}}if(add&&!this.hasClass(el,className)){this.addClass(el,className)
return}if(!add&&this.hasClass(el,className)){this.removeClass(el,className)
return}},absolutePosition:function(element,ignoreScrolling){var top=ignoreScrolling===true?0:document.body.scrollTop,left=0
do{top+=element.offsetTop||0;if(ignoreScrolling!==true)top-=element.scrollTop||0
left+=element.offsetLeft||0
element=element.offsetParent}while(element)return{top:top,left:left}},getCaretPosition:function(input){if(document.selection){var selection=document.selection.createRange()
selection.moveStart('character',-input.value.length)
return selection.text.length}if(input.selectionStart!==undefined)return input.selectionStart
return 0},setCaretPosition:function(input,position){if(document.selection){var range=input.createTextRange()
setTimeout(function(){range.collapse(true)
range.moveStart("character",position)
range.moveEnd("character",0)
range.select()
range=null
input=null},0)}if(input.selectionStart!==undefined){setTimeout(function(){input.selectionStart=position
input.selectionEnd=position
input=null},0)}},elementContainsPoint:function(element,point){var elementPosition=$.wn.foundation.element.absolutePosition(element),elementRight=elementPosition.left+element.offsetWidth,elementBottom=elementPosition.top+element.offsetHeight
return point.x>=elementPosition.left&&point.x<=elementRight&&point.y>=elementPosition.top&&point.y<=elementBottom}}
$.wn.foundation.element=Element;}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.foundation===undefined)$.wn.foundation={}
var Event={getTarget:function(ev,tag){var target=ev.target?ev.target:ev.srcElement
if(tag===undefined)return target
var tagName=target.tagName
while(tagName!=tag){target=target.parentNode
if(!target)return null
tagName=target.tagName}return target},stop:function(ev){if(ev.stopPropagation)ev.stopPropagation()
else ev.cancelBubble=true
if(ev.preventDefault)ev.preventDefault()
else ev.returnValue=false},pageCoordinates:function(ev){if(ev.pageX||ev.pageY){return{x:ev.pageX,y:ev.pageY}}else if(ev.clientX||ev.clientY){return{x:(ev.clientX+document.body.scrollLeft+document.documentElement.scrollLeft),y:(ev.clientY+document.body.scrollTop+document.documentElement.scrollTop)}}return{x:0,y:0}}}
$.wn.foundation.event=Event;}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.foundation===undefined)$.wn.foundation={}
var ControlUtils={markDisposable:function(el){el.setAttribute('data-disposable','')},disposeControls:function(container){var controls=container.querySelectorAll('[data-disposable]')
for(var i=0,len=controls.length;i<len;i++)$(controls[i]).triggerHandler('dispose-control')
if(container.hasAttribute('data-disposable'))$(container).triggerHandler('dispose-control')}}
$.wn.foundation.controlUtils=ControlUtils;$(document).on('ajaxBeforeReplace',function(ev){$.wn.foundation.controlUtils.disposeControls(ev.target)})}(window.jQuery);+function($){"use strict";var FlashMessage=function(options,el){var options=$.extend({},FlashMessage.DEFAULTS,options),$element=$(el)
$('body > p.flash-message').remove()
if($element.length==0){$element=$('<p />').addClass(options.class).html(options.text)}$element.addClass('flash-message fade')
$element.attr('data-control',null)
$element.append('<button type="button" class="close" aria-hidden="true">&times;</button>')
$element.on('click','button',remove)
$element.on('click',remove)
$(document.body).append($element)
setTimeout(function(){$element.addClass('in')},100)
var timer=window.setTimeout(remove,options.interval*1000)
function removeElement(){$element.remove()}function remove(){window.clearInterval(timer)
$element.removeClass('in')
$.support.transition&&$element.hasClass('fade')?$element.one($.support.transition.end,removeElement).emulateTransitionEnd(500):removeElement()}}
FlashMessage.DEFAULTS={class:'success',text:'Default text',interval:5}
if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
$.wn.flashMsg=FlashMessage
$(document).render(function(){$('[data-control=flash-message]').each(function(){$.wn.flashMsg($(this).data(),this)})})}(window.jQuery);!function($){"use strict";var Autocomplete=function(element,options){this.$element=$(element)
this.options=$.extend({},$.fn.autocomplete.defaults,options)
this.matcher=this.options.matcher||this.matcher
this.sorter=this.options.sorter||this.sorter
this.highlighter=this.options.highlighter||this.highlighter
this.updater=this.options.updater||this.updater
this.source=this.options.source
this.$menu=$(this.options.menu)
this.shown=false
this.listen()}
Autocomplete.prototype={constructor:Autocomplete,select:function(){var val=this.$menu.find('.active').attr('data-value')
this.$element.val(this.updater(val)).change()
return this.hide()},updater:function(item){return item},show:function(){var offset=this.options.bodyContainer?this.$element.offset():this.$element.position(),pos=$.extend({},offset,{height:this.$element[0].offsetHeight}),cssOptions={top:pos.top+pos.height,left:pos.left}
if(this.options.matchWidth){cssOptions.width=this.$element[0].offsetWidth}this.$menu.css(cssOptions)
if(this.options.bodyContainer){$(document.body).append(this.$menu)}else{this.$menu.insertAfter(this.$element)}this.$menu.show()
this.shown=true
return this},hide:function(){this.$menu.hide()
this.shown=false
return this},lookup:function(event){var items
this.query=this.$element.val()
if(!this.query||this.query.length<this.options.minLength){return this.shown?this.hide():this}items=$.isFunction(this.source)?this.source(this.query,$.proxy(this.process,this)):this.source
return items?this.process(items):this},itemValue:function(item){if(typeof item==='object')return item.value;return item;},itemLabel:function(item){if(typeof item==='object')return item.label;return item;},itemsToArray:function(items){var newArray=[]
$.each(items,function(value,label){newArray.push({label:label,value:value})})
return newArray},process:function(items){var that=this
if(typeof items=='object')items=this.itemsToArray(items)
items=$.grep(items,function(item){return that.matcher(item)})
items=this.sorter(items)
if(!items.length){return this.shown?this.hide():this}return this.render(items.slice(0,this.options.items)).show()},matcher:function(item){return~this.itemValue(item).toLowerCase().indexOf(this.query.toLowerCase())},sorter:function(items){var beginswith=[],caseSensitive=[],caseInsensitive=[],item,itemValue
while(item=items.shift()){itemValue=this.itemValue(item)
if(!itemValue.toLowerCase().indexOf(this.query.toLowerCase()))beginswith.push(item)
else if(~itemValue.indexOf(this.query))caseSensitive.push(item)
else caseInsensitive.push(item)}return beginswith.concat(caseSensitive,caseInsensitive)},highlighter:function(item){var query=this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,'\\$&')
return item.replace(new RegExp('('+query+')','ig'),function($1,match){return'<strong>'+match+'</strong>'})},render:function(items){var that=this
items=$(items).map(function(i,item){i=$(that.options.item).attr('data-value',that.itemValue(item))
i.find('a').html(that.highlighter(that.itemLabel(item)))
return i[0]})
items.first().addClass('active')
this.$menu.html(items)
return this},next:function(event){var active=this.$menu.find('.active').removeClass('active'),next=active.next()
if(!next.length){next=$(this.$menu.find('li')[0])}next.addClass('active')},prev:function(event){var active=this.$menu.find('.active').removeClass('active'),prev=active.prev()
if(!prev.length){prev=this.$menu.find('li').last()}prev.addClass('active')},listen:function(){this.$element.on('focus.autocomplete',$.proxy(this.focus,this)).on('blur.autocomplete',$.proxy(this.blur,this)).on('keypress.autocomplete',$.proxy(this.keypress,this)).on('keyup.autocomplete',$.proxy(this.keyup,this))
if(this.eventSupported('keydown')){this.$element.on('keydown.autocomplete',$.proxy(this.keydown,this))}this.$menu.on('click.autocomplete',$.proxy(this.click,this)).on('mouseenter.autocomplete','li',$.proxy(this.mouseenter,this)).on('mouseleave.autocomplete','li',$.proxy(this.mouseleave,this))},eventSupported:function(eventName){var isSupported=eventName in this.$element
if(!isSupported){this.$element.setAttribute(eventName,'return;')
isSupported=typeof this.$element[eventName]==='function'}return isSupported},move:function(e){if(!this.shown)return
switch(e.key){case'Tab':case'Enter':case'Escape':e.preventDefault()
break
case'ArrowUp':e.preventDefault()
this.prev()
break
case'ArrowDown':e.preventDefault()
this.next()
break}e.stopPropagation()},keydown:function(e){this.suppressKeyPressRepeat=~$.inArray(e.key,['ArrowDown','ArrowUp','Tab','Enter','Escape'])
this.move(e)},keypress:function(e){if(this.suppressKeyPressRepeat)return
this.move(e)},keyup:function(e){switch(e.keyCode){case 40:case 38:case 16:case 17:case 18:break
case 9:case 13:if(!this.shown)return
this.select()
break
case 27:if(!this.shown)return
this.hide()
break
default:this.lookup()}e.stopPropagation()
e.preventDefault()},focus:function(e){this.focused=true},blur:function(e){this.focused=false
if(!this.mousedover&&this.shown)this.hide()},click:function(e){e.stopPropagation()
e.preventDefault()
this.select()
this.$element.focus()},mouseenter:function(e){this.mousedover=true
this.$menu.find('.active').removeClass('active')
$(e.currentTarget).addClass('active')},mouseleave:function(e){this.mousedover=false
if(!this.focused&&this.shown)this.hide()},destroy:function(){this.hide()
this.$element.removeData('autocomplete')
this.$menu.remove()
this.$element.off('.autocomplete')
this.$menu.off('.autocomplete')
this.$element=null
this.$menu=null}}
var old=$.fn.autocomplete
$.fn.autocomplete=function(option){return this.each(function(){var $this=$(this),data=$this.data('autocomplete'),options=typeof option=='object'&&option
if(!data)$this.data('autocomplete',(data=new Autocomplete(this,options)))
if(typeof option=='string')data[option]()})}
$.fn.autocomplete.defaults={source:[],items:8,menu:'<ul class="autocomplete dropdown-menu"></ul>',item:'<li><a href="#"></a></li>',minLength:1,bodyContainer:false}
$.fn.autocomplete.Constructor=Autocomplete
$.fn.autocomplete.noConflict=function(){$.fn.autocomplete=old
return this}
function paramToObj(name,value){if(value===undefined)value=''
if(typeof value=='object')return value
try{return ocJSON("{"+value+"}")}catch(e){throw new Error('Error parsing the '+name+' attribute value. '+e)}}$(document).on('focus.autocomplete.data-api','[data-control="autocomplete"]',function(e){var $this=$(this)
if($this.data('autocomplete'))return
var opts=$this.data()
if(opts.source){opts.source=paramToObj('data-source',opts.source)}$this.autocomplete(opts)})}(window.jQuery);(function($){$(document).on('keypress','div.custom-checkbox',function(e){if(e.key==='(Space character)'||e.key==='Spacebar'||e.key===' '){var $cb=$('input[type=checkbox]',this)
if($cb.data('wn-space-timestamp')==e.timeStamp)return
$cb.get(0).checked=!$cb.get(0).checked
$cb.data('wn-space-timestamp',e.timeStamp)
$cb.trigger('change')
return false}})
$(document).render(function(){$('div.custom-checkbox.is-indeterminate > input').each(function(){var $el=$(this),checked=$el.data('checked')
switch(checked){case 1:$el.prop('indeterminate',true)
break
case 2:$el.prop('indeterminate',false)
$el.prop('checked',true)
break
default:$el.prop('indeterminate',false)
$el.prop('checked',false)}})})
$(document).on('click','div.custom-checkbox.is-indeterminate > label',function(){var $el=$(this).parent().find('input:first'),checked=$el.data('checked')
if(checked===undefined){checked=$el.is(':checked')?1:0}switch(checked){case 0:$el.data('checked',1)
$el.prop('indeterminate',true)
break
case 1:$el.data('checked',2)
$el.prop('indeterminate',false)
$el.prop('checked',true)
break
default:$el.data('checked',0)
$el.prop('indeterminate',false)
$el.prop('checked',false)}$el.trigger('change')
return false})})(jQuery);+function($){"use strict";var BalloonSelector=function(element,options){this.$el=$(element)
this.$field=$('input',this.$el)
this.options=options||{};var self=this;$('li',this.$el).click(function(){if(self.$el.hasClass('control-disabled')){return}$('li',self.$el).removeClass('active')
$(this).addClass('active')
self.$field.val($(this).data('value')).trigger('change')})}
BalloonSelector.DEFAULTS={}
var old=$.fn.balloonSelector
$.fn.balloonSelector=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.balloon-selector')
var options=$.extend({},BalloonSelector.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.balloon-selector',(data=new BalloonSelector(this,options)))})}
$.fn.balloonSelector.Constructor=BalloonSelector
$.fn.balloonSelector.noConflict=function(){$.fn.balloonSelector=old
return this}
$(document).on('render',function(){$('div[data-control=balloon-selector]').balloonSelector()})}(window.jQuery);+function($){"use strict";$(document).on('shown.bs.dropdown','.dropdown',function(event,relatedTarget){$(document.body).addClass('dropdown-open')
var dropdown=$(relatedTarget.relatedTarget).siblings('.dropdown-menu'),dropdownContainer=$(this).data('dropdown-container')
if(dropdown.length===0){dropdown=$('.dropdown-menu',this)}if($('.dropdown-container',dropdown).length==0){var title=$('[data-toggle=dropdown]',this).text(),titleAttr=dropdown.data('dropdown-title'),timer=null
if(titleAttr!==undefined)title=titleAttr
$('li:first-child',dropdown).addClass('first-item')
$('li:last-child',dropdown).addClass('last-item')
dropdown.prepend($('<li />').addClass('dropdown-title').text(title))
var container=$('<li />').addClass('dropdown-container'),ul=$('<ul />')
container.prepend(ul)
ul.prepend(dropdown.children())
dropdown.prepend(container)
dropdown.on('touchstart',function(){window.setTimeout(function(){dropdown.addClass('scroll')},200)})
dropdown.on('touchend',function(){window.setTimeout(function(){dropdown.removeClass('scroll')},200)})
dropdown.on('click','a',function(){if(dropdown.hasClass('scroll'))return false})}if(dropdownContainer!==undefined&&dropdownContainer=='body'){$(this).data('oc.dropdown',dropdown)
$(document.body).append(dropdown)
dropdown.css({'visibility':'hidden','left':0,'top':0,'display':'block'})
var targetOffset=$(this).offset(),targetHeight=$(this).height(),targetWidth=$(this).width(),position={x:targetOffset.left,y:targetOffset.top+targetHeight},leftOffset=targetWidth<30?-16:0,documentHeight=$(document).height(),dropdownHeight=dropdown.height()
if((dropdownHeight+position.y)>$(document).height()){position.y=targetOffset.top-dropdownHeight-12
dropdown.addClass('top')}else{dropdown.removeClass('top')}dropdown.css({'left':position.x+leftOffset,'top':position.y,'visibility':'visible'})}if($('.dropdown-overlay',document.body).length==0){$(document.body).prepend($('<div/>').addClass('dropdown-overlay'));}})
$(document).on('hidden.bs.dropdown','.dropdown',function(){var dropdown=$(this).data('oc.dropdown')
if(dropdown!==undefined){dropdown.css('display','none')
$(this).append(dropdown)}$(document.body).removeClass('dropdown-open');})
var $dropdown,$container,$target
function fixDropdownPosition(){var position=$container.offset()
$dropdown.css({position:'fixed',top:position.top-1-$(window).scrollTop()+$target.outerHeight(),left:position.left})}$(document).on('shown.bs.dropdown','.dropdown.dropdown-fixed',function(event,eventData){$container=$(this)
$dropdown=$('.dropdown-menu',$container)
$target=$(eventData.relatedTarget)
fixDropdownPosition()
$(window).on('scroll.oc.dropdown, resize.oc.dropdown',fixDropdownPosition)})
$(document).on('hidden.bs.dropdown','.dropdown.dropdown-fixed',function(){$(window).off('scroll.oc.dropdown, resize.oc.dropdown',fixDropdownPosition)})}(window.jQuery);+function($){'use strict';var dismiss='[data-dismiss="callout"]'
var Callout=function(el){$(el).on('click',dismiss,this.close)}
Callout.prototype.close=function(e){var $this=$(this)
var selector=$this.attr('data-target')
if(!selector){selector=$this.attr('href')
selector=selector&&selector.replace(/.*(?=#[^\s]*$)/,'')}var $parent=$(selector)
if(e)e.preventDefault()
if(!$parent.length){$parent=$this.hasClass('callout')?$this:$this.parent()}$parent.trigger(e=$.Event('close.oc.callout'))
if(e.isDefaultPrevented())return
$parent.removeClass('in')
function removeElement(){$parent.trigger('closed.oc.callout').remove()}$.support.transition&&$parent.hasClass('fade')?$parent.one($.support.transition.end,removeElement).emulateTransitionEnd(500):removeElement()}
var old=$.fn.callout
$.fn.callout=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.callout')
if(!data)$this.data('oc.callout',(data=new Callout(this)))
if(typeof option=='string')data[option].call($this)})}
$.fn.callout.Constructor=Callout
$.fn.callout.noConflict=function(){$.fn.callout=old
return this}
$(document).on('click.oc.callout.data-api',dismiss,Callout.prototype.close)}(jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var DatePicker=function(element,options){this.$el=$(element)
this.options=options||{}
$.wn.foundation.controlUtils.markDisposable(element)
Base.call(this)
this.init()}
DatePicker.prototype=Object.create(BaseProto)
DatePicker.prototype.constructor=DatePicker
DatePicker.prototype.init=function(){var self=this,$form=this.$el.closest('form'),changeMonitor=$form.data('oc.changeMonitor')
if(changeMonitor!==undefined){changeMonitor.pause()}this.dbDateTimeFormat='YYYY-MM-DD HH:mm:ss'
this.dbDateFormat='YYYY-MM-DD'
this.dbTimeFormat='HH:mm:ss'
this.$dataLocker=$('[data-datetime-value]',this.$el)
this.$datePicker=$('[data-datepicker]',this.$el)
this.$timePicker=$('[data-timepicker]',this.$el)
this.hasDate=!!this.$datePicker.length
this.hasTime=!!this.$timePicker.length
this.ignoreTimezone=this.$el.get(0).hasAttribute('data-ignore-timezone')
this.initRegion()
if(this.hasDate){this.initDatePicker()}if(this.hasTime){this.initTimePicker()}if(changeMonitor!==undefined){changeMonitor.resume()}this.$timePicker.on('change.oc.datepicker',function(){if(!$.trim($(this).val())){self.emptyValues()}else{self.onSelectTimePicker()}})
this.$datePicker.on('change.oc.datepicker',function(){if(!$.trim($(this).val())){self.emptyValues()}})
this.$el.one('dispose-control',this.proxy(this.dispose))}
DatePicker.prototype.dispose=function(){this.$timePicker.off('change.oc.datepicker')
this.$datePicker.off('change.oc.datepicker')
this.$el.off('dispose-control',this.proxy(this.dispose))
this.$el.removeData('oc.datePicker')
this.$el=null
this.options=null
BaseProto.dispose.call(this)}
DatePicker.prototype.initDatePicker=function(){var self=this,dateFormat=this.getDateFormat(),now=moment().tz(this.timezone).format(dateFormat)
var pikadayOptions={yearRange:this.options.yearRange,firstDay:this.options.firstDay,showWeekNumber:this.options.showWeekNumber,format:dateFormat,setDefaultDate:now,onOpen:function(){var $field=$(this._o.trigger)
$(this.el).css({left:'auto',right:$(window).width()-$field.offset().left-$field.outerWidth()})},onSelect:function(){self.onSelectDatePicker.call(self,this.getMoment())}}
var lang=this.getLang('datepicker',false)
if(lang){pikadayOptions.i18n=lang}this.$datePicker.val(this.getDataLockerValue(dateFormat))
if(this.options.minDate){pikadayOptions.minDate=new Date(this.options.minDate)}if(this.options.maxDate){pikadayOptions.maxDate=new Date(this.options.maxDate)}this.$datePicker.pikaday(pikadayOptions)}
DatePicker.prototype.onSelectDatePicker=function(pickerMoment){var pickerValue=pickerMoment.format(this.dbDateFormat)
var timeValue=this.options.mode==='date'?'00:00:00':this.getTimePickerValue()
var momentObj=moment.tz(pickerValue+' '+timeValue,this.dbDateTimeFormat,this.timezone).tz(this.appTimezone)
var lockerValue=momentObj.format(this.dbDateTimeFormat)
this.$dataLocker.val(lockerValue)}
DatePicker.prototype.getDatePickerValue=function(){var value=this.$datePicker.val()
if(!this.hasDate||!value){return moment.tz(this.appTimezone).tz(this.timezone).format(this.dbDateFormat)}return moment(value,this.getDateFormat()).format(this.dbDateFormat)}
DatePicker.prototype.getDateFormat=function(){var format='YYYY-MM-DD'
if(this.options.format){format=this.options.format}else if(this.locale){format=moment().locale(this.locale).localeData().longDateFormat('l')}return format}
DatePicker.prototype.initTimePicker=function(){this.$timePicker.clockpicker({autoclose:'true',placement:'auto',align:'right',twelvehour:this.isTimeTwelveHour(),afterDone:this.proxy(this.onChangeTimePicker)})
this.$timePicker.val(this.getDataLockerValue(this.getTimeFormat()))}
DatePicker.prototype.onSelectTimePicker=function(){var pickerValue=this.$timePicker.val()
var timeValue=moment(pickerValue,this.getTimeFormat()).format(this.dbTimeFormat)
var dateValue=this.getDatePickerValue()
var momentObj=moment.tz(dateValue+' '+timeValue,this.dbDateTimeFormat,this.timezone).tz(this.appTimezone)
var lockerValue=momentObj.format(this.dbDateTimeFormat)
this.$dataLocker.val(lockerValue)}
DatePicker.prototype.onChangeTimePicker=function(){this.$timePicker.trigger('change')}
DatePicker.prototype.getTimePickerValue=function(){var value=this.$timePicker.val()
if(!this.hasTime||!value){return moment.tz(this.appTimezone).tz(this.timezone).format(this.dbTimeFormat)}return moment(value,this.getTimeFormat()).format(this.dbTimeFormat)}
DatePicker.prototype.getTimeFormat=function(){return this.isTimeTwelveHour()?'hh:mm A':'HH:mm'}
DatePicker.prototype.isTimeTwelveHour=function(){return false}
DatePicker.prototype.emptyValues=function(){this.$dataLocker.val('')
this.$datePicker.val('')
this.$timePicker.val('')}
DatePicker.prototype.getDataLockerValue=function(format){var value=this.$dataLocker.val()
return value?this.getMomentLoadValue(value,format):null}
DatePicker.prototype.getMomentLoadValue=function(value,format){var momentObj=moment.tz(value,this.appTimezone)
if(this.locale){momentObj=momentObj.locale(this.locale)}momentObj=momentObj.tz(this.timezone)
return momentObj.format(format)}
DatePicker.prototype.initRegion=function(){this.locale=$('meta[name="backend-locale"]').attr('content')
this.timezone=$('meta[name="backend-timezone"]').attr('content')
this.appTimezone=$('meta[name="app-timezone"]').attr('content')
if(!this.appTimezone){this.appTimezone='UTC'}if(!this.timezone){this.timezone='UTC'}if(this.ignoreTimezone){this.appTimezone='UTC'
this.timezone='UTC'}}
DatePicker.prototype.getLang=function(name,defaultValue){if($.oc===undefined||$.wn.lang===undefined){return defaultValue}return $.wn.lang.get(name,defaultValue)}
DatePicker.DEFAULTS={minDate:null,maxDate:null,format:null,yearRange:10,firstDay:0,showWeekNumber:false,mode:'datetime'}
var old=$.fn.datePicker
$.fn.datePicker=function(option){var args=Array.prototype.slice.call(arguments,1),items,result
items=this.each(function(){var $this=$(this)
var data=$this.data('oc.datePicker')
var options=$.extend({},DatePicker.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.datePicker',(data=new DatePicker(this,options)))
if(typeof option=='string')result=data[option].apply(data,args)
if(typeof result!='undefined')return false})
return result?result:items}
$.fn.datePicker.Constructor=DatePicker
$.fn.datePicker.noConflict=function(){$.fn.datePicker=old
return this}
$(document).on('render',function(){$('[data-control="datepicker"]').datePicker()});}(window.jQuery);(function($){$(document).render(function(){$('[data-control="tooltip"], [data-toggle="tooltip"]').tooltip()})})(jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var Toolbar=function(element,options){var $el=this.$el=$(element),$toolbar=$el.closest('.control-toolbar')
$.wn.foundation.controlUtils.markDisposable(element)
this.$toolbar=$toolbar
this.options=options||{};var noDragSupport=options.noDragSupport!==undefined&&options.noDragSupport
Base.call(this)
var scrollClassContainer=options.scrollClassContainer!==undefined?options.scrollClassContainer:$el.parent()
if(this.options.useNativeDrag){$el.addClass('is-native-drag')}$el.dragScroll({scrollClassContainer:scrollClassContainer,useDrag:!noDragSupport,useNative:this.options.useNativeDrag})
$('.form-control.growable',$toolbar).on('focus.toolbar',function(){update()})
$('.form-control.growable',$toolbar).on('blur.toolbar',function(){update()})
this.$el.one('dispose-control',this.proxy(this.dispose))
function update(){$(window).trigger('resize')}}
Toolbar.prototype=Object.create(BaseProto)
Toolbar.prototype.constructor=Toolbar
Toolbar.prototype.dispose=function(){this.$el.off('dispose-control',this.proxy(this.dispose))
$('.form-control.growable',this.$toolbar).off('.toolbar')
this.$el.dragScroll('dispose')
this.$el.removeData('oc.toolbar')
this.$el=null
BaseProto.dispose.call(this)}
Toolbar.DEFAULTS={useNativeDrag:false}
var old=$.fn.toolbar
$.fn.toolbar=function(option){var args=Array.prototype.slice.call(arguments,1)
return this.each(function(){var $this=$(this)
var data=$this.data('oc.toolbar')
var options=$.extend({},Toolbar.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.toolbar',(data=new Toolbar(this,options)))
if(typeof option=='string')data[option].apply(data,args)})}
$.fn.toolbar.Constructor=Toolbar
$.fn.toolbar.noConflict=function(){$.fn.toolbar=old
return this}
$(document).on('render',function(){$('[data-control=toolbar]').toolbar()})}(window.jQuery);+function($){"use strict";var FilterWidget=function(element,options){this.$el=$(element);this.options=options||{}
this.scopeValues={}
this.scopeAvailable={}
this.$activeScope=null
this.activeScopeName=null
this.isActiveScopeDirty=false
this.dependantUpdateInterval=300
this.dependantUpdateTimers={}
this.init()}
FilterWidget.DEFAULTS={optionsHandler:null,updateHandler:null}
FilterWidget.prototype.getPopoverTemplate=function(){return'                                                                                                       \
                <form id="filterPopover-{{ scopeName }}">                                                              \
                    <input type="hidden" name="scopeName"  value="{{ scopeName }}" />                                  \
                    <div id="controlFilterPopover" class="control-filter-popover control-filter-box-popover --range">  \
                        <div class="filter-search loading-indicator-container size-input-text">                        \
                            <button class="close" data-dismiss="popover" type="button">&times;</button>                \
                            <input                                                                                     \
                                type="text"                                                                            \
                                name="search"                                                                          \
                                autocomplete="off"                                                                     \
                                class="filter-search-input form-control icon search popup-allow-focus"                 \
                                data-search />                                                                         \
                            <div class="filter-items">                                                                 \
                                <ul>                                                                                   \
                                    {{#available}}                                                                     \
                                        <li data-item-id="{{id}}"><a href="javascript:;">{{name}}</a></li>             \
                                    {{/available}}                                                                     \
                                    {{#loading}}                                                                       \
                                        <li class="loading"><span></span></li>                                         \
                                    {{/loading}}                                                                       \
                                </ul>                                                                                  \
                            </div>                                                                                     \
                            <div class="filter-active-items">                                                          \
                                <ul>                                                                                   \
                                    {{#active}}                                                                        \
                                        <li data-item-id="{{id}}"><a href="javascript:;">{{name}}</a></li>             \
                                    {{/active}}                                                                        \
                                </ul>                                                                                  \
                            </div>                                                                                     \
                            <div class="filter-buttons">                                                               \
                                <button class="btn btn-block btn-primary wn-icon-filter" data-filter-action="apply">   \
                                    {{ apply_button_text }}                                                            \
                                </button>                                                                              \
                                <button class="btn btn-block btn-secondary wn-icon-eraser" data-filter-action="clear"> \
                                    {{ clear_button_text }}                                                            \
                                </button>                                                                              \
                            </div>                                                                                     \
                        </div>                                                                                         \
                    </div>                                                                                             \
                </form>                                                                                                \
            '
}
FilterWidget.prototype.init=function(){var self=this
this.bindDependants()
this.$el.on('change','.filter-scope input[type="checkbox"]',function(){var $scope=$(this).closest('.filter-scope')
if($scope.hasClass('is-indeterminate')){self.switchToggle($(this))}else{self.checkboxToggle($(this))}})
$('.filter-scope input[type="checkbox"]',this.$el).each(function(){$(this).closest('.filter-scope').toggleClass('active',$(this).is(':checked'))})
this.$el.on('click','a.filter-scope',function(){var $scope=$(this),scopeName=$scope.data('scope-name')
if($scope.hasClass('filter-scope-open'))return
self.$activeScope=$scope
self.activeScopeName=scopeName
self.isActiveScopeDirty=false
self.displayPopover($scope)
$scope.addClass('filter-scope-open')})
this.$el.on('show.oc.popover','a.filter-scope',function(event){self.focusSearch()
$(event.relatedTarget).on('click','#controlFilterPopover .filter-items > ul > li',function(){self.selectItem($(this))})
$(event.relatedTarget).on('click','#controlFilterPopover .filter-active-items > ul > li',function(){self.selectItem($(this),true)})
$(event.relatedTarget).on('ajaxDone','#controlFilterPopover input.filter-search-input',function(event,context,data){self.filterAvailable(data.scopeName,data.options.available)})
$(event.relatedTarget).on('click','#controlFilterPopover [data-filter-action="apply"]',function(e){e.preventDefault()
self.filterScope()})
$(event.relatedTarget).on('click','#controlFilterPopover [data-filter-action="clear"]',function(e){e.preventDefault()
self.filterScope(true)})
$(event.relatedTarget).on('input','#controlFilterPopover input[data-search]',function(e){self.searchQuery($(this))})})
this.$el.on('hide.oc.popover','a.filter-scope',function(){var $scope=$(this)
self.pushOptions(self.activeScopeName)
self.activeScopeName=null
self.$activeScope=null
setTimeout(function(){$scope.removeClass('filter-scope-open')},200)})}
FilterWidget.prototype.bindDependants=function(){if(!$('[data-scope-depends]',this.$el).length){return}var self=this,scopeMap={},scopeElements=this.$el.find('.filter-scope')
scopeElements.filter('[data-scope-depends]').each(function(){var name=$(this).data('scope-name'),depends=$(this).data('scope-depends')
$.each(depends,function(index,depend){if(!scopeMap[depend]){scopeMap[depend]={scopes:[]}}scopeMap[depend].scopes.push(name)})})
$.each(scopeMap,function(scopeName,toRefresh){scopeElements.filter('[data-scope-name="'+scopeName+'"]').on('change.oc.filterScope',$.proxy(self.onRefreshDependants,self,scopeName,toRefresh))})}
FilterWidget.prototype.onRefreshDependants=function(scopeName,toRefresh){var self=this,scopeElements=this.$el.find('.filter-scope')
if(this.dependantUpdateTimers[scopeName]!==undefined){window.clearTimeout(this.dependantUpdateTimers[scopeName])}this.dependantUpdateTimers[scopeName]=window.setTimeout(function(){$.each(toRefresh.scopes,function(index,dependantScope){self.scopeValues[dependantScope]=null
var $scope=self.$el.find('[data-scope-name="'+dependantScope+'"]')
self.$el.request(self.options.optionsHandler,{data:{scopeName:dependantScope},success:function(data){self.fillOptions(dependantScope,data.options)
self.updateScopeSetting($scope,data.options.active.length)
$scope.loadIndicator('hide')}})})},this.dependantUpdateInterval)
$.each(toRefresh.scopes,function(index,scope){scopeElements.filter('[data-scope-name="'+scope+'"]').addClass('loading-indicator-container').loadIndicator()})}
FilterWidget.prototype.focusSearch=function(){if(Modernizr.touchevents)return
var $input=$('#controlFilterPopover input.filter-search-input'),length=$input.val().length
$input.focus()
$input.get(0).setSelectionRange(length,length)}
FilterWidget.prototype.updateScopeSetting=function($scope,amount){var $setting=$scope.find('.filter-setting')
if(amount){$setting.text(amount)
$scope.addClass('active')}else{$setting.text(this.getLang('filter.group.all','all'))
$scope.removeClass('active')}}
FilterWidget.prototype.selectItem=function($item,isDeselect){var $otherContainer=isDeselect?$item.closest('.control-filter-popover').find('.filter-items:first > ul'):$item.closest('.control-filter-popover').find('.filter-active-items:first > ul')
$item.addClass('animate-enter').prependTo($otherContainer).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',function(){$(this).removeClass('animate-enter')})
if(!this.scopeValues[this.activeScopeName])return
var itemId=$item.data('item-id'),active=this.scopeValues[this.activeScopeName],available=this.scopeAvailable[this.activeScopeName],fromItems=isDeselect?active:available,toItems=isDeselect?available:active,testFunc=function(active){return active.id==itemId},item=($.grep(fromItems,testFunc).pop()??{'id':itemId,'name':$item.text()}),filtered=$.grep(fromItems,testFunc,true)
if(isDeselect){this.scopeValues[this.activeScopeName]=filtered
this.scopeAvailable[this.activeScopeName].push(item)}else{this.scopeAvailable[this.activeScopeName]=filtered
this.scopeValues[this.activeScopeName].push(item)}this.toggleFilterButtons(active)
this.updateScopeSetting(this.$activeScope,isDeselect?filtered.length:active.length)
this.isActiveScopeDirty=true
this.focusSearch()}
FilterWidget.prototype.displayPopover=function($scope){var self=this,scopeName=$scope.data('scope-name'),data=null,isLoaded=true,container=false
if(typeof this.scopeAvailable[scopeName]!=="undefined"&&this.scopeAvailable[scopeName]){data=$.extend({},data,{available:this.scopeAvailable[scopeName],active:this.scopeValues[scopeName]})}var modalParent=$scope.parents('.modal-dialog')
if(modalParent.length>0){container=modalParent[0]}if(!data){data={loading:true}
isLoaded=false}data=$.extend({},data,{apply_button_text:this.getLang('filter.scopes.apply_button_text','Apply'),clear_button_text:this.getLang('filter.scopes.clear_button_text','Clear')})
data.scopeName=scopeName
data.optionsHandler=self.options.optionsHandler
$scope.data('oc.popover',null)
$scope.ocPopover({content:Mustache.render(self.getPopoverTemplate(),data),modal:false,highlightModalTarget:true,closeOnPageClick:true,placement:'bottom',container:container})
this.toggleFilterButtons()
if(!isLoaded){self.loadOptions(scopeName)}}
FilterWidget.prototype.loadOptions=function(scopeName){var self=this,data={scopeName:scopeName}
var populated=this.$el.data('filterScopes')
if(populated&&populated[scopeName]){self.fillOptions(scopeName,populated[scopeName])
return false}return this.$el.request(this.options.optionsHandler,{data:data,success:function(data){self.fillOptions(scopeName,data.options)
self.toggleFilterButtons()}})}
FilterWidget.prototype.fillOptions=function(scopeName,data){if(this.scopeValues[scopeName])return
if(!data.active)data.active=[]
if(!data.available)data.available=[]
this.scopeValues[scopeName]=data.active
this.scopeAvailable[scopeName]=data.available
if(scopeName!=this.activeScopeName)return
var container=$('#controlFilterPopover .filter-items > ul').empty()
this.addItemsToListElement(container,data.available)
var container=$('#controlFilterPopover .filter-active-items > ul')
this.addItemsToListElement(container,data.active)}
FilterWidget.prototype.filterAvailable=function(scopeName,available){if(this.activeScopeName!=scopeName)return
if(!this.scopeValues[this.activeScopeName])return
var self=this,filtered=[],items=this.scopeValues[scopeName]
if(items.length){var activeIds=[]
$.each(items,function(key,obj){activeIds.push(obj.id)})
filtered=$.grep(available,function(item){return $.inArray(item.id,activeIds)===-1})}else{filtered=available}var container=$('#controlFilterPopover .filter-items > ul').empty()
self.addItemsToListElement(container,filtered)}
FilterWidget.prototype.addItemsToListElement=function($ul,items){$.each(items,function(key,obj){var item=$('<li />').data({'item-id':obj.id}).append($('<a />').prop({'href':'javascript:;',}).text(obj.name))
$ul.append(item)})}
FilterWidget.prototype.toggleFilterButtons=function(data){var items=$('#controlFilterPopover .filter-active-items > ul'),buttonContainer=$('#controlFilterPopover .filter-buttons')
if(data){data.length>0?buttonContainer.show():buttonContainer.hide()}else{items.children().length>0?buttonContainer.show():buttonContainer.hide()}}
FilterWidget.prototype.pushOptions=function(scopeName){if(!this.isActiveScopeDirty||!this.options.updateHandler)return
var self=this,data={scopeName:scopeName,options:JSON.stringify(this.scopeValues[scopeName])}
$.wn.stripeLoadIndicator.show()
this.$el.request(this.options.updateHandler,{data:data}).always(function(){$.wn.stripeLoadIndicator.hide()}).done(function(){self.$el.find('[data-scope-name="'+scopeName+'"]').trigger('change.oc.filterScope')})}
FilterWidget.prototype.checkboxToggle=function($el){var isChecked=$el.is(':checked'),$scope=$el.closest('.filter-scope'),scopeName=$scope.data('scope-name')
this.scopeValues[scopeName]=isChecked
if(this.options.updateHandler){var data={scopeName:scopeName,value:isChecked}
$.wn.stripeLoadIndicator.show()
this.$el.request(this.options.updateHandler,{data:data}).always(function(){$.wn.stripeLoadIndicator.hide()})}$scope.toggleClass('active',isChecked)}
FilterWidget.prototype.switchToggle=function($el){var switchValue=$el.data('checked'),$scope=$el.closest('.filter-scope'),scopeName=$scope.data('scope-name')
this.scopeValues[scopeName]=switchValue
if(this.options.updateHandler){var data={scopeName:scopeName,value:switchValue}
$.wn.stripeLoadIndicator.show()
this.$el.request(this.options.updateHandler,{data:data}).always(function(){$.wn.stripeLoadIndicator.hide()})}$scope.toggleClass('active',!!switchValue)}
FilterWidget.prototype.filterScope=function(isReset){var scopeName=this.$activeScope.data('scope-name')
if(isReset){this.scopeValues[scopeName]=null
this.scopeAvailable[scopeName]=null
this.isActiveScopeDirty=true
this.updateScopeSetting(this.$activeScope,0)}this.pushOptions(scopeName)
this.isActiveScopeDirty=false
this.$activeScope.data('oc.popover').hide()}
FilterWidget.prototype.getLang=function(name,defaultValue){if($.oc===undefined||$.wn.lang===undefined){return defaultValue}return $.wn.lang.get(name,defaultValue)}
FilterWidget.prototype.searchQuery=function($el){if(this.dataTrackInputTimer!==undefined){window.clearTimeout(this.dataTrackInputTimer)}var self=this
this.dataTrackInputTimer=window.setTimeout(function(){var lastValue=$el.data('oc.lastvalue'),thisValue=$el.val()
if(lastValue!==undefined&&lastValue==thisValue){return}$el.data('oc.lastvalue',thisValue)
if(self.lastDataTrackInputRequest){self.lastDataTrackInputRequest.abort()}var data={scopeName:self.activeScopeName,search:thisValue}
$.wn.stripeLoadIndicator.show()
self.lastDataTrackInputRequest=self.$el.request(self.options.optionsHandler,{data:data}).success(function(data){self.filterAvailable(self.activeScopeName,data.options.available)
self.toggleFilterButtons()}).always(function(){$.wn.stripeLoadIndicator.hide()})},300)}
var old=$.fn.filterWidget
$.fn.filterWidget=function(option){var args=arguments,result
this.each(function(){var $this=$(this)
var data=$this.data('oc.filterwidget')
var options=$.extend({},FilterWidget.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.filterwidget',(data=new FilterWidget(this,options)))
if(typeof option=='string')result=data[option].call($this)
if(typeof result!='undefined')return false})
return result?result:this}
$.fn.filterWidget.Constructor=FilterWidget
$.fn.filterWidget.noConflict=function(){$.fn.filterWidget=old
return this}
$(document).render(function(){$('[data-control="filterwidget"]').filterWidget();})}(window.jQuery);+function($){"use strict";var FilterWidget=$.fn.filterWidget.Constructor;var overloaded_init=FilterWidget.prototype.init;FilterWidget.prototype.init=function(){overloaded_init.apply(this)
var self=this;this.$el.children().each(function(key,$filter){if($filter.hasAttribute('data-ignore-timezone')){self.ignoreTimezone=true;}});this.initRegion()
this.initFilterDate()}
FilterWidget.prototype.initFilterDate=function(){var self=this
this.$el.on('show.oc.popover','a.filter-scope-date',function(event){self.initDatePickers($(this).hasClass('range'))
$(event.relatedTarget).on('click','#controlFilterPopoverDate [data-filter-action="filter"]',function(e){e.preventDefault()
e.stopPropagation()
self.filterByDate()})
$(event.relatedTarget).on('click','#controlFilterPopoverDate [data-filter-action="clear"]',function(e){e.preventDefault()
e.stopPropagation()
self.filterByDate(true)})})
this.$el.on('hiding.oc.popover','a.filter-scope-date',function(){self.clearDatePickers()})
this.$el.on('hide.oc.popover','a.filter-scope-date',function(){var $scope=$(this)
self.pushOptions(self.activeScopeName)
self.activeScopeName=null
self.$activeScope=null
setTimeout(function(){$scope.removeClass('filter-scope-open')},200)})
this.$el.on('click','a.filter-scope-date',function(){var $scope=$(this),scopeName=$scope.data('scope-name')
if($scope.hasClass('filter-scope-open'))return
if(null!==self.activeScopeName)return
self.$activeScope=$scope
self.activeScopeName=scopeName
self.isActiveScopeDirty=false
if($scope.hasClass('range')){self.displayPopoverRange($scope)}else{self.displayPopoverDate($scope)}$scope.addClass('filter-scope-open')})}
FilterWidget.prototype.getPopoverDateTemplate=function(){return'                                                                                                        \
                <form id="controlFilterPopoverDate-{{ scopeName }}">                                                    \
                    <input type="hidden" name="scopeName" value="{{ scopeName }}" />                                    \
                    <div id="controlFilterPopoverDate" class="control-filter-popover control-filter-box-popover">       \
                        <div class="filter-search loading-indicator-container size-input-text">                         \
                            <div class="field-datepicker">                                                              \
                                <div class="input-with-icon right-align">                                               \
                                    <i class="icon icon-calendar-o"></i>                                                \
                                    <input                                                                              \
                                        type="text"                                                                     \
                                        name="date"                                                                     \
                                        value="{{ date }}"                                                              \
                                        class="form-control align-right popup-allow-focus"                              \
                                        autocomplete="off"                                                              \
                                        placeholder="{{ date_placeholder }}" />                                         \
                                </div>                                                                                  \
                            </div>                                                                                      \
                            <div class="filter-buttons">                                                                \
                                <button class="btn btn-block btn-secondary" data-filter-action="clear">                 \
                                    {{ reset_button_text }}                                                             \
                                </button>                                                                               \
                            </div>                                                                                      \
                        </div>                                                                                          \
                    </div>                                                                                              \
                </form>                                                                                                 \
            '
}
FilterWidget.prototype.getPopoverRangeTemplate=function(){return'                                                                                                          \
                <form id="controlFilterPopoverRange-{{ scopeName }}">                                                     \
                    <input type="hidden" name="scopeName" value="{{ scopeName }}" />                                      \
                    <div id="controlFilterPopoverDate" class="control-filter-popover control-filter-box-popover --range"> \
                        <div class="filter-search loading-indicator-container size-input-text">                           \
                            <div class="field-datepicker">                                                                \
                                <div class="input-with-icon right-align">                                                 \
                                    <i class="icon icon-calendar-o"></i>                                                  \
                                    <input                                                                                \
                                        type="text"                                                                       \
                                        name="date"                                                                       \
                                        value="{{ date }}"                                                                \
                                        class="form-control align-right popup-allow-focus"                                \
                                        autocomplete="off"                                                                \
                                        placeholder="{{ after_placeholder }}" />                                          \
                                </div>                                                                                    \
                            </div>                                                                                        \
                            <div class="field-datepicker">                                                                \
                                <div class="input-with-icon right-align">                                                 \
                                    <i class="icon icon-calendar-o"></i>                                                  \
                                    <input                                                                                \
                                        type="text"                                                                       \
                                        name="date"                                                                       \
                                        value="{{ date }}"                                                                \
                                        class="form-control align-right popup-allow-focus"                                \
                                        autocomplete="off"                                                                \
                                        placeholder="{{ before_placeholder }}" />                                         \
                                </div>                                                                                    \
                            </div>                                                                                        \
                            <div class="filter-buttons">                                                                  \
                                <button class="btn btn-block btn-primary" data-filter-action="filter">                    \
                                    {{ filter_button_text }}                                                              \
                                </button>                                                                                 \
                                <button class="btn btn-block btn-secondary" data-filter-action="clear">                   \
                                    {{ reset_button_text }}                                                               \
                                </button>                                                                                 \
                            </div>                                                                                        \
                        </div>                                                                                            \
                    </div>                                                                                                \
                </form>                                                                                                   \
            '
}
FilterWidget.prototype.displayPopoverDate=function($scope){var self=this,scopeName=$scope.data('scope-name'),data=this.scopeValues[scopeName]
data=$.extend({},data,{filter_button_text:this.getLang('filter.dates.filter_button_text'),reset_button_text:this.getLang('filter.dates.reset_button_text'),date_placeholder:this.getLang('filter.dates.date_placeholder','Date')})
data.scopeName=scopeName
$scope.data('oc.popover',null)
$scope.ocPopover({content:Mustache.render(this.getPopoverDateTemplate(),data),modal:false,highlightModalTarget:true,closeOnPageClick:true,placement:'bottom',onCheckDocumentClickTarget:function(target){return self.onCheckDocumentClickTargetDatePicker(target)}})}
FilterWidget.prototype.displayPopoverRange=function($scope){var self=this,scopeName=$scope.data('scope-name'),data=this.scopeValues[scopeName]
data=$.extend({},data,{filter_button_text:this.getLang('filter.dates.filter_button_text'),reset_button_text:this.getLang('filter.dates.reset_button_text'),after_placeholder:this.getLang('filter.dates.after_placeholder','After'),before_placeholder:this.getLang('filter.dates.before_placeholder','Before')})
data.scopeName=scopeName
$scope.data('oc.popover',null)
$scope.ocPopover({content:Mustache.render(this.getPopoverRangeTemplate(),data),modal:false,highlightModalTarget:true,closeOnPageClick:true,placement:'bottom',onCheckDocumentClickTarget:function(target){return self.onCheckDocumentClickTargetDatePicker(target)}})}
FilterWidget.prototype.initDatePickers=function(isRange){var self=this,scopeData=this.$activeScope.data('scope-data'),$inputs=$('.field-datepicker input','#controlFilterPopoverDate'),data=this.scopeValues[this.activeScopeName]
if(!data){data={dates:isRange?(scopeData.dates?scopeData.dates:[]):(scopeData.date?[scopeData.date]:[])}}$inputs.each(function(index,datepicker){var defaultValue='',$datepicker=$(datepicker),defaults={minDate:new Date(scopeData.minDate),maxDate:new Date(scopeData.maxDate),firstDay:scopeData.firstDay,yearRange:scopeData.yearRange,setDefaultDate:''!==defaultValue?defaultValue.toDate():'',format:self.getDateFormat(),i18n:self.getLang('datepicker')}
if(0<=index&&index<data.dates.length){defaultValue=data.dates[index]?moment.tz(data.dates[index],self.appTimezone).tz(self.timezone):''}if(!isRange){defaults.onSelect=function(){self.filterByDate()}}datepicker.value=''!==defaultValue?defaultValue.format(self.getDateFormat()):'';$datepicker.pikaday(defaults)})}
FilterWidget.prototype.clearDatePickers=function(){var $inputs=$('.field-datepicker input','#controlFilterPopoverDate')
$inputs.each(function(index,datepicker){var $datepicker=$(datepicker)
$datepicker.data('pikaday').destroy()})}
FilterWidget.prototype.updateScopeDateSetting=function($scope,dates){var $setting=$scope.find('.filter-setting'),dateFormat=this.getDateFormat(),dateRegex=/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/,reset=false
if(dates&&dates.length){dates[0]=dates[0]&&dates[0].match(dateRegex)?dates[0]:null
if(dates.length>1){dates[1]=dates[1]&&dates[1].match(dateRegex)?dates[1]:null
if(dates[0]||dates[1]){var after=dates[0]?moment.tz(dates[0],this.appTimezone).tz(this.timezone).format(dateFormat):'-∞',before=dates[1]?moment.tz(dates[1],this.appTimezone).tz(this.timezone).format(dateFormat):'∞'
$setting.text(after+' → '+before)}else{reset=true}}else if(dates[0]){$setting.text(moment.tz(dates[0],this.appTimezone).tz(this.timezone).format(dateFormat))}else{reset=true}}else{reset=true}if(reset){$setting.text(this.getLang('filter.dates.all','all'));$scope.removeClass('active')}else{$scope.addClass('active')}}
FilterWidget.prototype.filterByDate=function(isReset){var self=this,dates=[]
if(!isReset){var datepickers=$('.field-datepicker input','#controlFilterPopoverDate')
datepickers.each(function(index,datepicker){var date=$(datepicker).data('pikaday').toString('YYYY-MM-DD')
if(date.match(/\d{4}-\d{2}-\d{2}/)){if(index===0){date+=' 00:00:00'}else if(index===1){date+=' 23:59:59'}date=moment.tz(date,self.timezone).tz(self.appTimezone).format('YYYY-MM-DD HH:mm:ss')}else{date=null}dates.push(date)})}this.updateScopeDateSetting(this.$activeScope,dates);this.scopeValues[this.activeScopeName]={dates:dates}
this.isActiveScopeDirty=true;this.$activeScope.data('oc.popover').hide()}
FilterWidget.prototype.getDateFormat=function(){if(this.locale){return moment().locale(this.locale).localeData().longDateFormat('l')}return'YYYY-MM-DD'}
FilterWidget.prototype.onCheckDocumentClickTargetDatePicker=function(target){var $target=$(target)
return $target.hasClass('pika-next')||$target.hasClass('pika-prev')||$target.hasClass('pika-select')||$target.hasClass('pika-button')||$target.parents('.pika-table').length||$target.parents('.pika-title').length}
FilterWidget.prototype.initRegion=function(){this.locale=$('meta[name="backend-locale"]').attr('content')
this.timezone=$('meta[name="backend-timezone"]').attr('content')
this.appTimezone=$('meta[name="app-timezone"]').attr('content')
if(!this.appTimezone){this.appTimezone='UTC'}if(!this.timezone){this.timezone='UTC'}if(this.ignoreTimezone){this.appTimezone='UTC'
this.timezone='UTC'}}}(window.jQuery);+function($){"use strict";var FilterWidget=$.fn.filterWidget.Constructor;var overloaded_init=FilterWidget.prototype.init;FilterWidget.prototype.init=function(){overloaded_init.apply(this)
this.initFilterNumber()}
FilterWidget.prototype.initFilterNumber=function(){var self=this
this.$el.on('show.oc.popover','a.filter-scope-number',function(event){self.initNumberInputs($(this).hasClass('range'))
$(event.relatedTarget).on('click','#controlFilterPopoverNum [data-filter-action="filter"]',function(e){e.preventDefault()
e.stopPropagation()
self.filterByNumber()})
$(event.relatedTarget).on('click','#controlFilterPopoverNum [data-filter-action="clear"]',function(e){e.preventDefault()
e.stopPropagation()
self.filterByNumber(true)})})
this.$el.on('hide.oc.popover','a.filter-scope-number',function(){var $scope=$(this)
self.pushOptions(self.activeScopeName)
self.activeScopeName=null
self.$activeScope=null
setTimeout(function(){$scope.removeClass('filter-scope-open')},200)})
this.$el.on('click','a.filter-scope-number',function(){var $scope=$(this),scopeName=$scope.data('scope-name')
if($scope.hasClass('filter-scope-open'))return
if(null!==self.activeScopeName)return
self.$activeScope=$scope
self.activeScopeName=scopeName
self.isActiveScopeDirty=false
if($scope.hasClass('range')){self.displayPopoverNumberRange($scope)}else{self.displayPopoverNumber($scope)}$scope.addClass('filter-scope-open')})}
FilterWidget.prototype.getPopoverNumberTemplate=function(){return'                                                                                                        \
                <form id="filterPopoverNumber-{{ scopeName }}">                                                         \
                    <input type="hidden" name="scopeName" value="{{ scopeName }}" />                                    \
                    <div id="controlFilterPopoverNum" class="control-filter-popover control-filter-box-popover --range">\
                        <div class="filter-search loading-indicator-container size-input-text">                         \
                            <div class="field-number">                                                                  \
                                <input                                                                                  \
                                    type="number"                                                                       \
                                    name="number"                                                                       \
                                    value="{{ number }}"                                                                \
                                    class="form-control align-right"                                                    \
                                    autocomplete="off"                                                                  \
                                    placeholder="{{ number_placeholder }}" />                                           \
                            </div>                                                                                      \
                            <div class="filter-buttons">                                                                \
                                <button class="btn btn-block btn-primary" data-filter-action="filter">                  \
                                    {{ filter_button_text }}                                                            \
                                </button>                                                                               \
                                <button class="btn btn-block btn-secondary" data-filter-action="clear">                 \
                                    {{ reset_button_text }}                                                             \
                                </button>                                                                               \
                            </div>                                                                                      \
                        </div>                                                                                          \
                    </div>                                                                                              \
                </form>                                                                                                 \
            '
}
FilterWidget.prototype.getPopoverNumberRangeTemplate=function(){return'                                                                                                            \
                <form id="filterPopoverNumberRange-{{ scopeName }}">                                                        \
                    <input type="hidden" name="scopeName" value="{{ scopeName }}" />                                        \
                    <div id="controlFilterPopoverNum" class="control-filter-popover control-filter-box-popover --range">    \
                        <div class="filter-search loading-indicator-container size-input-text">                             \
                            <div class="field-number">                                                                      \
                                <div class="right-align">                                                                   \
                                    <input                                                                                  \
                                        type="number"                                                                       \
                                        name="number"                                                                       \
                                        value="{{ number }}"                                                                \
                                        class="form-control align-right"                                                    \
                                        autocomplete="off"                                                                  \
                                        placeholder="{{ min_placeholder }}" />                                              \
                                </div>                                                                                      \
                            </div>                                                                                          \
                            <div class="field-number">                                                                      \
                                <div class="right-align">                                                                   \
                                    <input                                                                                  \
                                        type="number"                                                                       \
                                        {{ maxNumber }}                                                                     \
                                        name="number"                                                                       \
                                        value="{{ number }}"                                                                \
                                        class="form-control align-right"                                                    \
                                        autocomplete="off"                                                                  \
                                        placeholder="{{ max_placeholder }}" />                                              \
                                </div>                                                                                      \
                            </div>                                                                                          \
                            <div class="filter-buttons">                                                                    \
                                <button class="btn btn-block btn-primary" data-filter-action="filter">                      \
                                    {{ filter_button_text }}                                                                \
                                </button>                                                                                   \
                                <button class="btn btn-block btn-secondary" data-filter-action="clear">                     \
                                    {{ reset_button_text }}                                                                 \
                                </button>                                                                                   \
                            </div>                                                                                          \
                        </div>                                                                                              \
                    </div>                                                                                                  \
                </form>                                                                                                     \
            '
}
FilterWidget.prototype.displayPopoverNumber=function($scope){var self=this,scopeName=$scope.data('scope-name'),data=this.scopeValues[scopeName]
data=$.extend({},data,{filter_button_text:this.getLang('filter.numbers.filter_button_text'),reset_button_text:this.getLang('filter.numbers.reset_button_text'),number_placeholder:this.getLang('filter.numbers.number_placeholder','Number')})
data.scopeName=scopeName
$scope.data('oc.popover',null)
$scope.ocPopover({content:Mustache.render(this.getPopoverNumberTemplate(),data),modal:false,highlightModalTarget:true,closeOnPageClick:true,placement:'bottom',})}
FilterWidget.prototype.displayPopoverNumberRange=function($scope){var self=this,scopeName=$scope.data('scope-name'),data=this.scopeValues[scopeName]
data=$.extend({},data,{filter_button_text:this.getLang('filter.numbers.filter_button_text'),reset_button_text:this.getLang('filter.numbers.reset_button_text'),min_placeholder:this.getLang('filter.numbers.min_placeholder','Min'),max_placeholder:this.getLang('filter.numbers.max_placeholder','Max')})
data.scopeName=scopeName
$scope.data('oc.popover',null)
$scope.ocPopover({content:Mustache.render(this.getPopoverNumberRangeTemplate(),data),modal:false,highlightModalTarget:true,closeOnPageClick:true,placement:'bottom',})}
FilterWidget.prototype.initNumberInputs=function(isRange){var self=this,scopeData=this.$activeScope.data('scope-data'),$inputs=$('.field-number input','#controlFilterPopoverNum'),data=this.scopeValues[this.activeScopeName]
if(!data){data={numbers:isRange?(scopeData.numbers?scopeData.numbers:[]):(scopeData.number?[scopeData.number]:[])}}$inputs.each(function(index,numberinput){var defaultValue=''
if(0<=index&&index<data.numbers.length){defaultValue=data.numbers[index]?data.numbers[index]:''}numberinput.value=''!==defaultValue?defaultValue:'';if(scopeData.step){numberinput.step=scopeData.step}if(scopeData.minValue){numberinput.min=scopeData.minValue}if(scopeData.maxValue){numberinput.max=scopeData.maxValue}})}
FilterWidget.prototype.updateScopeNumberSetting=function($scope,numbers){var $setting=$scope.find('.filter-setting'),numberRegex=/\d*/,reset=false
if(numbers&&numbers.length){numbers[0]=numbers[0]&&numbers[0].match(numberRegex)?numbers[0]:null
if(numbers.length>1){numbers[1]=numbers[1]&&numbers[1].match(numberRegex)?numbers[1]:null
if(numbers[0]||numbers[1]){var min=numbers[0]?numbers[0]:'-∞',max=numbers[1]?numbers[1]:'∞'
$setting.text(min+' → '+max)}else{reset=true}}else if(numbers[0]){$setting.text(numbers[0])}else{reset=true}}else{reset=true}if(reset){$setting.text(this.getLang('filter.numbers.all','all'));$scope.removeClass('active')}else{$scope.addClass('active')}}
FilterWidget.prototype.filterByNumber=function(isReset){var self=this,numbers=[]
if(!isReset){var numberinputs=$('.field-number input','#controlFilterPopoverNum')
numberinputs.each(function(index,numberinput){var number=$(numberinput).val()
numbers.push(number)})}this.updateScopeNumberSetting(this.$activeScope,numbers);this.scopeValues[this.activeScopeName]={numbers:numbers}
this.isActiveScopeDirty=true;this.$activeScope.data('oc.popover').hide()}}(window.jQuery);(function($){$(document).render(function(){var formatSelectOption=function(state){var text=$('<span>').text(state.text).html()
if(!state.id){return text}var $option=$(state.element),iconClass=state.icon?state.icon:$option.data('icon'),imageSrc=state.image?state.image:$option.data('image')
if(iconClass){return'<i class="select-icon '+iconClass+'"></i> '+text}if(imageSrc){return'<img class="select-image" src="'+imageSrc+'" alt="" /> '+text}return text}
var selectOptions={templateResult:formatSelectOption,templateSelection:formatSelectOption,escapeMarkup:function(m){return m},width:'style'}
$('select.custom-select').each(function(){var $element=$(this),extraOptions={dropdownCssClass:'',containerCssClass:''}
if($element.data('select2')!=null){return true;}$element.attr('data-disposable','data-disposable')
$element.one('dispose-control',function(){if($element.data('select2')){$element.select2('destroy')}})
if($element.hasClass('select-no-search')){extraOptions.minimumResultsForSearch=Infinity}if($element.hasClass('select-no-dropdown')){extraOptions.dropdownCssClass+=' select-no-dropdown'
extraOptions.containerCssClass+=' select-no-dropdown'}if($element.hasClass('select-hide-selected')){extraOptions.dropdownCssClass+=' select-hide-selected'}var source=$element.data('handler');if(source){extraOptions.ajax={transport:function(params,success,failure){var $request=$element.request(source,{data:params.data})
$request.done(success)
$request.fail(failure)
return $request},processResults:function(data,params){var results=data.result||data.results,options=[]
delete(data.result)
if(results[0]&&typeof(results[0])==='object'){options=results}else{for(var i in results){if(results.hasOwnProperty(i)){options.push({id:i,text:results[i],})}}}data.results=options
return data},dataType:'json'}}var separators=$element.data('token-separators')
if(separators){extraOptions.tags=true
extraOptions.tokenSeparators=separators.split('|')
if($element.hasClass('select-no-dropdown')){extraOptions.selectOnClose=true
extraOptions.closeOnSelect=true
extraOptions.minimumInputLength=1
$element.on('select2:closing',function(){if($('.select2-dropdown.select-no-dropdown:first .select2-results__option--highlighted').length>0){$('.select2-dropdown.select-no-dropdown:first .select2-results__option--highlighted').removeClass('select2-results__option--highlighted')
$('.select2-dropdown.select-no-dropdown:first .select2-results__option:first').addClass('select2-results__option--highlighted')}})}}var placeholder=$element.data('placeholder')
if(placeholder){extraOptions.placeholder=placeholder
extraOptions.allowClear=true}$element.select2($.extend({},selectOptions,extraOptions))})})
$(document).on('disable','select.custom-select',function(event,status){if($(this).data('select2')!=null){$(this).select2('enable',!status)}})})(jQuery);+function($){"use strict";var LoadIndicator=function(element,options){this.$el=$(element)
this.options=options||{}
this.tally=0
this.show()}
LoadIndicator.prototype.hide=function(){this.tally--
if(this.tally<=0){$('div.loading-indicator',this.$el).remove()
this.$el.removeClass('in-progress')}}
LoadIndicator.prototype.show=function(options){if(options)this.options=options
this.hide()
var indicator=$('<div class="loading-indicator"></div>')
indicator.append($('<div></div>').text(this.options.text))
indicator.append($('<span></span>'))
if(this.options.opaque!==undefined){indicator.addClass('is-opaque')}if(this.options.centered!==undefined){indicator.addClass('indicator-center')}if(this.options.size==='small'){indicator.addClass('size-small')}this.$el.prepend(indicator)
this.$el.addClass('in-progress')
this.tally++}
LoadIndicator.prototype.destroy=function(){this.$el.removeData('oc.loadIndicator')
this.$el=null}
LoadIndicator.DEFAULTS={text:''}
var old=$.fn.loadIndicator
$.fn.loadIndicator=function(option){var args=arguments;return this.each(function(){var $this=$(this)
var data=$this.data('oc.loadIndicator')
var options=$.extend({},LoadIndicator.DEFAULTS,typeof option=='object'&&option)
if(!data){if(typeof option=='string')return;$this.data('oc.loadIndicator',(data=new LoadIndicator(this,options)))}else{if(typeof option!=='string')data.show(options);else{var methodArgs=[];for(var i=1;i<args.length;i++)methodArgs.push(args[i])
data[option].apply(data,methodArgs)}}})}
$.fn.loadIndicator.Constructor=LoadIndicator
$.fn.loadIndicator.noConflict=function(){$.fn.loadIndicator=old
return this}
$(document).on('ajaxPromise','[data-load-indicator]',function(){var indicatorContainer=$(this).closest('.loading-indicator-container'),loadingText=$(this).data('load-indicator'),options={opaque:$(this).data('load-indicator-opaque'),centered:$(this).data('load-indicator-centered'),size:$(this).data('load-indicator-size')}
if(loadingText)options.text=loadingText
indicatorContainer.loadIndicator(options)}).on('ajaxFail ajaxDone ajaxRedirected','[data-load-indicator]',function(){$(this).closest('.loading-indicator-container').loadIndicator('hide')})}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
var CursorLoadIndicator=function(){if(Modernizr.touchevents)return
this.counter=0
this.indicator=$('<div/>').addClass('cursor-loading-indicator').addClass('hide')
$(document.body).append(this.indicator)}
CursorLoadIndicator.prototype.show=function(event){if(Modernizr.touchevents)return
this.counter++
if(this.counter>1)return
var self=this;if(event!==undefined&&event.clientY!==undefined){self.indicator.css({left:event.clientX+15,top:event.clientY+15})}this.indicator.removeClass('hide')
$(window).on('mousemove.cursorLoadIndicator',function(e){self.indicator.css({left:e.clientX+15,top:e.clientY+15,})})}
CursorLoadIndicator.prototype.hide=function(force){if(Modernizr.touchevents)return
this.counter--
if(force!==undefined&&force)this.counter=0
if(this.counter<=0){this.indicator.addClass('hide')
$(window).off('.cursorLoadIndicator');}}
$(document).ready(function(){$.wn.cursorLoadIndicator=new CursorLoadIndicator();})
$(document).on('ajaxPromise','[data-cursor-load-indicator]',function(){$.wn.cursorLoadIndicator.show()}).on('ajaxFail ajaxDone ajaxRedirected','[data-cursor-load-indicator]',function(){$.wn.cursorLoadIndicator.hide()})}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
var StripeLoadIndicator=function(){var self=this
this.counter=0
this.indicator=this.makeIndicator()
this.stripe=this.indicator.find('.stripe')
this.animationTimer=null
$(document).ready(function(){$(document.body).append(self.indicator)})}
StripeLoadIndicator.prototype.makeIndicator=function(){return $('<div/>').addClass('stripe-loading-indicator loaded').append($('<div />').addClass('stripe')).append($('<div />').addClass('stripe-loaded'))}
StripeLoadIndicator.prototype.show=function(){window.clearTimeout(this.animationTimer)
this.indicator.show()
this.counter++
this.stripe.after(this.stripe=this.stripe.clone()).remove()
if(this.counter>1){return}this.indicator.removeClass('loaded')
$(document.body).addClass('loading')}
StripeLoadIndicator.prototype.hide=function(force){this.counter--
if(force!==undefined&&force){this.counter=0}if(this.counter<=0){this.indicator.addClass('loaded')
$(document.body).removeClass('loading')
var self=this
this.animationTimer=window.setTimeout(function(){self.indicator.hide()},1000)}}
$.wn.stripeLoadIndicator=new StripeLoadIndicator()
$(document).on('ajaxPromise','[data-stripe-load-indicator]',function(event){event.stopPropagation()
$.wn.stripeLoadIndicator.show()
var $el=$(this)
$(window).one('ajaxUpdateComplete',function(){if($el.closest('html').length===0)$.wn.stripeLoadIndicator.hide()})}).on('ajaxFail ajaxDone ajaxRedirected','[data-stripe-load-indicator]',function(event){event.stopPropagation()
$.wn.stripeLoadIndicator.hide()})}(window.jQuery);+function($){"use strict";var Popover=function(element,options){var $el=this.$el=$(element);this.options=options||{};this.arrowSize=15
this.docClickHandler=null
this.show()}
Popover.prototype.hide=function(){var e=$.Event('hiding.oc.popover',{relatedTarget:this.$el})
this.$el.trigger(e,this)
if(e.isDefaultPrevented())return
this.$container.removeClass('in')
if(this.$overlay)this.$overlay.removeClass('in')
this.disposeControls()
$.support.transition&&this.$container.hasClass('fade')?this.$container.one($.support.transition.end,$.proxy(this.hidePopover,this)).emulateTransitionEnd(300):this.hidePopover()}
Popover.prototype.disposeControls=function(){if(this.$container){$.wn.foundation.controlUtils.disposeControls(this.$container.get(0))}}
Popover.prototype.hidePopover=function(){this.$container.remove();if(this.$overlay)this.$overlay.remove()
this.$el.removeClass('popover-highlight')
this.$el.trigger('hide.oc.popover')
this.$overlay=false
this.$container=false
this.$el.data('oc.popover',null)
$(document.body).removeClass('popover-open')
$(document).unbind('mousedown',this.docClickHandler);$(document).off('.oc.popover')
this.docClickHandler=null
this.options.onCheckDocumentClickTarget=null}
Popover.prototype.show=function(options){var self=this
var e=$.Event('showing.oc.popover',{relatedTarget:this.$el})
this.$el.trigger(e,this)
if(e.isDefaultPrevented())return
this.$container=$('<div />').addClass('control-popover')
if(this.options.containerClass){this.$container.addClass(this.options.containerClass)}if(this.options.useAnimation){this.$container.addClass('fade')}var $content=$('<div />').html(this.getContent())
this.$container.append($content)
if(this.options.width){this.$container.width(this.options.width)}if(this.options.modal){this.$overlay=$('<div />').addClass('popover-overlay')
$(document.body).append(this.$overlay)
if(this.options.highlightModalTarget){this.$el.addClass('popover-highlight')
this.$el.blur()}}else{this.$overlay=false}if(this.options.container){$(this.options.container).append(this.$container)}else{$(document.body).append(this.$container)}this.reposition()
$(window).on('resize',function(){if(self.$container){self.reposition()}})
this.$container.addClass('in')
if(this.$overlay)this.$overlay.addClass('in')
$(document.body).addClass('popover-open')
var showEvent=jQuery.Event('show.oc.popover',{relatedTarget:this.$container.get(0)})
this.$el.trigger(showEvent)
this.$container.on('close.oc.popover',function(e){self.hide()})
this.$container.on('click','[data-dismiss=popover]',function(e){self.hide()
return false})
this.docClickHandler=$.proxy(this.onDocumentClick,this)
$(document).bind('mousedown',this.docClickHandler);if(this.options.closeOnEsc){$(document).on('keyup.oc.popover',function(e){if($(e.target).hasClass('select2-offscreen'))return false
if(!self.options.closeOnEsc){return false}if(e.key==='Escape'){self.hide()
return false}})}}
Popover.prototype.reposition=function(){var placement=this.calcPlacement(),position=this.calcPosition(placement)
this.$container.removeClass('placement-center placement-bottom placement-top placement-left placement-right')
this.$container.css({left:position.x,top:position.y}).addClass('placement-'+placement)}
Popover.prototype.getContent=function(){if(this.options.contentFrom){return $(this.options.contentFrom).html()}if(typeof this.options.content=='function'){return this.options.content.call(this.$el[0],this)}return this.options.content}
Popover.prototype.calcDimensions=function(){var documentWidth=$(document).width(),documentHeight=$(document).height(),targetOffset=this.$el.offset(),targetWidth=this.$el.outerWidth(),targetHeight=this.$el.outerHeight()
return{containerWidth:this.$container.outerWidth()+this.arrowSize,containerHeight:this.$container.outerHeight()+this.arrowSize,targetOffset:targetOffset,targetHeight:targetHeight,targetWidth:targetWidth,spaceLeft:targetOffset.left,spaceRight:documentWidth-(targetWidth+targetOffset.left),spaceTop:targetOffset.top,spaceBottom:documentHeight-(targetHeight+targetOffset.top),spaceHorizontalBottom:documentHeight-targetOffset.top,spaceVerticalRight:documentWidth-targetOffset.left,documentWidth:documentWidth}}
Popover.prototype.fitsLeft=function(dimensions){return dimensions.spaceLeft>=dimensions.containerWidth&&dimensions.spaceHorizontalBottom>=dimensions.containerHeight}
Popover.prototype.fitsRight=function(dimensions){return dimensions.spaceRight>=dimensions.containerWidth&&dimensions.spaceHorizontalBottom>=dimensions.containerHeight}
Popover.prototype.fitsBottom=function(dimensions){return dimensions.spaceBottom>=dimensions.containerHeight&&dimensions.spaceVerticalRight>=dimensions.containerWidth}
Popover.prototype.fitsTop=function(dimensions){return dimensions.spaceTop>=dimensions.containerHeight&&dimensions.spaceVerticalRight>=dimensions.containerWidth}
Popover.prototype.calcPlacement=function(){var placement=this.options.placement,dimensions=this.calcDimensions();if(placement=='center')return placement
if(placement!='bottom'&&placement!='top'&&placement!='left'&&placement!='right')placement='bottom'
var placementFunctions={top:this.fitsTop,bottom:this.fitsBottom,left:this.fitsLeft,right:this.fitsRight}
if(placementFunctions[placement](dimensions))return placement
for(var index in placementFunctions){if(placementFunctions[index](dimensions))return index}return this.options.fallbackPlacement}
Popover.prototype.calcPosition=function(placement){var dimensions=this.calcDimensions(),result
switch(placement){case'left':var realOffset=this.options.offsetY===undefined?this.options.offset:this.options.offsetY
result={x:(dimensions.targetOffset.left-dimensions.containerWidth),y:dimensions.targetOffset.top+realOffset}
break;case'top':var realOffset=this.options.offsetX===undefined?this.options.offset:this.options.offsetX
result={x:dimensions.targetOffset.left+realOffset,y:(dimensions.targetOffset.top-dimensions.containerHeight)}
break;case'bottom':var realOffset=this.options.offsetX===undefined?this.options.offset:this.options.offsetX
result={x:dimensions.targetOffset.left+realOffset,y:(dimensions.targetOffset.top+dimensions.targetHeight+this.arrowSize)}
break;case'right':var realOffset=this.options.offsetY===undefined?this.options.offset:this.options.offsetY
result={x:(dimensions.targetOffset.left+dimensions.targetWidth+this.arrowSize),y:dimensions.targetOffset.top+realOffset}
break;case'center':var windowHeight=$(window).height()
result={x:(dimensions.documentWidth/2-dimensions.containerWidth/2),y:(windowHeight/2-dimensions.containerHeight/2)}
if(result.y<40)result.y=40
break;}if(!this.options.container)return result
var $container=$(this.options.container),containerOffset=$container.offset()
result.x-=containerOffset.left
result.y-=containerOffset.top
return result}
Popover.prototype.onDocumentClick=function(e){if(!this.options.closeOnPageClick)return
if(this.options.onCheckDocumentClickTarget&&this.options.onCheckDocumentClickTarget(e.target)){return}if($.contains(this.$container.get(0),e.target))return
this.hide();}
Popover.DEFAULTS={placement:'bottom',fallbackPlacement:'bottom',content:'<p>Popover content<p>',contentFrom:null,width:false,modal:false,highlightModalTarget:false,closeOnPageClick:true,closeOnEsc:true,container:false,containerClass:null,offset:15,useAnimation:false,onCheckDocumentClickTarget:null}
var old=$.fn.ocPopover
$.fn.ocPopover=function(option){var args=arguments;return this.each(function(){var $this=$(this)
var data=$this.data('oc.popover')
var options=$.extend({},Popover.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data){if(typeof option=='string')return;$this.data('oc.popover',(data=new Popover(this,options)))}else{if(typeof option!='string')return;var methodArgs=[];for(var i=1;i<args.length;i++)methodArgs.push(args[i])
data[option].apply(data,methodArgs)}})}
$.fn.ocPopover.Constructor=Popover
$.fn.ocPopover.noConflict=function(){$.fn.ocPopover=old
return this}
$(document).on('click','[data-control=popover]',function(e){$(this).ocPopover()
return false;})}(window.jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var Popup=function(element,options){this.options=options
this.$el=$(element)
this.$container=null
this.$modal=null
this.$backdrop=null
this.isOpen=false
this.isLoading=false
this.firstDiv=null
this.allowHide=true
this.$container=this.createPopupContainer()
this.$content=this.$container.find('.modal-content:first')
this.$dialog=this.$container.find('.modal-dialog:first')
this.$modal=this.$container.modal({show:false,backdrop:false,keyboard:this.options.keyboard})
$.wn.foundation.controlUtils.markDisposable(element)
Base.call(this)
this.initEvents()
this.init()}
Popup.prototype=Object.create(BaseProto)
Popup.prototype.constructor=Popup
Popup.DEFAULTS={ajax:null,handler:null,keyboard:true,extraData:{},content:null,size:null,adaptiveHeight:false,zIndex:null}
Popup.prototype.init=function(){var self=this
if(self.isOpen)return
this.setBackdrop(true)
if(!this.options.content){this.setLoading(true)}if(this.options.handler){this.$el.request(this.options.handler,{data:paramToObj('data-extra-data',this.options.extraData),success:function(data,textStatus,jqXHR){this.success(data,textStatus,jqXHR).done(function(){self.setContent(data.result)
$(window).trigger('ajaxUpdateComplete',[this,data,textStatus,jqXHR])
self.triggerEvent('popupComplete')
self.triggerEvent('complete.oc.popup')})},error:function(jqXHR,textStatus,errorThrown){this.error(jqXHR,textStatus,errorThrown).done(function(){if(self.isLoading){self.hideLoading();}else{self.hide()}self.triggerEvent('popupError')
self.triggerEvent('error.oc.popup')})}})}else if(this.options.ajax){$.ajax({url:this.options.ajax,data:paramToObj('data-extra-data',this.options.extraData),success:function(data){self.setContent(data)},cache:false})}else if(this.options.content){var content=typeof this.options.content=='function'?this.options.content.call(this.$el[0],this):this.options.content
this.setContent(content)}}
Popup.prototype.initEvents=function(){var self=this
this.$container.data('oc.popup',this)
this.$modal.on('hide.bs.modal',function(){self.triggerEvent('hide.oc.popup')
self.isOpen=false
self.setBackdrop(false)})
this.$modal.on('hidden.bs.modal',function(){self.triggerEvent('hidden.oc.popup')
$.wn.foundation.controlUtils.disposeControls(self.$container.get(0))
self.$container.remove()
$(document.body).removeClass('modal-open')
self.dispose()})
this.$modal.on('show.bs.modal',function(){self.isOpen=true
self.setBackdrop(true)
$(document.body).addClass('modal-open')})
this.$modal.on('shown.bs.modal',function(){self.triggerEvent('shown.oc.popup')})
this.$modal.on('close.oc.popup',function(){self.hide()
return false})}
Popup.prototype.dispose=function(){this.$modal.off('hide.bs.modal')
this.$modal.off('hidden.bs.modal')
this.$modal.off('show.bs.modal')
this.$modal.off('shown.bs.modal')
this.$modal.off('close.oc.popup')
this.$el.off('dispose-control',this.proxy(this.dispose))
this.$el.removeData('oc.popup')
this.$container.removeData('oc.popup')
this.$container=null
this.$content=null
this.$dialog=null
this.$modal=null
this.$el=null
this.options=null
BaseProto.dispose.call(this)}
Popup.prototype.createPopupContainer=function(){var modal=$('<div />').prop({class:'control-popup modal fade',role:'dialog',tabindex:-1}),modalDialog=$('<div />').addClass('modal-dialog'),modalContent=$('<div />').addClass('modal-content')
if(this.options.size)modalDialog.addClass('size-'+this.options.size)
if(this.options.adaptiveHeight)modalDialog.addClass('adaptive-height')
if(this.options.zIndex!==null)modal.css('z-index',this.options.zIndex+20)
return modal.append(modalDialog.append(modalContent))}
Popup.prototype.setContent=function(contents){this.$content.html(contents)
this.setLoading(false)
this.show()
this.firstDiv=this.$content.find('>div:first')
if(this.firstDiv.length>0)this.firstDiv.data('oc.popup',this)
var $defaultFocus=$('[default-focus]',this.$content)
if($defaultFocus.is(":visible")){window.setTimeout(function(){$defaultFocus.focus()
$defaultFocus=null},300)}}
Popup.prototype.setBackdrop=function(val){if(val&&!this.$backdrop){this.$backdrop=$('<div class="popup-backdrop fade" />')
if(this.options.zIndex!==null)this.$backdrop.css('z-index',this.options.zIndex)
this.$backdrop.appendTo(document.body)
this.$backdrop.addClass('in')
this.$backdrop.append($('<div class="modal-content popup-loading-indicator" />'))}else if(!val&&this.$backdrop){this.$backdrop.remove()
this.$backdrop=null}}
Popup.prototype.setLoading=function(val){if(!this.$backdrop)return;this.isLoading=val
var self=this
if(val){setTimeout(function(){self.$backdrop.addClass('loading');},100)}else{setTimeout(function(){self.$backdrop.removeClass('loading');},100)}}
Popup.prototype.setShake=function(){var self=this
this.$content.addClass('popup-shaking')
setTimeout(function(){self.$content.removeClass('popup-shaking')},1000)}
Popup.prototype.hideLoading=function(val){this.setLoading(false)
var self=this
setTimeout(function(){self.setBackdrop(false)},250)
setTimeout(function(){self.hide()},500)}
Popup.prototype.triggerEvent=function(eventName,params){if(!params){params=[this.$el,this.$modal]}var eventObject=jQuery.Event(eventName,{relatedTarget:this.$container.get(0)})
this.$el.trigger(eventObject,params)
if(this.firstDiv){this.firstDiv.trigger(eventObject,params)}}
Popup.prototype.reload=function(){this.init()}
Popup.prototype.show=function(){this.$modal.modal('show')
this.$modal.on('click.dismiss.popup','[data-dismiss="popup"]',$.proxy(this.hide,this))
this.triggerEvent('popupShow')
this.triggerEvent('show.oc.popup')
this.$dialog.css('transform','inherit')}
Popup.prototype.hide=function(){if(!this.isOpen)return
this.triggerEvent('popupHide')
this.triggerEvent('hide.oc.popup')
if(this.allowHide)this.$modal.modal('hide')
this.$dialog.css('transform','')}
Popup.prototype.visible=function(val){if(val){this.$modal.addClass('in')}else{this.$modal.removeClass('in')}this.setBackdrop(val)}
Popup.prototype.toggle=function(){this.triggerEvent('popupToggle',[this.$modal])
this.triggerEvent('toggle.oc.popup',[this.$modal])
this.$modal.modal('toggle')}
Popup.prototype.lock=function(val){this.allowHide=!val}
var old=$.fn.popup
$.fn.popup=function(option){var args=Array.prototype.slice.call(arguments,1)
return this.each(function(){var $this=$(this)
var data=$this.data('oc.popup')
var options=$.extend({},Popup.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.popup',(data=new Popup(this,options)))
else if(typeof option=='string')data[option].apply(data,args)
else data.reload()})}
$.fn.popup.Constructor=Popup
$.popup=function(option){return $('<a />').popup(option)}
$.fn.popup.noConflict=function(){$.fn.popup=old
return this}
function paramToObj(name,value){if(value===undefined)value=''
if(typeof value=='object')return value
try{return ocJSON("{"+value+"}")}catch(e){throw new Error('Error parsing the '+name+' attribute value. '+e)}}$(document).on('click.oc.popup','[data-control="popup"]',function(event){event.preventDefault()
$(this).popup()});$(document).on('ajaxPromise','[data-popup-load-indicator]',function(event,context){if($(this).data('request')!=context.handler)return
$(this).closest('.control-popup').removeClass('in').popup('setLoading',true)}).on('ajaxFail','[data-popup-load-indicator]',function(event,context){if($(this).data('request')!=context.handler)return
$(this).closest('.control-popup').addClass('in').popup('setLoading',false).popup('setShake')}).on('ajaxDone','[data-popup-load-indicator]',function(event,context){if($(this).data('request')!=context.handler)return
$(this).closest('.control-popup').popup('hideLoading')})}(window.jQuery);+function($){"use strict";var ChartUtils=function(){}
ChartUtils.prototype.defaultValueColor='#b8b8b8';ChartUtils.prototype.getColor=function(index){var colors=['#95b753','#cc3300','#e5a91a','#3366ff','#ff0f00','#ff6600','#ff9e01','#fcd202','#f8ff01','#b0de09','#04d215','#0d8ecf','#0d52d1','#2a0cd0','#8a0ccf','#cd0d74','#754deb','#dddddd','#999999','#333333','#000000','#57032a','#ca9726','#990000','#4b0c25'],colorIndex=index%(colors.length-1);return colors[colorIndex];}
ChartUtils.prototype.loadListValues=function($list){var result={values:[],total:0,max:0}
$('> li',$list).each(function(){var value=$(this).data('value')?parseFloat($(this).data('value')):parseFloat($('span',this).text());result.total+=value
result.values.push({value:value,color:$(this).data('color')})
result.max=Math.max(result.max,value)})
return result;}
ChartUtils.prototype.getLegendLabel=function($legend,index){return $('tr:eq('+index+') td:eq(1)',$legend).html();}
ChartUtils.prototype.initLegendColorIndicators=function($legend){var indicators=[];$('tr > td:first-child',$legend).each(function(){var indicator=$('<i></i>')
$(this).prepend(indicator)
indicators.push(indicator)})
return indicators;}
ChartUtils.prototype.createLegend=function($list){var $legend=$('<div>').addClass('chart-legend'),$table=$('<table>')
$legend.append($table)
$('> li',$list).each(function(){var label=$(this).clone().children().remove().end().html();$table.append($('<tr>').append($('<td class="indicator">')).append($('<td>').html(label)).append($('<td>').addClass('value').html($('span',this).html())))})
$legend.insertAfter($list)
$list.remove()
return $legend;}
ChartUtils.prototype.showTooltip=function(x,y,text){var $tooltip=$('#chart-tooltip')
if($tooltip.length)$tooltip.remove()
$tooltip=$('<div id="chart-tooltip">').html(text).css('visibility','hidden')
x+=10
y+=10
$(document.body).append($tooltip)
var tooltipWidth=$tooltip.outerWidth()
if((x+tooltipWidth)>$(window).width())x=$(window).width()-tooltipWidth-10;$tooltip.css({top:y,left:x,visibility:'visible'});}
ChartUtils.prototype.hideTooltip=function(){$('#chart-tooltip').remove()}
if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
$.wn.chartUtils=new ChartUtils();}(window.jQuery);+function($){"use strict";var ChartLine=function(element,options){var self=this
this.chartOptions={xaxis:{mode:"time",tickLength:5},selection:{mode:"x"},grid:{markingsColor:"rgba(0,0,0, 0.02)",backgroundColor:{colors:["#fff","#fff"]},borderColor:"#7bafcc",borderWidth:0,color:"#ddd",hoverable:true,clickable:true,labelMargin:10},series:{lines:{show:true,fill:true},points:{show:true}},tooltip:true,tooltipOpts:{defaultTheme:false,content:"%x: <strong>%y</strong>",dateFormat:"%y-%0m-%0d",shifts:{x:10,y:20}},legend:{show:true,noColumns:2}}
this.defaultDataSetOptions={shadowSize:0}
var parsedOptions={}
try{parsedOptions=ocJSON("{"+options.chartOptions+"}");}catch(e){throw new Error('Error parsing the data-chart-options attribute value. '+e);}this.chartOptions=$.extend({},this.chartOptions,parsedOptions)
this.options=options
this.$el=$(element)
this.fullDataSet=[]
this.resetZoomLink=$(options.resetZoomLink)
this.$el.trigger('oc.chartLineInit',[this])
this.resetZoomLink.on('click',$.proxy(this.clearZoom,this));if(this.options.zoomable){this.$el.on("plotselected",function(event,ranges){var newCoords={xaxis:{min:ranges.xaxis.from,max:ranges.xaxis.to}}
$.plot(self.$el,self.fullDataSet,$.extend(true,{},self.chartOptions,newCoords))
self.resetZoomLink.show()});}if(this.chartOptions.xaxis.mode=="time"&&this.options.timeMode=="weeks")this.chartOptions.markings=weekendAreas
function weekendAreas(axes){var markings=[],d=new Date(axes.xaxis.min);d.setUTCDate(d.getUTCDate()-((d.getUTCDay()+1)%7))
d.setUTCSeconds(0)
d.setUTCMinutes(0)
d.setUTCHours(0)
var i=d.getTime()
do{markings.push({xaxis:{from:i,to:i+2*24*60*60*1000}})
i+=7*24*60*60*1000}while(i<axes.xaxis.max)return markings}this.initializing=true
this.$el.find('>[data-chart="dataset"]').each(function(){var data=$(this).data(),processedData={};for(var key in data){var normalizedKey=key.substring(3),value=data[key];normalizedKey=normalizedKey.charAt(0).toLowerCase()+normalizedKey.slice(1);if(normalizedKey=='data')value=JSON.parse('['+value+']');processedData[normalizedKey]=value;}self.addDataSet($.extend({},self.defaultDataSetOptions,processedData));})
this.initializing=false
this.rebuildChart()}
ChartLine.DEFAULTS={chartOptions:"",timeMode:null,zoomable:false}
ChartLine.prototype.addDataSet=function(dataSet){this.fullDataSet.push(dataSet)
if(!this.initializing)this.rebuildChart()}
ChartLine.prototype.rebuildChart=function(){this.$el.trigger('oc.beforeChartLineRender',[this])
$.plot(this.$el,this.fullDataSet,this.chartOptions)}
ChartLine.prototype.clearZoom=function(){this.rebuildChart()
this.resetZoomLink.hide()}
var old=$.fn.chartLine
$.fn.chartLine=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('winter.chartLine')
var options=$.extend({},ChartLine.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('winter.chartLine',(data=new ChartLine(this,options)))
if(typeof option=='string')data[option].call($this)})}
$.fn.chartLine.Constructor=ChartLine
$.fn.chartLine.noConflict=function(){$.fn.chartLine=old
return this}
$(document).render(function(){$('[data-control="chart-line"]').chartLine()})}(window.jQuery);+function($){"use strict";var BarChart=function(element,options){this.options=options||{}
var $el=this.$el=$(element),size=this.size=$el.height(),self=this,values=$.wn.chartUtils.loadListValues($('ul',$el)),$legend=$.wn.chartUtils.createLegend($('ul',$el)),indicators=$.wn.chartUtils.initLegendColorIndicators($legend),isFullWidth=this.isFullWidth(),chartHeight=this.options.height!==undefined?this.options.height:size,chartWidth=isFullWidth?this.$el.width():size,barWidth=(chartWidth-(values.values.length-1)*this.options.gap)/values.values.length
var $canvas=$('<div/>').addClass('canvas').height(chartHeight).width(isFullWidth?'100%':chartWidth)
$el.prepend($canvas)
$el.toggleClass('full-width',isFullWidth)
Raphael($canvas.get(0),isFullWidth?'100%':chartWidth,chartHeight,function(){self.paper=this
self.bars=this.set()
self.paper.customAttributes.bar=function(start,height){return{path:[["M",start,chartHeight],["L",start,chartHeight-height],["L",start+barWidth,chartHeight-height],["L",start+barWidth,chartHeight],["Z"]]}}
var start=0
$.each(values.values,function(index,valueInfo){var color=valueInfo.color!==undefined?valueInfo.color:$.wn.chartUtils.getColor(index),path=self.paper.path().attr({"stroke-width":0}).attr({bar:[start,0]}).attr({fill:color})
self.bars.push(path)
indicators[index].css('background-color',color)
start+=barWidth+self.options.gap
path.hover(function(ev){$.wn.chartUtils.showTooltip(ev.pageX,ev.pageY,$.trim($.wn.chartUtils.getLegendLabel($legend,index))+': <strong>'+valueInfo.value+'</stong>')},function(){$.wn.chartUtils.hideTooltip()})})
start=0
$.each(values.values,function(index,valueInfo){var height=(values.max&&valueInfo.value)?chartHeight/values.max*valueInfo.value:0
self.bars[index].animate({bar:[start,height]},1000,"bounce")
start+=barWidth+self.options.gap})
if(isFullWidth){$(window).on('resize',function(){chartWidth=self.$el.width()
barWidth=(chartWidth-(values.values.length-1)*self.options.gap)/values.values.length
var start=0
$.each(values.values,function(index,valueInfo){var height=(values.max&&valueInfo.value)?chartHeight/values.max*valueInfo.value:0
self.bars[index].animate({bar:[start,height]},10,"bounce")
start+=barWidth+self.options.gap})})}})}
BarChart.prototype.isFullWidth=function(){return this.options.fullWidth!==undefined&&this.options.fullWidth}
BarChart.DEFAULTS={gap:2}
var old=$.fn.barChart
$.fn.barChart=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.barChart')
var options=$.extend({},BarChart.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.barChart',new BarChart(this,options))})}
$.fn.barChart.Constructor=BarChart
$.fn.barChart.noConflict=function(){$.fn.barChart=old
return this}
$(document).render(function(){$('[data-control=chart-bar]').barChart()})}(window.jQuery)+function($){"use strict";var PieChart=function(element,options){this.options=options||{}
var $el=this.$el=$(element),size=this.size=(this.options.size!==undefined?this.options.size:$el.height()),outerRadius=size/2-1,innerRadius=outerRadius-outerRadius/3.5,values=$.wn.chartUtils.loadListValues($('ul',$el)),$legend=$.wn.chartUtils.createLegend($('ul',$el)),indicators=$.wn.chartUtils.initLegendColorIndicators($legend),self=this
var $canvas=$('<div/>').addClass('canvas').width(size).height(size)
$el.prepend($canvas)
Raphael($canvas.get(0),size,size,function(){self.paper=this
self.segments=this.set()
self.paper.customAttributes.segment=function(startAngle,endAngle){var p1=self.arcCoords(outerRadius,startAngle),p2=self.arcCoords(outerRadius,endAngle),p3=self.arcCoords(innerRadius,endAngle),p4=self.arcCoords(innerRadius,startAngle),flag=(endAngle-startAngle)>180,path=[["M",p1.x,p1.y],["A",outerRadius,outerRadius,0,+flag,0,p2.x,p2.y],["L",p3.x,p3.y],["A",innerRadius,innerRadius,0,+flag,1,p4.x,p4.y],["Z"]]
return{path:path}}
self.paper.circle(size/2,size/2,innerRadius+(outerRadius-innerRadius)/2).attr({"stroke-width":outerRadius-innerRadius-0.5}).attr({stroke:$.wn.chartUtils.defaultValueColor})
$.each(values.values,function(index,valueInfo){var color=valueInfo.color!==undefined?valueInfo.color:$.wn.chartUtils.getColor(index),path=self.paper.path().attr({"stroke-width":0}).attr({segment:[0,0]}).attr({fill:color})
self.segments.push(path)
indicators[index].css('background-color',color)
path.hover(function(ev){$.wn.chartUtils.showTooltip(ev.pageX,ev.pageY,$.trim($.wn.chartUtils.getLegendLabel($legend,index))+': <strong>'+valueInfo.value+'</stong>')},function(){$.wn.chartUtils.hideTooltip()})})
var start=self.options.startAngle
$.each(values.values,function(index,valueInfo){var length=(values.total&&valueInfo.value)?360/values.total*valueInfo.value:0
if(length==360)length--
self.segments[index].animate({segment:[start,start+length]},1000,"bounce")
start+=length})})
if(this.options.centerText!==undefined){var $text=$('<span>').addClass('center').html(this.options.centerText)
$canvas.append($text)}}
PieChart.prototype.arcCoords=function(radius,angle){var a=Raphael.rad(angle),x=this.size/2+radius*Math.cos(a),y=this.size/2-radius*Math.sin(a)
return{'x':x,'y':y}}
PieChart.DEFAULTS={startAngle:45}
var old=$.fn.pieChart
$.fn.pieChart=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.pieChart')
var options=$.extend({},PieChart.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.pieChart',new PieChart(this,options))})}
$.fn.pieChart.Constructor=PieChart
$.fn.pieChart.noConflict=function(){$.fn.pieChart=old
return this}
$(document).render(function(){$('[data-control=chart-pie]').pieChart()})}(window.jQuery)+function($){"use strict";var GoalMeter=function(element,options){var $el=this.$el=$(element),self=this;this.options=options||{};this.$indicatorBar=$('<span/>').text(this.options.value+'%')
this.$indicatorOuter=$('<span/>').addClass('goal-meter-indicator').append(this.$indicatorBar)
$('p',this.$el).first().before(this.$indicatorOuter)
window.setTimeout(function(){self.update(self.options.value)},200)}
GoalMeter.prototype.update=function(value){this.$indicatorBar.css('height',value+'%')}
GoalMeter.DEFAULTS={value:50}
var old=$.fn.goalMeter
$.fn.goalMeter=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.goalMeter')
var options=$.extend({},GoalMeter.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.goalMeter',(data=new GoalMeter(this,options)))
else data.update(option)})}
$.fn.goalMeter.Constructor=GoalMeter
$.fn.goalMeter.noConflict=function(){$.fn.goalMeter=old
return this}
$(document).render(function(){$('[data-control=goal-meter]').goalMeter()})}(window.jQuery);+function($){"use strict";var RowLink=function(element,options){var self=this
this.options=options
this.$el=$(element)
var tr=this.$el.prop('tagName')=='TR'?this.$el:this.$el.find('tr:has(td)')
tr.each(function(){var link=$(this).find(options.target).filter(function(){return!$(this).closest('td').hasClass(options.excludeClass)&&!$(this).hasClass(options.excludeClass)}).first()
if(!link.length)return
var href=link.attr('href'),onclick=(typeof link.get(0).onclick=="function")?link.get(0).onclick:null,popup=link.is('[data-control=popup]'),request=link.is('[data-request]')
function handleClick(e){if($(document.body).hasClass('drag')){return}if(onclick){onclick.apply(link.get(0))}else if(request){link.request()}else if(popup){link.popup()}else if(e.ctrlKey||e.metaKey){window.open(href)}else{window.location=href}}$(this).not('.'+options.excludeClass).find('td').not('.'+options.excludeClass).click(function(e){handleClick(e)}).mousedown(function(e){if(e.which==2){window.open(href)}})
$(this).not('.'+options.excludeClass).on('keypress',function(e){if(e.key==='(Space character)'||e.key==='Spacebar'||e.key===' '){handleClick(e)
return false}})
$(this).addClass(options.linkedClass)
link.after(link.contents()).hide()})
$('tr.rowlink').attr('tabindex',0)}
RowLink.DEFAULTS={target:'a',excludeClass:'nolink',linkedClass:'rowlink'}
var old=$.fn.rowLink
$.fn.rowLink=function(option){var args=Array.prototype.slice.call(arguments,1)
return this.each(function(){var $this=$(this)
var data=$this.data('oc.rowlink')
var options=$.extend({},RowLink.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.rowlink',(data=new RowLink(this,options)))
else if(typeof option=='string')data[option].apply(data,args)})}
$.fn.rowLink.Constructor=RowLink
$.fn.rowLink.noConflict=function(){$.fn.rowLink=old
return this}
$(document).render(function(){$('[data-control="rowlink"]').rowLink()})}(window.jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var ChangeMonitor=function(element,options){this.$el=$(element);this.paused=false
this.options=options||{}
$.wn.foundation.controlUtils.markDisposable(element)
Base.call(this)
this.init()}
ChangeMonitor.prototype=Object.create(BaseProto)
ChangeMonitor.prototype.constructor=ChangeMonitor
ChangeMonitor.prototype.init=function(){this.$el.on('change',this.proxy(this.change))
this.$el.on('unchange.oc.changeMonitor',this.proxy(this.unchange))
this.$el.on('pause.oc.changeMonitor',this.proxy(this.pause))
this.$el.on('resume.oc.changeMonitor',this.proxy(this.resume))
this.$el.on('keyup input paste','input:not(.ace_search_field), textarea:not(.ace_text-input)',this.proxy(this.onInputChange))
$('input:not([type=hidden]):not(.ace_search_field), textarea:not(.ace_text-input)',this.$el).each(function(){$(this).data('oldval.oc.changeMonitor',$(this).val());})
if(this.options.windowCloseConfirm)$(window).on('beforeunload',this.proxy(this.onBeforeUnload))
this.$el.one('dispose-control',this.proxy(this.dispose))
this.$el.trigger('ready.oc.changeMonitor')}
ChangeMonitor.prototype.dispose=function(){if(this.$el===null)return
this.unregisterHandlers()
this.$el.removeData('oc.changeMonitor')
this.$el=null
this.options=null
BaseProto.dispose.call(this)}
ChangeMonitor.prototype.unregisterHandlers=function(){this.$el.off('change',this.proxy(this.change))
this.$el.off('unchange.oc.changeMonitor',this.proxy(this.unchange))
this.$el.off('pause.oc.changeMonitor ',this.proxy(this.pause))
this.$el.off('resume.oc.changeMonitor ',this.proxy(this.resume))
this.$el.off('keyup input paste','input:not(.ace_search_field), textarea:not(.ace_text-input)',this.proxy(this.onInputChange))
this.$el.off('dispose-control',this.proxy(this.dispose))
if(this.options.windowCloseConfirm)$(window).off('beforeunload',this.proxy(this.onBeforeUnload))}
ChangeMonitor.prototype.change=function(ev,inputChange){if(this.paused)return
if(ev.target.className==='ace_search_field')return
if(!inputChange){var type=$(ev.target).attr('type')
if(type==='text'||type==='password')return}if(!this.$el.hasClass('oc-data-changed')){this.$el.trigger('changed.oc.changeMonitor')
this.$el.addClass('oc-data-changed')}}
ChangeMonitor.prototype.unchange=function(){if(this.paused)return
if(this.$el.hasClass('oc-data-changed')){this.$el.trigger('unchanged.oc.changeMonitor')
this.$el.removeClass('oc-data-changed')}}
ChangeMonitor.prototype.onInputChange=function(ev){if(this.paused)return
var $el=$(ev.target)
if($el.data('oldval.oc.changeMonitor')!==$el.val()){$el.data('oldval.oc.changeMonitor',$el.val());this.change(ev,true);}}
ChangeMonitor.prototype.pause=function(){this.paused=true}
ChangeMonitor.prototype.resume=function(){this.paused=false}
ChangeMonitor.prototype.onBeforeUnload=function(){if($.contains(document.documentElement,this.$el.get(0))&&this.$el.hasClass('oc-data-changed'))return this.options.windowCloseConfirm}
ChangeMonitor.DEFAULTS={windowCloseConfirm:false}
var old=$.fn.changeMonitor
$.fn.changeMonitor=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.changeMonitor')
var options=$.extend({},ChangeMonitor.DEFAULTS,$this.data(),typeof option==='object'&&option)
if(!data)$this.data('oc.changeMonitor',(data=new ChangeMonitor(this,options)))})}
$.fn.changeMonitor.Constructor=ChangeMonitor
$.fn.changeMonitor.noConflict=function(){$.fn.changeMonitor=old
return this}
$(document).render(function(){$('[data-change-monitor]').changeMonitor()})}(window.jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var HotKey=function(element,options){if(!options.hotkey){throw new Error('No hotkey has been defined.');}this.$el=$(element)
this.$target=$(options.hotkeyTarget)
this.options=options||{}
this.keyConditions=[]
this.keyMap=null
$.wn.foundation.controlUtils.markDisposable(element)
Base.call(this)
this.init()}
HotKey.prototype=Object.create(BaseProto)
HotKey.prototype.constructor=HotKey
HotKey.prototype.dispose=function(){if(this.$el===null){return}this.unregisterHandlers()
this.$el.removeData('oc.hotkey')
this.$target=null
this.$el=null
this.keyConditions=null
this.keyMap=null
this.options=null
BaseProto.dispose.call(this)}
HotKey.prototype.init=function(){this.initKeyMap()
var keys=this.options.hotkey.toLowerCase().split(',')
for(var i=0,len=keys.length;i<len;i++){var keysTrimmed=this.trim(keys[i])
this.keyConditions.push(this.makeCondition(keysTrimmed))}this.$target.on('keydown',this.proxy(this.onKeyDown))
this.$el.one('dispose-control',this.proxy(this.dispose))}
HotKey.prototype.unregisterHandlers=function(){this.$target.off('keydown',this.proxy(this.onKeyDown))
this.$el.off('dispose-control',this.proxy(this.dispose))}
HotKey.prototype.makeCondition=function(keyBind){var condition={shift:false,ctrl:false,cmd:false,alt:false,specific:-1},keys=keyBind.split('+')
for(var i=0,len=keys.length;i<len;i++){switch(keys[i]){case'shift':condition.shift=true
break
case'ctrl':condition.ctrl=true
break
case'command':case'cmd':case'meta':condition.cmd=true
break
case'alt':case'option':condition.alt=true
break}}condition.specific=this.keyMap[keys[keys.length-1]]
if(typeof(condition.specific)=='undefined'){condition.specific=keys[keys.length-1].toUpperCase().charCodeAt()}return condition}
HotKey.prototype.initKeyMap=function(){this.keyMap={'esc':27,'tab':9,'space':32,'return':13,'enter':13,'backspace':8,'scroll':145,'capslock':20,'numlock':144,'pause':19,'break':19,'insert':45,'home':36,'delete':46,'suppr':46,'end':35,'pageup':33,'pagedown':34,'left':37,'up':38,'right':39,'down':40,'f1':112,'f2':113,'f3':114,'f4':115,'f5':116,'f6':117,'f7':118,'f8':119,'f9':120,'f10':121,'f11':122,'f12':123}}
HotKey.prototype.trim=function(str){return str.replace(/^\s+/,"").replace(/\s+$/,"")}
HotKey.prototype.testConditions=function(ev){for(var i=0,len=this.keyConditions.length;i<len;i++){var condition=this.keyConditions[i]
if(ev.which===condition.specific&&ev.originalEvent.shiftKey===condition.shift&&ev.originalEvent.ctrlKey===condition.ctrl&&ev.originalEvent.metaKey===condition.cmd&&ev.originalEvent.altKey===condition.alt){return true}}return false}
HotKey.prototype.onKeyDown=function(ev){if(this.testConditions(ev)){if(this.options.hotkeyVisible&&!this.$el.is(':visible')){return}if(this.options.callback){return this.options.callback(this.$el,ev.currentTarget,ev)}}}
HotKey.DEFAULTS={hotkey:null,hotkeyTarget:'html',hotkeyVisible:true,callback:function(element){element.trigger('click')
return false}}
var old=$.fn.hotKey
$.fn.hotKey=function(option){var args=arguments;return this.each(function(){var $this=$(this)
var data=$this.data('oc.hotkey')
var options=$.extend({},HotKey.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.hotkey',(data=new HotKey(this,options)))
if(typeof option=='string')data[option].apply(data,args)})}
$.fn.hotKey.Constructor=HotKey
$.fn.hotKey.noConflict=function(){$.fn.hotKey=old
return this}
$(document).render(function(){$('[data-hotkey]').hotKey()})}(window.jQuery);+function($){"use strict";var VIETNAMESE_MAP={'Á':'A','À':'A','Ã':'A','Ả':'A','Ạ':'A','Ắ':'A','Ằ':'A','Ẵ':'A','Ẳ':'A','Ặ':'A','Ấ':'A','Ầ':'A','Ẫ':'A','Ẩ':'A','Ậ':'A','Đ':'D','É':'E','È':'E','Ẽ':'E','Ẻ':'E','Ẹ':'E','Ế':'E','Ề':'E','Ễ':'E','Ể':'E','Ệ':'E','Ó':'O','Ò':'O','Ỏ':'O','Õ':'O','Ọ':'O','Ố':'O','Ồ':'O','Ổ':'O','Ỗ':'O','Ộ':'O','Ơ':'O','Ớ':'O','Ờ':'O','Ở':'O','Ỡ':'O','Ợ':'O','Í':'I','Ì':'I','Ỉ':'I','Ĩ':'I','Ị':'I','Ú':'U','Ù':'U','Ủ':'U','Ũ':'U','Ụ':'U','Ư':'U','Ứ':'U','Ừ':'U','Ử':'U','Ữ':'U','Ự':'U','Ý':'Y','Ỳ':'Y','Ỷ':'Y','Ỹ':'Y','Ỵ':'Y','á':'a','à':'a','ã':'a','ả':'a','ạ':'a','ắ':'a','ằ':'a','ẵ':'a','ẳ':'a','ặ':'a','ấ':'a','ầ':'a','ẫ':'a','ẩ':'a','ậ':'a','đ':'d','é':'e','è':'e','ẽ':'e','ẻ':'e','ẹ':'e','ế':'e','ề':'e','ễ':'e','ể':'e','ệ':'e','ó':'o',
'ò':'o','ỏ':'o','õ':'o','ọ':'o','ố':'o','ồ':'o','ổ':'o','ỗ':'o','ộ':'o','ơ':'o','ớ':'o','ờ':'o','ở':'o','ỡ':'o','ợ':'o','í':'i','ì':'i','ỉ':'i','ĩ':'i','ị':'i','ú':'u','ù':'u','ủ':'u','ũ':'u','ụ':'u','ư':'u','ứ':'u','ừ':'u','ử':'u','ữ':'u','ự':'u','ý':'y','ỳ':'y','ỷ':'y','ỹ':'y','ỵ':'y'},LATIN_MAP={'À':'A','Á':'A','Â':'A','Ã':'A','Ä':'A','Å':'A','Æ':'AE','Ç':'C','È':'E','É':'E','Ê':'E','Ë':'E','Ì':'I','Í':'I','Î':'I','Ï':'I','Ð':'D','Ñ':'N','Ò':'O','Ó':'O','Ô':'O','Õ':'O','Ö':'O','Ő':'O','Ø':'O','Ù':'U','Ú':'U','Û':'U','Ü':'U','Ű':'U','Ý':'Y','Þ':'TH','Ÿ':'Y','ß':'ss','à':'a','á':'a','â':'a','ã':'a','ä':'a','å':'a','æ':'ae','ç':'c','è':'e','é':'e','ê':'e','ë':'e','ì':'i','í':'i','î':'i','ï':'i','ð':'d','ñ':'n','ò':'o','ó':'o','ô':'o','õ':'o','ö':'o','ő':'o','ø':'o','ō':'o','œ':'oe','ù':'u','ú':'u','û':'u','ü':'u','ű':'u','ý':'y','þ':'th','ÿ':'y'},
LATIN_SYMBOLS_MAP={'©':'(c)'},GREEK_MAP={'α':'a','β':'b','γ':'g','δ':'d','ε':'e','ζ':'z','η':'h','θ':'8','ι':'i','κ':'k','λ':'l','μ':'m','ν':'n','ξ':'3','ο':'o','π':'p','ρ':'r','σ':'s','τ':'t','υ':'y','φ':'f','χ':'x','ψ':'ps','ω':'w','ά':'a','έ':'e','ί':'i','ό':'o','ύ':'y','ή':'h','ώ':'w','ς':'s','ϊ':'i','ΰ':'y','ϋ':'y','ΐ':'i','Α':'A','Β':'B','Γ':'G','Δ':'D','Ε':'E','Ζ':'Z','Η':'H','Θ':'8','Ι':'I','Κ':'K','Λ':'L','Μ':'M','Ν':'N','Ξ':'3','Ο':'O','Π':'P','Ρ':'R','Σ':'S','Τ':'T','Υ':'Y','Φ':'F','Χ':'X','Ψ':'PS','Ω':'W','Ά':'A','Έ':'E','Ί':'I','Ό':'O','Ύ':'Y','Ή':'H','Ώ':'W','Ϊ':'I','Ϋ':'Y'},TURKISH_MAP={'ş':'s','Ş':'S','ı':'i','İ':'I','ç':'c','Ç':'C','ü':'u','Ü':'U','ö':'o','Ö':'O','ğ':'g','Ğ':'G'},RUSSIAN_MAP={'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo','ж':'zh','з':'z','и':'i','й':'j','к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f'
,'х':'h','ц':'c','ч':'ch','ш':'sh','щ':'shch','ъ':'','ы':'y','ь':'','э':'e','ю':'yu','я':'ya','А':'A','Б':'B','В':'V','Г':'G','Д':'D','Е':'E','Ё':'Yo','Ж':'Zh','З':'Z','И':'I','Й':'J','К':'K','Л':'L','М':'M','Н':'N','О':'O','П':'P','Р':'R','С':'S','Т':'T','У':'U','Ф':'F','Х':'H','Ц':'C','Ч':'Ch','Ш':'Sh','Щ':'Shch','Ъ':'','Ы':'Y','Ь':'','Э':'E','Ю':'Yu','Я':'Ya'},UKRAINIAN_MAP={'Є':'Ye','І':'I','Ї':'Yi','Ґ':'G','є':'ye','і':'i','ї':'yi','ґ':'g'},CZECH_MAP={'č':'c','ď':'d','ě':'e','ň':'n','ř':'r','š':'s','ť':'t','ů':'u','ž':'z','Č':'C','Ď':'D','Ě':'E','Ň':'N','Ř':'R','Š':'S','Ť':'T','Ů':'U','Ž':'Z'},POLISH_MAP={'ą':'a','ć':'c','ę':'e','ł':'l','ń':'n','ó':'o','ś':'s','ź':'z','ż':'z','Ą':'A','Ć':'C','Ę':'E','Ł':'L','Ń':'N','Ó':'O','Ś':'S','Ź':'Z','Ż':'Z'},LATVIAN_MAP={'ā':'a','č':'c','ē':'e','ģ':'g','ī':'i','ķ':'k','ļ':'l','ņ':'n','š':'s','ū':'u','ž':'z','Ā':'A','Č':'C','Ē':'E','Ģ':'G',
'Ī':'I','Ķ':'K','Ļ':'L','Ņ':'N','Š':'S','Ū':'U','Ž':'Z'},ARABIC_MAP={'أ':'a','ب':'b','ت':'t','ث':'th','ج':'g','ح':'h','خ':'kh','د':'d','ذ':'th','ر':'r','ز':'z','س':'s','ش':'sh','ص':'s','ض':'d','ط':'t','ظ':'th','ع':'aa','غ':'gh','ف':'f','ق':'k','ك':'k','ل':'l','م':'m','ن':'n','ه':'h','و':'o','ي':'y'},PERSIAN_MAP={'آ':'a','ا':'a','پ':'p','چ':'ch','ژ':'zh','ک':'k','گ':'gh','ی':'y'},LITHUANIAN_MAP={'ą':'a','č':'c','ę':'e','ė':'e','į':'i','š':'s','ų':'u','ū':'u','ž':'z','Ą':'A','Č':'C','Ę':'E','Ė':'E','Į':'I','Š':'S','Ų':'U','Ū':'U','Ž':'Z'},SERBIAN_MAP={'ђ':'dj','ј':'j','љ':'lj','њ':'nj','ћ':'c','џ':'dz','đ':'d','Ђ':'Dj','Ј':'j','Љ':'Lj','Њ':'Nj','Ћ':'C','Џ':'Dz','Đ':'D'},AZERBAIJANI_MAP={'ç':'c','ə':'e','ğ':'g','ı':'i','ö':'o','ş':'s','ü':'u','Ç':'C','Ə':'E','Ğ':'G','İ':'I','Ö':'O','Ş':'S','Ü':'U'},ROMANIAN_MAP={'ă':'a','â':'a','î':'i','ș':'s','ț':'t','Ă':'A','Â':'A','Î':'I','Ș':'S','Ț':'T'}
,BELARUSIAN_MAP={'ў':'w','Ў':'W'},SPECIFIC_MAPS={'de':{'Ä':'AE','Ö':'OE','Ü':'UE','ä':'ae','ö':'oe','ü':'ue'}},ALL_MAPS=[VIETNAMESE_MAP,LATIN_MAP,LATIN_SYMBOLS_MAP,GREEK_MAP,TURKISH_MAP,RUSSIAN_MAP,UKRAINIAN_MAP,CZECH_MAP,POLISH_MAP,LATVIAN_MAP,ARABIC_MAP,PERSIAN_MAP,LITHUANIAN_MAP,SERBIAN_MAP,AZERBAIJANI_MAP,ROMANIAN_MAP,BELARUSIAN_MAP]
var removeList=["a","an","as","at","before","but","by","for","from","is","in","into","like","of","off","on","onto","per","since","than","the","this","that","to","up","via","with"]
var locale=$('meta[name="backend-locale"]').attr('content')
var Downcoder={Initialize:function(){if(Downcoder.map){return;}Downcoder.map={};Downcoder.chars=[];if(typeof SPECIFIC_MAPS[locale]==='object'){ALL_MAPS.push(SPECIFIC_MAPS[locale]);}for(var i=0;i<ALL_MAPS.length;i++){var lookup=ALL_MAPS[i];for(var c in lookup){if(lookup.hasOwnProperty(c)){Downcoder.map[c]=lookup[c];}}}for(var k in Downcoder.map){if(Downcoder.map.hasOwnProperty(k)){Downcoder.chars.push(k);}}Downcoder.regex=new RegExp(Downcoder.chars.join('|'),'g');}}
var InputPreset=function(element,options){var $el=this.$el=$(element)
this.options=options||{}
this.cancelled=false
var parent=options.inputPresetClosestParent!==undefined?$el.closest(options.inputPresetClosestParent):undefined,self=this,prefix=''
if(options.inputPresetPrefixInput!==undefined)prefix=$(options.inputPresetPrefixInput,parent).val()
if(prefix===undefined)prefix=''
if($el.val().length&&$el.val()!=prefix)return
$el.val(prefix).trigger('oc.inputPreset.afterUpdate')
this.$src=$(options.inputPreset,parent)
this.$src.on('input paste',function(event){if(self.cancelled)return
var timeout=event.type==='paste'?100:0
var updateValue=function(self,el,prefix){if(el.data('update')===false){return}el.val(prefix+self.formatValue()).trigger('oc.inputPreset.afterUpdate')}
var src=$(this)
setTimeout(function(){$el.trigger('oc.inputPreset.beforeUpdate',[src])
setTimeout(updateValue,100,self,$el,prefix)},timeout)})
this.$el.on('change',function(){self.cancelled=true})}
InputPreset.prototype.formatNamespace=function(){var value=this.toCamel(this.$src.val())
return value.substr(0,1).toUpperCase()+value.substr(1)}
InputPreset.prototype.formatValue=function(){if(this.options.inputPresetType=='exact'){return this.$src.val();}else if(this.options.inputPresetType=='namespace'){return this.formatNamespace()}if(this.options.inputPresetType=='camel'){var value=this.toCamel(this.$src.val())}else{var value=this.slugify(this.$src.val())}if(this.options.inputPresetType=='url'){value='/'+value}return value.replace(/\s/gi,"-")}
InputPreset.prototype.toCamel=function(slug,numChars){Downcoder.Initialize()
slug=slug.replace(Downcoder.regex,function(m){return Downcoder.map[m]})
slug=this.removeStopWords(slug);slug=slug.toLowerCase()
slug=slug.replace(/(\b|-)\w/g,function(m){return m.toUpperCase();});slug=slug.replace(/[^-\w\s]/g,'')
slug=slug.replace(/^\s+|\s+$/g,'')
slug=slug.replace(/[-\s]+/g,'')
slug=slug.substr(0,1).toLowerCase()+slug.substr(1);return slug.substring(0,numChars)}
InputPreset.prototype.slugify=function(slug,numChars){Downcoder.Initialize()
slug=slug.replace(Downcoder.regex,function(m){return Downcoder.map[m]})
slug=this.removeStopWords(slug);slug=slug.replace(/[^-\w\s]/g,'')
slug=slug.replace(/^\s+|\s+$/g,'')
slug=slug.replace(/[-\s]+/g,'-')
slug=slug.toLowerCase()
return slug.substring(0,numChars)}
InputPreset.prototype.removeStopWords=function(str){if(this.options.inputPresetRemoveWords){var regex=new RegExp('\\b('+removeList.join('|')+')\\b','gi')
str=str.replace(regex,'')}return str;}
InputPreset.DEFAULTS={inputPreset:'',inputPresetType:'slug',inputPresetClosestParent:undefined,inputPresetPrefixInput:undefined,inputPresetRemoveWords:true}
var old=$.fn.inputPreset
$.fn.inputPreset=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.inputPreset')
var options=$.extend({},InputPreset.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.inputPreset',(data=new InputPreset(this,options)))})}
$.fn.inputPreset.Constructor=InputPreset
$.fn.inputPreset.noConflict=function(){$.fn.inputPreset=old
return this}
$(document).render(function(){$('[data-input-preset]').inputPreset()})}(window.jQuery);+function($){"use strict";var TriggerOn=function(element,options){var $el=this.$el=$(element);this.options=options||{};if(this.options.triggerCondition===false)throw new Error('Trigger condition is not specified.')
if(this.options.trigger===false)throw new Error('Trigger selector is not specified.')
if(this.options.triggerAction===false)throw new Error('Trigger action is not specified.')
this.triggerCondition=this.options.triggerCondition
if(this.options.triggerCondition.indexOf('value')==0){var match=this.options.triggerCondition.match(/[^[\]]+(?=])/g)
this.triggerCondition='value'
this.triggerConditionValue=(match)?match:[""]}this.triggerParent=undefined
if(this.options.triggerClosestParent!==undefined){var closestParentElements=this.options.triggerClosestParent.split(',')
for(var i=0;i<closestParentElements.length;i++){var $triggerElement=$el.closest(closestParentElements[i])
if($triggerElement.length){this.triggerParent=$triggerElement
break}}}if(this.triggerCondition=='checked'||this.triggerCondition=='unchecked'||this.triggerCondition=='value'){$(document).on('change',this.options.trigger,$.proxy(this.onConditionChanged,this))}var self=this
$el.on('oc.triggerOn.update',function(e){e.stopPropagation()
self.onConditionChanged()})
self.onConditionChanged()}
TriggerOn.prototype.onConditionChanged=function(){if(this.triggerCondition=='checked'){this.updateTarget(!!$(this.options.trigger+':checked',this.triggerParent).length)}else if(this.triggerCondition=='unchecked'){this.updateTarget(!$(this.options.trigger+':checked',this.triggerParent).length)}else if(this.triggerCondition=='value'){var trigger,triggered=false
trigger=$(this.options.trigger,this.triggerParent).not('input[type=checkbox], input[type=radio], input[type=button], input[type=submit]')
if(!trigger.length){trigger=$(this.options.trigger,this.triggerParent).not(':not(input[type=checkbox]:checked, input[type=radio]:checked)')}var self=this
trigger.each(function(){var triggerValue=$(this).val();$.each($.isArray(triggerValue)?triggerValue:[triggerValue],function(key,val){triggered=$.inArray(val,self.triggerConditionValue)!=-1
return!triggered})
return!triggered})
this.updateTarget(triggered)}}
TriggerOn.prototype.updateTarget=function(status){var self=this,actions=this.options.triggerAction.split('|')
$.each(actions,function(index,action){self.updateTargetAction(action,status)})
$(window).trigger('resize')
this.$el.trigger('oc.triggerOn.afterUpdate',status)}
TriggerOn.prototype.updateTargetAction=function(action,status){if(action=='show'){this.$el.toggleClass('hide',!status).trigger('hide.oc.triggerapi',[!status])}else if(action=='hide'){this.$el.toggleClass('hide',status).trigger('hide.oc.triggerapi',[status])}else if(action=='enable'){this.$el.prop('disabled',!status).toggleClass('control-disabled',!status).trigger('disable.oc.triggerapi',[!status])}else if(action=='disable'){this.$el.prop('disabled',status).toggleClass('control-disabled',status).trigger('disable.oc.triggerapi',[status])}else if(action=='empty'&&status){this.$el.not('input[type=checkbox], input[type=radio], input[type=button], input[type=submit]').val('')
this.$el.not(':not(input[type=checkbox], input[type=radio])').prop('checked',false)
this.$el.trigger('empty.oc.triggerapi').trigger('change')}if(action=='show'||action=='hide'){this.fixButtonClasses()}}
TriggerOn.prototype.fixButtonClasses=function(){var group=this.$el.closest('.btn-group')
if(group.length>0&&this.$el.is(':last-child'))this.$el.prev().toggleClass('last',this.$el.hasClass('hide'))}
TriggerOn.DEFAULTS={triggerAction:false,triggerCondition:false,triggerClosestParent:undefined,trigger:false}
var old=$.fn.triggerOn
$.fn.triggerOn=function(option){return this.each(function(){var $this=$(this)
var data=$this.data('oc.triggerOn')
var options=$.extend({},TriggerOn.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.triggerOn',(data=new TriggerOn(this,options)))})}
$.fn.triggerOn.Constructor=TriggerOn
$.fn.triggerOn.noConflict=function(){$.fn.triggerOn=old
return this}
$(document).render(function(){$('[data-trigger]').triggerOn()})}(window.jQuery);+function($){"use strict";var DragValue=function(element,options){this.options=options
this.$el=$(element)
this.init()}
DragValue.DEFAULTS={dragClick:false}
DragValue.prototype.init=function(){this.$el.prop('draggable',true)
this.textValue=this.$el.data('textValue')
this.$el.on('dragstart',$.proxy(this.handleDragStart,this))
this.$el.on('drop',$.proxy(this.handleDrop,this))
this.$el.on('dragend',$.proxy(this.handleDragEnd,this))
if(this.options.dragClick){this.$el.on('click',$.proxy(this.handleClick,this))
this.$el.on('mouseover',$.proxy(this.handleMouseOver,this))}}
DragValue.prototype.handleDragStart=function(event){var e=event.originalEvent
e.dataTransfer.effectAllowed='all'
e.dataTransfer.setData('text/plain',this.textValue)
this.$el.css({opacity:0.5}).addClass('dragvalue-dragging')}
DragValue.prototype.handleDrop=function(event){event.stopPropagation()
return false}
DragValue.prototype.handleDragEnd=function(event){this.$el.css({opacity:1}).removeClass('dragvalue-dragging')}
DragValue.prototype.handleMouseOver=function(event){var el=document.activeElement
if(!el)return
if(el.isContentEditable||(el.tagName.toLowerCase()=='input'&&el.type=='text'||el.tagName.toLowerCase()=='textarea')){this.lastElement=el}}
DragValue.prototype.handleClick=function(event){if(!this.lastElement)return
var $el=$(this.lastElement)
if($el.hasClass('ace_text-input'))return this.handleClickCodeEditor(event,$el)
if(this.lastElement.isContentEditable)return this.handleClickContentEditable()
this.insertAtCaret(this.lastElement,this.textValue)}
DragValue.prototype.handleClickCodeEditor=function(event,$el){var $editorArea=$el.closest('[data-control=codeeditor]')
if(!$editorArea.length)return
$editorArea.codeEditor('getEditorObject').insert(this.textValue)}
DragValue.prototype.handleClickContentEditable=function(){var sel,range,html;if(window.getSelection){sel=window.getSelection();if(sel.getRangeAt&&sel.rangeCount){range=sel.getRangeAt(0);range.deleteContents();range.insertNode(document.createTextNode(this.textValue));}}else if(document.selection&&document.selection.createRange){document.selection.createRange().text=this.textValue;}}
DragValue.prototype.insertAtCaret=function(el,insertValue){if(document.selection){el.focus()
var sel=document.selection.createRange()
sel.text=insertValue
el.focus()}else if(el.selectionStart||el.selectionStart=='0'){var startPos=el.selectionStart,endPos=el.selectionEnd,scrollTop=el.scrollTop
el.value=el.value.substring(0,startPos)+insertValue+el.value.substring(endPos,el.value.length)
el.focus()
el.selectionStart=startPos+insertValue.length
el.selectionEnd=startPos+insertValue.length
el.scrollTop=scrollTop}else{el.value+=insertValue
el.focus()}}
var old=$.fn.dragValue
$.fn.dragValue=function(option){var args=Array.prototype.slice.call(arguments,1),result
this.each(function(){var $this=$(this)
var data=$this.data('oc.dragvalue')
var options=$.extend({},DragValue.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.dragvalue',(data=new DragValue(this,options)))
if(typeof option=='string')result=data[option].apply(data,args)
if(typeof result!='undefined')return false})
return result?result:this}
$.fn.dragValue.Constructor=DragValue
$.fn.dragValue.noConflict=function(){$.fn.dragValue=old
return this}
$(document).render(function(){$('[data-control="dragvalue"]').dragValue()});}(window.jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var Sortable=function(element,options){this.$el=$(element)
this.options=options||{}
this.cursorAdjustment=null
$.wn.foundation.controlUtils.markDisposable(element)
Base.call(this)
this.init()}
Sortable.prototype=Object.create(BaseProto)
Sortable.prototype.constructor=Sortable
Sortable.prototype.init=function(){this.$el.one('dispose-control',this.proxy(this.dispose))
var self=this,sortableOverrides={},sortableDefaults={onDragStart:this.proxy(this.onDragStart),onDrag:this.proxy(this.onDrag),onDrop:this.proxy(this.onDrop)}
if(this.options.onDragStart){sortableOverrides.onDragStart=function($item,container,_super,event){self.options.onDragStart($item,container,sortableDefaults.onDragStart,event)}}if(this.options.onDrag){sortableOverrides.onDrag=function($item,position,_super,event){self.options.onDrag($item,position,sortableDefaults.onDrag,event)}}if(this.options.onDrop){sortableOverrides.onDrop=function($item,container,_super,event){self.options.onDrop($item,container,sortableDefaults.onDrop,event)}}this.$el.jqSortable($.extend({},sortableDefaults,this.options,sortableOverrides))}
Sortable.prototype.dispose=function(){this.$el.jqSortable('destroy')
this.$el.off('dispose-control',this.proxy(this.dispose))
this.$el.removeData('oc.sortable')
this.$el=null
this.options=null
this.cursorAdjustment=null
BaseProto.dispose.call(this)}
Sortable.prototype.onDragStart=function($item,container,_super,event){var offset=$item.offset(),pointer=container.rootGroup.pointer
if(pointer){this.cursorAdjustment={left:pointer.left-offset.left,top:pointer.top-offset.top}}else{this.cursorAdjustment=null}if(this.options.tweakCursorAdjustment){this.cursorAdjustment=this.options.tweakCursorAdjustment(this.cursorAdjustment)}$item.css({height:$item.height(),width:$item.width()})
$item.addClass('dragged')
$('body').addClass('dragging')
this.$el.addClass('dragging')
if(this.options.useAnimation){$item.data('oc.animated',true)}if(this.options.usePlaceholderClone){$(container.rootGroup.placeholder).html($item.html())}if(!this.options.useDraggingClone){$item.hide()}}
Sortable.prototype.onDrag=function($item,position,_super,event){if(this.cursorAdjustment){$item.css({left:position.left-this.cursorAdjustment.left,top:position.top-this.cursorAdjustment.top})}else{$item.css(position)}}
Sortable.prototype.onDrop=function($item,container,_super,event){$item.removeClass('dragged').removeAttr('style')
$('body').removeClass('dragging')
this.$el.removeClass('dragging')
if($item.data('oc.animated')){$item.hide().slideDown(200)}}
Sortable.prototype.enable=function(){this.$el.jqSortable('enable')}
Sortable.prototype.disable=function(){this.$el.jqSortable('disable')}
Sortable.prototype.refresh=function(){this.$el.jqSortable('refresh')}
Sortable.prototype.serialize=function(){this.$el.jqSortable('serialize')}
Sortable.prototype.destroy=function(){this.dispose()}
Sortable.prototype.destroyGroup=function(){var jqSortable=this.$el.data('jqSortable')
if(jqSortable.group){jqSortable.group._destroy()}}
Sortable.DEFAULTS={useAnimation:false,usePlaceholderClone:false,useDraggingClone:true,tweakCursorAdjustment:null}
var old=$.fn.sortable
$.fn.sortable=function(option){var args=arguments;return this.each(function(){var $this=$(this)
var data=$this.data('oc.sortable')
var options=$.extend({},Sortable.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.sortable',(data=new Sortable(this,options)))
if(typeof option=='string')data[option].apply(data,args)})}
$.fn.sortable.Constructor=Sortable
$.fn.sortable.noConflict=function(){$.fn.sortable=old
return this}}(window.jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var DragScroll=function(element,options){this.options=$.extend({},DragScroll.DEFAULTS,options)
var $el=$(element),el=$el.get(0),dragStart=0,startOffset=0,self=this,dragging=false,eventElementName=this.options.vertical?'pageY':'pageX',isNative=this.options.useNative&&$('html').hasClass('mobile');this.el=$el
this.scrollClassContainer=this.options.scrollClassContainer?$(this.options.scrollClassContainer):$el
this.isScrollable=true
Base.call(this)
if(this.options.scrollMarkerContainer){$(this.options.scrollMarkerContainer).append($('<span class="before scroll-marker"></span><span class="after scroll-marker"></span>'))}var $scrollSelect=this.options.scrollSelector?$(this.options.scrollSelector,$el):$el
$scrollSelect.mousewheel(function(event){if(!self.options.useScroll){return;}var offset,offsetX=event.deltaFactor*event.deltaX,offsetY=event.deltaFactor*event.deltaY
if(!offsetX&&self.options.useComboScroll){offset=offsetY*-1}else if(!offsetY&&self.options.useComboScroll){offset=offsetX}else{offset=self.options.vertical?(offsetY*-1):offsetX}return!scrollWheel(offset)})
if(this.options.useDrag){$el.on('mousedown.dragScroll',this.options.dragSelector,function(event){if(event.target&&event.target.tagName==='INPUT'){return}if(!self.isScrollable){return}startDrag(event)
return false})}if(Modernizr.touchevents){$el.on('touchstart.dragScroll',this.options.dragSelector,function(event){var touchEvent=event.originalEvent
if(touchEvent.touches.length==1){startDrag(touchEvent.touches[0])
event.stopPropagation()}})}$el.on('click.dragScroll',function(){if($(document.body).hasClass(self.options.dragClass)){return false}})
$(document).on('ready',this.proxy(this.fixScrollClasses))
$(window).on('resize',this.proxy(this.fixScrollClasses))
function startDrag(event){dragStart=event[eventElementName]
startOffset=self.options.vertical?$el.scrollTop():$el.scrollLeft()
if(Modernizr.touchevents){$(window).on('touchmove.dragScroll',function(event){var touchEvent=event.originalEvent
moveDrag(touchEvent.touches[0])
if(!isNative){event.preventDefault()}})
$(window).on('touchend.dragScroll',function(event){stopDrag()})}$(window).on('mousemove.dragScroll',function(event){moveDrag(event)
return false})
$(window).on('mouseup.dragScroll',function(mouseUpEvent){var isClick=event.pageX==mouseUpEvent.pageX&&event.pageY==mouseUpEvent.pageY
stopDrag(isClick)
return false})}function moveDrag(event){var current=event[eventElementName],offset=dragStart-current
if(Math.abs(offset)>3){if(!dragging){dragging=true
$el.trigger('start.oc.dragScroll')
self.options.start()
$(document.body).addClass(self.options.dragClass)}if(!isNative){self.options.vertical?$el.scrollTop(startOffset+offset):$el.scrollLeft(startOffset+offset)}$el.trigger('drag.oc.dragScroll')
self.options.drag()}}function stopDrag(click){$(window).off('.dragScroll')
dragging=false;if(click){$(document.body).removeClass(self.options.dragClass)}else{self.fixScrollClasses()}window.setTimeout(function(){if(!click){$(document.body).removeClass(self.options.dragClass)
$el.trigger('stop.oc.dragScroll')
self.options.stop()
self.fixScrollClasses()}},100)}function scrollWheel(offset){startOffset=self.options.vertical?el.scrollTop:el.scrollLeft
self.options.vertical?$el.scrollTop(startOffset+offset):$el.scrollLeft(startOffset+offset)
var scrolled=self.options.vertical?el.scrollTop!=startOffset:el.scrollLeft!=startOffset
$el.trigger('drag.oc.dragScroll')
self.options.drag()
if(scrolled){if(self.wheelUpdateTimer!==undefined&&self.wheelUpdateTimer!==false)window.clearInterval(self.wheelUpdateTimer);self.wheelUpdateTimer=window.setTimeout(function(){self.wheelUpdateTimer=false;self.fixScrollClasses()},100);}return scrolled}this.fixScrollClasses();}
DragScroll.prototype=Object.create(BaseProto)
DragScroll.prototype.constructor=DragScroll
DragScroll.DEFAULTS={vertical:false,useDrag:true,useScroll:true,useNative:false,useComboScroll:true,scrollClassContainer:false,scrollMarkerContainer:false,scrollSelector:null,dragSelector:null,dragClass:'drag',start:function(){},drag:function(){},stop:function(){}}
DragScroll.prototype.fixScrollClasses=function(){var isStart=this.isStart(),isEnd=this.isEnd()
this.scrollClassContainer.toggleClass('scroll-before',!isStart)
this.scrollClassContainer.toggleClass('scroll-after',!isEnd)
this.scrollClassContainer.toggleClass('scroll-active-before',this.isActiveBefore())
this.scrollClassContainer.toggleClass('scroll-active-after',this.isActiveAfter())
this.isScrollable=!isStart||!isEnd}
DragScroll.prototype.isStart=function(){if(!this.options.vertical){return this.el.scrollLeft()<=0;}else{return this.el.scrollTop()<=0;}}
DragScroll.prototype.isEnd=function(){if(!this.options.vertical){return(this.el[0].scrollWidth-(this.el.scrollLeft()+this.el.width()))<=0}else{return(this.el[0].scrollHeight-(this.el.scrollTop()+this.el.height()))<=0}}
DragScroll.prototype.goToStart=function(){if(!this.options.vertical){return this.el.scrollLeft(0)}else{return this.el.scrollTop(0)}}
DragScroll.prototype.isActiveAfter=function(){var activeElement=$('.active',this.el);if(activeElement.length==0){return false}if(!this.options.vertical){return activeElement.get(0).offsetLeft>(this.el.scrollLeft()+this.el.width())}else{return activeElement.get(0).offsetTop>(this.el.scrollTop()+this.el.height())}}
DragScroll.prototype.isActiveBefore=function(){var activeElement=$('.active',this.el);if(activeElement.length==0){return false}if(!this.options.vertical){return(activeElement.get(0).offsetLeft+activeElement.width())<this.el.scrollLeft()}else{return(activeElement.get(0).offsetTop+activeElement.height())<this.el.scrollTop()}}
DragScroll.prototype.goToElement=function(element,callback,options){var $el=$(element)
if(!$el.length)return;var self=this,params={duration:300,queue:false,complete:function(){self.fixScrollClasses()
if(callback!==undefined)callback()}}
params=$.extend(params,options||{})
var offset=0,animated=false
if(!this.options.vertical){offset=$el.get(0).offsetLeft-this.el.scrollLeft()
if(offset<0){this.el.animate({'scrollLeft':$el.get(0).offsetLeft},params)
animated=true}else{offset=$el.get(0).offsetLeft+$el.width()-(this.el.scrollLeft()+this.el.width())
if(offset>0){this.el.animate({'scrollLeft':$el.get(0).offsetLeft+$el.width()-this.el.width()},params)
animated=true}}}else{offset=$el.get(0).offsetTop-this.el.scrollTop()
if(offset<0){this.el.animate({'scrollTop':$el.get(0).offsetTop},params)
animated=true}else{offset=$el.get(0).offsetTop-(this.el.scrollTop()+this.el.height())
if(offset>0){this.el.animate({'scrollTop':$el.get(0).offsetTop+$el.height()-this.el.height()},params)
animated=true}}}if(!animated&&callback!==undefined){callback()}}
DragScroll.prototype.dispose=function(){this.scrollClassContainer=null
$(document).off('ready',this.proxy(this.fixScrollClasses))
$(window).off('resize',this.proxy(this.fixScrollClasses))
this.el.off('.dragScroll')
this.el.removeData('oc.dragScroll')
this.el=null
BaseProto.dispose.call(this)}
var old=$.fn.dragScroll
$.fn.dragScroll=function(option){var args=arguments;return this.each(function(){var $this=$(this)
var data=$this.data('oc.dragScroll')
var options=typeof option=='object'&&option
if(!data)$this.data('oc.dragScroll',(data=new DragScroll(this,options)))
if(typeof option=='string'){var methodArgs=[];for(var i=1;i<args.length;i++)methodArgs.push(args[i])
data[option].apply(data,methodArgs)}})}
$.fn.dragScroll.Constructor=DragScroll
$.fn.dragScroll.noConflict=function(){$.fn.dragScroll=old
return this}}(window.jQuery);+function($){"use strict";var Tab=function(element,options){var $el=this.$el=$(element);this.options=options||{}
this.$tabsContainer=$('.nav-tabs:first',$el)
this.$pagesContainer=$('.tab-content:first',$el)
this.tabId='tabs'+$el.parents().length+Math.round(Math.random()*1000);if(this.options.closable!==undefined&&this.options.closable!==false)$el.attr('data-closable','')
this.init()}
Tab.prototype.init=function(){var self=this;this.options.slidable=this.options.slidable!==undefined&&this.options.slidable!==false
$('> li',this.$tabsContainer).each(function(index){self.initTab(this)})
this.$el.on('close.oc.tab',function(ev,data){ev.preventDefault()
var force=(data!==undefined&&data.force!==undefined)?data.force:false;self.closeTab($(ev.target).closest('ul.nav-tabs > li, div.tab-content > div'),force)})
this.$el.on('mousedown',"li[data-tab-id]",function(ev){if(ev.key==='2'){$(ev.target).trigger('close.oc.tab');}})
this.$el.on('toggleCollapse.oc.tab',function(ev,data){ev.preventDefault()
$(ev.target).closest('div.tab-content > div').toggleClass('collapsed')})
this.$el.on('modified.oc.tab',function(ev){ev.preventDefault()
self.modifyTab($(ev.target).closest('ul.nav-tabs > li, div.tab-content > div'))})
this.$el.on('unmodified.oc.tab',function(ev){ev.preventDefault()
self.unmodifyTab($(ev.target).closest('ul.nav-tabs > li, div.tab-content > div'))})
this.$tabsContainer.on('shown.bs.tab','li',function(){$(window).trigger('oc.updateUi')
var tabUrl=$('> a',this).data('tabUrl')
if(!tabUrl&&$(this).parent('ul').is('[data-linkable]')){tabUrl=$('> a',this).attr('href')}if(tabUrl){window.history.replaceState({},'Tab link reference',tabUrl)}})
if(this.options.slidable){this.$pagesContainer.touchwipe({wipeRight:function(){self.prev();},wipeLeft:function(){self.next();},preventDefaultEvents:false,min_move_x:60});}this.$tabsContainer.toolbar({scrollClassContainer:this.$el})
this.updateClasses()
if(location.hash&&this.$tabsContainer.is('[data-linkable]')){$('li > a[href="'+location.hash+'"]',this.$tabsContainer).tab('show')}}
Tab.prototype.initTab=function(li){var $tabs=$('>li',this.$tabsContainer),tabIndex=$tabs.index(li),time=new Date().getTime(),targetId=this.tabId+'-tab-'+tabIndex+time,$anchor=$('a',li)
$anchor.data('target','#'+targetId).attr('data-target','#'+targetId).attr('data-toggle','tab')
if(!$anchor.attr('title'))$anchor.attr('title',$anchor.text().trim())
if($anchor.find('> span.title > span').length<1){var html=$anchor.html()
$anchor.html('').append($('<span class="title"></span>').append($('<span></span>').html(html)))}var pane=$('> .tab-pane',this.$pagesContainer).eq(tabIndex).attr('id',targetId)
if(!$('span.tab-close',li).length){$(li).append($('<span class="tab-close"><i>&times;</i></span>').click(function(){$(this).trigger('close.oc.tab')
return false}))}pane.data('tab',li)
this.$el.trigger('initTab.oc.tab',[{'pane':pane,'tab':li}])}
Tab.prototype.addTab=function(title,content,identifier,tabClass){var processedTitle=this.generateTitleText(title,-1),$link=$('<a/>').attr('href','javascript:;').text(processedTitle),$li=$('<li/>'),$pane=$('<div>').html(content).addClass('tab-pane');$link.attr('title',title)
$li.append($link)
this.$tabsContainer.append($li)
this.$pagesContainer.append($pane)
if(tabClass!==undefined)$link.addClass(tabClass)
if(identifier!==undefined)$li.attr('data-tab-id',identifier)
if(this.options.paneClasses!==undefined)$pane.addClass(this.options.paneClasses)
this.initTab($li)
$link.tab('show')
$(window).trigger('resize')
this.$tabsContainer.dragScroll('goToElement',$li)
var defaultFocus=$('[default-focus]',$pane)
if(defaultFocus.is(":visible"))defaultFocus.focus()
this.updateClasses()}
Tab.prototype.updateTab=function(tab,title,content){var tabIndex=this.findTabIndex(tab)
if(tabIndex==-1)return
var processedTitle=this.generateTitleText(title,-1),$tab=$('> li',this.$tabsContainer).eq(tabIndex),$pane=$('> div',this.$pagesContainer).eq(tabIndex),$link=$('a',$tab)
$link.text(processedTitle).attr('title',title)
$pane.html(content)
this.initTab($tab)
this.updateClasses()}
Tab.prototype.generateTitleText=function(title,tabIndex){var newTitle=title
if(this.options.titleAsFileNames)newTitle=title.replace(/^.*[\\\/]/,'')
if(this.options.maxTitleSymbols&&newTitle.length>this.options.maxTitleSymbols)newTitle='...'+newTitle.substring(newTitle.length-this.options.maxTitleSymbols)
return newTitle}
Tab.prototype.closeTab=function(tab,force){var tabIndex=this.findTabIndex(tab)
if(tabIndex==-1)return
var $tab=$('> li',this.$tabsContainer).eq(tabIndex),$pane=$('> div',this.$pagesContainer).eq(tabIndex),isActive=$tab.hasClass('active'),isModified=$tab.attr('data-modified')!==undefined;if(isModified&&this.options.closeConfirmation!==undefined&&force!==true){if(!confirm(this.options.closeConfirmation))return}var e=$.Event('beforeClose.oc.tab',{relatedTarget:$pane})
this.$el.trigger(e)
if(e.isDefaultPrevented())return
$.wn.foundation.controlUtils.disposeControls($pane.get(0))
$pane.remove()
$tab.remove()
if(isActive)$('> li > a',this.$tabsContainer).eq(tabIndex-1).tab('show')
if($('> li > a',this.$tabsContainer).length==0)this.$el.trigger('afterAllClosed.oc.tab')
this.$el.trigger('closed.oc.tab',[$tab,$pane])
$(window).trigger('resize')
this.updateClasses()}
Tab.prototype.updateClasses=function(){if(this.$tabsContainer.children().length>0)this.$el.addClass('has-tabs')
else this.$el.removeClass('has-tabs')}
Tab.prototype.modifyTab=function(tab){var tabIndex=this.findTabIndex(tab)
if(tabIndex==-1)return
$('> li',this.$tabsContainer).eq(tabIndex).attr('data-modified','')
$('> div',this.$pagesContainer).eq(tabIndex).attr('data-modified','')}
Tab.prototype.unmodifyTab=function(tab){var tabIndex=this.findTabIndex(tab)
if(tabIndex==-1)return
$('> li',this.$tabsContainer).eq(tabIndex).removeAttr('data-modified')
$('> div',this.$pagesContainer).eq(tabIndex).removeAttr('data-modified')}
Tab.prototype.findTabIndex=function(tab){var tabToFind=tab
if(tab===undefined)tabToFind=$('li.active',this.$tabsContainer)
var tabParent=this.$pagesContainer
if($(tabToFind).parent().hasClass('nav-tabs'))tabParent=this.$tabsContainer
return tabParent.children().index($(tabToFind))}
Tab.prototype.findTabFromPane=function(pane){var id='#'+$(pane).attr('id'),tab=$('[data-target="'+id+'"]',this.$tabsContainer)
return tab}
Tab.prototype.findPaneFromTab=function(tab){var id=$(tab).find('> a').data('target'),pane=this.$pagesContainer.find(id)
return pane}
Tab.prototype.goTo=function(identifier){var $tab=$('[data-tab-id="'+identifier+'" ]',this.$tabsContainer)
if($tab.length==0)return false
var tabIndex=this.findTabIndex($tab)
if(tabIndex==-1)return false
this.goToIndex(tabIndex)
this.$tabsContainer.dragScroll('goToElement',$tab)
return true}
Tab.prototype.goToPane=function(pane){var $pane=$(pane),$tab=this.findTabFromPane($pane)
if($pane.length==0)return
$pane.removeClass('collapsed')
var tabIndex=this.findTabIndex($pane)
if(tabIndex==-1)return false
this.goToIndex(tabIndex)
if($tab.length>0)this.$tabsContainer.dragScroll('goToElement',$tab)
return true}
Tab.prototype.goToElement=function(element){return this.goToPane(element.closest('.tab-pane'))}
Tab.prototype.findByIdentifier=function(identifier){return $('[data-tab-id="'+identifier+'" ]',this.$tabsContainer);}
Tab.prototype.updateIdentifier=function(tab,identifier){var index=this.findTabIndex(tab)
if(index==-1)return
$('> li',this.$tabsContainer).eq(index).attr('data-tab-id',identifier)}
Tab.prototype.updateTitle=function(tab,title){var index=this.findTabIndex(tab)
if(index==-1)return
var processedTitle=this.generateTitleText(title,index),$link=$('> li > a span.title',this.$tabsContainer).eq(index)
$link.attr('title',title)
$link.text(processedTitle)}
Tab.prototype.goToIndex=function(index){$('> li > a',this.$tabsContainer).eq(index).tab('show')}
Tab.prototype.prev=function(){var tabIndex=this.findTabIndex()
if(tabIndex<=0)return
this.goToIndex(tabIndex-1)}
Tab.prototype.next=function(){var tabIndex=this.findTabIndex()
if(tabIndex==-1)return
this.goToIndex(tabIndex+1)}
Tab.DEFAULTS={}
var old=$.fn.ocTab
$.fn.ocTab=function(option){var args=arguments;return this.each(function(){var $this=$(this)
var data=$this.data('oc.tab')
var options=$.extend({},Tab.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data)$this.data('oc.tab',(data=new Tab(this,options)))
if(typeof option=='string'){var methodArgs=[];for(var i=1;i<args.length;i++)methodArgs.push(args[i])
data[option].apply(data,methodArgs)}})}
$.fn.ocTab.Constructor=Tab
$.fn.ocTab.noConflict=function(){$.fn.ocTab=old
return this}
$(document).render(function(){$('[data-control=tab]').ocTab()})
$(window).on('ajaxInvalidField',function(event,element,name,messages,isFirst){if(!isFirst)return
event.preventDefault()
var $el=$(element)
$el.closest('[data-control=tab]').ocTab('goToElement',$el)
$el.focus()})}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.inspector===undefined)$.wn.inspector={}
var Base=$.wn.foundation.base,BaseProto=Base.prototype
var Surface=function(containerElement,properties,values,inspectorUniqueId,options,parentSurface,group,propertyName){if(inspectorUniqueId===undefined){throw new Error('Inspector surface unique ID should be defined.')}this.options=$.extend({},Surface.DEFAULTS,typeof options=='object'&&options)
this.rawProperties=properties
this.parsedProperties=$.wn.inspector.engine.processPropertyGroups(properties)
this.container=containerElement
this.inspectorUniqueId=inspectorUniqueId
this.values=values!==null?values:{}
this.originalValues=$.extend(true,{},this.values)
this.idCounter=1
this.popupCounter=0
this.parentSurface=parentSurface
this.propertyName=propertyName
this.editors=[]
this.externalParameterEditors=[]
this.tableContainer=null
this.groupManager=null
this.group=null
if(group!==undefined){this.group=group}if(!this.parentSurface){this.groupManager=new $.wn.inspector.groupManager(this.inspectorUniqueId)}Base.call(this)
this.init()}
Surface.prototype=Object.create(BaseProto)
Surface.prototype.constructor=Surface
Surface.prototype.dispose=function(){this.unregisterHandlers()
this.disposeControls()
this.disposeEditors()
this.removeElements()
this.disposeExternalParameterEditors()
this.container=null
this.tableContainer=null
this.rawProperties=null
this.parsedProperties=null
this.editors=null
this.externalParameterEditors=null
this.values=null
this.originalValues=null
this.options.onChange=null
this.options.onPopupDisplayed=null
this.options.onPopupHidden=null
this.options.onGetInspectableElement=null
this.parentSurface=null
this.groupManager=null
this.group=null
BaseProto.dispose.call(this)}
Surface.prototype.init=function(){if(this.groupManager&&!this.group){this.group=this.groupManager.createGroup('root')}this.build()
if(!this.parentSurface){$.wn.foundation.controlUtils.markDisposable(this.tableContainer)}this.registerHandlers()}
Surface.prototype.registerHandlers=function(){if(!this.parentSurface){$(this.tableContainer).one('dispose-control',this.proxy(this.dispose))
$(this.tableContainer).on('click','tr.group, tr.control-group',this.proxy(this.onGroupClick))
$(this.tableContainer).on('focus-control',this.proxy(this.focusFirstEditor))}}
Surface.prototype.unregisterHandlers=function(){if(!this.parentSurface){$(this.tableContainer).off('dispose-control',this.proxy(this.dispose))
$(this.tableContainer).off('click','tr.group, tr.control-group',this.proxy(this.onGroupClick))
$(this.tableContainer).off('focus-control',this.proxy(this.focusFirstEditor))}}
Surface.prototype.build=function(){this.tableContainer=document.createElement('div')
var dataTable=document.createElement('table'),tbody=document.createElement('tbody')
$.wn.foundation.element.addClass(dataTable,'inspector-fields')
if(this.parsedProperties.hasGroups){$.wn.foundation.element.addClass(dataTable,'has-groups')}var currentGroup=this.group
for(var i=0,len=this.parsedProperties.properties.length;i<len;i++){var property=this.parsedProperties.properties[i]
if(property.itemType=='group'){currentGroup=this.getGroupManager().createGroup(property.groupIndex,this.group)}else{if(property.groupIndex===undefined){currentGroup=this.group}}var row=this.buildRow(property,currentGroup)
if(property.itemType=='group'){this.applyGroupLevelToRow(row,currentGroup.parentGroup)}else{this.applyGroupLevelToRow(row,currentGroup)}tbody.appendChild(row)
this.buildEditor(row,property,dataTable,currentGroup)}dataTable.appendChild(tbody)
this.tableContainer.appendChild(dataTable)
this.container.appendChild(this.tableContainer)
if(this.options.enableExternalParameterEditor){this.buildExternalParameterEditor(tbody)}if(!this.parentSurface){this.focusFirstEditor()}}
Surface.prototype.moveToContainer=function(newContainer){this.container=newContainer
this.container.appendChild(this.tableContainer)}
Surface.prototype.buildRow=function(property,group){var row=document.createElement('tr'),th=document.createElement('th'),titleSpan=document.createElement('span'),description=this.buildPropertyDescription(property)
if(property.property){row.setAttribute('data-property',property.property)
row.setAttribute('data-property-path',this.getPropertyPath(property.property))}this.applyGroupIndexAttribute(property,row,group)
$.wn.foundation.element.addClass(row,this.getRowCssClass(property,group))
this.applyHeadColspan(th,property)
titleSpan.setAttribute('class','title-element')
titleSpan.setAttribute('title',this.escapeJavascriptString(property.title))
this.buildGroupExpandControl(titleSpan,property,false,false,group)
titleSpan.innerHTML+=this.escapeJavascriptString(property.title)
var outerDiv=document.createElement('div'),innerDiv=document.createElement('div')
innerDiv.appendChild(titleSpan)
if(description){innerDiv.appendChild(description)}outerDiv.appendChild(innerDiv)
th.appendChild(outerDiv)
row.appendChild(th)
return row}
Surface.prototype.focusFirstEditor=function(){if(this.editors.length==0){return}var groupManager=this.getGroupManager()
for(var i=0,len=this.editors.length;i<len;i++){var editor=this.editors[i],group=editor.parentGroup
if(group&&!this.groupManager.isGroupExpanded(group)){continue}var externalParameterEditor=this.findExternalParameterEditor(editor.getPropertyName())
if(externalParameterEditor&&externalParameterEditor.isEditorVisible()){externalParameterEditor.focus()
return}editor.focus()
return}}
Surface.prototype.getRowCssClass=function(property,group){var result=property.itemType
if(property.itemType=='property'){if(group.parentGroup){result+=this.getGroupManager().isGroupExpanded(group)?' expanded':' collapsed'}}if(property.itemType=='property'&&!property.showExternalParam){result+=' no-external-parameter'}return result}
Surface.prototype.applyHeadColspan=function(th,property){if(property.itemType=='group'){th.setAttribute('colspan',2)}}
Surface.prototype.buildGroupExpandControl=function(titleSpan,property,force,hasChildSurface,group){if(property.itemType!=='group'&&!force){return}var groupIndex=this.getGroupManager().getGroupIndex(group),statusClass=this.getGroupManager().isGroupExpanded(group)?'expanded':'',anchor=document.createElement('a')
anchor.setAttribute('class','expandControl '+statusClass)
anchor.setAttribute('href','javascript:;')
anchor.innerHTML='<span>Expand/collapse</span>'
titleSpan.appendChild(anchor)}
Surface.prototype.buildPropertyDescription=function(property){if(property.description===undefined||property.description===null){return null}var span=document.createElement('span')
span.setAttribute('title',this.escapeJavascriptString(property.description))
span.setAttribute('class','info wn-icon-info with-tooltip')
$(span).tooltip({placement:'auto right',container:'body',delay:500})
return span}
Surface.prototype.buildExternalParameterEditor=function(tbody){var rows=tbody.children
for(var i=0,len=rows.length;i<len;i++){var row=rows[i],property=row.getAttribute('data-property')
if($.wn.foundation.element.hasClass(row,'no-external-parameter')||!property){continue}var propertyEditor=this.findPropertyEditor(property)
if(propertyEditor&&!propertyEditor.supportsExternalParameterEditor()){continue}var cell=row.querySelector('td'),propertyDefinition=this.findPropertyDefinition(property),initialValue=this.getPropertyValue(property)
if(initialValue===undefined){initialValue=propertyEditor.getUndefinedValue()}var editor=new $.wn.inspector.externalParameterEditor(this,propertyDefinition,cell,initialValue)
this.externalParameterEditors.push(editor)}}
Surface.prototype.applyGroupIndexAttribute=function(property,row,group,isGroupedControl){if(property.itemType=='group'||isGroupedControl){row.setAttribute('data-group-index',this.getGroupManager().getGroupIndex(group))
row.setAttribute('data-parent-group-index',this.getGroupManager().getGroupIndex(group.parentGroup))}else{if(group.parentGroup){row.setAttribute('data-parent-group-index',this.getGroupManager().getGroupIndex(group))}}}
Surface.prototype.applyGroupLevelToRow=function(row,group){if(row.hasAttribute('data-group-level')){return}var th=this.getRowHeadElement(row)
if(th===null){throw new Error('Cannot find TH element for the Inspector row')}var groupLevel=group.getLevel()
row.setAttribute('data-group-level',groupLevel)
th.children[0].style.marginLeft=groupLevel*10+'px'}
Surface.prototype.toggleGroup=function(row,forceExpand){var link=row.querySelector('a'),groupIndex=row.getAttribute('data-group-index'),table=this.getRootTable(),groupManager=this.getGroupManager(),collapse=true
if($.wn.foundation.element.hasClass(link,'expanded')&&!forceExpand){$.wn.foundation.element.removeClass(link,'expanded')}else{$.wn.foundation.element.addClass(link,'expanded')
collapse=false}var propertyRows=groupManager.findGroupRows(table,groupIndex,!collapse),duration=Math.round(50/propertyRows.length)
this.expandOrCollapseRows(propertyRows,collapse,duration,forceExpand)
groupManager.setGroupStatus(groupIndex,!collapse)}
Surface.prototype.expandGroupParents=function(group){var groups=group.getGroupAndAllParents(),table=this.getRootTable()
for(var i=groups.length-1;i>=0;i--){var row=groups[i].findGroupRow(table)
if(row){this.toggleGroup(row,true)}}}
Surface.prototype.expandOrCollapseRows=function(rows,collapse,duration,noAnimation){var row=rows.pop(),self=this
if(row){if(!noAnimation){setTimeout(function toggleRow(){$.wn.foundation.element.toggleClass(row,'collapsed',collapse)
$.wn.foundation.element.toggleClass(row,'expanded',!collapse)
self.expandOrCollapseRows(rows,collapse,duration,noAnimation)},duration)}else{$.wn.foundation.element.toggleClass(row,'collapsed',collapse)
$.wn.foundation.element.toggleClass(row,'expanded',!collapse)
self.expandOrCollapseRows(rows,collapse,duration,noAnimation)}}}
Surface.prototype.getGroupManager=function(){return this.getRootSurface().groupManager}
Surface.prototype.buildEditor=function(row,property,dataTable,group){if(property.itemType!=='property'){return}this.validateEditorType(property.type)
var cell=document.createElement('td'),type=property.type
row.appendChild(cell)
if(type===undefined){type='string'}var editor=new $.wn.inspector.propertyEditors[type](this,property,cell,group)
if(editor.isGroupedEditor()){$.wn.foundation.element.addClass(dataTable,'has-groups')
$.wn.foundation.element.addClass(row,'control-group')
this.applyGroupIndexAttribute(property,row,editor.group,true)
this.buildGroupExpandControl(row.querySelector('span.title-element'),property,true,editor.hasChildSurface(),editor.group)
if(cell.children.length==0){row.querySelector('th').setAttribute('colspan',2)
row.removeChild(cell)}}this.editors.push(editor)}
Surface.prototype.generateSequencedId=function(){this.idCounter++
return this.inspectorUniqueId+'-'+this.idCounter}
Surface.prototype.getPropertyValue=function(property){return this.values[property]}
Surface.prototype.setPropertyValue=function(property,value,supressChangeEvents,forceEditorUpdate){if(value!==undefined){this.values[property]=value}else{if(this.values[property]!==undefined){delete this.values[property]}}if(!supressChangeEvents){if(this.originalValues[property]===undefined||!this.comparePropertyValues(this.originalValues[property],value)){this.markPropertyChanged(property,true)}else{this.markPropertyChanged(property,false)}var propertyPath=this.getPropertyPath(property)
this.getRootSurface().notifyEditorsPropertyChanged(propertyPath,value)
if(this.options.onChange!==null){this.options.onChange(property,value)}}if(forceEditorUpdate){var editor=this.findPropertyEditor(property)
if(editor){editor.updateDisplayedValue(value)}}return value}
Surface.prototype.notifyEditorsPropertyChanged=function(propertyPath,value){for(var i=0,len=this.editors.length;i<len;i++){var editor=this.editors[i]
editor.onInspectorPropertyChanged(propertyPath,value)
editor.notifyChildSurfacesPropertyChanged(propertyPath,value)}}
Surface.prototype.makeCellActive=function(cell){var tbody=cell.parentNode.parentNode.parentNode,cells=tbody.querySelectorAll('tr td')
for(var i=0,len=cells.length;i<len;i++){$.wn.foundation.element.removeClass(cells[i],'active')}$.wn.foundation.element.addClass(cell,'active')}
Surface.prototype.markPropertyChanged=function(property,changed){var propertyPath=this.getPropertyPath(property),row=this.tableContainer.querySelector('tr[data-property-path="'+propertyPath+'"]')
if(changed){$.wn.foundation.element.addClass(row,'changed')}else{$.wn.foundation.element.removeClass(row,'changed')}}
Surface.prototype.findPropertyEditor=function(property){for(var i=0,len=this.editors.length;i<len;i++){if(this.editors[i].getPropertyName()==property){return this.editors[i]}}return null}
Surface.prototype.findExternalParameterEditor=function(property){for(var i=0,len=this.externalParameterEditors.length;i<len;i++){if(this.externalParameterEditors[i].getPropertyName()==property){return this.externalParameterEditors[i]}}return null}
Surface.prototype.findPropertyDefinition=function(property){for(var i=0,len=this.parsedProperties.properties.length;i<len;i++){var definition=this.parsedProperties.properties[i]
if(definition.property==property){return definition}}return null}
Surface.prototype.validateEditorType=function(type){if(type===undefined){type='string'}if($.wn.inspector.propertyEditors[type]===undefined){throw new Error('The Inspector editor class "'+type+'" is not defined in the $.wn.inspector.propertyEditors namespace.')}}
Surface.prototype.popupDisplayed=function(){if(this.popupCounter===0&&this.options.onPopupDisplayed!==null){this.options.onPopupDisplayed()}this.popupCounter++}
Surface.prototype.popupHidden=function(){this.popupCounter--
if(this.popupCounter<0){this.popupCounter=0}if(this.popupCounter===0&&this.options.onPopupHidden!==null){this.options.onPopupHidden()}}
Surface.prototype.getInspectableElement=function(){if(this.options.onGetInspectableElement!==null){return this.options.onGetInspectableElement()}}
Surface.prototype.getPropertyPath=function(propertyName){var result=[],current=this
result.push(propertyName)
while(current){if(current.propertyName){result.push(current.propertyName)}current=current.parentSurface}result.reverse()
return result.join('.')}
Surface.prototype.findDependentProperties=function(propertyName){var dependents=[]
for(var i in this.rawProperties){var property=this.rawProperties[i]
if(!property.depends){continue}if(property.depends.indexOf(propertyName)!==-1){dependents.push(property.property)}}return dependents}
Surface.prototype.mergeChildSurface=function(surface,mergeAfterRow){var rows=surface.tableContainer.querySelectorAll('table.inspector-fields > tbody > tr')
surface.tableContainer=this.getRootSurface().tableContainer
for(var i=rows.length-1;i>=0;i--){var row=rows[i]
mergeAfterRow.parentNode.insertBefore(row,mergeAfterRow.nextSibling)
this.applyGroupLevelToRow(row,surface.group)}}
Surface.prototype.getRowHeadElement=function(row){for(var i=row.children.length-1;i>=0;i--){var element=row.children[i]
if(element.tagName==='TH'){return element}}return null}
Surface.prototype.getInspectorUniqueId=function(){return this.inspectorUniqueId}
Surface.prototype.getRootSurface=function(){var current=this
while(current){if(!current.parentSurface){return current}current=current.parentSurface}}
Surface.prototype.removeElements=function(){if(!this.parentSurface){this.tableContainer.parentNode.removeChild(this.tableContainer);}}
Surface.prototype.disposeEditors=function(){for(var i=0,len=this.editors.length;i<len;i++){var editor=this.editors[i]
editor.dispose()}}
Surface.prototype.disposeExternalParameterEditors=function(){for(var i=0,len=this.externalParameterEditors.length;i<len;i++){var editor=this.externalParameterEditors[i]
editor.dispose()}}
Surface.prototype.disposeControls=function(){var tooltipControls=this.tableContainer.querySelectorAll('.with-tooltip')
for(var i=0,len=tooltipControls.length;i<len;i++){$(tooltipControls[i]).tooltip('destroy')}}
Surface.prototype.escapeJavascriptString=function(str){var div=document.createElement('div')
div.appendChild(document.createTextNode(str))
return div.innerHTML}
Surface.prototype.comparePropertyValues=function(oldValue,newValue){if(oldValue===undefined&&newValue!==undefined){return false}if(oldValue!==undefined&&newValue===undefined){return false}if(typeof oldValue=='object'&&typeof newValue=='object'){return JSON.stringify(oldValue)==JSON.stringify(newValue)}return oldValue==newValue}
Surface.prototype.getRootTable=function(){return this.getRootSurface().container.querySelector('table.inspector-fields')}
Surface.prototype.getValues=function(){var result={}
for(var i=0,len=this.parsedProperties.properties.length;i<len;i++){var property=this.parsedProperties.properties[i]
if(property.itemType!=='property'){continue}var value=null,externalParameterEditor=this.findExternalParameterEditor(property.property)
if(!externalParameterEditor||!externalParameterEditor.isEditorVisible()){value=this.getPropertyValue(property.property)
var editor=this.findPropertyEditor(property.property)
if(value===undefined){if(editor){value=editor.getUndefinedValue()}else{value=property.default}}if(value===$.wn.inspector.removedProperty){continue}if(property.ignoreIfEmpty!==undefined&&(property.ignoreIfEmpty===true||property.ignoreIfEmpty==="true")&&editor){if(editor.isEmptyValue(value)){continue}}if(property.ignoreIfDefault!==undefined&&(property.ignoreIfDefault===true||property.ignoreIfDefault==="true")&&editor){if(property.default===undefined){throw new Error('The ignoreIfDefault feature cannot be used without the default property value.')}if(this.comparePropertyValues(value,property.default)){continue}}}else{value=externalParameterEditor.getValue()
value='{{ '+value+' }}'}result[property.property]=value}return result}
Surface.prototype.getValidValues=function(){var allValues=this.getValues(),result={}
for(var property in allValues){var editor=this.findPropertyEditor(property)
if(!editor){throw new Error('Cannot find editor for property '+property)}var externalEditor=this.findExternalParameterEditor(property)
if(externalEditor&&externalEditor.isEditorVisible()&&!externalEditor.validate(true)){result[property]=$.wn.inspector.invalidProperty
continue}if(!editor.validate(true)){result[property]=$.wn.inspector.invalidProperty
continue}result[property]=allValues[property]}return result}
Surface.prototype.validate=function(silentMode){this.getGroupManager().unmarkInvalidGroups(this.getRootTable())
for(var i=0,len=this.editors.length;i<len;i++){var editor=this.editors[i],externalEditor=this.findExternalParameterEditor(editor.propertyDefinition.property)
if(externalEditor&&externalEditor.isEditorVisible()){if(!externalEditor.validate(silentMode)){if(!silentMode){editor.markInvalid()}return false}else{continue}}if(!editor.validate(silentMode)){if(!silentMode){editor.markInvalid()}return false}}return true}
Surface.prototype.hasChanges=function(originalValues){var values=originalValues!==undefined?originalValues:this.originalValues
return!this.comparePropertyValues(values,this.getValues())}
Surface.prototype.onGroupClick=function(ev){var row=ev.currentTarget
this.toggleGroup(row)
$.wn.foundation.event.stop(ev)
return false}
Surface.DEFAULTS={enableExternalParameterEditor:false,onChange:null,onPopupDisplayed:null,onPopupHidden:null,onGetInspectableElement:null}
$.wn.inspector.surface=Surface
$.wn.inspector.removedProperty={removed:true}
$.wn.inspector.invalidProperty={invalid:true}}(window.jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype
var InspectorManager=function(){Base.call(this)
this.init()}
InspectorManager.prototype=Object.create(BaseProto)
InspectorManager.prototype.constructor=Base
InspectorManager.prototype.init=function(){$(document).on('click','[data-inspectable]',this.proxy(this.onInspectableClicked))}
InspectorManager.prototype.getContainerElement=function($element){var $containerHolder=$element.closest('[data-inspector-container]')
if($containerHolder.length===0){return null}var $container=$containerHolder.find($containerHolder.data('inspector-container'))
if($container.length===0){throw new Error('Inspector container '+$containerHolder.data['inspector-container']+' element is not found.')}return $container}
InspectorManager.prototype.loadElementOptions=function($element){var options={}
if($element.data('inspector-css-class')){options.inspectorCssClass=$element.data('inspector-css-class')}return options}
InspectorManager.prototype.createInspectorPopup=function($element,containerSupported){var options=$.extend(this.loadElementOptions($element),{containerSupported:containerSupported})
new $.wn.inspector.wrappers.popup($element,null,options)}
InspectorManager.prototype.createInspectorContainer=function($element,$container){var options=$.extend(this.loadElementOptions($element),{containerSupported:true,container:$container})
new $.wn.inspector.wrappers.container($element,null,options)}
InspectorManager.prototype.switchToPopup=function(wrapper){var options=$.extend(this.loadElementOptions(wrapper.$element),{containerSupported:true})
new $.wn.inspector.wrappers.popup(wrapper.$element,wrapper,options)
wrapper.cleanupAfterSwitch()
this.setContainerPreference(false)}
InspectorManager.prototype.switchToContainer=function(wrapper){var $container=this.getContainerElement(wrapper.$element),options=$.extend(this.loadElementOptions(wrapper.$element),{containerSupported:true,container:$container})
if(!$container){throw new Error('Cannot switch to container: a container element is not found')}new $.wn.inspector.wrappers.container(wrapper.$element,wrapper,options)
wrapper.cleanupAfterSwitch()
this.setContainerPreference(true)}
InspectorManager.prototype.createInspector=function(element){var $element=$(element)
if($element.data('oc.inspectorVisible')){return false}var $container=this.getContainerElement($element)
if(!$container){this.createInspectorPopup($element,false)}else{if(!this.applyValuesFromContainer($container)||!this.containerHidingAllowed($container)){return}$.wn.foundation.controlUtils.disposeControls($container.get(0))
if(!this.getContainerPreference()){this.createInspectorPopup($element,true)}else{this.createInspectorContainer($element,$container)}}}
InspectorManager.prototype.getContainerPreference=function(){if(!Modernizr.localstorage){return false}return localStorage.getItem('oc.inspectorUseContainer')==="true"}
InspectorManager.prototype.setContainerPreference=function(value){if(!Modernizr.localstorage){return}return localStorage.setItem('oc.inspectorUseContainer',value?"true":"false")}
InspectorManager.prototype.applyValuesFromContainer=function($container){var applyEvent=$.Event('apply.oc.inspector')
$container.trigger(applyEvent)
return!applyEvent.isDefaultPrevented();}
InspectorManager.prototype.containerHidingAllowed=function($container){var allowedEvent=$.Event('beforeContainerHide.oc.inspector')
$container.trigger(allowedEvent)
return!allowedEvent.isDefaultPrevented();}
InspectorManager.prototype.onInspectableClicked=function(ev){var $element=$(ev.currentTarget)
if(this.createInspector($element)===false){return false}ev.stopPropagation()
return false}
$.wn.inspector.manager=new InspectorManager()
$.fn.inspector=function(){return this.each(function(){$.wn.inspector.manager.createInspector(this)})}}(window.jQuery);+function($){"use strict";if($.wn.inspector===undefined)$.wn.inspector={}
if($.wn.inspector.wrappers===undefined)$.wn.inspector.wrappers={}
var Base=$.wn.foundation.base,BaseProto=Base.prototype
var BaseWrapper=function($element,sourceWrapper,options){this.$element=$element
this.options=$.extend({},BaseWrapper.DEFAULTS,typeof options=='object'&&options)
this.switched=false
this.configuration=null
Base.call(this)
if(!sourceWrapper){if(!this.triggerShowingAndInit()){return}this.surface=null
this.title=null
this.description=null}else{this.surface=sourceWrapper.surface
this.title=sourceWrapper.title
this.description=sourceWrapper.description
sourceWrapper=null
this.init()}}
BaseWrapper.prototype=Object.create(BaseProto)
BaseWrapper.prototype.constructor=Base
BaseWrapper.prototype.dispose=function(){if(!this.switched){this.$element.removeClass('inspector-open')
this.setInspectorVisibleFlag(false)
this.$element.trigger('hidden.oc.inspector')}if(this.surface!==null&&this.surface.options.onGetInspectableElement===this.proxy(this.onGetInspectableElement)){this.surface.options.onGetInspectableElement=null}this.surface=null
this.$element=null
this.title=null
this.description=null
this.configuration=null
BaseProto.dispose.call(this)}
BaseWrapper.prototype.init=function(){if(!this.surface){this.loadConfiguration()}else{this.adoptSurface()}this.$element.addClass('inspector-open')}
BaseWrapper.prototype.getElementValuesInput=function(){return this.$element.find('> input[data-inspector-values]')}
BaseWrapper.prototype.normalizePropertyCode=function(code,configuration){var lowerCaseCode=code.toLowerCase()
for(var index in configuration){var propertyInfo=configuration[index]
if(propertyInfo.property.toLowerCase()==lowerCaseCode){return propertyInfo.property}}return code}
BaseWrapper.prototype.isExternalParametersEditorEnabled=function(){return this.$element.closest('[data-inspector-external-parameters]').length>0}
BaseWrapper.prototype.initSurface=function(containerElement,properties,values){var options=this.$element.data()||{}
options.enableExternalParameterEditor=this.isExternalParametersEditorEnabled()
options.onGetInspectableElement=this.proxy(this.onGetInspectableElement)
this.surface=new $.wn.inspector.surface(containerElement,properties,values,$.wn.inspector.helpers.generateElementUniqueId(this.$element.get(0)),options)}
BaseWrapper.prototype.isLiveUpdateEnabled=function(){return false}
BaseWrapper.prototype.createSurfaceAndUi=function(properties,values){}
BaseWrapper.prototype.setInspectorVisibleFlag=function(value){this.$element.data('oc.inspectorVisible',value)}
BaseWrapper.prototype.adoptSurface=function(){this.surface.options.onGetInspectableElement=this.proxy(this.onGetInspectableElement)}
BaseWrapper.prototype.cleanupAfterSwitch=function(){this.switched=true
this.dispose()}
BaseWrapper.prototype.loadValues=function(configuration){var $valuesField=this.getElementValuesInput()
if($valuesField.length>0){var valuesStr=$.trim($valuesField.val())
try{return valuesStr.length===0?{}:JSON.parse(valuesStr)}catch(err){throw new Error('Error parsing Inspector field values. '+err)}}var values={},attributes=this.$element.get(0).attributes
for(var i=0,len=attributes.length;i<len;i++){var attribute=attributes[i],matches=[]
if(matches=attribute.name.match(/^data-property-(.*)$/)){var normalizedPropertyName=this.normalizePropertyCode(matches[1],configuration)
values[normalizedPropertyName]=attribute.value}}return values}
BaseWrapper.prototype.applyValues=function(liveUpdateMode){var $valuesField=this.getElementValuesInput(),values=liveUpdateMode?this.surface.getValidValues():this.surface.getValues()
if(liveUpdateMode){var existingValues=this.loadValues(this.configuration)
for(var property in values){if(values[property]!==$.wn.inspector.invalidProperty){existingValues[property]=values[property]}}var filteredValues={}
for(var property in existingValues){if(values.hasOwnProperty(property)){filteredValues[property]=existingValues[property]}}values=filteredValues}if($valuesField.length>0){$valuesField.val(JSON.stringify(values))}else{for(var property in values){var value=values[property]
if($.isArray(value)||$.isPlainObject(value)){throw new Error('Inspector data-property-xxx attributes do not support complex values. Property: '+property)}this.$element.attr('data-property-'+property,value)}}if(liveUpdateMode){this.$element.trigger('livechange')}else{var hasChanges=false
if(this.isLiveUpdateEnabled()){var currentValues=this.loadValues(this.configuration)
hasChanges=this.surface.hasChanges(currentValues)}else{hasChanges=this.surface.hasChanges()}if(hasChanges){this.$element.trigger('change')}}}
BaseWrapper.prototype.loadConfiguration=function(){var configString=this.$element.data('inspector-config'),result={properties:{},title:null,description:null}
result.title=this.$element.data('inspector-title')
result.description=this.$element.data('inspector-description')
if(configString!==undefined){result.properties=this.parseConfiguration(configString)
this.configurationLoaded(result)
return}var $configurationField=this.$element.find('> input[data-inspector-config]')
if($configurationField.length>0){result.properties=this.parseConfiguration($configurationField.val())
this.configurationLoaded(result)
return}var $form=this.$element.closest('form'),data=this.$element.data(),self=this
$.wn.stripeLoadIndicator.show()
$form.request('onGetInspectorConfiguration',{data:data}).done(function inspectorConfigurationRequestDoneClosure(data){self.onConfigurartionRequestDone(data,result)}).always(function(){$.wn.stripeLoadIndicator.hide()})}
BaseWrapper.prototype.parseConfiguration=function(configuration){if(!$.isArray(configuration)&&!$.isPlainObject(configuration)){if($.trim(configuration)===0){return{}}try{return JSON.parse(configuration)}catch(err){throw new Error('Error parsing Inspector configuration. '+err)}}else{return configuration}}
BaseWrapper.prototype.configurationLoaded=function(configuration){var values=this.loadValues(configuration.properties)
this.title=configuration.title
this.description=configuration.description
this.configuration=configuration
this.createSurfaceAndUi(configuration.properties,values)}
BaseWrapper.prototype.onConfigurartionRequestDone=function(data,result){result.properties=this.parseConfiguration(data.configuration.properties)
if(data.configuration.title!==undefined){result.title=data.configuration.title}if(data.configuration.description!==undefined){result.description=data.configuration.description}this.configurationLoaded(result)}
BaseWrapper.prototype.triggerShowingAndInit=function(){var e=$.Event('showing.oc.inspector')
this.$element.trigger(e,[{callback:this.proxy(this.init)}])
if(e.isDefaultPrevented()){this.$element=null
return false}if(!e.isPropagationStopped()){this.init()}}
BaseWrapper.prototype.triggerHiding=function(){var hidingEvent=$.Event('hiding.oc.inspector'),values=this.surface.getValues()
this.$element.trigger(hidingEvent,[{values:values}])
return!hidingEvent.isDefaultPrevented();}
BaseWrapper.prototype.onGetInspectableElement=function(){return this.$element}
BaseWrapper.DEFAULTS={containerSupported:false}
$.wn.inspector.wrappers.base=BaseWrapper}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.wrappers.base,BaseProto=Base.prototype
var InspectorPopup=function($element,surface,options){this.$popoverContainer=null
this.popoverObj=null
this.cleaningUp=false
Base.call(this,$element,surface,options)}
InspectorPopup.prototype=Object.create(BaseProto)
InspectorPopup.prototype.constructor=Base
InspectorPopup.prototype.dispose=function(){this.unregisterHandlers()
this.$popoverContainer=null
this.popoverObj=null
BaseProto.dispose.call(this)}
InspectorPopup.prototype.createSurfaceAndUi=function(properties,values,title,description){this.showPopover()
this.initSurface(this.$popoverContainer.find('[data-surface-container]').get(0),properties,values)
this.repositionPopover()
this.registerPopupHandlers()}
InspectorPopup.prototype.adoptSurface=function(){this.showPopover()
this.surface.moveToContainer(this.$popoverContainer.find('[data-surface-container]').get(0))
this.repositionPopover()
this.registerPopupHandlers()
BaseProto.adoptSurface.call(this)}
InspectorPopup.prototype.cleanupAfterSwitch=function(){this.cleaningUp=true
this.switched=true
this.forceClose()}
InspectorPopup.prototype.getPopoverContents=function(){return'<div class="popover-head">                          \
                    <h3 data-inspector-title></h3>                  \
                    <p data-inspector-description></p>              \
                    <button type="button" class="close"             \
                        data-dismiss="popover"                      \
                        aria-hidden="true">&times;</button>         \
                </div>                                              \
                <form autocomplete="off" onsubmit="return false">   \
                    <div data-surface-container></div>              \
                <form>'}
InspectorPopup.prototype.showPopover=function(){var offset=this.$element.data('inspector-offset'),offsetX=this.$element.data('inspector-offset-x'),offsetY=this.$element.data('inspector-offset-y'),placement=this.$element.data('inspector-placement'),fallbackPlacement=this.$element.data('inspector-fallback-placement')
if(offset===undefined){offset=15}if(placement===undefined){placement='bottom'}if(fallbackPlacement===undefined){fallbackPlacement='bottom'}this.$element.ocPopover({content:this.getPopoverContents(),highlightModalTarget:true,modal:true,placement:placement,fallbackPlacement:fallbackPlacement,containerClass:'control-inspector',container:this.$element.data('inspector-container'),offset:offset,offsetX:offsetX,offsetY:offsetY,width:400})
this.setInspectorVisibleFlag(true)
this.popoverObj=this.$element.data('oc.popover')
this.$popoverContainer=this.popoverObj.$container
this.$popoverContainer.addClass('inspector-temporary-placement')
if(this.options.inspectorCssClass!==undefined){this.$popoverContainer.addClass(this.options.inspectorCssClass)}if(this.options.containerSupported){var moveToContainerButton=$('<span class="inspector-move-to-container wn-icon-download">')
this.$popoverContainer.find('.popover-head').append(moveToContainerButton)}this.$popoverContainer.find('[data-inspector-title]').text(this.title)
this.$popoverContainer.find('[data-inspector-description]').text(this.description)}
InspectorPopup.prototype.repositionPopover=function(){this.popoverObj.reposition()
this.$popoverContainer.removeClass('inspector-temporary-placement')
this.$popoverContainer.find('div[data-surface-container] > div').trigger('focus-control')}
InspectorPopup.prototype.forceClose=function(){this.$popoverContainer.trigger('close.oc.popover')}
InspectorPopup.prototype.registerPopupHandlers=function(){this.surface.options.onPopupDisplayed=this.proxy(this.onPopupEditorDisplayed)
this.surface.options.onPopupHidden=this.proxy(this.onPopupEditorHidden)
this.popoverObj.options.onCheckDocumentClickTarget=this.proxy(this.onCheckDocumentClickTarget)
this.$element.on('hiding.oc.popover',this.proxy(this.onBeforeHide))
this.$element.on('hide.oc.popover',this.proxy(this.onHide))
this.$popoverContainer.on('keydown',this.proxy(this.onPopoverKeyDown))
if(this.options.containerSupported){this.$popoverContainer.on('click','span.inspector-move-to-container',this.proxy(this.onMoveToContainer))}}
InspectorPopup.prototype.unregisterHandlers=function(){this.popoverObj.options.onCheckDocumentClickTarget=null
this.$element.off('hiding.oc.popover',this.proxy(this.onBeforeHide))
this.$element.off('hide.oc.popover',this.proxy(this.onHide))
this.$popoverContainer.off('keydown',this.proxy(this.onPopoverKeyDown))
if(this.options.containerSupported){this.$popoverContainer.off('click','span.inspector-move-to-container',this.proxy(this.onMoveToContainer))}this.surface.options.onPopupDisplayed=null
this.surface.options.onPopupHidden=null}
InspectorPopup.prototype.onBeforeHide=function(ev){if(this.cleaningUp){return}if(!this.surface.validate()){ev.preventDefault()
return false}if(!this.triggerHiding()){ev.preventDefault()
return false}this.applyValues()}
InspectorPopup.prototype.onHide=function(ev){this.dispose()}
InspectorPopup.prototype.onPopoverKeyDown=function(ev){if(ev.key==='Enter'){$(ev.currentTarget).trigger('close.oc.popover')}}
InspectorPopup.prototype.onPopupEditorDisplayed=function(){this.popoverObj.options.closeOnPageClick=false
this.popoverObj.options.closeOnEsc=false}
InspectorPopup.prototype.onPopupEditorHidden=function(){this.popoverObj.options.closeOnPageClick=true
this.popoverObj.options.closeOnEsc=true}
InspectorPopup.prototype.onCheckDocumentClickTarget=function(element){if($.contains(this.$element,element)||this.$element.get(0)===element){return true}}
InspectorPopup.prototype.onMoveToContainer=function(){$.wn.inspector.manager.switchToContainer(this)}
$.wn.inspector.wrappers.popup=InspectorPopup}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.wrappers.base,BaseProto=Base.prototype
var InspectorContainer=function($element,surface,options){if(!options.container){throw new Error('Cannot create Inspector container wrapper without a container element.')}this.surfaceContainer=null
Base.call(this,$element,surface,options)}
InspectorContainer.prototype=Object.create(BaseProto)
InspectorContainer.prototype.constructor=Base
InspectorContainer.prototype.init=function(){this.registerHandlers()
BaseProto.init.call(this)}
InspectorContainer.prototype.dispose=function(){this.unregisterHandlers()
this.removeControls()
this.surfaceContainer=null
BaseProto.dispose.call(this)}
InspectorContainer.prototype.createSurfaceAndUi=function(properties,values){this.buildUi()
this.initSurface(this.surfaceContainer,properties,values)
if(this.isLiveUpdateEnabled()){this.surface.options.onChange=this.proxy(this.onLiveUpdate)}}
InspectorContainer.prototype.adoptSurface=function(){this.buildUi()
this.surface.moveToContainer(this.surfaceContainer)
if(this.isLiveUpdateEnabled()){this.surface.options.onChange=this.proxy(this.onLiveUpdate)}BaseProto.adoptSurface.call(this)}
InspectorContainer.prototype.buildUi=function(){var scrollable=this.isScrollable(),head=this.buildHead(),layoutElements=this.buildLayout()
layoutElements.headContainer.appendChild(head)
if(scrollable){var scrollpad=this.buildScrollpad()
this.surfaceContainer=scrollpad.container
layoutElements.bodyContainer.appendChild(scrollpad.scrollpad)
$(scrollpad.scrollpad).scrollpad()}else{this.surfaceContainer=layoutElements.bodyContainer}this.setInspectorVisibleFlag(true)}
InspectorContainer.prototype.buildHead=function(){var container=document.createElement('div'),header=document.createElement('h3'),paragraph=document.createElement('p'),detachButton=document.createElement('span'),closeButton=document.createElement('span')
container.setAttribute('class','inspector-header')
detachButton.setAttribute('class','wn-icon-external-link-square detach')
closeButton.setAttribute('class','close')
header.textContent=this.title
paragraph.textContent=this.description
closeButton.innerHTML='&times;';container.appendChild(header)
container.appendChild(paragraph)
container.appendChild(detachButton)
container.appendChild(closeButton)
return container}
InspectorContainer.prototype.buildScrollpad=function(){var scrollpad=document.createElement('div'),scrollWrapper=document.createElement('div'),scrollableContainer=document.createElement('div')
scrollpad.setAttribute('class','control-scrollpad')
scrollpad.setAttribute('data-control','scrollpad')
scrollWrapper.setAttribute('class','scroll-wrapper inspector-wrapper')
scrollpad.appendChild(scrollWrapper)
scrollWrapper.appendChild(scrollableContainer)
return{scrollpad:scrollpad,container:scrollableContainer}}
InspectorContainer.prototype.buildLayout=function(){var layout=document.createElement('div'),headRow=document.createElement('div'),bodyRow=document.createElement('div')
layout.setAttribute('class','flex-layout-column fill-container')
headRow.setAttribute('class','flex-layout-item fix')
bodyRow.setAttribute('class','flex-layout-item stretch relative')
layout.appendChild(headRow)
layout.appendChild(bodyRow)
this.options.container.get(0).appendChild(layout)
$.wn.foundation.controlUtils.markDisposable(layout)
this.registerLayoutHandlers(layout)
return{headContainer:headRow,bodyContainer:bodyRow}}
InspectorContainer.prototype.validateAndApply=function(){if(!this.surface.validate()){return false}this.applyValues()
return true}
InspectorContainer.prototype.isScrollable=function(){return this.options.container.data('inspector-scrollable')!==undefined}
InspectorContainer.prototype.isLiveUpdateEnabled=function(){return this.options.container.data('inspector-live-update')!==undefined}
InspectorContainer.prototype.getLayout=function(){return this.options.container.get(0).querySelector('div.flex-layout-column')}
InspectorContainer.prototype.registerLayoutHandlers=function(layout){var $layout=$(layout)
$layout.one('dispose-control',this.proxy(this.dispose))
$layout.on('click','span.close',this.proxy(this.onClose))
$layout.on('click','span.detach',this.proxy(this.onDetach))}
InspectorContainer.prototype.registerHandlers=function(){this.options.container.on('apply.oc.inspector',this.proxy(this.onApplyValues))
this.options.container.on('beforeContainerHide.oc.inspector',this.proxy(this.onBeforeHide))}
InspectorContainer.prototype.unregisterHandlers=function(){var $layout=$(this.getLayout())
this.options.container.off('apply.oc.inspector',this.proxy(this.onApplyValues))
this.options.container.off('beforeContainerHide.oc.inspector',this.proxy(this.onBeforeHide))
$layout.off('dispose-control',this.proxy(this.dispose))
$layout.off('click','span.close',this.proxy(this.onClose))
$layout.off('click','span.detach',this.proxy(this.onDetach))
if(this.surface!==null&&this.surface.options.onChange===this.proxy(this.onLiveUpdate)){this.surface.options.onChange=null}}
InspectorContainer.prototype.removeControls=function(){if(this.isScrollable()){this.options.container.find('.control-scrollpad').scrollpad('dispose')}var layout=this.getLayout()
layout.parentNode.removeChild(layout)}
InspectorContainer.prototype.onApplyValues=function(ev){if(!this.validateAndApply()){ev.preventDefault()
return false}}
InspectorContainer.prototype.onBeforeHide=function(ev){if(!this.triggerHiding()){ev.preventDefault()
return false}}
InspectorContainer.prototype.onClose=function(ev){if(!this.validateAndApply()){ev.preventDefault()
return false}if(!this.triggerHiding()){ev.preventDefault()
return false}this.surface.dispose()
this.dispose()}
InspectorContainer.prototype.onLiveUpdate=function(){this.applyValues(true)}
InspectorContainer.prototype.onDetach=function(){$.wn.inspector.manager.switchToPopup(this)}
$.wn.inspector.wrappers.container=InspectorContainer}(window.jQuery);+function($){"use strict";var GroupManager=function(controlId){this.controlId=controlId
this.rootGroup=null
this.cachedGroupStatuses=null}
GroupManager.prototype.createGroup=function(groupId,parentGroup){var group=new Group(groupId)
if(parentGroup){parentGroup.groups.push(group)
group.parentGroup=parentGroup}else{this.rootGroup=group}return group}
GroupManager.prototype.getGroupIndex=function(group){return group.getGroupIndex()}
GroupManager.prototype.isParentGroupExpanded=function(group){if(!group.parentGroup){return true}return this.isGroupExpanded(group.parentGroup)}
GroupManager.prototype.isGroupExpanded=function(group){if(!group.parentGroup){return true}var groupIndex=this.getGroupIndex(group),statuses=this.readGroupStatuses()
if(statuses[groupIndex]!==undefined){return statuses[groupIndex]}return false}
GroupManager.prototype.setGroupStatus=function(groupIndex,expanded){var statuses=this.readGroupStatuses()
statuses[groupIndex]=expanded
this.writeGroupStatuses(statuses)}
GroupManager.prototype.readGroupStatuses=function(){if(this.cachedGroupStatuses!==null){return this.cachedGroupStatuses}var statuses=getInspectorGroupStatuses()
if(statuses[this.controlId]!==undefined){this.cachedGroupStatuses=statuses[this.controlId]}else{this.cachedGroupStatuses={}}return this.cachedGroupStatuses}
GroupManager.prototype.writeGroupStatuses=function(updatedStatuses){var statuses=getInspectorGroupStatuses()
statuses[this.controlId]=updatedStatuses
setInspectorGroupStatuses(statuses)
this.cachedGroupStatuses=updatedStatuses}
GroupManager.prototype.findGroupByIndex=function(index){return this.rootGroup.findGroupByIndex(index)}
GroupManager.prototype.findGroupRows=function(table,index,ignoreCollapsedSubgroups){var group=this.findGroupByIndex(index)
if(!group){throw new Error('Cannot find the requested row group.')}return group.findGroupRows(table,ignoreCollapsedSubgroups,this)}
GroupManager.prototype.markGroupRowInvalid=function(group,table){var currentGroup=group
while(currentGroup){var row=currentGroup.findGroupRow(table)
if(row){$.wn.foundation.element.addClass(row,'invalid')}currentGroup=currentGroup.parentGroup}}
GroupManager.prototype.unmarkInvalidGroups=function(table){var rows=table.querySelectorAll('tr.invalid')
for(var i=rows.length-1;i>=0;i--){$.wn.foundation.element.removeClass(rows[i],'invalid')}}
GroupManager.prototype.isRowVisible=function(table,rowGroupIndex){var group=this.findGroupByIndex(index)
if(!group){throw new Error('Cannot find the requested row group.')}var current=group
while(current){if(!this.isGroupExpanded(current)){return false}current=current.parentGroup}return true}
function getInspectorGroupStatuses(){var statuses=document.body.getAttribute('data-inspector-group-statuses')
if(statuses!==null){return JSON.parse(statuses)}return{}}function setInspectorGroupStatuses(statuses){document.body.setAttribute('data-inspector-group-statuses',JSON.stringify(statuses))}var Group=function(groupId){this.groupId=groupId
this.parentGroup=null
this.groupIndex=null
this.groups=[]}
Group.prototype.getGroupIndex=function(){if(this.groupIndex!==null){return this.groupIndex}var result='',current=this
while(current){if(result.length>0){result=current.groupId+'-'+result}else{result=String(current.groupId)}current=current.parentGroup}this.groupIndex=result
return result}
Group.prototype.findGroupByIndex=function(index){if(this.getGroupIndex()==index){return this}for(var i=this.groups.length-1;i>=0;i--){var groupResult=this.groups[i].findGroupByIndex(index)
if(groupResult!==null){return groupResult}}return null}
Group.prototype.getLevel=function(){var current=this,level=-1
while(current){level++
current=current.parentGroup}return level}
Group.prototype.getGroupAndAllParents=function(){var current=this,result=[]
while(current){result.push(current)
current=current.parentGroup}return result}
Group.prototype.findGroupRows=function(table,ignoreCollapsedSubgroups,groupManager){var groupIndex=this.getGroupIndex(),rows=table.querySelectorAll('tr[data-parent-group-index="'+groupIndex+'"]'),result=Array.prototype.slice.call(rows)
for(var i=0,len=this.groups.length;i<len;i++){var subgroup=this.groups[i]
if(ignoreCollapsedSubgroups&&!groupManager.isGroupExpanded(subgroup)){continue}var subgroupRows=subgroup.findGroupRows(table,ignoreCollapsedSubgroups,groupManager)
for(var j=0,subgroupLen=subgroupRows.length;j<subgroupLen;j++){result.push(subgroupRows[j])}}return result}
Group.prototype.findGroupRow=function(table){return table.querySelector('tr[data-group-index="'+this.groupIndex+'"]')}
$.wn.inspector.groupManager=GroupManager}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.inspector===undefined)$.wn.inspector={}
$.wn.inspector.engine={}
function findGroup(group,properties){for(var i=0,len=properties.length;i<len;i++){var property=properties[i]
if(property.itemType!==undefined&&property.itemType=='group'&&property.title==group){return property}}return null}$.wn.inspector.engine.processPropertyGroups=function(properties){var fields=[],result={hasGroups:false,properties:[]},groupIndex=0
for(var i=0,len=properties.length;i<len;i++){var property=properties[i]
if(property['sortOrder']===undefined){property['sortOrder']=(i+1)*20}}properties.sort(function(a,b){return a['sortOrder']-b['sortOrder']})
for(var i=0,len=properties.length;i<len;i++){var property=properties[i]
property.itemType='property'
if(property.group===undefined){fields.push(property)}else{var group=findGroup(property.group,fields)
if(!group){group={itemType:'group',title:property.group,properties:[],groupIndex:groupIndex}
groupIndex++
fields.push(group)}property.groupIndex=group.groupIndex
group.properties.push(property)}}for(var i=0,len=fields.length;i<len;i++){var property=fields[i]
result.properties.push(property)
if(property.itemType=='group'){result.hasGroups=true
for(var j=0,propertiesLen=property.properties.length;j<propertiesLen;j++){result.properties.push(property.properties[j])}delete property.properties}}return result}}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.inspector===undefined)$.wn.inspector={}
if($.wn.inspector.propertyEditors===undefined)$.wn.inspector.propertyEditors={}
var Base=$.wn.foundation.base,BaseProto=Base.prototype
var BaseEditor=function(inspector,propertyDefinition,containerCell,group){this.inspector=inspector
this.propertyDefinition=propertyDefinition
this.containerCell=containerCell
this.containerRow=containerCell.parentNode
this.parentGroup=group
this.group=null
this.childInspector=null
this.validationSet=null
this.disposed=false
Base.call(this)
this.init()}
BaseEditor.prototype=Object.create(BaseProto)
BaseEditor.prototype.constructor=Base
BaseEditor.prototype.dispose=function(){this.disposed=true
this.disposeValidation()
if(this.childInspector){this.childInspector.dispose()}this.inspector=null
this.propertyDefinition=null
this.containerCell=null
this.containerRow=null
this.childInspector=null
this.parentGroup=null
this.group=null
this.validationSet=null
BaseProto.dispose.call(this)}
BaseEditor.prototype.init=function(){this.build()
this.registerHandlers()
this.initValidation()}
BaseEditor.prototype.build=function(){return null}
BaseEditor.prototype.isDisposed=function(){return this.disposed}
BaseEditor.prototype.registerHandlers=function(){}
BaseEditor.prototype.onInspectorPropertyChanged=function(property,value){}
BaseEditor.prototype.notifyChildSurfacesPropertyChanged=function(property,value){if(!this.hasChildSurface()){return}this.childInspector.notifyEditorsPropertyChanged(property,value)}
BaseEditor.prototype.focus=function(){}
BaseEditor.prototype.hasChildSurface=function(){return this.childInspector!==null}
BaseEditor.prototype.getRootSurface=function(){return this.inspector.getRootSurface()}
BaseEditor.prototype.getPropertyPath=function(){return this.inspector.getPropertyPath(this.propertyDefinition.property)}
BaseEditor.prototype.updateDisplayedValue=function(value){}
BaseEditor.prototype.getPropertyName=function(){return this.propertyDefinition.property}
BaseEditor.prototype.getUndefinedValue=function(){return this.propertyDefinition.default===undefined?undefined:this.propertyDefinition.default}
BaseEditor.prototype.throwError=function(errorMessage){throw new Error(errorMessage+' Property: '+this.propertyDefinition.property)}
BaseEditor.prototype.getInspectableElement=function(){return this.getRootSurface().getInspectableElement()}
BaseEditor.prototype.isEmptyValue=function(value){return value===undefined||value===null||(typeof value=='object'&&$.isEmptyObject(value))||(typeof value=='string'&&$.trim(value).length===0)||(Object.prototype.toString.call(value)==='[object Array]'&&value.length===0)}
BaseEditor.prototype.initValidation=function(){this.validationSet=new $.wn.inspector.validationSet(this.propertyDefinition,this.propertyDefinition.property)}
BaseEditor.prototype.disposeValidation=function(){this.validationSet.dispose()}
BaseEditor.prototype.getValueToValidate=function(){return this.inspector.getPropertyValue(this.propertyDefinition.property)}
BaseEditor.prototype.validate=function(silentMode){var value=this.getValueToValidate()
if(value===undefined){value=this.getUndefinedValue()}var validationResult=this.validationSet.validate(value)
if(validationResult!==null){if(!silentMode){$.wn.flashMsg({text:validationResult,'class':'error','interval':5})}return false}return true}
BaseEditor.prototype.markInvalid=function(){$.wn.foundation.element.addClass(this.containerRow,'invalid')
this.inspector.getGroupManager().markGroupRowInvalid(this.parentGroup,this.inspector.getRootTable())
this.inspector.getRootSurface().expandGroupParents(this.parentGroup)
this.focus()}
BaseEditor.prototype.supportsExternalParameterEditor=function(){return true}
BaseEditor.prototype.onExternalPropertyEditorHidden=function(){}
BaseEditor.prototype.isGroupedEditor=function(){return false}
BaseEditor.prototype.initControlGroup=function(){this.group=this.inspector.getGroupManager().createGroup(this.propertyDefinition.property,this.parentGroup)}
BaseEditor.prototype.createGroupedRow=function(property){var row=this.inspector.buildRow(property,this.group),groupedClass=this.inspector.getGroupManager().isGroupExpanded(this.group)?'expanded':'collapsed'
this.inspector.applyGroupLevelToRow(row,this.group)
$.wn.foundation.element.addClass(row,'property')
$.wn.foundation.element.addClass(row,groupedClass)
return row}
$.wn.inspector.propertyEditors.base=BaseEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.base,BaseProto=Base.prototype
var StringEditor=function(inspector,propertyDefinition,containerCell,group){Base.call(this,inspector,propertyDefinition,containerCell,group)}
StringEditor.prototype=Object.create(BaseProto)
StringEditor.prototype.constructor=Base
StringEditor.prototype.dispose=function(){this.unregisterHandlers()
BaseProto.dispose.call(this)}
StringEditor.prototype.build=function(){var editor=document.createElement('input'),placeholder=this.propertyDefinition.placeholder!==undefined?this.propertyDefinition.placeholder:'',value=this.inspector.getPropertyValue(this.propertyDefinition.property)
editor.setAttribute('type','text')
editor.setAttribute('class','string-editor')
editor.setAttribute('placeholder',placeholder)
if(value===undefined){value=this.propertyDefinition.default}if(value===undefined){value=''}editor.value=value
$.wn.foundation.element.addClass(this.containerCell,'text')
this.containerCell.appendChild(editor)}
StringEditor.prototype.updateDisplayedValue=function(value){this.getInput().value=value}
StringEditor.prototype.getInput=function(){return this.containerCell.querySelector('input')}
StringEditor.prototype.focus=function(){this.getInput().focus()
this.onInputFocus()}
StringEditor.prototype.registerHandlers=function(){var input=this.getInput()
input.addEventListener('focus',this.proxy(this.onInputFocus))
input.addEventListener('keyup',this.proxy(this.onInputKeyUp))}
StringEditor.prototype.unregisterHandlers=function(){var input=this.getInput()
input.removeEventListener('focus',this.proxy(this.onInputFocus))
input.removeEventListener('keyup',this.proxy(this.onInputKeyUp))}
StringEditor.prototype.onInputFocus=function(ev){this.inspector.makeCellActive(this.containerCell)}
StringEditor.prototype.onInputKeyUp=function(){var value=$.trim(this.getInput().value)
this.inspector.setPropertyValue(this.propertyDefinition.property,value)}
StringEditor.prototype.onExternalPropertyEditorHidden=function(){this.focus()}
$.wn.inspector.propertyEditors.string=StringEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.base,BaseProto=Base.prototype
var CheckboxEditor=function(inspector,propertyDefinition,containerCell,group){Base.call(this,inspector,propertyDefinition,containerCell,group)}
CheckboxEditor.prototype=Object.create(BaseProto)
CheckboxEditor.prototype.constructor=Base
CheckboxEditor.prototype.dispose=function(){this.unregisterHandlers()
BaseProto.dispose.call(this)}
CheckboxEditor.prototype.build=function(){var editor=document.createElement('input'),container=document.createElement('div'),value=this.inspector.getPropertyValue(this.propertyDefinition.property),label=document.createElement('label'),isChecked=false,id=this.inspector.generateSequencedId()
container.setAttribute('tabindex',0)
container.setAttribute('class','custom-checkbox nolabel')
editor.setAttribute('type','checkbox')
editor.setAttribute('value','1')
editor.setAttribute('placeholder','placeholder')
editor.setAttribute('id',id)
label.setAttribute('for',id)
label.textContent=this.propertyDefinition.title
container.appendChild(editor)
container.appendChild(label)
if(value===undefined){if(this.propertyDefinition.default!==undefined){isChecked=this.normalizeCheckedValue(this.propertyDefinition.default)}}else{isChecked=this.normalizeCheckedValue(value)}editor.checked=isChecked
this.containerCell.appendChild(container)}
CheckboxEditor.prototype.normalizeCheckedValue=function(value){if(value=='0'||value=='false'){return false}return value}
CheckboxEditor.prototype.getInput=function(){return this.containerCell.querySelector('input')}
CheckboxEditor.prototype.focus=function(){this.getInput().parentNode.focus()}
CheckboxEditor.prototype.updateDisplayedValue=function(value){this.getInput().checked=this.normalizeCheckedValue(value)}
CheckboxEditor.prototype.isEmptyValue=function(value){if(value===0||value==='0'||value==='false'){return true}return BaseProto.isEmptyValue.call(this,value)}
CheckboxEditor.prototype.registerHandlers=function(){var input=this.getInput()
input.addEventListener('change',this.proxy(this.onInputChange))}
CheckboxEditor.prototype.unregisterHandlers=function(){var input=this.getInput()
input.removeEventListener('change',this.proxy(this.onInputChange))}
CheckboxEditor.prototype.onInputChange=function(){var isChecked=this.getInput().checked
this.inspector.setPropertyValue(this.propertyDefinition.property,isChecked?1:0)}
$.wn.inspector.propertyEditors.checkbox=CheckboxEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.base,BaseProto=Base.prototype
var DropdownEditor=function(inspector,propertyDefinition,containerCell,group){this.indicatorContainer=null
Base.call(this,inspector,propertyDefinition,containerCell,group)}
DropdownEditor.prototype=Object.create(BaseProto)
DropdownEditor.prototype.constructor=Base
DropdownEditor.prototype.init=function(){this.dynamicOptions=this.propertyDefinition.options?false:true
this.initialization=false
BaseProto.init.call(this)}
DropdownEditor.prototype.dispose=function(){this.unregisterHandlers()
this.destroyCustomSelect()
this.indicatorContainer=null
BaseProto.dispose.call(this)}
DropdownEditor.prototype.build=function(){var select=document.createElement('select')
$.wn.foundation.element.addClass(this.containerCell,'dropdown')
$.wn.foundation.element.addClass(select,'custom-select')
if(!this.dynamicOptions){this.loadStaticOptions(select)}this.containerCell.appendChild(select)
this.initCustomSelect()
if(this.dynamicOptions){this.loadDynamicOptions(true)}}
DropdownEditor.prototype.formatSelectOption=function(state){if(!state.id)return state.text;var option=state.element,iconClass=option.getAttribute('data-icon'),imageSrc=option.getAttribute('data-image')
if(iconClass){return'<i class="select-icon '+iconClass+'"></i> '+state.text}if(imageSrc){return'<img class="select-image" src="'+imageSrc+'" alt="" /> '+state.text}return state.text}
DropdownEditor.prototype.createOption=function(select,title,value){var option=document.createElement('option')
if(title!==null){if(!$.isArray(title)){option.textContent=title}else{if(title[1].indexOf('.')!==-1){option.setAttribute('data-image',title[1])}else{option.setAttribute('data-icon',title[1])}option.textContent=title[0]}}if(value!==null){option.value=value}select.appendChild(option)}
DropdownEditor.prototype.createOptions=function(select,options){for(var value in options){this.createOption(select,options[value],value)}}
DropdownEditor.prototype.initCustomSelect=function(){var select=this.getSelect()
var options={dropdownCssClass:'ocInspectorDropdown'}
if(this.propertyDefinition.emptyOption!==undefined){options.placeholder=this.propertyDefinition.emptyOption}if(this.propertyDefinition.placeholder!==undefined){options.placeholder=this.propertyDefinition.placeholder}options.templateResult=this.formatSelectOption
options.templateSelection=this.formatSelectOption
options.escapeMarkup=function(m){return m}
$(select).select2(options)
if(!Modernizr.touchevents){this.indicatorContainer=$('.select2-container',this.containerCell)
this.indicatorContainer.addClass('loading-indicator-container size-small')}}
DropdownEditor.prototype.createPlaceholder=function(select){var placeholder=this.propertyDefinition.placeholder||this.propertyDefinition.emptyOption
if(placeholder!==undefined&&!Modernizr.touchevents){this.createOption(select,null,null)}if(placeholder!==undefined&&Modernizr.touchevents){this.createOption(select,placeholder,null)}}
DropdownEditor.prototype.getSelect=function(){return this.containerCell.querySelector('select')}
DropdownEditor.prototype.clearOptions=function(select){while(select.firstChild){select.removeChild(select.firstChild)}}
DropdownEditor.prototype.hasOptionValue=function(select,value){var options=select.children
for(var i=0,len=options.length;i<len;i++){if(options[i].value==value){return true}}return false}
DropdownEditor.prototype.normalizeValue=function(value){if(!this.propertyDefinition.booleanValues){return value}var str=String(value)
if(str.length===0){return''}if(str==='true'){return true}return false}
DropdownEditor.prototype.registerHandlers=function(){var select=this.getSelect()
$(select).on('change',this.proxy(this.onSelectionChange))}
DropdownEditor.prototype.onSelectionChange=function(){var select=this.getSelect()
this.inspector.setPropertyValue(this.propertyDefinition.property,this.normalizeValue(select.value),this.initialization)}
DropdownEditor.prototype.onInspectorPropertyChanged=function(property){if(!this.propertyDefinition.depends||this.propertyDefinition.depends.indexOf(property)===-1){return}var dependencyValues=this.getDependencyValues()
if(this.prevDependencyValues===undefined||this.prevDependencyValues!=dependencyValues){this.loadDynamicOptions()}}
DropdownEditor.prototype.onExternalPropertyEditorHidden=function(){if(this.dynamicOptions){this.loadDynamicOptions(false)}}
DropdownEditor.prototype.updateDisplayedValue=function(value){var select=this.getSelect()
select.value=value}
DropdownEditor.prototype.getUndefinedValue=function(){if(this.propertyDefinition.default!==undefined){return this.propertyDefinition.default}if(this.propertyDefinition.placeholder!==undefined){return undefined}var select=this.getSelect()
if(select){return this.normalizeValue(select.value)}return undefined}
DropdownEditor.prototype.isEmptyValue=function(value){if(this.propertyDefinition.booleanValues){if(value===''){return true}return false}return BaseProto.isEmptyValue.call(this,value)}
DropdownEditor.prototype.destroyCustomSelect=function(){var $select=$(this.getSelect())
if($select.data('select2')!=null){$select.select2('destroy')}}
DropdownEditor.prototype.unregisterHandlers=function(){var select=this.getSelect()
$(select).off('change',this.proxy(this.onSelectionChange))}
DropdownEditor.prototype.loadStaticOptions=function(select){var value=this.inspector.getPropertyValue(this.propertyDefinition.property)
this.createPlaceholder(select)
this.createOptions(select,this.propertyDefinition.options)
if(value===undefined){value=this.propertyDefinition.default}select.value=value}
DropdownEditor.prototype.loadDynamicOptions=function(initialization){var currentValue=this.inspector.getPropertyValue(this.propertyDefinition.property),data=this.getRootSurface().getValues(),self=this,$form=$(this.getSelect()).closest('form'),dependents=this.inspector.findDependentProperties(this.propertyDefinition.property)
if(currentValue===undefined){currentValue=this.propertyDefinition.default}var callback=function dropdownOptionsRequestDoneClosure(data){self.hideLoadingIndicator()
self.optionsRequestDone(data,currentValue,true)
if(dependents.length>0){for(var i in dependents){var editor=self.inspector.findPropertyEditor(dependents[i])
if(editor&&typeof editor.onInspectorPropertyChanged==='function'){editor.onInspectorPropertyChanged(self.propertyDefinition.property)}}}}
if(this.propertyDefinition.depends){this.saveDependencyValues()}data['inspectorProperty']=this.getPropertyPath()
data['inspectorClassName']=this.inspector.options.inspectorClass
this.showLoadingIndicator()
if(this.triggerGetOptions(data,callback)===false){return}$form.request('onInspectableGetOptions',{data:data,}).done(callback).always(this.proxy(this.hideLoadingIndicator))}
DropdownEditor.prototype.triggerGetOptions=function(values,callback){var $inspectable=this.getInspectableElement()
if(!$inspectable){return true}var optionsEvent=$.Event('dropdownoptions.oc.inspector')
$inspectable.trigger(optionsEvent,[{values:values,callback:callback,property:this.inspector.getPropertyPath(this.propertyDefinition.property),propertyDefinition:this.propertyDefinition}])
if(optionsEvent.isDefaultPrevented()){return false}return true}
DropdownEditor.prototype.saveDependencyValues=function(){this.prevDependencyValues=this.getDependencyValues()}
DropdownEditor.prototype.getDependencyValues=function(){var result=''
for(var i=0,len=this.propertyDefinition.depends.length;i<len;i++){var property=this.propertyDefinition.depends[i],value=this.inspector.getPropertyValue(property)
if(value===undefined){value='';}result+=property+':'+value+'-'}return result}
DropdownEditor.prototype.showLoadingIndicator=function(){if(!Modernizr.touchevents){this.indicatorContainer.loadIndicator()}}
DropdownEditor.prototype.hideLoadingIndicator=function(){if(this.isDisposed()){return}if(!Modernizr.touchevents){this.indicatorContainer.loadIndicator('hide')
this.indicatorContainer.loadIndicator('destroy')}}
DropdownEditor.prototype.optionsRequestDone=function(data,currentValue,initialization){if(this.isDisposed()){return}var select=this.getSelect()
this.destroyCustomSelect()
this.clearOptions(select)
this.initCustomSelect()
this.createPlaceholder(select)
if(data.options){for(var i=0,len=data.options.length;i<len;i++){this.createOption(select,data.options[i].title,data.options[i].value)}}if(this.hasOptionValue(select,currentValue)){select.value=currentValue}else{select.selectedIndex=this.propertyDefinition.placeholder===undefined?0:-1}this.initialization=initialization
$(select).trigger('change')
this.initialization=false}
$.wn.inspector.propertyEditors.dropdown=DropdownEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.base,BaseProto=Base.prototype
var PopupBase=function(inspector,propertyDefinition,containerCell,group){this.popup=null
Base.call(this,inspector,propertyDefinition,containerCell,group)}
PopupBase.prototype=Object.create(BaseProto)
PopupBase.prototype.constructor=Base
PopupBase.prototype.dispose=function(){this.unregisterHandlers()
this.popup=null
BaseProto.dispose.call(this)}
PopupBase.prototype.build=function(){var link=document.createElement('a')
$.wn.foundation.element.addClass(link,'trigger')
link.setAttribute('href','#')
this.setLinkText(link)
$.wn.foundation.element.addClass(this.containerCell,'trigger-cell')
this.containerCell.appendChild(link)}
PopupBase.prototype.setLinkText=function(link,value){}
PopupBase.prototype.getPopupContent=function(){return'<form>                                                                                  \
                <div class="modal-header">                                                              \
                    <button type="button" class="close" data-dismiss="popup">&times;</button>           \
                    <h4 class="modal-title">{{property}}</h4>                                           \
                </div>                                                                                  \
                <div class="modal-body">                                                                \
                    <div class="form-group">                                                            \
                    </div>                                                                              \
                </div>                                                                                  \
                <div class="modal-footer">                                                              \
                    <button type="submit" class="btn btn-primary">OK</button>                           \
                    <button type="button" class="btn btn-default" data-dismiss="popup">Cancel</button>  \
                </div>                                                                                  \
                </form>'
}
PopupBase.prototype.updateDisplayedValue=function(value){this.setLinkText(this.getLink(),value)}
PopupBase.prototype.registerHandlers=function(){var link=this.getLink(),$link=$(link)
link.addEventListener('click',this.proxy(this.onTriggerClick))
$link.on('shown.oc.popup',this.proxy(this.onPopupShown))
$link.on('hidden.oc.popup',this.proxy(this.onPopupHidden))}
PopupBase.prototype.unregisterHandlers=function(){var link=this.getLink(),$link=$(link)
link.removeEventListener('click',this.proxy(this.onTriggerClick))
$link.off('shown.oc.popup',this.proxy(this.onPopupShown))
$link.off('hidden.oc.popup',this.proxy(this.onPopupHidden))}
PopupBase.prototype.getLink=function(){return this.containerCell.querySelector('a.trigger')}
PopupBase.prototype.configurePopup=function(popup){}
PopupBase.prototype.handleSubmit=function($form){}
PopupBase.prototype.hidePopup=function(){$(this.getLink()).popup('hide')}
PopupBase.prototype.onTriggerClick=function(ev){$.wn.foundation.event.stop(ev)
var content=this.getPopupContent()
content=content.replace('{{property}}',this.propertyDefinition.title)
$(ev.target).popup({content:content})
return false}
PopupBase.prototype.onPopupShown=function(ev,link,popup){$(popup).on('submit.inspector','form',this.proxy(this.onSubmit))
this.popup=popup.get(0)
this.configurePopup(popup)
this.getRootSurface().popupDisplayed()}
PopupBase.prototype.onPopupHidden=function(ev,link,popup){$(popup).off('.inspector','form',this.proxy(this.onSubmit))
this.popup=null
this.getRootSurface().popupHidden()}
PopupBase.prototype.onSubmit=function(ev){ev.preventDefault()
if(this.handleSubmit($(ev.target))===false){return false}this.setLinkText(this.getLink())
this.hidePopup()
return false}
$.wn.inspector.propertyEditors.popupBase=PopupBase}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.popupBase,BaseProto=Base.prototype
var TextEditor=function(inspector,propertyDefinition,containerCell,group){Base.call(this,inspector,propertyDefinition,containerCell,group)}
TextEditor.prototype=Object.create(BaseProto)
TextEditor.prototype.constructor=Base
TextEditor.prototype.setLinkText=function(link,value){var value=value!==undefined?value:this.inspector.getPropertyValue(this.propertyDefinition.property)
if(value===undefined){value=this.propertyDefinition.default}if(!value){value=this.propertyDefinition.placeholder
$.wn.foundation.element.addClass(link,'placeholder')}else{$.wn.foundation.element.removeClass(link,'placeholder')}if(typeof value==='string'){value=value.replace(/(?:\r\n|\r|\n)/g,' ');value=$.trim(value)
value=value.substring(0,300);}link.textContent=value}
TextEditor.prototype.getPopupContent=function(){return'<form>                                                                                  \
                <div class="modal-header">                                                              \
                    <button type="button" class="close" data-dismiss="popup">&times;</button>           \
                    <h4 class="modal-title">{{property}}</h4>                                           \
                </div>                                                                                  \
                <div class="modal-body">                                                                \
                    <div class="form-group">                                                            \
                        <p class="inspector-field-comment"></p>                                         \
                        <textarea class="form-control size-small field-textarea" name="name">           \
                        </textarea>                                                                     \
                    </div>                                                                              \
                </div>                                                                                  \
                <div class="modal-footer">                                                              \
                    <button type="submit" class="btn btn-primary">OK</button>                           \
                    <button type="button" class="btn btn-default" data-dismiss="popup">Cancel</button>  \
                </div>                                                                                  \
                </form>'
}
TextEditor.prototype.configureComment=function(popup){if(!this.propertyDefinition.description){return}var descriptionElement=$(popup).find('p.inspector-field-comment')
descriptionElement.text(this.propertyDefinition.description)}
TextEditor.prototype.configurePopup=function(popup){var $textarea=$(popup).find('textarea'),value=this.inspector.getPropertyValue(this.propertyDefinition.property)
if(this.propertyDefinition.placeholder){$textarea.attr('placeholder',this.propertyDefinition.placeholder)}if(value===undefined){value=this.propertyDefinition.default}$textarea.val(value)
$textarea.focus()
this.configureComment(popup)}
TextEditor.prototype.handleSubmit=function($form){var $textarea=$form.find('textarea'),link=this.getLink(),value=$.trim($textarea.val())
this.inspector.setPropertyValue(this.propertyDefinition.property,value)}
$.wn.inspector.propertyEditors.text=TextEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.base,BaseProto=Base.prototype
var SetEditor=function(inspector,propertyDefinition,containerCell,group){this.editors=[]
this.loadedItems=null
Base.call(this,inspector,propertyDefinition,containerCell,group)}
SetEditor.prototype=Object.create(BaseProto)
SetEditor.prototype.constructor=Base
SetEditor.prototype.init=function(){this.initControlGroup()
BaseProto.init.call(this)}
SetEditor.prototype.dispose=function(){this.disposeEditors()
this.disposeControls()
this.editors=null
BaseProto.dispose.call(this)}
SetEditor.prototype.build=function(){var link=document.createElement('a')
$.wn.foundation.element.addClass(link,'trigger')
link.setAttribute('href','#')
this.setLinkText(link)
$.wn.foundation.element.addClass(this.containerCell,'trigger-cell')
this.containerCell.appendChild(link)
if(this.propertyDefinition.items!==undefined){this.loadStaticItems()}else{this.loadDynamicItems()}}
SetEditor.prototype.loadStaticItems=function(){var itemArray=[]
for(var itemValue in this.propertyDefinition.items){itemArray.push({value:itemValue,title:this.propertyDefinition.items[itemValue]})}for(var i=itemArray.length-1;i>=0;i--){this.buildItemEditor(String(itemArray[i].value),itemArray[i].title)}}
SetEditor.prototype.setLinkText=function(link,value){var value=(value!==undefined&&value!==null)?value:this.getNormalizedValue(),text='[ ]'
if(value===undefined){value=this.propertyDefinition.default}if(value!==undefined&&value.length!==undefined&&value.length>0&&typeof value!=='string'){var textValues=[]
for(var i=0,len=value.length;i<len;i++){textValues.push(this.valueToText(value[i]))}text='['+textValues.join(', ')+']'
$.wn.foundation.element.removeClass(link,'placeholder')}else{text=this.propertyDefinition.placeholder
if((typeof text==='string'&&text.length==0)||text===undefined){text='[ ]'}$.wn.foundation.element.addClass(link,'placeholder')}link.textContent=text}
SetEditor.prototype.buildItemEditor=function(value,text){var property={title:text,itemType:'property',groupIndex:this.group.getGroupIndex()},newRow=this.createGroupedRow(property),currentRow=this.containerCell.parentNode,tbody=this.containerCell.parentNode.parentNode,cell=document.createElement('td')
this.buildCheckbox(cell,value,text)
newRow.appendChild(cell)
tbody.insertBefore(newRow,currentRow.nextSibling)}
SetEditor.prototype.buildCheckbox=function(cell,value,title){var property={property:value,title:title,default:this.isCheckedByDefault(value)},editor=new $.wn.inspector.propertyEditors.checkbox(this,property,cell,this.group)
this.editors.push(editor)}
SetEditor.prototype.isCheckedByDefault=function(value){if(!this.propertyDefinition.default){return false}return this.propertyDefinition.default.indexOf(value)>-1}
SetEditor.prototype.showLoadingIndicator=function(){$(this.getLink()).loadIndicator()}
SetEditor.prototype.hideLoadingIndicator=function(){if(this.isDisposed()){return}var $link=$(this.getLink())
$link.loadIndicator('hide')
$link.loadIndicator('destroy')}
SetEditor.prototype.loadDynamicItems=function(){var link=this.getLink(),data=this.inspector.getValues(),$form=$(link).closest('form')
$.wn.foundation.element.addClass(link,'loading-indicator-container size-small')
this.showLoadingIndicator()
data.inspectorProperty=this.getPropertyPath()
data.inspectorClassName=this.inspector.options.inspectorClass
$form.request('onInspectableGetOptions',{data:data,}).done(this.proxy(this.itemsRequestDone)).always(this.proxy(this.hideLoadingIndicator))}
SetEditor.prototype.itemsRequestDone=function(data,currentValue,initialization){if(this.isDisposed()){return}this.loadedItems={}
if(data.options){for(var i=data.options.length-1;i>=0;i--){this.buildItemEditor(data.options[i].value,data.options[i].title)
this.loadedItems[String(data.options[i].value)]=data.options[i].title}}this.setLinkText(this.getLink())}
SetEditor.prototype.getLink=function(){return this.containerCell.querySelector('a.trigger')}
SetEditor.prototype.getItemsSource=function(){if(this.propertyDefinition.items!==undefined){return this.propertyDefinition.items}return this.loadedItems}
SetEditor.prototype.valueToText=function(value){var source=this.getItemsSource()
if(!source){return value}for(var itemValue in source){if(itemValue==value){return source[itemValue]}}return value}
SetEditor.prototype.cleanUpValue=function(value){if(!value){return value}var result=[],source=this.getItemsSource()
for(var i=0,len=value.length;i<len;i++){var currentValue=value[i]
if(source[currentValue]!==undefined){result.push(currentValue)}}return result}
SetEditor.prototype.getNormalizedValue=function(){var value=this.inspector.getPropertyValue(this.propertyDefinition.property)
if(value===null){value=undefined}if(value===undefined){return value}if(value.length===undefined||typeof value==='string'){return undefined}return value}
SetEditor.prototype.supportsExternalParameterEditor=function(){return false}
SetEditor.prototype.isGroupedEditor=function(){return true}
SetEditor.prototype.getPropertyValue=function(checkboxValue){var value=this.getNormalizedValue(),checkboxValueStr=String(checkboxValue)
if(value===undefined){return this.isCheckedByDefault(checkboxValueStr)}if(!value){return false}return value.indexOf(checkboxValueStr)>-1}
SetEditor.prototype.setPropertyValue=function(checkboxValue,isChecked){var currentValue=this.getNormalizedValue(),checkboxValueStr=String(checkboxValue)
if(currentValue===undefined){currentValue=this.propertyDefinition.default}if(!currentValue){currentValue=[]}var resultValue=[],items=this.getItemsSource()
for(var itemValue in items){if(itemValue!==checkboxValueStr){if(currentValue.indexOf(itemValue)!==-1){resultValue.push(itemValue)}}else{if(isChecked){resultValue.push(itemValue)}}}this.inspector.setPropertyValue(this.propertyDefinition.property,this.cleanUpValue(resultValue))
this.setLinkText(this.getLink())}
SetEditor.prototype.generateSequencedId=function(){return this.inspector.generateSequencedId()}
SetEditor.prototype.disposeEditors=function(){for(var i=0,len=this.editors.length;i<len;i++){var editor=this.editors[i]
editor.dispose()}}
SetEditor.prototype.disposeControls=function(){var link=this.getLink()
if(this.propertyDefinition.items===undefined){$(link).loadIndicator('destroy')}link.parentNode.removeChild(link)}
$.wn.inspector.propertyEditors.set=SetEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.base,BaseProto=Base.prototype
var ObjectListEditor=function(inspector,propertyDefinition,containerCell,group){this.currentRowInspector=null
this.popup=null
if(propertyDefinition.titleProperty===undefined){throw new Error('The titleProperty property should be specified in the objectList editor configuration. Property: '+propertyDefinition.property)}if(propertyDefinition.itemProperties===undefined){throw new Error('The itemProperties property should be specified in the objectList editor configuration. Property: '+propertyDefinition.property)}Base.call(this,inspector,propertyDefinition,containerCell,group)}
ObjectListEditor.prototype=Object.create(BaseProto)
ObjectListEditor.prototype.constructor=Base
ObjectListEditor.prototype.init=function(){if(this.isKeyValueMode()){var keyProperty=this.getKeyProperty()
if(!keyProperty){throw new Error('Object list key property '+this.propertyDefinition.keyProperty+' is not defined in itemProperties. Property: '+this.propertyDefinition.property)}}BaseProto.init.call(this)}
ObjectListEditor.prototype.dispose=function(){this.unregisterHandlers()
this.removeControls()
this.currentRowInspector=null
this.popup=null
BaseProto.dispose.call(this)}
ObjectListEditor.prototype.supportsExternalParameterEditor=function(){return false}
ObjectListEditor.prototype.build=function(){var link=document.createElement('a')
$.wn.foundation.element.addClass(link,'trigger')
link.setAttribute('href','#')
this.setLinkText(link)
$.wn.foundation.element.addClass(this.containerCell,'trigger-cell')
this.containerCell.appendChild(link)}
ObjectListEditor.prototype.setLinkText=function(link,value){var value=value!==undefined&&value!==null?value:this.inspector.getPropertyValue(this.propertyDefinition.property)
if(value===null){value=undefined}if(value===undefined){var placeholder=this.propertyDefinition.placeholder
if(placeholder!==undefined){$.wn.foundation.element.addClass(link,'placeholder')
link.textContent=placeholder}else{link.textContent='Items: 0'}}else{var itemCount=0
if(!this.isKeyValueMode()){if(value.length===undefined){throw new Error('Object list value should be an array. Property: '+this.propertyDefinition.property)}itemCount=value.length}else{if(typeof value!=='object'){throw new Error('Object list value should be an object. Property: '+this.propertyDefinition.property)}itemCount=this.getValueKeys(value).length}$.wn.foundation.element.removeClass(link,'placeholder')
link.textContent='Items: '+itemCount}}
ObjectListEditor.prototype.getPopupContent=function(){return'<form>                                                                                  \
                <div class="modal-header">                                                              \
                    <button type="button" class="close" data-dismiss="popup">&times;</button>           \
                    <h4 class="modal-title">{{property}}</h4>                                           \
                </div>                                                                                  \
                <div>                                                                                   \
                    <div class="layout inspector-columns-editor">                                       \
                        <div class="layout-row">                                                        \
                            <div class="layout-cell items-column">                                      \
                                <div class="layout-relative">                                           \
                                    <div class="layout">                                                \
                                        <div class="layout-row min-size">                               \
                                            <div class="control-toolbar toolbar-padded">                \
                                                <div class="toolbar-item">                              \
                                                    <div class="btn-group">                             \
                                                        <button type="button" class="btn btn-primary    \
                                                            wn-icon-plus"                               \
                                                            data-cmd="create-item">Add</button>         \
                                                        <button type="button" class="btn btn-default    \
                                                            empty wn-icon-trash-o"                      \
                                                            data-cmd="delete-item"></button>            \
                                                    </div>                                              \
                                                </div>                                                  \
                                            </div>                                                      \
                                        </div>                                                          \
                                        <div class="layout-row">                                        \
                                            <div class="layout-cell">                                   \
                                                <div class="layout-relative">                           \
                                                    <div class="layout-absolute">                       \
                                                        <div class="control-scrollpad"                  \
                                                            data-control="scrollpad">                   \
                                                            <div class="scroll-wrapper">                \
                                                                <table class="table data                \
                                                                    no-offset-bottom                    \
                                                                    inspector-table-list">              \
                                                                </table>                                \
                                                            </div>                                      \
                                                        </div>                                          \
                                                    </div>                                              \
                                                </div>                                                  \
                                            </div>                                                      \
                                        </div>                                                          \
                                    </div>                                                              \
                                </div>                                                                  \
                            </div>                                                                      \
                            <div class="layout-cell">                                                   \
                                <div class="layout-relative">                                           \
                                    <div class="layout-absolute">                                       \
                                        <div class="control-scrollpad" data-control="scrollpad">        \
                                            <div class="scroll-wrapper inspector-wrapper">              \
                                                <div data-inspector-container>                          \
                                                </div>                                                  \
                                            </div>                                                      \
                                        </div>                                                          \
                                    </div>                                                              \
                                </div>                                                                  \
                            </div>                                                                      \
                        </div>                                                                          \
                    </div>                                                                              \
                </div>                                                                                  \
                <div class="modal-footer">                                                              \
                    <button type="submit" class="btn btn-primary">OK</button>                           \
                    <button type="button" class="btn btn-default" data-dismiss="popup">Cancel</button>  \
                </div>                                                                                  \
                </form>'
}
ObjectListEditor.prototype.buildPopupContents=function(popup){this.buildItemsTable(popup)}
ObjectListEditor.prototype.buildItemsTable=function(popup){var table=popup.querySelector('table'),tbody=document.createElement('tbody'),items=this.inspector.getPropertyValue(this.propertyDefinition.property),titleProperty=this.propertyDefinition.titleProperty
if(items===undefined||this.getValueKeys(items).length===0){var row=this.buildEmptyRow()
tbody.appendChild(row)}else{var firstRow=undefined
for(var key in items){var item=items[key],itemInspectorValue=this.addKeyProperty(key,item),itemText=item[titleProperty],row=this.buildTableRow(itemText,'rowlink')
row.setAttribute('data-inspector-values',JSON.stringify(itemInspectorValue))
tbody.appendChild(row)
if(firstRow===undefined){firstRow=row}}}table.appendChild(tbody)
if(firstRow!==undefined){this.selectRow(firstRow,true)}this.updateScrollpads()}
ObjectListEditor.prototype.buildEmptyRow=function(){return this.buildTableRow('No items found','no-data','nolink')}
ObjectListEditor.prototype.removeEmptyRow=function(){var tbody=this.getTableBody(),row=tbody.querySelector('tr.no-data')
if(row){tbody.removeChild(row)}}
ObjectListEditor.prototype.buildTableRow=function(text,rowClass,cellClass){var row=document.createElement('tr'),cell=document.createElement('td')
cell.textContent=text
if(rowClass!==undefined){$.wn.foundation.element.addClass(row,rowClass)}if(cellClass!==undefined){$.wn.foundation.element.addClass(cell,cellClass)}row.appendChild(cell)
return row}
ObjectListEditor.prototype.updateScrollpads=function(){$('.control-scrollpad',this.popup).scrollpad('update')}
ObjectListEditor.prototype.selectRow=function(row,forceSelect){var tbody=row.parentNode,inspectorContainer=this.getInspectorContainer(),selectedRow=this.getSelectedRow()
if(selectedRow===row&&!forceSelect){return}if(selectedRow){if(!this.validateKeyValue()){return}if(this.currentRowInspector){if(!this.currentRowInspector.validate()){return}}this.applyDataToRow(selectedRow)
$.wn.foundation.element.removeClass(selectedRow,'active')}this.disposeInspector()
$.wn.foundation.element.addClass(row,'active')
this.createInspectorForRow(row,inspectorContainer)}
ObjectListEditor.prototype.createInspectorForRow=function(row,inspectorContainer){var dataStr=row.getAttribute('data-inspector-values')
if(dataStr===undefined||typeof dataStr!=='string'){throw new Error('Values not found for the selected row.')}var properties=this.propertyDefinition.itemProperties,values=JSON.parse(dataStr),options={enableExternalParameterEditor:false,onChange:this.proxy(this.onInspectorDataChange),inspectorClass:this.inspector.options.inspectorClass}
this.currentRowInspector=new $.wn.inspector.surface(inspectorContainer,properties,values,$.wn.inspector.helpers.generateElementUniqueId(inspectorContainer),options)}
ObjectListEditor.prototype.disposeInspector=function(){$.wn.foundation.controlUtils.disposeControls(this.popup.querySelector('[data-inspector-container]'))
this.currentRowInspector=null}
ObjectListEditor.prototype.applyDataToRow=function(row){if(this.currentRowInspector===null){return}var data=this.currentRowInspector.getValues()
row.setAttribute('data-inspector-values',JSON.stringify(data))}
ObjectListEditor.prototype.updateRowText=function(property,value){var selectedRow=this.getSelectedRow()
if(!selectedRow){throw new Exception('A row is not found for the updated data')}if(property!==this.propertyDefinition.titleProperty){return}value=$.trim(value)
if(value.length===0){value='[No title]'
$.wn.foundation.element.addClass(selectedRow,'disabled')}else{$.wn.foundation.element.removeClass(selectedRow,'disabled')}selectedRow.firstChild.textContent=value}
ObjectListEditor.prototype.getSelectedRow=function(){if(!this.popup){throw new Error('Trying to get selected row without a popup reference.')}var rows=this.getTableBody().children
for(var i=0,len=rows.length;i<len;i++){if($.wn.foundation.element.hasClass(rows[i],'active')){return rows[i]}}return null}
ObjectListEditor.prototype.createItem=function(){var selectedRow=this.getSelectedRow()
if(selectedRow){if(!this.validateKeyValue()){return}if(this.currentRowInspector){if(!this.currentRowInspector.validate()){return}}this.applyDataToRow(selectedRow)
$.wn.foundation.element.removeClass(selectedRow,'active')}this.disposeInspector()
var title='New item',row=this.buildTableRow(title,'rowlink active'),tbody=this.getTableBody(),data={}
data[this.propertyDefinition.titleProperty]=title
row.setAttribute('data-inspector-values',JSON.stringify(data))
tbody.appendChild(row)
this.selectRow(row,true)
this.removeEmptyRow()
this.updateScrollpads()}
ObjectListEditor.prototype.deleteItem=function(){var selectedRow=this.getSelectedRow()
if(!selectedRow){return}var nextRow=selectedRow.nextElementSibling,prevRow=selectedRow.previousElementSibling,tbody=this.getTableBody()
this.disposeInspector()
tbody.removeChild(selectedRow)
var newSelectedRow=nextRow?nextRow:prevRow
if(newSelectedRow){this.selectRow(newSelectedRow)}else{tbody.appendChild(this.buildEmptyRow())}this.updateScrollpads()}
ObjectListEditor.prototype.applyDataToParentInspector=function(){var selectedRow=this.getSelectedRow(),tbody=this.getTableBody(),dataRows=tbody.querySelectorAll('tr[data-inspector-values]'),link=this.getLink(),result=this.getEmptyValue()
if(selectedRow){if(!this.validateKeyValue()){return}if(this.currentRowInspector){if(!this.currentRowInspector.validate()){return}}this.applyDataToRow(selectedRow)}for(var i=0,len=dataRows.length;i<len;i++){var dataRow=dataRows[i],rowData=JSON.parse(dataRow.getAttribute('data-inspector-values'))
if(!this.isKeyValueMode()){result.push(rowData)}else{var rowKey=rowData[this.propertyDefinition.keyProperty]
result[rowKey]=this.removeKeyProperty(rowData)}}this.inspector.setPropertyValue(this.propertyDefinition.property,result)
this.setLinkText(link,result)
$(link).popup('hide')
return false}
ObjectListEditor.prototype.validateKeyValue=function(){if(!this.isKeyValueMode()){return true}if(this.currentRowInspector===null){return true}var data=this.currentRowInspector.getValues(),keyProperty=this.propertyDefinition.keyProperty
if(data[keyProperty]===undefined){throw new Error('Key property '+keyProperty+' is not found in the Inspector data. Property: '+this.propertyDefinition.property)}var keyPropertyValue=data[keyProperty],keyPropertyTitle=this.getKeyProperty().title
if(typeof keyPropertyValue!=='string'){throw new Error('Key property ('+keyProperty+') value should be a string. Property: '+this.propertyDefinition.property)}if($.trim(keyPropertyValue).length===0){$.wn.flashMsg({text:'The value of key property '+keyPropertyTitle+' cannot be empty.','class':'error','interval':3})
return false}var selectedRow=this.getSelectedRow(),tbody=this.getTableBody(),dataRows=tbody.querySelectorAll('tr[data-inspector-values]')
for(var i=0,len=dataRows.length;i<len;i++){var dataRow=dataRows[i],rowData=JSON.parse(dataRow.getAttribute('data-inspector-values'))
if(selectedRow==dataRow){continue}if(rowData[keyProperty]==keyPropertyValue){$.wn.flashMsg({text:'The value of key property '+keyPropertyTitle+' should be unique.','class':'error','interval':3})
return false}}return true}
ObjectListEditor.prototype.getLink=function(){return this.containerCell.querySelector('a.trigger')}
ObjectListEditor.prototype.getPopupFormElement=function(){var form=this.popup.querySelector('form')
if(!form){this.throwError('Cannot find form element in the popup window.')}return form}
ObjectListEditor.prototype.getInspectorContainer=function(){return this.popup.querySelector('div[data-inspector-container]')}
ObjectListEditor.prototype.getTableBody=function(){return this.popup.querySelector('table.inspector-table-list tbody')}
ObjectListEditor.prototype.isKeyValueMode=function(){return this.propertyDefinition.keyProperty!==undefined}
ObjectListEditor.prototype.getKeyProperty=function(){for(var i=0,len=this.propertyDefinition.itemProperties.length;i<len;i++){var property=this.propertyDefinition.itemProperties[i]
if(property.property==this.propertyDefinition.keyProperty){return property}}}
ObjectListEditor.prototype.getValueKeys=function(value){var result=[]
for(var key in value){result.push(key)}return result}
ObjectListEditor.prototype.addKeyProperty=function(key,value){if(!this.isKeyValueMode()){return value}value[this.propertyDefinition.keyProperty]=key
return value}
ObjectListEditor.prototype.removeKeyProperty=function(value){if(!this.isKeyValueMode()){return value}var result=value
if(result[this.propertyDefinition.keyProperty]!==undefined){delete result[this.propertyDefinition.keyProperty]}return result}
ObjectListEditor.prototype.getEmptyValue=function(){if(this.isKeyValueMode()){return{}}else{return[]}}
ObjectListEditor.prototype.registerHandlers=function(){var link=this.getLink(),$link=$(link)
link.addEventListener('click',this.proxy(this.onTriggerClick))
$link.on('shown.oc.popup',this.proxy(this.onPopupShown))
$link.on('hidden.oc.popup',this.proxy(this.onPopupHidden))}
ObjectListEditor.prototype.unregisterHandlers=function(){var link=this.getLink(),$link=$(link)
link.removeEventListener('click',this.proxy(this.onTriggerClick))
$link.off('shown.oc.popup',this.proxy(this.onPopupShown))
$link.off('hidden.oc.popup',this.proxy(this.onPopupHidden))}
ObjectListEditor.prototype.onTriggerClick=function(ev){$.wn.foundation.event.stop(ev)
var content=this.getPopupContent()
content=content.replace('{{property}}',this.propertyDefinition.title)
$(ev.target).popup({content:content})
return false}
ObjectListEditor.prototype.onPopupShown=function(ev,link,popup){$(popup).on('submit.inspector','form',this.proxy(this.onSubmit))
$(popup).on('click','tr.rowlink',this.proxy(this.onRowClick))
$(popup).on('click.inspector','[data-cmd]',this.proxy(this.onCommand))
this.popup=popup.get(0)
this.buildPopupContents(this.popup)
this.getRootSurface().popupDisplayed()}
ObjectListEditor.prototype.onPopupHidden=function(ev,link,popup){$(popup).off('.inspector',this.proxy(this.onSubmit))
$(popup).off('click','tr.rowlink',this.proxy(this.onRowClick))
$(popup).off('click.inspector','[data-cmd]',this.proxy(this.onCommand))
this.disposeInspector()
$.wn.foundation.controlUtils.disposeControls(this.popup)
this.popup=null
this.getRootSurface().popupHidden()}
ObjectListEditor.prototype.onSubmit=function(ev){this.applyDataToParentInspector()
ev.preventDefault()
return false}
ObjectListEditor.prototype.onRowClick=function(ev){this.selectRow(ev.currentTarget)}
ObjectListEditor.prototype.onInspectorDataChange=function(property,value){this.updateRowText(property,value)}
ObjectListEditor.prototype.onCommand=function(ev){var command=ev.currentTarget.getAttribute('data-cmd')
switch(command){case'create-item':this.createItem()
break;case'delete-item':this.deleteItem()
break;}}
ObjectListEditor.prototype.removeControls=function(){if(this.popup){this.disposeInspector(this.popup)}}
$.wn.inspector.propertyEditors.objectList=ObjectListEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.base,BaseProto=Base.prototype
var ObjectEditor=function(inspector,propertyDefinition,containerCell,group){if(propertyDefinition.properties===undefined){this.throwError('The properties property should be specified in the object editor configuration.')}Base.call(this,inspector,propertyDefinition,containerCell,group)}
ObjectEditor.prototype=Object.create(BaseProto)
ObjectEditor.prototype.constructor=Base
ObjectEditor.prototype.init=function(){this.initControlGroup()
BaseProto.init.call(this)}
ObjectEditor.prototype.build=function(){var currentRow=this.containerCell.parentNode,inspectorContainer=document.createElement('div'),options={enableExternalParameterEditor:false,onChange:this.proxy(this.onInspectorDataChange),inspectorClass:this.inspector.options.inspectorClass},values=this.inspector.getPropertyValue(this.propertyDefinition.property)
if(values===undefined){values={}}this.childInspector=new $.wn.inspector.surface(inspectorContainer,this.propertyDefinition.properties,values,this.inspector.getInspectorUniqueId()+'-'+this.propertyDefinition.property,options,this.inspector,this.group,this.propertyDefinition.property)
this.inspector.mergeChildSurface(this.childInspector,currentRow)}
ObjectEditor.prototype.cleanUpValue=function(value){if(value===undefined||typeof value!=='object'){return undefined}if(this.propertyDefinition.ignoreIfPropertyEmpty===undefined){return value}return this.getValueOrRemove(value)}
ObjectEditor.prototype.getValueOrRemove=function(value){if(this.propertyDefinition.ignoreIfPropertyEmpty===undefined){return value}var targetProperty=this.propertyDefinition.ignoreIfPropertyEmpty,targetValue=value[targetProperty]
if(this.isEmptyValue(targetValue)){return $.wn.inspector.removedProperty}return value}
ObjectEditor.prototype.supportsExternalParameterEditor=function(){return false}
ObjectEditor.prototype.isGroupedEditor=function(){return true}
ObjectEditor.prototype.getUndefinedValue=function(){var result={}
for(var i=0,len=this.propertyDefinition.properties.length;i<len;i++){var propertyName=this.propertyDefinition.properties[i].property,editor=this.childInspector.findPropertyEditor(propertyName)
if(editor){result[propertyName]=editor.getUndefinedValue()}}return this.getValueOrRemove(result)}
ObjectEditor.prototype.validate=function(silentMode){var values=this.childInspector.getValues()
if(this.cleanUpValue(values)===$.wn.inspector.removedProperty){return true}return this.childInspector.validate(silentMode)}
ObjectEditor.prototype.onInspectorDataChange=function(property,value){var values=this.childInspector.getValues()
this.inspector.setPropertyValue(this.propertyDefinition.property,this.cleanUpValue(values))}
$.wn.inspector.propertyEditors.object=ObjectEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.text,BaseProto=Base.prototype
var StringListEditor=function(inspector,propertyDefinition,containerCell,group){Base.call(this,inspector,propertyDefinition,containerCell,group)}
StringListEditor.prototype=Object.create(BaseProto)
StringListEditor.prototype.constructor=Base
StringListEditor.prototype.setLinkText=function(link,value){var value=value!==undefined?value:this.inspector.getPropertyValue(this.propertyDefinition.property)
if(value===undefined){value=this.propertyDefinition.default}this.checkValueType(value)
if(!value){value=this.propertyDefinition.placeholder
$.wn.foundation.element.addClass(link,'placeholder')
if(!value){value='[]'}link.textContent=value}else{$.wn.foundation.element.removeClass(link,'placeholder')
link.textContent='['+value.join(', ')+']'}}
StringListEditor.prototype.checkValueType=function(value){if(value&&Object.prototype.toString.call(value)!=='[object Array]'){this.throwError('The string list value should be an array.')}}
StringListEditor.prototype.configurePopup=function(popup){var $textarea=$(popup).find('textarea'),value=this.inspector.getPropertyValue(this.propertyDefinition.property)
if(this.propertyDefinition.placeholder){$textarea.attr('placeholder',this.propertyDefinition.placeholder)}if(value===undefined){value=this.propertyDefinition.default}this.checkValueType(value)
if(value&&value.length){$textarea.val(value.join('\n'))}$textarea.focus()
this.configureComment(popup)}
StringListEditor.prototype.handleSubmit=function($form){var $textarea=$form.find('textarea'),link=this.getLink(),value=$.trim($textarea.val()),arrayValue=[],resultValue=[]
if(value.length){value=value.replace(/\r\n/g,'\n')
arrayValue=value.split('\n')
for(var i=0,len=arrayValue.length;i<len;i++){var currentValue=$.trim(arrayValue[i])
if(currentValue.length>0){resultValue.push(currentValue)}}}this.inspector.setPropertyValue(this.propertyDefinition.property,resultValue)}
$.wn.inspector.propertyEditors.stringList=StringListEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.popupBase,BaseProto=Base.prototype
var StringListAutocomplete=function(inspector,propertyDefinition,containerCell,group){this.items=null
Base.call(this,inspector,propertyDefinition,containerCell,group)}
StringListAutocomplete.prototype=Object.create(BaseProto)
StringListAutocomplete.prototype.constructor=Base
StringListAutocomplete.prototype.dispose=function(){BaseProto.dispose.call(this)}
StringListAutocomplete.prototype.init=function(){BaseProto.init.call(this)}
StringListAutocomplete.prototype.supportsExternalParameterEditor=function(){return false}
StringListAutocomplete.prototype.setLinkText=function(link,value){var value=value!==undefined?value:this.inspector.getPropertyValue(this.propertyDefinition.property)
if(value===undefined){value=this.propertyDefinition.default}this.checkValueType(value)
if(!value){value=this.propertyDefinition.placeholder
$.wn.foundation.element.addClass(link,'placeholder')
if(!value){value='[]'}link.textContent=value}else{$.wn.foundation.element.removeClass(link,'placeholder')
link.textContent='['+value.join(', ')+']'}}
StringListAutocomplete.prototype.checkValueType=function(value){if(value&&Object.prototype.toString.call(value)!=='[object Array]'){this.throwError('The string list value should be an array.')}}
StringListAutocomplete.prototype.getPopupContent=function(){return'<form>                                                                                  \
                <div class="modal-header">                                                              \
                    <button type="button" class="close" data-dismiss="popup">&times;</button>           \
                    <h4 class="modal-title">{{property}}</h4>                                           \
                </div>                                                                                  \
                <div class="modal-body">                                                                \
                    <div class="control-toolbar">                                                       \
                        <div class="toolbar-item">                                                      \
                            <div class="btn-group">                                                     \
                                <button type="button" class="btn btn-primary                            \
                                    wn-icon-plus"                                                       \
                                    data-cmd="create-item">Add</button>                                 \
                                <button type="button" class="btn btn-default                            \
                                    empty wn-icon-trash-o"                                              \
                                    data-cmd="delete-item"></button>                                    \
                            </div>                                                                      \
                        </div>                                                                          \
                    </div>                                                                              \
                    <div class="form-group">                                                            \
                        <div class="inspector-dictionary-container">                                    \
                            <div class="values">                                                        \
                                <div class="control-scrollpad"                                          \
                                    data-control="scrollpad">                                           \
                                    <div class="scroll-wrapper">                                        \
                                        <table class="                                                  \
                                            no-offset-bottom                                            \
                                            inspector-dictionary-table">                                \
                                        </table>                                                        \
                                    </div>                                                              \
                                </div>                                                                  \
                            </div>                                                                      \
                        </div>                                                                          \
                    </div>                                                                              \
                </div>                                                                                  \
                <div class="modal-footer">                                                              \
                    <button type="submit" class="btn btn-primary">OK</button>                           \
                    <button type="button" class="btn btn-default" data-dismiss="popup">Cancel</button>   \
                </div>                                                                                  \
                </form>'
}
StringListAutocomplete.prototype.configurePopup=function(popup){this.initAutocomplete()
this.buildItemsTable(popup.get(0))
this.focusFirstInput()}
StringListAutocomplete.prototype.handleSubmit=function($form){return this.applyValues()}
StringListAutocomplete.prototype.buildItemsTable=function(popup){var table=popup.querySelector('table.inspector-dictionary-table'),tbody=document.createElement('tbody'),items=this.inspector.getPropertyValue(this.propertyDefinition.property)
if(items===undefined){items=this.propertyDefinition.default}if(items===undefined||this.getValueKeys(items).length===0){var row=this.buildEmptyRow()
tbody.appendChild(row)}else{for(var key in items){var row=this.buildTableRow(items[key])
tbody.appendChild(row)}}table.appendChild(tbody)
this.updateScrollpads()}
StringListAutocomplete.prototype.buildTableRow=function(value){var row=document.createElement('tr'),valueCell=document.createElement('td')
this.createInput(valueCell,value)
row.appendChild(valueCell)
return row}
StringListAutocomplete.prototype.buildEmptyRow=function(){return this.buildTableRow(null)}
StringListAutocomplete.prototype.createInput=function(container,value){var input=document.createElement('input'),controlContainer=document.createElement('div')
input.setAttribute('type','text')
input.setAttribute('class','form-control')
input.value=value
controlContainer.appendChild(input)
container.appendChild(controlContainer)}
StringListAutocomplete.prototype.setActiveCell=function(input){var activeCells=this.popup.querySelectorAll('td.active')
for(var i=activeCells.length-1;i>=0;i--){$.wn.foundation.element.removeClass(activeCells[i],'active')}var activeCell=input.parentNode.parentNode
$.wn.foundation.element.addClass(activeCell,'active')
this.buildAutoComplete(input)}
StringListAutocomplete.prototype.createItem=function(){var activeRow=this.getActiveRow(),newRow=this.buildEmptyRow(),tbody=this.getTableBody(),nextSibling=activeRow?activeRow.nextElementSibling:null
tbody.insertBefore(newRow,nextSibling)
this.focusAndMakeActive(newRow.querySelector('input'))
this.updateScrollpads()}
StringListAutocomplete.prototype.deleteItem=function(){var activeRow=this.getActiveRow(),tbody=this.getTableBody()
if(!activeRow){return}var nextRow=activeRow.nextElementSibling,prevRow=activeRow.previousElementSibling,input=this.getRowInputByIndex(activeRow,0)
if(input){this.removeAutocomplete(input)}tbody.removeChild(activeRow)
var newSelectedRow=nextRow?nextRow:prevRow
if(!newSelectedRow){newSelectedRow=this.buildEmptyRow()
tbody.appendChild(newSelectedRow)}this.focusAndMakeActive(newSelectedRow.querySelector('input'))
this.updateScrollpads()}
StringListAutocomplete.prototype.applyValues=function(){var tbody=this.getTableBody(),dataRows=tbody.querySelectorAll('tr'),link=this.getLink(),result=[]
for(var i=0,len=dataRows.length;i<len;i++){var dataRow=dataRows[i],valueInput=this.getRowInputByIndex(dataRow,0),value=$.trim(valueInput.value)
if(value.length==0){continue}result.push(value)}this.inspector.setPropertyValue(this.propertyDefinition.property,result)
this.setLinkText(link,result)}
StringListAutocomplete.prototype.getValueKeys=function(value){var result=[]
for(var key in value){result.push(key)}return result}
StringListAutocomplete.prototype.getActiveRow=function(){var activeCell=this.popup.querySelector('td.active')
if(!activeCell){return null}return activeCell.parentNode}
StringListAutocomplete.prototype.getTableBody=function(){return this.popup.querySelector('table.inspector-dictionary-table tbody')}
StringListAutocomplete.prototype.updateScrollpads=function(){$('.control-scrollpad',this.popup).scrollpad('update')}
StringListAutocomplete.prototype.focusFirstInput=function(){var input=this.popup.querySelector('td input')
if(input){input.focus()
this.setActiveCell(input)}}
StringListAutocomplete.prototype.getEditorCell=function(cell){return cell.parentNode.parentNode}
StringListAutocomplete.prototype.getEditorRow=function(cell){return cell.parentNode.parentNode.parentNode}
StringListAutocomplete.prototype.focusAndMakeActive=function(input){input.focus()
this.setActiveCell(input)}
StringListAutocomplete.prototype.getRowInputByIndex=function(row,index){return row.cells[index].querySelector('input')}
StringListAutocomplete.prototype.navigateDown=function(ev){var cell=this.getEditorCell(ev.currentTarget),row=this.getEditorRow(ev.currentTarget),nextRow=row.nextElementSibling
if(!nextRow){return}var newActiveEditor=nextRow.cells[cell.cellIndex].querySelector('input')
this.focusAndMakeActive(newActiveEditor)}
StringListAutocomplete.prototype.navigateUp=function(ev){var cell=this.getEditorCell(ev.currentTarget),row=this.getEditorRow(ev.currentTarget),prevRow=row.previousElementSibling
if(!prevRow){return}var newActiveEditor=prevRow.cells[cell.cellIndex].querySelector('input')
this.focusAndMakeActive(newActiveEditor)}
StringListAutocomplete.prototype.initAutocomplete=function(){if(this.propertyDefinition.items!==undefined){this.items=this.prepareItems(this.propertyDefinition.items)
this.initializeAutocompleteForCurrentInput()}else{this.loadDynamicItems()}}
StringListAutocomplete.prototype.initializeAutocompleteForCurrentInput=function(){var activeElement=document.activeElement
if(!activeElement){return}var inputs=this.popup.querySelectorAll('td input.form-control')
if(!inputs){return}for(var i=inputs.length-1;i>=0;i--){if(inputs[i]===activeElement){this.buildAutoComplete(inputs[i])
return}}}
StringListAutocomplete.prototype.buildAutoComplete=function(input){if(this.items===null){return}$(input).autocomplete({source:this.items,matchWidth:true,menu:'<ul class="autocomplete dropdown-menu inspector-autocomplete"></ul>',bodyContainer:true})}
StringListAutocomplete.prototype.removeAutocomplete=function(input){var $input=$(input)
if(!$input.data('autocomplete')){return}$input.autocomplete('destroy')}
StringListAutocomplete.prototype.prepareItems=function(items){var result={}
if($.isArray(items)){for(var i=0,len=items.length;i<len;i++){result[items[i]]=items[i]}}else{result=items}return result}
StringListAutocomplete.prototype.loadDynamicItems=function(){if(this.isDisposed()){return}var data=this.getRootSurface().getValues(),$form=$(this.popup).find('form')
if(this.triggerGetItems(data)===false){return}data['inspectorProperty']=this.getPropertyPath()
data['inspectorClassName']=this.inspector.options.inspectorClass
$form.request('onInspectableGetOptions',{data:data,}).done(this.proxy(this.itemsRequestDone))}
StringListAutocomplete.prototype.triggerGetItems=function(values){var $inspectable=this.getInspectableElement()
if(!$inspectable){return true}var itemsEvent=$.Event('autocompleteitems.oc.inspector')
$inspectable.trigger(itemsEvent,[{values:values,callback:this.proxy(this.itemsRequestDone),property:this.inspector.getPropertyPath(this.propertyDefinition.property),propertyDefinition:this.propertyDefinition}])
if(itemsEvent.isDefaultPrevented()){return false}return true}
StringListAutocomplete.prototype.itemsRequestDone=function(data){if(this.isDisposed()){return}var loadedItems={}
if(data.options){for(var i=data.options.length-1;i>=0;i--){loadedItems[data.options[i].value]=data.options[i].title}}this.items=this.prepareItems(loadedItems)
this.initializeAutocompleteForCurrentInput()}
StringListAutocomplete.prototype.removeAutocompleteFromAllRows=function(){var inputs=this.popup.querySelector('td input.form-control')
for(var i=inputs.length-1;i>=0;i--){this.removeAutocomplete(inputs[i])}}
StringListAutocomplete.prototype.onPopupShown=function(ev,link,popup){BaseProto.onPopupShown.call(this,ev,link,popup)
popup.on('focus.inspector','td input',this.proxy(this.onFocus))
popup.on('blur.inspector','td input',this.proxy(this.onBlur))
popup.on('keydown.inspector','td input',this.proxy(this.onKeyDown))
popup.on('click.inspector','[data-cmd]',this.proxy(this.onCommand))}
StringListAutocomplete.prototype.onPopupHidden=function(ev,link,popup){popup.off('.inspector','td input')
popup.off('.inspector','[data-cmd]',this.proxy(this.onCommand))
this.removeAutocompleteFromAllRows()
this.items=null
BaseProto.onPopupHidden.call(this,ev,link,popup)}
StringListAutocomplete.prototype.onFocus=function(ev){this.setActiveCell(ev.currentTarget)}
StringListAutocomplete.prototype.onBlur=function(ev){if($(ev.relatedTarget).closest('ul.inspector-autocomplete').length>0){return}this.removeAutocomplete(ev.currentTarget)}
StringListAutocomplete.prototype.onCommand=function(ev){var command=ev.currentTarget.getAttribute('data-cmd')
switch(command){case'create-item':this.createItem()
break;case'delete-item':this.deleteItem()
break;}}
StringListAutocomplete.prototype.onKeyDown=function(ev){if(ev.key==='ArrowDown'){return this.navigateDown(ev)}else if(ev.key==='ArrowUp'){return this.navigateUp(ev)}}
$.wn.inspector.propertyEditors.stringListAutocomplete=StringListAutocomplete}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.popupBase,BaseProto=Base.prototype
var DictionaryEditor=function(inspector,propertyDefinition,containerCell,group){this.keyValidationSet=null
this.valueValidationSet=null
Base.call(this,inspector,propertyDefinition,containerCell,group)}
DictionaryEditor.prototype=Object.create(BaseProto)
DictionaryEditor.prototype.constructor=Base
DictionaryEditor.prototype.dispose=function(){this.disposeValidators()
this.keyValidationSet=null
this.valueValidationSet=null
BaseProto.dispose.call(this)}
DictionaryEditor.prototype.init=function(){this.initValidators()
BaseProto.init.call(this)}
DictionaryEditor.prototype.supportsExternalParameterEditor=function(){return false}
DictionaryEditor.prototype.setLinkText=function(link,value){var value=value!==undefined?value:this.inspector.getPropertyValue(this.propertyDefinition.property)
if(value===undefined){value=this.propertyDefinition.default}if(value===undefined||$.isEmptyObject(value)){var placeholder=this.propertyDefinition.placeholder
if(placeholder!==undefined){$.wn.foundation.element.addClass(link,'placeholder')
link.textContent=placeholder}else{link.textContent='Items: 0'}}else{if(typeof value!=='object'){this.throwError('Object list value should be an object.')}var itemCount=this.getValueKeys(value).length
$.wn.foundation.element.removeClass(link,'placeholder')
link.textContent='Items: '+itemCount}}
DictionaryEditor.prototype.getPopupContent=function(){return'<form>                                                                                  \
                <div class="modal-header">                                                              \
                    <button type="button" class="close" data-dismiss="popup">&times;</button>           \
                    <h4 class="modal-title">{{property}}</h4>                                           \
                </div>                                                                                  \
                <div class="modal-body">                                                                \
                    <div class="control-toolbar">                                                       \
                        <div class="toolbar-item">                                                      \
                            <div class="btn-group">                                                     \
                                <button type="button" class="btn btn-primary                            \
                                    wn-icon-plus"                                                       \
                                    data-cmd="create-item">Add</button>                                 \
                                <button type="button" class="btn btn-default                            \
                                    empty wn-icon-trash-o"                                              \
                                    data-cmd="delete-item"></button>                                    \
                            </div>                                                                      \
                        </div>                                                                          \
                    </div>                                                                              \
                    <div class="form-group">                                                            \
                        <div class="inspector-dictionary-container">                                    \
                            <table class="headers">                                                     \
                                <thead>                                                                 \
                                    <tr>                                                                \
                                        <td>Key</td>                                                    \
                                        <td>Value</td>                                                  \
                                    </tr>                                                               \
                                </thead>                                                                \
                            </table>                                                                    \
                            <div class="values">                                                        \
                                <div class="control-scrollpad"                                          \
                                    data-control="scrollpad">                                           \
                                    <div class="scroll-wrapper">                                        \
                                        <table class="                                                  \
                                            no-offset-bottom                                            \
                                            inspector-dictionary-table">                                \
                                        </table>                                                        \
                                    </div>                                                              \
                                </div>                                                                  \
                            </div>                                                                      \
                        </div>                                                                          \
                    </div>                                                                              \
                </div>                                                                                  \
                <div class="modal-footer">                                                              \
                    <button type="submit" class="btn btn-primary">OK</button>                           \
                    <button type="button" class="btn btn-default" data-dismiss="popup">Cancel</button>  \
                </div>                                                                                  \
                </form>'
}
DictionaryEditor.prototype.configurePopup=function(popup){this.buildItemsTable(popup.get(0))
this.focusFirstInput()}
DictionaryEditor.prototype.handleSubmit=function($form){return this.applyValues()}
DictionaryEditor.prototype.buildItemsTable=function(popup){var table=popup.querySelector('table.inspector-dictionary-table'),tbody=document.createElement('tbody'),items=this.inspector.getPropertyValue(this.propertyDefinition.property),titleProperty=this.propertyDefinition.titleProperty
if(items===undefined){items=this.propertyDefinition.default}if(items===undefined||this.getValueKeys(items).length===0){var row=this.buildEmptyRow()
tbody.appendChild(row)}else{for(var key in items){var row=this.buildTableRow(key,items[key])
tbody.appendChild(row)}}table.appendChild(tbody)
this.updateScrollpads()}
DictionaryEditor.prototype.buildTableRow=function(key,value){var row=document.createElement('tr'),keyCell=document.createElement('td'),valueCell=document.createElement('td')
this.createInput(keyCell,key)
this.createInput(valueCell,value)
row.appendChild(keyCell)
row.appendChild(valueCell)
return row}
DictionaryEditor.prototype.buildEmptyRow=function(){return this.buildTableRow(null,null)}
DictionaryEditor.prototype.createInput=function(container,value){var input=document.createElement('input'),controlContainer=document.createElement('div')
input.setAttribute('type','text')
input.setAttribute('class','form-control')
input.value=value
controlContainer.appendChild(input)
container.appendChild(controlContainer)}
DictionaryEditor.prototype.setActiveCell=function(input){var activeCells=this.popup.querySelectorAll('td.active')
for(var i=activeCells.length-1;i>=0;i--){$.wn.foundation.element.removeClass(activeCells[i],'active')}var activeCell=input.parentNode.parentNode
$.wn.foundation.element.addClass(activeCell,'active')}
DictionaryEditor.prototype.createItem=function(){var activeRow=this.getActiveRow(),newRow=this.buildEmptyRow(),tbody=this.getTableBody(),nextSibling=activeRow?activeRow.nextElementSibling:null
tbody.insertBefore(newRow,nextSibling)
this.focusAndMakeActive(newRow.querySelector('input'))
this.updateScrollpads()}
DictionaryEditor.prototype.deleteItem=function(){var activeRow=this.getActiveRow(),tbody=this.getTableBody()
if(!activeRow){return}var nextRow=activeRow.nextElementSibling,prevRow=activeRow.previousElementSibling
tbody.removeChild(activeRow)
var newSelectedRow=nextRow?nextRow:prevRow
if(!newSelectedRow){newSelectedRow=this.buildEmptyRow()
tbody.appendChild(newSelectedRow)}this.focusAndMakeActive(newSelectedRow.querySelector('input'))
this.updateScrollpads()}
DictionaryEditor.prototype.applyValues=function(){var tbody=this.getTableBody(),dataRows=tbody.querySelectorAll('tr'),link=this.getLink(),result={}
for(var i=0,len=dataRows.length;i<len;i++){var dataRow=dataRows[i],keyInput=this.getRowInputByIndex(dataRow,0),valueInput=this.getRowInputByIndex(dataRow,1),key=$.trim(keyInput.value),value=$.trim(valueInput.value)
if(key.length==0&&value.length==0){continue}if(key.length==0){$.wn.flashMsg({text:'The key cannot be empty.','class':'error','interval':3})
this.focusAndMakeActive(keyInput)
return false}if(value.length==0){$.wn.flashMsg({text:'The value cannot be empty.','class':'error','interval':3})
this.focusAndMakeActive(valueInput)
return false}if(result[key]!==undefined){$.wn.flashMsg({text:'Keys should be unique.','class':'error','interval':3})
this.focusAndMakeActive(keyInput)
return false}var validationResult=this.keyValidationSet.validate(key)
if(validationResult!==null){$.wn.flashMsg({text:validationResult,'class':'error','interval':5})
return false}validationResult=this.valueValidationSet.validate(value)
if(validationResult!==null){$.wn.flashMsg({text:validationResult,'class':'error','interval':5})
return false}result[key]=value}this.inspector.setPropertyValue(this.propertyDefinition.property,result)
this.setLinkText(link,result)}
DictionaryEditor.prototype.getValueKeys=function(value){var result=[]
for(var key in value){result.push(key)}return result}
DictionaryEditor.prototype.getActiveRow=function(){var activeCell=this.popup.querySelector('td.active')
if(!activeCell){return null}return activeCell.parentNode}
DictionaryEditor.prototype.getTableBody=function(){return this.popup.querySelector('table.inspector-dictionary-table tbody')}
DictionaryEditor.prototype.updateScrollpads=function(){$('.control-scrollpad',this.popup).scrollpad('update')}
DictionaryEditor.prototype.focusFirstInput=function(){var input=this.popup.querySelector('td input')
if(input){input.focus()
this.setActiveCell(input)}}
DictionaryEditor.prototype.getEditorCell=function(cell){return cell.parentNode.parentNode}
DictionaryEditor.prototype.getEditorRow=function(cell){return cell.parentNode.parentNode.parentNode}
DictionaryEditor.prototype.focusAndMakeActive=function(input){input.focus()
this.setActiveCell(input)}
DictionaryEditor.prototype.getRowInputByIndex=function(row,index){return row.cells[index].querySelector('input')}
DictionaryEditor.prototype.navigateDown=function(ev){var cell=this.getEditorCell(ev.currentTarget),row=this.getEditorRow(ev.currentTarget),nextRow=row.nextElementSibling
if(!nextRow){return}var newActiveEditor=nextRow.cells[cell.cellIndex].querySelector('input')
this.focusAndMakeActive(newActiveEditor)}
DictionaryEditor.prototype.navigateUp=function(ev){var cell=this.getEditorCell(ev.currentTarget),row=this.getEditorRow(ev.currentTarget),prevRow=row.previousElementSibling
if(!prevRow){return}var newActiveEditor=prevRow.cells[cell.cellIndex].querySelector('input')
this.focusAndMakeActive(newActiveEditor)}
DictionaryEditor.prototype.initValidators=function(){this.keyValidationSet=new $.wn.inspector.validationSet({validation:this.propertyDefinition.validationKey},this.propertyDefinition.property+'.validationKey')
this.valueValidationSet=new $.wn.inspector.validationSet({validation:this.propertyDefinition.validationValue},this.propertyDefinition.property+'.validationValue')}
DictionaryEditor.prototype.disposeValidators=function(){this.keyValidationSet.dispose()
this.valueValidationSet.dispose()}
DictionaryEditor.prototype.onPopupShown=function(ev,link,popup){BaseProto.onPopupShown.call(this,ev,link,popup)
popup.on('focus.inspector','td input',this.proxy(this.onFocus))
popup.on('keydown.inspector','td input',this.proxy(this.onKeyDown))
popup.on('click.inspector','[data-cmd]',this.proxy(this.onCommand))}
DictionaryEditor.prototype.onPopupHidden=function(ev,link,popup){popup.off('.inspector','td input')
popup.off('.inspector','[data-cmd]',this.proxy(this.onCommand))
BaseProto.onPopupHidden.call(this,ev,link,popup)}
DictionaryEditor.prototype.onFocus=function(ev){this.setActiveCell(ev.currentTarget)}
DictionaryEditor.prototype.onCommand=function(ev){var command=ev.currentTarget.getAttribute('data-cmd')
switch(command){case'create-item':this.createItem()
break;case'delete-item':this.deleteItem()
break;}}
DictionaryEditor.prototype.onKeyDown=function(ev){if(ev.key==='ArrowDown'){return this.navigateDown(ev)}else if(ev.key==='ArrowUp'){return this.navigateUp(ev)}}
$.wn.inspector.propertyEditors.dictionary=DictionaryEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.propertyEditors.string,BaseProto=Base.prototype
var AutocompleteEditor=function(inspector,propertyDefinition,containerCell,group){this.autoUpdateTimeout=null
Base.call(this,inspector,propertyDefinition,containerCell,group)}
AutocompleteEditor.prototype=Object.create(BaseProto)
AutocompleteEditor.prototype.constructor=Base
AutocompleteEditor.prototype.dispose=function(){this.clearAutoUpdateTimeout()
this.removeAutocomplete()
BaseProto.dispose.call(this)}
AutocompleteEditor.prototype.build=function(){var container=document.createElement('div'),editor=document.createElement('input'),placeholder=this.propertyDefinition.placeholder!==undefined?this.propertyDefinition.placeholder:'',value=this.inspector.getPropertyValue(this.propertyDefinition.property)
editor.setAttribute('type','text')
editor.setAttribute('class','string-editor')
editor.setAttribute('placeholder',placeholder)
container.setAttribute('class','autocomplete-container')
if(value===undefined){value=this.propertyDefinition.default}if(value===undefined){value=''}editor.value=value
$.wn.foundation.element.addClass(this.containerCell,'text autocomplete')
container.appendChild(editor)
this.containerCell.appendChild(container)
if(this.propertyDefinition.items!==undefined){this.buildAutoComplete(this.propertyDefinition.items)}else{this.loadDynamicItems()}}
AutocompleteEditor.prototype.buildAutoComplete=function(items){var input=this.getInput()
if(items===undefined){items=[]}var $input=$(input),autocomplete=$input.data('autocomplete')
if(!autocomplete){$input.autocomplete({source:this.prepareItems(items),matchWidth:true})}else{autocomplete.source=this.prepareItems(items)}}
AutocompleteEditor.prototype.removeAutocomplete=function(){var input=this.getInput()
$(input).autocomplete('destroy')}
AutocompleteEditor.prototype.prepareItems=function(items){var result={}
if($.isArray(items)){for(var i=0,len=items.length;i<len;i++){result[items[i]]=items[i]}}else{result=items}return result}
AutocompleteEditor.prototype.supportsExternalParameterEditor=function(){return false}
AutocompleteEditor.prototype.getContainer=function(){return this.getInput().parentNode}
AutocompleteEditor.prototype.registerHandlers=function(){BaseProto.registerHandlers.call(this)
$(this.getInput()).on('change',this.proxy(this.onInputKeyUp))}
AutocompleteEditor.prototype.unregisterHandlers=function(){BaseProto.unregisterHandlers.call(this)
$(this.getInput()).off('change',this.proxy(this.onInputKeyUp))}
AutocompleteEditor.prototype.saveDependencyValues=function(){this.prevDependencyValues=this.getDependencyValues()}
AutocompleteEditor.prototype.getDependencyValues=function(){var result=''
for(var i=0,len=this.propertyDefinition.depends.length;i<len;i++){var property=this.propertyDefinition.depends[i],value=this.inspector.getPropertyValue(property)
if(value===undefined){value='';}result+=property+':'+value+'-'}return result}
AutocompleteEditor.prototype.onInspectorPropertyChanged=function(property){if(!this.propertyDefinition.depends||this.propertyDefinition.depends.indexOf(property)===-1){return}this.clearAutoUpdateTimeout()
if(this.prevDependencyValues===undefined||this.prevDependencyValues!=dependencyValues){this.autoUpdateTimeout=setTimeout(this.proxy(this.loadDynamicItems),200)}}
AutocompleteEditor.prototype.clearAutoUpdateTimeout=function(){if(this.autoUpdateTimeout!==null){clearTimeout(this.autoUpdateTimeout)
this.autoUpdateTimeout=null}}
AutocompleteEditor.prototype.showLoadingIndicator=function(){$(this.getContainer()).loadIndicator()}
AutocompleteEditor.prototype.hideLoadingIndicator=function(){if(this.isDisposed()){return}var $container=$(this.getContainer())
$container.loadIndicator('hide')
$container.loadIndicator('destroy')
$container.removeClass('loading-indicator-container')}
AutocompleteEditor.prototype.loadDynamicItems=function(){if(this.isDisposed()){return}this.clearAutoUpdateTimeout()
var container=this.getContainer(),data=this.getRootSurface().getValues(),$form=$(container).closest('form')
$.wn.foundation.element.addClass(container,'loading-indicator-container size-small')
this.showLoadingIndicator()
if(this.triggerGetItems(data)===false){return}data['inspectorProperty']=this.getPropertyPath()
data['inspectorClassName']=this.inspector.options.inspectorClass
$form.request('onInspectableGetOptions',{data:data,}).done(this.proxy(this.itemsRequestDone)).always(this.proxy(this.hideLoadingIndicator))}
AutocompleteEditor.prototype.triggerGetItems=function(values){var $inspectable=this.getInspectableElement()
if(!$inspectable){return true}var itemsEvent=$.Event('autocompleteitems.oc.inspector')
$inspectable.trigger(itemsEvent,[{values:values,callback:this.proxy(this.itemsRequestDone),property:this.inspector.getPropertyPath(this.propertyDefinition.property),propertyDefinition:this.propertyDefinition}])
if(itemsEvent.isDefaultPrevented()){return false}return true}
AutocompleteEditor.prototype.itemsRequestDone=function(data){if(this.isDisposed()){return}this.hideLoadingIndicator()
var loadedItems={}
if(data.options){for(var i=data.options.length-1;i>=0;i--){loadedItems[data.options[i].value]=data.options[i].title}}this.buildAutoComplete(loadedItems)}
$.wn.inspector.propertyEditors.autocomplete=AutocompleteEditor}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.inspector===undefined)$.wn.inspector={}
$.wn.inspector.helpers={}
$.wn.inspector.helpers.generateElementUniqueId=function(element){if(element.hasAttribute('data-inspector-id')){return element.getAttribute('data-inspector-id')}var id=$.wn.inspector.helpers.generateUniqueId()
element.setAttribute('data-inspector-id',id)
return id}
$.wn.inspector.helpers.generateUniqueId=function(){return"inspectorid-"+Math.floor(Math.random()*new Date().getTime());}}(window.jQuery)+function($){"use strict";if($.wn.inspector.validators===undefined)$.wn.inspector.validators={}
var Base=$.wn.foundation.base,BaseProto=Base.prototype
var ValidationSet=function(options,propertyName){this.validators=[]
this.options=options
this.propertyName=propertyName
Base.call(this)
this.createValidators()}
ValidationSet.prototype=Object.create(BaseProto)
ValidationSet.prototype.constructor=Base
ValidationSet.prototype.dispose=function(){this.disposeValidators()
this.validators=null
BaseProto.dispose.call(this)}
ValidationSet.prototype.disposeValidators=function(){for(var i=0,len=this.validators.length;i<len;i++){this.validators[i].dispose()}}
ValidationSet.prototype.throwError=function(errorMessage){throw new Error(errorMessage+' Property: '+this.propertyName)}
ValidationSet.prototype.createValidators=function(){if((this.options.required!==undefined||this.options.validationPattern!==undefined||this.options.validationMessage!==undefined)&&this.options.validation!==undefined){this.throwError('Legacy and new validation syntax should not be mixed.')}if(this.options.required!==undefined&&this.options.required){var validator=new $.wn.inspector.validators.required({message:this.options.validationMessage})
this.validators.push(validator)}if(this.options.validationPattern!==undefined){var validator=new $.wn.inspector.validators.regex({message:this.options.validationMessage,pattern:this.options.validationPattern})
this.validators.push(validator)}if(this.options.validation===undefined){return}for(var validatorName in this.options.validation){if($.wn.inspector.validators[validatorName]==undefined){this.throwError('Inspector validator "'+validatorName+'" is not found in the $.wn.inspector.validators namespace.')}var validator=new $.wn.inspector.validators[validatorName](this.options.validation[validatorName])
this.validators.push(validator)}}
ValidationSet.prototype.validate=function(value){try{for(var i=0,len=this.validators.length;i<len;i++){var validator=this.validators[i],errorMessage=validator.isValid(value)
if(typeof errorMessage==='string'){return errorMessage}}return null}catch(err){this.throwError(err)}}
$.wn.inspector.validationSet=ValidationSet}(window.jQuery);+function($){"use strict";if($.wn.inspector.validators===undefined)$.wn.inspector.validators={}
var Base=$.wn.foundation.base,BaseProto=Base.prototype
var BaseValidator=function(options){this.options=options
this.defaultMessage='Invalid property value.'
Base.call(this)}
BaseValidator.prototype=Object.create(BaseProto)
BaseValidator.prototype.constructor=Base
BaseValidator.prototype.dispose=function(){this.defaultMessage=null
BaseProto.dispose.call(this)}
BaseValidator.prototype.getMessage=function(defaultMessage){if(this.options.message!==undefined){return this.options.message}if(defaultMessage!==undefined){return defaultMessage}return this.defaultMessage}
BaseValidator.prototype.isScalar=function(value){if(value===undefined||value===null){return true}return!!(typeof value==='string'||typeof value=='number'||typeof value=='boolean');}
BaseValidator.prototype.isValid=function(value){return null}
$.wn.inspector.validators.base=BaseValidator}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.validators.base,BaseProto=Base.prototype
var BaseNumber=function(options){Base.call(this,options)}
BaseNumber.prototype=Object.create(BaseProto)
BaseNumber.prototype.constructor=Base
BaseNumber.prototype.doCommonChecks=function(value){if(this.options.min!==undefined||this.options.max!==undefined){if(this.options.min!==undefined){if(this.options.min.value===undefined){throw new Error('The min.value parameter is not defined in the Inspector validator configuration')}if(value<this.options.min.value){return this.options.min.message!==undefined?this.options.min.message:"The value should not be less than "+this.options.min.value}}if(this.options.max!==undefined){if(this.options.max.value===undefined){throw new Error('The max.value parameter is not defined in the table Inspector validator configuration')}if(value>this.options.max.value){return this.options.max.message!==undefined?this.options.max.message:"The value should not be greater than "+this.options.max.value}}}}
$.wn.inspector.validators.baseNumber=BaseNumber}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.validators.base,BaseProto=Base.prototype
var RequiredValidator=function(options){Base.call(this,options)
this.defaultMessage='The property is required.'}
RequiredValidator.prototype=Object.create(BaseProto)
RequiredValidator.prototype.constructor=Base
RequiredValidator.prototype.isValid=function(value){if(value===undefined||value===null){return this.getMessage()}if(typeof value==='boolean'){return value?null:this.getMessage()}if(typeof value==='object'){return!$.isEmptyObject(value)?null:this.getMessage()}return $.trim(String(value)).length>0?null:this.getMessage()}
$.wn.inspector.validators.required=RequiredValidator}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.validators.base,BaseProto=Base.prototype
var RegexValidator=function(options){Base.call(this,options)}
RegexValidator.prototype=Object.create(BaseProto)
RegexValidator.prototype.constructor=Base
RegexValidator.prototype.isValid=function(value){if(this.options.pattern===undefined){this.throwError('The pattern parameter is not defined in the Regex Inspector validator configuration.')}if(!this.isScalar(value)){this.throwError('The Regex Inspector validator can only be used with string values.')}if(value===undefined||value===null){return null}var string=$.trim(String(value))
if(string.length===0){return null}var regexObj=new RegExp(this.options.pattern,this.options.modifiers)
return regexObj.test(string)?null:this.getMessage()}
$.wn.inspector.validators.regex=RegexValidator}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.validators.baseNumber,BaseProto=Base.prototype
var IntegerValidator=function(options){Base.call(this,options)}
IntegerValidator.prototype=Object.create(BaseProto)
IntegerValidator.prototype.constructor=Base
IntegerValidator.prototype.isValid=function(value){if(!this.isScalar(value)||typeof value=='boolean'){this.throwError('The Integer Inspector validator can only be used with string values.')}if(value===undefined||value===null){return null}var string=$.trim(String(value))
if(string.length===0){return null}var testResult=this.options.allowNegative?/^\-?[0-9]*$/.test(string):/^[0-9]*$/.test(string)
if(!testResult){var defaultMessage=this.options.allowNegative?'The value should be an integer.':'The value should be a positive integer.';return this.getMessage(defaultMessage)}return this.doCommonChecks(parseInt(string))}
$.wn.inspector.validators.integer=IntegerValidator}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.validators.baseNumber,BaseProto=Base.prototype
var FloatValidator=function(options){Base.call(this,options)}
FloatValidator.prototype=Object.create(BaseProto)
FloatValidator.prototype.constructor=Base
FloatValidator.prototype.isValid=function(value){if(!this.isScalar(value)||typeof value=='boolean'){this.throwError('The Float Inspector validator can only be used with string values.')}if(value===undefined||value===null){return null}var string=$.trim(String(value))
if(string.length===0){return null}var testResult=this.options.allowNegative?/^[-]?([0-9]+\.[0-9]+|[0-9]+)$/.test(string):/^([0-9]+\.[0-9]+|[0-9]+)$/.test(string)
if(!testResult){var defaultMessage=this.options.allowNegative?'The value should be a floating point number.':'The value should be a positive floating point number.';return this.getMessage(defaultMessage)}return this.doCommonChecks(parseFloat(string))}
$.wn.inspector.validators.float=FloatValidator}(window.jQuery);+function($){"use strict";var Base=$.wn.inspector.validators.base,BaseProto=Base.prototype
var LengthValidator=function(options){Base.call(this,options)}
LengthValidator.prototype=Object.create(BaseProto)
LengthValidator.prototype.constructor=Base
LengthValidator.prototype.isValid=function(value){if(value===undefined||value===null){return null}if(typeof value=='boolean'){this.throwError('The Length Inspector validator cannot work with Boolean values.')}var length=null
if(Object.prototype.toString.call(value)==='[object Array]'||typeof value==='string'){length=value.length}else if(typeof value==='object'){length=this.getObjectLength(value)}if(this.options.min!==undefined||this.options.max!==undefined){if(this.options.min!==undefined){if(this.options.min.value===undefined){throw new Error('The min.value parameter is not defined in the Length Inspector validator configuration.')}if(length<this.options.min.value){return this.options.min.message!==undefined?this.options.min.message:"The value should not be shorter than "+this.options.min.value}}if(this.options.max!==undefined){if(this.options.max.value===undefined)throw new Error('The max.value parameter is not defined in the Length Inspector validator configuration.')
if(length>this.options.max.value){return this.options.max.message!==undefined?this.options.max.message:"The value should not be longer than "+this.options.max.value}}}}
LengthValidator.prototype.getObjectLength=function(value){var result=0
for(var key in value){result++}return result}
$.wn.inspector.validators.length=LengthValidator}(window.jQuery);+function($){"use strict";if($.wn===undefined)$.wn={}
if($.oc===undefined)$.oc=$.wn
if($.wn.inspector===undefined)$.wn.inspector={}
var Base=$.wn.foundation.base,BaseProto=Base.prototype
var ExternalParameterEditor=function(inspector,propertyDefinition,containerCell,initialValue){this.inspector=inspector
this.propertyDefinition=propertyDefinition
this.containerCell=containerCell
this.initialValue=initialValue
Base.call(this)
this.init()}
ExternalParameterEditor.prototype=Object.create(BaseProto)
ExternalParameterEditor.prototype.constructor=Base
ExternalParameterEditor.prototype.dispose=function(){this.disposeControls()
this.unregisterHandlers()
this.inspector=null
this.propertyDefinition=null
this.containerCell=null
this.initialValue=null
BaseProto.dispose.call(this)}
ExternalParameterEditor.prototype.init=function(){this.tooltipText='Click to enter the external parameter name to load the property value from'
this.build()
this.registerHandlers()
this.setInitialValue()}
ExternalParameterEditor.prototype.build=function(){var container=document.createElement('div'),editor=document.createElement('div'),controls=document.createElement('div'),input=document.createElement('input'),link=document.createElement('a'),icon=document.createElement('i')
container.setAttribute('class','external-param-editor-container')
editor.setAttribute('class','external-editor')
controls.setAttribute('class','controls')
input.setAttribute('type','text')
input.setAttribute('tabindex','-1')
link.setAttribute('href','#')
link.setAttribute('class','external-editor-link')
link.setAttribute('tabindex','-1')
link.setAttribute('title',this.tooltipText)
$(link).tooltip({'container':'body',delay:500})
icon.setAttribute('class','wn-icon-terminal')
link.appendChild(icon)
controls.appendChild(input)
controls.appendChild(link)
editor.appendChild(controls)
while(this.containerCell.firstChild){var child=this.containerCell.firstChild
container.appendChild(child)}container.appendChild(editor)
this.containerCell.appendChild(container)}
ExternalParameterEditor.prototype.setInitialValue=function(){if(!this.initialValue){return}if(typeof this.initialValue!=='string'){return}var matches=[]
if(matches=this.initialValue.match(/^\{\{([^\}]+)\}\}$/)){var value=$.trim(matches[1])
if(value.length>0){this.showEditor(true)
this.getInput().value=value
this.inspector.setPropertyValue(this.propertyDefinition.property,null,true,true)}}}
ExternalParameterEditor.prototype.showEditor=function(building){var editor=this.getEditor(),input=this.getInput(),container=this.getContainer(),link=this.getLink()
var position=$(editor).position()
if(!building){editor.style.right=0
editor.style.left=position.left+'px'}else{editor.style.right=0}setTimeout(this.proxy(this.repositionEditor),0)
$.wn.foundation.element.addClass(container,'editor-visible')
link.setAttribute('data-original-title','Click to enter the property value')
this.toggleEditorVisibility(false)
input.setAttribute('tabindex',0)
if(!building){input.focus()}}
ExternalParameterEditor.prototype.repositionEditor=function(){this.getEditor().style.left=0
this.containerCell.scrollTop=0}
ExternalParameterEditor.prototype.hideEditor=function(){var editor=this.getEditor(),container=this.getContainer()
editor.style.left='auto'
editor.style.right='30px'
$.wn.foundation.element.removeClass(container,'editor-visible')
$.wn.foundation.element.removeClass(this.containerCell,'active')
var propertyEditor=this.inspector.findPropertyEditor(this.propertyDefinition.property)
if(propertyEditor){propertyEditor.onExternalPropertyEditorHidden()}}
ExternalParameterEditor.prototype.toggleEditor=function(ev){$.wn.foundation.event.stop(ev)
var link=this.getLink(),container=this.getContainer(),editor=this.getEditor()
$(link).tooltip('hide')
if(!this.isEditorVisible()){this.showEditor()
return}var left=container.offsetWidth
editor.style.left=left+'px'
link.setAttribute('data-original-title',this.tooltipText)
this.getInput().setAttribute('tabindex','-1')
this.toggleEditorVisibility(true)
setTimeout(this.proxy(this.hideEditor),200)}
ExternalParameterEditor.prototype.toggleEditorVisibility=function(show){var container=this.getContainer(),children=container.children,height=0
if(!show){height=this.containerCell.getAttribute('data-inspector-cell-height')
if(!height){height=$(this.containerCell).height()
this.containerCell.setAttribute('data-inspector-cell-height',height)}}height=Math.max(height,19)
for(var i=0,len=children.length;i<len;i++){var element=children[i]
if($.wn.foundation.element.hasClass(element,'external-editor')){continue}if(show){$.wn.foundation.element.removeClass(element,'hide')}else{container.style.height=height+'px'
$.wn.foundation.element.addClass(element,'hide')}}}
ExternalParameterEditor.prototype.focus=function(){this.getInput().focus()}
ExternalParameterEditor.prototype.validate=function(silentMode){var value=$.trim(this.getValue())
if(value.length===0){if(!silentMode){$.wn.flashMsg({text:'Please enter the external parameter name.','class':'error','interval':5})
this.focus()}return false}return true}
ExternalParameterEditor.prototype.registerHandlers=function(){var input=this.getInput()
this.getLink().addEventListener('click',this.proxy(this.toggleEditor))
input.addEventListener('focus',this.proxy(this.onInputFocus))
input.addEventListener('change',this.proxy(this.onInputChange))}
ExternalParameterEditor.prototype.onInputFocus=function(){this.inspector.makeCellActive(this.containerCell)}
ExternalParameterEditor.prototype.onInputChange=function(){this.inspector.markPropertyChanged(this.propertyDefinition.property,true)}
ExternalParameterEditor.prototype.unregisterHandlers=function(){var input=this.getInput()
this.getLink().removeEventListener('click',this.proxy(this.toggleEditor))
input.removeEventListener('focus',this.proxy(this.onInputFocus))
input.removeEventListener('change',this.proxy(this.onInputChange))}
ExternalParameterEditor.prototype.disposeControls=function(){$(this.getLink()).tooltip('destroy')}
ExternalParameterEditor.prototype.getInput=function(){return this.containerCell.querySelector('div.external-editor input')}
ExternalParameterEditor.prototype.getValue=function(){return this.getInput().value}
ExternalParameterEditor.prototype.getLink=function(){return this.containerCell.querySelector('a.external-editor-link')}
ExternalParameterEditor.prototype.getContainer=function(){return this.containerCell.querySelector('div.external-param-editor-container')}
ExternalParameterEditor.prototype.getEditor=function(){return this.containerCell.querySelector('div.external-editor')}
ExternalParameterEditor.prototype.getPropertyName=function(){return this.propertyDefinition.property}
ExternalParameterEditor.prototype.isEditorVisible=function(){return $.wn.foundation.element.hasClass(this.getContainer(),'editor-visible')}
$.wn.inspector.externalParameterEditor=ExternalParameterEditor}(window.jQuery);+function($){"use strict";var Base=$.wn.foundation.base,BaseProto=Base.prototype,listSortableIdCounter=0,elementsIdCounter=0
var ListSortable=function(element,options){this.lists=[]
this.options=options
this.listSortableId=null
this.lastMousePosition=null
Base.call(this)
$.wn.foundation.controlUtils.markDisposable(element)
this.init()
this.addList(element)}
ListSortable.prototype=Object.create(BaseProto)
ListSortable.prototype.constructor=ListSortable
ListSortable.prototype.init=function(){listSortableIdCounter++
this.listSortableId='listsortable/id/'+listSortableIdCounter}
ListSortable.prototype.addList=function(list){this.lists.push(list)
this.registerListHandlers(list)
if(this.lists.length==1){$(list).one('dispose-control',this.proxy(this.dispose))}}
ListSortable.prototype.registerListHandlers=function(list){var $list=$(list)
$list.on('dragstart','> li',this.proxy(this.onDragStart))
$list.on('dragover','> li',this.proxy(this.onDragOver))
$list.on('dragenter','> li',this.proxy(this.onDragEnter))
$list.on('dragleave','> li',this.proxy(this.onDragLeave))
$list.on('drop','> li',this.proxy(this.onDragDrop))
$list.on('dragend','> li',this.proxy(this.onDragEnd))}
ListSortable.prototype.unregisterListHandlers=function(list){var $list=$(list)
$list.off('dragstart','> li',this.proxy(this.onDragStart))
$list.off('dragover','> li',this.proxy(this.onDragOver))
$list.off('dragenter','> li',this.proxy(this.onDragEnter))
$list.off('dragleave','> li',this.proxy(this.onDragLeave))
$list.off('drop','> li',this.proxy(this.onDragDrop))
$list.off('dragend','> li',this.proxy(this.onDragEnd))}
ListSortable.prototype.unregisterHandlers=function(){$(document).off('dragover',this.proxy(this.onDocumentDragOver))
$(document).off('mousemove',this.proxy(this.onDocumentMouseMove))
$(this.lists[0]).off('dispose-control',this.proxy(this.dispose))}
ListSortable.prototype.unbindLists=function(){for(var i=this.lists.length-1;i>0;i--){var list=this.lists[i]
this.unregisterListHandlers(this.lists[i])
$(list).removeData('oc.listSortable')}}
ListSortable.prototype.dispose=function(){this.unbindLists()
this.unregisterHandlers()
this.options=null
this.lists=[]
BaseProto.dispose.call(this)}
ListSortable.prototype.elementBelongsToManagedList=function(element){for(var i=this.lists.length-1;i>=0;i--){var list=this.lists[i],children=[].slice.call(list.children);if(children.indexOf(element)!==-1){return true}}return false}
ListSortable.prototype.isDragStartAllowed=function(element){return true}
ListSortable.prototype.elementIsPlaceholder=function(element){return element.getAttribute('class')==='list-sortable-placeholder'}
ListSortable.prototype.getElementSortableId=function(element){if(element.hasAttribute('data-list-sortable-element-id')){return element.getAttribute('data-list-sortable-element-id')}elementsIdCounter++
var elementId=elementsIdCounter
element.setAttribute('data-list-sortable-element-id',elementsIdCounter)
return elementsIdCounter}
ListSortable.prototype.dataTransferContains=function(ev,element){if(ev.dataTransfer.types.indexOf!==undefined){return ev.dataTransfer.types.indexOf(element)>=0}return ev.dataTransfer.types.contains(element)}
ListSortable.prototype.isSourceManagedList=function(ev){return this.dataTransferContains(ev,this.listSortableId)}
ListSortable.prototype.removePlaceholders=function(){for(var i=this.lists.length-1;i>=0;i--){var list=this.lists[i],placeholders=list.querySelectorAll('.list-sortable-placeholder')
for(var j=placeholders.length-1;j>=0;j--){list.removeChild(placeholders[j])}}}
ListSortable.prototype.createPlaceholder=function(element,ev){var placeholder=document.createElement('li'),placement=this.getPlaceholderPlacement(element,ev)
this.removePlaceholders()
placeholder.setAttribute('class','list-sortable-placeholder')
placeholder.setAttribute('draggable',true)
if(placement=='before'){element.parentNode.insertBefore(placeholder,element)}else{element.parentNode.insertBefore(placeholder,element.nextSibling)}}
ListSortable.prototype.moveElement=function(target,ev){var list=target.parentNode,placeholder=list.querySelector('.list-sortable-placeholder')
if(!placeholder){return}var elementId=ev.dataTransfer.getData('listsortable/elementid')
if(!elementId){return}var item=this.findDraggedItem(elementId)
if(!item){return}placeholder.parentNode.insertBefore(item,placeholder)
$(item).trigger('dragged.list.sortable')}
ListSortable.prototype.findDraggedItem=function(elementId){for(var i=this.lists.length-1;i>=0;i--){var list=this.lists[i],item=list.querySelector('[data-list-sortable-element-id="'+elementId+'"]')
if(item){return item}}return null}
ListSortable.prototype.getPlaceholderPlacement=function(hoverElement,ev){var mousePosition=$.wn.foundation.event.pageCoordinates(ev),elementPosition=$.wn.foundation.element.absolutePosition(hoverElement)
if(this.options.direction=='vertical'){var elementCenter=elementPosition.top+hoverElement.offsetHeight/2
return mousePosition.y<=elementCenter?'before':'after'}else{var elementCenter=elementPosition.left+hoverElement.offsetWidth/2
return mousePosition.x<=elementCenter?'before':'after'}}
ListSortable.prototype.lastMousePositionChanged=function(ev){var mousePosition=$.wn.foundation.event.pageCoordinates(ev.originalEvent)
if(this.lastMousePosition===null||this.lastMousePosition.x!=mousePosition.x||this.lastMousePosition.y!=mousePosition.y){this.lastMousePosition=mousePosition
return true}return false}
ListSortable.prototype.mouseOutsideLists=function(ev){var mousePosition=$.wn.foundation.event.pageCoordinates(ev)
for(var i=this.lists.length-1;i>=0;i--){if($.wn.foundation.element.elementContainsPoint(this.lists[i],mousePosition)){return false}}return true}
ListSortable.prototype.getClosestDraggableParent=function(element){var current=element
while(current){if(current.tagName==='LI'&&current.hasAttribute('draggable')){return current}current=current.parentNode}return null}
ListSortable.prototype.onDragStart=function(ev){if(!this.isDragStartAllowed(ev.target)){return}ev.originalEvent.dataTransfer.effectAllowed='move'
ev.originalEvent.dataTransfer.setData('listsortable/elementid',this.getElementSortableId(ev.target))
ev.originalEvent.dataTransfer.setData(this.listSortableId,this.listSortableId)
$(document).on('mousemove',this.proxy(this.onDocumentMouseMove))
$(document).on('dragover',this.proxy(this.onDocumentDragOver))}
ListSortable.prototype.onDragOver=function(ev){if(!this.isSourceManagedList(ev.originalEvent)){return}var draggable=this.getClosestDraggableParent(ev.target)
if(!draggable){return}if(!this.elementIsPlaceholder(draggable)&&this.lastMousePositionChanged(ev)){this.createPlaceholder(draggable,ev.originalEvent)}ev.stopPropagation()
ev.preventDefault()
ev.originalEvent.dataTransfer.dropEffect='move'}
ListSortable.prototype.onDragEnter=function(ev){if(!this.isSourceManagedList(ev.originalEvent)){return}var draggable=this.getClosestDraggableParent(ev.target)
if(!draggable){return}if(this.elementIsPlaceholder(draggable)){return}this.createPlaceholder(draggable,ev.originalEvent)
ev.stopPropagation()
ev.preventDefault()}
ListSortable.prototype.onDragLeave=function(ev){if(!this.isSourceManagedList(ev.originalEvent)){return}ev.stopPropagation()
ev.preventDefault()}
ListSortable.prototype.onDragDrop=function(ev){if(!this.isSourceManagedList(ev.originalEvent)){return}var draggable=this.getClosestDraggableParent(ev.target)
if(!draggable){return}this.moveElement(draggable,ev.originalEvent)
this.removePlaceholders()}
ListSortable.prototype.onDragEnd=function(ev){$(document).off('dragover',this.proxy(this.onDocumentDragOver))}
ListSortable.prototype.onDocumentDragOver=function(ev){if(!this.isSourceManagedList(ev.originalEvent)){return}if(this.mouseOutsideLists(ev.originalEvent)){this.removePlaceholders()
return}}
ListSortable.prototype.onDocumentMouseMove=function(ev){$(document).off('mousemove',this.proxy(this.onDocumentMouseMove))
this.removePlaceholders()}
ListSortable.DEFAULTS={handle:null,direction:'vertical'}
var old=$.fn.listSortable
$.fn.listSortable=function(option){var args=arguments
return this.each(function(){var $this=$(this),data=$this.data('oc.listSortable'),options=$.extend({},ListSortable.DEFAULTS,$this.data(),typeof option=='object'&&option)
if(!data){$this.data('oc.listSortable',(data=new ListSortable(this,options)))}if(typeof option=='string'&&data){if(data[option]){var methodArguments=Array.prototype.slice.call(args)
methodArguments.shift()
data[option].apply(data,methodArguments)}}})}
$.fn.listSortable.Constructor=ListSortable
$.fn.listSortable.noConflict=function(){$.fn.listSortable=old
return this}
$(document).render(function(){$('[data-control=list-sortable]').listSortable()})}(window.jQuery);