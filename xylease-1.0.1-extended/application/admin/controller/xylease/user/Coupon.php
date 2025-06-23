<?php

namespace app\admin\controller\xylease\user;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\xylease\Coupon as CouponModel;

/**
 * 优惠券管理
 *
 * @icon fa fa-circle-o
 */
class Coupon extends Backend
{

    /**
     * Coupon模型对象
     * @var \app\admin\model\xylease\user\Coupon
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\user\Coupon;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("gettypeList", $this->model->getGettypeList());
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


    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }

        if($params['num'] < 1 || $params['num'] > 500){
            $this->error(__('赠送数量输入错误', ''));
        }

        $couponList = [];
        $coupon = CouponModel::get($params['coupon_id']);
        $endtime = 0;
        if($coupon['validitytype'] == 0){
            $endtime = $coupon['endusetime'];
        }

        if($coupon['validitytype'] == 1){
            $endtime = time() + $coupon['fixedterm'] * 3600 * 24;
        }

        for($i = 1; $i<=$params['num'];$i++){
            $couponList[] = [
                'user_id'   => $params['user_id'],
                'coupon_id' => $params['coupon_id'],
                'type'      => $coupon['type'],
                'name'      => $coupon['name'],
                'money'     => $coupon['money'],
                'atleast'   => $coupon['atleast'],
                'discount'  => $coupon['discount'],
                'gettype'      => 2,
                'endtime'      => $endtime
            ];
        }

        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            $result = $this->model->allowField(true)->saveAll($couponList);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }


}
