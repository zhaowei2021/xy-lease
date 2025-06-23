<?php

namespace addons\xylease\service\distribution;

use app\api\model\xylease\Config;
use app\api\model\xylease\user\User as UserModel;
use app\api\model\xylease\distribution\Distribution as DistributionModel;
use app\api\model\xylease\distribution\Level as LevelModel;
use app\api\model\xylease\Share;
use app\api\model\xylease\distribution\Commission;
use addons\xylease\exception\Exception;
use think\Db;

/**
 * 分销商业务
 */
class Distribution
{

    public $user;     // 用户
    public $distribution;    // 分销商用户
    public $config;    // 分销设置
    public $pid;     //上级ID

    // 分销商状态
    const DISTRIBUTION_STATUS_NORMAL = 'normal';       // 正常 
    const DISTRIBUTION_STATUS_FORBIDDEN = 'forbidden'; // 禁用
    const DISTRIBUTION_STATUS_NULL = NULL;             // 不是分销商

    public function __construct($user_id)
    {
       
        $this->user = UserModel::where('id', $user_id)->field('id, nickname,mobile, xylease_parent_user_id,xylease_consume')->find();
        $this->config = Config::getValueByName('distribution');
        $this->distribution = DistributionModel::get($user_id);

        //人人分销
        if(empty($this->distribution) && $this->config['isdis'] == 1){
            $this->becomeDistribution();
        }
    }

    // 获取分销商状态
    public function getDistributionStatus()
    {
        $distributionStatus = 'normal';
        
        if (empty($this->distribution)) {
            $distributionStatus = self::DISTRIBUTION_STATUS_NULL;
        }else{
            $distributionStatus = $this->distribution->status;
        }

        $response = [
            'status' => $distributionStatus,
            'msg'    => ''
        ];

        switch ($distributionStatus) {
            case self::DISTRIBUTION_STATUS_FORBIDDEN:
                $response['msg'] = '您的分销权限被禁用。';
                break;
            case self::DISTRIBUTION_STATUS_NULL:
                $response['msg'] = '您不是分销商';
                break;
        }
        return $response;
    }

    /**
     * 获取团队数量
     */
    public function getTeamNum()
    {
        
        $oneIds = UserModel::where(['xylease_parent_user_id' => $this->user->id])->column('id');
        $twoIds = [];
        if(count($oneIds) > 0){
            $twoIds = UserModel::where(['xylease_parent_user_id'=>['in',$oneIds] ])->column('id');
        }

        return [
            'one_child_num' => count($oneIds),
            'two_child_num' => count($twoIds),
            'all_num' => count($oneIds)+count($twoIds)
        ];
    }

    // 我的团队
    public function getTeamList($params){

        extract($params);
        
        if(!isset($level)){
            $level = 1;
        }

        $ids = [];
        switch ( $level ) {
            case 1:
                $ids[] = $this->user->id;
                break;
            case 2:
                $ids = UserModel::where(['xylease_parent_user_id' => $this->user->id])->column('id');
                if(count($ids) == 0){
                    $ids = [-1];
                }
                break;
        }

        $fields = 'id,nickname,avatar,xylease_parent_user_id,xylease_consume,createtime';
        $teams = UserModel::where(['xylease_parent_user_id'=>['in',$ids] ])->with(['distribution' => function ($query) {
            $query->avaliable()->with(['level' => function ($query) {
                $query->field('id,name,image');
            }]);
        }])->field($fields)->order('createtime', 'asc')->paginate();


        return $teams;
    }

    /**
     * @name 变更分销商佣金
     * @param  float        $money      变更金额
     * @param  int|object   $distribution       分销商对象或ID
     * @param  string       $type       变更类型
     * @param  int          $service_id    业务ID
     * @param  string       $memo       备注
     * @return boolean
     */
    public function commission($money, $type = '', $service_id = 0,$islog = true){
        
        // 判断金额
        if ($money == 0) {
            new Exception('请输入正确的金额');
        }

        $before = $this->distribution->commission;
        $after = $this->distribution->commission;
        if($islog){
            $after += $money;
        }
        // 只有后台操作，余额才可以是负值
        if ($after < 0 && !in_array($type, ['sys'])) {
            new Exception('可用余额不足');
        }
        try {
            
            if($type == 'apply_withdraw'){
                //申请提现，变更提现中佣金
                $this->distribution->withdrawing_commission = $this->distribution->withdrawing_commission - $money;
            }else if($type == 'refuse_withdraw'){
                //拒绝提现，变更分销商佣金信息和提现中佣金
                $this->distribution->commission = Db::raw('commission + ' . $money);
                $this->distribution->withdrawing_commission = Db::raw('withdrawing_commission - ' . $money);
            }else if($type == 'pay_withdraw'){
                //提现打款，变更分销商提现中佣金和已提现佣金
                $this->distribution->withdrawn_commission = Db::raw('withdrawn_commission + ' . $money);
                $this->distribution->withdrawing_commission = Db::raw('withdrawing_commission - ' . $money);
            }else if($type == 'order'){
                //分销订单结算
                $this->distribution->total_commission = Db::raw('total_commission + ' . $money);
            }

            //变更佣金
            $this->distribution->commission = $after;
            $this->distribution->save();

            //佣金变更记录
            if($islog){
                (new Commission)::create([
                        'distribution_id' => $this->distribution->user_id,
                        'type'  => $type,
                        'money' => $money, 
                        'before' => $before, 
                        'after' => $after,
                        'service_id' => $service_id
                    ]
                );
            }
            
        } catch (\Exception $e) {
            new Exception('您提交的数据不正确');
        }

        return true;
    }

    

