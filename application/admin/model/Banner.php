<?php

namespace app\admin\model;

use think\Model;


class Banner extends Model
{

    

    

    // 表名
    protected $name = 'banner';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'position_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getPositionList()
    {
        return ['积分商城' => __('积分商城'), 'AG兑换卡' => __('Ag兑换卡'), '首页' => __('首页')];
    }


    public function getPositionTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['position']) ? $data['position'] : '');
        $list = $this->getPositionList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
