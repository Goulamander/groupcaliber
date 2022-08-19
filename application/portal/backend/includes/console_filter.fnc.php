<?php

/*
	FILE DESCRIPTION:
		PATH: backend/includes/console_filter.fnc.php;
		TYPE: fnc (function declaration);
		PURPOSE: declares functions used to generate the Parameters panel HTML on the Console page and record the client Parameters settings into the database;
		REFERENCED IN: backend/ajax/console_filter.ajax.php (records client settings), backend/includes/console.fnc.php (includes the Parameters HTML);
		FUNCTIONS DECLARED:
			NAME: getFilter;
				PURPOSE: returns the Parameters HTML based on the client settings in table 'clients' and client rights;
				EXECUTED IN: backend/includes/console.fnc.php;
			NAME: setFilter;
				PURPOSE: records the client Parameters settings in table 'clients' whenever the change;
				EXECUTED IN: backend/ajax/console_filter.ajax.php;
				
			NAME: sortFilter;
			NAME: getFilterFieldArray;
			NAME: getFilterFieldList;
			
		STYLES: frontend/css/console_filter.css; 
*/ 


require_once (ROOT.'backend/includes/get_wordcloud_filters.php');

function setFilter($filter_settings) {

	global $con, $site_version;
	
	$filter_data_obj = getValue('client_filter_'.$site_version, 'clients', 'clients_id', $_SESSION['client_id']);
	
	$response = '';
	if (!$filter_data_obj) {
		$filter_settings = 'dummy';
		$response = 'no_filter';
	}

	setValue('client_filter_'.$site_version, $filter_settings, 'clients', 'clients_id', $_SESSION['client_id']);
	
	return $response;

}

function getFilter() {
	
	global $con, $site_version;

	$client_id = $_SESSION['client_id'];
	$is_admin = $_SESSION['client_role']==1;
	
//	$project_id = get_site_project();
	
	// get filter data
	$filter_data_obj  = getValue('client_filter_'.$site_version, 'clients', 'clients_id', $client_id);
	$filter_data_obj = !$filter_data_obj || $filter_data_obj == 'dummy' ? 'null' : $filter_data_obj;	
	
	// get filter and company modal country array
	$filter_country_array_not_sorted = [];
	$_SESSION['filter_country_array'] = []; // filter and modal country list
	$_SESSION['default_country_array'] = [];
	$sql = "SELECT `countries_id`, `country_name` FROM `countries` ORDER BY `country_order`, `country_name`";
	$result = $con->query($sql);	
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			if (in_array($row['countries_id'], $_SESSION['client_country_array'])) {
				$country_name = get_label($row['country_name']); 
				$filter_country_array_not_sorted[] = [$row['countries_id'],$country_name];
				$_SESSION['default_country_array'][] = $row['countries_id'];
			}
		}
	}
	
	// sort the country array by the country name
	$filter_country_array_sortable = [];
	foreach ($filter_country_array_not_sorted as $this_filter_country_array_not_sorted) {
		$filter_country_array_sortable[] = $this_filter_country_array_not_sorted[1]; // get an array of country names to sort by
	}
	$cur_lang_name = getValue('lang_name', 'langs', 'langs_id', get_lang()); // get current language
	setlocale(LC_ALL, strtolower($cur_lang_name).'_'.$cur_lang_name.'.utf8'); // set current language locale
	sort($filter_country_array_sortable, SORT_LOCALE_STRING); // sort the array of country names
	foreach ($filter_country_array_sortable as $this_filter_country_array_sortable) { // sort $_SESSION['filter_country_array'] by $filter_country_array_sortable
		foreach ($filter_country_array_not_sorted as $this_filter_country_array_not_sorted) {
			if ($this_filter_country_array_not_sorted[1] == $this_filter_country_array_sortable) 
			$_SESSION['filter_country_array'][] = $this_filter_country_array_not_sorted;
		}
	}
	
//************ Fields Arrays **************

	$filter_gender_array = getFieldArrayFromTable('gender', '', "", 'genders', false, 'order');
	$filter_age_array = getFieldArrayFromTable('age', '', "", 'ages', false, 'order');
	$filter_country_array = $_SESSION['filter_country_array'];
	$filter_region_type_array = getFieldArrayByCountry('region_type', 'region_types', false, 'name', "", false, false);
	$filter_region_array = getFieldArrayByCountry('region', 'regions', false, 'name', "AND `regions_id` NOT IN ('35','36','37','38')", false, true);
	$filter_education_type_array = getFieldArrayFromTable('education_type', '', "", 'education_types', false, 'order');
	$filter_income_type_array = getFieldArrayFromTable('income_type', '', "", 'income_types', false, 'order');
//	$filter_segment_array = getFieldArrayFromTable('segment', '', "`segment_value` NOT IN ('98','99')", 'segments', true, 'order');
	$filter_segment_array = getFieldArrayFromSubquestions(50,2,$client_id);
	$filter_industry_array = getFieldArrayFromTable('industry', '_short', "`industry_value` NOT IN ('98','99')", 'industries', false, 'order');
	$filter_company_size_array = getFieldArrayFromTable('company_size', '', "`company_size_value` NOT IN ('99')", 'company_sizes', false, 'order');
	$filter_diagnosis_array = getFieldArrayFromSubquestions(435, true);
	$filter_family_array = getFieldArrayFromAnswers('290');
	$filter_children_array = getFieldArrayFromAnswers('291');
	$filter_housing_array = getFieldArrayFromAnswers('292');
	$filter_living_array = getFieldArrayFromAnswers('293');
	$filter_engagement_array = getFieldArrayFromSubquestions(294, true);
//--$filter_engagement_array = getFieldArrayFromTable('engagement', '_short', "", 'engagements', true, 'order');
	$filter_facilities_array = getFieldArrayFromSubquestions(282, true);
	$filter_tg1_array = getFieldArrayFromAnswers('78');
	$filter_tg2_array = getFieldArrayFromAnswers('79');
	$filter_novo1_array = getFieldArrayFromAnswers('92');
	$filter_novo2_array = getFieldArrayFromAnswers('93');
	$filter_demant2_array = getFieldArrayFromAnswers('160');
	$filter_rw1_array = getFieldArrayFromSubquestions(140, true);
	$filter_rw2_array = getFieldArrayFromSubquestions(98, true);
	$filter_gr1_array = getFieldArrayFromSubquestions(425, true);
	$filter_gr2_array = getFieldArrayFromSubquestions(99, true);
	$filter_v_customer_array = getFieldArrayByCountry('v_customer', 'v_customers', false, 'only_name', "", false, false);
	$filter_gol1_array = getFieldArrayFromAnswers('179');
	$filter_gol2_array = getFieldArrayFromAnswers('180');
	$filter_gol3_array = getFieldArrayFromAnswers('181');
	$filter_gol5_array = getFieldArrayFromSubquestions(183, true);
	$filter_veolia1_array = getFieldArrayFromSubquestions(267, true);
//	$filter_touchpoints_array = getFieldArrayFromSubquestions(305, true);	
	$filter_touchpoints_array = getFieldArrayFromSubquestions(305, true, $client_id);	// TODO - Kluge (POR-893_20.723) - must be refactored!
	$filter_employment_array = getFieldArrayFromAnswers('373');
	$filter_race_array = getFieldArrayFromAnswers('393');
	$filter_ethnicity_array = getFieldArrayFromAnswers('394');
	$filter_politics_array = getFieldArrayFromAnswers('395');
	$filter_children_number_array = json_decode(get_label('filter_answers_children_number'), true);
	$filter_tccc_influence_array = json_decode(get_label('filter_answers_tccc_influence'), true);
	$filter_tccc_segment_array = json_decode(get_label('filter_answers_tccc_segment'), true);
	$filter_dmi1_array = getFieldArrayFromSubquestions(443, true);
	$filter_energinet1_array = getFieldArrayFromAnswers('459');
	$filter_stakeholder_type_array = getFieldArrayFromAnswers('461');
	$filter_hovedkategori_array = getFieldArrayFromAnswers('551');
	$filter_siemensq1_array = getFieldArrayFromAnswers('505');
	$filter_association_array = getFieldArrayFromSubquestions(529, true);
	$filter_segment2_array = getFieldArrayFromSubquestions(498, true);
//	$filter_education_type_array = getFieldArrayByCountry('education_type', 'education_types', false, 'order', "", false, false);
//	$filter_income_type_array = getFieldArrayByCountry('income_type', 'income_types', false, 'order', "", false, false);
	$filter_contact_array = getFieldArrayFromAnswers('473');
	$filter_studyarea_array = getFieldArrayFromAnswers('488');
	$filter_siemenss1_array = getFieldArrayFromAnswers('513');
	$filter_siemenss2_array = getFieldArrayFromAnswers('517');
	$filter_chr01_array = getFieldArrayFromAnswers('610');
	$filter_chr02_array = getFieldArrayFromAnswers('611');
	$filter_fk1_array = getFieldArrayFromSubquestions(681, true);
	$filter_region_zone_array = getFieldArrayByCountry('region_zone', 'region_zones', false, 'name', "", false, false);
	$filter_education_array = getFieldArrayByCountry('education', 'educations', false, 'order', "AND `education_value` <> '99'", true, true);
	$filter_income_array = getFieldArrayByCountry('income', 'incomes', false, 'order', "AND `income_value` <> '99'", true, true);
	$filter_cv2_array = getFieldArrayFromSubquestions(701, true, $client_id);
	$filter_cv3_array = getFieldArrayFromSubquestions(771, true);
	$filter_subregion_br_group_array = json_decode(get_label('filter_answers_subregion_br_group'), true);
	$filter_wsa1_array = getFieldArrayFromAnswers('794');
	$filter_wsa2_array = getFieldArrayFromAnswers('795');
	$filter_wsa3_studyarea_array = getFieldArrayFromAnswers('796');
	$filter_sf1_array = getFieldArrayFromSubquestions(806, true);
	$filter_eon1_array = getFieldArrayFromSubquestions(865, true);
	$filter_eon_customer_array = getFieldArrayByCountry('eon_customer', 'eon_customers', false, 'only_name', "", false, false);
	$filter_gn2_array = getFieldArrayFromSubquestions(889, true);
	$filter_gn1_array = json_decode(get_label('filter_answers_gn1'), true);
	$filter_gn3_array = getFieldArrayFromAnswers('897');
	$filter_gn4_array = getFieldArrayFromSubquestions(901, true);
	$filter_zs2_array = getFieldArrayFromSubquestions(925, true);
	$filter_zs3_array = getFieldArrayFromSubquestions(935, true);
	$filter_ess1_array = getFieldArrayFromSubquestions(956, true);
	$filter_ess2_array = getFieldArrayFromSubquestions(957, true);
	$filter_ori1_array = getFieldArrayFromAnswers('975');
	$filter_ovo_influencer_array = json_decode(get_label('filter_answers_ovo_influencer'), true);
	$filter_ovo_customer_array = getFieldArrayFromSubquestions(1002, true);
	$filter_bay5_array = getFieldArrayFromSubquestions(1081, true);
	$filter_ethnicity_ca_array = getFieldArrayFromSubquestions(1136, true);
	$filter_politics_ca_array = getFieldArrayFromAnswers('1127');
	$filter_air02_array = getFieldArrayFromSubquestions(1107, true);
	$filter_air03_array = getFieldArrayFromSubquestions(1113, true);
	$filter_air04_array = getFieldArrayFromSubquestions(1119, true);

	
	$default_gender_array = get_default_values($filter_gender_array);
	$default_age_array = get_default_values($filter_age_array);
	$default_country_array = get_default_values($filter_country_array);
	$default_region_type_array = get_default_values($filter_region_type_array);
	$default_region_array = getFieldArrayByCountry('region', 'regions', true, 'name', "AND `regions_id` NOT IN ('35','36','37','38')", false, true);		
