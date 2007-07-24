<?php
    list($usec, $sec) = explode(" ", microtime());
    $starttime=(float)$usec + (float)$sec;
    require_once("ses.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<?php
include_once("config.php");
require_once("gtdfuncs.php");
require_once("query.inc.php");
$thisurl=parse_url($_SERVER['PHP_SELF']);

$title = '	<title>'.$config['title'];
if ($config['title_suffix']) { $title .= '-'.basename($thisurl['path'],".php");}
$title .= "</title>\n";

echo $title;

if ($config['debug'] || defined('_DEBUG'))
	echo '<style type="text/css">pre,.debug {}</style>';

$config['theme']=$_SESSION['theme'];
?>

<!-- theme main stylesheet -->
<link rel="stylesheet" href="themes/<?php echo $config['theme']; ?>/style.css" type="text/css"/>

<!-- theme screen stylesheet (should check to see if this actually exists) -->
<link rel="stylesheet" href="themes/<?php echo $config['theme']; ?>/style_screen.css" type="text/css" media="screen" />

<!-- theme script (should check to see if this actually exists) -->
<script type="text/javascript" src="themes/<?php echo $config['theme']; ?>/theme.js"></script>

<!-- printing stylesheet -->
<link rel="stylesheet" href="print.css" type="text/css" media="print" />

<link rel="shortcut icon" href="./favicon.ico" />

<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />

<!-- main calendar program -->
<script type="text/javascript" src="calendar.js"></script>

<!-- language for the calendar -->
<script type="text/javascript" src="lang/calendar-en.js"></script>

<!-- the following script defines the Calendar.setup helper function, which makes
	  adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="calendar-setup.js"></script>

<!-- sort tables -->
<script type="text/javascript" src="gtdfuncs.js"></script>

<?php if ($config['debug'] || defined('_DEBUG'))
	echo '<script type="text/javascript">aps_debugInit("',$config['debugKey'],'");</script>'; ?>
