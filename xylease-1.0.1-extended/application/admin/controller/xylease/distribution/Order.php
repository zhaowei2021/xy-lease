<?php

namespace app\admin\controller\xylease\distribution;

use app\common\controller\Backend;

/**
 * 分销订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\xylease\distribution\Order
     */
    protected $model = null;
    protected $searchFields = 'ordersn';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\distribution\Order;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index($distribution_id = 0)
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
       
        $where1 = [];
        if($distribution_id>0){
            $where1 = ['one_distribution_id|two_distribution_id'=>$distribution_id];
        }

        $total = $this->model
                ->with(['buyer','one','two'])
                ->where($where)
                ->where($where1)
                ->order($sort, $order)
                ->count();

        $list = $this->model
                ->with(['buyer','one','two'])
                ->where($where)
                ->where($where1)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

        foreach ($list as $row) {
            $row->order_info = $this->getOrderInfo($row);
        }
        $list = collection($list)->toArray();
        $result = array("total" => $total, "rows" => $list);


        return json($result);
    }

    /**
     * 获取分销订单详情
     */
    private function getOrderInfo($row){
        $orderInfo = null;
        $type = $row['ordertype'];
        $orderId = $row['service_order_id'];
        if($type == 'order'){
            $orderInfo = (new \app\admin\model\xylease\order\Order())->with(['item'])->where(['id'=>$orderId])->find();
        }
        
        return $orderInfo;
    }



}
