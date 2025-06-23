define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/goods/stock_log/index' + location.search,
                    add_url: 'xylease/goods/stock_log/add',
                    edit_url: 'xylease/goods/stock_log/edit',
                    del_url: 'xylease/goods/stock_log/del',
                    multi_url: 'xylease/goods/stock_log/multi',
                    import_url: 'xylease/goods/stock_log/import',
                    table: 'xylease_goods_stock_log',
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
                        {field: 'goodsname',operate: 'LIKE', title: __('商品'),formatter:function(v,row){
                            var html = '<div style="display:flex;">'+
                                            '<img width="60px" height="60px" src="'+row.goodsimage+'" />'+
                                            '<div style="text-align:left;line-height:20px;margin-top:5px;margin-left:10px;width:150px">'+
                                                '<p>'+row.goodsname+'</p>'+
                                                '<p>'+row.goodsskutext+'</p>'+
                                            '</div>'+
                                        '</div>';
                                return html;
                            }
                        },
                        {field: 'type', title: __('Type'), searchList: {"sysadd":__('Type sysadd'),"sysedit":__('Type sysedit'),"ordercreate":__('Type ordercreate'),"ordercancel":__('Type ordercancel'),"orderclose":__('Type orderclose'),"orderback":__('Type orderback')}, formatter: Table.api.formatter.normal},
                        {field: 'before', title: __('Before'),operate:false},
                        {field: 'nums', title: __('Nums'),operate:false},
                        {field: 'after', title: __('After'),operate:false},
                        //{field: 'operaterole', title: __('Operaterole'), searchList: {"admin":__('Operaterole admin'),"staff":__('Operaterole staff'),"user":__('Operaterole user')}, formatter: Table.api.formatter.normal},
                        //{field: 'operate_id', title: __('Operate_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
