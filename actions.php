<?php
include "config.php";

if(isset($_POST['date'])) {
	$date = $_POST['date'];
	
	$i = 0;
	$select  = mysql_query("
		SELECT round((LAUNCH_TIME-".($date).") / 100000) as launchTime, 
		COUNT(*) as jobsAmount 
		FROM `jobs` 
		WHERE LAUNCH_TIME > ".($date)." 
		AND LAUNCH_TIME < ".($date+86400000)." 
		GROUP BY round((LAUNCH_TIME-".($date).") /100000) 
		ORDER BY LAUNCH_TIME");
		
	/*$chart_jobs_data[] = (object) ['Date' => date("Y-m-d", ($date/1000)-86400)."T23:00:00.000Z",
		'Amount of Jobs' => 0]; */
		
	//86400/100
	while($i <= 864) {
		$chart_jobs_data[$i] = (object) ['Date' => date('Y-m-d',($date/1000)+(100*$i)-7200)."T".date('H:i:s',($date/1000)+(100*$i)-7200).".000Z",
		'AmountofJobs' => 0];
		$i++;
	}
	
	while($list = mysql_fetch_object($select)) {
		$chart_jobs_data[$list->launchTime]->AmountofJobs = intval($list->jobsAmount);
	}
	
	$chart_data = [round(((864-mysql_num_rows($select))/864)*100, 2), $chart_jobs_data];

	echo json_encode($chart_data);
		

}
else if($_POST['action'] == "calculatePieCharts") {
	$totSelect = mysql_query("SELECT COUNT(*) as total FROM jobs LEFT JOIN counters ON counters.jobid = jobs.jobid");
		
	$select = mysql_query("SELECT 
		COUNT(*) as amount,
		CASE
			WHEN rack_local_maps/total_launched_maps >= 0 AND rack_local_maps/total_launched_maps <= 0.1 THEN '0-10'
			WHEN rack_local_maps/total_launched_maps > 0.1 AND rack_local_maps/total_launched_maps <= 0.2 THEN '11-20'
			WHEN rack_local_maps/total_launched_maps > 0.2 AND rack_local_maps/total_launched_maps <= 0.3 THEN '21-30'
			WHEN rack_local_maps/total_launched_maps > 0.3 AND rack_local_maps/total_launched_maps <= 0.4 THEN '31-40'
			WHEN rack_local_maps/total_launched_maps > 0.4 AND rack_local_maps/total_launched_maps <= 0.5 THEN '41-50'
			WHEN rack_local_maps/total_launched_maps > 0.5 AND rack_local_maps/total_launched_maps <= 0.6 THEN '51-60'
			WHEN rack_local_maps/total_launched_maps > 0.6 AND rack_local_maps/total_launched_maps <= 0.7 THEN '61-70'
			WHEN rack_local_maps/total_launched_maps > 0.7 AND rack_local_maps/total_launched_maps <= 0.8 THEN '71-80'
			WHEN rack_local_maps/total_launched_maps > 0.8 AND rack_local_maps/total_launched_maps <= 0.9 THEN '81-90'
			WHEN rack_local_maps/total_launched_maps > 0.9 AND rack_local_maps/total_launched_maps <= 1 THEN '91-100'
		END AS localityband

		FROM jobs LEFT JOIN counters ON counters.jobid = jobs.jobid
		GROUP BY localityband");	
		
		$select2 = mysql_query("SELECT 
		COUNT(*) as amount,
		CASE
			WHEN data_local_maps/total_launched_maps >= 0 AND data_local_maps/total_launched_maps <= 0.1 THEN '0-10'
			WHEN data_local_maps/total_launched_maps > 0.1 AND data_local_maps/total_launched_maps <= 0.2 THEN '11-20'
			WHEN data_local_maps/total_launched_maps > 0.2 AND data_local_maps/total_launched_maps <= 0.3 THEN '21-30'
			WHEN data_local_maps/total_launched_maps > 0.3 AND data_local_maps/total_launched_maps <= 0.4 THEN '31-40'
			WHEN data_local_maps/total_launched_maps > 0.4 AND data_local_maps/total_launched_maps <= 0.5 THEN '41-50'
			WHEN data_local_maps/total_launched_maps > 0.5 AND data_local_maps/total_launched_maps <= 0.6 THEN '51-60'
			WHEN data_local_maps/total_launched_maps > 0.6 AND data_local_maps/total_launched_maps <= 0.7 THEN '61-70'
			WHEN data_local_maps/total_launched_maps > 0.7 AND data_local_maps/total_launched_maps <= 0.8 THEN '71-80'
			WHEN data_local_maps/total_launched_maps > 0.8 AND data_local_maps/total_launched_maps <= 0.9 THEN '81-90'
			WHEN data_local_maps/total_launched_maps > 0.9 AND data_local_maps/total_launched_maps <= 1 THEN '91-100'
		END AS localityband

		FROM jobs LEFT JOIN counters ON counters.jobid = jobs.jobid
		GROUP BY localityband");		
		
		$select3 = mysql_query("SELECT 
		COUNT(*) as amount,
		CASE
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps >= 0 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.1 THEN '0-10'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.1 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.2 THEN '11-20'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.2 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.3 THEN '21-30'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.3 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.4 THEN '31-40'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.4 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.5 THEN '41-50'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.5 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.6 THEN '51-60'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.6 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.7 THEN '61-70'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.7 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.8 THEN '71-80'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.8 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 0.9 THEN '81-90'
			WHEN (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps > 0.9 AND (total_launched_maps-data_local_maps-rack_local_maps)/total_launched_maps <= 1 THEN '91-100'
		END AS localityband

		FROM jobs LEFT JOIN counters ON counters.jobid = jobs.jobid
		GROUP BY localityband");
		
	$totalList = mysql_fetch_object($totSelect);
	$totalJobs = $totalList->total;
	
	while($list2 = mysql_fetch_object($select2)) {
		$chartLocalityData[0][] = ['Locality' => $list2->localityband == null ? "N.A." : $list2->localityband, 'Amount' => 100*round($list2->amount/$totalJobs, 4)];
	}
	
	while($list = mysql_fetch_object($select)) {
		$chartLocalityData[1][] = ['Locality' => $list->localityband == null ? "N.A." : $list->localityband, 'Amount' => 100*round($list->amount/$totalJobs, 4)];
	}	
	
	while($list3 = mysql_fetch_object($select3)) {
		$chartLocalityData[2][] = ['Locality' => $list3->localityband == null ? "N.A." : $list3->localityband, 'Amount' => 100*round($list3->amount/$totalJobs, 4)];
	}
	

	
	echo json_encode($chartLocalityData);

}
?>