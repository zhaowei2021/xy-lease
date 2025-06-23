<?php

namespace app\api\model\xylease;

use think\Model;
use app\api\model\xylease\user\User;
use app\api\model\xylease\distribution\Distribution;

/**
 * 分享模型
 */
class Share extends Model
{

    // 表名,不含前缀
    protected $name = 'xylease_share';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $hidden = [];
    // 追加属性
    protected $append = [];

    //分享页面
    protected $pages = ['首页','课程包详情'];
    //分享平台
    protected $platforms = ['微信小程序'];
    //分享方式
    protected $froms =['转发','海报'];

    

    public static function add($spm)
    {
        $user = User::info();
        $shareParams = [];
        $spm = explode('.', $spm);
        $shareParams['share_id'] = intval($spm[0]);
        $shareParams['user_id'] = $user->id;

        // 不能分享给自己
        if ($user->id == $shareParams['share_id']) {
            return false;
        }
        
        $shareUser = User::get($shareParams['share_id']);

        // 未找到分享用户
        if (empty($shareUser)) {
            return false;
        }
        
        // 分享页面
        $page = '首页';
        if(isset(self::$pages[$spm[1]-1])) {
            $page = self::$pages[$spm[1]-1];
        }
        $shareParams['page'] = $page;
        $shareParams['page_id'] = $spm[2];

        // 分享平台
        $platform = '微信小程序';
        if(isset(self::$platforms[$spm[3]-1])) {
            $platform = self::$platforms[$spm[3]-1];
        }
        $shareParams['platform'] = $platform;

        // 查询用户分享记录
        $userShare = self::where($shareParams)->find();
        if ($userShare) { 
            return false;
        }

        $shareParams['createtime'] = time();
        
        $share = self::create($shareParams);
        return $share;
    }


    /**
     * 查用户分享记录
     * @return object
     */
    public static function getShareLogByDistribution($userId)
    {
        return self::where([
            'user_id' => $userId,
        ])->whereExists(function ($query) {
            $distribution_table_name = (new Distribution())->getQuery()->getTable();
            return $query->table($distribution_table_name)->where('share_id=' . $distribution_table_name . '.user_id')->where('status', 'in', ['normal']);
        })->order('id desc')->find();
    }
}
