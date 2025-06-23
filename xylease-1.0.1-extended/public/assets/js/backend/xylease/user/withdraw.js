define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/user/withdraw/index?type='+Config.type + location.search,
                    multi_url: 'xylease/user/withdraw/multi',
                    import_url: 'xylease/user/withdraw/import',
                    table: 'xylease_user_withdraw',
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
                        {field: 'user.nickname',operate: 'LIKE', title: __('会员信息'),formatter:function(v,row){
                                var html = '<div style="display:flex"><img width="50px" height="50px" src="'+row.user.avatar+'" /><p style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px">'+row.user.nickname+'(ID:'+row.user.id+')<br/>'+row.user.mobile+'</p></div>';
                                return html;
                            }
                        },
                        {field: 'applymoney', title: __('Apply_money'), operate:'BETWEEN'},
                        {field: 'rate', title: __('Rate'), operate:'BETWEEN'},
                        {field: 'servicemoney', title: __('Service_money'), operate:'BETWEEN'},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'realname', title: __('Realname'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'accountname', title: __('Account_name'), operate: 'LIKE'},
                        {field: 'accountno', title: __('account_no'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {'0' : __('待审核'),'1' : __('待转账'),'2' : __('已转账'),'-1' : __('已拒绝')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons :[
                                {
                                    name: 'detail',
                                    text: __('查看'),
                                    title:  __('提现详情'),
                                    classname: 'btn btn-xs btn-success btn-dialog',
                                    icon: 'fa fa-eye',
                                    extend: 'data-area=\'["70%", "80%"]\'',
                                    url: function (row) {
                                        return `xylease/user/withdraw/detail?ids=${row.id}`;
                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click"); //刷新数据
                                    }
                                },
                                {
                                    name: 'agree',
                                    text: __('同意'),
                                    title:  __('同意提现'),
                                    classname: 'btn btn-ajax btn-xs btn-success',
                                    icon: 'fa fa-check-square',
                                    url: 'xylease/user/withdraw/agree/ids/{ids}',
                                    confirm: function (row) {
                                        return '确定要同意提现吗？';
                                    },
                                    visible:function(row){
                                         return row.status == 0;
                                    },
                                    refresh: true,
                                },
                                {
                                    name: 'refuse',
                                    text: __('拒绝'),
                                    title:  __('拒绝提现'),
                                    classname: 'btn btn-xs btn-success btn-dialog',
                                    icon: 'fa fa-times',
                                    extend: 'data-area=\'["50%", "50%"]\'',
                                    url: function (row) {
                                        return `xylease/user/withdraw/refuse?ids=${row.id}`;
                                    },
                                    visible:function(row){
                                        return row.status == 0;
                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click"); //刷新数据
                                    }
                                },
                                {
                                    name: 'payment',
                                    text: __('确认打款'),
                                    title:  __('确认打款'),
                                    classname: 'btn btn-ajax btn-xs btn-success',
                                    icon: 'fa fa-check-square',
                                    url: 'xylease/user/withdraw/payment/ids/{ids}',
                                    confirm: function (row) {
                                        return '确定已经打款了吗？';
                                    },
                                    visible:function(row){
                                         return row.status == 1;
                                    },
                                    refresh: true,
                                }
                            ],
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        refuse: function () {
            Controller.api.bindevent();
        },
        detail: function () {
            Controller.api.bindevent();
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
