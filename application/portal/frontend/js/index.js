 
/*
	FILE DESCRIPTION:
		PATH: frontend/js/index.js;
		TYPE: js (js file);
		PURPOSE: mainly declares and runs function setDefaults to set the client-side default functionality settings;
		uses some data from the HTML tags generated in file backend/includes/console_filter.fnc.php on [age load;
		takes care of the site layout settings (function setLayout) depending on the screen resoliution/device used;
		draws the default View on page load (function drawTool in file frontend/js/console.js);
		checks if the user is logged and automatically logs him/her out when the session expires on the server (function autoLogOut in file frontend/js/login.js);		
		REFERENCED IN: backend/includes/index.lnk.php;
		FUNCTIONS DECLARED (see below) ;
		STYLES: - ; 
*/ 

$(document).ready(function(){
	
	//setIntervalAsync = SetIntervalAsync.dynamic.setIntervalAsync
	//setIntervalAsync = SetIntervalAsync.fixed.setIntervalAsync
	//setIntervalAsync = SetIntervalAsync.legacy.setIntervalAsync
	//clearIntervalAsync = SetIntervalAsync.clearIntervalAsync

	/*i = 0;
	tst = setIntervalAsync(
	  async function () {
		  console.log(i++);
		  if (i==5) clearIntervalAsync(tst);
	  },
	  1000
	)*/
	
	setDefaults();
	
	// initialize Appcues with required parameters like user id, name, email
	function appcuesIdentify() {
		if (window.Appcues != undefined && window.Appcues != null) {
			window.Appcues.identify(getUserData().id, {
				name: getUserData().name,
				email: getUserData().email,
				created_at: getUserData().created
			});
		} else {
			cLog('appcues was most likely blocked by an adblocker');
		}
	}

	if ($enable_appcues == 1) appcuesIdentify(); // enable or disable Appcues

	$('#preloader_map, #preloader_dashboard_modal, #preloader_report_modal, #preloader_chart, #preloader_table, #preloader_wordcloud').hide();
	setLayout(); 
	
	// For security reasons, Highcharts since version 9 filters out from custom html unknown tags and attributes introduced into highcharts API JSON. 
	// below is a list of attributes that should escape the highcharts attribute filter
	Highcharts.AST.allowedAttributes.push(
		'data-toggle',
		'data-trigger',
		'data-placement',
		'data-content',
		'data-html'
	);

});


$(window).resize(function(){
	setLayout(); 
	test_resize = true;
});

window.addEventListener('orientationchange', function() { // used for ios	
	if (!test_resize) setTimeout(function(){ setLayout(); }, 100);	
});

// this code attaches the bootstrap popover tooltip element where needed so that for activities on the development view the tooltip hiding delay is 1500ms and for the rest of the tooltips it's a zero
$(document).on('mouseover', 'div', function () {
	if (cur_tool == 'chart') { // tooltip settings for development (activities)
		$('[data-toggle="popover"]').popover({
			delay: {show:1000,hide:1500}
		});
	} else { // tooltip for pages other than development
		$('[data-toggle="popover"]').popover({
			delay: {show:1000,hide:0}
		});
	}
});

$('body').click(function () {
	if (cur_tool != 'chart') $('.popover').hide();
});

