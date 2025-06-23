define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/user/coupon/index' + location.search,
                    add_url: 'xylease/user/coupon/add',
                    del_url: 'xylease/user/coupon/del',
                    multi_url: 'xylease/user/coupon/multi',
                    import_url: 'xylease/user/coupon/import',
                    table: 'xylease_user_coupon',
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
                        {field: 'user.nickname',operate: 'LIKE', title: __('User_id'),formatter:function(v,row){
                                var html = '<div style="display:flex"><img width="50px" height="50px" src="'+row.user.avatar+'" /><p style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px">'+row.user.nickname+'(ID:'+row.user.id+')<br/>'+row.user.mobile+'</p></div>';
                                return html;
                            }
                        },
                        {field: 'type', title: __('Type'), searchList: {"reward":__('Type reward'),"discount":__('Type discount')}, formatter: Table.api.formatter.normal},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'endtime', title: __('Endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"3":__('Status 3')}, formatter: Table.api.formatter.status},
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
