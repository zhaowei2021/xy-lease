<?php

namespace app\api\controller\xylease\user;
use app\common\controller\Api;

use app\api\model\xylease\user\Address as AddressModel;

/**
 * 地址接口
 */
class Address extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

	/**
	 * 列表
	 */
	public function lists()
    {
    	$params = $this->request->post();
        $data = AddressModel::getLists($params);
        $this->success('收货地址列表', $data);
    }

    /**
     * 新增
     */
    public function add()
    {
        $params = $this->request->post();
        $order = AddressModel::add($params);
        $this->success('添加成功', $order);
    }

    /**
     * 详情
     */
    public function detail()
    {
        $id = $this->request->post('id');
        $detail = AddressModel::getDetail($id);

        if(!$detail){
            $this->error('收货地址不存在！');
        }
        $this->success('收货地址详情', $detail);
    }

    /**
     * 删除
     */
    public function del($id)
    {
        $this->success('删除成功', AddressModel::del($id));
    }

    /**
     * 设置默认
     */
     public function default($id)
     {
         $this->success('设置成功', AddressModel::setDefault($id));
     }
	
}