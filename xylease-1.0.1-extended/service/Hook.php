<?php

namespace addons\xylease\service;

class Hook
{

    public static function register ($behaviors = []) {
        $default = require ROOT_PATH . 'addons/xylease/hooks.php';

        $behaviors = array_merge($default, $behaviors);
        foreach ($behaviors as $tag => $behavior) {
            $behavior = array_reverse($behavior);
            foreach ($behavior as $be) {
                \think\Hook::add($tag, $be, true);
            }
        }
    }
}
