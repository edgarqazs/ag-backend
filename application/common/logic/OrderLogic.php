<?php



namespace app\common\logic;
use think\Model;
use think\Db;
use think\Config;
use think\Cache;
class OrderLogic
{

    /*
        method：添加收益记录
        参数
        type                1：推荐收益，2：团队收益，3：节点收益，4：加权分红
        user_id             用户id
        money               金额
    */
    public function addRecord($type,$user_id,$money){
        $user = Db::name('user')->where('id',$user_id)->find();

        $data['user_id'] = $user_id;
        $data['username'] = $user['username'];
        $data['money'] = $money;
        $data['ctime'] = time();

        if($type==1){
            Db::name('user')->where('id',$user_id)->setInc('share_money',$money);
            Db::name('order_share')->insert($data);
        }
        if($type==2){
            Db::name('user')->where('id',$user_id)->setInc('team_money',$money);
            Db::name('order_team')->insert($data);
        }
        if($type==3){
            Db::name('user')->where('id',$user_id)->setInc('node_money',$money);
            Db::name('order_node')->insert($data);
        }
        if($type==4){
            Db::name('user')->where('id',$user_id)->setInc('company_money',$money);
            Db::name('order_company')->insert($data);
        }

        return true;
    }


    /*
        method：添加收益记录
        参数
        type                usdt,cny,ag,sc,qc
        user_id             用户id
        money               金额
        operate             +,-
        content             描述
    */
    public function addMoneyRecord($type,$user_id,$money,$operate,$content){
        $user = Db::name('user')->where('id',$user_id)->find();
        switch ($type) {
            case 'usdt':
                $data['pre_money'] = $user['usdt'];
                break;
            case 'cny':
                $data['pre_money'] = $user['cny'];
                break;
            case 'ag':
                $data['pre_money'] = $user['ag'];
                break;
            case 'sc':
                $data['pre_money'] = $user['sc'];
                break;
            case 'qc':
                $data['pre_money'] = $user['qc'];
                break;
        }
        $data['user_id'] = $user_id;
        $data['username'] = $user['username'];
        $data['money'] = $money;
        $data['type'] = $type;
        $data['operate'] = $operate;
        $data['content'] = $content;
        $data['ctime'] = time();

        Db::name('money_record')->insert($data);

        return true;
    }

   
    
}