//	$default_region_array[] = '99999';	
	$default_education_type_array = get_default_values($filter_education_type_array);
	$default_income_type_array = get_default_values($filter_income_type_array);
	$default_segment_array = get_default_values($filter_segment_array);
	$default_industry_array = get_default_values($filter_industry_array);
	$default_company_size_array = get_default_values($filter_company_size_array);
	$default_diagnosis_array = get_default_values($filter_diagnosis_array);
	$default_family_array = get_default_values($filter_family_array);
	$default_children_array = get_default_values($filter_children_array);
	$default_housing_array = get_default_values($filter_housing_array);
	$default_living_array = get_default_values($filter_living_array);
	$default_engagement_array = get_default_values($filter_engagement_array);
	$default_facilities_array = get_default_values($filter_facilities_array);
	$default_tg1_array = get_default_values($filter_tg1_array);
	$default_tg2_array = get_default_values($filter_tg2_array);
	$default_novo1_array = get_default_values($filter_novo1_array);
	$default_novo2_array = get_default_values($filter_novo2_array);
	$default_demant2_array = get_default_values($filter_demant2_array);
	$default_rw1_array = get_default_values($filter_rw1_array);
	$default_rw2_array = get_default_values($filter_rw2_array);
	$default_gr1_array = get_default_values($filter_gr1_array);
	$default_gr2_array = get_default_values($filter_gr2_array);
	$default_v_customer_array = getFieldArrayByCountry('v_customer', 'v_customers', true, 'only_name', "", false, false);
	$default_gol1_array = get_default_values($filter_gol1_array);
	$default_gol2_array = get_default_values($filter_gol2_array);
	$default_gol3_array = get_default_values($filter_gol3_array);
	$default_gol5_array = get_default_values($filter_gol5_array);
	$default_veolia1_array = get_default_values($filter_veolia1_array);
	$default_touchpoints_array = get_default_values($filter_touchpoints_array);
	$default_employment_array = get_default_values($filter_employment_array);
	$default_race_array = get_default_values($filter_race_array);
	$default_ethnicity_array = get_default_values($filter_ethnicity_array);
	$default_politics_array = get_default_values($filter_politics_array);
	$default_children_number_array = get_default_values($filter_children_number_array);
	$default_tccc_influence_array = get_default_values($filter_tccc_influence_array);
	$default_tccc_segment_array = get_default_values($filter_tccc_segment_array);
	$default_dmi1_array = get_default_values($filter_dmi1_array);
	$default_energinet1_array = get_default_values($filter_energinet1_array);
	$default_stakeholder_type_array = get_default_values($filter_stakeholder_type_array);
	$default_hovedkategori_array = get_default_values($filter_hovedkategori_array);
	$default_siemensq1_array = get_default_values($filter_siemensq1_array);
	$default_association_array = get_default_values($filter_association_array);
	$default_segment2_array = get_default_values($filter_segment2_array);
	$default_fk1_array = get_default_values($filter_fk1_array);
	$default_region_zone_array = get_default_values($filter_region_zone_array);
	$default_education_array = getFieldArrayByCountry('education', 'educations', true, 'order', "AND `education_value` <> '99'", true, true);
	$default_income_array = getFieldArrayByCountry('income', 'incomes', true, 'order', "AND `income_value` <> '99'", true, true);
	$default_cv2_array = get_default_values($filter_cv2_array);
	$default_cv3_array = get_default_values($filter_cv3_array);
	$default_subregion_br_group_array = get_default_values($filter_subregion_br_group_array);
	$default_wsa1_array = get_default_values($filter_wsa1_array);
	$default_wsa2_array = get_default_values($filter_wsa2_array);
	$default_wsa3_studyarea_array = get_default_values($filter_wsa3_studyarea_array);
	$default_sf1_array = get_default_values($filter_sf1_array);
	$default_eon1_array = get_default_values($filter_eon1_array);
	$default_eon_customer_array = getFieldArrayByCountry('eon_customer', 'eon_customers', true, 'only_name', "", false, false);
	$default_gn2_array = get_default_values($filter_gn2_array);
	$default_gn1_array = get_default_values($filter_gn1_array);
	$default_gn3_array = get_default_values($filter_gn3_array);
	$default_gn4_array = get_default_values($filter_gn4_array);
	$default_zs2_array = get_default_values($filter_zs2_array);
	$default_zs3_array = get_default_values($filter_zs3_array);
	$default_ess1_array = get_default_values($filter_ess1_array);
	$default_ess2_array = get_default_values($filter_ess2_array);
	$default_ori1_array = get_default_values($filter_ori1_array);
	$default_ovo_influencer_array = get_default_values($filter_ovo_influencer_array);
	$default_ovo_customer_array = get_default_values($filter_ovo_customer_array);
	$default_bay5_array = get_default_values($filter_bay5_array);
	$default_ethnicity_ca_array = get_default_values($filter_ethnicity_ca_array);
	$default_politics_ca_array = get_default_values($filter_politics_ca_array);
	$default_air02_array = get_default_values($filter_air02_array);
	$default_air03_array = get_default_values($filter_air03_array);
	$default_air04_array = get_default_values($filter_air04_array);
	
/* -------------- */
	
	$filter_industry_array = sortFilter($filter_industry_array); // may be needed for all filters to be sorted by names

//********** Attributes Groups **********************
	
	// get filter attribute group array
	$client_model = getValue('client_model', 'clients', 'clients_id', $client_id);
	
	$filter_attribute_sql = $is_admin ? " `attribute_group_model` = '$client_model' AND `attribute_group_view` = 0 " : " `attribute_group_model` = '$client_model' AND `attribute_groups_id` <> '23' AND `attribute_group_view` = 0 ";
	
	$sql = "SELECT `attributes_id` FROM `attribute_groups`, `attributes` WHERE `attribute_groups_id` = `attribute_group` AND `attribute_group_model` = '$client_model' AND `attribute_group_core` = '1'";
	$result = $con->query($sql);	
	$client_core_element_id = ''; 
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			$client_core_element_id = $row['attributes_id']; 
		}
	}
	
	$filter_attribute_group_array = [];
	$sql = 
		"SELECT 
			`attribute_groups_id`, 
			`attribute_group_name`, 
			`attribute_group_is_attribute`, 
			`attribute_group_core` 
		FROM 
			`attribute_groups` 
		WHERE
			$filter_attribute_sql
		ORDER BY 
			`attribute_group_order`,
			`attribute_group_name`
		";
	
	$result = $con->query($sql);	
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			$filter_attribute_group_array[] = [$row['attribute_groups_id'],$row['attribute_group_name'],$row['attribute_group_is_attribute'],$row['attribute_group_core']]; 
		}
	}
	
	// get filter attribute array
	$filter_attribute_array = [];
	$sql = "SELECT `attributes_id`, `attribute_name`, `attribute_group`, `attribute_value`, `attribute_class` FROM `".TABLE_ATTRIBUTES."` ORDER BY `attribute_order`, `attribute_name`";
	$result = $con->query($sql);	
	if ($result && $result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			$filter_attribute_array[] = [
				$row['attribute_group'],
				$row['attributes_id'],
				$row['attribute_name'],
				$row['attribute_value'],
				$row['attribute_class']
			]; 
		}
	}
	
	$client_map_dashboard_disabled = getValue('client_map_dashboard_disabled', 'clients', 'clients_id', $client_id);
	$client_map_center = getValue('client_map_center', 'clients', 'clients_id', $client_id);

//************** Filter - Left Panel HTML (Lists) ********************
	
	$list_template = '
	<label for="%1$s">
		<input type="checkbox" id="%1$s" class="input_%2$s" name="%2$s" value="%3$s" %5$s checked>
		<span class="input_title">%4$s</span>
	</label>
	';
/*
	$list_template_v_customer = '
	<label for="%6$s">
		<input type="checkbox" id="%6$s" class="input_%2$s" name="%2$s" value="%3$s" %5$s checked>
		<span class="input_title">%4$s</span>
	</label>
	';
*/	
	
	$filter_company_list = '';
	foreach ($_SESSION['client_company_data_array'] as $this_client_company_data) {
		
		$index_ratings = get_indexes(date('Y-m-d'), true); // get index ratings
		
		$this_index_ratings = $index_ratings->{$this_client_company_data[0]}; // get an index rating list for the current index
		$this_index_ratings_str = implode(',',$this_index_ratings); // stringify the index rating list
		$index_ratings_icon = get_label('index_ratings_icon'); // get index ratings icon for the index ratings button from a label
		$this_index_ratings_button = $is_admin && $this_client_company_data[0] > 999000 ? ' <button class="btn btn-light show_index_ratings" data-index-ratings="'.$this_index_ratings_str.'">'.$index_ratings_icon.'</button>' : ''; // create a button for the index ratings if needed
		
		$filter_company_list .= '
		<label for="company_'.$this_client_company_data[0].'"  '.($this_client_company_data[0] == $this_client_company_data[2] ? 'class="client_default_company"' : "").'>
			<input type="radio" id="company_'.$this_client_company_data[0].'" class="input_company" name="company" value="'.$this_client_company_data[0].'" '.($this_client_company_data[0] == $this_client_company_data[2] ? "checked" : "").'>
			<span class="input_title">'.$this_client_company_data[1].$this_index_ratings_button.'<span class="far fa-minus-square"></span></span>
		</label>
		';
	}

	$benchmark_company_list = '';
	foreach ($_SESSION['client_company_data_array'] as $this_client_company_data) {
		$benchmark_company_list .= '<option value="'.$this_client_company_data[0].'" '.($this_client_company_data[0] == $this_client_company_data[2] ? "selected" : "").'>'.$this_client_company_data[1].'</option>';
	}
	
	$filter_gender_list = getFilterFieldList($filter_gender_array, 'gender', $list_template, false);
	$filter_age_list = getFilterFieldList($filter_age_array, 'age', $list_template, false);
	$filter_country_list = getFilterFieldList($filter_country_array, 'country', $list_template, false);
	$filter_region_type_list = getFilterFieldList($filter_region_type_array, 'regiontype', $list_template, true);
	$filter_region_list = getFilterFieldList($filter_region_array, 'region', $list_template, true);
	$filter_education_type_list = getFilterFieldList($filter_education_type_array, 'educationtype', $list_template, true);
	$filter_income_type_list = getFilterFieldList($filter_income_type_array, 'incometype', $list_template, true);
	$filter_segment_list = getFilterFieldList($filter_segment_array, 'segment', $list_template, false);
	$filter_industry_list = getFilterFieldList($filter_industry_array, 'industry', $list_template, false);
	$filter_company_size_list = getFilterFieldList($filter_company_size_array, 'companysize', $list_template, false);
	$filter_diagnosis_list = getFilterFieldList($filter_diagnosis_array, 'diagnosis', $list_template, false);
	$filter_family_list = getFilterFieldList($filter_family_array, 'family', $list_template, false);
	$filter_children_list = getFilterFieldList($filter_children_array, 'children', $list_template, false);
	$filter_housing_list = getFilterFieldList($filter_housing_array, 'housing', $list_template, false);
	$filter_living_list = getFilterFieldList($filter_living_array, 'living', $list_template, false);
	$filter_engagement_list = getFilterFieldList($filter_engagement_array, 'engagement', $list_template, false);
	$filter_facilities_list = getFilterFieldList($filter_facilities_array, 'facilities', $list_template, false);
	$filter_tg1_list = getFilterFieldList($filter_tg1_array, 'tg1', $list_template, false);
	$filter_tg2_list = getFilterFieldList($filter_tg2_array, 'tg2', $list_template, false);
	$filter_novo1_list = getFilterFieldList($filter_novo1_array, 'novo1', $list_template, false);
	$filter_novo2_list = getFilterFieldList($filter_novo2_array, 'novo2', $list_template, false);
	$filter_demant2_list = getFilterFieldList($filter_demant2_array, 'demant2', $list_template, false);
	$filter_rw1_list = getFilterFieldList($filter_rw1_array, 'rw1', $list_template, false);
	$filter_rw2_list = getFilterFieldList($filter_rw2_array, 'rw2', $list_template, false);
	$filter_gr1_list = getFilterFieldList($filter_gr1_array, 'gr1', $list_template, false);
	$filter_gr2_list = getFilterFieldList($filter_gr2_array, 'gr2', $list_template, false);
