<?php

namespace app\api\model\xylease\distribution;

use think\Model;
use traits\model\SoftDelete;

class Level extends Model
{

    // 表名
    protected $name = 'xylease_distribution_level';
    
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
        return ['normal' => __('显示'),'hidden' => __('隐藏')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getImageAttr($value, $data)
    {
        if (!empty($value)) return cdnurl($value, true);
    }

}