function setDefaults() {
	
	site_version = $('#site_version').html();
	console_log_on = $('#console_log_on').html();
	client_id = Number($('#client_id').html());	
	client_role = Number($('#client_role').html());
	$client_name = $('#client_name').html();
	$user_name = $('#user_name').html();
	$user_email = $('#user_email').html();
	client_model = $('#client_model').html();
	$user_id = Number($('#user_id').html());
	$customer_id = Number($('#customer_id').html());
	$user_created = $('#user_created').html();
	
	client_map_dashboard_disabled = $('#client_map_dashboard_disabled').html();
	client_show_all = $('#client_show_all').html();
	client_map_center = $('#client_map_center').html();
	
	draw_tool_timeout_report = 0;
	draw_tool_timeout_map = 0;
	draw_tool_timeout_dashboard = 0;
	draw_tool_timeout_chart = 0;
	draw_tool_timeout_table = 0;
	draw_tool_timeout_wordcloud = 0;
	draw_tool_timeout_offset = $('#view_render_start_offset').html() ? $('#view_render_start_offset').html() : 2000;
	
	core_element_id = $('#client_core_element_id').html();

	client_company_array = JSON.parse( $('#client_company_array').html() );
	client_company_array_all = JSON.parse( $('#client_company_array_all').html() );
	default_company_id = $('#default_company_id').html() ? $('#default_company_id').html() : 0;
	default_country_id = $('#default_country_id').html() ? $('#default_country_id').html() : 0;
	
	
	default_gender_array = JSON.parse( $('#default_gender_array').html() );
	default_age_array = JSON.parse( $('#default_age_array').html() );
	default_country_array = JSON.parse( $('#default_country_array').html() );
	default_region_type_array = JSON.parse( $('#default_region_type_array').html() );
	default_region_array = JSON.parse( $('#default_region_array').html() );
	default_education_type_array = JSON.parse( $('#default_education_type_array').html() );
	default_income_type_array = JSON.parse( $('#default_income_type_array').html() );
	default_segment_array = JSON.parse( $('#default_segment_array').html() );
	default_industry_array = JSON.parse( $('#default_industry_array').html() );
	default_company_size_array = JSON.parse( $('#default_company_size_array').html() );
	default_diagnosis_array = JSON.parse( $('#default_diagnosis_array').html() );
	default_family_array = JSON.parse( $('#default_family_array').html() );
	default_children_array = JSON.parse( $('#default_children_array').html() );
	default_housing_array = JSON.parse( $('#default_housing_array').html() );
	default_living_array = JSON.parse( $('#default_living_array').html() );
	default_engagement_array = JSON.parse( $('#default_engagement_array').html() );
	default_facilities_array = JSON.parse( $('#default_facilities_array').html() );
	default_tg1_array = JSON.parse( $('#default_tg1_array').html() );
	default_tg2_array = JSON.parse( $('#default_tg2_array').html() );
	default_novo1_array = JSON.parse( $('#default_novo1_array').html() );
	default_novo2_array = JSON.parse( $('#default_novo2_array').html() );
	default_demant2_array = JSON.parse( $('#default_demant2_array').html() );
	default_rw1_array = JSON.parse( $('#default_rw1_array').html() );
	default_rw2_array = JSON.parse( $('#default_rw2_array').html() );
	default_gr1_array = JSON.parse( $('#default_gr1_array').html() );
	default_gr2_array = JSON.parse( $('#default_gr2_array').html() );
	default_v_customer_array = JSON.parse( $('#default_v_customer_array').html() );
	default_gol1_array = JSON.parse( $('#default_gol1_array').html() );
	default_gol2_array = JSON.parse( $('#default_gol2_array').html() );
	default_gol3_array = JSON.parse( $('#default_gol3_array').html() );
	default_gol5_array = JSON.parse( $('#default_gol5_array').html() );
	default_veolia1_array = JSON.parse( $('#default_veolia1_array').html() );
	default_touchpoints_array = JSON.parse( $('#default_touchpoints_array').html() );
	default_employment_array = JSON.parse( $('#default_employment_array').html() );
	default_race_array = JSON.parse( $('#default_race_array').html() );
	default_ethnicity_array = JSON.parse( $('#default_ethnicity_array').html() );
	default_politics_array = JSON.parse( $('#default_politics_array').html() );
	default_children_number_array = JSON.parse( $('#default_children_number_array').html() );
	default_tccc_influence_array = JSON.parse( $('#default_tccc_influence_array').html() );
	default_tccc_segment_array = JSON.parse( $('#default_tccc_segment_array').html() );
	default_dmi1_array = JSON.parse( $('#default_dmi1_array').html() );
	default_energinet1_array = JSON.parse( $('#default_energinet1_array').html() );
	default_stakeholder_type_array = JSON.parse( $('#default_stakeholder_type_array').html() );
	default_hovedkategori_array = JSON.parse( $('#default_hovedkategori_array').html() );	
	default_siemensq1_array = JSON.parse( $('#default_siemensq1_array').html() );	
	default_association_array = JSON.parse( $('#default_association_array').html() );	
	default_segment2_array = JSON.parse( $('#default_segment2_array').html() );
	default_fk1_array = JSON.parse( $('#default_fk1_array').html() );
	default_region_zone_array = JSON.parse( $('#default_region_zone_array').html() );
	default_education_array = JSON.parse( $('#default_education_array').html() );
	default_income_array = JSON.parse( $('#default_income_array').html() );
	default_cv2_array = JSON.parse( $('#default_cv2_array').html() );
	default_cv3_array = JSON.parse( $('#default_cv3_array').html() );
	default_subregion_br_group_array = JSON.parse( $('#default_subregion_br_group_array').html() );
	default_wsa1_array = JSON.parse( $('#default_wsa1_array').html() );
	default_wsa2_array = JSON.parse( $('#default_wsa2_array').html() );
	default_wsa3_studyarea_array = JSON.parse( $('#default_wsa3_studyarea_array').html() );
	default_sf1_array = JSON.parse( $('#default_sf1_array').html() );
	default_eon1_array = JSON.parse( $('#default_eon1_array').html() );
	default_eon_customer_array = JSON.parse( $('#default_eon_customer_array').html() );
	default_gn2_array = JSON.parse( $('#default_gn2_array').html() );
	default_gn1_array = JSON.parse( $('#default_gn1_array').html() );
	default_gn3_array = JSON.parse( $('#default_gn3_array').html() );
	default_gn4_array = JSON.parse( $('#default_gn4_array').html() );
	default_zs2_array = JSON.parse( $('#default_zs2_array').html() );
	default_zs3_array = JSON.parse( $('#default_zs3_array').html() );
	default_ess1_array = JSON.parse( $('#default_ess1_array').html() );
	default_ess2_array = JSON.parse( $('#default_ess2_array').html() );
	default_ori1_array = JSON.parse( $('#default_ori1_array').html() );
	default_ovo_influencer_array = JSON.parse( $('#default_ovo_influencer_array').html() );
	default_ovo_customer_array = JSON.parse( $('#default_ovo_customer_array').html() );
	default_bay5_array = JSON.parse( $('#default_bay5_array').html() );
	default_ethnicity_ca_array = JSON.parse( $('#default_ethnicity_ca_array').html() );
	default_politics_ca_array = JSON.parse( $('#default_politics_ca_array').html() );
	default_air02_array = JSON.parse( $('#default_air02_array').html() );
	default_air03_array = JSON.parse( $('#default_air03_array').html() );
	default_air04_array = JSON.parse( $('#default_air04_array').html() );
//paste end
	
	$defaultView = $('#default_view').html();
	
	input_filter_data_obj = $('#input_filter_data_obj').html();
	filter_group = $('#filter_group').html();
	filter_ungroup = $('#filter_ungroup').html();
	filter_select_all = $('#filter_select_all').html();
	filter_deselect_all = $('#filter_deselect_all').html();
	map_ajax_delay = $('#map_ajax_delay').html();
	dashboard_ajax_delay = $('#dashboard_ajax_delay').html();
	chart_ajax_delay = $('#chart_ajax_delay').html();
	table_ajax_delay = $('#table_ajax_delay').html();
	wordcloud_ajax_delay = $('#chart_ajax_delay').html();
	$enableViewUpdateFromMenu = $('#enable_view_update_from_menu').html();

	cur_tool = '';
	cur_tool_id = '';
	
	new_tool_id = '';
	
	test_resize = false;
	client_layout = 0;
	animate_speed = 250;
	
	// tool defaults	
	y_zoom = 'out';
	spline = 'line';
	yAxisParams = {min: 0, max: 101, tickInterval: 10};
	cur_win_h = $(window).height();
	
	// progress bar defaults
	$reportAJAXInterval = 0;
	$mapAJAXInterval = 0;
	$dashboardAJAXInterval = 0;
	$chartAJAXInterval = 0;
	$tableAJAXInterval = 0;
	$wordcloudAJAXInterval = 0;
	
	progress_bar_show_details = $('#progress_bar_show_details').html();
	progress_interval_report = 0;
	progress_interval_map = 0;
	progress_interval_dashboard = 0;
	progress_interval_chart = 0;
	progress_interval_table = 0;
	progress_interval_wordcloud = 0;
	progress_cur_report = '';
	progress_cur_map = '';
	progress_cur_dashboard = '';
	progress_cur_chart = '';
	progress_cur_table = '';
	progress_cur_wordcloud = '';
	
	map_auto_increment = Number($('#map_auto_increment').html());
	dashboard_auto_increment = Number($('#dashboard_auto_increment').html());
	chart_auto_increment = Number($('#chart_auto_increment').html());
	table_auto_increment = Number($('#table_auto_increment').html());
	wordcloud_auto_increment = Number($('#chart_auto_increment').html());
	
	$progressBarStartDetailsMap = Number($('#progress_bar_start_details_map').html());
	$progressBarStartDetailsDashboard = Number($('#progress_bar_start_details_dashboard').html());
	$progressBarStartDetailsChart = Number($('#progress_bar_start_details_chart').html());
	$progressBarStartDetailsTable = Number($('#progress_bar_start_details_table').html());
	$progressBarStartDetailsWordcloud = Number($('#progress_bar_start_details_chart').html());
	
	$progressBarShowFractions = $('#progress_bar_show_fractions').html();
	
	$progressBarStartPercentWidthMap = Number($('#progress_bar_start_percent_width_map').html());
	$progressBarStartPercentWidthDashboard = Number($('#progress_bar_start_percent_width_dashboard').html());
	$progressBarStartPercentWidthChart = Number($('#progress_bar_start_percent_width_chart').html());
	$progressBarStartPercentWidthTable = Number($('#progress_bar_start_percent_width_table').html());
	$progressBarStartPercentWidthWordcloud = Number($('#progress_bar_start_percent_width_chart').html());
		
	$progressBarStartDetailsWidthMap = Number($('#progress_bar_start_details_width_map').html());
	$progressBarStartDetailsWidthDashboard = Number($('#progress_bar_start_details_width_dashboard').html());
	$progressBarStartDetailsWidthChart = Number($('#progress_bar_start_details_width_chart').html());
	$progressBarStartDetailsWidthTable = Number($('#progress_bar_start_details_width_table').html());
	$progressBarStartDetailsWidthWordcloud = Number($('#progress_bar_start_details_width_chart').html());
		
	$progressBarStartFractionsWidthMap = Number($('#progress_bar_start_fractions_width_map').html());
	$progressBarStartFractionsWidthDashboard = Number($('#progress_bar_start_fractions_width_dashboard').html());
	$progressBarStartFractionsWidthChart = Number($('#progress_bar_start_fractions_width_chart').html());
	$progressBarStartFractionsWidthTable = Number($('#progress_bar_start_fractions_width_table').html());
	$progressBarStartFractionsWidthWordcloud = Number($('#progress_bar_start_fractions_width_chart').html());
	
	$preloaderReportPause = 0;
	$preloaderMapPause = 0;
	$preloaderDashboardPause = 0;
	$preloaderChartPause = 0;
	$preloaderTablePause = 0;
	$preloaderWordcloudPause = 0;
	
	cur_report_ajax_key = 0;
	cur_marker_ajax_key = 0;
	cur_dashboard_ajax_key = 0;
	cur_chart_ajax_key = 0;
	cur_column_ajax_key = 0;
	cur_wordcloud_ajax_key = 0;
	
	$retryAJAXDashboardLimit = Number($('#retry_ajax_dashboard_limit').html());
	
	//table_offset = 0;
	table_offset_left = 0;
	table_offset_right = 1000000;
	
	no_filter_logout = false;
	
	$urlLabelTranslate = 'backend/ajax/labelTranslate.ajax.php';
	
	latestChartHTML = '';
	latestMapHTML  = '';
	
	$isReportGenerated = false;
	$isMapGenerated = false;
	$isDashboardGenerated = false;
	$isChartGenerated = false;
	$isWordcloudGenerated = false;
	$isTableGenerated = false;
	$isToolSwitchedFromMenu = false;
	$isToolRecalledFromMenu = false;
	$isToolRecalledFromFilter = false;
	
	$isRegionSelected = false;
	$isFilterSetByCountry = false;
	
	$isFilterChangedReport = {filter: false, company: false};
	$isFilterChangedMap = {filter: false, company: false};
	$isFilterChangedDashboard = {filter: false, company: false};
	$isFilterChangedChart = {filter: false, company: false};
	$isFilterChangedTable = {filter: false, company: false};
	$isFilterChangedWordcloud = {filter: false, company: false};
	
	$enableFilterSync = $('#enable_filter_sync').html() ? $('#enable_filter_sync').html() : 0;
	$enableFilterSyncRatings = $('#enable_filter_sync_ratings').html() ? $('#enable_filter_sync_ratings').html() : 0;
	
	$enableFilterSync = $('#filter_sync').is(':checked') ? 1 : 0;
	
	//calendar_cancelled = false;
	//console.log(core_element_id);
	$calendarSettings1 = JSON.parse($('#calendar_settings').html());
	/*$strPrevDateChart = $('#period_prev_date_chart').html();
	$hidePeriodInChart = $('#hide_period_in_chart').html();
	$disableMaxDateForUsers = Number($('#disable_max_date_for_users').html());
	$periodMinDate = $('#period_min_date').html();*/

	//console.log('++++++++++++++++',$calendarSettings1);
	
	//$isPeriodInputInitialized = false;
	$copyFilter = false;
	
	/*$mapFirstLoaded = true;
	$dashboardFirstLoaded = true;
	$chartFirstLoaded = true;
	$tableFirstLoaded = true;*/
	
	$htmlReport = '';
	$htmlDashboard = '';
	$hideReportView = Number($('#hide_report_view').html());
	$report_chart_intervals = Number($('#report_chart_intervals').html());
	
	$view_snapshot_name = $('#view_snapshot_name').html();
	$html2canvasSettings1 = JSON.parse($('#html2canvas_settings').html());
	
	//$wordcloud_exclusions = $('#wordcloud_exclusions').html().split(',');
	$wordcloud_threshold_label = $('#wordcloud_threshold_label').html();
	$wordcloud_threshold = Number($('#wordcloud_threshold_default').html());
	$wordcloud_threshold_max = $('#wordcloud_threshold_max').html();
	$wordcloud_rotation_settings = JSON.parse($('#wordcloud_rotation_settings').html());
	$wordcloud_respondents = $('#wordcloud_respondents').html(); // number of respondents text label
	//$wordcloud_split_exression = $('#wordcloud_split_exression').html();
	
	$hide_report_period = $('#hide_report_period').html();
	
	$table_auto_click_right = false;
	$table_auto_click_left = false;
	
	$chart_band_highlight = Number($('#chart_band_highlight').html());
	$chart_flag_focus_color = $('#chart_flag_focus_color').html();
	$activity_delete_ok = $('#activity_delete_ok').html();
	
	$enable_appcues = Number($('#enable_appcues').html());
	
	$filter_reset_msg = $('#filter_reset_msg').html();
	
	// default filter data object for first login or new tool creation	
	default_filter_data = JSON.stringify({
		company_checked: ['multiple', default_company_id], 
		company_visible: ['group','all', default_company_id], 
		
		trustaffection_checked: [core_element_id],
		reputation_checked: [],
		brand_checked: [],
		behavior_checked: [],
		awareness_checked: [],
		familiarity_checked: [],
		corestory_checked: [],
		trust_checked: [core_element_id],
		character_checked: [],
		support_checked: [],
		competency_checked: [],
		customstatements_checked: [],
		custom_checked: [],
		degreeofcommitment_checked: [],
		purposeattributes_checked: [],
			reputationscore_checked: [],
			sustainabilityawareness_checked: [],
//-			portfolioawareness_checked: [],
			tcccportfolioawareness_checked: [],
			advocacy_checked: [],
//?			support_checked: [],
			partofthesolution_checked: [],
			tcccinitiativesawareness_checked: [],
			industryissuesimportance_checked: [],
			b2bextras_checked: [],
			customattributes_checked: [],
			brandplatformstatements_checked: [],
			opensh_checked: [],
			opensingleword_checked: [],
			opencv1_checked: [],
			opencv4_checked: [],
			trustinsponsoredscience_checked: [],
			pillars_checked: [],
			transparency_checked: [],
			trustinscience_checked: [core_element_id],
			airbuspurpose_checked: [],
		
		column_checked: ['none'],		
		

// paste from here	
		companycompare_checked: [default_company_id, default_company_id],
		attribute_checked: [], 
		benchmarking_checked: ['prev_period'],		
		interval_checked: ['week'], 
		period_checked: [''],  
		gender_checked: $.merge(['group','all'],default_gender_array),
		age_checked: $.merge(['group','all'],default_age_array),
		      country_checked: ['group'/*,999*/,default_country_id],
		regiontype_checked: $.merge(['group','all'],default_region_type_array),
		      region_checked: $.merge(['group','all']/*,99999*/,default_region_array),
		educationtype_checked: $.merge(['group','all'],default_education_type_array),
		incometype_checked: $.merge(['group','all'],default_income_type_array),
		segment_checked: $.merge(['group','all'],default_segment_array),
		industry_checked: $.merge(['group','all'],default_industry_array),
		companysize_checked: $.merge(['group','all'],default_company_size_array),
		diagnosis_checked: $.merge(['group','all'],default_diagnosis_array),
		family_checked: $.merge(['group','all'],default_family_array),
		children_checked: $.merge(['group','all'],default_children_array),
		housing_checked: $.merge(['group','all'],default_housing_array),
		living_checked: $.merge(['group','all'],default_living_array),
		engagement_checked: $.merge(['group','all'],default_engagement_array),
		facilities_checked: $.merge(['group','all'],default_facilities_array),
		tg1_checked: $.merge(['group','all'],default_tg1_array),
		tg2_checked: $.merge(['group','all'],default_tg2_array),
		novo1_checked: $.merge(['group','all'],default_novo1_array),
		novo2_checked: $.merge(['group','all'],default_novo2_array),
		demant2_checked: $.merge(['group','all'],default_demant2_array),
		rw1_checked: $.merge(['group','all'],default_rw1_array),
		rw2_checked: $.merge(['group','all'],default_rw2_array),
		gr1_checked: $.merge(['group','all'],default_gr1_array),
		gr2_checked: $.merge(['group','all'],default_gr2_array),
		vcustomer_checked: $.merge(['group','all'],default_v_customer_array),
		gol1_checked: $.merge(['group','all'],default_gol1_array),
		gol2_checked: $.merge(['group','all'],default_gol2_array),
		gol3_checked: $.merge(['group','all'],default_gol3_array),
		gol5_checked: $.merge(['group','all'],default_gol5_array),
		veolia1_checked: $.merge(['group','all'],default_veolia1_array),
		touchpoints_checked: $.merge(['group','all'],default_touchpoints_array),
		employment_checked: $.merge(['group','all'],default_employment_array),
		race_checked: $.merge(['group','all'],default_race_array),
		ethnicity_checked: $.merge(['group','all'],default_ethnicity_array),
		politics_checked: $.merge(['group','all'],default_politics_array),
		childrennumber_checked: $.merge(['group','all'],default_children_number_array),
		tcccinfluence_checked: $.merge(['group','all'],default_tccc_influence_array),
		tcccsegment_checked: $.merge(['group','all'],default_tccc_segment_array),
		dmi1_checked: $.merge(['group','all'],default_dmi1_array),
		energinet1_checked: $.merge(['group','all'],default_energinet1_array),
		stakeholdertype_checked: $.merge(['group','all'],default_stakeholder_type_array),
		hovedkategori_checked: $.merge(['group','all'],default_hovedkategori_array),
		siemensq1_checked: $.merge(['group','all'],default_siemensq1_array),
		association_checked: $.merge(['group','all'],default_association_array),
		segment2_checked: $.merge(['group','all'],default_segment2_array),
		fk1_checked: $.merge(['group','all'],default_fk1_array),
		regionzone_checked: $.merge(['group','all'],default_region_zone_array),
		education_checked: $.merge(['group','all'],default_education_array),
		income_checked: $.merge(['group','all'],default_income_array),
		cv2_checked: $.merge(['group','all'],default_cv2_array),
		cv3_checked: $.merge(['group','all'],default_cv3_array),
		subregionbrgroup_checked: $.merge(['group','all'],default_subregion_br_group_array),
		wsa1_checked: $.merge(['group','all'],default_wsa1_array),
		wsa2_checked: $.merge(['group','all'],default_wsa2_array),
		wsa3studyarea_checked: $.merge(['group','all'],default_wsa3_studyarea_array),
		sf1_checked: $.merge(['group','all'],default_sf1_array),
		eon1_checked: $.merge(['group','all'],default_eon1_array),
		eoncustomer_checked: $.merge(['group','all'],default_eon_customer_array),
		gn2_checked: $.merge(['group','all'],default_gn2_array),
		gn1_checked: $.merge(['group','all'],default_gn1_array),
		gn3_checked: $.merge(['group','all'],default_gn3_array),
		gn4_checked: $.merge(['group','all'],default_gn4_array),
		zs2_checked: $.merge(['group','all'],default_zs2_array),
		zs3_checked: $.merge(['group','all'],default_zs3_array),
		ess1_checked: $.merge(['group','all'],default_ess1_array),
		ess2_checked: $.merge(['group','all'],default_ess2_array),
		ori1_checked: $.merge(['group','all'],default_ori1_array),
		ovoinfluencer_checked: $.merge(['group','all'],default_ovo_influencer_array),
		ovocustomer_checked: $.merge(['group','all'],default_ovo_customer_array),
		bay5_checked: $.merge(['group','all'],default_bay5_array),
		ethnicityca_checked: $.merge(['group','all'],default_ethnicity_ca_array),
		politicsca_checked: $.merge(['group','all'],default_politics_ca_array),
		air02_checked: $.merge(['group','all'],default_air02_array),
		air03_checked: $.merge(['group','all'],default_air03_array),
		air04_checked: $.merge(['group','all'],default_air04_array),
	//paste end
		addons_checked: []
		
	});

	default_filter_data_obj = {};

	default_filter_data_obj.report = JSON.parse(default_filter_data);
	default_filter_data_obj.map = JSON.parse(default_filter_data);
	default_filter_data_obj.dashboard = JSON.parse(default_filter_data);
	default_filter_data_obj.chart0 = JSON.parse(default_filter_data);
	default_filter_data_obj.table = JSON.parse(default_filter_data);
	default_filter_data_obj.wordcloud = JSON.parse(default_filter_data);
	if ($('.question input').first().val() !== undefined) default_filter_data_obj['wordcloud'][$('.question input').first().val().toLowerCase().replace(/_/g,'') + '_checked'][0] = $('.question input').first().val();
	
	filter_data_obj = input_filter_data_obj != 'null' ? JSON.parse(input_filter_data_obj) : default_filter_data_obj;
	
	// set specific tool on open defaults
	var d_cur = new Date();
	var d_prev_month = new Date(new Date().setDate(new Date().getDate() - 29 - 1));
	var d_prev_year = new Date(new Date().setDate(new Date().getDate() - 364 - 1));
	//var str_cur_date = (d_cur.getDate() - 1) + '/' + ((d_cur.getMonth() + 1) > 9 ? (d_cur.getMonth() + 1) : '0' + (d_cur.getMonth() + 1)) + '/' + d_cur.getFullYear().toString().slice(2);
	var str_cur_date = moment().subtract(1, 'days').format('DD/MM/YY');
	
	var str_prev_date_month = d_prev_month.getDate() + '/' + ((d_prev_month.getMonth() + 1) > 9 ? (d_prev_month.getMonth() + 1) : '0' + (d_prev_month.getMonth() + 1)) + '/' + d_prev_month.getFullYear().toString().slice(2);
	
	var str_prev_date_year = d_prev_year.getDate() + '/' + ((d_prev_year.getMonth() + 1) > 9 ? (d_prev_year.getMonth() + 1) : '0' + (d_prev_year.getMonth() + 1)) + '/' + d_prev_year.getFullYear().toString().slice(2);
	
	$strPrevDateChart = moment($calendarSettings1.startDateChart, 'DD/MM/YY').subtract(1, 'days').format('DD/MM/YY');
	
	//alert($calendarSettings1.startDateChart);
	
	var cur_period_month = str_prev_date_month + ' - ' + str_cur_date;
	var cur_period_year = str_prev_date_year + ' - ' + str_cur_date;
	var cur_period_chart = $strPrevDateChart + ' - ' + str_cur_date;
	
	$isChangeAutomatic = true;
	
    if (filter_data_obj['report'] === undefined)
    {
        filter_data_obj.report = JSON.parse(default_filter_data);
    }
	
	if (filter_data_obj['wordcloud'] === undefined)
    {
        filter_data_obj.wordcloud = JSON.parse(default_filter_data);
    }
    
    filter_data_obj['report'].period_checked[0] = cur_period_month;
    filter_data_obj['chart0'].interval_checked[0] = 'month';
    filter_data_obj['chart0'].period_checked[0] = cur_period_chart;
    filter_data_obj['dashboard'].benchmarking_checked[0] = 'prev_period';
    filter_data_obj['dashboard'].period_checked[0] = cur_period_month;
    filter_data_obj['map'].period_checked[0] = cur_period_month;
    filter_data_obj['table'].period_checked[0] = cur_period_month; 
	filter_data_obj['wordcloud'].period_checked[0] = cur_period_month;
	
	request = null;

	cLog(filter_data_obj,'filter on load');

}

