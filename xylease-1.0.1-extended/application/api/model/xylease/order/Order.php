<?php

namespace app\api\model\xylease\order;

use think\Model;
use app\api\model\xylease\user\User;
use app\api\model\xylease\goods\Goods as GoodsModel;
use app\api\model\xylease\goods\SkuPrice;
use app\api\model\xylease\goods\GoodsItem;
use app\api\model\xylease\order\Item as OrderItem;
use app\api\model\xylease\store\Store as StoreModel;
use app\api\model\xylease\refund\Log as RefundLog;
use app\api\model\xylease\Cart;
use addons\xylease\exception\Exception;
use app\api\model\xylease\Config;
use addons\xylease\service\PayService;
use app\api\model\xylease\user\Coupon as UserCouponModel;
use think\Db;
use think\Queue;

class Order extends Model
{


    // 表名
    protected $name = 'xylease_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text',
        'paytype_text',
        'paytime_text',
        'platform_text',
        'status_text',
        'deliverytype_text',
        'starttime_text',
        'endtime_text',
        'paytype_text',
        'paytime_text',
        'platform_text',
        'status_text',
        'ext_arr'
    ];

    public function getTypeList()
    {
        return ['lease' => __('Type lease'),];
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? '');
        $valueArr = explode(',', $value);
        $list = $this->getTypeList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }


    public function getStarttimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['starttime']) ? $data['starttime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endtime']) ? $data['endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    protected function setTypeAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function setStarttimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getExtArrAttr($value, $data)
    {
        $ext = (isset($data['ext']) && $data['ext']) ? json_decode($data['ext'], true) : [];
        return $ext;
    }

    // 加载订单数据
    public static function getInitData($params,$init_type = '')
    {
        extract($params);

        // 发货方式
        $deliveryTypeList = self::getExpressType();
        if(count($deliveryTypeList) == 0){
            new Exception('系统未配置发货方式！');
        }

        // 门店信息
        $storeInfo = StoreModel::get(1);

        // 租赁配置
        $leaseConfig = Config::getValueByName('lease');

        // 租赁方式
        $leaseTypeList = $leaseConfig['leasetype'];
        if(count($leaseTypeList) == 0 && $type == "lease"){
            new Exception('系统未配置租赁方式！');
        }

        if($init_type == 'create'){
            if($consignee == ''){
                new Exception('请输入联系人姓名');
            }
            if($mobile == ''){
                new Exception('请输入联系人手机号');
            }
        }

        $totalAmount = 0; //租赁商品金额
        $totalDeposit = 0; //租赁商品押金
        $orderGoodsList = []; //租赁商品列表
        $totalNum = 0; //租赁商品件数
        $leaseTypeIndex  = 0;

        if($leasetype == ''){
            $leasetype = $leaseConfig['defaultlease'];
        }else{
            foreach($leaseTypeList as $key=>$value){
                if($value == $leasetype){
                    $leaseTypeIndex  = $key;
                }
            }
        }

        foreach($leaseTypeList as $key=>$lt){
            if($lt == 'hour'){
                $leaseTypeList[$key] = ['value'=>'hour','name'=>'小时租'];
            }
            if($lt == 'days'){
                $leaseTypeList[$key] = ['value'=>'days','name'=>'全天租'];
            }
            if($lt == 'night'){
                $leaseTypeList[$key] = ['value'=>'night','name'=>'过夜租'];
            }
        }

        $today = date("Y-m-d");//今天
        $tomorrow = date("Y-m-d",strtotime("+1 day"));//明天
        $leaseTime = '';//租用时间
        $pickupTime = '';//自提时间
        $hour = date("H") + 1;
        if($starttime == '' && $endtime == ''){
            if($leasetype == 'hour'){
                if($hour+$leaseConfig['starthour'] > 24){
                    $starttime = strtotime($tomorrow . ' ' . $leaseConfig['hourzt']);
                }else{
                    $starttime = strtotime($today.' '.$hour.':00');
                }
                $endtime = $starttime+intval($leaseConfig['starthour']) * 3600;
            }

            if($leasetype == 'days'){
                $starttime = strtotime($today.' 00:00');
                $endtime = strtotime($today.' 23:59');
            }

            if($leasetype == 'night'){
                $starttime = strtotime($today.' '.$leaseConfig['nightst']);
                $endtime = strtotime($tomorrow.' '.$leaseConfig['nightet']);
            }
        }

        if($leasetype == 'night' && $endtime == ''){
            $nextDayTimestamp = strtotime('+1 day', $starttime);
            $nextDay = date('Y-m-d', $nextDayTimestamp);
            $endtime = strtotime($nextDay.' '.$leaseConfig['nightet']);
        }

        if($leasetype == 'hour'){
            $leaseTime = date("m月d日 H:i",$starttime).'-'.date("H:i",$endtime);
            $pickupTime = date('Y年m月d日H:i',$starttime-$leaseConfig['hourst'] * 3600).'至'.date("H:i",$starttime);
        }

        if($leasetype == 'days'){
            $leaseTime = date("m月d日",$starttime).'-'.date("m月d日",$endtime);
            $pickupTime = date('Y年m月d日',$starttime).$leaseConfig['daysst'].'至23:59';
        }

        if($leasetype == 'night'){
            $leaseTime = date("m月d日H:i",$starttime).'-次日'.date("H:i",$endtime);
            $pickupTime = date('Y年m月d日H:i',$starttime).'至23:59';
        }

        // 检测并重新组装要租赁的商品列表,返回商品列表和总价
        list($orderGoodsList,$totalAmount,$totalDeposit,$totalNum,$leaseTimeNum) = self::checkLeaseGoods($goodslist,$leasetype,$starttime,$endtime);

        $couponFee = 0; //优惠券抵扣金额

        if($user_coupon_id > 0){
            $user = User::info();
            $userCoupon = UserCouponModel::where(['user_id'=>$user->id,'id'=>$user_coupon_id,'status'=>0])->find();
            if(!empty($userCoupon) && $totalAmount >= $userCoupon['atleast']){
                if($userCoupon['type'] == 'reward'){
                    $couponFee = $userCoupon['money'];
                }
                if($userCoupon['type'] == 'discount'){
                    $couponFee = bcmul($totalAmount,(1-$userCoupon['discount']/10),2) ;
                }
            }
        }

        $totalFee = bcsub($totalAmount, $couponFee, 2);
        $totalFee = bcadd($totalFee,$totalDeposit,2);
        $totalFee = $totalFee < 0 ? 0 : $totalFee;

        $totaldeposit = $totalDeposit;

        return [
            'from'                  => isset($from) ? $from : 'buynow',
            'type'                  => isset($type) ? $type : 'lease',
            'consignee'             => isset($consignee) ? $consignee : '',
            'mobile'                => isset($mobile) ? $mobile : '',
            'remark'                => isset($remark) ? $remark  : '',
            'leasetimenum'          => isset($leaseTimeNum) ? $leaseTimeNum  : 0,
            'starttime'             => $starttime,
            'endtime'               => $endtime,
            'leasetype'             => $leasetype,
            'leasetime'             => isset($leaseTime) ? $leaseTime : '', //租赁时间
            'pickuptime'            => isset($pickupTime) ? $pickupTime : '', //自提时间
            'deliverytype'          => isset($deliverytype) ? $deliverytype : 'pickup',
            'deliverytypelist'      => $deliveryTypeList, //发货方式列表
            'ordergoodslist'        => $orderGoodsList, //租赁商品列表
            'totalamount'           => $totalAmount, //订单总价
            'totaldeposit'          => $totaldeposit, //订单总押金
            'couponfee'             => $couponFee, //优惠券抵扣金额
            'totalfee'              => $totalFee, //支付金额
            'totalnum'              => $totalNum, //商品总件数
            'storeinfo'             => $storeInfo,//门店信息
            'leasetypelist'         => $leaseTypeList, // 租赁类型
            'leasetypeindex'        => $leaseTypeIndex,
            'leaseconfig'           => $leaseConfig,
            'user_coupon_id'        => $user_coupon_id
        ];
    }

    /**
     * 创建订单
     */
    public static function addOrder($params)
    {
        $user = User::info();

        // 订单数据
        $params = self::getInitData($params, "create");

        $close_time = 10;
        $order = Db::transaction(function () use (
            $user,
            $params,
            $close_time
        ) {
            extract($params);

            $orderData = [];
            $orderData['ordersn']       = xyleaseCreateOrderNo();
            $orderData['user_id']       = $user->id;
            $orderData['from']          = $from;
            $orderData['type']          = $type;
            $orderData['leasetype']     = $leasetype;
            $orderData['starttime']     = $starttime;
            $orderData['endtime']       = $endtime;
            $orderData['leasetime']     = $leasetime;
            $orderData['leasetimenum']  = $leasetimenum;
            $orderData['pickuptime']    = $pickuptime;
            $orderData['totalnum'] = $totalnum;
            $orderData['totalamount'] = $totalamount;
            $orderData['totaldeposit'] = $totaldeposit;
            $orderData['couponfee']  = $couponfee;
            $orderData['totalfee']  = $totalfee;
            $orderData['remark']  = $remark;
            $orderData['consignee'] = $consignee;
            $orderData['mobile'] = $mobile;
            $orderData['user_coupon_id'] = $user_coupon_id;
            $orderData['platform'] = request()->header('platform');
            $orderData['ext'] = json_encode(['expired_time' => time() + ($close_time * 60)]);
            $order = new Order();
            $order->allowField(true)->save($orderData);

            // 添加订单选项
            foreach ($ordergoodslist as $leaseinfo) {

                $detail = $leaseinfo['detail'];
                $current_sku_price = $detail['current_sku_price'];
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->goods_id = $leaseinfo['goods_id'];
                $orderItem->goods_sku_price_id = $leaseinfo['sku_price_id'];
                $orderItem->goodsskutext = $current_sku_price['goodsskutext'];
                $orderItem->goodstype = $detail['type'];
                $orderItem->goodsname = $detail['name'];
                $orderItem->goodsimage = empty($current_sku_price['image']) ? $detail->image : $current_sku_price['image'];
                $orderItem->price = $current_sku_price['showprice'];
                $orderItem->deposit = $detail->current_sku_price->deposit;
                $orderItem->buynum = $leaseinfo['buynum'] ?? 1;
                $orderItem->save();
            }


            // 删除购物车
            if ($from == 'cart') {

                foreach ($ordergoodslist as $delCart) {
                    Cart::where([
                        'user_id' => $user->id,
                        'goods_id' => $delCart['goods_id'],
                        'sku_price_id' => $delCart['sku_price_id'],
                    ])->delete();
                }

            }

            return $order;
        });
        sleep(2);

        // // 订单创建后监听
        // $params = ['order'=>$order,'type'=>'order'];
        // \think\Hook::listen('xylease_order_create_after', $params);

        // // 订单关闭队列
        // Queue::later(($close_time * 60), '\addons\xylease\service\QueueOrder@autoClose', ['order' => $order,'type'=>'order'], 'XYlease');

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

        //更新商品租量
        $orderItem = OrderItem::where(['order_id'=>$order['id']])->select();
        foreach($orderItem as $oi){
            $goods = (new GoodsModel)->where(['id'=>$oi['goods_id']])->find();
            $goods->setInc('sales',$oi['buynum']);
        }

        // 支付后监听
        $params = ['order'=>$order,'type'=>'order'];
        \think\Hook::listen('xylease_order_payed_after', $params);

        return $order;
    }


    /**
     * 获取发货方式
     */

    public static function getExpressType()
    {
        $config = Config::getValueByName('lease')['deliverytype'];
        $expressType = [];
        if(in_array('pickup',$config)){
            $expressType[] = ['name'=>'门店自提','value'=>'pickup'];
        }
        return $expressType;
    }

    // 取消订单
    public static function cancelOrder($params)
    {
        $user = User::info();
        extract($params);
        $order = self::where(['user_id'=>$user->id,'id'=>$id,'status'=>0])->find($id);
        if (!$order) {
            new Exception('订单不存在或已取消');
        }
        $order->status = -1;        // 取消订单
        $order->ext = json_encode($order->setExt($order, ['cancel_time' => time()]));      // 取消时间
        $order->save();

        // 取消后监听
        $params = ['order'=>$order,'type'=>'order','operate' => 'ordercancel'];
        \think\Hook::listen('xylease_order_cancel_after', $params);

        return $order;
    }


    // 下单前检测购买商品状态、库存
    public static function checkBuyGoods($goodslist)
    {

        $orderBuyGoodsList = [];
        $goodsBuyAmount = 0;
        $totalBuyNum = 0;
        foreach ($goodslist as $buyinfo) {
            // 最少购买一件
            $buyinfo['buynum'] = intval($buyinfo['buynum']) < 1 ? 1 : intval($buyinfo['buynum']);

            $sku_price_id = $buyinfo['sku_price_id'];

            $detail = GoodsModel::getDetail($buyinfo['goods_id']);

            $sku_prices = $detail['sku_price'];
            foreach ($sku_prices as $k => $sku_price) {
                if ($sku_price['id'] == $sku_price_id) {
                    $detail->current_sku_price = $sku_price;
                    break;
                }
            }

            if (!$detail || $detail->status == 'down') {
                new Exception('商品不存在或已下架');
            }

            if (!isset($detail->current_sku_price) || !$detail->current_sku_price) {
                new Exception('商品规格不存在');
            }

            //商品详情
            $buyinfo['detail'] = $detail;
            $orderBuyGoodsList[] = $buyinfo;

            // 当前库存，小于要购买的数量
            if ($detail->current_sku_price['stock'] < $buyinfo['buynum']) {
                new Exception($detail['title'].'商品库存不足');
            }

            // 当前商品总价
            $currentAmount = bcmul($detail->current_sku_price->price, $buyinfo['buynum'], 2);
            $goodsBuyAmount = bcadd($goodsBuyAmount, $currentAmount, 2);

            // 商品件数
            $totalBuyNum = $totalBuyNum + $buyinfo['buynum'];
        }

        if (!count($orderBuyGoodsList)) {
            new Exception('请选择要购买的商品');
        }

        return [$orderBuyGoodsList,$goodsBuyAmount,$totalBuyNum];
    }

    // 下单前检测租赁商品状态、库存
    public static function checkLeaseGoods($leaseGoodsList,$leasetype,$starttime,$endtime)
    {

        $orderGoodsList = [];
        $totalAmount = 0;
        $totalDeposit = 0;
        $totalNum = 0;
        $leaseTimeNum = 0;
        foreach ($leaseGoodsList as $leaseinfo) {
            // 最少租赁一件
            $leaseinfo['buynum'] = intval($leaseinfo['buynum']) < 1 ? 1 : intval($leaseinfo['buynum']);

            $sku_price_id = $leaseinfo['sku_price_id'];

            $detail = GoodsModel::getDetail($leaseinfo['goods_id']);

            $sku_prices = $detail['sku_price'];
            foreach ($sku_prices as $k => $sku_price) {
                if ($sku_price['id'] == $sku_price_id) {
                    $detail->current_sku_price = $sku_price;
                    break;
                }
            }

            if (!$detail || $detail->status == 'down') {
                new Exception('商品不存在或已下架');
            }

            if (!isset($detail->current_sku_price) || !$detail->current_sku_price) {
                new Exception('商品规格不存在');
            }

            // 当前库存，小于要租赁的数量
            if ($detail->current_sku_price['type'] !='package' && $detail->current_sku_price['stock'] < $leaseinfo['buynum']) {
                new Exception($detail['name'].'库存不足');
            }

            // 检测租赁套餐库存
            if($detail->current_sku_price['type'] =='package'){
                $packageItem = GoodsItem::where(['package_id'=>$detail->current_sku_price['goods_id']])->select();
                foreach($packageItem as $pi){
                    $packageSkuPrice = SkuPrice::where(['id'=>$pi['goods_sku_price_id']])->find();
                    if(empty($packageSkuPrice) || $packageSkuPrice['stock'] < $leaseinfo['buynum'] * $pi['nums']){
                        new Exception($detail['name'].'库存不足');
                    }
                }
            }

            // 当前商品租金
            $currentAmount = 0;
            $currentDeposit = 0;
            if($leasetype == 'hour'){
                $hour = ceil(($endtime - $starttime)/3600);
                $leaseTimeNum = $hour;
                $currentAmount = bcmul($detail->current_sku_price->hourprice*$hour, $leaseinfo['buynum'], 2);
                $currentDeposit = bcmul($detail->current_sku_price->deposit, $leaseinfo['buynum'], 2);
                $detail->current_sku_price['showprice'] = $detail->current_sku_price->hourprice;
                $detail->current_sku_price['showtypetext'] = '小时';
                $detail->current_sku_price['shownum'] = $hour;
            }
            if($leasetype == 'days'){
                $days = ceil(($endtime - $starttime)/(24*3600));
                $leaseTimeNum = $days;
                $currentAmount = bcmul($detail->current_sku_price->daysprice*$days, $leaseinfo['buynum'], 2);
                $currentDeposit = bcmul($detail->current_sku_price->deposit, $leaseinfo['buynum'], 2);
                $detail->current_sku_price['showprice'] = $detail->current_sku_price->daysprice;
                $detail->current_sku_price['showtypetext'] = '天';
                $detail->current_sku_price['shownum'] = $days;
            }
            if($leasetype == 'night'){
                $leaseTimeNum = 1;
                $currentAmount = bcmul($detail->current_sku_price->nightprice, $leaseinfo['buynum'], 2);
                $currentDeposit = bcmul($detail->current_sku_price->deposit, $leaseinfo['buynum'], 2);
                $detail->current_sku_price['showprice'] = $detail->current_sku_price->nightprice;
                $detail->current_sku_price['showtypetext'] = '夜';
                $detail->current_sku_price['shownum'] = 1;
            }

            $detail->current_sku_price['showtype'] = $leasetype;

            $totalAmount = bcadd($totalAmount, $currentAmount, 2);
            $totalDeposit = bcadd($totalDeposit, $currentDeposit, 2);
            $totalNum = $totalNum + $leaseinfo['buynum'];

            $leaseinfo['detail'] = $detail;
            $orderGoodsList[] = $leaseinfo;

        }

        if (!count($orderGoodsList)) {
            new Exception('请选择要租赁的商品');
        }

        return [$orderGoodsList,$totalAmount,$totalDeposit,$totalNum,$leaseTimeNum];
    }

    // 订单详情
    public static function getDetail($params)
    {
        $user = User::info();
        extract($params);

        $order = self::with(['item'])->where('user_id', $user->id);

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

    public static function getLists($params)
    {
        extract($params);
        if(isset($user_id) && $user_id > 0){
            $where['user_id'] = $user_id;
        }else if(isset($staff_id) && $staff_id > 0){
            $where['user_id'] = ['>',0];
        }else{
            $user = User::info();
            $where['user_id'] = $user->id;
        }

        if(!is_array($status)){
            if($status != 'all'){
                $where['status'] = $status;
            }
        }else{
            $where['status'] = ['in',$status];
        }
        if(isset($paginate) && $paginate == false){
            $list = self::with(['item'])->where($where)->order('id desc')->select();
        }else{
            $list = self::with(['item'])->where($where)->order('id desc')->paginate();
        }

        return $list;
    }

    // 租赁取货
    public static function leasePickup($params){

        $order = self::where(['id'=>$params['id'],'status'=>1])->find();
        if (!$order) {
            new Exception('订单不存在');
        }

        if($order['status'] != 1){
            new Exception('已取货');
        }

        $result = Db::transaction(function () use ($params,$order) {

            extract($params);
            $order->status = 2;
            $order->ext = json_encode($order->setExt($order, ['leasepickup_operator_time' => time(),'leasepickup_operator_staff_id'=>$staff_id]));
            $order->save();
            return $order;

        });

        return $result;
    }

    // 租赁归还
    public static function leaseComplete($params){

        $order = self::where(['id'=>$params['id'],'status'=>2])->find();
        if (!$order) {
            new Exception('订单不存在');
        }

        if($order['status'] != 2){
            new Exception('已归还');
        }

        $result = Db::transaction(function () use ($params,$order) {

            extract($params);
            $order->status = 3;
            $order->ext = json_encode($order->setExt($order, ['leasecomplete_operator_time' => time(),'leasecomplete_operator_staff_id'=>$staff_id]));
            $order->save();

            //退押金
            if($deposit > 0){
                if($deposit > $order['totaldeposit']){
                    new Exception('退还押金不能大于支付押金');
                }

                // 生成退款单
                $refundLog = new RefundLog();
                $refundLog->ordersn = $order->ordersn;
                $refundLog->refundsn = xyleaseCreateOrderNo();
                $refundLog->order_id = $order->id;
                $refundLog->payfee = $order->totaldeposit;
                $refundLog->ordertype = 'order';
                $refundLog->refundfee = $deposit;
                $refundLog->paytype = $order->paytype;
                $refundLog->save();

                //余额支付退款
                if($order['paytype'] == 'balance'){
                    $user = User::get($order['user_id']);
                    User::money($deposit,$user->id,'return_order_deposit','',$order['id']);
                    self::refundFinish($refundLog);
                }else if($order['paytype'] == 'wechat'){ //微信支付退款

                    $totalFee = $order->payfee * 100;
                    $returnFee = $deposit * 100;
                    $order_data = [
                        'out_trade_no' => $order->ordersn,
                        'out_refund_no' => $refundLog->refundsn,
                        'total_fee' => $totalFee,
                        'refund_fee' => $returnFee,
                        'refund_desc' => '退还押金',
                    ];

                    $notify_url = request()->domain() . '/api/xylease/pay/notifyr/payment/' . $order->paytype . '/platform/' . $order->platform.'/ordertype/order';
                    $pay = new PayService($order->paytype, $order->platform, $notify_url);
                    $pay->refund($order_data);
                }

            }

            // 归还后监听
            $params = ['order'=>$order,'type'=>'goods'];
            \think\Hook::listen('xylease_order_back_after', $params);

            return $order;

        });

        return $result;
    }

    //退款完成
    public static function refundFinish($refundLog)
    {
        $refundLog->status = 1;
        $refundLog->save();
    }

    public function setExt($order, $field, $origin = [])
    {
        $newExt = array_merge($origin, $field);

        $orderExt = $order['ext_arr'];

        return array_merge($orderExt, $newExt);
    }

    public function getPayTypeList()
    {
        return ['wechat' => __('Wechat')];
    }

    public function getPlatformList()
    {
        return ['wxMiniProgram' => __('Wxminiprogram')];
    }

    public function getStatusList()
    {
        return ['-2' => __('交易关闭'), '-1' => __('已取消'), '0' => __('待付款'), '1' => __('待取货'),'2' => __('待归还'),'3' => __('已完成')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getDeliveryTypeList()
    {
        return ['pickup' => __('门店自提')];
    }

    public function getDeliveryTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['deliverytype']) ? $data['deliverytype'] : '');
        $list = $this->getDeliveryTypeList();
        return isset($list[$value]) ? $list[$value] : '';
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

    protected function setPaytimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function item()
    {
        return $this->hasMany(Item::class, 'order_id', 'id');
    }


}
