define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'usdtorder/index' + location.search,
                    add_url: 'usdtorder/add',
                    edit_url: 'usdtorder/edit',
                    del_url: 'usdtorder/del',
                    multi_url: 'usdtorder/multi',
                    table: 'usdtorder',
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
                        {field: 'head_image', title: __('Head_image'),operate:false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'nickname', title: __('Nickname'),operate:'LIKE'},
                        {field: 'num', title: __('Num'), operate:'BETWEEN'},
                        {field: 'single_money', title: __('Single_money'), operate:'BETWEEN'},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'sn', title: __('Sn'),operate:'LIKE'},
                        {field: 'buyer_id', title: __('Buyer_id'),operate:false},
                        {field: 'reason', title: __('Reason'),operate:'LIKE'},
                        {field: 'appeal_id', title: __('Appeal_id'),operate:false},
                        {field: 'is_bank', title: __('Is_bank'),searchList: {1:'支持',2:'不支持'}, formatter: Table.api.formatter.normal},
                        {field: 'is_ali', title: __('Is_ali'),searchList: {1:'支持',2:'不支持'}, formatter: Table.api.formatter.normal},
                        {field: 'is_wechat', title: __('Is_wechat'),searchList: {1:'支持',2:'不支持'}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('状态'),searchList: {0:'待交易',1:'待确认',2:'申诉中',3:'已完成',4:'撤单'}, formatter: Table.api.formatter.normal},
                        {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'appeal_time', title: __('Appeal_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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