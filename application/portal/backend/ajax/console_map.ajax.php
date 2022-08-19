<?php 

/*
	FILE DESCRIPTION:
		PATH: backend/ajax/console_map.ajax.php;
		TYPE: ajax (AJAX responce file);
		PURPOSE: runs function getDashboard in file backend/includes/console_dashboard.fnc.php through an AJAX call from the Dashboard or the Map View selection or parameter changing (files frontend/js/console_dashboard.js and frontend/js/console_map.js) to return the Dashboard or the Map View HTMLs to be inserted into the tool placeholder in the Console page;
		REFERENCED IN: frontend/js/console_dashboard.js, frontend/js/console_map.js;
		FUNCTIONS DECLARED - ;
		STYLES: frontend/css/console_dashboard.css, frontend/css/console_map.css; 
*/  

session_start();
require_once ('../../root.php');
require_once (ROOT.'sync/constants.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'backend/includes/security.fnc.php');
require_once (ROOT.'backend/includes/statistics.fnc.php');
require_once (ROOT.'backend/includes/console_dashboard.fnc.php');
require_once (ROOT.'sync/functions.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'sync/sql_connect.php');

checkAccess(0);

// get parameters
$params = json_decode(file_get_contents('php://input'));

//echo json_encode($params[0]);
//exit;

$return = [];

foreach($params as $param)
{
	list(
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
	) = $param;

	// ajax filter

	if ($type == 'map') { 
		if ($ajax_key >= $_SESSION['map_ajax_last']) {
			$_SESSION['map_ajax_last'] = $ajax_key;
		} else {
			echo 'ajax_false';
			return;
		}
	}

$threads = get_sql_threads(sql_stat()); $max_sql_threads = get_max_sql_threads();
if ($threads<$max_sql_threads) session_write_close();
else debug_log('console_map.ajax.php: session not closed - $threads='.$threads.', $max_sql_threads='.$max_sql_threads, ROOT.'/logs/debug_log.txt');

	$return[] = getDashboard(
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

	
//-	break;
}

echo $return;

sql_close();
