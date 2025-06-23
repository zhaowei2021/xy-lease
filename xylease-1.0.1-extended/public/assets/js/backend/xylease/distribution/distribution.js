define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'xylease_vue'], function ($, undefined, Backend, Table, Form,Vue) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/distribution/distribution/index' + location.search,
                    add_url: 'xylease/distribution/distribution/add',
                    edit_url: 'xylease/distribution/distribution/edit',
                    del_url: 'xylease/distribution/distribution/del',
                    multi_url: 'xylease/distribution/distribution/multi',
                    import_url: 'xylease/distribution/distribution/import',
                    table: 'xylease_distribution',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'user_id',
                sortName: 'user_id',
                //切换卡片视图和表格视图两种模式
                showToggle:false,
                //显示隐藏列可以快速切换字段列的显示和隐藏
                showColumns:false,
                //导出整个表的所有行导出整个表的所有行
                showExport:true,
                //导出数据格式
                exportTypes:['excel'],
                //搜索
                search: true,
                //表格上方的搜索搜索指表格上方的搜索
                searchFormVisible: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'user.nickname', title: __('会员信息'),operate:'LIKE',formatter:function(value,row){
                                var html = '<div style="display:flex"><img width="50px" height="50px" src="'+row.user.avatar+'" /><p style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px">ID:'+row.user.id+'<br/>'+value+'</p></div>';
                                return html;
                            }
                        },
                        {field: 'realname', title: __('姓名'),operate:'LIKE'},
                        {field: 'mobile', title: __('手机号'),operate:'LIKE'},
                        {field: 'level.name', title: __('Level_id'),operate:false},
                        {field: 'parent.nickname',operate:'LIKE', title: __('Pid'),formatter:function (value, row, index) {
                                if(row.parent.id ==null){
                                    return '无';
                                }
                                return row.parent.nickname + '(ID：'+row.pid+')';
                            }
                        },
                        {field: 'commission', title: __('当前佣金'),operate:false},
                        //{field: 'withdrawn_commission', title: __('已提现佣金'),operate:false},
                        
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Status normal'),"forbidden":__('Status forbidden'),"freeze":__('Status freeze')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate',
                            width: '140px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Detail',
                                    dropdown: '操作',
                                    text: __('查看详情'),
                                    title:'分销商详情',
                                    classname: 'btn-dialog btn',
                                    extend: 'data-area=\'["100%", "100%"]\'',
                                    url: function (row) {
                                        return 'xylease/distribution/distribution/detail?user_id='+row.user_id;
                                    } 
                                },
                                {
                                    name: 'recharge',
                                    dropdown: '操作',
                                    text: __('调整佣金'),
                                    classname: 'btn-dialog btn',
                                    extend: 'data-area=\'["50%", "60%"]\'',
                                    url: function (row) {
                                        return 'xylease/distribution/distribution/recharge?user_id='+row.user_id;
                                    } 
                                },
                                {
                                    name: 'relevel',
                                    dropdown: '操作',
                                    text: __('调整等级'),
                                    classname: 'btn-dialog btn',
                                    extend: 'data-area=\'["50%", "60%"]\'',
                                    url: function (row) {
                                        return 'xylease/distribution/distribution/relevel?user_id='+row.user_id;
                                    } 
                                },
                                {
                                    name: 'repid',
                                    dropdown: '操作',
                                    text: __('调整上级'),
                                    classname: 'btn-dialog btn',
                                    extend: 'data-area=\'["50%", "60%"]\'',
                                    url: function (row) {
                                        return 'xylease/distribution/distribution/repid?user_id='+row.user_id;
                                    } 
                                },
                               
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
       
        recharge: function () {
            Controller.api.bindevent();
        },
        relevel: function () {
            Controller.api.bindevent();
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
                                console.log(data);
                                that.userInfo = data
                            }
                        });
                    },
                },
            })
            Controller.api.bindevent();
        },
        detail: function () {
            // 初始化表格参数配置
            Table.api.init();
            
            //绑定事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var panel = $($(this).attr("href"));
                if (panel.length > 0) {
                    Controller.table[panel.attr("id")].call(this);
                    $(this).on('click', function (e) {
                        $($(this).attr("href")).find(".btn-refresh").trigger("click");
                    });
                }
                //移除绑定的事件
                $(this).unbind('shown.bs.tab');
            });
            
            //必须默认触发shown.bs.tab事件
            $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");
        },
        table: {
            first: function () {
                // 表格1
                var table1 = $("#table1"),distribution_id = $("#distribution_id").text();
                table1.bootstrapTable({
                    url: 'xylease/user/user/team?user_id='+distribution_id,
                    toolbar: '#toolbar1',
                    sortName: 'createtime',
                    search: false,
                    columns: [
                        [
                            {field: 'avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                            {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                            {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                            {field: 'xylease_consume', title: __('Consume'), operate: 'between',sortable: true},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                            {field: 'status', title: __('Status'), searchList: {"normal":__('Status normal'),"forbidden":__('Status forbidden'),"freeze":__('Status freeze')}, formatter: Table.api.formatter.status},
                            
                        ]
                    ]
                });

                // 为表格1绑定事件
                Table.api.bindevent(table1);
            },
            second: function () {
                // 表格2
                var table2 = $("#table2"),distribution_id = $("#distribution_id").text();
                table2.bootstrapTable({
                    url: 'xylease/distribution/commission/index?distribution_id='+distribution_id,
                    extend: {
                        index_url: '',
                        add_url: '',
                        edit_url: '',
                        del_url: '',
                        multi_url: '',
                        table: '',
                    },
                    toolbar: '#toolbar2',
                    sortName: 'id',
                    search: false,
                    columns: [
                        [
                            {field: 'id', title: __('ID')},
                            {field: 'type', title: __('Type'), searchList: 
                            {
                                'apply_withdraw' : __('申请提现'), 
                                'refuse_withdraw' : __('拒绝提现'), 
                                'order' : __('订单结算'), 
                                'sys' : __('后台操作'),
                                'bonus_team': __('团队分红'),
                                'bonus_team_proffer': __('贡献奖励'),
                                'bonus': __('每日分红'),
                                
                            }, 
                            formatter: Table.api.formatter.normal},
                            {field: 'money', title: __('变更余额'), operate:false},
                            {field: 'before', title: __('变更前'), operate:false},
                            {field: 'after', title: __('变更后'), operate:false},
                            {field: 'remark', title: __('Remark'), operate:false},
                            {field: 'createtime', title: __('变更时间'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        ]
                    ]
                });

                // 为表格2绑定事件
                Table.api.bindevent(table2);
            },
            three: function () {
                // 表格2
                var table3 = $("#table3"),distribution_id = $("#distribution_id").text();;
                table3.bootstrapTable({
                    url: 'xylease/distribution/order/index?distribution_id='+distribution_id,
                    extend: {
                        index_url: '',
                        add_url: '',
                        edit_url: '',
                        del_url: '',
                        multi_url: '',
                        table: '',
                    },
                    sortName: 'id',
                    search: false,
                    toolbar: '#toolbar3',
                    templateView: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate: false},
                            {field: 'ordersn', title: __('订单号'), operate: 'LIKE'},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                            {field: 'status', title: __('Status'), searchList: {"1":__('已结算'),"0":__('未结算'),"-2":__('已取消'),"-1":__('已退回')}, formatter: Table.api.formatter.status},
                        ]
                    ]
                });

                Template.helper("cdnurl", function(image) {
                    return Fast.api.cdnurl(image); 
                }); 
                
                Template.helper("Moment", Moment);

                // 为表格3绑定事件
                Table.api.bindevent(table3);
            }
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
