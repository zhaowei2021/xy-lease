<?php

namespace app\api\controller\xylease\goods;
use app\common\controller\Api;
use app\api\model\xylease\goods\Category as CategoryModel;

/**
 * 商品分类接口
 */
class Category extends Api
{
    protected $noNeedLogin = ['lists'];
    protected $noNeedRight = ['*'];
    
	/**
	 * 列表
	 */
	public function lists()
    {
        $params = $this->request->get();
        $data = CategoryModel::getAllCategory($params);
        $this->success('商品分类', $data);
    }
	
}