<?php

/*
	FILE DESCRIPTION:
		PATH: sync/functions2.php;
		TYPE: php (function declaration);
		PURPOSE: declares functions common for this and other projects that combine mysql and mysqli methods into one;
		REFERENCED IN: all files that use SQL connection;
		FUNCTIONS DECLARED (see below) :
		STYLES: - ;
*/ 

/*

function create_email ($email_id, $email_type=1, $email_from_user_id, $email_to, $email_to_name, $email_subj, $email_text, $project_id=0, $site_id=0, $event_id=0, $cc='', $cc_name='', $bcc='', $bcc_name='', $replyto='', $replyto_name='')

function send__mail($email, $subj, $text, $to='', $email_id=0, $project_id=0, $site_id=0, $event_id=0, $from_user_id=0, $cc='', $cc_name='', $bcc='', $bcc_name='')

function get_email_body($text, $email, $email_id=0, $project_id=0, $site_id=0, $event_id=0, $from_user_id=0)

function email_plus ($email, $id)
function is_email($email)
function get_email_trans($text, $email, $site=0, $project=0)
function get_id_by_mail ($email, $cache=true)
function check_email($email, $event_id=NULL) // TODO - redis
function is_email_exists($email, $from=NULL)
function check_email_exists($email, $from)
function block_email($email, $state=2)
function get_email_label($label, $email, $site=0, $project=0)
function get_project_option($project_id,$field_name) // TODO - Redis
function get_field_by_email ($field, $email)
function hash_me($a, $alt_salt=NULL)

*/

function create_email ($email_id, $email_type=1, $email_from_user_id, $email_to, $email_to_name, $email_subj, $email_text, $project_id=0, $site_id=0, $event_id=0, $cc='', $cc_name='', $bcc='', $bcc_name='', $replyto='', $replyto_name='')
{
    if ($project_id == 0)
    {
        // If no project has been specified for the email, use the project id function.
        $project_id = get_project();
    }

	if (!is_email($email_to)) 
	{
		debug_log ('functions2.php/create_email(): Попытка создать письмо для неправильного мейла: '.$email_to);	
		return false;
	}

	$email_to_name = check_in($email_to_name);
	$email_type = check_in($email_type, 3);
	
	$cc = check_in($cc, 9);
	$bcc = check_in($bcc, 9);
	$replyto = check_in($replyto, 9);
	$cc_name = check_in($cc_name);
	$bcc_name = check_in($bcc_name);
	$replyto_name = check_in($replyto_name);
	
	$email_subj = check_in($email_subj);
	$email_text = str_replace(array("\r\n", "\n", "\r"), '', check_in($email_text));
	
	if ($email_id>0)
	{ 
		$query = 
			"UPDATE `emails` SET ".
				"`email_from_user_id`='$email_from_user_id', ".
				"`email_type`='$email_type', ".
				"`email_to`='$email_to', ".
				"`email_to_name`='$email_to_name', ".
				"`email_CC`='$cc', ".
				"`email_CC_name`='$cc_name', ".
				"`email_BCC`='$bcc', ".
				"`email_BCC_name`='$bcc_name', ".
				"`email_replyto`='$replyto', ".
				"`email_replyto_name`='$replyto_name', ".
				"`email_subj`='$email_subj', ".
				"`email_text`='$email_text', ".
				"`email_project_id`='$project_id', ".
				"`email_site_id`='$site_id', ".
				"`email_event_id`='$event_id' ".
			"WHERE `emails_id`='$email_id'";
	}
	else
	{ 	
		$query = 
			"INSERT INTO `emails` (".
				"`email_from_user_id`, ".
				"`email_type`, ".
				"`email_to`, ".
				"`email_to_name`, ".
				"`email_CC`, ".
				"`email_CC_name`, ".
				"`email_BCC`, ".
				"`email_BCC_name`, ".
				"`email_replyto`, ".
				"`email_replyto_name`, ".
				"`email_subj`, ".
				"`email_text`, ".
				"`email_project_id`, ".
				"`email_site_id`, ".
				"`email_event_id`".
            ") VALUES (".
				"'$email_from_user_id', ".
				"'$email_type', ".
				"'$email_to', ".
				"'$email_to_name', ".
				"'$cc', ".
				"'$cc_name', ".
				"'$bcc', ".
				"'$bcc_name', ".
				"'$replyto', ".
				"'$replyto_name', ".
				"'$email_subj', ".
				"'$email_text', ".
				"'$project_id', ".
				"'$site_id', ".
				"'$event_id'".
			")";
	}
	
	$sql = sql_query($query);

	$return = ($email_id>0 ? $email_id : sql_insert_id());

	return $return;
}

