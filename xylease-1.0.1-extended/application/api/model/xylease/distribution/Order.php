<?php

namespace app\api\model\xylease\distribution;

use think\Model;
use app\api\model\xylease\user\User;
use app\api\model\xylease\order\Order as OrderModel;

class Order extends Model
{


    // 表名
    protected $name = 'xylease_distribution_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'paytype_text',
        'paytime_text',
        'platform_text',
        'status_text'
    ];

    public static function getLists($params)
    {
        extract($params);
        $user = User::info();
        $where = ['one_distribution_id|two_distribution_id'=>$user->id];
        if($status != 'all'){
            $where['status'] = $status;
        }
        $list = self::where($where)->order('id desc')->paginate();

        foreach($list as &$item){
            if($item['one_distribution_id'] == $user->id){
                $item['dis_level'] = 1;
            }else{
                $item['dis_level'] = 2;
            }
          
            if($item['ordertype'] == 'order'){
                $item['service_order'] = OrderModel::with(['item'])->find($item['service_order_id']);
            }
            
        }

        return $list;
    }

    
    public function getPayTypeList()
    {
        return ['wechat' => __('Wechat')];
    }

    public function getPlatformList()
    {
        return ['wxMiniProgram' => __('Wxminiprogram')];
    }

    public function getStatusList()
    {
        return [ '0' => __('未结算'), '1' => __('已结算'),'-2' => __('已取消'), '-1' => __('已退回')];
    }


    public function getPayTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytype']) ? $data['paytype'] : '');
        $list = $this->getPayTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPaytimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paytime']) ? $data['paytime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPlatformTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['platform']) ? $data['platform'] : '');
        $list = $this->getPlatformList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setPaytimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
