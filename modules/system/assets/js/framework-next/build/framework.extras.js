(()=>{var t={9662:(t,e,r)=>{var n=r(7854),o=r(614),i=r(6330),s=n.TypeError;t.exports=function(t){if(o(t))return t;throw s(i(t)+" is not a function")}},6077:(t,e,r)=>{var n=r(7854),o=r(614),i=n.String,s=n.TypeError;t.exports=function(t){if("object"==typeof t||o(t))return t;throw s("Can't set "+i(t)+" as a prototype")}},1223:(t,e,r)=>{var n=r(5112),o=r(30),i=r(3070),s=n("unscopables"),a=Array.prototype;null==a[s]&&i.f(a,s,{configurable:!0,value:o(null)}),t.exports=function(t){a[s][t]=!0}},9670:(t,e,r)=>{var n=r(7854),o=r(111),i=n.String,s=n.TypeError;t.exports=function(t){if(o(t))return t;throw s(i(t)+" is not an object")}},1318:(t,e,r)=>{var n=r(5656),o=r(1400),i=r(6244),s=function(t){return function(e,r,s){var a,u=n(e),c=i(u),l=o(s,c);if(t&&r!=r){for(;c>l;)if((a=u[l++])!=a)return!0}else for(;c>l;l++)if((t||l in u)&&u[l]===r)return t||l||0;return!t&&-1}};t.exports={includes:s(!0),indexOf:s(!1)}},4326:(t,e,r)=>{var n=r(1702),o=n({}.toString),i=n("".slice);t.exports=function(t){return i(o(t),8,-1)}},9920:(t,e,r)=>{var n=r(2597),o=r(3887),i=r(1236),s=r(3070);t.exports=function(t,e){for(var r=o(e),a=s.f,u=i.f,c=0;c<r.length;c++){var l=r[c];n(t,l)||a(t,l,u(e,l))}}},8544:(t,e,r)=>{var n=r(7293);t.exports=!n((function(){function t(){}return t.prototype.constructor=null,Object.getPrototypeOf(new t)!==t.prototype}))},4994:(t,e,r)=>{"use strict";var n=r(3383).IteratorPrototype,o=r(30),i=r(9114),s=r(8003),a=r(7497),u=function(){return this};t.exports=function(t,e,r){var c=e+" Iterator";return t.prototype=o(n,{next:i(1,r)}),s(t,c,!1,!0),a[c]=u,t}},8880:(t,e,r)=>{var n=r(9781),o=r(3070),i=r(9114);t.exports=n?function(t,e,r){return o.f(t,e,i(1,r))}:function(t,e,r){return t[e]=r,t}},9114:t=>{t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e}}},654:(t,e,r)=>{"use strict";var n=r(2109),o=r(6916),i=r(1913),s=r(6530),a=r(614),u=r(4994),c=r(9518),l=r(7674),f=r(8003),p=r(8880),h=r(1320),v=r(5112),d=r(7497),m=r(3383),y=s.PROPER,g=s.CONFIGURABLE,b=m.IteratorPrototype,x=m.BUGGY_SAFARI_ITERATORS,w=v("iterator"),O="keys",S="values",T="entries",L=function(){return this};t.exports=function(t,e,r,s,v,m,j){u(r,e,s);var E,P,M,C=function(t){if(t===v&&F)return F;if(!x&&t in I)return I[t];switch(t){case O:case S:case T:return function(){return new r(this,t)}}return function(){return new r(this)}},A=e+" Iterator",k=!1,I=t.prototype,_=I[w]||I["@@iterator"]||v&&I[v],F=!x&&_||C(v),R="Array"==e&&I.entries||_;if(R&&(E=c(R.call(new t)))!==Object.prototype&&E.next&&(i||c(E)===b||(l?l(E,b):a(E[w])||h(E,w,L)),f(E,A,!0,!0),i&&(d[A]=L)),y&&v==S&&_&&_.name!==S&&(!i&&g?p(I,"name",S):(k=!0,F=function(){return o(_,this)})),v)if(P={values:C(S),keys:m?F:C(O),entries:C(T)},j)for(M in P)(x||k||!(M in I))&&h(I,M,P[M]);else n({target:e,proto:!0,forced:x||k},P);return i&&!j||I[w]===F||h(I,w,F,{name:v}),d[e]=F,P}},9781:(t,e,r)=>{var n=r(7293);t.exports=!n((function(){return 7!=Object.defineProperty({},1,{get:function(){return 7}})[1]}))},317:(t,e,r)=>{var n=r(7854),o=r(111),i=n.document,s=o(i)&&o(i.createElement);t.exports=function(t){return s?i.createElement(t):{}}},8324:t=>{t.exports={CSSRuleList:0,CSSStyleDeclaration:0,CSSValueList:0,ClientRectList:0,DOMRectList:0,DOMStringList:0,DOMTokenList:1,DataTransferItemList:0,FileList:0,HTMLAllCollection:0,HTMLCollection:0,HTMLFormElement:0,HTMLSelectElement:0,MediaList:0,MimeTypeArray:0,NamedNodeMap:0,NodeList:1,PaintRequestList:0,Plugin:0,PluginArray:0,SVGLengthList:0,SVGNumberList:0,SVGPathSegList:0,SVGPointList:0,SVGStringList:0,SVGTransformList:0,SourceBufferList:0,StyleSheetList:0,TextTrackCueList:0,TextTrackList:0,TouchList:0}},8509:(t,e,r)=>{var n=r(317)("span").classList,o=n&&n.constructor&&n.constructor.prototype;t.exports=o===Object.prototype?void 0:o},8113:(t,e,r)=>{var n=r(5005);t.exports=n("navigator","userAgent")||""},7392:(t,e,r)=>{var n,o,i=r(7854),s=r(8113),a=i.process,u=i.Deno,c=a&&a.versions||u&&u.version,l=c&&c.v8;l&&(o=(n=l.split("."))[0]>0&&n[0]<4?1:+(n[0]+n[1])),!o&&s&&(!(n=s.match(/Edge\/(\d+)/))||n[1]>=74)&&(n=s.match(/Chrome\/(\d+)/))&&(o=+n[1]),t.exports=o},748:t=>{t.exports=["constructor","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","toLocaleString","toString","valueOf"]},2109:(t,e,r)=>{var n=r(7854),o=r(1236).f,i=r(8880),s=r(1320),a=r(3505),u=r(9920),c=r(4705);t.exports=function(t,e){var r,l,f,p,h,v=t.target,d=t.global,m=t.stat;if(r=d?n:m?n[v]||a(v,{}):(n[v]||{}).prototype)for(l in e){if(p=e[l],f=t.noTargetGet?(h=o(r,l))&&h.value:r[l],!c(d?l:v+(m?".":"#")+l,t.forced)&&void 0!==f){if(typeof p==typeof f)continue;u(p,f)}(t.sham||f&&f.sham)&&i(p,"sham",!0),s(r,l,p,t)}}},7293:t=>{t.exports=function(t){try{return!!t()}catch(t){return!0}}},6916:t=>{var e=Function.prototype.call;t.exports=e.bind?e.bind(e):function(){return e.apply(e,arguments)}},6530:(t,e,r)=>{var n=r(9781),o=r(2597),i=Function.prototype,s=n&&Object.getOwnPropertyDescriptor,a=o(i,"name"),u=a&&"something"===function(){}.name,c=a&&(!n||n&&s(i,"name").configurable);t.exports={EXISTS:a,PROPER:u,CONFIGURABLE:c}},1702:t=>{var e=Function.prototype,r=e.bind,n=e.call,o=r&&r.bind(n);t.exports=r?function(t){return t&&o(n,t)}:function(t){return t&&function(){return n.apply(t,arguments)}}},5005:(t,e,r)=>{var n=r(7854),o=r(614),i=function(t){return o(t)?t:void 0};t.exports=function(t,e){return arguments.length<2?i(n[t]):n[t]&&n[t][e]}},8173:(t,e,r)=>{var n=r(9662);t.exports=function(t,e){var r=t[e];return null==r?void 0:n(r)}},7854:(t,e,r)=>{var n=function(t){return t&&t.Math==Math&&t};t.exports=n("object"==typeof globalThis&&globalThis)||n("object"==typeof window&&window)||n("object"==typeof self&&self)||n("object"==typeof r.g&&r.g)||function(){return this}()||Function("return this")()},2597:(t,e,r)=>{var n=r(1702),o=r(7908),i=n({}.hasOwnProperty);t.exports=Object.hasOwn||function(t,e){return i(o(t),e)}},3501:t=>{t.exports={}},490:(t,e,r)=>{var n=r(5005);t.exports=n("document","documentElement")},4664:(t,e,r)=>{var n=r(9781),o=r(7293),i=r(317);t.exports=!n&&!o((function(){return 7!=Object.defineProperty(i("div"),"a",{get:function(){return 7}}).a}))},8361:(t,e,r)=>{var n=r(7854),o=r(1702),i=r(7293),s=r(4326),a=n.Object,u=o("".split);t.exports=i((function(){return!a("z").propertyIsEnumerable(0)}))?function(t){return"String"==s(t)?u(t,""):a(t)}:a},2788:(t,e,r)=>{var n=r(1702),o=r(614),i=r(5465),s=n(Function.toString);o(i.inspectSource)||(i.inspectSource=function(t){return s(t)}),t.exports=i.inspectSource},9909:(t,e,r)=>{var n,o,i,s=r(8536),a=r(7854),u=r(1702),c=r(111),l=r(8880),f=r(2597),p=r(5465),h=r(6200),v=r(3501),d="Object already initialized",m=a.TypeError,y=a.WeakMap;if(s||p.state){var g=p.state||(p.state=new y),b=u(g.get),x=u(g.has),w=u(g.set);n=function(t,e){if(x(g,t))throw new m(d);return e.facade=t,w(g,t,e),e},o=function(t){return b(g,t)||{}},i=function(t){return x(g,t)}}else{var O=h("state");v[O]=!0,n=function(t,e){if(f(t,O))throw new m(d);return e.facade=t,l(t,O,e),e},o=function(t){return f(t,O)?t[O]:{}},i=function(t){return f(t,O)}}t.exports={set:n,get:o,has:i,enforce:function(t){return i(t)?o(t):n(t,{})},getterFor:function(t){return function(e){var r;if(!c(e)||(r=o(e)).type!==t)throw m("Incompatible receiver, "+t+" required");return r}}}},614:t=>{t.exports=function(t){return"function"==typeof t}},4705:(t,e,r)=>{var n=r(7293),o=r(614),i=/#|\.prototype\./,s=function(t,e){var r=u[a(t)];return r==l||r!=c&&(o(e)?n(e):!!e)},a=s.normalize=function(t){return String(t).replace(i,".").toLowerCase()},u=s.data={},c=s.NATIVE="N",l=s.POLYFILL="P";t.exports=s},111:(t,e,r)=>{var n=r(614);t.exports=function(t){return"object"==typeof t?null!==t:n(t)}},1913:t=>{t.exports=!1},2190:(t,e,r)=>{var n=r(7854),o=r(5005),i=r(614),s=r(7976),a=r(3307),u=n.Object;t.exports=a?function(t){return"symbol"==typeof t}:function(t){var e=o("Symbol");return i(e)&&s(e.prototype,u(t))}},3383:(t,e,r)=>{"use strict";var n,o,i,s=r(7293),a=r(614),u=r(30),c=r(9518),l=r(1320),f=r(5112),p=r(1913),h=f("iterator"),v=!1;[].keys&&("next"in(i=[].keys())?(o=c(c(i)))!==Object.prototype&&(n=o):v=!0),null==n||s((function(){var t={};return n[h].call(t)!==t}))?n={}:p&&(n=u(n)),a(n[h])||l(n,h,(function(){return this})),t.exports={IteratorPrototype:n,BUGGY_SAFARI_ITERATORS:v}},7497:t=>{t.exports={}},6244:(t,e,r)=>{var n=r(7466);t.exports=function(t){return n(t.length)}},133:(t,e,r)=>{var n=r(7392),o=r(7293);t.exports=!!Object.getOwnPropertySymbols&&!o((function(){var t=Symbol();return!String(t)||!(Object(t)instanceof Symbol)||!Symbol.sham&&n&&n<41}))},8536:(t,e,r)=>{var n=r(7854),o=r(614),i=r(2788),s=n.WeakMap;t.exports=o(s)&&/native code/.test(i(s))},30:(t,e,r)=>{var n,o=r(9670),i=r(6048),s=r(748),a=r(3501),u=r(490),c=r(317),l=r(6200),f=l("IE_PROTO"),p=function(){},h=function(t){return"<script>"+t+"</"+"script>"},v=function(t){t.write(h("")),t.close();var e=t.parentWindow.Object;return t=null,e},d=function(){try{n=new ActiveXObject("htmlfile")}catch(t){}var t,e;d="undefined"!=typeof document?document.domain&&n?v(n):((e=c("iframe")).style.display="none",u.appendChild(e),e.src=String("javascript:"),(t=e.contentWindow.document).open(),t.write(h("document.F=Object")),t.close(),t.F):v(n);for(var r=s.length;r--;)delete d.prototype[s[r]];return d()};a[f]=!0,t.exports=Object.create||function(t,e){var r;return null!==t?(p.prototype=o(t),r=new p,p.prototype=null,r[f]=t):r=d(),void 0===e?r:i(r,e)}},6048:(t,e,r)=>{var n=r(9781),o=r(3070),i=r(9670),s=r(5656),a=r(1956);t.exports=n?Object.defineProperties:function(t,e){i(t);for(var r,n=s(e),u=a(e),c=u.length,l=0;c>l;)o.f(t,r=u[l++],n[r]);return t}},3070:(t,e,r)=>{var n=r(7854),o=r(9781),i=r(4664),s=r(9670),a=r(4948),u=n.TypeError,c=Object.defineProperty;e.f=o?c:function(t,e,r){if(s(t),e=a(e),s(r),i)try{return c(t,e,r)}catch(t){}if("get"in r||"set"in r)throw u("Accessors not supported");return"value"in r&&(t[e]=r.value),t}},1236:(t,e,r)=>{var n=r(9781),o=r(6916),i=r(5296),s=r(9114),a=r(5656),u=r(4948),c=r(2597),l=r(4664),f=Object.getOwnPropertyDescriptor;e.f=n?f:function(t,e){if(t=a(t),e=u(e),l)try{return f(t,e)}catch(t){}if(c(t,e))return s(!o(i.f,t,e),t[e])}},8006:(t,e,r)=>{var n=r(6324),o=r(748).concat("length","prototype");e.f=Object.getOwnPropertyNames||function(t){return n(t,o)}},5181:(t,e)=>{e.f=Object.getOwnPropertySymbols},9518:(t,e,r)=>{var n=r(7854),o=r(2597),i=r(614),s=r(7908),a=r(6200),u=r(8544),c=a("IE_PROTO"),l=n.Object,f=l.prototype;t.exports=u?l.getPrototypeOf:function(t){var e=s(t);if(o(e,c))return e[c];var r=e.constructor;return i(r)&&e instanceof r?r.prototype:e instanceof l?f:null}},7976:(t,e,r)=>{var n=r(1702);t.exports=n({}.isPrototypeOf)},6324:(t,e,r)=>{var n=r(1702),o=r(2597),i=r(5656),s=r(1318).indexOf,a=r(3501),u=n([].push);t.exports=function(t,e){var r,n=i(t),c=0,l=[];for(r in n)!o(a,r)&&o(n,r)&&u(l,r);for(;e.length>c;)o(n,r=e[c++])&&(~s(l,r)||u(l,r));return l}},1956:(t,e,r)=>{var n=r(6324),o=r(748);t.exports=Object.keys||function(t){return n(t,o)}},5296:(t,e)=>{"use strict";var r={}.propertyIsEnumerable,n=Object.getOwnPropertyDescriptor,o=n&&!r.call({1:2},1);e.f=o?function(t){var e=n(this,t);return!!e&&e.enumerable}:r},7674:(t,e,r)=>{var n=r(1702),o=r(9670),i=r(6077);t.exports=Object.setPrototypeOf||("__proto__"in{}?function(){var t,e=!1,r={};try{(t=n(Object.getOwnPropertyDescriptor(Object.prototype,"__proto__").set))(r,[]),e=r instanceof Array}catch(t){}return function(r,n){return o(r),i(n),e?t(r,n):r.__proto__=n,r}}():void 0)},2140:(t,e,r)=>{var n=r(7854),o=r(6916),i=r(614),s=r(111),a=n.TypeError;t.exports=function(t,e){var r,n;if("string"===e&&i(r=t.toString)&&!s(n=o(r,t)))return n;if(i(r=t.valueOf)&&!s(n=o(r,t)))return n;if("string"!==e&&i(r=t.toString)&&!s(n=o(r,t)))return n;throw a("Can't convert object to primitive value")}},3887:(t,e,r)=>{var n=r(5005),o=r(1702),i=r(8006),s=r(5181),a=r(9670),u=o([].concat);t.exports=n("Reflect","ownKeys")||function(t){var e=i.f(a(t)),r=s.f;return r?u(e,r(t)):e}},1320:(t,e,r)=>{var n=r(7854),o=r(614),i=r(2597),s=r(8880),a=r(3505),u=r(2788),c=r(9909),l=r(6530).CONFIGURABLE,f=c.get,p=c.enforce,h=String(String).split("String");(t.exports=function(t,e,r,u){var c,f=!!u&&!!u.unsafe,v=!!u&&!!u.enumerable,d=!!u&&!!u.noTargetGet,m=u&&void 0!==u.name?u.name:e;o(r)&&("Symbol("===String(m).slice(0,7)&&(m="["+String(m).replace(/^Symbol\(([^)]*)\)/,"$1")+"]"),(!i(r,"name")||l&&r.name!==m)&&s(r,"name",m),(c=p(r)).source||(c.source=h.join("string"==typeof m?m:""))),t!==n?(f?!d&&t[e]&&(v=!0):delete t[e],v?t[e]=r:s(t,e,r)):v?t[e]=r:a(e,r)})(Function.prototype,"toString",(function(){return o(this)&&f(this).source||u(this)}))},4488:(t,e,r)=>{var n=r(7854).TypeError;t.exports=function(t){if(null==t)throw n("Can't call method on "+t);return t}},3505:(t,e,r)=>{var n=r(7854),o=Object.defineProperty;t.exports=function(t,e){try{o(n,t,{value:e,configurable:!0,writable:!0})}catch(r){n[t]=e}return e}},8003:(t,e,r)=>{var n=r(3070).f,o=r(2597),i=r(5112)("toStringTag");t.exports=function(t,e,r){t&&!o(t=r?t:t.prototype,i)&&n(t,i,{configurable:!0,value:e})}},6200:(t,e,r)=>{var n=r(2309),o=r(9711),i=n("keys");t.exports=function(t){return i[t]||(i[t]=o(t))}},5465:(t,e,r)=>{var n=r(7854),o=r(3505),i="__core-js_shared__",s=n[i]||o(i,{});t.exports=s},2309:(t,e,r)=>{var n=r(1913),o=r(5465);(t.exports=function(t,e){return o[t]||(o[t]=void 0!==e?e:{})})("versions",[]).push({version:"3.19.1",mode:n?"pure":"global",copyright:"© 2021 Denis Pushkarev (zloirock.ru)"})},1400:(t,e,r)=>{var n=r(9303),o=Math.max,i=Math.min;t.exports=function(t,e){var r=n(t);return r<0?o(r+e,0):i(r,e)}},5656:(t,e,r)=>{var n=r(8361),o=r(4488);t.exports=function(t){return n(o(t))}},9303:t=>{var e=Math.ceil,r=Math.floor;t.exports=function(t){var n=+t;return n!=n||0===n?0:(n>0?r:e)(n)}},7466:(t,e,r)=>{var n=r(9303),o=Math.min;t.exports=function(t){return t>0?o(n(t),9007199254740991):0}},7908:(t,e,r)=>{var n=r(7854),o=r(4488),i=n.Object;t.exports=function(t){return i(o(t))}},7593:(t,e,r)=>{var n=r(7854),o=r(6916),i=r(111),s=r(2190),a=r(8173),u=r(2140),c=r(5112),l=n.TypeError,f=c("toPrimitive");t.exports=function(t,e){if(!i(t)||s(t))return t;var r,n=a(t,f);if(n){if(void 0===e&&(e="default"),r=o(n,t,e),!i(r)||s(r))return r;throw l("Can't convert object to primitive value")}return void 0===e&&(e="number"),u(t,e)}},4948:(t,e,r)=>{var n=r(7593),o=r(2190);t.exports=function(t){var e=n(t,"string");return o(e)?e:e+""}},6330:(t,e,r)=>{var n=r(7854).String;t.exports=function(t){try{return n(t)}catch(t){return"Object"}}},9711:(t,e,r)=>{var n=r(1702),o=0,i=Math.random(),s=n(1..toString);t.exports=function(t){return"Symbol("+(void 0===t?"":t)+")_"+s(++o+i,36)}},3307:(t,e,r)=>{var n=r(133);t.exports=n&&!Symbol.sham&&"symbol"==typeof Symbol.iterator},5112:(t,e,r)=>{var n=r(7854),o=r(2309),i=r(2597),s=r(9711),a=r(133),u=r(3307),c=o("wks"),l=n.Symbol,f=l&&l.for,p=u?l:l&&l.withoutSetter||s;t.exports=function(t){if(!i(c,t)||!a&&"string"!=typeof c[t]){var e="Symbol."+t;a&&i(l,t)?c[t]=l[t]:c[t]=u&&f?f(e):p(e)}return c[t]}},6992:(t,e,r)=>{"use strict";var n=r(5656),o=r(1223),i=r(7497),s=r(9909),a=r(654),u="Array Iterator",c=s.set,l=s.getterFor(u);t.exports=a(Array,"Array",(function(t,e){c(this,{type:u,target:n(t),index:0,kind:e})}),(function(){var t=l(this),e=t.target,r=t.kind,n=t.index++;return!e||n>=e.length?(t.target=void 0,{value:void 0,done:!0}):"keys"==r?{value:n,done:!1}:"values"==r?{value:e[n],done:!1}:{value:[n,e[n]],done:!1}}),"values"),i.Arguments=i.Array,o("keys"),o("values"),o("entries")},3948:(t,e,r)=>{var n=r(7854),o=r(8324),i=r(8509),s=r(6992),a=r(8880),u=r(5112),c=u("iterator"),l=u("toStringTag"),f=s.values,p=function(t,e){if(t){if(t[c]!==f)try{a(t,c,f)}catch(e){t[c]=f}if(t[l]||a(t,l,e),o[e])for(var r in s)if(t[r]!==s[r])try{a(t,r,s[r])}catch(e){t[r]=s[r]}}};for(var h in o)p(n[h]&&n[h].prototype,h);p(i,"DOMTokenList")}},e={};function r(n){var o=e[n];if(void 0!==o)return o.exports;var i=e[n]={exports:{}};return t[n](i,i.exports,r),i.exports}r.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),(()=>{"use strict";class t extends Winter.Module{constructor(t,e,r,n){super(t),this.message=e,this.type=r||"default",this.duration=n||7,this.clear(),this.timer=null,this.flashTimer=null,this.create()}dependencies(){return["transition"]}destructor(){null!==this.timer&&window.clearTimeout(this.timer),this.flash&&(this.flashTimer.remove(),this.flash.remove(),this.flash=null,this.flashTimer=null),super.destructor()}create(){this.flash=document.createElement("DIV"),this.flashTimer=document.createElement("DIV"),this.flash.innerHTML=this.message,this.flash.classList.add("flash-message",this.type),this.flashTimer.classList.add("flash-timer"),this.flash.removeAttribute("data-control"),this.flash.addEventListener("click",(()=>this.remove())),this.flash.addEventListener("mouseover",(()=>this.stopTimer())),this.flash.addEventListener("mouseout",(()=>this.startTimer())),this.flash.appendChild(this.flashTimer),document.body.appendChild(this.flash),this.winter.transition(this.flash,"show",(()=>{this.startTimer()}))}remove(){this.stopTimer(),this.winter.transition(this.flash,"hide",(()=>{this.flash.remove(),this.flash=null,this.destructor()}))}clear(){document.querySelectorAll("body > div.flash-message").forEach((t=>t.remove()))}startTimer(){this.timerTrans=this.winter.transition(this.flashTimer,"timeout",null,"".concat(this.duration,".0s"),!0),this.timer=window.setTimeout((()=>this.remove()),1e3*this.duration)}stopTimer(){this.timerTrans&&this.timerTrans.cancel(),window.clearTimeout(this.timer)}}class e extends Winter.Singleton{dependencies(){return["flash"]}listens(){return{ajaxErrorMessage:"ajaxErrorMessage"}}ajaxErrorMessage(t){return this.winter.flash(t,"error"),!1}}var n;r(3948);class o extends Winter.Module{constructor(t,e,r,n,o,i){if(super(t),e instanceof HTMLElement==!1)throw new Error("A HTMLElement must be provided for transitioning");if(this.element=e,"string"!=typeof r)throw new Error("Transition name must be specified as a string");if(this.transition=r,n&&"function"!=typeof n)throw new Error("Callback must be a valid function");this.callback=n,this.duration=o?this.parseDuration(o):null,this.trailTo=!0===i,this.doTransition()}eventClasses(){const t=Array.from(arguments),e={in:"".concat(this.transition,"-in"),active:"".concat(this.transition,"-active"),out:"".concat(this.transition,"-out")};if(0===t.length)return Object.values(e);const r=[];for(const[n,o]of Object.entries(e))-1!==t.indexOf(n)&&r.push(o);return r}doTransition(){null!==this.duration&&(this.element.style.transitionDuration=this.duration),this.resetClasses(),this.eventClasses("in","active").forEach((t=>{this.element.classList.add(t)})),window.requestAnimationFrame((()=>{"0s"!==window.getComputedStyle(this.element)["transition-duration"]?(this.element.addEventListener("transitionend",(()=>this.onTransitionEnd()),{once:!0}),window.requestAnimationFrame((()=>{this.element.classList.remove(this.eventClasses("in")[0]),this.element.classList.add(this.eventClasses("out")[0])}))):(this.resetClasses(),this.callback&&this.callback.apply(this.element),this.destructor())}))}onTransitionEnd(){this.eventClasses("active",this.trailTo?"":"out").forEach((t=>{this.element.classList.remove(t)})),this.callback&&this.callback.apply(this.element),null!==this.duration&&(this.element.style.transitionDuration=null),this.destructor()}cancel(){this.element.removeEventListener("transitionend",(()=>this.onTransitionEnd),{once:!0}),this.resetClasses(),null!==this.duration&&(this.element.style.transitionDuration=null),this.destructor()}resetClasses(){this.eventClasses().forEach((t=>{this.element.classList.remove(t)}))}parseDuration(t){const e=/^([0-9]+(\.[0-9]+)?)(m?s)?$/.exec(t),r=Number(e[1]);return"sec"===("s"===e[3]?"sec":"msec")?1e3*r+"ms":Math.floor(r)+"ms"}}(n=window.winter).addModule("transition",o),n.addModule("flash",t),n.addModule("flashListener",e)})()})();