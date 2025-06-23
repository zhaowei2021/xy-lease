<?php

namespace app\api\controller\xylease\recharge;
use app\common\controller\Api;
use app\api\model\xylease\recharge\Recharge as RechargeModel;

/**
 * 充值套餐接口
 */
class Recharge extends Api
{
    protected $noNeedLogin = ['lists'];
    protected $noNeedRight = ['*'];

	/**
	 * 充值套餐列表
	 */
	public function lists()
    {
    	$params = $this->request->get();
        $data = RechargeModel::getLists($params);
        $this->success('充值套餐列表', $data);
    }

}