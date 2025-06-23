<?php

namespace app\api\controller\xylease\goods;
use app\common\controller\Api;

use app\api\model\xylease\goods\Goods as GoodsModel;
use app\api\model\xylease\goods\Category as CategoryModel;
use app\api\model\xylease\user\View as ViewModel;
use app\api\model\xylease\Config;
use app\api\model\xylease\Article;

/**
 * 商品接口
 */
class Goods extends Api
{
    protected $noNeedLogin = ['lists','detail','categories'];
    protected $noNeedRight = ['*'];
    
	/**
	 * 商品列表
	 */
	public function lists()
    {
    	$params = $this->request->get();
        $data = GoodsModel::getLists($params);
        $this->success('商品列表', $data);
    }
	
    /**
     * 商品详情
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $detail = GoodsModel::getDetail($id);

        if(!$detail){
            $this->error('商品不存在！');
        }

        // 记录足记
        ViewModel::addView($detail,'goods');

        $sku_price = $detail['sku_price'];

        $detail = json_decode(json_encode($detail), true);
        $detail['sku_price'] = $sku_price;

        //租赁规则
        $leaseConfig = Config::getValueByName('lease');
        $leaseRule = '';
        if(isset($leaseConfig['rule']) && $leaseConfig['rule']){
            $leaseRule = Article::getDetail($leaseConfig['rule']);
        }
        $detail['lease_rule'] = $leaseRule;

        $this->success('商品详情', $detail);
    }

    public function getContentAttr($value, $data)
    {
        $content = $data['content'];
        $content = str_replace("<img src=\"/uploads", "<img style=\"width: 100%;!important\" src=\"" . cdnurl("/uploads", true), $content);
        $content = str_replace("<video src=\"/uploads", "<video style=\"width: 100%;!important\" src=\"" . cdnurl("/uploads", true), $content);
        return $content;

    }

    /**
     * 商品分类
     */
    public function categories(){
        $params = $this->request->get();
        $data = CategoryModel::getAllCategory($params);
        $this->success('商品分类', $data);
    }
	
}