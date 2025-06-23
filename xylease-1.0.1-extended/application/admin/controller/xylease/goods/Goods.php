<?php

namespace app\admin\controller\xylease\goods;

use app\common\controller\Backend;
use think\Db;
use Exception;
use fast\Tree;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use app\admin\model\xylease\goods\Sku;
use app\admin\model\xylease\goods\SkuPrice;
use app\admin\model\xylease\distribution\Level as DisLevelModel;
use app\admin\model\xylease\goods\GoodsItem as PackgeItem;
use addons\xylease\service\Stock as StockService;


/**
 * 商品管理
 *
 * @icon fa fa-circle-o
 */
class Goods extends Backend
{

    /**
     * Goods模型对象
     * @var \app\admin\model\xylease\Goods
     */
    protected $model = null;
    protected $categoryModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\goods\Goods;
        $this->view->assign("isSkuList", $this->model->getIsSkuList());
        $this->view->assign("statusList", $this->model->getStatusList());

        $this->view->assign("isDisList", $this->model->getIsDisList());
        $this->view->assign("disRuleList", $this->model->getDisRuleList());

        $this->categoryModel = model('app\admin\model\xylease\goods\Category');

        $tree = Tree::instance();
        $tree->init(collection($this->categoryModel->where(['status'=>'normal'])->order('id desc')->select())->toArray(), 'pid');
        $categoryList = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [];
        foreach ($categoryList as $k => $v) {
            $categorydata[$v['id']] = $v;
        }

