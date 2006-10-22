<?php
include_once('header.php');

if (!isset($_POST['submit'])) {
	//form not submitted
	?>
	<h1>New Category Definition </h1>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<div class='form'>
			<div class='formrow'>
				<label for='name' class='left first'>Category Name:</label>
				<input type="text" name="category" id="name">
			</div>

			<div class='formrow'>
				<label for='description' class='left first'>Description:</label>
				<textarea rows="10" name="description" id="description" wrap="virtual"></textarea>
			</div>
		</div>
		<div class='formbuttons'>
			<input type="submit" value="Add Category" name="submit">
			<input type="reset" value="Cancel">
		</div>
	</form>
	<?php
}

else{

$connection = mysql_connect($config['host'], $config['user'], $config['pass']) or die ("Unable to connect!");
mysql_select_db($config['db']) or die ("Unable to select database!");

    $values['category'] = ($_POST['category']=="") ? die('<META HTTP-EQUIV="Refresh" CONTENT="2; url=newCategory.php" /><p>Error: Enter a category name</p>') : mysql_escape_string($_POST['category']);
    $values['description'] =  mysql_escape_string($_POST['description']);

   $result = query("newcategory",$config,$values,$options,$sort);

    if ($result['ecode']=="0") echo "Category ".$values['category']." inserted.";
    else echo "Category NOT inserted.";
    if ($config['debug']=="true" || $config['debug']=="developer") echo $result['ecode'].": ".$result['etext'];

    echo '<META HTTP-EQUIV="Refresh" CONTENT="2; url=newCategory.php" />';
}


include_once('footer.php');
?>

