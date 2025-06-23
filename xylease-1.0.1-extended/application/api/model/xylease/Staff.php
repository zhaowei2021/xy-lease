<?php

namespace app\api\model\xylease;

use think\Model;


class Staff extends Model
{

    

    // 表名
    protected $name = 'xylease_staff';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'role_text',
        'status_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getRoleList()
    {
        return ['boss' => __('Role boss'), 'clerk' => __('Role clerk')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Status normal'), 'hidden' => __('Status hidden')];
    }


    public function getRoleTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['role']) ? $data['role'] : '');
        $list = $this->getRoleList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getHeadimageAttr($value)
    {
        if (!empty($value)) return cdnurl($value, true);

    }


    public function user()
    {
        return $this->belongsTo('\app\api\model\xylease\user\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