//-	$filter_v_customer_list = getFilterFieldList($filter_v_customer_array, 'vcustomer', get_label('list_template_v_customer'), false); 
	$filter_v_customer_list = getFilterFieldList($filter_v_customer_array, 'vcustomer', get_label('list_template_v_customer'), true);
	$filter_gol1_list = getFilterFieldList($filter_gol1_array, 'gol1', $list_template, false);
	$filter_gol2_list = getFilterFieldList($filter_gol2_array, 'gol2', $list_template, false);
	$filter_gol3_list = getFilterFieldList($filter_gol3_array, 'gol3', $list_template, false);
	$filter_gol5_list = getFilterFieldList($filter_gol5_array, 'gol5', $list_template, false);
	$filter_veolia1_list = getFilterFieldList($filter_veolia1_array, 'veolia1', $list_template, false);
	$filter_touchpoints_list = getFilterFieldList($filter_touchpoints_array, 'touchpoints', $list_template, false);
	$filter_employment_list = getFilterFieldList($filter_employment_array, 'employment', $list_template, false);
	$filter_race_list = getFilterFieldList($filter_race_array, 'race', $list_template, false);
	$filter_ethnicity_list = getFilterFieldList($filter_ethnicity_array, 'ethnicity', $list_template, false);
	$filter_politics_list = getFilterFieldList($filter_politics_array, 'politics', $list_template, false);
	$filter_children_number_list = getFilterFieldList($filter_children_number_array, 'childrennumber', $list_template, false);
	$filter_tccc_influence_list = getFilterFieldList($filter_tccc_influence_array, 'tcccinfluence', $list_template, false);
	$filter_tccc_segment_list = getFilterFieldList($filter_tccc_segment_array, 'tcccsegment', $list_template, false);
	$filter_dmi1_list = getFilterFieldList($filter_dmi1_array, 'dmi1', $list_template, false);
	$filter_energinet1_list = getFilterFieldList($filter_energinet1_array, 'energinet1', $list_template, false);
	$filter_stakeholder_type_list = getFilterFieldList($filter_stakeholder_type_array, 'stakeholdertype', $list_template, false);
	$filter_hovedkategori_list = getFilterFieldList($filter_hovedkategori_array, 'hovedkategori', $list_template, false);
	$filter_siemensq1_list = getFilterFieldList($filter_siemensq1_array, 'siemensq1', $list_template, false);
	$filter_association_list = getFilterFieldList($filter_association_array, 'association', $list_template, false);
	$filter_segment2_list = getFilterFieldList($filter_segment2_array, 'segment2', $list_template, false);
	$filter_fk1_list = getFilterFieldList($filter_fk1_array, 'fk1', $list_template, false);
	$filter_region_zone_list = getFilterFieldList($filter_region_zone_array, 'regionzone', $list_template, true);
	$filter_education_list = getFilterFieldList($filter_education_array, 'education', $list_template, true);
	$filter_income_list = getFilterFieldList($filter_income_array, 'income', $list_template, true);
	$filter_cv2_list = getFilterFieldList($filter_cv2_array, 'cv2', $list_template, false);
	$filter_cv3_list = getFilterFieldList($filter_cv3_array, 'cv3', $list_template, false);
	$filter_subregion_br_group_list = getFilterFieldList($filter_subregion_br_group_array, 'subregionbrgroup', $list_template, false);
	$filter_wsa1_list = getFilterFieldList($filter_wsa1_array, 'wsa1', $list_template, false);
	$filter_wsa2_list = getFilterFieldList($filter_wsa2_array, 'wsa2', $list_template, false);
	$filter_wsa3_studyarea_list = getFilterFieldList($filter_wsa3_studyarea_array, 'wsa3studyarea', $list_template, false);
	$filter_sf1_list = getFilterFieldList($filter_sf1_array, 'sf1', $list_template, false);
	$filter_eon1_list = getFilterFieldList($filter_eon1_array, 'eon1', $list_template, false);
	$filter_eon_customer_list = getFilterFieldList($filter_eon_customer_array, 'eoncustomer', get_label('list_template_eon_customer'), true);
	$filter_gn2_list = getFilterFieldList($filter_gn2_array, 'gn2', $list_template, false);
	$filter_gn1_list = getFilterFieldList($filter_gn1_array, 'gn1', $list_template, false);
	$filter_gn3_list = getFilterFieldList($filter_gn3_array, 'gn3', $list_template, false);
	$filter_gn4_list = getFilterFieldList($filter_gn4_array, 'gn4', $list_template, false);
	$filter_zs2_list = getFilterFieldList($filter_zs2_array, 'zs2', $list_template, false);
	$filter_zs3_list = getFilterFieldList($filter_zs3_array, 'zs3', $list_template, false);
	$filter_ess1_list = getFilterFieldList($filter_ess1_array, 'ess1', $list_template, false);
	$filter_ess2_list = getFilterFieldList($filter_ess2_array, 'ess2', $list_template, false);
	$filter_ori1_list = getFilterFieldList($filter_ori1_array, 'ori1', $list_template, false);
	$filter_ovo_influencer_list = getFilterFieldList($filter_ovo_influencer_array, 'ovoinfluencer', $list_template, false);
	$filter_ovo_customer_list = getFilterFieldList($filter_ovo_customer_array, 'ovocustomer', $list_template, false);
	$filter_bay5_list = getFilterFieldList($filter_bay5_array, 'bay5', $list_template, false);
	$filter_ethnicity_ca_list = getFilterFieldList($filter_ethnicity_ca_array, 'ethnicityca', $list_template, false);
	$filter_politics_ca_list = getFilterFieldList($filter_politics_ca_array, 'politicsca', $list_template, false);
	$filter_air02_list = getFilterFieldList($filter_air02_array, 'air02', $list_template, false);
	$filter_air03_list = getFilterFieldList($filter_air03_array, 'air03', $list_template, false);
	$filter_air04_list = getFilterFieldList($filter_air04_array, 'air04', $list_template, false);

//************** Attributes Lists ********************
	
	$filter_attribute_list = '';
	$filter_attribute_core = '';
	foreach ($filter_attribute_group_array as $this_filter_attribute_group) {
		
		$this_filter_attribute_group_name_label_text = get_label(str_replace('}}','',str_replace('{{','',$this_filter_attribute_group[1])),2);
		
		$this_filter_attribute_group_name_label_text = $this_filter_attribute_group_name_label_text ? $this_filter_attribute_group_name_label_text : str_replace('}}','',str_replace('{{','',$this_filter_attribute_group[1]));
		
		$this_filter_attribute_group_name_id = strtolower(
			str_replace(' ','',
				str_replace('&','',
					str_replace('_','',
						$this_filter_attribute_group_name_label_text)))
		);
		
		$filter_attribute_list .= $this_filter_attribute_group[2] == 0 && $this_filter_attribute_group[3] != 1  ? '
		<p class="attribute_group_title">'.$this_filter_attribute_group[1].'<span class="fas fa-angle-right"></span></p>

		<div class="input_group auto_toggle">

			<label id="attribute_label_'.$this_filter_attribute_group[0].'_group" for="attribute_'.$this_filter_attribute_group[0].'_group">
				<input type="checkbox" id="attribute_'.$this_filter_attribute_group[0].'_group" class="input_'.$this_filter_attribute_group_name_id.' group" name="'.$this_filter_attribute_group[0].'" value="group">
				<span class="input_title">- Group -</span>
			</label>

			<label for="attribute_'.$this_filter_attribute_group[0].'_all">
				<input type="checkbox" id="attribute_'.$this_filter_attribute_group[0].'_all" class="input_'.$this_filter_attribute_group_name_id.' check_all" name="'.$this_filter_attribute_group[0].'" value="all">
				<span class="input_title">- Select all -</span>
			</label>
			
		' : '';
	
		foreach ($filter_attribute_array as $this_filter_attribute) {
			
			$input_value = $this_filter_attribute[1];  // if core element 

			$input_class_id = $this_filter_attribute[3] == 'trust_affection' ? 'trustaffection' : $this_filter_attribute_group_name_id; // if core element  
			
			if (!empty($this_filter_attribute[4])) { $input_class_id = $this_filter_attribute[4]; }
			
			$input_icon = $this_filter_attribute_group[3] != 1 ? '' : '<span>{{filter_trust_like_icon}}</span>'; // if core element
			
			$attribute_checked = $this_filter_attribute_group[3] != 1 ? '' : 'checked'; // if core element  
			
			$filter_attribute = '
			<label id="attribute_label_'.$this_filter_attribute[1].'" for="attribute_'.$this_filter_attribute[1].'">
				'.$input_icon.'
				<input type="checkbox" id="attribute_'.$this_filter_attribute[1].'" class="input_'.$input_class_id.'" name="'.$this_filter_attribute[1].'" value="'.$input_value.'" '.$attribute_checked.'>
				<span class="input_title">'.$this_filter_attribute[2].'</span>
			</label>
			';
			
			if ($this_filter_attribute_group[0] == $this_filter_attribute[0]) {	
				if ( $this_filter_attribute_group[3] != 1 ) { // if not core element
					$filter_attribute_list .= $filter_attribute;
				} else { // if core element
					$filter_attribute_core = $filter_attribute;
				}	
			}
			
		}

		$filter_attribute_list .= $this_filter_attribute_group[2] == 0 ? '</div>' : '';

	}
	
//************* Clients Types ***********************
	
	require_once (ROOT.'backend/includes/clients.php');
	
//************* Show Fields ***********************
	
	require_once (ROOT.'backend/includes/show_filters.inc.php');

	
