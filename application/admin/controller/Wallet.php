<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Wallet extends Backend
{
    
    /**
     * Wallet模型对象
     * @var \app\admin\model\Wallet
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Wallet;
        $this->view->assign("typeList", $this->model->getTypeList());
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
                $list[$k]['user_id'] = Db::name('user')->where('id',$v['user_id'])->value('username');
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

                    if($params['status'] == '审核通过'){
                        if($row['type'] == '银行卡'){
                            Db::name('user')->where('id',$row['user_id'])->update(['is_bank'=>1]);
                        }
                        if($row['type'] == '支付宝'){
                            Db::name('user')->where('id',$row['user_id'])->update(['is_ali'=>1]);
                        }
                        if($row['type'] == '微信'){
                            Db::name('user')->where('id',$row['user_id'])->update(['is_wechat'=>1]);
                        }
                    }

                    if($params['status'] == '审核失败'){
                        if($row['type'] == '银行卡'){
                            Db::name('user')->where('id',$row['user_id'])->update(['is_bank'=>2]);
                        }
                        if($row['type'] == '支付宝'){
                            Db::name('user')->where('id',$row['user_id'])->update(['is_ali'=>2]);
                        }
                        if($row['type'] == '微信'){
                            Db::name('user')->where('id',$row['user_id'])->update(['is_wechat'=>2]);
                        }
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
