<?php

/*
	FILE DESCRIPTION:
		PATH: backend/includes/profile.fnc.php;
		TYPE: fnc (function declaration);
		PURPOSE: declares functions used to generate the Profile page HTML, some global client parameters, and process clients' requests coming from the Profile page;
		REFERENCED IN: index.php, backend/ajax/profile_subscription.ajax.php, api/v1/index.php;
		FUNCTIONS DECLARED:
			NAME: getDaughterCountries;
				PURPOSE: returns an array of companies tied with the lists of the countries where the daughter companies are located (used in the Company Select dialog when filtering companies by country);
				EXECUTED IN: backend/includes/profile.fnc.php;
			NAME: getClientCompanies;
				PURPOSE: returns an array of companies available to a client along with some of their parameters like company name, company countries, etc;
				EXECUTED IN: index.php;
			NAME: getClientCompany (a possible duplicate of function getClientCompanies in file backend/includes/profile.fnc.php);
				PURPOSE: returns an array of companies available to a client along with some of their parameters like company name, company countries, etc;
				EXECUTED IN: api/v1/get_client_companies.php;
			NAME: getProfile;
				PURPOSE: returns the Profile page HTML;
				EXECUTED IN: index.php;
			NAME: setDigest;
				PURPOSE: records the Digest Subscription settings whenever a client changes them in the Profile page;
				EXECUTED IN: backend/ajax/profile_subscription.ajax.php;
			NAME: setPassword;
				PURPOSE: returns '1' if a client has successfully changed his/her password in the Profile page;
				returns '0' if a client has failed changing his/her password in the Profile page
				EXECUTED IN: backend/ajax/profile_subscription.ajax.php;
		STYLES: frontend/css/profile.css; 
*/ 

function getClientBindings() { // describe !!! // do not run if client_show_all=1 !!!
	
	global $con;

	$_SESSION['client_binding_array'] = [];
	$return = false;
	
	if (isset($_SESSION['client_id']) && $_SESSION['client_id']>0)
	{
		$client_bindings = [];
//		$client_globals = [];
		$client_daughter_bindings = [];

		$sql = "SELECT `binding_rating` FROM `bindings` WHERE `binding_client` = '".$_SESSION['client_id']."'";

		$result = $con->query($sql);
		if ($result->num_rows > 0) { 
			while( $row = $result->fetch_assoc() ) {	
				$client_bindings[] = $row['binding_rating'];
			}
		}
		
/*		$enable_global_indexes = get_label('enable_global_indexes');
		
		if ($enable_global_indexes) {
			$sql = "SELECT `ratings_id` FROM `ratings` WHERE `rating_country` = '999'";
			$result = $con->query($sql);
			if ($result->num_rows > 0) { 
				while( $row = $result->fetch_assoc() ) {	
					$client_globals[] = $row['ratings_id'];
				}
			}	
			$client_bindings = array_merge($client_bindings,$client_globals);
		} */
		
		$sql = 
		"SELECT 
			`bindings`.`binding_rating` AS `binding_rating`,
			`daughters`.`ratings_id` AS `ratings_id`
		FROM 
			`bindings`,
			(SELECT
				`ratings_id`, 
				`rating_parent`
			FROM 
				`ratings`	
			) AS `daughters`
		WHERE 
			`daughters`.`rating_parent` = `bindings`.`binding_rating` AND 
			`bindings`.`binding_client` = '".$_SESSION['client_id']."' AND 
			`bindings`.`binding_show_daughters` = 1 
		";

		$result = $con->query($sql);
		if ($result->num_rows > 0) { 
			while( $row = $result->fetch_assoc() ) {	
				$client_daughter_bindings[] = $row['ratings_id'];
			}
		}

		$client_bindings = array_merge($client_bindings,$client_daughter_bindings);
		$output_array = [];
		foreach ($client_bindings as $this_client_binding) {
			if ( !in_array($this_client_binding,$output_array) ) {
				$_SESSION['client_binding_array'][] = $this_client_binding;
			}
		}
		$return = true;
	}
	
	return $return;
	//echo '<br><br><br><br>client_binding_array - all client_bindings with daughters'.json_encode($_SESSION['client_binding_array']);
}

