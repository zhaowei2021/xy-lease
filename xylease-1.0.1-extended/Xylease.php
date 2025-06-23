<?php

namespace addons\xylease;

use app\common\library\Menu;
use think\Addons;
use addons\xylease\service\Hook;

/**
 * 插件
 */
class XYlease extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = include ADDON_PATH . 'xylease' . DS . 'config' . DS . 'menu.php';
        Menu::create($menu);
        return true;
    }

    /**
     * 应用初始化
     */
    public function appInit()
    {
        // 公共方法
        require_once __DIR__ . '/function.php';
        // 注册行为事件
        Hook::register();
    }

    /**
     * 插件更新方法
     */
    public function upgrade()
    {
        $menu = include ADDON_PATH . 'xylease' . DS . 'config' . DS . 'menu.php';
        Menu::upgrade('xylease', $menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("xylease");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('xylease');
        return true;
    }
    

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('xylease');
        return true;
    }


}
