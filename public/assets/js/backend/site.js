define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'site/index' + location.search,
                    add_url: 'site/add',
                    edit_url: 'site/edit',
                    del_url: 'site/del',
                    multi_url: 'site/multi',
                    table: 'site',
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
                        {field: 'room_num', title: __('Room_num')},
                        {field: 'people_num', title: __('People_num')},
                        {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'zhong_rate', title: __('Zhong_rate'), operate:'BETWEEN'},
                        {field: 'wei_rate', title: __('Wei_rate'), operate:'BETWEEN'},
                        {field: 'cny_ag_rate', title: __('Cny_ag_rate'), operate:'BETWEEN'},
                        {field: 'usdt_cny_rate', title: __('Usdt_cny_rate'), operate:'BETWEEN'},
                        {field: 'withdraw_rate', title: __('Withdraw_rate'), operate:'BETWEEN'},
                        {field: 'fail_cny', title: __('Fail_cny'), operate:'BETWEEN'},
                        {field: 'v1_temp_cny', title: __('V1_temp_cny'), operate:'BETWEEN'},
                        {field: 'v2_temp_cny', title: __('V2_temp_cny'), operate:'BETWEEN'},
                        {field: 'v3_temp_cny', title: __('V3_temp_cny'), operate:'BETWEEN'},
                        {field: 'v4_temp_cny', title: __('V4_temp_cny'), operate:'BETWEEN'},
                        {field: 'ag_card_rate', title: __('Ag_card_rate'), operate:'BETWEEN'},
                        {field: 'node_cny', title: __('Node_cny'), operate:'BETWEEN'},
                        {field: 'node_num', title: __('Node_num')},
                        {field: 'node_every_get', title: __('Node_every_get'), operate:'BETWEEN'},
                        {field: 'node_company', title: __('Node_company'), operate:'BETWEEN'},
                        {field: 'node_every_pay', title: __('Node_every_pay'), operate:'BETWEEN'},
                        {field: 'bag_url', title: __('Bag_url'), formatter: Table.api.formatter.url},
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