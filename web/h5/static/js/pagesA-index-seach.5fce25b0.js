(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-index-seach"],{"3e1f":function(t,a,e){"use strict";e.r(a);var i=e("7212"),n=e.n(i);for(var o in i)["default"].indexOf(o)<0&&function(t){e.d(a,t,(function(){return i[t]}))}(o);a["default"]=n.a},"44f0":function(t,a,e){"use strict";e.r(a);var i=e("fdb6"),n=e("3e1f");for(var o in n)["default"].indexOf(o)<0&&function(t){e.d(a,t,(function(){return n[t]}))}(o);e("e2be");var s=e("f0c5"),c=Object(s["a"])(n["default"],i["b"],i["c"],!1,null,"3bfb07f8",null,!1,i["a"],void 0);a["default"]=c.exports},6768:function(t,a,e){var i=e("24fb");a=i(!1),a.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.history .history_one[data-v-3bfb07f8]{display:flex;flex-wrap:wrap;justify-content:flex-start;margin-top:%?30?%}.history .history_one > uni-view[data-v-3bfb07f8]{margin:%?0?% %?30?% %?30?% %?0?%;font-size:%?24?%;padding:%?10?% %?30?%;border-radius:%?20?%;background:#f5f5f5}.history > uni-view[data-v-3bfb07f8]:first-of-type{font-size:%?28?%;font-weight:600}.content[data-v-3bfb07f8]{padding:%?30?%}.con_a[data-v-3bfb07f8]{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap}.con_a .comm[data-v-3bfb07f8]{width:%?334?%;margin-bottom:%?36?%}.con_a .comm uni-image[data-v-3bfb07f8]{width:%?334?%;height:%?334?%}.con_a .comm .comm_a[data-v-3bfb07f8]{padding:%?14?% 0}.con_a .comm .comm_a uni-view[data-v-3bfb07f8]{text-align:center}.con_a .comm .comm_a .business[data-v-3bfb07f8]{font-size:%?28?%;font-weight:600;color:#323232}.con_a .comm .comm_a .name[data-v-3bfb07f8]{font-size:%?28?%;color:#323232;width:98%;margin:%?20?% auto;text-overflow:-o-ellipsis-lastline;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;min-height:%?76?%}.con_a .comm .comm_a .fl[data-v-3bfb07f8]{display:flex;justify-content:center;align-items:center}.con_a .comm .comm_a .fl .line[data-v-3bfb07f8]{font-size:%?28?%;text-decoration:line-through;color:#999;margin-right:%?20?%}.con_a .comm .comm_a .fl .pri[data-v-3bfb07f8]{font-size:%?28?%;color:#e21717}.seach[data-v-3bfb07f8]{position:fixed;top:%?0?%;background:#fff;z-index:5;padding:%?30?% %?30?% %?30?% %?30?%;width:%?690?%;display:flex;align-items:center;justify-content:space-between}.seach .inp[data-v-3bfb07f8]{border-radius:%?30?%;width:%?530?%;padding:%?15?% %?30?%;background:#f5f5f5;display:flex;align-items:center}.seach .inp > uni-input[data-v-3bfb07f8]{width:%?450?%;font-size:%?26?%}.seach > uni-view[data-v-3bfb07f8]:nth-of-type(2){font-size:%?28?%}',""]),t.exports=a},7212:function(t,a,e){"use strict";e("7a82"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0,e("e25e"),e("99af");var i={data:function(){return{title:"",value:"",hide:!0,catelist:[],searhList:[],loadStatus:"more",showNoList:!1,pageInfo:{page:1,per_page:10,last_page:1,total:0}}},onReachBottom:function(){"noMore"!=this.loadStatus&&(this.pageInfo.page++,this.loadStatus="loading",this.getData())},onLoad:function(){this.getSearchList()},methods:{getSearchList:function(){var t=this;this.request("/api/goods.marketGoods/search").then((function(a){1==a.data.code&&(t.searhList=a.data.data)}))},toPage:function(t){uni.navigateTo({url:t})},restData:function(){this.catelist=[],this.pageInfo.page=1,this.loadStatus="more",this.getData()},getData:function(){var t=this;this.request("/api/goods.products/getGoodsList",{category_id:0,page:this.pageInfo.page,title:this.title}).then((function(a){1==a.data.code&&(0==a.data.data.list.length?t.loadStatus="noMore":1==parseInt(t.pageInfo.page)?(t.catelist=a.data.data.list,a.data.data.list.length<10&&(t.loadStatus="noMore")):t.catelist=t.catelist.concat(a.data.data.list))}))},Seach:function(){this.hide=!this.hide,this.restData()},Back:function(){uni.navigateBack()}}};a.default=i},7905:function(t,a,e){var i=e("6768");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=e("4f06").default;n("1cebd9f3",i,!0,{sourceMap:!1,shadowMode:!1})},e2be:function(t,a,e){"use strict";var i=e("7905"),n=e.n(i);n.a},fdb6:function(t,a,e){"use strict";e.d(a,"b",(function(){return n})),e.d(a,"c",(function(){return o})),e.d(a,"a",(function(){return i}));var i={uIcon:e("ffd7").default},n=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("v-uni-view",[e("v-uni-view",{staticClass:"seach"},[e("v-uni-view",{staticClass:"inp"},[e("u-icon",{attrs:{name:"search",color:"#999",size:"18"}}),e("v-uni-input",{attrs:{type:"text",placeholder:"搜索藏品"},on:{confirm:function(a){arguments[0]=a=t.$handleEvent(a),t.Seach()}},model:{value:t.title,callback:function(a){t.title=a},expression:"title"}})],1),e("v-uni-view",{on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.Back()}}},[t._v("取消")])],1),e("v-uni-view",{staticStyle:{height:"100rpx"}}),e("v-uni-view",{staticClass:"content"},[e("v-uni-view",{staticClass:"con_a"},t._l(t.catelist,(function(a,i){return e("v-uni-view",{staticClass:"comm",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.toPage("/pagesA/market/commodityDet?id="+a.product_id)}}},[e("v-uni-image",{attrs:{src:a.thumbnail,mode:""}}),e("v-uni-view",{staticClass:"comm_a"},[e("v-uni-view",{staticClass:"business"},[t._v(t._s(a.system_name))]),e("v-uni-view",{staticClass:"name"},[t._v(t._s(a.product_name))]),e("v-uni-view",{staticClass:"fl"},[e("v-uni-view",{staticClass:"line"},[t._v("￥"+t._s(a.spec[0].line_price))]),e("v-uni-view",{staticClass:"pri"},[t._v("￥"+t._s(a.spec[0].goods_price))])],1)],1)],1)})),1),e("NoList",{attrs:{cList:t.catelist}})],1)],1)},o=[]}}]);