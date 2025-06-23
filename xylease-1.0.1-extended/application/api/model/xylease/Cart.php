<?php

namespace app\api\model\xylease;

use think\Model;
use app\api\model\xylease\user\User;
use app\api\model\xylease\goods\Goods;

/**
 * 购物车模型
 */
class Cart extends Model
{

    // 表名
    protected $name = 'xylease_cart';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $hidden = ['createtime', 'updatetime'];

    // 追加属性
    protected $append = [
    ];

    public static function getLists()
    {
        $user = User::info();
        // 已删除的商品从购物车中删除
        self::whereNotExists(function ($query) {
            $goodsTableName = (new Goods())->getQuery()->getTable();
            $tableName = (new self())->getQuery()->getTable();
            $query = $query->table($goodsTableName)->where($goodsTableName . '.id=' . $tableName . '.goods_id');
            return $query;
        })->where([
            'user_id' => $user->id
        ])->delete();

        $cartData = self::with(['goods','sku_price'])->where(['user_id' => $user->id])->select();

        foreach ($cartData as $key => &$cart) {
            if ($cart['goods']['status'] === 'down' || empty($cart['sku_price'])) {
                $cart['cart_type'] = 'invalid';
            }
        }

        return $cartData;
    }

    public static function add($goodsList)
    {

        $user = User::info();

        foreach ($goodsList as $v) {
            $where = [
                'user_id' => $user->id,
                'goods_id' => $v['goods_id'],
                'sku_price_id' => $v['sku_price_id'],
            ];
            $cart = self::get($where);
            if ($cart) {
                $cart->buynum += $v['buynum'];
                $cart->save();
            }else{
                $cartData = [
                    'user_id' => $user->id,
                    'goods_id' => $v['goods_id'],
                    'goodstype' => $v['goodstype'],
                    'buynum' => $v['buynum'],
                    'sku_price_id' => $v['sku_price_id']
                ];
                $cart = self::create($cartData);
            }

        }

        return $cart;

    }

    public static function edit($params)
    {
        extract($params);
        $user = User::info();
        $where['user_id'] = $user->id;
        switch ($act) {
            case 'del':
                self::where(['id'=>['in',$ids]])->delete();
                break;
            case 'change':
                foreach ($cart_list as $v) {
                    $where['id'] = $v;
                    self::where($where)->update(['goods_num' => $value]);
                }
                break;
        }

        return true;

    }

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }

    public function skuPrice()
    {
        return $this->hasOne('app\api\model\xylease\goods\SkuPrice', 'id', 'sku_price_id');
    }
    

}
