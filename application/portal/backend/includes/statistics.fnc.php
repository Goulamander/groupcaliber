<?php

/*
	FILE DESCRIPTION:
		PATH: backend/includes/statistics.fnc.php;
		TYPE: fnc (function declaration);
		PURPOSE: declares functions used to generate the company statistical data array;
		REFERENCED IN: various client- and server-side files that require the company statistical data;
		FUNCTIONS DECLARED:
			NAME: getCompanyStatistics;  
				PURPOSE: returns the data array of any company statistical indicators (attributes or elements) split by time intervals along with some auxiliary data with any filtering and groupping options based on the raw survey data from table 'data' and table 'quotas' in the database;
				EXECUTED IN: files and functions that generate all the Views HTML, Export functionality files, client Subscription and API files;
			NAME: getQuestionNameById ($question, $names);  
				PURPOSE: ;
				EXECUTED IN: function getCompanyStatistics();
            NAME: getQuestionAnswer ($question_id, $answer_value);
				PURPOSE: ;
				EXECUTED IN: function getCompanyStatistics();
            NAME: getSubQuestionName ($question_id, $subquestion_value);
				PURPOSE: ;
				EXECUTED IN: function getCompanyStatistics();
            NAME: getQuestionName ($field, $table, $table_id, $question_id);
				PURPOSE: ;
				EXECUTED IN: function getCompanyStatistics();
            NAME: get_field_name ($field_name_or_attribute_index);
                PURPOSE: return proper field_name and check its existense in fields table or return false
            NAME: getClientViewFields ($client_id)
				PURPOSE: returning list of fields from client view model;
		STYLES: - ; 
*/  

function getCompanyStatistics(
	$company_id	,
	$attribute_id	,
	$interval_id	,
	$period_id	,
	$gender_id	,
	$age_id	,
	$country_id	,
	$region_type_id	,
	$region_id	,
	$education_type_id	,
	$income_type_id	,
	$segment_id	,
	$industry_id	,
	$company_size_id	,
	$diagnosis_id	,
	$family_id	,
	$children_id	,
	$housing_id	,
	$living_id	,
	$engagement_id	,
	$facilities_id	,
	$tg1_id	,
	$tg2_id	,
	$novo1_id	,
	$novo2_id	,
	$demant2_id	,
	$rw1_id	,
	$rw2_id	,
	$gr1_id	,
	$gr2_id	,
	$v_customer_id	,
	$gol1_id	,
	$gol2_id	,
	$gol3_id	,
	$gol5_id	,
	$veolia1_id	,
	$touchpoints_id	,
	$employment_id	,
	$race_id	,
	$ethnicity_id	,
	$politics_id	,
	$children_number_id	,
	$tccc_influence_id	,
	$tccc_segment_id	,
	$dmi1_id	,
	$energinet1_id	,
	$stakeholder_type_id	,
	$hovedkategori_id	,
	$siemensq1_id	,
	$association_id	,
	$segment2_id	,
	$fk1_id = ["group", "all"]			, // The default value is an array which contains 2 values. "group" means that by default the filter options are not split and "all" means that by default the filter does not apply. The default value for this filter is the same as for the other filters. The filters above this one do not need the default value to be set where the function is declared because they are always set where the function is called.
	$region_zone_id	= ["group", "all"],
	$education_id = ["group", "all"]	,
	$income_id = ["group", "all"]	,
	$cv2_id	= ["group", "all"],
	$cv3_id	= ["group", "all"],
	$subregion_br_group_id	= ["group", "all"],	
	$wsa1_id	= ["group", "all"]	,
	$wsa2_id	= ["group", "all"]	,
	$wsa3_studyarea_id	= ["group", "all"]	,
	$sf1_id	= ["group", "all"]	,
	$eon1_id	= ["group", "all"]	,
	$eon_customer_id	= ["group", "all"]	,
	$gn2_id	= ["group", "all"]	,
	$gn1_id	= ["group", "all"]	,
	$gn3_id	= ["group", "all"]	,
	$gn4_id	= ["group", "all"]	,
	$zs2_id	= ["group", "all"]	,
	$zs3_id	= ["group", "all"]	,
	$ess1_id	= ["group", "all"]	,
	$ess2_id	= ["group", "all"]	,
	$ori1_id	= ["group", "all"]	,
	$ovo_influencer_id	= ["group", "all"]	,
	$ovo_customer_id	= ["group", "all"]	,
	$bay5_id	= ["group", "all"]	,
	$ethnicity_ca_id	= ["group", "all"]	,
	$politics_ca_id	= ["group", "all"]	,
	$air02_id	= ["group", "all"]	,
	$air03_id	= ["group", "all"]	,
	$air04_id	= ["group", "all"]	,
//paste end
	$ajax_key = ''		,
	$placeholder_id = 0
) {
	$start_time = microtime(true);
	$timing = get_label('statistics_log_timings')=='1';
	$timing_log = ROOT.'/logs/debug_log.txt';

    $is_admin = $_SESSION['client_role'] == 1;

	$client_id = $_SESSION['client_id'];
    $client_show_all = $_SESSION['client_show_all'];
    $client_binding_array = $_SESSION['client_binding_array'];
    
	$customer_id = getValue('client_customer', 'clients', 'clients_id', $client_id); // determine the client's customer at the begining of the file
	
	$get_site_project = get_site_project();
    $get_project = get_project();
    $project_id = max(1, $get_site_project, $get_project);
        
	if (is_array($interval_id)) 
    {
		$interval_id = $interval_id[0];
		if (is_array($interval_id)) {
			$interval_id = $interval_id[0]; 
		}
	} 
	
	if (is_array($period_id)) 
    {
		$period_id = $period_id[0];
		if (is_array($period_id)) {
			$period_id = $period_id[0]; 
		}
	} 
	
	// process attributes
	$attribute_id = is_array($attribute_id[0]) ? $attribute_id[0] : $attribute_id; // extract included array from $attribute_id array if it exists 
	$attribute_sql_str = 
	$attribute_sql_str_2 = 
	$attribute_level_up_sql_str = '';
	
	$any_group_attribute_id = '';
	
	$attribute_id = $attribute_id[0] == 'TL' ? ['trust_affection'] : $attribute_id; // TODO temporary rewrite - to be refactored!
	
	$attribute_count = 0;
	foreach ($attribute_id as $_attribute_id) 
    {
		if ($_attribute_id != 'group' && $_attribute_id != 'all') 
        {
			$_attribute_parts = explode('_',$_attribute_id);
			
            if ($_attribute_parts[0]=='V' || $_attribute_parts[0]=='OPEN')    
            {
                $_attribute_name = $_attribute_parts[0].'_'.$_attribute_parts[1];
                $_attribute_suffix = isset($_attribute_parts[2]) ? $_attribute_parts[2] : '';
                $_attribute_3rd_part = isset($_attribute_parts[3]) ? $_attribute_parts[3] : '';
            } else  {
                $_attribute_name = $_attribute_parts[0];	
                $_attribute_suffix = isset($_attribute_parts[1]) ? $_attribute_parts[1] : '';	
                $_attribute_3rd_part = isset($_attribute_parts[2]) ? $_attribute_parts[2] : '';	
            }
            
			$_attribute_column = $_attribute_name;	
			if ($_attribute_name!='S104' && $_attribute_name!='S105' && !empty($_attribute_suffix)) 
			{
				$_attribute_column .= '_'.$_attribute_suffix; 
				// attribute column var is required because of custom atributes like Q310_2_01 - Innovation which in fact is column Q310_2 - but must be treated as a unique attribute
			}
			
			// prepare query formulas
			
			$_attribute_sql = $_attribute_id;
			
			if (strpos($_attribute_id, 'trust_affection')===0) 
            {
				switch ($project_id)
				{
					case 11:
						$_attribute_sql = 
							"(".
								"100*(IF(`Q305_2` = 99 OR `Q305_2` = 0, NULL, `Q305_2`) - 1) / 4 ".
								" + 100*(IF(`Q305_3` = 99 OR `Q305_3` = 0, NULL, `Q305_3`) -1) / 4 ".
								" + 100*(IF(`REPUTATION` = 99 OR `REPUTATION` = 0, NULL, `REPUTATION`) -1) / 4 ".
							") / 3";
						break;
					case 30:
						$_attribute_sql = 
							"(".
								"   100*(IF(`Q305_2` = 99 OR `Q305_2` = 0, NULL, `Q305_2`) - 1) / 6 ".
								" + 100*(IF(`Q305_3` = 99 OR `Q305_3` = 0, NULL, `Q305_3`) - 1) / 6 ".
								" + 100*(IF(`Q305_4` = 99 OR `Q305_4` = 0, 1, `Q305_4`) - 1) / 6 ".
							") / ( 2 + IF(`Q305_4` = 99 OR `Q305_4` = 0, 0, 1) ) ";  // POR-1152a
						break;
					default:
						$_attribute_sql = 
							"(100*(IF(`Q305_2` = 99 OR `Q305_2` = 0, NULL, `Q305_2`) - 1) / 6 + 100*(IF(`Q305_3` = 99 OR `Q305_3` = 0, NULL, `Q305_3`) -1) / 6) / 2";
				}
			}

			if (in_array($_attribute_name, ['Q305', 'Q310', 'VEOLIA2', 'BAY3', 'BAY4', 'BAY11', 'BAY12', 'BAY21', 'BAY22' ])) 
            {
				$_attribute_sql = 
					"100*(IF(`$_attribute_column` = 99 OR `$_attribute_column` = 0, NULL, `$_attribute_column`) - 1) / 6";
			}

			if ($_attribute_name=='REPUTATION') 
            {
				$_attribute_sql = 
					"100*(IF(`$_attribute_column` = 99 OR `$_attribute_column` = 0, NULL, `$_attribute_column`) - 1) / 4";
			}

			if ($_attribute_name=='Q215') 
            {	
				switch ($_attribute_3rd_part)
				{
					case 'DISAGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` = 99 OR `$_attribute_column` IN (3,4,5,6,7), 0, IF(`$_attribute_column` IN (1, 2), 100, NULL))";
						break;
					case 'NEUTRAL':
						$_attribute_sql = 
							"IF(`$_attribute_column` = 99 OR `$_attribute_column` IN (1,2,4,5,6,7), 0, IF(`$_attribute_column` IN (3), 100, NULL))";
						break;
					case 'AGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` = 99 OR `$_attribute_column` IN (1,2,3,6,7), 0, IF(`$_attribute_column` IN (4,5), 100, NULL))";
						break;
					default:
						$_attribute_sql = 
							"IF(`$_attribute_column` = 99 OR `$_attribute_column` BETWEEN 1 AND 5, 0, IF(`$_attribute_column` BETWEEN 6 AND 7, 100, NULL))";
				}
			}

			if ($_attribute_name=='ADVOCACY') 
            {	
				switch ($_attribute_suffix)
				{
					case 'CRITICAL':
						$_attribute_sql = 
							"IF(`$_attribute_name` = 99 OR `$_attribute_name` IN (1,4,5,6,7), 0, IF(`$_attribute_name` IN (2, 3), 100, NULL))";
						break;
					case 'NEUTRAL':
						$_attribute_sql = 
							"IF(`$_attribute_name` = 99 OR `$_attribute_name` IN (2,3,5,6,7), 0, IF(`$_attribute_name` IN (1, 4), 100, NULL))";
						break;
					case 'HIGH':
						$_attribute_sql = 
							"IF(`$_attribute_name` = 99 OR `$_attribute_name` IN (1,2,3,4,7), 0, IF(`$_attribute_name` IN (5, 6), 100, NULL))";
						break;
				}
			}
			
            
            if ($_attribute_name=='V_PURPOSE') 
            {	
				switch ($_attribute_3rd_part)
				{
					case 'AGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,99), 0, IF(`$_attribute_column` IN (6,7), 100, NULL))";
						break;
					case 'NEUTRAL':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,6,7,99), 0, IF(`$_attribute_column` IN (3,4,5), 100, NULL))";
						break;
					case 'DISAGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (3,4,5,6,7,99), 0, IF(`$_attribute_column` IN (1,2), 100, NULL))";
						break;
					case 'NOTSURE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,6,7), 0, IF(`$_attribute_column` IN (99), 100, NULL))";
						break;
				}
			}            
            
			if ($_attribute_name=='CHR01' || $_attribute_name=='CHR02') 
            {	
				switch ($_attribute_suffix)
				{
					case '01':
						$_attribute_sql = 
							"IF(`$_attribute_name` IN (2,99), 0, IF(`$_attribute_name` = 1, 100, NULL))";
						break;
					case '02':
						$_attribute_sql = 
							"IF(`$_attribute_name` IN (1,99), 0, IF(`$_attribute_name` = 2, 100, NULL))";
						break;
					case '99':
						$_attribute_sql = 
							"IF(`$_attribute_name` IN (1,2), 0, IF(`$_attribute_name` = 99, 100, NULL))";
						break;
				}
			}			

            if ($_attribute_name=='FK2') 
            {	
				switch ($_attribute_3rd_part)
				{
					case '1':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (2,3,4,5,6,7), 0, IF(`$_attribute_column` = 1, 100, NULL))";
						break;
					case '2':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,3,4,5,6,7), 0, IF(`$_attribute_column` = 2, 100, NULL))";
						break;
					case '3':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,4,5,6,7), 0, IF(`$_attribute_column` = 3, 100, NULL))";
						break;
					case '4':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,5,6,7), 0, IF(`$_attribute_column` = 4, 100, NULL))";
						break;
					case '5':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,6,7), 0, IF(`$_attribute_column` = 5, 100, NULL))";
						break;
					case '6':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,7), 0, IF(`$_attribute_column` = 6, 100, NULL))";
						break;
					case '7':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,6), 0, IF(`$_attribute_column` = 7, 100, NULL))";
						break;
				}
			}  
			
			if ($_attribute_name=='ZS1') 
            {	
				switch ($_attribute_3rd_part)
				{
					case '1':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (2,3,4,5,6,7), 0, IF(`$_attribute_column` = 1, 100, NULL))";
						break;
					case '2':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,3,4,5,6,7), 0, IF(`$_attribute_column` = 2, 100, NULL))";
						break;
					case '3':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,4,5,6,7), 0, IF(`$_attribute_column` = 3, 100, NULL))";
						break;
					case '4':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,5,6,7), 0, IF(`$_attribute_column` = 4, 100, NULL))";
						break;
					case '5':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,6,7), 0, IF(`$_attribute_column` = 5, 100, NULL))";
						break;
					case '6':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,7), 0, IF(`$_attribute_column` = 6, 100, NULL))";
						break;
					case '7':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,6), 0, IF(`$_attribute_column` = 7, 100, NULL))";
						break;
				}
			}
			
			if ($_attribute_column=='Q310_23') 
            {	
				switch ($_attribute_3rd_part)
				{
					case 'AGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,99), 0, IF(`$_attribute_column` IN (6,7), 100, NULL))";
						break;
					case 'NEUTRAL':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,6,7,99), 0, IF(`$_attribute_column` IN (3,4,5), 100, NULL))";
						break;
					case 'DISAGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (3,4,5,6,7,99), 0, IF(`$_attribute_column` IN (1,2), 100, NULL))";
						break;
					case 'NOTSURE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,6,7), 0, IF(`$_attribute_column` IN (99), 100, NULL))";
						break;
				}
			}
			
			if (in_array($_attribute_column, ['BAY4_01', 'BAY4_02', 'BAY4_03', 'BAY4_04', 'BAY4_05', 'BAY4_06']))
            {	
				switch ($_attribute_3rd_part)
				{
					case 'FAMILIAR1':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (2,3,4,5,6,7,99), 0, IF(`$_attribute_column` IN (1), 100, NULL))";
						break;
					case 'FAMILIAR2':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,4,5,6,7,99), 0, IF(`$_attribute_column` IN (2,3), 100, NULL))";
						break;
					case 'FAMILIAR3':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,6,7,99), 0, IF(`$_attribute_column` IN (4,5), 100, NULL))";
						break;
					case 'FAMILIAR4':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,99), 0, IF(`$_attribute_column` IN (6,7), 100, NULL))";
						break;
					case 'FAMILIAR99':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,6,7), 0, IF(`$_attribute_column` IN (99), 100, NULL))";
						break;
				}
			}
			
			if (in_array($_attribute_column, ['AIR01_01', 'AIR01_02', 'AIR01_03', 'AIR01_04', 'AIR01_05']))
            {	
				switch ($_attribute_3rd_part)
				{
					case 'AGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,99), 0, IF(`$_attribute_column` IN (6,7), 100, NULL))";
						break;
					case 'NEUTRAL':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,6,7,99), 0, IF(`$_attribute_column` IN (3,4,5), 100, NULL))";
						break;
					case 'DISAGREE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (3,4,5,6,7,99), 0, IF(`$_attribute_column` IN (1,2), 100, NULL))";
						break;
					case 'NOTSURE':
						$_attribute_sql = 
							"IF(`$_attribute_column` IN (1,2,3,4,5,6,7), 0, IF(`$_attribute_column` IN (99), 100, NULL))";
						break;
				}
			}
			
			if ($_attribute_name=='GN1')
			{	
				$_attribute_sql = "IF(`$_attribute_column` IN (2,3,4,5,6,7,97,98,99), 0, IF(`$_attribute_column` = 1, 100, NULL))";
			}			
            
			if ($_attribute_name=='SUSTAINABILITY2') 
            {	
				$_attribute_sql = 
					"IF(`$_attribute_column` = 99 OR `$_attribute_column` BETWEEN 1 AND 3, 0, IF(`$_attribute_column` BETWEEN 4 AND 5, 100, NULL))";
			}

			if ($_attribute_name=='SUSTAINABILITY1') 
            {	
				$_attribute_sql = 
					"IF(`$_attribute_column` = 1, 100, IF(`$_attribute_column` IN (2,99), 0, NULL))";
			}

			if ($_attribute_name=='PORTFOLIO') 
            {	
				$_attribute_sql = 
					"IF(`$_attribute_name` = 1, 100, IF(`$_attribute_name` = 0, 0, NULL))";
			}

			$field_algorithm = getValue('field_algorithm', 'fields', 'field_name', $_attribute_name);
			if ($field_algorithm==4)
			{	
				$_attribute_sql = 
					"GROUP_CONCAT(`$_attribute_name` SEPARATOR ' ')";
			}

			$sql_func = in_array($_attribute_name, [
				'VEOLIA1', 
				'ENGAGEMENT', 
				'FACILITIES', 
				'INITIATIVES', 
				'TOUCHPOINTS',
				'AGENDA',
				'DMI1',
				'ASSOCIATION',
				'FK1',
				'ZS2',
				'GN2',
				'BAY5',
				'AIR04'
			]) ? 'SUM' : 'AVG';	
			
			$attribute_level_up_sql_str .= " $sql_func(`$_attribute_id`) AS `$_attribute_id`, ";
			$attribute_sql_str .= " $sql_func($_attribute_sql) AS `$_attribute_id`, ";
								
			if ($field_algorithm==4) 
			{	
				$attribute_sql_str = " $_attribute_sql AS `attribute`, ";
			}		

			if ($_attribute_name=='S104' || $_attribute_name=='S105') {	
				$attribute_sql_str = "IF(`S105` BETWEEN 1 AND 99, 1, NULL)";
				$attribute_sql_str_2 = "IF(`S105` BETWEEN 4 AND 7, 1, NULL)";
			}

