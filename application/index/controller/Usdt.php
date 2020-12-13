<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use app\common\controller\Frontend;
use think\Db;
use Endroid\QrCode\QrCode;
use think\db\Expression;

class Usdt extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    // 获取商兑交易信息
    public function getUsdtInfo(){
                
        $data['usdt_all_money'] = Db::name('usdtorder')->where('status',3)->sum('num');
        $data['usdt_all_sell'] = Db::name('usdtorder')->where('status',0)->sum('num');

        return json(['code'=>100,'msg'=>'成功','data'=>$data]);
    }

    // 获取订单
    public function getUsdtOrderList(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if( empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        $where['status'] = 0;
        $where['user_id'] = $param['user_id'];
        $user_list = Db::name('usdtorder')->where($where)->order('ctime desc')->select();

        $where['user_id'] = array('NEQ',$param['user_id']);
        $list = Db::name('usdtorder')->where($where)->order('ctime desc')->select();

        $list = array_merge($user_list,$list);

        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }

    // 发布usdt订单
    public function addUsdtOrder(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $param['head_image'] = $user['head_image'];
        $param['nickname'] = $user['nickname'];

        $param['sn'] = date('YmdHis').rand(100,999);
        $param['ctime'] = time();
        $param['money'] = $param['num']*$param['single_money'];
        $res = Db::name('usdtorder')->insert($param);
        if($res){//冻结部分usdt
            Db::name('user')->where('id',$param['user_id'])->setInc('freeze_usdt',$param['num']);
            Db::name('user')->where('id',$param['user_id'])->setDec('usdt',$param['num']);
            model('Order','logic','common')->addMoneyRecord('usdt',$param['user_id'],$param['num'],'-','商兑冻结');
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);   
    }


    // 购买usdt订单
    public function buyUsdtOrder(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(empty($param['user_id']) ||empty($param['id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $where['id'] = $param['id'];
        $data['buyer_id'] = $param['user_id'];
        $data['start_time'] = time();
        $data['status'] = 1;
        $res = Db::name('usdtorder')->where($where)->update($data);
        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 取消usdt订单
    public function cancelUsdtOrder(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(empty($param['user_id']) ||empty($param['id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $where['id'] = $param['id'];
        $usdtorder = Db::name('usdtorder')->where($where)->find();
        if($usdtorder){
            $data['status'] = 4;
            $res = Db::name('usdtorder')->where($where)->update($data);

            Db::name('user')->where('id',$param['user_id'])->setDec('freeze_usdt',$usdtorder['num']);
            Db::name('user')->where('id',$param['user_id'])->setInc('usdt',$usdtorder['num']);
            model('Order','logic','common')->addMoneyRecord('usdt',$param['user_id'],$usdtorder['num'],'+','商兑撤单解冻');
        }

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }


    // 获取交易中和历史订单
    public function getZhongUsdtOrderList(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if( empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        $status = "1,2,3";
        $exp = new Expression("field(status,$status)");

        $where['user_id|buyer_id'] = $param['user_id'];
        $where['status'] = array('IN','1,2,3');
        $list = Db::name('usdtorder')->where($where)->order($exp)->select();
        foreach ($list as $k => $v) {
            $list[$k]['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
        }

        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }

    // 卖家完成订单
    public function finishUsdtOrder(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if( empty($param['user_id']) || empty($param['id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $where['id'] = $param['id'];
        $where['user_id'] = $param['user_id'];
        $usdtorder = Db::name('usdtorder')->where($where)->find();
        if($usdtorder){
            $data['status'] = 3;
            $res = Db::name('usdtorder')->where($where)->update($data);

            // 买家加usdt，卖家释放usdt锁仓
            Db::name('user')->where('id',$usdtorder['buyer_id'])->setInc('usdt',$usdtorder['num']);
            Db::name('user')->where('id',$usdtorder['user_id'])->setDec('freeze_usdt',$usdtorder['num']);

            model('Order','logic','common')->addMoneyRecord('usdt',$usdtorder['buyer_id'],$usdtorder['num'],'+','商兑成交');
        }

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 获取订单详情
    public function getUsdtOrderInfo(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if( empty($param['id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $where['id'] = $param['id'];
        $info = Db::name('usdtorder')->where($where)->find();

        $info['start_time'] = date('Y-m-d H:i:s',$info['start_time']);
        $info['buyer_nickname'] = Db::name('user')->where('id',$info['buyer_id'])->value('nickname');
        $info['seller_nickname'] = Db::name('user')->where('id',$info['user_id'])->value('nickname');
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$info]);
    }

    // 申诉
    public function doAppeal(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if( empty($param['id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $where['id'] = $param['id'];

        $data['status'] = 2;
        $data['appeal_time'] = time();
        $data['reason'] = $param['reason'];

        Db::name('usdtorder')->where($where)->update($data);

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 取消申诉
    public function cancelAppeal(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if( empty($param['id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $where['id'] = $param['id'];
        $data['status'] = 1;

        Db::name('usdtorder')->where($where)->update($data);

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 获取今日价格
    public function getPrice(){
                
        $param = $_POST;

        $where['show_time'] = strtotime(date('Y-m-d'));
        $price = Db::name('usdtprice')->where($where)->value('price');

        return json(['code'=>100,'msg'=>'登录成功！','data'=>$price]);
    }
}
