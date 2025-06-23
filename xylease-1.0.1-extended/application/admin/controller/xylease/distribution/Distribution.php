<?php

namespace app\admin\controller\xylease\distribution;

use app\common\controller\Backend;
use app\admin\model\xylease\distribution\Commission as CommissionModel;
use addons\xylease\service\distribution\Distribution as DistributionService;
use app\admin\model\xylease\user\User;
use think\Db;
use Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 分销商
 *
 * @icon fa fa-circle-o
 */
class Distribution extends Backend
{

    /**
     * Distribution模型对象
     * @var \app\admin\model\xylease\Distribution
     */
    protected $model = null;
    protected $searchFields = 'realname,mobile';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\distribution\Distribution;
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
            ->with(['user','parent','level'])
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $distribution = $this->model->where(['user_id'=>$params['user_id']])->find();
        if(!empty($distribution) && $distribution['user_id'] != $params['user_id']){
            $this->error(__('会员已绑定分销商', ''));
        }

        if($params['pid'] > 0){
            if($params['user_id'] == $params['pid']){
                $this->error(__('不能选自己作为上级', ''));
            }

            $pid = $params['pid'];
            while($pid){
                $pdis = $this->model->where(['user_id'=>$pid])->find();

                if($pdis['pid'] == $params['user_id']){
                    $this->error(__('不能选下级作为上级', ''));
                }else{
                    $pid = $pdis['pid'];
                }
            }
           
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
            $result = $row->allowField(true)->save($params);

            $user = User::get($params['user_id']);
            $user->xylease_parent_user_id = $params['pid'];
            $user->save();

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

        $distribution = $this->model->where(['user_id'=>$params['user_id']])->find();
        if(!empty($distribution)){
            $this->error(__('会员已绑定分销商', ''));
        }

        if($params['pid'] > 0){
            if($params['user_id'] == $params['pid']){
                $this->error(__('不能选自己作为上级', ''));
            }

            $pid = $params['pid'];
            while($pid){
                $pdis = $this->model->where(['user_id'=>$pid])->find();

                if($pdis['pid'] == $params['user_id']){
                    $this->error(__('不能选下级作为上级', ''));
                }else{
                    $pid = $pdis['pid'];
                }
            }
           
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
            $result = $this->model->allowField(true)->save($params);
            $user = User::get($params['user_id']);
            $user->xylease_parent_user_id = $params['pid'];
            $user->save();
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

    /**
     * 详情
     *
     * @param $id
     */
    public function detail($user_id = null)
    {
        $row = $this->model->with(['user','level','parent'])->where(['user_id'=>$user_id])->find();

        $dis = new DistributionService($user_id);
        $row['teams'] = $dis->getTeamNum();

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
     * 调整佣金
     *
     * @param $id
     */
    public function recharge($user_id = null)
    {
        $row = $this->model->where(['user_id'=>$user_id])->find();
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
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

            $before = $row['commission'];
            $after = $row['commission']+$params['num'];

            $data = ['commission'=>$after];
            if($params['num']>0){
                $data['total_commission'] = $row['total_commission'] + $params['num'];
            }

            $row->allowField(true)->save($data);
            $result = CommissionModel::create([
                'distribution_id' => $row['user_id'],
                'type'    => 'sys',
                'money'    => $params['num'],
                'before'    => $before,
                'after'    => $after,
                'remark'   => $params['remark']
            ]);

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
     * 调整等级
     *
     * @param $id
     */
    public function relevel($user_id = null)
    {
        $row = $this->model->get($user_id);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
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
            $result = $row->allowField(true)->save($params);
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
     * 调整上级
     *
     * @param $id
     */
    public function repid($user_id = null)
    {
        $row = $this->model->get($user_id);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
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

        if($row['pid'] == $params['pid']){
            $this->error(__('未更改上级', ''));
        }

        if($row['user_id'] == $params['pid']){
            $this->error(__('不能将自己调为上级', ''));
        }

        // 当前分销商
        $curDis = new DistributionService($row['user_id']);

        Db::startTrans();
        $result = false;
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            
            $result = $row->allowField(true)->save(['pid'=>$params['pid']]);
            $curDis->user->allowField(true)->save(['xylease_parent_user_id'=>$params['pid']]);

            $parentDis = $this->model->get($params['pid']);

            if(!empty($parentDis) && $parentDis['pid'] == $row['user_id']){
                $parentDis->pid = 0;
                $parentDis->save();

                $parentUser = User::get($params['pid']);
                $parentUser->xylease_parent_user_id = 0;
                $parentUser->save();

            }

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
