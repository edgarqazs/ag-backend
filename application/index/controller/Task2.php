<?php

namespace app\index\controller;
use think\Controller;
use think\Db;
class Task2 extends Controller
{
    /**
     * 计算昨日AG订单 写入数据库
     */
    public function kDataByPrevDay(){
                
        $prev_day=date("Y-m-d",(time()-86400));
        $this->doBuildKDataByDay($prev_day);
        die("ok");
    }
    private function doBuildKDataByDay($day){
                
        $exist=Db::name("ag_k_data")->where("date_day='{$day}'")->count();
        if($exist){
            return true;
        }
        $begin=strtotime($day);
        $end=$begin+86400;
        $data=[];
        $data['date_day']=$day;
        $data['sum_trans_count']=Db::name("agorder")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and status=2 ")
            ->count();
        $data['sum_buy_count']=Db::name("agorder")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and type=1 and status!=3")
            ->count();
        $data['sum_sell_count']=Db::name("agorder")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and type=2 and status!=3")
            ->count();
        $data['sum_trans_amount']=Db::name("agorder")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and status=2 ")
            ->sum('money');
        $data['sum_buy_amount']=Db::name("agorder")
            ->field("sum(money) as d")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and type=1 and status=2 ")
            ->sum('money');
        $data['sum_sell_amount']=Db::name("agorder")
            ->field("sum(money)  as d")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and type=2 and status=2 ")
            ->sum('money');
        $data['sum_sell_amount']=Db::name("agorder")
            ->field("sum(money)  as d")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and type=2 and status=2 ")
            ->sum('money');
        $temp= Db::name("agorder")
            ->field("min(price)  as mind,max(price)  as maxd,avg(price)  as avgd,min(num) as min_c,max(num)  as max_c")
            ->where("ctime>='{$begin}' and ctime<'{$end}' and status=2 ")
            ->find();   
        if(!empty($temp)){
            $data['min_price']=$temp['mind'];
            $data['max_price']=$temp['maxd'];
            $data['agv_price']=$temp['avgd'];
            $data['min_num']=$temp['min_c'];
            $data['max_num']=$temp['max_c'];
        }else{
            $data['min_price']='0';
            $data['max_price']='0';
            $data['agv_price']='0';
            $data['min_num']='0';
            $data['max_num']='0';
        }
        $data['begin_price']=Db::name("agorder")->where("status=2 and ctime>'{$begin}'")->order("ctime asc")->limit(1)->value('price');
        if(empty($data['begin_price'])){
            $data['begin_price']=0;
        }
        $data['end_price']=Db::name("agorder")->where("status=2 and ctime<'{$end}'")->order("ctime desc")->limit(1)->value('price');
        if(empty($data['end_price'])){
            $data['end_price']=0;
        }
        $data['add_time']=time();
        return Db::name("ag_k_data")->insert($data);
    }
    public function initKData(){
        
        $days=[
            '2020-10-15',  
            '2020-10-16',  
            '2020-10-17',  
            '2020-10-18',  
            '2020-10-19',  
            '2020-10-20',  
            '2020-10-21',  
            '2020-10-22',  
            '2020-10-23',  
            '2020-10-24',  
            '2020-10-25',  
            '2020-10-26',  
            '2020-10-27',  
            '2020-10-28',
            '2020-10-29',
            
        ];
        foreach($days as $day){
            $this->doBuildKDataByDay($day);
        }
        die('ok');
    }
}
