<?php
namespace app\admin2\controller;
use think\Db;
use think\Request;
class Agorder extends Base{
    function orderlist(){
        if(request()->isGet()){
            
            return $this->fetch();
        }else{
            $page=request()->post('page',1);
            $limit=request()->post('limit',20);
            $status=request()->post('status','all');
            $type=request()->post('type','all');
            $date=request()->post('date','');
            $member=request()->post('member','');
            $sort_field=request()->post('sort_field','');
            $sort_value=request()->post('sort_value','');
            $where=[];
            $where[]="1=1";
            if($status!='all'){
                $where[]=" ao.status='{$status}'";
            }
            if($type!='all'){
                $where[]=" ao.type='{$type}'";
            }
            if($date!=''){
                list($begin_time,$end_time)=explode('~',$date);
                $begin_time=strtotime($begin_time);
                $end_time=strtotime($end_time);
                $where[]=" ao.ctime>='{$begin_time}' and ao.ctime<='{$end_time}'";
            }
            if($member){
                if($type=='all'){
                    $where[]=" (ao.buyer_id='{$member}' or ao.seller_id='{$member}')";
                }else if($type=='1'){
                    $where[]="(ao.buyer_id='{$member}')";
                }else if(1){
                    $where[]="(ao.seller_id='{$member}')";
                }
            }
            $where=join(' and ',$where);
            $count=Db::name("agorder")->alias("ao")->where($where)->count();
            $data=Db::name("agorder")->alias("ao")
            ->field("ao.*,u1.username as buyer_username,u2.username as seller_username")
            ->join("user u1"," u1.id=ao.buyer_id","left")
            ->join("user u2 "," u2.id=ao.seller_id","left")
            ->where($where)->page($page)->limit($limit);
            if($sort_value&&$sort_field){
                if($sort_value=='asc'){
                    $data->order($sort_field);
                }else{
                    $data->order("{$sort_field} desc");
                }
            }else{
                $data->order("ao.id desc");
            }
            $data=$data->select();
            if(!empty($data)){
                foreach($data as &$item){
                    $item['ctime']=date("Y-m-d H:i",$item['ctime']);
                    if(!empty($item['trade_time'])){
                        $item['trade_time']=date("Y-m-d H:i",$item['trade_time']);
                    }else{
                        $item['trade_time']='---';
                    }
                    $item['type']=$item['type']==1?'买入型':'卖出型';
                    if($item['status']==0){
                        $item['status_desc']='等待交易';
                    }else if($item['status']==1){//数据库状态跟实际代码状态对不上！！ 
                        $item['status_desc']='---';
                    }else if($item['status']==2){
                        $item['status_desc']='交易完成';
                    }else if($item['status']==3){
                        $item['status_desc']='已撤销';
                    }
                    if(empty($item['seller_id'])){
                        $item['seller_id']='--';
                        $item['seller_username']='--';
                    }
                    if(empty($item['buyer_id'])){
                        $item['buyer_id']='--';
                        $item['buyer_username']='--';
                    }
                }
            }else{
                $data=[];
            }
            return json([
                 'code'=>'0'
                ,'data'=>$data
                ,'count'=>$count
                ,'msg'=>''
            ]);
        }
    }
    //撤单
    function cd(){
        $id=request()->post('id');
        $agorder=Db::name("agorder")->where("id='{$id}' and status=0")->lock()->find();
        if(!empty($agorder)){
            if($agorder['type']==1){
                Db::name('agorder')->where('id',$id)->update(['status'=>3]);
                Db::name('user')->where('id',$agorder['buyer_id'])->setInc('cny',$agorder['money']);
                model('Order','logic','common')->addMoneyRecord('cny',$agorder['buyer_id'],$agorder['money'],'+','ag交易撤销，系统操作');
            }
            if($agorder['type']==2){
                Db::name('agorder')->where('id',$id)->update(['status'=>3]);
                Db::name('user')->where('id',$agorder['seller_id'])->setInc('flow_ag',$agorder['num']);
                model('Order','logic','common')->addMoneyRecord('flow_ag',$agorder['seller_id'],$agorder['num'],'+','ag交易撤销，系统操作');
            }
        }
        return json(['code'=>1,'msg'=>'操作成功']);
    }
    function tj(Request $req,$page=1,$limit=15,$date=''){
        if($req->isGet()){
            return $this->fetch();
        }else{
            $where="1=1";
            if($date!=''){
                list($begin,$end)=explode('~',$date);
                $begin=trim($begin);
                $end=trim($end);
                $where.=" and date_day >='{$begin}' and date_day<='{$end}'";
            }
            $count=Db::name("ag_k_data")->where($where)->count();
            $data=Db::name("ag_k_data")->where($where)->order("id desc")->page($page)->limit($limit)->select();
            return json([
                 'code'=>'0'
                ,'data'=>$data
                ,'count'=>$count
                ,'msg'=>''
            ]);
        }
    }
    function retj($id){
        $row=Db::name("ag_k_data")->where("id='{$id}'")->find();
        $data=$this->getKDataByDay($row['date_day']);
        Db::name("ag_k_data")->where("id='{$id}'")->update($data);
        return json(['code'=>1,'msg'=>'操作成功']);
    }
    private function getKDataByDay($day){
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
        return $data;
    }
    function editfield($id,$field,$value){
        Db::name("ag_k_data")->where("id='{$id}'")->update([$field=>$value]);
        return json(['code'=>1,'msg'=>'操作成功']);
    }
}