define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/goods/sku_price/index' + location.search,
                    add_url: 'xylease/goods/sku_price/add',
                    edit_url: 'xylease/goods/sku_price/edit',
                    del_url: 'xylease/goods/sku_price/del',
                    multi_url: 'xylease/goods/sku_price/multi',
                    import_url: 'xylease/goods/sku_price/import',
                    table: 'xylease_goods_sku_price',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'goods_sku_ids', title: __('Goods_sku_ids'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'stock', title: __('Stock')},
                        {field: 'sales', title: __('Sales')},
                        {field: 'sn', title: __('Sn'), operate: 'LIKE'},
                        {field: 'hourprice', title: __('Hourprice'), operate:'BETWEEN'},
                        {field: 'daysprice', title: __('Daysprice'), operate:'BETWEEN'},
                        {field: 'nightprice', title: __('Nightprice'), operate:'BETWEEN'},
                        {field: 'deposit', title: __('Deposit'), operate:'BETWEEN'},
                        {field: 'showprice', title: __('Showprice'), operate:'BETWEEN'},
                        {field: 'goodsskutext', title: __('Goodsskutext'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'status', title: __('Status'), searchList: {"up":__('Status up'),"down":__('Status down')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        select: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/goods/sku_price/select' + location.search,
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
                        {field: 'goods.name',operate: 'LIKE', title: __('商品'),formatter:function(v,row){
                            console.log(v,row);
                                if(row.goods == null){
                                    return '';
                                }else{
                                    var image = row.goods.image;
                                    if(row.image){
                                        image = row.image;
                                    }
                                    var html = '<div style="display:flex"><img width="80px" height="80px" src="'+image+'" /><p style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px;font-weight:bold;margin-top:10px">'+row.goods.name+'<br/><br/><span style="font-weight:normal">'+(row.goodsskutext?row.goodsskutext:'')+'</span></p></div>';
                                    return html;
                                }
                                
                            }
                        },
                        {field: 'stock', title: __('Stock')},
                        //{field: 'sales', title: __('Sales')},
                        //{field: 'sn', title: __('Sn'), operate: 'LIKE'},
                        {field: 'hourprice', title: __('Hourprice'), operate:'BETWEEN'},
                        {field: 'daysprice', title: __('Daysprice'), operate:'BETWEEN'},
                        {field: 'nightprice', title: __('Nightprice'), operate:'BETWEEN'},
                        {field: 'deposit', title: __('Deposit'), operate:'BETWEEN'},
                        //{field: 'goodsskutext', title: __('Goodsskutext'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
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
