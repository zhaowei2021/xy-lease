<?php

namespace app\api\controller\xylease\user;
use app\common\controller\Api;

use app\api\model\xylease\user\Withdraw as WithdrawModel;

/**
 * 提现接口
 */
class Withdraw extends Api
{

    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];


    /**
     * 初始化提现信息
     */
    public function init()
    {
        $params = $this->request->post();
        $detail = WithdrawModel::getWithdrawInit($params);
        $this->success('提现信息', $detail);
    }

    /**
	 * 列表
	 */
	public function lists()
    {
    	$params = $this->request->post();
        $data = WithdrawModel::getList($params);
        $this->success('提现列表', $data);
    }

    /**
     * 新增
     */
    public function add()
    {
        $params = $this->request->post();
        $order = WithdrawModel::add($params);
        $this->success('添加成功', $order);
    }

    
}
