<?php

namespace app\admin\controller\xylease\distribution;

use app\common\controller\Backend;

/**
 * 分销商佣金流水管理
 *
 * @icon fa fa-circle-o
 */
class Commission extends Backend
{

    /**
     * Commission模型对象
     * @var \app\admin\model\xylease\distribution\Commission
     */
    protected $model = null;
    protected $searchFields = 'id,user.nickname,user.mobile';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\distribution\Commission;
        $this->view->assign("typeList", $this->model->getTypeList());
    }



    /**
     * 查看
     */
    public function index($distribution_id = '')
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();


            if(!empty($distribution_id)){
                $list = $this->model
                ->with(['user'])
                ->where($where)
                ->where('distribution_id',$distribution_id)
                ->order($sort, $order)
                ->paginate($limit);
            }else{
                $list = $this->model
                ->with(['user'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            }
            
            unset($row);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->assignconfig('distribution_id',$distribution_id);
        return $this->view->fetch();
    }

}
