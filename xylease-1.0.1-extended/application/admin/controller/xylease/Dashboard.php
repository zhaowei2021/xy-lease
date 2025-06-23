<?php

namespace app\admin\controller\xylease;

use app\common\controller\Backend;
use app\admin\model\xylease\order\Order;
use app\admin\model\xylease\goods\Goods;
use app\admin\model\xylease\user\User;
use app\admin\model\xylease\Staff;
use app\admin\model\xylease\recharge\Order as RechargeOrder;

/**
 * 控制台
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {

        $this->view->assign([
            'total1'        => User::sum('xylease_consume'), //总消费
            'total2'        => User::sum('xylease_recharge'), //总充值
            'total3'        => User::count(), //会员数
            'total4'        => Staff::count(), //员工数
            'total5'        => Order::where(['status'=>['in',[1,2]]])->sum('totalfee'), //待退押金
            'total6'        => Goods::count(), //商品数
        ]);

        //今日起始时间戳
        $beginToday = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $endToday = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")) - 1;
        //本月起始时间戳
        $beginThismonth = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $endThismonth = mktime(23, 59, 59, date("m"), date("t"), date("Y"));

        //租赁订单数据
        $orderTotalMoney = Order::where(['status'=>['>',0]])->sum('totalfee');
        $orderTotalNum = Order::where(['status'=>['>',0]])->count();
        $orderTodayMoney = Order::where(['status'=>['>',0],'createtime' => ['between', [$beginToday, $endToday]]])->sum('totalfee');
        $orderTodayNum = Order::where(['status'=>['>',0],'createtime' => ['between', [$beginToday, $endToday]]])->count();
        $orderMonthMoney = Order::where(['status'=>['>',0],'createtime' => ['between', [$beginThismonth, $endThismonth]]])->sum('totalfee');
        $orderMonthNum = Order::where(['status'=>['>',0],'createtime' => ['between', [$beginThismonth, $endThismonth]]])->count();

        $this->view->assign("orderTotalMoney", $orderTotalMoney);
        $this->view->assign("orderTotalNum", $orderTotalNum);
        $this->view->assign("orderTodayMoney", $orderTodayMoney);
        $this->view->assign("orderTodayNum", $orderTodayNum);
        $this->view->assign("orderMonthMoney", $orderMonthMoney);
        $this->view->assign("orderMonthNum", $orderMonthNum);
        
        //充值订单数据
        $rechargeTotalMoney = RechargeOrder::where(['status'=>1])->sum('totalfee');
        $rechargeTotalNum = RechargeOrder::where(['status'=>1])->count();
        $rechargeTodayMoney = RechargeOrder::where(['status'=>1,'createtime' => ['between', [$beginToday, $endToday]]])->sum('totalfee');
        $rechargeTodayNum = RechargeOrder::where(['status'=>1,'createtime' => ['between', [$beginToday, $endToday]]])->count();
        $rechargeMonthMoney = RechargeOrder::where(['status'=>1,'createtime' => ['between', [$beginThismonth, $endThismonth]]])->sum('totalfee');
        $rechargeMonthNum = RechargeOrder::where(['status'=>1,'createtime' => ['between', [$beginThismonth, $endThismonth]]])->count();

        $this->view->assign("rechargeTotalMoney", $rechargeTotalMoney);
        $this->view->assign("rechargeTotalNum", $rechargeTotalNum);
        $this->view->assign("rechargeTodayMoney", $rechargeTodayMoney);
        $this->view->assign("rechargeTodayNum", $rechargeTodayNum);
        $this->view->assign("rechargeMonthMoney", $rechargeMonthMoney);
        $this->view->assign("rechargeMonthNum", $rechargeMonthNum);

        $weekDays = xyleaseGetWeeks();

        $orderDayTotalMoney = [];
        $orderDayTotalNum = [];
        $rechargeDayTotalMoney = [];
        $rechargeDayTotalNum = [];
        foreach($weekDays as $wd){
            // 营位订单
            $orderDayTotalMoney[] = Order::where(['createtime'=>['between', [strtotime(date('Y').'-'.$wd), strtotime(date('Y').'-'.$wd)+86400]]])->sum('totalfee');
            $orderDayTotalNum[] = Order::where(['createtime'=>['between', [strtotime(date('Y').'-'.$wd), strtotime(date('Y').'-'.$wd)+86400]]])->count();

            // 充值订单
            $rechargeDayTotalMoney[] = RechargeOrder::where(['createtime'=>['between', [strtotime(date('Y').'-'.$wd), strtotime(date('Y').'-'.$wd)+86400]]])->sum('totalfee');
            $rechargeDayTotalNum[] = RechargeOrder::where(['createtime'=>['between', [strtotime(date('Y').'-'.$wd), strtotime(date('Y').'-'.$wd)+86400]]])->count();

        }

        $this->assignconfig('weekDays',$weekDays);
        $this->assignconfig('orderDayTotalMoney',$orderDayTotalMoney);
        $this->assignconfig('orderDayTotalNum',$orderDayTotalNum);

        $this->assignconfig('rechargeDayTotalMoney',$rechargeDayTotalMoney);
        $this->assignconfig('rechargeDayTotalNum',$rechargeDayTotalNum);
        return $this->view->fetch();
    }


}
