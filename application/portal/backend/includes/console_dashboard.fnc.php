<?php

/*
	FILE DESCRIPTION: 
		PATH: backend/includes/console_dashboard.fnc.php;
		TYPE: fnc (function declaration);
		PURPOSE: declares functions used to generate the Dashboard HTML;
		REFERENCED IN: backend/ajax/console_dashboard.ajax.php;
		FUNCTIONS DECLARED:
			NAME: getDashboard;
				PURPOSE: returns the Dashboard HTML filled with indicators calculated based on data from function getCompanyStatistics in file backend/includes/statistics.fnc.php;
				EXECUTED IN: backend/ajax/console_dashboard.ajax.php;
			NAME: getAttributeColor;
				PURPOSE: 
				EXECUTED IN: console_dashboard.fnc.php;
		STYLES: frontend/css/console_dashboard.css; 
*/  

function getDashboard( 
	$type,
	$company_id,
	$company_compare_id,
	$attribute_id,
	$benchmarking_id,
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
) {

//?	if ($ajax_key==null) { return 'Error: $ajax_key is null!.. '; }
	
	global $con;

	$is_benchmark = $benchmarking_id == 'benchmark';
	
	// prepare company ids depending on dashboard mode (option_benchmark or cur_option_month)
	$type = explode('_',$type);
	$table_column_order = $type[1];
	$type = $type[0];
	
	
	// get company id (ids) and name
	$company_ids = [];	
	$company_count = 0;
	$any_company_id = '';
	if ($type == 'dashboard') { // $company_id is an array of ids
		
		$company_ids = $company_id;
		foreach ($company_id as $this_company_id) {
			if ($this_company_id != 'all' && $this_company_id != 'group' && $this_company_id != 'multiple') {
				if ($company_count == 0) $company_id = $this_company_id;
				$company_count++;	
			}
			if (/*$benchmarking_id == 'benchmark'*/ $is_benchmark) $company_id = $company_compare_id[0];
		}
		
		if ($company_count == 1 || $benchmarking_id != 'period') {
			$company_name = getValue('rating_name_short', TABLE_RATINGS, 'ratings_id', $company_id);
			$company_ids = ['group',$company_id];
		} else {
			$company_name = get_label('filter_multiple_companies');
				// {{filter_multiple_companies}}="Multiple companies"
		}	
		
	} else { // $company_id is not an array
		$company_name = getValue('rating_name_short', TABLE_RATINGS, 'ratings_id', $company_id);
		$company_ids = ['group',$company_id];
		$company_count = 1;
	}
	$company_compare_id = $company_compare_id[1];
	$company_2_compare_name = getValue('rating_name_short', TABLE_RATINGS, 'ratings_id', $company_compare_id);
	
	$small_number_mid_cls = 'small_number_mid';

	$legend_row2_1 = 'shown';
	$legend_row2_2 = 
	$legend_row2_3 = 
	$legend_row2_4 = 'hidden';

	$attributes_array_size = 0 // = 170
		+42 // +42 for Q310_x
		+2 // +2 for trust_affection & Q305_3_01
		+6 // +6 for Q215_x
		+2 // +2 for S104, S105
		+4 // +4 for VEOLIA2 
		+8 // +8 for VEOLIA1 
		+8 // +10 for ENGAGEMENT
		+7 // +7 for FACILITIES
//		+1 // +1 for REPUTATION
		+6 // +6 for SUSTAINABILITY1&2
		+7 // +7 for INITIATIVES
		+16 // +16 for AGENDA
		+12 // +12 for Q215_1_xx
		+3 // +3 ADVOCACY_xx
		+1 // +1 for REPUTATION_SCORE
		+1 // +1 for PORTFOLIO
		+12 // +12 for DMI1
		+11 // +11 for ENERGINET1
		+28 // +28 for SIEMENS
		+10 // +10 for HANSEN (CHR & Q310_60-63)
		+11 // +11 for FALCK
		+8 // +8 for FALCK (Q310_64-71)
		+16 // +16 for FK2_1_xx (Falck page2)
		+6 // +6 for Casa&Video (Q310_72-77)
		+23 //+23 gor Zeiss (zs_)
		+20 // +20 for GN
		+4 // +4 for (Q310_23_)
		+4 // +4 for (Ovo & Orifarm)
		+5 // +5 for Airbus
				
	; 
	
	$attributes_array_slice = $attributes_array_size; // - 8; 
	
	// retrieve averages for latest and previous 30 calendar days from function
	$attribute_last_arr =  
	$attribute_prev_arr = 
	$attribute_last_comp_arr = 
	$attribute_incr_arr = array_fill(0,$attributes_array_size,'');
	$responce_count_trust_affection = 
	$responce_count_awareness = 	
	$responce_count_familiarity = 
	$responce_uncertainty = 
	$responce_min_date = 
	$responce_max_date = '';
	$i = 0; 
															
	//$company_data_tst = [];
	$testarray = [];
	foreach ($attribute_id as $this_attribute) {
		if ($this_attribute) {
			
			$company_data = json_decode(
				getCompanyStatistics(
					$company_ids,
					[$this_attribute],
					[$period_id],
					[''],
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
				)
			);
			if(count($company_data) > 0) {
				return 'company_data of this_attribute is -'. $company_data;
			} else {
				array_push($testarray, $i);
			}
//-			$company_extras = json_decode($company_data[1]);
			$company_extras = $company_data[1];
/*            
            $table_column_title_full = '';
            foreach (array_slice($company_extras, 2, -3) as $extra) 
            {
                $table_column_title_full .= $extra;
//                if ($table_column_title_full!='') break;
            }
*/			
            $all_types = ['group', 'all', 'multiple'];
			$table_column_title_full = 
                ( count($gender_id)==1 && !in_array($gender_id[0], $all_types) ? $company_extras[2] : '').
                ( count($age_id)==1 && !in_array($age_id[0], $all_types) ? $company_extras[3] : '').
                ( count($country_id)==1 && !in_array($country_id[0], $all_types) ? $company_extras[4] : '').
                ( count($region_type_id)==1 && !in_array($region_type_id[0], $all_types) ? $company_extras[5] : '').
                ( count($region_id)==1 && !in_array($region_id[0], $all_types) ? $company_extras[6] : '').
                ( count($education_type_id)==1 && !in_array($education_type_id[0], $all_types) ? $company_extras[7] : '').
                ( count($income_type_id)==1 && !in_array($income_type_id[0], $all_types) ? $company_extras[8] : '').
                ( count($segment_id)==1 && !in_array($segment_id[0], $all_types) ? $company_extras[9] : '').
                ( count($industry_id)==1 && !in_array($industry_id[0], $all_types) ? $company_extras[10] : '').
                ( count($company_size_id)==1 && !in_array($company_size_id[0], $all_types) ? $company_extras[11] : '').
                ( count($diagnosis_id)==1 && !in_array($diagnosis_id[0], $all_types) ? $company_extras[12] : '').
                ( count($family_id)==1 && !in_array($family_id[0], $all_types) ? $company_extras[13] : '').
                ( count($children_id)==1 && !in_array($children_id[0], $all_types) ? $company_extras[14] : '').
                ( count($housing_id)==1 && !in_array($housing_id[0], $all_types) ? $company_extras[15] : '').
                ( count($living_id)==1 && !in_array($living_id[0], $all_types) ? $company_extras[16] : '').
                ( count($engagement_id)==1 && !in_array($engagement_id[0], $all_types) ? $company_extras[17] : '').
                ( count($facilities_id)==1 && !in_array($facilities_id[0], $all_types) ? $company_extras[18] : '').
                ( count($tg1_id)==1 && !in_array($tg1_id[0], $all_types) ? $company_extras[19] : '').
                ( count($tg2_id)==1 && !in_array($tg2_id[0], $all_types) ? $company_extras[20] : '').
                ( count($novo1_id)==1 && !in_array($novo1_id[0], $all_types) ? $company_extras[21] : '').
                ( count($novo2_id)==1 && !in_array($novo2_id[0], $all_types) ? $company_extras[22] : '').
                ( count($demant2_id)==1 && !in_array($demant2_id[0], $all_types) ? $company_extras[23] : '').
                ( count($rw1_id)==1 && !in_array($rw1_id[0], $all_types) ? $company_extras[24] : '').
                ( count($rw2_id)==1 && !in_array($rw2_id[0], $all_types) ? $company_extras[25] : '').
                ( count($gr1_id)==1 && !in_array($gr1_id[0], $all_types) ? $company_extras[26] : '').
                ( count($gr2_id)==1 && !in_array($gr2_id[0], $all_types) ? $company_extras[27] : '').
                ( count($v_customer_id)==1 && !in_array($v_customer_id[0], $all_types) ? $company_extras[28] : '').
                ( count($gol1_id)==1 && !in_array($gol1_id[0], $all_types) ? $company_extras[29] : '').
                ( count($gol2_id)==1 && !in_array($gol2_id[0], $all_types) ? $company_extras[30] : '').
                ( count($gol3_id)==1 && !in_array($gol3_id[0], $all_types) ? $company_extras[31] : '').
                ( count($gol5_id)==1 && !in_array($gol5_id[0], $all_types) ? $company_extras[32] : '').
                ( count($veolia1_id)==1 && !in_array($veolia1_id[0], $all_types) ? $company_extras[33] : '').
                ( count($touchpoints_id)==1 && !in_array($touchpoints_id[0], $all_types) ? $company_extras[34] : '').
                ( count($employment_id)==1 && !in_array($employment_id[0], $all_types) ? $company_extras[35] : '').
                ( count($race_id)==1 && !in_array($race_id[0], $all_types) ? $company_extras[36] : '').
                ( count($ethnicity_id)==1 && !in_array($ethnicity_id[0], $all_types) ? $company_extras[37] : '').
                ( count($politics_id)==1 && !in_array($politics_id[0], $all_types) ? $company_extras[38] : '').
                ( count($children_number_id)==1 && !in_array($children_number_id[0], $all_types) ? $company_extras[39] : '').
                ( count($tccc_influence_id)==1 && !in_array($tccc_influence_id[0], $all_types) ? $company_extras[40] : '').
                ( count($tccc_segment_id)==1 && !in_array($tccc_segment_id[0], $all_types) ? $company_extras[41] : '').
                ( count($dmi1_id)==1 && !in_array($dmi1_id[0], $all_types) ? $company_extras[42] : '').
                ( count($energinet1_id)==1 && !in_array($energinet1_id[0], $all_types) ? $company_extras[43] : '').
                ( count($stakeholder_type_id)==1 && !in_array($stakeholder_type_id[0], $all_types) ? $company_extras[44] : '').
                ( count($hovedkategori_id)==1 && !in_array($hovedkategori_id[0], $all_types) ? $company_extras[45] : '').
                ( count($siemensq1_id)==1 && !in_array($siemensq1_id[0], $all_types) ? $company_extras[46] : '').
                ( count($association_id)==1 && !in_array($association_id[0], $all_types) ? $company_extras[47] : '').
                ( count($segment2_id)==1 && !in_array($segment2_id[0], $all_types) ? $company_extras[48] : '').
				( count($fk1_id)==1 && !in_array($fk1_id[0], $all_types) ? $company_extras[49] : '').
				( count($region_zone_id)==1 && !in_array($region_zone_id[0], $all_types) ? $company_extras[50] : '').
				( count($education_id)==1 && !in_array($education_id[0], $all_types) ? $company_extras[51] : '').
				( count($income_id)==1 && !in_array($income_id[0], $all_types) ? $company_extras[52] : '').
				( count($cv2_id)==1 && !in_array($cv2_id[0], $all_types) ? $company_extras[53] : '').
				( count($cv3_id)==1 && !in_array($cv3_id[0], $all_types) ? $company_extras[54] : '').
				( count($subregion_br_group_id)==1 && !in_array($subregion_br_group_id[0], $all_types) ? $company_extras[55] : '').
				( count($wsa1_id)==1 && !in_array($wsa1_id[0], $all_types) ? $company_extras[56] : '').
				( count($wsa2_id)==1 && !in_array($wsa2_id[0], $all_types) ? $company_extras[57] : '').
				( count($wsa3_studyarea_id)==1 && !in_array($wsa3_studyarea_id[0], $all_types) ? $company_extras[58] : '').
				( count($sf1_id)==1 && !in_array($sf1_id[0], $all_types) ? $company_extras[59] : '').
				( count($eon1_id)==1 && !in_array($eon1_id[0], $all_types) ? $company_extras[60] : '').
				( count($eon_customer_id)==1 && !in_array($eon_customer_id[0], $all_types) ? $company_extras[61] : '').
				( count($gn2_id)==1 && !in_array($gn2_id[0], $all_types) ? $company_extras[62] : '').
				( count($gn1_id)==1 && !in_array($gn1_id[0], $all_types) ? $company_extras[63] : '').
				( count($gn3_id)==1 && !in_array($gn3_id[0], $all_types) ? $company_extras[64] : '').
				( count($gn4_id)==1 && !in_array($gn4_id[0], $all_types) ? $company_extras[65] : '').
				( count($zs2_id)==1 && !in_array($zs2_id[0], $all_types) ? $company_extras[66] : '').
				( count($zs3_id)==1 && !in_array($zs3_id[0], $all_types) ? $company_extras[67] : '').
				( count($ess1_id)==1 && !in_array($ess1_id[0], $all_types) ? $company_extras[68] : '').
				( count($ess2_id)==1 && !in_array($ess2_id[0], $all_types) ? $company_extras[69] : '').
				( count($ori1_id)==1 && !in_array($ori1_id[0], $all_types) ? $company_extras[70] : '').
				( count($ovo_influencer_id)==1 && !in_array($ovo_influencer_id[0], $all_types) ? $company_extras[71] : '').
				( count($ovo_customer_id)==1 && !in_array($ovo_customer_id[0], $all_types) ? $company_extras[72] : '').
				( count($bay5_id)==1 && !in_array($bay5_id[0], $all_types) ? $company_extras[73] : '').
				( count($ethnicity_ca_id)==1 && !in_array($ethnicity_ca_id[0], $all_types) ? $company_extras[74] : '').
				( count($politics_ca_id)==1 && !in_array($politics_ca_id[0], $all_types) ? $company_extras[75] : '').
				( count($air02_id)==1 && !in_array($air02_id[0], $all_types) ? $company_extras[76] : '').
				( count($air03_id)==1 && !in_array($air03_id[0], $all_types) ? $company_extras[77] : '').
				( count($air04_id)==1 && !in_array($air04_id[0], $all_types) ? $company_extras[78] : '').


				''
			;
			
			$table_column_title_full_split = explode(' - ',$table_column_title_full);
			$table_column_title_full_slice = strlen($table_column_title_full_split[0]) + 3;
			$table_column_title_full = substr($table_column_title_full, $table_column_title_full_slice);

			$table_title_max_length = get_label('table_title_max_length');

			// If the column title full is not null, create the short form, that is eventually used for UI, from it.
            // This if statement is implemented to handle the issue of no label being present in some contexts.
            // TODO: The underlying cause of this issue should be found and solved eventually.
            if ($table_column_title_full != null)
            {
                $table_column_title_short = get_left_chars($table_column_title_full, $table_title_max_length);
            }

            $table_column_title_full = $table_column_title_full == $table_column_title_short ? '' : $table_column_title_full;

            $responce_count_prev = 1;
			
			if(is_array($this_attribute)) {	// POR-721 - fixing 
				$this_attribute_name = array_diff($this_attribute, ['group', 'all'])[0];
			} else {
				$this_attribute_name = $this_attribute;
			}
			
			$attribute_field_type = getValue('field_type', 'fields', 'field_name', $this_attribute_name, 1);
			$attribute_percent = $company_data[1][count($company_data[1])-1] -> percent; // extract the percent marker from attribute data array
			$attribute_percent_suffix = $attribute_percent ? '%' : '';
			
			if ($company_data[0]) {
				
				$company_data = json_decode($company_data[0]);
				foreach ($company_data as $this_company_data) {
return 'this_company_data'.json_decode($this_company_data);
					// get data from prev period subarray
					if ( $this_company_data[1] == 0 ) {
						$responce_count_prev = $this_company_data[3];

						if ($attribute_field_type==7 /*VEOLIA, ENGAGEMENT, FACILITIES, INITIATIVES, AGENDA */ && $responce_count_prev!=0)
						{
							$attribute_prev_arr[$i] = round($this_company_data[2]/$this_company_data[3]*100,0).$attribute_percent_suffix;
						} else {
							$attribute_prev_arr[$i] = $this_company_data[2].$attribute_percent_suffix;
						}
					}
					
					// get data from last period subarray
					if ( $this_company_data[1] == 1 ) {

						if ($attribute_field_type==7 /*VEOLIA, ENGAGEMENT, FACILITIES, INITIATIVES, AGENDA*/ && $this_company_data[3]!=0)
						{
							$attribute_last_arr[$i] = round($this_company_data[2]/$this_company_data[3]*100,0).$attribute_percent_suffix;
						} else {
							$attribute_last_arr[$i] = $this_company_data[2].$attribute_percent_suffix;
						}

						if ( $this_attribute != 'S104' && $this_attribute != 'S105' ) { // this data is different for attributes awareness and familiarity - so we skip them
							$responce_count_trust_affection = $this_company_data[3];
							$responce_min_date = $this_company_data[4];
							$responce_max_date = $this_company_data[5];
							$responce_field = $this_company_data[6];
						}
						
						if ( $this_attribute == 'S104') $responce_count_awareness = $this_company_data[3];
						
						if ( $this_attribute == 'S105') $responce_count_familiarity = $this_company_data[3];

						$responce_uncertainty = $responce_count_trust_affection < 50 || $responce_count_awareness < 50 || $responce_count_familiarity < 50 ? 1 : 0;
					}	
				}
			}

			// calculate averages for latest 30 calendar days for compared company if benchmark mode on
			if ($is_benchmark) {
				$company_data = json_decode(
					getCompanyStatistics(
						['group',$company_compare_id],
						[$this_attribute],
						[$period_id],
						[''],
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
					)
				);
				// return 'company_data -'.json_encode($company_data);
				if ($company_data[0]) {
					
					$company_data = json_decode($company_data[0]);
					foreach ($company_data as $this_company_data) {
						// get data from last period subarray

						if ( $this_company_data[1] == 1 ) 
						{
							if ($attribute_field_type==7 /*VEOLIA, ENGAGEMENT, FACILITIES INITIATIVES, AGENDA*/ && $this_company_data[3]!=0)
							{
								$attribute_last_comp_arr[$i] = round($this_company_data[2]/$this_company_data[3]*100, 0).$attribute_percent_suffix;
							} else {
								$attribute_last_comp_arr[$i] = $this_company_data[2].$attribute_percent_suffix;
							}
							
						}
					}
				}
			}
		}
		
		$i++;
	}
	// return 'testarray'.json_encode($testarray);
	list(
		$offering_last	,
		$innovation_last	,
		$integrity_last	,
		$leadership_last	,
		$authenticity_last	,
		$differentiation_last	,
		$relevance_last	,
		$inspiration_last	,
		$Q310_12_last	,
		$Q310_13_last	,
		$Q310_14_last	,
		$Q310_15_last	,
		$Q310_16_last	,
		$Q310_21_last	,
		$Q310_22_last	,
		$Q310_23_last	,
		$Q310_24_last	,
		$Q310_25_last	,
		$Q310_26_last	,
		$Q310_27_last	,
		$Q310_28_last	,
		$Q310_29_last	,
		$Q310_30_last	,
		$Q310_31_last	,
		$Q310_32_last	,
		$Q310_33_last	,
		$Q310_34_last	,
		$Q310_35_last	,
		$Q310_36_last	,
		$Q310_37_last	,
		$trust_affection_last	,
		$Q305_3_01_last	,
		$consideration_last	,
		$recommendation_last	,
		$advocacy_last	,
		$employment_last	,
		$Q215_5_last	,
		$Q215_6_last	,
		$awareness_last	,
		$familiarity_last	,
		$VEOLIA2_1_last	,
		$VEOLIA2_2_last	,
		$VEOLIA2_3_last	,
		$VEOLIA2_4_last	,
		$VEOLIA1_1_last	,
		$VEOLIA1_2_last	,
		$VEOLIA1_3_last	,
		$VEOLIA1_4_last	,
		$VEOLIA1_5_last	,
		$VEOLIA1_6_last	,
		$VEOLIA1_7_last	,
		$VEOLIA1_98_last	,
		$ENGAGEMENT_1_last	,
		$ENGAGEMENT_2_last	,
		$ENGAGEMENT_3_last	,
		$ENGAGEMENT_4_last	,
		$ENGAGEMENT_5_last	,
		$ENGAGEMENT_6_last	,
		$ENGAGEMENT_7_last	,
		$ENGAGEMENT_8_last	,
		$FACILITIES_1_last	,
		$FACILITIES_2_last	,
		$FACILITIES_3_last	,
		$FACILITIES_4_last	,
		$FACILITIES_5_last	,
		$FACILITIES_98_last	,
		$FACILITIES_99_last	,
		$SUSTAINABILITY1_last	,
		$SUSTAINABILITY2_1_last	,
		$SUSTAINABILITY2_2_last	,
		$SUSTAINABILITY2_3_last	,
		$SUSTAINABILITY2_4_last	,
		$SUSTAINABILITY2_5_last	,
		$INITIATIVES_1_last	,
		$INITIATIVES_2_last	,
		$INITIATIVES_3_last	,
		$INITIATIVES_4_last	,
		$INITIATIVES_5_last	,
		$INITIATIVES_6_last	,
		$INITIATIVES_7_last	,
		$AGENDA_1_last	,
		$AGENDA_2_last	,
		$AGENDA_3_last	,
		$AGENDA_4_last	,
		$AGENDA_5_last	,
		$AGENDA_6_last	,
		$AGENDA_7_last	,
		$AGENDA_8_last	,
		$AGENDA_9_last	,
		$AGENDA_10_last	,
		$AGENDA_11_last	,
		$AGENDA_12_last	,
		$Q215_1_DISAGREE_last	,
		$Q215_1_NEUTRAL_last	,
		$Q215_1_AGREE_last	,
		$Q215_2_DISAGREE_last	,
		$Q215_2_NEUTRAL_last	,
		$Q215_2_AGREE_last	,
		$Q215_3_DISAGREE_last	,
		$Q215_3_NEUTRAL_last	,
		$Q215_3_AGREE_last	,
		$Q215_4_DISAGREE_last	,
		$Q215_4_NEUTRAL_last	,
		$Q215_4_AGREE_last	,
		$ADVOCACY_CRITICAL_last	,
		$ADVOCACY_NEUTRAL_last	,
		$ADVOCACY_HIGH_last	,
		$REPUTATION_SCORE_last	,
		$PORTFOLIO_last	,
		$DMI1_1_last	,
		$DMI1_2_last	,
		$DMI1_3_last	,
		$DMI1_4_last	,
		$DMI1_5_last	,
		$DMI1_6_last	,
		$DMI1_7_last	,
		$DMI1_8_last	,
		$DMI1_9_last	,
		$DMI1_10_last	,
		$DMI1_98_last	,
		$DMI1_99_last	,
		$Q310_39_last	,
		$Q310_40_last	,
		$Q310_41_last	,
		$Q310_42_last	,
		$Q310_43_last	,
		$Q310_44_last	,
		$Q310_45_last	,
		$Q310_46_last	,
		$Q310_47_last	,
		$Q310_48_last	,
		$Q310_49_last	,
		$Q310_50_last	,
		$Q310_51_last	,
		$Q310_52_last	,
		$Q310_53_last	,
		$Q310_54_last	,
		$Q310_55_last	,
		$Q310_56_last	,
		$Q310_60_last	,
		$Q310_61_last	,
		$Q310_62_last	,
		$Q310_63_last	,
		$ASSOCIATION_01_last	,
		$ASSOCIATION_02_last	,
		$ASSOCIATION_03_last	,
		$ASSOCIATION_04_last	,
		$ASSOCIATION_05_last	,
		$ASSOCIATION_06_last	,
		$ASSOCIATION_99_last	,
		$TOUCHPOINTS_01_last	,
		$TOUCHPOINTS_02_last	,
		$TOUCHPOINTS_03_last	,
		$TOUCHPOINTS_04_last	,
		$TOUCHPOINTS_05_last	,
		$TOUCHPOINTS_06_last	,
		$TOUCHPOINTS_07_last	,
		$TOUCHPOINTS_08_last	,
		$TOUCHPOINTS_09_last	,
		$TOUCHPOINTS_10_last	,
		$TOUCHPOINTS_11_last	,
		$TOUCHPOINTS_12_last	,
		$TOUCHPOINTS_98_last	,
		$TOUCHPOINTS_99_last	,
		$CHR01_01_last	,
		$CHR01_02_last	,
		$CHR01_99_last	,
		$CHR02_01_last	,
		$CHR02_02_last	,
		$CHR02_99_last	,
		$Q310_64_last	,
		$Q310_65_last	,
		$Q310_66_last	,
		$Q310_67_last	,
		$Q310_68_last	,
		$Q310_69_last	,
		$Q310_70_last	,
		$Q310_71_last	,
		$FK1_1_last	,
		$FK1_2_last	,
		$FK1_3_last	,
		$FK1_4_last	,
		$FK1_5_last	,
		$FK1_6_last	,
		$FK1_7_last	,
		$FK1_97_last	,
		$FK1_99_last	,
		$FK2_1_1_last	,
		$FK2_1_2_last	,
		$FK2_1_3_last	,
		$FK2_1_4_last	,
		$FK2_1_5_last	,
		$FK2_1_6_last	,
		$FK2_1_7_last	,
		$FK2_2_1_last	,
		$FK2_2_2_last	,
		$FK2_2_3_last	,
		$FK2_2_4_last	,
		$FK2_2_5_last	,
		$FK2_2_6_last	,
		$FK2_2_7_last	,
		$AGENDA_13_last	,
		$AGENDA_14_last	,
		$AGENDA_15_last	,
		$AGENDA_16_last	,
		$Q310_72_last	,
		$Q310_73_last	,
		$Q310_74_last	,
		$Q310_75_last	,
		$Q310_76_last	,
		$Q310_77_last	,
		$Q310_78_last	,
		$Q310_79_last	,
		$Q310_80_last	,
		$Q310_81_last	,
		$Q310_82_last	,
		$Q310_83_last	,
		$Q310_84_last	,
		$Q310_85_last	,
		$Q310_86_last	,
		$Q310_87_last	,
		$Q310_88_last	,
		$Q310_89_last	,
		$ZS1_1_1_last	,
		$ZS1_1_2_last	,
		$ZS1_1_3_last	,
		$ZS1_1_4_last	,
		$ZS1_1_5_last	,
		$ZS1_1_6_last	,
		$ZS1_1_7_last	,
		$ZS1_2_1_last	,
		$ZS1_2_2_last	,
		$ZS1_2_3_last	,
		$ZS1_2_4_last	,
		$ZS1_2_5_last	,
		$ZS1_2_6_last	,
		$ZS1_2_7_last	,
		$ZS2_01_last	,
		$ZS2_02_last	,
		$ZS2_03_last	,
		$ZS2_04_last	,
		$ZS2_05_last	,
		$ZS2_06_last	,
		$ZS2_07_last	,
		$ZS2_97_last	,
		$ZS2_99_last	,
		$GN2_01_last	,
		$GN2_02_last	,
		$GN2_03_last	,
		$GN2_04_last	,
		$GN2_05_last	,
		$GN2_97_last	,
		$GN2_99_last	,
		$Q310_23_AGREE_last	,
		$Q310_23_DISAGREE_last	,
		$Q310_23_NEUTRAL_last	,
		$Q310_23_NOTSURE_last	,
		$GN1_1_share_last	,
		$GN1_2_share_last	,
		$GN1_3_share_last	,
		$GN1_4_share_last	,
		$GN1_5_share_last	,
		$GN1_10_share_last	,
		$Q310_90_last	,
		$Q310_91_last	,
		$Q310_92_last	,
		$Q310_93_last	,
		$Q310_94_last	,
		$Q310_95_last	,
		$Q310_96_last	,
		$Q310_97_last	,
		$Q310_98_last	
		
	) = $attribute_last_arr;
	
	$attribute_incr_arr = [];
	for ($j = 0; $j<=$attributes_array_size-1; $j++) 
	{ 
		$incr = $attribute_last_arr[$j] - $attribute_prev_arr[$j];
		
		$sign = $incr > 0 ? '+' : '';
/*-
		if (true  
		   ) // >41 VEOLIA1, ENGAGEMENT, FACILITIES
*/		{
			$attribute_incr_arr[] = 
				$is_benchmark
					? $attribute_last_comp_arr[$j]
					: $sign.$incr.$attribute_percent_suffix
			;
			
		} 
	}
	// return $attribute_incr_arr;
	list(
		$offering_incr	,
		$innovation_incr	,
		$integrity_incr	,
		$leadership_incr	,
		$authenticity_incr	,
		$differentiation_incr	,
		$relevance_incr	,
		$inspiration_incr	,
		$Q310_12_incr	,
		$Q310_13_incr	,
		$Q310_14_incr	,
		$Q310_15_incr	,
		$Q310_16_incr	,
		$Q310_21_incr	,
		$Q310_22_incr	,
		$Q310_23_incr	,
		$Q310_24_incr	,
		$Q310_25_incr	,
		$Q310_26_incr	,
		$Q310_27_incr	,
		$Q310_28_incr	,
		$Q310_29_incr	,
		$Q310_30_incr	,
		$Q310_31_incr	,
		$Q310_32_incr	,
		$Q310_33_incr	,
		$Q310_34_incr	,
		$Q310_35_incr	,
		$Q310_36_incr	,
		$Q310_37_incr	,
		$trust_affection_incr	,
		$Q305_3_01_incr	,
		$consideration_incr	,
		$recommendation_incr	,
		$advocacy_incr	,
		$employment_incr	,
		$Q215_5_incr	,
		$Q215_6_incr	,
		$awareness_incr	,
		$familiarity_incr	,
		$VEOLIA2_1_incr	,
		$VEOLIA2_2_incr	,
		$VEOLIA2_3_incr	,
		$VEOLIA2_4_incr	,
		$VEOLIA1_1_incr	,
		$VEOLIA1_2_incr	,
		$VEOLIA1_3_incr	,
		$VEOLIA1_4_incr	,
		$VEOLIA1_5_incr	,
		$VEOLIA1_6_incr	,
		$VEOLIA1_7_incr	,
		$VEOLIA1_98_incr	,
		$ENGAGEMENT_1_incr	,
		$ENGAGEMENT_2_incr	,
		$ENGAGEMENT_3_incr	,
		$ENGAGEMENT_4_incr	,
		$ENGAGEMENT_5_incr	,
		$ENGAGEMENT_6_incr	,
		$ENGAGEMENT_7_incr	,
		$ENGAGEMENT_8_incr	,
		$FACILITIES_1_incr	,
		$FACILITIES_2_incr	,
		$FACILITIES_3_incr	,
		$FACILITIES_4_incr	,
		$FACILITIES_5_incr	,
		$FACILITIES_98_incr	,
		$FACILITIES_99_incr	,
		$SUSTAINABILITY1_incr	,
		$SUSTAINABILITY2_1_incr	,
		$SUSTAINABILITY2_2_incr	,
		$SUSTAINABILITY2_3_incr	,
		$SUSTAINABILITY2_4_incr	,
		$SUSTAINABILITY2_5_incr	,
		$INITIATIVES_1_incr	,
		$INITIATIVES_2_incr	,
		$INITIATIVES_3_incr	,
		$INITIATIVES_4_incr	,
		$INITIATIVES_5_incr	,
		$INITIATIVES_6_incr	,
		$INITIATIVES_7_incr	,
		$AGENDA_1_incr	,
		$AGENDA_2_incr	,
		$AGENDA_3_incr	,
		$AGENDA_4_incr	,
		$AGENDA_5_incr	,
		$AGENDA_6_incr	,
		$AGENDA_7_incr	,
		$AGENDA_8_incr	,
		$AGENDA_9_incr	,
		$AGENDA_10_incr	,
		$AGENDA_11_incr	,
		$AGENDA_12_incr	,
		$Q215_1_DISAGREE_incr	,
		$Q215_1_NEUTRAL_incr	,
		$Q215_1_AGREE_incr	,
		$Q215_2_DISAGREE_incr	,
		$Q215_2_NEUTRAL_incr	,
		$Q215_2_AGREE_incr	,
		$Q215_3_DISAGREE_incr	,
		$Q215_3_NEUTRAL_incr	,
		$Q215_3_AGREE_incr	,
		$Q215_4_DISAGREE_incr	,
		$Q215_4_NEUTRAL_incr	,
		$Q215_4_AGREE_incr	,
		$ADVOCACY_CRITICAL_incr	,
		$ADVOCACY_NEUTRAL_incr	,
		$ADVOCACY_HIGH_incr	,
		$REPUTATION_SCORE_incr	,
		$PORTFOLIO_incr	,
		$DMI1_1_incr	,
		$DMI1_2_incr	,
		$DMI1_3_incr	,
		$DMI1_4_incr	,
		$DMI1_5_incr	,
		$DMI1_6_incr	,
		$DMI1_7_incr	,
		$DMI1_8_incr	,
		$DMI1_9_incr	,
		$DMI1_10_incr	,
		$DMI1_98_incr	,
		$DMI1_99_incr	,
		$Q310_39_incr	,
		$Q310_40_incr	,
		$Q310_41_incr	,
		$Q310_42_incr	,
		$Q310_43_incr	,
		$Q310_44_incr	,
		$Q310_45_incr	,
		$Q310_46_incr	,
		$Q310_47_incr	,
		$Q310_48_incr	,
		$Q310_49_incr	,
		$Q310_50_incr	,
		$Q310_51_incr	,
		$Q310_52_incr	,
		$Q310_53_incr	,
		$Q310_54_incr	,
		$Q310_55_incr	,
		$Q310_56_incr	,
		$Q310_60_incr	,
		$Q310_61_incr	,
		$Q310_62_incr	,
		$Q310_63_incr	,
		$ASSOCIATION_01_incr	,
		$ASSOCIATION_02_incr	,
		$ASSOCIATION_03_incr	,
		$ASSOCIATION_04_incr	,
		$ASSOCIATION_05_incr	,
		$ASSOCIATION_06_incr	,
		$ASSOCIATION_99_incr	,
		$TOUCHPOINTS_01_incr	,
		$TOUCHPOINTS_02_incr	,
		$TOUCHPOINTS_03_incr	,
		$TOUCHPOINTS_04_incr	,
		$TOUCHPOINTS_05_incr	,
		$TOUCHPOINTS_06_incr	,
		$TOUCHPOINTS_07_incr	,
		$TOUCHPOINTS_08_incr	,
		$TOUCHPOINTS_09_incr	,
		$TOUCHPOINTS_10_incr	,
		$TOUCHPOINTS_11_incr	,
		$TOUCHPOINTS_12_incr	,
		$TOUCHPOINTS_98_incr	,
		$TOUCHPOINTS_99_incr	,
		$CHR01_01_incr	,
		$CHR01_02_incr	,
		$CHR01_99_incr	,
		$CHR02_01_incr	,
		$CHR02_02_incr	,
		$CHR02_99_incr	,
		$Q310_64_incr	,
		$Q310_65_incr	,
		$Q310_66_incr	,
		$Q310_67_incr	,
		$Q310_68_incr	,
		$Q310_69_incr	,
		$Q310_70_incr	,
		$Q310_71_incr	,
		$FK1_1_incr	,
		$FK1_2_incr	,
		$FK1_3_incr	,
		$FK1_4_incr	,
		$FK1_5_incr	,
		$FK1_6_incr	,
		$FK1_7_incr	,
		$FK1_97_incr	,
		$FK1_99_incr	,
		$FK2_1_1_incr	,
		$FK2_1_2_incr	,
		$FK2_1_3_incr	,
		$FK2_1_4_incr	,
		$FK2_1_5_incr	,
		$FK2_1_6_incr	,
		$FK2_1_7_incr	,
		$FK2_2_1_incr	,
		$FK2_2_2_incr	,
		$FK2_2_3_incr	,
		$FK2_2_4_incr	,
		$FK2_2_5_incr	,
		$FK2_2_6_incr	,
		$FK2_2_7_incr	,
		$AGENDA_13_incr	,
		$AGENDA_14_incr	,
		$AGENDA_15_incr ,
		$AGENDA_16_incr	,
		$Q310_72_incr	,
		$Q310_73_incr	,
		$Q310_74_incr	,
		$Q310_75_incr	,
		$Q310_76_incr	,
		$Q310_77_incr	,
		$Q310_78_incr	,
		$Q310_79_incr	,
		$Q310_80_incr	,
		$Q310_81_incr	,
		$Q310_82_incr	,
		$Q310_83_incr	,
		$Q310_84_incr	,
		$Q310_85_incr	,
		$Q310_86_incr	,
		$Q310_87_incr	,
		$Q310_88_incr	,
		$Q310_89_incr	,
		$ZS1_1_1_incr	,
		$ZS1_1_2_incr	,
		$ZS1_1_3_incr	,
		$ZS1_1_4_incr	,
		$ZS1_1_5_incr	,
		$ZS1_1_6_incr	,
		$ZS1_1_7_incr	,
		$ZS1_2_1_incr	,
		$ZS1_2_2_incr	,
		$ZS1_2_3_incr	,
		$ZS1_2_4_incr	,
		$ZS1_2_5_incr	,
		$ZS1_2_6_incr	,
		$ZS1_2_7_incr	,
		$ZS2_01_incr	,
		$ZS2_02_incr	,
		$ZS2_03_incr	,
		$ZS2_04_incr	,
		$ZS2_05_incr	,
		$ZS2_06_incr	,
		$ZS2_07_incr	,
		$ZS2_97_incr	,
		$ZS2_99_incr	,
		$GN2_01_incr	,
		$GN2_02_incr	,
		$GN2_03_incr	,
		$GN2_04_incr	,
		$GN2_05_incr	,
		$GN2_97_incr	,
		$GN2_99_incr	,
		$Q310_23_AGREE_incr	,
		$Q310_23_DISAGREE_incr	,
		$Q310_23_NEUTRAL_incr	,
		$Q310_23_NOTSURE_incr	,
		$GN1_1_share_incr	,
		$GN1_2_share_incr	,
		$GN1_3_share_incr	,
		$GN1_4_share_incr	,
		$GN1_5_share_incr	,
		$GN1_10_share_incr	,
		$Q310_90_incr	,
		$Q310_91_incr	,
		$Q310_92_incr	,
		$Q310_93_incr	,
		$Q310_94_incr	,
		$Q310_95_incr	,
		$Q310_96_incr	,
		$Q310_97_incr	,
		$Q310_98_incr	
		
	) = $attribute_incr_arr;

	
	// define increment arrow image
	$attribute_arrow_img_arr = [];
	for ($j = 0; $j<=$attributes_array_size-1; $j++) {
		$attribute_arrow_img_arr[] = 
			($attribute_incr_arr[$j] == 0 
			 	? "../../images/indicator_circle.png" 
			 	: ($attribute_incr_arr[$j] > 0 
				   	? "../../images/indicator_arrow_up.png" 
				   	: "../../images/indicator_arrow_down.png"
				  )
			)
		;
	}
	// return $attribute_incr_arr;
	list(
		$offering_arrow_img	,
		$innovation_arrow_img	,
		$integrity_arrow_img	,
		$leadership_arrow_img	,
		$authenticity_arrow_img	,
		$differentiation_arrow_img	,
		$relevance_arrow_img	,
		$inspiration_arrow_img	,
		$Q310_12_arrow_img	,
		$Q310_13_arrow_img	,
		$Q310_14_arrow_img	,
		$Q310_15_arrow_img	,
		$Q310_16_arrow_img	,
		$Q310_21_arrow_img	,
		$Q310_22_arrow_img	,
		$Q310_23_arrow_img	,
		$Q310_24_arrow_img	,
		$Q310_25_arrow_img	,
		$Q310_26_arrow_img	,
		$Q310_27_arrow_img	,
		$Q310_28_arrow_img	,
		$Q310_29_arrow_img	,
		$Q310_30_arrow_img	,
		$Q310_31_arrow_img	,
		$Q310_32_arrow_img	,
		$Q310_33_arrow_img	,
		$Q310_34_arrow_img	,
		$Q310_35_arrow_img	,
		$Q310_36_arrow_img	,
		$Q310_37_arrow_img	,
		$trust_affection_arrow_img	,
		$Q305_3_01_arrow_img	,
		$consideration_arrow_img	,
		$recommendation_arrow_img	,
		$advocacy_arrow_img	,
		$employment_arrow_img	,
		$Q215_5_arrow_img	,
		$Q215_6_arrow_img	,
		$awareness_arrow_img	,
		$familiarity_arrow_img	,
		$VEOLIA2_1_arrow_img	,
		$VEOLIA2_2_arrow_img	,
		$VEOLIA2_3_arrow_img	,
		$VEOLIA2_4_arrow_img	,
		$VEOLIA1_1_arrow_img	,
		$VEOLIA1_2_arrow_img	,
		$VEOLIA1_3_arrow_img	,
		$VEOLIA1_4_arrow_img	,
		$VEOLIA1_5_arrow_img	,
		$VEOLIA1_6_arrow_img	,
		$VEOLIA1_7_arrow_img	,
		$VEOLIA1_98_arrow_img	,
		$ENGAGEMENT_1_arrow_img	,
		$ENGAGEMENT_2_arrow_img	,
		$ENGAGEMENT_3_arrow_img	,
		$ENGAGEMENT_4_arrow_img	,
		$ENGAGEMENT_5_arrow_img	,
		$ENGAGEMENT_6_arrow_img	,
		$ENGAGEMENT_7_arrow_img	,
		$ENGAGEMENT_8_arrow_img	,
		$FACILITIES_1_arrow_img	,
		$FACILITIES_2_arrow_img	,
		$FACILITIES_3_arrow_img	,
		$FACILITIES_4_arrow_img	,
		$FACILITIES_5_arrow_img	,
		$FACILITIES_98_arrow_img	,
		$FACILITIES_99_arrow_img	,
		$SUSTAINABILITY1_arrow_img	,
		$SUSTAINABILITY2_1_arrow_img	,
		$SUSTAINABILITY2_2_arrow_img	,
		$SUSTAINABILITY2_3_arrow_img	,
		$SUSTAINABILITY2_4_arrow_img	,
		$SUSTAINABILITY2_5_arrow_img	,
		$INITIATIVES_1_arrow_img	,
		$INITIATIVES_2_arrow_img	,
		$INITIATIVES_3_arrow_img	,
		$INITIATIVES_4_arrow_img	,
		$INITIATIVES_5_arrow_img	,
		$INITIATIVES_6_arrow_img	,
		$INITIATIVES_7_arrow_img	,
		$AGENDA_1_arrow_img	,
		$AGENDA_2_arrow_img	,
		$AGENDA_3_arrow_img	,
		$AGENDA_4_arrow_img	,
		$AGENDA_5_arrow_img	,
		$AGENDA_6_arrow_img	,
		$AGENDA_7_arrow_img	,
		$AGENDA_8_arrow_img	,
		$AGENDA_9_arrow_img	,
		$AGENDA_10_arrow_img	,
		$AGENDA_11_arrow_img	,
		$AGENDA_12_arrow_img	,
		$Q215_1_DISAGREE_arrow_img	,
		$Q215_1_NEUTRAL_arrow_img	,
		$Q215_1_AGREE_arrow_img	,
		$Q215_2_DISAGREE_arrow_img	,
		$Q215_2_NEUTRAL_arrow_img	,
		$Q215_2_AGREE_arrow_img	,
		$Q215_3_DISAGREE_arrow_img	,
		$Q215_3_NEUTRAL_arrow_img	,
		$Q215_3_AGREE_arrow_img	,
		$Q215_4_DISAGREE_arrow_img	,
		$Q215_4_NEUTRAL_arrow_img	,
		$Q215_4_AGREE_arrow_img	,
		$ADVOCACY_CRITICAL_arrow_img	,
		$ADVOCACY_NEUTRAL_arrow_img	,
		$ADVOCACY_HIGH_arrow_img	,
		$REPUTATION_SCORE_arrow_img	,
		$PORTFOLIO_arrow_img	,
		$DMI1_1_arrow_img	,
		$DMI1_2_arrow_img	,
		$DMI1_3_arrow_img	,
		$DMI1_4_arrow_img	,
		$DMI1_5_arrow_img	,
		$DMI1_6_arrow_img	,
		$DMI1_7_arrow_img	,
		$DMI1_8_arrow_img	,
		$DMI1_9_arrow_img	,
		$DMI1_10_arrow_img	,
		$DMI1_98_arrow_img	,
		$DMI1_99_arrow_img	,
		$Q310_39_arrow_img	,
		$Q310_40_arrow_img	,
		$Q310_41_arrow_img	,
		$Q310_42_arrow_img	,
		$Q310_43_arrow_img	,
		$Q310_44_arrow_img	,
		$Q310_45_arrow_img	,
		$Q310_46_arrow_img	,
		$Q310_47_arrow_img	,
		$Q310_48_arrow_img	,
		$Q310_49_arrow_img	,
		$Q310_50_arrow_img	,
		$Q310_51_arrow_img	,
		$Q310_52_arrow_img	,
		$Q310_53_arrow_img	,
		$Q310_54_arrow_img	,
		$Q310_55_arrow_img	,
		$Q310_56_arrow_img	,
		$Q310_60_arrow_img	,
		$Q310_61_arrow_img	,
		$Q310_62_arrow_img	,
		$Q310_63_arrow_img	,
		$ASSOCIATION_01_arrow_img	,
		$ASSOCIATION_02_arrow_img	,
		$ASSOCIATION_03_arrow_img	,
		$ASSOCIATION_04_arrow_img	,
		$ASSOCIATION_05_arrow_img	,
		$ASSOCIATION_06_arrow_img	,
		$ASSOCIATION_99_arrow_img	,
		$TOUCHPOINTS_01_arrow_img	,
		$TOUCHPOINTS_02_arrow_img	,
		$TOUCHPOINTS_03_arrow_img	,
		$TOUCHPOINTS_04_arrow_img	,
		$TOUCHPOINTS_05_arrow_img	,
		$TOUCHPOINTS_06_arrow_img	,
		$TOUCHPOINTS_07_arrow_img	,
		$TOUCHPOINTS_08_arrow_img	,
		$TOUCHPOINTS_09_arrow_img	,
		$TOUCHPOINTS_10_arrow_img	,
		$TOUCHPOINTS_11_arrow_img	,
		$TOUCHPOINTS_12_arrow_img	,
		$TOUCHPOINTS_98_arrow_img	,
		$TOUCHPOINTS_99_arrow_img	,
		$CHR01_01_arrow_img	,
		$CHR01_02_arrow_img	,
		$CHR01_99_arrow_img	,
		$CHR02_01_arrow_img	,
		$CHR02_02_arrow_img	,
		$CHR02_99_arrow_img	,
		$Q310_64_arrow_img	,
		$Q310_65_arrow_img	,
		$Q310_66_arrow_img	,
		$Q310_67_arrow_img	,
		$Q310_68_arrow_img	,
		$Q310_69_arrow_img	,
		$Q310_70_arrow_img	,
		$Q310_71_arrow_img	,
		$FK1_1_arrow_img	,
		$FK1_2_arrow_img	,
		$FK1_3_arrow_img	,
		$FK1_4_arrow_img	,
		$FK1_5_arrow_img	,
		$FK1_6_arrow_img	,
		$FK1_7_arrow_img	,
		$FK1_97_arrow_img	,
		$FK1_99_arrow_img	,
		$FK2_1_1_arrow_img	,
		$FK2_1_2_arrow_img	,
		$FK2_1_3_arrow_img	,
		$FK2_1_4_arrow_img	,
		$FK2_1_5_arrow_img	,
		$FK2_1_6_arrow_img	,
		$FK2_1_7_arrow_img	,
		$FK2_2_1_arrow_img	,
		$FK2_2_2_arrow_img	,
		$FK2_2_3_arrow_img	,
		$FK2_2_4_arrow_img	,
		$FK2_2_5_arrow_img	,
		$FK2_2_6_arrow_img	,
		$FK2_2_7_arrow_img	,
		$AGENDA_13_arrow_img	,
		$AGENDA_14_arrow_img	,
		$AGENDA_15_arrow_img ,
		$AGENDA_16_arrow_img	,
		$Q310_72_arrow_img	,
		$Q310_73_arrow_img	,
		$Q310_74_arrow_img	,
		$Q310_75_arrow_img	,
		$Q310_76_arrow_img	,
		$Q310_77_arrow_img	,
		$Q310_78_arrow_img	,
		$Q310_79_arrow_img	,
		$Q310_80_arrow_img	,
		$Q310_81_arrow_img	,
		$Q310_82_arrow_img	,
		$Q310_83_arrow_img	,
		$Q310_84_arrow_img	,
		$Q310_85_arrow_img	,
		$Q310_86_arrow_img	,
		$Q310_87_arrow_img	,
		$Q310_88_arrow_img	,
		$Q310_89_arrow_img	,
		$ZS1_1_1_arrow_img	,
		$ZS1_1_2_arrow_img	,
		$ZS1_1_3_arrow_img	,
		$ZS1_1_4_arrow_img	,
		$ZS1_1_5_arrow_img	,
		$ZS1_1_6_arrow_img	,
		$ZS1_1_7_arrow_img	,
		$ZS1_2_1_arrow_img	,
		$ZS1_2_2_arrow_img	,
		$ZS1_2_3_arrow_img	,
		$ZS1_2_4_arrow_img	,
		$ZS1_2_5_arrow_img	,
		$ZS1_2_6_arrow_img	,
		$ZS1_2_7_arrow_img	,
		$ZS2_01_arrow_img	,
		$ZS2_02_arrow_img	,
		$ZS2_03_arrow_img	,
		$ZS2_04_arrow_img	,
		$ZS2_05_arrow_img	,
		$ZS2_06_arrow_img	,
		$ZS2_07_arrow_img	,
		$ZS2_97_arrow_img	,
		$ZS2_99_arrow_img	,
		$GN2_01_arrow_img	,
		$GN2_02_arrow_img	,
		$GN2_03_arrow_img	,
		$GN2_04_arrow_img	,
		$GN2_05_arrow_img	,
		$GN2_97_arrow_img	,
		$GN2_99_arrow_img	,
		$Q310_23_AGREE_arrow_img	,
		$Q310_23_DISAGREE_arrow_img	,
		$Q310_23_NEUTRAL_arrow_img	,
		$Q310_23_NOTSURE_arrow_img	,
		$GN1_1_share_arrow_img	,
		$GN1_2_share_arrow_img	,
		$GN1_3_share_arrow_img	,
		$GN1_4_share_arrow_img	,
		$GN1_5_share_arrow_img	,
		$GN1_10_share_arrow_img	,
		$Q310_90_arrow_img	,
		$Q310_91_arrow_img	,
		$Q310_92_arrow_img	,
		$Q310_93_arrow_img	,
		$Q310_94_arrow_img	,
		$Q310_95_arrow_img	,
		$Q310_96_arrow_img	,
		$Q310_97_arrow_img	,
		$Q310_98_arrow_img	
		
	) = $attribute_arrow_img_arr;
	
	// define flag image/color
	
	$j = 0;
	$attribute_last_sliced_arr = array_slice($attribute_last_arr,0,$attributes_array_slice); // 36
	foreach ($attribute_last_sliced_arr as $this_attribute_last) 
	{
		$color_arr[$j] = getAttributeColor($this_attribute_last, false);
		$color_class_arr[$j] = getAttributeColor($this_attribute_last, true);
		
		$j++;	
	}

	list(
		$offering_flag_color	,
		$innovation_flag_color	,
		$integrity_flag_color	,
		$leadership_flag_color	,
		$authenticity_flag_color	,
		$differentiation_flag_color	,
		$relevance_flag_color	,
		$inspiration_flag_color	,
		$Q310_12_flag_color	,
		$Q310_13_flag_color	,
		$Q310_14_flag_color	,
		$Q310_15_flag_color	,
		$Q310_16_flag_color	,
		$Q310_21_flag_color	,
		$Q310_22_flag_color	,
		$Q310_23_flag_color	,
		$Q310_24_flag_color	,
		$Q310_25_flag_color	,
		$Q310_26_flag_color	,
		$Q310_27_flag_color	,
		$Q310_28_flag_color	,
		$Q310_29_flag_color	,
		$Q310_30_flag_color	,
		$Q310_31_flag_color	,
		$Q310_32_flag_color	,
		$Q310_33_flag_color	,
		$Q310_34_flag_color	,
		$Q310_35_flag_color	,
		$Q310_36_flag_color	,
		$Q310_37_flag_color	,
		$trust_affection_flag_color	,
		$Q305_3_01_flag_color	,
		$consideration_flag_color	,
		$recommendation_flag_color	,
		$advocacy_flag_color	,
		$employment_flag_color	,
		$Q215_5_flag_color	,
		$Q215_6_flag_color	,
		$awareness_flag_color	,
		$familiarity_flag_color	,
		$VEOLIA2_1_flag_color	,
		$VEOLIA2_2_flag_color	,
		$VEOLIA2_3_flag_color	,
		$VEOLIA2_4_flag_color	,
		$VEOLIA1_1_flag_color	,
		$VEOLIA1_2_flag_color	,
		$VEOLIA1_3_flag_color	,
		$VEOLIA1_4_flag_color	,
		$VEOLIA1_5_flag_color	,
		$VEOLIA1_6_flag_color	,
		$VEOLIA1_7_flag_color	,
		$VEOLIA1_98_flag_color	,
		$ENGAGEMENT_1_flag_color	,
		$ENGAGEMENT_2_flag_color	,
		$ENGAGEMENT_3_flag_color	,
		$ENGAGEMENT_4_flag_color	,
		$ENGAGEMENT_5_flag_color	,
		$ENGAGEMENT_6_flag_color	,
		$ENGAGEMENT_7_flag_color	,
		$ENGAGEMENT_8_flag_color	,
		$FACILITIES_1_flag_color	,
		$FACILITIES_2_flag_color	,
		$FACILITIES_3_flag_color	,
		$FACILITIES_4_flag_color	,
		$FACILITIES_5_flag_color	,
		$FACILITIES_98_flag_color	,
		$FACILITIES_99_flag_color	,
		$SUSTAINABILITY1_flag_color	,
		$SUSTAINABILITY2_1_flag_color	,
		$SUSTAINABILITY2_2_flag_color	,
		$SUSTAINABILITY2_3_flag_color	,
		$SUSTAINABILITY2_4_flag_color	,
		$SUSTAINABILITY2_5_flag_color	,
		$INITIATIVES_1_flag_color	,
		$INITIATIVES_2_flag_color	,
		$INITIATIVES_3_flag_color	,
		$INITIATIVES_4_flag_color	,
		$INITIATIVES_5_flag_color	,
		$INITIATIVES_6_flag_color	,
		$INITIATIVES_7_flag_color	,
		$AGENDA_1_flag_color	,
		$AGENDA_2_flag_color	,
		$AGENDA_3_flag_color	,
		$AGENDA_4_flag_color	,
		$AGENDA_5_flag_color	,
		$AGENDA_6_flag_color	,
		$AGENDA_7_flag_color	,
		$AGENDA_8_flag_color	,
		$AGENDA_9_flag_color	,
		$AGENDA_10_flag_color	,
		$AGENDA_11_flag_color	,
		$AGENDA_12_flag_color	,
		$Q215_1_DISAGREE_flag_color	,
		$Q215_1_NEUTRAL_flag_color	,
		$Q215_1_AGREE_flag_color	,
		$Q215_2_DISAGREE_flag_color	,
		$Q215_2_NEUTRAL_flag_color	,
		$Q215_2_AGREE_flag_color	,
		$Q215_3_DISAGREE_flag_color	,
		$Q215_3_NEUTRAL_flag_color	,
		$Q215_3_AGREE_flag_color	,
		$Q215_4_DISAGREE_flag_color	,
		$Q215_4_NEUTRAL_flag_color	,
		$Q215_4_AGREE_flag_color	,
		$ADVOCACY_CRITICAL_flag_color	,
		$ADVOCACY_NEUTRAL_flag_color	,
		$ADVOCACY_HIGH_flag_color	,
		$REPUTATION_SCORE_flag_color	,
		$PORTFOLIO_flag_color	,
		$DMI1_1_flag_color	,
		$DMI1_2_flag_color	,
		$DMI1_3_flag_color	,
		$DMI1_4_flag_color	,
		$DMI1_5_flag_color	,
		$DMI1_6_flag_color	,
		$DMI1_7_flag_color	,
		$DMI1_8_flag_color	,
		$DMI1_9_flag_color	,
		$DMI1_10_flag_color	,
		$DMI1_98_flag_color	,
		$DMI1_99_flag_color	,
		$Q310_39_flag_color	,
		$Q310_40_flag_color	,
		$Q310_41_flag_color	,
		$Q310_42_flag_color	,
		$Q310_43_flag_color	,
		$Q310_44_flag_color	,
		$Q310_45_flag_color	,
		$Q310_46_flag_color	,
		$Q310_47_flag_color	,
		$Q310_48_flag_color	,
		$Q310_49_flag_color	,
		$Q310_50_flag_color	,
		$Q310_51_flag_color	,
		$Q310_52_flag_color	,
		$Q310_53_flag_color	,
		$Q310_54_flag_color	,
		$Q310_55_flag_color	,
		$Q310_56_flag_color	,
		$Q310_60_flag_color	,
		$Q310_61_flag_color	,
		$Q310_62_flag_color	,
		$Q310_63_flag_color	,
		$ASSOCIATION_01_flag_color	,
		$ASSOCIATION_02_flag_color	,
		$ASSOCIATION_03_flag_color	,
		$ASSOCIATION_04_flag_color	,
		$ASSOCIATION_05_flag_color	,
		$ASSOCIATION_06_flag_color	,
		$ASSOCIATION_99_flag_color	,
		$TOUCHPOINTS_01_flag_color	,
		$TOUCHPOINTS_02_flag_color	,
		$TOUCHPOINTS_03_flag_color	,
		$TOUCHPOINTS_04_flag_color	,
		$TOUCHPOINTS_05_flag_color	,
		$TOUCHPOINTS_06_flag_color	,
		$TOUCHPOINTS_07_flag_color	,
		$TOUCHPOINTS_08_flag_color	,
		$TOUCHPOINTS_09_flag_color	,
		$TOUCHPOINTS_10_flag_color	,
		$TOUCHPOINTS_11_flag_color	,
		$TOUCHPOINTS_12_flag_color	,
		$TOUCHPOINTS_98_flag_color	,
		$TOUCHPOINTS_99_flag_color	,
		$CHR01_01_flag_color	,
		$CHR01_02_flag_color	,
		$CHR01_99_flag_color	,
		$CHR02_01_flag_color	,
		$CHR02_02_flag_color	,
		$CHR02_99_flag_color	,
		$Q310_64_flag_color	,
		$Q310_65_flag_color	,
		$Q310_66_flag_color	,
		$Q310_67_flag_color	,
		$Q310_68_flag_color	,
		$Q310_69_flag_color	,
		$Q310_70_flag_color	,
		$Q310_71_flag_color	,
		$FK1_1_flag_color	,
		$FK1_2_flag_color	,
		$FK1_3_flag_color	,
		$FK1_4_flag_color	,
		$FK1_5_flag_color	,
		$FK1_6_flag_color	,
		$FK1_7_flag_color	,
		$FK1_97_flag_color	,
		$FK1_99_flag_color	,
		$FK2_1_1_flag_color	,
		$FK2_1_2_flag_color	,
		$FK2_1_3_flag_color	,
		$FK2_1_4_flag_color	,
		$FK2_1_5_flag_color	,
		$FK2_1_6_flag_color	,
		$FK2_1_7_flag_color	,
		$FK2_2_1_flag_color	,
		$FK2_2_2_flag_color	,
		$FK2_2_3_flag_color	,
		$FK2_2_4_flag_color	,
		$FK2_2_5_flag_color	,
		$FK2_2_6_flag_color	,
		$FK2_2_7_flag_color	,
		$AGENDA_13_flag_color	,
		$AGENDA_14_flag_color	,
		$AGENDA_15_flag_color ,
		$AGENDA_16_flag_color	,
		$Q310_72_flag_color	,
		$Q310_73_flag_color	,
		$Q310_74_flag_color	,
		$Q310_75_flag_color	,
		$Q310_76_flag_color	,
		$Q310_77_flag_color	,
		$Q310_78_flag_color	,
		$Q310_79_flag_color	,
		$Q310_80_flag_color	,
		$Q310_81_flag_color	,
		$Q310_82_flag_color	,
		$Q310_83_flag_color	,
		$Q310_84_flag_color	,
		$Q310_85_flag_color	,
		$Q310_86_flag_color	,
		$Q310_87_flag_color	,
		$Q310_88_flag_color	,
		$Q310_89_flag_color	,
		$ZS1_1_1_flag_color	,
		$ZS1_1_2_flag_color	,
		$ZS1_1_3_flag_color	,
		$ZS1_1_4_flag_color	,
		$ZS1_1_5_flag_color	,
		$ZS1_1_6_flag_color	,
		$ZS1_1_7_flag_color	,
		$ZS1_2_1_flag_color	,
		$ZS1_2_2_flag_color	,
		$ZS1_2_3_flag_color	,
		$ZS1_2_4_flag_color	,
		$ZS1_2_5_flag_color	,
		$ZS1_2_6_flag_color	,
		$ZS1_2_7_flag_color	,
		$ZS2_01_flag_color	,
		$ZS2_02_flag_color	,
		$ZS2_03_flag_color	,
		$ZS2_04_flag_color	,
		$ZS2_05_flag_color	,
		$ZS2_06_flag_color	,
		$ZS2_07_flag_color	,
		$ZS2_97_flag_color	,
		$ZS2_99_flag_color	,
		$GN2_01_flag_color	,
		$GN2_02_flag_color	,
		$GN2_03_flag_color	,
		$GN2_04_flag_color	,
		$GN2_05_flag_color	,
		$GN2_97_flag_color	,
		$GN2_99_flag_color	,
		$Q310_23_AGREE_flag_color	,
		$Q310_23_DISAGREE_flag_color	,
		$Q310_23_NEUTRAL_flag_color	,
		$Q310_23_NOTSURE_flag_color	,
		$GN1_1_share_flag_color	,
		$GN1_2_share_flag_color	,
		$GN1_3_share_flag_color	,
		$GN1_4_share_flag_color	,
		$GN1_5_share_flag_color	,
		$GN1_10_share_flag_color,
		$Q310_90_flag_color	,
		$Q310_91_flag_color	,
		$Q310_92_flag_color	,
		$Q310_93_flag_color	,
		$Q310_94_flag_color	,
		$Q310_95_flag_color	,
		$Q310_96_flag_color	,
		$Q310_97_flag_color	,
		$Q310_98_flag_color	
	) = $color_arr;

	list(
		$offering_color_class	,
		$innovation_color_class	,
		$integrity_color_class	,
		$leadership_color_class	,
		$authenticity_color_class	,
		$differentiation_color_class	,
		$relevance_color_class	,
		$inspiration_color_class	,
		$Q310_12_color_class	,
		$Q310_13_color_class	,
		$Q310_14_color_class	,
		$Q310_15_color_class	,
		$Q310_16_color_class	,
		$Q310_21_color_class	,
		$Q310_22_color_class	,
		$Q310_23_color_class	,
		$Q310_24_color_class	,
		$Q310_25_color_class	,
		$Q310_26_color_class	,
		$Q310_27_color_class	,
		$Q310_28_color_class	,
		$Q310_29_color_class	,
		$Q310_30_color_class	,
		$Q310_31_color_class	,
		$Q310_32_color_class	,
		$Q310_33_color_class	,
		$Q310_34_color_class	,
		$Q310_35_color_class	,
		$Q310_36_color_class	,
		$Q310_37_color_class	,
		$trust_affection_color_class	,
		$Q305_3_01_color_class	,
		$consideration_color_class	,
		$recommendation_color_class	,
		$advocacy_color_class	,
		$employment_color_class	,
		$Q215_5_color_class	,
		$Q215_6_color_class	,
		$awareness_color_class	,
		$familiarity_color_class	,
		$VEOLIA2_1_color_class	,
		$VEOLIA2_2_color_class	,
		$VEOLIA2_3_color_class	,
		$VEOLIA2_4_color_class	,
		$VEOLIA1_1_color_class	,
		$VEOLIA1_2_color_class	,
		$VEOLIA1_3_color_class	,
		$VEOLIA1_4_color_class	,
		$VEOLIA1_5_color_class	,
		$VEOLIA1_6_color_class	,
		$VEOLIA1_7_color_class	,
		$VEOLIA1_98_color_class	,
		$ENGAGEMENT_1_color_class	,
		$ENGAGEMENT_2_color_class	,
		$ENGAGEMENT_3_color_class	,
		$ENGAGEMENT_4_color_class	,
		$ENGAGEMENT_5_color_class	,
		$ENGAGEMENT_6_color_class	,
		$ENGAGEMENT_7_color_class	,
		$ENGAGEMENT_8_color_class	,
		$FACILITIES_1_color_class	,
		$FACILITIES_2_color_class	,
		$FACILITIES_3_color_class	,
		$FACILITIES_4_color_class	,
		$FACILITIES_5_color_class	,
		$FACILITIES_98_color_class	,
		$FACILITIES_99_color_class	,
		$SUSTAINABILITY1_color_class	,
		$SUSTAINABILITY2_1_color_class	,
		$SUSTAINABILITY2_2_color_class	,
		$SUSTAINABILITY2_3_color_class	,
		$SUSTAINABILITY2_4_color_class	,
		$SUSTAINABILITY2_5_color_class	,
		$INITIATIVES_1_color_class	,
		$INITIATIVES_2_color_class	,
		$INITIATIVES_3_color_class	,
		$INITIATIVES_4_color_class	,
		$INITIATIVES_5_color_class	,
		$INITIATIVES_6_color_class	,
		$INITIATIVES_7_color_class	,
		$AGENDA_1_color_class	,
		$AGENDA_2_color_class	,
		$AGENDA_3_color_class	,
		$AGENDA_4_color_class	,
		$AGENDA_5_color_class	,
		$AGENDA_6_color_class	,
		$AGENDA_7_color_class	,
		$AGENDA_8_color_class	,
		$AGENDA_9_color_class	,
		$AGENDA_10_color_class	,
		$AGENDA_11_color_class	,
		$AGENDA_12_color_class	,
		$Q215_1_DISAGREE_color_class	,
		$Q215_1_NEUTRAL_color_class	,
		$Q215_1_AGREE_color_class	,
		$Q215_2_DISAGREE_color_class	,
		$Q215_2_NEUTRAL_color_class	,
		$Q215_2_AGREE_color_class	,
		$Q215_3_DISAGREE_color_class	,
		$Q215_3_NEUTRAL_color_class	,
		$Q215_3_AGREE_color_class	,
		$Q215_4_DISAGREE_color_class	,
		$Q215_4_NEUTRAL_color_class	,
		$Q215_4_AGREE_color_class	,
		$ADVOCACY_CRITICAL_color_class	,
		$ADVOCACY_NEUTRAL_color_class	,
		$ADVOCACY_HIGH_color_class	,
		$REPUTATION_SCORE_color_class	,
		$PORTFOLIO_color_class	,
		$DMI1_1_color_class	,
		$DMI1_2_color_class	,
		$DMI1_3_color_class	,
		$DMI1_4_color_class	,
		$DMI1_5_color_class	,
		$DMI1_6_color_class	,
		$DMI1_7_color_class	,
		$DMI1_8_color_class	,
		$DMI1_9_color_class	,
		$DMI1_10_color_class	,
		$DMI1_98_color_class	,
		$DMI1_99_color_class	,
		$Q310_39_color_class	,
		$Q310_40_color_class	,
		$Q310_41_color_class	,
		$Q310_42_color_class	,
		$Q310_43_color_class	,
		$Q310_44_color_class	,
		$Q310_45_color_class	,
		$Q310_46_color_class	,
		$Q310_47_color_class	,
		$Q310_48_color_class	,
		$Q310_49_color_class	,
		$Q310_50_color_class	,
		$Q310_51_color_class	,
		$Q310_52_color_class	,
		$Q310_53_color_class	,
		$Q310_54_color_class	,
		$Q310_55_color_class	,
		$Q310_56_color_class	,
		$Q310_60_color_class	,
		$Q310_61_color_class	,
		$Q310_62_color_class	,
		$Q310_63_color_class	,
		$ASSOCIATION_01_color_class	,
		$ASSOCIATION_02_color_class	,
		$ASSOCIATION_03_color_class	,
		$ASSOCIATION_04_color_class	,
		$ASSOCIATION_05_color_class	,
		$ASSOCIATION_06_color_class	,
		$ASSOCIATION_99_color_class	,
		$TOUCHPOINTS_01_color_class	,
		$TOUCHPOINTS_02_color_class	,
		$TOUCHPOINTS_03_color_class	,
		$TOUCHPOINTS_04_color_class	,
		$TOUCHPOINTS_05_color_class	,
		$TOUCHPOINTS_06_color_class	,
		$TOUCHPOINTS_07_color_class	,
		$TOUCHPOINTS_08_color_class	,
		$TOUCHPOINTS_09_color_class	,
		$TOUCHPOINTS_10_color_class	,
		$TOUCHPOINTS_11_color_class	,
		$TOUCHPOINTS_12_color_class	,
		$TOUCHPOINTS_98_color_class	,
		$TOUCHPOINTS_99_color_class	,
		$CHR01_01_color_class	,
		$CHR01_02_color_class	,
		$CHR01_99_color_class	,
		$CHR02_01_color_class	,
		$CHR02_02_color_class	,
		$CHR02_99_color_class	,
		$Q310_64_color_class	,
		$Q310_65_color_class	,
		$Q310_66_color_class	,
		$Q310_67_color_class	,
		$Q310_68_color_class	,
		$Q310_69_color_class	,
		$Q310_70_color_class	,
		$Q310_71_color_class	,
		$FK1_1_color_class	,
		$FK1_2_color_class	,
		$FK1_3_color_class	,
		$FK1_4_color_class	,
		$FK1_5_color_class	,
		$FK1_6_color_class	,
		$FK1_7_color_class	,
		$FK1_97_color_class	,
		$FK1_99_color_class	,
		$FK2_1_1_color_class	,
		$FK2_1_2_color_class	,
		$FK2_1_3_color_class	,
		$FK2_1_4_color_class	,
		$FK2_1_5_color_class	,
		$FK2_1_6_color_class	,
		$FK2_1_7_color_class	,
		$FK2_2_1_color_class	,
		$FK2_2_2_color_class	,
		$FK2_2_3_color_class	,
		$FK2_2_4_color_class	,
		$FK2_2_5_color_class	,
		$FK2_2_6_color_class	,
		$FK2_2_7_color_class	,
		$AGENDA_13_color_class	,
		$AGENDA_14_color_class	,
		$AGENDA_15_color_class ,
		$AGENDA_16_color_class	,
		$Q310_72_color_class	,
		$Q310_73_color_class	,
		$Q310_74_color_class	,
		$Q310_75_color_class	,
		$Q310_76_color_class	,
		$Q310_77_color_class	,
		$Q310_78_color_class	,
		$Q310_79_color_class	,
		$Q310_80_color_class	,
		$Q310_81_color_class	,
		$Q310_82_color_class	,
		$Q310_83_color_class	,
		$Q310_84_color_class	,
		$Q310_85_color_class	,
		$Q310_86_color_class	,
		$Q310_87_color_class	,
		$Q310_88_color_class	,
		$Q310_89_color_class	,
		$ZS1_1_1_color_class	,
		$ZS1_1_2_color_class	,
		$ZS1_1_3_color_class	,
		$ZS1_1_4_color_class	,
		$ZS1_1_5_color_class	,
		$ZS1_1_6_color_class	,
		$ZS1_1_7_color_class	,
		$ZS1_2_1_color_class	,
		$ZS1_2_2_color_class	,
		$ZS1_2_3_color_class	,
		$ZS1_2_4_color_class	,
		$ZS1_2_5_color_class	,
		$ZS1_2_6_color_class	,
		$ZS1_2_7_color_class	,
		$ZS2_01_color_class	,
		$ZS2_02_color_class	,
		$ZS2_03_color_class	,
		$ZS2_04_color_class	,
		$ZS2_05_color_class	,
		$ZS2_06_color_class	,
		$ZS2_07_color_class	,
		$ZS2_97_color_class	,
		$ZS2_99_color_class	,
		$GN2_01_color_class	,
		$GN2_02_color_class	,
		$GN2_03_color_class	,
		$GN2_04_color_class	,
		$GN2_05_color_class	,
		$GN2_97_color_class	,
		$GN2_99_color_class	,
		$Q310_23_AGREE_color_class	,
		$Q310_23_DISAGREE_color_class	,
		$Q310_23_NEUTRAL_color_class	,
		$Q310_23_NOTSURE_color_class	,
		$GN1_1_share_color_class	,
		$GN1_2_share_color_class	,
		$GN1_3_share_color_class	,
		$GN1_4_share_color_class	,
		$GN1_5_share_color_class	,
		$GN1_10_share_color_class	,
		$Q310_90_color_class	,
		$Q310_91_color_class	,
		$Q310_92_color_class	,
		$Q310_93_color_class	,
		$Q310_94_color_class	,
		$Q310_95_color_class	,
		$Q310_96_color_class	,
		$Q310_97_color_class	,
		$Q310_98_color_class	
		
	) = $color_class_arr;

	// VEOLIA1 new algo
	
		if ($responce_count_prev==0) { $responce_count_prev = 1; }
		if ($responce_count_trust_affection==0) { $responce_count_trust_affection = 1; }
	
	if (false && $is_benchmark)
	{	
	
	} 
		$img_path = '../../images/';
		$img_0 = 'indicator_circle.png';
		$img_up = 'indicator_arrow_up.png';
		$img_down = 'indicator_arrow_down.png';
			
	if ($is_benchmark)
	{
		$VEOLIA1_1_arrow_img = 
		$VEOLIA1_2_arrow_img = 
		$VEOLIA1_3_arrow_img = 
		$VEOLIA1_4_arrow_img = 
		$VEOLIA1_5_arrow_img = 
		$VEOLIA1_6_arrow_img = 
		$VEOLIA1_7_arrow_img = 
		$VEOLIA1_98_arrow_img = '';

		$ENGAGEMENT_1_arrow_img = 
		$ENGAGEMENT_2_arrow_img = 
		$ENGAGEMENT_3_arrow_img = 
		$ENGAGEMENT_4_arrow_img = 
		$ENGAGEMENT_5_arrow_img = 
		$ENGAGEMENT_6_arrow_img = 
		$ENGAGEMENT_7_arrow_img = 
		$ENGAGEMENT_8_arrow_img = 
		$ENGAGEMENT_97_arrow_img = 
		$ENGAGEMENT_99_arrow_img = ''; //$img_path.$img_0;
	
		$FACILITIES_1_arrow_img = 
		$FACILITIES_2_arrow_img = 
		$FACILITIES_3_arrow_img = 
		$FACILITIES_4_arrow_img = 
		$FACILITIES_5_arrow_img = 
		$FACILITIES_98_arrow_img = 
		$FACILITIES_99_arrow_img = ''; //$img_path.$img_0;
		
	} else {
		
		$VEOLIA1_1_arrow_img = $img_path.($VEOLIA1_1_incr==0 ? $img_0 : ($VEOLIA1_1_incr>0 ? $img_up : $img_down));
		$VEOLIA1_2_arrow_img = $img_path.($VEOLIA1_2_incr==0 ? $img_0 : ($VEOLIA1_2_incr>0 ? $img_up : $img_down));
		$VEOLIA1_3_arrow_img = $img_path.($VEOLIA1_3_incr==0 ? $img_0 : ($VEOLIA1_3_incr>0 ? $img_up : $img_down));
		$VEOLIA1_4_arrow_img = $img_path.($VEOLIA1_4_incr==0 ? $img_0 : ($VEOLIA1_4_incr>0 ? $img_up : $img_down));
		$VEOLIA1_5_arrow_img = $img_path.($VEOLIA1_5_incr==0 ? $img_0 : ($VEOLIA1_5_incr>0 ? $img_up : $img_down));
		$VEOLIA1_6_arrow_img = $img_path.($VEOLIA1_6_incr==0 ? $img_0 : ($VEOLIA1_6_incr>0 ? $img_up : $img_down));
		$VEOLIA1_7_arrow_img = $img_path.($VEOLIA1_7_incr==0 ? $img_0 : ($VEOLIA1_7_incr>0 ? $img_up : $img_down));
		$VEOLIA1_98_arrow_img = $img_path.($VEOLIA1_98_incr==0 ? $img_0 : ($VEOLIA1_98_incr>0 ? $img_up : $img_down));
		
		$ENGAGEMENT_1_arrow_img = $img_path.($ENGAGEMENT_1_incr==0 ? $img_0 : ($ENGAGEMENT_1_incr>0 ? $img_up : $img_down));
		$ENGAGEMENT_2_arrow_img = $img_path.($ENGAGEMENT_2_incr==0 ? $img_0 : ($ENGAGEMENT_2_incr>0 ? $img_up : $img_down));
		$ENGAGEMENT_3_arrow_img = $img_path.($ENGAGEMENT_3_incr==0 ? $img_0 : ($ENGAGEMENT_3_incr>0 ? $img_up : $img_down));
		$ENGAGEMENT_4_arrow_img = $img_path.($ENGAGEMENT_4_incr==0 ? $img_0 : ($ENGAGEMENT_4_incr>0 ? $img_up : $img_down));
		$ENGAGEMENT_5_arrow_img = $img_path.($ENGAGEMENT_5_incr==0 ? $img_0 : ($ENGAGEMENT_5_incr>0 ? $img_up : $img_down));
		$ENGAGEMENT_6_arrow_img = $img_path.($ENGAGEMENT_6_incr==0 ? $img_0 : ($ENGAGEMENT_6_incr>0 ? $img_up : $img_down));
		$ENGAGEMENT_7_arrow_img = $img_path.($ENGAGEMENT_7_incr==0 ? $img_0 : ($ENGAGEMENT_7_incr>0 ? $img_up : $img_down));
		$ENGAGEMENT_8_arrow_img = $img_path.($ENGAGEMENT_8_incr==0 ? $img_0 : ($ENGAGEMENT_8_incr>0 ? $img_up : $img_down));
		//		$ENGAGEMENT_97_arrow_img = $img_path.($ENGAGEMENT_97_incr==0 ? $img_0 : ($ENGAGEMENT_97_incr>0 ? $img_up : $img_down));
		//		$ENGAGEMENT_99_arrow_img = $img_path.($ENGAGEMENT_99_incr==0 ? $img_0 : ($ENGAGEMENT_99_incr>0 ? $img_up : $img_down));
	
		$FACILITIES_1_arrow_img = $img_path.($FACILITIES_1_incr==0 ? $img_0 : ($FACILITIES_1_incr>0 ? $img_up : $img_down));
		$FACILITIES_2_arrow_img = $img_path.($FACILITIES_2_incr==0 ? $img_0 : ($FACILITIES_2_incr>0 ? $img_up : $img_down));
		$FACILITIES_3_arrow_img = $img_path.($FACILITIES_3_incr==0 ? $img_0 : ($FACILITIES_3_incr>0 ? $img_up : $img_down));
		$FACILITIES_4_arrow_img = $img_path.($FACILITIES_4_incr==0 ? $img_0 : ($FACILITIES_4_incr>0 ? $img_up : $img_down));
		$FACILITIES_5_arrow_img = $img_path.($FACILITIES_5_incr==0 ? $img_0 : ($FACILITIES_5_incr>0 ? $img_up : $img_down));
		$FACILITIES_98_arrow_img = $img_path.($FACILITIES_98_incr==0 ? $img_0 : ($FACILITIES_98_incr>0 ? $img_up : $img_down));
		$FACILITIES_99_arrow_img = $img_path.($FACILITIES_99_incr==0 ? $img_0 : ($FACILITIES_99_incr>0 ? $img_up : $img_down));
		
		$DMI1_1_arrow_img = $img_path.($DMI1_1_incr==0 ? $img_0 : ($DMI1_1_incr>0 ? $img_up : $img_down));
        $DMI1_2_arrow_img = $img_path.($DMI1_2_incr==0 ? $img_0 : ($DMI1_2_incr>0 ? $img_up : $img_down));
        $DMI1_3_arrow_img = $img_path.($DMI1_3_incr==0 ? $img_0 : ($DMI1_3_incr>0 ? $img_up : $img_down));	
        $DMI1_4_arrow_img = $img_path.($DMI1_4_incr==0 ? $img_0 : ($DMI1_4_incr>0 ? $img_up : $img_down));	
        $DMI1_5_arrow_img = $img_path.($DMI1_5_incr==0 ? $img_0 : ($DMI1_5_incr>0 ? $img_up : $img_down));	
        $DMI1_6_arrow_img = $img_path.($DMI1_6_incr==0 ? $img_0 : ($DMI1_6_incr>0 ? $img_up : $img_down));	
        $DMI1_7_arrow_img = $img_path.($DMI1_7_incr==0 ? $img_0 : ($DMI1_7_incr>0 ? $img_up : $img_down));
        $DMI1_8_arrow_img = $img_path.($DMI1_8_incr==0 ? $img_0 : ($DMI1_8_incr>0 ? $img_up : $img_down));	
        $DMI1_9_arrow_img = $img_path.($DMI1_9_incr==0 ? $img_0 : ($DMI1_9_incr>0 ? $img_up : $img_down));	
        $DMI1_10_arrow_img = $img_path.($DMI1_10_incr==0 ? $img_0 : ($DMI1_10_incr>0 ? $img_up : $img_down));
        $DMI1_98_arrow_img = $img_path.($DMI1_98_incr==0 ? $img_0 : ($DMI1_98_incr>0 ? $img_up : $img_down));	
        $DMI1_99_arrow_img = $img_path.($DMI1_99_incr==0 ? $img_0 : ($DMI1_99_incr>0 ? $img_up : $img_down));
		
		$Q310_39_arrow_img = $img_path.($Q310_39_incr==0 ? $img_0 : ($Q310_39_incr>0 ? $img_up : $img_down));
		$Q310_40_arrow_img = $img_path.($Q310_40_incr==0 ? $img_0 : ($Q310_40_incr>0 ? $img_up : $img_down));
		$Q310_41_arrow_img = $img_path.($Q310_41_incr==0 ? $img_0 : ($Q310_41_incr>0 ? $img_up : $img_down));
		$Q310_42_arrow_img = $img_path.($Q310_42_incr==0 ? $img_0 : ($Q310_42_incr>0 ? $img_up : $img_down));
		$Q310_43_arrow_img = $img_path.($Q310_43_incr==0 ? $img_0 : ($Q310_43_incr>0 ? $img_up : $img_down));
		$Q310_44_arrow_img = $img_path.($Q310_44_incr==0 ? $img_0 : ($Q310_44_incr>0 ? $img_up : $img_down));
		$Q310_45_arrow_img = $img_path.($Q310_45_incr==0 ? $img_0 : ($Q310_45_incr>0 ? $img_up : $img_down));
		$Q310_46_arrow_img = $img_path.($Q310_46_incr==0 ? $img_0 : ($Q310_46_incr>0 ? $img_up : $img_down));
		$Q310_47_arrow_img = $img_path.($Q310_47_incr==0 ? $img_0 : ($Q310_47_incr>0 ? $img_up : $img_down));
		$Q310_48_arrow_img = $img_path.($Q310_48_incr==0 ? $img_0 : ($Q310_48_incr>0 ? $img_up : $img_down));
		$Q310_49_arrow_img = $img_path.($Q310_49_incr==0 ? $img_0 : ($Q310_49_incr>0 ? $img_up : $img_down));
		
		$Q310_50_arrow_img = $img_path.($Q310_50_incr==0 ? $img_0 : ($Q310_50_incr>0 ? $img_up : $img_down));
		$Q310_51_arrow_img = $img_path.($Q310_51_incr==0 ? $img_0 : ($Q310_51_incr>0 ? $img_up : $img_down));
		$Q310_52_arrow_img = $img_path.($Q310_52_incr==0 ? $img_0 : ($Q310_52_incr>0 ? $img_up : $img_down));
		$Q310_53_arrow_img = $img_path.($Q310_53_incr==0 ? $img_0 : ($Q310_53_incr>0 ? $img_up : $img_down));
		$Q310_54_arrow_img = $img_path.($Q310_54_incr==0 ? $img_0 : ($Q310_54_incr>0 ? $img_up : $img_down));
		$Q310_55_arrow_img = $img_path.($Q310_55_incr==0 ? $img_0 : ($Q310_55_incr>0 ? $img_up : $img_down));
		$Q310_56_arrow_img = $img_path.($Q310_56_incr==0 ? $img_0 : ($Q310_56_incr>0 ? $img_up : $img_down));
		
		$TOUCHPOINTS_01_arrow_img = $img_path.($TOUCHPOINTS_01_incr==0 ? $img_0 : ($TOUCHPOINTS_01_incr>0 ? $img_up : $img_down));
        $TOUCHPOINTS_02_arrow_img = $img_path.($TOUCHPOINTS_02_incr==0 ? $img_0 : ($TOUCHPOINTS_02_incr>0 ? $img_up : $img_down));
        $TOUCHPOINTS_03_arrow_img = $img_path.($TOUCHPOINTS_03_incr==0 ? $img_0 : ($TOUCHPOINTS_03_incr>0 ? $img_up : $img_down));	
        $TOUCHPOINTS_04_arrow_img = $img_path.($TOUCHPOINTS_04_incr==0 ? $img_0 : ($TOUCHPOINTS_04_incr>0 ? $img_up : $img_down));	
        $TOUCHPOINTS_05_arrow_img = $img_path.($TOUCHPOINTS_05_incr==0 ? $img_0 : ($TOUCHPOINTS_05_incr>0 ? $img_up : $img_down));	
        $TOUCHPOINTS_06_arrow_img = $img_path.($TOUCHPOINTS_06_incr==0 ? $img_0 : ($TOUCHPOINTS_06_incr>0 ? $img_up : $img_down));	
        $TOUCHPOINTS_07_arrow_img = $img_path.($TOUCHPOINTS_07_incr==0 ? $img_0 : ($TOUCHPOINTS_07_incr>0 ? $img_up : $img_down));
        $TOUCHPOINTS_08_arrow_img = $img_path.($TOUCHPOINTS_08_incr==0 ? $img_0 : ($TOUCHPOINTS_08_incr>0 ? $img_up : $img_down));	
        $TOUCHPOINTS_09_arrow_img = $img_path.($TOUCHPOINTS_09_incr==0 ? $img_0 : ($TOUCHPOINTS_09_incr>0 ? $img_up : $img_down));	
        $TOUCHPOINTS_10_arrow_img = $img_path.($TOUCHPOINTS_10_incr==0 ? $img_0 : ($TOUCHPOINTS_10_incr>0 ? $img_up : $img_down));
		$TOUCHPOINTS_11_arrow_img = $img_path.($TOUCHPOINTS_11_incr==0 ? $img_0 : ($TOUCHPOINTS_11_incr>0 ? $img_up : $img_down));
		$TOUCHPOINTS_12_arrow_img = $img_path.($TOUCHPOINTS_12_incr==0 ? $img_0 : ($TOUCHPOINTS_12_incr>0 ? $img_up : $img_down));
        $TOUCHPOINTS_98_arrow_img = $img_path.($TOUCHPOINTS_98_incr==0 ? $img_0 : ($TOUCHPOINTS_98_incr>0 ? $img_up : $img_down));	
        $TOUCHPOINTS_99_arrow_img = $img_path.($TOUCHPOINTS_99_incr==0 ? $img_0 : ($TOUCHPOINTS_99_incr>0 ? $img_up : $img_down));
		
		$ASSOCIATION_01_arrow_img = $img_path.($ASSOCIATION_01_incr==0 ? $img_0 : ($ASSOCIATION_01_incr>0 ? $img_up : $img_down));
        $ASSOCIATION_02_arrow_img = $img_path.($ASSOCIATION_02_incr==0 ? $img_0 : ($ASSOCIATION_02_incr>0 ? $img_up : $img_down));
        $ASSOCIATION_03_arrow_img = $img_path.($ASSOCIATION_03_incr==0 ? $img_0 : ($ASSOCIATION_03_incr>0 ? $img_up : $img_down));	
        $ASSOCIATION_04_arrow_img = $img_path.($ASSOCIATION_04_incr==0 ? $img_0 : ($ASSOCIATION_04_incr>0 ? $img_up : $img_down));	
        $ASSOCIATION_05_arrow_img = $img_path.($ASSOCIATION_05_incr==0 ? $img_0 : ($ASSOCIATION_05_incr>0 ? $img_up : $img_down));	
        $ASSOCIATION_06_arrow_img = $img_path.($ASSOCIATION_06_incr==0 ? $img_0 : ($ASSOCIATION_06_incr>0 ? $img_up : $img_down));	
        $ASSOCIATION_99_arrow_img = $img_path.($ASSOCIATION_99_incr==0 ? $img_0 : ($ASSOCIATION_99_incr>0 ? $img_up : $img_down));
		
		$FK1_1_arrow_img = $img_path.($FK1_1_incr==0 ? $img_0 : ($FK1_1_incr>0 ? $img_up : $img_down));
        $FK1_2_arrow_img = $img_path.($FK1_2_incr==0 ? $img_0 : ($FK1_2_incr>0 ? $img_up : $img_down));
        $FK1_3_arrow_img = $img_path.($FK1_3_incr==0 ? $img_0 : ($FK1_3_incr>0 ? $img_up : $img_down));	
        $FK1_4_arrow_img = $img_path.($FK1_4_incr==0 ? $img_0 : ($FK1_4_incr>0 ? $img_up : $img_down));	
        $FK1_5_arrow_img = $img_path.($FK1_5_incr==0 ? $img_0 : ($FK1_5_incr>0 ? $img_up : $img_down));	
        $FK1_6_arrow_img = $img_path.($FK1_6_incr==0 ? $img_0 : ($FK1_6_incr>0 ? $img_up : $img_down));	
		$FK1_7_arrow_img = $img_path.($FK1_7_incr==0 ? $img_0 : ($FK1_7_incr>0 ? $img_up : $img_down));	
		$FK1_97_arrow_img = $img_path.($FK1_97_incr==0 ? $img_0 : ($FK1_97_incr>0 ? $img_up : $img_down));	
        $FK1_99_arrow_img = $img_path.($FK1_99_incr==0 ? $img_0 : ($FK1_99_incr>0 ? $img_up : $img_down));
		
		$ZS2_01_arrow_img = $img_path.($ZS2_01_incr==0 ? $img_0 : ($ZS2_01_incr>0 ? $img_up : $img_down));
        $ZS2_02_arrow_img = $img_path.($ZS2_02_incr==0 ? $img_0 : ($ZS2_02_incr>0 ? $img_up : $img_down));
		$ZS2_03_arrow_img = $img_path.($ZS2_03_incr==0 ? $img_0 : ($ZS2_03_incr>0 ? $img_up : $img_down));
		$ZS2_04_arrow_img = $img_path.($ZS2_04_incr==0 ? $img_0 : ($ZS2_04_incr>0 ? $img_up : $img_down));
		$ZS2_05_arrow_img = $img_path.($ZS2_05_incr==0 ? $img_0 : ($ZS2_05_incr>0 ? $img_up : $img_down));
		$ZS2_06_arrow_img = $img_path.($ZS2_06_incr==0 ? $img_0 : ($ZS2_06_incr>0 ? $img_up : $img_down));
		$ZS2_07_arrow_img = $img_path.($ZS2_07_incr==0 ? $img_0 : ($ZS2_07_incr>0 ? $img_up : $img_down));
		$ZS2_97_arrow_img = $img_path.($ZS2_97_incr==0 ? $img_0 : ($ZS2_97_incr>0 ? $img_up : $img_down));
		$ZS2_99_arrow_img = $img_path.($ZS2_99_incr==0 ? $img_0 : ($ZS2_99_incr>0 ? $img_up : $img_down));
		
	}
	
		$VEOLIA1_1_color_class = getAttributeColor($VEOLIA1_1_last, true);
		$VEOLIA1_2_color_class = getAttributeColor($VEOLIA1_2_last, true);
		$VEOLIA1_3_color_class = getAttributeColor($VEOLIA1_3_last, true);
		$VEOLIA1_4_color_class = getAttributeColor($VEOLIA1_4_last, true);
		$VEOLIA1_5_color_class = getAttributeColor($VEOLIA1_5_last, true);
		$VEOLIA1_6_color_class = getAttributeColor($VEOLIA1_6_last, true);
		$VEOLIA1_7_color_class = getAttributeColor($VEOLIA1_7_last, true);
		$VEOLIA1_98_color_class = getAttributeColor($VEOLIA1_98_last, true);

		$VEOLIA2_1_color_class = getAttributeColor($VEOLIA2_1_last, true);
		$VEOLIA2_2_color_class = getAttributeColor($VEOLIA2_2_last, true);
		$VEOLIA2_3_color_class = getAttributeColor($VEOLIA2_3_last, true);
		$VEOLIA2_4_color_class = getAttributeColor($VEOLIA2_4_last, true);
	
		$ENGAGEMENT_1_color_class = getAttributeColor($ENGAGEMENT_1_last, true);
		$ENGAGEMENT_2_color_class = getAttributeColor($ENGAGEMENT_2_last, true);
		$ENGAGEMENT_3_color_class = getAttributeColor($ENGAGEMENT_3_last, true);
		$ENGAGEMENT_4_color_class = getAttributeColor($ENGAGEMENT_4_last, true);
		$ENGAGEMENT_5_color_class = getAttributeColor($ENGAGEMENT_5_last, true);
		$ENGAGEMENT_6_color_class = getAttributeColor($ENGAGEMENT_6_last, true);
		$ENGAGEMENT_7_color_class = getAttributeColor($ENGAGEMENT_7_last, true);
		$ENGAGEMENT_8_color_class = getAttributeColor($ENGAGEMENT_8_last, true);
//		$ENGAGEMENT_97_color_class = getAttributeColor($ENGAGEMENT_97_last, true);
//		$ENGAGEMENT_99_color_class = getAttributeColor($ENGAGEMENT_99_last, true);
	
		$FACILITIES_1_color_class = getAttributeColor($FACILITIES_1_last, true);
		$FACILITIES_2_color_class = getAttributeColor($FACILITIES_2_last, true);
		$FACILITIES_3_color_class = getAttributeColor($FACILITIES_3_last, true);
		$FACILITIES_4_color_class = getAttributeColor($FACILITIES_4_last, true);
		$FACILITIES_5_color_class = getAttributeColor($FACILITIES_5_last, true);
		$FACILITIES_98_color_class = getAttributeColor($FACILITIES_98_last, true);
		$FACILITIES_99_color_class = getAttributeColor($FACILITIES_99_last, true);
	
	// VEOLIA1+ new algo END
	
	
	
	// if compare mode on
	if ($benchmarking_id == 'benchmark') {
		$legend_row2_1 = 
		$legend_row2_3 = 'hidden';
		$legend_row2_2 = 'shown';
	}
	
	// if importance mode on
	if ($benchmarking_id == 'importance') {
		
		$company_importance_arr = getValue('rating_importance', 'ratings', 'ratings_id', $company_id);
		$company_importance_arr = $company_importance_arr ? explode(',',$company_importance_arr) : [13,9,16,8,15,14,14,12];
		
		$company_importance_arr_processed = [];
		for ($j = 0; $j <= 30; $j++) // TODO: 1. 30 was 29; 2. do we need this?
		{
			if (isset($company_importance_arr[$j])) {
				$company_importance_arr_processed[] = $company_importance_arr[$j]/*.'%'*/;
			} else {
				$company_importance_arr_processed[] = '';
			}
		}

		list(
			$offering_incr	,
			$innovation_incr	,
			$integrity_incr	,
			$leadership_incr	,
			$authenticity_incr	,
			$differentiation_incr	,
			$relevance_incr	,
			$inspiration_incr	,
			$Q310_12_incr	,
			$Q310_13_incr	,
			$Q310_14_incr	,
			$Q310_15_incr	,
			$Q310_16_incr	,
			$Q310_21_incr	,
			$Q310_22_incr	,
			$Q310_23_incr	,
			$Q310_24_incr	,
			$Q310_25_incr	,
			$Q310_26_incr	,
			$Q310_27_incr	,
			$Q310_28_incr	,
			$Q310_29_incr	,
			$Q310_30_incr	,
			$Q310_31_incr	,
			$Q310_32_incr	,
			$Q310_33_incr	,
			$Q310_34_incr	,
			$Q310_35_incr	,
			$Q310_36_incr	,
			$Q310_37_incr	
			
		) = $company_importance_arr_processed;

		$advocacy_last = 
		$consideration_last = 
		$recommendation_last = 
		$employment_last = 
		
		$Q215_5_last =
		$Q215_6_last =
			
		$awareness_last =
		$familiarity_last = '';

		$legend_row2_1 = 
		$legend_row2_2 = 'hidden';
		$legend_row2_3 = 'shown';
	} else {
		// TODO - нужно ли тут такое?
/*		
		$advocacy_last .= $advocacy_last ? '%' : '';
		$consideration_last .= $consideration_last ? '%' : '';
		$recommendation_last .= $recommendation_last ? '%' : '';
		$employment_last .= $employment_last ? '%' : '';
		
		$Q215_5_last .= $Q215_5_last ? '%' : '';
		$Q215_6_last .= $Q215_6_last ? '%' : '';
		
		$awareness_last .= $awareness_last ? '%' : '';
		$familiarity_last .= $familiarity_last ? '%' : '';
*/
	}

	$responce_min_date_formatted = isset($responce_min_date) ? date_format(date_create($responce_min_date), 'd/m/Y') : '';
	$responce_max_date_formatted = isset($responce_max_date) ? date_format(date_create($responce_max_date), 'd/m/Y') : '';
	$title_date = date('d').' <span>'.ucfirst(substr(date('F'), 0, 1)).'</span>'.substr(date('F'), 1).' '.date('Y');
	// return 'responce_min_date_formatted '.$responce_min_date_formatted . ' /  responce_max_date_formatted '. $responce_max_date_formatted . ' / title_date ' . $title_date;
	if (is_array($period_id)) { $period_id = $period_id[0]; }
	
	$date_exploded = explode(' - ',$period_id);
	$date_from_cur = is_array($date_exploded) && isset($date_exploded[0]) && isset(explode('/',$date_exploded[0])[2]) ? $date_exploded[0] : date('d/m/Y');
	$date_to_cur = is_array($date_exploded) && isset($date_exploded[1]) && isset(explode('/',$date_exploded[1])[2]) ? $date_exploded[1] : date('d/m/Y');	

	$date_from_cur_d = explode('/',$date_from_cur)[0];
	$date_from_cur_m = explode('/',$date_from_cur)[1];
	$date_from_cur_y = explode('/',$date_from_cur)[2];
	$date_from_cur = '20'.$date_from_cur_y.'-'.$date_from_cur_m.'-'.$date_from_cur_d;
	
	$date_to_cur_d = explode('/',$date_to_cur)[0];
	$date_to_cur_m = explode('/',$date_to_cur)[1];
	$date_to_cur_y = explode('/',$date_to_cur)[2];
	$date_to_cur = '20'.$date_to_cur_y.'-'.$date_to_cur_m.'-'.$date_to_cur_d;

	$date_from_cur_date = new DateTime($date_from_cur);
	$date_to_cur_date = new DateTime($date_to_cur);
	$period_days = $date_to_cur_date->diff($date_from_cur_date)->format("%a") + 1;
	if ($type == 'table') {
		$legend_row2_1 = 'hidden';
		$legend_row2_4 = 'shown'; 
	}

	// get text alignment shifts for svg graphics
	if (strlen($trust_affection_last) == 1) $svg_text_shift = 19;
	if (strlen($trust_affection_last) == 2) $svg_text_shift = 14;
	if (strlen($trust_affection_last) == 3) $svg_text_shift = 8;

	// process company logo	
	$company_logo_file = 'logo_placeholder.png';
	$company_logo_ratio_class = 'tall';
	
	if ($company_count > 1 && $benchmarking_id == 'period') {
		$company_logo_file = get_label('dashboard_multiple_img');
		if (empty($company_logo_file)) { $company_logo_file = 'multiple.png'; }
		$company_logo_ratio_class = 'flat';
	} 

	if (($company_count == 1 || $benchmarking_id != 'period') && file_exists(ROOT.'images/logos/'.$company_id.'_300.png')) {
		$company_logo_size = getimagesize(ROOT.'images/logos/'.$company_id.'_300.png');
		$company_logo_file = $company_id.'_300.png';
		$company_logo_width = $company_logo_size[0];
		$company_logo_height = $company_logo_size[1];
		$company_logo_ratio = $company_logo_height/$company_logo_width;
		if ($company_logo_width > $company_logo_height) $company_logo_ratio_class = 'flat';
		if ($company_logo_ratio > 0.6) $company_logo_ratio_class = 'tall';
	}
	
	$rating_type = getValue('rating_type', 'ratings', 'ratings_id', $company_id);
	
	// LABLES //

	require_once(ROOT.'backend/includes/dashboard_variables.inc.php');
	
	
	$legend_days = get_label('legend_days');
	$legend_responces = get_label('legend_responces');
	$legend_period = get_label('legend_period');
	$legend_very_high = get_label('legend_very_high');
	$legend_high = get_label('legend_high');
	$legend_average = get_label('legend_average');
	$legend_low = get_label('legend_low');
	$legend_very_low = get_label('legend_very_low');
	$legend_positive_change = get_label('legend_positive_change');
	$legend_no_change = get_label('legend_no_change');
	$legend_negative_change = get_label('legend_negative_change');
	$legend_previous_period = get_label('legend_previous_period');
	$legend_benchmark = get_label('legend_benchmark');
	$legend_importance = get_label('legend_importance');
	// return '$legend_days ' . $legend_days . 
	// '<br>$legend_responces ' . $legend_responces . 
	// '<br>$legend_period ' . $legend_period . 
	// '<br>$legend_very_high ' . $legend_days . 
	// '<br>$legend_high ' . $legend_high . 
	// '<br>$legend_average ' . $legend_average . 
	// '<br>$legend_low ' . $legend_low . 
	// '<br>$legend_very_low ' . $legend_very_low . 
	// '<br>$legend_positive_change ' . $legend_positive_change . 
	// '<br>$legend_no_change ' . $legend_no_change . 
	// '<br>$legend_negative_change ' . $legend_negative_change . 
	// '<br>$legend_previous_period ' . $legend_previous_period . 
	// '<br>$legend_benchmark ' . $legend_benchmark . 
	// '<br>$legend_importance ' . $legend_importance 
	// ;
	// HTML //
	
	$client_dashboard_model = getValue('client_dashboard_model', 'clients', 'clients_id', $_SESSION['client_id']);

	$client_dashboard_template = getValue('view_template', 'views', 'view_type', $client_dashboard_model, 1, true, " AND `view_tool` = '$type'");
	// backend/html/map_default.html.php'
	if ($type == 'dashboard') {
		$html = [
			require_once(ROOT.'backend/html/'.$client_dashboard_template.'.html.php'),
			$ajax_key
		];
	}
	
	if ($type == 'table') {
		$html = [
			require_once(ROOT.'backend/html/'.$client_dashboard_template.'.html.php'),
			$ajax_key
		];
	}
	
	if ($type == 'map') {
		if ($trust_affection_last != '' /*|| $Q305_3_01_last != ''*/) {
			$html = [
				require_once(ROOT.'backend/html/'.$client_dashboard_template.'.html.php'),
				$company_extras,	
				$ajax_key
			];
		} else {
			$html = [
				[
					'',
					'',
					''
				],
				$company_extras,	
				$ajax_key
			];
		}
	}

	return json_encode($html);

}

	
function getAttributeColor($attribute_value, $get_class=false)
{
	$color = 'grey'; 
	$class = 'grey'; 
	switch ($attribute_value) 
	{
		case ($attribute_value == '?'): 
			$color = 'grey'; 
			$class = 'grey'; 
			break;
		case ($attribute_value < 40): 
			$color = '#D90000';
			$class = 'red'; 
			break;
		case ($attribute_value >= 40 && $attribute_value < 60): 
			$color = '#FF6600';
			$class = 'orange'; 
			break;
		case ($attribute_value >= 60 && $attribute_value < 70): 
			$color = '#FFCC00';
			$class = 'yellow'; 
			break;
		case ($attribute_value >= 70 && $attribute_value < 80): 
			$color = '#99CC00';
			$class = 'lime'; 
			break;
		case ($attribute_value >= 80): 
			$color = '#008000';
			$class = 'green'; 
			break;
	}
	return ($get_class ? $class : $color);
}

