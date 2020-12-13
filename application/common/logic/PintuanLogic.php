<?php



namespace app\common\logic;
use think\Model;
use think\Db;
use think\Config;
use think\Cache;
class PintuanLogic
{

    // 同步房间人数
    public function updateRoomPeople($room_num){

        $people_num = Db::name('pintuan')->where('room_num',$room_num)->count();
        $robot_num = Db::name('pintuan_robot')->where('room_num',$room_num)->count();

        $where['room_num'] = $room_num;
        $data['people_num'] = $people_num+$robot_num;
        Db::name('pintuan')->where($where)->update($data);

        return true;
    }    

    // 添加机器人
    public function addRobot($room_num){
        $robot_list = Db::name('robot')->select();
        $robot_arr = array_column($robot_list, 'id');
        $res_robot = uni($robot_arr,10);

        $robot = Db::name('robot')->where('id',$res_robot[0])->find();
        $data['room_num'] = $room_num;
        $data['head_image'] = $robot['head_image'];
        $data['nickname'] = $robot['nickname'];
        Db::name('pintuan_robot')->insert($data);

        model('Pintuan','logic','common')->updateRoomPeople($room_num);

        // model('Pintuan','logic','common')->resultPintuan($room_num);
    
    }    

    /*
        method：给上级增加团队人数
        参数
        user_id             用户id
    */
    public function addTeamNum($user_id){
        $user = Db::name('user')->where('id',$user_id)->find();
        $family_arr = explode(',', $user['family_id']);
        
        model('Pintuan','logic','common')->updateLevel($user_id);//更新级别

        foreach ($family_arr as $k => $v) {
            Db::name('user')->where('id',$v)->setInc('team_num');
            model('Pintuan','logic','common')->updateLevel($v);//更新级别
        }
        return true;
    }

    /*
        method：给上级增加分享人数
        参数
        user_id             用户id
    */
    public function addShareNum($user_id){
        Db::name('user')->where('id',$user_id)->setInc('share_num');
        model('Pintuan','logic','common')->updateLevel($user_id);//更新级别
        return true;
    }

    /*
        method：更新升级
        参数
        user_id             用户id
    */
    public function updateLevel($user_id){
        $user = Db::name('user')->where('id',$user_id)->find();

        $level_list = Db::name('level')->order('id desc')->select();
        foreach ($level_list as $k => $v) {
            if($v['share_num']<=$user['share_num'] && $v['team_num']<=$user['team_num'] && $v['v1_num']<=$user['v1_num'] && $v['v2_num']<=$user['v2_num'] && $v['v3_num']<=$user['v3_num']){

                if($user['level'] < $v['id'] && $user['level'] != -1){
                    Db::name('user')->where('id',$user_id)->update(['level'=>$v['id']]);

                    if($v['id'] == 1){
                        $where_user['id'] = array('IN',$user['family_id']);
                        Db::name('user')->where($where_user)->setInc('v1_num');
                    }
                    if($v['id'] == 2){
                        $where_user['id'] = array('IN',$user['family_id']);
                        Db::name('user')->where($where_user)->setInc('v2_num');
                    }
                    if($v['id'] == 3){
                        $where_user['id'] = array('IN',$user['family_id']);
                        Db::name('user')->where($where_user)->setInc('v3_num');
                    }
                    if($v['id'] == 4){
                        $where_user['id'] = array('IN',$user['family_id']);
                        Db::name('user')->where($where_user)->setInc('v4_num');
                    }
                    
                }
                return true;
            }
        }

        return true;
    }

    /*
        method：添加拼团记录
        参数
        user_id             用户id
        room_num            房间编号
    */
    public function addRecord($user_id,$room_num){
        $user = Db::name('user')->where('id',$user_id)->find();

        $data['room_num'] = $room_num;
        $data['user_id'] = $user_id;
        $data['head_image'] = $user['head_image'];
        $data['nickname'] = $user['nickname'];
        $data['ctime'] = time();

        Db::name('pintuan')->insert($data);


        model('Pintuan','logic','common')->updateRoomPeople($room_num);

        Db::name('user')->where('id',$user_id)->setInc('ten_pintuan_num');

        return true;
    }