function send__mail ($email, $subj, $text, $to='', $email_id=0, $project_id=0, $site_id=0, $event_id=0, $from_user_id=0, $cc='', $cc_name='', $bcc='', $bcc_name='', $replyto='', $replyto_name='')
{
	global $last_email_error;

	if(!is_email($email) && $email!='') 
	{		
		$last_email_error = get_email_trans("send__mail: {{mail_address_incorrect}} $email!", $email);
		debug_log($last_email_error);
			// {{mail_address_incorrect}}="неправильный адрес"
		return false;
	}

	if(!($user_id = get_id_by_mail($email)) || get_field('user_disabled',$user_id,'users')>0) 
	{
		$last_email_error = get_email_trans("send__mail: {{mail_user_blocked}} $email!", $email);
		debug_log($last_email_error);
			// {{mail_user_blocked}}="пользователь не существует или заблокирован"
		return false;
	}


    $SMTP_USERNAME = getenv("SMTP_USERNAME");
    $SMTP_PASSWORD = getenv("SMTP_PASSWORD");
    $SMTP_HOST = getenv("SMTP_HOST");
    $SMTP_PORT = getenv("SMTP_PORT");
    $SMTP_SENDER = getenv("SMTP_SENDER");
    $SMTP_REPLY_TO = getenv("SMTP_REPLY_TO");

    if(check_email($email,$event_id)===false)
	{
		$last_email_error = get_email_trans("send__mail: {{mail_address_unsubscribed}} $email!",$email);
		debug_log($last_email_error);
			// {{mail_address_unsubscribed}}="адрес помечен как отписавшийся, отправка невозможна"
		return false;
	}

	if(!is_email_exists($email)) 
	{
		$last_email_error = get_email_trans("send__mail: {{mail_address_not_exist}} $email!", $email);
		debug_log($last_email_error);
			// {{mail_address_not_exist}}="несуществующий адрес"
		return false; // TODO - добавить блокировку пользователя - его мейла
	}

	$site = $site_id; //get_project_site($project_id);
	$lang = get_lang(0, $user_id);
	
	$mail = new PHPMailer(true);

    $mail->IsSMTP();
    $mail->Host = $SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP_USERNAME; //paste one generated by Mailtrap
    $mail->Password = $SMTP_PASSWORD; //paste one generated by Mailtrap
    $mail->SMTPSecure = 'tls';
    $mail->Port = $SMTP_PORT;

	$mail->CharSet = 'utf-8';

	$to = get_email_trans(($to=='' ? $email : $to), $email, $site, $project_id);
	$mail->AddAddress($email, $to);
	
	$from = $from_user_id == 0 
			? get_label('project_name', $lang, $site, $project_id, $event_id) 
			: get_fio($from_user_id);

	$mail_from_label = $email_id==0 ? 'mail_link_address' : 'mail_info_address';

	if ($SMTP_SENDER == "")
    {
        $SMTP_SENDER = get_label($mail_from_label, $lang, $site, $project_id, $event_id);
    }
	$mail->SetFrom(
			$SMTP_SENDER,
			get_email_trans($from, $email, $site, $project_id)
	);
		// {{mail_link_address}}="no-reply@site.com"
		// {{mail_info_address}}="no-reply@site.com"

    if ($cc!='')
	{
		if (
			!is_email($cc) ||
			get_field('user_disabled',get_id_by_mail($cc),'users')>0 ||
			!check_email($cc, $event_id) ||
			!is_email_exists($cc)
			)
		{
			$cc = '';
		} else
		{
			$mail->AddCC($cc, $cc_name);
		}
	}

	if ($bcc!='')
	{
		if (
			!is_email($bcc) ||
			get_field('user_disabled',get_id_by_mail($bcc),'users')>0 ||
			!check_email($bcc, $event_id) ||
			!is_email_exists($bcc)
			)
		{
			$bcc = '';
		} else
		{
			$mail->AddCC($bcc, $bcc_name);
		}
	}

	$replyto = $replyto!='' ? $replyto : get_label('mail_replyto_address', $lang, $site, $project_id, $event_id);
    $replyto = email_plus($replyto, $email_id);

    $replyto_name = $replyto_name!='' ? $replyto_name : get_label('mail_replyto_name', $lang, $site, $project_id, $event_id);

    if ($SMTP_REPLY_TO == "")
    {
        $SMTP_REPLY_TO = $replyto;
    }
	$mail->AddReplyTo(
            $SMTP_REPLY_TO,
			$replyto_name
	);

	$mail->Subject = get_email_trans(replace_match($subj, $user_id), $email, $site, $project_id); 

	$body = get_email_body($text, $email, $email_id, $project_id, $site_id, $event_id, $from_user_id);
	$mail->MsgHTML($body);

/******************************************************************************************/

	$emails_return_email = get_label('emails_return_email', $lang, $site, $project_id, $event_id); 
		// {{emails_return_email}}="return@site.com"
	$emails_return_email = email_plus($emails_return_email, $email_id);
	$mail->addCustomHeader('Return-Path', $emails_return_email); 

	if (isset($email_id)) 
	{
		$emails_message_id_suffix = get_label('emails_message_id_suffix', $lang, $site, $project_id, $event_id); 
			// {{emails_message_id_suffix}}="email.site.com"
		$message_ID = $email_id.'@'.$emails_message_id_suffix;
//		$mail->$MessageID = $message_ID;
		$mail->addCustomHeader('X-Message-ID', $message_ID);
	}

	if (isset($event_id) && $event_id>0) 
	{
		$mail->addCustomHeader('Precedence', 'bulk');
		
		$list_unsub_link = 
			"http://".get_label('project_site', 0, $site_id, $project_id, $event_id)."/?".
				"unsubscribe=".($user_id)."&amp;".
				"hash=".(hash_me($email))."&amp;".
				"eid=".$event_id.
			"";
		$mail->addCustomHeader('List-Unsubscribe', $list_unsub_link);
	}

	$emails_return_abuse = get_label('emails_return_abuse', $lang, $site, $project_id, $event_id); 
		// {{emails_return_abuse}}="abuse@site.com"
	$emails_return_abuse = email_plus($emails_return_abuse, $email_id);
	
	$emails_return_errors = get_label('emails_return_errors', $lang, $site, $project_id, $event_id); 
		// {{emails_return_errors}}="errors@site.com"
	$emails_return_errors = email_plus($emails_return_errors, $email_id);

	$mail->addCustomHeader('Abuse-Reports-To', $emails_return_abuse);
	$mail->addCustomHeader('Errors-To', $emails_return_errors);
	$mail->addCustomHeader('Return-Receipt-To', $emails_return_email);
	$mail->addCustomHeader('X-Abuse-Info', $emails_return_abuse);
	$mail->addCustomHeader('X-Complaints-To', $emails_return_abuse);
	$mail->addCustomHeader('X-Report', $emails_return_abuse);

/* */

/******************************************************************************************/	
	
  	if(!$mail->Send()) {
		  $mail_error = $mail->ErrorInfo;
		  $last_email_error = get_email_trans("send__mail: [$email] {{mail_mailer_error}} = $mail_error", $email, $site, $project_id);
			  // {{mail_mailer_error}}="Ошибка почтовой программы:"
		  debug_log($last_email_error);
		  $return = false;
	} else 
	{
		$return = true;
	}
	return $return;
}

