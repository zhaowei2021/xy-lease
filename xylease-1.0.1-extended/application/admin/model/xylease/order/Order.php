<?php

namespace app\admin\model\xylease\order;

use think\Model;

class Order extends Model
{

    // 表名
    protected $name = 'xylease_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'paytype_text',
        'paytime_text',
        'platform_text',
        'type_text',
        'status_text'
    ];

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? '');
        $valueArr = explode(',', $value);
        $list = $this->getTypeList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    public function getTypeList()
    {
        return ['lease' => __('Type lease'), 'buy' => __('Type buy'), 'service' => __('Type service')];
    }
    
    public function getPayTypeList()
    {
        return ['wechat' => __('微信支付'),'balance'=>__('余额支付')];
    }

    public function getPlatformList()
    {
        return ['wxMiniProgram' => __('微信小程序')];
    }

    public function getStatusList()
    {
        return ['0' => __('待支付'), '1' => __('进行中'), '2' => __('待归还'),'3' => __('已完成'),'-1' => __('已取消'),'-2' => __('已关闭')];
    }

    public function getPayTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytype']) ? $data['paytype'] : '');
        $list = $this->getPayTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytime']) ? $data['paytime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPlatformTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['platform']) ? $data['platform'] : '');
        $list = $this->getPlatformList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setPaytimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    
    public function item()
    {
        return $this->hasMany('\app\admin\model\xylease\order\Item', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
