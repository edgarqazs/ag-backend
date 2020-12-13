define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'scoreorder/index' + location.search,
                    add_url: 'scoreorder/add',
                    edit_url: 'scoreorder/edit',
                    del_url: 'scoreorder/del',
                    multi_url: 'scoreorder/multi',
                    table: 'scoreorder',
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
                        // {field: 'user_id', title: __('User_id')},
                        {field: 'username', title: __('Username'),operate:'LIKE'},
                        {field: 'product_name', title: __('Product_name'),operate:'LIKE'},
                        {field: 'product_image', title: __('Product_image'),operate:false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'address_name', title: __('Address_name'),operate:'LIKE'},
                        {field: 'address_tel', title: __('Address_tel'),operate:'LIKE'},
                        {field: 'address', title: __('Address'),operate:'LIKE'},
                        {field: 'company', title: __('Company'),operate:'LIKE'},
                        {field: 'sn', title: __('Sn'),operate:'LIKE'},
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