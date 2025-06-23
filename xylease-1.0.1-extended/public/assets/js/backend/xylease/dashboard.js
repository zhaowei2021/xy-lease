define(['jquery', 'bootstrap', 'backend', 'table', 'xylease_vue', 'echarts', 'echarts-theme'], function($, undefined, Backend,Table, Vue, Echarts) {

	var Controller = {
		index: function() {
			var orderChart = Echarts.init($('#order-chart')[0], 'walden');
			orderChart.setOption({
				legend: {data: ['租赁订单','会员订单','充值订单']},
				tooltip: {trigger: 'axis',formatter: "{b}<br>{a0} : {c0} 个 <br>{a1} : {c1} 个 <br>{a2} : {c2} 个 "},
				toolbox: {show: true,feature: {magicType: {show: true,type: ['line','bar']},restore: {show: true},saveAsImage: {show: true}}},
				calculable: true,
				xAxis: [
					{
						type: 'category',
						data: Config.weekDays
					}
				],
				yAxis: [
					{type: 'value'}
				],
				yAxis: [
					{type: 'value'}
				],
				series: [
					{	
						name: '租赁订单',
						type: 'line',
						data: Config.orderDayTotalNum,
						markPoint: {data: [{type: 'max',name: '最大值'},{type: 'min',name: '最小值'}]}
					},
					{
						name: '充值订单',
						type: 'line',
						smooth: true,
						symbol: 'none',
						data: Config.rechargeDayTotalNum,
						markPoint: {data: [{type: 'max',name: '最大值'},{type: 'min',name: '最小值'}]}
					}
				]
			});

			var orderChart = Echarts.init($('#sales-chart')[0], 'walden');
			orderChart.setOption({
				legend: {data: ['租赁订单', '会员订单', '充值订单']},
				tooltip: {trigger: 'axis',formatter: "{b}<br>{a0} : {c0} 元 <br>{a1} : {c1} 元 <br>{a2} : {c2} 元 "},
				toolbox: {show: true,feature: {magicType: {show: true,type: ['line','bar']},restore: {show: true},saveAsImage: {show: true}}},
				calculable: true,
				xAxis: [
					{
						type: 'category',
						data: Config.weekDays
					}
				],
				yAxis: [
					{type: 'value'}
				],
				yAxis: [
					{type: 'value'}
				],
				series: [
					{	
						name: '租赁订单',
						type: 'bar',
						data: Config.orderDayTotalMoney,
						markPoint: {data: [{type: 'max',name: '最大值'},{type: 'min',name: '最小值'}]}
					},
					{
						name: '充值订单',
						type: 'bar',
						smooth: true,
						symbol: 'none',
						data: Config.rechargeDayTotalMoney,
						markPoint: {data: [{type: 'max',name: '最大值'},{type: 'min',name: '最小值'}]}
					}
				]
			});
		},
		api: {
			bindevent: function() {
				Form.api.bindevent($("form[role=form]"));
			}
		}
	};
	return Controller;
});
