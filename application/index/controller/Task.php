<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use app\common\controller\Frontend;
use think\Db;
use Endroid\QrCode\QrCode;

class Task extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';


    // 处理拼团开奖
    public function fanPintuan(){
                
        $lock_file = __DIR__ . '/task.lock';
		if (! is_file ( $lock_file )) {
			file_put_contents ( $lock_file, '' );
		}
		$fp = fopen ( $lock_file, "r" );
		if (! flock ( $fp, LOCK_EX | LOCK_NB )) {
		    flock ( $fp, LOCK_UN );
			fclose ( $fp );
			die ("有其他进程执行中");
		}
    	ignore_user_abort(true);
        ini_set('max_execution_time', '0');
        set_time_limit(0);
        $start_time=time();
        
        
    	$where['status'] = array('NEQ','未开奖');
    	$where['is_fan'] = 2;
        $where['ctime'] = array('LT',time()-3);
    	$ids = Db::name('pintuan')->where($where)->column('id');
    	if(!empty($ids)){
    	    foreach ($ids as $id) {
        	    $v=Db::name('pintuan')->where($where)->find($id);
        	    if(!empty($v)){
        	        Db::name('pintuan')->where('id',$id)->update(['is_fan'=>1]);
            		if($v['status'] == '未拼中'){
            			model('Pintuan','logic','common')->getWeiPintuan($v['user_id']);
            		}
            		if($v['status'] == '拼中'){ 
            			model('Pintuan','logic','common')->getZhongPintuan($v['user_id']);
            		}
        	    }
        	}
    	}
        flock ( $fp, LOCK_UN );
		fclose ( $fp );
		$run_time=time()-$start_time;
        return json(['code'=>100,'msg'=>'执行成功，耗时'.$run_time.'秒']);
    }

    // 拼团开奖运行脚本
    public function resultPintuan(){
                
    	// die;
        $site = Db::name('site')->where('id',1)->find();

        $where['status'] = '未开奖';
        // $where['ctime'] = array('LT',time()-$site['pintuan_time']+3);
        $list = Db::name('pintuan')->where($where)->select();
        foreach ($list as $k => $v) {
        	$room_time = Db::name('pintuan')->where('room_num',$v['room_num'])->min('ctime');
            // 分配机器人
            if($v['people_num']<10){
            	$jiange = time()-$room_time-$site['start_robot_time'];
            	if($jiange>0 && $jiange%$site['robot_in_time'] == 0 && $site['is_robot']==1 ){
            		//model('Pintuan','logic','common')->addRobot($v['room_num']);
            	}
            }else{
            	model('Pintuan','logic','common')->resultPintuan($v['room_num']);
            }
        }
        return json(['code'=>100,'msg'=>'登录成功！']);
    }

    // 每日重置拼团数
    public function updateUser(){
                
    	$where['id'] = array('NEQ',0);
    	$data['today_one_num'] = 0;
    	$data['today_five_num'] = 0;
        $data['today_ten_num'] = 0;
        $data['today_twenty_num'] = 0;
        $data['today_thirty_num'] = 0;
        $data['today_fifty_num'] = 0;
    	Db::name('user')->where($where)->update($data);
    }

    // 每日分红
    public function companyMoney(){
                
    	$site = Db::name('site')->where('id',1)->find();

        $start_time = strtotime(date('Y-m-d'))-86400;
        $end_time = strtotime(date('Y-m-d'))-1;

        $where_pintuan['ctime'] = array('BETWEEN',array($start_time,$end_time));
        $where_pintuan['status'] = '拼中';
        $count = Db::name('pintuan')->where($where_pintuan)->count();
        $all_money = 700*$count;

        //加权分红
        // $all_zhong_num = Db::name('user')->sum('zhong_num');//总加权分红资格数
        $all_zhong_num = 0;
        $where_user['zhong_num'] = array('NEQ',0);
        $user_list = Db::name('user')->where($where_user)->select();
        foreach ($user_list as $k => $v) {
            $all_zhong_num += intval($v['zhong_num']/10);
        }

        $user_list = Db::name('user')->where($where_user)->select();
        foreach ($user_list as $k => $v) {
            $zhong_num = intval($v['zhong_num']/10);
            if($zhong_num == 0){
                continue;
            }
            
            $rate = $zhong_num/$all_zhong_num;
            $money = $all_money*$rate*0.01;
            if($money > 21000){
                $money = 21000;
            }
            $sub_ag=$money*$site['cny_ag_rate']/4;
            // 判断我的AG是否充足
            if($v['ag'] >= $sub_ag){//充足

                $money = round($money,2);
                
                Db::name('user')->where('id',$v['id'])->setDec('ag',$sub_ag);//扣除等值ag
                model('Order','logic','common')->addMoneyRecord('ag',$v['id'],$sub_ag,'-','每日分红消耗');
	            
	            Db::name('user')->where('id',$v['id'])->setInc('cny',$money);
	            model('Order','logic','common')->addRecord(4,$v['id'],$money);
	            model('Order','logic','common')->addMoneyRecord('cny',$v['id'],$money,'+','加权分红');
	        }
        }
        //加权分红  end

        //额外分红
        $all_other_num = Db::name('user')->sum('other_num');//总额外分红资格数
        $where_user2['other_num'] = array('NEQ',0);
        $user_list = Db::name('user')->where($where_user2)->select();
        foreach ($user_list as $k => $v) {
            $rate = $v['other_num']/$all_other_num;
            $money = $all_money*$rate*0.01;
            if($money > 105000){
                $money = 105000;
            }
            $sub_ag=$money*$site['cny_ag_rate']/4;
            // 判断我的AG是否充足
            if($v['ag'] >= $sub_ag){//充足

                Db::name('user')->where('id',$v['id'])->setDec('ag',$sub_ag);//扣除等值ag
                model('Order','logic','common')->addMoneyRecord('ag',$v['id'],$sub_ag,'-','每日分红消耗');

	            Db::name('user')->where('id',$v['id'])->setInc('cny',$money);
	            model('Order','logic','common')->addRecord(4,$v['id'],$money);
	            model('Order','logic','common')->addMoneyRecord('cny',$v['id'],$money,'+','额外加权分红');
	        }
        }
        //额外分红  end

    }

    // 节点每日分红
    public function nodeMoney(){
                
        $start_time = strtotime(date('Y-m-d'))-86400;
        $end_time = strtotime(date('Y-m-d'))-1;

        $where_pintuan['ctime'] = array('BETWEEN',array($start_time,$end_time));
        $where_pintuan['status'] = '拼中';
        $count = Db::name('pintuan')->where($where_pintuan)->count();
        $all_money = 700*$count;


        $site = Db::name('site')->where('id',1)->find();

        $node_num = Db::name('node')->count();//总认购数

        $where_node['lock_ag'] = array('NEQ',0);
        $where_node['status'] = 1;
        $node_list = Db::name('node')->where($where_node)->select();
        foreach ($node_list as $k => $v) {
            
            $user = Db::name('user')->where('id',$v['user_id'])->find();
            
            // 获取分红金额
            $cny = $all_money*$site['node_company']/$node_num;
            if($cny > 200000){
                $cny = 200000;
            }
            // 获取分红金额   end  node_every_pay 0.25 扣除等值AG比例 cny_ag兑换比例 1
            $sub_ag=$cny*$site['node_every_pay']*$site['cny_ag_rate'];
            // 判断我的AG是否充足
            if($user['ag'] >= $sub_ag){//充足

                Db::name('user')->where('id',$v['user_id'])->setDec('ag',$sub_ag);//扣除等值ag
                model('Order','logic','common')->addMoneyRecord('ag',$v['user_id'],$sub_ag,'-','节点收益消耗');
                
                // $open_ag = $v['all_ag']*$site['node_every_get'];//解仓ag
                // if($open_ag > $v['lock_ag']){
                //     $open_ag = $v['lock_ag'];
                // }

                Db::name('user')->where('id',$v['user_id'])->setInc('cny',$cny);//获取公司收益分红
                model('Order','logic','common')->addRecord(3,$v['user_id'],$cny);
                model('Order','logic','common')->addMoneyRecord('cny',$v['user_id'],$cny,'+','节点分红');

                // Db::name('user')->where('id',$v['user_id'])->setDec('lock_ag',$open_ag);//用户锁仓ag减少
                // Db::name('user')->where('id',$v['user_id'])->setInc('ag',$open_ag);//用户ag增加
                // model('Order','logic','common')->addMoneyRecord('ag',$v['user_id'],$open_ag,'+','锁仓释放');

                // Db::name('node')->where('id',$v['id'])->setDec('lock_ag',$open_ag);//节点锁仓ag减少

                //增加分红记录
                $data['user_id'] = $v['user_id'];
                $data['node_id'] = $v['id'];
                $data['cny'] = $cny;
                $data['ctime'] = time();
                Db::name('node_record')->insert($data);
                //增加分红记录    end

            }
        }
        return 'success';

    }

   // 兑换卡锁仓释放ag
    public function exchangeMoney(){
                
        $site = Db::name('site')->where('id',1)->find();

        // $where['lock_ag'] = array('NEQ',0);
        $where['lock_ag'] = array('GT',0.1);
        $list = Db::name('exchange')->where($where)->select();
        foreach ($list as $k => $v) {
            $open_ag = bcmul($v['all_ag'],$site['node_every_get'],6);
            if($open_ag >= $v['lock_ag']){
                $open_ag = $v['lock_ag'];
                Db::name('exchange')->where('id',$v['id'])->update(['lock_ag'=>0]);
            }else{
                Db::name('exchange')->where('id',$v['id'])->setDec('lock_ag',$open_ag);
            }


            Db::name('user')->where('id',$v['user_id'])->setDec('lock_ag',$open_ag);
            Db::name('user')->where('id',$v['user_id'])->setInc('ag',$open_ag);

            model('Order','logic','common')->addMoneyRecord('ag',$v['user_id'],$open_ag,'+','AG兑换卡解仓');

            $data['user_id'] = $v['user_id'];
            $data['exchange_id'] = $v['id'];
            $data['ag'] = $open_ag;
            $data['ctime'] = time();
            Db::name('exchange_record')->insert($data);
        }
        return 'success';
    }
 
    
    
    
    
    public function bufa(){
            
        die;
        $uids=[512,323,281,131,151,271,219,206,1020,100,388,372,504,399,236,
			104,288,152,149,76,88,98,1476,136,117,158,1980,127,1370,
			2031,276,531,518,1124,119,2150,183 ];
        $dates=[
            //'2020-10-14','2020-10-15','2020-10-16','2020-10-17','2020-10-18',
		    //'2020-10-19','2020-10-20','2020-10-21','2020-10-22','2020-10-23',
		    //'2020-10-24','2020-10-25','2020-10-26'
		    //'2020-10-27','2020-10-28'
	    ];
        foreach($dates as $date){
            $this->nodeMoney2($date,$uids);
        }
    }
    
    
    
    
    //补发
    private function nodeMoney2($date,$uids){
                
        static $site=null;
        if(null==$site){
            $site = Db::name('site')->where('id',1)->find();
        }
        $start_time = strtotime($date)-86400;
        $end_time = strtotime($date)-1;
        $log_time =$end_time+1+(3600);
        $where_pintuan['ctime'] = array('BETWEEN',array($start_time,$end_time));
        $where_pintuan['status'] = '拼中';
        $count = Db::name('pintuan')->where($where_pintuan)->count();
        $all_money = 700*$count;
        if(!$all_money){
            return;
        }
        $node_num = Db::name('node')->where("ctime<'{$end_time}'")->count();//总认购数
        if($node_num==0){
            return;
        }
        $where_node['lock_ag'] = array('NEQ',0);
        $where_node['status'] = 1;
        $where_node['user_id']=['in',$uids];
        $node_list = Db::name('node')->where($where_node)->select();
        foreach ($node_list as $k => $v) {
            $exist= Db::name('node_record')->where("user_id='{$v['user_id']}' and node_id='{$v['id']}' and ctime>'{$end_time}' and ctime<='{$log_time}'")->find();
            if(!empty($exist)){
                continue;
            }
            
            $user = Db::name('user')->where('id',$v['user_id'])->find();
            $cny = $all_money*$site['node_company']/$node_num;
            if($cny > 200000){
                $cny = 200000;
            }
            $sub_ag=$cny*$site['node_every_pay']*$site['cny_ag_rate'];
            if($user['ag'] >= $sub_ag){//充足
                Db::name('user')->where('id',$v['user_id'])->setDec('ag',$sub_ag);//扣除等值ag
                model('Order','logic','common')->addMoneyRecord('ag',$v['user_id'],$sub_ag,'-','节点收益消耗');
                Db::name('user')->where('id',$v['user_id'])->setInc('cny',$cny);//获取公司收益分红
                model('Order','logic','common')->addRecord(3,$v['user_id'],$cny);
                model('Order','logic','common')->addMoneyRecord('cny',$v['user_id'],$cny,'+',"节点分红(补发{$date})");
                $data['user_id'] = $v['user_id'];
                $data['node_id'] = $v['id'];
                $data['cny'] = $cny;
                $data['ctime'] = $log_time;
                Db::name('node_record')->insert($data);
            }
        }
        return 'success';

    }

}
