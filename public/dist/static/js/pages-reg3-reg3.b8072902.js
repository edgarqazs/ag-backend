(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-reg3-reg3"],{"16e6":function(t,e,i){"use strict";var a=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("023c")),o={data:function(){return{baseurl:n.default.baseUrl,word:"",username:"",pwd:"",pwd_aga:"",invite_code:"",remind:""}},onLoad:function(){var t=uni.getStorageSync("user_id");t&&uni.redirectTo({url:"/pages/index/index"});var e=uni.getStorageSync("invite_code");e&&(this.invite_code=e)},methods:{doReg:function(){var t=this;return""==this.username?(uni.showToast({title:"请输入用户名",duration:2e3,icon:"none"}),!1):""==this.pwd?(uni.showToast({title:"请输入密码",duration:2e3,icon:"none"}),!1):""==this.pwd_aga?(uni.showToast({title:"请再次输入密码",duration:2e3,icon:"none"}),!1):""==this.remind?(uni.showToast({title:"忘记密码提示",duration:2e3,icon:"none"}),!1):""==this.invite_code?(uni.showToast({title:"请输入邀请码",duration:2e3,icon:"none"}),!1):this.pwd!=this.pwd_aga?(uni.showToast({title:"两次密码输入不一致",duration:2e3,icon:"none"}),!1):void uni.request({url:this.baseurl+"/Index/Login/doReg",data:{username:this.username,pwd:this.pwd,remind:this.remind,invite_code:this.invite_code,word:uni.getStorageSync("word"),user_id:uni.getStorageSync("user_id")},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(e){if("100"==e.data.code){var i=uni.getStorageSync("user_id");i?t.contactUser(e.data.data):(uni.setStorageSync("user_id",e.data.data),uni.navigateTo({url:"/pages/index/index"}))}else uni.showToast({title:e.data.msg,duration:2e3,icon:"none"})}})},contactUser:function(t){uni.request({url:this.baseurl+"/Index/Index/addContact",data:{user_id:uni.getStorageSync("user_id"),contact_id:t},method:"POST",header:{"Content-Type":"application/x-www-form-urlencoded"},success:function(t){"100"==t.data.code?uni.navigateTo({url:"/pages/account/account"}):uni.showToast({title:t.data.msg,duration:2e3,icon:"none"})}})},goReg3:function(){uni.navigateTo({url:"/pages/reg3/reg3"})}}};e.default=o},"1b15":function(t,e,i){"use strict";var a=i("ef73"),n=i.n(a);n.a},"417b":function(t,e,i){"use strict";i.r(e);var a=i("16e6"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);e["default"]=n.a},"72b3":function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",[a("v-uni-view",{staticClass:"title"},[t._v("新增帐号")]),a("v-uni-view",{staticClass:"tag"},[t._v("密码将作为该账号的登录密码和交易密码，为了您的账号安全，我们建议您设置一个高强度的密码。")]),a("v-uni-view",{staticClass:"hang"}),a("v-uni-view",{staticClass:"content"},[a("v-uni-view",{staticClass:"item"},[a("v-uni-view",{staticClass:"tou"},[t._v("账号名称")]),a("v-uni-input",{staticClass:"shu",attrs:{placeholder:"请输入账号名称"},model:{value:t.username,callback:function(e){t.username=e},expression:"username"}})],1),a("v-uni-view",{staticClass:"item"},[a("v-uni-view",{staticClass:"tou"},[t._v("密码")]),a("v-uni-input",{staticClass:"shu",attrs:{type:"password",placeholder:"请输入密码"},model:{value:t.pwd,callback:function(e){t.pwd=e},expression:"pwd"}})],1),a("v-uni-view",{staticClass:"item"},[a("v-uni-view",{staticClass:"tou"},[t._v("确认密码")]),a("v-uni-input",{staticClass:"shu",attrs:{type:"password",placeholder:"请再次输入密码"},model:{value:t.pwd_aga,callback:function(e){t.pwd_aga=e},expression:"pwd_aga"}})],1),a("v-uni-view",{staticClass:"item"},[a("v-uni-view",{staticClass:"tou"},[t._v("忘记密码提示")]),a("v-uni-input",{staticClass:"shu",attrs:{type:"text",placeholder:"请忘记密码提示"},model:{value:t.remind,callback:function(e){t.remind=e},expression:"remind"}})],1),a("v-uni-view",{staticClass:"item"},[a("v-uni-view",{staticClass:"tou"},[t._v("邀请码")]),a("v-uni-input",{staticClass:"shu",attrs:{placeholder:"请输入邀请码"},model:{value:t.invite_code,callback:function(e){t.invite_code=e},expression:"invite_code"}})],1),a("v-uni-image",{staticClass:"sure",attrs:{src:i("9e4d").replace(/^\./,"")},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.doReg()}}})],1)],1)},o=[]},"9e4d":function(t,e,i){t.exports=i.p+"static/img/reg_sure.ecda2bed.png"},a218:function(t,e,i){"use strict";i.r(e);var a=i("72b3"),n=i("417b");for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);i("1b15");var s,r=i("f0c5"),u=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,"75950532",null,!1,a["a"],s);e["default"]=u.exports},e9aa:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,"uni-page-body[data-v-75950532]{background:#1b1b23}.title[data-v-75950532]{float:left;height:%?140?%;width:90%;margin-left:5%;line-height:%?140?%;font-size:%?60?%;color:#fff}.tag[data-v-75950532]{float:left;width:95%;margin-left:5%;line-height:%?40?%;font-size:%?26?%;color:#1cebe6}.hang[data-v-75950532]{float:left;height:%?10?%;width:95%;margin-left:5%;margin-top:%?30?%;background:-webkit-gradient(linear,left top,right top,from(#2afefb),to(#1892a2));background:-webkit-linear-gradient(left,#2afefb,#1892a2);background:linear-gradient(90deg,#2afefb,#1892a2)}.content[data-v-75950532]{float:left;width:95%;margin-left:5%;height:%?930?%;background:#282834}.content .item[data-v-75950532]{float:left;height:%?180?%;width:95%;margin-left:5%;border-bottom:%?1?% solid #727284}.content .item .tou[data-v-75950532]{float:left;width:100%;height:%?40?%;line-height:%?40?%;margin-top:%?40?%;color:#1cebe6;font-size:%?30?%}.content .item .shu[data-v-75950532]{float:left;width:100%;height:%?100?%;color:#fff;font-size:%?38?%}.sure[data-v-75950532]{float:right;margin-top:%?65?%;margin-right:15%;width:%?100?%;height:%?100?%}body.?%PAGE?%[data-v-75950532]{background:#1b1b23}",""]),t.exports=e},ef73:function(t,e,i){var a=i("e9aa");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("758aafe7",a,!0,{sourceMap:!1,shadowMode:!1})}}]);