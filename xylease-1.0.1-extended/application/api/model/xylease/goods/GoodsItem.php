<?php

namespace app\api\model\xylease\goods;

use think\Model;


class GoodsItem extends Model
{


    // 表名
    protected $name = 'xylease_goods_item';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];



    public function getGoodsimageAttr($value, $data)
    {
        if (!empty($value)) return cdnurl($value, true);

    }



}
