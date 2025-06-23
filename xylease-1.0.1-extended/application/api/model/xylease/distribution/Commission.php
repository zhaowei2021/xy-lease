<?php

namespace app\api\model\xylease\distribution;

use think\Model;
use app\api\model\xylease\user\User;

class Commission extends Model
{
    

    // 表名
    protected $name = 'xylease_distribution_commission';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];
    
    public static function getLists($params)
    {
        extract($params);
        $user = User::info();
        $where = ['distribution_id'=>$user->id];
        $list = self::where($where)->order('id desc')->paginate();
        return $list;
    }

    
    public function getTypeList()
    {
        return [
            'apply_withdraw' => __('申请提现'), 
            'refuse_withdraw' => __('拒绝提现'), 
            'order' => __('订单结算'),
            'sys' => __('后台操作'),
            'bonus'=> __('每日分红'),
            'bonus_team'=> __('团队分红'),
            'bonus_team_proffer'=> __('贡献奖励'),
            'corich_commission'=> __('共富收益'),
        ];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


}
