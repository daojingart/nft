(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-login-resiger"],{"041e":function(t,e,n){"use strict";var i=n("7e3e"),a=n.n(i);a.a},"080a":function(t,e,n){"use strict";n.r(e);var i=n("77e5"),a=n.n(i);for(var s in i)["default"].indexOf(s)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(s);e["default"]=a.a},"1de5":function(t,e,n){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"30f0":function(t,e,n){var i=n("24fb"),a=n("1de5"),s=n("63d8");e=i(!1);var r=a(s);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.net_btn[data-v-1ca6cc94]{margin-top:%?90?%;height:%?110?%;text-align:center;line-height:%?110?%;font-size:%?35?%;color:#fff;font-weight:600;background:linear-gradient(108deg,#0f2cf9,#2570fc);border-radius:%?55?%}.message[data-v-1ca6cc94]{padding:%?30?%;border:%?2?% solid #a3a3a3;border-radius:%?16?%;margin-bottom:%?30?%;display:flex;align-items:center;justify-content:space-between}.forget[data-v-1ca6cc94]{text-align:right;font-size:%?28?%;color:#999}.phone[data-v-1ca6cc94]{padding:%?30?%;border:%?2?% solid #a3a3a3;border-radius:%?16?%;margin-bottom:%?30?%}.resig[data-v-1ca6cc94]{padding:%?30?% %?0?%;font-size:%?40?%;font-weight:600;background:url('+r+");background-size:100% 100%}.content[data-v-1ca6cc94]{padding:%?65?% %?75?%}",""]),t.exports=e},"63d8":function(t,e,n){t.exports=n.p+"static/img/bac_two.3349f60e.png"},"77e5":function(t,e,n){"use strict";n("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("e25e");var i={data:function(){return{nums:"",ticket:"",randstr:"",Pid:"",phone:"",sms_code:"",time:60,timer:null,title:"发送"}},onShow:function(){},onLoad:function(){this.getAPPID(),setTimeout((function(){var t=document.createElement("script");t.src="https://ssl.captcha.qq.com/TCaptcha.js",document.head.appendChild(t)}),1e3)},methods:{goSiven:function(){var t=["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"];this.nums="";for(var e=0;e<32;e++){var n=parseInt(61*Math.random());this.nums+=t[n]}uni.setStorageSync("TOKEN",this.nums),uni.navigateTo({url:"/pages/login/test_login"})},getAPPID:function(){var t=this;this.request("/api/index/getCaptchaAppId").then((function(e){1==e.data.code?(t.Pid=e.data.data.appid,uni.setStorageSync("Pid",t.Pid)):t.$tip(e.data.msg)}))},net_Code:function(){if(""!=this.phone){var t=this,e=t.Pid,n=new TencentCaptcha(e,(function(e){0==e.ret&&(t.ticket=e.ticket,t.randstr=e.randstr,t.Submit(),t.current)}));n.show()}else this.$tip("请输入手机号")},Submit:function(){var t=this;this.request("/api/Sms/behaviorVerificationCode",{ticket:this.ticket,randstr:this.randstr,scenes_type:"sms"}).then((function(e){1==e.data.code?t.getCode():t.$tip(e.data.msg)}))},getCode:function(){var t=this;"发送"==this.title&&this.request("/api/Sms/sendSms",{phone:this.phone,sendType:"register"},"GET").then((function(e){1==e.data.code?(t.$tip(e.data.msg),t.timer=setInterval((function(){t.time>=1?(t.time--,t.title=t.time+"s"):(clearInterval(t.timer),t.title="发送",t.time=60)}),1e3)):t.$tip(e.data.msg)}))},goPage:function(){if(""!=this.phone&&""!=this.sms_code){uni.navigateTo({url:"/pages/login/resiger_two?phone="+this.phone+"&sms_code="+this.sms_code})}else this.$tip("请先完善手机号和验证码")},Send:function(){var t=this;""!=this.phone?this.time<60||(this.timer=setInterval((function(){t.time>=1?(t.time--,t.title=t.time+"s"):(clearInterval(t.timer),t.title="发送验证码",t.time=60)}),1e3)):this.$tip("请填写手机号")},Change:function(t){this.current=t}}};e.default=i},"7e3e":function(t,e,n){var i=n("30f0");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var a=n("4f06").default;a("505772a3",i,!0,{sourceMap:!1,shadowMode:!1})},c53c:function(t,e,n){"use strict";n.r(e);var i=n("e388"),a=n("080a");for(var s in a)["default"].indexOf(s)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(s);n("041e");var r=n("f0c5"),o=Object(r["a"])(a["default"],i["b"],i["c"],!1,null,"1ca6cc94",null,!1,i["a"],void 0);e["default"]=o.exports},e388:function(t,e,n){"use strict";n.d(e,"b",(function(){return i})),n.d(e,"c",(function(){return a})),n.d(e,"a",(function(){}));var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",[n("Hearder",{attrs:{name:""}}),n("v-uni-view",{staticClass:"content"},[n("v-uni-view",{staticStyle:{display:"flex","margin-bottom":"50rpx"}},[n("v-uni-view",{staticClass:"resig"},[t._v("账号注册")])],1),n("v-uni-view",{staticClass:"phone"},[n("v-uni-input",{attrs:{type:"text",placeholder:"请输入手机号"},model:{value:t.phone,callback:function(e){t.phone=e},expression:"phone"}})],1),n("v-uni-view",{staticClass:"message"},[n("v-uni-input",{attrs:{type:"text",placeholder:"请输入验证码"},model:{value:t.sms_code,callback:function(e){t.sms_code=e},expression:"sms_code"}}),n("v-uni-view",{on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.net_Code()}}},[t._v(t._s(t.title))])],1),n("v-uni-view",{staticClass:"net_btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.goPage()}}},[t._v("下一步")])],1)],1)},a=[]}}]);