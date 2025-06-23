<?php

namespace app\admin\model\xylease\goods;

use think\Model;

class Goods extends Model
{
    

    // 表名
    protected $name = 'xylease_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'issku_text',
        'status_text',
        'stock',
        'type_text'
    ];
    
    public function getStockAttr($value, $data)
    {
        return SkuPrice::where([
            'goods_id' => $data['id'],
            'status' => 'up',
        ])->sum('stock');
    }

    

    public function getDisRuleList()
    {
        return ['0' => __('默认规则'),'1' => __('单独设置')];

    }
    
    public function getDisRuleTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['issku']) ? $data['issku'] : '');
        $list = $this->getDisRuleList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getIsDisList()
    {
        return ['0' => __('不参与'),'1' => __('参与')];
    }
    
    public function getIsDisTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['issku']) ? $data['issku'] : '');
        $list = $this->getIsDisList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getIsSkuList()
    {
        return ['0' => __('单规格'), '1' => __('多规格')];
    }
    
    public function getIsSkuTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['issku']) ? $data['issku'] : '');
        $list = $this->getIsSkuList();
        return isset($list[$value]) ? $list[$value] : '';
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

    public function getTypeList()
    {
        return ['single' => __('Type single'),  'package' => __('Type package')];
    }
    
    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getStocktypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function skuPrice()
    {
        return $this->hasMany('\app\admin\model\xylease\goods\SkuPrice', 'goods_id');
    }

}
