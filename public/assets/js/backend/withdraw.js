define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'withdraw/index' + location.search,
                    add_url: 'withdraw/add',
                    edit_url: 'withdraw/edit',
                    del_url: 'withdraw/del',
                    multi_url: 'withdraw/multi',
                    table: 'withdraw',
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
                      {field: 'mobile', title: __('绑定手机'),operate:false},

                      {field: 'num', title: __('Num'), operate:'BETWEEN'},
                      {field: 'address', title: __('Address'),operate:'LIKE'},
                      {field: 'withdraw_rate', title: __('Withdraw_rate'), operate:'BETWEEN'},
                      {field: 'arrive_num', title: __('Arrive_num'), operate:'BETWEEN'},
                      {field: 'status', title: __('Status'), searchList: {"未处理":__('未处理'),"已到账":__('已到账'),"到账失败":__('到账失败')}, formatter: Table.api.formatter.status},
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
