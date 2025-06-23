define(['jquery', 'bootstrap', 'backend', 'table', 'form','xylease_vue'], function ($, undefined, Backend, Table, Form,Vue,Vuedraggable) {

    var Controller = {
        index: function () {
		    
			var vm = new Vue({
				el: '#bottomNav',
				data() {
					return {
						tabbarList:{
							backgroundColor: "#FFFFFF",
							textColor: "#333333",
							textHoverColor: "#f05656",
							style:1,
							list:[
								{
									title: "首页",
									link: "/pages/index",
									iconPath: "/assets/addons/xylease/imgs/tabbar/index.png",
									selectedIconPath: "/assets/addons/xylease/imgs/tabbar/index-a.png",
									show: 1,
								},
								{
									title: "分类",
									link: "/pages/category",
									iconPath: "/assets/addons/xylease/imgs/tabbar/category.png",
									selectedIconPath: "/assets/addons/xylease/imgs/tabbar/category-a.png",
									show: 1,
								},
								{
									title: "租物车",
									link: "/pages/cart",
									iconPath: "/assets/addons/xylease/imgs/tabbar/cart.png",
									selectedIconPath: "/assets/addons/xylease/imgs/tabbar/cart-a.png",
									show: 1,
								},
								{
									title: "我的",
									link: "/pages/user",
									iconPath: "/assets/addons/xylease/imgs/tabbar/user.png",
									selectedIconPath: "/assets/addons/xylease/imgs/tabbar/user-a.png",
									show: 1,
								},
							],
						},
					}
				},
				created() {
					this.operationData();
                },
			
				methods: {
					operationData() {
                        if (Config.row) {
                            for (key in this.tabbarList) {
                                if (Config.row[key]) {
                                    if (Config.row[key] instanceof Object) {
                                        for (inner in Config.row[key]) {
                                            if (Config.row[key][inner]) {
                                                this.tabbarList[key][inner] = Config.row[key][inner]
                                            }
                                        }
                                    } else {
                                        this.tabbarList[key] = Config.row[key]
                                    }
                                }
                            }
                        }
                    },

					submitFrom() {
                        var submitData = JSON.parse(JSON.stringify(this.tabbarList))
						Fast.api.ajax({
							url: 'xylease/tabbar/index',
							loading: true,
							type: 'POST',
							data: {
								data: JSON.stringify(submitData),
							},
						}, function (ret, res) {
							
						})
                    },

					// 上传图片
					uploadImage(event, key, type) {
						var files = event.target.files[0]; //获取input的图片file值
						var formData = new FormData();
						var upload = Config.upload;
						if(upload.storage !== 'local'){
							var multipart = Object.entries(upload.multipart)[0];
							formData.append(multipart[0], multipart[1]);
						}
						formData.append('file', files, files.name);
						var item = this.tabbarList['list'][key],that = this;
						Fast.api.ajax({
						    url: upload.uploadurl, 
							data:formData,
							processData:false,
							contentType:false,
						}, function(data, ret){
							if(type == 1){
								item['iconPath'] = data.fullurl;
							}else{
								item['selectedIconPath'] = data.fullurl;
							}
							
							Vue.set(that.tabbarList['list'],key, item)
						});
					},
					
					// 选择链接
					selectLink(key){
						var item = this.tabbarList['list'][key],that = this;
						parent.Fast.api.open("xylease/link/select?multiple=false", __('选择链接'), {
							area: ['70%', '80%'],
						    callback: function (data) {
								item['link'] = data.url;
								Vue.set(that.tabbarList['list'],key, item)
						    }
						});
					},

					tnameInput(event,key){
						var item = this.tabbarList['list'][key],that = this;
						Vue.set(that.tabbarList['list'],key, item)
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
