(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-appeal-appeal"],{"00fa":function(t,i,e){"use strict";var a=e("4ea4");Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var n=a(e("023c")),o={data:function(){return{baseurl:n.default.baseUrl,id:"",info:[],sn:"",money:"",buyer_nickname:"",seller_nickname:"",reason:""}},onLoad:function(t){this.id=t.id,this.init()},methods:{init:function(){this.getUsdtOrderInfo()},doAppeal:function(){uni.request({url:this.baseurl+"/Index/usdt/doAppeal",data:{id:this.id,reason:this.reason},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){"100"==t.data.code&&uni.showModal({content:"提交申诉成功",showCancel:!1,success:function(t){t.confirm&&uni.navigateBack()}})}})},getUsdtOrderInfo:function(){var t=this;uni.request({url:this.baseurl+"/Index/usdt/getUsdtOrderInfo",data:{id:this.id},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(i){"100"==i.data.code&&(t.info=i.data.data,t.sn=t.info.sn,t.money=t.info.money,t.buyer_nickname=t.info.buyer_nickname,t.seller_nickname=t.info.seller_nickname)}})},goBindBank:function(){uni.navigateTo({url:"/pages/bind_bank/bind_bank"})},goBindAli:function(){uni.navigateTo({url:"/pages/bind_ali/bind_ali"})},goBindWechat:function(){uni.navigateTo({url:"/pages/bind_wechat/bind_wechat"})},goReturn:function(){uni.navigateBack()}}};i.default=o},"0878":function(t,i,e){var a=e("24fb");i=a(!1),i.push([t.i,"uni-page-body[data-v-008ac941]{color:#fff;background:#394956}.btn[data-v-008ac941]{float:left;height:%?90?%;width:40%;margin-left:30%;-webkit-border-radius:%?100?%;border-radius:%?100?%;font-size:%?54?%;line-height:%?90?%;text-align:center;background:-webkit-gradient(linear,left top,left bottom,from(#394956),to(#2f6371));background:-webkit-linear-gradient(top,#394956,#2f6371);background:linear-gradient(180deg,#394956,#2f6371);margin-top:%?80?%;margin-bottom:%?35?%}\n\n/* 交易大厅 */.list_one[data-v-008ac941]{float:left;width:90%;margin-left:5%}.list_one .title[data-v-008ac941]{float:left;width:100%;font-size:%?34?%;margin-top:%?40?%}.list_one .t_info[data-v-008ac941]{float:left;width:100%;height:%?250?%;-webkit-border-radius:%?20?%;border-radius:%?20?%;background:#2f6371;font-size:%?30?%;margin-top:%?30?%}.list_one .t_info uni-textarea[data-v-008ac941]{float:left;width:90%;height:%?220?%;margin-top:%?15?%;margin-left:5%}.list_one .info[data-v-008ac941]{float:left;width:100%;height:%?80?%;-webkit-border-radius:%?20?%;border-radius:%?20?%;background:#2f6371;font-size:%?30?%;margin-top:%?30?%;line-height:%?80?%}.list_one .info uni-image[data-v-008ac941]{float:left;display:block;width:%?200?%;height:%?200?%}.list_one .info uni-input[data-v-008ac941],select[data-v-008ac941]{float:left;display:block;height:%?80?%;width:90%;margin-left:5%;line-height:%?80?%;background:#2f6371;border:none;color:#fff}\n\n/* 交易大厅 end*/.content[data-v-008ac941]{position:absolute;z-index:2;margin-top:%?160?%;width:100%;min-height:%?400?%;background:#394956}.content .tou[data-v-008ac941]{float:left;width:100%;height:%?90?%;background:-webkit-gradient(linear,left top,left bottom,from(#2f6270),to(#3d3f4b));background:-webkit-linear-gradient(top,#2f6270,#3d3f4b);background:linear-gradient(180deg,#2f6270,#3d3f4b);border-bottom:%?3?% solid #535353}.content .tou .item[data-v-008ac941]{float:left;width:33.33%;height:%?90?%;text-align:center;color:#fff}.content .tou .item .word[data-v-008ac941]{float:left;width:100%;height:%?60?%;line-height:%?90?%;font-size:%?32?%;text-align:center;color:#fff}.content .tou .item .tab[data-v-008ac941]{float:left;width:26%;margin-left:37%;height:%?6?%;background:#fff;margin-top:%?10?%}.zuo_arrow[data-v-008ac941]{position:absolute;width:%?42?%;height:%?42?%;left:%?40?%;top:%?40?%;z-index:10}.ding[data-v-008ac941]{position:fixed;float:left;width:100%;height:%?110?%;z-index:2;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;top:%?30?%;font-size:%?42?%}.ding .zi[data-v-008ac941]{height:%?44?%;font-size:%?42?%;margin-left:%?4?%;color:#fff}.top_image[data-v-008ac941]{display:block;float:left;width:100%;height:%?436?%}body.?%PAGE?%[data-v-008ac941]{background:#394956}",""]),t.exports=i},"0e62":function(t,i,e){"use strict";var a=e("c4d8"),n=e.n(a);n.a},"455b":function(t,i){t.exports="data:image/png;base64,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"},"919a":function(t,i,e){t.exports=e.p+"static/img/index-background.255f5609.gif"},a102:function(t,i,e){"use strict";var a;e.d(i,"b",(function(){return n})),e.d(i,"c",(function(){return o})),e.d(i,"a",(function(){return a}));var n=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("v-uni-view",[a("v-uni-image",{staticClass:"top_image",attrs:{src:e("919a").replace(/^\./,"")}}),a("v-uni-image",{staticClass:"zuo_arrow",attrs:{src:e("455b").replace(/^\./,"")},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.goReturn.apply(void 0,arguments)}}}),a("v-uni-view",{staticClass:"ding"},[a("v-uni-view",{staticClass:"zi"},[t._v("申诉")])],1),a("v-uni-view",{staticClass:"content"},[a("v-uni-view",{staticClass:"list_one"},[a("v-uni-view",{staticClass:"title"},[t._v("订单编号")]),a("v-uni-view",{staticClass:"info"},[a("v-uni-input",{attrs:{type:"text",disabled:""},model:{value:t.sn,callback:function(i){t.sn=i},expression:"sn"}})],1),a("v-uni-view",{staticClass:"title"},[t._v("订单金额")]),a("v-uni-view",{staticClass:"info"},[a("v-uni-input",{attrs:{type:"text",disabled:""},model:{value:t.money,callback:function(i){t.money=i},expression:"money"}})],1),a("v-uni-view",{staticClass:"title"},[t._v("买家昵称")]),a("v-uni-view",{staticClass:"info"},[a("v-uni-input",{attrs:{type:"text",disabled:""},model:{value:t.buyer_nickname,callback:function(i){t.buyer_nickname=i},expression:"buyer_nickname"}})],1),a("v-uni-view",{staticClass:"title"},[t._v("卖家昵称")]),a("v-uni-view",{staticClass:"info"},[a("v-uni-input",{attrs:{type:"text",disabled:""},model:{value:t.seller_nickname,callback:function(i){t.seller_nickname=i},expression:"seller_nickname"}})],1),a("v-uni-view",{staticClass:"title"},[t._v("申诉理由")]),a("v-uni-view",{staticClass:"t_info"},[a("v-uni-textarea",{attrs:{placeholder:"请输入申诉理由"},model:{value:t.reason,callback:function(i){t.reason=i},expression:"reason"}})],1)],1),a("v-uni-view",{staticClass:"btn",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.doAppeal()}}},[t._v("提交申诉")])],1)],1)},o=[]},c0bb:function(t,i,e){"use strict";e.r(i);var a=e("a102"),n=e("f569");for(var o in n)"default"!==o&&function(t){e.d(i,t,(function(){return n[t]}))}(o);e("0e62");var r,c=e("f0c5"),s=Object(c["a"])(n["default"],a["b"],a["c"],!1,null,"008ac941",null,!1,a["a"],r);i["default"]=s.exports},c4d8:function(t,i,e){var a=e("0878");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=e("4f06").default;n("30c72a19",a,!0,{sourceMap:!1,shadowMode:!1})},f569:function(t,i,e){"use strict";e.r(i);var a=e("00fa"),n=e.n(a);for(var o in a)"default"!==o&&function(t){e.d(i,t,(function(){return a[t]}))}(o);i["default"]=n.a}}]);