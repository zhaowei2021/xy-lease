<?php

namespace addons\xylease\service;

use app\api\model\xylease\user\User as UserModel;
use app\api\model\xylease\Staff as StaffModel;
use app\api\model\xylease\store\Store as StoreModel;

/**
 * 员工业务
 */
class Staff
{

    public $user;     // 用户
    public $staff;    // 员工
    public $store;     // 门店
    public $role;     // 请求角色
    public $roleArr = ['clerk' => '职员','boss' => '管理']; //所有角色

    // 员工状态
    const STATUS_NORMAL         = 'normal';       // 正常 
    const STATUS_NULL           =  NULL;          // 未成为员工
    const STATUS_HIDDEN         = 'hidden';       // 离职
    const STATUS_NO_PERMISSION  = 'nopermission';  // 没有权限


    public function __construct($user_id,$role = 'clerk')
    {
        $this->role = $role;
        $this->user = UserModel::where('id', $user_id)->field('id, nickname, username,mobile')->find();
        $this->store = StoreModel::get(1);
        $this->staff = StaffModel::where(['user_id'=>$user_id])->find();
    }

    // 获取员工状态
    public function getStaffStatus()
    {

        if (empty($this->staff)) {
            $staffStatus = self::STATUS_NULL;
        }else{
            $staffStatus = $this->staff->status;
            if($staffStatus == self::STATUS_NORMAL){
                $roleArr = explode(',',$this->staff['role']);
                if(!in_array($this->role,$roleArr)){
                    $staffStatus = self::STATUS_NO_PERMISSION;
                }
            }
        }

        $response = [
            'status' => $staffStatus,
            'msg'    => ''
        ];

        switch ($staffStatus) {
            case self::STATUS_NULL:
                $response['msg'] = '您不是门店的'.$this->roleArr[$this->role];
                break;
            case self::STATUS_HIDDEN:
                $response['msg'] = '您已离职';
                break;
            case self::STATUS_NO_PERMISSION:
                $response['msg'] = '您没有权限';
                break;
        }
        return $response;
    }
    
    
}