function getDaughterCountries() {
	
	global $con;
	
	$start_time = microtime(true);
	$timing = true;
	$timing_log = ROOT.'/logs/debug_log.txt';
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('getDaughterCountries time 00: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// get client company countries 
	$daughter_rating_countries = [];
	$sql_bindings_filter = $_SESSION['client_show_all'] == 0 ? " AND `ratings_id` IN (".implode(',',$_SESSION['client_binding_array']).")" : " ";
	
	$sql = 
	"SELECT 
		`".TABLE_RATINGS."`.`ratings_id` AS `ratings_id`, 
		`".TABLE_RATINGS."`.`rating_name` AS `rating_name`, 
		`ratings_2`.`rating_country_group` AS `rating_country_group` 
	FROM 
		`".TABLE_RATINGS."`, 
		(SELECT 
			`rating_parent`, 
			`rating_country`, 
			GROUP_CONCAT(`rating_country` SEPARATOR ',') AS `rating_country_group` 
		FROM 
			`".TABLE_RATINGS."` 
		WHERE 
			`rating_parent` <> '0' 
			".$sql_bindings_filter." 
		GROUP BY 
			`rating_parent` 
		) AS `ratings_2` 
	WHERE 
		`ratings_2`.`rating_parent` = `".TABLE_RATINGS."`.`ratings_id` 
	ORDER BY 
		`".TABLE_RATINGS."`.`rating_name` ASC
	";
	
	//echo '<br><br><br><br><br><br><br>'.$sql;
	//echo '<br><br><br><br><br><br><br>'.count($_SESSION['client_binding_array']);
	
	$result = $con->query($sql);
	if ($result->num_rows > 0) { 
		while( $row = $result->fetch_assoc() ) {	
			$daughter_rating_countries[] = [$row['ratings_id'],$row['rating_country_group']];
		}
	}
	
	//echo '<br><br><br><br>$daughter_rating_countries'.json_encode($daughter_rating_countries);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('getDaughterCountries time 99: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	return $daughter_rating_countries;
	
}

function getClientCompanies() {
	
	global $con;

	$start_time = microtime(true);
	$timing = true;
	$timing_log = ROOT.'/logs/debug_log.txt';
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('getClientCompanies time 00: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	getClientBindings();
	
	// get client companies array
	$sql_client_show_all_str = $_SESSION['client_show_all'] == 0 ? " AND `ratings_id` IN (".implode(',',$_SESSION['client_binding_array']).")" : " ";

	$sql = 
	"SELECT 
		ratings_id,
		rating_parent,
		rating_country,
		rating_name_short,
		client_default_rating, 
		client_country
	FROM 
		".TABLE_CLIENTS.", 
		".TABLE_RATINGS."
	WHERE 
		clients_id='".$_SESSION['client_id']."' 
		".$sql_client_show_all_str."
	ORDER BY 
		rating_name ASC";

	$result = $con->query($sql);

	$daughter_rating_countries = getDaughterCountries();
	
	if ($result->num_rows > 0) { 
	
		$client_company_data_array = [];
		$_SESSION['client_country_array'] = [];
		$_SESSION['client_company_data_array'] = [];
		$_SESSION['client_company_array_all'] = [];
		$rating_parents_used = [];
		
		while( $row = $result->fetch_assoc() ) {

			// client company data array - process only parent ratings
			
			$rating_parent = $row['rating_parent'] ? $row['rating_parent'] : $row['ratings_id'];
			
			if (!in_array($rating_parent, $rating_parents_used)/*$rating_parent*/) { // rating parent not yet processed
				$rating_parents_used[] = $rating_parent;
				
				$rating_parent_name_short = getValue('rating_name_short', TABLE_RATINGS, 'ratings_id', $rating_parent);	// try to get before condition as array to avoid quering !!!
				
				$rating_countries = [];

				if ($_SESSION['client_show_all'] == 0) { 
					if ( in_array($rating_parent,$_SESSION['client_binding_array']) ) {
						$rating_countries[] = getValue('rating_country', TABLE_RATINGS, 'ratings_id', $rating_parent); // exclude rating country if rating is parent but not in bindings // try to get before condition as array to avoid quering !!!
					}
				} else {
					$rating_countries[] = getValue('rating_country', TABLE_RATINGS, 'ratings_id', $rating_parent);
				} 

				foreach ($daughter_rating_countries as $this_rating_country) {
					if ( $rating_parent == $this_rating_country[0] ) {
						$rating_countries = array_merge($rating_countries,explode(',',$this_rating_country[1]));
					}
				}
				
				$client_company_data_array[] = [
					$rating_parent,
					normalize_UTF($rating_parent_name_short),
					$row['client_default_rating'],
					$rating_countries
				];
				
			}

			if ( !in_array($row['ratings_id'], $_SESSION['client_company_array_all']) ) $_SESSION['client_company_array_all'][] = $row['ratings_id'];
			if ( !in_array($row['rating_country'], $_SESSION['client_country_array']) ) $_SESSION['client_country_array'][] = $row['rating_country'];
//			$_SESSION['client_country_array'][] = '999';
			$_SESSION['default_company_id'] = $row['client_default_rating'];
			$_SESSION['default_country_id'] = $row['client_country'];
			
		}
		
		// sort client_company_data_array by parent name in subarrays and render session variable $_SESSION['client_company_data_array']
		$client_company_data_array_sort = [];
		foreach ($client_company_data_array as $this_client_company_data_array) {
				$client_company_data_array_sort[] = $this_client_company_data_array[1];
		}
		natcasesort($client_company_data_array_sort);
		foreach ($client_company_data_array_sort as $this_client_company_data_array_sort) {
			foreach ($client_company_data_array as $this_client_company_data_array) {
				if ($this_client_company_data_array[1]==$this_client_company_data_array_sort) 
				$_SESSION['client_company_data_array'][]=$this_client_company_data_array;
			}
		}
		
	}
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('getClientCompanies time 00: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
}

function getClientCompany($user_id,$field='') {
	
	global $con;
	
	$show_all_companies = getValue('client_show_all', TABLE_CLIENTS, 'clients_id', $user_id);

	$client_company_data = [];
	$sql = 
		"SELECT 
			`ratings_id`, 
			`rating_name`, 
			`country_name` 
		FROM 
			".(!$show_all_companies ? " 
			`".TABLE_BINDINGS."`, 
			" : "")." 
			`".TABLE_RATINGS."`, 
			`countries` 
		WHERE 
			".(!$show_all_companies ? " 
			`binding_client` = '".$user_id."' AND 
			`binding_rating` = `ratings_id` AND 
			" :	"")."
			`rating_country` = `countries_id` 
		ORDER BY 
			`rating_name`, 
			`country_name`
	";
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			if ($field) {
				$client_company_data[] = $row[$field];
			} else {
				$client_company_data[] = [
					$row['ratings_id'],
					$row['rating_name'],
					$row['country_name']
				];
			}
		}
	}
	
	return $client_company_data;
	
}

function getProfile() {
	
	global $con;

	$sql = 
	"SELECT 
		client_default_rating, 
		client_name, 
		client_language, 
		client_rating, 
		client_subscription, 
		client_subscription_type, 
		client_country, 
		client_stakeholder, 
		client_custom_questions, 
		client_yearly_workshop,
		client_filter_sync 
	FROM 
		".TABLE_CLIENTS." 
	WHERE 
		clients_id='".$_SESSION['client_id']."' 
	LIMIT 1";

	$result = $con->query($sql);

	if ($result->num_rows > 0) { 
		while( $row = $result->fetch_assoc() ) {
			
			$profile_client_name = $row['client_name'];	
			//$_SESSION['client_language'] = $row['client_language'];	
			$profile_company_name = $row['client_rating'] == 0 ? 'Caliber' : getValue('rating_name', TABLE_RATINGS, 'ratings_id', $row['client_rating'], 1);
			$profile_client_subscription = $row['client_subscription'] ? date_format(date_create($row['client_subscription']), "d F Y") : "undefined";
			$_SESSION['profile_client_subscription_type'] = $row['client_subscription_type'] ? $row['client_subscription_type'] : "undefined";
			$profile_client_country = $row['client_country'] ? getValue('country_name', 'countries', 'countries_id', $row['client_country']) : "undefined";
			$profile_client_stakeholder = $row['client_stakeholder'] ? $row['client_stakeholder'] : "undefined";		
			$profile_sample = getValue('rating_quota', TABLE_RATINGS, 'ratings_id', $row['client_rating'], 1);
			$profile_client_custom_questions = $row['client_custom_questions']; 
			$profile_client_yearly_workshop = $row['client_yearly_workshop'] ? date_format(date_create($row['client_yearly_workshop']), "d F Y") : "not scheduled"; 
			$profile_client_filter_sync = $row['client_filter_sync']; 
	
		}
	}
	
	// get langs select list
	$profile_lang = getValue('language', 'users', 'users_id', $_SESSION['user']['id']);
	$profile_langs = json_decode(get_label('profile_langs'),true);
	$profile_langs_options = get_options_from_json($profile_langs,$profile_lang);
	$profile_langs_options = $profile_langs ? $profile_langs_options : get_langs_list($profile_lang);
	
	// get client digest array
	$client_digest_data_array = [];
	$sql = "SELECT subscription_interval, subscription_title FROM subscriptions WHERE subscription_client_id='".$_SESSION['client_id']."' AND subscription_type = 'digest'";
	$result = $con->query($sql);
	if ($result->num_rows > 0) { 
		while( $row = $result->fetch_assoc() ) {
			$client_digest_data_array[] = $row['subscription_interval'];	
		}
	}
	
	// HTML //
	
	$profile_company_list = '';
	foreach ($_SESSION['client_company_data_array'] as $this_client_company_data) {
		$profile_company_list .= $this_client_company_data[1]."<br>";
	}
	$profile_company_list = rtrim($profile_company_list, "<br>");

	$html = '
	<div class="user_profile_wrap">
		<form>
		<div class="user_profile_row">
			<div class="user_profile_column1">
				<p class="user_profile_column1_text">{{profile_account_info}}</p>
			</div>
			<div class="user_profile_column2">
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_company}}</p>
					</div>
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$profile_company_name.'</p>
					</div> 
				</div>
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_username}}</p>
					</div>  
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$profile_client_name.'</p>
					</div> 
				</div>
				<div id="user_profile_column2_row_password" class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_password}}</p>
					</div>  
					<div class="user_profile_column2_row_column2">

						<input id="current_password" type="password" class="change_password_input" autocomplete="on">
						<p class="input_sub">{{profile_type_curr_pwd}}</p>

						<input id="new_password" type="password" class="change_password_input" autocomplete="on">
						<p class="input_sub">{{profile_type_new_pwd}}</p>

						<input id="check_password" type="password" class="change_password_input" autocomplete="on">
						<p class="input_sub">{{profile_retype_pwd}}<span id="check_password_msg"></span></p>

						<button class="change_password_button" disabled style="text-transform: uppercase;">{{profile_change_pwd}}</button>

					</div> 
				</div>			
			</div>
		</div>
		
		<div class="user_profile_row_divider"></div>

		<div class="user_profile_row">
			<div class="user_profile_column1">
				<p class="user_profile_column1_text">{{profile_settings}}</p>
			</div>
			<div class="user_profile_column2">
				
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_lang}}</p>
					</div>				
					<label for="filter_sync">
						<select id="profile_lang" class="form-control">
							'.$profile_langs_options.'
						</select>
						<button id="profile_lang_apply" class="btn btn-light">{{profile_lang_apply}}</button>
					</label>	
				</div>
				
				<div id="user_profile_column2_row_sync" class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_filter_sync}}</p>
					</div>				
					<label for="filter_sync">
						<input type="checkbox" id="filter_sync" value="0" '.($profile_client_filter_sync ? 'checked' : '').'>
						<span>{{profile_filter_sync_desc}}</span>
					</label>
				</div>	
				
			</div>
		</div>
		
		<div class="user_profile_row_divider"></div>

		<div class="user_profile_row">
			<div class="user_profile_column1">
				<p class="user_profile_column1_text">{{profile_subcription}}</p>
			</div>
			<div class="user_profile_column2">
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_subcription_type}}</p>
					</div>
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$_SESSION['profile_client_subscription_type'].'</p>
					</div> 
				</div>
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_expiry_date}}</p>
					</div>
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$profile_client_subscription.'</p>
					</div> 
				</div>
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_country}}</p>
					</div>  
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$profile_client_country.'</p>
					</div> 
				</div>
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_stakeholder}}</p>
					</div>  
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$profile_client_stakeholder.'</p>
					</div> 
				</div>
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_companies}}</p>
					</div>  
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$profile_company_list.'</p>
					</div> 
				</div>
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_custom_questions}}</p>
					</div>  
					<div class="user_profile_column2_row_column2">
						<p class="user_profile_column2_row_column2_text">'.$profile_client_custom_questions.'</p>
					</div> 
				</div>
			</div>
		</div>

		<div class="user_profile_row_divider"></div>

		<div id="user_profile_row_digest" class="user_profile_row">
			<div class="user_profile_column1">
				<p class="user_profile_column1_text">{{profile_digests}}</p>
			</div>
			<div class="user_profile_column2">
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_digest_weekly}}</p>
					</div>				
					<label for="digest_weekly">
						<input type="checkbox" id="digest_weekly" class="digest_input" name="digest_weekly" value="week" '.(in_array('week',$client_digest_data_array) ? 'checked' : '').'>
						<span class="digest_title">{{profile_receive_weekly}}</span>
					</label>
				</div>	

				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_digest_monthly}}</p>
					</div>
					<label for="digest_monthly">
						<input type="checkbox" id="digest_monthly" class="digest_input" name="digest_monthly" value="month" '.(in_array('month',$client_digest_data_array) ? 'checked' : '').'>
						<span class="digest_title">{{profile_receive_monthly}}</span>
					</label>
				</div>

				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_digest_quarterly}}</p>
					</div>
					<label for="digest_quarterly">
						<input type="checkbox" id="digest_quarterly" class="digest_input" name="digest_quarterly" value="quarter" '.(in_array('quarter',$client_digest_data_array) ? 'checked' : '').'>
						<span class="digest_title">{{profile_receive_quarterly}}</span>
					</label> 
				</div>

				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column1">
						<p class="user_profile_column2_row_column1_text">{{profile_digest_yearly}}</p>
					</div>
					<label for="digest_yearly">
						<input type="checkbox" id="digest_yearly" class="digest_input" name="digest_yearly" value="year" '.(in_array('year',$client_digest_data_array) ? 'checked' : '').'>
						<span class="digest_title">{{profile_receive_yearly}}</span>
					</label>
				</div>
			</div>

		</div>


		<div class="user_profile_row_divider"></div>

		<div id="user_profile_row_alert" class="user_profile_row">
			<div class="user_profile_column1">
				<p class="user_profile_column1_text">{{profile_alerts}}</p>
			</div>
			<div class="user_profile_column2">
				<div class="user_profile_column2_row">
					<div class="user_profile_column2_row_column2">
						<div id="alert_table"></div>
					</div> 
				</div>	
			</div>

		</div>
		</form>
	</div>
	';
		// {{user_profile_footer}}="<p>For technical support<br>please contact:</p><p class="user_profile_footer_support">support@groupcaliber.com<br>+45 31 56 10 01</p>"
		
	return $html;
	
}

