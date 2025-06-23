<?php

namespace app\api\controller\xylease;
use app\common\controller\Api;

use app\api\model\xylease\Share as ShareModel;

class Share extends Api
{

    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    public function add()
    {
        $spm = $this->request->post('spm');
        if (!empty($spm)) {
            $share = \think\Db::transaction(function () use ($spm) {
                try {
                    $shareLog = ShareModel::add($spm);
                    if ($shareLog) {
                        \think\Hook::listen('xylease_share_after', $shareLog);
                    }
                    return true;
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                return false;
            });
        }
        $this->success('分享成功',$share); 
    }
}
