<?php
//INCLUDES
include_once('header.php');

//RETRIEVE ALL URL AND FORM VARIABLES
$values = array();
$values['listId'] =(int) $_GET["listId"];

//SQL CODE
$row = query("selectlist",$config,$values,$sort);
$values['categoryId']=$row[0]['categoryId'];
$cashtml = categoryselectbox($config,$values,$sort);


//PAGE DISPLAY CODE
	echo "<h2>Edit List: {$values['listTitle']}</h2>\n";
	echo '<form action="updateList.php?listId='.$values['listId'].'" method="post">'."\n";
?>

	<div class='form'>
		<div class='formrow'>
			<label for='title' class='left first'>List Title:</label>
			<input type='text' name='newlistTitle' id='title' value='<?php echo htmlspecialchars(stripslashes($row[0]['title'])); ?>' />
		</div>

		<div class='formrow'>
			<label for='category' class='left first'>Category:</label>
			<select name='newcategoryId' id='category'>
                        <?php echo $cashtml; ?>
			</select>
		</div>

		<div class='formrow'>
			<label for='description' class='left first'>Description:</label>
			<textarea rows="10" name="newdescription" id="description" cols="60"><?php echo htmlspecialchars(stripslashes($row[0]['description'])); ?></textarea>
		</div>
	</div>
	<div class='formbuttons'>
		<input type="submit" value="Update List" name="submit" />
		<input type="reset" class="button" value="Reset" />
		<input type="checkbox" name="delete" id='delete' class='notfirst' title="ALL items will be deleted!" value="y" />
                <input type="hidden" name="listId" value="<?php echo $values['listId'] ?>" />
		<label for='delete'>Delete&nbsp;List</label>
	</div>
	</form>

<?php
	include_once('footer.php');
?>
