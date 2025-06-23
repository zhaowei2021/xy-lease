<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;
use app\admin\model\xylease\Config;
use app\admin\model\xylease\store\Store;

use think\Db;
use Exception;
use fast\Random;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 自定义装修页面管理
 *
 * @icon fa fa-circle-o
 */
class Page extends Backend
{

    /**
     * Page模型对象
     * @var \app\admin\model\xylease\Page
     */
    protected $model = null;
	protected $searchFields = 'id,name';

    public function _initialize()
    {
        parent::_initialize();
		$leaseConfig = Config::getValueByName('lease');
        $this->model = new \app\admin\model\xylease\Page;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
		$this->assignconfig('leaseConfig',$leaseConfig);
    }


    /**
	 * 添加
	 */
	public function add()
	{
	    if ($this->request->isPost()) {
	        $params = $this->request->post("row/a");
	        if ($params) {
	            $params = $this->preExcludeFields($params);
	
	            if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
	                $params[$this->dataLimitField] = $this->auth->id;
	            }
	            $result = false;
				
	            Db::startTrans();
	            try {
	                //是否采用模型验证
	                if ($this->modelValidate) {
	                    $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
	                    $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
	                    $this->model->validateFailException(true)->validate($validate);
	                }
					$params['page_token'] = Random::alnum(16);
					$params['page'] = '{"params":{"navigationBarTitleText":"标题"},"style":{"navigationBarTextStyle":"#ffffff","navigationBarBackgroundColor":"#f05656","pageBackgroundColor":"#f7f7f7"}}';
					$params['item'] = '[]';
	                $result = $this->model->allowField(true)->save($params);
	                Db::commit();
	            } catch (ValidateException|PDOException|Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
	            if ($result !== false) {
	                $this->success();
	            } else {
	                $this->error(__('No rows were inserted'));
	            }
	        }
	        $this->error(__('Parameter %s can not be empty', ''));
	    }
	    return $this->view->fetch();
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
        if ($this->request->isPost()) {
			
	        $params = $this->request->post();
			$row = $this->model->where(['page_token'=>$params['page_token']])->find();
	        if ($params) {
				if(!array_key_exists("item",$params)){
					$this->error('页面还没有任何组件哦');
				}
				$this->model->name = $params['name'];
				$this->model->is_use = $row['is_use'];
				$this->model->cover = $params['cover'];
				$this->model->page_token = $params['page_token'];
				$this->model->type = $params['type'];
				$this->model->page = json_encode($params['page']);
				$this->model->item = json_encode($params['item']);
				$this->model->save();
				foreach ($this->model
					->where('page_token', 'eq', $this->model->page_token)
					->where('id', 'neq', $this->model->id)
					->select() as $k => $v) {
					$v->is_use = 0;
					$v->save();
				    $v->delete();
				}
				$this->success("发布并保存成功");
	        }
	        $this->error(__('Parameter %s can not be empty', ''));
	    }
		$row = $this->model->get($ids);

		//通用配置
		$appStyle = Config::getValueByName('appstyle');
		if (!$row) {
		    $this->error(__('No Results were found'));
		}
		$adminIds = $this->getDataLimitAdminIds();
		if (is_array($adminIds)) {
		    if (!in_array($row[$this->dataLimitField], $adminIds)) {
		        $this->error(__('You have no permission'));
		    }
		}
		$storeInfo = Store::get(1);
		$this->assignconfig('page', $row);
		$this->assignconfig('appStyle', $appStyle);
		$this->assignconfig('storeInfo', $storeInfo);
		$this->assignconfig('page', $row);
		$this->assignconfig('appStyle', $appStyle);
	    return $this->view->fetch();
    }

	/**
	 * 历史记录
	 */
	public function history($token = null, $search = null)
	{	
		//设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
		if ($this->request->isAjax()) {
			$list = $this->model
				->onlyTrashed()
				->where(['page_token' => $token])
				->where('name', 'like', '%'.$search.'%')
				->select();
			$result = array("total" => count($list), "rows" => $list);
			return json($result);
		}
		return $this->view->fetch();
	}

	/**
	 * 恢复历史
	 */
	public function recover($id = null){
		if ($this->request->isPost()) {
			$row = $this->model
				->onlyTrashed()
				->where('id',$id)
				->find();
			if (!$row) {
			    $this->error(__('No Results were found'));
			}
			$this->success("拉取历史数据成功", "url", $row);
		}
	}

	/**
	 * 使用模板
	 */
	public function use($ids = null)
	{
		if ($this->request->isPost()) {
			$rows = $this->model->get($ids);
			if(!$rows){
				$this->error('模板不存在');
			}

			Db::startTrans();
			try {
				$rows->is_use = 1;
				$rows->save();
				// 将其他模板设为未使用
				foreach ($this->model
					->where('type', 'eq', $rows['type'])
					->where('id', 'neq', $rows->id)
					->select() as $k => $v) {
					$v->is_use = 0;
					$v->save();
				}
				Db::commit();
			} catch (ValidateException|PDOException|Exception $e) {
				Db::rollback();
				$this->error($e->getMessage());
			}

			$this->success("发布成功");
			
		}
	}

	
}