/*
            $_attribute_sql = 
					"100*(IF(`$_attribute_column` = 99 OR `$_attribute_column` = 0, NULL, `$_attribute_column`) - 1) / 6";
*/

			$any_group_attribute_id = $_attribute_id;

			$attribute_count ++;	
		}
	}
		
	// get array parameters into sql filter str	
	$filter_data_arr = [
		[$company_id, 'RATING'],
		[$gender_id, 'GENDER'],
		[$age_id, 'yearborn'],
		[$country_id, 'country'],
		[$region_type_id, 'REGION_TYPE'],
		[$region_id, 'REGION'],
		[$education_type_id, 'EDUCATION_TYPE'],
		[$income_type_id, 'INCOME_TYPE'],
		[$segment_id, 'SEGMENT'],
		[$industry_id, 'INDUSTRY'],
		[$company_size_id, 'COMPANY_SIZE'],
		[$diagnosis_id, 'DIAGNOSIS'],
		[$family_id, 'FAMILY'],
		[$children_id, 'CHILDREN'],
		[$housing_id, 'HOUSING'],
		[$living_id, 'LIVING'],
		[$engagement_id, 'ENGAGEMENT'],
		[$facilities_id, 'FACILITIES'],
		[$tg1_id, 'TG1'],
		[$tg2_id, 'TG2'],
		[$novo1_id, 'NOVO1'],
		[$novo2_id, 'NOVO2'],
		[$demant2_id, 'DEMANT2'],
		[$rw1_id, 'RW1'],
		[$rw2_id, 'RW2'],
		[$gr1_id, 'GR1'],
		[$gr2_id, 'GR2'],
		[$v_customer_id, 'V_CUSTOMER'],
		[$gol1_id, 'GOL1'],
		[$gol2_id, 'GOL2'],
		[$gol3_id, 'GOL3'],
		[$gol5_id, 'GOL5'],
		[$veolia1_id, 'VEOLIA1'],
		[$touchpoints_id, 'TOUCHPOINTS'],
		[$employment_id, 'EMPLOYMENT'],
		[$race_id, 'RACE'],
		[$ethnicity_id, 'ETHNICITY'],
		[$politics_id, 'POLITICS'],
		[$children_number_id, 'CHILDREN_NUMBER'],
		[$tccc_influence_id, 'TCCC_INFLUENCE'],
		[$tccc_segment_id, 'TCCC_SEGMENT'],
		[$dmi1_id, 'DMI1'],
		[$energinet1_id, 'ENERGINET1'],
		[$stakeholder_type_id, 'STAKEHOLDER_TYPE'],
		[$hovedkategori_id, 'HOVEDKATEGORI'],
		[$siemensq1_id, 'SIEMENSQ1'],
		[$association_id, 'ASSOCIATION'],
		[$segment2_id, 'SEGMENT2'],
		[$fk1_id, 'FK1'],
		[$region_zone_id, 'REGION_ZONE'],
		[$education_id, 'EDUCATION'],
		[$income_id, 'INCOME'],
		[$cv2_id, 'CV2'],
		[$cv3_id, 'CV3'],
		[$subregion_br_group_id, 'SUBREGION_BR_GROUP'],
		[$wsa1_id, 'WSA1'],
		[$wsa2_id, 'WSA2'],
		[$wsa3_studyarea_id, 'WSA3_STUDYAREA'],
		[$sf1_id, 'SF1'],
		[$eon1_id, 'EON1'],
		[$eon_customer_id, 'EON_CUSTOMER'],
		[$gn2_id, 'GN2'],
		[$gn1_id, 'GN1'],
		[$gn3_id, 'GN3'],
		[$gn4_id, 'GN4'],
		[$zs2_id, 'ZS2'],
		[$zs3_id, 'ZS3'],
		[$ess1_id, 'ESS1'],
		[$ess2_id, 'ESS2'],
		[$ori1_id, 'ORI1'],
		[$ovo_influencer_id, 'OVO_INFLUENCER'],
		[$ovo_customer_id, 'OVO_CUSTOMER'],
		[$bay5_id, 'BAY5'],
		[$ethnicity_ca_id, 'ETHNICITY_CA'],
		[$politics_ca_id, 'POLITICS_CA'],
		[$air02_id, 'AIR02'],
		[$air03_id, 'AIR03'],
		[$air04_id, 'AIR04']
	];
	
    $filter_sql_str = $client_id && $client_show_all==0 ? " `RATING` IN (".implode(',', $client_binding_array).")" : " 1=1 ";
	$filter_activity_rating_sql_str = " ";
	$filter_activity_country_sql_str = " ";
	$filter_rating = [];
	$filter_country = [];
	$cur_y = date('Y');
	foreach ($filter_data_arr as $this_filter_data) 
    {
		if (is_array($this_filter_data[0]) && !in_array('all', $this_filter_data[0]) && count($this_filter_data[0])>0) 
		{ // if "all" option present - filter for this element is not generated
			$this_filter_sql_str = "";
			
			// get rating daughter ratings and and add them to ratings array
			if ($this_filter_data[1]=='RATING') 
            {
				foreach ($this_filter_data[0] as $this_filter_id) 
                {
					if ($this_filter_id!='group' && $this_filter_id!='multiple') 
                    {
						$query = "SELECT `ratings_id` FROM `ratings` WHERE `rating_parent` = '$this_filter_id'";
						if ($sql = query($query)) 
                        {
							while ($row = sql_fetch_assoc($sql)) 
                            {	
								$this_filter_data[0][] = $row['ratings_id'];		
							}
						}
					}
				} // foreach
			}
			
			$regions = [];
			foreach ($this_filter_data[0] as $this_filter_id) 
			{
				if ( $this_filter_id != 'group' ) 
				{
					$filter_value_arr = [];
					$this_filter_id = explode('-', $this_filter_id);
					if ( count($this_filter_id) > 1 ) {
						for ($i=$this_filter_id[0]; $i<=$this_filter_id[1]; $i++) {
							$filter_value_arr[] = $i;
						}
					} else {
						$filter_value_arr[] = $this_filter_id[0];
					}
					
					foreach ($filter_value_arr as $this_filter_value) 
					{
						switch ($this_filter_data[1]) 
						{
							case (
								$this_filter_data[1] == 'REGION'
							):
								if ($this_filter_value!='multiple') { $regions[] = $this_filter_value; }
								break;	
								
							case (false
								 || $this_filter_data[1] == 'RATING' 
								 || $this_filter_data[1] == 'GENDER' 
								 || $this_filter_data[1] == 'country' 
								 || $this_filter_data[1] == 'REGION_TYPE' 
								 || $this_filter_data[1] == 'EDUCATION_TYPE' 
								 || $this_filter_data[1] == 'INCOME_TYPE' 
								 || $this_filter_data[1] == 'INDUSTRY' 
								 || $this_filter_data[1] == 'COMPANY_SIZE' 
								 || $this_filter_data[1] == 'FAMILY' 
								 || $this_filter_data[1] == 'CHILDREN' 
								 || $this_filter_data[1] == 'HOUSING' 
								 || $this_filter_data[1] == 'LIVING' 
								 || $this_filter_data[1] == 'TG1' 
								 || $this_filter_data[1] == 'TG2' 
								 || $this_filter_data[1] == 'NOVO1' 
								 || $this_filter_data[1] == 'NOVO2' 
								 || $this_filter_data[1] == 'DEMANT2' 
								 || $this_filter_data[1] == 'GOL1' 
								 || $this_filter_data[1] == 'GOL2' 
								 || $this_filter_data[1] == 'GOL3' 
								 || $this_filter_data[1] == 'EMPLOYMENT' 
								 || $this_filter_data[1] == 'RACE' 
								 || $this_filter_data[1] == 'ETHNICITY' 
								 || $this_filter_data[1] == 'POLITICS' 
								 || $this_filter_data[1] == 'CHILDREN_NUMBER' 
								 || $this_filter_data[1] == 'TCCC_SEGMENT' 
								 || $this_filter_data[1] == 'ENERGINET1' 
								 || $this_filter_data[1] == 'STAKEHOLDER_TYPE' 
								 || $this_filter_data[1] == 'HOVEDKATEGORI' 
								 || $this_filter_data[1] == 'SIEMENSQ1' 
								 || $this_filter_data[1] == 'REGION_ZONE' 
								 || $this_filter_data[1] == 'EDUCATION' 
								 || $this_filter_data[1] == 'INCOME' 
								 || $this_filter_data[1] == 'SUBREGION_BR_GROUP' 
								 || $this_filter_data[1] == 'WSA1' 
								 || $this_filter_data[1] == 'WSA2' 
								 || $this_filter_data[1] == 'WSA3_STUDYAREA'
								 || $this_filter_data[1] == 'GN1' 
 								 || $this_filter_data[1] == 'GN3' 
								 || $this_filter_data[1] == 'ORI1' 
								 || $this_filter_data[1] == 'POLITICS_CA' 
							): 
								$this_filter_sql_str .= " `".$this_filter_data[1]."` = '".$this_filter_value."' OR ";
								if ($this_filter_data[1] == 'RATING') $filter_rating[] = $this_filter_value;
								if ($this_filter_data[1] == 'country') $filter_country[] = $this_filter_value;
								break;	
								
							/*case ($this_filter_data[1] == 'GENDER'): 	
							$this_filter_sql_str .= $this_filter_value == 1 ? " `".$this_filter_data[1]."` IN('1','3') OR " : " `".$this_filter_data[1]."` = '".$this_filter_value."' OR ";
							break;*/
								
							case (false
								 || $this_filter_data[1] == 'SEGMENT' 
								 || $this_filter_data[1] == 'DIAGNOSIS' 
								 || $this_filter_data[1] == 'ENGAGEMENT' 
								 || $this_filter_data[1] == 'FACILITIES' 
								 || $this_filter_data[1] == 'RW1' 
								 || $this_filter_data[1] == 'RW2' 
								 || $this_filter_data[1] == 'GR1' 
								 || $this_filter_data[1] == 'GR2' 
								 || $this_filter_data[1] == 'V_CUSTOMER' 
								 || $this_filter_data[1] == 'GOL5' 
								 || $this_filter_data[1] == 'VEOLIA1' 
								 || $this_filter_data[1] == 'TOUCHPOINTS' 
								 || $this_filter_data[1] == 'TCCC_INFLUENCE'
								 || $this_filter_data[1] == 'DMI1' 
								 || $this_filter_data[1] == 'ASSOCIATION' 
								 || $this_filter_data[1] == 'SEGMENT2' 
								 || $this_filter_data[1] == 'FK1' 
								 || $this_filter_data[1] == 'CV2' 
								 || $this_filter_data[1] == 'CV3' 
								 || $this_filter_data[1] == 'SF1' 
								 || $this_filter_data[1] == 'EON1' 
								 || $this_filter_data[1] == 'EON_CUSTOMER' 
								 || $this_filter_data[1] == 'GN2' 
								 || $this_filter_data[1] == 'GN4' 
								 || $this_filter_data[1] == 'ZS2' 
								 || $this_filter_data[1] == 'ZS3'
								 || $this_filter_data[1] == 'ESS1' 
								 || $this_filter_data[1] == 'ESS2' 
								 || $this_filter_data[1] == 'OVO_INFLUENCER' 
								 || $this_filter_data[1] == 'OVO_CUSTOMER'
								 || $this_filter_data[1] == 'BAY5'
								 || $this_filter_data[1] == 'ETHNICITY_CA' 
								 || $this_filter_data[1] == 'AIR02' 
								 || $this_filter_data[1] == 'AIR03' 
								 || $this_filter_data[1] == 'AIR04' 
							): 
								$this_filter_sql_str .= " FIND_IN_SET('".$this_filter_value."', `".$this_filter_data[1]."`) > 0 OR ";
								break;
								
							case ($this_filter_data[1] == 'yearborn'): 
								$add_yearborn = " `".$this_filter_data[1]."` BETWEEN YEAR(`ENDDATE`)-".end($filter_value_arr)." AND YEAR(`ENDDATE`)-".$filter_value_arr[0]." OR ";
								if (strpos($this_filter_sql_str,$add_yearborn)===false) { $this_filter_sql_str .= $add_yearborn; }
						}
					} // foreach ($filter_value_arr as $this_filter_value)
					
				} // if ( $this_filter_id != 'group' ) 
			} // foreach ($this_filter_data[0] as $this_filter_id) 
			
			if (!empty($regions))
			{
				$this_filter_sql_str .= "
				`REGION` IN (".implode(', ', $regions).") ";
			}
			
			$filter_sql_str .= " AND (".rtrim($this_filter_sql_str,"OR ").") ";
		}
	}
	$filter_sql_str = ltrim($filter_sql_str," AND");
	
	if ( count($filter_rating)>0 ) {
		$filter_rating[] = '0';
		$filter_activity_rating_sql_str .= " AND `activity_rating` IN ('".(implode("','",$filter_rating))."') ";
	}
	
	if ( count($filter_country)>0 ) {
		$filter_country[] = '999';
		$filter_activity_country_sql_str .= " AND `activity_country` IN ('".(implode("','",$filter_country))."') ";
	}
	
	// get series addon data
	
