<?php

namespace app\admin\controller\xylease\goods;

use app\common\controller\Backend;

/**
 * 商品规格管理
 *
 * @icon fa fa-circle-o
 */
class SkuPrice extends Backend
{

    /**
     * SkuPrice模型对象
     * @var \app\admin\model\xylease\goods\SkuPrice
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\goods\SkuPrice;
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
	 * 选择
	 */
	public function select()
	{
	    //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $list = $this->model
            ->with(['goods'])
            ->where($where)
            ->where('type','single')
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
	}


}
