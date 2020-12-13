<?php
namespace app\admin2\controller;
use think\Db;
class NodeOrder extends Base{
     
    function index($page=1,$limit=20,$member=''){
        if(request()->isGet()){
            return $this->fetch(); 
        }else{
        
            $where=[" 1=1 "];
            if($member!=''){
                $where[]=" n.user_id = '{$member}' or u.username like '%{$member}%' ";
            }
            
            $where=join(" and ",$where);
            $count=Db::name("node")->alias('n')->join("user u","u.id=n.user_id")->where($where)->count();
            $data=Db::name("node")->alias('n')->field("n.*,u.username")->join("user u","u.id=n.user_id")->where($where)->page($page)->limit($limit)->select();
            foreach($data as &$item){
                $item['ctime']=date("Y-m-d H:i",$item['ctime']);
            }
            return json([
                 'code'=>'0'
                ,'data'=>$data
                ,'count'=>$count
                ,'msg'=>''
            ]);
            
        }
    }
    
  
    
}