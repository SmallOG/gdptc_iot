define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template', 'form'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template, Form) {
	var Controller = {
		indexs: function () {
			// 初始化表格参数配置
			Table.api.init({
				extend: {
					index_url: '/admin/nginxlogs/indexs',
					dragsort_url: '',
					table: 'nginxlogs',
				}
			});
			var table = $("#table");				 
			var tableOptions = {
				url: $.fn.bootstrapTable.defaults.extend.index_url+'?dates='+Orderdata.dates,
				escape: false,
				pk: 'id',
				sortName: 'count',
				search: false,
				pagination: false,
				commonSearch: false,
				columns: [
					[
						{checkbox: true},
						{field: 'host', title: '域名'},
						{field: 'count', title: '访问', sortable: true},
						{field: 'url', title: "访问链接", formatter:function (value, row, index) {
							return '<a href="'+value+'" _target="blank" >打开链接</a>';
						}}
					]
				]
			};
			if ($(".datetimepicker").size() > 0) {
				require(['bootstrap-datetimepicker'], function () {
					var options = {
						format: 'YYYY-MM-DD HH:mm:ss',
						icons: {
							time: 'fa fa-clock-o',
							date: 'fa fa-calendar',
							up: 'fa fa-chevron-up',
							down: 'fa fa-chevron-down',
							previous: 'fa fa-chevron-left',
							next: 'fa fa-chevron-right',
							today: 'fa fa-history',
							clear: 'fa fa-trash',
							close: 'fa fa-remove'
						},
						showTodayButton: true,
						showClose: true
					};
					$('.datetimepicker').parent().css('position', 'relative');
					$('.datetimepicker').datetimepicker(options).on("dp.change", function(e) {
						var paddNum = function(num){return num>9?num:'0'+num; };
						var d = new Date(e.date);
						var now_date = d.getFullYear()+"-"+paddNum( d.getMonth() + 1)+'-'+paddNum(d.getDate());
						if( now_date !== e.date._i  ){
							window.location.href = $.fn.bootstrapTable.defaults.extend.index_url+'?dates='+now_date;
						}
			        		 
			        });
				});
			}

			// 初始化表格
			table.bootstrapTable(tableOptions);
			Controller.api.bindevent();
		},
		index: function () {
			// 初始化表格参数配置
			Table.api.init({
				extend: {
					index_url: '/admin/nginxlogs/index',
					dragsort_url: '',
					table: 'nginxlogs',
				}
			});

			var table = $("#table");				 
			var tableOptions = {
				url: $.fn.bootstrapTable.defaults.extend.index_url+'?dates='+Orderdata.dates+'&hostf='+Orderdata.hostf,
				escape: false,
				pk: 'id',
				sortName: 'count',
				search: false,
				pagination: false,
				commonSearch: false,
				columns: [
					[
						{checkbox: true},
						{field: 'host', title: '域名'},
						{field: 'count', title: '访问', sortable: true},
						{field: 'url', title: "访问链接", formatter:function (value, row, index) {
							return '<a href="'+value+'" _target="blank" >打开链接</a>';
						}}
					]
				]
			};
			if ($(".datetimepicker").size() > 0) {
				require(['bootstrap-datetimepicker'], function () {
					var options = {
						format: 'YYYY-MM-DD HH:mm:ss',
						icons: {
							time: 'fa fa-clock-o',
							date: 'fa fa-calendar',
							up: 'fa fa-chevron-up',
							down: 'fa fa-chevron-down',
							previous: 'fa fa-chevron-left',
							next: 'fa fa-chevron-right',
							today: 'fa fa-history',
							clear: 'fa fa-trash',
							close: 'fa fa-remove'
						},
						showTodayButton: true,
						showClose: true
					};
					$('.datetimepicker').parent().css('position', 'relative');
					$('.datetimepicker').datetimepicker(options).on("dp.change", function(e) {
						var paddNum = function(num){return num>9?num:'0'+num; };
						var d = new Date(e.date);
						var now_date = d.getFullYear()+"-"+paddNum( d.getMonth() + 1)+'-'+paddNum(d.getDate());

						if( now_date !== e.date._i  ){
							window.location.href = $.fn.bootstrapTable.defaults.extend.index_url+'?dates='+now_date+'&hostf='+Orderdata.hostf;
						}
			        		 
			        });
				});
			}

			// 初始化表格
			table.bootstrapTable(tableOptions);
			Controller.api.bindevent();
		},
		index_des: function () {
			// 初始化表格参数配置
			Table.api.init({
				extend: {
					index_url: '/admin/nginxlogs/index_des',
					dragsort_url: '',
					table: 'nginxlogs',
				}
			});

			var table = $("#table");				 
			var tableOptions = {
				url: $.fn.bootstrapTable.defaults.extend.index_url+'?dates='+Orderdata.dates+'&host='+Orderdata.host+'&hostf='+Orderdata.hostf,
				escape: false,
				pk: 'id',
				sortName: 'count',
				search: false,
				pagination: false,
				commonSearch: false,
				columns: [
					[
						{checkbox: true},
						{field: 'host', title: '域名'},
						{field: 'count', title: '访问', sortable: true},
						{field: 'url', title: "访问链接", formatter:function (value, row, index) {
							return '<a href="'+value+'" _target="blank" >打开链接</a>';
						}}
					]
				]
			};

			if ($(".datetimepicker").size() > 0) {
				require(['bootstrap-datetimepicker'], function () {
					var options = {
						format: 'YYYY-MM-DD HH:mm:ss',
						icons: {
							time: 'fa fa-clock-o',
							date: 'fa fa-calendar',
							up: 'fa fa-chevron-up',
							down: 'fa fa-chevron-down',
							previous: 'fa fa-chevron-left',
							next: 'fa fa-chevron-right',
							today: 'fa fa-history',
							clear: 'fa fa-trash',
							close: 'fa fa-remove'
						},
						showTodayButton: true,
						showClose: true
					};
					$('.datetimepicker').parent().css('position', 'relative');
					$('.datetimepicker').datetimepicker(options).on("dp.change", function(e) {
						var paddNum = function(num){return num>9?num:'0'+num; };
						var d = new Date(e.date);
						var now_date = d.getFullYear()+"-"+paddNum( d.getMonth() + 1)+'-'+paddNum(d.getDate());

						if( now_date !== e.date._i  ){
							window.location.href = $.fn.bootstrapTable.defaults.extend.index_url+'?dates='+now_date;
						}
			        		 
			        });
		
				});
			}

			// 初始化表格
			table.bootstrapTable(tableOptions);
			Controller.api.bindevent();
		},
		descs: function () {

			// 初始化表格参数配置
			Table.api.init({
				extend: {
					index_url: '/admin/nginxlogs/descs',
					dragsort_url: '',
					table: 'nginxlogs',
				}
			});



			var table = $("#table");				 
			var tableOptions = {
				url: $.fn.bootstrapTable.defaults.extend.index_url+'?dates='+Orderdata.dates+'&host='+Orderdata.host+'&table='+Orderdata.table+'&hostf='+Orderdata.hostf,
				escape: false,
				pk: 'id',
				sortName: 'count',
				search: false,
				pagination: false,
				sortable: true,
				cache: true,
				commonSearch: false,
				columns: [
					[
						{checkbox: true},
						{field: 'host', title: '域名'},
						{field: 'app',  title: '项目名', sortable: true},
						{field: 'port',  title: '端口', sortable: true},
						{field: 'count', title: '访问', sortable: true},
						{field: 'url', title: "访问链接", formatter:function (value, row, index) {
							return '<a href="'+value+'" _target="blank" >'+'打开链接'+'</a>';
						}}
					]
				]
			};

			// 初始化表格
			table.bootstrapTable(tableOptions);
			Controller.api.bindevent();
		},
		descs_des: function () {
			// 基于准备好的dom，初始化echarts实例
			var myChart = Echarts.init(document.getElementById('echart'), 'walden');
			// 指定图表的配置项和数据
			var option = {
				title: {
					text: '',
					subtext: ''
				},
				tooltip: {
					trigger: 'axis'
				},
				legend: {
					data: ['今天数量','昨天数量']
				},
				toolbox: {
					show: false,
					feature: {
						magicType: {show: true, type: ['stack', 'tiled']},
						saveAsImage: {show: true}
					}
				},
				xAxis: {
					type: 'category',
					boundaryGap: false,
					data: Orderdata.column
				},
				yAxis: {

				},
				grid: [{
						left: 50,
						top: 5,
						right: 10,
						bottom: 30
					}],
				series: [{
						name: '今天数量',
						type: 'line',
						areaStyle: {
							normal: {
							}
						},
						lineStyle: {
							normal: {
								width: 1.5
							}
						},
                        markLine : {
                            data : [
                                {type : 'average', name: '平均值'}
                            ]
                        },
						data: Orderdata.paydata
					},
					{
						name: '昨天数量',
						type: 'line',
                        color: '#fbfbfb',
						lineStyle: {
							normal: {
								width: 1.5
							}
						},
						data: Orderdata.paydata_yesterday
					}
				   ]
			};
			myChart.setOption(option);
			$(window).resize(function () {
				myChart.resize();
			});

			// 基于准备好的dom，初始化echarts实例
			var myChartTime = Echarts.init(document.getElementById('echart_time'), 'walden');
			// 指定图表的配置项和数据
            option = {
                title : {
                    text: '',
                    subtext: ''
                },
                tooltip : {
                    trigger: 'axis'
                },
                legend: {
                    data:['今天时间','昨天时间']
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis : [
                    {
                        type : 'category',
                        data: Orderdata.column
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
				grid: [{
						left: 50,
						top: 5,
						right: 10,
						bottom: 20
					}],
                series : [
                    {
                        name:'今天时间',
                        type: 'line',
                        data: Orderdata.time,
                        areaStyle: {
							normal: {
							}
						},
						lineStyle: {
							normal: {
								width: 1.5
							}
						},
                        markLine : {
                            data : [
                                {type : 'average', name: '平均值'}
                            ]
                        }
                    },
                    {
                        name:'昨天时间',
                        type: 'line',
                        color: '#fbfbfb',
                        data: Orderdata.time_yesterday,
						lineStyle: {
							normal: {
								width: 1.5
							}
						}
                    }
                ]
            };


			myChartTime.setOption(option);
			$(window).resize(function () {
				myChartTime.resize();
			});

			$(document).on("click", ".btn-checkversion", function(){
				top.window.$("[data-toggle=checkupdate]").trigger("click");
			});


			var index_url = '/admin/nginxlogs/descs_des';

			$("#select_type_t").change(function(){
				var type_t = $( this ).val();		 
				window.location.href = index_url+'?dates='+Orderdata.dates+'&host='+Orderdata.host+'&table='+Orderdata.table+'&hostf='+Orderdata.hostf+'&app='+Orderdata.app+'&port='+Orderdata.port+'&type_t='+type_t;			 		
			});
			
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