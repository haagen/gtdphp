<?php
//INCLUDES
include_once('header.php');

//RETRIEVE FORM URL VARIABLES
$values=array();
$values['listId'] = (int) $_GET['listId'];
$values['newlistTitle']=$_POST['newlistTitle'];
$values['newcategoryId']=(int) $_POST['newcategoryId'];
$values['newdescription']=$_POST['newdescription'];
$values['delete']=$_POST['delete']{0};

//SQL CODE AREA
if($values['delete']=="y") {
    query("deletelist",$config,$values);
        //echo "<p>Number of lists deleted: ";
        //echo mysql_affected_rows();
    query("removelistitems",$config,$values);
        //echo "<p>Number of list items deleted: ";
        //echo mysql_affected_rows();
    echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listReport.php?listId='.$values['listId'].'">';
	}

else {
    query("updatelist",$config,$values);
    echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listReport.php?listId='.$values['listId'].'">';
	}

include_once('footer.php');
?>
