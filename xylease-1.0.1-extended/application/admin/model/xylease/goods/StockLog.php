<?php

namespace app\admin\model\xylease\goods;

use think\Model;


class StockLog extends Model
{

    

    

    // 表名
    protected $name = 'xylease_goods_stock_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'operaterole_text'
    ];
    

    
    public function getTypeList()
    {
        return ['sysadd' => __('Type sysadd'), 'sysedit' => __('Type sysedit'), 'ordercreate' => __('Type ordercreate'), 'ordercancel' => __('Type ordercancel'), 'orderclose' => __('Type orderclose'), 'orderback' => __('Type orderback')];
    }

    public function getOperateroleList()
    {
        return ['admin' => __('Operaterole admin'), 'staff' => __('Operaterole staff'), 'user' => __('Operaterole user')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOperateroleTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['operaterole']) ? $data['operaterole'] : '');
        $list = $this->getOperateroleList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
