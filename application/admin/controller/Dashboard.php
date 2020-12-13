<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use think\Db;
/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        // // 今日买入
        // $day_start = strtotime(date('Y-m-d'));
        // $day_end = strtotime(date('Y-m-d'))+24*3600-1;

        // $where['ctime'] = array('BETWEEN',array($day_start,$day_end));
        // $day_money = Db::name('pintuan')->where($where)->count();
        // $day_money = $day_money*700;
        // // 今日买入 end

        
        // // 本周买入
        // $sdefaultDate = date("Y-m-d");  
        // $first = 1;  
        // $w = date('w',strtotime($sdefaultDate));  
        // $week_start = strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days');
        // $week_end = $week_start+7*24*3600-1;
        
        // $where['ctime'] = array('BETWEEN',array($week_start,$week_end));
        // $week_money = Db::name('pintuan')->where($where)->count();
        // $week_money = $week_money*700;
        // // 本周买入 end


        // // 本月买入
        // $month_day = date('t');
        // $month_start = strtotime(date('Y-m-01'));
        // $month_end = $month_start+date('t')*24*3600-1;

        // $where['ctime'] = array('BETWEEN',array($month_start,$month_end));
        // $month_money = Db::name('pintuan')->where($where)->count();
        // $month_money = $month_money*700;
        // // 本月买入 end


        // // 买入
        // $all_money = Db::name('pintuan')->count();
        // $all_money = $all_money*700;
        // // 本月买入 end

        // $sell_num = Db::name('usdtorder')->where('status',0)->sum('num');
        // $buy_num = Db::name('usdtorder')->where('status',3)->sum('num');

        // $where_usdt['status'] = array('IN','1,2');
        // $now_num = Db::name('usdtorder')->where($where_usdt)->sum('num');

        // $this->assign('day_money',$day_money);
        // $this->assign('week_money',$week_money);
        // $this->assign('month_money',$month_money);
        // $this->assign('all_money',$all_money);

        // $this->assign('sell_num',$sell_num);
        // $this->assign('buy_num',$buy_num);
        // $this->assign('now_num',$now_num);


        $blue_one = Db::name('user')->sum('usdt');
        $blue_two = Db::name('recharge')->sum('money');
        $blue_three = Db::name('withdraw')->where('status','未处理')->sum('num');
        $blue_four = Db::name('withdraw')->where('status','已到账')->sum('num');

        $this->assign('blue_one',round($blue_one,2));
        $this->assign('blue_two',round($blue_two,2));
        $this->assign('blue_three',round($blue_three,2));
        $this->assign('blue_four',round($blue_four,2));


        $yellow_one = Db::name('user')->sum('cny');

        $where['type'] = 'cny';
        $where['content'] = '闪兑转入';
        $yellow_two = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $where['type'] = 'cny';
        $where['content'] = '闪兑转出';
        $yellow_three = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $where['type'] = 'cny';
        $where['content'] = array('IN',['直推转入','间推转入']);
        $yellow_four = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $where['type'] = 'cny';
        $where['content'] = '团队奖励转入';
        $yellow_five = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $where['type'] = 'cny';
        $where['content'] = '节点分红';
        $yellow_six = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $where['type'] = 'cny';
        $where['content'] = array('IN',['加权分红','额外加权分红']);
        $yellow_seven = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $where['status'] = 1;
        $yellow_eight = Db::name('agorder')->where($where)->sum('num');
        unset($where);

        $where['status'] = 1;
        $yellow_nine = Db::name('agorder')->where($where)->sum('money');
        unset($where);

        $where['status'] = 0;
        $where['type'] = 2;
        $yellow_ten = Db::name('agorder')->where($where)->sum('money');
        unset($where);

        $where['status'] = 0;
        $where['type'] = 2;
        $yellow_eleven= Db::name('agorder')->where($where)->count();
        unset($where);

        $this->assign('yellow_one',round($yellow_one,2));
        $this->assign('yellow_two',round($yellow_two,2));
        $this->assign('yellow_three',round($yellow_three,2));
        $this->assign('yellow_four',round($yellow_four,2));
        $this->assign('yellow_five',round($yellow_five,2));
        $this->assign('yellow_six',round($yellow_six,2));
        $this->assign('yellow_seven',round($yellow_seven,2));
        $this->assign('yellow_eight',round($yellow_eight,2));
        $this->assign('yellow_nine',round($yellow_nine,2));
        $this->assign('yellow_ten',round($yellow_ten,2));
        $this->assign('yellow_eleven',round($yellow_eleven,2));


        $red_one = Db::name('user')->sum('ag');;
        $red_two = Db::name('user')->sum('flow_ag');;
        $red_three = Db::name('user')->sum('lock_ag');;

        $where['type'] = 'ag';
        $where['content'] = 'ag认购';
        $red_four = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $where['type'] = 'ag';
        $where['operate'] = '-';
        $red_five = Db::name('money_record')->where($where)->sum('money');
        unset($where);

        $red_six = Db::name('pintuan')->count();
        unset($where);

        $start_time = strtotime(date('Y-m-d'));
        $end_time = $start_time+24*3600-1;
        $where['ctime'] = array('BETWEEN',array($start_time,$end_time));
        $red_seven = Db::name('pintuan')->where($where)->count();
        unset($where);

        $this->assign('red_one',round($red_one,2));
        $this->assign('red_two',round($red_two,2));
        $this->assign('red_three',round($red_three,2));
        $this->assign('red_four',round($red_four,2));
        $this->assign('red_five',round($red_five,2));
        $this->assign('red_six',round($red_six,2));
        $this->assign('red_seven',round($red_seven,2));


        return $this->view->fetch();
    }

}
