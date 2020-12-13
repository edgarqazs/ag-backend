<?php
namespace app\admin2\controller;
use think\Db;
class Ptfx extends Base{
     
    function index($date='',$page=1,$limit=15){
        if(request()->isGet()){
            return $this->fetch();
        }else{ 
            if($date==''){
                $begin_time=strtotime(date('Y-m-d'));
                $end_time=time();
            }else{
                list($begin_time,$end_time)=explode('~',$date);
                $begin_time=strtotime($begin_time);
                $end_time=strtotime($end_time);
            }
            $count=Db::name("pintuan")->where("ctime>'{$begin_time}' and ctime<'{$end_time}' and status='未拼中'")->group("user_id")->count();
                
            $pt_data=Db::name("pintuan")->alias("pd")->join("user u","u.id=pd.user_id","inner")->field('pd.user_id,count(pd.id) as un_count,u.username')->where("pd.ctime>'{$begin_time}' and pd.ctime<'{$end_time}' and pd.status='未拼中'")->group("user_id")->order("user_id desc")->page($page)->limit($limit)->select();
            
        
            if(!empty($pt_data)){
                foreach($pt_data as &$item){
                    $th_count=Db::name("money_record")->where("user_id='{$item['user_id']}' and ctime>'{$begin_time}' and ctime<'{$end_time}' and type='cny' and content='未拼中退还'")->count();
                    $th_count=$th_count?$th_count:0;
                    $item['th_count']=$th_count;
                    if($th_count==$item['un_count']){
                        $item['status']='--';
                    }else{
                        $item['status']='<red>异常</red>';
                    }
                }
            }
            
             return json([
                 'code'=>'0'
                ,'data'=>$pt_data
                ,'count'=>$count
                ,'msg'=>''
            ]);
            
        }
    }
    function dayc($date='',$page=1,$limit=15,$count="all"){
        if(request()->isGet()){
            return $this->fetch();
        }else{ 
            
            $where=[];
            if($count!='all'){
                $having =" play_count > '{$count}'";
            }else{
                $having =" play_count >= '1'";
            }
            if($date==''){
                $begin_time=strtotime(date('Y-m-d'));
                $end_time=time();
            }else{
                list($begin_time,$end_time)=explode('~',$date);
                $begin_time=strtotime($begin_time);
                $end_time=strtotime($end_time);
            }
            $where[]=" pd.ctime>'{$begin_time}'  ";
            $where[]="  pd.ctime<'{$end_time}'  ";
            try{
                $res_count=Db::name("pintuan")->alias("pd")->field("count(pd.id) as 'play_count'")->where(join(" and ",$where))->group("user_id")->having($having)->count();
                
                $pt_data=Db::name("pintuan")->alias("pd")->join("user u","u.id=pd.user_id","inner")->field("pd.user_id,count(pd.id) as 'play_count',u.username")->where(join(" and ",$where))->group("user_id")->order("user_id desc")->having($having)->page($page)->limit($limit)->select();
            }catch(\Exception $e){
                die($e->getMessage());
            }
            

             return json([
                 'code'=>'0'
                ,'data'=>$pt_data
                ,'count'=>$res_count
                ,'msg'=>''
            ]);
        }
    }
    
   
    
}