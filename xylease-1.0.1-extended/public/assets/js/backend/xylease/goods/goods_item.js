define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/goods/goods_item/index' + location.search,
                    add_url: 'xylease/goods/goods_item/add',
                    edit_url: 'xylease/goods/goods_item/edit',
                    del_url: 'xylease/goods/goods_item/del',
                    multi_url: 'xylease/goods/goods_item/multi',
                    import_url: 'xylease/goods/goods_item/import',
                    table: 'xylease_goods_goods_item',
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
                        {field: 'package_id', title: __('Package_id')},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'deposit', title: __('Deposit'), operate:'BETWEEN'},
                        {field: 'goods_sku_price_id', title: __('Goods_sku_price_id')},
                        {field: 'goodsskutext', title: __('Goodsskutext'), operate: 'LIKE'},
                        {field: 'goodsname', title: __('Goodsname'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'goodsimage', title: __('Goodsimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'nums', title: __('Nums')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