function setDigest($digest_action,$digest_interval) {

	global $con;

	if ($digest_action == 'add_digest') {	
		$result = $con->query("INSERT INTO `subscriptions` (`subscription_client_id`, `subscription_type`, `subscription_interval` ) VALUES ('".$_SESSION['client_id']."', 'digest', '$digest_interval');");
	} else {
		$result = $con->query("DELETE FROM `subscriptions` WHERE `subscription_client_id` = '".$_SESSION['client_id']."' AND `subscription_interval` = '$digest_interval' AND `subscription_type` = 'digest';");	
	}

}

function setFilterSync ($filter_sync_value) 
{
//-	$result = $con->query("UPDATE `clients` SET `client_filter_sync` = '$filter_sync_value' WHERE `clients_id` = '".$_SESSION['client_id']."';");
    $client_id = get_client_id();
    setValue('client_filter_sync', $filter_sync_value, 'clients', 'clients_id', $client_id);
    return $result;
}

function emailPW ($email_to, $name, $new_password, $type) 
{
	$lang = getValue('language', 'users', 'email', $email_to);

	$subject = get_label('pw_email_subject', $lang);
	$subscription_digest_dear = sprintf(get_label('subscription_digest_dear', $lang), $name);
	$pw_email_new_pw = sprintf(get_label('pw_email_new_pw', $lang), $new_password);
	$pw_email_advised = get_label('pw_email_advised', $lang);
	$pw_email_pw_changed = get_label('pw_email_pw_changed', $lang);
	$pw_email_support = get_label('pw_email_support', $lang);
	$subscription_digest_thank = get_label('subscription_digest_thank', $lang);
	$pw_email_administration = get_label('pw_email_administration', $lang);
	$pw_email_footer = get_label('mail_footer2', $lang);

	switch ($type)
	{
		case 'automatic':
			$notification = '<br><br><br>'.$pw_email_new_pw.'<br><br>'.$pw_email_advised.'<br><br><br>';
			break;
		case 'personal':
			$notification = '<br><br><br>'.$pw_email_pw_changed.'<br><br>'.$pw_email_support.'<br><br><br>';
			break;
		default:
			$notification = '';
			break;
	}
	
	$text = 
		$subscription_digest_dear.','.
		$notification.
		$subscription_digest_thank.'<br>'.
		$pw_email_administration.'<br><br>'
	;

	$email_id = 
		create_email(
			0, 
			2, 
			0, 
			$email_to, 
			$name, 
			$subject, 
			$text, 
			get_project(), 
			get_site()	// added site_id for correct header label (POR-1956)
	);
	
	return $email_id;
}

function setPassword($current_password,$new_password) {

	global $con;

	$sql = "SELECT `client_name`, `client_md5`, client_email FROM `".TABLE_CLIENTS."` WHERE `clients_id` = '".$_SESSION['client_id']."' LIMIT 1";
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			$client_name = $row['client_name'];
			$client_md5 = $row['client_md5'];
			$email_to = $row['client_email'];
		}
	} else {
		$success = 0;
	}

	if ( $client_md5 == md5($_SESSION['client_id'].".".$client_name.".".$current_password) ) {
		$new_client_md5 = md5($_SESSION['client_id'].".".$client_name.".".$new_password);
		setValue('client_md5', $new_client_md5, 'clients', 'clients_id', $_SESSION['client_id']);
		$user_name = getValue('name', 'users', 'user_client', $_SESSION['client_id'], 1);
		emailPW($email_to,$user_name,$new_password,'personal');
		$success = 1;
	} else {
		$success = 0;
	}

	return $success;
}
