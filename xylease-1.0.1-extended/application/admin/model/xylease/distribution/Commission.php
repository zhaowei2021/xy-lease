<?php

namespace app\admin\model\xylease\distribution;

use think\Model;

class Commission extends Model
{


    // 表名
    protected $name = 'xylease_distribution_commission';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    
    public function getTypeList()
    {
        return [' apply_withdraw' => __('Type  apply_withdraw'), 'refuse_withdraw' => __('Type refuse_withdraw'), 'pay_withdraw' => __('Type pay_withdraw'), 'order' => __('Type order'), 'sys' => __('Type sys')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function user()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'distribution_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
