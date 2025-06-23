<?php

namespace app\api\controller\xylease;
use app\common\controller\Api;
use app\api\model\xylease\Cart as CartModel;

/**
 * 购物车接口
 */
class Cart extends Api
{

    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    public function lists()
    {
        $data = CartModel::getLists();
        $this->success('购物车列表', $data);
    }

    public function add()
    {
        $params = $this->request->post();
        $this->success('添加成功', CartModel::add($params));
    }

    public function edit()
    {
        $params = $this->request->post();
        $this->success('', CartModel::edit($params));
    }

}