<?php
namespace app\admin2\controller;
use think\Db;
use think\Request;
class Pt extends Base{
    
    function order(Request $req,$status='all',$page=1,$limit=15,$search_field='',$search_value='',$date=''){
        if($req->isGet()){
            return $this->fetch();
        }else{
            $where="status='拼中'";
            if($date){
                list($begin,$end)=explode('~',$date);
                $begin=strtotime($begin);
                $end=strtotime($end);
                $where.=" and ctime>='{$begin}' and ctime<='{$end}'";
            }
            if($search_value && $search_field){
                $where.=" and {$search_field}={$search_value} ";
            }
            if($status!='all'){//这个拼团表 是前人设计的 没有发货状态字段！！前人的拼团逻辑代码我这边是不会改动的！我只能这样做，毕竟这个表是运行状态
            //按理说 拼团逻辑 跟订单逻辑是要分开的 两个表才对
                if($status==0){//待选择商品
                    $where.=" and product_id=0 and is_send=0 ";
                }else if($status==1){//已选择商品 待发货
                    $where.=" and product_id>0 and is_send=0 ";
                    //$where.=" and product_id>0 and (company=null or company='' )";
                }else if($status==2){//已选择商品 已发货
                    $where.=" and is_send=1 ";
                    //$where.=" and product_id>0 and (company!=null and company!='' )";
                }
            }
            $count=Db::name('pintuan')->where($where)->count();
            $data=Db::name('pintuan')->where($where)->order('id desc')->page($page)->limit($limit)->select();
            foreach ($data as &$item){
                $item['ctime']=date('Y-m-d H:i:s',$item['ctime']);
                if($item['product_id']==0){
                    $item['state']='0';
                }else if(empty($item['company'])){
                    $item['state']='1';
                }else if(!empty($item['company'])){
                    $item['state']='2';
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
    function edit1(Request $req,$id){
        if($req->isGet()){
            $this->assign('id',$id);
            return $this->fetch();
        }else{
            $company=$req->post('company');
            $sn=$req->post('sn');
            $r=Db::name('pintuan')->where("id='{$id}'")->update(['is_send'=>'1','company'=>$company,'sn'=>$sn]);
            return $r?json(['code'=>1,'msg'=>'操作成功']):json(['code'=>0,'msg'=>'操作失败']);
        }
    }
    function edit2($id){
        $r=Db::name('pintuan')->where("id='{$id}'")->update(['company'=>'','sn'=>'','is_send'=>'0']);
        return $r?json(['code'=>1,'msg'=>'操作成功']):json(['code'=>0,'msg'=>'操作失败']);
    }
    function edit3(){
        $ids=request()->post('ids/a');
        $fail=[];
        foreach($ids as $id){
            $r=Db::name('pintuan')->where("id='{$id}'")->where("(company=null or company='')and (sn=null or sn='') and is_send=0")->update(['company'=>'已发货','sn'=>'--','is_send'=>'1']);
            if(!$r){
                $fail[]=$id;
            }
            if(!empty($fail)){
                return json(['code'=>0,'msg'=>'部分操作失败','data'=>$fail]);
            }
            return json(['code'=>1,'msg'=>'操作成功']);
        }
    }
    function ptlog(){
        die('wait');
    }
    
}