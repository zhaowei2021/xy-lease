define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/distribution/order/index' + location.search,
                    add_url: 'xylease/distribution/order/add',
                    edit_url: 'xylease/distribution/order/edit',
                    del_url: 'xylease/distribution/order/del',
                    multi_url: 'xylease/distribution/order/multi',
                    import_url: 'xylease/distribution/order/import',
                    table: 'xylease_distribution_order',
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
                //切换卡片视图和表格视图两种模式
                showToggle:false,
                //显示隐藏列可以快速切换字段列的显示和隐藏
                showColumns:false,
                //导出整个表的所有行导出整个表的所有行
                showExport:false,
                //导出数据格式
                exportTypes:['excel'],
                //搜索
                search: true,
                //表格上方的搜索搜索指表格上方的搜索
                searchFormVisible: false,
                //searchFormTemplate: 'customformtpl',
                //commonSearch:false,
                templateView: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'ordersn', title: __('Ordersn'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"1":__('已结算'),"0":__('未结算'),"-2":__('已取消'),"-1":__('已退回')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            Template.helper("cdnurl", function(image) {
				return Fast.api.cdnurl(image); 
			}); 
            
			Template.helper("Moment", Moment);

            //点击详情
			$(document).on("click", ".detail[data-id]", function () {
			    Backend.api.open('xylease/vip.order/detail/id/' + $(this).data('id'), __('订单详情'),{area:['80%', '80%']});
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
