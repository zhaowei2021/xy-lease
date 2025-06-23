<?php

namespace addons\xylease\service;
use app\admin\model\xylease\goods\StockLog;
use app\admin\model\xylease\goods\Goods;
use app\admin\model\xylease\goods\SkuPrice;

/**
 * 商品库存
 */
class Stock
{

    /**
     * 更新库存、添加库存记录
     */
    public static function update($goods_sku_price_id, $nums, $operate_id = 0, $type = 'sysadd',$operaterole = 'admin') 
    {

        $goodsSkuPrice = SkuPrice::get($goods_sku_price_id);
        $goods = Goods::get($goodsSkuPrice['goods_id']);

        $before = $goodsSkuPrice['stock'];
        $after = $goodsSkuPrice['stock'] + $nums;

        $stockLog = new StockLog();
        $stockLog->type = $type;
        $stockLog->goods_id = $goodsSkuPrice['goods_id'];
        $stockLog->goods_sku_price_id = $goodsSkuPrice['id'];
        $stockLog->goodsname = $goods['name'];
        $stockLog->goodsimage = $goodsSkuPrice['image'] ? $goodsSkuPrice['image'] : $goods['image'];
        $stockLog->goodsskutext = is_array($goodsSkuPrice['goodsskutext']) ? join(',', $goodsSkuPrice['goodsskutext']) : $goodsSkuPrice['goodsskutext'];
        $stockLog->before = $before;
        $stockLog->nums = $nums;
        $stockLog->after = $after;
        $stockLog->operate_id = $operate_id;
        $stockLog->operaterole = $operaterole;
        $stockLog->save();

        $goodsSkuPrice->setInc('stock',$nums);

    }
    
}
