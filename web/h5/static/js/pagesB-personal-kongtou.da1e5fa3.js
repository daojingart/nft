(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesB-personal-kongtou"],{4289:function(t,n,a){"use strict";a.d(n,"b",(function(){return i})),a.d(n,"c",(function(){return e})),a.d(n,"a",(function(){}));var i=function(){var t=this,n=t.$createElement,a=t._self._c||n;return a("v-uni-view",[a("Hearder",{attrs:{name:"我的空投"}}),a("v-uni-view",{staticClass:"bgc"},[a("v-uni-view",{staticClass:"zuo"},[a("v-uni-view",{staticClass:"yv"},[t._v("剩余空投次数")]),a("v-uni-view",{staticClass:"num"},[t._v(t._s(t.info.volume_drop))])],1),a("v-uni-view",{staticClass:"tuoy",on:{click:function(n){arguments[0]=n=t.$handleEvent(n),t.Kongtou()}}},[a("span",{staticClass:"color_span"},[t._v("立即空投")])])],1),a("v-uni-view",{staticClass:"news1"},[a("v-uni-view",{staticClass:"jvxing1"}),t._v("空投怎么获得")],1),a("v-uni-view",{staticClass:"txt1"},[t._v(t._s(t.info.drop_obtain))]),a("v-uni-view",{staticClass:"news1"},[a("v-uni-view",{staticClass:"jvxing1"}),t._v("空投会在什么时候下发")],1),a("v-uni-view",{staticClass:"txt2"},[t._v(t._s(t.info.drop_shelves))]),a("v-uni-view",{staticClass:"news1"},[a("v-uni-view",{staticClass:"jvxing1"}),t._v("空投发放的顺序是什么")],1),a("v-uni-view",{staticClass:"txt2"},[t._v(t._s(t.info.drop_order))])],1)},e=[]},6436:function(t,n,a){"use strict";a.r(n);var i=a("4289"),e=a("db4b");for(var r in e)["default"].indexOf(r)<0&&function(t){a.d(n,t,(function(){return e[t]}))}(r);a("781f");var o=a("f0c5"),s=Object(o["a"])(e["default"],i["b"],i["c"],!1,null,"b6a91fb2",null,!1,i["a"],void 0);n["default"]=s.exports},"781f":function(t,n,a){"use strict";var i=a("ff4c"),e=a.n(i);e.a},a40f:function(t,n,a){"use strict";a("7a82"),Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var i={data:function(){return{info:""}},onLoad:function(){this.getDetail()},methods:{Kongtou:function(){var t=this;this.request("/api/activity.airdrop/executeAirDrop").then((function(n){1==n.data.code?(t.getDetail(),setTimeout((function(){t.$tip(n.data.msg)}),500)):t.$tip(n.data.msg)}))},getDetail:function(){var t=this;this.request("/api/activity.airdrop/getAirDropInfo").then((function(n){1==n.data.code&&(t.info=n.data.data)}))}}};n.default=i},cea2:function(t,n,a){var i=a("24fb");n=i(!1),n.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.bgc[data-v-b6a91fb2]{position:relative;display:flex;width:%?690?%;height:%?247?%;margin-left:%?30?%;border-radius:5vw;background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);background-size:100%;margin-top:%?30?%}.bgc .tuoy[data-v-b6a91fb2]{position:absolute;color:#fff;width:%?202?%;height:%?77?%;top:%?85?%;right:%?31?%;border:%?1?% solid #fff;text-align:center;line-height:%?77?%;font-weight:600;border-radius:8vw;border:1px solid transparent;background-clip:padding-box,border-box;background-origin:padding-box,border-box;background-image:linear-gradient(90deg,#fff,#fff),linear-gradient(90deg,#4140ff 9.67%,#c376e2 93.43%)}.bgc .tuoy .color_span[data-v-b6a91fb2]{background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-fill-color:transparent}.yv[data-v-b6a91fb2]{padding-left:%?28?%;padding-top:%?74?%;font-size:%?30?%;color:#fff;font-family:PingFang SC}.num[data-v-b6a91fb2]{font-size:%?53?%;color:#fff;padding-left:%?28?%;padding-bottom:%?75?%}.news1[data-v-b6a91fb2]{margin-top:%?50?%;display:flex;align-items:center;font-size:%?30?%;font-weight:700;color:#323232;font-family:PingFang SC}.news1 .jvxing1[data-v-b6a91fb2]{width:%?7?%;height:%?31?%;background:linear-gradient(0deg,#0f2cf9,#2570fc);margin-left:%?30?%;margin-right:%?21?%}.txt1[data-v-b6a91fb2],\r\n.txt2[data-v-b6a91fb2]{font-size:%?28?%;font-family:PingFang SC;color:#a3a3a3;margin-top:%?33?%;margin-left:%?59?%}.txt2[data-v-b6a91fb2]{width:%?642?%;height:%?110?%}',""]),t.exports=n},db4b:function(t,n,a){"use strict";a.r(n);var i=a("a40f"),e=a.n(i);for(var r in i)["default"].indexOf(r)<0&&function(t){a.d(n,t,(function(){return i[t]}))}(r);n["default"]=e.a},ff4c:function(t,n,a){var i=a("cea2");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var e=a("4f06").default;e("cf377b64",i,!0,{sourceMap:!1,shadowMode:!1})}}]);