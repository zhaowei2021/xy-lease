<?php

namespace app\admin\model\xylease\user;

use think\Model;


class View extends Model
{

    

    

    // 表名
    protected $table = 'xylease_user_view';
    
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
        return ['notice' => __('Notice'), 'activity' => __('Activity')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
