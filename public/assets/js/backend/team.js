define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'team/index' + location.search,
                    add_url: 'team/add',
                    edit_url: 'team/edit',
                    del_url: 'team/del',
                    multi_url: 'team/multi',
                    table: 'user',
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
                        {field: 'username', title: __('Username'),operate:'LIKE'},
                        {field: 'cny', title: __('Cny'), operate:'BETWEEN'},
                        {field: 'ag', title: __('Ag'), operate:'BETWEEN'},
                        {field: 'sc', title: __('Sc'), operate:'BETWEEN'},
                        {field: 'qc', title: __('Qc'), operate:'BETWEEN'},
                        {field: 'usdt', title: __('Usdt'), operate:'BETWEEN'},
                        {field: 'level', title: __('等级'), searchList: {"-1":__('未激活'),"0":__('普通用户'),"1":__('v1'),"2":__('v2'),"3":__('v3'),"4":__('v4')}, formatter: Table.api.formatter.status},
                        {field: 'team_level', title: __('级别'), operate:false},
                    ]
                ]
            });

            table.on('load-success.bs.table', function (e, data) {
                //这里我们手动设置底部的值
                $("#team_num").text(data.team_num);
            });

            table.on('load-success.bs.table', function (e, data) {
                //这里我们手动设置底部的值
                $("#real_num").text(data.real_num);
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