<?php

namespace app\api\model\xylease\user;

use think\Model;
use app\api\model\xylease\user\User;

class Address extends Model
{

    // 表名
    protected $name = 'xylease_user_address';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [];

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

    /**
     * 添加
     */
    public static function add($params){

        extract($params);
       
        $user = User::info();

        if(isset($id) && $id > 0){
            $accont = self::get($id);
        }else{
            $accont = new self();
        }

        $accountData['user_id'] = $user->id;
        $accountData['consignee'] = $params['consignee'];
        $accountData['mobile'] = $params['mobile'];
        $accountData['city'] = $params['city'];
        $accountData['address'] = $params['address'];

        if(!self::where(['user_id'=>$user->id,'is_default'=>1])->count()){
            $accountData['is_default'] = 1;
        }

        return $accont->allowField(true)->save($accountData);

    }

    /**
     * 详情
     */
    public static function getDetail($id)
    {
        $user = User::info();
        return self::where(['id'=>$id,'user_id'=>$user->id])->find();
    }

    /**
     * 删除
     */
    public static function del($id)
    {
        $user = User::info();
        return self::get(['id' => $id, 'user_id' => $user->id])->delete();
    }

    /**
     * 设置默认
     */
    public static function setDefault($id){
        $user = User::info();
        self::where(['user_id' => $user->id, 'is_default' => '1'])->update(['is_default' => '0']);
        self::where(['user_id' => $user->id, 'id' => $id])->update(['is_default' => '1']);
        return true;
    }

}
