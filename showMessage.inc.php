<?php
if (count($_SESSION['message'])) {
    if (is_array($_SESSION['message'])) {
        echo "<div class='success'>\n";
        foreach ($_SESSION['message'] as $msg)
            echo "$msg<br />\n";
        echo "</div>";
    }
    $_SESSION['message']='';
}
