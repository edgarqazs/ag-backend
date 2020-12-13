<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use app\common\controller\Frontend;
use think\Db;
use Endroid\QrCode\QrCode;

class Login extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';


    public function getRandWord(){
                
        $list = Db::name('word')->select();
        $word_list = array_rand($list,12);
        shuffle($word_list);
        
        $real_list = [];
        foreach ($word_list as $k => $v) {
            $real_list[] = $list[$v]['name'];
        }
        $real_list = implode(' ', $real_list);
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$real_list]);
    }
    private function utf8_strlen($string = null) {
        // 将字符串分解为单元
        preg_match_all("/./us", $string, $match);
        // 返回单元个数
        return count($match[0]);
    }
    public function doReg(){
                
        
    	$param = $_POST;

    	$user = Db::name('user')->where('username',$param['username'])->find();
    	if($user){
			return json(['code'=>200,'msg'=>'该用户名已存在']);	    		
    	}
        if(preg_match("/[\x7f-\xff]/", $param['username'])){
            if($this->utf8_strlen($param['username'])<2){
                return json(['code'=>200,'msg'=>'用户名(中文)必须不少于2个字符']);
            }
        }else if(strlen($param['username'])<6){
            return json(['code'=>200,'msg'=>'用户名(英文、数字)必须不少于6个字符']);
        }
    	if($param['invite_code']){
    		$father = Db::name('user')->where('invite_code',$param['invite_code'])->find();
    		if(empty($father)){
    			return json(['code'=>200,'msg'=>'该邀请码不存在']);
    		}
    		$param['father_id'] = $father['id'];
            $param['grandfather_id'] = $father['father_id'];

    		if(empty($father['family_id'])){
    			$param['family_id'] = $father['id'];
    		}else{
    			$param['family_id'] = $father['family_id'].','.$father['id'];
    		}

    	}


    	$param['ctime'] = time();
    	$param['pwd'] = md5($param['pwd']);
    	$param['nickname'] = $param['username'];
    	$param['private_key'] = getRandom(16);
    	$param['invite_code'] = getRandomSmall(6);

        unset($param['user_id']);
    	$id = Db::name('user')->insertGetId($param);

        // 生成二维码
        $text = $qrcode_image = 'http://'.$_SERVER['HTTP_HOST'].'/#/pages/welcome/welcome?invite_code='.$param['invite_code'];
        $qrCode = new QrCode($text);
        $qrCode->setSize(200);
        $qrCode->writeFile($_SERVER['DOCUMENT_ROOT'].'/qrcode/'.$id.'.png');
        // 生成二维码    end

        $qrcode_image = '/qrcode/'.$id.'.png';
        Db::name('user')->where('id',$id)->update(['qrcode_image'=>$qrcode_image]);

    	// return json(['code'=>100,'msg'=>'登录成功！','data'=>$id]);	
    	return json(['code'=>100,'msg'=>'登录成功！','data'=>getToken($id)]);	
    }

    public function doLogin(){
                
    	$param = $_POST;

    	$where['username'] = $param['username'];
    	$user = Db::name('user')->where($where)->find();
    	if(empty($user)){
			return json(['code'=>200,'msg'=>'该用户名不存在']);	    		
    	}

    	if($user['status'] == '禁用'){
    		return json(['code'=>200,'msg'=>'该账号已被禁用']);
    	}
        //if($user['id']==2210){//调整 拼团
            
        //}else{
            if($user['pwd'] != md5($param['pwd'])){
    		    return json(['code'=>200,'msg'=>'密码错误']);
    	    }
        //}
    	
    	
    	if($param['type'] == 1){
    		if($user['word'] != $param['word']){
    			return json(['code'=>200,'msg'=>'助记词错误']);
    		}
    	}
    	if($param['type'] == 2){
    		if($user['private_key'] != $param['private_key']){
    			return json(['code'=>200,'msg'=>'私钥错误']);
    		}
    	}
        db('user_login_log')->insert([
            'user_id'=>$user['id'],
            'username'=>$user['username'],
            'ip'=>request()->ip(),
            'add_time'=>time()
        ]);
    	// return json(['code'=>100,'msg'=>'登录成功！','data'=>$user['id']]);	
    	return json(['code'=>100,'msg'=>'登录成功！','data'=>getToken($user['id'])]);	
    }

    public function doForget(){
                
        $param = $_POST;

        $where['username'] = $param['username'];
        $user = Db::name('user')->where($where)->find();
        if(empty($user)){
            return json(['code'=>200,'msg'=>'该用户名不存在']);
        }

        if($user['remind'] != $param['remind']){
            return json(['code'=>200,'msg'=>'忘记密码提示错误']);
        }

        if($user['status'] == '禁用'){
            return json(['code'=>200,'msg'=>'该账号已被禁用']);
        }
        
        if($param['type'] == 1){
            if($user['word'] != $param['word']){
                return json(['code'=>200,'msg'=>'助记词错误']);
            }
        }
        if($param['type'] == 2){
            if($user['private_key'] != $param['private_key']){
                return json(['code'=>200,'msg'=>'私钥错误']);
            }
        }

        $data['pwd'] = md5($param['pwd']);
        Db::name('user')->where('username',$where['username'])->update($data);

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);  
    }


}
