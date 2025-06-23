define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/refund/log/index' + location.search,
                    add_url: 'xylease/refund/log/add',
                    edit_url: 'xylease/refund/log/edit',
                    del_url: 'xylease/refund/log/del',
                    multi_url: 'xylease/refund/log/multi',
                    import_url: 'xylease/refund/log/import',
                    table: 'xylease_refund_log',
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
                        {field: 'ordersn', title: __('Ordersn'), operate: 'LIKE'},
                        {field: 'refundsn', title: __('Refundsn'), operate: 'LIKE'},
                        {field: 'order_id', title: __('Order_id'), operate: 'LIKE'},
                        {field: 'ordertype', title: __('Ordertype'), operate: 'LIKE'},
                        {field: 'payfee', title: __('Payfee'), operate:'BETWEEN'},
                        {field: 'refundfee', title: __('Refundfee'), operate:'BETWEEN'},
                        {field: 'paytype', title: __('Paytype'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"-1":__('Status -1')}, formatter: Table.api.formatter.status},
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