function email_plus ($email, $id)
{
	$email = explode("@", $email);
    $username = $email[0];
    $domain = isset($email[1]) ? $email[1] : 'nodomain.com';
	return $username.'+'.$id.'@'.$domain;
}

function is_email($email)
{
	$s = filter_var($email, FILTER_VALIDATE_EMAIL);
	return !empty($s);
}

function get_email_trans($text, $email, $site=0, $project=0)
{
	$user_id = get_id_by_mail($email);
	
	if($user_id>0) 
		return 
			translate($text, 0, $site, $project, 0, $user_id);
	else return 
			translate($text);
			
}

function get_id_by_mail ($email, $cache=true)
{
	global $redis, $redis_delay;
	
	$return = false;
	
	if ($cache)
	{
		if (isset($redis))
		{
			$value = $redis->get('email_'.$email);
			if ($value!==false)
			{
				return $value;
			}
		}
	}
	
	$select = "SELECT `users_id` FROM `users` WHERE `email`='$email' OR `email`='".urldecode($email)."' OR `email`='".urlencode($email)."' LIMIT 1";
	if ($sql = sql_query($select))
	{
	    if (sql_num_rows($sql) > 0) 
		{ 
			$row = sql_fetch_array ($sql);
			$return = $row['users_id'];
		} 
	}
	if ($cache)
	{
		if (isset($redis))
		{
			$redis->set('email_'.$email, $return, array('nx', 'ex'=>$redis_delay));
		}
	}
	return $return;
}

