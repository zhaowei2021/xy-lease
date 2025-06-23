<?php

namespace addons\xylease\service;

use Yansongda\Pay\Pay;
use app\api\model\xylease\Config;

class PayService
{
    protected $config;
    protected $platform;
    protected $payment;
    protected $notify_url;
    protected $type;
    public $method;


    public function __construct($payment, $platform = '', $notify_url = '', $type = 'pay')
    {
        $this->platform = $platform;
        $this->payment = $payment;
        $this->notify_url = $notify_url;
        $this->type = $type;

        $this->setPaymentConfig();
    }

    private function setPaymentConfig()
    {
        $paymentConfig = json_decode(Config::get(['name' => $this->payment])->value, true);

        $this->config = $paymentConfig;
        $this->config['notify_url'] = $this->notify_url;

        $this->config['http'] = [
            'timeout' => 10,
            'connect_timeout' => 10,
        ];

        if ($this->payment === 'wechat') {
            $this->setWechatAppId();
        }

        $this->setCert();
    }

    private function setWechatAppId()
    {
        switch ($this->platform) {
            case 'wxMiniProgram':
                $platformConfig = json_decode(Config::get(['name' => $this->platform])->value, true);
                $this->config['miniapp_id'] = $platformConfig['app_id'];
                $this->config['app_id'] = $platformConfig['app_id'];
                break;
            case 'wxOfficialAccount':
                $platformConfig = json_decode(Config::get(['name' => $this->platform])->value, true);
                $this->config['app_id'] = $platformConfig['app_id'];
                break;
        }
    }


    private function setCert()
    {
        // 微信支付证书
        if ($this->payment == 'wechat') {
            $this->config['cert_client'] = ROOT_PATH . 'public' . $this->config['cert_client'];
            $this->config['cert_key'] = ROOT_PATH . 'public' . $this->config['cert_key'];
        }
    }


    private function setPaymentMethod()
    {
        $method = [
            'wechat' => [
                'wxMiniProgram' => 'miniapp', //小程序支付
                'wxOfficialAccount' => 'mp',   //公众号支付
            ],
        ];
        
        $this->method = $method[$this->payment][$this->platform];
    }

    public function create($order)
    {
        $this->setPaymentMethod();

        $method = $this->method;

        switch ($this->payment) {
            case 'wechat':
                if (isset($this->config['mode']) && $this->config['mode'] === 'service') {
                    $order['sub_openid'] = $order['openid'] ?? '';
                    unset($order['openid']);
                }
                $order['total_fee'] = $order['total_fee'] * 100;
                unset($order['order_id']);
                $pay = Pay::wechat($this->config)->$method($order);
                break;
            
        }

        return $pay;
    }

    public function refund($order_data)
    {
        $pay = $this->getPay();

        $order_data['type'] = $this->platform == 'wxMiniProgram' ? 'miniapp' : '';

        $result = $pay->refund($order_data);

        \think\Log::write('refund-result' . json_encode($result));

        if ($this->payment == 'wechat') {
            // 微信通知回调 pay->notifyr
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                return $result;
            } else {
                throw new \Exception($result['return_msg']);
            }
        }
    }

    
    public function notify($callback)
    {
        $pay = $this->getPay();

        try {
            $data = $pay->verify(); 

            $result = $callback($data, $pay);

        } catch (\Exception $e) {
            \think\Log::error('notify-error:' . $e->getMessage());
        }

        return $result;
    }

    private function getPay()
    {
        switch ($this->payment) {
            case 'wechat':
                $pay = Pay::wechat($this->config);
                break;
        }

        return $pay;
    }

    public function notifyRefund($callback)
    {
        $pay = $this->getPay();

        try {
            $data = $pay->verify(null, true);
            \think\Log::write('notifyr-result:' . json_encode($data));
            $result = $callback($data, $pay);
        } catch (\Exception $e) {
            \think\Log::write('notifyr-verify-error:' . $e->getMessage()); // $e->getMessage();
            return false;
        }

        return $result;
    }
}
