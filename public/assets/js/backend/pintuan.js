define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pintuan/index' + location.search,
                    add_url: 'pintuan/add',
                    edit_url: 'pintuan/edit',
                    del_url: 'pintuan/del',
                    multi_url: 'pintuan/multi',
                    table: 'pintuan',
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
                      {field: 'user_id', title: __('用户ID'),operate:'='},
                      {field: 'nickname', title: __('Nickname'),operate:'LIKE'},
                      {field: 'room_num', title: __('Room_num')},
                        {field: 'status', title: __('Status'), searchList: {"拼中":__('拼中'),"未拼中":__('未拼中'),"未开奖":__('未开奖')}, formatter: Table.api.formatter.status},
                        {field: 'product_status', title: __('商品选择状态'),operate:false},
                        {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
