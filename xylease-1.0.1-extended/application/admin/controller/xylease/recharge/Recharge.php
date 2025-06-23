<?php

namespace app\admin\controller\xylease\recharge;

use app\common\controller\Backend;

/**
 * 充值套餐管理
 *
 * @icon fa fa-circle-o
 */
class Recharge extends Backend
{

    /**
     * Recharge模型对象
     * @var \app\admin\model\xylease\Recharge
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\recharge\Recharge;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    
}
