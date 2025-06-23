define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/recharge/order/index' + location.search,
                    del_url: 'xylease/recharge/order/del',
                    multi_url: 'xylease/recharge/order/multi',
                    import_url: 'xylease/recharge/order/import',
                    table: 'xylease_recharge_order',
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
                        {field: 'user.nickname',operate: 'LIKE', title: __('充值会员'),formatter:function(v,row){
                                var html = '<div style="display:flex"><img width="50px" height="50px" src="'+row.user.avatar+'" /><p style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px">ID:'+row.user.id+'<br/>'+v+'</p></div>';
                                return html;
                            }
                        },
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'payfee', title: __('Pay_fee'), operate:'BETWEEN'},
                        //{field: 'platform', title: __('Platform'), searchList: {"wxMiniProgram":__('Platform wxminiprogram'),"wxOfficialAccount":__('Platform wxofficialaccount')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"-2":__('Status -2'),"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
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
