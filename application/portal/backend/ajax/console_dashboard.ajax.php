<?php 

/*
	FILE DESCRIPTION:
		PATH: backend/ajax/console_dashboard.ajax.php;
		TYPE: ajax (AJAX responce file);
		PURPOSE: runs function getDashboard in file backend/includes/console_dashboard.fnc.php through an AJAX call from the Dashboard or the Map View selection or parameter changing (files frontend/js/console_dashboard.js and frontend/js/console_map.js) to return the Dashboard or the Map View HTMLs to be inserted into the tool placeholder in the Console page;
		REFERENCED IN: frontend/js/console_dashboard.js, frontend/js/console_map.js;
		FUNCTIONS DECLARED - ;
		STYLES: frontend/css/console_dashboard.css, frontend/css/console_map.css; 
*/  

require_once ('../../root.php');

	$start_time = microtime(true);
	$timing = true;
	$timing_log = ROOT.'/logs/debug_log.txt';
	
	
require_once (ROOT.'backend/includes/security.fnc.php');

session_start();

//sleep(105);

if (!empty($_POST['param']) && is_array($_POST['param'])) {
	$param = $_POST['param'];
} else {
//	return 'ajax_empty';	// TODO - make handler in front 
	return '';
}
 
list(
	$ajax_key,
	$type,
//paste start
	$company_id,
	$company_compare_id,
	$attribute_id,
	$benchmarking,
	$period_id,
	$gender_id,
	$age_id,
	$country_id,
	$region_type_id,
	$region_id,
	$education_type_id,
	$income_type_id,
	$segment_id,
	$industry_id,
	$company_size_id,
	$diagnosis_id,
	$family_id,
	$children_id,
	$housing_id,
	$living_id,
	$engagement_id,
	$facilities_id,
	$tg1_id,
	$tg2_id,
	$novo1_id,
	$novo2_id,
	$demant2_id,
	$rw1_id,
	$rw2_id,
	$gr1_id,
	$gr2_id,
	$v_customer_id,
	$gol1_id,
	$gol2_id,
	$gol3_id,
	$gol5_id,
	$veolia1_id,
	$touchpoints_id,
	$employment_id,
	$race_id,
	$ethnicity_id,
	$politics_id,
	$children_number_id,
	$tccc_influence_id,
	$tccc_segment_id,
	$dmi1_id,
	$energinet1_id,
	$stakeholder_type_id,
	$hovedkategori_id,
	$siemensq1_id,
	$association_id,
	$segment2_id,
	$fk1_id,
	$region_zone_id,
	$education_id,
	$income_id,
	$cv2_id,
	$cv3_id,
	$subregion_br_group_id,
	$wsa1_id,
	$wsa2_id,
	$wsa3_studyarea_id,
	$sf1_id,
	$eon1_id,
	$eon_customer_id,
	$gn2_id,
	$gn1_id,
	$gn3_id,
	$gn4_id,
	$zs2_id,
	$zs3_id,
	$ess1_id,
	$ess2_id,
	$ori1_id,
	$ovo_influencer_id,
	$ovo_customer_id,
	$bay5_id,
	$ethnicity_ca_id,
	$politics_ca_id,
	$air02_id,
	$air03_id,
	$air04_id
) = $_POST['param'];

