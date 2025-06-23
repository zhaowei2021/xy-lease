<?php

$menu = [
    [
        'name'    => 'xylease',
        'title'   => 'XYlease租赁',
        'icon'    => 'fa fa-th',
		'weigh'	  => '10000',
        'sublist' => [
            [
    		    'name'    => 'xylease/dashboard/index',
    		    'title'   => '控制台',
    		    'icon'    => 'fa fa-dashboard',
    			'weigh'	  => '100',
                'ismenu' => 1, 
    		],
            [
                'name'    => 'xylease/store',
			    'title'   => '门店管理',
			    'icon'    => 'fa fa-columns',
				'weigh'	  => '99',
			    'sublist' => [
                    [
                        'name'    => 'xylease/store/store/info', 
                        'title'   => '门店信息', 
                        'icon'    => 'fa fa-align-justify',
                        'weigh'   => '100', 
                        'ismenu'  => 1, 
                    ],
                    [
                        'name'    => 'xylease/staff', 
                        'title'   => '门店员工',
                        'icon'    => 'fa fa-align-justify',
                        'weigh'   => '100', 
                        'ismenu'  => 1, 
                        'sublist' => [
                            ['name' => 'xylease/staff/index', 'title' => '查看'],
                            ['name' => 'xylease/staff/add', 'title' => '添加'],
                            ['name' => 'xylease/staff/edit', 'title' => '编辑'],
                            ['name' => 'xylease/staff/del', 'title' => '删除'],
                            ['name' => 'xylease/staff/multi', 'title' => '批量更新'],
                        ],
                    ],
			        [
                        'name'    => 'xylease/notice', 
                        'title'   => '通知公告',
                        'icon'    => 'fa fa-align-justify',
                        'weigh'   => '100', 
                        'ismenu'  => 1, 
                        'sublist' => [
                            ['name' => 'xylease/notice/index', 'title' => '查看'],
                            ['name' => 'xylease/notice/add', 'title' => '添加'],
                            ['name' => 'xylease/notice/edit', 'title' => '编辑'],
                            ['name' => 'xylease/notice/del', 'title' => '删除'],
                            ['name' => 'xylease/notice/multi', 'title' => '批量更新'],
                        ],
                    ],
                    [
                        'name'    => 'xylease/article', 
                        'title'   => '文章管理',
                        'icon'    => 'fa fa-align-justify',
                        'weigh'   => '100', 
                        'ismenu'  => 1, 
                        'sublist' => [
                            ['name' => 'xylease/article/index', 'title' => '查看'],
                            ['name' => 'xylease/article/add', 'title' => '添加'],
                            ['name' => 'xylease/article/edit', 'title' => '编辑'],
                            ['name' => 'xylease/article/del', 'title' => '删除'],
                            ['name' => 'xylease/article/multi', 'title' => '批量更新'],
                        ],
                    ]
                ]
            ],
            [
                'name'    => 'xylease/user',
                'title'   => '会员管理',
                'icon'    => 'fa fa-user-circle',
                'weigh'	  => '98',
                'sublist' => [
                    [
                        'name' => 'xylease/user/user', 
                        'title' => '会员列表', 
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/user/user/index', 'title' => '查看'],
                            ['name' => 'xylease/user/user/add', 'title' => '添加'],
                            ['name' => 'xylease/user/user/edit', 'title' => '编辑'],
                            ['name' => 'xylease/user/user/multi', 'title' => '批量更新'],
                            ['name' => 'xylease/user/user/import', 'title' => '导入'],
                        ]
                    ],
                    [
                        'name'    => 'xylease/user/coupon', 
                        'title'   => '会员优惠券', 
                        'icon'    => 'fa fa-align-justify',
                        'weigh'   => '100', 
                        'ismenu'  => 1, 
                        'sublist' => [
                            ['name' => 'xylease/user/coupon/index', 'title' => '查看'],
                            ['name' => 'xylease/user/coupon/add', 'title' => '赠券'],
                            ['name' => 'xylease/user/coupon/del', 'title' => '删除'],
                        ]
                    ]
                ]
            ],
            [
                'name'    => 'xylease/goods',
                'title'   => '商品管理',
                'icon'    => 'fa fa-window-restore',
                'weigh'	  => '97',
                'sublist' => [
                    [
                        'name' => 'xylease/goods/goods/index/type/single', 
                        'title' => '租赁商品', 
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/goods/goods/add/type/single', 'title' => '添加'],
                            ['name' => 'xylease/goods/goods/edit/type/single', 'title' => '编辑'],
                            ['name' => 'xylease/goods/goods/multi/type/single', 'title' => '批量更新'],
                            ['name' => 'xylease/goods/goods/del/type/single', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'xylease/goods/goods/index/type/package', 
                        'title' => '租赁套餐', 
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '99', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/goods/goods/add/type/package', 'title' => '添加'],
                            ['name' => 'xylease/goods/goods/edit/type/package', 'title' => '编辑'],
                            ['name' => 'xylease/goods/goods/multi/type/package', 'title' => '批量更新'],
                            ['name' => 'xylease/goods/goods/del/type/package', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'xylease/goods/category/index', 
                        'title' => '商品分类', 
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '98', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/goods/category/add', 'title' => '添加'],
                            ['name' => 'xylease/goods/category/edit', 'title' => '编辑'],
                            ['name' => 'xylease/goods/category/multi', 'title' => '批量更新'],
                            ['name' => 'xylease/goods/category/del', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'xylease/goods/stock_log/index', 
                        'title' => '库存记录', 
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '97', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/goods/stock_log/del', 'title' => '删除'],
                        ]
                    ]
                ]
            ],
            [
                'name'    => 'xylease/order',
                'title'   => '订单管理',
                'icon'    => 'fa fa-first-order',
                'weigh'	  => '96',
                'sublist' => [
                    
                    [
                        'name' => 'xylease/order/order/index', 
                        'title' => '租赁订单', 
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1, 
                        'sublist' => [
                            ['name' => 'xylease/order/order/del', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'xylease/recharge/order/index', 
                        'title' => '充值订单',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1, 
                        'sublist' => [
                            ['name' => 'xylease/recharge/order/del', 'title' => '删除'],
                        ]
                    ]
                ]
            ],
            [
                'name'    => 'xylease/market',
                'title'   => '营销管理',
                'icon'    => 'fa fa-tty',
                'weigh'	  => '95',
                'sublist' => [
                    
                    [
                        'name' => 'xylease/coupon', 
                        'title' => '优惠券', 
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1, 
                        'sublist' => [
                            ['name' => 'xylease/coupon/index', 'title' => '查看'],
                            ['name' => 'xylease/coupon/add', 'title' => '添加'],
                            ['name' => 'xylease/coupon/edit', 'title' => '编辑'],
                            ['name' => 'xylease/coupon/del', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'xylease/recharge/recharge', 
                        'title' => '充值套餐',
                        'icon'    => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1, 
                        'sublist' => [
                            ['name' => 'xylease/recharge/recharge/index', 'title' => '查看'],
                            ['name' => 'xylease/recharge/recharge/add', 'title' => '添加'],
                            ['name' => 'xylease/recharge/recharge/edit', 'title' => '编辑'],
                            ['name' => 'xylease/recharge/recharge/del', 'title' => '删除'],
                            ['name' => 'xylease/recharge/recharge/multi', 'title' => '批量更新'],
                        ]
                    ]
                ]
            ],
            [
                'name'    => 'xylease/distribution',
                'title'   => '分销管理',
                'icon'    => 'fa fa-sitemap',
                'weigh'	  => '94',
                'sublist' => [
                    [
                        'name' => 'xylease/distribution/distribution', 
                        'title' => '分销商',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/distribution/distribution/index', 'title' => '查看'],
                            ['name' => 'xylease/distribution/distribution/add', 'title' => '添加'],
                            ['name' => 'xylease/distribution/distribution/edit', 'title' => '编辑'],
                            ['name' => 'xylease/distribution/distribution/multi', 'title' => '批量更新'],
                            ['name' => 'xylease/distribution/distribution/del', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'xylease/distribution/level', 
                        'title' => '分销等级', 
                        'weigh' => '99', 
                        'ismenu' => 1, 
                        'sublist' => [
                            ['name' => 'xylease/distribution/level/index', 'title' => '查看'],
                            ['name' => 'xylease/distribution/level/add', 'title' => '添加'],
                            ['name' => 'xylease/distribution/level/edit', 'title' => '编辑'],
                            ['name' => 'xylease/distribution/level/del', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'xylease/distribution/order/index', 
                        'title' => '分销订单',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '98', 
                        'ismenu' => 1, 
                    ],
                    [
                        'name' => 'xylease/distribution/commission/index', 
                        'title' => '佣金明细',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '97', 
                        'ismenu' => 1, 
                    ],
                ]
            ],
            
            
            [
                'name'    => 'xylease/page',
                'title'   => '装修管理',
                'icon'    => 'fa fa-camera-retro',
                'weigh'	  => '91',
                'sublist' => [
                    [
                        'name' => 'xylease/page/index', 
                        'title' => '页面模板', 
                        'weigh' => '100', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/page/add', 'title' => '新建模板'],
                            ['name' => 'xylease/page/edit', 'title' => '装修'],
                            ['name' => 'xylease/page/use', 'title' => '发布'],
                            ["name" => "xylease/page/recyclebin", "title" => "回收站"],
                            ["name" => "xylease/page/restore", "title" => "还原"],
                            ["name" => "xylease/page/destroy", "title" => "真实删除"]
                        ]
                    ],
                    [
                        'name' => 'xylease/link/index', 
                        'title' => '页面链接', 
                        'weigh' => '99', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/link/add', 'title' => '新建模板'],
                            ['name' => 'xylease/link/edit', 'title' => '装修'],
                            ['name' => 'xylease/link/use', 'title' => '发布'],
                            ["name" => "xylease/link/del", "title" => "删除"]
                        ]
                    ],
                    [
                        'name' => 'xylease/tabbar/index', 
                        'title' => '底部导航',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '98', 
                        'ismenu' => 1,
                    ],
                ]
            ],
            [
                'name'    => 'xylease/money',
                'title'   => '财务管理',
                'icon'    => 'fa fa-diamond',
                'weigh'	  => '93',
                'sublist' => [
                    [
                        'name' => 'xylease/user/withdraw/index/type/distribution', 
                        'title' => '佣金提现',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '100', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/user/withdraw/detail/type/distribution', 'title' => '详情'],
                            ['name' => 'xylease/user/withdraw/agree/type/distribution', 'title' => '同意'],
                            ['name' => 'xylease/user/withdraw/refuse/type/distribution', 'title' => '拒绝'],
                        ]
                    ],
                    [
                        'name' => 'xylease/user/withdraw/index/type/balance', 
                        'title' => '余额提现',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '99', 
                        'ismenu' => 1,
                        'sublist' => [
                            ['name' => 'xylease/user/withdraw/detail/type/balance', 'title' => '详情'],
                            ['name' => 'xylease/user/withdraw/agree/type/balance', 'title' => '同意'],
                            ['name' => 'xylease/user/withdraw/refuse/type/balance', 'title' => '拒绝'],
                        ]
                    ],
                    [
                        'name' => 'xylease/user/money/index', 
                        'title' => '余额明细',
                        'icon'  => 'fa fa-align-justify',
                        'weigh' => '98', 
                        'ismenu' => 1, 
                    ],
                ]
            ],
            [
    		    'name'    => 'xylease/config/index',
    		    'title'   => '配置中心',
    		    'icon'    => 'fa fa-gear',
    			'weigh'	  => '89',
                'ismenu' => 1,
    		],
        ],
    ]
];
return $menu;