<?php

namespace app\api\controller\xylease\distribution;

use app\common\controller\Api;
use addons\xylease\service\distribution\Distribution as DistributionService;
use app\api\model\xylease\distribution\Commission as CommissionModel;
use app\api\model\xylease\distribution\Order as DistributionOrderModel;

/**
 * 分销中心接口
 */
class Center extends Api
{

    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    protected $distributionService = null;

    public function _initialize()
	{
	    parent::_initialize();

        $distributionService = new DistributionService($this->auth->id);
        $statusArr = $distributionService->getDistributionStatus();
        if($statusArr['status'] !== 'normal'){
            $this->error($statusArr['msg']);
        }

        $this->distributionService = $distributionService;

	}

    /**
     * 分销商详情
     */
    public function info()
    {
        $distributionInfo = $this->distributionService->distribution;
        $distributionInfo['levelInfo'] = $this->distributionService->getDistributionLevelInfo();
        $distributionInfo['teamNum'] = $this->distributionService->getTeamNum();
        $this->success('分销商详情', $distributionInfo);
    }



    /**
     * 用户团队
     */
    public function team()
    {
        $params = $this->request->post();
        $team = $this->distributionService->getTeamList($params);
        $this->success('我的团队', $team);
        
    }

    /**
     * 账单(佣金明细)
     */
    public function bill()
    {
        $params = $this->request->post();
        $data = CommissionModel::getLists($params);
        $this->success('佣金明细', $data);
    }

    /**
	 * 分销订单
	 */
	public function orders()
    {
    	$params = $this->request->post();
        $data = DistributionOrderModel::getLists($params);
        $this->success('分销订单列表', $data);
    }

    
}