// echo $ajax_key;
// echo $type;
// //paste start
// echo ' - ' . $company_id . ' ++ ';
// echo ' - ' . $company_compare_id . ' ++ ';
// echo ' - ' . $attribute_id . ' ++ ';
// echo ' - ' . $benchmarking . ' ++ benchmarking ';
// echo ' - ' . $period_id . ' ++period_id ';
// echo ' - ' . $gender_id . ' ++ ';
// echo ' - ' . $age_id . ' ++ ';
// echo ' - ' . $country_id . ' ++ ';
// echo ' - ' . $region_type_id . ' ++ ';
// echo ' - ' . $region_id . ' ++ ';
// echo ' - ' . $education_type_id . ' ++ ';
// echo ' - ' . $income_type_id . ' ++ ';
// echo ' - ' . $segment_id . ' ++ ';
// echo ' - ' . $industry_id . ' ++ ';
// echo ' - ' . $company_size_id . ' ++ ';
// echo ' - ' . $diagnosis_id . ' ++ ';
// echo ' - ' . $family_id . ' ++ ';
// echo ' - ' . $children_id . ' ++ ';
// echo ' - ' . $housing_id . ' ++ ';
// echo ' - ' . $living_id . ' ++ ';
// echo ' - ' . $engagement_id . ' ++ ';
// echo ' - ' . $facilities_id . ' ++ ';
// echo ' - ' . $tg1_id . ' ++ ';
// echo ' - ' . $tg2_id . ' ++ ';
// echo ' - ' . $novo1_id . ' ++ ';
// echo ' - ' . $novo2_id . ' ++ ';
// echo ' - ' . $demant2_id . ' ++ ';
// echo ' - ' . $rw1_id . ' ++ ';
// echo ' - ' . $rw2_id . ' ++ ';
// echo ' - ' . $gr1_id . ' ++ ';
// echo ' - ' . $gr2_id . ' ++ ';
// echo ' - ' . $v_customer_id . ' ++ ';
// echo ' - ' . $gol1_id . ' ++ ';
// echo ' - ' . $gol2_id . ' ++ ';
// echo ' - ' . $gol3_id . ' ++ ';
// echo ' - ' . $gol5_id . ' ++ ';
// echo ' - ' . $veolia1_id . ' ++ ';
// echo ' - ' . $touchpoints_id . ' ++ ';
// echo ' - ' . $employment_id . ' ++ ';
// echo ' - ' . $race_id . ' ++ ';
// echo ' - ' . $ethnicity_id . ' ++ ';
// echo ' - ' . $politics_id . ' ++ ';
// echo ' - ' . $children_number_id . ' ++ ';
// echo ' - ' . $tccc_influence_id . ' ++ ';
// echo ' - ' . $tccc_segment_id . ' ++ ';
// echo ' - ' . $dmi1_id . ' ++ ';
// echo ' - ' . $energinet1_id . ' ++ ';
// echo ' - ' . $stakeholder_type_id . ' ++ ';
// echo ' - ' . $hovedkategori_id . ' ++ ';
// echo ' - ' . $siemensq1_id . ' ++ ';
// echo ' - ' . $association_id . ' ++ ';
// echo ' - ' . $segment2_id . ' ++ ';
// echo ' - ' . $fk1_id . ' ++ ';
// echo ' - ' . $region_zone_id . ' ++ ';
// echo ' - ' . $education_id . ' ++ ';
// echo ' - ' . $income_id . ' ++ ';
// echo ' - ' . $cv2_id . ' ++ ';
// echo ' - ' . $cv3_id . ' ++ ';
// echo ' - ' . $subregion_br_group_id . ' ++ ';
// echo ' - ' . $wsa1_id . ' ++ ';
// echo ' - ' . $wsa2_id . ' ++ ';
// echo ' - ' . $wsa3_studyarea_id . ' ++ ';
// echo ' - ' . $sf1_id . ' ++ ';
// echo ' - ' . $eon1_id . ' ++ ';
// echo ' - ' . $eon_customer_id . ' ++ ';
// echo ' - ' . $gn2_id . ' ++ ';
// echo ' - ' . $gn1_id . ' ++ ';
// echo ' - ' . $gn3_id . ' ++ ';
// echo ' - ' . $gn4_id . ' ++ ';
// echo ' - ' . $zs2_id . ' ++ ';
// echo ' - ' . $zs3_id . ' ++ ';
// echo ' - ' . $ess1_id . ' ++ ';
// echo ' - ' . $ess2_id . ' ++ ';
// echo ' - ' . $ori1_id . ' ++ ';
// echo ' - ' . $ovo_influencer_id . ' ++ ';
// echo ' - ' . $ovo_customer_id . ' ++ ';
// echo ' - ' . $bay5_id . ' ++ ';
// echo ' - ' . $ethnicity_ca_id . ' ++ ';
// echo ' - ' . $politics_ca_id . ' ++ ';
// echo ' - ' . $air02_id . ' ++ ';
// echo ' - ' . $air03_id . ' ++ ';
// echo ' - ' . $air04_id . ' ++ ';
// ajax filter

