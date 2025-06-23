<?php

namespace app\api\model\xylease\user;

use think\Model;
use app\api\model\xylease\user\User;
use app\api\model\xylease\Config;
use addons\xylease\exception\Exception;
use addons\xylease\service\distribution\Distribution as DistributionService;
use addons\xylease\service\Coach as CoachService;
use addons\xylease\service\Store as StoreService;
use app\api\model\xylease\user\Account;
use think\Db;

class Withdraw extends Model
{


    // 表名
    protected $name = 'xylease_user_withdraw';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text',
        'audit_time_text',
        'payment_time_text',
        'status_text'
    ];

    /**
     * 初始化用户提现信息
     */

    public static function getWithdrawInit($params)
    {
        
        extract($params);
        $response = [
            'status' => 1,
            'data'   => null,
            'msg'    => ''
        ];
        $config = Config::getValueByName('withdraw');
        $user = User::info();
        $account = Account::where(['user_id'=>$user->id,'is_default'=>1])->find();
        
        switch ($type) {
            case 'distribution':
                $distributionService =  new DistributionService($user->id);
                $distributionInfo = $distributionService->distribution;
                $response['status'] = $distributionInfo['status'];
                $response['msg'] = '';
                $config['able'] = $distributionInfo['commission'];
                $config['account'] = $account;
                $response['data'] = $config;
                break;
            case 'balance':
                $config['able'] = $user->money;
                $config['text'] = '余额';
                $config['account'] = $account;
                $response['status'] = 'normal';
                $response['msg'] = '申请提现';
                $response['data'] = $config;
                break;
            default :
                new Exception('不支持该提现类型');
                break;
            
        }

        return $response;
       
    }

    /**
     * 列表
     */
    public static function getList($params)
    {
        extract($params);
        $user = User::info();

        $where = ['user_id'=>$user->id,'type'=>$type];
        if(isset($store_id)){
            $where['store_id'] = $store_id;
        }

        $list = self::where($where)->order('id desc')->paginate();
        return $list;
    }

    /**
     * 添加
     */
    public static function add($params){
       
        $user = User::info();

        //提现配置获取手续费
        $config = Config::getValueByName('withdraw');

        //提现金额
        $applyMoney = $params['applymoney'];

        if($applyMoney <= 0){
            new Exception('提现金额要大于0');
        }

        if($applyMoney < $config['min']){
            new Exception('提现金额不能低于'.$config['min']);
        }

        if($applyMoney > $config['max']){
            new Exception('单次提现金额不能大于'.$config['max']);
        }

        //手续费比例
        $rate = $config['rate'];

        //手续费
        $serviceMoney = $applyMoney * $rate / 100;

        //实际转账金额
        $money = $applyMoney - $serviceMoney;

        $withdrawData['user_id'] = $user->id;
        $withdrawData['withdrawsn'] = xyleaseCreateOrderNo();
        $withdrawData['type'] = $params['type'];
        $withdrawData['applymoney'] = $applyMoney;
        $withdrawData['rate'] = $rate;
        $withdrawData['servicemoney'] = $serviceMoney;
        $withdrawData['money'] = $money;
        $withdrawData['realname'] = $params['realname'];
        $withdrawData['mobile'] = $params['mobile'];
        $withdrawData['accounttype'] = $params['accounttype'];
        $withdrawData['accountname'] = $params['accountname'];
        $withdrawData['accountno'] = $params['accountno'];

        $withdraw = Db::transaction(function () use ($user,$withdrawData) {
            
            //创建提现订单
            $withdraw = self::create($withdrawData);
            switch ($withdrawData['type']) {
                case 'distribution':
                    //分销商佣金变更
                    $distribution =  new DistributionService($withdrawData['user_id']);
                    $distribution->commission(-$withdrawData['applymoney'],'apply_withdraw',$withdraw->id);
                    break;
                case 'balance' :
                    //余额变更
                    User::money(-$withdrawData['applymoney'],$user->id,'apply_withdraw','',$withdraw->id);
                    break;
            }

            return true;
        });

        return $withdraw;

    }
    

    
    public function getTypeList()
    {
        return ['balance' => __('余额提现'), 'distribution' => __('分销商提现')];
    }

    public function getStatusList()
    {
        return ['0' => __('待审核'),'1' => __('待转账'),'2' => __('已转账'),'-1' => __('已拒绝'),'-2' => __('转账失败')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAuditTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['audit_time']) ? $data['audit_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPaymentTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['payment_time']) ? $data['payment_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setAuditTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPaymentTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
