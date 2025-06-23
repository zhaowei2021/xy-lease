<?php

namespace app\api\model\xylease\goods;

use think\Model;
use app\api\model\xylease\goods\Sku;
use app\api\model\xylease\goods\SkuPrice;
use app\api\model\xylease\goods\GoodsItem;
use app\api\model\xylease\user\User;

class Goods extends Model
{

    // 表名
    protected $name = 'xylease_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'issku_text',
        'status_text',
        'type_text',
        'stock',
    ];

    //列表动态隐藏字段
    public static $list_hidden = ['content','images','updatetime','updatetime'];

    /**
     * 详情
     */
    public static function getDetail($id)
    {
        $user = User::info();

        $detail = (new self)->where('id', $id)->with(['item','skuPrice','favorite' => function ($query) use ($user) {
            $user_id = empty($user) ? 0 : $user->id;
            return $query->where(['user_id' => $user_id]);
        }])->field('*,(sales + virtualsales) as total_sales')->find();

        if (!$detail || $detail->status === 'down') {
            return null;
        }
        
        $detail = $detail->append(['sku']);
        
        return $detail;
    }
    
    /**
     * 列表
     */
    public static function getLists($params)
    {
        extract($params);
        $where = [
            'status' => 'up'
        ];
        if (isset($order)) {
            $order = self::getListOrder($order);

        }else{
            $order = 'weigh desc';
        }
        if (isset($search) && $search !== '') {
            $where['name'] = ['like', "%$search%"];
        }

        if (isset($ids) && $ids !== '') {
            $order = 'field(id, ' . $ids . ')';
            $idsArray = explode(',', $ids);
            $where['id'] = ['in', $idsArray];
        }

        $category_ids = [];
        if (isset($category_id) && $category_id != 0) {
            $category_ids = Category::getCategoryIds($category_id);
        }

        $goods = self::where($where)->where(function ($query) use ($category_ids) {
            foreach($category_ids as $key => $category_id) {
                $category_id = xyleaseFilterSql($category_id);
                $query->whereOrRaw("find_in_set($category_id, category_ids)");
            }
        })->with(['skuPrice']);

        $order = xyleaseFilterSql($order);
        $goods = $goods->field('*,(sales + virtualsales) as total_sales')->orderRaw($order)->order('id desc');

        $cacheKey = 'goodslist-' . (isset($page) ? 'page' : 'all') . '-' . md5(json_encode($params));

        // 判断缓存
        $goodsCache = cache($cacheKey);
        if ($goodsCache) {
            // 存在缓存直接 返回
            $goodsCache = json_decode($goodsCache, true);
            return $goodsCache ? : [];
        } 

        if (isset($page)) {
            $goods = $goods->paginate();
            $goodsData = $goods->items();
        } else {
            $goods = $goodsData = $goods->select();
        }

        $data = [];
        if ($goodsData) {
            $collection = collection($goodsData);
            $data = $collection->hidden(self::$list_hidden);
        }

        if (isset($page)) {
            $goods->data = $data;
        } else {
            $goods = $data;
            cache($cacheKey, json_encode($goods), (600 + mt_rand(0, 300)));
        }

        return $goods;
    }

    public function getTypeList()
    {
        return ['single' => __('Type single'),  'package' => __('Type package'),'sell' => __('Type Sell'),'service' => __('Type Service')];
    }
    
    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    //列表排序
    private static function getListOrder($orderStr)
    {
        $order = 'weigh desc';
        $orderList = json_decode(htmlspecialchars_decode($orderStr), true);
        extract($orderList);
        if (isset($defaultOrder) && $defaultOrder === 1) {
            $order = 'weigh desc';
        }
        if (isset($salesOrder) && $salesOrder === 1){
            $order = 'sales desc';
        }
        return $order;

    }

    protected function getSkuAttr($value, $data)
    {
        $sku = Sku::all([
            'goods_id'=>$data['id'],
            'pid' => 0,
        ]);
        foreach ($sku as $s => &$k) {
            $sku[$s]['content'] = Sku::all([
                'goods_id' => $data['id'],
                'pid' => $k['id'],
            ]);
        }
        return $sku;
    }

    

    public function getStockAttr($value, $data)
    {
        return SkuPrice::where([
            'goods_id' => $data['id'],
            'status' => 'up',
        ])->sum('stock');
    }

    public function getImageAttr($value, $data)
    {
        if (!empty($value)) return cdnurl($value, true);

    }

    


    public function getImagesAttr($value, $data)
    {
        $imagesArray = [];
        if (!empty($value)) {
            $imagesArray = explode(',', $value);
            foreach ($imagesArray as &$v) {
                $v = cdnurl($v, true);
            }
            return $imagesArray;
        }
        return $imagesArray;
    }

    public function getTagsAttr($value, $data)
    {
        return explode(',', $value);
    }
    
    public function getIsSkuList()
    {
        return ['0' => __('单规格'), '1' => __('多规格')];
    }

    public function getStatusList()
    {
        return ['up' => __('Status up'),  'down' => __('Status down')];
    }


    public function getIsSkuTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['issku']) ? $data['issku'] : '');
        $list = $this->getIsSkuList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getContentAttr($value, $data)
    {
        $content = $data['content'];
        $content = str_replace("<img src=\"/uploads", "<img src=\"" . cdnurl("/uploads", true), $content);
        $content = str_replace("<video src=\"/uploads", "<video src=\"" . cdnurl("/uploads", true), $content);
        $content = str_replace("<img src=\"/assets", "<img src=\"" . cdnurl("/assets", true), $content);
        $content = str_replace("<video src=\"/assets", "<video src=\"" . cdnurl("/assets", true), $content);
        return $content;

    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function favorite()
    {
        return $this->hasOne('\app\api\model\xylease\user\Favorite', 'target_id', 'id')->where(['type'=>'goods']);
    }

    public function skuPrice()
    {
        return $this->hasMany(SkuPrice::class, 'goods_id', 'id')->order('id', 'asc');
    }

    public function item()
    {
        return $this->hasMany(GoodsItem::class, 'package_id');
    }

}
