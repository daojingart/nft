(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-index-real"],{"0998":function(t,a,e){"use strict";e.d(a,"b",(function(){return n})),e.d(a,"c",(function(){return i})),e.d(a,"a",(function(){}));var n=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("v-uni-view",{staticClass:"integralMall"},[e("Hearder",{attrs:{name:"新闻资讯"}}),e("v-uni-view",{staticClass:"nav"},t._l(t.bar_list,(function(a,n){return e("v-uni-view",{class:t.current==a.category_id?"avtive":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.ChangeCurrent(a.category_id)}}},[t._v(t._s(a.name))])})),1),e("v-uni-view",{staticClass:"content"},t._l(t.list,(function(a,n){return e("v-uni-view",{staticClass:"gro",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.toPage("realDet?id="+a.id)}}},[e("v-uni-view",{staticClass:"gro_a"},[e("v-uni-view",{staticClass:"name"},[t._v(t._s(a.title))]),e("v-uni-view",{staticClass:"time"},[t._v(t._s(a.author)+t._s(a.create_time))])],1),e("v-uni-view",{staticClass:"gro_b"},[e("v-uni-image",{attrs:{src:a.image_url,mode:""}})],1)],1)})),1)],1)},i=[]},3102:function(t,a,e){"use strict";var n=e("aa22"),i=e.n(n);i.a},"3ad2":function(t,a,e){"use strict";e.r(a);var n=e("0998"),i=e("5caa");for(var r in i)["default"].indexOf(r)<0&&function(t){e.d(a,t,(function(){return i[t]}))}(r);e("3102");var o=e("f0c5"),s=Object(o["a"])(i["default"],n["b"],n["c"],!1,null,"92e5a9ac",null,!1,n["a"],void 0);a["default"]=s.exports},"4bdc":function(t,a,e){"use strict";e("7a82");var n=e("4ea4").default;Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0,e("e25e"),e("99af");var i=n(e("ade3")),r={data:function(){var t;return t={list:[],current:0,bar_list:[]},(0,i.default)(t,"list",[]),(0,i.default)(t,"loadStatus","more"),(0,i.default)(t,"showNoList",!1),(0,i.default)(t,"pageInfo",{page:1,per_page:10,last_page:1,total:0}),t},onLoad:function(){this.getBarList()},onReachBottom:function(){"noMore"!=this.loadStatus&&(this.pageInfo.page++,this.loadStatus="loading",this.getData())},methods:{ChangeCurrent:function(t){this.current=t,this.restData()},getBarList:function(){var t=this;this.request("/api/member.Notice/getcategory").then((function(a){1==a.data.code&&(t.bar_list=a.data.data,t.current=a.data.data[0].category_id,t.restData())}))},restData:function(){this.list=[],this.pageInfo.page=1,this.loadStatus="more",this.getData()},getData:function(){var t=this;this.request("/api/member.Notice/list",{page:this.pageInfo.page,category_id:this.current}).then((function(a){1==a.data.code&&(0==a.data.data.length?t.loadStatus="noMore":1==parseInt(t.pageInfo.page)?(t.list=a.data.data,a.data.data.length<10&&(t.loadStatus="noMore")):t.list=t.list.concat(a.data.data))}))},toPage:function(t){uni.navigateTo({url:t})}}};a.default=r},"5caa":function(t,a,e){"use strict";e.r(a);var n=e("4bdc"),i=e.n(n);for(var r in n)["default"].indexOf(r)<0&&function(t){e.d(a,t,(function(){return n[t]}))}(r);a["default"]=i.a},aa22:function(t,a,e){var n=e("d863");n.__esModule&&(n=n.default),"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=e("4f06").default;i("f47a1914",n,!0,{sourceMap:!1,shadowMode:!1})},d863:function(t,a,e){var n=e("24fb");a=n(!1),a.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.integralMall .nav[data-v-92e5a9ac]{height:%?90?%;white-space:nowrap;overflow-x:auto;overflow-y:hidden;border-bottom:%?2?% solid #eee}.integralMall .nav uni-view[data-v-92e5a9ac]{display:inline-block;font-size:%?28?%;color:#a3a3a3;height:%?90?%;line-height:%?90?%;margin:0 %?26?%}.integralMall .nav .avtive[data-v-92e5a9ac]{font-weight:600;color:#323232;position:relative}.integralMall .nav .avtive[data-v-92e5a9ac]::after{position:absolute;content:"";bottom:0;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%);width:%?38?%;height:%?6?%;background:linear-gradient(90deg,#0f2cf9,#2570fc);border-radius:%?4?%}.integralMall .content[data-v-92e5a9ac]{padding:0 4% %?40?%}.integralMall .content .gro[data-v-92e5a9ac]{padding:%?30?% 0;display:flex;justify-content:space-between;align-items:center;border-bottom:%?2?% solid #eee}.integralMall .content .gro .gro_a[data-v-92e5a9ac]{display:flex;justify-content:space-between;flex-direction:column;width:calc(100% - %?258?%);height:%?166?%}.integralMall .content .gro .gro_a .name[data-v-92e5a9ac]{font-size:%?28?%;color:#323232}.integralMall .content .gro .gro_a .time[data-v-92e5a9ac]{font-size:%?24?%;color:#999}.integralMall .content .gro .gro_b[data-v-92e5a9ac]{width:%?228?%;height:%?166?%}.integralMall .content .gro .gro_b uni-image[data-v-92e5a9ac]{width:100%;height:100%}',""]),t.exports=a}}]);