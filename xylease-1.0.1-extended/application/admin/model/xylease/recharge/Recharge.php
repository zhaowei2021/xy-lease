<?php

namespace app\admin\model\xylease\recharge;

use think\Model;


class Recharge extends Model
{

    

    

    // 表名
    protected $name = 'xylease_recharge';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['normal' => __('Status normal'), 'hidden' => __('Status hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function store()
    {
        return $this->belongsTo('\app\admin\model\xylease\store\Store', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }



}
