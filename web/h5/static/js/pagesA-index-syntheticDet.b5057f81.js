(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-index-syntheticDet"],{1570:function(t,n,i){"use strict";i.d(n,"b",(function(){return e})),i.d(n,"c",(function(){return o})),i.d(n,"a",(function(){return a}));var a={uIcon:i("ffd7").default,uParse:i("a3ab").default},e=function(){var t=this,n=t.$createElement,i=t._self._c||n;return i("v-uni-view",{staticClass:"integralMall"},[i("Hearder",{attrs:{name:"合成详情"}}),i("v-uni-view",{staticClass:"content"},[i("v-uni-view",{staticClass:"con_a"},[i("v-uni-swiper",{staticClass:"swiper",staticStyle:{height:"688rpx"},attrs:{autoplay:!0}},t._l(t.info.goods_thumb,(function(t,n){return i("v-uni-swiper-item",[i("v-uni-image",{attrs:{src:t.goods_thumb,mode:""}})],1)})),1)],1),i("v-uni-view",{staticClass:"con_b bottom"},[i("v-uni-view",{staticClass:"name"},[t._v(t._s(t.info.name))]),i("v-uni-view",{staticClass:"gro mag"},[i("v-uni-view",{staticClass:"drop"}),i("v-uni-view",[t._v("剩余 "+t._s(t.info.count))])],1),i("v-uni-view",{staticClass:"gro"},[i("v-uni-view",{staticClass:"drop"}),i("v-uni-view",[t._v(t._s(t.$u.timeFormat(t.info.strtotime_start_time,"yyyy-mm-dd hh:MM:ss"))+"  至 "+t._s(t.$u.timeFormat(t.info.strtotime_end_time,"yyyy-mm-dd hh:MM:ss")))])],1)],1),i("v-uni-view",{staticClass:"con_c bottom"},[i("v-uni-view",{staticClass:"tle"},[t._v("合成材料")]),i("v-uni-view",{staticClass:"spc"},t._l(t.info.counts,(function(n,a){return i("v-uni-view",{staticClass:"spc_a",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.goPage("/pagesA/index/coll_list?id="+t.id+"&goods_id="+n.goods_id+"&count="+n.count+"&index="+a)}}},[i("v-uni-image",{staticClass:"img_a",attrs:{src:n.goods_thumb,mode:""}}),i("v-uni-view",{class:t.arr[a].length<n.count?"acts spc_b":"spc_b"},[i("v-uni-view",{staticClass:"fe"},[t._v(t._s(t.arr[a].length)+"/"+t._s(n.count))]),t.arr[a].length<n.count?i("v-uni-view",{staticClass:"plus"},[i("u-icon",{attrs:{name:"plus-circle",color:"#ffffff",size:"27"}}),i("v-uni-view",[t._v("点击填入")])],1):t._e()],1)],1)})),1)],1),t.content?i("v-uni-view",{staticClass:"con_d bottom"},[i("v-uni-view",{staticClass:"tle"},[t._v("合成详情")]),i("u-parse",{attrs:{content:t.content}})],1):t._e()],1),i("v-uni-view",{staticClass:"bot"},[i("v-uni-view",{staticClass:"sub",on:{click:function(n){arguments[0]=n=t.$handleEvent(n),t.Hecheng()}}},[t._v("立即合成")])],1)],1)},o=[]},"181a":function(t,n,i){"use strict";var a=i("ead1"),e=i.n(a);e.a},5573:function(t,n,i){"use strict";i("7a82"),Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0,i("d3b7"),i("159b"),i("14d9");var a={data:function(){return{arr:[],info:"",content:""}},onShow:function(){this.$forceUpdate()},onLoad:function(t){this.id=t.id,this.getDetail()},methods:{Hecheng:function(){var t=this,n=[];this.arr.forEach((function(t){t.length>=1&&t.forEach((function(t,i){n.push(t)}))})),n=n.join(","),this.request("/api/activity.synthesis/synthetic",{id:this.id,material_id:n}).then((function(i){t.$tip(i.data.msg),1==i.data.code?setTimeout((function(){uni.reLaunch({url:"/pagesB/personal/cangpin"})}),1e3):(t.arr=[],n=[],setTimeout((function(){t.getDetail()}),500))}))},goPage:function(t){uni.navigateTo({url:t})},getDetail:function(){var t=this;this.request("/api/activity.synthesis/getDetails",{id:this.id}).then((function(n){if(1==n.data.code){t.info=n.data.data;var i=n.data.data.counts;t.content=n.data.data.content,i.forEach((function(n){t.arr.push("")}))}}))}}};n.default=a},7679:function(t,n,i){var a=i("24fb");n=a(!1),n.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.acts[data-v-76d843c8]{position:absolute;top:0;left:0;width:100%;height:100%;border-radius:%?20?%;background:rgba(0,0,0,.7)}.bot[data-v-76d843c8]{position:fixed;bottom:0;left:0;background:#fff;height:%?98?%;width:92%;padding:0 4%;display:flex;justify-content:space-between;align-items:center}.bot .sub[data-v-76d843c8]{width:100%;height:%?80?%;text-align:center;line-height:%?80?%;font-size:%?32?%;font-weight:600;background:linear-gradient(0deg,#0f2cf9,#2570fc);color:#fff;border-radius:%?40?%}.content[data-v-76d843c8]{padding-bottom:%?98?%}.content .bottom[data-v-76d843c8]{border-bottom:%?18?% solid #f8f8f8}.content .tle[data-v-76d843c8]{padding:%?28?% 0 %?24?%;font-weight:700;color:#323232}.content .con_a[data-v-76d843c8]{padding:%?30?% 4%}.content .con_a .swiper[data-v-76d843c8] .uni-swiper-wrapper{border-radius:%?26?%}.content .con_a uni-image[data-v-76d843c8]{width:100%;height:%?688?%;border-radius:%?26?%}.content .con_b[data-v-76d843c8]{padding:0 4% %?32?%}.content .con_b .name[data-v-76d843c8]{font-size:%?32?%;font-weight:600;color:#323232}.content .con_b .gro[data-v-76d843c8]{display:flex;justify-content:flex-start;align-items:center}.content .con_b .gro .drop[data-v-76d843c8]{width:%?10?%;height:%?10?%;background:linear-gradient(0deg,#0f2cf9,#2570fc);border-radius:50%;margin-right:%?12?%}.content .con_b .gro uni-view[data-v-76d843c8]{font-size:%?22?%;color:#323232}.content .con_b .mag[data-v-76d843c8]{margin:%?10?% 0}.content .con_c[data-v-76d843c8]{padding:0 4% %?36?%}.content .con_c .spc[data-v-76d843c8]{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap}.content .con_c .spc .spc_a[data-v-76d843c8]{margin-bottom:%?20?%;position:relative;width:%?334?%;height:%?332?%;border-radius:%?20?%}.content .con_c .spc .spc_a .img_a[data-v-76d843c8]{width:%?334?%;height:%?332?%;border-radius:%?20?%}.content .con_c .spc .spc_a .spc_b .fe[data-v-76d843c8]{position:absolute;top:%?18?%;left:%?26?%;width:%?86?%;height:%?40?%;background:linear-gradient(0deg,#0f2cf9,#2570fc);border-radius:%?20?%;text-align:center;line-height:%?40?%;font-size:%?24?%;color:#fff}.content .con_c .spc .spc_a .spc_b .plus[data-v-76d843c8]{position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);text-align:center}.content .con_c .spc .spc_a .spc_b .plus uni-view[data-v-76d843c8]{margin-top:%?12?%;font-size:%?28?%;color:#fff}.content .con_d[data-v-76d843c8]{padding:0 4%}',""]),t.exports=n},bde4:function(t,n,i){"use strict";i.r(n);var a=i("5573"),e=i.n(a);for(var o in a)["default"].indexOf(o)<0&&function(t){i.d(n,t,(function(){return a[t]}))}(o);n["default"]=e.a},bfd5:function(t,n,i){"use strict";i.r(n);var a=i("1570"),e=i("bde4");for(var o in e)["default"].indexOf(o)<0&&function(t){i.d(n,t,(function(){return e[t]}))}(o);i("181a");var c=i("f0c5"),s=Object(c["a"])(e["default"],a["b"],a["c"],!1,null,"76d843c8",null,!1,a["a"],void 0);n["default"]=s.exports},ead1:function(t,n,i){var a=i("7679");a.__esModule&&(a=a.default),"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var e=i("4f06").default;e("2d0e567a",a,!0,{sourceMap:!1,shadowMode:!1})}}]);