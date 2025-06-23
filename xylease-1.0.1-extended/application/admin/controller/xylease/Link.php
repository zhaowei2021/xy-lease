<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;
use app\admin\model\xylease\goods\Category as CategoryModel;
use app\admin\model\xylease\Notice as NoticeModel;
use app\admin\model\xylease\Article as ArticleModel;
use app\admin\model\xylease\goods\Goods as GoodsModel;


/**
 * 链接管理
 *
 * @icon fa fa-circle-o
 */
class Link extends Backend
{

    /**
     * Link模型对象
     * @var \app\admin\model\xylease\Link
     */
    protected $model = null;
    protected $searchFields = 'id,name,type';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\Link;
        $this->view->assign("typeList", $this->model->getTypeList());
    }

    /**
     * 生成链接
     */
    public function load(){
        //基础链接
        $basicLink = [
            ['name'=>'首页','url'=>'/pages/index','type'=>'basic'],
            ['name'=>'分类','url'=>'/pages/category','type'=>'basic'],
            ['name'=>'购物车','url'=>'/pages/cart','type'=>'basic'],
            ['name'=>'我的','url'=>'/pages/user','type'=>'basic'],
            ['name'=>'门店信息','url'=>'/pages/store/detail','type'=>'basic'],
            ['name'=>'公告列表','url'=>'/pages/service/notice/list','type'=>'basic'],
            ['name'=>'领券中心','url'=>'/pages/market/coupon/list','type'=>'basic'],
            ['name'=>'充值套餐','url'=>'/pages/user/balance/recharge','type'=>'basic'],
            ['name'=>'联系客服','url'=>'phone','type'=>'basic'],
            ['name'=>'小程序客服','url'=>'contact','type'=>'basic'],
            ['name'=>'分享好友','url'=>'share','type'=>'basic'],
        ];

        //会员中心
        $userLink = [
            ['name'=>'我的余额','url'=>'/pages/user/balance/detail','type'=>'user'],
            ['name'=>'租赁订单','url'=>'/pages/user/order/list','type'=>'user'],
            ['name'=>'我的优惠券','url'=>'/pages/user/coupon/list','type'=>'user'],
            ['name'=>'分销中心','url'=>'/pages/distribution/center','type'=>'user'],
            ['name'=>'我的收藏','url'=>'/pages/user/favorite','type'=>'user'],
            ['name'=>'我的资料','url'=>'/pages/user/info','type'=>'user'],
            ['name'=>'员工中心','url'=>'/pages/staff/clerk','type'=>'user'],
        ];
        
        //商品链接
        $goodsLink = [
            ['name'=>'商品列表','url'=>'/pages/goods/list','type'=>'goods'],
        ];
        $goodsCategory = CategoryModel::where(['status'=>'normal'])->select();
        foreach($goodsCategory as $cc){
            $goodsLink[] = ['name'=>'商品分类-'.$cc['name'],'url'=>'/pages/goods/list?cid='.$cc['id'].'&name='.$cc['name'],'type'=>'goods'];
        }
        $goods = GoodsModel::where(['status'=>'up'])->select();
        foreach($goods as $c){
            $goodsLink[] = ['name'=>'商品详情-'.$c['name'],'url'=>'/pages/goods/detail?id='.$c['id'],'type'=>'goods'];
        }

        //公告链接
        $noticeLink = [];
        $notice = NoticeModel::where(['status'=>'normal'])->select();
        foreach($notice as $s){
            $noticeLink[] = ['name'=>$s['title'],'url'=>'/pages/service/notice/detail?id='.$s['id'],'type'=>'notice'];
        }

        //文章链接
        $articleLink = [];
        $article = ArticleModel::where(['status'=>'normal'])->select();
        foreach($article as $s){
            $articleLink[] = ['name'=>$s['title'],'url'=>'/pages/service/article/detail?id='.$s['id'],'type'=>'notice'];
        }

        $allLink = array_merge($basicLink,$userLink,$noticeLink,$articleLink,$goodsLink);
        foreach($allLink as $l){
            $link = $this->model->where($l)->find();
            if(!$link){
                $this->model->create($l);
            }
        }
        $this->success('生成成功');
    }

    /**
     * 选择链接
     */
    public function select()
    {
        if ($this->request->isAjax()) {
            return $this->index();
        }
        return $this->view->fetch();
    }


}