//************ Filter Titles ************************
	
	$label_suffix = '_filter_title_'.($is_admin ? 'admin' : 'user');
	
	$region_type_filter_title = get_label('region_type'.$label_suffix);
	$education_type_filter_title = get_label('education_type'.$label_suffix);
	$income_type_filter_title = get_label('income_type'.$label_suffix);
	$segment_filter_title = get_label('segment'.$label_suffix);
	$industry_filter_title = get_label('industry'.$label_suffix);
	$company_size_filter_title = get_label('company_size'.$label_suffix);
	$diagnosis_filter_title = get_label('diagnosis'.$label_suffix);
	$family_filter_title = get_label('family'.$label_suffix);
	$children_filter_title = get_label('children'.$label_suffix);
	$housing_filter_title = get_label('housing'.$label_suffix);
	$living_filter_title = get_label('living'.$label_suffix);
	$engagement_filter_title = get_label('engagement'.$label_suffix);
	$facilities_filter_title = get_label('facilities'.$label_suffix);
	$tg1_filter_title = get_label('tg1'.$label_suffix);
	$tg2_filter_title = get_label('tg2'.$label_suffix);
	$novo1_filter_title = get_label('novo1'.$label_suffix);
	$novo2_filter_title = get_label('novo2'.$label_suffix);
	$demant2_filter_title = get_label('demant2'.$label_suffix);
	$rw1_filter_title = get_label('rw1'.$label_suffix);
	$rw2_filter_title = get_label('rw2'.$label_suffix);
	$gr1_filter_title = get_label('gr1'.$label_suffix);
	$gr2_filter_title = get_label('gr2'.$label_suffix);
	$v_customer_filter_title = get_label('v_customer'.$label_suffix);
	$gol1_filter_title = get_label('gol1'.$label_suffix);
	$gol2_filter_title = get_label('gol2'.$label_suffix);
	$gol3_filter_title = get_label('gol3'.$label_suffix);
	$gol5_filter_title = get_label('gol5'.$label_suffix);
	$veolia1_filter_title = get_label('veolia1'.$label_suffix);
	$touchpoints_filter_title = get_label('touchpoints'.$label_suffix);
	$employment_filter_title = get_label('employment'.$label_suffix);
	$race_filter_title = get_label('race'.$label_suffix);
	$ethnicity_filter_title = get_label('ethnicity'.$label_suffix);
	$politics_filter_title = get_label('politics'.$label_suffix);
	$children_number_filter_title = get_label('children_number'.$label_suffix);
	$tccc_influence_filter_title = get_label('tccc_influence'.$label_suffix);
	$tccc_segment_filter_title = get_label('tccc_segment'.$label_suffix);
	$dmi1_filter_title = get_label('dmi1'.$label_suffix);
	$energinet1_filter_title = get_label('energinet1'.$label_suffix);
	$stakeholder_type_filter_title = get_label('stakeholder_type'.$label_suffix);
	$hovedkategori_filter_title = get_label('hovedkategori'.$label_suffix);
	$siemensq1_filter_title = get_label('siemensq1'.$label_suffix);
	$association_filter_title = get_label('association'.$label_suffix);
	$segment2_filter_title = get_label('segment2'.$label_suffix);
	$fk1_filter_title = get_label('fk1'.$label_suffix);
	$region_zone_filter_title = get_label('region_zone'.$label_suffix);
	$education_filter_title = get_label('education'.$label_suffix);
	$income_filter_title = get_label('income'.$label_suffix);
	$cv2_filter_title = get_label('cv2'.$label_suffix);
	$cv3_filter_title = get_label('cv3'.$label_suffix);
	$subregion_br_group_filter_title = get_label('subregion_br_group'.$label_suffix);
	$wsa1_filter_title = get_label('wsa1'.$label_suffix);
	$wsa2_filter_title = get_label('wsa2'.$label_suffix);
	$wsa3_studyarea_filter_title = get_label('wsa3_studyarea'.$label_suffix);
	$sf1_filter_title = get_label('sf1'.$label_suffix);
	$eon1_filter_title = get_label('eon1'.$label_suffix);
	$eon_customer_filter_title = get_label('eon_customer'.$label_suffix);
	$gn2_filter_title = get_label('gn2'.$label_suffix);
	$gn1_filter_title = get_label('gn1'.$label_suffix);
	$gn3_filter_title = get_label('gn3'.$label_suffix);
	$gn4_filter_title = get_label('gn4'.$label_suffix);
	$zs2_filter_title = get_label('zs2'.$label_suffix);
	$zs3_filter_title = get_label('zs3'.$label_suffix);
	$ess1_filter_title = get_label('ess1'.$label_suffix);
	$ess2_filter_title = get_label('ess2'.$label_suffix);
	$ori1_filter_title = get_label('ori1'.$label_suffix);
	$ovo_influencer_filter_title = get_label('ovo_influencer'.$label_suffix);
	$ovo_customer_filter_title = get_label('ovo_customer'.$label_suffix);
	$bay5_filter_title = get_label('bay5'.$label_suffix);
	$ethnicity_ca_filter_title = get_label('ethnicity_ca'.$label_suffix);
	$politics_ca_filter_title = get_label('politics_ca'.$label_suffix);
	$air02_filter_title = get_label('air02'.$label_suffix);
	$air03_filter_title = get_label('air03'.$label_suffix);
	$air04_filter_title = get_label('air04'.$label_suffix);

	
