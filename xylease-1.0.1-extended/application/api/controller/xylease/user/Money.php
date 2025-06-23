<?php

namespace app\api\controller\xylease\user;
use app\common\controller\Api;

use app\api\model\xylease\user\Money as UserMoneyModel;

/**
 * 余额接口
 */
class Money extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    

	/**
	 * 列表
	 */
	public function lists()
    {
    	$params = $this->request->post();
        $data = UserMoneyModel::getLists($params);
        $this->success('账户列表', $data);
    }

    
}