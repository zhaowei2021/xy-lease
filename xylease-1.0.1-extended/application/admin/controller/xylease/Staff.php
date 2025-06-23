<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;

/**
 * 门店员工管理
 *
 * @icon fa fa-circle-o
 */
class Staff extends Backend
{

    /**
     * Staff模型对象
     * @var \app\admin\model\xylease\Staff
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\Staff;
        $this->view->assign("roleList", $this->model->getRoleList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        $this->relationSearch = true;
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
            ->with(['user'])
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);

        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

}
