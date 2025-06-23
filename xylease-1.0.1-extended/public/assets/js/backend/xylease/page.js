define(['jquery', 'bootstrap', 'backend', 'table', 'form','xylease_vue','xylease_vuedraggable'], function ($, undefined, Backend, Table, Form,Vue,Vuedraggable) {

    var Controller = {
        index: function () {
		    // 初始化表格参数配置
		    Table.api.init({
		        extend: {
		            index_url: 'xylease/page/index' + location.search,
		            add_url: 'xylease/page/add',
		            edit_url: 'xylease/page/edit',
		            del_url: 'xylease/page/del',
		            multi_url: "",
		            table: 'xylease_page',
		        }
		    });
			
		    var table = $("#table");
		    Template.helper("cdnurl", function(image) {
		    	return Fast.api.cdnurl(image); 
		    }); 
		    Template.helper("Moment", Moment);
		
		    // 初始化表格
		    table.bootstrapTable({
		        url: $.fn.bootstrapTable.defaults.extend.index_url,
				pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
		        templateView: true,
				visible: false,
				showToggle: false,
				showColumns: false,
				showExport: false,
		        columns: [
		            [
		                {checkbox: true},
		                {field: 'id', title: __('Id'),operate: false},
						{field: 'page_token', title: __('Token'),operate: false},
						{field: 'type', title: __('Type'), searchList: {"index":__('首页模板'),"user":__('用户中心模板')}, formatter: Table.api.formatter.normal},
		                {field: 'name', title: __('Name'),operate: 'LIKE'},
						{field: 'url', title: __('Url'),operate: false},
						{field: 'cover', title: __('Cover'),operate: false},
		                {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
		            ]
		        ]
		    });
			
			// 使用模板
			$(document).on("click", ".btn-use", function () {
				Fast.api.ajax({
				    url: "xylease/page/use",
				    data: {
						'ids': $(this).data('id'),
					}
				}, function(data, ret){
					 //刷新table表单
					 table.bootstrapTable('refresh');
					 return true;
				});
			});

			// 通用配置
			$(document).on("click", "#btn-appstyle", function () {
				Fast.api.open('xylease/config/set?type=appstyle&tab=other&title=样式通用配置','通用配置',{
					area: ['900px', '600px']
				});
			});
			
			// 修改默认弹窗大小
			Fast.config.openArea = ['100%', '100%'];

		    // 为表格绑定事件
		    Table.api.bindevent(table);
		},
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ""
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'xylease/page/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '140px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'xylease/page/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'xylease/page/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
		
		history: function () {
			// 初始化表格参数配置
			Table.api.init({
			    extend: {
			        index_url: `xylease/page/history/token/${Fast.api.query('token')}`,
			    }
			});
			var table = $("#table");
			// 初始化表格
			table.bootstrapTable({
			    url: $.fn.bootstrapTable.defaults.extend.index_url,
				commonSearch: false,
			    columns: [
			        [
			            {checkbox: true},
						{field: 'id', title: __('Id')},
						{field: 'name', title: __('Name')},
						{field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
						{field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
						{field: 'deletetime', title: __('Deletetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
			            {
			                field: 'operate', title: __('Operate'), events: {
			                    'click .btn-chooseone': function (e, value, row, index) {
									Fast.api.close(row.id);
			                    },
			                }, formatter: function () {
			                    return '<a href="javascript:;" class="btn btn-danger btn-chooseone btn-xs"><i class="fa fa-check"></i> ' + __('还原') + '</a>';
			                }
			            }
			        ]
			    ]
			});
			
			// 为表格绑定事件
			Table.api.bindevent(table);
		},
        add: function () {
            Controller.api.bindevent();
        },
        edit: function() {
			var vm = new Vue({
				el: '#app',
				components: {
					Vuedraggable
				},
				data() {
					return {
						appStyle:Config.appStyle, //通用样式配置
						leaseConfig:Config.leaseConfig,//租赁配置
						module: {
							"basics": [{
									"name": "轮播组件",
									"type": "banner",
									"icon": 'random',
									"params": {
										'autoplay': 1, //是否自动切换
										"interval": "5000", //自动切换间隔时间
										"height": "400", //图片高度
										'indicatorDots': 1, //显示面板指示点
										'indicatorColor': '#ffffff', //指示点颜色
										'indicatorActiveColor': '#f05656',//当前选中的指示点颜色
										'lrmargin':0,
										'borderRadius': 0,
										'showfloat':0,//显示悬浮模块
										'floatimg1':'/assets/addons/xylease/imgs/floatimg.png',
										'floatlink1':"",
										'floatimg2':'/assets/addons/xylease/imgs/floatimg.png',
										'floatlink2':"",
										'floatimg3':'/assets/addons/xylease/imgs/floatimg.png',
										'floatlink3':"",
									},
									"data": [
										{
											"image":'/assets/addons/xylease/imgs/banner.png',
											"link": ""
										}
									],
								},
								{
									"name": "菜单组件",
									"type": "menu",
									"icon": "list",
									"params": {
										"title": "",
										"linktitle":"",
										"link":"",
										"colnum": "3",
										"textimgpl":"2",//文字图片排列
										'lrmargin':30,
										'lrnjj':30,
										'upnjj':40,
										'borderRadius': 0,
										'itemBorderRadius': 10,
										'bgColor': '#FFFCF7',
										'itemBgColor': '#ffffff',
										'imgwh':80,
										'textsize':30,
										'itemjj':30,
										'itemnjj':0,
										'textbold':1,
										'textColor': '#333333'
									},
									"data": [
										{
											"name": "菜单选项",
											"image": "/assets/addons/xylease/imgs/menu.png",
											"link": ""
										},
									]
								},
								
								{
									"name": "标题组件",
									"type": "title",
									'icon': 'header',
									"params": {
										"title": "标题",
										"linktitle":"更多",
										'lrmargin':30,
										'bgColor': '#FFFCF7',
										'borderRadius': 0,
										"link":"",
									}
								},
								{
									"name": "文字组件",
									"type": "text",
									'icon': 'text-width',
									"params": {
										"textContent": "--湖南行云网络提供技术支持--",
										"textColor":"#cccccc",
										"textSize":30,
										"textAlign":"center",
										'lrmargin':30,
									}
								},
								{
									"name": "图片橱窗",
									"type": "image",
									"icon": "photo",
									"params": {
										"imgLayout": 1,
										'lrmargin':30,
										'imgRadius': 0,
									},
									"data": [{
										"image": "/assets/addons/xylease/imgs/ads.png",
										"link": ""
									}]
								},
								{
									"name": "视频组件",
									"type": "video",
									"icon": "photo",
									"params": {
										'lrmargin':30,
										'borderRadius': 0,
										'videourl':"",
										'poster':"/assets/addons/xylease/imgs/poster.png",
									},
								},
								{
									"name": "搜索组件",
									"type": "search",
									'icon': 'search',
									"params": {
										'tiptext': '请输入搜索内容',
										'bgColor': '#ffffff',
										'height': 70,
										'borderRadius': 35,
										'lrmargin':30
									}
								},
								{
									"name": "公告组件",
									"type": "notice",
									"icon": "bullhorn",
									"dataType":"notice",
									"data": [],
									"params": {
										'bgColor': '#ffffff', //背景颜色
										"lefticon": "/assets/addons/xylease/imgs/notice.png", //左侧图标
										'lrmargin':30,
										'borderRadius': 10,
										"scroll":'tb',//滚动方式
									}
								},
								{
									"name": "空白行",
									"type": "empty",
									"icon": "window-maximize",
									"params": {
										"height": "30"
									},
								},
							],
							"store":[
								{
									"name": "门店信息",
									"type": "store",
									"icon": "map",
									"params": {
										'bgColor': '#ffffff',
										'borderRadius': 20,
										'lrmargin':30,
										'njj':30,
										"logo": Config.storeInfo.logo,
										"name": Config.storeInfo.name,
										"address": Config.storeInfo.address,
										"businesshours": Config.storeInfo.businesshours,
										"phone": Config.storeInfo.phone,
										"weixin": Config.storeInfo.weixin,
										"longitude": Config.storeInfo.longitude,
										"latitude": Config.storeInfo.latitude,
									}
								},
							],
							"goods":[
								{
									"name": "商品组件",
									"type": "goods",
									"icon": "archive",
									"dataType":"goods",
									"data": [],
									"params": {
										"title": "推荐产品",
										"linktitle":"更多",
										"link": "",
										"lrmargin":30,
										"njj":30,
										"itemjj":30,
										"itemBorderRadius":10,
										"itemBgColor":'#ffffff',
										"showStyle":1,
										"bgColor": '#ffffff',
										"borderRadius": 20,
										
									}
								},
							],
							"usercenter":[
								{
									"name": "用户卡片",
									"type": "user-card",
									"icon": "user",
									"params": {
										'bgColor': '#ffffff',
										'borderRadius': 20,
										'lrmargin':30,
										'njj':20,
									},
								},
								{
									"name": "钱包模块",
									"type": "user-money",
									"icon": "jpy",
									"params": {
										'bgColor': '#ffffff', //背景颜色
										'borderRadius': 20,
										'lrmargin':30,
										'njj':20,
										"rechargeicon": "/assets/addons/xylease/imgs/recharge.png", //充值图标
										"walleticon": "/assets/addons/xylease/imgs/wallet.png", //钱包图标
									}
								},
							]
						},
						//通用数据
						pageData: Config.page,
						pageType: Config.page.type,
						type: 'page', //选中
						nowTime: '10:00', //时间
						moveDom: "",
						changeDom: "",
						startY: 0,
						endY: 0

					}
				},
				created() {
					this.nowTimes();
				},
				mounted() {
					this.nowTimes();
				},
				filters: {
					formatDate(timestamp) {
						var date = new Date(timestamp * 1000);
						var Y = date.getFullYear() + '-';
						var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
						var D = date.getDate() + ' ';
						var h = date.getHours() + ':';
						var m = date.getMinutes() + ':';
						var s = date.getSeconds();
						return Y + M + D + h + m + s;
					}
				},
				methods: {
					// 保存数据
					publish() {
						var _this = this;
						Fast.api.ajax({
							url: 'xylease/page/edit',
							data: _this.pageData,
						}, function(data, ret){
							//刷新父级页面
							parent.$("a.btn-refresh").trigger("click");
						});
					},
					// 还原历史页面
					historyPage(){
						var _this = this;
						parent.Fast.api.open(`xylease/page/history/token/${_this.pageData.page_token}`, __('历史记录'), {
							area: ['900px', '600px'],
						    callback: function (id) {
								_this.recover(id);
						    }
						});
					},
					// 恢复历史记录
					recover(id) {
						var _this = this;
						Fast.api.ajax({
						    url: "xylease/page/recover",
						    data: {"id":id}
						}, function(data, ret){
							_this.pageData = data;
						});
					},
					onType(e) {
						this.type = e;
					},
					addData(key, arr) {
						Vue.set(vm.pageData.item[key].data, vm.pageData.item[key].data.length, JSON.parse(JSON.stringify(arr)));
					},
					delData(key, num) {
						if (vm.pageData.item[key].data.length == 1) {
							Toastr.warning("最后一个不能删除");
						} else {
							Vue.delete(vm.pageData.item[key].data, num);
						}
					},
					addStyle(key, type, text) {
						Vue.set(vm.pageData.item[key].style, type, text);
					},
					delStyle(key, type) {
						Vue.delete(vm.pageData.item[key].style, type);
					},
					delModule(key) {
						Vue.delete(vm.pageData.item, key);
						this.type = this.type - 1;
					},
					
					addTemplate(arr) {
						this.type = this.pageData.item.length;
						Vue.set(this.pageData.item, this.pageData.item.length, JSON.parse(JSON.stringify(arr))); //数据 追加
					},
					
					// 页面上传图片
					changeImage(event, key, type = false){
						var files = event.target.files[0]; //获取input的图片file值
						var formData = new FormData();
						var upload = Config.upload;
						if(upload.storage !== 'local'){
							var multipart = Object.entries(upload.multipart)[0];
							formData.append(multipart[0], multipart[1]);
						}
						formData.append('file', files, files.name);
						Fast.api.ajax({
						    url: upload.uploadurl, 
							data:formData,
							processData:false,
							contentType:false,
						}, function(data, ret){
							if(type){
								Vue.set(vm.pageData, key, data.fullurl);
							}else{
								Vue.set(vm.pageData.page.style, key, data.fullurl);
							}
						});
					},

					// 页面选择图片
					selectImage(key, type) {
                        var that = this;
                        Fast.api.open("general/attachment/select?multiple=false", "选择图片", {
                            callback: function (data) {
                                if(type){
									Vue.set(vm.pageData, key, that.cdnurl(data.url));
								}else{
									Vue.set(vm.pageData.page.style, key, that.cdnurl(data.url));
								}
                            }
                        });
                        return false;
                    },


					// 配置上传图片
					paramsUpload(event, index, type) {
						var files = event.target.files[0];
						var formData = new FormData();
						var upload = Config.upload;
						if(upload.storage !== 'local'){
							var multipart = Object.entries(upload.multipart)[0];
							formData.append(multipart[0], multipart[1]);
						}
						formData.append('file', files, files.name);
						Fast.api.ajax({
						    url: upload.uploadurl, 
							data:formData,
							processData:false,
							contentType:false,
						}, function(data, ret){
							Vue.set(vm.pageData.item[index].params, type, data.url);
						});
					},

					// 配置选择图片
					paramsAttSelect(index, type) {
                        Fast.api.open("general/attachment/select?multiple=false", "选择图片", {
                            callback: function (data) {
                                Vue.set(vm.pageData.item[index].params, type, data.url);
                            }
                        });
                        return false;
                    },

					// 数据选择图片
					dataAttSelect(key, num, type) {
                        var that = this;
                        Fast.api.open("general/attachment/select?multiple=false", "选择图片", {
                            callback: function (data) {
                                switch (type) {
                                    case "image":
										Vue.set(vm.pageData.item[key].data[num], type, that.cdnurl(data.url));
                                        break;
                                }
                            }
                        });
                        return false;
                    },

					// 数据上传图片
					dataUpload(event, key, num, type) {
						var files = event.target.files[0]; //获取input的图片file值
						var formData = new FormData();
						var upload = Config.upload;
						if(upload.storage !== 'local'){
							var multipart = Object.entries(upload.multipart)[0];
							formData.append(multipart[0], multipart[1]);
						}
						formData.append('file', files, files.name);
						Fast.api.ajax({
						    url: upload.uploadurl, 
							data:formData,
							processData:false,
							contentType:false,
						}, function(data, ret){
							Vue.set(vm.pageData.item[key].data[num], type, data.fullurl);
						});
					},
					
					// 选择链接
					selectLink(key, num, type, multiple){
						parent.Fast.api.open("xylease/link/select?multiple=" + multiple, __('选择链接'), {
							area: ['70%', '80%'],
						    callback: function (data) {
								console.log(data);
								if(num == -1){
									Vue.set(vm.pageData.item[key].params, type, data.url);
								}else{
									Vue.set(vm.pageData.item[key].data[num], type, data.url);	
								}
								
						    }
						});
					},

					// 选择商品
					selectGoods(key,multiple){
						parent.Fast.api.open("xylease/goods/goods/select?multiple=" + multiple, __('选择商品'), {
							area: ['80%', '80%'],
							callback: function (data) {
								Vue.set(vm.pageData.item[key],'data',data);	
							}
						});
					},

					// 选择公告
					selectNotice(key,multiple){
						parent.Fast.api.open("xylease/notice/select?multiple=" + multiple, __('选择公告'), {
							area: ['80%', '80%'],
							callback: function (data) {
								Vue.set(vm.pageData.item[key],'data',data);	
							}
						});
					},

					// 删除数据
					delData(key,index){
						vm.pageData.item[key].data.splice(index,1);
					},
					
					// 获取当前时间函数
					timeFormate(timeStamp) {
						var hh = new Date(timeStamp).getHours() < 10 ? "0" + new Date(timeStamp).getHours() : new Date(timeStamp).getHours();
						var mm = new Date(timeStamp).getMinutes() < 10 ? "0" + new Date(timeStamp).getMinutes() : new Date(
							timeStamp).getMinutes();
						this.nowTime = hh + ":" + mm;
					},
					// 定时器函数
					nowTimes() {
						this.timeFormate(new Date());
						setInterval(this.nowTimes, 50 * 1000);
					},
					cdnurl(url){
						if(url) return Fast.api.cdnurl(url);
					},
					getParameter(name) {
						var language = {
							'basics': '基础板块',
							'store': '门店板块',
							'goods': '商品板块',
							'interval': '轮播速度(毫秒)',
							'height': '组件高度(px)',
							'colnum': '每行数量',
							'nums': '显示数量',
							'borderRadius': '模块圆角(px)',
							'itemBorderRadius':'选项圆角(px)',
							'name': '名称',
							'title': '标题',
							'intro': '简介',
							'lrmargin': '左右间距(px)',
							'tbmargin': '上下间距(px)',
							'tiptext': '提示文字',
							'linktitle': '链接文字',
							'usercenter':'会员中心',
							'scroll':'滚动方式',
							'imgwh':'图片大小(px)',
							'textsize':'文字大小(px)',
							'itemjj':'选项间距',
							'storename':'门店名称',
							'address':'地址',
							'businesshours':'开放时间',
							'phone':'电话',
							'weixin':'微信',
							'longitude':'经度',
							'latitude':'纬度',
							'njj':'模块内间距',
							'itemnjj':'选项内间距',
							'imgRadius':'图片圆角',
							'imagejj':'图片间距',
							'lcbg':'分类背景',
							'lcabg':'选中分类背景',
							'showcimg':'显示分类图片',
							'lcbr':'分类圆角(px)',
							'lcjj':'分类间距(px)',
							'lrnjj':'左右内间距',
							'upnjj':'上下内间距',
							'videourl':'视频',
							'poster':'视频封面',
							'textSize':'文字大小(px)',
							'textContent':'文字内容'
						};
						return language.hasOwnProperty(name)?language[name]:name;
					}
				}
			});
			
			
		},
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
