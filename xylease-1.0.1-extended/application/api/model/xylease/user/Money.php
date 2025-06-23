<?php

namespace app\api\model\xylease\user;

use think\Model;
use app\api\model\xylease\user\User;

class Money extends Model
{
    

    // 表名
    protected $name = 'xylease_user_money';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    
    public function getTypeList()
    {
        return [
                    'recharge' => '在线充值',
                    'sys' => '后台操作', 
                    'pay_order' => '订单支付',
                    'apply_withdraw' => __('申请提现'), 
                    'refuse_withdraw' => __('拒绝提现'), 
                    'pay_withdraw' => __('提现打款'),
                    'return_order_deposit' => '订单退押金',
                ];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    

    /**
     * 列表
     */
    public static function getLists($params)
    {
        extract($params);
        $user = User::info();
        $list = self::where(['user_id'=>$user->id])->order('id desc')->paginate();
        return $list;
    }


}
