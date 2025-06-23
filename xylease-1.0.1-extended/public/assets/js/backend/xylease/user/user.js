define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'xylease_vue'], function ($, undefined, Backend, Table, Form, Vue) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/user/user/index' + location.search,
                    add_url: 'xylease/user/user/add',
                    edit_url: 'xylease/user/user/edit',
                    multi_url: 'xylease/user/user/multi',
                    import_url: 'xylease/user/user/import',
                    table: 'user',
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
                        {field: 'id', title: __('ID'), sortable: true},
                        {field: 'avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'money', title: __('Money'), operate: false,sortable: true},
                        {field: 'xylease_consume', title: __('Consume'), operate: false,sortable: true},
                        {field: 'xylease_recharge', title: __('Recharge'), operate: false,sortable: true},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('禁用')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons :[
                                {
                                    name: 'balance',
                                    dropdown: '操作',
                                    text: __('余额明细'),
                                    title: function (row) {
                                        return row.nickname + ' 的余额明细';
                                    },
                                    classname: 'btn btn-dialog',
                                    extend: 'data-area=\'["80%", "80%"]\'',
                                    url: function (row) {
                                        return `xylease/user/money/index?user_id=${row.id}`;
                                    }
                                },
                                {
                                    name: 'recharge',
                                    dropdown: '操作',
                                    text: __('调整余额'),
                                    title: function (row) {
                                        return '调整 ' + row.nickname + ' 的余额';
                                    },
                                    classname: 'btn btn-dialog',
                                    extend: 'data-area=\'["50%", "60%"]\'',
                                    url: function (row) {
                                        return `xylease/user/user/recharge?id=${row.id}`;
                                    }
                                }
                                
                            ],
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        repid: function () {
            new Vue({
                el: "#app",
                data() {
                    return {
                        userInfo:{},//会员
                    }
                },
                methods: {
                    selectUser(){
                        var that = this;
                        parent.Fast.api.open("xylease/user/user/select?multiple=false", __('选择'), {
                            area: ['80%', '80%'],
                            callback: function (data) {
                                that.userInfo = data
                            }
                        });
                    },
                },
            })
            Controller.api.bindevent();
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        recharge: function () {
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
