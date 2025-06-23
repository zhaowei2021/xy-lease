<?php

namespace app\admin\model\xylease\order;

use think\Model;

class Item extends Model
{

    // 表名
    protected $name = 'xylease_order_item';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [

    ];


}
