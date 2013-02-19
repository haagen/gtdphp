<?php
$title='Fetch Email';
include_once 'header.inc.php';
?>

<h2>Email Import Results</h2>
<?php

$imap_src = "{" . $config["mail"]["host"] . ":" . $config["mail"]["port"] . "/" . $config["mail"]["secure"] . "}INBOX";
$username = $config["mail"]["username"];
$password = $config["mail"]["password"];

if (!$config["mail"]) 
{
    ?>
No mail configuration found. Please add mail server settings to config.inc.php. <br /><br />
<pre>
$config = array(

    //connection information
        "host"      =>  '',    // the hostname of your database server
        "db"        =>  '',    // the name of your database
        "prefix"    =>  '',    // the GTD table prefix for your installation (optional)
        "user"      =>  '',    // username for database access
        "pass"      =>  '',    // database password
        "dbtype"    =>  'mysql',     //database type: currently only mysql is valid.  DO NOT CHANGE!
        
        "mail"      =>  array(
                            "host" =>   "",     // Address to IMAP server, ex: imap.gmail.com
                            "port" =>   "",     // Port for the IMAP service, ex: 997
                            "secure" => "",     // Security setting for the connection, ex: ssl
                            "username" => "",   // Username for IMAP service
                            "password" => ""    // Password for IMAP service
                        )
);
</pre>
    
    <?php   
}
else if (!function_exists("imap_open"))
{
?>
Your PHP installation does not support IMAP. Please install the imap module to use this functionallity!
<?php
}
else
{
    $mbox = @imap_open($imap_src, $username, $password);
    if($mbox) 
    {
        $mails=imap_num_msg($mbox);
        $cntImported = 0;
        for ($i=1;$i<=$mails;$i++) {
            $header_info = imap_headerinfo($mbox, $i);

            $subjects = imap_mime_header_decode($header_info->Subject);
            $subject = "";
            for ($k=0;$k<count($subjects);$k++) $subject .= $subjects[$k]->text;
            
            $s = imap_fetchstructure($mbox,$i);
            //print_r($s);
            if (!$s->parts) 
            {
                $body = imap_body($mbox, $i);    
                if ($s->encoding == 4) {
                    $body = quoted_printable_decode(($body));
                } else if ($s->encoding == 3) {
                    $body = base64_decode($body);   
                }
            } else {
                for ($k=1;$k<count($s->parts);$k++) {
                    $v = $s->parts[$k];
                    if ($v->type!=0 && $v->subtype != "PLAIN") continue;
                    $body_part = imap_fetchbody($mbox,$i,$k);
                    if ($v->encoding == 4) {
                        $body_part = quoted_printable_decode($body_part);
                    } else if ($v->encoding == 3) {
                        $body_part = base64_decode($body_part);   
                    }
                    $body .= $body_part;
                }
            }
            
            $values = array();
            $values["title"] = $subject;
            $values["description"] = $body;
            $values["type"] = "i";
            $values["dateCompleted"] = "NULL";
            $values["tickledate"] = "NULL";
            $values["deadline"] = "NULL";
            $result = query("newitem",$values);
            if (!$result) {
                echo "Failed to save item: " . $values["title"] . "<br />";
                continue;
            }
            $values['newitemId'] = $GLOBALS['lastinsertid'];        
            $result = query("newitemstatus",$values);
            if (!$result) {
                // Try to clean up - don't bother with the result
                $result = query("deleteitem", array("itemId" => $values['newitemId'])); 
                echo "Failed to save item: " . $values["title"] . "<br />";
                continue;
            }
            $cntImported++;
            
            imap_delete($mbox, $i);
            
            echo "Imported: " . $values["title"] . " <br />";

        }
        imap_close($mbox);
        echo "<br /><br />Number of imported <a href='listItems.php?type=i'>Inbox</a> items was " . $cntImported . " <br />";
    }
    else
    {
        echo "Could not connect to the email server: " . imap_last_error() . "<br /><br />";
    }
}

include_once 'footer.inc.php';
?>