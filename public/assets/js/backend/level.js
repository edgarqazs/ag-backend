define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'level/index' + location.search,
                    add_url: 'level/add',
                    edit_url: 'level/edit',
                    del_url: 'level/del',
                    multi_url: 'level/multi',
                    table: 'level',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                sortOrder: 'asc',
                search:false,
                commonSearch:false,
                showExport: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name')},
                        {field: 'share_num', title: __('Share_num')},
                        {field: 'v1_num', title: __('V1_num')},
                        {field: 'v2_num', title: __('V2_num')},
                        {field: 'v3_num', title: __('V3_num')},
                        {field: 'team_num', title: __('Team_num')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {
                            field: 'operate', 
                            title: __('Operate'), 
                            table: table, 
                            events: Table.api.events.operate,
                            
                            formatter: function (value, row, index) {
                                var that = $.extend({}, this);
                                var table = $(that.table).clone(true);

                                $(table).data("operate-del", null);
                                that.table = table;
                                return Table.api.formatter.operate.call(that, value, row, index);
                            },
                        }
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