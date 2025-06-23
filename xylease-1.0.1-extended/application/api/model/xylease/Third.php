<?php

namespace app\api\model\xylease;

use think\Model;

/**
 * 第三方登录模型
 */
class Third extends Model
{

    // 表名
    protected $name = 'xylease_third';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
}
