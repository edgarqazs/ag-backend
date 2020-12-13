<?php

namespace app\admin\model;

use think\Model;


class Moneyrecord extends Model
{

    

    

    // 表名
    protected $name = 'money_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'operate_text',
        'ctime_text'
    ];
    

    
    public function getTypeList()
    {
        return ['usdt' => __('Usdt'), 'cny' => __('Cny'), 'ag' => __('Ag'), 'sc' => __('Sc'), 'qc' => __('Qc')];
    }

    public function getOperateList()
    {
        return ['+' => __('+'), '-' => __('-')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOperateTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['operate']) ? $data['operate'] : '');
        $list = $this->getOperateList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['ctime']) ? $data['ctime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
