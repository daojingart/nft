(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pagesA-market-commParse"],{"323b":function(t,n,e){"use strict";e.r(n);var i=e("f894"),a=e("4e94");for(var u in a)["default"].indexOf(u)<0&&function(t){e.d(n,t,(function(){return a[t]}))}(u);var r=e("f0c5"),s=Object(r["a"])(a["default"],i["b"],i["c"],!1,null,"3a497c72",null,!1,i["a"],void 0);n["default"]=s.exports},3869:function(t,n,e){"use strict";e("7a82"),Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var i={data:function(){return{info:"",name:"",type:1,content:'\n\t\t\t\t\t\t\t\t<p>露从今夜白，月是故乡明</p>\n\t\t\t\t\t\t\t\t<img src="https://cdn.uviewui.com/uview/swiper/2.jpg" />\n\t\t\t\t\t\t\t'}},onLoad:function(t){this.id=t.id,this.getDetail(),this.type=t.type,1==this.type?this.name="尺码对照标":2==this.type&&(this.name="更多详情"),uni.setNavigationBarTitle({title:this.name})},methods:{getDetail:function(){var t=this;this.request("/api/goods.products/getProducyInfo",{product_id:this.id}).then((function(n){1==n.data.code&&(t.info=n.data.data)}))}}};n.default=i},"4e94":function(t,n,e){"use strict";e.r(n);var i=e("3869"),a=e.n(i);for(var u in i)["default"].indexOf(u)<0&&function(t){e.d(n,t,(function(){return i[t]}))}(u);n["default"]=a.a},f894:function(t,n,e){"use strict";e.d(n,"b",(function(){return a})),e.d(n,"c",(function(){return u})),e.d(n,"a",(function(){return i}));var i={uParse:e("a3ab").default},a=function(){var t=this.$createElement,n=this._self._c||t;return n("v-uni-view",{staticClass:"integralMall"},[n("Hearder",{attrs:{name:this.name}}),n("v-uni-view",{staticClass:"u-content"},[n("u-parse",{attrs:{content:this.info.content}})],1)],1)},u=[]}}]);