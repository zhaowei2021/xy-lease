<?php

namespace addons\xylease\service;

use app\api\model\xylease\order\Order as OrderModel;
use think\queue\Job;
use think\Log;
use think\Db;


/**
 * 订单自动操作
 */
class QueueOrder
{

    /**
     * 订单自动关闭
     */
    public static function autoClose(Job $job, $params){

        try {
            $orderData = $params['order'];
            $orderType = $params['type']; // 订单类型

            // 重新查询订单
            $order = null;

            // z订单
            if ($orderType == 'order') {
                $order = OrderModel::where(['id'=>$orderData['id']])->find();
            }

            if ($order && $order['status'] == 0) {
                Db::transaction(function () use ($order) {
                    $order->status = -2;
                    $order->ext = json_encode($order->setExt($order, ['close_time' => time()]));
                    $order->save();
                });

                // 关闭后监听
                $params['operate'] = 'orderclose';
                \think\Hook::listen('xylease_order_cancel_after', $params);
            }

            $job->delete();
        } catch (\Exception $e) {
            Log::write('订单自动关闭队列执行失败，错误信息：' . $e->getMessage());
        }
    }
    
}