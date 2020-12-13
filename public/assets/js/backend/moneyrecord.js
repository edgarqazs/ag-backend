define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'moneyrecord/index' + location.search,
                    add_url: 'moneyrecord/add',
                    edit_url: 'moneyrecord/edit',
                    del_url: 'moneyrecord/del',
                    multi_url: 'moneyrecord/multi',
                    table: 'money_record',
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
                        {field: 'user_id', title: __('用户ID'),operate:'='},
                        {field: 'username', title: __('Username'),operate:'LIKE'},
                        {field: 'pre_money', title: __('余额'), operate:'BETWEEN'},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'operate', title: __('Operate'), searchList: {"+":__('+'),"-":__('-')}, formatter: Table.api.formatter.normal},
                        {field: 'type', title: __('Type'), searchList: {"usdt":__('Usdt'),"cny":__('Cny'),"ag":__('Ag'),"sc":__('Sc'),"qc":__('Qc'),'freeze_usdt':__('Freeze_usdt'),'lock_ag':__('Lock_ag'),'freeze_ag':__('Freeze_ag'),'flow_ag':__('Flow_ag'),'ag_card':__('Ag_card')}, formatter: Table.api.formatter.normal},
                        //{field: 'type', title: __('Type'), searchList: {"usdt":__('Usdt'),"cny":__('Cny'),"ag":__('Ag'),"sc":__('Sc'),"qc":__('Qc')}, formatter: Table.api.formatter.normal},
                      {field: 'content', title: __('描述'),operate:'LIKE'},
                      {field: 'remark', title: __('备注'),operate:'LIKE'},
                      {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
                            }
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
