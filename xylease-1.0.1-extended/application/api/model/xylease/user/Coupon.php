<?php

namespace app\api\model\xylease\user;
use addons\xylease\exception\Exception;
use app\api\model\xylease\user\User as UserModel;
use think\Db;
use think\Model;


class Coupon extends Model
{


    // 表名
    protected $name = 'xylease_user_coupon';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'gettype_text',
        'usetime_text',
        'endtime_text',
        'status_text'
    ];


    /**
     * 转增
     */
    public static function handsel($params){
        extract($params);
        
        $userCoupon = self::where(['id'=>$id])->find();

        $handUser = UserModel::where(['mobile'=>$mobile])->find();

        if(empty($handUser)){
            new Exception('会员不存在');
        }

        $user = UserModel::info();

        if($user->id == $handUser['id']){
            new Exception('不能转增给自己');
        }

        $result = Db::transaction(function () use ($userCoupon,$handUser,$user) {
            
            $userCoupon->status = 3;
            $userCoupon->save();

            self::create([
                'user_id'   => $handUser['id'],
                'store_id'   => $userCoupon['store_id'],
                'coupon_id'   => $userCoupon['coupon_id'],
                'type'   => $userCoupon['type'],
                'name'   => $userCoupon['name'],
                'money'   => $userCoupon['money'],
                'atleast'   => $userCoupon['atleast'],
                'discount'   => $userCoupon['discount'],
                'gettype'   => 3,
                'handuserid'   => $user->id,
            ]);

            return true;
        });


        return $result;


    }

    /**
     * 列表
     */
    public static function getLists($params)
    {
        extract($params);
        $where = ['user_id'=>$user_id];
        $order = 'id desc';
        if($status == 2){
            $where['endtime'] = ['<',time()];
        }else{
            $where['status'] = $status;
            $where['endtime'] = ['>',time()];
        }

        if(isset($atleast) && $atleast>0){
            $where['atleast'] = ['<=',$atleast];
            $order = 'money desc,discount desc';
        }

        $list = self::where($where)->order($order)->paginate();
        return $list;
    }
    
    public function getTypeList()
    {
        return ['reward' => __('Type reward'), 'discount' => __('Type discount')];
    }

    public function getIshandselList()
    {
        return ['0' => __('Ishandsel 0'), '1' => __('Ishandsel 1')];
    }
    

    public function getGettypeList()
    {
        return ['1' => __('Gettype 1'), '2' => __('Gettype 2'), '3' => __('Gettype 3')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '3' => __('Status 3')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getGettypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['gettype']) ? $data['gettype'] : '');
        $list = $this->getGettypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getUsetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['usetime']) ? $data['usetime'] : '');
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

    protected function setUsetimeAttr($value)
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

    public function user()
    {
        return $this->belongsTo('\app\api\model\xylease\user\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
