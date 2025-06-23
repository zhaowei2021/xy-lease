<?php

namespace app\admin\model\xylease\user;

use think\Model;


class Money extends Model
{

    

    

    // 表名
    protected $name = 'xylease_user_money';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text'
    ];

    public function getTypeList()
    {
        return [
                    'recharge' => '在线充值',
                    'sys' => '后台操作', 
                    'return_order_deposit' => '订单退押金',
                    'pay_order' => '订单支付',
                ];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function user()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
