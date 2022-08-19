<?php

/*
	FILE DESCRIPTION:
		PATH: backend/includes/index.lnk.php;
		TYPE: lnk (links container file);
		PURPOSE: sets various HTML header links for the site;
		REFERENCED IN: index.php;
		FUNCTIONS DECLARED - :
		STYLES: - ; 
*/  

if (!isset($file_version)) { $file_version = '?v='.rand(1000,9999); }

$links_global = '

<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="/images/icons/apple-touch-icon.png'.$file_version.'">
<link rel="icon" type="image/png" sizes="32x32" href="/images/icons/favicon-32x32.png'.$file_version.'">
<link rel="icon" type="image/png" sizes="16x16" href="/images/icons/favicon-16x16.png'.$file_version.'">
<link rel="manifest" href="/images/icons/site.webmanifest'.$file_version.'">
<link rel="mask-icon" href="/images/icons/safari-pinned-tab.svg'.$file_version.'" color="#003300">

<meta name="msapplication-TileColor" content="#cccc33">
<meta name="msapplication-TileImage" content="/images/icons/mstile-150x150.png'.$file_version.'">
<meta name="msapplication-config" content="/images/icons/browserconfig.xml'.$file_version.'">

<meta name="theme-color" content="#ffffff">

<link rel="shortcut icon" href="/images/icons/favicon.ico'.$file_version.'">

<meta name="apple-mobile-web-app-title" content="Caliber Portal">
<meta name="application-name" content="Caliber Portal">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.min.css">
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js" integrity="sha256-hlKLmzaRlE8SCJC1Kw8zoUbU8BxA+8kR3gseuKfMjxA=" crossorigin="anonymous"></script>

<!-- Async Set Interval -->
<script src="https://unpkg.com/set-interval-async"></script>

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>

<!-- Awesome Glyphicons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Browser Detect -->
<script src="plugins/detect/detect.min.js"></script>

<!-- General Project Styles -->
<link rel="stylesheet" type="text/css" href="frontend/css/index.css'.$file_version.'">

';

$enable_appcues = get_label('enable_appcues')==1;
$links_global .= $enable_appcues == 1 ? '
<!-- Appcues (User Guide app) -->
<script src="//fast.appcues.com/94245.js"></script>
' : '';
	

$links_before_login = '
<script src="frontend/js/libs/labelTranslate.js'.$file_version.'" type="text/javascript" defer></script>

<!-- Project Styles -->
<link rel="stylesheet" type="text/css" href="frontend/css/login.css'.$file_version.'">

<!-- Project Scripts -->
<script src="frontend/js/login.js'.$file_version.'" type="text/javascript" defer></script>
';

$links_after_login_all = '
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/wordcloud.js"></script>

<!-- html2canvas -->

<script type="text/javascript" src="https://github.com/niklasvh/html2canvas/releases/download/v1.0.0-rc.7/html2canvas.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js"></script>

<!-- LeafJet Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

<!-- Swipe -->
<script src="plugins/touch-swipe/jquery.touchSwipe.min.js"></script>

<!-- jTable -->
<link href="plugins/jtable.2.6.0/lib/themes/lightcolor/gray/jtable.css" rel="stylesheet" type="text/css" />
<script src="plugins/jtable.2.6.0/lib/jquery.jtable.min.js" type="text/javascript"></script>

<!-- Flag Sprites -->
<link rel="stylesheet" type="text/css" href="plugins/flag-sprites/flags.css">

<!-- Daterangepicker -->
<!-- Moment --> <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Project Styles -->
<link rel="stylesheet" type="text/css" href="frontend/css/menu.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/console.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/console_filter.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/report.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/console_dashboard.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/console_chart.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/console_map.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/console_table.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/wordcloud.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/console_company.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/activity.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/profile.css'.$file_version.'">
<link rel="stylesheet" type="text/css" href="frontend/css/help.css'.$file_version.'">

<!-- Project Scripts -->
<script src="frontend/js/libs/labelTranslate.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/login.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/index.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/menu.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_filter.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_report.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_dashboard.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_chart.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_map.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_table.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_wordcloud.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/console_company.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/activity.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/profile.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/profile_subscription.js'.$file_version.'" type="text/javascript" defer></script>

<!-- Color manipulation -->
<script src="plugins/color/tinycolor.js'.$file_version.'"></script>
<script src="plugins/color/chroma.js'.$file_version.'"></script>


<!-- Admin Tables available for all users -->
<script src="frontend/js/admin_activity.js'.$file_version.'" type="text/javascript" defer></script>

';

$links_after_login_admin = '
<script src="frontend/js/admin_client.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_rating.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_survey.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_country.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_age.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_gender.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_segment.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_industry.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_company_size.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_attribute.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_question.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_field.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_label.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_views.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_project.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_site.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_upload.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_email.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_response.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_subscription.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_customer.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_report.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_ph.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_ph_filter.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_lang.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_data.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_user.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_interview.js'.$file_version.'" type="text/javascript" defer></script>
<script src="frontend/js/admin_index.js'.$file_version.'" type="text/javascript" defer></script>
';

$links_export_rights = '
<link rel="stylesheet" type="text/css" href="frontend/css/admin.css'.$file_version.'">
<script src="frontend/js/export.js'.$file_version.'" type="text/javascript" defer></script>
';

/* Google Analytics */
$links_global .= "
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-121105245-2\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-121105245-2');
</script>
";
