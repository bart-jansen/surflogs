<?php
include "config.php";

	
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SURFsara logs - Charts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/jchartfx/jchartfx.css" />
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: calc(100% - 25px);
      }
      .container-narrow > hr {
        margin: 30px 0;
      }
		svg.jchartfx rect {
			fill: #fff !important;
		}
   
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
	<link rel="shortcut icon" href="../assets/ico/favicon.png">
	
    <script src="js/jquery.js"></script>
	    <script src="js/charts.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jchartfx/jchartfx.system.js"></script>
    <script type="text/javascript" src="js/jchartfx/jchartfx.coreBasic.js"></script>
    <script type="text/javascript" src="js/jchartfx/jchartfx.advanced.js"></script>
    <script type="text/javascript" src="js/jchartfx/jchartfx.animation.js"></script>
  </head>

  <body>

    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li><a href="index.php">Home</a></li>
          <li class="active"><a href="charts.php">Charts</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
        <h3 class="muted">SURFsara logs</h3>
      </div>

      <hr>
	  

	  <div>
		<div id="ChartDiv" style="width:100%;height:400px;display:inline-block"></div>

	  </div>      
	  
	  <hr>
	  

	  <div>
		<div id="div_Chart" style="width:100%;height:400px;display:inline-block"></div>

	  </div>	  
	  
	 	  <hr>

	  <div>
		<div id="jobDistribution" style="width:100%;height:400px;display:inline-block; text-align: center;"></div>

	  </div>	 	  
	  
	  <hr>

	  <div>
		<div id="localityChart" style="width:100%;height:400px;display:inline-block; text-align: center;">Click to calculate data locality </div>

	  </div>
	  <?php
	  $select = mysql_query("SELECT DATE(FROM_UNIXTIME(LAUNCH_TIME/1000)) AS ForDate,
		COUNT(*) AS NumPosts,
		SUM(FINISHED_MAPS) as amountOfMaps,
		SUM(FINISHED_REDUCES) as amountOfReduces,
		SUM(LAUNCH_TIME) as launchTimeSum,
		SUM(FINISH_TIME) as finishedTimeSum,
		SUM(HDFS_BYTES_WRITTEN) as outputBytes,
		SUM(HDFS_BYTES_READ) as inputBytes,
		SUM(FILE_BYTES_READ) as inputReducersBytes,
		SUM(CPU_MILLISECONDS) as cpuMS,
		SUM(TOTAL_LAUNCHED_MAPS) as launchedMaps
		FROM   jobs
		LEFT JOIN counters ON counters.jobid=jobs.jobid
		WHERE LAUNCH_TIME > 1388534400000
		GROUP BY DATE(FROM_UNIXTIME(LAUNCH_TIME/1000))
		ORDER BY ForDate");
		
	$job_status_select = mysql_query("SELECT COUNT(*) as counter, JOB_STATUS FROM `jobs` GROUP BY JOB_STATUS");
	
	$chart_data = [];
	
	while($list = mysql_fetch_object($select)) {
		$chart_data[] = (object) ['Date' => date('Y-m-d',strtotime($list->ForDate))."T00:00:00.000Z",
		'Maps' => intval($list->amountOfMaps),
		'Reduces' => intval($list->amountOfReduces),
		'Duration' => ($list->finishedTimeSum-$list->launchTimeSum)/1000/3600]; 
		
		$chart_filesize_data[] = (object) ['Date' => date('Y-m-d',strtotime($list->ForDate))."T00:00:00.000Z",
		'Input' => intval($list->inputBytes/1024/1024/1024/1024),
		'InputReduce' => intval($list->inputReducersBytes/1024/1024/1024/1024),
		'Output' => intval($list->outputBytes/1024/1024/1024/1024),
		'Maps' => intval($list->amountOfMaps)]; 
	}
	
	while($status = mysql_fetch_object($job_status_select)) {
		$statuses[] = (object) ['Amount' => intval($status->counter), 'Status' => $status->JOB_STATUS];
	}
	$js_array = json_encode($chart_data);
	$datajs_array = json_encode($chart_filesize_data);
	$status_array = json_encode($statuses);

	echo "<script> makeChart('".$js_array."'); </script>";		
	echo "<script> makeFileSizeChart('".$datajs_array."'); </script>";		
	echo "<script> jobDistribution('".$status_array."'); </script>";		
	?>
      <hr>

      <div class="footer">
        <p>&copy; SURFsara logs 2014</p>
      </div>

    </div> <!-- /container -->


  </body>
</html>
