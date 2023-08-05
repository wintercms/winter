"use strict";(self.webpackChunk_wintercms_wn_system_module=self.webpackChunk_wintercms_wn_system_module||[]).push([[459],{579:function(e,t,s){s.d(t,{Z:function(){return a}});class a{constructor(e){this.snowboard=e}construct(){}dependencies(){return[]}traits(){return[]}listens(){return{}}destruct(){this.detach(),delete this.snowboard}destructor(){this.destruct()}}},281:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(579);class i extends a.Z{}},809:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(281);class i extends a.Z{listens(){return{ajaxLoadAssets:"load"}}dependencies(){return["url"]}async load(e){if(e.js&&e.js.length>0)for(const t of e.js)try{await this.loadScript(t)}catch(e){return Promise.reject(e)}if(e.css&&e.css.length>0)for(const t of e.css)try{await this.loadStyle(t)}catch(e){return Promise.reject(e)}if(e.img&&e.img.length>0)for(const t of e.img)try{await this.loadImage(t)}catch(e){return Promise.reject(e)}return Promise.resolve()}loadScript(e){return new Promise(((t,s)=>{e=this.snowboard.url().asset(e);if(document.querySelector(`script[src="${e}"]`))return void t();const a=document.createElement("script");a.setAttribute("type","text/javascript"),a.setAttribute("src",e),a.addEventListener("load",(()=>{this.snowboard.globalEvent("assetLoader.loaded","script",e,a),t()})),a.addEventListener("error",(()=>{this.snowboard.globalEvent("assetLoader.error","script",e,a),s(new Error(`Unable to load script file: "${e}"`))})),document.body.append(a)}))}loadStyle(e){return new Promise(((t,s)=>{e=this.snowboard.url().asset(e);if(document.querySelector(`link[rel="stylesheet"][href="${e}"]`))return void t();const a=document.createElement("link");a.setAttribute("rel","stylesheet"),a.setAttribute("href",e),a.addEventListener("load",(()=>{this.snowboard.globalEvent("assetLoader.loaded","style",e,a),t()})),a.addEventListener("error",(()=>{this.snowboard.globalEvent("assetLoader.error","style",e,a),s(new Error(`Unable to load stylesheet file: "${e}"`))})),document.head.append(a)}))}loadImage(e){return new Promise(((t,s)=>{e=this.snowboard.url().asset(e);const a=new Image;a.addEventListener("load",(()=>{this.snowboard.globalEvent("assetLoader.loaded","image",e,a),t()})),a.addEventListener("error",(()=>{this.snowboard.globalEvent("assetLoader.error","image",e,a),s(new Error(`Unable to load image file: "${e}"`))})),a.src=e}))}}},553:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(281);class i extends a.Z{dependencies(){return["request"]}listens(){return{ajaxStart:"ajaxStart",ajaxDone:"ajaxDone"}}ajaxStart(e,t){if(t.element)if("FORM"===t.element.tagName){const e=t.element.querySelectorAll("[data-attach-loading]");e.length>0&&e.forEach((e=>{e.classList.add(this.getLoadingClass(e))}))}else void 0!==t.element.dataset.attachLoading&&t.element.classList.add(this.getLoadingClass(t.element))}ajaxDone(e,t){if(t.element)if("FORM"===t.element.tagName){const e=t.element.querySelectorAll("[data-attach-loading]");e.length>0&&e.forEach((e=>{e.classList.remove(this.getLoadingClass(e))}))}else void 0!==t.element.dataset.attachLoading&&t.element.classList.remove(this.getLoadingClass(t.element))}getLoadingClass(e){return void 0!==e.dataset.attachLoading&&""!==e.dataset.attachLoading?e.dataset.attachLoading:"wn-loading"}}},763:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(579);class i extends a.Z{construct(e,t,s){if(e instanceof a.Z==!1)throw new Error("You must provide a Snowboard plugin to enable data configuration");if(t instanceof HTMLElement==!1)throw new Error("Data configuration can only be extracted from HTML elements");this.instance=e,this.element=t,this.localConfig=s||{},this.instanceConfig={},this.acceptedConfigs={},this.refresh()}get(e){return void 0===e?this.instanceConfig:void 0!==this.instanceConfig[e]?this.instanceConfig[e]:void 0}set(e,t,s){if(void 0===e)throw new Error("You must provide a configuration key to set");this.instanceConfig[e]=t,!0===s&&(this.element.dataset[e]=t,this.localConfig[e]=t)}refresh(){this.acceptedConfigs=this.getAcceptedConfigs(),this.instanceConfig=this.processConfig()}getAcceptedConfigs(){return void 0!==this.instance.acceptAllDataConfigs&&!0===this.instance.acceptAllDataConfigs||void 0!==this.instance.defaults&&"function"==typeof this.instance.defaults&&"object"==typeof this.instance.defaults()&&Object.keys(this.instance.defaults())}getDefaults(){return void 0!==this.instance.defaults&&"function"==typeof this.instance.defaults&&"object"==typeof this.instance.defaults()?this.instance.defaults():{}}processConfig(){const e=this.getDefaults();if(!1===this.acceptedConfigs)return e;for(const t in this.element.dataset)(!0===this.acceptedConfigs||this.acceptedConfigs.includes(t))&&(e[t]=this.coerceValue(this.element.dataset[t]));for(const t in this.localConfig)(!0===this.acceptedConfigs||this.acceptedConfigs.includes(t))&&(e[t]=this.localConfig[t]);return e}coerceValue(e){const t=String(e);if("null"===t)return null;if("undefined"!==t){if(t.startsWith("base64:")){const e=t.replace(/^base64:/,""),s=atob(e);return this.coerceValue(s)}if(["true","yes"].includes(t.toLowerCase()))return!0;if(["false","no"].includes(t.toLowerCase()))return!1;if(/^[-+]?[0-9]+(\.[0-9]+)?$/.test(t))return Number(t);try{return this.snowboard.jsonParser().parse(t)}catch(e){return""===t||t}}}}},270:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(579);class i extends a.Z{construct(e,t,s){if(this.message=e,this.type=t||"default",this.duration=Number(s||7),this.duration<0)throw new Error("Flash duration must be a positive number, or zero");this.clear(),this.timer=null,this.flashTimer=null,this.create()}dependencies(){return["transition"]}destruct(){null!==this.timer&&window.clearTimeout(this.timer),this.flashTimer&&this.flashTimer.remove(),this.flash&&(this.flash.remove(),this.flash=null,this.flashTimer=null),super.destruct()}create(){this.snowboard.globalEvent("flash.create",this),this.flash=document.createElement("DIV"),this.flash.innerHTML=this.message,this.flash.classList.add("flash-message",this.type),this.flash.removeAttribute("data-control"),this.flash.addEventListener("click",(()=>this.remove())),this.flash.addEventListener("mouseover",(()=>this.stopTimer())),this.flash.addEventListener("mouseout",(()=>this.startTimer())),this.duration>0?(this.flashTimer=document.createElement("DIV"),this.flashTimer.classList.add("flash-timer"),this.flash.appendChild(this.flashTimer)):this.flash.classList.add("no-timer"),document.body.appendChild(this.flash),this.snowboard.transition(this.flash,"show",(()=>{this.startTimer()}))}remove(){this.snowboard.globalEvent("flash.remove",this),this.stopTimer(),this.snowboard.transition(this.flash,"hide",(()=>{this.flash.remove(),this.flash=null,this.destruct()}))}clear(){document.querySelectorAll("body > div.flash-message").forEach((e=>e.remove()))}startTimer(){0!==this.duration&&(this.timerTrans=this.snowboard.transition(this.flashTimer,"timeout",null,`${this.duration}.0s`,!0),this.timer=window.setTimeout((()=>this.remove()),1e3*this.duration))}stopTimer(){this.timerTrans&&this.timerTrans.cancel(),this.timer&&window.clearTimeout(this.timer)}}},277:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(281);class i extends a.Z{dependencies(){return["request"]}listens(){return{ready:"ready",ajaxStart:"ajaxStart"}}ready(){this.counter=0,this.createStripe()}ajaxStart(e,t){!1!==t.options.stripe&&(this.show(),e.then((()=>{this.hide()})).catch((()=>{this.hide()})))}createStripe(){this.indicator=document.createElement("DIV"),this.stripe=document.createElement("DIV"),this.stripeLoaded=document.createElement("DIV"),this.indicator.classList.add("stripe-loading-indicator","loaded"),this.stripe.classList.add("stripe"),this.stripeLoaded.classList.add("stripe-loaded"),this.indicator.appendChild(this.stripe),this.indicator.appendChild(this.stripeLoaded),document.body.appendChild(this.indicator)}show(){this.counter+=1;const e=this.stripe.cloneNode(!0);this.indicator.appendChild(e),this.stripe.remove(),this.stripe=e,this.counter>1||(this.indicator.classList.remove("loaded"),document.body.classList.add("wn-loading"))}hide(e){this.counter-=1,!0===e&&(this.counter=0),this.counter<=0&&(this.indicator.classList.add("loaded"),document.body.classList.remove("wn-loading"))}}},32:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(281);class i extends a.Z{listens(){return{ready:"ready"}}ready(){let e=!1;if(document.querySelectorAll('link[rel="stylesheet"]').forEach((t=>{t.href.endsWith("/modules/system/assets/css/snowboard.extras.css")&&(e=!0)})),!e){const e=document.createElement("link");e.setAttribute("rel","stylesheet"),e.setAttribute("href",this.snowboard.url().asset("/modules/system/assets/css/snowboard.extras.css")),document.head.appendChild(e)}}}},269:function(e,t,s){s.d(t,{Z:function(){return i}});var a=s(579);class i extends a.Z{construct(e,t,s,a,i){if(e instanceof HTMLElement==!1)throw new Error("A HTMLElement must be provided for transitioning");if(this.element=e,"string"!=typeof t)throw new Error("Transition name must be specified as a string");if(this.transition=t,s&&"function"!=typeof s)throw new Error("Callback must be a valid function");this.callback=s,this.duration=a?this.parseDuration(a):null,this.trailTo=!0===i,this.doTransition()}eventClasses(){for(var e=arguments.length,t=new Array(e),s=0;s<e;s++)t[s]=arguments[s];const a={in:`${this.transition}-in`,active:`${this.transition}-active`,out:`${this.transition}-out`};if(0===t.length)return Object.values(a);const i=[];return Object.entries(a).forEach((e=>{const[s,a]=e;-1!==t.indexOf(s)&&i.push(a)})),i}doTransition(){null!==this.duration&&(this.element.style.transitionDuration=this.duration),this.resetClasses(),this.eventClasses("in","active").forEach((e=>{this.element.classList.add(e)})),window.requestAnimationFrame((()=>{"0s"!==window.getComputedStyle(this.element)["transition-duration"]?(this.element.addEventListener("transitionend",(()=>this.onTransitionEnd()),{once:!0}),window.requestAnimationFrame((()=>{this.element.classList.remove(this.eventClasses("in")[0]),this.element.classList.add(this.eventClasses("out")[0])}))):(this.resetClasses(),this.callback&&this.callback.apply(this.element),this.destruct())}))}onTransitionEnd(){this.eventClasses("active",this.trailTo?"":"out").forEach((e=>{this.element.classList.remove(e)})),this.callback&&this.callback.apply(this.element),null!==this.duration&&(this.element.style.transitionDuration=null),this.destruct()}cancel(){this.element.removeEventListener("transitionend",(()=>this.onTransitionEnd),{once:!0}),this.resetClasses(),null!==this.duration&&(this.element.style.transitionDuration=null),this.destruct()}resetClasses(){this.eventClasses().forEach((e=>{this.element.classList.remove(e)}))}parseDuration(e){const t=/^([0-9]+(\.[0-9]+)?)(m?s)?$/.exec(e),s=Number(t[1]);return"sec"===("s"===t[3]?"sec":"msec")?1e3*s+"ms":`${Math.floor(s)}ms`}}},820:function(e,t,s){var a=s(270),i=s(281);class r extends i.Z{dependencies(){return["flash"]}listens(){return{ready:"ready",ajaxErrorMessage:"ajaxErrorMessage",ajaxFlashMessages:"ajaxFlashMessages"}}ready(){document.querySelectorAll('[data-control="flash-message"]').forEach((e=>{this.snowboard.flash(e.innerHTML,e.dataset.flashType,e.dataset.flashDuration),e.remove()}))}ajaxErrorMessage(e){return this.snowboard.flash(e,"error"),!1}ajaxFlashMessages(e){return Object.entries(e).forEach((e=>{const[t,s]=e;this.snowboard.flash(s,t)})),!1}}class n extends i.Z{construct(){this.errorBags=[]}listens(){return{ready:"ready",ajaxStart:"clearValidation",ajaxValidationErrors:"doValidation"}}ready(){this.collectErrorBags(document)}doValidation(e,t,s){if(s.element&&void 0===s.element.dataset.requestValidate)return null;if(!e)return null;return this.errorBags.filter((t=>t.form===e)).forEach((e=>{this.showErrorBag(e,t)})),!1}clearValidation(e,t){if(t.element&&void 0===t.element.dataset.requestValidate)return;if(!t.form)return;this.errorBags.filter((e=>e.form===t.form)).forEach((e=>{this.hideErrorBag(e)}))}collectErrorBags(e){e.querySelectorAll("[data-validate-error], [data-validate-for]").forEach((e=>{const t=e.closest("form[data-request-validate]");if(!t)return void e.parentNode.removeChild(e);let s=null;e.matches("[data-validate-error]")&&(s=e.querySelector("[data-message]"));const a=document.createComment(""),i={element:e,form:t,validateFor:e.dataset.validateFor?e.dataset.validateFor.split(/\s*,\s*/):"*",placeholder:a,messageListElement:s?s.cloneNode(!0):null,messageListAnchor:null,customMessage:!!e.dataset.validateFor&&(""!==e.textContent||e.childNodes.length>0)};if(s){const e=document.createComment("");s.parentNode.replaceChild(e,s),i.messageListAnchor=e}e.parentNode.replaceChild(a,e),this.errorBags.push(i)}))}hideErrorBag(e){e.element.isConnected&&e.element.parentNode.replaceChild(e.placeholder,e.element)}showErrorBag(e,t){if(this.errorBagValidatesField(e,t))if(e.element.isConnected||e.placeholder.parentNode.replaceChild(e.element,e.placeholder),"*"!==e.validateFor){if(!e.customMessage){const s=Object.keys(t).filter((t=>e.validateFor.includes(t))).shift();[e.element.innerHTML]=t[s]}}else e.messageListElement?(e.element.querySelectorAll("[data-validation-message]").forEach((e=>{e.parentNode.removeChild(e)})),Object.entries(t).forEach((t=>{const[,s]=t;s.forEach((t=>{const s=e.messageListElement.cloneNode(!0);s.dataset.validationMessage="",s.innerHTML=t,e.messageListAnchor.after(s)}))}))):[e.element.innerHTML]=t[Object.keys(t).shift()]}errorBagValidatesField(e,t){return"*"===e.validateFor||Object.keys(t).filter((t=>e.validateFor.includes(t))).length>0}}var o,l=s(269),d=s(553),c=s(277),h=s(32),u=s(809),m=s(763);if(void 0===window.Snowboard)throw new Error("Snowboard must be loaded in order to use the extra plugins.");(o=window.Snowboard).addPlugin("assetLoader",u.Z),o.addPlugin("dataConfig",m.Z),o.addPlugin("extrasStyles",h.Z),o.addPlugin("transition",l.Z),o.addPlugin("flash",a.Z),o.addPlugin("flashListener",r),o.addPlugin("formValidation",n),o.addPlugin("attachLoading",d.Z),o.addPlugin("stripeLoader",c.Z)}},function(e){var t;t=820,e(e.s=t)}]);