(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-login-resiger_two"],{"0203":function(t,e,n){"use strict";n("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={data:function(){return{code:"",phone:"",sms_code:"",password:"",repeat_password:"",invitation_code:"",time:60,timer:null,placeholder:"",InvitCode:"",title:"发送"}},onLoad:function(t){uni.getStorageSync("code")&&(this.invitation_code=uni.getStorageSync("code")),this.phone=t.phone,this.sms_code=t.sms_code,this.getDetails()},methods:{getDetails:function(){var t=this;this.request("/api/index/getIsInvitationCode").then((function(e){1==e.data.code&&(t.InvitCode=e.data.data)}))},Resign:function(){var t=this;this.request("/api/member.member/register",{phone:this.phone,sms_code:this.sms_code,password:this.password,repeat_password:this.repeat_password,invitation_code:this.invitation_code}).then((function(e){1==e.data.code?(t.$tip(e.data.msg),uni.setStorageSync("TOKEN",e.data.data.userinfo.token),uni.setStorageSync("phone",e.data.data.userinfo.phone),uni.setStorageSync("member_id",e.data.data.userinfo.code),setTimeout((function(){uni.switchTab({url:"/pages/main/index"})}),1e3)):t.$tip(e.data.msg)}))},goPage:function(t){uni.navigateTo({url:t})},Send:function(){var t=this;this.time<60||(this.timer=setInterval((function(){t.time>=1?(t.time--,t.title=t.time+"s"):(clearInterval(t.timer),t.title="发送验证码",t.time=60)}),1e3))},Change:function(t){this.current=t}}};e.default=a},"1de5":function(t,e,n){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"311d":function(t,e,n){"use strict";n.r(e);var a=n("0203"),i=n.n(a);for(var o in a)["default"].indexOf(o)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(o);e["default"]=i.a},5817:function(t,e,n){"use strict";n.d(e,"b",(function(){return a})),n.d(e,"c",(function(){return i})),n.d(e,"a",(function(){}));var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",[n("Hearder",{attrs:{name:""}}),n("v-uni-view",{staticClass:"content"},[n("v-uni-view",{staticStyle:{display:"flex","margin-bottom":"50rpx"}},[n("v-uni-view",{staticClass:"resig"},[t._v("账号注册")])],1),n("v-uni-view",{staticClass:"phone"},[n("v-uni-input",{attrs:{type:"text",placeholder:"输入密码"},model:{value:t.password,callback:function(e){t.password=e},expression:"password"}})],1),n("v-uni-view",{staticClass:"phone"},[n("v-uni-input",{attrs:{type:"text",placeholder:"重复密码"},model:{value:t.repeat_password,callback:function(e){t.repeat_password=e},expression:"repeat_password"}})],1),10==t.InvitCode?n("v-uni-view",{staticClass:"phone"},[n("v-uni-input",{attrs:{type:"text",placeholder:"请输入邀请码(必填)"},model:{value:t.invitation_code,callback:function(e){t.invitation_code=e},expression:"invitation_code"}})],1):t._e(),n("v-uni-view",{staticClass:"net_btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Resign()}}},[t._v("注册并登录")])],1)],1)},i=[]},6890:function(t,e,n){var a=n("24fb"),i=n("1de5"),o=n("c80d");e=a(!1);var r=i(o);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.net_btn[data-v-a9ab747a]{margin-top:%?90?%;height:%?110?%;text-align:center;line-height:%?110?%;font-size:%?35?%;color:#fff;font-weight:600;background:linear-gradient(108deg,#0f2cf9,#2570fc);border-radius:%?55?%}.message[data-v-a9ab747a]{padding:%?30?%;border:%?2?% solid #a3a3a3;border-radius:%?16?%;margin-bottom:%?30?%;display:flex;align-items:center;justify-content:space-between}.forget[data-v-a9ab747a]{text-align:right;font-size:%?28?%;color:#999}.phone[data-v-a9ab747a]{padding:%?30?%;border:%?2?% solid #a3a3a3;border-radius:%?16?%;margin-bottom:%?30?%}.resig[data-v-a9ab747a]{padding:%?30?% %?0?%;font-size:%?40?%;font-weight:600;background:url('+r+");background-size:100% 100%}.content[data-v-a9ab747a]{padding:%?65?% %?75?%}",""]),t.exports=e},"72a4":function(t,e,n){var a=n("6890");a.__esModule&&(a=a.default),"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var i=n("4f06").default;i("187ead82",a,!0,{sourceMap:!1,shadowMode:!1})},9504:function(t,e,n){"use strict";var a=n("72a4"),i=n.n(a);i.a},c78a:function(t,e,n){"use strict";n.r(e);var a=n("5817"),i=n("311d");for(var o in i)["default"].indexOf(o)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(o);n("9504");var r=n("f0c5"),s=Object(r["a"])(i["default"],a["b"],a["c"],!1,null,"a9ab747a",null,!1,a["a"],void 0);e["default"]=s.exports},c80d:function(t,e,n){t.exports=n.p+"static/img/bac_two.3349f60e.png"}}]);