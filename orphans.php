<?php
//INCLUDES
include_once('header.php');

//RETRIEVE URL VARIABLES
$values=array();
$maintable = query("getorphaneditems",$config,$values,$options,$sort);
foreach ($maintable as $key=>$row) $maintable[$key]['title']=makeclean($row['title']);

$dispArray=array();
$thisrow=0;
$dispArray=array(
    'type'=>'Type'
    ,'title'=>'Name'
    ,'description'=>'Description'
    );
$show=array();
foreach ($dispArray as $key=>$val)
    $show[$key]=true;
if ($config['debug'] & _GTD_DEBUG) echo '<pre>Orphans:',print_r($maintable,true),'</pre>';
echo "<h2>",count($maintable)," Orphaned Item",(count($maintable)===1)?'':'s',"</h2>";
if ($maintable!=-1 && count($maintable)) { ?>
    <table class="datatable sortable" id="typetable" summary='table of orphans'>
        <?php require('displayItems.inc.php'); ?>
    </table>
<?php } else {
    $message="Nothing was found.";
    nothingFound($message);
}

include_once('footer.php');
?>
