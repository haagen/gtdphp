<?php
include_once('header.php');

//GET URL AND FORM DATA
$values=array();
$values['categoryId'] = (int) $_GET['categoryId'];
$values['category']=mysql_real_escape_string($_POST['category']);
$values['description']=mysql_real_escape_string($_POST['description']);
$values['delete']=$_POST['delete']{0};
$values['newCategoryId']=(int) $_POST['newCategoryId'];

if ($values['delete']=="y") {
    query("reassigncategory",$config,$values);
    query("deletecategory",$config,$values);
	}

else $result = query("updatecategory",$config,$values);

    if ($GLOBALS['ecode']=="0") echo "Category ".$values['category']." updated.";
    else {
        echo "Category NOT updated.";
        if ($config['debug']=="true" || $config['debug']=="developer") echo $GLOBALS['ecode'].": ".$GLOBALS['etext'];
        }

echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listProjects.php"/>';

include_once('footer.php');
?>