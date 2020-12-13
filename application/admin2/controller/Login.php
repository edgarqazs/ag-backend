<?php

namespace app\admin2\controller;

use think\Controller;
use think\Request;

class Login extends Controller {
	function index(Request $req) {
		if ($req->isGet ()) {
			return $this->fetch();
		} else {
		    $account=$req->post('account');
		    $pass=$req->post('pass');
		    $admin_info=db("admin")->where("username='{$account}'")->find();
		    if(!empty($admin_info)){
		        if(md5(md5($pass).$admin_info['salt'])==$admin_info['password']){
		            session ( "admin2_info",$admin_info);
		            return $this->success("登录成功",'Index/index');
		        }
		    }
		    return $this->error("账号或密码错误");
		}
	}
}
