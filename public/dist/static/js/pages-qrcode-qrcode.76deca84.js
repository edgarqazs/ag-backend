(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-qrcode-qrcode"],{1465:function(t,i,e){"use strict";var a;e.d(i,"b",(function(){return n})),e.d(i,"c",(function(){return o})),e.d(i,"a",(function(){return a}));var n=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("v-uni-view",[a("v-uni-image",{staticClass:"top_image",attrs:{src:e("b9a6").replace(/^\./,"")}}),a("v-uni-image",{staticClass:"zuo_arrow",attrs:{src:e("455b").replace(/^\./,"")},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.goReturn.apply(void 0,arguments)}}}),a("v-uni-view",{staticClass:"ding"},[a("v-uni-view",{staticClass:"zi"},[t._v("邀请好友")])],1),a("v-uni-view",{staticClass:"invite_code",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.copy(t.user["invite_code"])}}},[t._v("邀请码："+t._s(t.user["invite_code"])),a("span",[t._v("点击复制")])]),a("v-uni-view",{staticClass:"card"},[a("v-uni-view",{staticClass:"qrcode_bg"},[a("v-uni-image",{staticClass:"qrcode",attrs:{src:t.baseurl+t.user["qrcode_image"]}})],1)],1),a("v-uni-view",{staticClass:"download-link"},[t._l(t.download,(function(i,e){return[a("v-uni-view",{key:e+"_0",staticClass:"link-name"},[t._v(t._s(e)+" 下载地址")]),t._l(i,(function(i,e){return[a("v-uni-view",{staticClass:"link",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.copy(i)}}},[t._v(t._s(i)),a("span",[t._v("点击复制")])])]}))]}))],2)],1)},o=[]},"1de5":function(t,i,e){"use strict";t.exports=function(t,i){return i||(i={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),i.hash&&(t+=i.hash),/["'() \t\n]/.test(t)||i.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"38fd":function(t,i,e){"use strict";var a=e("c3f0"),n=e.n(a);n.a},"455b":function(t,i){t.exports="data:image/png;base64,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"},"606f":function(t,i,e){t.exports=e.p+"static/img/invite_qrcode_bg.c421c062.png"},"73e2":function(t,i,e){"use strict";e.r(i);var a=e("9954"),n=e.n(a);for(var o in a)"default"!==o&&function(t){e.d(i,t,(function(){return a[t]}))}(o);i["default"]=n.a},"899d":function(t,i,e){var a=e("24fb"),n=e("1de5"),o=e("606f");i=a(!1);var r=n(o);i.push([t.i,"uni-page-body[data-v-d71112c4]{color:#fff;background:#2d2d35}.invite_code[data-v-d71112c4]{position:absolute;margin-top:%?220?%;width:100%;text-align:center;font-size:%?46?%}.invite_code span[data-v-d71112c4], .link span[data-v-d71112c4]{font-size:%?24?%;color:#28ebec}.tag[data-v-d71112c4]{float:left;width:70%;margin-left:15%;font-size:%?30?%;height:auto;margin-top:%?60?%}.tag .item[data-v-d71112c4]{float:left;width:100%;height:%?50?%;line-height:%?50?%}.card[data-v-d71112c4]{position:absolute;z-index:4;top:%?330?%;width:100%;background:#2d2d35;-webkit-border-radius:%?60?% %?60?% 0 0;border-radius:%?60?% %?60?% 0 0}.card .a_title[data-v-d71112c4]{float:left;height:%?80?%;line-height:%?80?%;width:90%;margin-left:5%;margin-top:%?20?%;font-size:%?30?%}.card .address[data-v-d71112c4]{float:left;height:%?80?%;line-height:%?80?%;width:90%;margin-left:5%;margin-top:%?10?%;-webkit-border-radius:%?20?%;border-radius:%?20?%;background:#3d3f4b}.card .address .info[data-v-d71112c4]{float:left;margin-left:5%;font-size:%?24?%}.card .address .copy[data-v-d71112c4]{float:right;margin-right:5%;color:#04eafe;font-size:%?30?%}.card .qrcode_bg[data-v-d71112c4]{margin:0 auto;width:%?480?%;height:%?530?%;background:url("+r+");background-size:cover;margin-top:%?40?%}.card .qrcode_bg .qrcode[data-v-d71112c4]{position:absolute;margin:0 auto;left:0;right:0;width:%?330?%;height:%?330?%;margin-top:%?80?%}.zuo_arrow[data-v-d71112c4]{position:absolute;width:%?42?%;height:%?42?%;left:%?40?%;top:%?40?%;z-index:10}.ding[data-v-d71112c4]{position:fixed;float:left;width:100%;height:%?110?%;z-index:2;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;top:%?30?%;font-size:%?42?%}.ding .zi[data-v-d71112c4]{height:%?44?%;font-size:%?42?%;margin-left:%?4?%;color:#fff}.top_image[data-v-d71112c4]{display:block;float:left;width:100%;height:%?436?%}.info_word[data-v-d71112c4]{position:fixed;z-index:2;top:%?160?%;width:100%;text-align:center;color:#fff;font-size:%?32?%}.money[data-v-d71112c4]{position:fixed;z-index:2;top:%?210?%;width:100%;text-align:center;color:#fff;font-size:%?60?%;font-weight:600}.download-link[data-v-d71112c4]{position:absolute;top:30rem;margin:0 auto;width:85%;text-align:left;left:0;right:0}.link-name[data-v-d71112c4]{margin:1rem 0}.link[data-v-d71112c4]{text-indent:2rem}.link span[data-v-d71112c4]{margin:0 1rem 0 4rem}body.?%PAGE?%[data-v-d71112c4]{background:#2d2d35}",""]),t.exports=i},9954:function(t,i,e){"use strict";var a=e("4ea4");Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var n=a(e("023c")),o={data:function(){return{baseurl:n.default.baseUrl,user:[],version:0,download:{android:[],iphone:[]}}},onLoad:function(){this.init()},onShow:function(){this.getDistrbutionLinks()},methods:{getDistrbutionLinks:function(t){var i=this;uni.request({url:n.default.baseUrl+"/Index/index/getDownloadUrl",method:"POST",data:{user_id:uni.getStorageSync("user_id")},header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){t.data.code&&(i.download=t.data.data.download),console.log("下载地址",t)},fail:function(t){console.log(t)},dataType:"JSON"})},init:function(){var t=this;uni.request({url:this.baseurl+"/Index/index/getUser",data:{user_id:uni.getStorageSync("user_id")},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(i){"100"==i.data.code&&(t.user=i.data.data)}})},copy:function(t){uni.setClipboardData({data:t,success:function(){uni.showToast({title:"复制成功",duration:2e3})}})},goNotice:function(){uni.navigateTo({url:"/pages/notice/notice"})},goRecharge:function(){uni.navigateTo({url:"/pages/recharge/recharge"})},goReturn:function(){uni.navigateBack()}}};i.default=o},b9a6:function(t,i,e){t.exports=e.p+"static/img/beijing.21aa7305.gif"},c3f0:function(t,i,e){var a=e("899d");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=e("4f06").default;n("149a0d6f",a,!0,{sourceMap:!1,shadowMode:!1})},cba5:function(t,i,e){"use strict";e.r(i);var a=e("1465"),n=e("73e2");for(var o in n)"default"!==o&&function(t){e.d(i,t,(function(){return n[t]}))}(o);e("38fd");var r,d=e("f0c5"),c=Object(d["a"])(n["default"],a["b"],a["c"],!1,null,"d71112c4",null,!1,a["a"],r);i["default"]=c.exports}}]);