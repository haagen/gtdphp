<?php
//INCLUDES
include_once('header.php');

//RETRIVE FORM VARIABLES
$values=array();
$values['type']=$_POST['type']{0};
$values['title'] = mysql_real_escape_string($_POST['title']);
$values['description'] = mysql_real_escape_string($_POST['description']);
$values['desiredOutcome'] = mysql_real_escape_string($_POST['desiredOutcome']);
$values['categoryId'] = (int) $_POST['categoryId'];
$values['contextId'] = (int) $_POST['contextId'];
$values['timeframeId'] = (int) $_POST['timeframeId'];
$parents = $_POST['parentId'];
$values['deadline'] = $_POST['deadline'];
$values['repeat'] = (int) $_POST['repeat'];
$values['suppress'] = $_POST['suppress']{0};
$values['suppressUntil'] = (int) $_POST['suppressUntil'];
$values['nextAction'] = $_POST['nextAction']{0};
$values['dateCompleted']=mysql_real_escape_string($_POST['dateCompleted']);
if ($_POST['isSomeday']{0}=='y') $values['isSomeday']='y';
else $values['isSomeday']='n';


//CRUDE error checking
if ($values['suppress']!="y") $values['suppress']="n";
if ($values['nextaction']!="y") $values['nextaction']="n";
if (!isset($values['title'])) die ("No title. Item NOT added.");

//Insert new records
$result = query("newitem",$config,$values);
$values['newitemId'] = $GLOBALS['lastinsertid'];
$result = query("newitemattributes",$config,$values);
$result = query("newitemstatus",$config,$values);

if($values['nextAction']=='y') $result = query("newnextaction",$config,$values);
    foreach ($parents as $values['parentId']) $result = query("newparent",$config,$values);

echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=itemReport.php?itemId='.$values['newitemId'].'">';

include_once('footer.php');
?>
