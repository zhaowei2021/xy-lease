<?php

namespace app\api\controller\xylease;
use app\common\controller\Api;
use think\Db;
use think\Log;
use app\api\model\xylease\user\User;
use addons\xylease\service\PayService;
use app\api\model\xylease\recharge\Order as RechargeOrderModel;
use app\api\model\xylease\order\Order as OrderModel;
use app\api\model\xylease\refund\Log as RefundLog;
use app\api\model\xylease\Third;

class Pay extends Api
{

    protected $noNeedLogin = ['prepay', 'notifyx'];
    protected $noNeedRight = ['*'];


    /**
     * 拉起支付
     */
    public function prepay()
    {
        xyleaseCheckEnv('yansongda');

        $user = User::info();
        $ordersn = $this->request->post('ordersn');
        $payment = $this->request->post('payment');
        $ordertype = $this->request->post('ordertype');
        $platform = request()->header('platform');

        list($order, $title) = $this->getOrderInstance($ordertype);
        $order = $order->where(['user_id'=>$user->id,'ordersn'=>$ordersn])->find();
        
        if (!$order) {
            $this->error("订单不存在");
        }

        if (in_array($order->status, [-1])) {
            $this->error("订单已失效");
        }

        if ($payment == 'balance') {
            // 余额支付
            $this->balancePay($order,$ordertype);
        }

        $order_data = [
            'order_id' => $order->id,
            'out_trade_no' => $order->ordersn,
            'total_fee' => $order->totalfee,
        ];


        if ($payment == 'wechat') {
            if (in_array($platform, ['wxOfficialAccount', 'wxMiniProgram'])) {
                if (isset($openid) && $openid) {
                    $order_data['openid'] = $openid;
                } else {
                    $third = Third::where([
                        'user_id' => $order->user_id,
                        'platform' => $platform
                    ])->find();
        
                    $order_data['openid'] = $third ? $third->openid : '';
                }
            }
            $order_data['body'] = $title;
        }
        
        try {
            $notify_url = $this->request->root(true) . '/api/xylease/pay/notifyx/payment/' . $payment . '/platform/' . $platform .'/ordertype/'.$ordertype;
            $pay = new PayService($payment, $platform, $notify_url);
            $result = $pay->create($order_data);
        } catch (\Exception $e) {
            $this->error("支付配置错误：" . $e->getMessage());
        }

        return $this->success('获取预付款成功', [
            'pay_data' => $result,
        ]);
    }

    /**
     * 支付成功回调
     */
    public function notifyx()
    {

        $payment = $this->request->param('payment');
        $platform = $this->request->param('platform');
        $ordertype = $this->request->param('ordertype');

        $pay = new PayService($payment, $platform);

        $result = $pay->notify(function ($data, $pay = null) use ($payment,$ordertype) {
            
            try {
                $out_trade_no = $data['out_trade_no'];

                list($order, $title) = $this->getOrderInstance($ordertype);
                
                
                if ($payment == 'wechat' && ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS')) {
                    // 微信交易未成功，返回 false，让微信再次通知
                    return false;
                }

                // 支付成功流程
                $payfee = $data['total_fee'] / 100;

                $order = $order->where('ordersn', $out_trade_no)->find();

                if (!$order || $order->status > 0) {
                    // 订单不存在，或者订单已支付
                    return $pay->success()->send();
                }

                Db::transaction(function () use ($order, $data, $payment, $payfee) {
                    $notify = [
                        'ordersn' => $data['out_trade_no'],
                        'transaction_id' => $payment == 'alipay' ? $data['trade_no'] : $data['transaction_id'],
                        'notifytime' => date('Y-m-d H:i:s', strtotime($data['time_end'] ?? $data['notify_time'])),
                        'paymentjson' => json_encode($data),
                        'payfee' => $payfee,
                        'paytype' => $payment
                    ];

                    $order->paySuccess($order, $notify);

                });

                return $pay->success()->send();
            } catch (\Exception $e) {
                Log::write('notifyx-error:' . json_encode($e->getMessage()));
            }
        });

        return $result;
    }

    // 余额支付
    public function balancePay ($order,$ordertype) {

        $order = Db::transaction(function () use ($order,$ordertype) {
            if (!$order) {
                $this->error("订单已支付");
            }
            $totalfee = $order->totalfee;

            $user = User::info();

            User::money(-$totalfee, $user->id, 'pay_'.$ordertype, '',$order->id);

            $notify = [
                'ordersn' => $order['ordersn'],
                'transaction_id' => '',
                'notify_time' => date('Y-m-d H:i:s'),
                'user_id' => $user->id,
                'payfee' => $order->totalfee,
                'paytype' => 'balance'
            ];
            $notify['paymentjson'] = json_encode($notify);
            $order->paySuccess($order, $notify);

            return $order;
        });

        $this->success('支付成功', $order);
    }

    /**
     * 退款成功回调
     */
    public function notifyr()
    {


        $payment = $this->request->param('payment');
        $platform = $this->request->param('platform');
        $ordertype = $this->request->param('ordertype');

        $pay = new PayService($payment, $platform);

        //Log::write('退款成功回调1','log',true);

        $result = $pay->notifyRefund(function ($data, $pay = null) use ($payment, $platform,$ordertype) {
            Log::write('退款成功回调:' . $data);
            try {

                $out_trade_no = $data['out_trade_no'];
                $out_refund_no = $data['out_refund_no'];
                list($order, $title) = $this->getOrderInstance($ordertype);
                $order = $order->where('ordersn', $out_trade_no)->find();
                $refundLog = RefundLog::where('refund_sn', $out_refund_no)->find();

                $order->refundFinish($order,$refundLog);

                return $pay->success()->send();

            } catch (\Exception $e) {
                Log::write('notifyreturn-error:' . json_encode($e->getMessage()));
                return false;
            }
        });

        return $result;
    }


    /**
     * 根据订单类型获取订单实例
     */
    private function getOrderInstance($ordertype) 
    {
        $order = null;
        $title = '订单支付';

        // 租赁订单
        if($ordertype == 'order'){
            $order = new OrderModel();
            $title = '租赁'.$title;
        }
        
        // 充值订单
        if($ordertype == 'recharge'){
            $order = new RechargeOrderModel();
            $title = '充值'.$title;
        }

        return [$order,$title];
    }
}
