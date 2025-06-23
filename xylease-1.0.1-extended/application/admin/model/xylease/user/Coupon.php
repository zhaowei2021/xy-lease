<?php

namespace app\admin\model\xylease\user;

use think\Model;


class Coupon extends Model
{

    

    

    // 表名
    protected $name = 'xylease_user_coupon';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'gettype_text',
        'usetime_text',
        'endtime_text',
        'status_text'
    ];
    

    
    public function getTypeList()
    {
    return ['reward' => __('Type reward'),/* 'discount' => __('Type discount')*/];
    }

    
    public function getGettypeList()
    {
        return ['1' => __('Gettype 1'), '2' => __('Gettype 2'), '3' => __('Gettype 3')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getGettypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['gettype']) ? $data['gettype'] : '');
        $list = $this->getGettypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getUsetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['usetime']) ? $data['usetime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endtime']) ? $data['endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setUsetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function store()
    {
        return $this->belongsTo('\app\admin\model\xylease\store\Store', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function user()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
