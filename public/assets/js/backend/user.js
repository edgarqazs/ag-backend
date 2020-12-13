define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/index' + location.search,
                    add_url: 'user/add',
                    edit_url: 'user/edit',
                    del_url: 'user/del',
                    multi_url: 'user/multi',
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
                        {field: 'nickname', title: __('Nickname'),operate:'LIKE'},
                        {field: 'mobile', title: __('Mobile'),operate:'LIKE'},
                        // {field: 'pwd', title: __('Pwd')},
                        {field: 'word', title: __('Word'),operate:false},
                      {field: 'remind', title: __('忘记密码提示'),operate:false},
                      {field: 'cny', title: __('Cny'), operate:'BETWEEN'},
                      {field: 'ag', title: __('Ag'), operate:'BETWEEN'},
                      {field: 'usdt', title: 'usdt', operate:'BETWEEN'},
                      {field: 'sc', title: 'sc', operate:'BETWEEN'},
                      {field: 'qc', title: 'qc', operate:'BETWEEN'},

                      {field: 'ag_card', title: __('Ag_card'),operate:false},
                      {field: 'private_key', title: __('Private_key'),operate:'LIKE'},
                      {field: 'invite_code', title: __('Invite_code'),operate:'LIKE'},
                      {field: 'qrcode_image', title: __('Qrcode_image'),operate:false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                      {field: 'head_image', title: __('Head_image'),operate:false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                      {field: 'pintuan_num', title: __('Pintuan_num'), operate:'BETWEEN'},
                      {field: 'zhong_num', title: __('Zhong_num'), operate:'BETWEEN'},
                      {field: 'other_num', title: __('Other_num'), operate:'BETWEEN'},
                      // {field: 'father_id', title: __('Father_id')},
                      // {field: 'grandfather_id', title: __('Grandfather_id')},
                      
                      {field: 'share_num', title: __('分享人数')},
                      {field: 'team_num', title: __('团队人数')},
                      {field: 'father_id', title: __('父级')},

                      {field: 'level', title: __('等级'), searchList: {"-1":__('未激活'),"0":__('普通用户'),"1":__('v1'),"2":__('v2'),"3":__('v3'),"4":__('v4')}, formatter: Table.api.formatter.status},
                      {field: 'status', title: __('Status'), searchList: {"正常":__('正常'),"禁用":__('禁用')}, formatter: Table.api.formatter.status},
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
                        },
                        buttons: [
                          {
                            name: 'addtabs',
                            title: __('团队管理'),
                            classname: 'btn btn-xs btn-info btn-dialog',
                            icon: 'fa fa-users',
                            url: 'team?user_id={id}'
                          },
                          {
                            name: 'addtabs',
                            title: __('地址管理'),
                            classname: 'btn btn-xs btn-warning btn-dialog',
                            icon: 'fa fa-home',
                            url: 'address?user_id={id}'
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
