<?php

namespace app\api\controller\xylease\staff;
use app\common\controller\Api;
use addons\xylease\service\Staff as StaffService;
use app\api\model\xylease\order\Order as OrderModel;
use app\api\model\xylease\user\User as UserModel;

/**
 * 职员中心接口
 */
class Clerk extends Api
{
    protected $noNeedLogin = ['lists','detail'];
    protected $noNeedRight = ['*'];
    

    protected $staffService = null;

    public function _initialize()
	{
	    parent::_initialize();
        $staffService = new StaffService($this->auth->id,'clerk');
        $statusArr = $staffService->getStaffStatus();
        if($statusArr['status'] !== 'normal'){
            $this->error($statusArr['msg']);
        }
        $this->staffService = $staffService;
	}
   
    /**
     * 租赁取货
     */
    public function leasePickup()
    {
        $params = $this->request->post();
        $params['staff_id'] = $this->staffService->staff->id;
        $data = OrderModel::leasePickup($params);
        $this->success('操作成功', $data);
    }

    /**
     * 租赁归还
     */
    public function leaseComplete(){
        $params = $this->request->post();
        $params['staff_id'] = $this->staffService->staff->id;
        $data = OrderModel::leaseComplete($params);
        $this->success('操作成功', $data);
    }

    
    /**
     * 租赁订单
     */
    public function orderList(){
        $params = $this->request->post();
        $params['staff_id'] = $this->staffService->staff->id;
        $data = OrderModel::getLists($params);
        $this->success('门票订单', $data);
    }

    /**
     * 会员详情
     */
    public function userInfo() {
        $user_id = $this->request->post('user_id');
        $userInfo = UserModel::where(['id'=>$user_id])->find();
        if(empty($userInfo)){
            $this->error('会员不存在');
        }

        //待处理租赁订单
        $orderList = OrderModel::getLists(['user_id'=>$userInfo['id'],'status'=>[1,2],'paginate'=>false]);

        $this->success('会员详情', [
            'userInfo'          => $userInfo,
            'orderList'         => $orderList,
        ]);
    }

    /**
     * 员工中心
     */
    public function info()
    {
        $staffInfo = $this->staffService->staff;
        $this->success('员工详情', $staffInfo);
    }
	
	
}