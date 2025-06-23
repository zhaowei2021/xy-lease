<?php

namespace app\api\controller\xylease;
use app\common\controller\Api;

use app\api\model\xylease\Coupon as CouponModel;


/**
 * 优惠券接口
 */
class Coupon extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    

    /**
	 * 列表
	 */
	public function lists()
    {
    	$params = $this->request->post();
        $data = CouponModel::getLists($params);
        $this->success('订单列表', $data);
    }

	/**
     * 领取
     */
    public function receive() {
        $params = $this->request->post();
        $data = CouponModel::receive($params);
        $this->success('领取成功', $data);
    }

    
}