(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-personal-duihuan_list"],{"2b6e":function(t,e,i){"use strict";i.r(e);var a=i("7ce2"),n=i.n(a);for(var s in a)["default"].indexOf(s)<0&&function(t){i.d(e,t,(function(){return a[t]}))}(s);e["default"]=n.a},6782:function(t,e,i){"use strict";var a=i("caa4"),n=i.n(a);n.a},"7ce2":function(t,e,i){"use strict";i("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("e25e"),i("99af");var a={data:function(){return{list:[],loadStatus:"more",showNoList:!1,pageInfo:{page:1,per_page:10,last_page:1,total:0}}},onReachBottom:function(){"noMore"!=this.loadStatus&&(this.pageInfo.page++,this.loadStatus="loading",this.getData())},onLoad:function(){this.getData()},methods:{restData:function(){this.list=[],this.pageInfo.page=1,this.loadStatus="more",this.getData()},getData:function(){var t=this;this.request("/api/activity.synthesis/getList",{page:this.pageInfo.page,type:this.current}).then((function(e){1==e.data.code&&(0==e.data.data.length?t.loadStatus="noMore":1==parseInt(t.pageInfo.page)?(t.list=e.data.data,e.data.data.length<10&&(t.loadStatus="noMore")):t.list=t.list.concat(e.data.data))}))}}};e.default=a},8362:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.mj[data-v-dfa9020a]{font-size:%?24?%;color:#b2b2b2}.items_two[data-v-dfa9020a]{height:%?200?%;display:flex;justify-content:space-between;flex-direction:column}.items_two .items_three[data-v-dfa9020a]{width:%?464?%;display:flex;position:relative;justify-content:space-between}.items_two .items_three > uni-view[data-v-dfa9020a]:first-of-type{font-size:%?30?%;font-weight:600;color:#f46728}.items_two .items_three > uni-view[data-v-dfa9020a]:nth-of-type(2){display:flex}.items_two .items_three > uni-view:nth-of-type(2) > uni-view[data-v-dfa9020a]{width:%?107?%;height:%?50?%;border-radius:%?25?%;font-size:%?26?%;text-align:center;line-height:%?50?%}.items_two .items_three > uni-image[data-v-dfa9020a]{position:absolute;right:%?30?%;bottom:%?0?%;width:%?110?%;height:%?97?%}.items_two > uni-view[data-v-dfa9020a]:first-of-type{font-size:%?28?%;font-weight:600;width:%?464?%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}.items_two > uni-view:nth-of-type(2) > uni-text[data-v-dfa9020a]{font-size:%?24?%}.items_two > uni-view:nth-of-type(2) > uni-text[data-v-dfa9020a]:nth-of-type(2){margin-left:%?40?%}.items[data-v-dfa9020a]{display:flex;margin-bottom:%?30?%}.items .items_con[data-v-dfa9020a]{height:%?200?%;display:flex;justify-content:space-between;flex-direction:column}.items .items_con .items_one[data-v-dfa9020a]{width:%?464?%;display:flex;position:relative;justify-content:space-between}.items .items_con .items_one > uni-image[data-v-dfa9020a]{position:absolute;right:%?30?%;bottom:%?0?%;width:%?110?%;height:%?97?%}.items .items_con .items_one > uni-view[data-v-dfa9020a]:first-of-type{font-size:%?30?%;font-weight:600;color:#f46728}.items .items_con .items_one > uni-view[data-v-dfa9020a]:nth-of-type(2){display:flex}.items .items_con .items_one > uni-view:nth-of-type(2) > uni-view[data-v-dfa9020a]{width:%?107?%;height:%?50?%;border-radius:%?25?%;font-size:%?26?%;text-align:center;line-height:%?50?%}.items .items_con > uni-view[data-v-dfa9020a]:first-of-type{font-size:%?28?%;font-weight:600;width:%?464?%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}.items > uni-image[data-v-dfa9020a]{width:%?200?%;height:%?200?%;border-radius:%?14?%;margin-right:%?20?%}',""]),t.exports=e},ae9d:function(t,e,i){"use strict";i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return n})),i.d(e,"a",(function(){}));var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",[i("Hearder",{attrs:{name:"兑换记录"}}),i("v-uni-view",{staticStyle:{padding:"30rpx"}},t._l(t.list,(function(e,a){return i("v-uni-view",{staticClass:"items"},[i("v-uni-image",{attrs:{src:e.goods_thumb}}),i("v-uni-view",{staticClass:"items_two"},[i("v-uni-view",[t._v(t._s(e.name))]),i("v-uni-view",[i("v-uni-text",[t._v("兑换时间："+t._s(e.start_time))])],1)],1)],1)})),1),i("NoList",{attrs:{cList:t.list}})],1)},n=[]},ca38:function(t,e,i){"use strict";i.r(e);var a=i("ae9d"),n=i("2b6e");for(var s in n)["default"].indexOf(s)<0&&function(t){i.d(e,t,(function(){return n[t]}))}(s);i("6782");var o=i("f0c5"),r=Object(o["a"])(n["default"],a["b"],a["c"],!1,null,"dfa9020a",null,!1,a["a"],void 0);e["default"]=r.exports},caa4:function(t,e,i){var a=i("8362");a.__esModule&&(a=a.default),"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("4ca1fd8a",a,!0,{sourceMap:!1,shadowMode:!1})}}]);