(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesB-set-shangchengdingdan"],{"093f":function(t,e,n){"use strict";n.r(e);var i=n("2833"),a=n.n(i);for(var r in i)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(r);e["default"]=a.a},"12fd":function(t,e,n){"use strict";var i=n("731a"),a=n.n(i);a.a},"130a":function(t,e,n){"use strict";function i(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:2,n="".concat(t);while(n.length<e)n="0".concat(n);return n}n("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.isSameSecond=function(t,e){return Math.floor(t/1e3)===Math.floor(e/1e3)},e.parseFormat=function(t,e){var n=e.days,a=e.hours,r=e.minutes,o=e.seconds,s=e.milliseconds;-1===t.indexOf("DD")?a+=24*n:t=t.replace("DD",i(n));-1===t.indexOf("HH")?r+=60*a:t=t.replace("HH",i(a));-1===t.indexOf("mm")?o+=60*r:t=t.replace("mm",i(r));-1===t.indexOf("ss")?s+=1e3*o:t=t.replace("ss",i(o));return t.replace("SSS",i(s,3))},e.parseTimeData=function(t){var e=Math.floor(t/864e5),n=Math.floor(t%864e5/36e5),i=Math.floor(t%36e5/6e4),a=Math.floor(t%6e4/1e3),r=Math.floor(t%1e3);return{days:e,hours:n,minutes:i,seconds:a,milliseconds:r}},n("c975"),n("ac1f"),n("5319")},2833:function(t,e,n){"use strict";n("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("e25e"),n("99af");var i={data:function(){return{current:1,opacitynum:100,bar_list:["全部","待支付","已完成","已取消"],timeData:[],catelist:[],loadStatus:"more",showNoList:!1,pageInfo:{page:1,per_page:10,last_page:1,total:0}}},onReachBottom:function(){"noMore"!=this.loadStatus&&(this.pageInfo.page++,this.loadStatus="loading",this.getData())},onLoad:function(t){this.current=t.current},onShow:function(){this.restData()},methods:{Cencle:function(t){var e=this;this.request("/api/member.order/cancel",{order_id:t}).then((function(t){e.$tip(t.data.msg),1==t.data.code&&e.restData()}))},restData:function(){this.catelist=[],this.pageInfo.page=1,this.loadStatus="more",this.getData()},getData:function(){var t=this;this.request("/api/member.order/orderList",{type:this.current,page:this.pageInfo.page}).then((function(e){if(1==e.data.code){0==e.data.data.list.length?t.loadStatus="noMore":1==parseInt(t.pageInfo.page)?(t.catelist=e.data.data.list,e.data.data.list.length<10&&(t.loadStatus="noMore")):t.catelist=t.catelist.concat(e.data.data.list);for(var n=0;n<t.catelist.length;n++);}}))},goPage:function(t){uni.navigateTo({url:t})},ChangeCurrent:function(t){this.current=t,this.restData()},onChange:function(t,e){this.timeData[e]=t}}};e.default=i},"2fd4":function(t,e,n){var i=n("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.qiehuan[data-v-b992b44e]{position:fixed;width:100%;height:%?80?%;background-color:#fff;z-index:8}.yiqv[data-v-b992b44e]{margin-top:%?24?%;padding-top:%?28?%;padding-left:%?26?%;padding-right:%?24?%;border-top:%?20?% solid #f7f7f7}.yiqv .yihang[data-v-b992b44e]{display:flex;border-bottom:%?1?% dashed #eee;padding-bottom:%?31?%;justify-content:space-between;flex-wrap:nowrap;align-items:center}.yiqv .yihang .yihang_left[data-v-b992b44e]{font-size:%?28?%;font-family:PingFang SC;font-weight:700;color:#323232}.yiqv .yihang .txt_beifen[data-v-b992b44e]{font-size:%?28?%;font-family:PingFang SC;font-weight:400;color:#a3a3a3}.yiqv .rig_yihang[data-v-b992b44e]{display:flex;align-items:center;margin-top:%?30?%}.yiqv .rig_yihang .img_rig[data-v-b992b44e]{font-size:%?26?%;font-family:PingFang SC;font-weight:400;color:#a3a3a3}.yiqv .touxiang[data-v-b992b44e]{height:%?31?%;width:%?31?%;border-radius:%?15?%;margin-right:%?14?%}.yiqv .time[data-v-b992b44e]{display:flex;flex-direction:row;align-items:center}.yiqv .time__custom[data-v-b992b44e]{margin-top:4px;width:22px;height:22px;background-color:initial;border:%?3?% solid #e3e3e3;border-radius:4px;display:flex;justify-content:center;align-items:center}.yiqv .time__custom__item[data-v-b992b44e]{color:#7f7e91;text-align:center;font-size:%?28?%;font-family:PingFang SC}.yiqv .time__doc[data-v-b992b44e]{color:#3c9cff;padding:0 4px}.yiqv .time__item[data-v-b992b44e]{color:#606266;font-size:15px;margin-right:4px}.erhang[data-v-b992b44e]{padding-top:%?20?%;display:flex;justify-content:space-between}.erhang .img[data-v-b992b44e]{height:%?160?%;width:%?160?%;border-radius:%?20?%}.erhang .txt[data-v-b992b44e]{display:flex;flex-flow:column}.erhang .txt1[data-v-b992b44e]{width:%?518?%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:%?26?%;font-family:PingFang SC;font-weight:400;color:#323232}.erhang .txt2[data-v-b992b44e]{display:flex;justify-content:space-between;align-items:center;margin-top:%?37?%}.erhang .txt2 .txt2_lef[data-v-b992b44e]{font-size:%?22?%;font-family:PingFang SC;color:#323232}.erhang .txt2 .txt2_rig[data-v-b992b44e]{font-size:%?28?%}.sanhang[data-v-b992b44e]{display:flex;justify-content:space-between;margin-top:%?40?%}.sanhang .san_txt1[data-v-b992b44e]{font-size:%?28?%;font-family:PingFang SC;font-weight:700;color:#323232}.sanhang .sanhang_btn[data-v-b992b44e]{display:flex}.sanhang .sanhang_btn .san_btnl[data-v-b992b44e]{width:%?160?%;height:%?60?%;border:%?1?% solid #b2b1c1;border-radius:%?30?%;text-align:center;line-height:%?60?%;color:#b2b1c1;font-size:%?26?%;margin-right:%?20?%}.sanhang .sanhang_btn .san_btnr[data-v-b992b44e]{width:%?160?%;height:%?60?%;background:linear-gradient(112deg,#0f2cf9,#2570fc);border-radius:%?30?%;text-align:center;line-height:%?60?%;font-size:%?26?%;color:#fff}.yangshi2[data-v-b992b44e]{display:flex}.bianhao[data-v-b992b44e]{font-size:%?28?%;font-family:PingFang SC;font-weight:400;color:#7f7e91;margin-left:%?230?%}.txt_left[data-v-b992b44e]{padding:%?0?% %?18?%;border:%?3?% solid #0f2cf9;text-align:center;line-height:%?40?%;font-size:%?22?%}.tit_bar[data-v-b992b44e]{margin:%?20?% %?57?% 0 %?55?%;display:flex;justify-content:space-between}.tit_bar > uni-view[data-v-b992b44e]{font-size:%?28?%;color:#a3a3a3;font-weight:600}.tit_bar .active[data-v-b992b44e]{color:#000!important;font-weight:600;font-size:%?28?%;position:relative}.tit_bar .active[data-v-b992b44e]::before{content:"";position:absolute;bottom:%?-20?%;width:%?40?%;height:%?8?%;background-color:#0f2cf9;border-radius:.2rem;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}',""]),t.exports=e},"366b":function(t,e,n){"use strict";n.r(e);var i=n("3c85"),a=n.n(i);for(var r in i)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(r);e["default"]=a.a},"3c85":function(t,e,n){"use strict";n("7a82");var i=n("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("d401"),n("d3b7"),n("25f0");var a=i(n("52e5")),r=n("130a"),o={name:"u-count-down",mixins:[uni.$u.mpMixin,uni.$u.mixin,a.default],data:function(){return{timer:null,timeData:(0,r.parseTimeData)(0),formattedTime:"0",runing:!1,endTime:0,remainTime:0}},watch:{time:function(t){this.reset()}},mounted:function(){this.init()},methods:{init:function(){this.reset()},start:function(){this.runing||(this.runing=!0,this.endTime=Date.now()+this.remainTime,this.toTick())},toTick:function(){this.millisecond?this.microTick():this.macroTick()},macroTick:function(){var t=this;this.clearTimeout(),this.timer=setTimeout((function(){var e=t.getRemainTime();(0,r.isSameSecond)(e,t.remainTime)&&0!==e||t.setRemainTime(e),0!==t.remainTime&&t.macroTick()}),30)},microTick:function(){var t=this;this.clearTimeout(),this.timer=setTimeout((function(){t.setRemainTime(t.getRemainTime()),0!==t.remainTime&&t.microTick()}),50)},getRemainTime:function(){return Math.max(this.endTime-Date.now(),0)},setRemainTime:function(t){this.remainTime=t;var e=(0,r.parseTimeData)(t);this.$emit("change",e),this.formattedTime=(0,r.parseFormat)(this.format,e),t<=0&&(this.pause(),this.$emit("finish"))},reset:function(){this.pause(),this.remainTime=this.time,this.setRemainTime(this.remainTime),this.autoStart&&this.start()},pause:function(){this.runing=!1,this.clearTimeout()},clearTimeout:function(t){function e(){return t.apply(this,arguments)}return e.toString=function(){return t.toString()},e}((function(){clearTimeout(this.timer),this.timer=null}))},beforeDestroy:function(){this.clearTimeout()}};e.default=o},5039:function(t,e,n){var i=n("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */uni-view[data-v-9803901c], uni-scroll-view[data-v-9803901c], uni-swiper-item[data-v-9803901c]{display:flex;flex-direction:column;flex-shrink:0;flex-grow:0;flex-basis:auto;align-items:stretch;align-content:flex-start}.u-count-down__text[data-v-9803901c]{color:#606266;font-size:15px;line-height:22px}',""]),t.exports=e},"52e5":function(t,e,n){"use strict";n("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("a9e3");var i={props:{time:{type:[String,Number],default:uni.$u.props.countDown.time},format:{type:String,default:uni.$u.props.countDown.format},autoStart:{type:Boolean,default:uni.$u.props.countDown.autoStart},millisecond:{type:Boolean,default:uni.$u.props.countDown.millisecond}}};e.default=i},"6df8":function(t,e,n){"use strict";n.d(e,"b",(function(){return i})),n.d(e,"c",(function(){return a})),n.d(e,"a",(function(){}));var i=function(){var t=this.$createElement,e=this._self._c||t;return e("v-uni-view",{staticClass:"u-count-down"},[this._t("default",[e("v-uni-text",{staticClass:"u-count-down__text"},[this._v(this._s(this.formattedTime))])])],2)},a=[]},"731a":function(t,e,n){var i=n("2fd4");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var a=n("4f06").default;a("069c68a2",i,!0,{sourceMap:!1,shadowMode:!1})},aab0:function(t,e,n){"use strict";var i=n("e335"),a=n.n(i);a.a},d61e:function(t,e,n){"use strict";n.r(e);var i=n("6df8"),a=n("366b");for(var r in a)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(r);n("aab0");var o=n("f0c5"),s=Object(o["a"])(a["default"],i["b"],i["c"],!1,null,"9803901c",null,!1,i["a"],void 0);e["default"]=s.exports},e335:function(t,e,n){var i=n("5039");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var a=n("4f06").default;a("49067033",i,!0,{sourceMap:!1,shadowMode:!1})},e39b:function(t,e,n){"use strict";n.d(e,"b",(function(){return a})),n.d(e,"c",(function(){return r})),n.d(e,"a",(function(){return i}));var i={uCountDown:n("d61e").default},a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",[n("Hearder",{attrs:{name:"购买订单"}}),n("v-uni-view",{staticClass:"qiehuan"},[n("v-uni-view",{staticClass:"tit_bar"},t._l(t.bar_list,(function(e,i){return n("v-uni-view",{class:t.current==i+1?"active":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.ChangeCurrent(i+1)}}},[t._v(t._s(e))])})),1)],1),n("v-uni-view",{staticStyle:{height:"50rpx"}}),t._l(t.catelist,(function(e,i){return n("v-uni-view",{staticClass:"yiqv",on:{click:function(n){arguments[0]=n=t.$handleEvent(n),t.goPage("/pagesB/set/goumailistdetail?id="+e.order_id)}}},[n("v-uni-view",{staticClass:"yihang"},[n("v-uni-view",{staticClass:"yihang_left"},[t._v(t._s(1==e.order_status.value?"待支付":4==e.order_status.value?"已完成":2==e.order_status.value?"已支付":3==e.order_status.value?"进行中":"已取消"))]),e.end_time>e.now_time&&1==e.order_status.value?n("v-uni-view",[n("u-count-down",{ref:"countDown",refInFor:!0,attrs:{time:1e3*(e.end_time-e.now_time),format:"HH:mm:ss",autoStart:!0,millisecond:!0},on:{change:function(e){arguments[0]=e=t.$handleEvent(e),t.onChange(e,i)}}})],1):t._e(),1!=e.order_status.value?n("v-uni-view",{staticClass:"txt_beifen"},[t._v("订单编号:"+t._s(e.order_no))]):t._e()],1),n("v-uni-view",{staticClass:"erhang"},[n("v-uni-image",{staticClass:"img",attrs:{src:e.goods[0].goods_image}}),n("v-uni-view",{staticClass:"txt"},[n("v-uni-view",{staticClass:"txt1"},[t._v(t._s(e.goods[0].goods_name))]),n("v-uni-view",{staticClass:"rig_yihang"}),n("v-uni-view",{staticClass:"txt2"},[n("v-uni-view",{staticClass:"san_txt1"},[t._v("合计:￥"+t._s(e.pay_price))])],1)],1)],1),n("v-uni-view",{staticClass:"sanhang",staticStyle:{"justify-content":"flex-end"}},[n("v-uni-view",{staticClass:"sanhang_btn"},[1==e.order_status.value?n("v-uni-view",{staticClass:"san_btnl",on:{click:function(n){n.stopPropagation(),arguments[0]=n=t.$handleEvent(n),t.Cencle(e.order_id)}}},[t._v("取消订单")]):t._e(),1==e.order_status.value?n("v-uni-view",{staticClass:"san_btnr",on:{click:function(n){n.stopPropagation(),arguments[0]=n=t.$handleEvent(n),t.goPage("/pagesA/market/cashier?order_id="+e.order_id)}}},[t._v("立即支付")]):t._e()],1)],1)],1)})),n("NoList",{attrs:{cList:t.catelist}})],2)},r=[]},f027:function(t,e,n){"use strict";n.r(e);var i=n("e39b"),a=n("093f");for(var r in a)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(r);n("12fd");var o=n("f0c5"),s=Object(o["a"])(a["default"],i["b"],i["c"],!1,null,"b992b44e",null,!1,i["a"],void 0);e["default"]=s.exports}}]);