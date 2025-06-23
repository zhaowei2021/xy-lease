define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/cart/index' + location.search,
                    add_url: 'xylease/cart/add',
                    edit_url: 'xylease/cart/edit',
                    del_url: 'xylease/cart/del',
                    multi_url: 'xylease/cart/multi',
                    import_url: 'xylease/cart/import',
                    table: 'xylease_cart',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'goods_num', title: __('Goods_num')},
                        {field: 'sku_price_id', title: __('Sku_price_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'goods.id', title: __('Goods.id')},
                        {field: 'goods.title', title: __('Goods.title'), operate: 'LIKE'},
                        {field: 'goods.tags', title: __('Goods.tags'), operate: 'LIKE', formatter: Table.api.formatter.flag},
                        {field: 'goods.category_ids', title: __('Goods.category_ids'), operate: 'LIKE'},
                        {field: 'goods.image', title: __('Goods.image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'goods.images', title: __('Goods.images'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'goods.price', title: __('Goods.price'), operate: 'LIKE'},
                        {field: 'goods.lineprice', title: __('Goods.lineprice'), operate:'BETWEEN'},
                        {field: 'goods.issku', title: __('Goods.issku')},
                        {field: 'goods.likes', title: __('Goods.likes')},
                        {field: 'goods.views', title: __('Goods.views')},
                        {field: 'goods.sales', title: __('Goods.sales')},
                        {field: 'goods.virtualsales', title: __('Goods.virtualsales')},
                        {field: 'goods.weigh', title: __('Goods.weigh')},
                        {field: 'goods.status', title: __('Goods.status'), formatter: Table.api.formatter.status},
                        {field: 'goods.createtime', title: __('Goods.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'goods.updatetime', title: __('Goods.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'goods.deletetime', title: __('Goods.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'price.id', title: __('Price.id')},
                        {field: 'price.goods_sku_ids', title: __('Price.goods_sku_ids'), operate: 'LIKE'},
                        {field: 'price.goods_id', title: __('Price.goods_id')},
                        {field: 'price.image', title: __('Price.image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'price.stock', title: __('Price.stock')},
                        {field: 'price.sales', title: __('Price.sales')},
                        {field: 'price.sn', title: __('Price.sn'), operate: 'LIKE'},
                        {field: 'price.price', title: __('Price.price'), operate:'BETWEEN'},
                        {field: 'price.goodsskutext', title: __('Price.goodsskutext'), operate: 'LIKE'},
                        {field: 'price.weigh', title: __('Price.weigh')},
                        {field: 'price.status', title: __('Price.status'), operate: 'LIKE', formatter: Table.api.formatter.status},
                        {field: 'price.createtime', title: __('Price.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'price.updatetime', title: __('Price.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'price.deletetime', title: __('Price.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
