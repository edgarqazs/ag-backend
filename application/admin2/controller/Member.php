<?php
namespace app\admin2\controller;
use think\Db;
class Member extends Base{
     
    function index($page=1,$limit=20,$member=''){
        if(request()->isGet()){
            return $this->fetch();
        }else{
         
            $where=[" 1=1 "];
            if($member!=''){
                $where[]=" id = '{$member}' or username like '%{$member}%'";
            }
            
            $where=join(" and ",$where);
            $count=Db::name("user")->field("count(*)")->where($where)->count();
            $data=Db::name("user")->where($where)->page($page)->limit($limit)->select();
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
    
    function relation(){
        if(request()->isGet()){
			return $this->fetch();
		}else{
			$uid=request()->param('uid');
			$count=request()->param('count/d');
			if($count>8){
				$count=8;
			}
			function t($uid,$count){
				$user=db("user")->field("username,id")->where("id",$uid)->find();
				$name='ID:'.$user['id'].'账号：'.$user['username'];
				$children=[];
				if($count){
					$childs=db("user")->where("father_id",$uid)->column('id');
					if(!empty($childs)){
						foreach ($childs as $tid){
							$children[]=t($tid,$count-1);
						}
					}
				}
				return [
					'name'=>$name,
					'children'=>$children
				];
			}
			$res=t($uid,$count);
			return $this->result($res,1);
		}
    }
    function loginlog($page=1,$limit=20,$member='',$ip='',$date=''){
        //cc_user_login_log
        if(request()->isGet()){
            return $this->fetch();
        }else{
         
            $where=[" 1=1 "];
            if($member!=''){
                $where[]=" user_id = '{$member}' or username like '%{$member}%'";
            }
            if($ip!=''){
                $where[]=" ip = '{$ip}'";
            }
            if($date==''){
                $begin_time=strtotime(date('Y-m-d'));
                $end_time=time();
            }else{
                list($begin_time,$end_time)=explode('~',$date);
                $begin_time=strtotime($begin_time);
                $end_time=strtotime($end_time);
            }
            $where[]=" add_time>'{$begin_time}'  ";
            $where[]=" add_time<'{$end_time}'  ";
            $where=join(" and ",$where);
            $count=Db::name("user_login_log")->field("count(*)")->where($where)->count();
            $data=Db::name("user_login_log")->where($where)->page($page)->limit($limit)->order("id desc")->select();
            foreach($data as &$item){
                $item['add_time']=date("Y-m-d H:i",$item['add_time']);
            }
            return json([
                 'code'=>'0'
                ,'data'=>$data
                ,'count'=>$count
                ,'msg'=>''
            ]);
            
        }
    }
    function jq($page=1,$limit=20,$member='',$sort_field='',$sort_value=''){
        if(request()->isGet()){
            return $this->fetch();
        }else{
            $where=[" zhong_num>=10 "];
            if($member!=''){
                $where[]=" id = '{$member}' or username like '%{$member}%'";
            }
            
            $where=join(" and ",$where);
            $count=Db::name("user")->field("count(*)")->where($where)->count();
            $data=Db::name("user")->where($where);
            if($sort_value && $sort_field){
                if($sort_value=='asc'){
                    $data->order("{$sort_field}");
                }else{
                    $data->order("{$sort_field} desc");
                }
            }
            $data=$data->page($page)->limit($limit)->select();
            if(!empty($data)){
                foreach ($data as &$item){
                    $item['ctime']=date('Y-m-d ',$item['ctime']);
                    $item['jq_num']=intval($item['zhong_num']/10);
                    $item['other_num']=intval($item['zhong_num']/50);
                }
            }
            return json([
                 'code'=>'0'
                ,'data'=>$data
                ,'count'=>$count
                ,'msg'=>''
            ]);
        }
    }
    
    function resetPayPwd($id){
        $r=Db::name("user")->where("id='{$id}'")->update(['pay_pwd'=>'']);
        return json($r?['code'=>1,'msg'=>'操作成功']:['code'=>0,'msg'=>'操作失败']);
    }

}