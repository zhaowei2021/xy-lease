<?php

namespace app\api\model\xylease;
use app\api\model\xylease\user\User;
use addons\xylease\exception\Exception;
use app\api\model\xylease\user\Coupon as UserCouponModel;
use think\Model;
use think\Db;

class Coupon extends Model
{
    

    // 表名
    protected $name = 'xylease_coupon';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'validitytype_text',
        'startusetime_text',
        'endusetime_text',
        'endtime_text',
        'status_text'
    ];
    
    public static function getLists($params)
    {
        extract($params);

        $where = ['isshow'=>1];
        
        if($type != 'all'){
            $where['type'] = $type;
        }

        $list = self::where($where)->order('id desc')->paginate();

        return $list;
    }

    public static function receive($params){

        $user = User::info();
        extract($params);
        $couponInfo = self::where(['id'=>$id])->find();
        if(empty($couponInfo)){
            new Exception('优惠券不存在');
        }

        $userCouponCount = UserCouponModel::where(['coupon_id'=>$couponInfo['id'],'user_id'=>$user->id])->count();

        if($couponInfo['leadcount'] >= $couponInfo['count']){
            new Exception('此优惠券已领完');
        }

        if($userCouponCount >= $couponInfo['maxfetch']){
            new Exception('您只能领取'.$couponInfo['maxfetch'].'张此优惠券');
        }

        $result = Db::transaction(function () use ($user,$couponInfo) {
            
            $couponInfo->setInc('leadcount');

            $endtime = 0;
            if($couponInfo['validitytype'] == 0){
                $endtime = $couponInfo['endusetime'];
            }

            if($couponInfo['validitytype'] == 1){
                $endtime = time() + $couponInfo['fixedterm'] * 3600 * 24;
            }

            UserCouponModel::create([
                'user_id'  => $user->id,
                'coupon_id'  => $couponInfo['id'],
                'type'  => $couponInfo['type'],
                'name'  => $couponInfo['name'],
                'money'  => $couponInfo['money'],
                'atleast'  => $couponInfo['atleast'],
                'discount'  => $couponInfo['discount'],
                'gettype'  => 1,
                'endtime' => $endtime
            ]);
            return true;
        });


        return $result;
    }

    
    public function getTypeList()
    {
    return ['reward' => __('Type reward'), 'discount' => __('Type discount')];
    }

    public function getIsShowList()
    {
        return ['0' => __('Isshow 0'), '1' => __('Isshow 1')];
    }

    public function getValiditytypeList()
    {
        return ['0' => __('Validitytype 0'), '1' => __('Validitytype 1'), '2' => __('Validitytype 2')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Status normal'), 'hidden' => __('Status hidden')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    


    public function getValiditytypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['validitytype']) ? $data['validitytype'] : '');
        $list = $this->getValiditytypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStartusetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['startusetime']) ? $data['startusetime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndusetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endusetime']) ? $data['endusetime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['endtime']) ? $data['endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setStartusetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndusetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function store()
    {
        return $this->belongsTo('\app\api\model\xylease\store\Store', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
