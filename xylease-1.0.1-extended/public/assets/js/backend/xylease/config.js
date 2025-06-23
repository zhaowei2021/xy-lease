define(['jquery', 'bootstrap', 'backend', 'table', 'form','xylease_vue'], function ($, undefined, Backend, Table, Form,Vue) {

    var Controller = {
        index: function () {
            new Vue({
                el: "#configIndex",
                data() {
                    return {
                        activeName: "basic",
                        configData: {
                            basic: [
                                {
                                    id: 'xylease',
                                    title: '通用配置',
                                    tip: '系统通用配置',
                                },
                                {
                                    id: 'share',
                                    title: '分享配置',
                                    tip: '配置分享标题、图片',
                                },
                                {
                                    id:'distribution',
                                    title:'分销配置',
                                    tip: '分销配置信息',
                                },
                                {
                                    id: 'lease',
                                    title: '租赁配置',
                                    tip: '配置租赁规则等',
                                },
                                {
                                    id: 'withdraw',
                                    title: '提现配置',
                                    tip: '配置提现手续费、最小最大金额等',
                                },
                            ],
                            platform: [
                                {
                                    id: 'wxminiprogram',
                                    title: '微信小程序',
                                    tip: '配置微信小程序',
                                },
                                
                            ],
                            payment: [
                                {
                                    id: 'wechat',
                                    title: '微信支付',
                                    tip: '配置微信支付',
                                },
                            ],
                            other:[
                                {
                                    id:'appstyle',
                                    title:'模板通用配置'
                                },
                            ]
                        }
                    }
                },
                methods: {
                    tabClick(name) {
                        this.activeName = name;
                    },
                    operation(id, title) {
                        var that = this;
                        Fast.api.open("xylease/config/set?type=" + id + "&tab=" + that.activeName + "&title=" + title, title,{
                            area: ['80%', '80%']
                        });
                    },
                },
            })
        },
        set: function () {
            new Vue({
                el: "#configSet",
                data() {
                    return {
                        setData: {
                            xylease:{
                                useravatar:'/assets/addons/xylease/imgs/logo.jpg',
                                agreement:'',
                                privacy:'',
                                ticketagree:''
                            },
                            share: {
                                title: 'XYlease露营助手',
                                image: '/assets/addons/xylease/imgs/share.png',
                                user_poster_bg:'/assets/addons/xylease/imgs/sharebg.jpg'
                            },
                            wxminiprogram: {
                                app_id: '',
                                secret: '',
                            },
                            wechat: {
                                mch_id: '',
                                key: '',
                                cert_client: '',
                                cert_key: '',
                            },
                            appstyle:{
                                mainColor:'#f05656', //主色调
                                navBarBgColor:'#ffffff', //导航栏背景颜色
                                navBarFrontColor:'#000000', //导航栏字体颜色
                                pageBgColor:'#f7f7f7', //页面背景颜色
                                textMainColor:'#333333', //文字主色调
                                textLightColor:'#808080', //文字浅色调
                                textPriceColor:'#ff5335',//价格文字颜色
                                pageModuleBgColor:'#ffffff', //模块背景颜色
                            },
                            withdraw: {
                                methods: [],
                                rate: 0.6, //手续费%
                                min: 100,
                                max: 10000,
                            },
                            distribution:{
                                open:1,//是否开启分销
                                isdis:1,//成为分销商方式
                                child_condition:'share',//绑定下级条件
                                level:'1',//分销层级
                                
                            },
                            lease:{
                                deliverytype:[],// 发货方式
                                leasetype:[],// 可选租赁类型
                                defaultlease:'days',//默认租赁类型
                                rule:'',// 租赁规则
                                hourzt:'09:00',// 小时租开始时间
                                hourst:'2',// 小时租提前取货小时
                                starthour:'5',// 小时租起租小时
                                daysst:'09:00',// 全日租最早取货时间
                                nightst:'09:00',// 过夜租最早取货时间
                                nightet:'10:59',// 过夜租次日归还时间

                            }
                        },
                        type: Config.type, 
                        tab: new URLSearchParams(location.search).get('tab'),
                        title: new URLSearchParams(location.search).get('title'),
                        detailForm: {},
                    }
                },
                mounted() {
                    this.operationData();
                },
                methods: {

                    operationData() {
                        this.detailForm = this.setData[this.type]
                        if (Config.row) {
                            for (key in this.detailForm) {
                                if (Config.row[key]) {
                                    if (Config.row[key] instanceof Object) {
                                        for (inner in Config.row[key]) {
                                            if (Config.row[key][inner]) {
                                                this.detailForm[key][inner] = Config.row[key][inner]
                                            }
                                        }
                                    } else {
                                        this.detailForm[key] = Config.row[key]
                                    }
                                }
                            }
                        }
                    },
                    
                    submitFrom(type) {
                        var that = this; 
                        
                        if (type == 'yes') {
                            
                            var submitData = JSON.parse(JSON.stringify(that.detailForm))
                            
                            Fast.api.ajax({
                                url: 'xylease/config/set?type=' + that.type,
                                loading: true,
                                type: 'POST',
                                data: {
                                    data: JSON.stringify(submitData),
                                    group: that.tab,
                                    title: that.title
                                },
                            }, function (ret, res) {
                                Fast.api.close()
                            })
                        } else {
                            Fast.api.close()
                        }
                    },

                    attSelect(field,type = 'image') {
                        var that = this;
                        Fast.api.open("general/attachment/select?multiple=false", "选择图片", {
                            callback: function (data) {
                                switch (type) {
                                    case "image":
                                        that.detailForm[field] = that.cdnurl(data.url);
                                        break;
                                }
                            }
                        });
                        return false;
                    },

                    articleSelect(field,multiple = 'false'){
                        var that = this;
                        Fast.api.open("xylease/article/select?multiple="+multiple, "选择文章", {
                            callback: function (data) {
                                console.log(data);
                                that.detailForm[field] = data.id;
                            }
                        });
                        return false;
                    },

                    cdnurl(url){
					    var url = Fast.api.cdnurl(url);
                        if(url.substr(0,7).toLowerCase() == "http://" || url.substr(0,8).toLowerCase() == "https://"){
                            return url;
                        }else{
                            return window.location.protocol +'//'+ window.location.host + url;
                        }
					},
                    
                },
            })

            
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});