function check_email($email, $event_id=NULL) // TODO - redis
{
	$return = true;
	$query = "SELECT `unsubscribe_state` FROM `unsubscribes` WHERE `unsubscribe_email`='$email' ".
			($event_id ? " AND (`unsubscribe_event`='$event_id' OR `usubscribe_event`='0')" : '')." LIMIT 1";
	$sql = sql_query($query);
	if (sql_num_rows($sql)>0)
	{
		$r = sql_fetch_array($sql);
		if($r['unsubscribe_state']>=1) $return = false;
	}
	return $return;
}

function is_email_exists($email, $from=NULL)
{
	
	if (!check_email($email)) return false;

	if (!isset($from)) $from = get_label('mail_info_address'); 

	$is_email_exists = check_email_exists($email, $from);
	
	if (!$is_email_exists)
	{
		block_email($email, 5);
	}
	return $is_email_exists;
	
}

function get_email_body($text, $email, $email_id=0, $project_id=0, $site_id=0, $event_id=0, $from_user_id=0)
{
	global $mail_footer2, $email_sign, $email_template_header, $email_template_footer, $site_com, $track_php;

	$user_id = get_id_by_mail ($email);
	if ($project_id==0)
	{
//?		if ($event_id!=0) $project_id = get_field ('id_project', $event_id, 'events');
//?		else $project_id = get_field ('user_project_id', $user_id, 'users');
		$project_id = get_project($project_id, $user_id, $site_id, $event_id);	// TODO - addedd in POR-907_20.80a
	}

	$site = $site_id; // get_project_site($project_id);

	$event_topic = empty($event_id) ? '' : 
		"<a target=\"blank\" href=\"$site_com/?event=$event_id\">".
			"[$event_id] ".get_field('topic',$event_id,'events').
		"</a>";
				
	$email_sign_plus = 
		($event_id == 0 
			? "{{mail_ps_receiving_because1}}"
			: sprintf(
				get_email_label('mail_ps_receiving_because2', $email, $site, $project_id),
				$event_topic
			)
		)."<br>".get_email_label('mail_ps_unsubscribe', $email, $site, $project_id);

	// {{mail_ps_receiving_because1}}="PS: Вы получили это письмо, потому что регистрировались на сайте {{project_site}} или посещали одно из наших событий."
	// {{mail_ps_receiving_because2}}="PS: Вы получили это письмо, потому что регистрировались на сайте {{project_site}} и посещали событие %s."
	// {{mail_ps_unsubscribe}}="Если вы хотите управлять получением этих уведомлений, перейдите на <a href='http://{{project_site}}/?unsubscribe=_{uid}_&amp;hash=_{hash}_&amp;eid=_{eid}_'>страницу подписки/отписки</a>",
	// {{project_site}}="web1nar.com"

	$email_sign2 = $email_sign."<span style='font-size: 0.65em; color: grey;'>".$email_sign_plus."</span>";
	
	$body1 = $text;

/*!!!*/	$body1 = $email_template_header.$text.'<br>'./*$email_sign2.'<br>'.*/$mail_footer2.'<br><br>'.$email_template_footer;

	$match = array(
		'_{email_header_image}_',
		'_{name}_',
		'_{fam}_',
		'_{event}_',
		'_{from_fio}_',
		'_{email_stamp_image}_',
		'_{eid}_',
		'_{uid}_',
		'_{hash}_',
		'_{email_id}_'
	);

	$event_disabled = empty($event_id) ? 0 : get_field('disabled', $event_id, 'events');

	$sid = rand(1000,9999);
	$email_header_image = 
		$project_id==1 
			? get_email_label('email_header_image', $email, $site, $project_id)
			: get_project_option($project_id, 'project_email_header_image')	// TODO - can be removed after changing all labels and settings (POR-1956)
	;
	$replace = array(
		$email_header_image,
		get_field_by_email('name', $email, $site),
		get_field_by_email('fam', $email, $site),
		(($event_id == 0 || $event_disabled > 1)
			? '{{emails_event}}' 
			: $event_topic
		),
		($from_user_id == 0 ? '{{mail_signee}}' : get_fio($from_user_id)), 
			// {{mail_signee}}=""
		get_project_option($project_id,'project_email_stamp_image'),
		$event_id,
		get_id_by_mail($email),
		hash_me($email),
		($email_id>0 ? "<small>emailID: $email_id</small><br><img src=\"".$track_php."?track=$email_id&sid=$sid\">" : "")					
	);

	$body0 = str_replace ($match, $replace, $body1);

	$body1 = get_email_trans($body0, $email, $site, $project_id);

    // Insert invironment variable in the tracking URL, so that tracking can be tested in development and staging.
    $body1 = insert_environment_id_in_url ($body1);

	return $body1;
}

