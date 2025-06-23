<?php

namespace app\api\model\xylease\user;

use think\Model;
use app\api\model\xylease\user\User;

class View extends Model
{

    // 表名
    protected $name = 'xylease_user_view';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];

    // 添加浏览记录
    public static function addView($model,$type = null) {
        $user = User::info();
        if($user) {
            $view = self::where([
                'type' => $type,
                'user_id' => $user->id,
                'target_id' => $model->id
            ])->find();
            if ($view) {
                $view->updatetime = time();
                $view->save();
            } else {
                self::create([
                    'user_id' => $user->id,
                    'type'    => $type,
                    'target_id' => $model->id
                ]);
            }
        }
        $model->setInc('views');
    }

    
    public function getTypeList()
    {
        return ['goods' => __('Goods')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


}
