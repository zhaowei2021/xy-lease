<?php

namespace addons\xylease\service;

use EasyWeChat\Factory;
use app\api\model\xylease\Config;

class Wechat
{
    protected $config;
    protected $app;

    public function __construct($platform)
    {
        $this->setConfig($platform);
        switch ($platform) {
            case 'wxMiniProgram':
                $this->app    = Factory::miniProgram($this->config);
                break;
            case 'wxOfficialAccount':
                $this->app    = Factory::officialAccount($this->config);
                break;
        }
    }

    public function getApp() {
        return $this->app;
    }

    public function code($code)
    {
        return $this->app->auth->session($code);
    }

    public function buildConfig($jssdk, $jsApiList, $debug = false, $beta = false, $json = false, $openTagList = [], $url = '')
    {
        $url = $url ?: $jssdk->getUrl();
        $nonce = \EasyWeChat\Kernel\Support\Str::quickRandom(10);
        $timestamp = time();
        $signature = [
            'appId' => $this->config['app_id'],
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $jssdk->getTicketSignature($jssdk->getTicket()['ticket'], $nonce, $timestamp, $url),
        ];
        $config = array_merge(compact('debug', 'beta', 'jsApiList', 'openTagList'), $signature);
        return $json ? json_encode($config) : $config;
    }

    private function setConfig($platform) {
        $debug = config('app_debug');
        $defaultConfig = [
            'log' => [
                'default' => $debug ? 'dev' : 'prod', 
                'channels' => [
                    'dev' => [
                        'driver' => 'single',
                        'path' => '/tmp/easywechat.log',
                        'level' => 'debug',
                    ],
                    'prod' => [
                        'driver' => 'daily',
                        'path' => '/tmp/easywechat.log',
                        'level' => 'info',
                    ],
                ],
            ],
        ];

        // 获取对应平台的配置
        $this->config = Config::getValueByName($platform);

        // 合并配置
        $this->config = array_merge($this->config, $defaultConfig);
    }
}
