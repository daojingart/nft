(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-login-phoneLogin"],{"1d1f":function(t,e,n){"use strict";n.r(e);var a=n("e3f4"),i=n.n(a);for(var r in a)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(r);e["default"]=i.a},"1de5":function(t,e,n){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},3533:function(t,e,n){"use strict";var a=n("ca64"),i=n.n(a);i.a},"778e":function(t,e,n){"use strict";n.d(e,"b",(function(){return a})),n.d(e,"c",(function(){return i})),n.d(e,"a",(function(){}));var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",[n("Hearder",{attrs:{name:""}}),n("v-uni-view",{staticClass:"content"},[n("v-uni-view",{staticClass:"bar_top"},[n("v-uni-view",{class:0==t.current?"act":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Change(0)}}},[t._v("密码登录")]),n("v-uni-view",{class:1==t.current?"act":"",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Change(1)}}},[t._v("验证码登录")])],1),n("v-uni-view",{staticClass:"phone"},[n("v-uni-input",{attrs:{type:"text",placeholder:"请输入手机号"},model:{value:t.phone,callback:function(e){t.phone=e},expression:"phone"}})],1),0==t.current?n("v-uni-view",{staticClass:"phone"},[n("v-uni-input",{attrs:{type:"password",placeholder:"请输入登录密码"},model:{value:t.password,callback:function(e){t.password=e},expression:"password"}})],1):t._e(),1==t.current?n("v-uni-view",{staticClass:"message"},[n("v-uni-input",{attrs:{type:"text",placeholder:"请输入验证码"},model:{value:t.sms_code,callback:function(e){t.sms_code=e},expression:"sms_code"}}),n("v-uni-view",{staticClass:"color_span",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.net_Code()}}},[t._v(t._s(t.title))])],1):t._e(),n("v-uni-view",{staticClass:"forget"},[n("span",{on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.goPage("/pages/login/forget_cz_password")}}},[t._v("忘记密码？")])]),0==t.current?n("v-uni-view",{staticClass:"login_btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Login()}}},[t._v("登录")]):t._e(),1==t.current?n("v-uni-view",{staticClass:"login_btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.Code_Login()}}},[t._v("登录")]):t._e()],1)],1)},i=[]},8765:function(t,e,n){"use strict";n.r(e);var a=n("778e"),i=n("1d1f");for(var r in i)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(r);n("3533");var s=n("f0c5"),o=Object(s["a"])(i["default"],a["b"],a["c"],!1,null,"a54d2d5c",null,!1,a["a"],void 0);e["default"]=o.exports},c80d:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJIAAABZCAYAAADVVH35AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAALwSURBVHgB7daJjdwwDEBRktm+kv6LMiPxkuxUEOA/IDsztqyDouiIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAC/657c/8yP/qLt4fNVzeV3r3+ubr2+6/rjua/r6Hjejra7L67rsGxpdRru8d9qL5QjVTz8Xn/HsHty0xvfub3cQ7WbsfXe1y36rncx49W/3WctUfWoc7b7jeZk17P67XQ4WY0mP5WqSHda6ak0+gdOad9y/ru15xZo8F241Zgwz/alYxrtjNOPl95hMPtbrmj3oGE5fsWk99v6svite+cCJfW18jP2Kt9z73M//VMJUbCoCubnqV0cyG3BNpoI5A3Rfe1KWSbS+xUbciTCbdU8sFvKcxJwkWddsxhOzCURe7s+zeb7axBxs9ScV7AzESYRJ8nomfpv0fDpB76TWTOKcr+3hbD/7dN+aa/BYnVUc90WrAyRXol1JVs/tr+5yHbw5AJpJkzGo/rX2otZtu5dJrt6vq6/Yt3u9/TGJEX1lolbsTSpGnlOPIfzM+zqcP98ksk9WSm+myutkR9tYlLplckZS5cOewdlt7U6OpzbYK3m1T05EQDs5zSsZd29eydP3/6kyp3poLbwCI3XPdAqsv/u6K9MJuslVkSMR3ic0T7PKdbq1K+A6QO957YYrflbX5HPia8+rougZLybwnATYvlXtqtSZUP7PBp+q4jO+6RTwO3Gq/SkK0odg3izvGHQebD+/9CrL/XrR89qp5I1rpjKn/lShKcURBauqIn69Gjqh7sSJy71J/brQTEaZMhtJ1EHLjX6kE16+J22XwRNEzbmpXAn12UTPsqsdmGeq6qtSnQr52UC/5rJ/P6/KfDZrquInmfy07WTQbv/ag/Ma2o/vAeOATeXq15b3nsS8duzc59X5Gbdea/vlsQfIe51YVpXSu4hI773G/3IEAAAAAAAAAID/2l/SSFEKO+QqbgAAAABJRU5ErkJggg=="},ca64:function(t,e,n){var a=n("dd5c");a.__esModule&&(a=a.default),"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var i=n("4f06").default;i("7391a566",a,!0,{sourceMap:!1,shadowMode:!1})},dd5c:function(t,e,n){var a=n("24fb"),i=n("1de5"),r=n("c80d");e=a(!1);var s=i(r);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.message[data-v-a54d2d5c]{padding:%?30?%;border:%?2?% solid #a3a3a3;border-radius:%?16?%;margin-bottom:%?30?%;display:flex;align-items:center;justify-content:space-between}.message .color_span[data-v-a54d2d5c]{background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-fill-color:transparent}.login_btn[data-v-a54d2d5c]{margin-top:%?60?%;height:%?110?%;text-align:center;line-height:%?110?%;font-size:%?35?%;color:#fff;font-weight:600;background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);border-radius:%?55?%}.forget[data-v-a54d2d5c]{text-align:right;font-size:%?28?%;background:linear-gradient(98.63deg,#4140ff 9.67%,#c376e2 93.43%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-fill-color:transparent}.phone[data-v-a54d2d5c]{padding:%?30?%;border:%?2?% solid #a3a3a3;border-radius:%?16?%;margin-bottom:%?30?%}.act[data-v-a54d2d5c]{font-size:%?40?%!important;color:#323232!important;padding:%?40?% %?0?%;background:url('+s+");background-size:100% 100%}.content[data-v-a54d2d5c]{padding:%?75?%}.content .bar_top[data-v-a54d2d5c]{margin-bottom:%?90?%;display:flex;align-items:center}.content .bar_top > uni-view[data-v-a54d2d5c]{font-size:%?33?%;color:#a3a3a3;margin-right:%?55?%;font-weight:600}",""]),t.exports=e},e3f4:function(t,e,n){"use strict";n("7a82"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("e25e");var a={data:function(){return{nums:"",ticket:"",randstr:"",Pid:"",phone:"",password:"",sms_code:"",current:0,time:60,timer:null,title:"发送验证码"}},onShow:function(){},onLoad:function(){this.getAPPID(),setTimeout((function(){var t=document.createElement("script");t.src="https://ssl.captcha.qq.com/TCaptcha.js",document.head.appendChild(t)}),1e3)},methods:{goPage:function(t){var e=["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"];this.nums="";for(var n=0;n<32;n++){var a=parseInt(61*Math.random());this.nums+=e[a]}console.log(this.nums),uni.setStorageSync("TOKEN",this.nums),uni.navigateTo({url:t})},goSiven:function(){uni.navigateTo({url:"/pages/login/test_login"})},getAPPID:function(){var t=this;this.request("/api/index/getCaptchaAppId").then((function(e){1==e.data.code?(t.Pid=e.data.data.appid,uni.setStorageSync("Pid",t.Pid)):t.$tip(e.data.msg)}))},net_Code:function(){if(""!=this.phone){var t=this,e=t.Pid,n=new TencentCaptcha(e,(function(e){0==e.ret&&(t.ticket=e.ticket,t.randstr=e.randstr,1==t.current&&t.Submit())}));n.show()}else this.$tip("请输入手机号")},Submit:function(){var t=this;this.request("/api/Sms/behaviorVerificationCode",{ticket:this.ticket,randstr:this.randstr,scenes_type:"sms"}).then((function(e){1==e.data.code?t.getCode():t.$tip(e.data.msg)}))},getCode:function(){var t=this;"发送验证码"==this.title&&this.request("/api/Sms/sendSms",{phone:this.phone,sendType:"login"},"GET").then((function(e){1==e.data.code?(t.$tip(e.data.msg),t.timer=setInterval((function(){t.time>=1?(t.time--,t.title=t.time+"s"):(clearInterval(t.timer),t.title="发送验证码",t.time=60)}),1e3)):t.$tip(e.data.msg)}))},Code_Login:function(){var t=this;this.request("/api/member.member/mobilelogin",{phone:this.phone,sms_code:this.sms_code}).then((function(e){1==e.data.code?(t.$tip(e.data.msg),uni.setStorageSync("TOKEN",e.data.data.userinfo.token),uni.setStorageSync("member_id",e.data.data.userinfo.code),setTimeout((function(){uni.switchTab({url:"/pages/main/personal"})}),1e3)):t.$tip(e.data.msg)}))},Login:function(){var t=this;this.request("/api/member.member/login",{phone:this.phone,password:this.password}).then((function(e){1==e.data.code?(t.$tip(e.data.msg),uni.setStorageSync("TOKEN",e.data.data.userinfo.token),uni.setStorageSync("member_id",e.data.data.userinfo.code),setTimeout((function(){uni.switchTab({url:"/pages/main/personal"})}),1e3)):t.$tip(e.data.msg)}))},Send:function(){var t=this;""!=this.phone?this.time<60||(this.timer=setInterval((function(){t.time>=1?(t.time--,t.title=t.time+"s"):(clearInterval(t.timer),t.title="发送验证码",t.time=60)}),1e3)):this.$tip(res.data.msg)},Change:function(t){this.current=t}}};e.default=a}}]);