        //分销等级
        $disLevel = DisLevelModel::where(['status'=>'normal'])->select();
        $this->assignconfig('disLevel',$disLevel);
        $this->view->assign("categoryList", $categorydata);
    }

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index($type = 'single')
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            $this->assignconfig('type',$type);
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();

        if($type == ''){
            $list = $this->model
            ->with(['sku_price'])
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        }else{
            $list = $this->model
            ->with(['sku_price'])
            ->where($where)
            ->where(['type'=>$type])
            ->order($sort, $order)
            ->paginate($limit);
        }
        
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }


    /**
     * 编辑库存
     */
    public function editStock($ids = null){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
           
            $skuList = Sku::all(['pid' => 0, 'goods_id' => $ids]);
            if ($skuList) {
                foreach ($skuList as &$s) {
                    $s->children = Sku::all(['pid' => $s->id, 'goods_id' => $ids]);
                }
            }
            $this->assignconfig('skuList', $skuList);
            $skuPrice = SkuPrice::all(['goods_id' => $ids]);

            $this->assignconfig('skuPrice', $skuPrice);
            $this->assignconfig('issku',$row['issku']);
            $this->assignconfig('type',$row['type']);
            $this->assignconfig('isdis',$row['isdis']);
            $this->assignconfig('disrule',$row['disrule']);
            
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        $skuPrice = $this->request->post("skuprice");
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            if ($row['issku']) {
                $result = $this->editMultStock($row, $skuPrice);
            } else {
                $result = $this->editSimStock($row, $params['nums']);
            }
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }



    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add($type = 'single')
    {
        if (false === $this->request->isPost()) {
            $this->assignconfig('type',$type);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $sku = $this->request->post("sku");

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }

        $goodsList = [];
        if($type == 'package'){
            $goodsList = json_decode($params['goodslist'],true);
        }

        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }

            $params['type'] = $type;
            $params['category_ids'] = implode(',',$params['category_ids']);
            $result = $this->model->allowField(true)->save($params);

            if ($result) {

                if($type == 'package'){
                    $this->editPackageItem($this->model,$goodsList);
                }

                if(($type == 'single' || $type == 'sell') && $params['issku']){ 
                    $this->editMultSku($this->model, $sku);
                }else{
                    $this->editSimSku($this->model, $sku);
                }
                
                Db::commit();
            }

            
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
           
        }

        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }

        return $this->success();
    }

    /**
     * 添加编辑套餐选项
     */
    protected function editPackageItem($package,$goodsList,$type = 'add'){
        $itemData = [];
        foreach($goodsList as $goods){
            $itemData[] = [
                'package_id' => $package->id,
                'goods_id'   => $goods['goods_id'],
                'goods_sku_price_id'    => $goods['goods_sku_price_id'],
                'goodsskutext'  => is_array($goods['goodsskutext']) ? implode(',', $goods['goodsskutext']) : '11',
                'goodsname'  => $goods['goodsname'],
                'goodsimage'  => $goods['goodsimage'],
                'nums'  => $goods['nums'],
                'createtime'   => time()
            ];
        }
        if($type == 'edit'){
            PackgeItem::where(['package_id'=>$package->id])->delete();
        }
        (new PackgeItem())->allowField(true)->saveAll($itemData);
    }

    /**
     * 多规格编辑库存
     */
    protected function editMultStock($goods, $skuPrice) {

        $skuPrice = json_decode($skuPrice, true);

        foreach ($skuPrice as $sp) {
            if($sp['nums'] != 0){
                $goodsSkuPrice = SkuPrice::get($sp['id']);
                if($goodsSkuPrice['stock'] + $sp['nums'] < 0){
                    throw new ValidateException('库存不能小于0');
                }
                //更新库存
                StockService::update($goodsSkuPrice->id,$sp['nums'],$this->auth->id,'sysedit');
            }
        }

        return true;
    }

    /**
     * 添加编辑多规格
     */
    protected function editMultSku($goods, $sku, $type = 'add') {

        $params = $this->request->post("row/a");
        $skuData = json_decode($sku, true);
        $skuList = json_decode($skuData['listData'], true);
        $skuPrice = json_decode($skuData['priceData'], true);
        
        if (count($skuList) < 1) {
            throw new Exception('请填写规格列表');
        }
        foreach ($skuList as $key => $sku) {
            if (count($sku['children']) <= 0) {
                throw new Exception('主规格至少要有一个子规格');
            }
            
            // 验证子规格不能为空
            foreach ($sku['children'] as $k => $child) {
                if (!isset($child['name']) || empty(trim($child['name']))) {
                    throw new Exception('子规格不能为空');
                }
            }
        }

        if (count($skuPrice) < 1) {
            throw new Exception('请填写规格信息');
        }

        $allChildrenSku = $this->saveSkuList($goods, $skuList, $type);

        if ($type == 'add') {
            foreach ($skuPrice as $s3 => &$k3) {
                $k3['goods_sku_ids'] = $this->checkRealIds($k3['goods_sku_temp_ids'], $allChildrenSku);
                $k3['goods_id'] = $goods->id;
                $k3['type'] = $goods['type'];
                $k3['goodsskutext'] = implode(',', $k3['goodsskutext']);
                $k3['commissionrule'] = $params['isdis'] && $params['disrule'] ? json_encode($k3['commissionrule']) : null;
                $k3['createtime'] = time();

                unset($k3['id']);
                unset($k3['temp_id']);      
                unset($k3['goods_sku_temp_ids']);
                $stock = $k3['stock'];
                $k3['stock'] = 0;

                $goodsSkuPrice = new SkuPrice;
                $goodsSkuPrice->allowField(true)->save($k3);

                //更新库存
                if($stock > 0){
                    StockService::update($goodsSkuPrice->id,$stock,$this->auth->id);
                }

            }
            

        } else {

            $oldSkuPriceIds = array_column($skuPrice, 'id');

            SkuPrice::where('goods_id', $goods->id)->where('id', 'not in', $oldSkuPriceIds)->delete();
            
            foreach ($skuPrice as $s3 => &$k3) {
                $data['goods_sku_ids'] = $this->checkRealIds($k3['goods_sku_temp_ids'], $allChildrenSku);
                $data['goods_id'] = $goods->id;
                $data['goodsskutext'] = implode(',', $k3['goodsskutext']);
                $data['commissionrule'] = $params['isdis'] && $params['disrule'] ? json_encode($k3['commissionrule']) : null;
                $data['weigh'] = $k3['weigh'];
                $data['image'] = $k3['image'];
                //$data['stock'] = $k3['stock'];
                $data['price'] = $k3['price'];
                $data['sn'] = $k3['sn'];
                $data['hourprice'] = $k3['hourprice'];
                $data['daysprice'] = $k3['daysprice'];
                $data['nightprice'] = $k3['nightprice'];
                $data['deposit'] = $k3['deposit'];
                $data['status'] = $k3['status'];
                $data['createtime'] = time();

                if ($k3['id']) {
                    $goodsSkuPrice = SkuPrice::get($k3['id']);
                } else {
                    $goodsSkuPrice = new SkuPrice();
                }

                if ($goodsSkuPrice) {
                    $goodsSkuPrice->save($data);
                }
            }
        }

        return true;
    }

    private function checkRealIds($newGoodsSkuIds, $allChildrenSku)
    {
        $newIdsArray = [];
        foreach ($newGoodsSkuIds as $id) {
            $newIdsArray[] = $allChildrenSku[$id];
        }
        return implode(',', $newIdsArray);

    }

    private function saveSkuList($goods, $skuList, $type = 'add') {
        $allChildrenSku = [];

        if ($type == 'edit') {
            $oldSkuIds = [];
            foreach ($skuList as $key => $sku) {
                $oldSkuIds[] = $sku['id'];

                $childSkuIds = [];
                if ($sku['children']) {
                    $childSkuIds = array_column($sku['children'], 'id');
                }

                $oldSkuIds = array_merge($oldSkuIds, $childSkuIds);
                $oldSkuIds = array_unique($oldSkuIds);
            }

            Sku::where('goods_id', $goods->id)->where('id', 'not in', $oldSkuIds)->delete();
        }

        foreach ($skuList as $s1 => &$k1) {
            if ($k1['id']) {
                Sku::where('id', $k1['id'])->update([
                    'name' => $k1['name'],
                ]);

                $skuId[$s1] = $k1['id'];
            } else {
                $skuId[$s1] = Sku::insertGetId([
                    'name' => $k1['name'],
                    'pid' => 0,
                    'goods_id' => $goods->id
                ]);
            }
            $k1['id'] = $skuId[$s1];
            foreach ($k1['children'] as $s2 => &$k2) {
                if ($k2['id']) {
                    Sku::where('id', $k2['id'])->update([
                        'name' => $k2['name'],
                    ]);

                    $skuChildrenId[$s1][$s2] = $k2['id'];
                } else {
                    $skuChildrenId[$s1][$s2] = Sku::insertGetId([
                        'name' => $k2['name'],
                        'pid' => $k1['id'],
                        'goods_id' => $goods->id
                    ]);
                }
                
                $allChildrenSku[$k2['temp_id']] = $skuChildrenId[$s1][$s2];
                $k2['id'] = $skuChildrenId[$s1][$s2];
                $k2['pid'] = $k1['id'];
            }
        }

        return $allChildrenSku;
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
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
           
            $skuList = Sku::all(['pid' => 0, 'goods_id' => $ids]);
            if ($skuList) {
                foreach ($skuList as &$s) {
                    $s->children = Sku::all(['pid' => $s->id, 'goods_id' => $ids]);
                }
            }
            $this->assignconfig('skuList', $skuList);
            $skuPrice = SkuPrice::all(['goods_id' => $ids]);

            $goodsList = PackgeItem::all(['package_id' => $ids]);
            $this->assignconfig('skuPrice', $skuPrice);
            $this->assignconfig('goodsList', $goodsList);
            $this->assignconfig('issku',$row['issku']);
            $this->assignconfig('type',$row['type']);
            $this->assignconfig('isdis',$row['isdis']);
            $this->assignconfig('disrule',$row['disrule']);
            $row['stock'] = $skuPrice[0]['stock'];
            $row['sn'] = $skuPrice[0]['sn'];
            $row['price'] = $skuPrice[0]['price'];
            $row['hourprice'] = $skuPrice[0]['hourprice'];
            $row['daysprice'] = $skuPrice[0]['daysprice'];
            $row['nightprice'] = $skuPrice[0]['nightprice'];
            $row['deposit'] = $skuPrice[0]['deposit'];
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $sku = $this->request->post("sku");

        $goodsList = [];
        if($row->type == 'package'){
            $goodsList = json_decode($params['goodslist'],true);
        }

        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $params['category_ids'] = implode(',',$params['category_ids']);
            $row->allowField(true)->save($params);
          

            if($row->type == 'package'){
                $this->editPackageItem($row,$goodsList,'edit');
            }

            if (($row->type == 'single' || $row->type == 'sell') && $params['issku']) {
                $result = $this->editMultSku($row, $sku, 'edit');
            } else {
                $result = $this->editSimSku($row, $sku, 'edit');
            }
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }


    /**
     * 单规格编辑库存
     */
    protected function editSimStock($goods,$nums){
        $goodsSkuPrice = SkuPrice::where('goods_id', $goods['id'])->order('id', 'asc')->find();

        if($goodsSkuPrice['stock'] + $nums < 0){
            throw new ValidateException('库存不能小于0');
        }

        //更新库存
        StockService::update($goodsSkuPrice->id,$nums,$this->auth->id,'sysedit');

    }

    /**
     * 添加编辑单规格
     */
    protected function editSimSku($goods, $sku, $type = 'add') {
        $params = $this->request->post("row/a");

        $skuData = json_decode($sku, true);
        $skuPrice = json_decode($skuData['priceData'], true);

        $data = [
            "goods_id" => $goods['id'],
            'type'  => $goods['type'],
            //"stock" => isset($params['stock']) ? $params['stock'] : 0,
            "sn" => isset($params['sn']) ? $params['sn'] : '',
            "price" => isset($params['price']) ? $params['price'] : 0,
            "hourprice" => isset($params['hourprice']) ? $params['hourprice'] : 0,
            "daysprice" => isset($params['daysprice']) ? $params['daysprice'] : 0,
            "nightprice" => isset($params['nightprice']) ? $params['nightprice'] : 0,
            "deposit" => isset($params['deposit']) ? $params['deposit'] :0,
            "status" => $params['status']
        ];

        if ($type == 'add') {
            $goodsSkuPrice = new SkuPrice();
            $result = $goodsSkuPrice->create($data);
            if($params['isdis'] && $params['disrule']){
                $commissionRule = $skuPrice[0]['commissionrule'];
                foreach($commissionRule as &$cr){
                    $cr['id'] = $result->id;
                }
                $result->save(['commissionrule'=>json_encode($commissionRule)]);
            }

            //更新库存
            $stock = isset($params['stock']) ? $params['stock'] : 0;
            if($stock > 0){
                StockService::update($result->id,$stock,$this->auth->id);
            }
            
        } else {
            $goodsSkuPrice = SkuPrice::where('goods_id', $goods['id'])->order('id', 'asc')->find();
            if($params['isdis'] && $params['disrule']){
                $data['commissionrule'] = json_encode($skuPrice[0]['commissionrule']) ;
            }
            $goodsSkuPrice->save($data);
        }

        return true;

    }


    /**
	 * 选择
	 */
	public function select()
	{
	    if ($this->request->isAjax()) {
	        return $this->index('');
	    }
	    return $this->view->fetch();
	}

    /**
     * 删除
     */
    public function del($ids = null)
    {
        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ?: $this->request->post("ids");
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = $this->model->where($pk, 'in', $ids)->select();

        $count = 0;
        Db::startTrans();
        try {
            foreach ($list as $item) {
                $count += $item->delete();
                Sku::where(['goods_id'=>$item->id])->delete();
                SkuPrice::where(['goods_id'=>$item->id])->delete();
                PackgeItem::where(['package_id'=>$item->id])->delete();
            }

            Db::commit();
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were deleted'));
    }

}