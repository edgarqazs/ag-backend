<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use app\common\controller\Frontend;
use think\Db;
use Endroid\QrCode\QrCode;

class Pay extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function getAddress(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        if( empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失']);
        }
        // $sendUrl = 'http://8.210.180.205/api/address'; //接口的URL
        // $app_name='test';
        // $key='test123';
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if(empty($user['address'])){
            $sendUrl = 'http://47.242.138.157/api/address'; //接口的URL
            $app_name='xusjnennck';
            $key='xjdjdnzklakndxjw';
            $smsConf = array(
                'app_name'  =>  $app_name,
                'nonce'=>'address'.uniqid(),
                'symbol'=>'trx'
            );
            $smsConf['sign'] = MakeSign($smsConf,$key);
            $content = post($sendUrl,$smsConf,1); //请求接口
            $result = json_decode($content,true);
            $address = $result['data']['address'];
            if($address){
                // 更新
                $text = $address;
                $qrCode = new QrCode($text);
                $qrCode->setSize(200);
                $qrCode->writeFile($_SERVER['DOCUMENT_ROOT'].'/address/'.$address.'.png');

                // 生成二维码    end
                $qrcode_image = '/address/'.$address.'.png';
                Db::name('user')->where('id',$param['user_id'])->update(['address'=>$address]);
            }else{
                return json(['code'=>100,'msg'=>'获取地址失败！','data'=>'获取地址失败']);
            }
        }

        return json(['code'=>100,'msg'=>'登录成功！','data'=>$user['address']]);
    }

    public function callback(){
                
        try{
             file_put_contents(__DIR__.'/pay.log',date("Y-m-d H:i:s")."\n",FILE_APPEND);
	        // $temp['content'] = 444;
            // $temp['ctime'] = time();
            // Db::name('ceshi')->insert($temp);
            //记录post数据到log.txt
            // $log = file_get_contents('php://input');
            // file_put_contents('log.txt', $log.PHP_EOL, FILE_APPEND);
            $app_name = 'xusjnennck';
            $key = 'xjdjdnzklakndxjw';
            $ip = '47.242.137.149';
            $param = $this->request->post("");
            
            // if(!isset($param)){
            //     $data['error'] =0;
            //     $data['msg'] ='参数缺失';
            //     echo json_encode($data); //$this->success('返回成功', $data);
            //     die;
            // }
    
            $sign = MakeSign($param,$key);
            //验证签名
            if($param['sign'] != $sign){
                $msg['error'] = 200;
                $msg['msg'] ='sign error';
                echo json_encode($msg); //$this->success('返回成功', $data);
                die;
            }
            
            //验证appname
            if($param['app_name'] != $app_name){
                $msg['error'] = 200;
                $msg['msg'] ='app_name error';
                echo json_encode($msg); //$this->success('返回成功', $data);
                die;
            }
            
            // //获取请求IP地址
            // $serverip = $this->request->ip();
            // //验证IP白名单
            // if($serverip != $ip){
            //     echo'whitelist_ip error';
            //     die();
            // }
            
            //下面处理业务逻辑、保存数据库记录等。用address这个字段唯一识别订单
            $where['hash'] = $param['tx_hash'];
            $recharge = Db::name('recharge')->where($where)->find();
            //如果充值记录没有此订单，则进行充值
            if(empty($recharge)){
                //获取汇率
                $site = Db::name('site')->where('id',1)->find();
                //获取username
                $data['username'] = Db::name('user')->where('address',$param['address'])->value('username');
                //如果获取不到username,返回错误
                if(empty($data['username'])){
                    $msg['error'] = 200;
                    $msg['msg'] ='username error';
                    echo json_encode($msg); //$this->success('返回成功', $data);
                    die;
                }
                //获取userid
                $data['user_id'] = Db::name('user')->where('address',$param['address'])->value('id');
                //如果获取不到userid返回错误
                if(empty($data['user_id'])){
                    $msg['error'] = 200;
                    $msg['msg'] ='user_id error';
                    echo json_encode($msg); //$this->success('返回成功', $data);
                    die;
                }
                
                $data['address'] = $param['address'];
                $data['money'] = $param['balance'];
                $data['usdt'] = $param['balance']*$site['usdt_recharge_rate'];
                $data['hash'] = $param['tx_hash'];
                $data['status'] = '充值成功';
                $data['pay_time'] = time();
                //插入充值记录表
                $recharge = Db::name('recharge')->insert($data);
                //如果插入失败返回错误
                if(empty($recharge)){
                    $msg['error'] = 200;
                    $msg['msg'] ='recharge insert error';
                    echo json_encode($msg); //$this->success('返回成功', $data);
                    die;
                }
                //获取user信息
                $user = Db::name('user')->where('address',$param['address'])->find();
                //获取不到user信息报错
                if(empty($user)){
                    $msg['error'] = 200;
                    $msg['msg'] ='user error';
                    echo json_encode($msg); //$this->success('返回成功', $data);
                    die;
                }
                //更新usdt
                $update = Db::name('user')->where('address',$param['address'])->setInc('usdt',$data['usdt']);
                //如果更新错误返回错误
                if(empty($update)){
                    $msg['error'] = 200;
                    $msg['msg'] ='update usdt error';
                    echo json_encode($msg); //$this->success('返回成功', $data);
                    die;
                }
                //插入日志
                $log = model('Order','logic','common')->addMoneyRecord('usdt',$user['id'],$data['usdt'],'+','充值');
                if($log){
                    $msg['error'] =0;
                    $msg['msg'] ='success';
                }else{
                    $msg['error'] =200;
                    $msg['msg'] ='log error';
                }
                echo json_encode($msg); //$this->success('返回成功', $data);
                die;
            }else{
                //业务逻辑处理结束，返回success
                $data['error'] = 200;
                $data['msg'] ='Repeat notify';
                echo json_encode($data); //$this->success('返回成功', $data);
                die;
            }
        }catch (\Exception $e){
	        echo($e->getMessage());
	        
	        file_put_contents(__DIR__.'/pay.log', $e->getMessage()."\n",FILE_APPEND);
	        file_put_contents(__DIR__.'/pay.log', file_get_contents('php://input')."\n",FILE_APPEND);
        }
        

    }
}
