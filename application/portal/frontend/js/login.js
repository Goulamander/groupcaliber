
/*
	FILE DESCRIPTION:
		PATH: frontend/js/login.js;
		TYPE: js (js file);
		PURPOSE: supports the client-side functionality of the Login page controlling user login/logout process, setting a cookie for storing logins and paswords, providing the abilty to restore forgotten passwords, checking the user corrent login status to automatically log him/her out when the server session expires (function autoLogOut);		
		REFERENCED IN: backend/includes/index.lnk.php;
		FUNCTIONS DECLARED (see below) ;
		STYLES: - ; 
*/  

///////////// LOG IN /////////////////////

$(document).ready(function(){	

	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		var expires = 'expires='+d.toUTCString();
		document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
	}

	function getCookie(cname) {
		var name = cname + '=';
		var ca = document.cookie.split(';');
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return '';
	}
	// get cookie login
	cur_cname = '2p0ljkiiu5n80nn34xhaq3n9';
	cur_cvalue = ['','','off'];
	cur_cvalue[0] = getCookie(cur_cname).split(',')[0] == '' ? '' : getCookie(cur_cname).split(',')[0];
	cur_cvalue[1] = getCookie(cur_cname).split(',')[1] == '' ? '' : getCookie(cur_cname).split(',')[1];
	cur_cvalue[2] = getCookie(cur_cname).split(',')[0] == '01' ? '01' : '10';
	cur_exdays = 365;
	
	$('#login').val( getCookie(cur_cname).split(',')[0] );
	
	setTimeout(function(){ $('#password').val( getCookie(cur_cname).split(',')[1] ).prop('type','password'); }, 100);

	if ( getCookie(cur_cname).split(',')[2] == '10' ) {
		$('#inputCookie').prop('checked', true);
	} else {
		$('#inputCookie').prop('checked', false);
	}
	
	// set/unset cookie login
	$('#login, #password, #inputCookie').on('input click',function() {
		
		if ($('#inputCookie').is(':checked')) {	
			cur_cvalue[0] = $('#login').val();
			cur_cvalue[1] = $('#password').val();
			cur_cvalue[2] = '10';
		} else {
			cur_cvalue[0] = '';
			cur_cvalue[1] = '';
			cur_cvalue[2] = '01';
		}
		setCookie(cur_cname, cur_cvalue, cur_exdays);
	});
	
	$('#login').focus();

	// log in procedure
	function logIn(){
		$('.login_preloader').show();
		$.ajax({
			type: 'POST',  
			url: 'backend/ajax/login.ajax.php',
			data: { 
				param1: $('#login').val(),
				param2: $('#password').val()
			},
			cache: false,
			success: function(data){			
				if ( data != 0 ) {
					location.reload(true);				
				} else {
					$('.login_preloader').hide();
					alert('Login failed... Please check your data and try again!');
				}
			}
    	});
	};
	
	// restore password procedure
	function restorePassword(){
		$.ajax({
			type: 'POST',  
			url: 'backend/ajax/login.ajax.php',
			data: { 
				param3: $('#restore_email').val()
			},
			cache: false,
			success: function(data){
				if ( data != 0 ) {
					alert('Please find your new password in your email!');			
				} else {
					alert('No such user found... Please try again!');	
				}
			}
    	});
	};
	
	$('.modal_login_button').click(function(){
		logIn();
	});
	
	$('.modal_restore_button').click(function(){
		restorePassword();
	});
	
	$(document).keypress(function (e) {
		if (e.which == 13 || e.keyCode == 13) {
			if ( $('#login').val() && $('#password').val() ) {
				logIn();
			}
		}
	});

	// toggle modal pages
	$('#modal_login_forgot_pw').click(function(){
		$('#modal_login_page1').hide();
		$('#modal_login_page2').show();
	});
	
	$('#modal_login_back').click(function(){
		$('#modal_login_page2').hide();
		$('#modal_login_page1').show();
	});
	
	// jira issue collector code
	window.ATL_JQ_PAGE_PROPS = $.extend(window.ATL_JQ_PAGE_PROPS, {
		// ==== custom trigger function ====
		/*triggerFunction : function( showCollectorDialog ) {
			$('#feedback-button').on( 'click', function(e) {
				e.preventDefault();
				showCollectorDialog();
			});
		},*/
		// ==== we add the code below to set the field values ====
		fieldValues: {
			customfield_10036: getUserData().id ? getUserData().id : 0, // reporter id (client or user id)
			fullname : getUserData().name ? getUserData().name : getCookie(cur_cname).split(',')[0],
			email : getUserData().email ? getUserData().email : '',
			recordWebInfo: '1',
			recordWebInfoConsent: ['1']
		}					
	});
		
});

//////////// LOG OUT //////////////////

function logOut() {
	$.ajax({
		type: 'POST',  
		url: 'backend/ajax/login.ajax.php',
		data: { 
			logout: 1
		},
		cache: false,
		success: function(data){	
			location.reload(true);	
		}
	});
}

function autoLogOut(auto_logout) {
	$.ajax({
		type: 'POST',  
		url: 'backend/ajax/login.ajax.php',
		data: { 
			login_status: 1
		},
		cache: false,
		success: function(data){
			if (data == '') {
				logOut();	
			}
		}
	});
}

// this function returns the user data from the backend as an object
function getUserData() {
	userData = {
		id: Number($('#user_id').html()),
		name: $('#user_name').html(),
		email: $('#user_email').html(),
		created: $('#user_created').html()
	}
	return userData;
}
