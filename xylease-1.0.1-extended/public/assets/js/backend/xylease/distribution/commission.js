define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/distribution/commission/index' + location.search,
                    multi_url: 'xylease/distribution/commission/multi',
                    import_url: 'xylease/distribution/commission/import',
                    table: 'xylease_distribution_commission',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {field: 'id', title: __('Id')},
                        {field: 'user.nickname',operate:'LIKE', title: __('Distribution_id'), formatter:function(value,row){
                                var html = '<div style="display:flex"><img width="50px" height="50px" src="'+row.user.avatar+'" /><p style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px">'+value+'<br/>'+row.user.mobile+'</p></div>';
                                return html;
                            }
                        },
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'type', title: __('Type'), searchList: {"apply_withdraw":__('Type  apply_withdraw'),"refuse_withdraw":__('Type refuse_withdraw'),"pay_withdraw":__('Type pay_withdraw'),"order":__('Type order'),"sys":__('Type sys')}, formatter: Table.api.formatter.normal},
                        {field: 'before', title: __('Before'), operate:'BETWEEN'},
                        {field: 'after', title: __('After'), operate:'BETWEEN'},
                        {field: 'remark', title: __('Remark'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
