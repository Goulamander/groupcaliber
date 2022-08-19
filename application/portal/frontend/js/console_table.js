
/*
	FILE DESCRIPTION:
		PATH: frontend/js/console_table.js;
		TYPE: js (js file);
		PURPOSE: mainly declares function drawTable that generates an AJAX call to file backend/ajax/console_dashboard.ajax.php to get the Table view HTML and insert it into the tool placeholder in the Console page whenever a user triggers this view or changes its parameters (function drawTable is triggered in a more universal function drawTool in file frontend/js/console.js);
		REFERENCED IN: backend/includes/index.lnk.php;
		FUNCTIONS DECLARED (see below);
		STYLES: frontend/css/console_???.css; 	
*/ 

function drawTable($isFullRenderForced) {
	
	if ( cur_tool == 'table' && $isToolSwitchedFromMenu && $isTableGenerated && !$isToolRecalledFromMenu  && !$isFullRenderForced ) {

		runPreloaderTable('off');
		runTable();

		return;
	}
	
	// stop previous ajaxes
	clearInterval($tableAJAXInterval);
	
	//cur_column_ajax_key = Math.floor((Math.random() * 1000000) + 1);
	cur_column_ajax_key++;
	cur_column_ajax_start = Date.now();
	
	table_ajax_send = [];
	columns = [];
	columns_sorted = [];
	column_order = [];
	column_responce_uncertainty = 0;

	cLog(cur_column_ajax_key,'cur_column_ajax_key');

	// get filter data for ajax	
	table_ajax_send_array = [
		cur_column_ajax_key, 'table_0',	filter_data_obj['table'].company_checked[filter_data_obj['table'].company_checked.length-1],
		filter_data_obj['table'].companycompare_checked,
		[
			'Q310_1',
			'Q310_2',
			'Q310_3',
			'Q310_4',
			'Q310_5',
			'Q310_6',
			'Q310_7',
			'Q310_8',
			'Q310_12',
			'Q310_13',
			'Q310_14',
			'Q310_15',
			'Q310_16',
			'Q310_21',
			'Q310_22',
			'Q310_23',
			'Q310_24',
			'Q310_25',
			'Q310_26',
			'Q310_27',
			'Q310_28',
			'Q310_29',
			'Q310_30',
			'Q310_31',
			'Q310_32',
			'',
			'',
			'',
			'',
			'',
			'trust_affection',
			'Q305_3_01',
			'Q215_3',
			'Q215_1',
			'Q215_2',
			'Q215_4',
			'Q215_5',
			'',
			'S104',
			'S105',
			'VEOLIA2_1',
			'VEOLIA2_2',
			'VEOLIA2_3',
			'VEOLIA2_4',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'SUSTAINABILITY1',
			'SUSTAINABILITY2_1',
			'SUSTAINABILITY2_2',
			'SUSTAINABILITY2_3',
			'SUSTAINABILITY2_4',
			'SUSTAINABILITY2_5',
			'INITIATIVES_1',
			'INITIATIVES_2',
			'INITIATIVES_3',
			'INITIATIVES_4',
			'INITIATIVES_5',
			'INITIATIVES_6',
			'INITIATIVES_7',
			'AGENDA_1',
			'AGENDA_2',
			'AGENDA_3',
			'AGENDA_4',
			'AGENDA_5',
			'AGENDA_6',
			'AGENDA_7',
			'AGENDA_8',
			'AGENDA_9',
			'AGENDA_10',
			'AGENDA_11',
			'AGENDA_12',
			'',
			'',
			'Q215_1_AGREE',
			'',
			'',
			'Q215_2_AGREE',
			'',
			'',
			'Q215_3_AGREE',
			'',
			'',
			'Q215_4_AGREE',
			'',
			'',
			'ADVOCACY_HIGH',
			'REPUTATION_SCORE',
			'PORTFOLIO',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Q310_39',
			'Q310_40',
			'Q310_41',
			'Q310_42',
			'Q310_43',
			'Q310_44',
			'Q310_45',
			'Q310_46',
			'Q310_47',
			'Q310_48',
			'Q310_49',
			'Q310_50',
			'Q310_51',
			'Q310_52',
			'Q310_53',
			'Q310_54',
			'Q310_55',
			'Q310_56',
			'Q310_60',
			'Q310_61',
			'Q310_62',
			'Q310_63',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Q310_64',
			'Q310_65',
			'Q310_66',
			'Q310_67',
			'Q310_68',
			'Q310_69',
			'Q310_70',
			'Q310_71',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'AGENDA_13',
			'AGENDA_14',
			'AGENDA_15',
			'AGENDA_16',
			'Q310_72',
			'Q310_73',
			'Q310_74',
			'Q310_75',
			'Q310_76',
			'Q310_77',
			'Q310_78',
			'Q310_79',
			'Q310_80',
			'Q310_81',
			'Q310_82',
			'Q310_83',
			'Q310_84',
			'Q310_85',
			'Q310_86',
			'Q310_87',
			'Q310_88',
			'Q310_89',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Q310_90',
			'Q310_91',
			'Q310_92',
			'Q310_93',
			'Q310_94',
			'Q310_95',
			'Q310_96',
			'Q310_97',
			'Q310_98'
		],
		
		filter_data_obj['table'].benchmarking_checked[0]	,
		filter_data_obj['table'].period_checked[0]	,
		filter_data_obj['table'].gender_checked	,
		filter_data_obj['table'].age_checked	,
		filter_data_obj['table'].country_checked	,
		filter_data_obj['table'].regiontype_checked	,
		filter_data_obj['table'].region_checked	,
		filter_data_obj['table'].educationtype_checked,
		filter_data_obj['table'].incometype_checked,
		filter_data_obj['table'].segment_checked	,
		filter_data_obj['table'].industry_checked	,
		filter_data_obj['table'].companysize_checked	,
		filter_data_obj['table'].diagnosis_checked	,
		filter_data_obj['table'].family_checked	,
		filter_data_obj['table'].children_checked	,
		filter_data_obj['table'].housing_checked	,
		filter_data_obj['table'].living_checked	,
		filter_data_obj['table'].engagement_checked	,
		filter_data_obj['table'].facilities_checked	,
		filter_data_obj['table'].tg1_checked	,
		filter_data_obj['table'].tg2_checked	,
		filter_data_obj['table'].novo1_checked	,
		filter_data_obj['table'].novo2_checked	,
		filter_data_obj['table'].demant2_checked	,
		filter_data_obj['table'].rw1_checked	,
		filter_data_obj['table'].rw2_checked	,
		filter_data_obj['table'].gr1_checked	,
		filter_data_obj['table'].gr2_checked	,
		filter_data_obj['table'].vcustomer_checked	,
		filter_data_obj['table'].gol1_checked	,
		filter_data_obj['table'].gol2_checked	,
		filter_data_obj['table'].gol3_checked	,
		filter_data_obj['table'].gol5_checked	,
		filter_data_obj['table'].veolia1_checked	,
		filter_data_obj['table'].touchpoints_checked	,
		filter_data_obj['table'].employment_checked	,
		filter_data_obj['table'].race_checked	,
		filter_data_obj['table'].ethnicity_checked	,
		filter_data_obj['table'].politics_checked	,
		filter_data_obj['table'].childrennumber_checked	,
		filter_data_obj['table'].tcccinfluence_checked,
		filter_data_obj['table'].tcccsegment_checked	,
		filter_data_obj['table'].dmi1_checked,
		filter_data_obj['table'].energinet1_checked,
		filter_data_obj['table'].stakeholdertype_checked,
		filter_data_obj['table'].hovedkategori_checked,
		filter_data_obj['table'].siemensq1_checked,
		filter_data_obj['table'].association_checked,
		filter_data_obj['table'].segment2_checked,
		filter_data_obj['table'].fk1_checked,
		filter_data_obj['table'].regionzone_checked,
		filter_data_obj['table'].education_checked,
		filter_data_obj['table'].income_checked,
		filter_data_obj['table'].cv2_checked,
		filter_data_obj['table'].cv3_checked,
		filter_data_obj['table'].subregionbrgroup_checked,
		filter_data_obj['table'].wsa1_checked,
		filter_data_obj['table'].wsa2_checked,
		filter_data_obj['table'].wsa3studyarea_checked,
		filter_data_obj['table'].sf1_checked,
		filter_data_obj['table'].eon1_checked,
		filter_data_obj['table'].eoncustomer_checked,
		filter_data_obj['table'].gn2_checked,
		filter_data_obj['table'].gn1_checked,
		filter_data_obj['table'].gn3_checked,
		filter_data_obj['table'].gn4_checked,
		filter_data_obj['table'].zs2_checked,
		filter_data_obj['table'].zs3_checked,
		filter_data_obj['table'].ess1_checked,
		filter_data_obj['table'].ess2_checked,
		filter_data_obj['table'].ori1_checked,
		filter_data_obj['table'].ovoinfluencer_checked,
		filter_data_obj['table'].ovocustomer_checked,
		filter_data_obj['table'].bay5_checked,
		filter_data_obj['table'].ethnicityca_checked,
		filter_data_obj['table'].politicsca_checked,
		filter_data_obj['table'].air02_checked,
		filter_data_obj['table'].air03_checked,
		filter_data_obj['table'].air04_checked
	];
	
	table_ajax_send.push(fixFilter(table_ajax_send_array));

	var column_name = filter_data_obj['table'].column_checked[0];

	switch(column_name) {
		case 'gender': var element_index = 7; break;
		case 'age': var element_index = 8; break;
		case 'country': var element_index = 9; break;
		case 'regiontype': var element_index = 10; break;
		case 'region': var element_index = 11; break;
		case 'educationtype': var element_index = 12; break;
		case 'incometype': var element_index = 13; break;
		case 'segment': var element_index = 14; break;
		case 'industry': var element_index = 15; break;
		case 'companysize': var element_index = 16; break;
		case 'diagnosis': var element_index = 17; break;
		case 'family': var element_index = 18; break;
		case 'children': var element_index = 19; break;
		case 'housing': var element_index = 20; break;
		case 'living': var element_index = 21; break;
		case 'engagement': var element_index = 22; break;
		case 'facilities': var element_index = 23; break;
		case 'tg1': var element_index = 24; break;
		case 'tg2': var element_index = 25; break;
		case 'novo1': var element_index = 26; break;
		case 'novo2': var element_index = 27; break;
		case 'demant2': var element_index = 28; break;
		case 'rw1': var element_index = 29; break;
		case 'rw2': var element_index = 30; break;
		case 'gr1': var element_index = 31; break;
		case 'gr2': var element_index = 32; break;
		case 'vcustomer': var element_index = 33; break;
		case 'gol1': var element_index = 34; break;
		case 'gol2': var element_index = 35; break;
		case 'gol3': var element_index = 36; break;
		case 'gol5': var element_index = 37; break;
		case 'veolia1': var element_index = 38; break;
		case 'touchpoints': var element_index = 39; break;
		case 'employment': var element_index = 40; break;
		case 'race': var element_index = 41; break;
		case 'ethnicity': var element_index = 42; break;
		case 'politics': var element_index = 43; break;
		case 'childrennumber': var element_index = 44; break;
		case 'tcccinfluence': var element_index = 45; break;
		case 'tcccsegment': var element_index = 46; break;
		case 'dmi1': var element_index = 47; break;
		case 'energinet1': var element_index = 48; break;
		case 'stakeholdertype': var element_index = 49; break;
		case 'hovedkategori': var element_index = 50; break;
		case 'siemensq1': var element_index = 51; break;
		case 'association': var element_index = 52; break;
		case 'segment2': var element_index = 53; break;
		case 'fk1': var element_index = 54; break;
		case 'regionzone': var element_index = 55; break;
		case 'education': var element_index = 56; break;
		case 'income': var element_index = 57; break;
		case 'cv2': var element_index = 58; break;
		case 'cv3': var element_index = 59; break;
		case 'subregionbrgroup': var element_index = 60; break;
		case 'wsa1': var element_index = 61; break;
		case 'wsa2': var element_index = 62; break;
		case 'wsa3studyarea': var element_index = 63; break;
		case 'sf1': var element_index = 64; break;
		case 'eon1': var element_index = 65; break;
		case 'eoncustomer': var element_index = 66; break;
		case 'gn2': var element_index = 67; break;
		case 'gn1': var element_index = 68; break;
		case 'gn3': var element_index = 69; break;
		case 'gn4': var element_index = 70; break;
		case 'zs2': var element_index = 71; break;
		case 'zs3': var element_index = 72; break;
		case 'ess1': var element_index = 73; break;
		case 'ess2': var element_index = 74; break;
		case 'ori1': var element_index = 75; break;
		case 'ovoinfluencer': var element_index = 76; break;
		case 'ovocustomer': var element_index = 77; break;
		case 'bay5': var element_index = 78; break;
		case 'ethnicityca': var element_index = 79; break;
		case 'politicsca': var element_index = 80; break;
		case 'air02': var element_index = 81; break;
		case 'air03': var element_index = 82; break;
		case 'air04': var element_index = 83; break;
			
	}
	
	if (column_name != 'none') {
		var column_count = 1;
		$.each(filter_data_obj['table'][column_name + '_checked'], function (i) {
			var column_value = filter_data_obj['table'][column_name + '_checked'][i];
			if ( column_value != 'all' && column_value != 'multiple' && column_value != 'group' ) {
				table_ajax_send_array_new = [];
				$.each(table_ajax_send_array, function (j) {
					if (j == 1) {
						table_ajax_send_array_new.push('table_' + column_count);
					} else if (j == element_index) {
						table_ajax_send_array_new.push([column_value]);
					} else {
						table_ajax_send_array_new.push(table_ajax_send_array[j]);	
					}
				});	
				table_ajax_send.push(fixFilter(table_ajax_send_array_new));
				column_count++;
			}
		});	
	}
	
	cLog(table_ajax_send,'table_ajax_send');

	// start ajax request and run table when all data received
	$tableAJAXCount = 0;
	table_ajaxes_to_process = table_ajax_send.length;
	table_ajaxes_processed = 0;
	table_ajaxes_false = 0;
	table_ajax_errors = 0;
	
	// update progress bar
	getProgressTable(table_ajaxes_to_process,0);

	$tableAJAXInterval = setInterval(function(){ 
			
		$.ajax({
				type: 'POST',  
				url: 'backend/ajax/console_dashboards.ajax.php',
				data: { 
					param: table_ajax_send[$tableAJAXCount]
				},
				cache: false,
				success: function(data){

					cLog(data,'table_ajax_back_success');

					// get statistics
					if (data == 'ajax_false') table_ajaxes_false++;

					// process relevant data
					if (data && data != 'ajax_false') {
						try {
							
							this_column_ajax_key = JSON.parse(data)[1]; 
							cLog(this_column_ajax_key,'this_column_ajax_key');
							if (this_column_ajax_key == cur_column_ajax_key) {
								
								// update progress bar
								table_ajaxes_processed++;
								getProgressTable(table_ajaxes_to_process,table_ajaxes_processed);
								
								columns.push([JSON.parse(data)[0][0],JSON.parse(data)[0][1]]);
								column_order.push(JSON.parse(data)[0][1]);
								column_responce_uncertainty = column_responce_uncertainty + JSON.parse(data)[0][2];
								
								// finalize ajax
								if ( table_ajaxes_to_process == table_ajaxes_processed ) {

									// attach table	
									if (table_ajax_errors == 0) {
										$preloaderTablePause = setTimeout(function(){ 
											try {
												if ( cur_tool == 'table' ) runTable();	

												runPreloaderTable('off');
												$isTableGenerated = true;
											} catch(err) {
												runPreloaderTable('error'); 
												cLog(err.message,'TRYCATCH_table');
											}	
										}, 1000);

										column_number = table_ajaxes_processed - 1;	

									} else {
										runPreloaderTable('error');
									}

									// print statistics
									cur_column_ajax_end = Date.now();
									cLog((cur_column_ajax_end - cur_column_ajax_start),'column with ajax key ' + cur_column_ajax_key + ' speed');
									cLog(table_ajaxes_processed,'column with ajax key ' + cur_column_ajax_key + ' ajaxes_processed');
									cLog((table_ajaxes_false ? table_ajaxes_false : '0'),'column with ajax key ' + cur_column_ajax_key + ' false_ajaxes');
									cLog((table_ajax_errors ? table_ajax_errors : '0'),'column with ajax key ' + cur_column_ajax_key + ' ajax_errors');

								}

							}
							
						} catch (e) {
							table_ajax_errors++;
							runPreloaderTable('error');
							cLog(e.message,'TRYCATCH_table');
						}
					}

				},
				error: function(data){

					cLog(data,'table_ajax_back_error');

					// get statistics
					table_ajax_errors++

					// finalize ajax
					if ( cur_tool == 'table' ) {
						runPreloaderTable('error');

						// print statistics
						cur_column_ajax_end = Date.now();
						cLog((cur_column_ajax_end - cur_column_ajax_start),'column with ajax key ' + cur_column_ajax_key + ' speed');
						cLog(table_ajaxes_processed,'column with ajax key ' + cur_column_ajax_key + ' ajaxes_processed');
						cLog((table_ajaxes_false ? table_ajaxes_false : '0'),'column with ajax key ' + cur_column_ajax_key + ' false_ajaxes');
						cLog((table_ajax_errors ? table_ajax_errors : '0'),'column with ajax key ' + cur_column_ajax_key + ' ajax_errors');
						
					}

				}
			});
				
		if ( $tableAJAXCount + 1 == table_ajaxes_to_process ) clearInterval($tableAJAXInterval);
			
		$tableAJAXCount++;
	}, table_ajax_delay);	
	
};

