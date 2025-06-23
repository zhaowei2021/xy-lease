<?php

namespace app\api\model\xylease\user;

use think\Model;
use app\common\library\Auth;
use app\api\model\xylease\user\Money as UserMoneyModel;
use addons\xylease\exception\Exception;
use app\api\model\xylease\Config;

/**
 * 会员模型
 */
class User extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

   
    public static function info()
    {
        if (Auth::instance()->isLogin()) {
            return Auth::instance();
        }
        return null;
    }

    /**
     * 获取头像
     * @param   string $value
     * @param   array  $data
     * @return string
     */
    public function getAvatarAttr($value, $data)
    {
        if (!$value) {
            $config = Config::getValueByName('xylease');
            $value = $config['useravatar'];
        }

        return cdnurl($value, true);
    }

    /**
     * 变更会员余额
     * @param int    $money   余额
     * @param int    $user_id 会员ID
     * @param string $memo    备注
     */
    public static function money($money, $user_id, $type,$remark = '',$sid = 0)
    {

        // 判断金额
        if ($money == 0) {
            new Exception('请输入正确的金额');
        }

        $user = self::lock(true)->find($user_id);
        $before = $user->money;
        $after = function_exists('bcadd') ? bcadd($user->money, $money, 2) : $user->money + $money;

        // 只有后台操作，余额才可以是负值
        if ($after < 0 && !in_array($type, ['sys'])) {
            new Exception('可用余额不足');
        }

        //更新会员信息
        $user->save(['money' => $after]);
        
        //写入日志
        UserMoneyModel::create(['user_id' => $user_id,'type'=>$type, 'money' => $money, 'before' => $before, 'after' => $after,'type'=>$type, 'remark' => $remark,'service_id'=>$sid]);
   
        return true;
    }
    
    /**
     * 获取验证字段数组值
     * @param   string $value
     * @param   array  $data
     * @return  object
     */
    public function getVerificationAttr($value, $data)
    {
        $value = array_filter((array)json_decode($value, true));
        $value = array_merge(['email' => 0, 'mobile' => 0], $value);
        return (object)$value;
    }

    /**
     * 设置验证字段
     * @param mixed $value
     * @return string
     */
    public function setVerificationAttr($value)
    {
        $value = is_object($value) || is_array($value) ? json_encode($value) : $value;
        return $value;
    }
    
    /**
     * 分销商
     */
    public function distribution()
    {
        return $this->hasOne('\app\api\model\xylease\distribution\Distribution', 'user_id')->field('user_id,level_id,pid,status,createtime');
    }
   
}