// ************* Filter Titles ****************************
	
	$label_suffix = '_filter_title_'.($is_admin ? 'admin' : 'user');
	
		$gender_filter_title = get_label('gender'.$label_suffix);
		$age_filter_title = get_label('age'.$label_suffix);
		$country_filter_title = get_label('country'.$label_suffix);
		$country_export_multiple = get_label('country_export_multiple');
		$region_type_filter_title = get_label('region_type'.$label_suffix);
		$region_filter_title = get_label('region'.$label_suffix);
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

	$single_company_id = '';
	$company_count = 0;
	foreach ($company_id as $this_company_id) {
		if ($this_company_id != 'group' && $this_company_id != 'all' && $this_company_id != 'multiple') {
			$single_company_id = $this_company_id;
			$company_count++;
		} 
	}
	$single_company_id = $company_count > 1 ? '' : $single_company_id;
	$single_attribute_id = $attribute_count > 1 ? '' : $any_group_attribute_id;

	$attribute_group_id = getValue('attribute_group', TABLE_ATTRIBUTES, 'attributes_id', $any_group_attribute_id);
	$attribute_group_name = getValue('attribute_group_name', 'attribute_groups', 'attribute_groups_id', $attribute_group_id);
	
// *********** 	Filter Names ************************
	
	
	$company_name = $single_company_id ? getValue('rating_name_short', 'ratings', 'ratings_id', $single_company_id) : '';
	$attribute_name = $single_attribute_id ? getValue('attribute_name', 'attributes', 'attributes_id', $single_attribute_id) : '';
	$gender_name = !is_array($gender_id) ? '' : (count($gender_id)==0 || in_array('group', $gender_id) ? '' : $gender_filter_title.' - '.(getQuestionName('gender_name', 'genders', 'gender_value', $gender_id[0])));
	$age_name = !is_array($age_id) ? '' : (count($age_id)==0 || in_array('group', $age_id) ? '' : $age_filter_title.' - '.(getQuestionName('age_name', 'ages', 'age_value', $age_id[0])));
	$country_name = !is_array($country_id) ? '' : (count($country_id)==0 || in_array('group', $country_id) ? '' : $country_filter_title.' - '.(getQuestionName('country_name', 'countries', 'countries_id', $country_id[0])));
	$country_name_export = !is_array($country_id) ? '' : (count($country_id)==0 || in_array('group', $country_id) ? '' : $country_filter_title.' - '.(getQuestionName('country_name', 'countries', 'countries_id', $country_id[0])));		
	$region_type_name = !is_array($region_type_id) ? '' : (count($region_type_id)==0 || in_array('group', $region_type_id) ? '' : $region_type_filter_title.' - '.(getQuestionName('region_type_name', 'region_types', 'region_types_id', $region_type_id[0])));
	$region_name = !is_array($region_id) ? '' : (count($region_id)==0 || in_array('group', $region_id) ? '' : $region_filter_title.' - '.(getQuestionName('region_name', 'regions', 'regions_id', $region_id[0])));
	$education_type_name = !is_array($education_type_id) ? '' : (count($education_type_id)==0 || in_array('group', $education_type_id) ? '' : $education_type_filter_title.' - '.(getQuestionName('education_type_name', 'education_types', 'education_type_value', $education_type_id[0])));
	$income_type_name = !is_array($income_type_id) ? '' : (count($income_type_id)==0 || in_array('group', $income_type_id) ? '' : $income_type_filter_title.' - '.(getQuestionName('income_type_name', 'income_types', 'income_type_value', $income_type_id[0])));	
	$segment_name = !is_array($segment_id) ? '' : (count($segment_id)==0 || in_array('group', $segment_id) ? '' : $segment_filter_title.' - '.(getSubQuestionName('50', $segment_id[0])));
	$industry_name = !is_array($industry_id) ? '' : (count($industry_id)==0 || in_array('group', $industry_id) ? '' : $industry_filter_title.' - '.(getQuestionName('industry_name', 'industries', 'industry_value', $industry_id[0])));
	$company_size_name = !is_array($company_size_id) ? '' : (count($company_size_id)==0 || in_array('group', $company_size_id) ? '' : $company_size_filter_title.' - '.(getQuestionName('company_size_name', 'company_sizes', 'company_size_value', $company_size_id[0])));
	$diagnosis_name = !is_array($diagnosis_id) ? '' : (count($diagnosis_id)==0 || in_array('group', $diagnosis_id) ? '' : $diagnosis_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $diagnosis_id[0])));
	$family_name = !is_array($family_id) ? '' : (count($family_id)==0 || in_array('group', $family_id) ? '' : $family_filter_title.' - '.getQuestionAnswer(290, $family_id[0]));
	$children_name = !is_array($children_id) ? '' : (count($children_id)==0 || in_array('group', $children_id) ? '' : $children_filter_title.' - '.getQuestionAnswer(291, $children_id[0]));
	$housing_name = !is_array($housing_id) ? '' : (count($housing_id)==0 || in_array('group', $housing_id) ? '' : $housing_filter_title.' - '.getQuestionAnswer(292, $housing_id[0]));
	$living_name = !is_array($living_id) ? '' : (count($living_id)==0 || in_array('group', $living_id) ? '' : $living_filter_title.' - '.getQuestionAnswer(293, $living_id[0]));
	$engagement_name = !is_array($engagement_id) ? '' : (count($engagement_id)==0 || in_array('group', $engagement_id) ? '' : $engagement_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $engagement_id[0])));
	$facilities_name = !is_array($facilities_id) ? '' : (count($facilities_id)==0 || in_array('group', $facilities_id) ? '' : $facilities_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $facilities_id[0])));
	$tg1_name = !is_array($tg1_id) ? '' : (count($tg1_id)==0 || in_array('group', $tg1_id) ? '' : $tg1_filter_title.' - '.getQuestionAnswer(78, $tg1_id[0]));
	$tg2_name = !is_array($tg2_id) ? '' : (count($tg2_id)==0 || in_array('group', $tg2_id) ? '' : $tg2_filter_title.' - '.getQuestionAnswer(79, $tg2_id[0]));
	$novo1_name = !is_array($novo1_id) ? '' : (count($novo1_id)==0 || in_array('group', $novo1_id) ? '' : $novo1_filter_title.' - '.getQuestionAnswer(92, $novo1_id[0]));
	$novo2_name = !is_array($novo2_id) ? '' : (count($novo2_id)==0 || in_array('group', $novo2_id) ? '' : $novo2_filter_title.' - '.getQuestionAnswer(93, $novo2_id[0]));
	$demant2_name = !is_array($demant2_id) ? '' : (count($demant2_id)==0 || in_array('group', $demant2_id) ? '' : $demant2_filter_title.' - '.getQuestionAnswer(160, $demant2_id[0]));
	$rw1_name = !is_array($rw1_id) ? '' : (count($rw1_id)==0 || in_array('group', $rw1_id) ? '' : $rw1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $rw1_id[0])));
	$rw2_name = !is_array($rw2_id) ? '' : (count($rw2_id)==0 || in_array('group', $rw2_id) ? '' : $rw2_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $rw2_id[0])));
	$gr1_name = !is_array($gr1_id) ? '' : (count($gr1_id)==0 || in_array('group', $gr1_id) ? '' : $gr1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $gr1_id[0])));
	$gr2_name = !is_array($gr2_id) ? '' : (count($gr2_id)==0 || in_array('group', $gr2_id) ? '' : $gr2_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $gr2_id[0])));
	$v_customer_name = !is_array($v_customer_id) ? '' : (count($v_customer_id)==0 || in_array('group', $v_customer_id) ? '' : $v_customer_filter_title.' - '.(getQuestionName('v_customer_name', 'v_customers', 'v_customer_value', $v_customer_id[0])));
	$gol1_name = !is_array($gol1_id) ? '' : (count($gol1_id)==0 || in_array('group', $gol1_id) ? '' : $gol1_filter_title.' - '.getQuestionAnswer(179, $gol1_id[0]));
	$gol2_name = !is_array($gol2_id) ? '' : (count($gol2_id)==0 || in_array('group', $gol2_id) ? '' : $gol2_filter_title.' - '.getQuestionAnswer(180, $gol2_id[0]));
	$gol3_name = !is_array($gol3_id) ? '' : (count($gol3_id)==0 || in_array('group', $gol3_id) ? '' : $gol3_filter_title.' - '.getQuestionAnswer(181, $gol3_id[0]));
	$gol5_name = !is_array($gol5_id) ? '' : (count($gol5_id)==0 || in_array('group', $gol5_id) ? '' : $gol5_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $gol5_id[0])));
	$veolia1_name = !is_array($veolia1_id) ? '' : (count($veolia1_id)==0 || in_array('group', $veolia1_id) ? '' : $veolia1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $veolia1_id[0])));
	$touchpoints_name = !is_array($touchpoints_id) ? '' : (count($touchpoints_id)==0 || in_array('group', $touchpoints_id) ? '' : $touchpoints_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $touchpoints_id[0])));
	$employment_name = !is_array($employment_id) ? '' : (count($employment_id)==0 || in_array('group', $employment_id) ? '' : $employment_filter_title.' - '.getQuestionAnswer(373, $employment_id[0]));
	$race_name = !is_array($race_id) ? '' : (count($race_id)==0 || in_array('group', $race_id) ? '' : $race_filter_title.' - '.getQuestionAnswer(393, $race_id[0]));
	$ethnicity_name = !is_array($ethnicity_id) ? '' : (count($ethnicity_id)==0 || in_array('group', $ethnicity_id) ? '' : $ethnicity_filter_title.' - '.getQuestionAnswer(394, $ethnicity_id[0]));
	$politics_name = !is_array($politics_id) ? '' : (count($politics_id)==0 || in_array('group', $politics_id) ? '' : $politics_filter_title.' - '.getQuestionAnswer(395, $politics_id[0]));
	$children_number_name = !is_array($children_number_id) ? '' : (count($children_number_id)==0 || in_array('group', $children_number_id) ? '' : $children_number_filter_title.' - '.($children_number_id[0]==1 ? 'Yes' : 'No')
//    (getValue('children_number_name', 'children_number_filter_answers', 'children_number_filter_answers_', $children_number_id[0]))
	);
	$tccc_influence_name = !is_array($tccc_influence_id) ? '' : (count($tccc_influence_id)==0 || in_array('group', $tccc_influence_id) ? '' : $tccc_influence_filter_title.' - '.($tccc_influence_id[0]==1 ? 'Yes' : 'No'));
	$tccc_segment_name = !is_array($tccc_segment_id) || $tccc_segment_id[0]==0 ? '' : 
		(count($tccc_segment_id)==0 || in_array('group', $tccc_segment_id) ? '' : $tccc_segment_filter_title.' - '.	
		(json_decode(get_label('filter_answers_tccc_segment'), true)[$tccc_segment_id[0]-1][1])
	);
	$dmi1_name = !is_array($dmi1_id) ? '' : (count($dmi1_id)==0 || in_array('group', $dmi1_id) ? '' : $dmi1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $dmi1_id[0])));
	$energinet1_name = !is_array($energinet1_id) ? '' : (count($energinet1_id)==0 || in_array('group', $energinet1_id) ? '' : $energinet1_filter_title.' - '.getQuestionAnswer(459, $energinet1_id[0]));
	$stakeholder_type_name = !is_array($stakeholder_type_id) ? '' : (count($stakeholder_type_id)==0 || in_array('group', $stakeholder_type_id) ? '' : $stakeholder_type_filter_title.' - '.getQuestionAnswer(461, $stakeholder_type_id[0]));
	$hovedkategori_name = !is_array($hovedkategori_id) ? '' : (count($hovedkategori_id)==0 || in_array('group', $hovedkategori_id) ? '' : $hovedkategori_filter_title.' - '.getQuestionAnswer(551, $hovedkategori_id[0]));
	$siemensq1_name = !is_array($siemensq1_id) ? '' : (count($siemensq1_id)==0 || in_array('group', $siemensq1_id) ? '' : $siemensq1_filter_title.' - '.getQuestionAnswer(505, $siemensq1_id[0]));
	$association_name = !is_array($association_id) ? '' : (count($association_id)==0 || in_array('group', $association_id) ? '' : $association_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $association_id[0])));
	$segment2_name = !is_array($segment2_id) ? '' : (count($segment2_id)==0 || in_array('group', $segment2_id) ? '' : $segment2_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $segment2_id[0])));
	$fk1_name = !is_array($fk1_id) ? '' : (count($fk1_id)==0 || in_array('group', $fk1_id) ? '' : $fk1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $fk1_id[0])));
	$region_zone_name = !is_array($region_zone_id) ? '' : (count($region_zone_id)==0 || in_array('group', $region_zone_id) ? '' : $region_zone_filter_title.' - '.(getQuestionName('region_zone_name', 'region_zones', 'region_zones_id', $region_zone_id[0])));
	$education_name = !is_array($education_id) ? '' : (count($education_id)==0 || in_array('group', $education_id) ? '' : $education_filter_title.' - '.($education_id[0] == '9999' ? '{{EDUCATION_NOT_SURE}}' : getQuestionName('education_name', 'educations', 'educations_id', $education_id[0])));
	$income_name = !is_array($income_id) ? '' : (count($income_id)==0 || in_array('group', $income_id) ? '' : $income_filter_title.' - '.($income_id[0] == '9999' ? '{{INCOME_NOT_SURE}}' : getQuestionName('income_name', 'incomes', 'incomes_id', $income_id[0])));
	$cv2_name = !is_array($cv2_id) ? '' : (count($cv2_id)==0 || in_array('group', $cv2_id) ? '' : $cv2_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $cv2_id[0])));
	$cv3_name = !is_array($cv3_id) ? '' : (count($cv3_id)==0 || in_array('group', $cv3_id) ? '' : $cv3_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $cv3_id[0])));
