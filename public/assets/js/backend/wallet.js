define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wallet/index' + location.search,
                    add_url: 'wallet/add',
                    edit_url: 'wallet/edit',
                    del_url: 'wallet/del',
                    multi_url: 'wallet/multi',
                    table: 'wallet',
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
                        {field: 'type', title: __('Type'), searchList: {"银行卡":__('银行卡'),"支付宝":__('支付宝'),"微信":__('微信')}, formatter: Table.api.formatter.normal},
                        {field: 'user_id', title: __('User_id'),operate:false},
                        {field: 'account', title: __('Account'),operate:'LIKE'},
                        {field: 'name', title: __('Name'),operate:'LIKE'},
                        {field: 'tel', title: __('Tel'),operate:'LIKE'},
                        {field: 'money_image', title: __('Money_image'),operate:false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'bank_name', title: __('Bank_name'),operate:'LIKE'},
                        {field: 'sub_bank_name', title: __('Sub_bank_name'),operate:'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"未审核":__('未审核'),"审核通过":__('审核通过'),"审核失败":__('审核失败')}, formatter: Table.api.formatter.status},
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