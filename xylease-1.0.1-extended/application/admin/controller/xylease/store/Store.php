<?php

namespace app\admin\controller\xylease\store;

use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 门店管理
 *
 * @icon fa fa-circle-o
 */
class Store extends Backend
{

    /**
     * Store模型对象
     * @var \app\admin\model\xylease\store\Store
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\store\Store;

    }

    /**
     * 门店信息
     *
     * @return string
     * @throws \think\Exception
     */
    public function info()
    {
        $row = $this->model->get(1);
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

        $laloArr = explode(',',$params['latitude-longitude']);
        if(count($laloArr) != 2){
            $this->error(__('经纬度输入错误', ''));
        }

        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $params['latitude'] = $laloArr[0];
            $params['longitude'] = $laloArr[1];
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



}
