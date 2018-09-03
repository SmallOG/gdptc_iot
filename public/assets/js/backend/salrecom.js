define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'salrecom/index',
                    add_url: 'salrecom/add',
                    edit_url: 'salrecom/edit',
                    del_url: 'salrecom/del',
                    multi_url: 'salrecom/multi',
                    table: 'salrecom',
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
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'title', title: __('Title')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'wxh', title: __('Wxh')},
                        {field: 'url', title: "访问链接", formatter:function (value, row, index) {
                            return '<a href="'+value+'" target="blank" >'+value+'</a>';
                        }},
                        {field: '1_image', title: __('1_image'), formatter: Table.api.formatter.image},
                        {field: '2_image', title: __('2_image'), formatter: Table.api.formatter.image},
                        {field: '3_image', title: __('3_image'), formatter: Table.api.formatter.image},
                        {field: '4_image', title: __('4_image'), formatter: Table.api.formatter.image},
                        {field: '5_image', title: __('5_image'), formatter: Table.api.formatter.image},
                        {field: '6_image', title: __('6_image'), formatter: Table.api.formatter.image},
                        {field: '7_image', title: __('7_image'), formatter: Table.api.formatter.image},
                        {field: '8_image', title: __('8_image'), formatter: Table.api.formatter.image},
                        {field: 'views', title: __('Views')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"normal":__('normal'),"hidden":__('hidden')}},
                        {field: 'status_text', title: __('Status'), operate:false},
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