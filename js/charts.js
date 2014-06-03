$(document).ready(function($){
	$("#localityChart").on('click', function() {
		$("#localityChart").html("Please wait..");
		calculatePieCharts();

	});
});

function makeChart(data) {
	var items = JSON.parse(data);	
	
	chart1 = new cfx.Chart();
	
	var title = new cfx.TitleDockable();
	
	title.setText("Maps and reduces");
	chart1.getTitles().add(title);

	//chart1.getAnimations().getLoad().setEnabled(true);
	chart1.setDataSource(items);
	chart1.getAxisX().getLabelsFormat().setFormat(cfx.AxisFormat.Date);
	chart1.getAxisX().getLabelsFormat().setCustomFormat("dd-MMM-yy");
	
	var axis3 = new cfx.AxisY();
	axis3.setAutoScale(true);
	chart1.getAxisX().getTitle().setText("Date");
	chart1.getAxisY().getTitle().setText("Amount");	
	axis3.getTitle().setText("Duration (hours)");	
	

	var axis1 = chart1.getAxisY();
    chart1.getAxesY().add(axis3);
	
    var series1 = chart1.getSeries().getItem(0);
    var series2 = chart1.getSeries().getItem(1);
	var series3 = chart1.getSeries().getItem(2);

    series1.setAxisY(axis1);
    series2.setAxisY(axis1);
    series3.setAxisY(axis3);
	
	var divHolder = document.getElementById('ChartDiv');
	chart1.create(divHolder); 
	
}

function makeFileSizeChart(chartData, mapData) {
	var chartData = JSON.parse(chartData);

	var td;
	td = new cfx.TitleDockable();
	td.setText("Input vs Map output vs Reduce output ratios");
	
	chart2 = new cfx.Chart();
	chart2.getTitles().add(td);
	chart2.getLegendBox().setVisible(true);
	chart2.setGallery(cfx.Gallery.Area);

	//chart2.getView3D().setEnabled(true);
	chart2.getAllSeries().setStacked(cfx.Stacked.Normal);
	chart2.setDataSource(chartData);

	var mapAxis = new cfx.AxisY();
	mapAxis.setAutoScale(true);
	mapAxis.getTitle().setText("# mappers");	
    chart2.getAxesY().add(mapAxis);
	
	var mapSeries = chart2.getSeries().getItem(3);
    mapSeries.setAxisY(mapAxis);
	
	chart2.getSeries().getItem(3).setGallery(cfx.Gallery.Lines);

	
	chart2.getAxisX().getLabelsFormat().setFormat(cfx.AxisFormat.Date);
	chart2.getAxisX().getLabelsFormat().setCustomFormat("dd-MMM-yy");

	
	chart2.create(document.getElementById('div_Chart'))
}

function calculatePieCharts() {
	$.ajax({
		url:"actions.php",
		type: "POST",
		data: { action: "calculatePieCharts" },
		success:function(result){
			$("#localityChart").html("");
			var jsonResult = JSON.parse(result);
			createPieChart(jsonResult);						
		}
	});

}

function createPieChart(json) {
	localityChart = new cfx.Chart();
	var td;
	td = new cfx.TitleDockable();
	td.setText("Locality distribution across all jobs");
	localityChart.getTitles().add(td);
	localityChart.getLegendBox().setContentLayout(cfx.ContentLayout.Spread);
	localityChart.getLegendBox().setDock(cfx.DockArea.Right);
	var data = localityChart.getData();
	data.setSeries(3);
	data.setPoints(10);
	var i = 0;
	var j = 0;
	for(i = 0; (i < json.length); (i)++) {
		for(j = 1; (j < json[i].length); (j)++) {
			data.setItem(i, j-1, json[i][j].Amount);
		}
	}
	
	var labels = localityChart.getAxisX().getLabels();
    labels.clear();
    //labels.setItem(0, "N.A.");
    labels.setItem(0, "1-10%");
    labels.setItem(1, "11-20%");
    labels.setItem(2, "21-30%");
    labels.setItem(3, "31-40%");
    labels.setItem(4, "41-50%");
    labels.setItem(5, "51-60%");
    labels.setItem(6, "61-70%");
    labels.setItem(7, "71-80%");
    labels.setItem(8, "81-90%");
    labels.setItem(9, "91-100%");

	localityChart.getSeries().getItem(0).setText('Data local');
	localityChart.getSeries().getItem(1).setText('Rack local');
	localityChart.getSeries().getItem(2).setText('Outer rack');
	
	localityChart.getAxisX().getTitle().setText("Locality percentage");
	localityChart.getAxisY().getTitle().setText("Percentage of jobs");	
	
	
	localityChart.create(document.getElementById('localityChart'))
	
	/*

	localityChart.getLegendBox().setVisible(true);
	localityChart.getLegendBox().setBorder(cfx.DockBorder.External);
	localityChart.getLegendBox().setContentLayout(cfx.ContentLayout.Spread);
	localityChart.getLegendBox().setDock(cfx.DockArea.Left);
	var td;
	td = new cfx.TitleDockable();
	td.setText("Data locality distribution across all jobs");
	localityChart.getTitles().add(td);
	localityChart.setGallery(cfx.Gallery.Doughnut);
	localityChart.getAllSeries().getPointLabels().setVisible(true);
	var myPie;
	myPie = (localityChart.getGalleryAttributes());
	myPie.setExplodingMode(cfx.ExplodingMode.First);
	myPie.setSliceSeparation(20);
	
	localityChart.setDataSource(json);
	localityChart.create(document.getElementById('localityChart'))
	*/

}

function jobDistribution(json) {
	json = JSON.parse(json);


	jobDistributionChart = new cfx.Chart();

	jobDistributionChart.getLegendBox().setVisible(true);
	jobDistributionChart.getLegendBox().setBorder(cfx.DockBorder.External);
	jobDistributionChart.getLegendBox().setContentLayout(cfx.ContentLayout.Spread);
	jobDistributionChart.getLegendBox().setDock(cfx.DockArea.Left);
	var td;
	td = new cfx.TitleDockable();
	td.setText("Data locality distribution across all jobs");
	jobDistributionChart.getTitles().add(td);
	jobDistributionChart.setGallery(cfx.Gallery.Doughnut);
	jobDistributionChart.getAllSeries().getPointLabels().setVisible(true);
	var myPie;
	myPie = (jobDistributionChart.getGalleryAttributes());
	myPie.setExplodingMode(cfx.ExplodingMode.First);
	myPie.setSliceSeparation(20);
	
	jobDistributionChart.setDataSource(json);
	jobDistributionChart.create(document.getElementById('jobDistribution'))
}
