define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/link/index' + location.search,
                    add_url: 'xylease/link/add',
                    edit_url: 'xylease/link/edit',
                    del_url: 'xylease/link/del',
                    multi_url: 'xylease/link/multi',
                    import_url: 'xylease/link/import',
                    table: 'xylease_link',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'type', title: __('类型'), searchList: {'basic' : __('Basic'), 'user' : __('User'), 'notice' : __('Notice'), 'activity' : __('Activity'), 'goods' : __('商品链接')}, formatter: Table.api.formatter.status},
                        {field: 'name', title: __('名称'), align:'left', operate: 'LIKE'},
                        {field: 'url', title: __('路径'),align:'left', operate: 'LIKE'},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            //生成链接
            $(document).on("click", ".btn-load", function () {
                Fast.api.ajax("xylease/link/load", (data, ret) =>{
                    table.bootstrapTable('refresh', {});
                });
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        
        select: function () {
		    // 初始化表格参数配置
		    Table.api.init({
		        extend: {
		            index_url: 'xylease/link/select',
		        }
		    });
		    var urlArr = [];
		
		    var table = $("#table");
		
		    table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function (e, row) {
		        if (e.type == 'check' || e.type == 'uncheck') {
		            row = [row];
		        } else {
		            urlArr = [];
		        }
		        $.each(row, function (i, j) {
		            if (e.type.indexOf("uncheck") > -1) {
		                var index = urlArr.indexOf(j.route);
		                if (index > -1) {
		                    urlArr.splice(index, 1);
		                }
		            } else {
		                urlArr.indexOf(j.route) == -1 && urlArr.push(j.route);
		            }
		        });
		    });
		
		    // 初始化表格
		    table.bootstrapTable({
		        url: $.fn.bootstrapTable.defaults.extend.index_url,
		        sortName: 'id',
				pagination: false,
				search: false,
		        showToggle: false,
		        showExport: false,
		        columns: [
		            [
                        {field: 'id', title: __('Id')},
                        {field: 'type', title: __('类型'), searchList: {'basic' : __('基础链接'), 'user' : __('会员中心'), 'store' : __('门店链接'), 'course' : __('课程链接'), 'coach' : __('教练链接'), 'goods' : __('商品链接'), 'market' : __('营销链接')}, formatter: Table.api.formatter.status},
                        {field: 'name', title: __('名称'), align:'left', operate: 'LIKE'},
                        {field: 'url', title: __('路径'),align:'left', operate: 'LIKE'},
		                {
		                    field: 'operate', title: __('Operate'), events: {
		                        'click .btn-chooseone': function (e, value, row, index) {
		                            var multiple = Backend.api.query('multiple');
		                            multiple = multiple == 'true' ? true : false;
		                            Fast.api.close({url: row.url,name:row.name, multiple: multiple});
		                        },
		                    }, formatter: function () {
		                        return '<a href="javascript:;" class="btn btn-danger btn-chooseone btn-xs"><i class="fa fa-check"></i> ' + __('Choose') + '</a>';
		                    }
		                }
		            ]
		        ]
		    });
		
		    // 选中多个
		    $(document).on("click", ".btn-choose-multi", function () {
		        var multiple = Backend.api.query('multiple');
		        multiple = multiple == 'true' ? true : false;
		        Fast.api.close({url: urlArr.join(","), multiple: multiple});
		    });

            //生成链接
            $(document).on("click", ".btn-load", function () {
                Fast.api.ajax("xylease/link/load", (data, ret) =>{
                    table.bootstrapTable('refresh', {});
                });
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
