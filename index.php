<?php
include "config.php";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SURFsara logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
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
    <script src="js/bootstrap.min.js"></script>
    <script src="js/data.js"></script>
	
    <script type="text/javascript" src="js/jchartfx/jchartfx.system.js"></script>
    <script type="text/javascript" src="js/jchartfx/jchartfx.coreBasic.js"></script>
    <script type="text/javascript" src="js/jchartfx/jchartfx.advanced.js"></script>
    <script type="text/javascript" src="js/jchartfx/jchartfx.animation.js"></script>
	
  </head>

  <body>

    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="index.php">Home</a></li>
          <li><a href="charts.php">Charts</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
        <h3 class="muted">SURFsara logs</h3>
      </div>

      <hr>
	  
	  <div>
		<table class="table table-striped">
			<tr>
				<th>Date</th>
				<th># jobs</th>
				<th># maps</th>
				<th># reduces</th>
				<th>map : reduce ratio</th>
				<th>avg job dur</th>
				<th>max(job dur)</th>
				<th>Success rate (%)</th>
				<th>Input (GB)</th>
				<th>Input Reducers (GB)</th>
				<th>Output (GB)</th>
				<th>avg CPU</th>
				<th>CPU : duration</th>
				<!--<th>total CPU</th>//-->
			</tr>
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
				MAX(LAUNCH_TIME) as maxLaunch,
				MAX(FINISH_TIME) as maxFinish
				
				FROM   jobs
				LEFT JOIN counters ON counters.jobid=jobs.jobid
				WHERE LAUNCH_TIME > 0
				GROUP BY DATE(FROM_UNIXTIME(LAUNCH_TIME/1000))
				ORDER BY ForDate");
				
			while($list = mysql_fetch_object($select)) {
				$t = ($list->finishedTimeSum-$list->launchTimeSum)/1000/$list->NumPosts;
				$maxT = ($list->maxFinish-$list->maxLaunch)/1000;
				$cpuMS = $list->cpuMS/$list->NumPosts/1000;
				
				//$totalCPU = $list->cpuMS/1000;
				//$success_rate = mysql_query("SELECT COUNT(*) AS successPosts FROM jobs WHERE JOB_STATUS='SUCCESS' AND LAUNCH_TIME > '".(strtotime($list->ForDate)*1000)."' AND LAUNCH_TIME < '".((strtotime($list->ForDate)*1000)+86400000)."'")or die(mysql_error());		
				//$list2 = mysql_fetch_object($success_rate);
				?>
				<tr>
					<td><span class="actualDate"><?php echo $list->ForDate; ?></span><input type="hidden" class='dateJob' value="<?php echo (strtotime($list->ForDate)*1000); ?>" /></td>
					<td><?php echo $list->NumPosts; ?></td>
					<td><?php echo $list->amountOfMaps; ?></td>
					<td><?php echo $list->amountOfReduces; ?></td>
					<td>1 : <?php echo $list->amountOfReduces == 0 ? "&infin;" : round($list->amountOfMaps/$list->amountOfReduces,2); ?></td>
					<td><?php echo sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60); ?></td>
					<td><?php echo sprintf('%02d:%02d:%02d', ($maxT/3600),($maxT/60%60), $maxT%60); ?></td>
					<td><?php echo "xx"; //echo round(($list2->successPosts/$list->NumPosts)*100,2); ?></td>
					<td><?php echo round($list->inputBytes/1024/1024/1024,2); ?></td>
					<td><?php echo round($list->inputReducersBytes/1024/1024/1024,2); ?></td>
					<td><?php echo round($list->outputBytes/1024/1024/1024,2); ?></td>
					<td><?php echo sprintf('%02d:%02d:%02d', ($cpuMS/3600),($cpuMS/60%60), $cpuMS%60); ?></td>
					<!--<td><?php //echo sprintf('%02d:%02d:%02d', ($totalCPU/3600),($totalCPU/60%60), $totalCPU%60); ?></td>//-->
					<td>1: <?php echo round($cpuMS/$t,2); ?></td>
				</tr>
				<tr style="display:none; height: 400px;">
					<td colspan="13"><div class="chart" style="width: 100%; height:400px;"></div></td>
				</tr>
			<?php } 
			$sums = mysql_query("SELECT SUM(LAUNCH_TIME) as launchTimeSum, SUM(FINISH_TIME) as finishedTimeSum, COUNT(*) as sumJobs, SUM(FINISHED_MAPS) as sumMaps, SUM(FINISHED_REDUCES) as sumReduces FROM jobs WHERE LAUNCH_TIME>0 ");
			$totals = mysql_fetch_object($sums);
			$t = ($totals->finishedTimeSum-$totals->launchTimeSum)/1000/$totals->sumJobs;
			?>
			<tr>
				<th>Totals/averages</th>
				<th><?php echo $totals->sumJobs; ?></th>
				<th><?php echo $totals->sumMaps; ?></th>
				<th><?php echo $totals->sumReduces; ?></th>
				<th>1 : <?php echo round($totals->sumMaps/$totals->sumReduces,2); ?></th>
				<th><?php echo  sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60); ?></th>
			</tr>
		</table>
	  </div>
		<!--
      <div class="jumbotron">
        <h1>Super awesome marketing speak!</h1>
        <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
        <a class="btn btn-large btn-success" href="#">Sign up today</a>
      </div>

      <hr>

      <div class="row-fluid marketing">
        <div class="span6">
          <h4>Subheading</h4>
          <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

          <h4>Subheading</h4>
          <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

          <h4>Subheading</h4>
          <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
        </div>

        <div class="span6">
          <h4>Subheading</h4>
          <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

          <h4>Subheading</h4>
          <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

          <h4>Subheading</h4>
          <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
        </div>
      </div>
	//-->
      <hr>

      <div class="footer">
        <p>&copy; SURFsara logs 2014</p>
      </div>

    </div> <!-- /container -->


  </body>
</html>