function runTable() {
	
	column_order.sort(function(a, b){return a-b});
	
	if (columns_sorted.length == 0) {
		$.each(column_order, function (i) {	
			$.each(columns, function (j) {
				if (i == columns[j][1]) {
					columns_sorted.push(columns[j][0]);
				}
			});			
		});	
	}
	
	$('#tool_placeholder').html(columns_sorted[0]);

	$.each(columns_sorted, function (i) {	
		if (i > 0) {
			$.each(columns_sorted[i], function (j) {
				$('#table_detail tr:nth-child(' + (j + 1) + ')').append(columns_sorted[i][j]);
			});	
		}
	});	

	if (column_responce_uncertainty==0) {
		$('.legend_wrap div:nth-child(8)').hide();
	} else {
		$('.legend_wrap div:nth-child(8)').show();
	}

	setTableButtons();
	setTableRows();
	
};

$(document).on('click', '#table_scroll_right', function() {	
		
	var container_x = $('.table_detail_wrap').offset().left + 4;

	$('#table_scroll_left').prop('disabled',false); // костыль
	
	for (i = 1; i <= column_number; i++) { 
		var column_x = $('#table_detail th:nth-child(' + i + ')').offset().left;
		if (column_x > container_x) {		
			var table_offset = column_x - $('#table_detail').offset().left;
			double_scroll = Math.abs(table_offset_left - table_offset) <= 1 ? true : false;
			table_offset_right = table_offset;
			$('.table_detail_wrap').animate( { scrollLeft: table_offset + 1 }, (double_scroll ? 0 : animate_speed) );
			
			if ($table_auto_click_right) { // stops recursive behavior
				$table_auto_click_right = false;
				return false;			
			}
			
			if (double_scroll) {
				$table_auto_click_right = true;
				$('#table_scroll_right').click(); 
			}
			
			return false;	
		}
	}

});

