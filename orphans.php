<?php
//INCLUDES
include_once('header.php');

//RETRIEVE URL VARIABLES
$values=array();
$values['notOrphansfilterquery']=(empty($config['suppressAsOrphans']))?"'i','m'":$config['suppressAsOrphans'];
$maintable = query("getorphaneditems",$config,$values,$options,$sort);

$cnt=($maintable==-1)?0:count($maintable);
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
echo "<h2>$cnt Orphaned Item",($cnt===1)?'':'s',"</h2>";
if ($cnt) { ?>
    <table class="datatable sortable" id="typetable" summary='table of orphans'>
        <?php require('displayItems.inc.php'); ?>
    </table>
<?php } else { ?>
    <p>Congratulations: you have no orphaned items.</p>
<?php } include_once('footer.php'); ?>