    // 获取分销商等级
    public function getDistributionLevel()
    {
        if (empty($this->distribution)) {
            return 0;
        }
        $level = LevelModel::get($this->distribution->level);
        if (!$level) {
            return 1;
        }
        return $level->level;
    }

    // 获取分销商等级信息
    public function getDistributionLevelInfo()
    {
        if (empty($this->distribution)) {
            return null;
        }
        $level = LevelModel::get($this->distribution->level_id);
        if (!$level) {
            return LevelModel::get(1);  // 找不到分销商等级,则默认为初级
        }
        return $level;
    }
    

    // 查询上级用户是否为分销商 是则返回分销商用户ID
    public function getParentDistributionId()
    {
        if ($this->pid === null) {
            $this->pid = 0;
            $xylease_parent_user_id = $this->user->xylease_parent_user_id;
            // 未绑定分销商,从分享记录查找最近的分销商
            if ($xylease_parent_user_id === 0) {
                $sharedLog = Share::getShareLogByDistribution($this->user->id);
                if ($sharedLog) {
                    $xylease_parent_user_id = $sharedLog['share_id'];
                }
            }
            if ($xylease_parent_user_id > 0) {
                // 再次检查上级分销商是否可用
                $parentDistribution = DistributionModel::avaliable()->where(['user_id' => $xylease_parent_user_id])->find();
                if ($parentDistribution) {
                    $this->pid = $parentDistribution->user_id;
                }
            }
        }

        return $this->pid;
    }

    /**
     * 成为分销商
     */
    public function becomeDistribution()
    {
        $distributionStatus = self::DISTRIBUTION_STATUS_NULL;

        // 无需成为分销商
        if ($this->getDistributionStatus()['status'] !== $distributionStatus) {
            return $this->getDistributionStatus();
        }

        $distributionStatus = self::DISTRIBUTION_STATUS_NORMAL;
        $pid = $this->getParentDistributionId();

        $defaultLevel = LevelModel::where([])->find();

        $levelId = empty($defaultLevel) ? 0 : $defaultLevel->id;

        $this->distribution = DistributionModel::create([
            'user_id' => $this->user->id,
            'level_id' => $levelId,
            'realname' => $this->user->nickname,
            'mobile' => $this->user->mobile,
            'pid' => $pid,
            'status' => $distributionStatus,
        ]);

        $this->user->xylease_parent_user_id = $pid;
        $this->user->save();
        
        return $distributionStatus;
    }

    // 获取分销商可参与状态
    public function isDistributionAvaliable()
    {
        $status = $this->getDistributionStatus();

        if (in_array($status['status'], [self::DISTRIBUTION_STATUS_NORMAL])) {
            return true;
        }
        return false;
    }


    /**
     * 绑定用户关系
     * @param string   $event          事件标识(share=点击分享链接, pay=首次支付)
     */
    public function bindUserRelation($event, $bindDistributionId = 0)
    {
        
        // 不满足绑定用户事件
        if ($this->config['child_condition'] !== $event) {
            return false;
        }

        // 该用户已经有上级
        if ($this->user->xylease_parent_user_id > 0) {
            return false;
        }
        
        
        if ($bindDistributionId) {
            $bindDistribution = new Distribution($bindDistributionId);
        } else {
            $sharedLog = Share::getShareLogByDistribution($this->user->id);
            if ($sharedLog) {
                $bindDistribution = new Distribution($sharedLog['share_id']);
            }
        }

        if (!isset($bindDistribution) || !$bindDistribution->isDistributionAvaliable()) {
            return false;
        }

        $bindCheck = false;   // 默认不绑定
        switch ($this->config['child_condition']) {
            case 'share':
                $bindCheck = true;
                break;
            case 'pay':
                if ($this->user->xylease_consume > 0) {
                    $bindCheck = true;
                }
                break;
        }

        // 允许绑定用户
        if ($bindCheck) {
            $this->user->xylease_parent_user_id = $bindDistribution->user->id;
            $this->user->save();


            if($this->distribution){
                $this->distribution->pid = $bindDistribution->user->id;
                $this->distribution->save();
            }

            return true;
        }
        return false;
    }

    
}
