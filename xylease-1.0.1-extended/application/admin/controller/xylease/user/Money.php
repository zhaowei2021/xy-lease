<?php

namespace app\admin\controller\xylease\user;

use app\common\controller\Backend;

/**
 * 会员余额明细管理
 *
 * @icon fa fa-circle-o
 */
class Money extends Backend
{

    /**
     * Money模型对象
     * @var \app\admin\model\xylease\user\Money
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\user\Money;

    }

    /**
     * 查看
     */
    public function index($user_id = 0)
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            if($user_id > 0){
                $list = $this->model
                    ->with(['user'])
                    ->where($where)
                    ->where('user_id',$user_id)
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
        $this->assignconfig('user_id',$user_id);
        return $this->view->fetch();
    }

    

}
