<?php

namespace app\api\model\xylease\goods;

use think\Model;

class SkuPrice extends Model
{

    // 表名
    protected $name = 'xylease_goods_sku_price';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text',
        'goods_sku_id_arr'
    ];
    
    public function getImageAttr($value, $data)
    {
        if (!empty($value)) return cdnurl($value, true);
        return $value;

    }

    public function getGoodsSkuIdArrAttr($value, $data)
    {
        return explode(',', $data['goods_sku_ids']);
    }

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }
    
    public function getStatusList()
    {
        return ['up' => __('Status up'),  'down' => __('Status down')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }



}
