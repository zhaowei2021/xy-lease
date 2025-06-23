<?php

namespace app\admin\controller\xylease\goods;

use app\common\controller\Backend;
use fast\Tree;

/**
 * 分类管理
 *
 * @icon fa fa-circle-o
 */

class Category extends Backend
{

    /**
     * Category模型对象
     * @var \app\admin\model\xylease\goods\Category
     */
    protected $model = null;
    protected $categorylist = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\goods\Category;
        $this->view->assign("statusList", $this->model->getStatusList());

        $tree = Tree::instance();
        $tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $this->categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($this->categorylist as $k => $v) {
            $categorydata[$v['id']] = $v;
        }
        $this->view->assign("parentList", $categorydata);

    }



    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $list = $this->categorylist;
            $total = count($list);
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();

    }


}
