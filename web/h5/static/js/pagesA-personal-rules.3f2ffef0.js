(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-personal-rules"],{"1dd8":function(n,t,r){"use strict";r.d(t,"b",(function(){return a})),r.d(t,"c",(function(){return u})),r.d(t,"a",(function(){return e}));var e={uParse:r("825a3").default},a=function(){var n=this.$createElement,t=this._self._c||n;return t("v-uni-view",[t("Hearder",{attrs:{name:"隐私协议"}}),t("v-uni-view",{staticClass:"content"},[t("u-parse",{attrs:{content:this.rule}})],1)],1)},u=[]},"2bf0":function(n,t,r){var e=r("69c7");e.__esModule&&(e=e.default),"string"===typeof e&&(e=[[n.i,e,""]]),e.locals&&(n.exports=e.locals);var a=r("4f06").default;a("6bd32a5a",e,!0,{sourceMap:!1,shadowMode:!1})},"2dfb":function(n,t,r){"use strict";var e=r("2bf0"),a=r.n(e);a.a},"32a7":function(n,t,r){"use strict";r.r(t);var e=r("1dd8"),a=r("df6e");for(var u in a)["default"].indexOf(u)<0&&function(n){r.d(t,n,(function(){return a[n]}))}(u);r("2dfb");var i=r("f0c5"),s=Object(i["a"])(a["default"],e["b"],e["c"],!1,null,"26238151",null,!1,e["a"],void 0);t["default"]=s.exports},"69c7":function(n,t,r){var e=r("24fb");t=e(!1),t.push([n.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.content[data-v-26238151]{padding:%?30?% 4%}',""]),n.exports=t},c7a8:function(n,t,r){"use strict";r("7a82"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;t.default={data:function(){return{rule:""}},onShow:function(){this.getAgreement()},methods:{getAgreement:function(){var n=this;this.request("/api/index/getAgreement").then((function(t){1==t.data.code&&(n.rule=t.data.data)}))}}}},df6e:function(n,t,r){"use strict";r.r(t);var e=r("c7a8"),a=r.n(e);for(var u in e)["default"].indexOf(u)<0&&function(n){r.d(t,n,(function(){return e[n]}))}(u);t["default"]=a.a}}]);