// generate layout class
function setLayout() {

	// set initial vars
	layout_class = 'screen_landscape_desktop';
	var user = detect.parse(navigator.userAgent);
	var device_type = user.device.type;
	var device_family = user.device.family;
	var orientation = $(window).width() > $(window).height() ? 'landscape' : 'portrait';
	
	//alert(device_family);
	
	if (device_family == 'iPad' && orientation == 'landscape' ) device_type == 'Desktop'; // horizontal ipad = desktop
	
	// force user breakpoint
	if (client_layout === undefined)
	{
		client_layout = 0;
	}
	if (client_layout == 1) device_type = 'Desktop';
	if (client_layout == 2) device_type = 'Mobile';

	// define layout class and client_layout (on load)
	if ( orientation == 'landscape' && device_type == 'Desktop' ) {
		layout_class = 'screen_landscape_desktop';
		client_layout = client_layout == 0 ? 1 : client_layout; // if layout auto (on open) - define actual layout
	}
	if ( orientation == 'landscape' && device_type == 'Mobile' ) {
		layout_class = 'screen_landscape_mobile';
		client_layout = client_layout == 0 ? 2 : client_layout;// if layout auto (on open) - define actual layout
	}
	if ( orientation == 'portrait' ) {
		layout_class = 'screen_portrait_all';
		client_layout = client_layout == 0 ? 2 : client_layout;// if layout auto (on open) - define actual layout
	}
	
	// set layout classes
	$('body').removeClass('screen_landscape_desktop').removeClass('screen_landscape_mobile').removeClass('screen_portrait_all').addClass(layout_class).removeClass('body_hidden');
	
	// controll menu and filter visibility on orientation change - those cases when they have been changed with javascript - move to menu.js?
	// menu
	if (layout_class == 'screen_landscape_desktop') $('.menu').show();
	if (layout_class == 'screen_portrait_all') $('.menu').show();
	if (layout_class == 'screen_landscape_mobile' && $('.menu_toggle').hasClass('menu_max')) $('.menu').hide();
	if (layout_class == 'screen_landscape_mobile' && $('.menu_toggle').hasClass('menu_min')) $('.menu').show();
	// filter
	if (layout_class == 'screen_landscape_desktop') $('.tool_navigation').show();
	if ((layout_class == 'screen_portrait_all' || layout_class == 'screen_landscape_mobile') && !$('.tool_navigation').hasClass('on')) $('.tool_navigation').hide();
	if ((layout_class == 'screen_portrait_all' || layout_class == 'screen_landscape_mobile') && $('.tool_navigation').hasClass('on')) $('.tool_navigation').show();
	
	controlSlideWrapHeight();

}

