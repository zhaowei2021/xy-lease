<?php

namespace app\api\controller\xylease;
use app\common\controller\Api;

use app\api\model\xylease\order\Order as OrderModel;


/**
 * 租赁订单接口
 */
class Order extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    

    /**
	 * 列表
	 */
	public function lists()
    {
    	$params = $this->request->post();
        $data = OrderModel::getLists($params);
        $this->success('订单列表', $data);
    }

    /**
	 * 初始化订单加载
	 */
	public function init()
    {
    	$params = $this->request->post();
        $data = OrderModel::getInitData($params);
        $this->success('订单数据', $data);
    }

    /**
     * 创建
     */
	public function add()
    {
        $params = $this->request->post();
        $order = OrderModel::addOrder($params);
        $this->success('创建成功', $order);
    }

    /**
     * 详情
     */
    public function detail()
    {
        $params = $this->request->post();
        $this->success('订单详情', OrderModel::getDetail($params));
    }

    public function cancel()
    {
        $params = $this->request->post();
        $this->success('取消成功', OrderModel::cancelOrder($params));
    }
    
	
}