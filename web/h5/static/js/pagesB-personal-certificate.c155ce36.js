(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesB-personal-certificate"],{"0140":function(t,e,n){"use strict";n.d(e,"b",(function(){return i})),n.d(e,"c",(function(){return a})),n.d(e,"a",(function(){}));var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",{staticClass:"integralMall"},[n("Hearder",{attrs:{name:"证书"}}),n("v-uni-view",{staticClass:"content"},[n("v-uni-image",{style:"width:"+t.width+"px;height:"+t.height+"px;",attrs:{src:t.image,mode:""}}),n("v-uni-view",{staticClass:"sml_code",staticStyle:{position:"absolute",width:"100%",top:"-1000px"}},[n("v-uni-canvas",{staticClass:"canvas-poster",style:{width:t.width+"px",height:t.height+"px"},attrs:{id:"canvasPoster","canvas-id":"canvasPoster"}})],1),n("v-uni-view",{staticClass:"sub",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.saveImage.apply(void 0,arguments)}}},[t._v("保存证书")])],1)],1)},a=[]},"1de5":function(t,e,n){"use strict";t.exports=function(t,e){return e||(e={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),e.hash&&(t+=e.hash),/["'() \t\n]/.test(t)||e.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"25b0":function(t,e,n){var i=n("24fb"),a=n("1de5"),s=n("65df");e=i(!1);var o=a(s);e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.content[data-v-542aa597]{padding:%?40?% 4%}.content .box[data-v-542aa597]{width:100%;height:%?1080?%;background:url('+o+") no-repeat;background-size:contain}.content .box .box_a[data-v-542aa597]{width:70%;margin:0 auto;padding:%?50?% 0 0}.content .box .box_a[data-v-542aa597]:first-of-type{padding-top:%?90?%}.content .box .box_a uni-view[data-v-542aa597]{text-align:center}.content .box .box_a .tle[data-v-542aa597]{font-size:%?26?%;color:#323232}.content .box .box_a .con[data-v-542aa597]{margin-top:%?16?%;font-size:%?30?%;font-weight:600;color:#323232}.content .sub[data-v-542aa597]{margin-top:%?60?%;height:%?98?%;background:linear-gradient(0deg,#0f2cf9,#2570fc);border-radius:%?56?%;text-align:center;line-height:%?98?%;font-size:%?32?%;font-family:PingFang SC;font-weight:600;color:#fff}",""]),t.exports=e},"5a7a":function(t,e,n){"use strict";n.r(e);var i=n("0140"),a=n("a6a7");for(var s in a)["default"].indexOf(s)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(s);n("f182");var o=n("f0c5"),r=Object(o["a"])(a["default"],i["b"],i["c"],!1,null,"542aa597",null,!1,i["a"],void 0);e["default"]=r.exports},"65df":function(t,e,n){t.exports=n.p+"static/img/zs.d66601d0.png"},"7aa7":function(t,e,n){var i=n("25b0");i.__esModule&&(i=i.default),"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var a=n("4f06").default;a("742bc21a",i,!0,{sourceMap:!1,shadowMode:!1})},a6a7:function(t,e,n){"use strict";n.r(e);var i=n("e3917"),a=n.n(i);for(var s in i)["default"].indexOf(s)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(s);e["default"]=a.a},e3917:function(t,e,n){"use strict";n("7a82");var i=n("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("14d9");var a=i(n("5530")),s={data:function(){return{id:"",width:"",height:"",systemInfo:"",shareInfo:"",image:""}},onLoad:function(t){uni.showLoading({title:"加载中"}),this.id=t.id;var e=this;uni.getSystemInfo({success:function(t){t.pixelRatio>2&&(t.pixelRatio=2),e.systemInfo=t,e.width=.92*t.windowWidth,e.height=1.56*e.width}})},onShow:function(){this.getCertificate()},methods:{saveImage:function(){this.$tip("长按图片保存")},getCertificate:function(){var t=this;this.request("/api/member.goods/getCertificate",{id:this.id}).then((function(e){if(1==e.data.code){t.info=e.data.data,t.shareInfo={tle:"藏品名称",hash:"藏品哈希值",collection:"藏品编码",writer:"创作者",issue:"发行方",issueTwo:"发行平台",user:"收藏者",bgc:"../static/zs.png"};var n=(0,a.default)((0,a.default)({},t.info),t.shareInfo);t.drawImage(n)}}))},drawtext:function(t,e,n,i,a){for(var s=e.split(""),o="",r=[],l=0;l<s.length;l++)t.measureText(o).width<a&&t.measureText(o+s[l]).width<=a?o+=s[l]:(r.push(o),o=s[l]);r.push(o);for(var d=0;d<r.length;d++)t.fillText(r[d],n,i+20*(d+1))},drawImage:function(t){var e=this,n=uni.createCanvasContext("canvasPoster",this),i=e.width,a=e.height,s=i/2,o=n.measureText(t.tle).width,r=n.measureText(t.goods_name).width,l=n.measureText(t.hash).width,d=n.measureText(t.hash_url).width,c=n.measureText(t.collection).width,u=n.measureText(t.collection_number).width,f=n.measureText(t.writer).width,h=n.measureText(t.writer_name).width,g=n.measureText(t.issue).width,p=n.measureText(t.issue_name).width,x=n.measureText(t.user).width,v=n.measureText(t.nickname).width;n.drawImage(t.bgc,0,0,i,a),n.save(),n.setFillStyle("#323232"),n.font="13px PingFang SC",n.fillText(t.tle,s-o/2,70,o),n.setFillStyle("#323232"),n.font="bold 15px PingFang SC",n.fillText(t.goods_name,s-r/2,93,r),n.setFillStyle("#323232"),n.font="13px PingFang SC",n.fillText(t.hash,s-l/2,137,l),n.setFillStyle("#323232"),n.font="bold 15px PingFang SC",n.fillText(t.hash_url,s-d/2,160,d),n.setFillStyle("#323232"),n.font="13px PingFang SC",n.fillText(t.collection,s-c/2,204,c),n.setFillStyle("#323232"),n.font="bold 15px PingFang SC",n.fillText(t.collection_number,s-u/2,227,u),n.setFillStyle("#323232"),n.font="13px PingFang SC",n.fillText(t.writer,s-f/2,271,f),n.setFillStyle("#323232"),n.font="bold 15px PingFang SC",n.fillText(t.writer_name,s-h/2,294,h),n.setFillStyle("#323232"),n.font="13px PingFang SC",n.fillText(t.issue,s-g/2,338,g),n.setFillStyle("#323232"),n.font="bold 15px PingFang SC",n.fillText(t.issue_name,s-p/2,361,p),n.setFillStyle("#323232"),n.font="13px PingFang SC",n.fillText(t.user,s-x/2,405,x),n.setFillStyle("#323232"),n.font="bold 15px PingFang SC",n.fillText(t.nickname,s-v/2,428,v),n.draw(),setTimeout((function(){uni.canvasToTempFilePath({canvasId:"canvasPoster",width:i,height:a,destWidth:i*e.systemInfo.pixelRatio,destHeight:a*e.systemInfo.pixelRatio+120,success:function(t){e.image=t.tempFilePath,uni.hideLoading()},fail:function(t){console.log(t)}})}),500)}}};e.default=s},f182:function(t,e,n){"use strict";var i=n("7aa7"),a=n.n(i);a.a}}]);