<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;

/**
 * 文章管理
 *
 * @icon fa fa-circle-o
 */
class Article extends Backend
{

    /**
     * Article模型对象
     * @var \app\admin\model\xylease\Article
     */
    protected $model = null;
    protected $categoryModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\Article;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 选择文章
     */
    public function select()
    {
        if ($this->request->isAjax()) {
            return $this->index();
        }
        $mimetype = $this->request->get('mimetype', '');
        $mimetype = substr($mimetype, -1) === '/' ? $mimetype . '*' : $mimetype;
        $this->view->assign('mimetype', $mimetype);
        return $this->view->fetch();
    }


}
