/*!
 * Pikaday jQuery plugin.
 *
 * Copyright © 2013 David Bushell | BSD & MIT license | https://github.com/Pikaday/Pikaday
 */
!function(e,t){"use strict";"object"==typeof exports?t(require("jquery"),require("pikaday")):"function"==typeof define&&define.amd?define(["jquery","pikaday"],t):t(e.jQuery,e.Pikaday)}(this,(function(e,t){"use strict";e.fn.pikaday=function(){var a=arguments;return a&&a.length||(a=[{}]),this.each((function(){var i=e(this),n=i.data("pikaday");if(n instanceof t)"string"==typeof a[0]&&"function"==typeof n[a[0]]&&(n[a[0]].apply(n,Array.prototype.slice.call(a,1)),"destroy"===a[0]&&i.removeData("pikaday"));else if("object"==typeof a[0]){var r=e.extend({},a[0]);r.field=i[0],i.data("pikaday",new t(r))}}))}}));
