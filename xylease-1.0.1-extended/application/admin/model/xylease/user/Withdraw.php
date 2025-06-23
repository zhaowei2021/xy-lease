<?php

namespace app\admin\model\xylease\user;

use think\Model;

class Withdraw extends Model
{


    

    // 表名
    protected $name = 'xylease_user_withdraw';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text',
        'audit_time_text',
        'payment_time_text',
        'status_text'
    ];
    

    
    public function getTypeList()
    {
        return ['coach' => __('Type coach'), 'distribution' => __('Type distribution')];
    }

    public function getStatusList()
    {
        return ['0' => __('待审核'),'1' => __('待转账'),'2' => __('已转账'),'-1' => __('已拒绝')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAuditTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['audit_time']) ? $data['audit_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPaymentTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['payment_time']) ? $data['payment_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setAuditTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPaymentTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function user()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