/**
 * Insert environment in the tracking URL of emails.
 * This is necessary when emails are sent from and tested in non-production environments.
 * @param $value Default tracking url.
 * @return array|mixed|string|string[] Modified tracking url.
 */
function insert_environment_id_in_url($value)
{
    // Domain (to search for when inserting the environment.
    $domain = "groupcaliber.com";

    // The default return values is the provided default. This is the case when running in production.
    $result = $value;

    // Add the . to the domain.
    $search_string = ".".$domain;

    if (ENVIRONMENT != "production")
    {
        // If not in production insert the environment in the url.
        // E.g. for staging portal.groupcaliber.com becomes portalstaging.groupcaliber.com
        $result = str_replace($search_string, ENVIRONMENT.$search_string, $result);
    }

    // Return the modified url.
    return $result;
}

function check_email_exists($email, $from)
{
	return true; //TODO: временная мера
	
	$validator = new SMTP_Validate_Email($email, $from);
	$smtp_results = $validator->validate();

	return ($smtp_results[$email]);
}

function block_email($email, $state=2)
{
	if (check_email($email))
	{
		$query = "INSERT INTO `unsubscribes` (".
					" unsubscribe_email, ".
					" unsubscribe_state ".
				" ) VALUES (".
					" '$email',".
					" '$state'".
				" )";
		$sql = sql_query($query);
	}
}

function get_email_label($label, $email, $site=0, $project=0)
{
	return get_label($label, 0, $site, $project, 0, get_id_by_mail($email));	
}

function get_project_option($project_id,$field_name) // TODO - Redis
{
	if ($project_id==0) $project_id=1;
	$query = "SELECT `$field_name` FROM `projects` WHERE `projects_id`='$project_id' LIMIT 1";
	$sql = sql_query($query);
	if(sql_num_rows($sql)>0)
	{
		if ($r=sql_fetch_array($sql))
		{
			if($r[0]!='') return $r[0];
			else 
				if($r = sql_fetch_array(sql_query("SELECT `$field_name` FROM `projects` WHERE `projects_id`='1' LIMIT 1")))
					return $r[0];
				else return false;
		} else return false;
	} else return false;
}

function get_field_by_email ($field, $email)
{
	return get_user_field_by_id($field, get_id_by_mail($email));
}

function hash_me($a, $alt_salt=NULL)
{
	global $salt;
	if (!isset($alt_salt)) $alt_salt = $salt;
	$a=md5(md5($a).$alt_salt);
	return $a;
}


function replace_match ($text, $user_id)
{
	if ($user_id<=0) return $text; // false - так было

	$query = "SELECT * FROM `users` WHERE `users_id`='$user_id' LIMIT 1";
	$sql = sql_query($query);

	if(sql_num_rows($sql)==0) return false;

	if(($row = sql_fetch_array($sql)))
	{
		$q = "SHOW COLUMNS FROM `users`";
		$s = sql_query($q);
		$match = array();
		$replace = array();
		
		while ($r = sql_fetch_array($s))
		{
			array_push($match, '_{'.$r['Field'].'}_');
			array_push($replace, $row[$r['Field']]);
		}
		
		return str_replace($match, $replace, $text);	
	}
	return -2;
}


