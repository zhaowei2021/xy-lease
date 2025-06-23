<?php

namespace app\api\controller\xylease\user;
use app\common\controller\Api;

use app\api\model\xylease\user\Favorite as UserFavoriteModel;

/**
 * 收藏接口
 */
class Favorite extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    
	/**
	 * 列表
	 */
	public function lists()
    {
    	$params = $this->request->post();
        $data = UserFavoriteModel::getLists($params);
        $this->success('收藏列表', $data);
    }

    /**
     * 新增｜删除
     */
    public function add()
    {
        $params = $this->request->post();
        $result = UserFavoriteModel::add($params);
        $this->success($result ? '收藏成功' : '取消收藏', $result);
    }
	
}