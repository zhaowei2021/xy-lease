<?php

namespace app\api\controller\xylease\recharge;
use app\common\controller\Api;

use app\api\model\xylease\recharge\Order as RechargeOrderModel;


/**
 * 充值订单接口
 */
class Order extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];


    /**
     * 创建
     */
	public function add()
    {
        $params = $this->request->post();
        $order = RechargeOrderModel::addOrder($params);
        $this->success('订单创建成功', $order);
    }

    /**
     * 详情
     */
    public function detail()
    {
        $params = $this->request->post();
        $this->success('充值订单详情', RechargeOrderModel::getDetail($params));
    }
   
	
}