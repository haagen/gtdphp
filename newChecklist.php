<?php
//INCLUDES
include_once('header.php');

if (!isset($_POST['submit'])) {
	//form not submitted
?>
<h1>New Checklist</h1>

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<?php
$cashtml=categoryselectbox($config,$values,$options,$sort);
?>
	<div class='form'>
		<div class='formrow'>
			<label for='title' class='left first'>Title:</label>
			<input type="text" name="title" id="title">
		</div>

		<div class='formrow'>
			<label for='category' class='left first'>Category:</label>
			<select name='categoryId' id='category'>
                        <?php echo $cashtml ?>
			</select>
		</div>

		<div class='formrow'>
			<label for='description' class='left first'>Description:</label>
			<textarea rows="10" name="description" id="description" wrap="virtual"></textarea>
		</div>
	</div>
	<div class='formbuttons'>
		<input type="submit" value="Add Checklist" name="submit">
	</div>
</form>

<?php
}else {
    $values = array();
    $values['title'] = empty($_POST['title']) ? die("Error: Enter a checklist title") : $_POST['title'];
    $values['description'] = empty($_POST['description']) ? die("Error: Enter a checklist description") : $_POST['description'];
    $values['categoryId'] = (int) $_POST['categoryId'];

    $result= query("newchecklist",$config,$values,$options,$sort);

    if ($GLOBALS['ecode']=="0") echo "Checklist: ".$values['title']." inserted.";
    else echo "Checklist NOT inserted.";
    if (($config['debug']=="true" || $config['debug']=="developer") && $GLOBALS['ecode']!="0") echo "<p>Error Code: ".$GLOBALS['ecode']."=> ".$GLOBALS['etext']."</p>";

	$nextURL='checklistReport.php?checklistId='.mysql_insert_id();
	if ($config['debug']==='false') {
		echo '<META HTTP-EQUIV="Refresh" CONTENT="2; url=',$nextURL,'">';
	} else {
		echo '<p>Next page is <a href="',$nextURL,'">&lt;',htmlspecialchars($nextURL),'&gt;</a> (would be auto-refresh in non-debug mode)</p>';
	}
}

include_once('footer.php');
?>

