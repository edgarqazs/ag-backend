<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use app\common\controller\Frontend;
use think\Db;
use Endroid\QrCode\QrCode;

class Pintuan extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    
    function selectgoods(){ 
                
        // $user_id=request()->post('user_id');
        $user_id=getUserIdByToken(request()->post('user_id'));
        if(!$user_id){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        // if($user_id!=9){
        //     return json(['code'=>0,'msg'=>'','data'=>'']);
        // }
        
        
        $goods_id=request()->post('goods_id');
        $id=request()->post('id');
        $address = Db::name('address')->where('user_id',$user_id)->find();
        $pintuan_data=Db::name("pintuan")->find($id);
        if(   !empty($address)
            &&!empty($pintuan_data)
            &&empty($pintuan_data['product_id'])
            && $pintuan_data['user_id']==$user_id 
            && $pintuan_data['status']=='拼中'
            ){
            $product = Db::name('product')->where('id',$goods_id)->find();
            $data=[];
            $data['product_id']=$goods_id;
            $data['product_name'] = $product['name'];
            $data['address_name'] = $address['name'];
            $data['address_detail'] = $address['address'];
            $data['address_tel'] = $address['tel'];
            Db::name('pintuan')->where("id='{$id}'")->update($data);
        }

        return json(['code'=>1,'msg'=>'操作成功','data'=>'']);
        
        
        
    }
    /**
     * 
     * 给我的订单用的
     */
    function ptorder(){
                
        $limit=request()->post('limit',50);
        $page=request()->post('page',1);
        // $user_id=request()->post("user_id");//连 user_id 都是前台传过来 真他么牛逼 666
        $user_id=getUserIdByToken(request()->post('user_id'));
        if(!$user_id){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $data=Db::name("pintuan")
            ->where("user_id='{$user_id}' and status='拼中'")
            ->order("id desc")
            ->page($page)
            ->limit($limit)
            ->select();
        if(!empty($data)){
            if(!is_array($data)){
                $data=$data->toArray();//他么的 tp6 有可能不是数组，没准，宁可多写点
            }
            $goods_list222=Db::name("product")->select();
            foreach ($goods_list222 as $item){
                $goods_list[$item['id']]=$item['cover_image'];
            }
            foreach($data as &$item){
                $item['ctime']=date("Y-m-d H:i",$item['ctime']);
                if($item['product_id']>0){
                    $item['img']=isset($goods_list[$item['product_id']])?$goods_list[$item['product_id']]:'';
                }
            }
        }else{ 
            $data=[];
        }
        return json(['data'=>$data]);
    }
    // public function test(){
    //     model('Pintuan','logic','common')->addTeamMoney(1);
    // }

    // 判断是否有未完成拼团
    public function weiPintuan(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        
        if( empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();

        if($user['pintuan_num']!=0){
            return json(['code'=>200,'msg'=>'您上次有未完成的拼团，即将进入','data'=>'']);
        }else{
            $where['user_id'] = $param['user_id'];
            $where['status'] = '未开奖';
            $pintuan = Db::name('pintuan')->where($where)->find();
            if($pintuan){
                return json(['code'=>200,'msg'=>'您上次有未完成的拼团，即将进入','data'=>'']);
            }else{
                return json(['code'=>100,'msg'=>'正常进入','data'=>'']);
            }
        }

    }

    // 获取拼团记录
    public function getJoinList(){
                
        $param = $_POST;
        // $param['user_id'] = getUserIdByToken($param['user_id']);
        // if(!$param['user_id']){
        //     return json(['code'=>200,'msg'=>'用户不存在']);
        // }
        if( empty($param['room_num']) ){
            return json(['code'=>200,'msg'=>'参数缺失']);
        }


        $list = Db::name('pintuan')->where('room_num',$param['room_num'])->select();

        $pintuan_robot_list = Db::name('pintuan_robot')->where('room_num',$param['room_num'])->select();
        $list = array_merge($list,$pintuan_robot_list);

        $count = count($list);
        if($count>10){
            for ($i=10; $i < $count; $i++) { 
                unset($list[$i]);
            }
        }
        if($count>=10){
            return json(['code'=>200,'msg'=>'人数够了','data'=>$list]);
        }else{
            return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
        }
    }


    // 添加拼团记录
    public function addJoin(){
                
    	$param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);        
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if( empty($param['user_id']) || empty($param['num']) ){
            return json(['code'=>200,'msg'=>'参数缺失']);
        }

        $user = Db::name('user')->where('id',$param['user_id'])->find();
        $site = Db::name('site')->where('id',1)->find();

        if(time()<$site['pintuan_open_time'] || time()>$site['pintuan_close_time']){
            return json(['code'=>200,'msg'=>'暂未开放！','data'=>'']);
        }

        // 判断是否还在拼团中
        $where_pintuan['user_id'] = $param['user_id'];
        $where_pintuan['status'] = '未开奖';
        // $where_pintuan['ctime'] = array('GT',time()-10);
        $pintuan = Db::name('pintuan')->where($where_pintuan)->find();
        if($pintuan){
        	return json(['code'=>100,'msg'=>'登录成功！','data'=>$pintuan['room_num']]);   
        }
        // 判断是否还在拼团中	end

        // 2020.11.10 start
        // 增加用户每日拼团次数控制
        $pintuan_num_array = [1=>'one',5=>'five',10=>'ten',20=>'twenty',30=>'thirty',50=>'fifty'];
        $pintuan_num_limit = Db::name('user_pintuan_num')->where('user_id',$param['user_id'])
                                                         ->where('num_zone', $param['num'])
                                                         ->where('pin_date',strtotime(date('Y-m-d')))
                                                         ->find();
        if($pintuan_num_limit){
            if($pintuan_num_limit['num_left'] <= 0){
                return json(['code'=>200,'msg'=>'今日拼团次数已达上限,欢迎明天再来！','data'=>'']);
            }
        } else{
            if($site['pintuan_'.$pintuan_num_array[$param['num']].'_num'] <= 0){
                return json(['code'=>200,'msg'=>'今日拼团次数已达上限,欢迎明天再来！','data'=>'']);
            }else{
                $data = ['user_id'=>$user['id'],
                         'num_zone'=>(int)$param['num'],
                         'pin_date'=>strtotime(date('Y-m-d')),
                         'num_left'=>(int)$site['pintuan_'.$pintuan_num_array[$param['num']].'_num']];
                Db::name('user_pintuan_num')->insert($data);
            }
        }
        // 2020.11.10 end

        if($user['pintuan_num'] > 0){//还有次数，不扣钱
            // 减去次数
            Db::name('user')->where('id',$param['user_id'])->setDec('pintuan_num');
        }else{
            // CNY不够或AG不够，不让拼团
            $consume = Db::name('consume')->where('num',$param['num'])->find();
            if( $user['ag'] < $consume['ag']*$site['cny_ag_rate'] ){
                return json(['code'=>201,'msg'=>'AG不足','data'=>'']);
            }
            if( $user['cny'] < $consume['cny'] ){
                return json(['code'=>202,'msg'=>'CNY不足','data'=>'']);
            }
            // CNY不够或AG不够，不让拼团  end

            // 未激活用户=>普通用户
            if($user['level'] == -1){
                Db::name('user')->where('id',$param['user_id'])->update(['level'=>0]);
                model('Pintuan','logic','common')->addTeamNum($param['user_id']);
            }
            // 未激活用户=>普通用户  end

            // 增加上级分享人数
            if($user['is_add_share'] == 2){
                Db::name('user')->where('id',$param['user_id'])->update(['is_add_share'=>1]);
                model('Pintuan','logic','common')->addShareNum($user['father_id']);
            }
            // 增加上级分享人数 end

            // 减CNY和AG
            Db::name('user')->where('id',$param['user_id'])->setDec('cny',$consume['cny']);
            Db::name('user')->where('id',$param['user_id'])->setDec('ag',$consume['ag']*$site['cny_ag_rate']);
            model('Order','logic','common')->addMoneyRecord('cny',$param['user_id'],$consume['cny'],'-','拼团消耗');
            model('Order','logic','common')->addMoneyRecord('ag',$param['user_id'],$consume['ag']*$site['cny_ag_rate'],'-','拼团消耗');
            // 减CNY和AG		end
            Db::name('user')->where('id',$param['user_id'])->setInc('pintuan_num',$param['num']);//增加用户剩余拼团次数

            Db::name('user')->where('id',$param['user_id'])->setDec('pintuan_num');

            // $user = Db::name('user')->where('id',$param['user_id'])->find();
            // if($user['pintuan_num'] <= 0){
            //     return json(['code'=>203,'msg'=>'拼团结束','data'=>'']);
            // }

            // 增加用户今日拼团次数
            if($param['num']==1){
                Db::name('user')->where('id',$param['user_id'])->setInc('today_one_num');
            }
            if($param['num']==5){
                Db::name('user')->where('id',$param['user_id'])->setInc('today_five_num');
            }
            if($param['num']==10){
                Db::name('user')->where('id',$param['user_id'])->setInc('today_ten_num');
            }
            if($param['num']==20){
                Db::name('user')->where('id',$param['user_id'])->setInc('today_twenty_num');
            }
            if($param['num']==30){
                Db::name('user')->where('id',$param['user_id'])->setInc('today_thirty_num');
            }
            if($param['num']==50){
                Db::name('user')->where('id',$param['user_id'])->setInc('today_fifty_num');
            }
            // 增加用户今日拼团次数  end

        }
        

    	$site = Db::name('site')->where('id',1)->find();

        // 2020.11.10 start
        // 增加用户每日拼团次数控制
        $pintuan_num_limit = Db::name('user_pintuan_num')->where('user_id',(int)$param['user_id'])
                                                         ->where('num_zone', (int)$param['num'])
                                                         ->where('pin_date',strtotime(date('Y-m-d')))
                                                         ->setDec('num_left');
        // 2020.11.10 end

        // 是否存在未开奖的房间
        $where_pin['status'] = '未开奖';
        $pintuan = Db::name('pintuan')->where($where_pin)->find();
        if($pintuan){//进这个房间
            
            // 添加用户拼团记录
            model('Pintuan','logic','common')->addRecord($param['user_id'],$pintuan['room_num']);
            // 添加用户拼团记录 end
            model('Pintuan','logic','common')->resultPintuan($pintuan['room_num']);

            return json(['code'=>100,'msg'=>'登录成功！','data'=>$pintuan['room_num']]);
        }else{//开新房间
            Db::name('site')->where('id',1)->setInc('room_num');
            $room_num = $site['room_num']+1;
            // 添加用户拼团记录
            model('Pintuan','logic','common')->addRecord($param['user_id'],$room_num);
            // 添加用户拼团记录 end

            return json(['code'=>100,'msg'=>'登录成功！','data'=>$room_num]);
        }
        // 是否存在未开奖的房间   end


        //房间满10人，上局已结束，重置房间
        // if( $site['people_num'] >=10 ){
        //     $data_site['room_num'] = $site['room_num']+1;
        //     $data_site['people_num'] = 1;
        //     $data_site['start_time'] = time();
        //     Db::name('site')->where('id',1)->update($data_site);

        //     $room_num = $site['room_num']+1;
        //     // 添加用户拼团记录
        //     model('Pintuan','logic','common')->addRecord($param['user_id'],$room_num);
        //     // 添加用户拼团记录 end
        //     return json(['code'=>100,'msg'=>'登录成功！','data'=>$room_num]);   
        // }

        // $site = Db::name('site')->where('id',1)->find();
        // if($site['people_num'] == 0){//新房间没人，成为第一人
        //     $data['start_time'] = time();
        //     $data['people_num'] = 1;
        //     Db::name('site')->where('id',1)->update($data);

        //     $room_num = $site['room_num'];
        //     // 添加用户拼团记录
        //     model('Pintuan','logic','common')->addRecord($param['user_id'],$room_num);
        //     // 添加用户拼团记录 end
        // }else{
        //     Db::name('site')->where('id',1)->setInc('people_num');

        //     $room_num = $site['room_num'];
        //     // 添加用户拼团记录
        //     model('Pintuan','logic','common')->addRecord($param['user_id'],$room_num);
        //     // 添加用户拼团记录 end
        // }

return json(['code'=>100,'msg'=>'登录成功！','data'=>$room_num]);	
    }

    // 开奖
    public function resultPintuan(){
        
                
    	$param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if( empty($param['room_num']) || empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失']);   
        }
 
        $pintuan = Db::name('pintuan')->where($param)->find();
        if($pintuan['status'] == '未开奖'){
        	return json(['code'=>200,'msg'=>'登录成功！','data'=>'']);	
        }else{
        	return json(['code'=>100,'msg'=>'登录成功！','data'=>$pintuan['status']]);
        }
        
    }

    // 获取拼团记录
    public function getPintuanList(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        if( empty($param['user_id']) ){
            return json(['code'=>200,'msg'=>'参数缺失']);   
        }

        $where['user_id'] = $param['user_id'];
        if($param['type'] == 1){
            $where['status'] = '拼中';
            // $where['product_name'] = array('NEQ','');
        }else{
            $where['status'] = '未拼中';
        }

        $list = Db::name('pintuan')->where($where)->order('ctime desc')->select();
        foreach ($list as $k => $v) {
            $list[$k]['ctime'] = date('Y-m-d H:i',$v['ctime']);
        }
        return json(['code'=>100,'msg'=>'登录成功！','data'=>$list]);
    }

    // 收益记录
    // type 1：推荐收益，2：团队收益，3：节点收益，4：加权分红
    public function getRecordList(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $where['user_id'] = $param['user_id'];
        if($param['type'] == 1){
            $list = Db::name('order_share')->where($where)->order('ctime desc')->select();
        }
        if($param['type'] == 2){
            $list = Db::name('order_team')->where($where)->order('ctime desc')->select();
        }
        if($param['type'] == 3){
            $list = Db::name('order_node')->where($where)->order('ctime desc')->select();
        }
        if($param['type'] == 4){
            $list = Db::name('order_company')->where($where)->order('ctime desc')->select();
        }
        foreach ($list as $k => $v) {
            $list[$k]['ctime'] = date('Y-m-d H:i',$v['ctime']);
        }
        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    
    }

    // 资金变动记录
    // type 
    public function getMoneyRecordList(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $where['user_id'] = $param['user_id'];
        if($param['type']=='ag'){
            //
            $where['type']=['in',['ag','lock_ag','freeze_ag','flow_ag']];
        }else{
            $where['type'] = $param['type'];
        }
        
        
        $list = Db::name('money_record')->where($where)->order('ctime desc')->limit(50)->select();
        $types=['usdt'=>'USDT','cny'=>'CNY','ag'=>'AG','sc'=>'SC','freeze_usdt'=>'冻结USDT','lock_ag'=>'锁仓AG','freeze_ag'=>'冻结AG','flow_ag'=>'流通AG','ag_card'=>'兑换卡','qc'=>'QC'];
        foreach ($list as $k => &$v) {
            $list[$k]['ctime'] = date('Y-m-d H:i',$v['ctime']);
            $v['type']=' '.$types[$v['type']];
            $v['money']*=100;
            $v['money']=intval($v['money'])/100;
            
        }
        return json(['code'=>100,'msg'=>'成功','data'=>$list]);
    
    }

    // 获取拼团广告
    public function getPintuanAd(){
                
        $list = Db::name('pintuanad')->order('weigh desc')->select();
        return json(['code'=>100,'msg'=>'成功','data'=>$list]);    
    }
    
    // 检查是否存在未选商品
    public function checkChooseProduct(){
                
        $param = $_POST;
        $param['user_id'] = getUserIdByToken($param['user_id']);
        if(!$param['user_id']){
            return json(['code'=>200,'msg'=>'用户不存在']);
        }
        $where['user_id'] = $param['user_id'];
        $where['product_name'] = array('EQ','');
        $pintuan = Db::name('pintuan')->where($where)->find();
        if($pintuan){
            return json(['code'=>100,'msg'=>'成功','data'=>$pintuan['room_num']]);
        }else{
            return json(['code'=>200,'msg'=>'成功','data'=>'']);
        }        
    }

}
