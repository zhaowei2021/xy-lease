FCREATE TABLE IF NOT EXISTS `__PREFIX__xylease_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  `content` text NOT NULL COMMENT '内容',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品',
  `goodstype` varchar(100) NOT NULL DEFAULT '' COMMENT '商品类型',
  `sku_price_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规格',
  `buynum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购物车表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` longtext NOT NULL COMMENT '变量值',
  `content` longtext CHARACTER SET utf8mb4 COMMENT '变量字典数据',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展属性',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

INSERT INTO `__PREFIX__xylease_config` VALUES (1, 'xylease', 'basic', '通用配置', '', 'array', '{\"useravatar\":\"/assets/addons/xylease/imgs/logo.png\",\"agreement\":1,\"privacy\":2,\"ticketagree\":\"\"}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (2, 'share', 'basic', '分享配置', '', 'array', '{\"title\":\"XYlease租赁\",\"image\":\"/assets/addons/xylease/imgs/share.jpg\",\"user_poster_bg\":\"/assets/addons/xylease/imgs/sharebg.jpg\"}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (3, 'wxminiprogram', 'platform', '微信小程序', '', 'array', '{\"app_id\":\"\",\"secret\":\"\"}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (4, 'appstyle', 'other', '样式通用配置', '', 'array', '{\"mainColor\":\"#f05656\",\"navBarBgColor\":\"#ffffff\",\"navBarFrontColor\":\"#000000\",\"pageBgColor\":\"#eff0f2\",\"textMainColor\":\"#333333\",\"textLightColor\":\"#808080\",\"textPriceColor\":\"#ff5335\",\"pageModuleBgColor\":\"#ffffff\"}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (5, 'lease', 'basic', '租赁配置', '', 'array', '{\"deliverytype\":[\"pickup\"],\"leasetype\":[\"hour\",\"days\",\"night\"],\"defaultlease\":\"hour\",\"rule\":3,\"hourzt\":\"09:00\",\"hourst\":\"2\",\"starthour\":\"5\",\"daysst\":\"09:00\",\"nightst\":\"09:00\",\"nightet\":\"10:00\"}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (6, 'distribution', 'basic', '分销配置', '', 'array', '{\"open\":1,\"isdis\":1,\"child_condition\":\"share\",\"level\":\"2\"}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (7, 'wechat', 'payment', '微信支付', '', 'array', '{\"mch_id\":\"\",\"key\":\"\",\"cert_client\":\"\",\"cert_key\":\"\"}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (8, 'tabbar', 'tabbar', '底部导航', '', 'array', '{\"backgroundColor\":\"#FFFFFF\",\"textColor\":\"#333333\",\"textHoverColor\":\"#f05656\",\"style\":\"1\",\"list\":[{\"title\":\"首页\",\"link\":\"/pages/index\",\"iconPath\":\"/assets/addons/xylease/imgs/tabbar/index.png\",\"selectedIconPath\":\"/assets/addons/xylease/imgs/tabbar/index-a.png\",\"show\":1},{\"title\":\"分类\",\"link\":\"/pages/category\",\"iconPath\":\"/assets/addons/xylease/imgs/tabbar/category.png\",\"selectedIconPath\":\"/assets/addons/xylease/imgs/tabbar/category-a.png\",\"show\":1},{\"title\":\"租物车\",\"link\":\"/pages/cart\",\"iconPath\":\"/assets/addons/xylease/imgs/tabbar/cart.png\",\"selectedIconPath\":\"/assets/addons/xylease/imgs/tabbar/cart-a.png\",\"show\":1},{\"title\":\"我的\",\"link\":\"/pages/user\",\"iconPath\":\"/assets/addons/xylease/imgs/tabbar/user.png\",\"selectedIconPath\":\"/assets/addons/xylease/imgs/tabbar/user-a.png\",\"show\":1}]}', NULL, '', '');
INSERT INTO `__PREFIX__xylease_config` VALUES (9, 'withdraw', 'basic', '提现配置', '', 'array', '{\"methods\":[\"bank\"],\"rate\":0.6,\"min\":100,\"max\":10000}', NULL, '', '');


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_coupon` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` enum('reward','discount') NOT NULL DEFAULT 'reward' COMMENT '优惠券类型:reward=满减,discount=折扣',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '发放数量',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券面额',
  `leadcount` int(11) NOT NULL DEFAULT '0' COMMENT '已领取数量',
  `usedcount` int(11) NOT NULL DEFAULT '0' COMMENT '已使用数量',
  `atleast` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '满多少元使用0无限制',
  `isshow` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否允许直接领取:0=否,1=是',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣',
  `validitytype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '过期类型:0=固定时间范围过期,1=领取之日起,2=长期有效',
  `endusetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '使用结束时间',
  `fixedterm` int(11) NOT NULL DEFAULT '0' COMMENT '有效天数',
  `maxfetch` int(11) NOT NULL DEFAULT '0' COMMENT '每人最大领取个数',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_distribution` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户',
  `level_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `pid` int(10) unsigned NOT NULL COMMENT '上级分销商',
  `realname` varchar(100) NOT NULL COMMENT '姓名',
  `mobile` varchar(20) NOT NULL COMMENT '手机号',
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可提现佣金',
  `total_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总佣金',
  `withdrawn_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '已提现佣金',
  `withdrawing_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现中佣金',
  `status` varchar(100) NOT NULL DEFAULT 'normal' COMMENT '分销商状态:normal=正常,forbidden=禁用',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销商';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_distribution_commission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户',
  `distribution_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分销商',
  `type` varchar(100) NOT NULL DEFAULT 'withdraw' COMMENT '类型: apply_withdraw=申请提现,refuse_withdraw=拒绝提现,pay_withdraw=提现打款,order=订单结算,sys=后台操作',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '费用',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后',
  `service_id` int(10) NOT NULL DEFAULT '0' COMMENT '业务ID',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销商佣金流水表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_distribution_level` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `grade` enum('1','2','3','4','5','6','7','8','9','10') NOT NULL DEFAULT '1' COMMENT '等级:1=一级,2=二级,3=三级,4=四级,5=五级,6=六级,7=七级,8=八级,9=九级,10=十级',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '等级名称',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '等级徽章',
  `commission_one` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '一级佣金比例',
  `commission_two` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '二级佣金比例',
  `commission_team` int(3) NOT NULL DEFAULT '0' COMMENT '团队业绩佣金比例',
  `commission_bonus` int(3) NOT NULL DEFAULT '0' COMMENT '日交易额分红比例',
  `upgrade_type` enum('or','and') NOT NULL DEFAULT 'or' COMMENT '升级方式',
  `upgrade_rules` text COMMENT '升级条件',
  `status` varchar(100) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销商等级';


INSERT INTO `__PREFIX__xylease_distribution_level` VALUES (2, '1', '一级', '/assets/addons/xylease/imgs/level.png', 10, 5, 0, 0, 'or', NULL, 'normal', 1704789558, 1704789558);


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_distribution_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `service_order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '业务订单',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买家',
  `ordersn` varchar(60) NOT NULL COMMENT '订单号',
  `ordertype` varchar(100) NOT NULL COMMENT '订单类型:order=租赁订单',
  `commission_event` varchar(255) DEFAULT 'payed' COMMENT '佣金结算方式: payed=支付后',
  `one_distribution_id` int(10) DEFAULT NULL COMMENT '一级分销商',
  `two_distribution_id` int(10) DEFAULT NULL COMMENT '二级分销商',
  `one_commission` decimal(10,2) DEFAULT NULL COMMENT '一级佣金',
  `two_commission` decimal(10,2) DEFAULT NULL COMMENT '二级佣金',
  `corich_commission` decimal(10,2) DEFAULT '0.00' COMMENT '共富收益',
  `totalfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `status` tinyint(1) DEFAULT '0' COMMENT '佣金处理状态:-2=已取消,-1=已退回,0=未结算,1=已结算',
  `settle_time` int(11) DEFAULT NULL COMMENT '结算时间',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销订单表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `category_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '所属分类',
  `type` enum('single','package') NOT NULL DEFAULT 'single' COMMENT '类型:single=租赁单品,package=租赁套餐',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '商品主图',
  `images` varchar(2500) NOT NULL COMMENT '轮播图',
  `content` text NOT NULL COMMENT '详情',
  `issku` enum('0','1') NOT NULL DEFAULT '0' COMMENT '规格:0=单规格,1=多规格',
  `favorite` int(10) NOT NULL DEFAULT '0' COMMENT '收藏',
  `views` int(10) NOT NULL DEFAULT '0' COMMENT '浏览',
  `sales` int(10) NOT NULL DEFAULT '0' COMMENT '租量',
  `virtualsales` int(10) NOT NULL DEFAULT '0' COMMENT '虚拟租量',
  `isdis` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否分销: 0=不参与,1=参与',
  `disrule` tinyint(2) NOT NULL DEFAULT '0' COMMENT '分销规则: 0=默认规则,1=单独设置',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('up','down') NOT NULL DEFAULT 'up' COMMENT '状态:up=上架,down=下架',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `intro` varchar(255) DEFAULT '' COMMENT '描述',
  `image` varchar(100) DEFAULT '' COMMENT '图片',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态:normal=正常,hidden=隐藏',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `weigh_id` (`weigh`,`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_goods_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `package_id` int(10) NOT NULL DEFAULT '0' COMMENT '套餐ID',
  `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `goodsname` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `goodsimage` varchar(255) DEFAULT NULL COMMENT '商品图片',
  `goodsskutext` varchar(60) DEFAULT NULL COMMENT '规格名',
  `goods_sku_price_id` int(10) NOT NULL DEFAULT '0' COMMENT '规格 id',
  `nums` int(10) NOT NULL DEFAULT '1' COMMENT '数量',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='套餐商品明细';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_goods_sku` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_id` int(10) NOT NULL COMMENT '产品',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '所属规格',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品规格表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_goods_sku_price` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` enum('single','package') DEFAULT 'single' COMMENT '类型:single=租赁单品,package=租赁套餐',
  `goods_sku_ids` varchar(255) DEFAULT NULL,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属产品',
  `image` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `stock` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
  `sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已租',
  `sn` varchar(50) DEFAULT NULL COMMENT '货号',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '售价',
  `hourprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '小时租价格',
  `daysprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '全日租价格',
  `nightprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '过夜租价格',
  `deposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '押金',
  `showprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '显示价格',
  `commissionrule` text COMMENT '佣金规则',
  `goodsskutext` varchar(255) DEFAULT NULL COMMENT '规格',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` varchar(10) NOT NULL DEFAULT 'up' COMMENT '状态:up=上架,down=下架',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品规格表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_goods_stock_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` enum('sysadd','sysedit','ordercreate','ordercancel','orderclose','orderback') NOT NULL DEFAULT 'sysadd' COMMENT '变更类型:sysadd=后台添加,sysedit=后台编辑,ordercreate=订单创建,ordercancel=订单取消,orderclose=订单关闭,orderback=订单归还',
  `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `goods_sku_price_id` int(10) NOT NULL DEFAULT '0' COMMENT '规格 id',
  `goodsname` varchar(255) NOT NULL COMMENT '商品名称',
  `goodsimage` varchar(255) NOT NULL COMMENT '商品图片',
  `goodsskutext` varchar(60) NOT NULL COMMENT '规格名',
  `before` int(10) NOT NULL DEFAULT '0' COMMENT '变更前',
  `nums` int(10) NOT NULL DEFAULT '0' COMMENT '变更数量',
  `after` int(10) NOT NULL DEFAULT '0' COMMENT '变更后',
  `operaterole` enum('admin','staff','user') DEFAULT 'admin' COMMENT '操作人角色:admin=管理员,staff=员工,user=会员',
  `operate_id` int(10) NOT NULL COMMENT '操作人ID|订单ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品库存明细';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(100) NOT NULL DEFAULT 'basic' COMMENT '链接类型:basic=基础链接,user=会员中心,notice=公告链接,activity=活动链接',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '链接名称',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '路径',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='链接表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `content` text NOT NULL COMMENT '内容',
  `views` int(10) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态:normal=正常,hidden=隐藏',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `weigh_id` (`weigh`,`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公告表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ordersn` varchar(60) NOT NULL DEFAULT '' COMMENT '订单号',
  `type` set('lease','buy','service') DEFAULT 'lease' COMMENT '类型:lease=租赁,buy=购买,service=服务',
  `from` varchar(100) NOT NULL DEFAULT 'buynow' COMMENT '来源:buynow=立即购买,cart=购物车',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '租赁用户',
  `leasetype` varchar(100) NOT NULL DEFAULT '' COMMENT '租赁类型',
  `leasetimenum` int(10) NOT NULL DEFAULT '0' COMMENT '租赁时长',
  `starttime` bigint(16) DEFAULT NULL COMMENT '租赁开始时间',
  `endtime` bigint(16) DEFAULT NULL COMMENT '租赁结束时间',
  `leasetime` varchar(100) NOT NULL DEFAULT '' COMMENT '租赁时间',
  `pickuptime` varchar(100) NOT NULL DEFAULT '' COMMENT '租赁自提时间',
  `totalnum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品件数',
  `totalamount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品租金',
  `totaldeposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总押金',
  `couponfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `totalfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '需付金额',
  `payfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实付金额',
  `transaction_id` varchar(60) DEFAULT NULL COMMENT '交易单号',
  `user_coupon_id` int(10) NOT NULL DEFAULT '0' COMMENT '会员优惠券',
  `paymentjson` text COMMENT '交易原始数据',
  `paytype` varchar(100) NOT NULL DEFAULT '' COMMENT '支付方式:wechat=微信支付,balance=余额支付',
  `paytime` bigint(16) DEFAULT NULL COMMENT '支付时间',
  `platform` enum('wxMiniProgram','wxOfficialAccount') DEFAULT 'wxMiniProgram' COMMENT '平台:wxMiniProgram=微信小程序,wxOfficialAccount=微信公众号',
  `consignee` varchar(100) NOT NULL DEFAULT '' COMMENT '联系人',
  `mobile` varchar(20) NOT NULL COMMENT '联系电话',
  `deliverytype` varchar(100) DEFAULT 'pickup' COMMENT '发货方式:express=物流快递,pickup=门店自提',
  `ext` text COMMENT '附加字段',
  `remark` varchar(255) DEFAULT NULL COMMENT '用户备注',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '订单状态:-2=交易关闭,-1=已取消,0=待付款,1=待取货,2=待归还,3=已完成',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ordersn` (`ordersn`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `createtime` (`createtime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品订单表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT ' 订单',
  `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '租金|购买单价',
  `deposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '押金单价',
  `goods_sku_price_id` int(10) NOT NULL DEFAULT '0' COMMENT '规格 id',
  `goodsskutext` varchar(60) DEFAULT NULL COMMENT '规格名',
  `goodstype` varchar(100) DEFAULT '' COMMENT '商品类型',
  `goodsname` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `goodsimage` varchar(255) DEFAULT NULL COMMENT '商品图片',
  `buynum` int(10) NOT NULL DEFAULT '0' COMMENT '租赁数量',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单商品明细';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `page_token` varchar(16) NOT NULL DEFAULT '' COMMENT '页面Token',
  `name` varchar(100) NOT NULL DEFAULT '自定义页面' COMMENT '页面名称',
  `cover` varchar(256) DEFAULT NULL COMMENT '页面封面',
  `type` varchar(100) NOT NULL DEFAULT 'index' COMMENT '模板类型:index=首页模板',
  `page` longtext COMMENT '页面配置',
  `item` longtext COMMENT '项目',
  `is_use` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否使用',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自定义装修页面表';

INSERT INTO `__PREFIX__xylease_page` VALUES (1, 'J72RZlmTXIPqn39N', '个人中心', '/assets/addons/xylease/imgs/user.jpg', 'user', '{\"params\":{\"navigationBarTitleText\":\"\\u4e2a\\u4eba\\u4e2d\\u5fc3\"},\"style\":{\"navigationBarTextStyle\":\"#ffffff\",\"navigationBarBackgroundColor\":\"#f05656\",\"pageBackgroundColor\":\"#f7f7f7\",\"pageBackgroundImage\":\"\"}}', '[{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u7528\\u6237\\u5361\\u7247\",\"type\":\"user-card\",\"icon\":\"user\",\"params\":{\"bgColor\":\"#ffffff\",\"borderRadius\":\"20\",\"lrmargin\":\"30\",\"njj\":\"20\"}},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u94b1\\u5305\\u6a21\\u5757\",\"type\":\"user-money\",\"icon\":\"jpy\",\"params\":{\"bgColor\":\"#ffffff\",\"borderRadius\":\"20\",\"lrmargin\":\"30\",\"njj\":\"20\",\"rechargeicon\":\"\\/assets\\/addons\\/xylease\\/imgs\\/recharge.png\",\"walleticon\":\"\\/assets\\/addons\\/xylease\\/imgs\\/wallet.png\"}},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u83dc\\u5355\\u7ec4\\u4ef6\",\"type\":\"menu\",\"icon\":\"list\",\"params\":{\"title\":\"\\u6211\\u7684\\u670d\\u52a1\",\"linktitle\":\"\",\"link\":\"\",\"colnum\":\"3\",\"textimgpl\":\"2\",\"lrmargin\":\"30\",\"lrnjj\":\"30\",\"upnjj\":\"40\",\"borderRadius\":\"20\",\"itemBorderRadius\":\"10\",\"bgColor\":\"#ffffff\",\"itemBgColor\":\"#ffffff\",\"imgwh\":\"80\",\"textsize\":\"30\",\"itemjj\":\"50\",\"itemnjj\":\"0\",\"textbold\":\"0\",\"textColor\":\"#333333\"},\"data\":[{\"name\":\"\\u79df\\u8d41\\u8ba2\\u5355\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/us2.png\",\"link\":\"\\/pages\\/user\\/order\\/list\"},{\"name\":\"\\u4f18\\u60e0\\u5238\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/us7.png\",\"link\":\"\\/pages\\/user\\/coupon\\/list\"},{\"name\":\"\\u5206\\u9500\\u4e2d\\u5fc3\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/us6.png\",\"link\":\"\\/pages\\/distribution\\/center\"},{\"name\":\"\\u6211\\u7684\\u6536\\u85cf\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/us9.png\",\"link\":\"\\/pages\\/user\\/favorite\"},{\"name\":\"\\u8054\\u7cfb\\u5ba2\\u670d\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/us5.png\",\"link\":\"phone\"},{\"name\":\"\\u6211\\u7684\\u8d44\\u6599\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/us4.png\",\"link\":\"\\/pages\\/user\\/info\"}]},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u83dc\\u5355\\u7ec4\\u4ef6\",\"type\":\"menu\",\"icon\":\"list\",\"params\":{\"title\":\"\",\"linktitle\":\"\",\"link\":\"\",\"colnum\":\"1\",\"textimgpl\":\"1\",\"lrmargin\":\"30\",\"lrnjj\":\"30\",\"upnjj\":\"0\",\"borderRadius\":\"20\",\"itemBorderRadius\":\"10\",\"bgColor\":\"#ffffff\",\"itemBgColor\":\"#ffffff\",\"imgwh\":\"80\",\"textsize\":\"30\",\"itemjj\":\"0\",\"itemnjj\":\"20\",\"textbold\":\"1\",\"textColor\":\"#333333\"},\"data\":[{\"name\":\"\\u5458\\u5de5\\u4e2d\\u5fc3\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/us6.png\",\"link\":\"\\/pages\\/staff\\/clerk\"}]}]', 1, 'normal', 1710578986, 1710578986, NULL);
INSERT INTO `__PREFIX__xylease_page` VALUES (2, 'xzdbpmCN5lUXA0Ok', '首页', '/assets/addons/xylease/imgs/index.jpg', 'index', '{\"params\":{\"navigationBarTitleText\":\"XYlease\\u79df\\u8d41\\u5c0f\\u7a0b\\u5e8f\"},\"style\":{\"navigationBarTextStyle\":\"#ffffff\",\"navigationBarBackgroundColor\":\"#f05656\",\"pageBackgroundColor\":\"#fafafa\",\"pageBackgroundImage\":\"\"}}', '[{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u641c\\u7d22\\u7ec4\\u4ef6\",\"type\":\"search\",\"icon\":\"search\",\"params\":{\"tiptext\":\"\\u8bf7\\u8f93\\u5165\\u641c\\u7d22\\u5185\\u5bb9\",\"bgColor\":\"#ffffff\",\"height\":\"70\",\"borderRadius\":\"35\",\"lrmargin\":\"30\"}},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u8f6e\\u64ad\\u7ec4\\u4ef6\",\"type\":\"banner\",\"icon\":\"random\",\"params\":{\"autoplay\":\"1\",\"interval\":\"5000\",\"height\":\"280\",\"indicatorDots\":\"1\",\"indicatorColor\":\"#f26767\",\"indicatorActiveColor\":\"#ffffff\",\"lrmargin\":\"30\",\"borderRadius\":\"20\",\"showfloat\":\"0\",\"floatimg1\":\"\",\"floatlink1\":\"\",\"floatimg2\":\"\",\"floatlink2\":\"\",\"floatimg3\":\"\",\"floatlink3\":\"\\/pages\\/lease\"},\"data\":[{\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/banner.jpg\",\"link\":\"\\/pages\\/store\\/detail\"},{\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/banner.jpg\",\"link\":\"\\/pages\\/store\\/detail\"},{\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/banner.jpg\",\"link\":\"\\/pages\\/store\\/detail\"}]},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u516c\\u544a\\u7ec4\\u4ef6\",\"type\":\"notice\",\"icon\":\"bullhorn\",\"dataType\":\"notice\",\"data\":[{\"id\":\"1\",\"title\":\"XYlease\\u79df\\u8d41\\u5c0f\\u7a0b\\u5e8f\\u4e0a\\u7ebf\\u4e86\\u6b22\\u8fce\\u4f53\\u9a8c\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/logo.png\",\"content\":\"XYlease\\u79df\\u8d41\\u5c0f\\u7a0b\\u5e8f\\u4e0a\\u7ebf\\u4e86\\u6b22\\u8fce\\u4f53\\u9a8c\",\"views\":\"0\",\"weigh\":\"6\",\"status\":\"normal\",\"createtime\":\"1709696211\",\"updatetime\":\"1709869264\",\"status_text\":\"\\u6b63\\u5e38\",\"state\":\"true\"}],\"params\":{\"bgColor\":\"#ffffff\",\"lefticon\":\"\\/assets\\/addons\\/xylease\\/imgs\\/notice.png\",\"lrmargin\":\"30\",\"borderRadius\":\"10\",\"scroll\":\"tb\"}},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u83dc\\u5355\\u7ec4\\u4ef6\",\"type\":\"menu\",\"icon\":\"list\",\"params\":{\"title\":\"\",\"linktitle\":\"\",\"link\":\"\",\"colnum\":\"4\",\"textimgpl\":\"2\",\"lrmargin\":\"40\",\"lrnjj\":\"0\",\"upnjj\":\"30\",\"borderRadius\":\"20\",\"itemBorderRadius\":\"0\",\"bgColor\":\"#ffffff\",\"itemBgColor\":\"#ffffff\",\"imgwh\":\"80\",\"textsize\":\"30\",\"itemjj\":\"0\",\"itemnjj\":\"0\",\"textbold\":\"0\",\"textColor\":\"#333333\"},\"data\":[{\"name\":\"\\u9886\\u5238\\u4e2d\\u5fc3\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/home1.png\",\"link\":\"\\/pages\\/market\\/coupon\\/list\"},{\"name\":\"\\u79df\\u8d41\\u89c4\\u5219\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/home2.png\",\"link\":\"\\/pages\\/service\\/article\\/detail?id=3\"},{\"name\":\"\\u6211\\u7684\\u6536\\u85cf\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/home4.png\",\"link\":\"\\/pages\\/user\\/favorite\"},{\"name\":\"\\u5728\\u7ebf\\u5145\\u503c\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/home3.png\",\"link\":\"\\/pages\\/user\\/balance\\/recharge\"}]},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u5546\\u54c1\\u7ec4\\u4ef6\",\"type\":\"goods\",\"icon\":\"archive\",\"dataType\":\"goods\",\"data\":[{\"id\":\"8\",\"name\":\"\\u665a\\u971e\\u65e5\\u843d\\u5bb6\\u5ead\\u623f\\u5e10\\u7bf72\\u4eba\\u5957\\u9910\",\"category_ids\":\"1\",\"type\":\"package\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"images\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png,\\/assets\\/addons\\/xylease\\/imgs\\/goods.png,\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"content\":\"<p><br \\/><\\/p><p>\\u665a\\u971e\\u65e5\\u843d\\u5bb6\\u5ead\\u623f\\u5e10\\u7bf72\\u4eba\\u5957\\u9910<\\/p><p><br \\/><\\/p><p><img src=\\\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\\\" alt=\\\"\\\" \\/><\\/p>\",\"issku\":\"0\",\"favorite\":\"0\",\"views\":\"0\",\"sales\":\"0\",\"virtualsales\":\"0\",\"isdis\":\"0\",\"disrule\":\"0\",\"weigh\":\"0\",\"status\":\"up\",\"createtime\":\"1709796892\",\"updatetime\":\"1710554376\",\"sku_price\":[{\"id\":\"8\",\"type\":\"package\",\"goods_sku_ids\":\"\",\"goods_id\":\"8\",\"image\":\"\",\"stock\":\"0\",\"sales\":\"0\",\"sn\":\"\",\"price\":\"0.00\",\"hourprice\":\"10.00\",\"daysprice\":\"150.00\",\"nightprice\":\"220.00\",\"deposit\":\"100.00\",\"showprice\":\"0.00\",\"commissionrule\":\"\",\"weigh\":\"116\",\"status\":\"up\",\"createtime\":\"1709796892\",\"status_text\":\"\\u4e0a\\u67b6\"}],\"issku_text\":\"\\u5355\\u89c4\\u683c\",\"status_text\":\"\\u4e0a\\u67b6\",\"stock\":\"0\",\"type_text\":\"\\u5957\\u9910\",\"state\":\"true\"},{\"id\":\"7\",\"name\":\"\\u665a\\u971e\\u65e5\\u843d\\u5bb6\\u5ead\\u623f\\u5e10\\u7bf74\\u4eba\\u5957\\u9910\",\"category_ids\":\"1\",\"type\":\"package\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"images\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png,\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"content\":\"<p class=\\\"MsoNormal\\\"><br \\/><\\/p><p class=\\\"MsoNormal\\\">\\u665a\\u971e\\u65e5\\u843d\\u5bb6\\u5ead\\u623f\\u5e10\\u7bf74\\u4eba\\u5957\\u9910<\\/p><p class=\\\"MsoNormal\\\"><br \\/><\\/p><p class=\\\"MsoNormal\\\"><span style=\\\"font-family:\\u5fae\\u8f6f\\u96c5\\u9ed1;\\\"><img src=\\\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\\\" alt=\\\"\\\" \\/><\\/span><o:p><\\/o:p><\\/p>\",\"issku\":\"0\",\"favorite\":\"0\",\"views\":\"0\",\"sales\":\"0\",\"virtualsales\":\"0\",\"isdis\":\"0\",\"disrule\":\"0\",\"weigh\":\"0\",\"status\":\"up\",\"createtime\":\"1708594511\",\"updatetime\":\"1710554474\",\"sku_price\":[{\"id\":\"7\",\"type\":\"package\",\"goods_sku_ids\":\"\",\"goods_id\":\"7\",\"image\":\"\",\"stock\":\"0\",\"sales\":\"0\",\"sn\":\"\",\"price\":\"0.00\",\"hourprice\":\"20.00\",\"daysprice\":\"300.00\",\"nightprice\":\"488.00\",\"deposit\":\"100.00\",\"showprice\":\"0.00\",\"commissionrule\":\"\",\"weigh\":\"106\",\"status\":\"up\",\"createtime\":\"1708594511\",\"status_text\":\"\\u4e0a\\u67b6\"}],\"issku_text\":\"\\u5355\\u89c4\\u683c\",\"status_text\":\"\\u4e0a\\u67b6\",\"stock\":\"0\",\"type_text\":\"\\u5957\\u9910\",\"state\":\"true\"}],\"params\":{\"title\":\"\\u79df\\u8d41\\u5957\\u9910\",\"linktitle\":\"\\u66f4\\u591a\",\"link\":\"\\/pages\\/goods\\/list?cid=1&name=\\u79df\\u8d41\\u5957\\u9910\",\"lrmargin\":\"30\",\"njj\":\"30\",\"itemjj\":\"30\",\"itemBorderRadius\":\"10\",\"itemBgColor\":\"#ffffff\",\"showStyle\":\"2\",\"bgColor\":\"#ffffff\",\"borderRadius\":\"20\"}},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u8425\\u5730\\u4fe1\\u606f\",\"type\":\"camp\",\"icon\":\"map\",\"params\":{\"bgColor\":\"#ffffff\",\"borderRadius\":\"20\",\"lrmargin\":\"30\",\"njj\":\"30\",\"logo\":\"/assets/addons/xylease/imgs/logo.png\",\"name\":\"XYlease\\u79df\\u8d41\\u5c0f\\u7a0b\\u5e8f\\u6d4b\\u8bd5\\u95e8\\u5e97\",\"address\":\"\\u7984\\u53e3\\u533a\\u9f99\\u6f6d\\u6751\\u84ec\\u6e90\\u4ed9\\u98763387\\u9732\\u8425\\u57fa\\u5730\",\"businesshours\":\"24\\u5c0f\\u65f6\\u8425\\u4e1a\",\"phone\":\"13762308505\",\"weixin\":\"13762308505\",\"longitude\":\"113.05\",\"latitude\":\"28.24\"}},{\"name\":\"\\u5546\\u54c1\\u7ec4\\u4ef6\",\"type\":\"goods\",\"icon\":\"archive\",\"dataType\":\"goods\",\"data\":[{\"id\":\"4\",\"name\":\"\\u6237\\u5916\\u70e4\\u706b\\u7089\\u51ac\\u5b63\\u9732\\u8425\\u5fc5\\u5907\",\"category_ids\":\"4\",\"type\":\"single\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"images\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png,\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"content\":\"<p><br \\/><\\/p><p>\\u6237\\u5916\\u70e4\\u706b\\u7089\\u51ac\\u5b63\\u9732\\u8425\\u5fc5\\u5907\\u795e\\u5668<\\/p><p><br \\/><\\/p><p><img src=\\\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\\\" alt=\\\"\\\" \\/><\\/p>\",\"issku\":\"0\",\"favorite\":\"0\",\"views\":\"0\",\"sales\":\"0\",\"virtualsales\":\"0\",\"isdis\":\"0\",\"disrule\":\"0\",\"weigh\":\"0\",\"status\":\"up\",\"createtime\":\"1708590033\",\"updatetime\":\"1710553349\",\"sku_price\":[{\"id\":\"4\",\"type\":\"single\",\"goods_sku_ids\":\"\",\"goods_id\":\"4\",\"image\":\"\",\"stock\":\"999\",\"sales\":\"0\",\"sn\":\"\",\"price\":\"0.00\",\"hourprice\":\"10.00\",\"daysprice\":\"100.00\",\"nightprice\":\"150.00\",\"deposit\":\"100.00\",\"showprice\":\"0.00\",\"commissionrule\":\"\",\"weigh\":\"96\",\"status\":\"up\",\"createtime\":\"1708590033\",\"status_text\":\"\\u4e0a\\u67b6\"}],\"issku_text\":\"\\u5355\\u89c4\\u683c\",\"status_text\":\"\\u4e0a\\u67b6\",\"stock\":\"999\",\"type_text\":\"\\u79df\\u8d41\\u5355\\u54c1\",\"state\":\"true\"},{\"id\":\"3\",\"name\":\"5m\\u4e32\\u706f\\u9732\\u8425\\u5e10\\u7bf7\\u88c5\\u9970\\u54c1\",\"category_ids\":\"4\",\"type\":\"single\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"images\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png,\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"content\":\"<p><br \\/><\\/p><p>5m\\u4e32\\u706f\\u9732\\u8425\\u5e10\\u7bf7\\u88c5\\u9970\\u54c1<\\/p><p><br \\/><\\/p><p><img src=\\\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\\\" alt=\\\"\\\" \\/><\\/p><p><br \\/><\\/p>\",\"issku\":\"0\",\"favorite\":\"0\",\"views\":\"0\",\"sales\":\"0\",\"virtualsales\":\"0\",\"isdis\":\"0\",\"disrule\":\"0\",\"weigh\":\"0\",\"status\":\"up\",\"createtime\":\"1708589948\",\"updatetime\":\"1710553432\",\"sku_price\":[{\"id\":\"3\",\"type\":\"single\",\"goods_sku_ids\":\"\",\"goods_id\":\"3\",\"image\":\"\",\"stock\":\"999\",\"sales\":\"0\",\"sn\":\"\",\"price\":\"0.00\",\"hourprice\":\"2.38\",\"daysprice\":\"13.86\",\"nightprice\":\"15.18\",\"deposit\":\"19.80\",\"showprice\":\"0.00\",\"commissionrule\":\"\",\"weigh\":\"95\",\"status\":\"up\",\"createtime\":\"1708589948\",\"status_text\":\"\\u4e0a\\u67b6\"}],\"issku_text\":\"\\u5355\\u89c4\\u683c\",\"status_text\":\"\\u4e0a\\u67b6\",\"stock\":\"999\",\"type_text\":\"\\u79df\\u8d41\\u5355\\u54c1\",\"state\":\"true\"},{\"id\":\"2\",\"name\":\"\\u86cb\\u5377\\u684c\\u9732\\u8425\\u70e7\\u70e4\\u5fc5\\u5907\",\"category_ids\":\"4\",\"type\":\"single\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"images\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png,\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"content\":\"<p><br \\/><\\/p><p>\\u86cb\\u5377\\u684c\\u9732\\u8425\\u70e7\\u70e4\\u5fc5\\u5907<\\/p><p><br \\/><\\/p><p><img src=\\\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\\\" alt=\\\"\\\" \\/><\\/p>\",\"issku\":\"0\",\"favorite\":\"0\",\"views\":\"0\",\"sales\":\"0\",\"virtualsales\":\"0\",\"isdis\":\"0\",\"disrule\":\"0\",\"weigh\":\"0\",\"status\":\"up\",\"createtime\":\"1708589857\",\"updatetime\":\"1710553628\",\"sku_price\":[{\"id\":\"2\",\"type\":\"single\",\"goods_sku_ids\":\"\",\"goods_id\":\"2\",\"image\":\"\",\"stock\":\"999\",\"sales\":\"0\",\"sn\":\"\",\"price\":\"0.00\",\"hourprice\":\"7.17\",\"daysprice\":\"43.00\",\"nightprice\":\"47.80\",\"deposit\":\"71.70\",\"showprice\":\"0.00\",\"commissionrule\":\"\",\"weigh\":\"94\",\"status\":\"up\",\"createtime\":\"1708589857\",\"status_text\":\"\\u4e0a\\u67b6\"}],\"issku_text\":\"\\u5355\\u89c4\\u683c\",\"status_text\":\"\\u4e0a\\u67b6\",\"stock\":\"999\",\"type_text\":\"\\u79df\\u8d41\\u5355\\u54c1\",\"state\":\"true\"},{\"id\":\"1\",\"name\":\"\\u70e7\\u70e4\\u7528\\u9910\\u9910\\u5177\",\"category_ids\":\"4\",\"type\":\"single\",\"image\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"images\":\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png,\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\",\"content\":\"<p><br \\/><\\/p><p>\\u70e7\\u70e4\\u7528\\u9910\\u9910\\u5177\\r\\n<\\/p><p><br \\/><\\/p><p><img src=\\\"\\/assets\\/addons\\/xylease\\/imgs\\/goods.png\\\" alt=\\\"\\\" \\/><\\/p>\",\"issku\":\"0\",\"favorite\":\"0\",\"views\":\"0\",\"sales\":\"0\",\"virtualsales\":\"0\",\"isdis\":\"0\",\"disrule\":\"0\",\"weigh\":\"0\",\"status\":\"up\",\"createtime\":\"1708586715\",\"updatetime\":\"1710553892\",\"sku_price\":[{\"id\":\"1\",\"type\":\"single\",\"goods_sku_ids\":\"\",\"goods_id\":\"1\",\"image\":\"\",\"stock\":\"999\",\"sales\":\"0\",\"sn\":\"\",\"price\":\"0.00\",\"hourprice\":\"2.00\",\"daysprice\":\"12.00\",\"nightprice\":\"20.00\",\"deposit\":\"2.00\",\"showprice\":\"0.00\",\"commissionrule\":\"\",\"weigh\":\"75\",\"status\":\"up\",\"createtime\":\"1708586715\",\"status_text\":\"\\u4e0a\\u67b6\"}],\"issku_text\":\"\\u5355\\u89c4\\u683c\",\"status_text\":\"\\u4e0a\\u67b6\",\"stock\":\"999\",\"type_text\":\"\\u79df\\u8d41\\u5355\\u54c1\",\"state\":\"true\"}],\"params\":{\"title\":\"\\u79df\\u8d41\\u5355\\u54c1\",\"linktitle\":\"\\u66f4\\u591a\",\"link\":\"\\/pages\\/goods\\/list?cid=2&name=\\u79df\\u8d41\\u5957\\u9910\",\"lrmargin\":\"30\",\"njj\":\"30\",\"itemjj\":\"30\",\"itemBorderRadius\":\"10\",\"itemBgColor\":\"#ffffff\",\"showStyle\":\"1\",\"bgColor\":\"#ffffff\",\"borderRadius\":\"20\"}},{\"name\":\"\\u7a7a\\u767d\\u884c\",\"type\":\"empty\",\"icon\":\"window-maximize\",\"params\":{\"height\":\"30\"}},{\"name\":\"\\u95e8\\u5e97\\u4fe1\\u606f\",\"type\":\"store\",\"icon\":\"map\",\"params\":{\"bgColor\":\"#ffffff\",\"borderRadius\":\"20\",\"lrmargin\":\"30\",\"njj\":\"30\",\"logo\":\"\\/assets\\/addons\\/xylease\\/imgs\\/logo.png\",\"name\":\"XYlease\\u79df\\u8d41\\u95e8\\u5e97\",\"address\":\"\\u6e56\\u5357\\u682a\\u6d32\\u672a\\u6765\\u4e91\\u95e8\\u5e97\\u8be6\\u7ec6\\u5730\\u5740\",\"businesshours\":\"09:00 - 22:00\",\"phone\":\"13888888888\",\"weixin\":\"13888888888\",\"longitude\":\"113.05\",\"latitude\":\"28.24\"}}]', 1, 'normal', 1710555273, 1710555273, NULL);

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_recharge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `facevalue` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '面值',
  `buyprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '售价',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态:normal=正常,hidden=隐藏',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值套餐表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_recharge_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ordersn` varchar(60) NOT NULL DEFAULT '' COMMENT '订单号',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `totalfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `payfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
  `transaction_id` varchar(60) DEFAULT NULL COMMENT '交易单号',
  `paymentjson` text COMMENT '交易原始数据',
  `paytype` varchar(100) DEFAULT '' COMMENT '支付方式:wechat=微信支付,balance=余额支付',
  `paytime` bigint(16) DEFAULT NULL COMMENT '支付时间',
  `ext` text CHARACTER SET utf8mb4 COMMENT '附加字段',
  `platform` enum('wxMiniProgram','wxOfficialAccount') DEFAULT 'wxMiniProgram' COMMENT '平台:wxMiniProgram=微信小程序,wxOfficialAccount=微信公众号',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '订单状态:-2=交易关闭,-1=已取消,0=未支付,1=已支付',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ordersn` (`ordersn`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `createtime` (`createtime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值订单表';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_refund_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ordersn` varchar(45) DEFAULT NULL COMMENT '订单号',
  `refundsn` varchar(45) DEFAULT NULL COMMENT '退款单号',
  `order_id` varchar(45) DEFAULT NULL COMMENT '订单ID',
  `ordertype` varchar(100) DEFAULT '' COMMENT '订单类型',
  `payfee` decimal(10,2) DEFAULT NULL COMMENT '支付金额',
  `refundfee` decimal(10,2) DEFAULT NULL COMMENT '退款金额',
  `paytype` varchar(20) NOT NULL DEFAULT '' COMMENT '付款方式',
  `paymentjson` varchar(1024) DEFAULT NULL COMMENT '退款原始数据',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态:0=退款中,1=退款完成,-1=退款失败',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='退款记录';


CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户',
  `share_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分享人',
  `page` varchar(100) NOT NULL DEFAULT '' COMMENT '分享页面',
  `page_id` int(10) NOT NULL DEFAULT '0' COMMENT '分享页面ID',
  `platform` varchar(20) DEFAULT NULL COMMENT '分享平台',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户分享';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_staff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) DEFAULT '0' COMMENT '绑定会员',
  `role` set('boss','clerk') DEFAULT 'clerk' COMMENT '角色:boss=管理,clerk=职员',
  `headimage` varchar(200) NOT NULL DEFAULT '' COMMENT '头像',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态:normal=在职,hidden=离职',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门店员工表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `businesshours` varchar(255) NOT NULL DEFAULT '' COMMENT '营业时间',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT 'Logo',
  `images` text NOT NULL COMMENT '门店图片',
  `videofile` varchar(255) NOT NULL DEFAULT '' COMMENT '介绍视频',
  `contacts` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `weixin` varchar(100) NOT NULL DEFAULT '' COMMENT '联系微信',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `latitude` varchar(100) NOT NULL DEFAULT '' COMMENT '纬度',
  `longitude` varchar(100) NOT NULL DEFAULT '' COMMENT '经度',
  `content` text COMMENT '详细介绍',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='营地表';


INSERT INTO `__PREFIX__xylease_store` VALUES (1, 'XYlease租赁门店', '09:00 - 22:00', '/assets/addons/xylease/imgs/logo.png', '/assets/addons/xylease/imgs/store.png,/assets/addons/xylease/imgs/store.png,/assets/addons/xylease/imgs/store.png', '', '伍经理', '13888888888', '13888888888', '湖南株洲未来云门店详细地址', '28.24', '113.05', 'XYlease租赁门店详细介绍', NULL, 1710493365);

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_third` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `platform` varchar(30) NOT NULL DEFAULT '' COMMENT '平台',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT 'openid',
  `session_key` varchar(255) NOT NULL DEFAULT '' COMMENT 'session_key',
  `logintime` bigint(16) unsigned DEFAULT NULL COMMENT '登录时间',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`,`platform`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='第三方登录表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_user_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL COMMENT '归属会员',
  `coupon_id` int(10) NOT NULL DEFAULT '0' COMMENT '优惠券',
  `type` enum('reward','discount') NOT NULL DEFAULT 'reward' COMMENT '优惠券类型:reward=满减,discount=折扣',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `useorderid` int(10) DEFAULT '0' COMMENT '使用订单ID',
  `useordertype` varchar(100) DEFAULT '0' COMMENT '使用订单类型',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '面额',
  `atleast` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '满多少元可使用',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣',
  `ishandsel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否可转增:0=否,1=是',
  `handuserid` int(10) NOT NULL DEFAULT '0' COMMENT '赠送用户ID',
  `gettype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '获取方式:1=直接领取,2=系统赠送,3=转赠',
  `usetime` bigint(10) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `endtime` bigint(10) NOT NULL DEFAULT '0' COMMENT '到期时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态:0=未使用,1=已使用,3=已转赠',
  `createtime` bigint(16) DEFAULT '0' COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_user_favorite` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户',
  `type` enum('coach','store','course','goods') NOT NULL DEFAULT 'coach' COMMENT '类型',
  `target_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '目标ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户收藏';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_user_money` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '变更类型',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更费用',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后',
  `service_id` int(10) NOT NULL DEFAULT '0' COMMENT '业务ID',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员余额明细表';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_user_view` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '类型',
  `target_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '目标ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户浏览记录';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_user_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `type` varchar(32) NOT NULL DEFAULT 'bank' COMMENT '账户类型 alipay=支付宝 ,bank=银行卡',
  `accountname` varchar(50) NOT NULL DEFAULT '' COMMENT '账号名称',
  `accountno` varchar(50) NOT NULL DEFAULT '' COMMENT '账号',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户提现账号';

CREATE TABLE IF NOT EXISTS `__PREFIX__xylease_user_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `withdrawsn` varchar(60) NOT NULL DEFAULT '' COMMENT '提现交易号',
  `type` enum('balance','distribution') NOT NULL DEFAULT 'balance' COMMENT '类型:balance=余额,distribution=佣金',
  `applymoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现申请金额',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `accounttype` varchar(50) NOT NULL DEFAULT '' COMMENT '提现账号类型',
  `accountname` varchar(255) NOT NULL DEFAULT '' COMMENT '提现账号名称',
  `servicemoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现手续费',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际转账金额',
  `audittime` bigint(16) NOT NULL DEFAULT '0' COMMENT '审核时间',
  `paymenttime` bigint(16) NOT NULL DEFAULT '0' COMMENT '转账时间',
  `accountno` varchar(255) NOT NULL DEFAULT '' COMMENT '收款账号',
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现手续费比率',
  `refusereason` varchar(500) NOT NULL DEFAULT '' COMMENT '拒绝原因',
  `memo` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `platform` enum('wxMiniProgram','wxOfficialAccount') DEFAULT 'wxMiniProgram' COMMENT '平台:wxMiniProgram=微信小程序',
  `status` int(3) NOT NULL DEFAULT '0' COMMENT '状态0待审核1.待转账2已转账 -1拒绝',
  `createtime` bigint(16) DEFAULT NULL COMMENT '申请时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现表';

ALTER TABLE `__PREFIX__user` ADD COLUMN `xylease_parent_user_id` int(10) NULL DEFAULT 0 COMMENT '上级ID,0=无' AFTER `verification`;
ALTER TABLE `__PREFIX__user` ADD COLUMN `xylease_consume` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '消费金额' AFTER `xylease_parent_user_id`;
ALTER TABLE `__PREFIX__user` ADD COLUMN `xylease_recharge` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '充值金额' AFTER `xylease_consume`;

