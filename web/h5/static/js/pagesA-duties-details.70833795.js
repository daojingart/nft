(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-duties-details"],{"1d14":function(t,n,e){"use strict";e("7a82"),Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;n.default={data:function(){return{info:"",type:1}},onLoad:function(t){this.type=t.type,1==this.type?this.getDetail():this.getYS()},methods:{getYS:function(){var t=this;this.request("/api/index/getAgreement").then((function(n){1==n.data.code&&(t.info=n.data.data)}))},getDetail:function(){var t=this;this.request("/api/index/getUserAgreement").then((function(n){1==n.data.code&&(t.info=n.data.data)}))}}}},"2b65":function(t,n,e){"use strict";e.r(n);var r=e("bbb8"),i=e("85b5");for(var a in i)["default"].indexOf(a)<0&&function(t){e.d(n,t,(function(){return i[t]}))}(a);e("d61b");var s=e("f0c5"),u=Object(s["a"])(i["default"],r["b"],r["c"],!1,null,"0c9775d8",null,!1,r["a"],void 0);n["default"]=u.exports},"3fff":function(t,n,e){var r=e("24fb");n=r(!1),n.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.tops[data-v-0c9775d8]{text-align:center;font-size:%?34?%;font-weight:600;margin-bottom:%?30?%}',""]),t.exports=n},"85b5":function(t,n,e){"use strict";e.r(n);var r=e("1d14"),i=e.n(r);for(var a in r)["default"].indexOf(a)<0&&function(t){e.d(n,t,(function(){return r[t]}))}(a);n["default"]=i.a},"8b5c":function(t,n,e){var r=e("3fff");r.__esModule&&(r=r.default),"string"===typeof r&&(r=[[t.i,r,""]]),r.locals&&(t.exports=r.locals);var i=e("4f06").default;i("335a3c28",r,!0,{sourceMap:!1,shadowMode:!1})},bbb8:function(t,n,e){"use strict";e.d(n,"b",(function(){return i})),e.d(n,"c",(function(){return a})),e.d(n,"a",(function(){return r}));var r={uParse:e("a3ab").default},i=function(){var t=this.$createElement,n=this._self._c||t;return n("v-uni-view",[n("Hearder"),n("v-uni-view",{staticStyle:{padding:"30rpx"}},[n("v-uni-view",{staticClass:"tops"},[this._v(this._s(1==this.type?"用户协议":"隐私协议"))]),n("v-uni-view",{staticClass:"u-content"},[n("u-parse",{attrs:{content:this.info}})],1)],1)],1)},a=[]},d61b:function(t,n,e){"use strict";var r=e("8b5c"),i=e.n(r);i.a}}]);