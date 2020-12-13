<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
/**
 * 提现
 *
 * @icon fa fa-circle-o
 */
class Withdraw extends Backend
{
    
    /**
     * Withdraw模型对象
     * @var \app\admin\model\Withdraw
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Withdraw;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $list = json_decode(json_encode($list),true);
            foreach ($list as $k => $v) {
                // $list[$k]['user_id'] = Db::name('user')->where('id',$v['user_id'])->value('username');
                //$list[$k]['username'] = Db::name('user')->where('id',$v['user_id'])->value('username');
                $username = Db::name('user')->where('id',$v['user_id'])->value('username');
                $username = $username .'(ID:'. $v['user_id'] .')';
                $list[$k]['user_id'] = $username;

                $list[$k]['mobile'] = Db::name('user')->where('id',$v['user_id'])->value('mobile');
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    $site = Db::name('site')->where('id',1)->find();
                    // 手续费一半到sc，一半到qc
                    if($params['status'] == '已到账'){
                        
                        // $score = $row['num']*$row['withdraw_rate']/2;
                        
                        // Db::name('user')->where('id',$row['user_id'])->setInc('sc',$score);
                        // Db::name('user')->where('id',$row['user_id'])->setInc('qc',$score);

                        // model('Order','logic','common')->addMoneyRecord('sc',$row['user_id'],$score,'+','提现转入');
                        // model('Order','logic','common')->addMoneyRecord('qc',$row['user_id'],$score,'+','提现转入');
                        Db::name('user')->where('id',$row['user_id'])->setDec('freeze_usdt',$row['num']);
                        model('Order','logic','common')->addMoneyRecord('usdt',$row['user_id'],$row['num'],'-','提现转出');
                    }
                    if($params['status'] == '到账失败'){
                        Db::name('user')->where('id',$row['user_id'])->setInc('usdt',$row['num']);
                        Db::name('user')->where('id',$row['user_id'])->setDec('freeze_usdt',$row['num']);
                        model('Order','logic','common')->addMoneyRecord('usdt',$row['user_id'],$row['num'],'+','提现失败');

                        Db::name('user')->where('id',$row['user_id'])->setInc('ag',$row['num']*$site['usdt_ag_rate']*0.03);
                        model('Order','logic','common')->addMoneyRecord('ag',$row['user_id'],$row['num']*$site['usdt_ag_rate']*0.03,'+','提现失败冲正');
                    }
                    // 手续费一半到sc，一半到qc   end

                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $user = Db::name('user')->where('id',$row['user_id'])->find();
        $row['cny'] = $user['cny'];
        $row['freeze_cny'] = $user['freeze_cny'];
        $row['ag'] = $user['ag'];
        $row['freeze_ag'] = $user['freeze_ag'];
        $row['lock_ag'] = $user['lock_ag'];
        $row['flow_ag'] = $user['flow_ag'];
        $row['sc'] = $user['sc'];
        $row['qc'] = $user['qc'];
        $row['usdt'] = $user['usdt'];
        $row['freeze_usdt'] = $user['freeze_usdt'];
        $row['ag_card'] = $user['ag_card'];


        $row['money_record_cny'] = 0;
        $row['money_record_usdt'] = 0;
        $row['money_record_ag'] = 0;
        $row['money_record_sc'] = 0;
        $row['money_record_freeze_usdt'] = 0;
        $row['money_record_lock_ag'] = 0;
        $row['money_record_freeze_ag'] = 0;
        $row['money_record_flow_ag'] = 0;
        $row['money_record_ag_card'] = 0;
        $row['money_record_qc'] = 0;
        
        $money_record_usdt_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','+')->sum('money');
        // $money_record_usdt_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','-')->sum('money');
        $money_record_usdt_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','-')->where('content','NEQ','提现转出')->sum('money');
        $row['money_record_usdt'] = bcsub($money_record_usdt_add,$money_record_usdt_sub,3);

        // USDT 平台系统调节
        $row['money_record_usdt_platform'] = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','-')->where('content','LIKE','%系统调节%')->sum('money');
        // CNY 平台系统调节
        $row['money_record_cny_platform'] = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'cny')->where('operate','-')->where('content','LIKE','%系统调节%')->sum('money');


        
        // $money_record_freeze_usdt_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'freeze_usdt')->where('operate','+')->sum('money');
        // $money_record_freeze_usdt_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'freeze_usdt')->where('operate','-')->sum('money');
        $money_record_freeze_usdt_add1 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','-')->where('content','提现冻结')->sum('money');
        $money_record_freeze_usdt_add2 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','+')->where('content','提现转出冲正')->sum('money');
        $money_record_freeze_usdt_sub1 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','+')->where('content','提现失败')->sum('money');
        $money_record_freeze_usdt_sub2 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'usdt')->where('operate','-')->where('content','提现转出')->sum('money');
        $money_record_freeze_usdt_add = bcadd($money_record_freeze_usdt_add1,$money_record_freeze_usdt_add2,3);
        $money_record_freeze_usdt_sub = bcadd($money_record_freeze_usdt_sub1,$money_record_freeze_usdt_sub2,3);
        $row['money_record_freeze_usdt'] = bcsub($money_record_freeze_usdt_add,$money_record_freeze_usdt_sub,3);


        // $money_record_lock_ag_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'lock_ag')->where('operate','+')->sum('money');
        // $money_record_lock_ag_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'lock_ag')->where('operate','-')->sum('money');
        $row['money_record_lock_ag'] = Db::name("exchange")->where('user_id',$row['user_id'])->sum('lock_ag');

        $money_record_flow_ag_add1 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'flow_ag')->where('operate','+')->sum('money');
        $money_record_flow_ag_sub1 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'flow_ag')->where('operate','-')->sum('money');
        $money_record_flow_ag_add2 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'ag')->where('operate','-')->where('content','可用转流通')->sum('money');
        $money_record_flow_ag_sub2 = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'ag')->where('operate','+')->where('content','流通换可用')->sum('money');
        $money_record_flow_ag_add = bcadd($money_record_flow_ag_add1,$money_record_flow_ag_add2,3);
        $money_record_flow_ag_sub = bcadd($money_record_flow_ag_sub1,$money_record_flow_ag_sub2,3);
        $row['money_record_flow_ag'] = bcsub($money_record_flow_ag_add,$money_record_flow_ag_sub,3);

        $money_record_ag_card_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'ag_card')->where('operate','+')->sum('money');
        $money_record_ag_card_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'ag_card')->where('operate','-')->sum('money');
        $row['money_record_ag_card'] = bcsub($money_record_ag_card_add,$money_record_ag_card_sub,3);

        $money_record_cny_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'cny')->where('operate','+')->sum('money');
        $money_record_cny_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'cny')->where('operate','-')->sum('money');
        $row['money_record_cny'] = bcsub($money_record_cny_add,$money_record_cny_sub,3);
        $money_record_ag_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'ag')->where('operate','+')->sum('money');
        $money_record_ag_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'ag')->where('operate','-')->sum('money');
        $row['money_record_ag'] = bcsub($money_record_ag_add,$money_record_ag_sub,3);
        $money_record_sc_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'sc')->where('operate','+')->sum('money');
        $money_record_sc_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'sc')->where('operate','-')->sum('money');
        $row['money_record_sc'] = bcsub($money_record_sc_add,$money_record_sc_sub,3);
        $money_record_freeze_ag_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'freeze_ag')->where('operate','+')->sum('money');
        $money_record_freeze_ag_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'freeze_ag')->where('operate','-')->sum('money');
        $row['money_record_freeze_ag'] = bcsub($money_record_freeze_ag_add,$money_record_freeze_ag_sub,3);
        $money_record_qc_add = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'qc')->where('operate','+')->sum('money');
        $money_record_qc_sub = Db::name("money_record")->where('user_id',$row['user_id'])->where('type', 'qc')->where('operate','-')->sum('money');
        $row['money_record_qc'] = bcsub($money_record_qc_add,$money_record_qc_sub,3);

        // 拼中次数
        $row['pintuan_win'] = Db::name('pintuan')->where('user_id',$row['user_id'])->where('status','拼中')->count();
        // 未拼中次数
        $row['pintuan_not_win'] = Db::name('pintuan')->where('user_id',$row['user_id'])->where('status','未拼中')->count();
        // 未拼中退还次数
        $row['pintuan_not_win_is_not_fan'] = Db::name('money_record')->where('user_id',$row['user_id'])->where('type','cny')->where('content','未拼中退还')->count();

        // 用户(成功)总充值
        $row['recharge_sum'] = Db::name('recharge')->where('user_id',$row['user_id'])
                                                   ->where('status','充值成功')
                                                   ->sum('usdt');
        // 用户(已到帐)总提现:
        $row['withdraw_sum'] = Db::name('withdraw')->where('user_id',$row['user_id'])
                                                   ->where('status','已到账')
                                                   ->sum('arrive_num');
        // 钱包地址是否在黑名单
        $row['in_blacklist'] = 0;
        $bl = Db::name('wallet_address_blacklist')->where('wallet_address', $row['address'])->find();
        if($bl){
            $row['in_blacklist'] = 1;
        }
        
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
