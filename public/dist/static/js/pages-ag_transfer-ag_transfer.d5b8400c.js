(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-ag_transfer-ag_transfer"],{"1de5":function(t,a,e){"use strict";t.exports=function(t,a){return a||(a={}),t=t&&t.__esModule?t.default:t,"string"!==typeof t?t:(/^['"].*['"]$/.test(t)&&(t=t.slice(1,-1)),a.hash&&(t+=a.hash),/["'() \t\n]/.test(t)||a.needQuotes?'"'.concat(t.replace(/"/g,'\\"').replace(/\n/g,"\\n"),'"'):t)}},"23e2":function(t,a,e){"use strict";var i=e("4ea4");e("c975"),e("acd8"),e("e25e"),e("498a"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var n=i(e("023c")),o={data:function(){return{baseurl:n.default.baseUrl,user:[],type:1,num:0,site:[],useag_flowag_money:0,flowag_useag_money:0,verify:!1,getCodeText:"获取验证码",mobile:"",code:"",rate:0,serviceFee:0,input:0,output:0,usable:0,total:0,account:""}},onLoad:function(){var t=this;uni.request({url:t.baseurl+"/Index/index/getAgTransferCharge",data:{user_id:uni.getStorageSync("user_id"),num:t.num},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(a){100==a.data.code&&(t.serviceFee=parseFloat(a.data.data["charge"]),t.usable=parseFloat(a.data.data["flow_ag_left"]),t.total=parseFloat(a.data.data["flow_ag_left"]),t.rate=a.data.data["charge_percent"]),console.log(a)},fail:function(t){console.log(t)},dataType:"JSON"})},onShow:function(){this.init()},computed:{realReduce:function(){return""==this.num?0:parseFloat(this.num)+parseFloat(this.num*(this.rate/100))}},methods:{getNum:function(t){0==this.num.indexOf("0")&&(this.num=parseInt(this.num));var a=this;this.num>this.total&&(uni.showToast({title:"不能超出可用AG数额",icon:"none"}),this.num=0),uni.request({url:a.baseurl+"/Index/index/getAgTransferCharge",data:{user_id:uni.getStorageSync("user_id"),num:a.num},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){100==t.data.code&&(a.serviceFee=parseFloat(t.data.data["charge"]),a.usable=parseFloat(t.data.data["flow_ag_left"]),a.rate=t.data.data["charge_percent"]),console.log(t)},fail:function(t){console.log(t)},dataType:"JSON"})},init:function(){this.getUser(),this.getSite()},doAll:function(){1==this.type?this.num=parseInt(this.user["ag"]):this.num=parseInt(this.user["flow_ag"]),uni.showToast({title:"成功",mask:!0})},changeType:function(){1==this.type?this.type=2:this.type=1,this.num=""},checkParam:function(t){var a=this;""!=a.account.trim()?a.num<=0?uni.showToast({title:"转出AG的数额不能是0",icon:"none"}):a.verify=!0:uni.showToast({title:"收款方帐户不能为空",icon:"none"})},toTransfer:function(t){var a=this;""!=a.account.trim()?a.num<=0?uni.showToast({title:"转出AG的数额不能是0",icon:"none"}):uni.request({url:this.baseurl+"/Index/index/doAgTransfer",data:{user_id:uni.getStorageSync("user_id"),to_username:a.account,code:a.code,num:this.num},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){"100"==t.data.code?uni.showModal({content:"转账成功",showCancel:!1,success:function(t){a.init()}}):uni.showModal({content:t.data.msg,showCancel:!1,success:function(t){}}),a.num=0,a.account="",a.verify=!1}}):uni.showToast({title:"收款方帐户不能为空",icon:"none"})},getUser:function(){var t=this,a=this;uni.request({url:this.baseurl+"/Index/index/getUser",data:{user_id:uni.getStorageSync("user_id")},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(e){"100"==e.data.code&&(t.user=e.data.data,a.mobile=e.data.data["mobile"])}})},getSite:function(){var t=this;uni.request({url:this.baseurl+"/Index/index/getSite",data:{},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(a){"100"==a.data.code&&(t.site=a.data.data)}})},goNotice:function(){uni.navigateTo({url:"/pages/notice/notice"})},goRecharge:function(){uni.navigateTo({url:"/pages/recharge/recharge"})},goReturn:function(){uni.navigateBack()},getCode:function(t){var a=this;if("获取验证码"==this.getCodeText){this.getCodeText=59;var e=setInterval((function(t){a.getCodeText--,a.getCodeText<=0&&(a.getCodeText="获取验证码",clearInterval(e))}),1e3);uni.showLoading({mask:!0}),uni.request({url:this.baseurl+"/Index/index/codeSendToUser",data:{user_id:uni.getStorageSync("user_id")},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){uni.hideLoading(),uni.showToast({title:"验证码已发送"}),console.log(t)},fail:function(t){uni.hideLoading()}})}},bindCancle:function(t){this.verify=!1},inputDigit:function(t){console.log(t)}}};a.default=o},4010:function(t,a,e){t.exports=e.p+"static/img/qrcode_bg.ffff8f6b.png"},"455b":function(t,a){t.exports="data:image/png;base64,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"},"4bdf":function(t,a,e){var i=e("fdcf");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=e("4f06").default;n("57c93775",i,!0,{sourceMap:!1,shadowMode:!1})},"81d7":function(t,a,e){"use strict";e.r(a);var i=e("23e2"),n=e.n(i);for(var o in i)"default"!==o&&function(t){e.d(a,t,(function(){return i[t]}))}(o);a["default"]=n.a},afa6:function(t,a,e){"use strict";var i;e.d(a,"b",(function(){return n})),e.d(a,"c",(function(){return o})),e.d(a,"a",(function(){return i}));var n=function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("v-uni-view",[i("v-uni-image",{staticClass:"top_image",attrs:{src:e("b9a6").replace(/^\./,"")}}),i("v-uni-image",{staticClass:"zuo_arrow",attrs:{src:e("455b").replace(/^\./,"")},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.goReturn.apply(void 0,arguments)}}}),i("v-uni-view",{staticClass:"ding"},[i("v-uni-view",{staticClass:"zi"},[t._v("AG转账")])],1),i("v-uni-view",{staticClass:"card"},[i("v-uni-view",{staticClass:"a_title"},[i("v-uni-view",{staticClass:"item"},[t._v("收取方用户账号：")])],1),i("v-uni-view",{staticClass:"address",staticStyle:{border:"1rpx solid #7a7b91",background:"#2d2d35"}},[i("v-uni-input",{staticClass:"info",attrs:{type:"text",placeholder:"请输入收款方账号"},model:{value:t.account,callback:function(a){t.account=a},expression:"account"}})],1),i("v-uni-view",{staticClass:"a_title"},[i("v-uni-view",{staticClass:"item"},[t._v("转出数量："),i("v-uni-text",{staticClass:"fee",model:{value:t.num,callback:function(a){t.num=a},expression:"num"}},[t._v(t._s(t.num))])],1)],1),i("v-uni-view",{staticClass:"address"},[i("v-uni-input",{staticClass:"info",attrs:{type:"number",placeholder:"请输入转出数量"},on:{input:function(a){arguments[0]=a=t.$handleEvent(a),t.getNum.apply(void 0,arguments)}},model:{value:t.num,callback:function(a){t.num=a},expression:"num"}})],1),i("v-uni-view",{staticClass:"a_title"},[i("v-uni-view",{staticClass:"item"},[t._v("实际消耗AG："),i("v-uni-text",{staticClass:"fee",model:{value:t.num,callback:function(a){t.num=a},expression:"num"}},[t._v(t._s(t.realReduce))])],1),i("v-uni-view",{staticClass:"item",staticStyle:{"text-align":"right","font-size":"26rpx"}},[t._v("可用流通AG "+t._s(t.usable))])],1),i("v-uni-view",{staticClass:"a_title"},[i("v-uni-view",{staticClass:"item"},[t._v("手续费："),i("v-uni-text",{staticClass:"fee",model:{value:t.rate,callback:function(a){t.rate=a},expression:"rate"}},[t._v("("+t._s(t.rate)+"%)")])],1),i("v-uni-view",{staticClass:"item",staticStyle:{"text-align":"right","font-size":"26rpx"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.doAll.apply(void 0,arguments)}}},[t._v("全部转出")])],1),i("v-uni-view",{staticClass:"address"},[1==t.type?[i("v-uni-view",{staticClass:"info"},[t._v(t._s(t.num*(t.rate/100)))])]:t._e()],2)],1),i("v-uni-view",{staticClass:"btn",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.checkParam.apply(void 0,arguments)}}},[t._v("确认转账")]),t.verify?i("v-uni-view",{staticClass:"mask"},[i("v-uni-view",{staticClass:"show-modal"},[i("v-uni-view",{staticClass:"title"},[t._v("交易验证")]),i("v-uni-view",{staticClass:"input phone"},[i("v-uni-text",[t._v("已绑定的手机号码 "+t._s(t.mobile))])],1),i("v-uni-view",{staticClass:"input code"},[i("v-uni-input",{attrs:{type:"number",placeholder:"验证码",maxlength:"6"},model:{value:t.code,callback:function(a){t.code=a},expression:"code"}}),i("v-uni-text",{on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.getCode.apply(void 0,arguments)}}},[t._v(t._s(t.getCodeText))])],1),i("v-uni-view",{staticClass:"bind-btn"},[i("v-uni-button",{staticClass:"sure",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.toTransfer.apply(void 0,arguments)}}},[t._v("确认")]),i("v-uni-button",{staticClass:"cancle",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.bindCancle.apply(void 0,arguments)}}},[t._v("取消")])],1)],1)],1):t._e()],1)},o=[]},b9a6:function(t,a,e){t.exports=e.p+"static/img/beijing.21aa7305.gif"},f9af:function(t,a,e){"use strict";e.r(a);var i=e("afa6"),n=e("81d7");for(var o in n)"default"!==o&&function(t){e.d(a,t,(function(){return n[t]}))}(o);e("f9fc");var s,r=e("f0c5"),d=Object(r["a"])(n["default"],i["b"],i["c"],!1,null,"7aa5802a",null,!1,i["a"],s);a["default"]=d.exports},f9fc:function(t,a,e){"use strict";var i=e("4bdf"),n=e.n(i);n.a},fdcf:function(t,a,e){var i=e("24fb"),n=e("1de5"),o=e("4010");a=i(!1);var s=n(o);a.push([t.i,"uni-page-body[data-v-7aa5802a]{color:#fff;background:#2d2d35}.c_blue[data-v-7aa5802a]{color:#00f3ff!important}.line[data-v-7aa5802a]{float:left;height:%?4?%;width:90%;margin-left:5%;background:#3e414d}.info_word[data-v-7aa5802a]{position:fixed;z-index:2;top:%?120?%;width:100%;text-align:center;color:#fff;font-size:%?28?%}.info_word .item[data-v-7aa5802a]{float:left;width:50%;text-align:center}.money[data-v-7aa5802a]{position:fixed;z-index:2;top:%?170?%;width:100%;text-align:center;color:#fff;font-size:%?50?%;font-weight:600}.money .item[data-v-7aa5802a]{float:left;width:50%;text-align:center}.huan[data-v-7aa5802a]{float:left;height:%?180?%;width:90%;margin-left:5%}.huan .zuo[data-v-7aa5802a]{float:left;width:33.33%}.huan .zuo .word[data-v-7aa5802a]{float:left;width:100%;margin-top:%?40?%;height:%?60?%;color:#05e9fe;font-size:%?30?%;line-height:%?60?%}.huan .zuo .bi[data-v-7aa5802a]{float:left;width:100%;height:%?70?%}.huan .zuo .bi uni-image[data-v-7aa5802a]{float:left;display:block;width:%?40?%;height:%?40?%;-webkit-border-radius:%?100?%;border-radius:%?100?%}.huan .zuo .bi span[data-v-7aa5802a]{float:left;display:block;height:%?40?%;line-height:%?40?%;font-size:%?28?%}.huan .zhong[data-v-7aa5802a]{float:left;width:33.33%}.huan .zhong uni-image[data-v-7aa5802a]{display:block;width:%?40?%;height:%?40?%;margin:0 auto;left:0;right:0;margin-top:%?100?%}.huan .you[data-v-7aa5802a]{float:right;width:33.33%;text-align:right}.huan .you .word[data-v-7aa5802a]{float:left;width:100%;margin-top:%?40?%;height:%?60?%;color:#05e9fe;font-size:%?30?%;line-height:%?60?%}.huan .you .bi uni-image[data-v-7aa5802a]{float:right;display:block;width:%?40?%;height:%?40?%;-webkit-border-radius:%?100?%;border-radius:%?100?%;margin-right:%?15?%}.huan .you .bi span[data-v-7aa5802a]{float:right;display:block;height:%?40?%;line-height:%?40?%;font-size:%?28?%}.export[data-v-7aa5802a]{position:absolute;width:76%;margin-top:%?280?%;height:%?70?%;z-index:20;left:12%;-webkit-border-radius:%?100?%;border-radius:%?100?%;background:#3d3f4b;text-align:center;font-size:%?24?%;line-height:%?35?%}.btn[data-v-7aa5802a]{position:fixed;width:100%;height:%?100?%;line-height:%?100?%;bottom:%?0?%;background:-webkit-gradient(linear,left top,right top,from(#0efbf5),to(#1b90a8));background:-webkit-linear-gradient(left,#0efbf5,#1b90a8);background:linear-gradient(90deg,#0efbf5,#1b90a8);text-align:center;color:#fff;font-size:%?32?%;z-index:30}.tag[data-v-7aa5802a]{float:left;width:70%;margin-left:15%;font-size:%?30?%;height:auto;margin-top:%?60?%;margin-bottom:%?140?%}.tag .item[data-v-7aa5802a]{float:left;width:100%;height:%?50?%;line-height:%?50?%}.card[data-v-7aa5802a]{position:absolute;z-index:4;top:%?315?%;width:100%;background:#2d2d35;-webkit-border-radius:%?60?% %?60?% 0 0;border-radius:%?60?% %?60?% 0 0}.card .a_title[data-v-7aa5802a]{float:left;height:%?80?%;line-height:%?80?%;width:90%;margin-left:5%;margin-top:%?20?%;font-size:%?30?%}.card .a_title .item[data-v-7aa5802a]{float:left;height:%?80?%;line-height:%?80?%;width:50%;font-size:%?30?%}.card .address[data-v-7aa5802a]{float:left;height:%?80?%;line-height:%?80?%;width:90%;margin-left:5%;margin-top:%?10?%;-webkit-border-radius:%?20?%;border-radius:%?20?%;background:#3d3f4b}.card .address .info[data-v-7aa5802a]{float:left;margin-left:5%;font-size:%?30?%;height:%?80?%;line-height:%?80?%;width:70%}.card .address .unit[data-v-7aa5802a]{float:right;margin-right:5%;font-size:%?30?%;height:%?80?%;line-height:%?80?%;width:20%;text-align:right}.card .qrcode_bg[data-v-7aa5802a]{margin:0 auto;width:%?480?%;height:%?530?%;background:url("+s+');background-size:cover;margin-top:%?40?%}.card .qrcode_bg .qrcode[data-v-7aa5802a]{position:absolute;margin:0 auto;left:0;right:0;width:%?330?%;height:%?330?%;margin-top:%?80?%}.zuo_arrow[data-v-7aa5802a]{position:absolute;width:%?42?%;height:%?42?%;left:%?40?%;top:%?40?%;z-index:10}.ding[data-v-7aa5802a]{position:fixed;float:left;width:100%;height:%?110?%;z-index:2;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;top:%?30?%;font-size:%?42?%}.ding .zi[data-v-7aa5802a]{height:%?44?%;font-size:%?42?%;margin-left:%?4?%;color:#fff}.top_image[data-v-7aa5802a]{display:block;float:left;width:100%;height:%?436?%}.mask[data-v-7aa5802a]{display:-webkit-box;display:-webkit-flex;display:flex;position:fixed;top:0;left:0;width:100%;height:100%;background-color:hsla(0,0%,100%,.3);z-index:99;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.show-modal[data-v-7aa5802a]{width:80%;\n\t/* height: 450rpx; */padding-bottom:1.5rem;background-color:#fff;-webkit-border-radius:1rem;border-radius:1rem}.show-modal .title[data-v-7aa5802a]{float:none;height:55px;width:90%;margin-left:15%;font-size:16px;line-height:55px;color:#333}.show-modal uni-view.input[data-v-7aa5802a]{height:3rem;width:75%;margin:0 auto 1rem auto;color:#999}.show-modal uni-view.input uni-input[data-v-7aa5802a]{border:%?1?% solid #ddd;-webkit-border-radius:3rem;border-radius:3rem;height:3rem;text-indent:1rem;color:#333}.show-modal uni-view.code[data-v-7aa5802a]{position:relative}.show-modal uni-view.code uni-text[data-v-7aa5802a]{position:absolute;top:%?27?%;right:%?15?%;color:#666}.show-modal uni-button[data-v-7aa5802a]::after{content:"";border:none}.show-modal .bind-btn[data-v-7aa5802a]{display:-webkit-box;display:-webkit-flex;display:flex;margin:0 1rem;-webkit-box-pack:space-evenly;-webkit-justify-content:space-evenly;justify-content:space-evenly}.show-modal uni-button[data-v-7aa5802a]{width:6rem;-webkit-border-radius:6rem;border-radius:6rem;display:inline-block}.show-modal uni-button.sure[data-v-7aa5802a]{border:%?1?% solid #ddd;color:#fff;background-color:#009688}body.?%PAGE?%[data-v-7aa5802a]{background:#2d2d35}',""]),t.exports=a}}]);