$(document).on('click', '#table_scroll_left', function() {
	
	var container_x = $('.table_detail_wrap').offset().left + 4;

	for (i = 1; i <= column_number; i++) {
		var column_x = $('#table_detail th:nth-child(' + i + ')').offset().left
		if (column_x >= container_x) {
			if ( $('#table_detail th:nth-child(' + (i - 1) + ')').length == 1 ) column_x = $('#table_detail th:nth-child(' + (i - 1) + ')').offset().left;
			var table_offset = column_x - $('#table_detail').offset().left;
			double_scroll = Math.abs(table_offset_right - table_offset) <= 1 ? true : false;
			table_offset_left = table_offset;
			
			if ($table_auto_click_left) { // stops recursive behavior
				$table_auto_click_left = false;
				return false;			
			}
			
			$('.table_detail_wrap').animate( { scrollLeft: table_offset }, (double_scroll ? 0 : animate_speed) );
			if (double_scroll) {
				$table_auto_click_left = true;
				$('#table_scroll_left').click(); 	
			}
			return false;	
		}
	}

});

window.addEventListener('orientationchange', function() {	
	$('.table_detail_wrap').scrollLeft(0);
});

document.addEventListener('scroll', function (event) {
    if (event.target.className === 'table_detail_wrap') setTableButtons();	
}, true);

