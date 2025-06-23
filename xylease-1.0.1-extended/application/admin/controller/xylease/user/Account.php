<?php

namespace app\api\controller\xylease\user;
use app\common\controller\Api;

use app\api\model\xylease\user\Account as UserAccountModel;

/**
 * 账户接口
 */
class Account extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    

	/**
	 * 列表
	 */
	public function lists()
    {
    	$params = $this->request->post();
        $data = UserAccountModel::getList($params);
        $this->success('账户列表', $data);
    }

    /**
     * 新增
     */
    public function add()
    {
        $params = $this->request->post();
        $order = UserAccountModel::add($params);
        $this->success('添加成功', $order);
    }

    /**
     * 详情
     */
    public function detail()
    {
        $id = $this->request->post('id');
        $detail = UserAccountModel::getDetail($id);

        if(!$detail){
            $this->error('账户不存在！');
        }
        $this->success('账户详情', $detail);
    }

    /**
     * 删除
     */
    public function del($id)
    {
        $this->success('删除成功', UserAccountModel::del($id));
    }

    /**
     * 设置默认
     */
     public function default($id)
     {
         $this->success('设置成功', UserAccountModel::setDefault($id));
     }
	
}