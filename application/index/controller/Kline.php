<?php

namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
use think\Controller;
use think\Db;

class Kline extends Controller
{

    
    function aglog(){
        $param['status'] = 2;
        $list = Db::name('agorder')->where($param)->order('ctime desc')->limit(20)->select();
        foreach ($list as &$item){
            $item['ctime']=date('H:i:s',$item['ctime']);
        }
        return json(['code'=>1,'msg'=>'成功','data'=>$list]);
    }
    function aglog_group(){
        $list=Db::name('ag_k_data')->limit(50)->order('id desc')->select();
        return json(['code'=>1,'msg'=>'成功','data'=>$list]);
    }
    
    function getTxData(){
        /**
         * "lastPrice": 8.944,
		// 涨幅
		"upRate": "-79.67%",
		// 1涨绿 2跌红
		"upFlag": "2",
		// 24小时交易量
		"volume": 3,
		// 24小时最高价
		"high": 11.922,
		// 24小时最低价
		"low": 8.944
         * 
         */
         $prev=Db::name('ag_k_data')->limit(1)->order('id desc')->find();
         $data['lastPrice']= Db::name('agorder')->where("status=2")->order('id desc')->limit(1)->value('price');
         $upVal=intval(($data['lastPrice']-$prev['agv_price'])*100/$prev['agv_price']);
         if($data['lastPrice']>=$prev['agv_price']){
             $upRate='+'.$upVal.'%';
             $upFlag=2;
         }else{
             $upRate='-'.$upVal.'%';
             $upFlag=1;
         }
         $data['upRate']=$upRate;
         $data['upFlag']=$upFlag;
         $prev_time=time()-86400;
         $data['volume']=Db::name('agorder')->where("status=2 and trade_time>='{$prev_time}'")->count();
         $row=Db::name('agorder')->field("min(price) as mi,max(price) as ma")->where("status=2 and trade_time>='{$prev_time}'")->find();
         $data['high']=$row['ma'];
         $data['low']=$row['mi'];
         return json(['code'=>1,'data'=>$data]);
         
    }
}
