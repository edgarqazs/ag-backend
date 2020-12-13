<?php

namespace app\admin\controller;
use app\admin\model\AdminLog;
use app\common\controller\Backend;
use think\Db;
/**
 * 
 *
 * @icon fa fa-user
 */
class User extends Backend
{
    
    /**
     * User模型对象
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\User;
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
                $list[$k]['father_id'] = Db::name('user')->where('id',$v['father_id'])->value('username');
                $list[$k]['zhong_num'] = intval($v['zhong_num']/10);
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
                    $row_arr=$row->toArray();
                    $money_fields=[
                        'cny'=>'CNY'
                        ,'freeze_cny'=>'冻结CNY'
                        ,'ag'=>'AG'
                        ,'freeze_ag'=>'冻结AG'
                        ,'lock_ag'=>'锁仓AG'
                        ,'flow_ag'=>'流通AG'
                        ,'usdt'=>'USDT'
                        ,'freeze_usdt'=>'冻结USDT'
                        ,'sc'=>'SC'
                        ,'qc'=>'QC'
                        ,'ag_card'=>'兑换卡'
                    ];
                    
                    $title=['操作 '.$row_arr['username'].' ID：'.$ids];
                    foreach($money_fields as $field =>$name){
                        if($row_arr[$field] != $params[$field]){
                            $abs_amount=$params[$field]-$row_arr[$field];
                            $mark=$abs_amount>0?'+':'-';
                            $type=$abs_amount>0?'增加':'减少';
                            $abs_amount=abs($abs_amount);
                            $title[] = "【{$name}】{$type} {$abs_amount} ";
                            db("money_record")->insert([
                                'user_id'=>$ids,
                                'username'=>$row_arr['username'],
                                'pre_money'=>$row_arr[$field],
                                'money'=>$abs_amount,
                                'type'=>$field,
                                'content'=>'平台系统调节',
                                'operate'=>$mark,
                                'ctime'=>time()
                            ]);
                        }
                    }
                    if(!empty($title)){
                        AdminLog::setTitle(join(' ',$title));
                    }
                    
                    
                    if($params['pwd']){
                        $params['pwd'] = md5($params['pwd']);
                    }else{
                        unset($params['pwd']);
                    }

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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