    /*
        method：开奖
        参数
        room_num            房间编号
    */
    public function resultPintuan($room_num){

        $where['room_num'] = $room_num;
        $where['status'] = '未开奖';
        $where['people_num'] = array('EGT',10);
        $list = Db::name('pintuan')->where($where)->select();
        if(empty($list)){
            return false;
        }
        
        // foreach ($list as $k => $v) {
            
        //     $user = Db::name('user')->where('id',$v['user_id'])->find();

        //     $is_zhong = rand(0,10-$user['ten_pintuan_num']);
        //     if($is_zhong == 0 && $user['is_zhong']!=1 ){//拼中
        //         Db::name('user')->where('id',$v['user_id'])->update(['is_zhong'=>1]);

        //         Db::name('pintuan')->where('id',$v['id'])->update(['status'=>'拼中']);
        //         // model('Pintuan','logic','common')->getZhongPintuan($v['user_id']);
        //     }else{//未拼中
        //         Db::name('pintuan')->where('id',$v['id'])->update(['status'=>'未拼中']);
        //         // model('Pintuan','logic','common')->getWeiPintuan($v['user_id']);
        //     }

        //     if($user['ten_pintuan_num'] >= 10){
        //         Db::name('user')->where('id',$v['user_id'])->update(['is_zhong'=>2]);
        //         Db::name('user')->where('id',$v['user_id'])->update(['ten_pintuan_num'=>0]);
        //     }
        // }
        // return true;

        if($list){
            $count = count($list);
            $temp = rand(0,$count-1);//中奖人下标
            $zhong_id = $list[$temp]['id'];//中奖人id
        }

        foreach ($list as $k => $v) {
            if($zhong_id == $v['id']){//中奖
                Db::name('pintuan')->where('id',$v['id'])->update(['status'=>'拼中']);
                // model('Pintuan','logic','common')->getZhongPintuan($v['user_id']);
            }else{
                Db::name('pintuan')->where('id',$v['id'])->update(['status'=>'未拼中']);
                // model('Pintuan','logic','common')->getWeiPintuan($v['user_id']);
            }
        }

        return $zhong_id;
        
    }

    /*
        method：获取中奖奖励
        参数
        user_id            用户id
    */
    public function getZhongPintuan($user_id){
        $site = Db::name('site')->where('id',1)->find();
        // 返还本金700CNY*比例
        Db::name('user')->where('id',$user_id)->setInc('cny',700*$site['zhong_rate']);
        // 获得一张AG通证兑换卡
        Db::name('user')->where('id',$user_id)->setInc('ag_card');
        // 增加用户加权分红资格数
        Db::name('user')->where('id',$user_id)->setInc('zhong_num');
        // 增加加权分红资格数
        $user = Db::name('user')->where('id',$user_id)->find();
        if($user['zhong_num']%50==0){
            Db::name('user')->where('id',$user_id)->setInc('other_num');
        }
        // 增加分享奖励
        model('Pintuan','logic','common')->addShareMoney($user_id);
        // 增加团队奖励
        model('Pintuan','logic','common')->addTeamMoney($user_id);
    }

    /*
        method：获取未中奖奖励
        参数
        user_id            用户id
    */
    public function getWeiPintuan($user_id){
        $site = Db::name('site')->where('id',1)->find();
        // 返还本金700CNY*比例
        Db::name('user')->where('id',$user_id)->setInc('cny',700*$site['wei_rate']);
        model('Order','logic','common')->addMoneyRecord('cny',$user_id,700*$site['wei_rate'],'+','未拼中退还');
        // 失败者奖励10CNY*0.8和拼团收益
        Db::name('user')->where('id',$user_id)->setInc('cny',$site['fail_cny']*0.8);
        model('Order','logic','common')->addMoneyRecord('cny',$user_id,$site['fail_cny']*0.8,'+','未拼中转入');
        Db::name('user')->where('id',$user_id)->setInc('pintuan_money',$site['fail_cny']*0.8);

        // 失败者奖励10CNY*0.1的SC和QC
        Db::name('user')->where('id',$user_id)->setInc('sc',$site['fail_cny']*0.1);
        Db::name('user')->where('id',$user_id)->setInc('qc',$site['fail_cny']*0.1);
        model('Order','logic','common')->addMoneyRecord('sc',$user_id,$site['fail_cny']*0.1,'+','未拼中转入');
        model('Order','logic','common')->addMoneyRecord('qc',$user_id,$site['fail_cny']*0.1,'+','未拼中转入');

        // 增加分享奖励
        model('Pintuan','logic','common')->addShareMoney($user_id);
        // 增加团队奖励
        model('Pintuan','logic','common')->addTeamMoney($user_id);
    }

