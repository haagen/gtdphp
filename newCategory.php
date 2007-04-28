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

else {
    $values = array();
    $values['category'] = ($_POST['category']=="") ? die('<META HTTP-EQUIV="Refresh" CONTENT="2; url=newCategory.php" /><p>Error: Enter a category name</p>') : $_POST['category'];
    $values['description'] =  $_POST['description'];

    $result = query("newcategory",$config,$values);

    if ($GLOBALS['ecode']=="0") echo "Category ".$values['category']." inserted.";
    else echo "Category NOT inserted.";
    if (($config['debug']=="true" || $config['debug']=="developer") && $GLOBALS['ecode']!="0") echo "<p>Error Code: ".$GLOBALS['ecode']."=> ".$GLOBALS['etext']."</p>";

    echo '<META HTTP-EQUIV="Refresh" CONTENT="2; url=newCategory.php" />';
    }

include_once('footer.php');
?>

