(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-personal-set_Phone_two"],{"438d":function(t,e,n){"use strict";n.d(e,"b",(function(){return i})),n.d(e,"c",(function(){return a})),n.d(e,"a",(function(){}));var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",[n("Hearder",{attrs:{name:"修改手机号"}}),n("v-uni-view",{staticClass:"content"},[n("v-uni-view",[t._v("修改手机号")]),n("v-uni-view",[t._v("点击发送验证码至"),n("span"),t._v("手机，输入验证码并绑定")]),n("v-uni-view",[t._v("手机号")]),n("v-uni-view",[n("v-uni-input",{attrs:{type:"text",placeholder:"请输入手机号"},model:{value:t.phone,callback:function(e){t.phone=e},expression:"phone"}})],1),n("v-uni-view",[t._v("验证码")]),n("v-uni-view",[n("v-uni-input",{attrs:{type:"text",placeholder:"请输入验证码"},model:{value:t.sms_code,callback:function(e){t.sms_code=e},expression:"sms_code"}}),n("v-uni-view",{on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.net_Code()}}},[t._v(t._s(t.title))])],1)],1),n("v-uni-view",{staticClass:"btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Sure()}}},[t._v("确定绑定")])],1)},a=[]},6488:function(t,e,n){"use strict";n("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={data:function(){return{nums:"",ticket:"",randstr:"",Pid:"",sms_code:"",phone:"",time:60,timer:null,title:"发送"}},onShow:function(){},onLoad:function(){this.getAPPID(),setTimeout((function(){var t=document.createElement("script");t.src="https://ssl.captcha.qq.com/TCaptcha.js",document.head.appendChild(t)}),1e3)},methods:{goSiven:function(){uni.navigateTo({url:"/pages/login/test_login"})},getAPPID:function(){var t=this;this.request("/api/index/getCaptchaAppId").then((function(e){1==e.data.code?(t.Pid=e.data.data.appid,uni.setStorageSync("Pid",t.Pid)):t.$tip(e.data.msg)}))},net_Code:function(){if(""!=this.phone){var t=this,e=t.Pid,n=new TencentCaptcha(e,(function(e){0==e.ret&&(t.ticket=e.ticket,t.randstr=e.randstr,t.Submit(),t.current)}));n.show()}else this.$tip("请输入手机号")},Submit:function(){var t=this;this.request("/api/Sms/behaviorVerificationCode",{ticket:this.ticket,randstr:this.randstr,scenes_type:"sms"}).then((function(e){1==e.data.code?t.getCode():t.$tip(e.data.msg)}))},getCode:function(){var t=this;"发送"==this.title&&this.request("/api/Sms/sendSms",{phone:this.phone,sendType:"register"},"GET").then((function(e){1==e.data.code?(t.$tip(e.data.msg),t.timer=setInterval((function(){t.time>=1?(t.time--,t.title=t.time+"s"):(clearInterval(t.timer),t.title="发送",t.time=60)}),1e3)):t.$tip(e.data.msg)}))},Sure:function(){var t=this;this.request("/api/member.member/replacePhone",{sms_code:this.sms_code,phone:this.phone}).then((function(e){t.$tip(e.data.msg),1==e.data.data&&setTimeout((function(){uni.navigateBack()}),1e3)}))},Send:function(){var t=this;this.time<60||(this.timer=setInterval((function(){t.time>=1?(t.time--,t.title=t.time+"s后重新发送"):(clearInterval(t.timer),t.title="发送验证码",t.time=60)}),1e3))}}};e.default=i},"9a0cc":function(t,e,n){var i=n("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.btn[data-v-1e6ffaf4]{background:linear-gradient(0deg,#0f2cf9,#2570fc);position:fixed;bottom:%?40?%;left:%?30?%;font-size:%?34?%;width:%?690?%;height:%?110?%;border-radius:%?55?%;font-weight:600;text-align:center;color:#fff;line-height:%?110?%}.content[data-v-1e6ffaf4]{padding:%?50?% %?30?%}.content > uni-view[data-v-1e6ffaf4]:nth-of-type(8){background:#f8f8f8;border-radius:%?13?%;margin-top:%?20?%}.content > uni-view:nth-of-type(8) > uni-input[data-v-1e6ffaf4]{padding:%?30?%}.content > uni-view:nth-of-type(8) > uni-input > uni-input[data-v-1e6ffaf4]{font-size:%?28?%;width:%?500?%}.content > uni-view[data-v-1e6ffaf4]:nth-of-type(6){margin-top:%?30?%;display:flex;justify-content:space-between;align-items:center;padding:%?30?%;background:#f8f8f8;border-radius:%?13?%}.content > uni-view:nth-of-type(6) > uni-input[data-v-1e6ffaf4]{font-size:%?34?%}.content > uni-view:nth-of-type(6) > uni-view[data-v-1e6ffaf4]{font-size:%?26?%}.content > uni-view[data-v-1e6ffaf4]:nth-of-type(7){margin-top:%?40?%;font-size:%?26?%;font-weight:600}.content > uni-view[data-v-1e6ffaf4]:nth-of-type(5){margin-top:%?40?%;font-size:%?26?%;font-weight:600}.content > uni-view[data-v-1e6ffaf4]:nth-of-type(4){background:#f8f8f8;border-radius:%?13?%;margin-top:%?20?%}.content > uni-view:nth-of-type(4) > uni-input[data-v-1e6ffaf4]{padding:%?30?%}.content > uni-view:nth-of-type(4) > uni-input > uni-input[data-v-1e6ffaf4]{font-size:%?28?%;width:%?500?%}.content > uni-view[data-v-1e6ffaf4]:first-of-type{font-size:%?47?%;font-weight:600}.content > uni-view[data-v-1e6ffaf4]:nth-of-type(2){font-size:%?30?%;margin-top:%?50?%}.content > uni-view:nth-of-type(2) > span[data-v-1e6ffaf4]{color:#0af}.content > uni-view[data-v-1e6ffaf4]:nth-of-type(3){margin-top:%?115?%;font-size:%?26?%;font-weight:600}',""]),t.exports=e},beae:function(t,e,n){"use strict";n.r(e);var i=n("6488"),a=n.n(i);for(var o in i)["default"].indexOf(o)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(o);e["default"]=a.a},e52a:function(t,e,n){var i=n("9a0cc");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var a=n("4f06").default;a("33b1b3c2",i,!0,{sourceMap:!1,shadowMode:!1})},e65b:function(t,e,n){"use strict";n.r(e);var i=n("438d"),a=n("beae");for(var o in a)["default"].indexOf(o)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(o);n("fdb0");var f=n("f0c5"),r=Object(f["a"])(a["default"],i["b"],i["c"],!1,null,"1e6ffaf4",null,!1,i["a"],void 0);e["default"]=r.exports},fdb0:function(t,e,n){"use strict";var i=n("e52a"),a=n.n(i);a.a}}]);