<?php

namespace app\api\model\xylease\distribution;

use think\Model;
use addons\xylease\service\distribution\Distribution as DistributionService;

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
        return ['forbidden' => __('Status forbidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    /**
     * 可参与的分销商
     */
    public function scopeAvaliable($query) {
        return $query->where('status', 'in', [DistributionService::DISTRIBUTION_STATUS_NORMAL]);
    }

    /**
     * 等级
     */
    public function level()
    {
        return $this->hasOne('\app\api\model\xylease\distribution\Level', 'level_id','id')->field('id,name,image');
    }

}
