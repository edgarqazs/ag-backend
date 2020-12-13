<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
/**
 * 
 *
 * @icon fa fa-user
 */
class Team extends Backend
{
    
    /**
     * Team模型对象
     * @var \app\admin\model\Team
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Team;
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

            $request = $this->request->request();
            

            $total = $this->model
                ->where($where)
                ->where('find_in_set('.$request['user_id'].',family_id)')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('find_in_set('.$request['user_id'].',family_id)')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $list = json_decode(json_encode($list),true);
            foreach ($list as $k => $v) {
                $temp = explode(',', $v['family_id']);
                $temp = array_reverse($temp);
                $level = 0;
                foreach ($temp as $key => $val) {
                    if($val == $request['user_id']){
                        $level = $key+1;
                    }
                }
                $list[$k]['team_level'] = '第'.$level.'代下级';
            }

            $team_num = $this->model
                ->where($where)
                ->where('find_in_set('.$request['user_id'].',family_id)')
                ->count();

            $mywhere['level'] = array('NEQ','-1');
            $real_num = $this->model
                ->where($where)
                ->where($mywhere)
                ->where('find_in_set('.$request['user_id'].',family_id)')
                ->count();

            $result = array("total" => $total, "rows" => $list,'team_num'=>$team_num,'real_num'=>$real_num);

            return json($result);
        }
        return $this->view->fetch();
    }

}
