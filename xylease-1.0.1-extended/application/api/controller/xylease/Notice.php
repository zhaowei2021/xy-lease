<?php

namespace app\api\controller\xylease;
use app\common\controller\Api;

use app\api\model\xylease\Notice as NoticeModel;
use app\api\model\xylease\user\View as ViewModel;

/**
 * 公告接口
 */
class Notice extends Api
{
    protected $noNeedLogin = ['lists','detail'];
    protected $noNeedRight = ['*'];
    
	/**
	 * 公告列表
	 */
	public function lists()
    {
    	$params = $this->request->get();
        $data = NoticeModel::getLists($params);
        $this->success('公告列表', $data);
    }
	
    /**
     * 公告详情
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $detail = NoticeModel::getDetail($id);
        if(!$detail){
            $this->error('公告不存在！');
        }
        ViewModel::addView($detail,'notice'); // 记录足记
        $this->success('公告详情', $detail);
    }
	
}