if ($type == 'dashboard') { 
	if ($ajax_key >= $_SESSION['dashboard_ajax_last']) {
		$_SESSION['dashboard_ajax_last'] = $ajax_key;
	} else {
		echo 'ajax_false';
		return;
	}
}

if ($type == 'map') { 
	if ($ajax_key >= $_SESSION['map_ajax_last']) {
		$_SESSION['map_ajax_last'] = $ajax_key;
	} else {
		echo 'ajax_false';
		return;
	}
}

if ($type == 'table') { 
	if ($ajax_key >= $_SESSION['table_ajax_last']) {
		$_SESSION['table_ajax_last'] = $ajax_key;
	} else {
		echo 'ajax_false';
		return;
	}
}


require_once (ROOT.'sync/constants.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'backend/includes/statistics.fnc.php');
require_once (ROOT.'backend/includes/console_dashboard.fnc.php');
require_once (ROOT.'sync/functions.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'sync/sql_connect.php');

checkAccess(0);

// get parameters

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('console_dashboard.ajax.php time 01: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//? $threads = get_sql_threads(sql_stat()); $max_sql_threads = get_max_sql_threads();
//? if ($threads<$max_sql_threads) 
	session_write_close();
//? else debug_log('console_dashboard.ajax.php: session not closed - $threads='.$threads.', $max_sql_threads='.$max_sql_threads, ROOT.'/logs/debug_log.txt');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('console_dashboard.ajax.php time 02: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
echo 'okok before ';

$return = getDashboard(
	$type,
//paste start
	$company_id,
	$company_compare_id,
	$attribute_id,
	$benchmarking,
	$period_id,
	$gender_id,
	$age_id,
	$country_id,
	$region_type_id,
	$region_id,
	$education_type_id,
	$income_type_id,
	$segment_id,
	$industry_id,
	$company_size_id,
	$diagnosis_id,
	$family_id,
	$children_id,
	$housing_id,
	$living_id,
	$engagement_id,
	$facilities_id,
	$tg1_id,
	$tg2_id,
	$novo1_id,
	$novo2_id,
	$demant2_id,
	$rw1_id,
	$rw2_id,
	$gr1_id,
	$gr2_id,
	$v_customer_id,
	$gol1_id,
	$gol2_id,
	$gol3_id,
	$gol5_id,
	$veolia1_id,
	$touchpoints_id,
	$employment_id,
	$race_id,
	$ethnicity_id,
	$politics_id,
	$children_number_id,
	$tccc_influence_id,
	$tccc_segment_id,
	$dmi1_id,
	$energinet1_id,
	$stakeholder_type_id,
	$hovedkategori_id,
	$siemensq1_id,
	$association_id,
	$segment2_id,
	$fk1_id,
	$region_zone_id,
	$education_id,
	$income_id,
	$cv2_id,
	$cv3_id,
	$subregion_br_group_id,
	$wsa1_id,
	$wsa2_id,
	$wsa3_studyarea_id,
	$sf1_id,
	$eon1_id,
	$eon_customer_id,
	$gn2_id,
	$gn1_id,
	$gn3_id,
	$gn4_id,
	$zs2_id,
	$zs3_id,
	$ess1_id,
	$ess2_id,
	$ori1_id,
	$ovo_influencer_id,
	$ovo_customer_id,
	$bay5_id,
	$ethnicity_ca_id,
	$politics_ca_id,
	$air02_id,
	$air03_id,
	$air04_id,
	$ajax_key
);
echo 'okok';
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('console_dashboard.ajax.php time 99: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if (empty($return))
{
	debug_log('console_dashboard.ajax.php: empty $return from getDashboard()...');
}

echo '*****';
echo '<pre>';
print_r($return);

sql_close();
