<?php

namespace app\api\model\xylease\store;

use think\Model;


class Store extends Model
{

    

    // 表名
    protected $name = 'xylease_store';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public static function getDetail($id)
    {
        $store = self::where(['id'=>$id])->find();
        return $store;
    }

    public function getImagesAttr($value)
    {
        $imagesArray = [];
        if (!empty($value)) {
            $imagesArray = explode(',', $value);
            foreach ($imagesArray as &$v) {
                $v = cdnurl($v, true);
            }
            return $imagesArray;
        }
        return $imagesArray;
    }

    public function getLogoAttr($value)
    {
        if (!empty($value)) return cdnurl($value, true);
    }
    

}
