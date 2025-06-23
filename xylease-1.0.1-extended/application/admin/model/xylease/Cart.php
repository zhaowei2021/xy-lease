<?php

namespace app\admin\model\xylease;

use think\Model;
use app\api\model\xylease\user\User;

class Cart extends Model
{


    // 表名
    protected $name = 'xylease_cart';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    // 追加属性
    protected $append = [];


    public static  function add($goodsList)
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
                $cart->goods_num += $v['goods_num'];
                $cart->save();
            }else{
                $cartData = [
                    'user_id' => $user->id,
                    'goods_id' => $v['goods_id'],
                    'goods_num' => $v['goods_num'],
                    'sku_price_id' => $v['sku_price_id']
                ];
                $cart = self::create($cartData);
            }

        }

        return $cart;


    }


    /**
     * 写入数据
     * @access public
     * @param array      $data  数据数组
     * @param array|true $field 允许字段
     * @return $this
     */
    public static function create($data = [], $field = null)
    {
        $model = new static();
        if (!empty($field)) {
            $model->allowField($field);
        }
        $model->isUpdate(false)->save($data, []);
        return $model;
    }




    public function goods()
    {
        return $this->hasOne('Goods', 'id', 'goods_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function price()
    {
        return $this->hasOne('app\admin\model\xylease\goods\SkuPrice', 'id', 'sku_price_id', [], 'LEFT')->setEagerlyType(0);
    }
}
