(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-index-syntheticDet"],{"0ae6":function(t,n,e){"use strict";e.r(n);var a=e("e6fe"),i=e.n(a);for(var o in a)["default"].indexOf(o)<0&&function(t){e.d(n,t,(function(){return a[t]}))}(o);n["default"]=i.a},1310:function(t,n,e){"use strict";var a=e("fe45"),i=e.n(a);i.a},9611:function(t,n,e){"use strict";e.d(n,"b",(function(){return i})),e.d(n,"c",(function(){return o})),e.d(n,"a",(function(){return a}));var a={uIcon:e("161f").default,uParse:e("825a3").default},i=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("v-uni-view",{staticClass:"integralMall"},[e("Hearder",{attrs:{name:"合成详情"}}),e("v-uni-view",{staticClass:"content"},[e("v-uni-view",{staticClass:"con_a"},[e("v-uni-swiper",{staticClass:"swiper",staticStyle:{height:"688rpx"},attrs:{autoplay:!0}},t._l(t.info.goods_thumb,(function(t,n){return e("v-uni-swiper-item",[e("v-uni-image",{attrs:{src:t.goods_thumb,mode:""}})],1)})),1)],1),e("v-uni-view",{staticClass:"con_b bottom"},[e("v-uni-view",{staticClass:"name"},[t._v(t._s(t.info.name))]),e("v-uni-view",{staticClass:"gro mag"},[e("v-uni-view",{staticClass:"drop"}),e("v-uni-view",[t._v("剩余 "+t._s(t.info.count))])],1),e("v-uni-view",{staticClass:"gro"},[e("v-uni-view",{staticClass:"drop"}),e("v-uni-view",[t._v(t._s(t.$u.timeFormat(t.info.strtotime_start_time,"yyyy-mm-dd hh:MM:ss"))+"  至 "+t._s(t.$u.timeFormat(t.info.strtotime_end_time,"yyyy-mm-dd hh:MM:ss")))])],1)],1),e("v-uni-view",{staticClass:"con_c bottom"},[e("v-uni-view",{staticClass:"tle"},[t._v("合成材料")]),e("v-uni-view",{staticClass:"spc"},t._l(t.info.counts,(function(n,a){return e("v-uni-view",{staticClass:"spc_a",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.goPage("/pagesA/index/coll_list?id="+t.id+"&goods_id="+n.goods_id+"&count="+n.count+"&index="+a)}}},[e("v-uni-image",{staticClass:"img_a",attrs:{src:n.goods_thumb,mode:""}}),e("v-uni-view",{class:t.arr[a].length<n.count?"acts spc_b":"spc_b"},[e("v-uni-view",{staticClass:"fe"},[t._v(t._s(t.arr[a].length)+"/"+t._s(n.count))]),t.arr[a].length<n.count?e("v-uni-view",{staticClass:"plus"},[e("u-icon",{attrs:{name:"plus-circle",color:"#ffffff",size:"27"}}),e("v-uni-view",[t._v("点击填入")])],1):t._e()],1)],1)})),1)],1),t.content?e("v-uni-view",{staticClass:"con_d bottom"},[e("v-uni-view",{staticClass:"tle"},[t._v("合成详情")]),e("u-parse",{attrs:{content:t.content}})],1):t._e()],1),e("v-uni-view",{staticClass:"bot"},[e("v-uni-view",{staticClass:"sub",on:{click:function(n){arguments[0]=n=t.$handleEvent(n),t.Hecheng()}}},[t._v("立即合成")])],1)],1)},o=[]},a2ee:function(t,n,e){var a=e("24fb");n=a(!1),n.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.acts[data-v-385e4a84]{position:absolute;top:0;left:0;width:100%;height:100%;border-radius:%?20?%;background:rgba(0,0,0,.7)}.bot[data-v-385e4a84]{position:fixed;bottom:0;left:0;background:#fff;height:%?98?%;width:92%;padding:0 4%;display:flex;justify-content:space-between;align-items:center}.bot .sub[data-v-385e4a84]{width:100%;height:%?80?%;text-align:center;line-height:%?80?%;font-size:%?32?%;font-weight:600;background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);color:#fff;border-radius:%?40?%}.content[data-v-385e4a84]{padding-bottom:%?98?%}.content .bottom[data-v-385e4a84]{border-bottom:%?18?% solid #f8f8f8}.content .tle[data-v-385e4a84]{padding:%?28?% 0 %?24?%;font-weight:700;color:#323232}.content .con_a[data-v-385e4a84]{padding:%?30?% 4%}.content .con_a .swiper[data-v-385e4a84] .uni-swiper-wrapper{border-radius:%?26?%}.content .con_a uni-image[data-v-385e4a84]{width:100%;height:%?688?%;border-radius:%?26?%}.content .con_b[data-v-385e4a84]{padding:0 4% %?32?%}.content .con_b .name[data-v-385e4a84]{font-size:%?32?%;font-weight:600;color:#323232}.content .con_b .gro[data-v-385e4a84]{display:flex;justify-content:flex-start;align-items:center}.content .con_b .gro .drop[data-v-385e4a84]{width:%?10?%;height:%?10?%;background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);border-radius:50%;margin-right:%?12?%}.content .con_b .gro uni-view[data-v-385e4a84]{font-size:%?22?%;color:#323232}.content .con_b .mag[data-v-385e4a84]{margin:%?10?% 0}.content .con_c[data-v-385e4a84]{padding:0 4% %?36?%}.content .con_c .spc[data-v-385e4a84]{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap}.content .con_c .spc .spc_a[data-v-385e4a84]{margin-bottom:%?20?%;position:relative;width:%?334?%;height:%?332?%;border-radius:%?20?%}.content .con_c .spc .spc_a .img_a[data-v-385e4a84]{width:%?334?%;height:%?332?%;border-radius:%?20?%}.content .con_c .spc .spc_a .spc_b .fe[data-v-385e4a84]{position:absolute;top:%?18?%;left:%?26?%;width:%?86?%;height:%?40?%;background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);border-radius:%?20?%;text-align:center;line-height:%?40?%;font-size:%?24?%;color:#fff}.content .con_c .spc .spc_a .spc_b .plus[data-v-385e4a84]{position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);text-align:center}.content .con_c .spc .spc_a .spc_b .plus uni-view[data-v-385e4a84]{margin-top:%?12?%;font-size:%?28?%;color:#fff}.content .con_d[data-v-385e4a84]{padding:0 4%}',""]),t.exports=n},be84:function(t,n,e){"use strict";e.r(n);var a=e("9611"),i=e("0ae6");for(var o in i)["default"].indexOf(o)<0&&function(t){e.d(n,t,(function(){return i[t]}))}(o);e("1310");var s=e("f0c5"),c=Object(s["a"])(i["default"],a["b"],a["c"],!1,null,"385e4a84",null,!1,a["a"],void 0);n["default"]=c.exports},e6fe:function(t,n,e){"use strict";e("7a82"),Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0,e("d3b7"),e("159b"),e("14d9");var a={data:function(){return{arr:[],info:"",content:""}},onShow:function(){this.$forceUpdate()},onLoad:function(t){this.id=t.id,this.getDetail()},methods:{Hecheng:function(){var t=this,n=[];this.arr.forEach((function(t){t.length>=1&&t.forEach((function(t,e){n.push(t)}))})),n=n.join(","),this.request("/api/activity.synthesis/synthetic",{id:this.id,material_id:n}).then((function(e){t.$tip(e.data.msg),1==e.data.code?setTimeout((function(){uni.reLaunch({url:"/pagesB/personal/cangpin"})}),1e3):(t.arr=[],n=[],setTimeout((function(){t.getDetail()}),500))}))},goPage:function(t){uni.navigateTo({url:t})},getDetail:function(){var t=this;this.request("/api/activity.synthesis/getDetails",{id:this.id}).then((function(n){if(1==n.data.code){t.info=n.data.data;var e=n.data.data.counts;t.content=n.data.data.content,e.forEach((function(n){t.arr.push("")}))}}))}}};n.default=a},fe45:function(t,n,e){var a=e("a2ee");a.__esModule&&(a=a.default),"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var i=e("4f06").default;i("1650f328",a,!0,{sourceMap:!1,shadowMode:!1})}}]);