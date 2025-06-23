<?php

// 钩子
$hooks = [


  //注册后
  'xylease_register_after' => [            
    'addons\xylease\listener\Distribution'
  ],

  //分享后
  'xylease_share_after' => [            
    'addons\xylease\listener\Distribution'
  ],

  //订单创建成功后
  'xylease_order_create_after' => [            
    'addons\xylease\listener\Order',
  ],

  //订单取消|关闭后
  'xylease_order_cancel_after' => [            
    'addons\xylease\listener\Order',
  ],

  //订单归还
  'xylease_order_back_after' => [            
    'addons\xylease\listener\Order',
  ],

  //订单支付成功后
  'xylease_order_payed_after' => [            
    'addons\xylease\listener\Order',
    'addons\xylease\listener\Distribution'
  ]
  
];

return $hooks;
