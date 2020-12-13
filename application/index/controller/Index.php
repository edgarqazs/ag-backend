<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use app\common\controller\Frontend;
use think\Db;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\Cache;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        $url = '/dist';
        header("Location:".$url);
        exit;
        return $this->view->fetch();
    }

    public function getNoticeList(){

        $list = Db::name('cate')->order('weigh desc')->select();
        foreach ($list as $k => $v) {
            $where['status'] = '已发布';
            $where['cate_id'] = $v['id'];
            $notice_list = Db::name('notice')->where($where)->order('ctime desc')->select();
            $list[$k]['notice'] = $notice_list;
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }

    public function getNoticeDetail(){

        $param = $_POST;
        $info = Db::name('notice')->where('id',$param['id'])->find();
        $info['ctime'] = date('Y-m-d H:i:s',$info['ctime']);
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$info]);
    }

    /*
      * 获取应用安装包下载地址
      */
    public function getDownloadUrl(){
        $param = $_REQUEST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $site = Db::name('site')->where('id',1)->find();

        $info = Db::name('user')->where('id',$param['user_id'])->find();
        if(!$info){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        //下载地址
        $download = array();
        $download['download']['android'] = explode(",", $site['android_download_url']);
        $download['download']['iphone'] = explode(",", $site['iphone_download_url']);
        return json(['code'=>100,'msg'=>'请求成功','data'=>$download]);
    }

    public function getUser(){

        $param = $_REQUEST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $site = Db::name('site')->where('id',1)->find();

        $info = Db::name('user')->where('id',$param['user_id'])->find();
        if(!$info){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        unset($info['pwd']);
        unset($info['pay_pwd']);
        unset($info['word']);
        unset($info['private_key']);

        switch ($info['level']) {
        case -1:
            $info['level'] = '未激活';
            break;
        case 0:
            $info['level'] = '普通用户';
            break;
        case 1:
            $info['level'] = '一星达人';
            break;
        case 2:
            $info['level'] = '二星达人';
            break;
        case 3:
            $info['level'] = '三星达人';
            break;
        case 4:
            $info['level'] = '四星达人';
            break;
        }

            $info['usdt'] = round($info['usdt'],6);
            $info['cny'] = round($info['cny'],6);
            $info['ag'] = round($info['ag'],6);
            $info['sc'] = round($info['sc'],6);
            $info['qc'] = round($info['qc'],6);
        // 2020-11-16 暂且这边输出，最好由前端控制 start
            $info['lock_ag'] = round($info['lock_ag'],2);
        // 2020-11-16 end

            $info['usdt_cny_money'] = round($info['usdt']*$site['usdt_cny_rate'],6);

            $where_node['user_id'] = $param['user_id'];
            $where_node['status'] = 1;

            $node = Db::name('node')->where("user_id='{$param['user_id']}'")->count();

        if($node){
            $info['node_status'] = '已激活';
        }else{
            $info['node_status'] = '未激活';
        }

        $info['invite_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/#/pages/welcome/welcome?invite_code='.$info['invite_code'];
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$info]);
    }


    // 获取关联账号列表
    public function getUserList(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        $user = Db::name('user')->where('id',$param['user_id'])->find();

        $user_str = $param['user_id'];
        if($user['contact_id']){
            $user_str = $user_str.','.$user['contact_id'];
        }

        $where['id'] = array('IN',$user_str);
        $list = Db::name('user')->where($where)->select();
        unset($list['pwd']);
        unset($list['pay_pwd']);
        unset($list['word']);
        unset($list['private_key']);


        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }

    public function getUserWord(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if($user['pwd'] == md5($param['pwd']) ){
            return json(['code'=>100,'msg'=>'登录成功！','data'=>$user['word']]);
        }else{
            return json(['code'=>200,'msg'=>'密码错误']);
        }
    }

    public function getUserKey(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if($user['pwd'] == md5($param['pwd']) ){
            return json(['code'=>100,'msg'=>'登录成功！','data'=>$user['private_key']]);
        }else{
            return json(['code'=>200,'msg'=>'密码错误']);
        }
    }


    public function getAddressList(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $where['user_id'] = $param['user_id'];
        if($param['search_content']){
            $where['name|address'] = array('LIKE','%'.$param['search_content'].'%');
        }
        $list = Db::name('address')->where($where)->select();
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }
    public function getAddressDetail(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $info = Db::name('address')->where('id',$param['address_id'])->find();
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$info]);
    }
    public function addAddress(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        Db::name('address')->insert($param);
        return json(['code'=>100,'msg'=>'登录成功！']);
    }
    public function delAddress(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        Db::name('address')->where('id',$param['address_id'])->delete();
        return json(['code'=>100,'msg'=>'登录成功！']);
    }
    public function editAddress(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        Db::name('address')->where('id',$param['id'])->update($param);
        return json(['code'=>100,'msg'=>'登录成功！']);
    }

    // 添加账号关联
    public function addContact(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $user = Db::name('user')->where('id',$param['user_id'])->find();

        // 去重
        $temp = explode(',', $user['contact_id']);
        if(in_array($param['contact_id'], $temp)){
            return json(['code'=>200,'msg'=>'该账号已关联过','data'=>'']);
        }
        if($param['user_id'] == $param['contact_id']){
            return json(['code'=>200,'msg'=>'该账号已关联过','data'=>'']);
        }

        // 去重   end
        if($user['contact_id']){
            $data['contact_id'] = $user['contact_id'].','.$param['contact_id'];
        }else{
            $data['contact_id'] = $param['contact_id'];
        }

        Db::name('user')->where('id',$param['user_id'])->update($data);
        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }


    public function getBanner(){

        $param = $_POST;
        $list = Db::name('banner')->where($param)->order('weigh desc')->select();
        foreach ($list as $k => $v) {
            $list[$k]['banner_image'] = $_SERVER['SERVER_NAME'].$list[$k]['banner_image'];
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }


    // 兑换卡兑换
    public function doExchangeAg(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['type']) || empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }


        // 2020.11.11 start
        // 增加短信验证码验证
        $user= Db::name('user')->where('id',$param['user_id'])->find();
        $code_cache = Cache::get('verify_code_'.$user['mobile']);
        if(!$param['code'] || !$code_cache){
            return json(['code'=>200,'msg'=>'短信验证码错误']);
        }else{
            if($param['code'] != $code_cache){
                return json(['code'=>200,'msg'=>'短信验证码错误']);
            }
        }
        Cache::rm('verify_code_'.$user['mobile']);

        // 2020.11.11 end

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $site = Db::name('site')->where('id',1)->find();

        if($param['type'] == 1){
            if($user['ag_card'] <= 0){
                return json(['code'=>200,'msg'=>'兑换卡不足','data'=>'']);
            }
            $num = $user['ag_card']>3?3:$user['ag_card'];
            $ag = 210*$site['ag_card_rate']*$num;
        }

        if($param['type'] == 2){
            if($user['ag_card'] < 4){
                return json(['code'=>200,'msg'=>'兑换卡不足','data'=>'']);
            }
            $num = $user['ag_card']>8?8:$user['ag_card'];
            $ag = 280*$site['ag_card_rate']*$num;
        }

        if($param['type'] == 3){
            if($user['ag_card'] < 9){
                return json(['code'=>200,'msg'=>'兑换卡不足','data'=>'']);
            }
            $num = 9;
            $ag = 350*$site['ag_card_rate']*$num;
        }

        if($param['type'] == 4){
            if($user['ag_card'] < 10){
                return json(['code'=>200,'msg'=>'兑换卡不足','data'=>'']);
            }
            $num = 10;
            $ag = 840*$site['ag_card_rate']*$num;
        }

        Db::name('user')->where('id',$param['user_id'])->setDec('ag_card',$num);

        Db::name('user')->where('id',$param['user_id'])->setInc('lock_ag',$ag);

        $data['user_id'] = $param['user_id'];
        $data['all_ag'] = $ag;
        $data['lock_ag'] = $ag;
        $data['ctime'] = time();

        Db::name('exchange')->insert($data);

        model('Order','logic','common')->addMoneyRecord('lock_ag',$param['user_id'],$ag,'+','AG兑换卡转入');

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    public function getSite(){

        $site = Db::name('site')->where('id',1)->find();
        $site['cny_usdt_rate'] = number_format(1/$site['usdt_cny_rate'],2);

        return json(['code'=>100,'msg'=>'登录成功！','data'=>$site]);
    }

    // 闪兑
    public function doExchange(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) || empty($param['type']) || empty($param['num']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        if(getfloatlength($param['num'])!=0){
            return json(['code'=>200,'msg'=>'请输入整数','data'=>'']);
        }
        $user = Db::name('user')->where('id',$param['user_id'])->lock(true)->find();
        $site = Db::name('site')->where('id',1)->find();
        //$site['cny_usdt_rate'] = number_format(1/$site['usdt_cny_rate'],2);

        if($param['type'] == 1){//usdt换cny
            if($user['usdt'] < $param['num']){
                return json(['code'=>200,'msg'=>'usdt余额不足','data'=>'']);
            }
        }
        if($param['type'] == 2){//cny换usdt
            if($user['cny'] < $param['num']){
                return json(['code'=>200,'msg'=>'cny余额不足','data'=>'']);
            }
        }
        if(!empty($user['pay_pwd'])){
            $pay_pwd=request()->post('pay_pwd');
            if(md5($pay_pwd.$user['id'])!=$user['pay_pwd']){
                 return json(['code'=>200,'msg'=>'交易密码错误','data'=>'']);
            }
        }

        if($param['type']==1){//usdt换cny
            $amount=intval($site['usdt_cny_rate']*$param['num']*100)/100;
            Db::name('user')->where('id',$param['user_id'])->setDec('usdt',$param['num']);
            Db::name('user')->where('id',$param['user_id'])->setInc('cny',$amount);
            model('Order','logic','common')->addMoneyRecord('usdt',$param['user_id'],$param['num'],'-','闪兑转出');
            model('Order','logic','common')->addMoneyRecord('cny',$param['user_id'],$param['num']*$site['usdt_cny_rate'],'+','闪兑转入');
        }
        if($param['type']==2){//cny换usdt
            $amount=intval($param['num']/$site['usdt_cny_rate']*100)/100;
            Db::name('user')->where('id',$param['user_id'])->setDec('cny',$param['num']);
            Db::name('user')->where('id',$param['user_id'])->setInc('usdt',$amount);
            model('Order','logic','common')->addMoneyRecord('cny',$param['user_id'],$param['num'],'-','闪兑转出');
            model('Order','logic','common')->addMoneyRecord('usdt',$param['user_id'],$amount,'+','闪兑转入');
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 提现
    public function doWithdraw(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $param['num']=abs( $param['num']);
        $site = Db::name('site')->where('id',1)->find();

        if(empty($param['user_id']) || empty($param['num']) || empty($param['address']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        // 2020.11.24 start
        // TODO: 做成后台配置
        $h = (int)date('G');
        if($h < 10 || $h >= 18){
            return json(['code'=>200,'msg'=>'提现开放时间为10点至18点','data'=>'']);
        }
        // 2020.11.24 end

        // 2020.11.11 start
        // 增加短信验证码验证
        $user= Db::name('user')->where('id',$param['user_id'])->find();
        $code_cache = Cache::get('verify_code_'.$user['mobile']);
        if(!$param['code'] || !$code_cache){
            return json(['code'=>200,'msg'=>'短信验证码错误']);
        }else{
            if($param['code'] != $code_cache){
                return json(['code'=>200,'msg'=>'短信验证码错误']);
            }
        }
        Cache::rm('verify_code_'.$user['mobile']);
        if(isset($param['code'])){
            unset($param['code']);
        }

        // 2020.11.11 end

        // if(!is_int($param['num']) && getfloatlength($param['num'])>3 ){
        //     return json(['code'=>200,'msg'=>'最多输入3位小数','data'=>'']);
        // }
        if(!is_int($param['num']) && getfloatlength($param['num'])>0 ){
            return json(['code'=>200,'msg'=>'请输入整数','data'=>'']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if($user['usdt'] < $param['num']){
            return json(['code'=>200,'msg'=>'usdt余额不足','data'=>'']);
        }

        if($user['ag'] < $param['num']*$site['usdt_ag_rate']*0.03){
            return json(['code'=>200,'msg'=>'ag余额不足','data'=>'']);
        }

        // if(!empty($user['pay_pwd'])){
        //     $pay_pwd=request()->post('pay_pwd');
        //     if(md5($pay_pwd.$user['id'])!=$user['pay_pwd']){
        //         return json(['code'=>200,'msg'=>'交易密码错误','data'=>'']);
        //         }

        // }
        if(isset($param['pay_pwd'])){
            unset($param['pay_pwd']);
        }
        $param['withdraw_rate'] = $site['withdraw_rate'];
        $param['arrive_num'] = $param['num']*(1-$site['withdraw_rate']);
        $param['ctime'] = time();

        $res = Db::name('withdraw')->insert($param);
        if($res){
            Db::name('user')->where('id',$param['user_id'])->setDec('ag',$param['num']*$site['usdt_ag_rate']*0.03);
            model('Order','logic','common')->addMoneyRecord('ag',$param['user_id'],$param['num']*$site['usdt_ag_rate']*0.03,'-','提现消耗');

            Db::name('user')->where('id',$param['user_id'])->setDec('usdt',$param['num']);
            Db::name('user')->where('id',$param['user_id'])->setInc('freeze_usdt',$param['num']);
            model('Order','logic','common')->addMoneyRecord('usdt',$param['user_id'],$param['num'],'-','提现冻结');
        }

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 节点购买
    public function doNodeBuy(){

        return json(['code'=>200,'msg'=>'暂未开放','data'=>'']);

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $site = Db::name('site')->where('id',1)->find();

        if($user['cny'] < $site['node_cny']){
            return json(['code'=>200,'msg'=>'cny余额不足','data'=>'']);
        }

        $node = Db::name('node')->where('user_id',$param['user_id'])->find();
        if($node){
            return json(['code'=>200,'msg'=>'已认购过，不可重复购买','data'=>'']);
        }

        $count = Db::name('node')->count();
        if($count >= 30){
            return json(['code'=>200,'msg'=>'名额已满，无法认购','data'=>'']);
        }

        $param['lock_ag'] = $site['node_cny']*$site['cny_ag_rate'];
        $param['all_ag'] = $site['node_cny']*$site['cny_ag_rate'];
        $param['ctime'] = time();
        $res = Db::name('node')->insert($param);
        if($res){

            Db::name('user')->where('id',$param['user_id'])->setDec('cny',$site['node_cny']);
            model('Order','logic','common')->addMoneyRecord('cny',$param['user_id'],$site['node_cny'],'-','认购节点消耗');

            $data['cny'] = $user['cny']-$site['node_cny'];
            $data['lock_ag'] = $user['lock_ag']+$site['node_cny']*$site['cny_ag_rate'];
            if($user['level'] < 2){
                $data['level'] = 2;
            }
            Db::name('user')->where('id',$param['user_id'])->update($data);
        }

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 获取分红记录
    public function getNodeRecord(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        $list = Db::name('node_record')->where('user_id',$param['user_id'])->order('ctime desc')->select();
        foreach ($list as $k => $v) {
            $list[$k]['ctime'] = date('Y-m-d H:i',$v['ctime']);
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }

    // AG流通，可用转化
    public function doAgExchange(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) || empty($param['type']) || empty($param['num']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        if(getfloatlength($param['num'])!=0){
            return json(['code'=>200,'msg'=>'请输入整数！','data'=>'']);
        }
        $user = Db::name('user')->where('id',$param['user_id'])->lock(true)->find();
        $site = Db::name('site')->where('id',1)->find();


        if($param['type'] == 1){//可用换流通
            if($user['ag'] < $param['num']){
                return json(['code'=>200,'msg'=>'可用AG余额不足','data'=>'']);
            }
        }
        if($param['type'] == 2){//流通换可用
            if($user['flow_ag'] < $param['num']){
                return json(['code'=>200,'msg'=>'流通AG余额不足','data'=>'']);
            }
        }

        if($param['type']==1){//可用换流通
            Db::name('user')->where('id',$param['user_id'])->setDec('ag',$param['num']);
            Db::name('user')->where('id',$param['user_id'])->setInc('flow_ag',$param['num']*$site['useag_flowag_rate']);
            model('Order','logic','common')->addMoneyRecord('ag',$param['user_id'],$param['num'],'-','可用转流通');
        }
        if($param['type']==2){//流通换可用
            Db::name('user')->where('id',$param['user_id'])->setDec('flow_ag',$param['num']);
            Db::name('user')->where('id',$param['user_id'])->setInc('ag',$param['num']*$site['flowag_useag_rate']);
            model('Order','logic','common')->addMoneyRecord('ag',$param['user_id'],$param['num'],'+','流通换可用');
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    // 获取钱包信息
    public function getWallet(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) || empty($param['type']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        $wallet = Db::name('wallet')->where($param)->find();
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$wallet]);
    }
    // 添加钱包信息
    public function addWallet(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) || empty($param['type']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        $param['ctime'] = time();
        $res = Db::name('wallet')->insert($param);
        if($res){
            if($param['type'] == '银行卡'){
                Db::name('user')->where('id',$param['user_id'])->update(['is_bank'=>3]);
            }
            if($param['type'] == '支付宝'){
                Db::name('user')->where('id',$param['user_id'])->update(['is_ali'=>3]);
            }
            if($param['type'] == '微信'){
                Db::name('user')->where('id',$param['user_id'])->update(['is_wechat'=>3]);
            }
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }



    // ag交易doAgTrade
    public function doAgTrade(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $user_id = $param['user_id'];

        if($param['type'] == 1){
            $param['buyer_id'] = $param['user_id'];
        }
        if($param['type'] == 2){
            $param['seller_id'] = $param['user_id'];
        }
        $param['ctime'] = time();

        $type = $param['type'];
        $user_id = $param['user_id'];

        unset($param['user_id']);

        $where_ag['num'] = $param['num'];
        $where_ag['price'] = $param['price'];
    if($param['num']<0 || $param['price']<0){
        return json(['code'=>100,'msg'=>'参数错误','data'=>'']);
    }

    $site=Db::name("site")->find();
    if($site['switch_ag_trade']!=1){
        return json(['code'=>200,'msg'=>'暂未开放','data'=>'']);
}
        if( $param['price']<$site['transact_min'] ){
            return json(['code'=>100,'msg'=>$site['transact_min_err'],'data'=>'']);
        }
        if( $param['price'] >$site['transact_max'] ){
            return json(['code'=>100,'msg'=>$site['transact_max_err'],'data'=>'']);
        }
        $transact_time=$site['transact_time'];
        if(!empty($transact_time)){
            list($begin_time,$end_time)=explode('~',$transact_time);
            $begin_time=trim($begin_time);
            $end_time=trim($end_time);
            $curr_time=date("H:i:s");
            if($curr_time>=$begin_time && $curr_time<=$end_time){

            }else{
                return json(['code'=>100,'msg'=>$site['transact_time_err'],'data'=>'']);
            }
        }



        //判断是否有合适的单子
        if($param['type'] == 1){//我要买入，找合适的卖出
            $where_ag['type'] = 2;
            $where_ag['status'] = 0;
            $where_ag['seller_id'] = array('NEQ',$user_id);
            $agorder = Db::name('agorder')->where($where_ag)->find();
            if($agorder){//直接成交

                $where_c['id'] = $agorder['id'];
                $data_c['status'] = 2;
                $data_c['buyer_id'] = $user_id;
                $data_c['trade_time'] = time();
                $res2 = Db::name('agorder')->where($where_c)->update($data_c);
                if($res2){//买入交易成功。卖家+cny。买家+flow_ag，-cny
                    Db::name('user')->where('id',$agorder['seller_id'])->setInc('cny',$agorder['money']);
                    model('Order','logic','common')->addMoneyRecord('cny',$agorder['seller_id'],$agorder['money'],'+','ag交易卖出');

                    Db::name('user')->where('id',$data_c['buyer_id'])->setInc('flow_ag',$agorder['num']);
                    model('Order','logic','common')->addMoneyRecord('flow_ag',$data_c['buyer_id'],$agorder['num'],'+','ag交易买入');

                    Db::name('user')->where('id',$data_c['buyer_id'])->setDec('cny',$agorder['money']);
                    model('Order','logic','common')->addMoneyRecord('cny',$data_c['buyer_id'],$agorder['money'],'-','ag交易买入');

                    return json(['code'=>100,'msg'=>'交易成功','data'=>'']);
                }
            }
        }
        if($param['type'] == 2){//我要卖出，找合适的买入

            // 2020.11.11 start
            // 限制每日卖出数量
            $not_fit = $this->agSellLimit($user_id,$param['num']);
            if($not_fit) return $not_fit;
            // 2020.11.11 end


            $where_ag['type'] = 1;
            $where_ag['status'] = 0;
            $where_ag['buyer_id'] = array('NEQ',$user_id);

            $agorder = Db::name('agorder')->where($where_ag)->find();
            if($agorder){//直接成交

                $where_c['id'] = $agorder['id'];
                $data_c['status'] = 2;
                $data_c['seller_id'] = $user_id;
                $data_c['trade_time'] = time();
                $res2 = Db::name('agorder')->where($where_c)->update($data_c);
                if($res2){//卖出交易成功。买家+flow_ag。卖家-flow_ag，+cny
                    Db::name('user')->where('id',$agorder['buyer_id'])->setInc('flow_ag',$agorder['num']);
                    model('Order','logic','common')->addMoneyRecord('flow_ag',$agorder['buyer_id'],$agorder['num'],'+','ag交易买入');

                    Db::name('user')->where('id',$data_c['seller_id'])->setDec('flow_ag',$agorder['num']);
                    model('Order','logic','common')->addMoneyRecord('flow_ag',$data_c['seller_id'],$agorder['num'],'-','ag交易卖出');

                    Db::name('user')->where('id',$data_c['seller_id'])->setInc('cny',$agorder['money']);
                    model('Order','logic','common')->addMoneyRecord('cny',$data_c['seller_id'],$agorder['money'],'+','ag交易卖出');

                    // 2020.11.11 start
                    $this->agSellLimitSub($user_id,$param['num']);
                    // 2020.11.11 end

                    return json(['code'=>100,'msg'=>'交易成功','data'=>'']);
                }
            }
        }
        //判断是否有合适的单子    end
        $money =$param['price']*$param['num'];
        $param['money'] = $money;
        $user= Db::name('user')->where('id',$user_id)->find();
        if($type==1){
            if($user['cny']<$money){
                 return json(['code'=>100,'msg'=>'委托失败','data'=>'']);
            }
            $res = Db::name('agorder')->insert($param);
            Db::name('user')->where('id',$user_id)->setDec('cny',$money);
            model('Order','logic','common')->addMoneyRecord('cny',$user_id,$money,'-','ag交易挂单');
        }else{
            //return json(['code'=>100,'msg'=>'暂未开放','data'=>'']);
            if($user['flow_ag']<$param['num']){
                return json(['code'=>100,'msg'=>'委托失败','data'=>'']);
            }
            $res = Db::name('agorder')->insert($param);
            Db::name('user')->where('id',$user_id)->setDec('flow_ag',$param['num']);
            model('Order','logic','common')->addMoneyRecord('flow_ag',$user_id,$param['num'],'-','ag交易挂单');

            // 2020.11.11 start
            Db::name('ag_sell_limit')->where('user_id',$user_id)
                                     ->where('sell_date',strtotime(date('Y-m-d')))
                      ->setDec('num_left', $param['num']);
            // 2020.11.11 end
        }
        return json(['code'=>100,'msg'=>'委托成功','data'=>'']);
    }

    // ag交易列表
    public function getAgTradeList(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $u_list=[];
        $where['status'] = 0;
        $where['money'] = ['gt',0];
        if($param['type'] == 1){//买入委托
            $where['seller_id'] = 0;

            $where['buyer_id'] = $param['user_id'];
            $u_list = Db::name('agorder')->where($where)->order('ctime desc')->select();

            $where['buyer_id'] = array('NEQ',$param['user_id']);
            $list = Db::name('agorder')->where($where)->order('ctime desc')->select();
        }else{//卖出委托
             $list=[];

            $where['buyer_id'] = 0;

            $where['seller_id'] = $param['user_id'];
            $u_list = Db::name('agorder')->where($where)->order('ctime desc')->select();

            $where['seller_id'] = array('NEQ',$param['user_id']);
            //$list = Db::name('agorder')->where($where)->order('ctime desc')->select();
            $lim = 30 - count($u_list);
            $list = Db::name('agorder')->where($where)->order('ctime desc')->limit($lim)->select();

        }


        $list = array_merge($u_list,$list);
        foreach ($list as $k => $v) {
            $list[$k]['ctime'] = date('Y-m-d H:i',$v['ctime']);
        }
        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    }

    // 撤销交易
    public function cancelAgTrade(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $agorder=Db::name('agorder')->find($param['id']);
        if($agorder['status']!=0){
            return json(['code'=>100,'msg'=>'撤销失败','data'=>'']);
        }
        $agorder = Db::name('agorder')->where('id',$param['id'])->find();
        if($agorder['type']==1){
            if($agorder['buyer_id']!=$param['user_id']){
                return json(['code'=>100,'msg'=>'撤销失败','data'=>'']);
            }
            $res = Db::name('agorder')->where('id',$param['id'])->update(['status'=>3]);
            Db::name('user')->where('id',$param['user_id'])->setInc('cny',$agorder['money']);
            model('Order','logic','common')->addMoneyRecord('cny',$param['user_id'],$agorder['money'],'+','ag交易撤销');
        }
        if($agorder['type']==2){
            if($agorder['seller_id']!=$param['user_id']){
                return json(['code'=>100,'msg'=>'撤销失败','data'=>'']);
            }
            $res = Db::name('agorder')->where('id',$param['id'])->update(['status'=>3]);
            Db::name('user')->where('id',$param['user_id'])->setInc('flow_ag',$agorder['num']);
            model('Order','logic','common')->addMoneyRecord('flow_ag',$param['user_id'],$agorder['num'],'+','ag交易撤销');

            if($agorder['ctime'] >= strtotime(date('Y-m-d'))){
                Db::name('ag_sell_limit')->where('user_id', $param['user_id'])
                                         ->where('sell_date',strtotime(date('Y-m-d')))
                                         ->setInc('num_left',$agorder['num']);
            }

        }
        return json(['code'=>100,'msg'=>'成功','data'=>'']);
    }

    // 买入ag成交
    public function doBuyAg(){

        //return json(['code'=>200,'msg'=>'暂未开放','data'=>'']);
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        //sleep(2);
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $agorder = Db::name('agorder')->where('id',$param['id'])->lock(true)->find();
        if($agorder && $agorder['status']==0){//直接成交
            // 判断我的cny够不够
            if($user['cny'] < $agorder['money']){
                return json(['code'=>200,'msg'=>'CNY不足，无法购买','data'=>'']);
            }

            $where_c['id'] = $agorder['id'];
            $data_c['status'] = 2;
            $data_c['buyer_id'] = $param['user_id'];
            $data_c['trade_time'] = time();
            $res2 = Db::name('agorder')->where($where_c)->update($data_c);
            if($res2){//主动买入交易成功。买家+flow_ag，-cny。卖家+cny
                Db::name('user')->where('id',$data_c['buyer_id'])->setInc('flow_ag',$agorder['num']);
                model('Order','logic','common')->addMoneyRecord('flow_ag',$data_c['buyer_id'],$agorder['num'],'+','ag交易买入');

                Db::name('user')->where('id',$data_c['buyer_id'])->setDec('cny',$agorder['money']);
                model('Order','logic','common')->addMoneyRecord('cny',$data_c['buyer_id'],$agorder['money'],'-','ag交易买入');

                Db::name('user')->where('id',$agorder['seller_id'])->setInc('cny',$agorder['money']);
                model('Order','logic','common')->addMoneyRecord('cny',$agorder['seller_id'],$agorder['money'],'+','ag交易卖出');

                return json(['code'=>100,'msg'=>'交易成功','data'=>'']);
            }
        }else{
            return json(['code'=>200,'msg'=>'该交易已下架','data'=>'']);
        }
    }

    /**
     * 卖出限制
     */
    private function agSellLimit($user_id,$num){
        $site = Db::name('site')->where('id',1)->find();
        if($num > $site['ag_sell_limit']){
            return json(['code'=>200,'msg'=>'您今日剩余可卖出AG数量为'.$site['ag_sell_limit'],'data'=>'']);
        }

        $ag_sell_limit = Db::name('ag_sell_limit')->where('user_id', $user_id)
                                                      ->where('sell_date',strtotime(date('Y-m-d')))
                                                      ->find();
        if($ag_sell_limit){
            if($ag_sell_limit['num_left'] <= 0){
                return json(['code'=>200,'msg'=>'您今日卖出AG数已达上限,欢迎明天再来！','data'=>'']);
            }elseif($ag_sell_limit['num_left'] < $num){
                return json(['code'=>200,'msg'=>'您今日剩余可卖出AG数量为'.$ag_sell_limit['num_left'],'data'=>'']);
            }
            } else{
                $data = ['user_id'=>$user_id,
                         'sell_date'=>strtotime(date('Y-m-d')),
                         'num_left'=>(int)$site['ag_sell_limit']];
                Db::name('ag_sell_limit')->insert($data);
            }

    }

    private function agSellLimitSub($user_id ,$num){
        Db::name('ag_sell_limit')->where('user_id',$user_id)
                                 ->where('sell_date',strtotime(date('Y-m-d')))
                                 ->setDec('num_left', $num);
    }

    // 卖出ag成交
    public function doSellAg(){
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $user_id = $param['user_id'];

        sleep(2);

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $agorder = Db::name('agorder')->where('id',$param['id'])->lock(true)->find();
        if($agorder && $agorder['status']==0){//直接成交

            // 2020-11-21 start
            // 卖出数量限制
            $not_fit = $this->agSellLimit($user_id,$agorder['num']);
            if($not_fit) return $not_fit;
            // 2020-11-21 end

            // 判断我的flow_ag够不够
            if($user['flow_ag'] < $agorder['num']){
                return json(['code'=>200,'msg'=>'AG不足，无法购买','data'=>'']);
            }

            $where_c['id'] = $agorder['id'];
            $data_c['status'] = 2;
            $data_c['seller_id'] = $param['user_id'];
            $data_c['trade_time'] = time();

            $res2 = Db::name('agorder')->where($where_c)->update($data_c);
            if($res2){//主动卖出交易成功。卖家+cny，-flow_ag。买家+flow_ag。
                Db::name('user')->where('id',$data_c['seller_id'])->setInc('cny',$agorder['money']);
                model('Order','logic','common')->addMoneyRecord('cny',$data_c['seller_id'],$agorder['money'],'+','ag交易卖出');

                Db::name('user')->where('id',$data_c['seller_id'])->setDec('flow_ag',$agorder['num']);
                model('Order','logic','common')->addMoneyRecord('flow_ag',$data_c['seller_id'],$agorder['num'],'-','ag交易卖出');

                Db::name('user')->where('id',$agorder['buyer_id'])->setInc('flow_ag',$agorder['num']);
                model('Order','logic','common')->addMoneyRecord('flow_ag',$agorder['buyer_id'],$agorder['num'],'+','ag交易挂单买入成功');

                // 2020-11-21 start
                // 卖出限制
                $this->agSellLimitSub($user_id,$agorder['num']);
                // 2020-11-21 end

                return json(['code'=>100,'msg'=>'交易成功','data'=>'']);
            }
        }else{
            return json(['code'=>200,'msg'=>'该交易已下架','data'=>'']);
        }


    }

    // 获取价格表
    public function getAgList(){

        $param = $_POST;

        $param['status'] = 0;
        if($param['type'] == 1){
            $list = Db::name('agorder')->where($param)->order('ctime desc')->limit(5)->select();
        }
        if($param['type'] == 2){
            $list = Db::name('agorder')->where($param)->order('ctime desc')->limit(5)->select();
        }

        $t_id = 1;
        foreach ($list as $k => $v) {
            $list[$k]['t_id'] = $t_id++;
        }
        $list = array_reverse($list);
        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    }

    // 产品列表
    public function getProductList(){

        $param = $_POST;
        $where['status'] = '上架';
        $list = Db::name('product')->where($where)->order('weigh desc')->select();
        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    }
    function checkchooseproduct(){

        //select count(*) from cc_pintuan where user_id status='拼团' and product_id=0
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $where['user_id'] = $param['user_id'];
        $where['product_name'] = array('EQ','');
        $where['status'] = '拼中';
        $pintuan = Db::name('pintuan')->where($where)->find();
        if($pintuan){
            return json(['code'=>100,'msg'=>'成功','data'=>$pintuan['room_num']]);
        }else{
            return json(['code'=>200,'msg'=>'成功','data'=>'']);
        }
    }
    // 选择产品 checkchooseproduct
    public function chooseProduct(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $product = Db::name('product')->where('id',$param['product_id'])->find();
        $address = Db::name('address')->where('user_id',$param['user_id'])->find();

        $where['user_id'] = $param['user_id'];
        $where['room_num'] = $param['room_num'];
        $where['status'] = '拼中';

        $data['product_id'] = $param['product_id'];
        $data['product_name'] = $product['name'];

        $data['address_name'] = $address['name'];
        $data['address_detail'] = $address['address'];
        $data['address_tel'] = $address['tel'];


        Db::name('pintuan')->where($where)->update($data);

        return json(['code'=>100,'msg'=>'成功','data'=>'']);
    }

    // 获取地址
    public function getAddress(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $address = Db::name('address')->where('user_id',$param['user_id'])->find();
        if($address){
            return json(['code'=>100,'msg'=>'成功','data'=>$address]);
        }else{
            return json(['code'=>200,'msg'=>'成功','data'=>'']);
        }
    }


    // 获取已完成ag交易
    public function getFinishTrade(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $where['status'] = 2;

        if($param['type'] == 1){
            $where['buyer_id'] = $param['user_id'];
        }
        if($param['type'] == 2){
            $where['seller_id'] = $param['user_id'];
        }

        $list = Db::name('agorder')->where($where)->order("ctime desc")->select();
        foreach ($list as $k => $v) {
            $list[$k]['trade_time'] = date('Y-m-d H:i',$v['trade_time']);;
        }
        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    }


    // 新闻列表
    public function getNews(){

        $param = $_POST;

        $list = Db::name('news')->order('ctime desc')->select();
        foreach ($list as $k => $v) {
            $list[$k]['ctime'] = date('Y-m-d',$v['ctime']);
        }

        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    }

    // 新闻详情
    public function getNewsDetail(){

        $param = $_POST;

        $news = Db::name('news')->where('id',$param['id'])->find();
        $news['ctime'] = date('Y-m-d H:i:s',$news['ctime']);

        return json(['code'=>100,'msg'=>'成功','data'=>$news]);
    }

    // 积分产品列表
    public function getScoreProductList(){

        $param = $_POST;

        $where['status'] = '上架';
        $list = Db::name('score_product')->where($where)->order('weigh desc')->select();

        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    }

    // 兑换积分产品
    public function chooseScoreProduct(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $product = Db::name('score_product')->where('id',$param['id'])->find();
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $address = Db::name('address')->where('user_id',$param['user_id'])->find();

        if($user['sc'] < $product['price']){
            return json(['code'=>200,'msg'=>'sc不足','data'=>'']);
        }

        $data['user_id'] = $param['user_id'];
        $data['username'] = $user['username'];
        $data['product_name'] = $product['name'];
        $data['product_image'] = $product['cover_image'];
        $data['address_name'] = $address['name'];
        $data['address_tel'] = $address['tel'];
        $data['address'] = $address['address'];
        $data['ctime'] = time();

        $res = Db::name('scoreorder')->insert($data);
        if($res){
            Db::name('user')->where('id',$param['user_id'])->setDec('sc',$product['price']);
        }

        return json(['code'=>100,'msg'=>'成功','data'=>'']);
    }

    // 获取团队列表
    public function getTeamList(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $mywhere['level'] = array('NEQ','-1');
        if($param['type'] == 1){
            $where['father_id'] = $param['user_id'];
            // $list = Db::name('user')->where($mywhere)->where($where)->select();
            // $num = count($list);
            $list = Db::name('user')->where($mywhere)->where($where)->select();
            $num = Db::name('user')->where($mywhere)->where($where)->count();
        }
        if($param['type'] == 2){
            $where['grandfather_id'] = $param['user_id'];
            // $list = Db::name('user')->where($mywhere)->where($where)->select();
            // $num = count($list);
            $list = [];
            $num = Db::name('user')->where($mywhere)->where($where)->count();
        }
        if($param['type'] == 3){
            // $list = Db::name('user')->where('find_in_set('.$param['user_id'].',family_id)')->select();
            // $num = count($list);
            $list = [];
            $num = Db::name('user')->where('find_in_set('.$param['user_id'].',family_id)')->count();
        }
        if($param['type'] == 4){
            // $list = Db::name('user')->where($mywhere)->where('find_in_set('.$param['user_id'].',family_id)')->select();
            // $num = count($list);
            $list = [];
            $num = Db::name('user')->where($mywhere)->where('find_in_set('.$param['user_id'].',family_id)')->count();
        }
        if($param['type'] == 5){
            $user_list = Db::name('user')->where('find_in_set('.$param['user_id'].',family_id)')->select();
            $user_arr = array_column($user_list, 'id');
            $user_str = implode(',', $user_arr);

            if($user_str){
                $where_recharge['user_id'] = array('IN',$user_str);
                $where_recharge['status'] = '充值成功';
                $list = Db::name('recharge')->where($where_recharge)->sum('money');
            }else{
                $list = 0;
            }

            $list = round($list,6);
            $num = '';
        }
        if($param['type'] == 6){
            $user_list = Db::name('user')->where('find_in_set('.$param['user_id'].',family_id)')->select();
            $user_arr = array_column($user_list, 'id');
            $user_str = implode(',', $user_arr);

            if($user_str){
                $where_pintuan['user_id'] = array('IN',$user_str);
                $where_pintuan['status'] = '拼中';
                $list = Db::name('pintuan')->where($where_pintuan)->count()*700;
            }else{
                $list = 0;
            }

            $num = '';
        }

        return json(['code'=>100,'msg'=>'成功','data'=>$list,'num'=>$num]);
    }


    // 添加充值订单
    public function doRecharge(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $param['username'] = $user['username'];
        $param['ctime'] = time();

        Db::name('recharge')->insert($param);
        return json(['code'=>100,'msg'=>'成功','data'=>'']);
    }


    // 打乱随机词顺序
    public function doLuan(){

        $param = $_POST;

        $word_arr = explode(' ', $param['word']);
        shuffle($word_arr);
        return json(['code'=>100,'msg'=>'成功','data'=>$word_arr]);
    }

    // ag认购
    public function doAgBuy(){

        //return json(['code'=>200,'msg'=>'暂未开放','data'=>'']);

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['type']) || empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $site = Db::name('site')->where('id',1)->find();

        if($user['is_buy_ag'] == 1){
            return json(['code'=>200,'msg'=>'您已认购过，没有认购资格','data'=>'']);
        }

        if($param['type'] == 1){
            if($user['cny'] < 730){
                return json(['code'=>200,'msg'=>'您的CNY余额不足，未达到认购资格','data'=>'']);
            }
            $buy_ag = 30;
            $consume_cny = 30*(1/$site['cny_ag_rate']);

            Db::name('user')->where('id',$param['user_id'])->update(['is_buy_ag'=>1]);

            Db::name('user')->where('id',$param['user_id'])->setInc('ag',$buy_ag);
            model('Order','logic','common')->addMoneyRecord('ag',$param['user_id'],$buy_ag,'+','ag认购');

            Db::name('user')->where('id',$param['user_id'])->setDec('cny',$consume_cny);
            model('Order','logic','common')->addMoneyRecord('cny',$param['user_id'],$consume_cny,'-','ag认购消耗');
        }

        if($param['type'] == 2){
            if($user['cny'] < 7300){
                return json(['code'=>200,'msg'=>'您的CNY余额不足，未达到认购资格','data'=>'']);
            }

            $buy_ag = 300;
            $consume_cny = 300*(1/$site['cny_ag_rate']);

            Db::name('user')->where('id',$param['user_id'])->update(['is_buy_ag'=>1]);

            Db::name('user')->where('id',$param['user_id'])->setInc('ag',$buy_ag);
            model('Order','logic','common')->addMoneyRecord('ag',$param['user_id'],$buy_ag,'+','ag认购');

            Db::name('user')->where('id',$param['user_id'])->setDec('cny',$consume_cny);
            model('Order','logic','common')->addMoneyRecord('cny',$param['user_id'],$consume_cny,'-','ag认购消耗');

        }

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }


    // 编辑用户
    public function editUser(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失！','data'=>'']);
        }
        $user_id = $param['user_id'];
        unset($param['user_id']);
        Db::name('user')->where('id',$user_id)->update($param);

        return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }

    public function getExeName($fileName){

        $pathinfo = pathinfo($fileName);

        return strtolower($pathinfo['extension']);
    }
    /*图片上传*/
    public function upload(){

        exit('');
        // 获取表单上传文件 例如上传了001.jpg
        if(!empty($_FILES['file'])){
            //获取扩展名
            $exename  = $this->getExeName($_FILES['file']['name']);
            if($exename != 'png' && $exename != 'jpg' && $exename != 'gif'){
                exit('不允许的扩展名');
            }
            $dirname = "uploads/".date('Ymd').'/';
            if(!is_dir($dirname)){
                mkdir($dirname,0777);
            }
            $file_name = uniqid();
            $imageSavePath = ROOT_PATH . 'public' . DS . 'uploads/'.date('Ymd').'/'.$file_name.'.'.$exename;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $imageSavePath)){
                return '/uploads/'.date('Ymd').'/'.$file_name.'.'.$exename;
            }
        }

    }


    function agSwitch(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }

        $site = Db::name('site')->where('id',1)->find();
        $data=[
            'agBuy'=> $site['switch_ag_buy']=='1',
            'flowAg'=> $site['switch_flow_ag']=='1',
            'agTrade'=> $site['switch_ag_trade']=='1',
            'agOrder'=> $site['switch_ag_order']=='1',
            'agMarket'=>$site['switch_ag_market']=='1'
        ];
        if($param['user_id']){
            $user_id=$param['user_id'];
            $debue_user_ids=[
                '230'
            ];
            if(in_array($user_id,$debue_user_ids)){
                $data=[
                    'agBuy'=> true,
                    'flowAg'=> true,
                    'agTrade'=> true,
                    'agOrder'=> true,
                    'agMarket'=>true
                ];
            }
        }
        return json($data);
    }

    function checkTransac($num){

        $site=Db::name("site")->find();
        if($site['switch_ag_trade']!='1'){
            //return json(['code'=>200,'msg'=>'暂未开放','data'=>'']);
        }
        if( $num<$site['transact_min'] ){
            return json(['code'=>200,'msg'=>$site['transact_min_err'],'data'=>'']);
        }
        if( $num >$site['transact_max'] ){
            return json(['code'=>200,'msg'=>$site['transact_max_err'],'data'=>'']);
        }
        $transact_time=$site['transact_time'];
        if(!empty($transact_time)){
            list($begin_time,$end_time)=explode('~',$transact_time);
            $begin_time=trim($begin_time);
            $end_time=trim($end_time);
            $curr_time=date("H:i:s");
            if($curr_time>=$begin_time && $curr_time<=$end_time){
                return false;
            }else{
                return json(['code'=>200,'msg'=>$site['transact_time_err'],'data'=>'']);
            }
        }
        return false;
    }
    function bindpaypwd(){

        $user_id=getUserIdByToken(request()->post('user_id'));
        if(!$user_id){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $pay_pwd=request()->post('pay_pwd');
        if(preg_match('/[a-zA-Z]/',$pay_pwd)){
            if(preg_match('/\d/',$pay_pwd)){
                $pay_pwd=md5($pay_pwd.$user_id);
                Db::name("user")->where("id='{$user_id}' and pay_pwd=''")->update(['pay_pwd'=>$pay_pwd]);
                return json(['code'=>1,'msg'=>'操作成功']);
            }
        }
        return json(['code'=>0,'msg'=>'为了交易安全，交易密码必须包含字母及数字！']);
    }


    public function codeSendToUser(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if(!$user){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(!$user['mobile']){
            return json(['code'=>200,'msg'=>'用户未绑定手机号,请先绑定手机号再进行操作']);
        }
        $code = rand(100000, 999999);

        $res = $this->SMSSend($user['mobile'],(string)$code);
        if($res['Code'] != "OK"){
            return json(['code'=>200,'msg'=>'短信平台验证码发送失败' . $res['Message']]);
        }
        Cache::set("verify_code_". $user['mobile'], $code, 600);
        return json(['code'=>100,'msg'=>$res['Message']]);
    }

    public function codeSend(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if(!$user){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(!$param['mobile']){
            return json(['code'=>200,'msg'=>'缺少参数 mobile 或为空']);
        }

        $code = rand(100000, 999999);

        $res = $this->SMSSend($param['mobile'],(string)$code);
        if($res['Code'] != "OK"){
            return json(['code'=>200,'msg'=>'短信平台验证码发送失败' . $res['Message']]);
        }
        Cache::set("verify_code_". $param['mobile'], $code, 600);
        return json(['code'=>100,'msg'=>$res['Message']]);
    }

    public function bindMobile(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if(!$user){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if($user['mobile']){
            return json(['code'=>200,'msg'=>'您的账户已经绑定过手机号码!']);
        }
        if(empty($param['mobile'])){
            return json(['code'=>200,'msg'=>'缺少参数 mobile']);
        }
        if(empty($param['code'])){
            return json(['code'=>200,'msg'=>'缺少参数 code']);
        }

        $mobile = $param['mobile'];
        $code = $param['code'];

        $verifyCode = Cache::get('verify_code_'.$mobile);
        if(!$verifyCode){
            return json(['code'=>200,'msg'=>'请先获取验证码']);
        }
        if($verifyCode != $code){
            return json(['code'=>200,'msg'=>'验证码错误']);
        }

        Db::name('user')->where('id',$param['user_id'])->update(['mobile'=>$mobile]);
        return json(['code'=>100,'msg'=>'手机号绑定成功']);
    }

    public function resetMobile(){

        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        $user = Db::name('user')->where('id',$param['user_id'])->find();
        if(!$user){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $codeOld = $param['code_old'];
        $mobileNew = $param['mobile_new'];
        $codeNew = $param['code_new'];
        $verifyCodeOld = Cache::get('verify_code_'.$user['mobile']);
        if(!$verifyCodeOld){
            return json(['code'=>200,'msg'=>'请先获取原手机验证码']);
        }
        if($verifyCodeOld != $codeOld){
            return json(['code'=>200,'msg'=>'原手机验证码错误']);
        }

        $verifyCodeNew = Cache::get('verify_code_'.$mobileNew);
        if(!$verifyCodeNew){
            return json(['code'=>200,'msg'=>'请先获取新手机验证码']);
        }
        if($verifyCodeNew != $codeNew){
            return json(['code'=>200,'msg'=>'新手机验证码错误']);
        }

      Db::name('user')->where('id',$param['user_id'])->update(['mobile'=>$mobileNew]);
      return json(['code'=>100,'msg'=>'手机号修改成功']);
  }

    /**
     * 阿里云短信服务
     * {
     * "Message": "OK",
     * "RequestId": "EF322F87-5D13-40AE-812C-DF6B9A9C6F27",
     * "BizId": "920502678658765255^0",
     * "Code": "OK"
     * }
     */
    public function SMSSend($phoneNumbers, $code, $signName = "智慧生态", $TemplateCode = "SMS_205444398") {
        // Download：https://github.com/aliyun/openapi-sdk-php
        // Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md

        AlibabaCloud::accessKeyClient('LTAI4GJ1NP3hrdnZS8SQFcj1', '2rB2jQtDHtFJxOdFXcQYmsNH95u35u')
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                    ->product('Dysmsapi')
                    // ->scheme('https') // https | http
                    ->version('2017-05-25')
                    ->action('SendSms')
                    ->method('POST')
                    ->host('dysmsapi.aliyuncs.com')
                    ->options([
                        'query' => [
                            'RegionId' => "cn-hangzhou",
                            'PhoneNumbers' => $phoneNumbers,
                            'SignName' => $signName,
                            'TemplateCode' => $TemplateCode,
                            'TemplateParam' => "{\"code\":\"" . $code . "\"}",
                        ],
                    ])
                    ->request();
            //print_r($result->toArray());
            return $result->toArray();
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }

    public function doAgTransfer(){
            return json(['code'=>200,'msg'=>'暂未开放','data'=>'']);
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if(empty($param['num'])){
            return json(['code'=>200,'msg'=>'缺少参数 num']);
        }
        if(empty($param['to_username'])){
            return json(['code'=>200,'msg'=>'缺少参数 to_username']);
        }
        if(empty($param['code'])){
            return json(['code'=>200,'msg'=>'缺少参数 code']);
        }

        $user= Db::name('user')->where('id',$param['user_id'])->find();
        $to_user= Db::name('user')->where('username',$param['to_username'])->find();
        if(!$to_user){
            return json(['code'=>200,'msg'=>'目标用户不存在']);
        }
        $code_cache = Cache::get('verify_code_'.$user['mobile']);
        if(!$param['code'] || !$code_cache){
            return json(['code'=>200,'msg'=>'短信验证码错误']);
        }else{
            if($param['code'] != $code_cache){
                return json(['code'=>200,'msg'=>'短信验证码错误']);
            }
        }
        Cache::rm('verify_code_'.$user['mobile']);


        $num = $param['num'];
        $user_flow_ag = Db::name('user')->where('id',$param['user_id'])->value('flow_ag');

        if($num > $user_flow_ag){
            return json(['code'=>200,'msg'=>'数量不能超过您的流通AG余额'.$user_flow_ag]);
        }

        $flow_ag_charge_percent = Db::name('site')->where('id',1)->value('flow_ag_charge_percent');
        if($flow_ag_charge_percent <= 0){
            $flow_ag_charge = 0;
            $user_flow_ag_left = bcsub($user_flow_ag, $num, 2);
        }else{
            $user_flow_ag_left = bcsub($user_flow_ag, $num, 2);
            $flow_ag_charge = bcdiv(bcmul($num,$flow_ag_charge_percent, 2),100,2);

            if($user_flow_ag_left < $flow_ag_charge){
                $num = bcsub($num,bcsub($flow_ag_charge,$user_flow_ag_left,2),2);
                $user_flow_ag_left = 0;
            }else{
                $user_flow_ag_left = bcsub($user_flow_ag_left,$flow_ag_charge,2);
            }
        }


        try {
            Db::startTrans();
            Db::name('user')->where('id',$user['id'])->SetDec('flow_ag',$num);
            Db::name('user')->where('username',$param['to_username'])->SetInc('flow_ag',$num);


            Db::name('ag_transfer_record')->insert(['from_user_id'=>$user['id'],
                                                    'to_user_id'=>$to_user['id'],
                                                    'flow_ag'=>$num,
                                                    'charge'=>$flow_ag_charge,
                                                    'ctime'=>time()]);

            model('Order','logic','common')->addMoneyRecord('flow_ag',$user['id'],$num,'-','AG点对点转账');
            model('Order','logic','common')->addMoneyRecord('flow_ag',$to_user['id'],$num,'+','AG点对点转账');
            Db::commit();
        }catch (\Exception $e) {
            //获取到异常信息回滚至操作前的状态
            Db::rollback();
            return json(['code'=>200,'msg'=>'AG点对点转账失败']);
            // $this->error($e);
        }

        return json(['code'=>100,'msg'=>'AG点对点转账成功']);

    }

        public function getAgTransferCharge(){
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
       $flow_ag_charge_percent = Db::name('site')->where('id',1)->value('flow_ag_charge_percent');
        $user_flow_ag = Db::name('user')->where('id',$param['user_id'])->value('flow_ag');
        if(empty($param['num'])){
            // return json(['code'=>200,'msg'=>'缺少参数 num']);
            $res = [];
            $res['flow_ag_left'] = $user_flow_ag;
            $res['charge_percent'] = $flow_ag_charge_percent;
            // $res['charge'] = $flow_ag_charge;
            // $res['num'] = $num;
            return json(['code'=>100,'msg'=>'请求发送成功','data'=>$res]);
        }

        $num = $param['num'];

        if($num > $user_flow_ag){
            return json(['code'=>200,'msg'=>'数量不能超过您的流通AG余额'.$user_flow_ag]);
        }

        if($flow_ag_charge_percent <= 0){
            $flow_ag_charge = 0;
            $user_flow_ag_left = bcsub($user_flow_ag, $num, 2);
        }else{
            $user_flow_ag_left = bcsub($user_flow_ag, $num, 2);
            $flow_ag_charge = bcdiv(bcmul($num,$flow_ag_charge_percent, 2),100,2);

            if($user_flow_ag_left < $flow_ag_charge){
                $num = bcsub($num,bcsub($flow_ag_charge,$user_flow_ag_left,2),2);
                $user_flow_ag_left = 0;
            }else{
                $user_flow_ag_left = bcsub($user_flow_ag_left,$flow_ag_charge,2);
            }
        }

        $res = [];
        $res['flow_ag_left'] = $user_flow_ag_left;
        $res['charge_percent'] = $flow_ag_charge_percent;
        $res['charge'] = $flow_ag_charge;
        $res['num'] = $num;

        return json(['code'=>100,'msg'=>'请求发送成功','data'=>$res]);
    }



}
