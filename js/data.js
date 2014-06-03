$(document).ready(function($){
	$("tr").on('click', function() {
		if($(this).find('th').length == 0) {
			if($(this).next().css('display') == 'none') {
				$(this).next().slideDown();
				
				var that = $(this);
				if(!$(that.next().find('div.chart')).html()) {
				
				var date = $(this).find('input.dateJob').val();
				$.ajax({
					url:"actions.php",
					type: "POST",
					data: { date: date },
					success:function(result){
						var jsonResult = JSON.parse(result);
						createDayChart(jsonResult[1], that.next().find('div.chart'), that.find('.actualDate').html(), jsonResult[0]);						
					}
				});
				}
			}
			else {
				$(this).next().slideUp();
			
			}
		}
	});
	
});

function createDayChart(data, dayChartDiv,date, idleTime) {
	
	chart1 = new cfx.Chart();
	
	chart1.getAnimations().getLoad().setEnabled(true);
	chart1.setDataSource(data);
	chart1.getAxisX().getLabelsFormat().setFormat(cfx.AxisFormat.Time);
	chart1.getAxisX().getLabelsFormat().setCustomFormat("HH:mm");
	
	var title = new cfx.TitleDockable();
	title.setText("Amount of jobs for " + date +" | Percent idle:" + idleTime +" %");
	chart1.getTitles().add(title);	
	
	chart1.create($(dayChartDiv)[0]);
		
}
