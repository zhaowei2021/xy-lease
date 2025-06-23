define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/order/item/index' + location.search,
                    add_url: 'xylease/order/item/add',
                    edit_url: 'xylease/order/item/edit',
                    del_url: 'xylease/order/item/del',
                    multi_url: 'xylease/order/item/multi',
                    import_url: 'xylease/order/item/import',
                    table: 'xylease_order_item',
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
                        {field: 'order_id', title: __('Goods_order_id')},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'goods_sku_price_id', title: __('Goods_sku_price_id')},
                        {field: 'goodsskutext', title: __('Goods_sku_text'), operate: 'LIKE'},
                        {field: 'goodsname', title: __('Goods_title'), operate: 'LIKE'},
                        {field: 'goodsimage', title: __('Goods_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'goodsprice', title: __('Goods_price'), operate:'BETWEEN'},
                        {field: 'buynum', title: __('Buy_num')},
                        {field: 'goodsweight', title: __('Goods_weight')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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
