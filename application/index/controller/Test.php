<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use app\common\controller\Frontend;
use think\Db;
use Endroid\QrCode\QrCode;

class Test extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';


    public function index(){
        $where['room_num'] = array('GT',147);
        $list = Db::name('pintuan_robot')->where($where)->limit(100)->select();
        dump($list);
        die;

    	// $robot_list = Db::name('robot')->select();
     //    $robot_arr = array_column($robot_list, 'id');
     //    $res_robot = uni($robot_arr,10);
     //    dump($res_robot[0]);
     //    die;
        // model('Pintuan','logic','common')->updateLevels();
        // model('Pintuan','logic','common')->addTeamMoney(65);
        // return json(['code'=>100,'msg'=>'登录成功！','data'=>'']);
    }


    public function hour(){
        echo (int)date('G');
    }

        private function userExist($user_id,$arr)
    {
        if(!$arr){
            return 0;
        }

        $exist = array_key_exists($user_id,$arr);
        if($exist){
            // return $user_id;
            return $arr[$user_id];
        }

        return 0;
    }

    public function diffBalance()
    {
        // USDT平台系统调节
        $sql = "select user_id,sum(money) from cc_money_record
      where type='usdt' and operate='-' and (content='平台系统调节' or content='平台系统调节20.11.6多返奖励扣除' or content='平台系统调节20.11.10多返奖励扣除' or content='平台系统调节(多返奖励扣除)')
      group by user_id";
        $usdt_platform_balances = Db::query($sql);
        // var_dump($usdt_platform_balances);exit;
        $u = [];
        foreach($usdt_platform_balances as $v){
            $u[$v['user_id']]=$v['sum(money)'];
        }

        // CNY平台系统调节
        $sql = "select user_id,sum(money) from cc_money_record
      where type='cny' and operate='-' and (content='平台系统调节' or content='平台系统调节20.11.6多返奖励扣除' or content='平台系统调节20.11.10多返奖励扣除' or content='平台系统调节(多返奖励扣除)')
      group by user_id";
        $cny_platform_balances = Db::query($sql);
        // var_dump($cny_platform_balances);exit;
        $c = [];
        foreach($cny_platform_balances as $v){
            $c[$v['user_id']]=$v['sum(money)'];
        }

        // 获得 未拼中退还 比 未拼中 多的(即多返的)用户
        $sql = "select l.user_id,r.cny,r.usdt,r.freeze_usdt,l.diff from
      (select wh.user_id,wh.c as weih,wz.c as weiz,(wh.c-wz.c) as diff from
        (select user_id,count(1) as c from cc_pintuan
         where status='未拼中' group by user_id) as wz
         inner join
        (select user_id,count(1) as c from cc_money_record
         where type='cny' and content='未拼中退还'  group by user_id) as wh
        on wz.user_id=wh.user_id and wh.c>wz.c) as l
      inner join
      cc_user as r
      on l.user_id=r.id";
        $users = Db::query($sql);
        // echo count($users);exit;
        // var_dump($users);exit;

        foreach ($users as $v) {

            $sub = bcmul($v['diff'],700,2);

            $usdt_platform_balance = $this->userExist($v['user_id'],$u); //echo 'u:'.$usdt_platform_balance; echo "\n\n";
            $cny_platform_balance = $this->userExist($v['user_id'],$c); //echo 'c:'.$cny_platform_balance; echo "\n\n";

            $sub = bcsub($sub,bcadd(bcmul($usdt_platform_balance,6.68,2),$cny_platform_balance,2),2); //echo $sub; echo "\n\n";
            if($sub <= 1){
                continue;
            }

            Db::startTrans();
            $cny_sub = 0;
            $usdt_sub = 0;
            try{
                // 如果 cny 够扣
                if($v['cny'] >= $sub){
                    $cny_sub = $sub;
                    Db::name('user')->where('id',$v['user_id'])->setDec('cny',$cny_sub); //echo 'cny:'.$cny_sub; echo "\n\n";
                    model('Order','logic','common')->addMoneyRecord('cny',$v['user_id'],$cny_sub,'-','平台系统调节(多返奖励扣除)');
                }else{
                    $cny_sub = $v['cny'];
                    Db::name('user')->where('id',$v['user_id'])->update(['cny'=>0]); //echo 'cny:'.$cny_sub; echo "\n\n";
                    model('Order','logic','common')->addMoneyRecord('cny',$v['user_id'],$cny_sub,'-','平台系统调节(多返奖励扣除)');

                    $usdt_sub = bcdiv(bcsub($sub,$v['cny'],2),6.68 ,2); //echo 'usdt:'.$usdt_sub; echo "\n\n";
                    Db::name('user')->where('id',$v['user_id'])->setDec('usdt',$usdt_sub);
                    model('Order','logic','common')->addMoneyRecord('usdt',$v['user_id'],$usdt_sub,'-','平台系统调节(多返奖励扣除)');

                }
                Db::commit();
                echo 'user_id:'.$v['user_id'].' cny_pre:'.$v['cny'].' cny_sub:'.$cny_sub.' usdt_pre:'.$v['usdt'].' usdt_sub:'.$usdt_sub.' cny_after:'.($v['cny']-$cny_sub).' usdt_after:'.($v['usdt']-$usdt_sub)."\n\n";
            }catch (\Exception $e) {
                //获取到异常信息回滚至操作前的状态
                Db::rollback();
                $this->error($e);
            }

        }
        echo '脚本成功执行完毕';
    }

    /**
     * 驳回 2020-11-23 之前 未处理 提现申请
     */
    public function rejectWithdraw(){
        $site = Db::name('site')->where('id',1)->find();
        
        $ws = Db::name('withdraw')->where('status','未处理')
                                  ->where('ctime','LT', 1606060800)
                                  ->where('num','GT', 0)
                                  ->select();
        // echo count($ws)."\n\n";
        // print_r($ws);exit;
        if(!$ws){
            echo "未有 2020-11-23 之前的 未处理 提现申请";exit;
        }

        $p = [];
        foreach($ws as $row){
            if($row['num'] < 0) continue;
            
            $status = '到账失败';
            // echo $row['user_id'] . "\n";
               // echo $row['num'] . "\n";
               // echo $site['usdt_ag_rate'];

                Db::startTrans();
                try{
                    $user_before = Db::name('user')->where('id',$row['user_id'])->find();
                Db::name('user')->where('id',$row['user_id'])->setInc('usdt',$row['num']);
                Db::name('user')->where('id',$row['user_id'])->setDec('freeze_usdt',$row['num']);
                model('Order','logic','common')->addMoneyRecord('usdt',$row['user_id'],$row['num'],'+','提现失败');

                Db::name('user')->where('id',$row['user_id'])->setInc('ag',$row['num']*$site['usdt_ag_rate']*0.03);
                model('Order','logic','common')->addMoneyRecord('ag',$row['user_id'],$row['num']*$site['usdt_ag_rate']*0.03,'+','提现失败冲正');
                Db::name('withdraw')->where('id',$row['id'])->update(['status'=>$status]);;
                $user_after = Db::name('user')->where('id',$row['user_id'])->find();

                Db::commit();

                $p[] = ['user_id'=>$row['user_id'],
                        'usdt_before'=>$user_before['usdt'],
                       'usdt_inc'=>$row['num'],
                       'usdt_after'=>$user_after['usdt'],
                       'freeze_usdt_before'=>$user_before['freeze_usdt'],
                       'freeze_usdt_dec'=>$row['num'],
                       'freeze_usdt_after'=>$user_after['freeze_usdt'],
                       'ag_before'=>$user_before['ag'],
                       'ag_inc'=>($row['num']*$site['usdt_ag_rate']*0.03),
                       'ag_after'=>$user_after['ag'],
                ];
                }catch (\Exception $e){
                    Db::rollback();
                $this->error($e);
            }

        }
        echo json_encode($p);
        echo '脚本成功执行完毕';
    }


    /**
     * 以用户的 ag 填补其 usdt 的负值
     */
    public function repareUsdtWithAg(){
        $site = Db::name('site')->where('id',1)->find();
        $users = Db::name('user')->where('usdt','LT', 0)->select();
        foreach($users as $v){
            
            Db::startTrans();
            try{
                $user = Db::name('user')->field('id,usdt,ag,lock_ag,flow_ag')->where('id',$v['id'])->find();
                $u2a = bcmul($user['usdt'],$site['usdt_ag_rate'],2);
                $diff = bcadd($user['ag'],$u2a,2);
                // 若AG不够抵消USDT
                if($diff <0){
                    Db::name('user')->where('id',$v['id'])->setInc('usdt',abs($user['ag']));
                    Db::name('user')->where('id',$v['id'])->update(['ag'=>0]);
                }

                Db:commit();
            }catch (\Exception $e){
                Db::rollback();
                $this->error($e);
            }

        }
            ;
    }

    
    /**
     * 锁仓ag为负数用户处理
     */
        public function lockAg()
        {
            $where = array();
            $where['lock_ag'] = ['lt','0'];
            //锁仓ag为负数用户
            $list = Db::name('user')->field('id,username,mobile,ag,lock_ag,flow_ag')->where($where)->select();//->limit(0, 100)
            //echo '<pre />';print_r($list);exit;
            foreach ($list as $v){
                //用户需要抵扣AG数量
            $lock_ag = abs($v['lock_ag']);
            //用户帐户可抵扣AG最大值   可抵扣有可用AG与流通AG  抵扣优先级 可用AG>流通AG
            $usable_ag = ($v['ag']+$v['flow_ag']);
            //检查用户帐户AG是否足够抵扣
            if($lock_ag>$usable_ag){
                $log = date("Y-m-d H:i:s").'--用户ID:'.$v['id'].'--AG不够抵扣'."\n";
            }else{
                Db::startTrans();
                try {
                    //可用AG抵扣数量
                    $ag_deduction = $lock_ag;
                    //流通AG抵扣数量
                    $flow_ag_deduction = 0;
                    //检测用户可用AG是否足够抵扣    如果不够抵扣时先抵扣所有可用AG  再抵扣流通AG
                    if($v['ag']>=$lock_ag){
                        Db::name('user')->where('id',$v['id'])->setDec('ag',$lock_ag);
                        //添加资金变动记录
                        db("money_record")->insert([
                            'user_id'=>$v['id'],
                            'username'=>$v['username'],
                            'pre_money'=>'',
                            'money'=>$lock_ag,
                            'type'=>'correction_ag',
                            'content'=>'AG抵扣异常锁仓AG',
                            'remark'=>'效正异常锁仓ag脚本执行效正',
                            'operate'=>'-',
                            'ctime'=>time()
                        ]);
                    }else{
                        $ag_deduction = $v['ag'];
                        //优先抵扣可用AG
                        Db::name('user')->where('id',$v['id'])->update(['ag'=>'0']);
                        //添加资金变动记录
                        db("money_record")->insert([
                            'user_id'=>$v['id'],
                            'username'=>$v['username'],
                            'pre_money'=>'',
                            'money'=>$lock_ag,
                            'type'=>'correction_ag',
                            'content'=>'AG抵扣异常锁仓AG',
                            'remark'=>'效正异常锁仓ag脚本执行效正',
                            'operate'=>'-',
                            'ctime'=>time()
                        ]);
                        $flow_ag_deduction = $lock_ag-$v['ag'];
                        //可用AG不够抵扣时使用流通AG进行抵扣
                        Db::name('user')->where('id',$v['id'])->setDec('ag',$flow_ag_deduction);
                        //添加资金变动记录
                        db("money_record")->insert([
                            'user_id'=>$v['id'],
                            'username'=>$v['username'],
                            'pre_money'=>'',
                            'money'=>$lock_ag,
                            'type'=>'correction_ag',
                            'content'=>'AG抵扣异常锁仓AG',
                            'remark'=>'效正异常锁仓ag脚本执行效正',
                            'operate'=>'-',
                            'ctime'=>time()
                        ]);
                    }
                    //用户锁仓AG修改为0
                    Db::name('user')->where('id',$v['id'])->update(['lock_ag'=>'0']);
                    Db::commit();
                    $log = date("Y-m-d H:i:s").'--用户ID:'.$v['id'].'--AG效正成功，抵扣可用AG：'.$ag_deduction.'抵扣流通AG：'.$flow_ag_deduction."\n";
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                    $log = date("Y-m-d H:i:s").'--用户ID:'.$v['id'].'--AG效正失败'.$e->getMessage()."\n";
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                    $log = date("Y-m-d H:i:s").'--用户ID:'.$v['id'].'--AG效正失败'.$e->getMessage()."\n";
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                    $log = date("Y-m-d H:i:s").'--用户ID:'.$v['id'].'--AG效正失败'.$e->getMessage()."\n";
                }
            }
            file_put_contents(__DIR__.'/lock_ag.log',$log,FILE_APPEND);
        }
        echo '执行结束';exit;
        //return $this->view->fetch();
    }

    /**
     * 提现驳回2020-11-23日处理为到帐失败
     */
    public function withdrawRejected(){
        $where = [];
        //$where['id'] = '2617';
        $where['status'] = '未处理';
        //2020-11-23日前的数据
        $where['ctime'] = ['lt','1606060800'];
        $where['num'] = ['>','0'];
        //2020-11-23日前未处理的提现列表
        $list = Db::name('withdraw')->where($where)->select();//->limit(0, 10)
        echo '<pre />';print_r($list);exit;
        //$site = Db::name('site')->where('id',1)->find();
        $params = array();
        $params['status'] = '到账失败';
        foreach ($list as $row){
            $params['user_id'] = $row['user_id'];
            Db::startTrans();
            try {
                //增加用户USDT
                Db::name('user')->where('id',$row['user_id'])->setInc('usdt',$row['num']);

                //扣除用户冻结USDT
                Db::name('user')->where('id',$row['user_id'])->setDec('freeze_usdt',$row['num']);

                model('Order','logic','common')->addMoneyRecord('usdt',$row['user_id'],$row['num'],'+','提现失败');
                Db::name('user')->where('id',$row['user_id'])->setInc('ag',$row['num']*$site['usdt_ag_rate']*0.03);
                model('Order','logic','common')->addMoneyRecord('ag',$row['user_id'],$row['num']*$site['usdt_ag_rate']*0.03,'+','提现失败冲正');

                // 手续费一半到sc，一半到qc   end

                //是否采用模型验证
                /*if ($this->modelValidate) {
                    $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                    $row->validateFailException(true)->validate($validate);
                }*/
                $result = Db::name('withdraw')->where('id',$row['id'])->update(['status'=>$params['status']]);;
                Db::commit();
                $log = date("Y-m-d H:i:s").'--提现ID:'.$row['id'].'--到账失败操作成功'."\n";
            } catch (ValidateException $e) {
                Db::rollback();
                $this->error($e->getMessage());
                $log = date("Y-m-d H:i:s").'--提现ID:'.$row['id'].'--到账失败操作失败'.$e->getMessage()."\n";
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
                $log = date("Y-m-d H:i:s").'--提现ID:'.$row['id'].'--到账失败操作失败'.$e->getMessage()."\n";
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
                $log = date("Y-m-d H:i:s").'--提现ID:'.$row['id'].'--到账失败操作失败'.$e->getMessage()."\n";
            }
        }
        echo '执行结束';exit;
    }
}
