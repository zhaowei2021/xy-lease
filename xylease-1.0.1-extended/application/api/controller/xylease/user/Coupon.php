<?php

namespace app\api\controller\xylease\user;
use app\common\controller\Api;

use app\api\model\xylease\user\Coupon as CouponModel;

/**
 * 用户优惠券接口
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
        $params['user_id'] = $this->auth->id;
        $data = CouponModel::getLists($params);
        $this->success('优惠券列表', $data);
    }

    /**
     * 转增
     */
    public function handsel(){
        $params = $this->request->post();
        $data = CouponModel::handsel($params);
        $this->success('转增成功', $data);
    }

    
}