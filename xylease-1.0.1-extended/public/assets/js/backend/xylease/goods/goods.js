define(['jquery', 'bootstrap', 'backend', 'table', 'form','xylease_vue'], function ($, undefined, Backend, Table, Form,Vue) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/goods/goods/index/type/'+ Config.type + location.search,
                    add_url: 'xylease/goods/goods/add/type/'+ Config.type,
                    edit_url: 'xylease/goods/goods/edit',
                    del_url: 'xylease/goods/goods/del',
                    multi_url: 'xylease/goods/goods/multi',
                    import_url: 'xylease/goods/goods/import',
                    table: 'xylease_goods_goods',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'stock', title: __('Stock'),visible:Config.type != 'package'},
                        {field: 'sales', title: Config.type == 'single'||Config.type == 'package' ? '已租' : '已售'},
                        {field: 'favorite', title: __('Favorite')},
                        {field: 'views', title: __('Views')},
                        {field: 'status', title: __('Status'), searchList: {"up":__('Status up'),"down":__('Status down')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons :[
                                {
                                    name: 'detail',
                                    text: __('调整库存'),
                                    title: function (row) {
                                        return row.name;
                                    },
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    icon: 'fa fa-database',
                                    visible: Config.type != 'package',
                                    extend: 'data-area=\'["60%", "80%"]\'',
                                    url: function (row) {
                                        return `xylease/goods/goods/editStock?ids=${row.id}`;
                                    }
                                },
                            ],
                        }
                    ]
                ]
            });

            //修改默认弹窗大小
            Fast.config.openArea = ['90%', '90%'];

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        select: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/goods/goods/select' + location.search,
                }
            });
            var dataArr = [];
            var multiple = Backend.api.query('multiple');
            multiple = multiple == 'true' ? true : false;

            var table = $("#table");

            table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function (e, row) {
                if (e.type == 'check' || e.type == 'uncheck') {
                    row = [row];
                } else {
                    dataArr = [];
                }
                $.each(row, function (i, j) {
                    if (e.type.indexOf("uncheck") > -1) {
                        var index = dataArr.indexOf(j.id);
                        if (index > -1) {
                            dataArr.splice(index, 1);
                        }
                    } else {
                        dataArr.indexOf(j.id) == -1 && dataArr.push(j);
                    }
                });
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'id',
                showToggle: false,
                showExport: false,
                maintainSelected: true,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {field: 'state', checkbox: multiple, visible: multiple, operate: false},
                        {field: 'type', title: __('Type'), searchList: {"single":__('Type single'),"package":__('Type package'),"sell":__('Type sell'),"service":__('Type service')}, formatter: Table.api.formatter.normal},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'stock', title: __('Stock'),visible:Config.type != 'package'},
                        {field: 'status', title: __('Status'), searchList: {"up":__('Status up'),"down":__('Status down')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate', title: __('Operate'), width: 85, events: {
                                'click .btn-chooseone': function (e, value, row, index) {
                                    var multiple = Backend.api.query('multiple');
		                            multiple = multiple == 'true' ? true : false;
                                    var data = multiple ? [row] : row;
                                    Fast.api.close(data);
                                },
                            }, formatter: function () {
                                return '<a href="javascript:;" class="btn btn-danger btn-chooseone btn-xs"><i class="fa fa-check"></i> ' + __('Choose') + '</a>';
                            }
                        }
                    ]
                ]
            });

            // 绑定过滤事件
            $('.filter-type ul li a', table.closest(".panel-intro")).on('click', function (e) {
                $(this).closest("ul").find("li").removeClass("active");
                $(this).closest("li").addClass("active");
                var field = 'mimetype';
                var value = $(this).data("value") || '';
                var object = $("[name='" + field + "']", table.closest(".bootstrap-table").find(".commonsearch-table"));
                if (object.prop('tagName') == "SELECT") {
                    $("option[value='" + value + "']", object).prop("selected", true);
                } else {
                    object.val(value);
                }
                table.trigger("uncheckbox");
                table.bootstrapTable('refresh', {pageNumber: 1});
            });

            // 选中多个
            $(document).on("click", ".btn-choose-multi", function () {
                Fast.api.close(dataArr);
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            require(['upload'], function (Upload) {
                $("#toolbar .faupload").data("category", function (file) {
                    var category = $("ul.nav-tabs[data-field='category'] li.active a").data("value");
                    return category;
                });
                Upload.api.upload($("#toolbar .faupload"), function () {
                    $(".btn-refresh").trigger("click");
                });
            });
        },

        add: function () {

            var goodsDetail = new Vue({
                el: "#goodsDetail",
                data() {
                    return {
                        type:Config.type,
                        issku:0,
                        skuList: [],
                        skuPrice: [],
                        skuListData: '',
                        skuPriceData: '',
                        skuModal: '',
                        childrenModal: [],
                        countId: 1,
                        isEditInit: false, // 编辑时候初始化是否完成
                        isResetSku: 0,
                        isdis:0,//是否参与分销
                        disrule:0,//分佣方式
                        commissionRule:[],//佣金规则
                        disLevel:Config.disLevel,//分销等级
                        goodsList:[],
                    }
                },
                mounted() {
                    this.getInit([], []);
                },
                methods: {
                    changeSku(value){
                        this.issku = value;
                    },
                    //是否分销
                    changeDis(value){
                        this.isdis = value;
                    },
                    //切换分销
                    changeRule(value){
                        this.disrule = value;
                    },
                    //设置分销规则
                    setCommissionRule(){
                        var commissionRule = [];
                       
                        if(this.skuPrice.length == 0){
                            this.disLevel.forEach((item,index) => {
                                commissionRule.push({
                                    id:index,
                                    level_id:item.id,
                                    level_name:item.name,
                                    goodsskutext:'',
                                    commission_one:0.00,
                                    commission_two:0.00
                                });
                            });
                        }else{
                            this.skuPrice.forEach((item1,index1)=>{
                                if(item1.commissionrule){
                                    var commissionRuleTemp = JSON.parse(item1.commissionrule);
    
                                    this.disLevel.forEach((item,index) => {
                                        var flag = false;
                                        commissionRuleTemp.forEach((item2,index2)=>{
                                            if(item.id == item2.level_id){
                                                flag = true;
                                            }
                                        })
                                        if(!flag){
                                            commissionRuleTemp.push({
                                                id:item1.temp_id?item1.temp_id:item1.id,
                                                level_id:item.id,
                                                level_name:item.name,
                                                goodsskutext:item1.goodsskutext,
                                                commission_one:0.00,
                                                commission_two:0.00
                                            });
                                        }
                                        
                                    });
    
                                    commissionRule = commissionRule.concat(commissionRuleTemp)
    
                                }else{
                                    this.disLevel.forEach((item,index) => {
                                        commissionRule.push({
                                            id:item1.temp_id?item1.temp_id:item1.id,
                                            level_id:item.id,
                                            level_name:item.name,
                                            goodsskutext:item1.goodsskutext,
                                            commission_one:0.00,
                                            commission_two:0.00
                                        });
                                    });
                                    
                                    
                                }
                            });
                        }
                        
                        this.commissionRule = commissionRule;
                    },
                    
                    
                    getInit(skuList, skuPrice) {
                        var tempIdArr = {};
                        for (var i in skuList) {
                            skuList[i]['temp_id'] = this.countId++
                            for (var j in skuList[i]['children']) {
                                skuList[i]['children'][j]['temp_id'] = this.countId++
                                tempIdArr[skuList[i]['children'][j]['id']] = skuList[i]['children'][j]['temp_id']
                            }
                        }
                        for (var i = 0; i < skuPrice.length; i++) {
                            var tempSkuPrice = skuPrice[i]
                            tempSkuPrice['temp_id'] = i + 1

                            tempSkuPrice['goods_sku_temp_ids'] = [];
                            var goods_sku_id_arr = tempSkuPrice['goods_sku_ids'].split(',');
                            for (var ids of goods_sku_id_arr) {
                                tempSkuPrice['goods_sku_temp_ids'].push(tempIdArr[ids])
                            }

                            skuPrice[i] = tempSkuPrice
                        }
                        
                        this.skuList = skuList;
                        this.skuPrice = skuPrice;
                        this.setCommissionRule();
                        setTimeout(() => {
                            this.isEditInit = true;
                        }, 100)
                    },
                    
                    addImg(index, multiple) {
                        var that = this;
                        parent.Fast.api.open("general/attachment/select?multiple=" + multiple, "选择图片", {
                            callback: function (data) {
                                that.skuPrice[index].image = data.url;
                            }
                        });
                        return false;
                    },

                    delImg(index) {
                        this.skuPrice[index].image = '';
                    },

                    //选择商品
                    goodsSelect(){
                        var that = this;
                        parent.Fast.api.open("xylease/goods/sku_price/select?multiple=true", __('选择'), {
                            area: ['80%', '80%'],
                            callback: function (data) {

                                console.log('data',data);

                                var goodsList = that.goodsList;
                                data.forEach((item,index)=>{
                                    item['nums'] = 1;
                                    var flag = false;
                                    goodsList.forEach((item1,index1)=>{
                                        if(item1.goods_sku_price_id == item.goods_sku_price_id){
                                            flag = true;
                                        }
                                    })
                                    if(!flag){
                                        goodsList.push({goods_id:item.goods.id,goods_sku_price_id:item.id,nums:1,goodsimage:item.image?item.image:item.goods.image,goodsname:item.goods.name,goodsskutext:item.goodsskutext});
                                    }
                                    
                                });
                                that.goodsList = goodsList
                            }
                        });
                    },

                    //删除商品
                    deleteGoods(index){
                        this.goodsList.splice(index,1);
                    },

                    //添加主规格
                    addMainSku() {
                        this.skuList.push({
                            id: 0,
                            temp_id: this.countId++,
                            name: this.skuModal,
                            pid: 0,
                            children: []
                        })
                        this.skuModal = '';
                        this.buildSkuPriceTable()
                    },
                    //添加子规格
                    addChildrenSku(k) {
                        var isExist = false
                        this.skuList[k].children.forEach(e => {
                            if (e.name == this.childrenModal[k] && e.name != "") {
                                isExist = true
                            }
                        })

                        if (isExist) {
                            Toastr.error('子规格已存在');
                            return false;
                        }

                        this.skuList[k].children.push({
                            id: 0,
                            temp_id: this.countId++,
                            name: this.childrenModal[k],
                            pid: this.skuList[k].id,
                        })

                        this.childrenModal[k] = '';

                        if (this.skuList[k].children.length == 1) {
                            this.skuPrice = [] // 规格大变化，清空skuPrice
                            this.isResetSku = 1; // 重置规格
                        }

                        this.buildSkuPriceTable();
                        this.setCommissionRule();
                    },
                    //删除主规格
                    deleteMainSku(k) {
                        var data = this.skuList[k]
                        this.skuList.splice(k, 1)
                        if (data.children.length > 0) {
                            this.skuPrice = [] // 规格大变化，清空skuPrice
                            this.isResetSku = 1; // 重置规格
                        }
                        this.buildSkuPriceTable()
                    },
                    //删除子规格
                    deleteChildrenSku(k, i) {
                        var data = this.skuList[k].children[i]
                        this.skuList[k].children.splice(i, 1)

                        var deleteArr = []
                        this.skuPrice.forEach((item, index) => {
                            item.goodsskutext.forEach((e, i) => {
                                if (e == data.name) {
                                    deleteArr.push(index)
                                }
                            })
                        })
                        deleteArr.sort(function (a, b) {
                            return b - a;
                        })
                        // 移除有相关子规格的项
                        deleteArr.forEach((i, e) => {
                            this.skuPrice.splice(i, 1)
                        })

                        // 当前规格项，所有子规格都被删除，清空 skuPrice
                        if (this.skuList[k].children.length <= 0) {
                            this.skuPrice = [] // 规格大变化，清空skuPrice
                            this.isResetSku = 1; // 重置规格
                        }
                        this.buildSkuPriceTable()
                    },
                    editStatus(i) {
                        if (this.skuPrice[i].status == 'up') {
                            this.skuPrice[i].status = 'down'
                        } else {
                            this.skuPrice[i].status = 'up'
                        }

                    },
                    //组合新的规格价格库存重量编码图片
                    buildSkuPriceTable() {
                        var arr = [];
                        this.skuList.forEach((s1, k1) => {
                            var children = s1.children;
                            var childrenIdArray = [];
                            if (children.length > 0) {
                                children.forEach((s2, k2) => {
                                    childrenIdArray.push(s2.temp_id);
                                })
                                arr.push(childrenIdArray);
                            }
                        })

                        this.recursionSku(arr, 0, []);
                        this.setCommissionRule();
                    },
                    //递归找规格集合
                    recursionSku(arr, k, temp) {
                        if (k == arr.length && k != 0) {
                            var tempDetail = []
                            var tempDetailIds = []

                            temp.forEach((item, index) => {
                                for (var sku of this.skuList) {
                                    for (var child of sku.children) {
                                        if (item == child.temp_id) {
                                            tempDetail.push(child.name)
                                            tempDetailIds.push(child.temp_id)
                                        }
                                    }
                                }
                            })

                            var flag = false // 默认添加新的
                            for (var i = 0; i < this.skuPrice.length; i++) {
                                if (this.skuPrice[i].goods_sku_temp_ids.join(',') == tempDetailIds.join(',')) {
                                    flag = i
                                    break;
                                }
                            }

                            if (flag === false) {
                                this.skuPrice.push({
                                    id: 0,
                                    temp_id: this.skuPrice.length + 1,
                                    goods_sku_ids: '',
                                    goods_id: 0,
                                    weigh: 0,
                                    image: '',
                                    stock: 0,
                                    price: 0,
                                    sn: '',
                                    status: 'up',
                                    goodsskutext: tempDetail,
                                    goods_sku_temp_ids: tempDetailIds,
                                });
                            } else {
                                this.skuPrice[flag].goodsskutext = tempDetail
                                this.skuPrice[flag].goods_sku_temp_ids = tempDetailIds
                            }
                            return;
                        }
                        if (arr.length) {
                            for (var i = 0; i < arr[k].length; i++) {
                                temp[k] = arr[k][i]
                                this.recursionSku(arr, k + 1, temp)
                            }
                        }
                    },
                    
                },
                
                watch: {
                    skuList: {
                        handler(newName, oldName) {
                            if (this.isEditInit) {
                                this.buildSkuPriceTable();
                            }
                        },
                        deep: true
                    }
                },
            })

            Form.api.bindevent($("form[role=form]"), function(data, ret){
                console.log(data,ret);
            }, function(data, ret){
                console.log(data,ret);
            }, function(success, error){

                var skuPrice =  goodsDetail.skuPrice;
                
                if(goodsDetail.isdis == 1 && goodsDetail.disrule == 1){
                    if(skuPrice.length == 0){
                        skuPrice.push({commissionrule:goodsDetail.commissionRule});
                    }else{
    
                        skuPrice.forEach((item,index)=>{
                            var scr = [];
                            goodsDetail.commissionRule.forEach((item1,index1)=>{
                                if(item.temp_id == item1.id){
                                    scr.push(item1);
                                    
                                }
                            });
                            skuPrice[index]['commissionrule'] = scr;
                        })
                    }
                }

                var sku = JSON.stringify(
                    {
                        listData: JSON.stringify(goodsDetail.skuList),
                        priceData: JSON.stringify(skuPrice)
                    }
                ); 

                $("#skuData").val(sku);

                if(goodsDetail.type == 'package' && goodsDetail.goodsList.length == 0){
                    Toastr.error("请选择套餐包含商品");
                    return false;
                }

                $("#goodslist").val(JSON.stringify(goodsDetail.goodsList));
            });
        },
        edit: function () {
            var goodsDetail = new Vue({
                el: "#goodsDetail",
                data() {
                    return {
                        type: Config.type,
                        issku: Config.issku,
                        skuList: [],
                        skuPrice: [],
                        skuListData: '',
                        skuPriceData: '',
                        skuModal: '',
                        childrenModal: [],
                        countId: 1,
                        isEditInit: false, // 编辑时候初始化是否完成
                        isResetSku: 0,
                        isdis:Config.isdis,//是否参与分销
                        disrule:Config.disrule,//分佣方式
                        commissionRule:[],//佣金规则
                        disLevel:Config.disLevel,//分销等级
                        goodsList:Config.goodsList,
                    }
                },
                mounted() {
                    this.getInit(Config.skuList, Config.skuPrice);
                },
                methods: {
                    changeSku(value){
                        this.issku = value;
                    },
                    
                    getInit(skuList, skuPrice) {
                        var tempIdArr = {};
                        for (var i in skuList) {
                            skuList[i]['temp_id'] = this.countId++
                            for (var j in skuList[i]['children']) {
                                skuList[i]['children'][j]['temp_id'] = this.countId++
                                tempIdArr[skuList[i]['children'][j]['id']] = skuList[i]['children'][j]['temp_id']
                            }
                        }
                        for (var i = 0; i < skuPrice.length; i++) {
                            var tempSkuPrice = skuPrice[i]
                            tempSkuPrice['temp_id'] = i + 1

                            tempSkuPrice['goods_sku_temp_ids'] = [];
                            var goods_sku_id_arr = tempSkuPrice['goods_sku_ids'] ? tempSkuPrice['goods_sku_ids'].split(',') : '';
                            for (var ids of goods_sku_id_arr) {
                                tempSkuPrice['goods_sku_temp_ids'].push(tempIdArr[ids])
                            }

                            skuPrice[i] = tempSkuPrice
                        }
                        
                        this.skuList = skuList;
                        this.skuPrice = skuPrice;
                        this.setCommissionRule();

                        setTimeout(() => {
                            this.isEditInit = true;
                        }, 100)
                    },

                    //设置分销规则
                    setCommissionRule(){
                        var commissionRule = [];
                        this.skuPrice.forEach((item1,index1)=>{
                            if(item1.commissionrule){
                                var commissionRuleTemp = JSON.parse(item1.commissionrule);

                                //删除不存在的等级
                                commissionRuleTemp.forEach((e1,di1)=>{
                                    var f = false;
                                    this.disLevel.forEach((e2,di1) => {
                                        if(e1.level_id == e2.id){
                                            f = true;
                                        }
                                        
                                    });
                                    if(!f){
                                        commissionRuleTemp.splice(di1,1);
                                    }
                                })

                                //添加新的等级
                                this.disLevel.forEach((item,index) => {
                                    var flag = false;
                                    commissionRuleTemp.forEach((item2,index2)=>{
                                        if(item.id == item2.level_id){
                                            flag = true;
                                        }
                                    })
                                    if(!flag){
                                        commissionRuleTemp.push({
                                            id:item1.temp_id?item1.temp_id:item1.id,
                                            level_id:item.id,
                                            level_name:item.name,
                                            goodsskutext:item1.goodsskutext,
                                            commission_one:0.00,
                                            commission_two:0.00
                                        });
                                    }
                                    
                                });

                                commissionRule = commissionRule.concat(commissionRuleTemp)

                            }else{
                                this.disLevel.forEach((item,index) => {
                                    commissionRule.push({
                                        id:item1.temp_id?item1.temp_id:item1.id,
                                        level_id:item.id,
                                        level_name:item.name,
                                        goodsskutext:item1.goodsskutext,
                                        commission_one:0.00,
                                        commission_two:0.00
                                    });
                                });
                                
                                
                            }
                        });
                        
                        this.commissionRule = commissionRule;

                    },
                    
                    addImg(index, multiple) {
                        var that = this;
                        parent.Fast.api.open("general/attachment/select?multiple=" + multiple, "选择图片", {
                            callback: function (data) {
                                that.skuPrice[index].image = data.url;
                            }
                        });
                        return false;
                    },

                    delImg(index) {
                        this.skuPrice[index].image = '';
                    },

                    //添加主规格
                    addMainSku() {
                        this.skuList.push({
                            id: 0,
                            temp_id: this.countId++,
                            name: this.skuModal,
                            pid: 0,
                            children: []
                        })
                        this.skuModal = '';
                        this.buildSkuPriceTable()
                    },

                    //添加子规格
                    addChildrenSku(k) {
                        var isExist = false
                        this.skuList[k].children.forEach(e => {
                            if (e.name == this.childrenModal[k] && e.name != "") {
                                isExist = true
                            }
                        })

                        if (isExist) {
                            Toastr.error('子规格已存在');
                            return false;
                        }

                        this.skuList[k].children.push({
                            id: 0,
                            temp_id: this.countId++,
                            name: this.childrenModal[k],
                            pid: this.skuList[k].id,
                        })

                        this.childrenModal[k] = '';

                        if (this.skuList[k].children.length == 1) {
                            this.skuPrice = [] // 规格大变化，清空skuPrice
                            this.isResetSku = 1; // 重置规格
                        }

                        this.buildSkuPriceTable();
                        
                    },

                    //删除主规格
                    deleteMainSku(k) {
                        var data = this.skuList[k]
                        this.skuList.splice(k, 1)
                        if (data.children.length > 0) {
                            this.skuPrice = [] // 规格大变化，清空skuPrice
                            this.isResetSku = 1; // 重置规格
                        }
                        this.buildSkuPriceTable()
                    },

                    //删除子规格
                    deleteChildrenSku(k, i) {
                        var data = this.skuList[k].children[i]
                        this.skuList[k].children.splice(i, 1)

                        var deleteArr = []
                        this.skuPrice.forEach((item, index) => {
                            item.goodsskutext.forEach((e, i) => {
                                if (e == data.name) {
                                    deleteArr.push(index)
                                }
                            })
                        })
                        deleteArr.sort(function (a, b) {
                            return b - a;
                        })
                        // 移除有相关子规格的项
                        deleteArr.forEach((i, e) => {
                            this.skuPrice.splice(i, 1)
                        })

                        // 当前规格项，所有子规格都被删除，清空 skuPrice
                        if (this.skuList[k].children.length <= 0) {
                            this.skuPrice = [] // 规格大变化，清空skuPrice
                            this.isResetSku = 1; // 重置规格
                        }
                        this.buildSkuPriceTable()
                    },
                    editStatus(i) {
                        if (this.skuPrice[i].status == 'up') {
                            this.skuPrice[i].status = 'down'
                        } else {
                            this.skuPrice[i].status = 'up'
                        }

                    },
                    //组合新的规格价格库存重量编码图片
                    buildSkuPriceTable() {
                        var arr = [];
                        this.skuList.forEach((s1, k1) => {
                            var children = s1.children;
                            var childrenIdArray = [];
                            if (children.length > 0) {
                                children.forEach((s2, k2) => {
                                    childrenIdArray.push(s2.temp_id);
                                })
                                arr.push(childrenIdArray);
                            }
                        })

                        this.recursionSku(arr, 0, []);
                        this.setCommissionRule();
                    },
                    //递归找规格集合
                    recursionSku(arr, k, temp) {
                        if (k == arr.length && k != 0) {
                            var tempDetail = []
                            var tempDetailIds = []

                            temp.forEach((item, index) => {
                                for (var sku of this.skuList) {
                                    for (var child of sku.children) {
                                        if (item == child.temp_id) {
                                            tempDetail.push(child.name)
                                            tempDetailIds.push(child.temp_id)
                                        }
                                    }
                                }
                            })

                            var flag = false // 默认添加新的
                            for (var i = 0; i < this.skuPrice.length; i++) {
                                if (this.skuPrice[i].goods_sku_temp_ids.join(',') == tempDetailIds.join(',')) {
                                    flag = i
                                    break;
                                }
                            }

                            if (flag === false) {
                                this.skuPrice.push({
                                    id: 0,
                                    temp_id: this.skuPrice.length + 1,
                                    goods_sku_ids: '',
                                    goods_id: 0,
                                    weigh: 0,
                                    image: '',
                                    stock: 0,
                                    price: 0,
                                    sn: '',
                                    status: 'up',
                                    goodsskutext: tempDetail,
                                    goods_sku_temp_ids: tempDetailIds,
                                });
                            } else {
                                this.skuPrice[flag].goodsskutext = tempDetail
                                this.skuPrice[flag].goods_sku_temp_ids = tempDetailIds
                            }
                            return;
                        }
                        if (arr.length) {
                            for (var i = 0; i < arr[k].length; i++) {
                                temp[k] = arr[k][i]
                                this.recursionSku(arr, k + 1, temp)
                            }
                        }
                    },
                    //是否分销
                    changeDis(value){
                        this.isdis = value;
                    },
                    //切换分销
                    changeRule(value){
                        this.disrule = value;
                    },

                    //选择商品
                    goodsSelect(){
                        var that = this;
                        parent.Fast.api.open("xylease/goods/sku_price/select?multiple=true", __('选择'), {
                            area: ['80%', '80%'],
                            callback: function (data) {

                                console.log('data',data);

                                var goodsList = that.goodsList;
                                data.forEach((item,index)=>{
                                    item['nums'] = 1;
                                    var flag = false;
                                    goodsList.forEach((item1,index1)=>{
                                        if(item1.goods_sku_price_id == item.goods_sku_price_id){
                                            flag = true;
                                        }
                                    })
                                    if(!flag){
                                        goodsList.push({goods_id:item.goods.id,goods_sku_price_id:item.id,nums:1,goodsimage:item.image?item.image:item.goods.image,goodsname:item.goods.name,goodsskutext:item.goodsskutext});
                                    }
                                    
                                });
                                that.goodsList = goodsList
                            }
                        });
                    },

                    //删除商品
                    deleteGoods(index){
                        this.goodsList.splice(index,1);
                    },
                    
                },
                watch: {
                    skuList: {
                        handler(newName, oldName) {
                            if (this.isEditInit) {
                                this.buildSkuPriceTable();
                            }
                        },
                        deep: true
                    }
                },
            })

            Form.api.bindevent($("form[role=form]"), function(data, ret){
                console.log(data,ret);
            }, function(data, ret){
                console.log(data,ret);
            }, function(success, error){

                var skuPrice =  goodsDetail.skuPrice;

                if(goodsDetail.isdis == 1 && goodsDetail.disrule == 1){
                    skuPrice.forEach((item,index)=>{
                        var scr = [];
                        goodsDetail.commissionRule.forEach((item1,index1)=>{
                            if(item.temp_id == item1.id){
                                scr.push(item1);
                            }
                        });
                        skuPrice[index]['commissionrule'] = scr;
                    })
                }

                var sku = JSON.stringify(
                    {
                        listData: JSON.stringify(goodsDetail.skuList),
                        priceData:JSON.stringify(skuPrice)
                    }
                ); 

                $("#skuData").val(sku);

                $("#goodslist").val(JSON.stringify(goodsDetail.goodsList));
            });
        },
        editstock: function () {
            var goodsDetail = new Vue({
                el: "#goodsDetail",
                data() {
                    return {
                        type: Config.type,
                        issku: Config.issku,
                        skuList: [],
                        skuPrice: [],
                    }
                },
                mounted() {
                    this.getInit(Config.skuList, Config.skuPrice);
                },
                methods: {
                    getInit(skuList, skuPrice) {
                        var tempIdArr = {};
                        for (var i in skuList) {
                            skuList[i]['temp_id'] = this.countId++
                            for (var j in skuList[i]['children']) {
                                skuList[i]['children'][j]['temp_id'] = this.countId++
                                tempIdArr[skuList[i]['children'][j]['id']] = skuList[i]['children'][j]['temp_id']
                            }
                        }
                        for (var i = 0; i < skuPrice.length; i++) {
                            var tempSkuPrice = skuPrice[i]
                            tempSkuPrice['nums'] = 0;
                            skuPrice[i] = tempSkuPrice
                        }

                        this.skuList = skuList;
                        this.skuPrice = skuPrice;
                      
                    }
                    
                }
                
            })

            Form.api.bindevent($("form[role=form]"), function(data, ret){
                console.log(data,ret);
            }, function(data, ret){
                console.log(data,ret);
            }, function(success, error){
                var skuPrice =  goodsDetail.skuPrice;
                $("#skuPrice").val(JSON.stringify(skuPrice));
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        },
        
    };
    return Controller;
});
