define(['jquery', 'bootstrap', 'backend', 'table', 'form','xylease_vue'], function ($, undefined, Backend, Table, Form,Vue) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/coupon/index' + location.search,
                    add_url: 'xylease/coupon/add',
                    edit_url: 'xylease/coupon/edit',
                    del_url: 'xylease/coupon/del',
                    multi_url: 'xylease/coupon/multi',
                    import_url: 'xylease/coupon/import',
                    table: 'xylease_coupon',
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
                        {field: 'type', title: __('Type'), searchList: {"reward":__('Type reward'),"discount":__('Type discount')}, formatter: Table.api.formatter.normal},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'isshow', title: __('Isshow'), searchList: {"0":__('Isshow 0'),"1":__('Isshow 1')}, formatter: Table.api.formatter.normal},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            //修改默认弹窗大小
            Fast.config.openArea = ['80%', '90%'];

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            new Vue({
                el: "#app",
                data() {
                    return {
                        type:'reward',// 优惠券类型
                        isshow:0,
                        validitytype:0,
                    }
                },
                methods: {

                    //更改优惠券类型
                    changeType(){
                        this.type = $("#c-type").val();
                    },

                    //是否可领取
                    changeIsshow(){
                        this.isshow = parseInt($("#c-isshow").val());
                    },

                    changeValiditytype(){
                        this.validitytype = parseInt($("#c-validitytype").val());
                    }

                },
            })
            Controller.api.bindevent();
        },
        edit: function () {

            new Vue({
                el: "#app",
                data() {
                    return {
                        type:Config.row.type,// 优惠券类型
                        isshow:Config.row.isshow,
                        validitytype:Config.row.validitytype,
                    }
                },
                methods: {

                    //更改优惠券类型
                    changeType(){
                        this.type = $("#c-type").val();
                    },

                    //是否可领取
                    changeIsshow(){
                        this.isshow = parseInt($("#c-isshow").val());
                    },

                    changeValiditytype(){
                        this.validitytype = parseInt($("#c-validitytype").val());
                    }

                },
            })
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