// controll slide list height in the report-view left panel
function controlSlideWrapHeight() {
	$('.slide_wrap').css('height','0px');
	let $tool_navigation_bottom = $('.tool_navigation').position().top + $('.tool_navigation').outerHeight(true);
	let $slide_wrap_top = $('.slide_wrap').position().top;
	let $visual_export_wrap_height = $('#visual_export_wrap').outerHeight(true);
	let $offset = $visual_export_wrap_height ? 55 : 21;
	let $slide_wrap_margin = $visual_export_wrap_height ? '2vh' : '0px';
	let $slide_wrap_height = $tool_navigation_bottom - $slide_wrap_top - $visual_export_wrap_height - $offset + 'px';
	$('.slide_wrap').css('height',$slide_wrap_height).css('margin-bottom',$slide_wrap_margin);
}

// autologout
// logout on session expire every 1 min
$(document).ready(function(){
	setInterval(function(){ autoLogOut(); }, 60000);
});

// logout on session expire when ajax sent
$(document).ajaxStart(function() {
	autoLogOut();
});

// optimized function console.log()
function cLog(var_value,var_name,divider) {
	if (console_log_on==0) return;
	
	if (divider) console.log(divider + ' {');
	if (var_name) console.log(var_name + ' ->');
	if (var_value) console.log(var_value);
	if (var_value===true) console.log('true_boolean');
	if (var_value===false) console.log('false_boolean');
	if (divider) console.log(divider + ' }');
}
