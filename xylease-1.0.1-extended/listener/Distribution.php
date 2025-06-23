<?php

namespace addons\xylease\listener;

use addons\xylease\service\distribution\Distribution as DistributionService;
use addons\xylease\service\distribution\Commission;
use app\api\model\xylease\order\Order as OrderModel;

/**
 * 分销钩子处理
 */
class Distribution
{

    // 订单支付成功 
    public function xyleaseOrderPayedAfter(&$params){

        $orderData = $params['order'];
        $orderType = $params['type'];

        // 重新查询订单
        $order = null;

        // 租赁订单
        if ($orderType == 'order') {
            $order = OrderModel::where(['id'=>$orderData['id']])->find();
        }

        if (!$order) return false;

        $distributon = new DistributionService($order['user_id']);

        if (!$distributon->user)  return false;

        // 绑定关系
        $distributon->bindUserRelation('pay');
        

        // 记录、处理分佣升级
        $commission = new Commission($order,$orderType);
        // 检查能否分销
        if ($commission->checkAndSetCommission()) {
            // 添加分销订单
            $distributionOrder = $commission->addDistributionOrder();
            // 支付后分佣
            $commission->grantDistributionOrder('payed', $distributionOrder->id);

        }
    }

    /**
     * 用户通过分享进入后,绑定用户上级
     */
    public function xyleaseShareAfter($shareLog){
        if ($shareLog) {
            $user_id = intval($shareLog->user_id); // 受邀用户
            $share_id = intval($shareLog->share_id); // 邀请人

            $distribution = new DistributionService($user_id);
            $distribution->bindUserRelation('share', $share_id); //绑定关系
        }
    }

    /**
     * 用户注册账号之后，添加分销账号
     */
    public function xyleaseRegisterAfter($user){
        if($user){
            new DistributionService($user->id);
        }
    }
    
}
