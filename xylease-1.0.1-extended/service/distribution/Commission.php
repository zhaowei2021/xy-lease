<?php

namespace addons\xylease\service\distribution;

use app\api\model\xylease\Config;
use addons\xylease\service\distribution\Distribution;
use app\api\model\xylease\distribution\Order as DistributionOrderModel;
use app\api\model\xylease\order\Item as OrderItemModel;
use app\api\model\xylease\goods\Goods as GoodsModel;
use app\api\model\xylease\goods\SkuPrice as SkuPriceModel;

/**
 * 分佣业务
 */
class Commission
{

    public $config;    // 分销配置
    public $order;       // 订单
    public $orderType; // order = 租赁订单
    public $buyer;      // 购买人
    public $oneDistribution; //一级分销商
    public $oneDistributionLevel;//一级分销商等级
    public $twoDistribution; //二级分销商
    public $twoDistributionLevel; //二级分销商等级
    public $commissionLevel = 0;    // 分销层级
    public $selfBuy;    // 是否内购

    
    /**
     * 构造函数
     */
    public function __construct($order,$orderType)
    {
        $this->buyer = new Distribution($order['user_id']);
        $this->order = $order;
        $this->orderType = $orderType;
        $this->config = Config::getValueByName('distribution');
    }


    /**
     * 检测、设置分销数据
     */
    public function checkAndSetCommission()
    {

        // 分销中心已关闭
        if ($this->config['open'] == 0) {
            return false;
        }

        // 未找到订单或购买人
        if (!$this->order || !$this->buyer->user) {
            return false;
        }

        // 获取系统设置分销层级
        $this->commissionLevel = $this->config['level'];
       
        
        // 未找到上级分销商
        if (!$this->buyer->getParentDistributionId()) {
            return false;
        }

        // 一级分销商
        $this->oneDistribution = new Distribution($this->buyer->getParentDistributionId());
        $this->oneDistributionLevel = $this->oneDistribution->getDistributionLevelInfo();
        

        // 二级分销商
        if($this->commissionLevel == 2 && $this->oneDistribution->getParentDistributionId()){
            $this->twoDistribution = new Distribution($this->oneDistribution->getParentDistributionId());
            $this->twoDistributionLevel = $this->twoDistribution->getDistributionLevelInfo();
        }

        return true;
    }

    
    /**
     * 添加分销订单
     * 
     */
    public function addDistributionOrder()
    {
        $distributionOrder = DistributionOrderModel::where(['id'=>$this->order['id'],'ordertype'=>$this->orderType])->find();

        // 已添加过分销订单
        if ($distributionOrder) {
            return $distributionOrder;
        }


        // 获取订单佣金信息
        $orderCommission = $this->caculateCommission();

        $distributionOrderParams = [
            'service_order_id' => $this->order['id'],
            'buyer_id' => $this->order['user_id'],
            'ordersn' => xyleaseCreateOrderNo(),
            'ordertype' => $this->orderType,
            'totalfee' => $this->order['totalfee'],
            'one_distribution_id' => $this->oneDistribution->user->id,
            'two_distribution_id' => $this->twoDistribution ? $this->twoDistribution->user->id : 0,
            'one_commission' => $orderCommission['one_commission'],
            'two_commission' => $orderCommission['two_commission'],
        ];

        $distributionOrder = DistributionOrderModel::create($distributionOrderParams);

        return $distributionOrder;
        
    }

    /**
     * 计算订单分销佣金
     */
    public function caculateCommission()
    {

        // 租赁订单分销佣金
        if($this->orderType == 'order'){
            return $this->calculateOrderCommission();
        }
       

        return [
            'one_commission' => 0.00,
            'two_commission' => 0.00,
        ];

    }


    /**
     * 计算课程包订单实际佣金
     */
    private function calculateOrderCommission()
    {
        $oneCommission = 0.00;
        $twoCommission = 0.00;
        $orderItem = OrderItemModel::where(['order_id'=>$this->order['id']])->select();
        foreach($orderItem as $item){

            $goods = GoodsModel::get($item['goods_id']);
            if($goods['isdis'] == 1){
                
                $oneCommissionRate = $this->oneDistributionLevel ? $this->oneDistributionLevel['commission_one'] :0; //一级分销比例
                $twoCommissionRate = $this->twoDistributionLevel ? $this->twoDistributionLevel['commission_two'] : 0; //二级分销比例
    
                
                if($goods['disrule'] == 1){
    
                    //单独设置规则
                    $skuPrice = SkuPriceModel::get($item['goods_sku_price_id']);
                    $commissionRule = json_decode($skuPrice['commissionrule'],true);
                 
                    foreach($commissionRule as $rule){

                        if($this->oneDistributionLevel && $this->oneDistributionLevel->id == $rule['level_id']){
                            $oneCommissionRate = $rule['commission_one'];
                        }
                        if($this->twoDistributionLevel && $this->twoDistributionLevel->id == $rule['level_id']){
                            $twoCommissionRate = $rule['commission_two'];
                        }
                    }
    
                }
                if($this->commissionLevel >= 1 && $this->oneDistribution){
                    $oneCommission += $item['price'] * $item['buynum'] * $oneCommissionRate * 0.01;
                }
        
                if($this->commissionLevel >= 2 && $this->twoDistribution){
                    $twoCommission += $item['price'] * $item['buynum'] * $twoCommissionRate * 0.01;
                }
            }
        }

        return [
            'one_commission' => $oneCommission,
            'two_commission' => $twoCommission,
        ];
    }

    

    /**
     * 分销订单派发佣金
     * 
     * $event payed = 支付后
     */
    public function grantDistributionOrder($event, $distributionOrder)
    {
        if (is_numeric($distributionOrder)) {
            $distributionOrder = DistributionOrderModel::get($distributionOrder);
        }

        // 未找到分销订单
        if (!$distributionOrder) {
            return false;
        }

        // 已经操作过了
        if ($distributionOrder['status'] !== 0) {
            return false;
        }

        $commissionEvent = $distributionOrder['commission_event'];

        // 不满足分佣事件
        if ($commissionEvent !== $event && $event !== 'admin') {
            return false;
        }

        // 更新分销订单结算状态
        $distributionOrder->status = 1;
        $distributionOrder->settle_time = time();
        $distributionOrder->save();

        // 添加分销商收益
        $oneCommission = $distributionOrder['one_commission'];
        $twoCommission = $distributionOrder['two_commission'];
        $oneDistributionId = $distributionOrder['one_distribution_id'];
        $twoDistributionId = $distributionOrder['two_distribution_id'];
        if($oneDistributionId > 0 && $oneCommission > 0){
            $this->oneDistribution->commission($oneCommission,'order',$distributionOrder['id']);
        }

        if($twoDistributionId > 0 && $twoCommission > 0){
            $this->twoDistribution->commission($twoCommission,'order',$distributionOrder['id']);
        }

        return $oneCommission + $twoCommission;
    }
}
