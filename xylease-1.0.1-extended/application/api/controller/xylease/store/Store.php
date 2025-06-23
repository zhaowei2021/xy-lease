<?php

namespace app\api\controller\xylease\store;
use app\common\controller\Api;

use app\api\model\xylease\store\Store as StoreModel;


/**
 * 门店接口
 */
class Store extends Api
{
    protected $noNeedLogin = ['detail'];
    protected $noNeedRight = ['*'];


    /**
     * 门店详情
     */
    public function detail()
    {
        $detail = StoreModel::getDetail(1);
        $this->success('门店详情', $detail);
    }

	
}