//	$subregion_br_group_name = !is_array($subregion_br_group_id) ? '' : (count($subregion_br_group_id)==0 || in_array('group', $subregion_br_group_id) ? '' : $subregion_br_group_filter_title.' - '.getQuestionAnswer(782, $subregion_br_group_id[0]));
	$subregion_br_group_name = !is_array($subregion_br_group_id) || $subregion_br_group_id[0]==0 ? '' : 
		(count($subregion_br_group_id)==0 || in_array('group', $subregion_br_group_id) ? '' : $subregion_br_group_filter_title.' - '.
		(json_decode(get_label('filter_answers_subregion_br_group'), true)[$subregion_br_group_id[0]-1][1]));
	$wsa1_name = !is_array($wsa1_id) ? '' : (count($wsa1_id)==0 || in_array('group', $wsa1_id) ? '' : $wsa1_filter_title.' - '.getQuestionAnswer(794, $wsa1_id[0]));
	$wsa2_name = !is_array($wsa2_id) ? '' : (count($wsa2_id)==0 || in_array('group', $wsa2_id) ? '' : $wsa2_filter_title.' - '.getQuestionAnswer(795, $wsa2_id[0]));
	$wsa3_studyarea_name = !is_array($wsa3_studyarea_id) ? '' : (count($wsa3_studyarea_id)==0 || in_array('group', $wsa3_studyarea_id) ? '' : $wsa3_studyarea_filter_title.' - '.getQuestionAnswer(796, $wsa3_studyarea_id[0]));
	$sf1_name = !is_array($sf1_id) ? '' : (count($sf1_id)==0 || in_array('group', $sf1_id) ? '' : $sf1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $sf1_id[0])));
	$eon1_name = !is_array($eon1_id) ? '' : (count($eon1_id)==0 || in_array('group', $eon1_id) ? '' : $eon1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $eon1_id[0])));
	$eon_customer_name = !is_array($eon_customer_id) ? '' : (count($eon_customer_id)==0 || in_array('group', $eon_customer_id) ? '' : $eon_customer_filter_title.' - '.(getQuestionName('eon_customer_name', 'eon_customers', 'eon_customer_value', $eon_customer_id[0])));
	$gn2_name = !is_array($gn2_id) ? '' : (count($gn2_id)==0 || in_array('group', $gn2_id) ? '' : $gn2_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $gn2_id[0])));
	$gn1_name = !is_array($gn1_id) ? '' : 
		(count($gn1_id)==0 || in_array('group', $gn1_id) ? '' 
		: $gn1_filter_title.' - '.(json_decode(get_label('filter_answers_gn1'), true)[$gn1_id[0]][1]));
	$gn3_name = !is_array($gn3_id) ? '' : (count($gn3_id)==0 || in_array('group', $gn3_id) ? '' : $gn3_filter_title.' - '.getQuestionAnswer(897, $gn3_id[0]));
	$gn4_name = !is_array($gn4_id) ? '' : (count($gn4_id)==0 || in_array('group', $gn4_id) ? '' : $gn4_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $gn4_id[0])));
	$zs2_name = !is_array($zs2_id) ? '' : (count($zs2_id)==0 || in_array('group', $zs2_id) ? '' : $zs2_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $zs2_id[0])));
	$zs3_name = !is_array($zs3_id) ? '' : (count($zs3_id)==0 || in_array('group', $zs3_id) ? '' : $zs3_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $zs3_id[0])));
	$ess1_name = !is_array($ess1_id) ? '' : (count($ess1_id)==0 || in_array('group', $ess1_id) ? '' : $ess1_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $ess1_id[0])));
	$ess2_name = !is_array($ess2_id) ? '' : (count($ess2_id)==0 || in_array('group', $ess2_id) ? '' : $ess2_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $ess2_id[0])));
	$ori1_name = !is_array($ori1_id) ? '' : (count($ori1_id)==0 || in_array('group', $ori1_id) ? '' : $ori1_filter_title.' - '.getQuestionAnswer(975, $ori1_id[0]));
	$ovo_influencer_name = !is_array($ovo_influencer_id) ? '' : 
		(count($ovo_influencer_id)==0 || in_array('group', $ovo_influencer_id) ? '' : $ovo_influencer_filter_title.' - '.
		(json_decode(get_label('filter_answers_ovo_influencer'), true)[$ovo_influencer_id[0]][1]));
	$ovo_customer_name = !is_array($ovo_customer_id) ? '' : (count($ovo_customer_id)==0 || in_array('group', $ovo_customer_id) ? '' : $ovo_customer_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $ovo_customer_id[0])));
	$bay5_name = !is_array($bay5_id) ? '' : (count($bay5_id)==0 || in_array('group', $bay5_id) ? '' : $bay5_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $bay5_id[0])));
	$ethnicity_ca_name = !is_array($ethnicity_ca_id) ? '' : (count($ethnicity_ca_id)==0 || in_array('group', $ethnicity_ca_id) ? '' : $ethnicity_ca_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $ethnicity_ca_id[0])));
	$politics_ca_name = !is_array($politics_ca_id) ? '' : (count($politics_ca_id)==0 || in_array('group', $politics_ca_id) ? '' : $politics_ca_filter_title.' - '.getQuestionAnswer(1127, $politics_ca_id[0]));
	$air02_name = !is_array($air02_id) ? '' : (count($air02_id)==0 || in_array('group', $air02_id) ? '' : $air02_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $air02_id[0])));
	$air03_name = !is_array($air03_id) ? '' : (count($air03_id)==0 || in_array('group', $air03_id) ? '' : $air03_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $air03_id[0])));
	$air04_name = !is_array($air04_id) ? '' : (count($air04_id)==0 || in_array('group', $air04_id) ? '' : $air04_filter_title.' - '.(getQuestionName('question_name', 'questions', 'questions_id', $air04_id[0])));
	
	
	$country_name_export = '';
	$country_id_pure = [];
	if (is_array($country_id)) {
		foreach ($country_id as $this_country_id) {
			if ($this_country_id != 'group' && $this_country_id != 'multiple' && $this_country_id != 'all') $country_id_pure[] = $this_country_id;
		}
		$country_name_export = count($country_id_pure) == 0 || count($country_id_pure) > 1 ? $country_export_multiple : getQuestionName('country_name', 'countries', 'countries_id', $country_id_pure[0]); 
	}


