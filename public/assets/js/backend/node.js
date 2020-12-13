define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'node/index' + location.search,
                    add_url: 'node/add',
                    edit_url: 'node/edit',
                    del_url: 'node/del',
                    multi_url: 'node/multi',
                    table: 'node',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                showExport: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'user_id', title: __('User_id'),operate:false},
                        {field: 'all_ag', title: __('All_ag'), operate:'BETWEEN'},
                        {field: 'lock_ag', title: __('Lock_ag'), operate:'BETWEEN'},
                        {field: 'status', title: '状态',searchList: {1:'正常',2:'暂停'}, formatter: Table.api.formatter.normal},
                        {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate', 
                            title: __('Operate'), 
                            table: table, 
                            events: Table.api.events.operate, 
                            formatter: function (value, row, index) {
                                var that = $.extend({}, this);
                                var table = $(that.table).clone(true);

                                // $(table).data("operate-edit", null);
                                $(table).data("operate-del", null);

                                that.table = table;
                                return Table.api.formatter.operate.call(that, value, row, index);
                            },
                            buttons: [
                                {
                                    name: 'addtabs',
                                    title: __('分红记录'),
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'noderecord?node_id={id}'
                                }
                            ],
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