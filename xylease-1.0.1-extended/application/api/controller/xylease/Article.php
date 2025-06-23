<?php

namespace app\api\controller\xylease;
use app\common\controller\Api;

use app\api\model\xylease\Article as ArticleModel;
use app\api\model\xylease\user\View as ViewModel;

/**
 * 文章接口
 */
class Article extends Api
{
    protected $noNeedLogin = ['lists','detail'];
    protected $noNeedRight = ['*'];
    
	
    /**
     * 文章详情
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $detail = ArticleModel::getDetail($id);

        if(!$detail){
            $this->error('文章不存在！');
        }

        // 记录足记
        ViewModel::addView($detail,'article');

        $this->success('文章详情', $detail);
    }
	
}