$(window).resize(function(){
	setTableButtons();
	setTableRows();
});

function setTableButtons() {

$('#table_scroll_left').prop('disabled',false);	// костыль
$('#table_scroll_right').prop('disabled',false); // костыль
return;	
	
		if ( Math.abs($('.table_detail_wrap').scrollLeft() - ($('#table_detail').width() - $('.table_detail_wrap').width())) < 3 ) { // not 1 because of IE of Firefox
			$('#table_scroll_right').prop('disabled',true);
		} else {
			$('#table_scroll_right').prop('disabled',false);
		}
		
		if ( $('.table_detail_wrap').scrollLeft() < 3 ) { // not ==0 because of redundant pixels on mobile devices and not 1 because of IE of Firefox
			$('#table_scroll_left').prop('disabled',true);
		} else {
			$('#table_scroll_left').prop('disabled',false);
		}

		//if ( $('.table_detail_wrap').width() == $('#table_detail').outerWidth() ) $('#table_scroll_left, #table_scroll_right').prop('disabled', true);
	
}

function setTableRows() {
	$table_detail_th_height = $('#table_detail th').outerHeight();
	$table_detail_td_height = $('#table_detail td').outerHeight();
	$('[id^=table_total] th').css('height', $table_detail_th_height + 'px');
	$('[id^=table_total] td').css('height', $table_detail_td_height + 'px');
}
