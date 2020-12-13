define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pintuanorder/index' + location.search,
                    add_url: 'pintuanorder/add',
                    edit_url: 'pintuanorder/edit',
                    del_url: 'pintuanorder/del',
                    multi_url: 'pintuanorder/multi',
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
                        {field: 'nickname', title: __('Nickname'),operate:'LIKE'},
                        {field: 'product_name', title: __('Product_name'),operate:'LIKE'},
                        {field: 'address_name', title: __('Address_name'),operate:'LIKE'},
                        {field: 'address_detail', title: __('Address_detail'),operate:'LIKE'},
                        {field: 'address_tel', title: __('Address_tel'),operate:'LIKE'},
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

          //*************************** 自定义export开始
          var submitForm = function (ids, layero) {
            var options = table.bootstrapTable('getOptions');
            //console.log(options);
            console.log(options.searchText);
            var columns = [];
            $.each(options.columns[0], function (i, j) {
              if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                columns.push(j.field);
              }
            });
            var search = options.queryParams({});
            // console.log(search.filter);
            // console.log(search.filter);
            $("input[name=search]", layero).val(options.searchText);
            $("input[name=ids]", layero).val(ids);
            $("input[name=filter]", layero).val(search.filter);
            $("input[name=op]", layero).val(search.op);
            $("input[name=columns]", layero).val(columns.join(','));
            $("form", layero).submit();
          };
          
          $(document).on("click", ".btn-export", function () {
            var ids = Table.api.selectedids(table);
            var page = table.bootstrapTable('getData');
            var all = table.bootstrapTable('getOptions').totalRows;
            // console.log(ids, page, all);
            // console.log($("input[name=filter]"));

            var options = table.bootstrapTable('getOptions');
            // console.log(options);
            var search = options.queryParams({});
            // console.log(search.filter);
            // console.log(JSON.stringify(search.filter));
            // console.log(Object.keys(search.filter).length);

            // if(JSON.stringify(search.filter) === "{}"){
            if(Object.keys(search.filter).length === 2){
              // console.log('yes');
              Layer.alert('请先使用筛选功能');exit
            }
            // if(ids.length == 0){
            //   Layer.alert('请先点选需要导出的数据');exit
            // }
            // if(ids.length > 1000){
            //   Layer.alert('选择的条数已超过上限(1000条), 建议先使用筛选功能');exit
            // }
            var notice = '';
            if(all > 1000){
              notice = '全部'+all+'条,由于已超1000条上限,仅能导出1000条.';
            }else{
              notice = "全部"+all+"条";
            }

            
            Layer.confirm(notice+"<form action='" + Fast.api.fixurl("pintuanorder/export") + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
              title: "导出",
              // btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)", "上限(" + row_num + "条)"],
              // btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "最近(" + 1000 + "条)"],
              btn: "确定",
              success: function (layero, index) {
                $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
              }
              // ,yes: function (index, layero) {
              //   submitForm(ids.join(","), layero);
              //   Layer.close(index);
              //   // return;
              // }
              // ,
              // btn2: function (index, layero) {
              //   var ids = [];
              //   $.each(page, function (i, j) {
              //     ids.push(j.id);
              //   });
              //   submitForm(ids.join(","), layero);
              //   return false;
              // }
              ,yes: function (index, layero) {
                submitForm("all", layero);
                // Layer.close(index);
                return false;
              }
            })
          });
          //*************************** 自定义export结束

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