// *********** 	Filter Names End ***********************
													 
													 
// *********  JSON series extra ******

 	$country_geo = in_array('group', $country_id) ? '' : getValue('country_geo', 'countries', 'countries_id', $country_id[0]);
	$region_type_geo = is_array($region_type_id) && in_array('group', $region_type_id) && isset($region_type_id[0]) ? '' : getValue('region_type_geo', 'region_types', 'region_types_id', $region_type_id[0]);
	$region_geo = is_array($region_id) && in_array('group', $region_id) && isset($region_id[0]) ? '' : getValue('region_geo', TABLE_REGIONS, 'regions_id', $region_id[0]);

    $all_companies = in_array('all', $company_id);
    $all_attributes = in_array('all', $attribute_id);

	$company_name = $single_company_id ? $company_name : get_label($all_companies ? 'chart_legend_all_companies' : 'chart_legend_multiple_companies');
	$attribute_name = $single_attribute_id ? $attribute_name : sprintf(get_label($all_attributes ? 'chart_legend_all_group_elements' : 'chart_legend_multiple_group_elements'), translate($attribute_group_name));
	
	$json_series_extras = [
		translate($company_name), 
		translate($attribute_name), 
		translate($gender_name), 
		translate($age_name), 
		translate($country_name), 
		translate($region_type_name), 
		translate($region_name), 
		translate($education_type_name), 
		translate($income_type_name), 
		translate($segment_name), 
		translate($industry_name), 
		translate($company_size_name), 
		translate($diagnosis_name), 
		translate($family_name), 
		translate($children_name), 
		translate($housing_name), 
		translate($living_name), 
		translate($engagement_name), 
		translate($facilities_name), 
		translate($tg1_name), 
		translate($tg2_name), 
		translate($novo1_name), 
		translate($novo2_name), 
		translate($demant2_name), 
		translate($rw1_name), 
		translate($rw2_name), 
		translate($gr1_name), 
		translate($gr2_name), 
		translate($v_customer_name), 
		translate($gol1_name), 
		translate($gol2_name), 
		translate($gol3_name), 
		translate($gol5_name), 
		translate($veolia1_name), 
		translate($touchpoints_name), 
		translate($employment_name), 
		translate($race_name), 
		translate($ethnicity_name), 
		translate($politics_name), 
		translate($children_number_name), 
		translate($tccc_influence_name), 
		translate($tccc_segment_name), 
		translate($dmi1_name), 
		translate($energinet1_name), 
		translate($stakeholder_type_name), 
		translate($hovedkategori_name), 
		translate($siemensq1_name), 
		translate($association_name), 
		translate($segment2_name),
		translate($fk1_name), 
		translate($region_zone_name), 
		translate($education_name), 
		translate($income_name), 
		translate($cv2_name), 
		translate($cv3_name), 
		translate($subregion_br_group_name), 
		translate($wsa1_name), 
		translate($wsa2_name), 
		translate($wsa3_studyarea_name),
		translate($sf1_name), 
		translate($eon1_name), 
		translate($eon_customer_name), 
		translate($gn2_name), 
		translate($gn1_name), 
		translate($gn3_name), 
		translate($gn4_name), 
		translate($zs2_name), 
		translate($zs3_name), 
		translate($ess1_name), 
		translate($ess2_name), 
		translate($ori1_name), 
		translate($ovo_influencer_name), 
		translate($ovo_customer_name), 
		translate($bay5_name),
		translate($ethnicity_ca_name), 
		translate($politics_ca_name), 
		translate($air02_name), 
		translate($air03_name), 
		translate($air04_name), 
		$country_geo,
		[$region_geo, $region_type_geo],
		$single_attribute_id,
		translate($country_name_export)
	];
	

// *********  JSON series extra END ******
													 
