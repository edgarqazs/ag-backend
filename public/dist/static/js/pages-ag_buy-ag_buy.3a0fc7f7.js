(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-ag_buy-ag_buy"],{2689:function(t,e,i){"use strict";var n;i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}));var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",[n("v-uni-view",{staticClass:"list"},[n("v-uni-view",{staticClass:"item",staticStyle:{"line-height":"100rpx",color:"#FFFFFF","text-indent":"40rpx"}},[t._v("我的CNY："+t._s(t.user["cny"]))]),n("v-uni-view",{staticClass:"item"},[n("v-uni-image",{staticClass:"tu",attrs:{src:i("6916").replace(/^\./,"")}}),n("v-uni-view",{staticClass:"zi"},[t._v("认购30AG")]),n("v-uni-view",{staticClass:"jiantou",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.doBuyAg(1)}}},[t._v("认购")])],1),n("v-uni-view",{staticClass:"item"},[n("v-uni-image",{staticClass:"tu",attrs:{src:i("6916").replace(/^\./,"")}}),n("v-uni-view",{staticClass:"zi"},[t._v("认购300AG")]),n("v-uni-view",{staticClass:"jiantou",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.doBuyAg(2)}}},[t._v("认购")])],1)],1),n("v-uni-view",{staticClass:"desc"},[n("p",[t._v("温馨提示：")]),n("p",[t._v("1.当您账户CNY数量为0-729CNY时，未达到认购资格")]),n("p",[t._v("2.当您账户CNY数量为730-7299CNY时，您可以一次性认购30AG（仅限一次），同时消耗30AG等值的CNY")]),n("p",[t._v("3.当您账户CNY数量为7300CNY及以上时，您可以一次性认购300AG（仅限一次），同时消耗300AG等值的CNY")]),n("p",[t._v("4.认购30AG后您将没有资格再次认购300AG")])])],1)},o=[]},"3df0":function(t,e,i){"use strict";i.r(e);var n=i("2689"),a=i("c11b");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("ef44");var d,s=i("f0c5"),c=Object(s["a"])(a["default"],n["b"],n["c"],!1,null,"e27fdd26",null,!1,n["a"],d);e["default"]=c.exports},"64f2":function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,"uni-page-body[data-v-e27fdd26]{background:#2d2d35;color:#fff}.desc[data-v-e27fdd26]{float:left;width:90%;margin-left:5%;margin-top:%?30?%;margin-bottom:%?30?%;font-size:%?28?%}.desc p[data-v-e27fdd26]{margin-top:%?5?%}.list[data-v-e27fdd26]{float:left;width:100%;margin-bottom:%?40?%;margin-top:%?30?%}.item[data-v-e27fdd26]{float:left;height:%?100?%;width:90%;margin-left:5%;background:#3d3f4b;-webkit-border-radius:%?10?%;border-radius:%?10?%;margin-top:%?15?%}.item .tu[data-v-e27fdd26]{float:left;display:block;width:%?60?%;height:%?60?%;margin-top:%?20?%;margin-left:5%}.item .zi[data-v-e27fdd26]{float:left;display:block;width:60%;height:%?100?%;line-height:%?100?%;margin-left:5%;font-size:%?30?%;color:#fff}.item .jiantou[data-v-e27fdd26]{float:right;display:block;width:%?100?%;height:%?56?%;margin-top:%?22?%;margin-right:5%;font-size:%?30?%;color:#fff;background:#e54d42;line-height:%?56?%;text-align:center;-webkit-border-radius:%?10?%;border-radius:%?10?%}.swiper[data-v-e27fdd26]{height:%?370?%}\n/* swiper-item 里面的图片高度 */uni-swiper-item uni-image[data-v-e27fdd26]{width:100%;height:%?370?%}body.?%PAGE?%[data-v-e27fdd26]{background:#2d2d35}",""]),t.exports=e},6916:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADUAAAA1CAYAAADh5qNwAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxNjc5MEM0MUY2OTMxMUVBOEU4OUI3MjI1ODNCRjUyNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxNjc5MEM0MkY2OTMxMUVBOEU4OUI3MjI1ODNCRjUyNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjE2NzkwQzNGRjY5MzExRUE4RTg5QjcyMjU4M0JGNTI2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjE2NzkwQzQwRjY5MzExRUE4RTg5QjcyMjU4M0JGNTI2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+2YdmZgAAArVJREFUeNrcms1LFVEYh3W8N7BSCS8WJbhTJHAZLQI/0MwE0fwIyQL3/QWByzbt26QulHLhRgRJtNBQ1NAgKBQXLgSJUlIpqMAi/R15DxzGmWHmzvm4Z37wcIcZmDvPPWfeOefMza+qvJFnMPWgAoyBI1knTRkUeg1awD/QBh6APzJO7BgSmiMh/sN2gBfgoq1Sc9Tt3OkDw6DYNik/IZ4e8DxuixVkSq/lihBPDbXWdLZflK+p+oUVYtkFV3K9+2kV0iElQ6gKpHNFSoZQNdgEr0CRaSlZQhu03Q0GwQVTUrKFeO6TWInuYZIqIZ5e9hgC/eC3jpZSLSQ+oIdAoWopXUJii730u37HQiGeDpqypGVLmRI6HQ1R8Rh31wbHUiEx7WAUnI8rlStC4j02wsVSCRDi6aKZ8+NUQoR4HoL3ToKEeDachAl9Ae+cBAl9BeVhqp9NQlfDlHQrhYKkrBXyk9It9Bn8lSXkJaVbqBk8AR9lCbml5jUL3QGzYCrMbDaskCj1DNRpFpqh7VlaMZIiJI79Wg0JvQGNMoV4S50DW+A4CUJcir3segQWkiAk3lM/aBa5aLuQu/qxi++msm6tkNdzapfmJEtgz0ah08ULn1c5l0nQOqGgsZ8KoWMdQlEWXq7HFPpF+/6rFoqy8DIaU6iJPpnUXZVCUVqqkcaG2Qg1gBXwDdxWLRRF6pCWoJZ9jj8NEFoFpWDbo2dIF4oixXIAOtnChscx9kePWvDWR2gn7+xbCiVCQSU9KGVgEtx07f8OMiTEpjBrJoSithQPeyjf8xgrZuhzAKybEspWil8YE/vgcayPKpwRoWy7n7t1JsAt13625pA2IRSnpcT7qJUKghhjQjKkWH5Sd1tQPVLQKcWyT+V+xbSQTCneFbtoIeUTrRJpF5JRKLxyiUYgxnIiwACaK/CkMGjNWAAAAABJRU5ErkJggg=="},b177:function(t,e,i){"use strict";var n=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=n(i("023c")),o={data:function(){return{baseurl:a.default.baseUrl,banner_list:[],user:[],site:[]}},onLoad:function(){this.getBanner(),this.init()},methods:{init:function(){this.getSite(),this.getUser()},getSite:function(){var t=this;uni.request({url:this.baseurl+"/Index/index/getSite",data:{},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(e){"100"==e.data.code&&(t.site=e.data.data)}})},getUser:function(){var t=this;uni.request({url:this.baseurl+"/Index/index/getUser",data:{user_id:uni.getStorageSync("user_id")},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(e){"100"==e.data.code&&(t.user=e.data.data)}})},doBuyAg:function(t){var e=this;if(1==this.user["is_buy_ag"])return uni.showModal({content:"您已认购过，没有认购资格",showCancel:!1,success:function(t){}}),!1;if(1==t){if(this.user["cny"]<730)return uni.showModal({content:"您的CNY余额不足，未达到认购资格",showCancel:!1,success:function(t){}}),!1;if(this.user["cny"]>=7300)return uni.showModal({content:"请选择认购300AG",showCancel:!1,success:function(t){}}),!1;uni.showModal({content:"确定认购30AG",success:function(t){t.confirm&&uni.request({url:e.baseurl+"/Index/index/doAgBuy",data:{type:1,user_id:uni.getStorageSync("user_id")},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){"100"==t.data.code&&e.init()}})}})}if(2==t){if(this.user["cny"]<7300)return uni.showModal({content:"您的CNY余额不足，未达到认购资格",showCancel:!1,success:function(t){}}),!1;uni.showModal({content:"确定认购300AG",success:function(t){t.confirm&&uni.request({url:e.baseurl+"/Index/index/doAgBuy",data:{type:2,user_id:uni.getStorageSync("user_id")},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){"100"==t.data.code&&e.init()}})}})}},goUrl:function(t){window.location.href=t},getBanner:function(){var t=this;uni.request({url:this.baseurl+"/Index/index/getBanner",data:{position:"AG兑换卡"},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(e){"100"==e.data.code&&(t.banner_list=e.data.data)}})}}};e.default=o},c11b:function(t,e,i){"use strict";i.r(e);var n=i("b177"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},c1ef:function(t,e,i){var n=i("64f2");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("ee3306da",n,!0,{sourceMap:!1,shadowMode:!1})},ef44:function(t,e,i){"use strict";var n=i("c1ef"),a=i.n(n);a.a}}]);