//*************** HTML tool_navigation *******************	
	
	$html = '<div class="tool_navigation">';
	
	$html .= '
	<div class="filter_toggler_cross">
		<span class="fas fa-times"></span>
	</div>
	';
	
	$html .= '
	<span class="report_filters">
	';
	
	$client_customer = getValue('client_customer', 'clients', 'clients_id', $client_id);	// getting customer id for current client
	$customer_sql_filter = $_SESSION['client_role'] == 1 ? " " : " WHERE `customers_id` = '$client_customer' ";
	
	$customer_list = '';
	$sql = "SELECT `customers_id`, `customer_name` FROM `customers` $customer_sql_filter ORDER BY `customer_name`";
	$result = $con->query($sql);	
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			$customer_list .= '<option value="'.$row['customers_id'].'">'.$row['customer_name'].'</option>'; 
		}
	}
	
	$html .= '
	<p class="customer_title"> {{customer_filter_title}}</p>
	<div class="customer">
		<label for="customer">
			<select id="customer" class="input_customer">
				'.$customer_list.'
			</select>
		</label>
	</div>
	';
	
	$report_list = '';
	$sql = "SELECT `reports_id`, `report_title`, `report_customer` FROM `reports` WHERE `report_hidden` = 0 ORDER BY `report_order`";
	$result = $con->query($sql);	
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			$report_list .= '<option value="'.$row['reports_id'].'" data-customer="'.$row['report_customer'].'">'.$row['report_title'].'</option>'; 
		}
	}

	$html .= '
	<p class="report_title"> {{report_filter_title}}</p>
	<div class="report">
		<label for="report">
			<select id="report" class="input_report">
				'.$report_list.'
			</select>
		</label>
	</div>
	';

	$html .= '
	<p class="year_title"> {{period_filter_title}}</p>
	<div class="period_wrap">
	<div class="year">
		<label for="year">
			<select id="year" class="input_year">
				{{year_filter_options}}
			</select>
		</label>
	</div>
	';
	
	$html .= '
	<div class="quarter">
		<label for="quarter">
			<select id="quarter" class="input_quarter">
				{{quarter_filter_options}}
			</select>
		</label>
	</div>
	</div>
	
	';
	
	$html .= '
	<div class="divider_slides"></div>
	';
	
	$slide_list = '';
	$sql = "SELECT `slides_id`, `slide_title`, `slide_report`, `slide_preview`, `slide_settings` FROM `slides` WHERE `slide_hidden` = 0 ORDER BY `slide_order`";
	$result = $con->query($sql);	
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			
			$slide_settings = translate($row['slide_settings']);
			$slide_settings = json_decode($slide_settings, true);
			$lock_period = !isset($slide_settings['lock_period']) || $slide_settings['lock_period'] == 0 ? 0 : 1;
			$hide_year = !isset($slide_settings['hide_year']) || $slide_settings['hide_year'] == 0 ? 0 : 1;
			$hide_slide = !isset($slide_settings['hide_slide']) || $slide_settings['hide_slide'] == 0 ? 0 : 1;
			$diff_threshold = !isset($slide_settings['diff_threshold']) || !$slide_settings['diff_threshold'] ? '' : $slide_settings['diff_threshold'];
			
			$slide_list .= '
				<p class="slide_title"> '.$row['slide_title'].'</p>
				<div class="slide">
					<label for="slide_'.$row['slides_id'].'">
						<input type="radio" id="slide_'.$row['slides_id'].'" class="input_slide" data-report="'.$row['slide_report'].'" data-lock-period="'.$lock_period.'" data-hide-year="'.$hide_year.'" data-hide-slide="'.$hide_slide.'" data-diff-threshold="'.$diff_threshold.'" name="slide">
						<img src="images/slides/'.$row['slide_preview'].'">
					</label>
				</div>
			'; 
		}
	}
	
	$html .= '
	<div class="slide_wrap">
		'.$slide_list.'
	</div>
	'; 
	
	
	$html .= '
	</span>
	'; // end of report filters
	
	$html .= '
	<span class="non_report_filters">
	';
	
	$html .= '
		<p class="filter_title"><span>{{filter_shortcuts_icon}}</span> {{filter_shortcuts}}</p>
		<div id="reset_filter" class="off">
			<div id="reset_filter_button"><span class="far fa-circle"></span></div>
			<div id="reset_filter_text">{{filter_shortcuts_default}}</div>
		</div>'
	;
	
	$html .= // rw1 filter shortcut
		(/*$is_admin || $rw_clients*/
		$show_rw1 ? '
		<div id="rw1_target" class="off">
			<div id="rw1_target_button"><span class="far fa-circle"></span></div>
			<div id="rw1_target_text">{{filter_shortcuts_target}}</div>
		</div>' : '')
	;

	$html .= // tccc influence filter shortcut
		($show_tccc_influence ? '
		<div id="tccc_influence" class="off">
			<div id="tccc_influence_button"><span class="far fa-circle"></span></div>
			<div id="tccc_influence_text">{{influential_consumers}}</div>
		</div>' : '')
	;
	
	$html .= // energinet stakeholder type filter shortcut
	($show_stakeholder_type ? '
	<div id="stakeholder_type_1" class="off">
		<div id="stakeholder_type_1_button"><span class="far fa-circle"></span></div>
		<div id="stakeholder_type_1_text">{{STAKEHOLDER_TYPE_01}}</div>
	</div>
	<div id="stakeholder_type_2" class="off">
		<div id="stakeholder_type_2_button"><span class="far fa-circle"></span></div>
		<div id="stakeholder_type_2_text">{{STAKEHOLDER_TYPE_02}}</div>
	</div>
	' : '')
	;
	
	
	// period
	$html .= '
	test 
	test
	<p class="period_title"><span>{{filter_period_icon}}</span> {{filter_period}}</p>
	<div class="period">
		<input type="text" id="period" class="input_period" readonly>
	</div>
	';
	
// ***************** HTML columns ***********************
	
	$html .= '
	<p id="column_title" class="column_title"><span>{{filter_column_icon}}</span> {{filter_column}}</p>
	<div class="column">
		<label for="column">
			<select id="column" class="input_column">
				<option value="none">{{filter_column_none}}</option>
				<option value="country">{{filter_country}}</option>
				<option value="age">{{filter_age}}</option>
				<option value="gender">{{filter_gender}}</option>
				<option value="region">{{filter_region}}</option>'.
				
				($show_region_type ? '<option value="regiontype">'.$region_type_filter_title.'</option>' : '').
				($show_subregion_br_group ? '<option value="subregionbrgroup">'.$subregion_br_group_filter_title.'</option>' : '').
				($show_education_type ? '<option value="educationtype">'.$education_type_filter_title.'</option>' : '').					
				($show_income_type ? '<option value="incometype">'.$income_type_filter_title.'</option>' : '').				
				($show_segment ? '<option value="segment">'.$segment_filter_title.'</option>' : '').
				($show_industry ? '<option value="industry">'.$industry_filter_title.'</option>' : '').
				($show_company_size ? '<option value="companysize">'.$company_size_filter_title.'</option>' : '').
				($show_diagnosis ? '<option value="diagnosis">'.$diagnosis_filter_title.'</option>' : '').
				($show_family ? '<option value="family">'.$family_filter_title.'</option>' : '').
				($show_children ? '<option value="children">'.$children_filter_title.'</option>' : '').
				($show_housing ? '<option value="housing">'.$housing_filter_title.'</option>' : '').
				($show_living ? '<option value="living">'.$living_filter_title.'</option>' : '').
				($show_engagement ? '<option value="engagement">'.$engagement_filter_title.'</option>' : '').
				($show_facilities ? '<option value="facilities">'.$facilities_filter_title.'</option>' : '').
				($show_tg1 ? '<option value="tg1">'.$tg1_filter_title.'</option>' : '').
				($show_tg2 ? '<option value="tg2">'.$tg2_filter_title.'</option>' : '').
				($show_novo1 ? '<option value="novo1">'.$novo1_filter_title.'</option>' : '').
				($show_novo2 ? '<option value="novo2">'.$novo2_filter_title.'</option>' : '').
				($show_demant2 ? '<option value="demant2">'.$demant2_filter_title.'</option>' : '').
				($show_rw1 ? '<option value="rw1">'.$rw1_filter_title.'</option>' : '').
				($show_rw2 ? '<option value="rw2">'.$rw2_filter_title.'</option>' : '').
				($show_gr1 ? '<option value="gr1">'.$gr1_filter_title.'</option>' : '').
				($show_gr2 ? '<option value="gr2">'.$gr2_filter_title.'</option>' : '').
				($show_v_customer ? '<option value="vcustomer">'.$v_customer_filter_title.'</option>' : '').
				($show_gol1 ? '<option value="gol1">'.$gol1_filter_title.'</option>' : '').
				($show_gol2 ? '<option value="gol2">'.$gol2_filter_title.'</option>' : '').
				($show_gol3 ? '<option value="gol3">'.$gol3_filter_title.'</option>' : '').
				($show_gol5 ? '<option value="gol5">'.$gol5_filter_title.'</option>' : '').
				($show_veolia1 ? '<option value="veolia1">'.$veolia1_filter_title.'</option>' : '').
				($show_touchpoints ? '<option value="touchpoints">'.$touchpoints_filter_title.'</option>' : '').
				($show_employment ? '<option value="employment">'.$employment_filter_title.'</option>' : '').
				($show_race ? '<option value="race">'.$race_filter_title.'</option>' : '').
				($show_ethnicity ? '<option value="ethnicity">'.$ethnicity_filter_title.'</option>' : '').
				($show_politics ? '<option value="politics">'.$politics_filter_title.'</option>' : '').
				($show_children_number ? '<option value="childrennumber">'.$children_number_filter_title.'</option>' : '').
				($show_tccc_influence ? '<option value="tcccinfluence">'.$tccc_influence_filter_title.'</option>' : '').
				($show_tccc_segment ? '<option value="tcccsegment">'.$tccc_segment_filter_title.'</option>' : '').
				($show_dmi1 ? '<option value="dmi1">'.$dmi1_filter_title.'</option>' : '').
				($show_energinet1 ? '<option value="energinet1">'.$energinet1_filter_title.'</option>' : '').
				($show_stakeholder_type ? '<option value="stakeholdertype">'.$stakeholder_type_filter_title.'</option>' : '').
				($show_hovedkategori ? '<option value="hovedkategori">'.$hovedkategori_filter_title.'</option>' : '').
				($show_siemensq1 ? '<option value="siemensq1">'.$siemensq1_filter_title.'</option>' : '').
				($show_association ? '<option value="association">'.$association_filter_title.'</option>' : '').
				($show_segment2 ? '<option value="segment2">'.$segment2_filter_title.'</option>' : '').
				($show_fk1 ? '<option value="fk1">'.$fk1_filter_title.'</option>' : '').
				($show_region_zone ? '<option value="regionzone">'.$region_zone_filter_title.'</option>' : '').
				($show_education ? '<option value="education">'.$education_filter_title.'</option>' : '').	
				($show_income ? '<option value="income">'.$income_filter_title.'</option>' : '').
				($show_cv2 ? '<option value="cv2">'.$cv2_filter_title.'</option>' : '').
				($show_cv3 ? '<option value="cv3">'.$cv3_filter_title.'</option>' : '').
				($show_wsa1 ? '<option value="wsa1">'.$wsa1_filter_title.'</option>' : '').
				($show_wsa2 ? '<option value="wsa2">'.$wsa2_filter_title.'</option>' : '').
				($show_wsa3_studyarea ? '<option value="wsa3studyarea">'.$wsa3_studyarea_filter_title.'</option>' : '').
				($show_sf1 ? '<option value="sf1">'.$sf1_filter_title.'</option>' : '').
				($show_eon1 ? '<option value="eon1">'.$eon1_filter_title.'</option>' : '').
				($show_eon_customer ? '<option value="eoncustomer">'.$eon_customer_filter_title.'</option>' : '').
				($show_gn2 ? '<option value="gn2">'.$gn2_filter_title.'</option>' : '').
				($show_gn1 ? '<option value="gn1">'.$gn1_filter_title.'</option>' : '').
				($show_gn3 ? '<option value="gn3">'.$gn3_filter_title.'</option>' : '').
				($show_gn4 ? '<option value="gn4">'.$gn4_filter_title.'</option>' : '').
				($show_zs2 ? '<option value="zs2">'.$zs2_filter_title.'</option>' : '').
				($show_zs3 ? '<option value="zs3">'.$zs3_filter_title.'</option>' : '').	
				($show_ess1 ? '<option value="ess1">'.$ess1_filter_title.'</option>' : '').
				($show_ess2 ? '<option value="ess2">'.$ess2_filter_title.'</option>' : '').
				($show_ori1 ? '<option value="ori1">'.$ori1_filter_title.'</option>' : '').
				($show_ovo_influencer ? '<option value="ovoinfluencer">'.$ovo_influencer_filter_title.'</option>' : '').
				($show_ovo_customer ? '<option value="ovocustomer">'.$ovo_customer_filter_title.'</option>' : '').
				($show_bay5 ? '<option value="bay5">'.$bay5_filter_title.'</option>' : '').
				($show_ethnicity_ca ? '<option value="ethnicityca">'.$ethnicity_ca_filter_title.'</option>' : '').
				($show_politics_ca ? '<option value="politicsca">'.$politics_ca_filter_title.'</option>' : '').
				($show_air02 ? '<option value="air02">'.$air02_filter_title.'</option>' : '').
				($show_air03 ? '<option value="air03">'.$air03_filter_title.'</option>' : '').
				($show_air04 ? '<option value="air04">'.$air04_filter_title.'</option>' : '').
				'
			</select>
		</label>
	</div>
	';
	
	// intervals
	$html .= '
	<p class="interval_title"><span>{{filter_interval_icon}}</span> {{filter_interval}}</p>
	<div id="interval_checkbox" class="interval_checkbox">

		<label for="interval_week">
			<input type="radio" id="interval_week" class="input_interval" name="interval" value="week">
			<span class="input_title">{{filter_interval_week}}</span>
		</label>

		<label for="interval_month">
			<input type="radio" id="interval_month" class="input_interval" name="interval" value="month"  checked>
			<span class="input_title">{{filter_interval_month}}</span>
		</label>

		<label for="interval_quarter">
			<input type="radio" id="interval_quarter" class="input_interval" name="interval" value="quarter">
			<span class="input_title">{{filter_interval_quarter}}</span>
		</label>

		<label for="interval_year">
			<input type="radio" id="interval_year" class="input_interval" name="interval" value="year">
			<span class="input_title">{{filter_interval_year}}</span>
		</label>

	</div>
	';

	// events
	$html .= ($show_addons? '' : '
	<p class="addons_title"><span class="fas fa-flag"></span> {{filter_addons}}</p>
	<div class="addons_checkbox">

		<div class="addon_input_wrap">
			<label for="addons_event">
				<input type="checkbox" id="addons_event" class="input_addons" value="event">
				<span class="input_title">{{filter_addons_events}}</span>
			</label>
			<span class="far fa-plus-square"></span>
		</div>
		
		<div class="addon_input_wrap">
			<label for="addons_campaign">
				<input type="checkbox" id="addons_campaign" class="input_addons" value="campaign">
				<span class="input_title">{{filter_addons_campaigns}}</span>
			</label>
			<span class="far fa-plus-square"></span>
		</div>

	</div>
	');

	// companies
	$html .= '
	<p id="company_title" class="company_title">
		<!--span class="fas fa-briefcase"></span-->
		{{filter_company_icon}} {{filter_company}} <span class="far fa-plus-square"></span>
	</p>
	<div class="company_checkbox">
		<label for="company_group">
			<input type="checkbox" id="company_group" class="input_company group" name="company0" value="group">
			<span id="company_group_input_title" class="input_title">- Group -</span>
		</label>

		<label for="company_all">
			<input type="checkbox" id="company_all" class="input_company check_all" name="company0" value="all">
			<span class="input_title">- Select all -</span>
		</label>
	';

	$html .= $filter_company_list;
	
	$html .= '
	<p id="no_company_msg">no companies listed...<p>
	</div>
	';

	// compare companies
	$html .= '
	<div class="company_compare">
		<label for="company_1">
			<p>{{filter_company_compare}}</p>
			<select id="company_1" class="input_companycompare">
				'.$benchmark_company_list.'
			</select>
		</label>

		<label for="company_2">
			<p>{{filter_company_vs}}</p>
			<select id="company_2" class="input_companycompare">
				'.$benchmark_company_list.'
			</select>
		</label>
	</div>
	';
	
	// attributes
	$html .= '
	<div class="attribute_checkbox group_element">
		
		'.$filter_attribute_core.'

	</div>
	';
	
	$html .= '
	<p id="attribute_title" class="attribute_title"><span>{{filter_element_icon}}</span> {{filter_element}}</p>
	<div id="attribute_checkbox" class="attribute_checkbox">
	
		'.$filter_attribute_list.'
	
	</div>
	';
	
	// wordcloud filters
	
	$wordcloud_filters_list = get_wordcloud_filters($client_customer);
	
	$html .= '
		<p class="attribute_title question"><span>{{filter_question_icon}}</span> {{filter_question}}</p>
		<div class="attribute_checkbox question">
			
			'.$wordcloud_filters_list.'
			
		</div>	
	';

	// filters
	$html .= '<p id="filter_title" class="filter_title"><span>{{filter_filter_icon}}</span> {{filter_filter}}</p>';
	
	$html .= '
	<div id="filter_checkbox" class="filter_checkbox">
	';
	
//************** HTML Fields Groups ************
	
	$html_template = 
		//	1 = country
		//	2 = $filter_country_list
		//	3 = list
	'
		<p id="filter_group_title_%1$s" class="filter_group_title">%2$s<span class="fas fa-angle-right"></span></p>

		<div class="input_group auto_toggle">

			<label for="%1$s_group">
				<input type="checkbox" id="%1$s_group" class="input_%1$s group" name="%1$s" value="group" checked>
				<span id="%1$s_group_input_title" class="input_title">- {{filter_ungroup}} -</span>
			</label>

			<label for="%1$s_all">
				<input type="checkbox" id="%1$s_all" class="input_%1$s check_all" name="%1$s" value="all" checked>
				<span class="input_title">- {{filter_deselect_all}} -</span>
			</label>

			%3$s

		</div>
	';
	
	$all_countries_id_list = 
		"1,2,3,4,5,6,7,8,9,10,".
		"11,12,13,14,15,16,17,18,19,20,".
		"21,22,23,24,25,26,27,28,29,30,".
		"31,32,33,34,35,36,37,38,39,40,".
		"41,42,43,44,45,46,47,48,49,50,".
		"51,52,53,54,55,56,57,58,59,60,".
		"61,62,63,64,65,66,67,68,69,70,".
		"71,72,73,74,75,76,77,78,79,80,".
		"81,82,83,84,85,86,87,88,89,90,".
		"91,92,93,94,95,96,97,98,99,100"
	;

	
	$template_9999 = get_label('template_9999');
/*-	'
			<label for="%1$s_9999">
				<input type="checkbox" id="%1$s_9999" class="input_%1$s" name="%1$s" value="9999" data-%1$s_country="%3$s" checked>
				<span class="input_title">%2$s</span>
			</label>
	';	
*/
	
	$education_9999 = sprintf($template_9999, 'education', '{{EDU_NOT_SURE}}', $all_countries_id_list);
	$income_9999 = sprintf($template_9999, 'income', '{{INCOME_NOT_SURE}}', $all_countries_id_list);

	$html .= sprintf($html_template, 'country', '{{filter_country}}', $filter_country_list);
	$html .= sprintf($html_template, 'age', '{{filter_age}}', $filter_age_list);
	$html .= sprintf($html_template, 'gender', '{{filter_gender}}', $filter_gender_list);
	$html .= $show_region_zone ? sprintf($html_template, 'regionzone', $region_zone_filter_title, $filter_region_zone_list) : '';
	$html .= $show_region_type ? sprintf($html_template, 'regiontype', $region_type_filter_title, $filter_region_type_list) : '';
	$html .= sprintf($html_template, 'region', '{{filter_region}}', $filter_region_list);
	$html .= $show_subregion_br_group ? sprintf($html_template, 'subregionbrgroup', $subregion_br_group_filter_title, $filter_subregion_br_group_list) : '';
	$html .= $show_education_type ? sprintf($html_template, 'educationtype', $education_type_filter_title, $filter_education_type_list) : '';
	$html .= $show_education ? sprintf($html_template, 'education', $education_filter_title, $filter_education_list.$education_9999) : '';
	$html .= $show_income_type ? sprintf($html_template, 'incometype', $income_type_filter_title, $filter_income_type_list) : '';	
	$html .= $show_income ? sprintf($html_template, 'income', $income_filter_title, $filter_income_list.$income_9999) : '';
	$html .= $show_segment ? sprintf($html_template, 'segment', $segment_filter_title, $filter_segment_list) : '';
	$html .= $show_industry ? sprintf($html_template, 'industry', $industry_filter_title, $filter_industry_list) : '';
	$html .= $show_company_size ? sprintf($html_template, 'companysize', $company_size_filter_title, $filter_company_size_list) : '';
	$html .= $show_diagnosis ? sprintf($html_template, 'diagnosis', $diagnosis_filter_title, $filter_diagnosis_list) : '';
	$html .= $show_family ? sprintf($html_template, 'family', $family_filter_title, $filter_family_list) : '';
	$html .= $show_children ? sprintf($html_template, 'children', $children_filter_title, $filter_children_list) : '';
	$html .= $show_housing ? sprintf($html_template, 'housing', $housing_filter_title, $filter_housing_list) : '';
	$html .= $show_living ? sprintf($html_template, 'living', $living_filter_title, $filter_living_list) : '';
	$html .= $show_engagement ? sprintf($html_template, 'engagement', $engagement_filter_title, $filter_engagement_list) : '';
	$html .= $show_facilities ? sprintf($html_template, 'facilities', $facilities_filter_title, $filter_facilities_list) : '';
	$html .= $show_tg1 ? sprintf($html_template, 'tg1', $tg1_filter_title, $filter_tg1_list) : '';
	$html .= $show_tg2 ? sprintf($html_template, 'tg2', $tg2_filter_title, $filter_tg2_list) : '';
	$html .= $show_novo1 ? sprintf($html_template, 'novo1', $novo1_filter_title, $filter_novo1_list) : '';
	$html .= $show_novo2 ? sprintf($html_template, 'novo2', $novo2_filter_title, $filter_novo2_list) : '';
	$html .= $show_demant2 ? sprintf($html_template, 'demant2', $demant2_filter_title, $filter_demant2_list) : '';
	$html .= $show_rw1 ? sprintf($html_template, 'rw1', $rw1_filter_title, $filter_rw1_list) : '';
	$html .= $show_rw2 ? sprintf($html_template, 'rw2', $rw2_filter_title, $filter_rw2_list) : '';
	$html .= $show_gr1 ? sprintf($html_template, 'gr1', $gr1_filter_title, $filter_gr1_list) : '';
	$html .= $show_gr2 ? sprintf($html_template, 'gr2', $gr2_filter_title, $filter_gr2_list) : '';
	$html .= $show_v_customer ? sprintf($html_template, 'vcustomer', $v_customer_filter_title, $filter_v_customer_list) : '';
	$html .= $show_gol1 ? sprintf($html_template, 'gol1', $gol1_filter_title, $filter_gol1_list) : '';
	$html .= $show_gol2 ? sprintf($html_template, 'gol2', $gol2_filter_title, $filter_gol2_list) : '';
	$html .= $show_gol3 ? sprintf($html_template, 'gol3', $gol3_filter_title, $filter_gol3_list) : '';
	$html .= $show_gol5 ? sprintf($html_template, 'gol5', $gol5_filter_title, $filter_gol5_list) : '';
	$html .= $show_veolia1 ? sprintf($html_template, 'veolia1', $veolia1_filter_title, $filter_veolia1_list) : '';
	$html .= $show_touchpoints ? sprintf($html_template, 'touchpoints', $touchpoints_filter_title, $filter_touchpoints_list) : '';
	$html .= $show_employment ? sprintf($html_template, 'employment', $employment_filter_title, $filter_employment_list) : '';
	$html .= $show_race ? sprintf($html_template, 'race', $race_filter_title, $filter_race_list) : '';
	$html .= $show_ethnicity ? sprintf($html_template, 'ethnicity', $ethnicity_filter_title, $filter_ethnicity_list) : '';
	$html .= $show_politics ? sprintf($html_template, 'politics', $politics_filter_title, $filter_politics_list) : '';
	$html .= $show_children_number ? sprintf($html_template, 'childrennumber', $children_number_filter_title, $filter_children_number_list) : '';
	$html .= $show_tccc_influence ? sprintf($html_template, 'tcccinfluence', $tccc_influence_filter_title, $filter_tccc_influence_list) : '';
	$html .= $show_tccc_segment ? sprintf($html_template, 'tcccsegment', $tccc_segment_filter_title, $filter_tccc_segment_list) : '';
	$html .= $show_dmi1 ? sprintf($html_template, 'dmi1', $dmi1_filter_title, $filter_dmi1_list) : '';
	$html .= $show_energinet1 ? sprintf($html_template, 'energinet1', $energinet1_filter_title, $filter_energinet1_list) : '';
	$html .= $show_stakeholder_type ? sprintf($html_template, 'stakeholdertype', $stakeholder_type_filter_title, $filter_stakeholder_type_list) : '';
	$html .= $show_hovedkategori ? sprintf($html_template, 'hovedkategori', $hovedkategori_filter_title, $filter_hovedkategori_list) : '';
	$html .= $show_siemensq1 ? sprintf($html_template, 'siemensq1', $siemensq1_filter_title, $filter_siemensq1_list) : '';
	$html .= $show_association ? sprintf($html_template, 'association', $association_filter_title, $filter_association_list) : '';
	$html .= $show_segment2 ? sprintf($html_template, 'segment2', $segment2_filter_title, $filter_segment2_list) : '';
	$html .= $show_fk1 ? sprintf($html_template, 'fk1', $fk1_filter_title, $filter_fk1_list) : '';
	$html .= $show_cv2 ? sprintf($html_template, 'cv2', $cv2_filter_title, $filter_cv2_list) : '';
	$html .= $show_cv3 ? sprintf($html_template, 'cv3', $cv3_filter_title, $filter_cv3_list) : '';
	$html .= $show_wsa1 ? sprintf($html_template, 'wsa1', $wsa1_filter_title, $filter_wsa1_list) : '';
	$html .= $show_wsa2 ? sprintf($html_template, 'wsa2', $wsa2_filter_title, $filter_wsa2_list) : '';
	$html .= $show_wsa3_studyarea ? sprintf($html_template, 'wsa3studyarea', $wsa3_studyarea_filter_title, $filter_wsa3_studyarea_list) : '';
	$html .= $show_sf1 ? sprintf($html_template, 'sf1', $sf1_filter_title, $filter_sf1_list) : '';
	$html .= $show_eon1 ? sprintf($html_template, 'eon1', $eon1_filter_title, $filter_eon1_list) : '';
	$html .= $show_eon_customer ? sprintf($html_template, 'eoncustomer', $eon_customer_filter_title, $filter_eon_customer_list) : '';
	$html .= $show_gn2 ? sprintf($html_template, 'gn2', $gn2_filter_title, $filter_gn2_list) : '';
	$html .= $show_gn1 ? sprintf($html_template, 'gn1', $gn1_filter_title, $filter_gn1_list) : '';
	$html .= $show_gn3 ? sprintf($html_template, 'gn3', $gn3_filter_title, $filter_gn3_list) : '';
	$html .= $show_gn4 ? sprintf($html_template, 'gn4', $gn4_filter_title, $filter_gn4_list) : '';
	$html .= $show_zs2 ? sprintf($html_template, 'zs2', $zs2_filter_title, $filter_zs2_list) : '';
	$html .= $show_zs3 ? sprintf($html_template, 'zs3', $zs3_filter_title, $filter_zs3_list) : '';
	$html .= $show_ess1 ? sprintf($html_template, 'ess1', $ess1_filter_title, $filter_ess1_list) : '';
	$html .= $show_ess2 ? sprintf($html_template, 'ess2', $ess2_filter_title, $filter_ess2_list) : '';
	$html .= $show_ori1 ? sprintf($html_template, 'ori1', $ori1_filter_title, $filter_ori1_list) : '';
	$html .= $show_ovo_influencer ? sprintf($html_template, 'ovoinfluencer', $ovo_influencer_filter_title, $filter_ovo_influencer_list) : '';
	$html .= $show_ovo_customer ? sprintf($html_template, 'ovocustomer', $ovo_customer_filter_title, $filter_ovo_customer_list) : '';
	$html .= $show_bay5 ? sprintf($html_template, 'bay5', $bay5_filter_title, $filter_bay5_list) : '';
	$html .= $show_ethnicity_ca ? sprintf($html_template, 'ethnicityca', $ethnicity_ca_filter_title, $filter_ethnicity_ca_list) : '';
	$html .= $show_politics_ca ? sprintf($html_template, 'politicsca', $politics_ca_filter_title, $filter_politics_ca_list) : '';
	$html .= $show_air02 ? sprintf($html_template, 'air02', $air02_filter_title, $filter_air02_list) : '';
	$html .= $show_air03 ? sprintf($html_template, 'air03', $air03_filter_title, $filter_air03_list) : '';
	$html .= $show_air04 ? sprintf($html_template, 'air04', $air04_filter_title, $filter_air04_list) : '';

	$html .= '
	</div>
	';

	$hide_importance = get_label('filter_hide_importance')=='1';
	$hide_benchmark = get_label('filter_hide_benchmark')=='1';
	$hide_compare_block = get_label('filter_hide_compare_block')=='1'; 
	
	$html .= '
	<div id="compare_block" '.($hide_compare_block ? 'style="display: none;"' : '').'>
		<p class="benchmarking_title"><span>{{filter_compare_to_icon}}</span> {{filter_compare_to}}</p>
		<div class="benchmarking_checkbox">

			<label for="previous_period">
				<input type="radio" id="previous_period" class="input_benchmarking" name="benchmarking" value="prev_period" checked>
				<span class="input_title">{{filter_compare_to_previous_period}}</span>
			</label>

			'.($hide_benchmark ? '' : '
			<label for="benchmark">
				<input type="radio" id="benchmark" class="input_benchmarking" name="benchmarking" value="benchmark">
				<span class="input_title">{{filter_compare_to_benchmark}}</span>
			</label>
			').'
			'.($hide_importance ? '' : '
			<label for="importance">
				<input type="radio" id="importance" class="input_benchmarking" name="benchmarking" value="importance">
				<span class="input_title">{{filter_compare_to_importance}}</span>
			</label>
			').'
		</div>
	</div>
	';
	
	$show_export_button = $is_admin || ($_SESSION['client_subscription_type'] && $_SESSION['client_subscription_type'] != 'Free') && get_label('hide_export_button')=='0';
	$html .= $show_export_button ? '<button type="button" id="export_chart_button" class="btn btn-light">{{chart_download}}</button>
	' : '';
	
	$html .= '
	</span>'; // end of non report filters
	
	$show_visual_export_button = $is_admin || get_label('show_visual_export_button')==1;
	$html .= ($show_visual_export_button ? '
	<span id="visual_export_wrap">
		<button type="button" id="visual_export_button" class="btn btn-light">{{visual_export_text}}</button>
<!--	<button type="button" id="visual_export_body_button" class="btn btn-light">{{visual_export_body_text}}</button> -->
	</span>
	' : '<span id="visual_export_wrap"></span>'); 
	
	$show_copy_filter_button = get_label('show_copy_filter_button')==1;
	$html .= $show_copy_filter_button ? '<span class="non_report_filters"><button type="button" id="copy_filter_button" class="btn btn-light">COPY FILTER</button></span>' : '';
	
//****** Export data for JS via HTML *****************
	
	$default_country_id = $_SESSION['default_country_id'];
	$default_company_id = $_SESSION['default_company_id'];
	$default_country_array = $_SESSION['default_country_array'];
	
	$html .= '<span id="default_country_array" class="php_js">'.json_encode($default_country_array).'</span>';
	
	$html .= '<span id="default_company_id" class="php_js">'.$default_company_id.'</span>';
	$html .= '<span id="default_country_id" class="php_js">'.$default_country_id.'</span>';
	$html .= '<span id="default_gender_array" class="php_js">'.json_encode($default_gender_array)."</span>\n";
	$html .= '<span id="default_age_array" class="php_js">'.json_encode($default_age_array)."</span>\n";
	$html .= '<span id="default_country_array" class="php_js">'.json_encode($default_country_array)."</span>\n";
	$html .= '<span id="default_region_type_array" class="php_js">'.json_encode($default_region_type_array)."</span>\n";
	$html .= '<span id="default_region_array" class="php_js">'.json_encode($default_region_array)."</span>\n";
	$html .= '<span id="default_education_type_array" class="php_js">'.json_encode($default_education_type_array)."</span>\n";
	$html .= '<span id="default_income_type_array" class="php_js">'.json_encode($default_income_type_array)."</span>\n";
	$html .= '<span id="default_segment_array" class="php_js">'.json_encode($default_segment_array)."</span>\n";
	$html .= '<span id="default_industry_array" class="php_js">'.json_encode($default_industry_array)."</span>\n";
	$html .= '<span id="default_company_size_array" class="php_js">'.json_encode($default_company_size_array)."</span>\n";
	$html .= '<span id="default_diagnosis_array" class="php_js">'.json_encode($default_diagnosis_array)."</span>\n";
	$html .= '<span id="default_family_array" class="php_js">'.json_encode($default_family_array)."</span>\n";
	$html .= '<span id="default_children_array" class="php_js">'.json_encode($default_children_array)."</span>\n";
	$html .= '<span id="default_housing_array" class="php_js">'.json_encode($default_housing_array)."</span>\n";
	$html .= '<span id="default_living_array" class="php_js">'.json_encode($default_living_array)."</span>\n";
	$html .= '<span id="default_engagement_array" class="php_js">'.json_encode($default_engagement_array)."</span>\n";
	$html .= '<span id="default_facilities_array" class="php_js">'.json_encode($default_facilities_array)."</span>\n";
	$html .= '<span id="default_tg1_array" class="php_js">'.json_encode($default_tg1_array)."</span>\n";
	$html .= '<span id="default_tg2_array" class="php_js">'.json_encode($default_tg2_array)."</span>\n";
	$html .= '<span id="default_novo1_array" class="php_js">'.json_encode($default_novo1_array)."</span>\n";
	$html .= '<span id="default_novo2_array" class="php_js">'.json_encode($default_novo2_array)."</span>\n";
	$html .= '<span id="default_demant2_array" class="php_js">'.json_encode($default_demant2_array)."</span>\n";
	$html .= '<span id="default_rw1_array" class="php_js">'.json_encode($default_rw1_array)."</span>\n";
	$html .= '<span id="default_rw2_array" class="php_js">'.json_encode($default_rw2_array)."</span>\n";
	$html .= '<span id="default_gr1_array" class="php_js">'.json_encode($default_gr1_array)."</span>\n";
	$html .= '<span id="default_gr2_array" class="php_js">'.json_encode($default_gr2_array)."</span>\n";
	$html .= '<span id="default_v_customer_array" class="php_js">'.json_encode($default_v_customer_array)."</span>\n";
	$html .= '<span id="default_gol1_array" class="php_js">'.json_encode($default_gol1_array)."</span>\n";
	$html .= '<span id="default_gol2_array" class="php_js">'.json_encode($default_gol2_array)."</span>\n";
	$html .= '<span id="default_gol3_array" class="php_js">'.json_encode($default_gol3_array)."</span>\n";
	$html .= '<span id="default_gol5_array" class="php_js">'.json_encode($default_gol5_array)."</span>\n";
	$html .= '<span id="default_veolia1_array" class="php_js">'.json_encode($default_veolia1_array)."</span>\n";
	$html .= '<span id="default_touchpoints_array" class="php_js">'.json_encode($default_touchpoints_array)."</span>\n";
	$html .= '<span id="default_employment_array" class="php_js">'.json_encode($default_employment_array)."</span>\n";
	$html .= '<span id="default_race_array" class="php_js">'.json_encode($default_race_array)."</span>\n";
	$html .= '<span id="default_ethnicity_array" class="php_js">'.json_encode($default_ethnicity_array)."</span>\n";
	$html .= '<span id="default_politics_array" class="php_js">'.json_encode($default_politics_array)."</span>\n";
	$html .= '<span id="default_children_number_array" class="php_js">'.json_encode($default_children_number_array)."</span>\n";
	$html .= '<span id="default_tccc_influence_array" class="php_js">'.json_encode($default_tccc_influence_array)."</span>\n";
	$html .= '<span id="default_tccc_segment_array" class="php_js">'.json_encode($default_tccc_segment_array)."</span>\n";
	$html .= '<span id="default_dmi1_array" class="php_js">'.json_encode($default_dmi1_array)."</span>\n";
	$html .= '<span id="default_energinet1_array" class="php_js">'.json_encode($default_energinet1_array)."</span>\n";
	$html .= '<span id="default_stakeholder_type_array" class="php_js">'.json_encode($default_stakeholder_type_array)."</span>\n";
	$html .= '<span id="default_hovedkategori_array" class="php_js">'.json_encode($default_hovedkategori_array)."</span>\n";
	$html .= '<span id="default_siemensq1_array" class="php_js">'.json_encode($default_siemensq1_array)."</span>\n";
	$html .= '<span id="default_association_array" class="php_js">'.json_encode($default_association_array)."</span>\n";
	$html .= '<span id="default_segment2_array" class="php_js">'.json_encode($default_segment2_array)."</span>\n";
	$html .= '<span id="default_fk1_array" class="php_js">'.json_encode($default_fk1_array)."</span>\n";
	$html .= '<span id="default_region_zone_array" class="php_js">'.json_encode($default_region_zone_array)."</span>\n";
	$html .= '<span id="default_education_array" class="php_js">'.json_encode($default_education_array)."</span>\n";
	$html .= '<span id="default_income_array" class="php_js">'.json_encode($default_income_array)."</span>\n";
	$html .= '<span id="default_cv2_array" class="php_js">'.json_encode($default_cv2_array)."</span>\n";
	$html .= '<span id="default_cv3_array" class="php_js">'.json_encode($default_cv3_array)."</span>\n";
	$html .= '<span id="default_subregion_br_group_array" class="php_js">'.json_encode($default_subregion_br_group_array)."</span>\n";
	$html .= '<span id="default_wsa1_array" class="php_js">'.json_encode($default_wsa1_array)."</span>\n";
	$html .= '<span id="default_wsa2_array" class="php_js">'.json_encode($default_wsa2_array)."</span>\n";
	$html .= '<span id="default_wsa3_studyarea_array" class="php_js">'.json_encode($default_wsa3_studyarea_array)."</span>\n";
	$html .= '<span id="default_sf1_array" class="php_js">'.json_encode($default_sf1_array)."</span>\n";
	$html .= '<span id="default_eon1_array" class="php_js">'.json_encode($default_eon1_array)."</span>\n";
	$html .= '<span id="default_eon_customer_array" class="php_js">'.json_encode($default_eon_customer_array)."</span>\n";
	$html .= '<span id="default_gn2_array" class="php_js">'.json_encode($default_gn2_array)."</span>\n";
	$html .= '<span id="default_gn1_array" class="php_js">'.json_encode($default_gn1_array)."</span>\n";
	$html .= '<span id="default_gn3_array" class="php_js">'.json_encode($default_gn3_array)."</span>\n";
	$html .= '<span id="default_gn4_array" class="php_js">'.json_encode($default_gn4_array)."</span>\n";
	$html .= '<span id="default_zs2_array" class="php_js">'.json_encode($default_zs2_array)."</span>\n";
	$html .= '<span id="default_zs3_array" class="php_js">'.json_encode($default_zs3_array)."</span>\n";
	$html .= '<span id="default_ess1_array" class="php_js">'.json_encode($default_ess1_array)."</span>\n";
	$html .= '<span id="default_ess2_array" class="php_js">'.json_encode($default_ess2_array)."</span>\n";
	$html .= '<span id="default_ori1_array" class="php_js">'.json_encode($default_ori1_array)."</span>\n";
	$html .= '<span id="default_ovo_influencer_array" class="php_js">'.json_encode($default_ovo_influencer_array)."</span>\n";
	$html .= '<span id="default_ovo_customer_array" class="php_js">'.json_encode($default_ovo_customer_array)."</span>\n";
	$html .= '<span id="default_bay5_array" class="php_js">'.json_encode($default_bay5_array)."</span>\n";
	$html .= '<span id="default_ethnicity_ca_array" class="php_js">'.json_encode($default_ethnicity_ca_array)."</span>\n";
	$html .= '<span id="default_politics_ca_array" class="php_js">'.json_encode($default_politics_ca_array)."</span>\n";
	$html .= '<span id="default_air02_array" class="php_js">'.json_encode($default_air02_array)."</span>\n";
	$html .= '<span id="default_air03_array" class="php_js">'.json_encode($default_air03_array)."</span>\n";
	$html .= '<span id="default_air04_array" class="php_js">'.json_encode($default_air04_array)."</span>\n";
	
// ***** END of paste block	
	
	$html .= '<span id="input_filter_data_obj" class="php_js">'.$filter_data_obj.'</span>
	
	<span id="client_id" class="php_js">'.$client_id.'</span>
	<span id="client_name" class="php_js">'.$_SESSION['client_name'].'</span>
	<span id="user_name" class="php_js">'.$_SESSION['user']['name'].'</span>
	<span id="user_email" class="php_js">'.$_SESSION['user']['email'].'</span>
	<span id="user_id" class="php_js">'.$_SESSION['user']['id'].'</span>
	<span id="customer_id" class="php_js">'.getValue('client_customer', 'clients', 'clients_id', $client_id).'</span>
	<span id="user_created" class="php_js">'.date_format(date_create(getValue('user_created', 'users', 'users_id', $_SESSION['user']['id'])),"d/m/y").'</span>
	
	<span id="client_show_all" class="php_js">'.$_SESSION['client_show_all'].'</span>
	<span id="client_company_array" class="php_js">'.json_encode($_SESSION['client_company_array']).'</span>	
	<span id="client_company_array_all" class="php_js">'.json_encode($_SESSION['client_company_array_all']).'</span>
	<span id="client_model" class="php_js">'.$client_model.'</span>
	<span id="client_role" class="php_js">'.$_SESSION['client_role'].'</span>
	<span id="client_core_element_id" class="php_js">'.$client_core_element_id.'</span>
	<span id="client_map_dashboard_disabled" class="php_js">'.$client_map_dashboard_disabled.'</span>
	<span id="client_map_center" class="php_js">'.$client_map_center.'</span>
	<span id="console_log_on" class="php_js">'.(($site_version == 'production' || $site_version == 'cron') && get_label('console_log_alfa') == 0 ? 0 : 1).'</span>
	<span id="site_version" class="php_js">'.$site_version.'</span>
	
	<span id="map_auto_increment" class="php_js">{{progress_auto_increment_map}}</span>
	<span id="dashboard_auto_increment" class="php_js">{{progress_auto_increment_dashboard}}</span>
	<span id="chart_auto_increment" class="php_js">{{progress_auto_increment_chart}}</span>
	<span id="table_auto_increment" class="php_js">{{progress_auto_increment_table}}</span>
	
	<span id="filter_group" class="php_js">{{filter_group}}</span>
	<span id="filter_ungroup" class="php_js">{{filter_ungroup}}</span>
	<span id="filter_select_all" class="php_js">{{filter_select_all}}</span>
	<span id="filter_deselect_all" class="php_js">{{filter_deselect_all}}</span>
	<span id="map_ajax_delay" class="php_js">{{map_ajax_delay}}</span>
	<span id="dashboard_ajax_delay" class="php_js">{{dashboard_ajax_delay}}</span>
	<span id="chart_ajax_delay" class="php_js">{{chart_ajax_delay}}</span>
	<span id="table_ajax_delay" class="php_js">{{table_ajax_delay}}</span>
	<span id="enable_view_update_from_menu" class="php_js">{{enable_view_update_from_menu}}</span>
	<span id="view_render_start_offset" class="php_js">{{view_render_start_offset}}</span>
	<span id="progress_bar_show_details" class="php_js">{{progress_bar_show_details}}</span>
	<span id="progress_bar_start_details_map" class="php_js">{{progress_bar_start_details_map}}</span>
	<span id="progress_bar_start_details_dashboard" class="php_js">{{progress_bar_start_details_dashboard}}</span>
	<span id="progress_bar_start_details_chart" class="php_js">{{progress_bar_start_details_chart}}</span>
	<span id="progress_bar_start_details_table" class="php_js">{{progress_bar_start_details_table}}</span>
	<span id="progress_bar_show_fractions" class="php_js">{{progress_bar_show_fractions}}</span>
	
	<span id="progress_bar_start_percent_width_map" class="php_js">{{progress_bar_start_percent_width_map}}</span>
	<span id="progress_bar_start_percent_width_dashboard" class="php_js">{{progress_bar_start_percent_width_dashboard}}</span>
	<span id="progress_bar_start_percent_width_chart" class="php_js">{{progress_bar_start_percent_width_chart}}</span>
	<span id="progress_bar_start_percent_width_table" class="php_js">{{progress_bar_start_percent_width_table}}</span>
		
	<span id="progress_bar_start_details_width_map" class="php_js">{{progress_bar_start_details_width_map}}</span>
	<span id="progress_bar_start_details_width_dashboard" class="php_js">{{progress_bar_start_details_width_dashboard}}</span>
	<span id="progress_bar_start_details_width_chart" class="php_js">{{progress_bar_start_details_width_chart}}</span>
	<span id="progress_bar_start_details_width_table" class="php_js">{{progress_bar_start_details_width_table}}</span>
		
	<span id="progress_bar_start_fractions_width_map" class="php_js">{{progress_bar_start_fractions_width_map}}</span>
	<span id="progress_bar_start_fractions_width_dashboard" class="php_js">{{progress_bar_start_fractions_width_dashboard}}</span>
	<span id="progress_bar_start_fractions_width_chart" class="php_js">{{progress_bar_start_fractions_width_chart}}</span>
	<span id="progress_bar_start_fractions_width_table" class="php_js">{{progress_bar_start_fractions_width_table}}</span>
	
	<span id="retry_ajax_dashboard_limit" class="php_js">{{retry_ajax_dashboard_limit}}</span>
	
	<span id="enable_filter_sync" class="php_js">{{enable_filter_sync}}</span>
	<span id="enable_filter_sync_ratings" class="php_js">{{enable_filter_sync_ratings}}</span>
	
	<span id="calendar_settings" class="php_js">{{calendar_settings}}</span>

	<span id="period_prev_date_chart" class="php_js">{{period_prev_date_chart}}</span>
	<span id="period_prev_date_offset" class="php_js">{{period_prev_date_offset}}</span>
	<span id="default_view" class="php_js">{{default_view}}</span>
	<span id="hide_report_view" class="php_js">{{hide_report_view}}</span>
	
	<span id="report_chart_intervals" class="php_js">{{report_chart_intervals}}</span>
	<span id="view_snapshot_name" class="php_js">{{view_snapshot_name}}</span>
	<span id="html2canvas_settings" class="php_js">{{html2canvas_settings}}</span>
	
	<!--span id="wordcloud_exclusions" class="php_js">{{wordcloud_exclusions}}</span-->
	<span id="wordcloud_threshold_label" class="php_js">{{wordcloud_threshold_label}}</span>
	<span id="wordcloud_threshold_max" class="php_js">{{wordcloud_threshold_max}}</span>


	<span id="wordcloud_threshold_default" class="php_js">{{wordcloud_threshold_default}}</span>
	<span id="wordcloud_rotation_settings" class="php_js">{{wordcloud_rotation_settings}}</span>
	<!--span id="wordcloud_dividers" class="php_js">{{wordcloud_dividers}}</span-->
	<span id="report_chart_options" class="php_js">{{report_chart_options}}</span>
	<span id="hide_report_period" class="php_js">{{hide_report_period}}</span>
	<span id="chart_band_highlight" class="php_js">{{chart_band_highlight}}</span>
	<span id="chart_flag_focus_color" class="php_js">{{chart_flag_focus_color}}</span>
	<span id="activity_delete_ok" class="php_js">{{activity_delete_ok}}</span>
	<span id="enable_appcues" class="php_js">{{enable_appcues}}</span>
	<span id="wordcloud_respondents" class="php_js">{{wordcloud_respondents}}</span>

	<span id="filter_reset_msg" class="php_js">{{filter_reset_msg}}</span>
	';
 
	$html .= '
	</div>
	';

	return $html;
	
}

function sortFilter($filter) { // this function may be more universal across the application - TODO
	$filter_sort = [];
	$filter_sorted = [];
	foreach ($filter as $this_filter) {
		$filter_sort[] = $this_filter[1];
	}
	natcasesort($filter_sort);
	foreach ($filter_sort as $this_filter_sort) {
		foreach ($filter as $this_filter) {
			if ($this_filter[1] == $this_filter_sort) 
			$filter_sorted[] = [$this_filter[0],$this_filter[1]]; 
		}
	}
	return $filter_sorted;
}


function getFilterFieldList($field_array, $field_name, $template, $country=false)
{
/* // template example	
	$list_template = '
		<label for="%1$s">
			<input type="checkbox" id="%1$s" class="input_%2$s" name="%2$s" value="%3$s" %5$s checked>
			<span class="input_title">%4$s</span>
		</label>
	';
*/	
	$field_list = '';
	foreach ($field_array as $field) 
	{
		$value = $field[0];
		$name = $field_name.'_'.$value;	
		$title = $field[1];
		$country = isset($field[2]) ? $field[2] : '';
		$_id = isset($field[3]) ? $field[3] : '';
		$data_country = !empty($country) ? 'data-'.$field_name.'_country="'.$country.'"' : '';
		$field_list .= sprintf($template, $name, $field_name, $value, $title, $data_country, $_id);
	}
	return $field_list;
}

function getFieldArrayFromAnswers($question)
{
	$query = "SELECT `answer_value`, `answer_name` FROM `answers` WHERE `answer_question`='$question' ORDER BY `answer_order` ";
	
	$sql = sql_query($query);
	if ($sql)
	{	
		$result = array();
		while ($row = sql_fetch_array($sql)) 
		{
			$result[] = [$row['answer_value'], strip_tags($row['answer_name'])]; 
		}
	} else $result = false;	
	return $result;
}

function getFieldArrayFromSubquestions($field_question_id, $use_id=false, $client_id=0)
{
	$field_array = [];
	
	$query = 
		"SELECT `questions_id`, `question_order`, `question_name`, `question_portal_name`, `question_value` FROM `questions` WHERE `question_parent`='$field_question_id' ORDER BY `question_order` "
	;
	
	$result = sql_query($query);	
	if (sql_num_rows($result) > 0) 
	{
		while ($row = sql_fetch_array($result)) 
		{
			if ($use_id===true) { $_index = 'questions_id'; }
			if ($use_id===false) { $_index = 'question_order'; }
			if ($use_id===2) { $_index = 'question_value'; }
			$_value = $row[$_index];
			$_name = empty($row['question_portal_name']) ? $row['question_name'] : $row['question_portal_name'];
			if ($client_id>0)
			{
				$filter_label = 'filter_clients_question_'.$row['questions_id'];
				$question_clients = get_label($filter_label);
				if (!empty($question_clients))
				{
					$clients_array = explode(',', $question_clients);
					if (in_array($client_id, $clients_array)) {
						$field_array[] = [$_value, $_name]; 
					}
				} else {
					$field_array[] = [$_value, $_name]; 
				}
			} else {
				$field_array[] = [$_value, $_name]; 
			}
		}
	}
	
	return $field_array;
}

function getFieldArrayFromTable($field, $name="", $where="", $from="", $use_id=false, $order="")
{
	
	$field_array = [];
	$table = (empty($from) ? "{$field}s" : "{$from}");
	$query = 
		"SELECT ".
			($use_id ? "`{$table}_id`, " : "`{$field}_value`, ").
			"`{$field}_name{$name}` ".
		" FROM ".
			"`$table` ".
		(empty($where) ? ' ' : " WHERE $where ").
		" ORDER BY ".
			(empty($order) || $order=='order' ? " `{$field}_order` " : $order);
	
	$result = sql_query($query);	
	if (sql_num_rows($result) > 0) 
	{
		while ($row = sql_fetch_array($result)) 
		{
			$value = $use_id ? $row[$table.'_id'] : $row[$field.'_value'];
			$field_array[] = [$value, $row[$field.'_name'.$name]]; 
		}
	}
	return $field_array;
}

function getFieldArrayByCountry($field, $table, $default=false, $order='value', $where='', $is9999=false, $use_id=true)
{
    $return_array = [];
	$order_by = explode('_', $order);
	
	$query = 
		"SELECT ".
			"`{$table}_id`, ".
			(!$use_id ? "`{$field}_value`, " : "").
			"`{$field}_order`, ".
			"`{$field}_name`, ".
			"`{$field}_country` ".
		"FROM ".
			"`{$table}`, ".
			"`countries` ".
		"WHERE ".
			"`{$field}_country` = `countries_id` ".
			"$where ".
		"ORDER BY ".
			(isset($order_by[1]) 
			 ? 
				"`{$field}_{$order_by[1]}`"
			 :
				"`country_name`, ".
				"`{$field}_{$order}`"
			)
	;
	
	$index = ($use_id ? $table.'_id' : $field.'_value');
//-	$result = sql_query($query);    
	$sql = query($query);   // table dosen't exist error quickfix - TODO: must be refactored in functions query() and sql_query()
    while ($row = sql_fetch_assoc($sql)) 
    {
        if (in_array($row[$field.'_country'], $_SESSION['client_country_array'])) {
            if ($default) {
                if ($row[$field.'_country'] == $_SESSION['default_country_id']) {
                    if (!find_in_array($return_array, $row[$index])) {
                        $return_array[] = $row[$index];
                    } 
                }
            } else {
                $i = find_in_array($return_array, $row[$index]);
                if ($i===false) {
                    $return_array[] = [$row[$index], $row[$field.'_name'], $row[$field.'_country'], $row[$field.'_order']];
                } else {
                    $return_array[$i][2] = $return_array[$i][2].','.$row[$field.'_country'];
                }
            }
        }
    }

	if ($is9999) { if ($default) { $return_array[] = '9999'; } }

	return $return_array;
}

function find_in_array($array, $index)
{
	$i = 0;
	foreach ($array as $line) {
		$i++;
		$value = is_array($line) ? $line[0] : $line;
		if ($value==$index) { return ($i-1); }
	}
	return false;
}
	
function get_default_values($array)
{
	$return = array();
	foreach ($array as $element)
	{
		$return[] = $element[0];
	}
	return $return;
}

