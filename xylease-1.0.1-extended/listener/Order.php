<?php

namespace addons\xylease\listener;
use app\api\model\xylease\order\Order as OrderModel;
use app\api\model\xylease\order\Item as OrderItem;
use app\api\model\xylease\recharge\Order as RechargeModel;
use app\api\model\xylease\user\Coupon as UserCouponModel;
use app\api\model\xylease\goods\GoodsItem as PackageItem;
use addons\xylease\service\Stock as StockService;
use app\api\model\xylease\goods\SkuPrice;
use app\api\model\xylease\user\User;

/**
 * 订单钩子
 */
class Order
{

    // 订单支付成功 
    public function xyleaseOrderPayedAfter(&$params){

        $orderData = $params['order'];
        $orderType = $params['type'];  // 订单类型

        // 重新查询订单
        $order = null;

        // 充值订单
        if ($orderType == 'recharge') {
            $order = RechargeModel::where(['id'=>$orderData['id']])->find();
            
        }

        // 租赁订单
        if ($orderType == 'order') {
            $order = OrderModel::where(['id'=>$orderData['id']])->find();
        }

        if (!empty($order)){
            $user = User::get($order->user_id);
            if(!empty($user)){
                //充值订单
                if($orderType == 'recharge'){
                    $user->setInc('xylease_recharge',$order['money']);
                }else{
                    // 增加用户消费金额
                    $user->setInc('xylease_consume',$order->totalfee);
                }
            }
        }        
        
    }

    // 订单创建成功 
    public function xyleaseOrderCreateAfter(&$params){

        $orderData = $params['order'];
        $orderType = $params['type'];  // 订单类型
       
        // 租赁订单
        if ($orderType == 'order') {
            $order = OrderModel::where(['id'=>$orderData['id']])->find();
            if (!$order) return false;
            $orderItem = OrderItem::where(['order_id'=>$order['id']])->select();

            foreach($orderItem as $item){
                if($item['goodstype'] == 'package'){
                    $packageItem = PackageItem::where(['package_id'=>$item['goods_id']])->select();
                    foreach($packageItem as $pi){
                        $goodsSkuPrice = SkuPrice::where(['id'=>$pi['goods_sku_price_id'],'goods_id'=>$pi['goods_id']])->find();
                        //更新库存
                        StockService::update($goodsSkuPrice->id,-$item['buynum']*$pi['nums'],$order['id'],'ordercreate','user');
                    }
                }else{
                    $goodsSkuPrice = SkuPrice::where(['id'=>$item['goods_sku_price_id'],'goods_id'=>$item['goods_id']])->find();
                    //更新库存
                    StockService::update($goodsSkuPrice->id,-$item['buynum'],$order['id'],'ordercreate','user');
                }
            }
            
        }

        // 更新优惠券状态
        if(isset($order['user_coupon_id']) && $order['user_coupon_id'] > 0){
            $userCoupon  = UserCouponModel::get($order['user_coupon_id']);
            $userCoupon->status = 1;
            $userCoupon->useorderid = $order['id'];
            $userCoupon->useordertype = $orderType;
            $userCoupon->save();
        }
    }

    // 订单取消｜自动关闭
    public function xyleaseOrderCancelAfter(&$params){

        $orderData = $params['order'];
        $orderType = $params['type'];
        $operateType = $params['operate'];

        // 租赁订单
        if ($orderType == 'order') {
            $order = OrderModel::where(['id'=>$orderData['id']])->find();
            if (!$order) return false;
            $orderItem = OrderItem::where(['order_id'=>$order['id']])->select();

            foreach($orderItem as $item){
                if($item['goodstype'] == 'package'){
                    $packageItem = PackageItem::where(['package_id'=>$item['goods_id']])->select();
                    foreach($packageItem as $pi){
                        $goodsSkuPrice = SkuPrice::where(['id'=>$pi['goods_sku_price_id'],'goods_id'=>$pi['goods_id']])->find();
                        //更新库存
                        StockService::update($goodsSkuPrice->id,$item['buynum']*$pi['nums'],$order['id'],$operateType,'user');
                    }
                }else{
                    $goodsSkuPrice = SkuPrice::where(['id'=>$item['goods_sku_price_id'],'goods_id'=>$item['goods_id']])->find();
                    //更新库存
                    StockService::update($goodsSkuPrice->id,$item['buynum'],$order['id'],$operateType,'user');
                }
            }
        }

    }

    // 租赁订单归还
    public function xyleaseOrderBackAfter(&$params){

        $orderData = $params['order'];
        $orderType = $params['type'];
        $operateType = 'orderback';

        // 租赁订单
        if ($orderType == 'order') {
            $order = OrderModel::where(['id'=>$orderData['id']])->find();
            if (!$order) return false;
            $orderItem = OrderItem::where(['order_id'=>$order['id'],'goodstype'=>['in',['single','package']]])->select();

            foreach($orderItem as $item){
                if($item['goodstype'] == 'package'){
                    $packageItem = PackageItem::where(['package_id'=>$item['goods_id']])->select();
                    foreach($packageItem as $pi){
                        $goodsSkuPrice = SkuPrice::where(['id'=>$pi['goods_sku_price_id'],'goods_id'=>$pi['goods_id']])->find();
                        //更新库存
                        StockService::update($goodsSkuPrice->id,$item['buynum']*$pi['nums'],$order['id'],$operateType,'user');
                    }
                }else{
                    $goodsSkuPrice = SkuPrice::where(['id'=>$item['goods_sku_price_id'],'goods_id'=>$item['goods_id']])->find();
                    //更新库存
                    StockService::update($goodsSkuPrice->id,$item['buynum'],$order['id'],$operateType,'user');
                }
            }
            
        }
    }
    
}
