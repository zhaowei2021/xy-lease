<?php

namespace app\admin\model\xylease\distribution;

use think\Model;

class Distribution extends Model
{
    

    // 表名
    protected $name = 'xylease_distribution';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['normal' => __('Status normal'), 'forbidden' => __('Status forbidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function user()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function level()
    {
        return $this->belongsTo('\app\admin\model\xylease\distribution\Level', 'level_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function parent()
    {
        return $this->belongsTo('\app\admin\model\xylease\user\User', 'pid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    


}
