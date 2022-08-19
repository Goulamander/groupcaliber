<?php 

/*
	FILE DESCRIPTION:
		PATH: backend/cron/email.cron.php;
		TYPE: cron (scheduled job file);
		PURPOSE: runs function send__mail of the phpmailer plugin to send unsent emails from table 'emails' in the database and updates the status of such emails accordingly;
		REFERENCED IN: server job scheduler;
		FUNCTIONS DECLARED - ;
		STYLES: - ; 
*/  
// POR-1957 : add dirname( __FILE__ ) to fixe relative path errors when running this script thru php-cli 
require_once ( dirname( __FILE__ ).'/../../root.php');
require_once (ROOT.'sync/constants.php');
require_once (ROOT.'config/site_version.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'sync/sql_connect.php');
require_once (ROOT.'sync/email_templates.php');	

require_once (ROOT.'plugins/smtp-validator/smtp-validate-email.php');
require_once (ROOT.'plugins/PHPMailer-master/src/Exception.php');
require_once (ROOT.'plugins/PHPMailer-master/src/PHPMailer.php');
require_once (ROOT.'plugins/PHPMailer-master/src/SMTP.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// require_once (ROOT.'plugins/phpmailer/class.phpmailer.php');

//require_once ('plugins/smtp-validator-1.0/src/Validator.php');
//require_once ('plugins/phpmailer-6.0.5/src/PHPMailer.php');

// Only run cron for one server (the server with id 0)
if (SERVER_ID != "0")
{
    exit;
}

$cron_log = ROOT.'logs/cron_log.txt';

if (!isset($echo)) { $echo = false; }

if ($echo) echo("email.cron started...");

if (!get_label('enable_email_cron')) {
	$error = 'email.cron: cron not enabled';
	debug_log($error, $cron_log);
	die($error);
}

/*
if ($site_version!='production') {
	$error = 'email.cron: not production server';
	debug_log($error, $cron_log);
	die($error);
}
*/

set_time_limit(55);

debug_log('email.cron: started', $cron_log);

for($i=1;$i<=5;$i++) 
{
	
	$query = "SELECT * FROM `emails` WHERE `email_sent`='0' AND `email_status`<>'-1' ".
				" ORDER BY `emails_id` DESC LIMIT 4"; // 4 = по 4 письма в каждом цикле
	$sql = sql_query($query);
	
//-if ($sql) echo "debug_log('email.cron: query successful='.sql_num_rows($query), $cron_log);"; else echo "HELP!";
	
	if (sql_num_rows($sql)>0)
	{
		while ($r = sql_fetch_array($sql))
		{
            // For any environment that is not production send only e-mails to Olga or groupcaliber.com.
            // For all others send to nk@aperify.com
            if ($site_version != "production")
            {
                if ($r['email_to'] == "olgapotapenko375@gmail.com")
                {}
                else if (strpos($r['email_to'], "groupcaliber.com")!==false)
                {}
                else
                {
                    $r['email_to'] = "nk@aperify.com";
                }
            }

			$status = send__mail(
				$r['email_to'], 
				htmlspecialchars_decode($r['email_subj']), 
				htmlspecialchars_decode($r['email_text']), 
				$r['email_to_name'], 
				$r['emails_id'], 
				$r['email_project_id'], 
				$r['email_site_id'],
				$r['email_event_id'],
				$r['email_from_user_id'],
				$r['email_CC'],
				$r['email_CC_name'],
				$r['email_BCC'],
				$r['email_BCC_name'],
				$r['email_replyto'],
				$r['email_replyto_name']
			);
			
$debug_text = 
	"$status = send__mail(
		$r[email_to], 
		htmlspecialchars_decode($r[email_subj]), 
		htmlspecialchars_decode($r[email_text]), 
		$r[email_to_name], 
		$r[emails_id], 
		$r[email_project_id], 
		$r[email_site_id],
		$r[email_event_id],
		$r[email_from_user_id],
		$r[email_CC],
		$r[email_CC_name],
		$r[email_BCC],
		$r[email_BCC_name],
		$r[email_replyto],
		$r[email_replyto_name]
	);
";
			
if ($echo) echo ($debug_text); //else debug_log($debug_text);

			if ($status!==false) {
			  $query = "UPDATE `emails` SET `email_sent`='1', `email_sent_datetime`=NOW() ";
			} else {
			  $query = "UPDATE `emails` SET `email_status`='-1', `email_error`='E:$last_email_error', `email_error_datetime`=NOW() ";
			}
			$query .= "WHERE `emails_id`='".$r['emails_id']."'";

			sql_query($query);
			
			debug_log('email.cron: '.$i.' : '.$r['email_to'], $cron_log); 
					
			sleep(rand(2, 4));
		}
	}
}

$debug_text = "email.cron: ended";
if ($echo) echo ($debug_text); else debug_log($debug_text, $cron_log);

sql_close();
		

