<?php

namespace app\api\model\xylease\recharge;

use think\Model;
use app\api\model\xylease\user\User;
use addons\xylease\exception\Exception;
use app\api\model\xylease\recharge\Recharge as RechargeModel;


class Order extends Model
{

    // 表名
    protected $name = 'xylease_recharge_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'paytype_text',
        'paytime_text',
        'platform_text',
        'status_text'
    ];

    /**
     * 添加订单
     */
    public static function addOrder($params){

        extract($params);

        $recharge = RechargeModel::where(['id'=>$recharge_id,'status'=>'normal'])->find();
        if(empty($recharge)){
            new Exception('充值套餐已下架！');
        }
        $money = $recharge['facevalue'];
        $totalfee = $recharge['buyprice'];

        $user = User::info();

        $orderData = [];
        $orderData['ordersn'] = xyleaseCreateOrderNo();
        $orderData['user_id'] = $user->id;
        $orderData['money'] = $money;
        $orderData['totalfee'] = $totalfee;
        $orderData['paytype'] = $paytype;

        $order = self::create($orderData);

        return $order;
    }

    /**
     * 订单支付成功
     *
     * @param [type] $order
     * @param [type] $notify
     * @return void
     */
    public function paySuccess($order, $notify)
    {

        $order->status = 1;
        $order->paytime = time();
        $order->transaction_id = $notify['transaction_id'];
        $order->paymentjson = $notify['paymentjson'];
        $order->paytype = $notify['paytype'];
        $order->payfee = $notify['payfee'];
        $order->save();


        //更新用户余额
        User::money($order->money, $order->user_id, 'recharge', '',$order->id);

        //增加用户充值金额
        $user = User::get($order->user_id);
        $user->setInc('xylease_recharge',$order->totalfee);

        // 支付后监听
        $params = ['order'=>$order,'type'=>'recharge'];
        \think\Hook::listen('xylease_order_payed_after', $params);

        
        return $order;
    }

    // 订单详情
    public static function getDetail($params)
    {
        $user = User::info();
        extract($params);

        $order = self::where('user_id', $user->id);

        if (isset($ordersn)) {
            $order = $order->where('ordersn', $ordersn);
        }
        if (isset($id)) {
            $order = $order->where('id', $id);
        }

        $order = $order->find();

        if (!$order) {
            new Exception('订单不存在');
        }

        return $order;
    }

    
    public function getPayTypeList()
    {
        return ['wechat' => __('Wechat')];
    }

    public function getPlatformList()
    {
        return ['wxMiniProgram' => __('Platform wxminiprogram'), 'wxOfficialAccount' => __('Platform wxofficialaccount')];
    }

    public function getStatusList()
    {
        return ['-2' => __('Status -2'), '-1' => __('Status -1'), '0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getPayTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytype']) ? $data['paytype'] : '');
        $list = $this->getPayTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytime']) ? $data['paytime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPlatformTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['platform']) ? $data['platform'] : '');
        $list = $this->getPlatformList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setPaytimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
