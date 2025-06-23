<?php

namespace app\admin\controller\xylease\user;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use addons\xylease\service\Coach as CoachService;
use addons\xylease\service\distribution\Distribution as DistributionService;
use addons\xylease\service\Store as StoreService;
use app\api\model\xylease\user\User as UserModel;

/**
 * 会员提现管理
 *
 * @icon fa fa-circle-o
 */
class Withdraw extends Backend
{

    /**
     * Withdraw模型对象
     * @var \app\admin\model\xylease\user\Withdraw
     */
    protected $model = null;
    protected $searchFields = 'id,realname,mobile';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\user\Withdraw;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 查看
     */
    public function index($type = '')
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['user'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            
            unset($row);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 详情
     *
     * @param $id
     */
    public function detail($ids = null)
    {
        $row = $this->model->where(['id'=>$ids])->find();
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        $this->view->assign('row', $row);
        return $this->view->fetch();
    }

    /**
     * 同意提现
     */
    public function agree($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        
        if($row['status'] != 0){
            $this->error(__('状态错误，请刷新页面再操作。'));
        }
        
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save(['status'=>1,'audittime'=>time()]);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    /**
     * 确认打款
     */
    public function payment($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        
        if($row['status'] != 1){
            $this->error(__('状态错误，请刷新页面再操作。'));
        }
        
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }

            if($row['type'] == 'distribution'){
                $distribution =  new DistributionService($row['user_id']);
                $distribution->commission($row['applymoney'],'pay_withdraw',$row->id,false);
            }
            

            $result = $row->allowField(true)->save(['status'=>2,'paymenttime'=>time()]);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    /**
     * 拒绝提现
     */
    public function refuse($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }

        if($row['status'] != 0){
            $this->error(__('状态错误，请刷新页面再操作。'));
        }
        
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            

            if($row['type'] == 'distribution'){
                 $distribution =  new DistributionService($row['user_id']);
                 $distribution->commission($row['applymoney'],'refuse_withdraw',$row->id);
            }

            if($row['type'] == 'balance'){
                UserModel::money($row['applymoney'], $row['user_id'],'refuse_withdraw','',$row->id);
            }

            $result = $row->allowField(true)->save(['status'=>-1,'audittime'=>time(),'refusereason'=>$params['refusereason']]);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

}
