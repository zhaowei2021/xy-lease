define(['jquery', 'bootstrap', 'backend', 'table', 'form','xylease_vue'], function ($, undefined, Backend, Table, Form, Vue) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xylease/distribution/level/index' + location.search,
                    add_url: 'xylease/distribution/level/add',
                    edit_url: 'xylease/distribution/level/edit',
                    del_url: 'xylease/distribution/level/del',
                    multi_url: 'xylease/distribution/level/multi',
                    import_url: 'xylease/distribution/level/import',
                    table: 'xylease_distribution_level',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'grade',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'grade', title: __('Grade'), searchList: {"1":__('Grade 1'),"2":__('Grade 2'),"3":__('Grade 3'),"4":__('Grade 4'),"5":__('Grade 5'),"6":__('Grade 6'),"7":__('Grade 7'),"8":__('Grade 8'),"9":__('Grade 9'),"10":__('Grade 10')}, formatter: Table.api.formatter.normal},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'commission_one', title: __('Commission_one')},
                        {field: 'commission_two', title: __('Commission_two')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            //修改默认弹窗大小
            Fast.config.openArea = ['70%', '80%'];

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {

            var level = new Vue({
                el: "#app",
                data() {
                    return {
                        conditionList:Config.conditionList,//升级条件
                        upgradeRules:[],//升级规则
                    }
                },
                methods: {

                    //分销条件选择
                    onCondition(current){
                        var upgradeRules = this.upgradeRules;
                        var conditionList = this.conditionList;
                        var rules = {key:current.key,name:current.name,value:''};
                        var flag = true;

                        upgradeRules.forEach((item,index)=>{
                            if(item.key == current.key){
                                upgradeRules.splice(index,1);
                                flag = false;
                            }
                        })
                        if(flag){
                            upgradeRules.push(rules);
                        }

                        conditionList.forEach((item,index)=>{
                            if(item.key == current.key){
                                conditionList[index]['select'] = !current.select;
                            }
                        });

                        this.upgradeRules = upgradeRules;
                        this.conditionList = conditionList;
                    },
                },
                
            })

            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //console.log(data,ret);
            }, function(data, ret){
                //console.log(data,ret);
            }, function(success, error){
                //提交之前设置数据
                $("#upgrade_rules").val(JSON.stringify(level.upgradeRules));
            });
        },
        edit: function () {
            var level = new Vue({
                el: "#app",
                data() {
                    return {
                        conditionList:Config.conditionList,//升级条件
                        upgradeRules:Config.upgradeRules,//升级规则
                    }
                },
                mounted() {
                    this.getInit(Config.conditionList,Config.upgradeRules);
                },
                methods: {
                    getInit(c,u) {
                        c.forEach((item,index) => {
                            u.forEach((item1,index1)=>{
                                if(item.key == item1.key){
                                    c[index]['select'] = true;
                                }
                            })
                        });
                        this.conditionList = c;
                    },

                    //分销条件选择
                    onCondition(current){
                        var upgradeRules = this.upgradeRules;
                        var conditionList = this.conditionList;
                        var rules = {key:current.key,name:current.name,value:''};
                        var flag = true;

                        upgradeRules.forEach((item,index)=>{
                            if(item.key == current.key){
                                upgradeRules.splice(index,1);
                                flag = false;
                            }
                        })
                        if(flag){
                            upgradeRules.push(rules);
                        }

                        conditionList.forEach((item,index)=>{
                            if(item.key == current.key){
                                conditionList[index]['select'] = !current.select;
                            }
                        });

                        this.upgradeRules = upgradeRules;
                        this.conditionList = conditionList;

                        console.log('this.upgradeRules',upgradeRules);
                        console.log('this.conditionList',conditionList);
                    },
                },
                
            })
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //console.log(data,ret);
            }, function(data, ret){
                //console.log(data,ret);
            }, function(success, error){
                //提交之前设置数据
                $("#upgrade_rules").val(JSON.stringify(level.upgradeRules));
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
