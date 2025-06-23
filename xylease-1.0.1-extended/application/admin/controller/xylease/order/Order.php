<?php

namespace app\admin\controller\xylease\order;

use app\common\controller\Backend;
use think\Db;
use Exception;
use think\exception\DbException;
use think\exception\PDOException;
use app\api\model\xylease\order\Item as OrderItem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * 租赁订单
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\xylease\goods\Order
     */
    protected $model = null;
    protected $searchFields = 'ordersn,user.nickname';
    protected $noNeedRight = ['index'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xylease\order\Order;
        $this->view->assign("payTypeList", $this->model->getPayTypeList());
        $this->view->assign("platformList", $this->model->getPlatformList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }


    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $list = $this->model
            ->with(['item','user'])
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    //导出数据
    public function export()
    {
        if ($this->request->isPost()) {
            set_time_limit(0);
            $this->relationSearch = true;
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');
            
            $whereIds = $ids == 'all' ? '1=1' : ['order.id' => ['in', explode(',', $ids)]];
            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            //设置过滤方法
            $this->request->filter(['strip_tags']);
            
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['user'])
                ->where($whereIds)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THICK,
                    ],
                ],
            ];

            $sheet->getDefaultColumnDimension()->setWidth(20);// 列宽
            $sheet->getDefaultRowDimension()->setRowHeight(20);// 行高
            // 标题
            $tabletitle = '营订单';
            $sheet->mergeCells('A1:J1');
            $sheet->getRowDimension('1')->setRowHeight(40);// 行高
            $sheet->getStyle('A1')->applyFromArray($styleArray);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->setCellValue('A1', $tabletitle);
            
            //$sheet->getStyle('A2:J2')->applyFromArray($styleArray);
            //$sheet->getStyle('A2:J2')->getFont()->setBold(true)->setSize(12);
            $sheet->setCellValue('A2','订单号');
            $sheet->setCellValue('B2','联系人');
            $sheet->setCellValue('C2','联系电话');
            $sheet->setCellValue('D2','营位金额');
            $sheet->setCellValue('E2','押金金额');
            $sheet->setCellValue('F2','优惠金额');
            $sheet->setCellValue('G2','需付金额');
            $sheet->setCellValue('H2','实付金额');
            $sheet->setCellValue('I2','支付方式');
            $sheet->setCellValue('J2','订单状态');
            $sheet->setCellValue('K2','下单时间');
            $sheet->setCellValue('L2','客户备注');

            $sort = 0;
            foreach ($list as $v){
                $sheet->getCell('A' . ($sort + 3))->setValueExplicit($v['ordersn'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                $sheet->setCellValue('B' . ($sort + 3), $v['consignee']);
                $sheet->getCell('C' . ($sort + 3))->setValueExplicit($v['user']['mobile'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                $sheet->setCellValue('D' . ($sort + 3), $v['totalamount']);
                $sheet->setCellValue('E' . ($sort + 3), $v['totaldeposit']);
                $sheet->setCellValue('F' . ($sort + 3), $v['couponfee']);
                $sheet->setCellValue('G' . ($sort + 3), $v['totalfee']);
                $sheet->setCellValue('H' . ($sort + 3), $v['payfee']);
                $sheet->setCellValue('I' . ($sort + 3), $v['paytype_text']);
                $sheet->setCellValue('J' . ($sort + 3), $v['status_text']);
                $sheet->setCellValue('K' . ($sort + 3), date("Y-m-d h:s",$v['createtime']));
                $sheet->setCellValue('L' . ($sort + 3), $v['remark']);
                $sort++;
            }
            // 工作簿标题
            $sheettitle = '会员卡订单';
            $sheet->setTitle($sheettitle);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.$sheettitle.'.xlsx');
            header('Cache-Control: max-age=0');

            $write = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $write->save('php://output');

            return;
        }
    }

    /**
     * 删除
     */
    public function del($ids = null)
    {
        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ?: $this->request->post("ids");
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = $this->model->where($pk, 'in', $ids)->select();

        $count = 0;
        Db::startTrans();
        try {
            foreach ($list as $item) {
                $count += $item->delete();
                OrderItem::where(['order_id'=>$item->id])->delete();
            }

            Db::commit();
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were deleted'));
    }

}
