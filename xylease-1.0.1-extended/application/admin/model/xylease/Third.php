<?php

namespace app\admin\model\xylease;

use think\Model;

class Third extends Model
{

    // 表名
    protected $name = 'xylease_third';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'platform_text'
    ];

    public function getPlatformList()
    {
        return ['wxMiniProgram' => __('微信小程序'),'wxOfficialAccount' => __('微信公众号')];
    }


    public function getPlatformTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['platform']) ? $data['platform'] : '');
        $list = $this->getPlatformList();
        return isset($list[$value]) ? $list[$value] : '';
    }
}