    /*
        method：给上级，上上级，增加分享奖励和推荐受益
        参数
        user_id            用户id
    */
    public function addShareMoney($user_id){
        $site = Db::name('site')->where('id',1)->find();
        $user = Db::name('user')->where('id',$user_id)->find();
        if($user['father_id']){
        	$father = Db::name('user')->where('id',$user['father_id'])->find();
        	if($father['level'] != -1){
        		Db::name('user')->where('id',$user['father_id'])->setInc('cny',$site['level_one']*0.8);
	            model('Order','logic','common')->addRecord(1,$user['father_id'],$site['level_one']*0.8);
                model('Order','logic','common')->addMoneyRecord('cny',$user['father_id'],$site['level_one']*0.8,'+','直推转入');

	            Db::name('user')->where('id',$user['father_id'])->setInc('sc',$site['level_one']*0.1);
	            Db::name('user')->where('id',$user['father_id'])->setInc('qc',$site['level_one']*0.1);
	            model('Order','logic','common')->addMoneyRecord('sc',$user['father_id'],$site['level_one']*0.1,'+','直推转入');
	            model('Order','logic','common')->addMoneyRecord('qc',$user['father_id'],$site['level_one']*0.1,'+','直推转入');
        	}
        }
        if($user['grandfather_id']){
        	$grandfather = Db::name('user')->where('id',$user['grandfather_id'])->find();
        	if($grandfather['level'] != -1){
        		Db::name('user')->where('id',$user['grandfather_id'])->setInc('cny',$site['level_two']*0.8);
	            model('Order','logic','common')->addRecord(1,$user['grandfather_id'],$site['level_two']*0.8);
                model('Order','logic','common')->addMoneyRecord('cny',$user['grandfather_id'],$site['level_two']*0.8,'+','间推转入');

	            Db::name('user')->where('id',$user['grandfather_id'])->setInc('sc',$site['level_two']*0.1);
	            Db::name('user')->where('id',$user['grandfather_id'])->setInc('qc',$site['level_two']*0.1);
	            model('Order','logic','common')->addMoneyRecord('sc',$user['grandfather_id'],$site['level_two']*0.1,'+','间推转入');
	            model('Order','logic','common')->addMoneyRecord('qc',$user['grandfather_id'],$site['level_two']*0.1,'+','间推转入');
        	}
        }
    }

    /*
        method：增加团队奖励
        参数
        user_id            用户id
    */
    public function addTeamMoney($user_id){
        $user = Db::name('user')->where('id',$user_id)->find();
        $site = Db::name('site')->where('id',1)->find();
        
        $family_arr = explode(',', $user['family_id']);
        $family_arr = array_reverse($family_arr);
        $level = 0;
        $level_money = 0;
        $money = 1;//极差金额
        foreach ($family_arr as $k => $v) {
            $temp = Db::name('user')->where('id',$v)->find();//上级
            if($temp['level'] == 1||$temp['level'] == 2||$temp['level'] == 3||$temp['level'] == 4){
                
                $level_temp = Db::name('level')->where('id',$level)->find();
                if($level_temp){
                    $level_money = $level_temp['money'];
                }


                if($temp['level'] <= $level){//平级或低于
                    Db::name('user')->where('id',$v)->setInc('cny',$money*0.1*0.8);
                    model('Order','logic','common')->addRecord(2,$v,$money*0.1*0.8);
                    model('Order','logic','common')->addMoneyRecord('cny',$v,$money*0.1*0.8,'+','团队奖励转入');

                    Db::name('user')->where('id',$v)->setInc('sc',$money*0.1*0.1);
                    Db::name('user')->where('id',$v)->setInc('qc',$money*0.1*0.1);
                    model('Order','logic','common')->addMoneyRecord('sc',$v,$money*0.1*0.1,'+','团队奖励转入');
                    model('Order','logic','common')->addMoneyRecord('qc',$v,$money*0.1*0.1,'+','团队奖励转入');
                    // Db::name('user')->where('id',$v)->setInc('cny',$site['fail_cny']*0.1*0.8);
                    // model('Order','logic','common')->addRecord(2,$v,$site['fail_cny']*0.1*0.8);
                    // model('Order','logic','common')->addMoneyRecord('cny',$v,$site['fail_cny']*0.1*0.8,'+','团队奖励转入');

                    // Db::name('user')->where('id',$v)->setInc('sc',$site['fail_cny']*0.1*0.1);
                    // Db::name('user')->where('id',$v)->setInc('qc',$site['fail_cny']*0.1*0.1);
                    // model('Order','logic','common')->addMoneyRecord('sc',$v,$site['fail_cny']*0.1*0.1,'+','团队奖励转入');
                    // model('Order','logic','common')->addMoneyRecord('qc',$v,$site['fail_cny']*0.1*0.1,'+','团队奖励转入');        
                }else{
                    $level_now = Db::name('level')->where('id',$temp['level'])->find();

                    $level = $temp['level'];
                    $level_money_now = $level_now['money'];


                    $money = $level_money_now-$level_money;//极差金额
                    Db::name('user')->where('id',$v)->setInc('cny',$money*0.8);
                    model('Order','logic','common')->addRecord(2,$v,$money*0.8);
                    model('Order','logic','common')->addMoneyRecord('cny',$v,$money*0.8,'+','团队奖励转入');

                    Db::name('user')->where('id',$v)->setInc('sc',$money*0.1);
                    Db::name('user')->where('id',$v)->setInc('qc',$money*0.1);
                    model('Order','logic','common')->addMoneyRecord('sc',$v,$money*0.1,'+','团队奖励转入');
                    model('Order','logic','common')->addMoneyRecord('qc',$v,$money*0.1,'+','团队奖励转入');

                }

            }

            

        }
    }
    
}