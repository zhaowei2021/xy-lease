<?php

namespace app\api\model\xylease\user;

use think\Model;
use app\api\model\xylease\user\User;
use app\api\model\xylease\goods\Goods as GoodsModel;
use app\api\model\xylease\lease\Goods as LeaseGoodsModel;

class Favorite extends Model
{

    // 表名
    protected $name = 'xylease_user_favorite';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];

    
    /**
     * 添加｜删除
     */
    public static function add($params)
    {
        extract($params);
        $user = User::info();
        $favorite = self::get(['target_id' => $target_id, 'user_id' => $user->id,'type' => $type]);
        if ($favorite) {
            $favorite->delete();
            $favorite = null;
        }else{
           $favorite = self::create([
                'user_id' => $user->id,
                'target_id' => $target_id,
                'type'  => $type
            ]);
        }
        if($type == 'goods'){
            $goods = GoodsModel::get($target_id);
            if($favorite){
                $goods->setInc('favorite');
            }else{
                $goods->setDec('favorite');
            }
        }

        if($type == 'lease'){
            $goods = LeaseGoodsModel::get($target_id);
            if($favorite){
                $goods->setInc('favorite');
            }else{
                $goods->setDec('favorite');
            }
        }
        return $favorite;
    }

    /**
     * params 请求参数
     */
    public static function getLists($params)
    {
        extract($params);
        $user = User::info();
        $list = self::where(['user_id'=>$user->id])->order('id desc')->paginate();

        foreach($list as &$item){
            $target = null;
            if($item['type'] == 'goods'){
                $target = GoodsModel::getDetail($item['target_id']); 
            }
            $item['target'] = $target;
        }

        return $list;
    }

    
    public function getTypeList()
    {
        return ['goods' => __('Goods')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }



}
