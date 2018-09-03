define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'volume/index',
                    add_url: 'volume/add',
                    edit_url: 'volume/edit',
                    del_url: 'volume/del',
                    multi_url: 'volume/multi',
                    table: 'volume',
                }
            });

            var table = $("#table");
            var tableOptions = {
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'note', title: __('Note')},
                        {field: 'url', title: "访问链接", formatter:function (value, row, index) {
                            return '<a href="'+value+'" target="blank" >'+value+'</a>';
                        }},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            };

  

                 
            // 初始化表格
            table.bootstrapTable( tableOptions );

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            var card = {
                "car_color_select": function() {
                    var q_this = $(this);
                    q_this.parents(".form-group").css({
                        "background-color": "#" + q_this.val()
                    });
                },
                'add_car': function() {
                    var html = $("#hidden_car").html();
                    html = html.replace(/c-type_tmp_id/g, 'c-type_' + ($("#edit-form").find('.car_parent').size()));
                    $("#from_footer").before(html);

                    if( ! $("#edit-form").find('.car_parent').last().find(".selectpicker").selectpicker ){
                        require(['bootstrap-select', 'bootstrap-select-lang'], function () {
                            $("#edit-form").find('.car_parent').last().find(".selectpicker").selectpicker();
                        });
                    }else{
                        $("#edit-form").find('.car_parent').last().find(".selectpicker").selectpicker('refresh');
                    }
                    
                },
                'delete_car': function() {
                    var q_par = $(this).parents('.car_parent');
                    var id = q_par.find("[name='car[id][]']").val();
                    if( id == 0 ){
                        q_par.remove();
                    }else{

                        Layer.confirm( '你确定要删除吗！', function(index) {
                            $.ajax({
                                url: 'volume/del_car',
                                dataType: 'json',
                                data: {type: 'del',"ids":id},
                                cache: false,
                                success: function (ret) {
                                    if (ret.hasOwnProperty("code")) {
                                        if (ret.code === 1) {
                                            Toastr.success( '删除成功！' );
                                            q_par.remove();
                                        } else {
                                            Toastr.error( '删除失败！' );
                                        }
                                    } else {
                                        Toastr.error(__('Unknown data format'));
                                    }
                                }, error: function () {
                                    Toastr.error(__('Network error'));
                                }
                            });
                            Layer.close(index);
                        });

                    }

                }
            };

            $( document).on('change',".car_color_select",card.car_color_select);
            $(".add_car").click(card.add_car);
            $(document).on('click','.car_delete',card.delete_car);




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