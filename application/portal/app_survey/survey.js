
/*
	FILE DESCRIPTION:
		PATH: app_survey/survey.js;
		TYPE: js (javascript file);
		PURPOSE: processes respondents' inputs before submitting them to app_survey/survey.php to be recorded;
		REFERENCED IN: app_survey/survey.lnk.php;
		FUNCTIONS DECLARED - :
		STYLES: - ; 
*/   

$(document).ready(function()
{
	
	var limit = $('#question_limit').val();
	if (limit>0)
	{
		$('input.checkboxes').on('change', function(e) {
			if ($("input.checkboxes:checked").length > limit) {
				this.checked = false;
			}
		});
	}
	
	// hiding/showing question rows
	if ($('.card').hasClass('subtype_1')) {
		row_number = 1;
		$('.row, #submit').hide(); 
//-console.log('.row submit hided!');		
		$('.row:nth-child(' + row_number + ')').show();
		$('.titles').show();
	
		$('.col-md-1 .rating_logo').click(function(){

				$('.row:nth-child(' + row_number + ')').fadeOut(500);
//-console.log(row_number + ' hided!');
				$('.titles').fadeIn(600);
				row_number++;

				setTimeout(function(){ 
					$('.row:nth-child(' + row_number + ')').show();
					if (row_number > $('.row').length) $('#submit').click();
				}, 500);

		});
	}
	
	// timeout procedure
	if ( $('#first_login').val() == 1 ) {
		setCookie('cs_server_start_time', $('#server_start_time').val(), 30);
		setCookie('cs_client_start_time', new Date().getTime(), 30);
	}
	
	var timeout = Number($('#timeout').val())*60*1000;
	var server_start_time = Number(getCookie('cs_server_start_time'));
	var client_start_time = Number(getCookie('cs_client_start_time'));
	var server_start_day = Math.floor(server_start_time/86400000);

	
	setInterval(function(){ 
		
		var server_cur_time = new Date().getTime() + server_start_time - client_start_time;
		var server_cur_day = Math.floor(server_cur_time/86400000);
		
		if ( (client_start_time + timeout) < new Date().getTime() || server_start_day != server_cur_day ) {
			setTimeout(function(){ 
				$('#submit').hide(); //-console.log('#submit hided!');
				location.reload(true);
			}, 60*1000);
		}	
	}, 60*1000);

	getInput();

	let $input_data_array = $('#input_data').val().slice(2,-2).split('","');
	let $survey = $input_data_array[0].replace('"', '');
	let $question = $input_data_array[2];
	
	var $question_hidden = $('.card-header').data('question_hidden');
	
	var $autoclick = 
		($survey==30 || $survey==31 || $survey==32) && $question==7 
		|| $question_hidden==1
	;

	if ($autoclick)
	{
		$('.card').hide(0); //-console.log('.card hided!');
	}
	

	// record question shown
	//$('.next_preloader').css('display','inline');
	$('#submit').prop('disabled',true);
	$('.survey_next').hide(); //-console.log('.survey_next hided!');
	$('.next_preloader, .survey_next_wait').show();
	$.ajax({
		type: 'POST',  
		url: window.location.protocol + '//' + window.location.host + '/app_survey/survey.ajax.php',
		data: {
			input_datas: $('#input_data').val()
		},
		cache: false,
		success: function(data){
			setTimeout(function(){ 
				if (data == 1) {
					$('.next_preloader, .survey_next_wait').hide(); //-console.log('..next_preloader, .survey_next_wait hided!');
					$('.survey_next').show();
					$('#submit').prop('disabled',false);
					
					if ($autoclick) 
					{
//-console.log('survey 3x, question 7 autoclick!');
						$('#submit').click();
					}
					
				}
			}, 1000);
		}
	});
	
	document.styleSheets[0].addRule('.col-md-3.question::before','content: "' + $('.card-header').data('question_title1') + '";'); // add question subtitle 1
	document.styleSheets[0].addRule('.answer_row:nth-child(1) > div:nth-child(1)::before','content: "' + $('.card-header').data('question_title2') + '";'); // add question subtitle 2

});

// prepare all inputs data array to send to server
$('#submit').click(function(event){
	
	question_id = $('.card-header').data('question_id');
	
	getInput();

	// if questioin mandatory and not completed - prevent submit and fire a message
	var question_mandatory = $('.card-body').data('question_mandatory');
	if ( (question_mandatory == 1 && inputs_answered == 0) || (question_mandatory == 'all' && inputs_all > inputs_answered) ) {
		event.preventDefault(); 
		alert( $('#msg_mandatory').text() );	
	} else {
		$('#submit').hide(); //-console.log('#submit hided!');
	}
	
	// process language question
	if ( question_id == '94' ) $("#lang").val(JSON.parse($("#input_data").val())[0][11]);
	
	//alert( JSON.parse($("#input_data").val())[0][11] );

});

// control single choice inputs in multiple-choice questions
$('.card-body input').click(function() {

	var question_type = $('.card-header').data('question_type'); // type 4
	
	if ( question_type == 4 ) {
		var question_singles = $('.card-header').data('question_singles').split(',');
		var subquestion = $(this).prop('name').split('_')[1];
		if ( $.inArray( subquestion, question_singles ) > -1 ) {
			$('.card-body input').each(function() {	
				if ( subquestion != $(this).prop('name').split('_')[1] ) $(this).prop('checked', false);
			});	
		} else {
			$('.card-body input').each(function() {	
				if ( $.inArray( $(this).prop('name').split('_')[1], question_singles ) > -1 ) $(this).prop('checked', false);	
			});
		}
	}

});

function getInput() {
	
	// record raw input data to a single question/subquestion input
	$('.card-body input').each(function() {
		if ( $(this).is(':checked') ) {
			$('#default_input_' + $(this).prop('name')).data('input_data', $(this).data('raw_input_data') + '|1');	
		}
	});
	
	// record all input_datas (including default_inputs) to a single form-level input
	inputs_all = 0;
	inputs_answered = 0;
	cur_input_datas = [];
	$('.card-body input, .card-body select, .card-body textarea').each(function() {	
		
		var this_input_data = $(this).data('input_data') ? $(this).data('input_data') : $(this).find(':selected').data('input_data');
		
		var this_input_value = $(this).val(); // default_inputs' values are null - this pertains only to text inputs and textareas - checkbox and radio values (1) are already attached above - move this to above procedure for non checkbox inputs and delete adding value below and add default inputs in function getQuestionHTML and change to raw inputs for more universal functionality
		
		if (this_input_data) {
			
			var cur_input_data = (this_input_data + '|' + this_input_value).split('|');

			// check if question not answered
			inputs_all++;
			
			if ( cur_input_data[10] != '' || cur_input_data[11] != '' || cur_input_data[14] != '' ) inputs_answered++;

			cur_input_datas.push( cur_input_data );
		}
		
	});
	
	$('#input_data').val( JSON.stringify(cur_input_datas) );
	
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}