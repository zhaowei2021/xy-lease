<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;
use think\Db;
use Exception;
use fast\Random;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 底部导航
 */
class Tabbar extends Backend
{

    /**
     * Tarbar模型对象
     * @var \app\admin\model\xylease\Tarbar
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
		$this->model = model('app\admin\model\xylease\Config');
    }

	public function index()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post("data");
            if ($data) {
                try {
                    $config = $this->model->get(['name' => 'tabbar']);
                    if(!$config) {
                        $this->model->allowField(true)->save([
                            'name' => 'tabbar',
                            'title' => '底部导航',
                            'group' => 'tabbar',
                            'type' => 'array',
                            'value' => $data,
                        ]);
                    }else {
                        $config->value = $data;
                        $config->save();
                    }
                    
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
                $this->success('保存成功');
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $config = $this->model->where(['name' => 'tabbar'])->value('value');
        $config = json_decode($config, true);
        $this->assignconfig('row', $config);
        return $this->view->fetch();  
    }
	
}
