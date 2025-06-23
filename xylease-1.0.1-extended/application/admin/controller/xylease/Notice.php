<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;

/**
 * 公告管理
 *
 * @icon fa fa-circle-o
 */
class Notice extends Backend
{

    /**
     * Notice模型对象
     * @var \app\admin\model\xylease\Notice
     */
    protected $model = null;
    protected $searchFields = 'id,title';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\Notice;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
	 * 选择
	 */
	public function select()
	{
	    if ($this->request->isAjax()) {
	        return $this->index();
	    }
	    return $this->view->fetch();
	}

}
