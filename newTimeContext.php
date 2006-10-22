<?php
include_once('header.php');

if (!isset($_POST['submit'])) {
	//form not submitted
	?>
	<h1>New Temporal Context Definition </h1>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<div class='form'>
			<div class='formrow'>
				<label for='name' class='left first'>Context Name:</label>
				<input type="text" name="name" id="name">
			</div>

			<div class='formrow'>
				<label for='description' class='left first'>Description:</label>
				<textarea rows="10" name="description" id="description" wrap="virtual"></textarea>
			</div>
		</div>
		<div class='formbuttons'>
			<input type="submit" value="Add Context" name="submit">
			<input type="reset" value="Cancel">
		</div>
	</form>
	<?php
}else{
$connection = mysql_connect($config['host'], $config['user'], $config['pass']) or die ("Unable to connect!");
mysql_select_db($config['db']) or die ("Unable to select database!");

	$name = empty($_POST['name']) ? die("Error: Enter a context name") : mysql_real_escape_string($_POST['name']);
	$description = empty($_POST['description']) ? die("Error: Enter a description") : mysql_real_escape_string($_POST['description']);
	$dateCreated = date('Y-m-d');
	# don't forget null
	$query = "INSERT into timeitems  values (NULL, '$name', '$description')";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());


    echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=newTimeContext.php"';
	mysql_close($connection);
}
include_once('footer.php');
?>


