<?php
namespace app\admin2\controller;
use think\Request;
use think\Db;
class System extends Base{
    private $sets=[
        [
            'group_title'=>'交易设置',
            'fields'=>[
                 [
                    'type'=>'switch',
                    'title'=>'交易开关',
                    'field'=>'switch_ag_trade',
                    'value'=>'0',
                    'desc'=>'关闭后APP显示暂未开放！'
                ],
                [
                    'type'=>'switch',
                    'title'=>'行情开关',
                    'field'=>'switch_ag_market',
                    'value'=>'0',
                    'desc'=>'关闭后APP显示暂未开放！'
                ],
                [
                    'type'=>'number',
                    'title'=>'参考报价',
                    'field'=>'transact_new_price',
                    'value'=>'0',
                    'desc'=>'前端交易参考报价！'
                ],
                [
                    'type'=>'number',
                    'title'=>'最小交易',
                    'field'=>'transact_min',
                    'value'=>'0',
                    'desc'=>'前端交易时最大值，低于此值则不可挂单！'
                ],
                [
                    'type'=>'number',
                    'title'=>'最大交易',
                    'field'=>'transact_max',
                    'value'=>'999999',
                    'desc'=>'前端交易时最小值，高于此值则不可挂单！'
                ],
                [
                    'type'=>'time',
                    'title'=>'交易时间',
                    'field'=>'transact_time',
                    'value'=>'00:00:00 ~ 23:59:59',
                    'desc'=>'交易时间范围，非在此范围不可发生交易'
                ],
                [
                    'type'=>'text',
                    'title'=>'最小报错消息',
                    'field'=>'transact_min_err',
                    'value'=>'',
                    'desc'=>'用于交易时 低于最小值 显示的报错消息'
                ],
                [
                    'type'=>'text',
                    'title'=>'最大报错消息',
                    'field'=>'transact_max_err',
                    'value'=>'',
                    'desc'=>'用于交易时 高于最大值 显示的报错消息'
                ],
                [
                    'type'=>'text',
                    'title'=>'时间报错消息',
                    'field'=>'transact_time_err',
                    'value'=>'',
                    'desc'=>'用于交易时 不在交易期间 显示的报错消息'
                ]
            ]
        ],
        [
            'group_title'=>'其他设置',
            'fields'=>[
                
            ]
        ]
    ];
    function index(Request $req,$idx=0){
        $data=Db::name("site")->find();
        if($req->isGet()){
            $this->assign('idx',$idx);
            foreach ($this->sets[$idx]['fields'] as &$item){
                if(isset($data[$item['field']])){
                    $item['value']=$data[$item['field']];
                }
            }
            $this->assign('sets',$this->sets);
            return $this->fetch();
        }else{
            $post_data=$req->post('data/a');
            $update=[];
            foreach ($post_data as $field=>$value){
                if(isset($data[$field]) && $data[$field]!=$value){
                    $update[$field]=$value;
                }
            }
            if(!empty($update)){
                Db::name("site")->where("id=1")->update($update);
            }
            return json(['code'=>1,'msg'=>'修改成功']);
        }
    }
}