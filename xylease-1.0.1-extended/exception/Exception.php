<?php
namespace addons\xylease\exception;

use think\Response;

class Exception
{
    public function __construct($msg = '', $code = 0)
    {
        $this->error($msg,$code);
    }

    protected function error($msg,$code)
    {
        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => null,
            'time' => time()
        ];
        $response = Response::create($data, 'json', $code);
        throw new \think\exception\HttpResponseException($response);
    }
}
