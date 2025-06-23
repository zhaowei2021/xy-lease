<?php

namespace app\admin\model\xylease\distribution;

use think\Model;

class Order extends Model
{


    // 表名
    protected $name = 'xylease_distribution_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text',
        'ordertype_text'
    ];

    public function getOrderTypeList()
    {
        return [ 'order' => __('租赁订单')];
    }

    public function getOrderTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['ordertype']) ? $data['ordertype'] : '');
        $list = $this->getOrderTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getStatusList()
    {
        return ['1' => __('已结算'),'0' => __('未结算'),'-2' => __('已取消'), '-1' => __('已退回')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function buyer()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'buyer_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function one()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'one_distribution_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function two()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'two_distribution_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
