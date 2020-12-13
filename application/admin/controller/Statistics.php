<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use think\Db;
/**
 * 统计
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Statistics extends Backend
{

    /**
     * 今日统计
     */
    public function today()
    {
        //今日起始时间
        $day_start = strtotime(date('Y-m-d'));
        $day_end = strtotime(date('Y-m-d'))+24*3600-1;

        //AG挂卖总额
        $where['ctime'] = array('BETWEEN',array($day_start,$day_end));
        $where['status'] = 0;
        $where['type'] = 2;
        $red_one = Db::name('agorder')->where($where)->sum('money');
        unset($where);

        $this->assign('red_one',round($red_one,2));

        //AG挂卖订单数量
        $where['ctime'] = array('BETWEEN',array($day_start,$day_end));
        $where['status'] = 0;
        $where['type'] = 2;
        $yellow_one= Db::name('agorder')->where($where)->count();
        unset($where);

        $this->assign('yellow_one',round($yellow_one,2));

        return $this->view->fetch();
    }

}
