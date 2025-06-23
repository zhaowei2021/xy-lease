define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/notice/index' + location.search,
                    add_url: 'xylease/notice/add',
                    edit_url: 'xylease/notice/edit',
                    del_url: 'xylease/notice/del',
                    multi_url: 'xylease/notice/multi',
                    import_url: 'xylease/notice/import',
                    table: 'xylease_notice',
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
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'title', title: __('Title'), operate: 'LIKE', table: table},
                        {field: 'views', title: __('Views'), operate: false,},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Status normal'),"hidden":__('Status hidden')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            //修改默认弹窗大小
            Fast.config.openArea = ['70%', '80%'];

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        select: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/notice/select' + location.search,
                }
            });
            var dataArr = [];
            var multiple = Backend.api.query('multiple');
            multiple = multiple == 'true' ? true : false;

            var table = $("#table");

            table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function (e, row) {
                if (e.type == 'check' || e.type == 'uncheck') {
                    row = [row];
                } else {
                    dataArr = [];
                }
                $.each(row, function (i, j) {
                    if (e.type.indexOf("uncheck") > -1) {
                        var index = dataArr.indexOf(j.id);
                        if (index > -1) {
                            dataArr.splice(index, 1);
                        }
                    } else {
                        dataArr.indexOf(j.id) == -1 && dataArr.push(j);
                    }
                });
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'id',
                showToggle: false,
                showExport: false,
                maintainSelected: true,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {field: 'state', checkbox: multiple, visible: multiple, operate: false},
                        {field: 'id', title: __('Id')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'title', title: __('Title'), operate: 'LIKE', table: table},
                        {field: 'views', title: __('Views'), operate: false,},
                        {field: 'status', title: __('Status'), searchList: {"up":__('Status up'),"down":__('Status down')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate', title: __('Operate'), width: 85, events: {
                                'click .btn-chooseone': function (e, value, row, index) {
                                    var multiple = Backend.api.query('multiple');
		                            multiple = multiple == 'true' ? true : false;
                                    var data = multiple ? [row] : row;
                                    Fast.api.close(data);
                                },
                            }, formatter: function () {
                                return '<a href="javascript:;" class="btn btn-danger btn-chooseone btn-xs"><i class="fa fa-check"></i> ' + __('Choose') + '</a>';
                            }
                        }
                    ]
                ]
            });

            // 绑定过滤事件
            $('.filter-type ul li a', table.closest(".panel-intro")).on('click', function (e) {
                $(this).closest("ul").find("li").removeClass("active");
                $(this).closest("li").addClass("active");
                var field = 'mimetype';
                var value = $(this).data("value") || '';
                var object = $("[name='" + field + "']", table.closest(".bootstrap-table").find(".commonsearch-table"));
                if (object.prop('tagName') == "SELECT") {
                    $("option[value='" + value + "']", object).prop("selected", true);
                } else {
                    object.val(value);
                }
                table.trigger("uncheckbox");
                table.bootstrapTable('refresh', {pageNumber: 1});
            });

            // 选中多个
            $(document).on("click", ".btn-choose-multi", function () {
                Fast.api.close(dataArr);
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            require(['upload'], function (Upload) {
                $("#toolbar .faupload").data("category", function (file) {
                    var category = $("ul.nav-tabs[data-field='category'] li.active a").data("value");
                    return category;
                });
                Upload.api.upload($("#toolbar .faupload"), function () {
                    $(".btn-refresh").trigger("click");
                });
            });
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
