(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesB-personal-poster"],{"20dd":function(t,e,a){"use strict";a.d(e,"b",(function(){return n})),a.d(e,"c",(function(){return o})),a.d(e,"a",(function(){return i}));var i={uPopup:a("5154").default,"u-Image":a("edb8").default,uLoadingIcon:a("2c87").default},n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",[a("Hearder",{attrs:{name:"邀请好友",opacitynum:t.opacitynum}}),a("v-uni-view",{staticClass:"tops"},[a("v-uni-view",{staticClass:"tops_head"},[a("v-uni-image",{attrs:{src:t.info.avatarUrl}}),a("v-uni-view",[a("v-uni-view",[t._v(t._s(t.poster_info.code))]),a("v-uni-view",[t._v("您的好友邀请码")])],1)],1)],1),a("v-uni-view",{staticClass:"cont"},[a("v-uni-view",{staticClass:"img_view"},[a("v-uni-image",{attrs:{src:t.poster_info.invitation,mode:"widthFix"}}),a("v-uni-view",{staticClass:"tacc"},[a("v-uni-view",{on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Copys()}}},[t._v("复制链接")]),a("v-uni-view",{on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Shows()}}},[t._v("立即邀请好友")])],1)],1)],1),a("v-uni-view",{staticClass:"sml_code",staticStyle:{position:"absolute",width:"100%",top:"-1000px"}},[a("v-uni-canvas",{staticClass:"canvas-code",attrs:{"canvas-id":"myQrcode"}})],1),a("u-popup",{attrs:{show:t.show,mode:"center"},on:{close:function(e){arguments[0]=e=t.$handleEvent(e),t.close.apply(void 0,arguments)}}},[a("v-uni-view",[a("v-uni-view",{staticClass:"poster_box"},[a("v-uni-canvas",{staticClass:"canvas-poster",style:{width:t.width+"px",height:t.height+"px"},attrs:{id:"canvasPoster","canvas-id":"canvasPoster"}}),a("u--image",{staticStyle:{"margin-top":"-170rpx"},attrs:{src:t.poster,width:"642rpx",height:"1000rpx",radius:"12rpx"},scopedSlots:t._u([{key:"loading",fn:function(){return[a("u-loading-icon",{attrs:{color:"red"}})]},proxy:!0}])})],1)],1)],1)],1)},o=[]},"3e89":function(t,e,a){"use strict";var i=a("6f00"),n=a.n(i);n.a},"6f00":function(t,e,a){var i=a("b753");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("4f06").default;n("6213309e",i,!0,{sourceMap:!1,shadowMode:!1})},"8c5c":function(t,e,a){"use strict";a.r(e);var i=a("f5aa"),n=a.n(i);for(var o in i)["default"].indexOf(o)<0&&function(t){a.d(e,t,(function(){return i[t]}))}(o);e["default"]=n.a},b753:function(t,e,a){var i=a("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */[data-v-6ae12b50] .u-popup__content{background-color:initial!important}.poster_box[data-v-6ae12b50]{text-align:center}.poster_box .canvas-poster[data-v-6ae12b50]{position:absolute;width:100%;top:-1000px}.poster_box > uni-image[data-v-6ae12b50]{width:%?532?%;margin-top:%?260?%}.poster_box[data-v-6ae12b50] .u-image{margin:%?240?% auto 0}.img_view[data-v-6ae12b50]{text-align:center}.img_view .tacc[data-v-6ae12b50]{display:flex;justify-content:space-between;margin-top:%?60?%}.img_view .tacc > uni-view[data-v-6ae12b50]:first-of-type{width:%?264?%;height:%?90?%;text-align:center;line-height:%?90?%;border-radius:%?45?%;border:%?1?% solid #eee}.img_view .tacc > uni-view[data-v-6ae12b50]:nth-of-type(2){width:%?400?%;height:%?90?%;border-radius:%?45?%;color:#fff;text-align:center;line-height:%?90?%;background:linear-gradient(96deg,#0f2cf9,#2570fc)}.img_view > uni-image[data-v-6ae12b50]{width:100%}.cont[data-v-6ae12b50]{position:relative;z-index:6;padding:%?30?%;border-radius:%?16?% %?16?% %?0?% %?0?%;margin-top:%?-20?%;background:#fff}.tops[data-v-6ae12b50]{padding:%?130?% %?30?% %?70?% %?30?%;background:linear-gradient(0deg,#0f2cf9,#2570fc)}.tops .tops_head[data-v-6ae12b50]{display:flex;align-items:center}.tops .tops_head > uni-image[data-v-6ae12b50]{width:%?92?%;height:%?92?%;border-radius:50%;margin-right:%?20?%}.tops .tops_head > uni-view:first-of-type > uni-view[data-v-6ae12b50]:first-of-type{font-size:%?40?%;font-weight:600;color:#fff}.tops .tops_head > uni-view:first-of-type > uni-view[data-v-6ae12b50]:nth-of-type(2){font-size:%?26?%;color:#fff}',""]),t.exports=e},e9f1:function(t,e,a){"use strict";a.r(e);var i=a("20dd"),n=a("8c5c");for(var o in n)["default"].indexOf(o)<0&&function(t){a.d(e,t,(function(){return n[t]}))}(o);a("3e89");var s=a("f0c5"),r=Object(s["a"])(n["default"],i["b"],i["c"],!1,null,"6ae12b50",null,!1,i["a"],void 0);e["default"]=r.exports},f5aa:function(t,e,a){"use strict";(function(t){a("7a82");var i=a("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=i(a("ade3")),o=(a("dd29"),a("3b19")),s={data:function(){var t;return t={show:!1,opacitynum:0,image:"",img:[],shareInfo:"",urls:""},(0,n.default)(t,"opacitynum",0),(0,n.default)(t,"width",""),(0,n.default)(t,"height",""),(0,n.default)(t,"systemInfo",""),(0,n.default)(t,"poster",""),(0,n.default)(t,"userInfo",{link_url:"gagagaga",header_img:"../static/test_hb.png",username:"",ID:""}),(0,n.default)(t,"type",1),(0,n.default)(t,"info",""),(0,n.default)(t,"ios",0),(0,n.default)(t,"poster_info",""),t},onPageScroll:function(t){this.opacitynum=t.scrollTop},onLoad:function(){var t=this;this.request("/api/member.member/getPoster").then((function(e){1==e.data.code&&(t.poster_info=e.data.data)}));var e=this;uni.getSystemInfo({success:function(t){t.pixelRatio>2&&(t.pixelRatio=2),e.systemInfo=t,e.width=.7*t.windowWidth,e.height=1.5*e.width}}),this.getUserInfo();e=this;switch(uni.getSystemInfoSync().platform){case"ios":e.ios=1;break;default:e.ios=0;break}},methods:{getUserInfo:function(){var t=this;this.request("/api/member.member/getUserDetails").then((function(e){1==e.data.code&&(t.info=e.data.data)}))},Copys:function(){this.$Config.url;uni.setClipboardData({data:this.poster_info.code_url,success:function(){t("log","success"," at pagesB/personal/poster.vue:125")}})},Shows:function(){var t=this;this.show=!0;var e=this;setTimeout((function(){new o("myQrcode",{text:t.poster_info.code_url,width:150,height:150,padding:4,correctLevel:o.CorrectLevel.M,callback:function(t){var a=t.path;e.shareInfo={width:e.width,height:e.height,bgImg:e.poster_info.invitation,headerImg:"../static/test_hb.png",username:e.poster_info.system_name,yaoqing:"限时可得海量空投",qrcode:a},e.drawImage(e.shareInfo)}})}),200)},close:function(){this.show=!1},saveImage:function(){this.$tip("点击图片保存")},drawImage:function(e){var a=this,i=uni.createCanvasContext("canvasPoster",this),n=e.width,o=e.height,s=n/6,r=.04*n,c=.04*o,d=r+s+.03*n,u=c+.06*n,f=1.8*u;i.beginPath(),i.setFillStyle("white"),i.fillRect(0,0,n,o),i.drawImage(e.bgImg,0,0,n,o-90),i.save(),i.drawImage(e.qrcode,d-50,10.1*u,52.5,52.5),i.save(),i.restore(),i.save(),i.setFillStyle("black"),i.font="bold 15px PingFang SC",i.fillText(e.username,d+7,10.8*u),i.setFillStyle("black"),i.font="12px PingFang SC",i.fillText(e.yaoqing,d+7,6.4*f),i.draw(),setTimeout((function(){uni.canvasToTempFilePath({canvasId:"canvasPoster",width:n,height:o,destWidth:n*a.systemInfo.pixelRatio,destHeight:o*a.systemInfo.pixelRatio,success:function(t){a.poster=t.tempFilePath,uni.hideLoading()},fail:function(e){t("log",e," at pagesB/personal/poster.vue:285")}})}),200)}}};e.default=s}).call(this,a("0de9")["log"])}}]);