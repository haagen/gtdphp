<?php
//INCLUDES
include_once('header.php');

//CONNECT TO DATABASE
$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
mysql_select_db($db) or die ("Unable to select database!");

//RETRIEVE URL AND FORM VARIABLES
$values['newitem']=mysql_real_escape_string($_POST['newitem']);
$values['newnotes']=mysql_real_escape_string($_POST['newnotes']);
$values['listId'] = (int) $_POST['listId'];
$values['newdateCompleted'] = $_POST['newdateCompleted'];
$values['listItemId'] = (int) $_GET['listItemId'];
$values['delete']=$_POST['delete']{0};

//SQL CODE AREA
if($values['delete']=="y") {
    query("deletelistitem",$config,$values);
    echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listReport.php?listId='.$values['listId'].'">';
	}
else {
    query("updatelistitem",$config,$values);
    echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listReport.php?listId='.$values['listId'].'">';
	}

mysql_close($connection);
include_once('footer.php');
?>
