<?php

namespace app\admin\model\xylease\distribution;

use think\Model;

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
        'grade_text',
        'upgrade_type_text',
        'status_text'
    ];

    
    public function getGradeList()
    {
        return ['1' => __('Grade 1'), '2' => __('Grade 2'), '3' => __('Grade 3'), '4' => __('Grade 4'), '5' => __('Grade 5'), '6' => __('Grade 6'), '7' => __('Grade 7'), '8' => __('Grade 8'), '9' => __('Grade 9'), '10' => __('Grade 10')];
    }

    public function getUpgradeTypeList()
    {
        return ['no' => __('不自动升级'),'or' => __('满足以下任意条件')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal')];
    }

    //升级条件
    public function getConditionList()
    {
        return ['c1' => __('一级分销订单总数'),'c2' => __('一级分销订单总额'),'c3' => __('自购订单总数'),'c4' => __('自购订单总额'),'c5' => __('一级下线人数')];
    }


    public function getGradeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['grade']) ? $data['grade'] : '');
        $list = $this->getGradeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getUpgradeTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['upgrade_type']) ? $data['upgrade_type'] : '');
        $list = $this->getUpgradeTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