// determine interval or attribute-dependent etc variables - and first of all - calculation algorithm
    
	$cache_on = get_label('redis_cache_on')==1;
	$interval_sql_filter_str = "";
	$period_sql_filter_str = "";
	$interval_pregroup = "`interval`";
	
	$algorithm = 1;
	$years_where = '';
	$customer_startdate = getValue('customer_startdate', 'customers', 'customers_id', $customer_id); // get the customer's startdate to cut off unavailable data periods
	if ( is_array($interval_id) ) { $interval_id = implode('',$interval_id); }
	
	if ( in_array($interval_id, ['week','month','quarter','year']) ) {
		$date_exploded = explode('-',str_replace(' - ','-',$period_id));
	} else {
		$date_exploded = explode('-',str_replace(' - ','-',$interval_id));
	}
	echo '$date_from_cur - '. $date_from_cur = is_array($date_exploded) && isset($date_exploded[0]) && isset(explode('/',$date_exploded[0])[2]) ? $date_exploded[0] : date('d/m/Y'/*, strtotime('yesterday')*/);
	echo '<br>$date_to_cur - '. $date_to_cur = is_array($date_exploded) && isset($date_exploded[1]) && isset(explode('/',$date_exploded[1])[2]) ? $date_exploded[1] : date('d/m/Y'/*, strtotime('yesterday')*/);
	echo '<br>$date_from_cur_d - '. $date_from_cur_d = explode('/',$date_from_cur)[0];
	echo '<br> $date_from_cur_m- '. $date_from_cur_m = explode('/',$date_from_cur)[1];
	echo '<br> $date_from_cur_y - '. $date_from_cur_y = explode('/',$date_from_cur)[2];
	echo '<br> $date_from_cur- '. $date_from_cur = substr('20'.$date_from_cur_y,-4).'-'.$date_from_cur_m.'-'.$date_from_cur_d;
	
	$date_from_cur = strtotime($customer_startdate) > strtotime($date_from_cur) ? $customer_startdate : $date_from_cur; // ensure $date_from_cur is not earlier than $customer_startdate

	$date_to_cur_d = explode('/',$date_to_cur)[0];
	$date_to_cur_m = explode('/',$date_to_cur)[1];
	$date_to_cur_y = explode('/',$date_to_cur)[2];
	$date_to_cur = substr('20'.$date_to_cur_y,-4).'-'.$date_to_cur_m.'-'.$date_to_cur_d;
	$date_from_cur_date = new DateTime($date_from_cur);
	$date_to_cur_date = new DateTime($date_to_cur);
	$days = $date_to_cur_date->diff($date_from_cur_date)->format("%a") + 1;
	$date_from_prev_date = strtotime('-'.$days.' days', strtotime($date_from_cur));
	$date_from_prev = date('Y-m-d', $date_from_prev_date); 
	
	$date_from_prev = strtotime($customer_startdate) > strtotime($date_from_prev) ? $customer_startdate : $date_from_prev; // ensure $date_from_prev is not earlier than $customer_startdate
	
	$date_to_prev = date('Y-m-d', strtotime('-1 days', strtotime($date_from_cur)));

	if ( in_array($interval_id, ['week','month','quarter','year']) ) $period_sql_filter_str = " AND (`ENDDATE` BETWEEN '$date_from_cur' AND '$date_to_cur') ";
	
	switch ($interval_id) {
		case ('week'): 
			$interval_sql_str = "`interval_week`"; 
			break;
		case ('month'): 
			$interval_sql_str = "`interval_month`"; 
			break;
		case ('quarter'): 
			$interval_sql_str = "`interval_quarter`"; 
//?POR-793			$interval_pregroup = "`interval_month`";
			break;
		case ('year'): 
			$interval_sql_str = "`interval_year`"; 
//?POR-793			$interval_pregroup = "`interval_month`";
			break;
		default: // dashboard dates
			$interval_sql_str = " IF(`ENDDATE` BETWEEN '$date_from_prev' AND '$date_to_prev', 0, IF(`ENDDATE` BETWEEN '$date_from_cur' AND '$date_to_cur', 1, 2)) "; 
			
			$dates_between = " (`ENDDATE` BETWEEN '$date_from_prev' AND '$date_to_cur') AND ";
			
			$year_from = date('Y', $date_from_prev_date);
			$year_to = $date_to_cur_date->format('Y');
			$years_where = '(`YEAR`'.($year_from==$year_to ? " = {$year_from}) " : " BETWEEN {$year_from} AND {$year_to})").' AND ';
// TODO			$years_where .= ($date_from_cur_y==$date_to_cur_y ? "(`MONTH` BETWEEN $date_from_cur_m AND $date_to_cur_m) AND " : '');
	}
	
    $field_name = get_field_name($attribute_id[0]);
	if (explode('_',$attribute_id[0])[0]=='S104') { $algorithm = 2; } // awareness
	if (explode('_',$attribute_id[0])[0]=='S105') { $algorithm = 3; } // familiarity	
	if ($field_algorithm==4) { $algorithm = 4; } //wordcloud  // receiving field `field_algorithm` from `fields` table using $attribute_id[0] as field_name
	
	$field_project = getValue('field_project', 'fields', 'field_name', $field_name, 1); 
	$is_subscription = $ajax_key==='subscription';
	if ($field_project>0 && $field_project!=$project_id	// skip field if field not in current project
		&& !$is_subscription 	// POR-1868 fix for project_id!=field_project in subscription for custom core element
	   ) 
	{ 
		$algorithm = -1; 
	}
	
	// checking current field in views list
	$view_fields = getClientViewFields($client_id);
	$view_fields_array = explode(',', $view_fields);
	
	if ($field_name!='group' && $view_fields!==false && !in_array($field_name, $view_fields_array)) 
	{ 
		$algorithm = -2;
        debug_log("statistics: -2, client_id = $client_id, field_name = $field_name, view_fields = $view_fields");
	}
		
	if (in_array('all', $company_id) && !in_array('group', $company_id)) // turn cache off for subscription function
	{
		$cache_on = false; 
	}
	
	// group and order sql str
	if ( in_array('all', $company_id) && !in_array('group', $company_id) ) {
		$group_order_sql_str = " GROUP BY `RATING`, `interval` ORDER BY `RATING` ASC, `interval` ASC ";
	} else {
		$group_order_sql_str = " GROUP BY `interval` ORDER BY `interval` ASC ";
	}

	// get activity data for the chart view
	$events = [];
	$campaigns = [];
	$query_activities = 
		"SELECT ".
        	"`activities_id`, ".
			"`activity_type`, ".
			"`activity_name`, ".
			"`activity_start`, ".
			"`activity_end`, ".
			"`activity_color`, ".
			"CONV(`activity_color`,10,16) AS `activity_color_hex`, ".
			"DATE_FORMAT(`activity_start`, '%d %b %Y') AS `activity_start_formatted`, ".
			"DATE_FORMAT(`activity_end`, '%d %b %Y') AS `activity_end_formatted`, ".
			"`activity_height`, ".
			"`activity_country`, ".
			"UNIX_TIMESTAMP(`activity_start`) * 1000 AS `activity_start_date`, ".
			"UNIX_TIMESTAMP(`activity_end`) * 1000 AS `activity_end_date`, ".
			"`activity_details`, ".
			"`activity_created` ".
		"FROM  ".
			"`activities` ".
		"WHERE  ".
			"`activity_customer` = '$customer_id' ".
			"AND `activity_start` BETWEEN '$date_from_cur' AND '$date_to_cur' ".
		$filter_activity_rating_sql_str.
		$filter_activity_country_sql_str;
	
	$sql_activities = query($query_activities);
    
    if ($sql_activities)
    {
        while ($row = sql_fetch_array($sql_activities)) 
        {
            $activity_created = getValue('email', 'users', 'users_id', $row['activity_created']);
            if ($row['activity_type'] == 1) {
                $events[] = [
                    $row['activity_start_date'],
                    $row['activity_height'],
                    $row['activity_start_formatted'],
                    $row['activity_name'],
                    $row['activity_details'],
                    $activity_created,
                    $row['activities_id']
                ];
            }
            if ($row['activity_type'] == 2) {
                $campaigns[] = [
                    $row['activity_start_date'],
                    $row['activity_end_date'],
                    $row['activity_color_hex'],
                    $row['activity_start_formatted'],
                    $row['activity_end_formatted'],
                    $row['activity_name'],
                    $row['activity_details'],
                    $activity_created,
                    $row['activities_id']
                ];
            }
        } // while
    }
    
	// create field options object for each series to use on frontend in ajax response
	$field_options = new stdClass();
	$field_percent = getValue('field_percent', 'fields', 'field_name', $field_name, 1); 
	$field_options->name = $field_name;
	$field_options->percent = $field_percent;
    
	// get redis caching key and retrieve cached value
	if ($date_to_cur >= date('Y-m-d')) $cache_on = false; // disable caching if period ending is more than yesterday
	if ($cache_on===true) 
    {
		$redis_key = 
			str_replace(' ', '', 'project:'.$project_id.
            '|client:'.$client_id. 
			($placeholder_id===0 ? '' : '|placeholder:'.$placeholder_id).												
			'|company:'.implode('_', (is_array($company_id) ? $company_id : [$company_id])).
			'|attribute:'.implode('_', (is_array($attribute_id) ? $attribute_id : [$attribute_id])).
			'|interval:'.implode('_', (is_array($interval_id) ? $interval_id : [$interval_id])).
			'|period:'.implode('_', (is_array($period_id) ? $period_id : [$period_id])).
			'|gender:'.implode('_', (is_array($gender_id) ? $gender_id : [$gender_id])).
			'|age:'.implode('_', (is_array($age_id) ? $age_id : [$age_id])).
			'|country:'.implode('_', (is_array($country_id) ? $country_id : [$country_id])).
			'|region_type:'.implode('_', (is_array($region_type_id) ? $region_type_id : [$region_type_id])).
			'|region:'.implode('_', (is_array($region_id) ? $region_id : [$region_id])).
			'|education_type:'.implode('_', (is_array($education_type_id) ? $education_type_id : [$education_type_id])).
			'|income_type:'.implode('_', (is_array($income_type_id) ? $income_type_id : [$income_type_id])).
			'|segment:'.implode('_', (is_array($segment_id) ? $segment_id : [$segment_id])).
			'|industry:'.implode('_', (is_array($industry_id) ? $industry_id : [$industry_id])).
			'|company_size:'.implode('_', (is_array($company_size_id) ? $company_size_id : [$company_size_id])).
			'|diagnosis:'.implode('_', (is_array($diagnosis_id) ? $diagnosis_id : [$diagnosis_id])).
			'|family:'.implode('_', (is_array($family_id) ? $family_id : [$family_id])).
			'|children:'.implode('_', (is_array($children_id) ? $children_id : [$children_id])).
			'|housing:'.implode('_', (is_array($housing_id) ? $housing_id : [$housing_id])).
			'|living:'.implode('_', (is_array($living_id) ? $living_id : [$living_id])).
			'|engagement:'.implode('_', (is_array($engagement_id) ? $engagement_id : [$engagement_id])).
			'|facilities:'.implode('_', (is_array($facilities_id) ? $facilities_id : [$facilities_id])).
			'|tg1:'.implode('_', (is_array($tg1_id) ? $tg1_id : [$tg1_id])).
			'|tg2:'.implode('_', (is_array($tg2_id) ? $tg2_id : [$tg2_id])).
			'|novo1:'.implode('_', (is_array($novo1_id) ? $novo1_id : [$novo1_id])).
			'|novo2:'.implode('_', (is_array($novo2_id) ? $novo2_id : [$novo2_id])).
			'|demant2:'.implode('_', (is_array($demant2_id) ? $demant2_id : [$demant2_id])).
			'|rw1:'.implode('_', (is_array($rw1_id) ? $rw1_id : [$rw1_id])).
			'|rw2:'.implode('_', (is_array($rw2_id) ? $rw2_id : [$rw2_id])).
			'|gr1:'.implode('_', (is_array($gr1_id) ? $gr1_id : [$gr1_id])).
			'|gr2:'.implode('_', (is_array($gr2_id) ? $gr2_id : [$gr2_id])).
			'|v_customer:'.implode('_', (is_array($v_customer_id) ? $v_customer_id : [$v_customer_id])).
			'|gol1:'.implode('_', (is_array($gol1_id) ? $gol1_id : [$gol1_id])).
			'|gol2:'.implode('_', (is_array($gol2_id) ? $gol2_id : [$gol2_id])).
			'|gol3:'.implode('_', (is_array($gol3_id) ? $gol3_id : [$gol3_id])).
			'|gol5:'.implode('_', (is_array($gol5_id) ? $gol5_id : [$gol5_id])).
			'|veolia1:'.implode('_', (is_array($veolia1_id) ? $veolia1_id : [$veolia1_id])).
			'|touchpoints:'.implode('_', (is_array($touchpoints_id) ? $touchpoints_id : [$touchpoints_id])).
			'|employment:'.implode('_', (is_array($employment_id) ? $employment_id : [$employment_id])).
			'|race:'.implode('_', (is_array($race_id) ? $race_id : [$race_id])).
			'|ethnicity:'.implode('_', (is_array($ethnicity_id) ? $ethnicity_id : [$ethnicity_id])).
			'|politics:'.implode('_', (is_array($politics_id) ? $politics_id : [$politics_id])).
			'|children_number:'.implode('_', (is_array($children_number_id) ? $children_number_id : [$children_number_id])).
			'|tccc_influence:'.implode('_', (is_array($tccc_influence_id) ? $tccc_influence_id : [$tccc_influence_id])).
			'|tccc_segment:'.implode('_', (is_array($tccc_segment_id) ? $tccc_segment_id : [$tccc_segment_id])).
			'|dmi1:'.implode('_', (is_array($dmi1_id) ? $dmi1_id : [$dmi1_id])).
			'|energinet1:'.implode('_', (is_array($energinet1_id) ? $energinet1_id : [$energinet1_id])).
			'|stakeholder_type:'.implode('_', (is_array($stakeholder_type_id) ? $stakeholder_type_id : [$stakeholder_type_id])).
			'|hovedkategori:'.implode('_', (is_array($hovedkategori_id) ? $hovedkategori_id : [$hovedkategori_id])).
			'|siemensq1:'.implode('_', (is_array($siemensq1_id) ? $siemensq1_id : [$siemensq1_id])).
			'|association:'.implode('_', (is_array($association_id) ? $association_id : [$association_id])).
			'|segment2:'.implode('_', (is_array($segment2_id) ? $segment2_id : [$segment2_id])).
			'|fk1:'.implode('_', (is_array($fk1_id) ? $fk1_id : [$fk1_id])).
			'|region_zone:'.implode('_', (is_array($region_zone_id) ? $region_zone_id : [$region_zone_id])).
			'|education:'.implode('_', (is_array($education_id) ? $education_id : [$education_id])).
			'|income:'.implode('_', (is_array($income_id) ? $income_id : [$income_id])).										
			'|cv2:'.implode('_', (is_array($cv2_id) ? $cv2_id : [$cv2_id])).
			'|cv3:'.implode('_', (is_array($cv3_id) ? $cv3_id : [$cv3_id])).
			'|subregion_br_group:'.implode('_', (is_array($subregion_br_group_id) ? $subregion_br_group_id : [$subregion_br_group_id])).
			'|wsa1:'.implode('_', (is_array($wsa1_id) ? $wsa1_id : [$wsa1_id])).
			'|wsa2:'.implode('_', (is_array($wsa2_id) ? $wsa2_id : [$wsa2_id])).
			'|wsa3_studyarea:'.implode('_', (is_array($wsa3_studyarea_id) ? $wsa3_studyarea_id : [$wsa3_studyarea_id])).
			'|sf1:'.implode('_', (is_array($sf1_id) ? $sf1_id : [$sf1_id])).
			'|eon1:'.implode('_', (is_array($eon1_id) ? $eon1_id : [$eon1_id])).
			'|eon_customer:'.implode('_', (is_array($eon_customer_id) ? $eon_customer_id : [$eon_customer_id])).
			'|gn2:'.implode('_', (is_array($gn2_id) ? $gn2_id : [$gn2_id])).
			'|gn1:'.implode('_', (is_array($gn1_id) ? $gn1_id : [$gn1_id])).
			'|gn3:'.implode('_', (is_array($gn3_id) ? $gn3_id : [$gn3_id])).
			'|gn4:'.implode('_', (is_array($gn4_id) ? $gn4_id : [$gn4_id])).
			'|zs2:'.implode('_', (is_array($zs2_id) ? $zs2_id : [$zs2_id])).
			'|zs3:'.implode('_', (is_array($zs3_id) ? $zs3_id : [$zs3_id])).
			'|ess1:'.implode('_', (is_array($ess1_id) ? $ess1_id : [$ess1_id])).
			'|ess2:'.implode('_', (is_array($ess2_id) ? $ess2_id : [$ess2_id])).
			'|ori1:'.implode('_', (is_array($ori1_id) ? $ori1_id : [$ori1_id])).
			'|ovo_influencer:'.implode('_', (is_array($ovo_influencer_id) ? $ovo_influencer_id : [$ovo_influencer_id])).
			'|ovo_customer:'.implode('_', (is_array($ovo_customer_id) ? $ovo_customer_id : [$ovo_customer_id])).
			'|bay5:'.implode('_', (is_array($bay5_id) ? $bay5_id : [$bay5_id])).
			'|ethnicity_ca:'.implode('_', (is_array($ethnicity_ca_id) ? $ethnicity_ca_id : [$ethnicity_ca_id])).
			'|politics_ca:'.implode('_', (is_array($politics_ca_id) ? $politics_ca_id : [$politics_ca_id])).
			'|air02:'.implode('_', (is_array($air02_id) ? $air02_id : [$air02_id])).
			'|air03:'.implode('_', (is_array($air03_id) ? $air03_id : [$air03_id])).
			'|air04:'.implode('_', (is_array($air04_id) ? $air04_id : [$air04_id])).
			'')
		;
		
		$redis_value = get_redis_key($redis_key);
		// if redis value exists - create a filter for latest interval from redis value series to be the only one recalculated
		
		if ($redis_value!==false) 
        {
			if (get_label('log_stat_queries')=='1')
			{
				debug_log('statistics.fnc.php redis_key='.$redis_key);
				debug_log('statistics.fnc.php redis_value='.$redis_value);
			}
/*			
			$redis_interval_last = json_decode($redis_value);
			$redis_interval_last = end(json_decode($redis_interval_last[0]));
			$redis_interval_last = $redis_interval_last[0];	
			$interval_sql_filter_str = " AND $interval_sql_str >= $redis_interval_last ";
*/			
            $json_series_extras[] = $events;
			$json_series_extras[] = $campaigns;
			$json_series_extras[] = $field_options;
			
            $json_final = [
				$redis_value,
				$json_series_extras,
				$ajax_key
			];	

			$json_final = json_encode($json_final);

			return $json_final;
		}
	}
	
	$filter_sql_str = (isset($dates_between) ? $dates_between : $years_where).$filter_sql_str.$period_sql_filter_str.$interval_sql_filter_str;
	
	// get series data depending on algorithm
	$query = '/*no algorithm*/'." field_name=$field_name, field_project=$field_project, project = $project_id, site_project=".get_site_project();
	
	// most atributes
	if ($algorithm==1) 
    {
        $query = 
        "SELECT ".
            " `RATING`, ".
            " `interval`, ".
            " $attribute_level_up_sql_str ".	// $attribute_level_up_sql_str .= " $sql_func(`$_attribute_id`) AS `$_attribute_id`, ";
            " SUM(`responce_count`) AS `responce_count`, ".
            " MIN(`responce_min_date`) AS `responce_min_date`, ".
            " MAX(`responce_max_date`) AS `responce_max_date`   
        FROM
        (
            SELECT ".
                " `RATING`, ".
                " `interval`, ".
                " $attribute_level_up_sql_str ".	// $attribute_level_up_sql_str .= " $sql_func(`$_attribute_id`) AS `$_attribute_id`, ";
                " SUM(`responce_count`) AS `responce_count`, ".
                " MIN(`responce_min_date`) AS `responce_min_date`, ".
                " MAX(`responce_max_date`) AS `responce_max_date` ".   
            "FROM
            (
                SELECT ".
                    " `RATING`, ".
                    " `interval_month`, ".
                    " $interval_sql_str AS `interval`, ".
                    " $attribute_sql_str ".	// $attribute_sql_str .= " $sql_func($_attribute_sql) AS `$_attribute_id`, ";
                    " COUNT(*) AS `responce_count`, ".
                    " MIN(`ENDDATE`) AS `responce_min_date`, ".
                    " MAX(`ENDDATE`) AS `responce_max_date` ".
                " FROM `data` FORCE INDEX (ALGO1_1) ".	// POR-867_20.81a
                " WHERE ".
                    " `Q305_2` BETWEEN 1 AND 7 ".
                    " AND `Q305_3` BETWEEN 1 AND 7 AND ".
                    " $filter_sql_str ".
                " GROUP BY ".
                    " `RATING`, ".
                    " $interval_pregroup ".
            ") AS `sub_q_1`
            GROUP BY `RATING`, `interval` 
        ) AS `sub_q_2`
        $group_order_sql_str
        ";
    }
    
	$force_index = strpos($filter_sql_str,'REGION') ? "FORCE INDEX (".(isset($dates_between) ? "ALGO3" : "CHART").")" : '';
	
	// awereness
	// new version #2 - with quotas but without pregrouping
	if ($algorithm==2) 
	{
		$query = 
			"SELECT 
				`RATING`, 
				`interval`, 
				ROUND(AVG(`attribute`), 0) AS `attribute`, 
				SUM(`responce_count`) AS `responce_count`
			  FROM
			  (
				SELECT 
				  `RATING`, 
				  `interval_month`, 
				  $interval_sql_str AS `interval`, 
				  100*SUM($attribute_sql_str)/SUM(`quota_counter`) AS `attribute`, 
				  SUM(`quota_counter`) AS `responce_count`  
				FROM `data` $force_index 
				LEFT JOIN `quotas` ON `quota_rating` = `RATING` AND `quota_pid` = `CODERESP` 

				WHERE $filter_sql_str 
				GROUP BY `RATING`, `interval`
			  ) AS `sub_q_2`
			  $group_order_sql_str
			";
	}
/*	
	// new version #1- without quotas table and without pregrouping - but getting the same results
	if ($algorithm == 2) 
	{
		$query = 
			"SELECT 
				`RATING`, 
				`interval`, 
				ROUND(AVG(`attribute`), 0) AS `attribute`, 
				SUM(`responce_count`) AS `responce_count`
			FROM (
				SELECT 
					`RATING`, 
					`interval_month`, 
					$interval_sql_str AS `interval`, 
					100*SUM($attribute_sql_str)/COUNT(*) AS `attribute`, 
					COUNT(*) AS `responce_count`	
				FROM `data` 
					WHERE $filter_sql_str 
				GROUP BY `RATING`, `interval`
			) AS `sub_q_2`
			$group_order_sql_str
		"; 
	} 
*/
		
/*
//- old version with quotas table and pregrouping
	if ($algorithm == 2) 
	{
		$query = 
		"SELECT `RATING`, `interval`, ROUND(AVG(`attribute`), 0) AS `attribute`, SUM(`responce_count`) AS `responce_count`
		FROM
		(
			SELECT `RATING`, `interval`, AVG(`attribute`) AS `attribute`, SUM(`responce_count`) AS `responce_count`
			FROM
			(
				SELECT `RATING`, `interval_month`, $interval_sql_str AS `interval`, 100*SUM($attribute_sql_str)/SUM(`quota_counter`) AS `attribute`, SUM(`quota_counter`) AS `responce_count`	
				FROM `".TABLE_QUOTAS."`  
				LEFT JOIN `".TABLE_DATA."` ON `quota_rating` = `RATING` AND `quota_pid` = `CODERESP` 
				WHERE $filter_sql_str 
				GROUP BY `RATING`, $interval_pregroup
			) AS `sub_q_1`	
			GROUP BY `RATING`, `interval` 		
		) AS `sub_q_2`
		$group_order_sql_str
		";
	}
*/	
	
// familiarity
/*	"CREATE TEMPORARY TABLE `sub_q_1` ".
	"
		SELECT `RATING`, `CODERESP`, `interval_month`, $interval_sql_str AS `interval`, AVG($attribute_sql_str) AS `attribute`   
		FROM `".TABLE_DATA."` 
		WHERE $filter_sql_str
		GROUP BY `RATING`, `CODERESP`
	;
	".	
*/	

// familiarity without quotas table		
/*		
"SELECT `RATING`, `interval`, ROUND(AVG(`attribute`), 0) AS `attribute`, `responce_count` 
	FROM 
	(
		SELECT `sub_q_2_1`.`RATING` AS `RATING`, `sub_q_2_1`.`interval` AS `interval`, 100*`attribute_s104`*`attribute_s105` AS `attribute`, `responce_count`  
		FROM 
		(	
			SELECT `RATING`, `interval_month`, `interval`, SUM(`attribute`)/SUM(`quota_counter`) AS `attribute_s104`  
			FROM
			(
				SELECT `RATING`, `interval_month`, `interval`, `quota_counter`, AVG(`attribute`) AS `attribute`  
				FROM  
					
					(
						SELECT `RATING`, `CODERESP`, `interval_month`, $interval_sql_str AS `interval`, AVG($attribute_sql_str) AS `attribute`,
							COUNT(*) as `quota_counter`
						FROM `data` 
						WHERE $filter_sql_str
						GROUP BY `RATING`, `CODERESP`
					) AS 
 
					`sub_q_1` 
					
				  
				GROUP BY `RATING`, `CODERESP`
			) AS `sub_q_2`
			GROUP BY `RATING`, $interval_pregroup
		) AS `sub_q_2_1`, 
		(
			SELECT `RATING`, `interval_month`, `interval`, SUM(`attribute_1`)/SUM(`attribute_2`) AS `attribute_s105`  
			FROM
				(
					SELECT `RATING`, `CODERESP`, `interval_month`, $interval_sql_str AS `interval`, AVG($attribute_sql_str_2) AS `attribute_1`, AVG($attribute_sql_str) AS `attribute_2` 
					FROM `data` 
					WHERE $filter_sql_str
					GROUP BY `RATING`, `CODERESP`
				) AS `sub_q_3`
			GROUP BY `RATING`, $interval_pregroup
		) AS `sub_q_4`,
		(
		SELECT `interval`, SUM(`attribute_2`) AS `responce_count`   
			FROM
				(
					SELECT $interval_sql_str AS `interval`, AVG($attribute_sql_str) AS `attribute_2` 
					FROM `data` 
					WHERE $filter_sql_str
					GROUP BY `RATING`, `CODERESP`
				) AS `sub_q_5`
			GROUP BY `interval`
		) AS `sub_q_6` 
		WHERE `sub_q_2_1`.`interval` = `sub_q_4`.`interval` AND `sub_q_2_1`.`RATING` = `sub_q_4`.`RATING` AND `sub_q_2_1`.`interval` = sub_q_6.`interval` 
	) AS `sub_q_7` 
	$group_order_sql_str
	";
		
	*/	
		
	if ($algorithm==3)
    {
        $query = 
        "SELECT `RATING`, `interval`, ROUND(AVG(`attribute`), 0) AS `attribute`, `responce_count` 
        FROM 
        (
            SELECT `sub_q_2_1`.`RATING` AS `RATING`, `sub_q_2_1`.`interval` AS `interval`, 100*`attribute_s104`*`attribute_s105` AS `attribute`, `responce_count`  
            FROM 
            (	
                SELECT `RATING`, `interval_month`, `interval`, SUM(`attribute`)/SUM(`quota_counter`) AS `attribute_s104`  
                FROM
                (
                    SELECT `RATING`, `interval_month`, `interval`, `quota_counter`, AVG(`attribute`) AS `attribute`  
                    FROM `quotas`, 
                        (
                            SELECT `RATING`, `CODERESP`, `interval_month`, $interval_sql_str AS `interval`, AVG($attribute_sql_str) AS `attribute`
                            FROM `data` $force_index
                            WHERE $filter_sql_str
                            GROUP BY `RATING`, `CODERESP`
                        ) AS 
                        `sub_q_1` 

                      WHERE `quota_rating` = `RATING`	AND `quota_pid` = `CODERESP`
                    GROUP BY `RATING`, `CODERESP`
                ) AS `sub_q_2`
                GROUP BY `RATING`, $interval_pregroup
            ) AS `sub_q_2_1`, 
            (
                SELECT `RATING`, `interval_month`, `interval`, SUM(`attribute_1`)/SUM(`attribute_2`) AS `attribute_s105`  
                FROM
                    (
                        SELECT `RATING`, `CODERESP`, `interval_month`, $interval_sql_str AS `interval`, AVG($attribute_sql_str_2) AS `attribute_1`, AVG($attribute_sql_str) AS `attribute_2` 
                        FROM `data` $force_index
                        WHERE $filter_sql_str
                        GROUP BY `RATING`, `CODERESP`
                    ) AS `sub_q_3`
                GROUP BY `RATING`, $interval_pregroup
            ) AS `sub_q_4`,
            (
            SELECT `interval`, SUM(`attribute_2`) AS `responce_count`   
                FROM
                    (
                        SELECT $interval_sql_str AS `interval`, AVG($attribute_sql_str) AS `attribute_2` 
                        FROM `data` $force_index
                        WHERE $filter_sql_str
                        GROUP BY `RATING`, `CODERESP`
                    ) AS `sub_q_5`
                GROUP BY `interval`
            ) AS `sub_q_6` 
            WHERE `sub_q_2_1`.`interval` = `sub_q_4`.`interval` AND `sub_q_2_1`.`RATING` = `sub_q_4`.`RATING` AND `sub_q_2_1`.`interval` = sub_q_6.`interval` 
        ) AS `sub_q_7` 
        $group_order_sql_str
        ";
    }
    
	// wordcloud algorithm
	if ($algorithm==4)
    {
        $query = 
        "SELECT ".
            " '0' AS `group`, ".
            //" `interval_month`, ".
            //" $interval_sql_str AS `interval`, ".
            " `RATING`, ".
            " $attribute_sql_str ".	// $attribute_sql_str .= " $sql_func($_attribute_sql) AS `$_attribute_id`, ";
            " COUNT(*) AS `responce_count`, ".
            " MIN(`ENDDATE`) AS `responce_min_date`, ".
            " MAX(`ENDDATE`) AS `responce_max_date` ".
        " FROM `data` FORCE INDEX (ALGO1_1) ".	// POR-867_20.81a
        " WHERE ".
            " `Q305_2` BETWEEN 1 AND 7 ".
            " AND `Q305_3` BETWEEN 1 AND 7 AND ".
            " $filter_sql_str ".
        //" GROUP BY ".
            //" `RATING`, ".
            //" $interval_pregroup ".
        "GROUP BY `group`";
    }
	
		// most atributes
	
	/*
		WHERE `sub_q_2_1`.`interval` = `sub_q_4`.`interval` AND `sub_q_2_1`.`interval` = sub_q_6.`interval`
	*/ // was
	
	
	/*  
		WHERE `sub_q_2_1`.`interval` = `sub_q_4`.`interval` AND `sub_q_2_1`.`RATING` = `sub_q_4`.`RATING` AND `sub_q_2_1`.`interval` = sub_q_6.`interval` 
		GROUP BY `sub_q_2_1`.`interval`, `sub_q_4`.`interval`, `sub_q_2_1`.`RATING`, `sub_q_4`.`RATING` 
	*/ // became
	
	/*
		GROUP BY `RATING`, $interval_pregroup
	*/
	$query = "/* a=$algorithm */ ".$query;
	
	if (get_label('log_stat_queries')>0) 
    { 
		debug_log('statistics.fnc.php (project='.$project_id.'): 
		'.$query); 
	}
	
	$json_series_data = NULL; 

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if ($timing) { debug_log('statistics.fnc.php time 00: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	$sql = query($query);
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($timing) 
    {
        $_time = (microtime(true)-$start_time)*1000;
//			debug_log('statistics.fnc.php time 01: '.$_time.'ms', $timing_log); $start_time = microtime(true); 
        if ($_time > get_label('statistics_slow_query_timing')) // {{statistics_slow_query_timing}}=500
        { 
            debug_log("statistics.fnc.php time=$_time : 
            ".$query); 
        }
    }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	if ($algorithm>0 && $sql) 
    {
		while($row = sql_fetch_assoc($sql)) 
        {
			$attribute_field = '';
			if ($algorithm==1) 
            {
				$attribute_group_avg = 0;
				$attribute_not_null_count = 0;
				foreach ($attribute_id as $_attribute) 
                {
					if ($_attribute != 'group' && $_attribute != 'all') 
					{
						$_attribute_parts = explode('_',$_attribute);
						$_attribute_name = $_attribute_parts[0];
						$_attribute_suffix = isset($_attribute_parts[1]) ? $_attribute_parts[1] : '';
						$_attribute_column = $_attribute_name.(!empty($_attribute_suffix) ? '_'.$_attribute_suffix : '');
						
						$attribute_field = isset($row[$_attribute]) ? $_attribute : (isset($row[$_attribute_column]) ? $_attribute_column : '');
						$add_value = !empty($attribute_field) ? $row[$attribute_field] : 0;
						
						$attribute_group_avg += $add_value;
						if ($add_value) $attribute_not_null_count++;
					}
				}
				
				if ($attribute_not_null_count>0) {
					$attribute_calculated = round($attribute_group_avg / $attribute_not_null_count);
				} else {
					$attribute_calculated = null;
				}
	
			} // $algorithm==1
			
			if ($algorithm==2)
			{
				$attribute_calculated = $row['attribute'];
				$attribute_field = 'S104'; // awareness
			}
            
			if ($algorithm==3)
			{
				$attribute_calculated = $row['attribute'];
				$attribute_field = 'S105'; // familiarity
			}
            
			if ($algorithm==4)
			{
				$attribute_calculated = $row['attribute'];
			}
			
			$responce_rating = $row['RATING'];
			$responce_interval = $row['interval'];
			$responce_count = isset($row['responce_count']) ? $row['responce_count'] : '';
			$responce_min_date = isset($row['responce_min_date']) ? $row['responce_min_date'] : '';
			$responce_max_date = isset($row['responce_max_date']) ? $row['responce_max_date'] : '';
			$json_series_data[] = [
				$responce_rating, 
				$responce_interval, 
				$attribute_calculated, 
				$responce_count, 
				$responce_min_date,
				$responce_max_date,
				$attribute_field	
			];
		}
		$json_series_data = json_encode($json_series_data, JSON_NUMERIC_CHECK);
	} // if $sql

	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log("statistics.fnc.php time algo=$algorithm: "." [field=$field_name, atrib=$attribute_calculated, field=$attribute_field, field_project=$field_project / project_id=$project_id] ".((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		
	if (!isset($ajax_key)) $ajax_key = 0;

	// pack final json output
	$json_series_extras[] = $events;
	$json_series_extras[] = $campaigns;
	$json_series_extras[] = $field_options;
	
	$json_final = [
		$json_series_data,
		$json_series_extras,
		$ajax_key
	];	

	$json_final = json_encode($json_final);

	// if redis value exists - replace cached latest periods in redis value with updated latest preiods and return repacked redis array
	$redis_ttl = get_label('redis_ttl');
	if (empty($redis_ttl)) $redis_ttl = 6000;
	if (set_redis_key($redis_key, $json_series_data, (int)$redis_ttl)===false)
	debug_log("key not set: set_redis_key('$redis_key', '$json_series_data', '$redis_ttl');");
	
	// if no redis value - output final json data for a series
	return $json_final;

} // getCompanyStatistics()

function getQuestionNameById ($question, $names)
{
	if (empty($question) || !is_array($question))
	{
		$return = false;
	} else {
		$return = '';
		if (in_array('group', $question) || count($question)==0) {
			$return = '';
		} else {
			if (!empty($names) && is_array($names))
			{
				foreach ($names as $id=>$value)
				{
					if ($id==$question[0])
					{
						$return = $value;
						break;
					}
				}
			}
		}
	}
	return $return;
}

function getQuestionAnswer ($question_id, $answer_value)
{
	$answer_name = getValue('answer_name', 'answers', 'answer_question', $question_id, 1, true, " AND `answer_value`='$answer_value' ");

/* refactored - can be deleted after testing    
    $query = "SELECT `answer_name` FROM `answers` WHERE `answer_question`='$question_id' AND `answer_value`='$answer_value' LIMIT 1;";
	$sql = sql_query($query);
	if ($sql && sql_num_rows($sql)>0 && $row = sql_fetch_array($sql))
	{
		$answer_name = $row['answer_name'];
	} else {
		$answer_name = '';
	}
*/    
	return $answer_name===false ? '' : $answer_name;
}

function getSubQuestionName ($question_id, $subquestion_value)
{	
//	$return = getValue($field, $table, $id, $key, $limit, $cache, $where);
    $question_portal_name = getValue('question_portal_name', 'questions', 'question_parent', $question_id, 1, true, " AND `question_value`='$subquestion_value' ");
    $question_name = getValue('question_name', 'questions', 'question_parent', $question_id, 1, true, " AND `question_value`='$subquestion_value' ");
	$return = empty($question_portal_name) ? (empty($question_name) ? '' : $question_name) : $question_portal_name;
    
/* refactored - can be deleted after testing    
	$query = "SELECT `question_name`, `question_portal_name` FROM `questions` WHERE `question_parent`='$question_id' AND `question_value`='$subquestion_value' LIMIT 1;";
	$sql = sql_query($query);
	if ($sql && sql_num_rows($sql)>0 && $row = sql_fetch_array($sql))
	{
		$question_name = empty($row['question_portal_name']) ? $row['question_name'] : $row['question_portal_name'];
	} else {
		$question_name = '';
	}
*/    
	return $return;
}
	
function getQuestionName ($field, $table, $table_id, $question_id)
{
	$question_portal_name = getValue(str_replace('_name', '_portal_name', $field), $table, $table_id, $question_id);
	$question_name = empty($question_portal_name) ? getValue($field, $table, $table_id, $question_id) : $question_portal_name;
	return $question_name;
}

function get_field_name ($field_name_or_attribute_index)
    // return proper field_name and check its existense in fields table or return false
{
    $return = false;
    if (strpos($field_name_or_attribute_index, 'trust_affection')!==false) 
    {  
        $return = 'trust_affection'; 
        
    } elseif (getValue('fields_id', 'fields', 'field_name', $field_name_or_attribute_index)>0)
        // if we can find field by name in fields table - this means that field_name are correct and can be returned back
    {
        $return = $field_name_or_attribute_index;    
    } else {
        $attribute_name = getValue('attribute_value', 'attributes', 'attributes_id', $field_name_or_attribute_index);
            // getting field_name from attributes table by attributes_id
        if (getValue('fields_id', 'fields', 'field_name', $attribute_name)>0)
            // but anyway checking that field exists in fileds table
        {
            $return = $attribute_name;
        } else {
            // last chance - remove index last part from field/attribute name
            $parts = explode('_', $field_name_or_attribute_index); // spliting by _ to get rid of last index element
            array_pop($parts);  // removing last element - index (from attributes)
            $field_wo_index = implode('_', $parts);
            if (getValue('fields_id', 'fields', 'field_name', $field_wo_index)>0)
            {
                $return = $field_wo_index;
            } 
        }
    }
    if (!$return) { debug_log("get_field_name(): field not found - '$field_name_or_attribute_index' "); }
    return $return;
}

function getClientViewFields ($client_id)
    // returning list of fields from client view model
{
	if (empty($client_id)) 
    { 
        $return = false; 
    } else {
        $dashboard_model = getValue('client_dashboard_model', 'clients', 'clients_id', $client_id);
        $client_model = getValue('client_model', 'clients', 'clients_id', $client_id); // chart model
        $view_type = $dashboard_model;
//-	        if (empty($view_type)) { return false; } // POR-805
        $view_fields = getValue('view_fields', 'views', 'view_type', $view_type, 1, true, " AND `view_tool`='dashboard' ");
        $return = empty($view_fields) ? false : str_replace(' ', '', $view_fields);
    }
    return $return;
}
