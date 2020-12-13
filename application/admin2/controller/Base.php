<?php
namespace app\admin2\controller;

use think\Controller;

class Base extends Controller {
	protected $beforeActionList = [ 
			'checkLogin'
	];
	protected function checkLogin() {
		if (! session ( "?admin2_info" )) {
			return $this->error ( "尚未登陆", "login/index" );
		}
	}
	
}