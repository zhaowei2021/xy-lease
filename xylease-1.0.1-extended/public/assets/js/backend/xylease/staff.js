define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/staff/index' + location.search,
                    add_url: 'xylease/staff/add',
                    edit_url: 'xylease/staff/edit',
                    del_url: 'xylease/staff/del',
                    multi_url: 'xylease/staff/multi',
                    import_url: 'xylease/staff/import',
                    table: 'xylease_staff',
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
                        {field: 'user.nickname',operate: 'LIKE', title: __('User_id'),formatter:function(v,row){
                                if(v == null){
                                    return '<div style="display:flex;justify-content: center;">暂未绑定</div>';
                                }else{
                                    var html = '<div style="display:flex;justify-content: center;"><img width="50px" height="50px" src="'+row.user.avatar+'" /><p style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px;width:150px">'+row.user.nickname+'(ID:'+row.user.id+')<br/>'+row.user.mobile+'</p></div>';
                                    return html;
                                }
                            }
                        },
                        {field: 'role', title: __('Role'), searchList: {"boss":__('Role boss'),"clerk":__('Role clerk')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'headimage', title: __('Headimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'realname', title: __('Realname'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Status normal'),"hidden":__('Status hidden')}, formatter: Table.api.formatter.status},
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
