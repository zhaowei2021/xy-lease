<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;
use think\Exception;

/**
 * xylease配置
 */
class Config extends Backend
{

    /**
     * @var \app\admin\model\xylease\Config
     */
    protected $model = null;
    protected $noNeedRight = ['index'];


    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('app\admin\model\xylease\Config');
    }

    /**
     * 查看
     */
    public function index()
    {
        return $this->view->fetch();
    }

    public function set($type)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post("data");
            if ($data) {
                try {
                    $config = $this->model->get(['name' => $type]);
                    if(!$config) {
                        $this->model->allowField(true)->save([
                            'name' => $type,
                            'title' => $this->request->post("title"),
                            'group' => $this->request->post("group"),
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
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $config = $this->model->where(['name' => $type])->value('value');
        $config = json_decode($config, true);
        $this->assignconfig('type',$type);
        $this->assignconfig('row', $config);
        return $this->view->fetch();  
    }

    


}
